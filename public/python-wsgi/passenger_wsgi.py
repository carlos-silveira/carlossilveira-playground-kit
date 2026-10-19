"""
Carlos Silveira Playground — Live Python 3.12 WSGI & CGI Telemetry Micro-App
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
        "title": "Python 3.12 WSGI & CGI Telemetry Micro-App",
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
  <title>Python {py_ver} WSGI &amp; Telemetry App — carlossilveira.dev/lab/</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700;900&family=JetBrains+Mono:wght@400;700;800&display=swap" rel="stylesheet">
  <style>
    :root {{
      color-scheme: light dark;
      --bg: light-dark(#f8f6f6, #02040a);
      --card: light-dark(rgba(255,255,255,0.82), rgba(255,255,255,0.03));
      --code: light-dark(#0f172a, #050914);
      --border: light-dark(rgba(0,0,0,0.08), rgba(255,255,255,0.08));
      --text: light-dark(#0a0f1c, #f8fafc);
      --muted: light-dark(#475569, #94a3b8);
      --primary: #00b4ff;
    }}
    :root:has(> head > meta[name="color-scheme"][content="dark"]) {{ color-scheme: dark; }}
    :root:has(> head > meta[name="color-scheme"][content="light"]) {{ color-scheme: light; }}
    * {{ box-sizing: border-box; }}
    body {{
      margin: 0;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background: var(--bg);
      color: var(--text);
      padding: 2.5rem 1.5rem 4rem;
      line-height: 1.6;
    }}
    .wrap {{ max-width: 1040px; margin: 0 auto; }}
    .topbar {{ display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; margin-bottom: 2.5rem; }}
    .back-link {{
      font-family: 'JetBrains Mono', monospace;
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.16em;
      color: var(--primary);
      text-decoration: none;
    }}
    .badge {{
      font-family: 'JetBrains Mono', monospace;
      font-size: 10px;
      font-weight: 800;
      letter-spacing: 0.25em;
      text-transform: uppercase;
      color: var(--primary);
    }}
    h1 {{ font-size: clamp(2.2rem, 5vw, 3.5rem); font-weight: 900; letter-spacing: -0.04em; margin: 0.4rem 0 0.75rem; line-height: 1; }}
    .subtitle {{ color: var(--muted); font-weight: 300; max-width: 720px; margin: 0 0 2rem; }}
    .grid {{ display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 1rem; margin-bottom: 2rem; }}
    .stat {{
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 1.25rem;
      padding: 1.25rem;
    }}
    .stat-label {{
      font-family: 'JetBrains Mono', monospace;
      font-size: 9px;
      font-weight: 800;
      letter-spacing: 0.2em;
      text-transform: uppercase;
      color: var(--muted);
    }}
    .stat-val {{ font-size: 1.55rem; font-weight: 900; letter-spacing: -0.03em; margin-top: 0.3rem; }}
    .controls {{ display: flex; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 2rem; }}
    .btn {{
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
    }}
    .btn.primary {{ background: var(--primary); color: #02040a; border-color: var(--primary); }}
    pre {{
      background: var(--code);
      color: #38bdf8;
      border: 1px solid var(--border);
      border-radius: 1.25rem;
      padding: 1.25rem;
      overflow-x: auto;
      font-family: 'JetBrains Mono', monospace;
      font-size: 0.82rem;
    }}
  </style>
</head>
<body>
  <main class="wrap">
    <div class="topbar">
      <a class="back-link" href="/lab/">&larr; Back to Playground Hub (/lab/)</a>
      <a class="btn primary" href="?format=json">Raw JSON (?format=json)</a>
    </div>

    <div class="badge">LIVE RUNTIME // CLOUDLINUX PYTHON {py_ver}</div>
    <h1>Python 3.12 WSGI &amp; Telemetry API.</h1>
    <p class="subtitle">
      Running PEP 3333 WSGI via <code>{html.escape(sys.executable)}</code> on <code>server58.shared.spaceship.host</code>.
    </p>

    <div class="grid">
      <div class="stat">
        <div class="stat-label">PYTHON VERSION</div>
        <div class="stat-val">{py_ver}</div>
      </div>
      <div class="stat">
        <div class="stat-label">SHA-256 THROUGHPUT</div>
        <div class="stat-val">{ops_sec} ops/s</div>
      </div>
      <div class="stat">
        <div class="stat-label">REQUEST TIME</div>
        <div class="stat-val">{total_ms} ms</div>
      </div>
      <div class="stat">
        <div class="stat-label">WORKER PID</div>
        <div class="stat-val">#{os.getpid()}</div>
      </div>
    </div>

    <div class="controls">
      <a class="btn" href="?rounds=10000">10k SHA-256 Rounds</a>
      <a class="btn" href="?rounds=25000">25k SHA-256 Rounds</a>
      <a class="btn" href="?rounds=75000">75k SHA-256 Rounds</a>
    </div>

    <div class="badge" style="margin-bottom:0.75rem;">LIVE WSGI TELEMETRY PAYLOAD</div>
    <pre>{json_pretty}</pre>
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
