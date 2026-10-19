# frozen_string_literal: true

# Carlos Silveira Lab Starter — Ruby 3.3 Rack Micro-App
# Target URL: https://carlossilveira.dev/lab/__LAB_SLUG__/
# Runtime: /opt/alt/ruby33/bin/ruby (Phusion Passenger Rack or CGI)

require 'json'
require 'time'
require 'cgi'

LAB_SLUG = '__LAB_SLUG__'

app = lambda do |env|
  query = CGI.parse(env['QUERY_STRING'].to_s)
  accept = env['HTTP_ACCEPT'].to_s
  path = env['PATH_INFO'].to_s
  wants_json = query['format']&.first == 'json' || accept.include?('application/json') || path.end_with?('/api')

  payload = {
    ok: true,
    lab: LAB_SLUG,
    stack: 'ruby-rack',
    runtime: "Ruby #{RUBY_VERSION} (#{RUBY_PLATFORM})",
    binary: '/opt/alt/ruby33/bin/ruby',
    method: env['REQUEST_METHOD'] || 'GET',
    path: path.empty? ? '/' : path,
    timestamp: Time.now.utc.iso8601
  }

  if wants_json
    body = JSON.pretty_generate(payload)
    return [
      200,
      {
        'content-type' => 'application/json; charset=utf-8',
        'access-control-allow-origin' => '*',
        'content-length' => body.bytesize.to_s
      },
      [body]
    ]
  end

  escaped_slug = CGI.escapeHTML(LAB_SLUG)
  escaped_json = CGI.escapeHTML(JSON.pretty_generate(payload))
  html = <<~HTML
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
      <title>#{escaped_slug} — Ruby #{RUBY_VERSION} Rack Lab | carlossilveira.dev</title>
      <style>
        :root {
          color-scheme: light dark;
          --bg: light-dark(#f8f6f6, #02040a);
          --card: light-dark(#ffffff, #0a0f1c);
          --border: light-dark(rgba(0,0,0,0.08), rgba(255,255,255,0.08));
          --text: light-dark(#0a0f1c, #f8fafc);
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
        <div class="badge">RUBY 3.3 RACK STARTER // #{escaped_slug}</div>
        <h1>#{escaped_slug}</h1>
        <p>Running on CloudLinux Ruby <code>#{RUBY_VERSION}</code>. Append <a href="?format=json"><code>?format=json</code></a> for JSON output.</p>
        <pre>#{escaped_json}</pre>
        <p><a href="/lab/">&larr; Back to carlossilveira.dev/lab/</a></p>
      </main>
    </body>
    </html>
  HTML

  [
    200,
    {
      'content-type' => 'text/html; charset=utf-8',
      'content-length' => html.bytesize.to_s
    },
    [html]
  ]
end

run app
