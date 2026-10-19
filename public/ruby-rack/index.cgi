#!/opt/alt/ruby33/bin/ruby
# frozen_string_literal: true

# Carlos Silveira Playground — Ruby 3.3 Runtime & GC Inspector
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
  title: 'Ruby 3.3 Runtime & GC Inspector',
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
    <title>Ruby #{RUBY_VERSION} Runtime — Carlos Silveira Lab</title>
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

      <div class="eyebrow">Ruby #{RUBY_VERSION} Runtime</div>
      <h1>Ruby 3.3 Runtime &amp; GC Inspector.</h1>
      <p class="subtitle">
        Running via <code>#{CGI.escapeHTML(RbConfig.ruby)}</code>.
      </p>

      <div class="grid">
        <div class="card">
          <div class="stat-label">Ruby Engine</div>
          <div class="stat-val">#{RUBY_ENGINE} #{RUBY_VERSION}</div>
        </div>
        <div class="card">
          <div class="stat-label">SHA-256 Throughput</div>
          <div class="stat-val">#{ops_sec} /s</div>
        </div>
        <div class="card">
          <div class="stat-label">Live Heap Slots</div>
          <div class="stat-val">#{gc_stat[:heap_live_slots]}</div>
        </div>
        <div class="card">
          <div class="stat-label">Execution Time</div>
          <div class="stat-val">#{total_ms} ms</div>
        </div>
      </div>

      <div class="controls">
        <a class="btn-secondary" href="?rounds=10000">10,000 Rounds</a>
        <a class="btn-secondary" href="?rounds=25000">25,000 Rounds</a>
        <a class="btn-secondary" href="?rounds=75000">75,000 Rounds</a>
      </div>

      <div class="card">
        <div class="stat-label" style="margin-bottom: var(--space-sm);">JSON Payload</div>
        <pre>#{escaped_json}</pre>
      </div>
    </main>
  </body>
  </html>
HTML
