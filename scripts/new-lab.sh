#!/usr/bin/env bash
set -euo pipefail

# ==============================================================================
# Carlos Silveira Playground Kit — Lab Scaffolding CLI (`new-lab.sh`)
# Usage: ./scripts/new-lab.sh <stack> <slug>
# Example: ./scripts/new-lab.sh python-django shopify-store
# ==============================================================================

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
KIT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
WORKSPACE_ROOT="$(cd "$KIT_ROOT/.." && pwd)"

usage() {
  cat <<EOF
Usage: ./scripts/new-lab.sh <stack> <slug>

Available stacks (Liquid Glass Design System + app.json pre-configured):
  python-django   Python 3.12 WSGI / CGI / Django-Ready Micro-App (/opt/alt/python312/bin/python3)
  nextjs-react    Next.js 15 / React 19 Static Export (GitHub Actions build)
  php-api         PHP 8.3 LiteSpeed API & Micro-App (/opt/alt/php83/usr/bin/php)
  go-cgi          Go 1.23 Hybrid HTTP/CGI Static Linux Binary (GOOS=linux GOARCH=amd64)
  rust-cgi        Rust 2021 Static Musl CGI Binary (x86_64-unknown-linux-musl)
  ruby-rack       Ruby 3.3 Rack / CGI Micro-App (/opt/alt/ruby33/bin/ruby)
  java-graalvm    Java 21 GraalVM Native Image Binary (native-image --static --libc=musl)

Example:
  ./scripts/new-lab.sh python-django shopify-store
  ./scripts/deploy-lab.sh ../lab-shopify-store shopify-store
EOF
  exit 1
}

if [[ $# -lt 2 ]]; then
  usage
fi

STACK="$1"
SLUG="$2"
STARTER_DIR="$KIT_ROOT/starters/$STACK"
TARGET_DIR="$WORKSPACE_ROOT/lab-$SLUG"

if [[ ! -d "$STARTER_DIR" ]]; then
  echo "Error: Unknown stack '$STACK'. Starter directory not found at $STARTER_DIR" >&2
  usage
fi

if [[ ! "$SLUG" =~ ^[a-z0-9][a-z0-9-]*$ ]]; then
  echo "Error: <slug> must be lowercase alphanumeric with hyphens (e.g. 'shopify-store')." >&2
  exit 1
fi

if [[ -e "$TARGET_DIR" ]]; then
  echo "Error: Target directory '$TARGET_DIR' already exists." >&2
  exit 1
fi

echo "==> Scaffolding new '$STACK' micro-app at: $TARGET_DIR"
mkdir -p "$TARGET_DIR"

# 1. Copy starter template files (including hidden files like .htaccess, .github, and app.json)
cp -R "$STARTER_DIR/." "$TARGET_DIR/"

# 2. Copy shared AI Agent skills (.agents/) and brand assets
mkdir -p "$TARGET_DIR/.agents"
cp -R "$KIT_ROOT/.agents/." "$TARGET_DIR/.agents/"

if [[ ! -f "$TARGET_DIR/AGENTS.md" ]]; then
  cp "$KIT_ROOT/AGENTS.md" "$TARGET_DIR/AGENTS.md"
fi

cat > "$TARGET_DIR/GEMINI.md" <<EOF
# GEMINI.md — AI Context for \`lab-$SLUG\`

See [\`AGENTS.md\`](./AGENTS.md) for mandatory Git identity (\`Carlos Silveira <carlos@personal.me>\`), remote (\`git@github-personal:carlos-silveira/lab-$SLUG.git\`), Liquid Glass Design System (\`.agents/skills/liquid-glass-design/SKILL.md\`), and Spaceship deployment rules targeting \`https://carlossilveira.dev/lab/$SLUG/\`.
EOF

# 3. Initialize docs/AI_ACTIVITY.md
mkdir -p "$TARGET_DIR/docs"
TIMESTAMP="$(date -u +"%Y-%m-%dT%H:%M:%SZ")"
TODAY="$(date -u +"%Y-%m-%d")"
cat > "$TARGET_DIR/docs/AI_ACTIVITY.md" <<EOF
# AI Activity Log — \`lab-$SLUG\`

All AI agent actions and modifications in this repository are recorded below in reverse-chronological order.

## $TIMESTAMP — Repository Scaffolded via \`new-lab.sh\`
- **Stack**: \`$STACK\`
- **Live Target**: \`https://carlossilveira.dev/lab/$SLUG/\`
- **Remote**: \`git@github-personal:carlos-silveira/lab-$SLUG.git\`
- **Details**: Initialized from \`carlossilveira-playground-kit/starters/$STACK\` with Liquid Glass Design System, \`app.json\`, CloudLinux runtime configuration, \`.agents/skills/\`, and GitHub Actions CI/CD workflow.
EOF

# 4. Generate stack-specific .github/workflows/deploy.yml if not already provided by starter
mkdir -p "$TARGET_DIR/.github/workflows"
if [[ ! -f "$TARGET_DIR/.github/workflows/deploy.yml" ]]; then
  case "$STACK" in
    nextjs-react)
      cat > "$TARGET_DIR/.github/workflows/deploy.yml" <<'YAML'
name: Build & Deploy Next.js Micro-App to Spaceship

on:
  push:
    branches: [main]
  workflow_dispatch:

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - name: Checkout repository
        uses: actions/checkout@v4

      - name: Set up Node.js 22
        uses: actions/setup-node@v4
        with:
          node-version: '22'

      - name: Install dependencies & build static export
        run: |
          npm install
          npm run build
          cp app.json out/app.json

      - name: Deploy out/ to Spaceship server58 (/lab/__LAB_SLUG__/)
        env:
          SSH_PRIVATE_KEY: ${{ secrets.SPACESHIP_SSH_KEY }}
          REMOTE_HOST: server58.shared.spaceship.host
          REMOTE_PORT: 21098
          REMOTE_USER: bfjydpuzth
          REMOTE_DIR: /home/bfjydpuzth/carlossilveira.dev/lab/__LAB_SLUG__
        run: |
          mkdir -p ~/.ssh
          echo "$SSH_PRIVATE_KEY" > ~/.ssh/id_rsa
          chmod 600 ~/.ssh/id_rsa
          ssh-keyscan -p "$REMOTE_PORT" "$REMOTE_HOST" >> ~/.ssh/known_hosts
          ssh -p "$REMOTE_PORT" "$REMOTE_USER@$REMOTE_HOST" "mkdir -p $REMOTE_DIR"
          rsync -avz --delete -e "ssh -p $REMOTE_PORT" \
            out/ \
            "$REMOTE_USER@$REMOTE_HOST:$REMOTE_DIR/"
YAML
      ;;
    *)
      cat > "$TARGET_DIR/.github/workflows/deploy.yml" <<'YAML'
name: Deploy Micro-App (__LAB_SLUG__) to Spaceship

on:
  push:
    branches: [main]
  workflow_dispatch:

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - name: Checkout repository
        uses: actions/checkout@v4

      - name: Sync files to Spaceship server58 (/lab/__LAB_SLUG__/)
        env:
          SSH_PRIVATE_KEY: ${{ secrets.SPACESHIP_SSH_KEY }}
          REMOTE_HOST: server58.shared.spaceship.host
          REMOTE_PORT: 21098
          REMOTE_USER: bfjydpuzth
          REMOTE_DIR: /home/bfjydpuzth/carlossilveira.dev/lab/__LAB_SLUG__
        run: |
          mkdir -p ~/.ssh
          echo "$SSH_PRIVATE_KEY" > ~/.ssh/id_rsa
          chmod 600 ~/.ssh/id_rsa
          ssh-keyscan -p "$REMOTE_PORT" "$REMOTE_HOST" >> ~/.ssh/known_hosts
          ssh -p "$REMOTE_PORT" "$REMOTE_USER@$REMOTE_HOST" "mkdir -p $REMOTE_DIR"
          rsync -avz --delete \
            --exclude=".git" \
            --exclude=".github" \
            --exclude=".agents" \
            --exclude="docs" \
            -e "ssh -p $REMOTE_PORT" \
            ./ "$REMOTE_USER@$REMOTE_HOST:$REMOTE_DIR/"
          ssh -p "$REMOTE_PORT" "$REMOTE_USER@$REMOTE_HOST" "find $REMOTE_DIR -name '*.cgi' -exec chmod 755 {} +"
YAML
      ;;
  esac
fi

# 5. Replace placeholders (__LAB_SLUG__, __LAB_DATE__, and nextjs-demo) across all text files in target directory
while IFS= read -r -d '' file; do
  LC_ALL=C sed -i '' \
    -e "s/__LAB_SLUG__/${SLUG}/g" \
    -e "s/__LAB_DATE__/${TODAY}/g" \
    -e "s/nextjs-demo/${SLUG}/g" \
    "$file"
done < <(find "$TARGET_DIR" -type f -not -path '*/.git/*' -print0)

# Ensure any .cgi scripts are executable
find "$TARGET_DIR" -name "*.cgi" -exec chmod 755 {} +

# 6. Initialize Git repository with mandatory personal identity and remote
(
  cd "$TARGET_DIR"
  git init -b main >/dev/null 2>&1 || git init >/dev/null 2>&1
  git config user.name "Carlos Silveira"
  git config user.email "carlos@personal.me"
  git remote add origin "git@github-personal:carlos-silveira/lab-${SLUG}.git"
)

echo "==> Done! Micro-App repository initialized at: $TARGET_DIR"
echo "    Git Identity : Carlos Silveira <carlos@personal.me>"
echo "    Git Remote   : git@github-personal:carlos-silveira/lab-${SLUG}.git"
echo "    Deploy Now   : ./scripts/deploy-lab.sh \"$TARGET_DIR\" $SLUG"
echo "    Live Target  : https://carlossilveira.dev/lab/${SLUG}/"
