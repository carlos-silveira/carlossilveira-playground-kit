#!/opt/alt/ruby33/bin/ruby
# frozen_string_literal: true

# Carlos Silveira Playground — Live Ruby 3.3 Runtime & GC/YJIT Telemetry Micro-App
# Live URL: https://carlossilveira.dev/lab/ruby-rack/
# Binary on Spaceship server58: /opt/alt/ruby33/bin/ruby

require 'json'
require 'time'
require 'digest'
require 'cgi'
require 'rbconfig'

t0 = Process.clock_gettime(Process::CLOCK_MONOTONIC)

query = CGI.parse(ENV['QUERY_STRING'].to_s)
accept = ENV['HTTP_ACCEPT'].to_s
wants_json = query['format']&.first == 'json' || accept.include?('application/json')

rounds = (query['rounds']&.first || '25000').to_i.clamp(1000, 150_000)

hash_t0 = Process.clock_gettime(Process::CLOCK_MONOTONIC)
digest = 'carlossilveira-ruby-33'
rounds.times do |i|
  digest = Digest::SHA256.hexdigest("#{digest}#{i}")
end
hash_ms = ((Process.clock_gettime(Process::CLOCK_MONOTONIC) - hash_t0) * 1000.0).round(3)

gc_stat = GC.stat
yjit_enabled = defined?(RubyVM::YJIT) && RubyVM::YJIT.respond_to?(:enabled?) ? RubyVM::YJIT.enabled? : false
total_ms = ((Process.clock_gettime(Process::CLOCK_MONOTONIC) - t0) * 1000.0).round(3)

payload = {
  ok: true,
  app: 'ruby-rack',
  title: 'Ruby 3.3 Runtime & YJIT/GC Inspector',
  runtime: {
    ruby_version: RUBY_VERSION,
    ruby_description: RUBY_DESCRIPTION,
    platform: RUBY_PLATFORM,
    engine: RUBY_ENGINE,
    binary_path: RbConfig.ruby,
    yjit_enabled: yjit_enabled,
    pid: Process.pid
  },
  gc: {
    count: gc_stat[:count],
    heap_allocated_pages: gc_stat[:heap_allocated_pages],
    heap_live_slots: gc_stat[:heap_live_slots],
    total_allocated_objects: gc_stat[:total_allocated_objects]
  },
  benchmark: {
    sha256_rounds: rounds,
    sha256_duration_ms: hash_ms,
    sha256_ops_per_sec: hash_ms.positive? ? ((rounds / hash_ms) * 1000).round : 0,
    digest_sample: "#{digest[0, 16]}...",
    total_request_ms: total_ms
  },
  timestamp: Time.now.utc.iso8601
}

if wants_json
  print "Status: 200 OK\r\n"
  print "Content-Type: application/json; charset=utf-8\r\n"
  print "Access-Control-Allow-Origin: *\r\n\r\n"
  puts JSON.pretty_generate(payload)
  exit 0
end

escaped_json = CGI.escapeHTML(JSON.pretty_generate(payload))
ops_sec = payload[:benchmark][:sha256_ops_per_sec].to_s.reverse.gsub(/(\d{3})(?=\d)/, '\\1,').reverse

print "Status: 200 OK\r\n"
print "Content-Type: text/html; charset=utf-8\r\n\r\n"
puts <<~HTML
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
    <title>Ruby #{RUBY_VERSION} Runtime &amp; GC Inspector — carlossilveira.dev/lab/</title>
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
      .controls { display: flex; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 2rem; }
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
      .btn.primary { background: var(--primary); color: #02040a; border-color: var(--primary); }
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

      <div class="badge">LIVE RUNTIME // CLOUDLINUX RUBY #{RUBY_VERSION}</div>
      <h1>Ruby 3.3 Runtime &amp; GC Inspector.</h1>
      <p class="subtitle">
        Executing on <code>server58.shared.spaceship.host</code> via <code>#{CGI.escapeHTML(RbConfig.ruby)}</code>.
      </p>

      <div class="grid">
        <div class="stat">
          <div class="stat-label">RUBY ENGINE</div>
          <div class="stat-val">#{RUBY_ENGINE} #{RUBY_VERSION}</div>
        </div>
        <div class="stat">
          <div class="stat-label">SHA-256 THROUGHPUT</div>
          <div class="stat-val">#{ops_sec} ops/s</div>
        </div>
        <div class="stat">
          <div class="stat-label">LIVE HEAP SLOTS</div>
          <div class="stat-val">#{gc_stat[:heap_live_slots]}</div>
        </div>
        <div class="stat">
          <div class="stat-label">EXECUTION TIME</div>
          <div class="stat-val">#{total_ms} ms</div>
        </div>
      </div>

      <div class="controls">
        <a class="btn" href="?rounds=10000">10k Hashes</a>
        <a class="btn" href="?rounds=25000">25k Hashes</a>
        <a class="btn" href="?rounds=75000">75k Hashes</a>
      </div>

      <div class="badge" style="margin-bottom:0.75rem;">LIVE RUBY VM &amp; GC TELEMETRY</div>
      <pre>#{escaped_json}</pre>
    </main>
  </body>
  </html>
HTML
