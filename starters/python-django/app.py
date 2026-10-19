"""
Carlos Silveira Lab Starter — Python 3.12 WSGI / Django-Ready App
Target URL: https://carlossilveira.dev/lab/__LAB_SLUG__/
Runtime: /opt/alt/python312/bin/python3
"""
from __future__ import annotations

import datetime
import html
import json
import os
import platform
import sys
from urllib.parse import parse_qs
from wsgiref.simple_server import make_server

LAB_SLUG = "__LAB_SLUG__"


def application(environ: dict, start_response):
    """Standard PEP 3333 WSGI entrypoint compatible with Phusion Passenger, Gunicorn, and CGIHandler."""
    query = parse_qs(environ.get("QUERY_STRING", ""))
    accept = environ.get("HTTP_ACCEPT", "")
    path_info = environ.get("PATH_INFO", "/")
    wants_json = (
        query.get("format", [""])[0] == "json"
        or "application/json" in accept
        or path_info.endswith("/api")
    )

    payload = {
        "ok": True,
        "lab": LAB_SLUG,
        "stack": "python-django",
        "runtime": f"Python {platform.python_version()} ({platform.python_implementation()})",
        "executable": sys.executable,
        "platform": platform.platform(),
        "method": environ.get("REQUEST_METHOD", "GET"),
        "path": path_info,
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

    escaped_slug = html.escape(LAB_SLUG)
    escaped_json = html.escape(json.dumps(payload, indent=2))
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
  <title>{escaped_slug} — Python {platform.python_version()} Lab | carlossilveira.dev</title>
  <style>
    :root {{
      color-scheme: light dark;
      --bg: light-dark(#f8f6f6, #02040a);
      --card: light-dark(#ffffff, #0a0f1c);
      --border: light-dark(rgba(0,0,0,0.08), rgba(255,255,255,0.08));
      --text: light-dark(#0a0f1c, #f8fafc);
      --accent: #00b4ff;
    }}
    :root:has(> head > meta[name="color-scheme"][content="dark"]) {{ color-scheme: dark; }}
    :root:has(> head > meta[name="color-scheme"][content="light"]) {{ color-scheme: light; }}
    body {{
      margin: 0;
      font-family: Inter, -apple-system, BlinkMacSystemFont, sans-serif;
      background: var(--bg);
      color: var(--text);
      padding: 3rem 1.5rem;
    }}
    .wrap {{ max-width: 760px; margin: 0 auto; }}
    .badge {{
      font-family: 'JetBrains Mono', monospace;
      font-size: 10px;
      font-weight: 800;
      letter-spacing: 0.2em;
      text-transform: uppercase;
      color: var(--accent);
    }}
    h1 {{ font-size: 2.5rem; font-weight: 900; letter-spacing: -0.04em; margin: 0.5rem 0 1rem; }}
    pre {{
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 1rem;
      padding: 1.25rem;
      overflow-x: auto;
      font-family: 'JetBrains Mono', monospace;
      font-size: 0.85rem;
    }}
    a {{ color: var(--accent); text-decoration: none; font-weight: 700; }}
  </style>
</head>
<body>
  <main class="wrap">
    <div class="badge">PYTHON 3.12 WSGI LAB STARTER // {escaped_slug}</div>
    <h1>{escaped_slug}</h1>
    <p>Running on CloudLinux Python <code>{html.escape(platform.python_version())}</code>. Append <a href="?format=json"><code>?format=json</code></a> for JSON API mode.</p>
    <pre>{escaped_json}</pre>
    <p><a href="/lab/">&larr; Back to carlossilveira.dev/lab/</a></p>
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


if __name__ == "__main__":
    port = int(os.environ.get("PORT", "8000"))
    print(f"Serving Python WSGI starter on http://localhost:{port}")
    with make_server("127.0.0.1", port, application) as httpd:
        httpd.serve_forever()
