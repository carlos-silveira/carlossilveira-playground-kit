<?php
declare(strict_types=1);

/**
 * Carlos Silveira — Private Micro-Apps Launchpad & Manager (`public/index.php`)
 * Live URL: https://carlossilveira.dev/lab/
 * Protected by `public/auth.php` (WordPress Admin session or direct credentials).
 */

require_once __DIR__ . '/auth.php';

/**
 * Scan `/lab/` subdirectories for `app.json` or active entrypoints.
 *
 * @return array<int, array<string, mixed>>
 */
function cs_discover_micro_apps(string $baseDir): array {
    $apps = [];
    $entries = scandir($baseDir) ?: [];

    foreach ($entries as $dir) {
        if ($dir === '.' || $dir === '..' || $dir === 'assets' || str_starts_with($dir, '.')) {
            continue;
        }
        $fullPath = $baseDir . '/' . $dir;
        if (!is_dir($fullPath)) {
            continue;
        }

        $appJsonFile = $fullPath . '/app.json';
        if (is_file($appJsonFile)) {
            $decoded = json_decode((string) file_get_contents($appJsonFile), true);
            if (is_array($decoded)) {
                $decoded['slug'] = $decoded['slug'] ?? $dir;
                $decoded['entry_url'] = $decoded['entry_url'] ?? ('./' . $dir . '/');
                $apps[] = $decoded;
                continue;
            }
        }

        // Fallback auto-discovery if a folder has index.php / index.cgi / index.html without app.json
        if (is_file($fullPath . '/index.php') || is_file($fullPath . '/index.cgi') || is_file($fullPath . '/index.html')) {
            $stack = is_file($fullPath . '/passenger_wsgi.py')
                ? 'Django / Python 3.12'
                : (is_file($fullPath . '/config.ru')
                    ? 'Ruby 3.3 / Rack'
                    : (is_file($fullPath . '/index.php') ? 'PHP 8.3' : 'Web Micro-App'));

            $apps[] = [
                'name' => ucwords(str_replace('-', ' ', $dir)),
                'slug' => $dir,
                'stack' => $stack,
                'category' => 'Micro-App',
                'status' => 'Activa',
                'description' => 'Micro-aplicación desplegada en /lab/' . $dir . '/',
                'local_path' => '/home/bfjydpuzth/carlossilveira.dev/lab/' . $dir,
                'entry_url' => './' . $dir . '/',
                'updated_at' => gmdate('Y-m-d', filemtime($fullPath) ?: time()),
            ];
        }
    }

    // Put shopify-django-app first as featured micro-app
    usort($apps, static function (array $a, array $b): int {
        if (($a['slug'] ?? '') === 'shopify-django-app') {
            return -1;
        }
        if (($b['slug'] ?? '') === 'shopify-django-app') {
            return 1;
        }
        return strcmp((string) ($a['name'] ?? ''), (string) ($b['name'] ?? ''));
    });

    return $apps;
}

$microApps = cs_discover_micro_apps(__DIR__);

if (isset($_GET['format']) && $_GET['format'] === 'json') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'ok' => true,
        'workspace' => 'carlossilveira.dev/lab/',
        'authenticated_user' => $csLabUser,
        'micro_apps_count' => count($microApps),
        'micro_apps' => $microApps,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <meta name="color-scheme" content="light dark">
  <script>
    try {
      document.querySelector('meta[name="color-scheme"]').content = localStorage.getItem("color-scheme") ?? "light dark";
    } catch (_) {}
  </script>
  <title>Mis Micro-Apps — Carlos Silveira Private Lab</title>
  <meta name="description" content="Panel privado de micro-aplicaciones y despliegues rápidos de Carlos Silveira en carlossilveira.dev/lab/.">

  <!-- Favicon (Uses CS logo-mark ONLY) -->
  <link rel="icon" type="image/svg+xml" href="./assets/brand/logo-mark.svg">
  <link rel="apple-touch-icon" href="./assets/brand/logo-mark.svg">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/liquid-glass.css">
  <style>
    .hero-banner {
      display: grid;
      grid-template-columns: 1.35fr 0.65fr;
      gap: 24px;
      margin: 32px 0;
      align-items: stretch;
    }
    @media (max-width: 980px) {
      .hero-banner {
        grid-template-columns: 1fr;
      }
    }
    .kpi-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 14px;
    }
    .kpi-box {
      padding: 18px;
      border-radius: 20px;
      background: var(--lg-surface-elevated);
      border: 1px solid var(--lg-border);
      box-shadow: var(--lg-specular);
    }
    .kpi-num {
      font-family: var(--font-display);
      font-size: 28px;
      font-weight: 800;
      color: var(--text-primary);
      line-height: 1.1;
    }
    .kpi-label {
      font-size: 13px;
      color: var(--text-secondary);
      margin-top: 4px;
    }
    .app-card {
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      gap: 20px;
    }
    .app-meta-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
      margin-bottom: 12px;
    }
    .app-path-box {
      font-family: var(--font-mono);
      font-size: 12px;
      padding: 10px 14px;
      border-radius: 14px;
      background: var(--lg-surface-elevated);
      border: 1px solid var(--lg-border);
      color: var(--text-secondary);
      word-break: break-all;
      margin-top: 12px;
    }
    .deploy-grid {
      display: grid;
      grid-template-columns: 1fr 1.15fr;
      gap: 24px;
      align-items: stretch;
    }
    @media (max-width: 920px) {
      .deploy-grid {
        grid-template-columns: 1fr;
      }
    }
    .terminal-glass {
      background: rgba(7, 11, 20, 0.84);
      color: #f8fafc;
      border-radius: 22px;
      padding: 22px;
      font-family: var(--font-mono);
      font-size: 13px;
      line-height: 1.65;
      border: 1px solid rgba(255, 255, 255, 0.16);
      box-shadow: inset 0 1.5px 1px 0 rgba(255, 255, 255, 0.2);
      overflow-x: auto;
    }
  </style>
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
  <!-- High-Performance Living Ambient Mesh Backdrop -->
  <div class="lg-ambient-canvas" aria-hidden="true"></div>

  <!-- Unified Floating Liquid Glass Navbar -->
  <header class="cs-site-header">
    <div class="cs-container-wide">
      <div class="cs-nav-shell" id="cs-nav-shell">
        <div class="cs-nav-bar">
          <!-- Brand Wordmark -->
          <a href="/lab/" class="cs-brand-link" aria-label="Carlos Silveira — Labs">
            <span class="cs-brand-wordmark">
              <span class="cs-brand-first">Carlos</span>
              <span class="cs-brand-last">Silveira</span>
              <span class="cs-brand-badge">.lab</span>
            </span>
          </a>

          <!-- Desktop Navigation Links -->
          <nav class="cs-nav-desktop" aria-label="Navegación Principal">
            <a href="/" class="cs-nav-link">
              Blog
            </a>
            <a href="/#articulos" class="cs-nav-link">
              Artículos
            </a>
            <a href="/tools/" class="cs-nav-link">
              Herramientas
            </a>
            <a href="/lab/" class="cs-nav-link is-active" aria-current="page">
              Mis Labs
            </a>
            <a href="#deploy-workflow" class="cs-nav-link">
              Desplegar Proyecto
            </a>
          </nav>

          <!-- Actions: Theme Toggle + Logout + Mobile Menu Button -->
          <div class="cs-nav-actions">
            <button
              type="button"
              class="cs-theme-toggle"
              id="cs-theme-toggle"
              data-theme-toggle
              aria-label="Cambiar tema claro / oscuro"
              title="Cambiar tema"
            >
              <span class="cs-theme-icon-dark" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                </svg>
              </span>
              <span class="cs-theme-icon-light" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="5"/>
                  <line x1="12" y1="1" x2="12" y2="3"/>
                  <line x1="12" y1="21" x2="12" y2="23"/>
                  <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
                  <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                  <line x1="1" y1="12" x2="3" y2="12"/>
                  <line x1="21" y1="12" x2="23" y2="12"/>
                  <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
                  <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                </svg>
              </span>
            </button>

            <a href="?logout=1" class="lg-btn lg-btn-secondary cs-hide-mobile" title="Sesión activa: <?= htmlspecialchars((string) $csLabUser) ?>" style="min-height: 40px; padding: 6px 14px; font-size: 13px;">
              Salir (<?= htmlspecialchars((string) $csLabUser) ?>)
            </a>

            <button
              type="button"
              class="cs-mobile-menu-btn"
              id="cs-mobile-toggle"
              data-mobile-toggle
              aria-label="Abrir menú"
              aria-expanded="false"
            >
              <svg class="cs-icon-hamburger" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="6" x2="21" y2="6"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
              </svg>
              <svg class="cs-icon-close" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Mobile Drawer -->
        <div class="cs-mobile-drawer" id="cs-mobile-drawer" aria-hidden="true">
          <nav class="cs-mobile-nav">
            <a href="/" class="cs-mobile-link">
              <span class="cs-mobile-icon"><i data-lucide="newspaper" style="width:16px;height:16px;"></i></span>
              <span>Blog</span>
            </a>
            <a href="/#articulos" class="cs-mobile-link">
              <span class="cs-mobile-icon"><i data-lucide="book-open" style="width:16px;height:16px;"></i></span>
              <span>Artículos</span>
            </a>
            <a href="/tools/" class="cs-mobile-link">
              <span class="cs-mobile-icon"><i data-lucide="calculator" style="width:16px;height:16px;"></i></span>
              <span>Herramientas (/tools/)</span>
            </a>
            <a href="/lab/" class="cs-mobile-link is-active" aria-current="page">
              <span class="cs-mobile-icon"><i data-lucide="lock" style="width:16px;height:16px;"></i></span>
              <span>Mis Labs</span>
            </a>
            <a href="#deploy-workflow" class="cs-mobile-link">
              <span class="cs-mobile-icon"><i data-lucide="rocket" style="width:16px;height:16px;"></i></span>
              <span>Desplegar Proyecto</span>
            </a>
            <a href="?logout=1" class="cs-mobile-link" style="color: var(--danger);">
              <span class="cs-mobile-icon"><i data-lucide="log-out" style="width:16px;height:16px;"></i></span>
              <span>Cerrar sesión (<?= htmlspecialchars((string) $csLabUser) ?>)</span>
            </a>
          </nav>
        </div>
      </div>
    </div>
  </header>

  <main class="lg-container" style="padding-bottom: 96px;">
    <!-- Hero Launchpad Banner -->
    <section class="hero-banner">
      <div class="lg-card" style="display:flex; flex-direction:column; justify-content:space-between;">
        <div>
          <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin-bottom:14px;">
            <span class="lg-badge lg-badge-success"><i data-lucide="lock" style="width:13px;height:13px;display:inline-block;vertical-align:middle;margin-right:4px;"></i> Sesión Privada Activa</span>
            <span class="lg-badge">Liquid Glass Design System</span>
          </div>
          <h1 class="lg-hero-title">
            Tus Micro-Apps en <span class="lg-gradient-text">carlossilveira.dev/lab/</span>
          </h1>
          <p style="color:var(--text-secondary); font-size:17px; max-width:760px; margin:14px 0 0;">
            Programa cualquier idea en tu Mac —como una tienda de Shopify en Django, un SaaS en Next.js, un bot en Python o una API en Go— y publícala como micro-app dentro de tu propio sitio en segundos con <code>./scripts/deploy-lab.sh</code>.
          </p>
        </div>

        <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:24px;">
          <a href="./shopify-django-app/" class="lg-btn lg-btn-primary">
            Abrir Demo: Tienda Shopify en Django &rarr;
          </a>
          <a href="#deploy-workflow" class="lg-btn lg-btn-secondary">
            + Desplegar Nueva Micro-App
          </a>
        </div>
      </div>

      <div class="lg-card" style="display:flex; flex-direction:column; justify-content:space-between;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
          <span style="font-family:var(--font-display); font-weight:800; font-size:16px;">Resumen del Entorno</span>
          <img src="./assets/brand/logo-mark.svg" alt="CS" width="38" height="38">
        </div>
        <div class="kpi-grid">
          <div class="kpi-box">
            <div class="kpi-num"><?= count($microApps) ?></div>
            <div class="kpi-label">Micro-Apps Activas</div>
          </div>
          <div class="kpi-box">
            <div class="kpi-num">Privado</div>
            <div class="kpi-label">Acceso con Auth + NoIndex</div>
          </div>
          <div class="kpi-box">
            <div class="kpi-num">7 Stacks</div>
            <div class="kpi-label">Django, Next.js, Go, Rust, PHP, Ruby, Java</div>
          </div>
          <div class="kpi-box">
            <div class="kpi-num">1 Comando</div>
            <div class="kpi-label"><code>deploy-lab.sh</code></div>
          </div>
        </div>
      </div>
    </section>

    <!-- Active Micro-Apps Horizontal Grid -->
    <section style="margin-top: 44px;" id="mis-micro-apps">
      <div style="display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:16px; margin-bottom:24px;">
        <div>
          <span class="lg-badge">Directorio Automático (`app.json`)</span>
          <h2 class="lg-section-title" style="margin-top:10px;">Mis Micro-Apps Activas (`/lab/<proyecto>/`)</h2>
        </div>
        <div style="color:var(--text-secondary); font-size:14px;">
          Cualquier carpeta con <code>app.json</code> dentro de <code>/lab/</code> aparece automáticamente aquí.
        </div>
      </div>

      <div class="lg-grid-3">
        <?php foreach ($microApps as $app): ?>
          <?php
            $slug = (string) ($app['slug'] ?? '');
            $name = (string) ($app['name'] ?? $slug);
            $stack = (string) ($app['stack'] ?? 'Micro-App');
            $category = (string) ($app['category'] ?? 'Proyecto');
            $status = (string) ($app['status'] ?? 'Activa');
            $desc = (string) ($app['description'] ?? '');
            $entryUrl = (string) ($app['entry_url'] ?? ('./' . $slug . '/'));
            $localPath = (string) ($app['local_path'] ?? ('/lab/' . $slug));
          ?>
          <article class="lg-card app-card">
            <div>
              <div class="app-meta-row">
                <span class="lg-badge"><?= htmlspecialchars($stack) ?></span>
                <span class="lg-badge lg-badge-success">&#9679; <?= htmlspecialchars($status) ?></span>
              </div>

              <h3 style="font-size:22px; font-weight:800; margin-bottom:8px;">
                <?= htmlspecialchars($name) ?>
              </h3>
              <p style="color:var(--text-secondary); font-size:14.5px; margin:0;">
                <?= htmlspecialchars($desc) ?>
              </p>

              <div class="app-path-box">
                <div><strong>URL:</strong> <code>/lab/<?= htmlspecialchars($slug) ?>/</code></div>
                <div style="margin-top:4px;"><strong>Categoría:</strong> <?= htmlspecialchars($category) ?></div>
              </div>
            </div>

            <div style="display:flex; gap:10px; flex-wrap:wrap;">
              <a href="<?= htmlspecialchars($entryUrl) ?>" class="lg-btn lg-btn-primary">
                Abrir Micro-App &rarr;
              </a>
              <button
                type="button"
                class="lg-btn lg-btn-secondary"
                data-probe-url="<?= htmlspecialchars($entryUrl) ?>?format=json"
                data-probe-name="<?= htmlspecialchars($name) ?>"
              >
                Probar JSON
              </button>
            </div>
          </article>
        <?php endforeach; ?>
      </div>

      <!-- Live JSON Inspector Drawer -->
      <div class="lg-card" style="margin-top: 24px;" id="json-inspector-card" hidden>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
          <strong style="font-family:var(--font-display);" id="probe-title">Respuesta JSON de la Micro-App</strong>
          <span class="lg-badge" id="probe-time">Listo</span>
        </div>
        <pre class="terminal-glass" id="probe-json" style="margin:0;"></pre>
      </div>
    </section>

    <!-- 1-Command Deploy & Scaffolding Workflow -->
    <section style="margin-top: 56px;" id="deploy-workflow">
      <div style="margin-bottom: 24px;">
        <span class="lg-badge">Flujo de Trabajo Rápido</span>
        <h2 class="lg-section-title" style="margin-top:10px;">Crea o Despliega una Micro-App en Segundos</h2>
        <p style="color:var(--text-secondary); max-width:780px; margin:6px 0 0;">
          Elige el stack y el nombre de tu proyecto para generar el comando exacto de creación (`new-lab.sh`) y el comando de despliegue directo a `carlossilveira.dev/lab/<slug>/` (`deploy-lab.sh`).
        </p>
      </div>

      <div class="deploy-grid">
        <div class="lg-card">
          <div style="margin-bottom:16px;">
            <label style="display:block; font-family:var(--font-display); font-size:13px; font-weight:700; margin-bottom:8px;" for="stack-select">
              1. Stack de tu Micro-App
            </label>
            <select id="stack-select" class="lg-select">
              <option value="python-django">Django / Python 3.12 (Ej. Tienda Shopify, IA, Scrapers)</option>
              <option value="nextjs-react">Next.js 15 / React 19 (Dashboards, SaaS Frontends)</option>
              <option value="php-api">PHP 8.3 LiteSpeed (Webhooks, APIs, Micro-Sitios)</option>
              <option value="go-cgi">Go 1.23 Binario Estático (Microservicios Ultra-Rápidos)</option>
              <option value="rust-cgi">Rust 2021 Musl (APIs de Alto Rendimiento)</option>
              <option value="ruby-rack">Ruby 3.3 Rack / Sinatra (Apps &amp; Automatización)</option>
              <option value="java-graalvm">Java 21 GraalVM Native Image</option>
            </select>
          </div>

          <div style="margin-bottom:16px;">
            <label style="display:block; font-family:var(--font-display); font-size:13px; font-weight:700; margin-bottom:8px;" for="slug-input">
              2. Slug de la URL (`/lab/&lt;slug&gt;/`)
            </label>
            <input id="slug-input" class="lg-input" type="text" value="mi-tienda-shopify" placeholder="ej. mi-tienda-shopify">
          </div>

          <div style="margin-bottom:20px;">
            <label style="display:block; font-family:var(--font-display); font-size:13px; font-weight:700; margin-bottom:8px;" for="local-folder-input">
              3. Carpeta Local en tu Mac (para `deploy-lab.sh`)
            </label>
            <input id="local-folder-input" class="lg-input" type="text" value="/Users/carlossilveira/Documents/Personal/carlossilveiradev/lab-mi-tienda-shopify">
          </div>

          <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <button type="button" class="lg-btn lg-btn-primary" id="copy-deploy-btn">
              Copiar Comando de Deploy
            </button>
            <button type="button" class="lg-btn lg-btn-secondary" id="copy-new-btn">
              Copiar Comando new-lab.sh
            </button>
          </div>
        </div>

        <div class="lg-card" style="display:flex; flex-direction:column; justify-content:space-between;">
          <div>
            <div style="font-family:var(--font-display); font-weight:700; font-size:15px; margin-bottom:12px;">
              Comandos Listos para tu Terminal
            </div>
            <pre class="terminal-glass" id="cmd-output"># Opción A: Crear proyecto nuevo desde plantilla Liquid Glass
./scripts/new-lab.sh python-django mi-tienda-shopify

# Opción B: Desplegar cualquier carpeta de tu Mac directo a /lab/mi-tienda-shopify/
./scripts/deploy-lab.sh /Users/carlossilveira/Documents/Personal/carlossilveiradev/lab-mi-tienda-shopify mi-tienda-shopify</pre>
          </div>
          <div style="font-size:13.5px; color:var(--text-secondary); margin-top:14px;">
            Al ejecutar <code>./scripts/deploy-lab.sh</code>, el script genera automáticamente <code>app.json</code> si no existe, compila si es Go/Rust/Next.js, sincroniza los archivos por SSH a <code>server58</code> y la micro-app aparece al instante en este panel en <code>https://carlossilveira.dev/lab/<span id="live-slug-preview">mi-tienda-shopify</span>/</code>.
          </div>
        </div>
      </div>
    </section>
  </main>

  <script src="./assets/js/liquid-glass.js"></script>
  <script>
    (function () {
      // Live JSON Probe
      const inspectorCard = document.getElementById('json-inspector-card');
      const probeTitle = document.getElementById('probe-title');
      const probeTime = document.getElementById('probe-time');
      const probeJson = document.getElementById('probe-json');

      document.querySelectorAll('[data-probe-url]').forEach(function (btn) {
        btn.addEventListener('click', async function () {
          const url = btn.getAttribute('data-probe-url');
          const name = btn.getAttribute('data-probe-name');
          inspectorCard.hidden = false;
          probeTitle.textContent = name + ' — Consultando ' + url + '...';
          probeTime.textContent = 'Cargando...';
          const t0 = performance.now();
          try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            const ms = (performance.now() - t0).toFixed(1);
            const data = await res.json();
            probeTitle.textContent = name + ' (HTTP ' + res.status + ')';
            probeTime.textContent = ms + ' ms';
            probeJson.textContent = JSON.stringify(data, null, 2);
          } catch (err) {
            probeTitle.textContent = name + ' — Vista Local';
            probeTime.textContent = '';
            probeJson.textContent = JSON.stringify({
              endpoint: url,
              detail: String(err)
            }, null, 2);
          }
        });
      });

      // Deploy & Scaffolding Command Generator
      const stackSelect = document.getElementById('stack-select');
      const slugInput = document.getElementById('slug-input');
      const folderInput = document.getElementById('local-folder-input');
      const cmdOutput = document.getElementById('cmd-output');
      const liveSlugPreview = document.getElementById('live-slug-preview');
      const copyDeployBtn = document.getElementById('copy-deploy-btn');
      const copyNewBtn = document.getElementById('copy-new-btn');

      let userEditedFolder = false;
      folderInput.addEventListener('input', function () { userEditedFolder = true; });

      function getCleanSlug() {
        return slugInput.value.trim().toLowerCase().replace(/[^a-z0-9-]/g, '-') || 'mi-micro-app';
      }

      function updateCommands() {
        const stack = stackSelect.value;
        const slug = getCleanSlug();
        if (!userEditedFolder) {
          folderInput.value = '/Users/carlossilveira/Documents/Personal/carlossilveiradev/lab-' + slug;
        }
        const folder = folderInput.value.trim() || ('/Users/carlossilveira/Documents/Personal/carlossilveiradev/lab-' + slug);
        liveSlugPreview.textContent = slug;
        cmdOutput.textContent =
          '# Opción A: Crear proyecto nuevo desde plantilla Liquid Glass\n' +
          './scripts/new-lab.sh ' + stack + ' ' + slug + '\n\n' +
          '# Opción B: Desplegar cualquier carpeta de tu Mac directo a /lab/' + slug + '/\n' +
          './scripts/deploy-lab.sh "' + folder + '" ' + slug;
      }

      stackSelect.addEventListener('change', updateCommands);
      slugInput.addEventListener('input', updateCommands);
      folderInput.addEventListener('input', updateCommands);

      copyDeployBtn.addEventListener('click', async function () {
        const slug = getCleanSlug();
        const folder = folderInput.value.trim();
        const cmd = './scripts/deploy-lab.sh "' + folder + '" ' + slug;
        try {
          await navigator.clipboard.writeText(cmd);
          const orig = copyDeployBtn.textContent;
          copyDeployBtn.textContent = '¡Comando Copiado!';
          setTimeout(function () { copyDeployBtn.textContent = orig; }, 1600);
        } catch (_) {}
      });

      copyNewBtn.addEventListener('click', async function () {
        const slug = getCleanSlug();
        const stack = stackSelect.value;
        const cmd = './scripts/new-lab.sh ' + stack + ' ' + slug;
        try {
          await navigator.clipboard.writeText(cmd);
          const orig = copyNewBtn.textContent;
          copyNewBtn.textContent = '¡Copiado!';
          setTimeout(function () { copyNewBtn.textContent = orig; }, 1600);
        } catch (_) {}
      });
    })();
  </script>
  <script>if (window.lucide) { lucide.createIcons(); }</script>
</body>
</html>
