"""
Carlos Silveira Private Lab — Python 3.12 WSGI Microservice
Live URL: https://carlossilveira.dev/lab/python-wsgi/
Binary on Spaceship server58: /opt/alt/python312/bin/python3
"""
from __future__ import annotations

import datetime
import html
import json
import os
import platform
import sys
from http.cookies import SimpleCookie
from urllib.parse import parse_qs


def is_authenticated(environ: dict) -> bool:
    raw_cookie = environ.get("HTTP_COOKIE", "")
    if "cs_lab_auth=" in raw_cookie or "wordpress_logged_in_" in raw_cookie:
        return True
    cookie = SimpleCookie()
    try:
        cookie.load(raw_cookie)
        if "cs_lab_auth" in cookie:
            return True
    except Exception:
        pass
    return False


def application(environ: dict, start_response):
    if not is_authenticated(environ):
        start_response(
            "302 Found",
            [
                ("Location", "/lab/"),
                ("X-Robots-Tag", "noindex, nofollow"),
            ],
        )
        return [b""]

    query = parse_qs(environ.get("QUERY_STRING", ""))
    accept = environ.get("HTTP_ACCEPT", "")
    wants_json = query.get("format", [""])[0] == "json" or "application/json" in accept

    payload = {
        "ok": True,
        "app": "python-wsgi",
        "title": "Python 3.12 WSGI Microservice",
        "runtime": {
            "python_version": platform.python_version(),
            "implementation": platform.python_implementation(),
            "executable": sys.executable,
            "platform": platform.platform(),
            "pid": os.getpid(),
        },
        "timestamp": datetime.datetime.now(datetime.timezone.utc).isoformat(),
    }

    if wants_json:
        body = json.dumps(payload, indent=2).encode("utf-8")
        start_response(
            "200 OK",
            [
                ("Content-Type", "application/json; charset=utf-8"),
                ("X-Robots-Tag", "noindex, nofollow"),
                ("Content-Length", str(len(body))),
            ],
        )
        return [body]

    py_ver = html.escape(platform.python_version())
    json_pretty = html.escape(json.dumps(payload, indent=2))

    page = f"""<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <meta name="color-scheme" content="light dark">
  <script>
    try {{
      document.querySelector('meta[name="color-scheme"]').content = localStorage.getItem("color-scheme") ?? "light dark";
    }} catch (_) {{}}
  </script>
  <title>Python {py_ver} WSGI — Carlos Silveira Lab</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/liquid-glass.css">
</head>
<body>
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
      <a href="?format=json" class="lg-nav-item">Respuesta JSON</a>
      <button type="button" class="lg-btn lg-btn-secondary" id="theme-toggle-btn">
        <span class="theme-opt-dark">&#9790; Oscuro</span>
        <span class="theme-opt-light">&#9728; Claro</span>
      </button>
      <a href="/lab/?logout=1" class="lg-btn lg-btn-secondary">Cerrar sesión</a>
    </nav>
  </header>

  <main class="lg-container" style="padding: 36px 0 80px;">
    <section class="lg-card" style="margin-bottom: 24px;">
      <span class="lg-badge lg-badge-success">Python {py_ver} WSGI Activo</span>
      <h1 class="lg-hero-title" style="margin: 14px 0 10px;">Python 3.12 WSGI Microservice.</h1>
      <p style="color:var(--text-secondary); margin:0;">
        Ejecutándose sobre <code>{html.escape(sys.executable)}</code>.
      </p>
    </section>

    <section class="lg-card">
      <h2 class="lg-section-title" style="font-size:20px; margin-bottom:14px;">Estado del Servicio (JSON)</h2>
      <pre style="margin:0; overflow-x:auto; font-family:var(--font-mono); font-size:13px;">{json_pretty}</pre>
    </section>
  </main>

  <script src="../assets/js/liquid-glass.js"></script>
</body>
</html>"""
    body = page.encode("utf-8")
    start_response(
        "200 OK",
        [
            ("Content-Type", "text/html; charset=utf-8"),
            ("X-Robots-Tag", "noindex, nofollow"),
            ("Content-Length", str(len(body))),
        ],
    )
    return [body]
