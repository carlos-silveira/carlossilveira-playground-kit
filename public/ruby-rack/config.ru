# frozen_string_literal: true

# Phusion Passenger Rack Entrypoint for https://carlossilveira.dev/lab/ruby-rack/
# Binary on Spaceship server58: /opt/alt/ruby33/bin/ruby

require 'json'
require 'time'
require 'digest'
require 'cgi'
require 'rbconfig'

app = lambda do |env|
  t0 = Process.clock_gettime(Process::CLOCK_MONOTONIC)
  query = CGI.parse(env['QUERY_STRING'].to_s)
  accept = env['HTTP_ACCEPT'].to_s
  wants_json = query['format']&.first == 'json' || accept.include?('application/json')

  rounds = (query['rounds']&.first || '25000').to_i.clamp(1000, 150_000)
  digest = 'carlossilveira-ruby-33'
  rounds.times { |i| digest = Digest::SHA256.hexdigest("#{digest}#{i}") }
  total_ms = ((Process.clock_gettime(Process::CLOCK_MONOTONIC) - t0) * 1000.0).round(3)

  payload = {
    ok: true,
    app: 'ruby-rack',
    mode: 'passenger-rack',
    ruby_version: RUBY_VERSION,
    binary_path: RbConfig.ruby,
    gc_live_slots: GC.stat[:heap_live_slots],
    total_request_ms: total_ms,
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

  escaped_json = CGI.escapeHTML(JSON.pretty_generate(payload))
  html = <<~HTML
    <!doctype html>
    <html lang="en">
    <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="color-scheme" content="light dark">
      <title>Ruby #{RUBY_VERSION} Rack — Carlos Silveira Lab</title>
      <style>
        :root {
          color-scheme: light dark;
          --font-sans: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'SF Pro Text', 'Segoe UI', sans-serif;
          --font-mono: 'SF Mono', ui-monospace, Menlo, Monaco, Consolas, monospace;
          --text-hero: 600 48px/1.1 var(--font-sans);
          --text-body: 400 17px/1.5 var(--font-sans);
          --text-caption: 400 13px/1.4 var(--font-sans);
          --bg-primary: light-dark(#ffffff, #000000);
          --bg-secondary: light-dark(#f5f5f7, #1d1d1f);
          --text-primary: light-dark(#1d1d1f, #f5f5f7);
          --text-secondary: #86868b;
          --accent: #0071e3;
          --card-bg: light-dark(rgba(255,255,255,0.8), rgba(29,29,31,0.8));
          --card-border: light-dark(rgba(0,0,0,0.05), rgba(255,255,255,0.08));
        }
        body {
          margin: 0;
          font: var(--text-body);
          background: var(--bg-secondary);
          color: var(--text-primary);
          padding: 48px 24px 96px;
        }
        .wrap { max-width: 680px; margin: 0 auto; }
        h1 { font: var(--text-hero); margin: 8px 0 16px; }
        .card {
          background: var(--card-bg);
          backdrop-filter: blur(20px);
          border-radius: 18px;
          border: 1px solid var(--card-border);
          box-shadow: 0 4px 24px rgba(0,0,0,0.06);
          padding: 24px;
        }
        pre { margin: 0; overflow-x: auto; font-family: var(--font-mono); font-size: 13px; }
        a { color: var(--accent); text-decoration: none; }
      </style>
    </head>
    <body>
      <main class="wrap">
        <p><a href="/lab/">&larr; Back to Engineering Lab</a></p>
        <h1>Ruby #{RUBY_VERSION} Rack</h1>
        <div class="card">
          <pre>#{escaped_json}</pre>
        </div>
      </main>
    </body>
    </html>
  HTML

  [200, { 'content-type' => 'text/html; charset=utf-8', 'content-length' => html.bytesize.to_s }, [html]]
end

run app
