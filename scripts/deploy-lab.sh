#!/usr/bin/env bash
set -euo pipefail

# ==============================================================================
# Carlos Silveira Private Lab — 1-Command Micro-App Deployer (`deploy-lab.sh`)
# Usage: ./scripts/deploy-lab.sh <ruta-proyecto-local> <slug-en-lab>
# Example: ./scripts/deploy-lab.sh ~/Documents/Personal/lab-shopify-store shopify-store
# ==============================================================================

usage() {
  cat <<EOF
Uso: ./scripts/deploy-lab.sh <ruta-proyecto-local> <slug-en-lab>

 Despliega cualquier carpeta de proyecto desde tu Mac directamente a:
   https://carlossilveira.dev/lab/<slug-en-lab>/

Ejemplos:
  ./scripts/deploy-lab.sh /Users/carlossilveira/Documents/Personal/carlossilveiradev/lab-shopify shopify
  ./scripts/deploy-lab.sh ./public/shopify-django-app shopify-django-app
EOF
  exit 1
}

if [[ $# -lt 2 ]]; then
  usage
fi

SOURCE_DIR="$1"
SLUG="$2"
REMOTE_HOST="${SPACESHIP_HOST:-server58.shared.spaceship.host}"
REMOTE_PORT="${SPACESHIP_PORT:-21098}"
REMOTE_USER="${SPACESHIP_USER:-bfjydpuzth}"
SSH_KEY="${SPACESHIP_SSH_KEY_PATH:-$HOME/.ssh/techynews_rsa}"
REMOTE_BASE="/home/bfjydpuzth/carlossilveira.dev/lab"
REMOTE_TARGET="${REMOTE_BASE}/${SLUG}"

if [[ ! -d "$SOURCE_DIR" ]]; then
  echo "Error: El directorio local '$SOURCE_DIR' no existe." >&2
  exit 1
fi

if [[ ! "$SLUG" =~ ^[a-z0-9][a-z0-9-]*$ ]]; then
  echo "Error: <slug-en-lab> debe usar solo minúsculas, números y guiones (ej. 'mi-tienda-shopify')." >&2
  exit 1
fi

SOURCE_ABS="$(cd "$SOURCE_DIR" && pwd)"

# 1. Auto-detect stack for app.json and pre-build steps
DETECTED_STACK="Web Micro-App"
if [[ -f "$SOURCE_ABS/manage.py" || -f "$SOURCE_ABS/passenger_wsgi.py" ]]; then
  DETECTED_STACK="Django / Python 3.12"
elif [[ -f "$SOURCE_ABS/next.config.mjs" || -f "$SOURCE_ABS/next.config.js" ]]; then
  DETECTED_STACK="Next.js / React"
elif [[ -f "$SOURCE_ABS/go.mod" || -f "$SOURCE_ABS/main.go" ]]; then
  DETECTED_STACK="Go 1.23 (Linux amd64)"
elif [[ -f "$SOURCE_ABS/Cargo.toml" ]]; then
  DETECTED_STACK="Rust 2021 (Musl)"
elif [[ -f "$SOURCE_ABS/config.ru" || -f "$SOURCE_ABS/Gemfile" ]]; then
  DETECTED_STACK="Ruby 3.3 / Rack"
elif [[ -f "$SOURCE_ABS/Main.java" ]]; then
  DETECTED_STACK="Java 21 GraalVM"
elif [[ -f "$SOURCE_ABS/index.php" ]]; then
  DETECTED_STACK="PHP 8.3 / LiteSpeed"
fi

echo "==> Preparando micro-app '$SLUG' ($DETECTED_STACK) desde: $SOURCE_ABS"

# 2. Generate app.json if not present so /lab/ Launchpad lists it automatically
if [[ ! -f "$SOURCE_ABS/app.json" ]]; then
  TODAY="$(date -u +"%Y-%m-%d")"
  cat > "$SOURCE_ABS/app.json" <<EOF
{
  "name": "${SLUG}",
  "slug": "${SLUG}",
  "stack": "${DETECTED_STACK}",
  "category": "Micro-App",
  "status": "Activa",
  "description": "Micro-aplicación desplegada en /lab/${SLUG}/ (${DETECTED_STACK}).",
  "local_path": "${SOURCE_ABS}",
  "entry_url": "./${SLUG}/",
  "updated_at": "${TODAY}"
}
EOF
  echo "    + Creado app.json automáticamente para el directorio de /lab/."
fi

# 3. Pre-compile Go static Linux binary if main.go exists and go is installed
if [[ -f "$SOURCE_ABS/main.go" ]] && command -v go >/dev/null 2>&1; then
  echo "    + Compilando binario Go estático para Linux amd64 (index.cgi)..."
  (
    cd "$SOURCE_ABS"
    CGO_ENABLED=0 GOOS=linux GOARCH=amd64 go build -ldflags="-s -w" -o index.cgi .
    chmod 755 index.cgi
  )
fi

# Ensure any .cgi scripts have 755 permissions locally
find "$SOURCE_ABS" -name "*.cgi" -exec chmod 755 {} +

# Determine sync source directory (use out/ if Next.js static export exists)
SYNC_SRC="${SOURCE_ABS}/"
if [[ -d "$SOURCE_ABS/out" && ( -f "$SOURCE_ABS/next.config.mjs" || -f "$SOURCE_ABS/next.config.js" ) ]]; then
  cp "$SOURCE_ABS/app.json" "$SOURCE_ABS/out/app.json"
  SYNC_SRC="${SOURCE_ABS}/out/"
fi

# 4. Deploy via SSH / rsync to Spaceship server58
SSH_OPTS="-p ${REMOTE_PORT} -o StrictHostKeyChecking=accept-new"
if [[ -f "$SSH_KEY" ]]; then
  SSH_OPTS="${SSH_OPTS} -i ${SSH_KEY}"
fi

echo "==> Sincronizando hacia ${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_TARGET}/ ..."
ssh $SSH_OPTS "${REMOTE_USER}@${REMOTE_HOST}" "mkdir -p '${REMOTE_TARGET}'"

rsync -avz --delete \
  --exclude=".git" \
  --exclude=".github" \
  --exclude=".agents" \
  --exclude="node_modules" \
  --exclude=".next" \
  --exclude="target" \
  --exclude="__pycache__" \
  -e "ssh ${SSH_OPTS}" \
  "$SYNC_SRC" "${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_TARGET}/"

ssh $SSH_OPTS "${REMOTE_USER}@${REMOTE_HOST}" "find '${REMOTE_TARGET}' -name '*.cgi' -exec chmod 755 {} +"

echo "==> ¡Despliegue completado!"
echo "    Micro-App URL : https://carlossilveira.dev/lab/${SLUG}/"
echo "    Panel Privado : https://carlossilveira.dev/lab/"
