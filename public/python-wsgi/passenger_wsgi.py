"""
Carlos Silveira Playground — Python 3.12 WSGI Service
Live URL: https://carlossilveira.dev/lab/python-wsgi/
Binary on Spaceship server58: /opt/alt/python312/bin/python3
"""
from __future__ import annotations

import datetime
import hashlib
import html
import json
import os
import platform
import sys
import time
from urllib.parse import parse_qs


def application(environ: dict, start_response):
    t0 = time.perf_counter()
    query = parse_qs(environ.get("QUERY_STRING", ""))
    accept = environ.get("HTTP_ACCEPT", "")
    wants_json = query.get("format", [""])[0] == "json" or "application/json" in accept

    try:
        rounds = max(1000, min(150000, int(query.get("rounds", ["20000"])[0])))
    except ValueError:
        rounds = 20000

    digest = b"carlossilveira-python-312"
    hash_t0 = time.perf_counter()
    for _ in range(rounds):
        digest = hashlib.sha256(digest).digest()
    hash_ms = round((time.perf_counter() - hash_t0) * 1000.0, 3)
    total_ms = round((time.perf_counter() - t0) * 1000.0, 3)

    payload = {
        "ok": True,
        "app": "python-wsgi",
        "title": "Python 3.12 WSGI Service",
        "runtime": {
            "python_version": platform.python_version(),
            "implementation": platform.python_implementation(),
            "compiler": platform.python_compiler(),
            "executable": sys.executable,
            "platform": platform.platform(),
            "gateway_interface": environ.get("GATEWAY_INTERFACE", "WSGI/Passenger"),
            "pid": os.getpid(),
        },
        "benchmark": {
            "sha256_rounds": rounds,
            "sha256_duration_ms": hash_ms,
            "sha256_ops_per_sec": int(round((rounds / hash_ms) * 1000)) if hash_ms > 0 else 0,
            "digest_hex_prefix": digest.hex()[:16] + "...",
            "total_request_ms": total_ms,
        },
        "timestamp": datetime.datetime.now(datetime.timezone.utc).isoformat(),
    }

    if wants_json:
        body = json.dumps(payload, indent=2).encode("utf-8")
        headers = [
            ("Content-Type", "application/json; charset=utf-8"),
            ("Access-Control-Allow-Origin", "*"),
            ("Content-Length", str(len(body))),
        ]
        start_response("200 OK", headers)
        return [body]

    py_ver = html.escape(platform.python_version())
    ops_sec = f"{payload['benchmark']['sha256_ops_per_sec']:,}"
    json_pretty = html.escape(json.dumps(payload, indent=2))

    page = f"""<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="color-scheme" content="light dark">
  <script>
    try {{
      document.querySelector('meta[name="color-scheme"]').content = localStorage.getItem("color-scheme") ?? "light dark";
    }} catch (_) {{}}
  </script>
  <title>Python {py_ver} WSGI — Carlos Silveira Lab</title>
  <style>
    :root {{
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
    }}
    :root:has(> head > meta[name="color-scheme"][content="dark"]) {{ color-scheme: dark; }}
    :root:has(> head > meta[name="color-scheme"][content="light"]) {{ color-scheme: light; }}
    @keyframes fadeUp {{
      from {{ opacity: 0; transform: translateY(20px); }}
      to {{ opacity: 1; transform: translateY(0); }}
    }}
    * {{ box-sizing: border-box; }}
    body {{
      margin: 0;
      font: var(--text-body);
      background: var(--bg-secondary);
      color: var(--text-primary);
      padding: var(--space-xl) var(--space-lg) var(--space-2xl);
      -webkit-font-smoothing: antialiased;
    }}
    .wrap {{
      max-width: 880px;
      margin: 0 auto;
      animation: fadeUp 0.5s cubic-bezier(0.25, 0.1, 0.25, 1) both;
    }}
    .topbar {{
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: var(--space-md);
      flex-wrap: wrap;
      margin-bottom: var(--space-xl);
    }}
    .back-link {{
      font: var(--text-caption);
      font-weight: 500;
      color: var(--accent-link);
      text-decoration: none;
    }}
    .eyebrow {{
      font: var(--text-caption);
      font-weight: 600;
      color: var(--accent-link);
      margin-bottom: var(--space-sm);
    }}
    h1 {{
      font: var(--text-hero);
      letter-spacing: -0.025em;
      margin: 0 0 var(--space-md);
    }}
    .subtitle {{
      color: var(--text-secondary);
      max-width: 680px;
      margin: 0 0 var(--space-xl);
    }}
    .grid {{
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
      gap: var(--space-md);
      margin-bottom: var(--space-lg);
    }}
    .card {{
      background: var(--card-bg);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-radius: 18px;
      border: 1px solid var(--card-border);
      box-shadow: var(--card-shadow);
      padding: var(--space-lg);
      transition: all 0.3s cubic-bezier(0.25, 0.1, 0.25, 1);
    }}
    .stat-label {{
      font: var(--text-caption);
      color: var(--text-secondary);
      margin-bottom: var(--space-xs);
    }}
    .stat-val {{
      font-size: 24px;
      font-weight: 600;
      letter-spacing: -0.02em;
    }}
    .controls {{
      display: flex;
      gap: var(--space-sm);
      flex-wrap: wrap;
      margin-bottom: var(--space-lg);
    }}
    .btn-primary, .btn-secondary {{
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
    }}
    .btn-primary {{
      background: var(--accent);
      color: #ffffff;
    }}
    .btn-primary:hover, .btn-secondary:hover {{
      transform: scale(1.02);
      filter: brightness(1.1);
    }}
    .btn-secondary {{
      background: var(--card-bg);
      color: var(--text-primary);
      border: 1px solid var(--card-border);
    }}
    pre {{
      margin: 0;
      overflow-x: auto;
      font-family: var(--font-mono);
      font-size: 13px;
      line-height: 1.55;
    }}
    code {{
      font-family: var(--font-mono);
      font-size: 13px;
    }}
  </style>
</head>
<body>
  <main class="wrap">
    <div class="topbar">
      <a class="back-link" href="/lab/">&larr; Back to Engineering Lab</a>
      <a class="btn-primary" href="?format=json">View JSON Response</a>
    </div>

    <div class="eyebrow">Python {py_ver} WSGI</div>
    <h1>Python 3.12 WSGI Service.</h1>
    <p class="subtitle">
      Running PEP 3333 WSGI via <code>{html.escape(sys.executable)}</code>.
    </p>

    <div class="grid">
      <div class="card">
        <div class="stat-label">Python Version</div>
        <div class="stat-val">{py_ver}</div>
      </div>
      <div class="card">
        <div class="stat-label">SHA-256 Throughput</div>
        <div class="stat-val">{ops_sec} /s</div>
      </div>
      <div class="card">
        <div class="stat-label">Request Time</div>
        <div class="stat-val">{total_ms} ms</div>
      </div>
      <div class="card">
        <div class="stat-label">Process ID</div>
        <div class="stat-val">{os.getpid()}</div>
      </div>
    </div>

    <div class="controls">
      <a class="btn-secondary" href="?rounds=10000">10,000 Rounds</a>
      <a class="btn-secondary" href="?rounds=25000">25,000 Rounds</a>
      <a class="btn-secondary" href="?rounds=75000">75,000 Rounds</a>
    </div>

    <div class="card">
      <div class="stat-label" style="margin-bottom: var(--space-sm);">JSON Payload</div>
      <pre>{json_pretty}</pre>
    </div>
  </main>
</body>
</html>"""
    body = page.encode("utf-8")
    headers = [
        ("Content-Type", "text/html; charset=utf-8"),
        ("Content-Length", str(len(body))),
    ]
    start_response("200 OK", headers)
    return [body]
