<?php
declare(strict_types=1);

/**
 * Carlos Silveira Lab Starter — PHP 8.3+ Web & API
 * Target URL: https://carlossilveira.dev/lab/__LAB_SLUG__/
 * Runtime: /opt/alt/php83/usr/bin/php (LiteSpeed SAPI)
 */

$slug = '__LAB_SLUG__';
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$wantsJson = isset($_GET['format']) && $_GET['format'] === 'json'
    || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')
    || str_ends_with(parse_url($requestUri, PHP_URL_PATH) ?: '', '/api');

$payload = [
    'ok' => true,
    'lab' => $slug,
    'stack' => 'php-api',
    'runtime' => 'PHP ' . PHP_VERSION . ' (' . PHP_SAPI . ')',
    'binary' => '/opt/alt/php83/usr/bin/php',
    'method' => $method,
    'uri' => $requestUri,
    'timestamp' => gmdate('c'),
    'memory_usage_kb' => round(memory_get_usage(true) / 1024, 2),
];

if ($wantsJson) {
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="color-scheme" content="light dark">
  <script>
    try {
      document.querySelector('meta[name="color-scheme"]').content = localStorage.getItem("color-scheme") ?? "light dark";
    } catch (_) {}
  </script>
  <title><?= htmlspecialchars($slug) ?> — PHP <?= PHP_VERSION ?> | Carlos Silveira Lab</title>
  <style>
    :root {
      color-scheme: light dark;
      --font-sans: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'SF Pro Text', 'Segoe UI', sans-serif;
      --font-mono: 'SF Mono', ui-monospace, Menlo, Monaco, Consolas, monospace;
      --text-hero: 600 48px/1.1 var(--font-sans);
      --text-title: 600 32px/1.2 var(--font-sans);
      --text-body: 400 17px/1.5 var(--font-sans);
      --text-caption: 400 13px/1.4 var(--font-sans);
      --bg-primary: light-dark(#ffffff, #000000);
      --bg-secondary: light-dark(#f5f5f7, #1d1d1f);
      --text-primary: light-dark(#1d1d1f, #f5f5f7);
      --text-secondary: #86868b;
      --accent: #0071e3;
      --accent-link: light-dark(#0071e3, #2997ff);
      --card-bg: light-dark(rgba(255,255,255,0.8), rgba(29,29,31,0.8));
      --card-border: light-dark(rgba(0,0,0,0.05), rgba(255,255,255,0.08));
      --card-shadow: light-dark(0 4px 24px rgba(0,0,0,0.06), 0 4px 24px rgba(0,0,0,0.4));
      --space-xs: 4px;
      --space-sm: 8px;
      --space-md: 16px;
      --space-lg: 24px;
      --space-xl: 48px;
      --space-2xl: 96px;
    }
    :root:has(> head > meta[name="color-scheme"][content="dark"]) { color-scheme: dark; }
    :root:has(> head > meta[name="color-scheme"][content="light"]) { color-scheme: light; }
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font: var(--text-body);
      background: var(--bg-secondary);
      color: var(--text-primary);
      padding: var(--space-xl) var(--space-lg) var(--space-2xl);
      -webkit-font-smoothing: antialiased;
    }
    .wrap {
      max-width: 680px;
      margin: 0 auto;
      animation: fadeUp 0.5s cubic-bezier(0.25, 0.1, 0.25, 1) both;
    }
    .eyebrow {
      font: var(--text-caption);
      font-weight: 600;
      color: var(--accent-link);
      margin-bottom: var(--space-sm);
    }
    h1 {
      font: var(--text-hero);
      letter-spacing: -0.025em;
      margin: 0 0 var(--space-md);
    }
    p {
      color: var(--text-secondary);
      margin: 0 0 var(--space-lg);
    }
    .card {
      background: var(--card-bg);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-radius: 18px;
      border: 1px solid var(--card-border);
      box-shadow: var(--card-shadow);
      padding: var(--space-lg);
      margin-bottom: var(--space-lg);
      transition: all 0.3s cubic-bezier(0.25, 0.1, 0.25, 1);
    }
    pre {
      margin: 0;
      overflow-x: auto;
      font-family: var(--font-mono);
      font-size: 13px;
      line-height: 1.55;
    }
    .actions {
      display: flex;
      gap: var(--space-md);
      align-items: center;
      flex-wrap: wrap;
    }
    .btn-primary {
      background: var(--accent);
      color: #ffffff;
      padding: 12px 24px;
      border-radius: 980px;
      font: var(--text-caption);
      font-size: 14px;
      font-weight: 500;
      min-height: 44px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      transition: all 0.2s ease;
    }
    .btn-primary:hover {
      transform: scale(1.02);
      filter: brightness(1.1);
    }
    a.link {
      color: var(--accent-link);
      text-decoration: none;
      font: var(--text-caption);
      font-weight: 500;
    }
  </style>
</head>
<body>
  <main class="wrap">
    <div class="eyebrow">PHP <?= htmlspecialchars(PHP_VERSION) ?> Starter</div>
    <h1><?= htmlspecialchars($slug) ?></h1>
    <p>Running natively on LiteSpeed SAPI (<code><?= htmlspecialchars(PHP_VERSION) ?></code>).</p>
    <div class="card">
      <pre><?= htmlspecialchars((string) json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) ?></pre>
    </div>
    <div class="actions">
      <a class="btn-primary" href="?format=json">View JSON Response</a>
      <a class="link" href="/lab/">&larr; Back to Engineering Lab</a>
    </div>
  </main>
</body>
</html>
