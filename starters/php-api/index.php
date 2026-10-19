<?php
declare(strict_types=1);

/**
 * Carlos Silveira Lab Starter — PHP 8.3+ API & Micro-App
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
  <title><?= htmlspecialchars($slug) ?> — PHP <?= PHP_VERSION ?> Lab | carlossilveira.dev</title>
  <style>
    :root {
      color-scheme: light dark;
      --bg: light-dark(#f8f6f6, #02040a);
      --card: light-dark(#ffffff, #0a0f1c);
      --border: light-dark(rgba(0,0,0,0.08), rgba(255,255,255,0.08));
      --text: light-dark(#0a0f1c, #f8fafc);
      --muted: light-dark(#475569, #94a3b8);
      --accent: #00b4ff;
    }
    :root:has(> head > meta[name="color-scheme"][content="dark"]) { color-scheme: dark; }
    :root:has(> head > meta[name="color-scheme"][content="light"]) { color-scheme: light; }
    body {
      margin: 0;
      font-family: Inter, -apple-system, BlinkMacSystemFont, sans-serif;
      background: var(--bg);
      color: var(--text);
      padding: 3rem 1.5rem;
    }
    .wrap { max-width: 760px; margin: 0 auto; }
    .badge {
      font-family: 'JetBrains Mono', monospace;
      font-size: 10px;
      font-weight: 800;
      letter-spacing: 0.2em;
      text-transform: uppercase;
      color: var(--accent);
    }
    h1 { font-size: 2.5rem; font-weight: 900; letter-spacing: -0.04em; margin: 0.5rem 0 1rem; }
    pre {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 1rem;
      padding: 1.25rem;
      overflow-x: auto;
      font-family: 'JetBrains Mono', monospace;
      font-size: 0.85rem;
    }
    a { color: var(--accent); text-decoration: none; font-weight: 700; }
  </style>
</head>
<body>
  <main class="wrap">
    <div class="badge">PHP 8.3 LAB STARTER // <?= htmlspecialchars($slug) ?></div>
    <h1><?= htmlspecialchars($slug) ?></h1>
    <p>Running natively on LiteSpeed SAPI (<code><?= PHP_VERSION ?></code>). Append <a href="?format=json"><code>?format=json</code></a> for raw JSON output.</p>
    <pre><?= htmlspecialchars((string) json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) ?></pre>
    <p><a href="/lab/">&larr; Back to carlossilveira.dev/lab/</a></p>
  </main>
</body>
</html>
