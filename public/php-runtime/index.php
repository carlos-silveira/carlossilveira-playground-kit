<?php
declare(strict_types=1);

/**
 * Carlos Silveira Playground — Live PHP 8.3 Runtime Inspector & Micro-Benchmark
 * Live URL: https://carlossilveira.dev/lab/php-runtime/
 * Binary on Spaceship server58: /opt/alt/php83/usr/bin/php
 */

$t0 = hrtime(true);

$iterations = isset($_GET['iterations']) ? max(1000, min(200000, (int) $_GET['iterations'])) : 25000;

// 1. Run CPU / Hash / Array Micro-Benchmark
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
    'title' => 'PHP Runtime Inspector & Micro-Benchmark',
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
  <title>PHP <?= htmlspecialchars(PHP_VERSION) ?> Runtime Inspector &amp; Benchmark — carlossilveira.dev/lab/</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700;900&family=JetBrains+Mono:wght@400;700;800&display=swap" rel="stylesheet">
  <style>
    :root {
      color-scheme: light dark;
      --bg: light-dark(#f8f6f6, #02040a);
      --card: light-dark(rgba(255,255,255,0.82), rgba(255,255,255,0.03));
      --code: light-dark(#0f172a, #050914);
      --border: light-dark(rgba(0,0,0,0.08), rgba(255,255,255,0.08));
      --text: light-dark(#0a0f1c, #f8fafc);
      --muted: light-dark(#475569, #94a3b8);
      --primary: #00b4ff;
      --emerald: #10b981;
    }
    :root:has(> head > meta[name="color-scheme"][content="dark"]) { color-scheme: dark; }
    :root:has(> head > meta[name="color-scheme"][content="light"]) { color-scheme: light; }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background: var(--bg);
      color: var(--text);
      padding: 2.5rem 1.5rem 4rem;
      line-height: 1.6;
    }
    .wrap { max-width: 1040px; margin: 0 auto; }
    .topbar { display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; margin-bottom: 2.5rem; }
    .back-link {
      font-family: 'JetBrains Mono', monospace;
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.16em;
      color: var(--primary);
      text-decoration: none;
    }
    .badge {
      font-family: 'JetBrains Mono', monospace;
      font-size: 10px;
      font-weight: 800;
      letter-spacing: 0.25em;
      text-transform: uppercase;
      color: var(--primary);
    }
    h1 { font-size: clamp(2.2rem, 5vw, 3.5rem); font-weight: 900; letter-spacing: -0.04em; margin: 0.4rem 0 0.75rem; line-height: 1; }
    .subtitle { color: var(--muted); font-weight: 300; max-width: 720px; margin: 0 0 2rem; }
    .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
    .stat {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 1.25rem;
      padding: 1.25rem;
    }
    .stat-label {
      font-family: 'JetBrains Mono', monospace;
      font-size: 9px;
      font-weight: 800;
      letter-spacing: 0.2em;
      text-transform: uppercase;
      color: var(--muted);
    }
    .stat-val { font-size: 1.55rem; font-weight: 900; letter-spacing: -0.03em; margin-top: 0.3rem; }
    .controls {
      display: flex;
      gap: 0.75rem;
      flex-wrap: wrap;
      margin-bottom: 2rem;
    }
    .btn {
      font-family: 'JetBrains Mono', monospace;
      font-size: 10px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.15em;
      padding: 0.7rem 1.1rem;
      border-radius: 0.75rem;
      text-decoration: none;
      border: 1px solid var(--border);
      color: var(--text);
      background: var(--card);
    }
    .btn.primary {
      background: var(--primary);
      color: #02040a;
      border-color: var(--primary);
    }
    .pills { display: flex; flex-wrap: wrap; gap: 0.45rem; margin-bottom: 2rem; }
    .pill {
      font-family: 'JetBrains Mono', monospace;
      font-size: 10px;
      padding: 0.25rem 0.6rem;
      border-radius: 9999px;
      background: var(--card);
      border: 1px solid var(--border);
    }
    pre {
      background: var(--code);
      color: #38bdf8;
      border: 1px solid var(--border);
      border-radius: 1.25rem;
      padding: 1.25rem;
      overflow-x: auto;
      font-family: 'JetBrains Mono', monospace;
      font-size: 0.82rem;
    }
  </style>
</head>
<body>
  <main class="wrap">
    <div class="topbar">
      <a class="back-link" href="/lab/">&larr; Back to Playground Hub (/lab/)</a>
      <a class="btn primary" href="?format=json">Raw JSON (?format=json)</a>
    </div>

    <div class="badge">LIVE RUNTIME // LITESPEED PHP <?= htmlspecialchars(PHP_VERSION) ?></div>
    <h1>PHP Runtime Inspector &amp; Benchmark.</h1>
    <p class="subtitle">
      Executing natively on <code>server58.shared.spaceship.host</code> via LiteSpeed SAPI (<code><?= htmlspecialchars($payload['runtime']['binary_path']) ?></code>).
    </p>

    <div class="grid">
      <div class="stat">
        <div class="stat-label">PHP VERSION / SAPI</div>
        <div class="stat-val"><?= htmlspecialchars(PHP_VERSION . ' (' . PHP_SAPI . ')') ?></div>
      </div>
      <div class="stat">
        <div class="stat-label">SHA-256 THROUGHPUT</div>
        <div class="stat-val"><?= number_format($payload['benchmark']['sha256_ops_per_sec']) ?> ops/s</div>
      </div>
      <div class="stat">
        <div class="stat-label">TOTAL EXECUTION TIME</div>
        <div class="stat-val"><?= htmlspecialchars((string) $totalMs) ?> ms</div>
      </div>
      <div class="stat">
        <div class="stat-label">LOADED EXTENSIONS</div>
        <div class="stat-val"><?= count($extensions) ?> modules</div>
      </div>
    </div>

    <div class="controls">
      <a class="btn" href="?iterations=10000">10k Hashes</a>
      <a class="btn" href="?iterations=25000">25k Hashes</a>
      <a class="btn" href="?iterations=75000">75k Hashes</a>
      <a class="btn" href="?iterations=150000">150k Hashes</a>
    </div>

    <div class="badge" style="margin-bottom:0.75rem;">LOADED PHP EXTENSIONS (<?= count($extensions) ?>)</div>
    <div class="pills">
      <?php foreach ($extensions as $ext): ?>
        <span class="pill"><?= htmlspecialchars($ext) ?></span>
      <?php endforeach; ?>
    </div>

    <div class="badge" style="margin-bottom:0.75rem;">LIVE TELEMETRY PAYLOAD</div>
    <pre><?= htmlspecialchars((string) json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) ?></pre>
  </main>
</body>
</html>
