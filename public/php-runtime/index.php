<?php
declare(strict_types=1);

/**
 * Carlos Silveira Playground — PHP 8.3 Runtime & Benchmark
 * Live URL: https://carlossilveira.dev/lab/php-runtime/
 * Binary on Spaceship server58: /opt/alt/php83/usr/bin/php
 */

$t0 = hrtime(true);
$iterations = isset($_GET['iterations']) ? max(1000, min(200000, (int) $_GET['iterations'])) : 25000;

$hashStart = hrtime(true);
$acc = '';
for ($i = 0; $i < $iterations; $i++) {
    $acc = hash('sha256', $acc . (string) $i);
}
$hashMs = round((hrtime(true) - $hashStart) / 1e6, 3);

$arrayStart = hrtime(true);
$items = [];
for ($i = 0; $i < 5000; $i++) {
    $items[$i] = ($i * 2654435761) & 0xFFFFFFFF;
}
rsort($items, SORT_NUMERIC);
$arrayMs = round((hrtime(true) - $arrayStart) / 1e6, 3);

$opcacheStatus = function_exists('opcache_get_status') ? @opcache_get_status(false) : false;
$extensions = get_loaded_extensions();
sort($extensions, SORT_NATURAL | SORT_FLAG_CASE);
$totalMs = round((hrtime(true) - $t0) / 1e6, 3);

$payload = [
    'ok' => true,
    'app' => 'php-runtime',
    'title' => 'PHP Runtime & Benchmark',
    'runtime' => [
        'php_version' => PHP_VERSION,
        'zend_version' => zend_version(),
        'sapi' => PHP_SAPI,
        'os' => PHP_OS_FAMILY . ' (' . php_uname('m') . ')',
        'binary_path' => PHP_BINARY ?: '/opt/alt/php83/usr/bin/php',
        'opcache_enabled' => is_array($opcacheStatus) ? (bool) ($opcacheStatus['opcache_enabled'] ?? false) : false,
        'jit_enabled' => is_array($opcacheStatus) ? (bool) ($opcacheStatus['jit']['enabled'] ?? false) : false,
        'memory_limit' => ini_get('memory_limit'),
        'memory_usage_kb' => round(memory_get_usage(true) / 1024, 2),
        'peak_memory_kb' => round(memory_get_peak_usage(true) / 1024, 2),
    ],
    'benchmark' => [
        'sha256_iterations' => $iterations,
        'sha256_duration_ms' => $hashMs,
        'sha256_ops_per_sec' => $hashMs > 0 ? (int) round(($iterations / $hashMs) * 1000) : 0,
        'digest_sample' => substr($acc, 0, 16) . '...',
        'array_sort_5k_ms' => $arrayMs,
        'total_request_ms' => $totalMs,
    ],
    'extensions_count' => count($extensions),
    'extensions' => $extensions,
    'timestamp' => gmdate('c'),
];

$wantsJson = (isset($_GET['format']) && $_GET['format'] === 'json')
    || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');

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
  <title>PHP <?= htmlspecialchars(PHP_VERSION) ?> Runtime — Carlos Silveira Lab</title>
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
      max-width: 880px;
      margin: 0 auto;
      animation: fadeUp 0.5s cubic-bezier(0.25, 0.1, 0.25, 1) both;
    }
    .topbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: var(--space-md);
      flex-wrap: wrap;
      margin-bottom: var(--space-xl);
    }
    .back-link {
      font: var(--text-caption);
      font-weight: 500;
      color: var(--accent-link);
      text-decoration: none;
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
    .subtitle {
      color: var(--text-secondary);
      max-width: 680px;
      margin: 0 0 var(--space-xl);
    }
    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
      gap: var(--space-md);
      margin-bottom: var(--space-lg);
    }
    .card {
      background: var(--card-bg);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-radius: 18px;
      border: 1px solid var(--card-border);
      box-shadow: var(--card-shadow);
      padding: var(--space-lg);
      transition: all 0.3s cubic-bezier(0.25, 0.1, 0.25, 1);
    }
    .stat-label {
      font: var(--text-caption);
      color: var(--text-secondary);
      margin-bottom: var(--space-xs);
    }
    .stat-val {
      font-size: 24px;
      font-weight: 600;
      letter-spacing: -0.02em;
    }
    .controls {
      display: flex;
      gap: var(--space-sm);
      flex-wrap: wrap;
      margin-bottom: var(--space-lg);
    }
    .btn-primary, .btn-secondary {
      padding: 12px 24px;
      border-radius: 980px;
      font: var(--text-caption);
      font-size: 14px;
      font-weight: 500;
      min-height: 44px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s ease;
    }
    .btn-primary {
      background: var(--accent);
      color: #ffffff;
    }
    .btn-primary:hover, .btn-secondary:hover {
      transform: scale(1.02);
      filter: brightness(1.1);
    }
    .btn-secondary {
      background: var(--card-bg);
      color: var(--text-primary);
      border: 1px solid var(--card-border);
    }
    .pills {
      display: flex;
      flex-wrap: wrap;
      gap: var(--space-sm);
      margin-top: var(--space-sm);
    }
    .pill {
      font: var(--text-caption);
      padding: 4px 12px;
      border-radius: 980px;
      background: var(--bg-primary);
      border: 1px solid var(--card-border);
    }
    pre {
      margin: 0;
      overflow-x: auto;
      font-family: var(--font-mono);
      font-size: 13px;
      line-height: 1.55;
    }
    code {
      font-family: var(--font-mono);
      font-size: 13px;
    }
  </style>
</head>
<body>
  <main class="wrap">
    <div class="topbar">
      <a class="back-link" href="/lab/">&larr; Back to Engineering Lab</a>
      <a class="btn-primary" href="?format=json">View JSON Response</a>
    </div>

    <div class="eyebrow">PHP <?= htmlspecialchars(PHP_VERSION) ?> Runtime</div>
    <h1>PHP Runtime &amp; Benchmark.</h1>
    <p class="subtitle">
      Running natively on LiteSpeed SAPI (<code><?= htmlspecialchars($payload['runtime']['binary_path']) ?></code>).
    </p>

    <div class="grid">
      <div class="card">
        <div class="stat-label">PHP Version</div>
        <div class="stat-val"><?= htmlspecialchars(PHP_VERSION) ?></div>
      </div>
      <div class="card">
        <div class="stat-label">SHA-256 Throughput</div>
        <div class="stat-val"><?= number_format($payload['benchmark']['sha256_ops_per_sec']) ?> /s</div>
      </div>
      <div class="card">
        <div class="stat-label">Execution Time</div>
        <div class="stat-val"><?= htmlspecialchars((string) $totalMs) ?> ms</div>
      </div>
      <div class="card">
        <div class="stat-label">Loaded Extensions</div>
        <div class="stat-val"><?= count($extensions) ?></div>
      </div>
    </div>

    <div class="controls">
      <a class="btn-secondary" href="?iterations=10000">10,000 Iterations</a>
      <a class="btn-secondary" href="?iterations=25000">25,000 Iterations</a>
      <a class="btn-secondary" href="?iterations=75000">75,000 Iterations</a>
      <a class="btn-secondary" href="?iterations=150000">150,000 Iterations</a>
    </div>

    <div class="card" style="margin-bottom: var(--space-lg);">
      <div class="stat-label">Active Extensions (<?= count($extensions) ?>)</div>
      <div class="pills">
        <?php foreach ($extensions as $ext): ?>
          <span class="pill"><?= htmlspecialchars($ext) ?></span>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="card">
      <div class="stat-label" style="margin-bottom: var(--space-sm);">JSON Payload</div>
      <pre><?= htmlspecialchars((string) json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) ?></pre>
    </div>
  </main>
</body>
</html>
