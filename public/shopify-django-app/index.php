<?php
declare(strict_types=1);

/**
 * Carlos Silveira Private Lab — Micro-App: Shopify Headless Storefront & Webhook Studio (Django Architecture)
 * Live URL: https://carlossilveira.dev/lab/shopify-django-app/
 */

require_once dirname(__DIR__) . '/auth.php';

$shopDomain = 'carlos-silveira-dev.myshopify.com';
$webhookSecret = 'shpss_cs_liquid_glass_shopify_2026_secret';

$products = [
    [
        'id' => 'gid://shopify/Product/891023411',
        'title' => 'Teclado Mecánico Aluminio CNC 75%',
        'handle' => 'teclado-mecanico-aluminio-cnc-75',
        'sku' => 'CS-KB75-ALU',
        'price_mxn' => 3490.00,
        'inventory' => 18,
        'status' => 'ACTIVE',
        'collection' => 'Hardware & Setup',
    ],
    [
        'id' => 'gid://shopify/Product/891023412',
        'title' => 'Monitor Ultrawide 34" Nano-IPS 144Hz',
        'handle' => 'monitor-ultrawide-34-nano-ips',
        'sku' => 'CS-MON34-UW',
        'price_mxn' => 11850.00,
        'inventory' => 7,
        'status' => 'ACTIVE',
        'collection' => 'Displays',
    ],
    [
        'id' => 'gid://shopify/Product/891023413',
        'title' => 'Dock Thunderbolt 4 Pro (Dual 6K + 96W PD)',
        'handle' => 'dock-thunderbolt-4-pro',
        'sku' => 'CS-TB4-DOCK',
        'price_mxn' => 5290.00,
        'inventory' => 24,
        'status' => 'ACTIVE',
        'collection' => 'Conectividad',
    ],
];

// AJAX / JSON API endpoint for simulating Shopify Webhook HMAC verification & Checkout creation
if ((isset($_GET['format']) && $_GET['format'] === 'json') || isset($_GET['action'])) {
    header('Content-Type: application/json; charset=utf-8');
    $action = (string) ($_GET['action'] ?? 'status');

    if ($action === 'simulate_webhook') {
        $rawInput = file_get_contents('php://input') ?: '{}';
        $topic = $_GET['topic'] ?? 'orders/create';
        $hmac = base64_encode(hash_hmac('sha256', $rawInput, $webhookSecret, true));
        echo json_encode([
            'ok' => true,
            'shop_domain' => $shopDomain,
            'topic' => $topic,
            'headers' => [
                'X-Shopify-Topic' => $topic,
                'X-Shopify-Shop-Domain' => $shopDomain,
                'X-Shopify-Hmac-Sha256' => $hmac,
                'X-Shopify-API-Version' => '2026-01',
            ],
            'hmac_verified' => true,
            'django_handler' => 'shopify_app.views.shopify_webhook_receiver',
            'processed_at' => gmdate('c'),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }

    echo json_encode([
        'ok' => true,
        'app' => 'shopify-django-app',
        'stack' => 'Django 5.1 / Python 3.12',
        'shop_domain' => $shopDomain,
        'products_count' => count($products),
        'products' => $products,
        'authenticated_user' => $csLabUser ?? 'carlos',
        'timestamp' => gmdate('c'),
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
  <title>Shopify Storefront &amp; Webhook Studio (Django) — Carlos Silveira Lab</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/liquid-glass.css">
  <style>
    .shop-layout {
      display: grid;
      grid-template-columns: 1.35fr 0.85fr;
      gap: 24px;
      margin-top: 28px;
    }
    @media (max-width: 980px) {
      .shop-layout {
        grid-template-columns: 1fr;
      }
    }
    .product-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
      gap: 18px;
      margin-top: 18px;
    }
    .product-item {
      padding: 20px;
      border-radius: 20px;
      background: var(--lg-surface-elevated);
      border: 1px solid var(--lg-border);
      box-shadow: var(--lg-specular);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      gap: 14px;
    }
    .price-tag {
      font-family: var(--font-display);
      font-size: 22px;
      font-weight: 800;
      color: var(--text-primary);
    }
    .code-box {
      background: rgba(7, 11, 20, 0.82);
      color: #e2e8f0;
      border-radius: 18px;
      padding: 18px;
      font-family: var(--font-mono);
      font-size: 12.5px;
      line-height: 1.55;
      overflow-x: auto;
      border: 1px solid rgba(255, 255, 255, 0.14);
    }
  </style>
</head>
<body>
  <svg width="0" height="0" style="position:absolute; pointer-events:none;" aria-hidden="true">
    <defs>
      <filter id="lg-refraction" x="-10%" y="-10%" width="120%" height="120%">
        <feTurbulence type="fractalNoise" baseFrequency="0.015" numOctaves="2" result="noise"/>
        <feDisplacementMap in="SourceGraphic" in2="noise" scale="4" xChannelSelector="R" yChannelSelector="G"/>
      </filter>
    </defs>
  </svg>

  <div class="lg-ambient-canvas" aria-hidden="true">
    <div class="lg-orb lg-orb-sapphire"></div>
    <div class="lg-orb lg-orb-indigo"></div>
    <div class="lg-orb lg-orb-aqua"></div>
    <div class="lg-orb lg-orb-rose"></div>
  </div>

  <header class="lg-navbar">
    <a href="/lab/" class="lg-brand-link">
      <img src="../assets/brand/logo-combined.svg" alt="Carlos Silveira .dev">
    </a>
    <nav class="lg-nav-links">
      <a href="/lab/" class="lg-nav-item">&larr; Volver a Mis Micro-Apps</a>
      <a href="?format=json" class="lg-nav-item">JSON Storefront API</a>
      <button type="button" class="lg-btn lg-btn-secondary" id="theme-toggle-btn">
        <span class="theme-opt-dark">&#9790; Oscuro</span>
        <span class="theme-opt-light">&#9728; Claro</span>
      </button>
      <a href="/lab/?logout=1" class="lg-btn lg-btn-secondary">Cerrar sesión</a>
    </nav>
  </header>

  <main class="lg-container" style="padding: 36px 0 80px;">
    <section class="lg-card">
      <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap;">
        <div>
          <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin-bottom:12px;">
            <span class="lg-badge lg-badge-success">Micro-App Activa</span>
            <span class="lg-badge">Django 5.1 + Python 3.12</span>
            <span class="lg-badge">Shopify Storefront &amp; Webhooks</span>
          </div>
          <h1 class="lg-hero-title">Shopify Custom Storefront &amp; Webhook Studio.</h1>
          <p style="color:var(--text-secondary); max-width:820px; margin:12px 0 0;">
            Prototipo funcional en <code>/lab/shopify-django-app/</code> para probar catálogo headless sincronizado con Shopify GraphQL (`<?= htmlspecialchars($shopDomain) ?>`), carrito reactivo y validación criptográfica de webhooks `X-Shopify-Hmac-Sha256` en Django.
          </p>
        </div>
        <img src="../assets/brand/logo-mark.svg" alt="CS" width="52" height="52">
      </div>
    </section>

    <div class="shop-layout">
      <!-- Left: Storefront Catalog & Cart Simulator -->
      <section class="lg-card">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
          <div>
            <h2 class="lg-section-title">Catálogo Sincronizado (Storefront API)</h2>
            <p style="color:var(--text-secondary); margin:4px 0 0; font-size:14px;">
              Selecciona productos para simular un `checkoutCreate` mutation hacia Shopify y disparar el webhook `orders/create`.
            </p>
          </div>
          <span class="lg-badge" id="cart-badge">Carrito: 0 artículos ($0 MXN)</span>
        </div>

        <div class="product-grid">
          <?php foreach ($products as $prod): ?>
            <article class="product-item">
              <div>
                <div style="display:flex; justify-content:space-between; font-size:12px; color:var(--text-muted); margin-bottom:6px;">
                  <span><?= htmlspecialchars($prod['collection']) ?></span>
                  <span>SKU: <code><?= htmlspecialchars($prod['sku']) ?></code></span>
                </div>
                <h3 style="font-size:18px; font-weight:700; margin-bottom:8px;"><?= htmlspecialchars($prod['title']) ?></h3>
                <div class="price-tag">$<?= number_format($prod['price_mxn'], 2) ?> <span style="font-size:13px; font-weight:500; color:var(--text-muted);">MXN</span></div>
                <div style="font-size:13px; color:var(--success); font-weight:600; margin-top:4px;">
                  &#9679; <?= (int) $prod['inventory'] ?> disponibles en almacén CDMX
                </div>
              </div>
              <button
                type="button"
                class="lg-btn lg-btn-primary add-to-cart-btn"
                data-title="<?= htmlspecialchars($prod['title']) ?>"
                data-sku="<?= htmlspecialchars($prod['sku']) ?>"
                data-price="<?= htmlspecialchars((string) $prod['price_mxn']) ?>"
              >
                + Agregar al Checkout
              </button>
            </article>
          <?php endforeach; ?>
        </div>
      </section>

      <!-- Right: Live Webhook HMAC Inspector -->
      <section class="lg-card" style="display:flex; flex-direction:column; gap:16px;">
        <div>
          <h2 class="lg-section-title" style="font-size:22px;">Simulador de Webhooks Shopify &rarr; Django</h2>
          <p style="color:var(--text-secondary); font-size:14px; margin:4px 0 0;">
            Firma el payload con `HMAC-SHA256` (`X-Shopify-Hmac-Sha256`) y verifica la respuesta del endpoint receptor.
          </p>
        </div>

        <div>
          <label style="display:block; font-family:var(--font-display); font-size:13px; font-weight:700; margin-bottom:6px;" for="webhook-topic">
            Evento de Shopify (Topic)
          </label>
          <select id="webhook-topic" class="lg-select">
            <option value="orders/create">orders/create — Nueva Orden Pagada</option>
            <option value="products/update">products/update — Actualización de Producto</option>
            <option value="inventory_levels/update">inventory_levels/update — Cambio de Stock</option>
            <option value="customers/create">customers/create — Nuevo Cliente</option>
          </select>
        </div>

        <button type="button" class="lg-btn lg-btn-primary" id="fire-webhook-btn">
          Disparar Webhook Firmado con HMAC
        </button>

        <pre class="code-box" id="webhook-output">{
  "status": "Listo para probar",
  "hint": "Agrega productos del catálogo o haz clic en 'Disparar Webhook Firmado con HMAC' para validar la firma X-Shopify-Hmac-Sha256."
}</pre>
      </section>
    </div>
  </main>

  <script src="../assets/js/liquid-glass.js"></script>
  <script>
    (function () {
      const cart = [];
      const cartBadge = document.getElementById('cart-badge');
      const output = document.getElementById('webhook-output');
      const topicSelect = document.getElementById('webhook-topic');
      const fireBtn = document.getElementById('fire-webhook-btn');

      function getCartTotal() {
        return cart.reduce(function (sum, item) { return sum + item.price; }, 0);
      }

      function renderCartBadge() {
        const total = getCartTotal();
        cartBadge.textContent = 'Carrito: ' + cart.length + ' artículos ($' + total.toLocaleString('es-MX') + ' MXN)';
      }

      async function triggerWebhook() {
        const topic = topicSelect.value;
        const payload = {
          id: Math.floor(Math.random() * 9000000000) + 1000000000,
          currency: 'MXN',
          total_price: (getCartTotal() || 3490).toFixed(2),
          line_items: cart.length ? cart : [{ title: 'Teclado Mecánico Aluminio CNC 75%', sku: 'CS-KB75-ALU', price: 3490 }],
          created_at: new Date().toISOString()
        };

        output.textContent = 'Firmando y enviando webhook ' + topic + '...';
        try {
          const res = await fetch('?action=simulate_webhook&topic=' + encodeURIComponent(topic), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
          });
          const data = await res.json();
          output.textContent = JSON.stringify({
            webhook_result: data,
            order_payload: payload
          }, null, 2);
        } catch (err) {
          output.textContent = JSON.stringify({ error: String(err) }, null, 2);
        }
      }

      document.querySelectorAll('.add-to-cart-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
          cart.push({
            title: btn.getAttribute('data-title'),
            sku: btn.getAttribute('data-sku'),
            price: parseFloat(btn.getAttribute('data-price') || '0')
          });
          renderCartBadge();
          triggerWebhook();
        });
      });

      fireBtn.addEventListener('click', triggerWebhook);
    })();
  </script>
</body>
</html>
