#!/opt/alt/ruby33/bin/ruby
# frozen_string_literal: true

# Carlos Silveira Private Lab — Ruby 3.3 Rack Micro-App
# Live URL: https://carlossilveira.dev/lab/ruby-rack/

require 'json'
require 'time'
require 'cgi'
require 'rbconfig'

raw_cookie = ENV['HTTP_COOKIE'].to_s
unless raw_cookie.include?('cs_lab_auth=') || raw_cookie.include?('wordpress_logged_in_')
  print "Status: 302 Found\r\n"
  print "Location: /lab/\r\n"
  print "X-Robots-Tag: noindex, nofollow\r\n\r\n"
  exit 0
end

query = CGI.parse(ENV['QUERY_STRING'].to_s)
accept = ENV['HTTP_ACCEPT'].to_s
wants_json = query['format']&.first == 'json' || accept.include?('application/json')

payload = {
  ok: true,
  app: 'ruby-rack',
  title: 'Ruby 3.3 Rack Micro-App',
  runtime: {
    ruby_version: RUBY_VERSION,
    platform: RUBY_PLATFORM,
    engine: RUBY_ENGINE,
    binary_path: RbConfig.ruby,
    pid: Process.pid
  },
  timestamp: Time.now.utc.iso8601
}

if wants_json
  print "Status: 200 OK\r\n"
  print "Content-Type: application/json; charset=utf-8\r\n"
  print "X-Robots-Tag: noindex, nofollow\r\n\r\n"
  puts JSON.pretty_generate(payload)
  exit 0
end

escaped_json = CGI.escapeHTML(JSON.pretty_generate(payload))

print "Status: 200 OK\r\n"
print "Content-Type: text/html; charset=utf-8\r\n"
print "X-Robots-Tag: noindex, nofollow\r\n\r\n"
puts <<~HTML
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
    <title>Ruby #{RUBY_VERSION} Micro-App — Carlos Silveira Lab</title>
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
        <span class="lg-badge lg-badge-success">Ruby #{RUBY_VERSION} Activo</span>
        <h1 class="lg-hero-title" style="margin: 14px 0 10px;">Ruby 3.3 Rack Micro-App.</h1>
        <p style="color:var(--text-secondary); margin:0;">
          Ejecutándose sobre <code>#{CGI.escapeHTML(RbConfig.ruby)}</code>.
        </p>
      </section>

      <section class="lg-card">
        <h2 class="lg-section-title" style="font-size:20px; margin-bottom:14px;">Estado del Servicio (JSON)</h2>
        <pre style="margin:0; overflow-x:auto; font-family:var(--font-mono); font-size:13px;">#{escaped_json}</pre>
      </section>
    </main>

    <script src="../assets/js/liquid-glass.js"></script>
  </body>
  </html>
HTML
