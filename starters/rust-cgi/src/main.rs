//! Carlos Silveira Lab Starter — Rust CGI/HTTP Micro-App
//! Target URL: https://carlossilveira.dev/lab/__LAB_SLUG__/
//! Compiled as a zero-dependency static Linux musl binary (`x86_64-unknown-linux-musl`).

use std::env;
use std::time::{SystemTime, UNIX_EPOCH};

const LAB_SLUG: &str = "__LAB_SLUG__";

fn escape_html(input: &str) -> String {
    input
        .replace('&', "&amp;")
        .replace('<', "&lt;")
        .replace('>', "&gt;")
        .replace('"', "&quot;")
}

fn main() {
    let method = env::var("REQUEST_METHOD").unwrap_or_else(|_| "GET".to_string());
    let uri = env::var("REQUEST_URI").unwrap_or_else(|_| "/lab/__LAB_SLUG__/".to_string());
    let query = env::var("QUERY_STRING").unwrap_or_default();
    let accept = env::var("HTTP_ACCEPT").unwrap_or_default();
    let unix_ts = SystemTime::now()
        .duration_since(UNIX_EPOCH)
        .map(|d| d.as_secs())
        .unwrap_or(0);

    let wants_json = query.contains("format=json")
        || accept.contains("application/json")
        || uri.ends_with("/api");

    let json_payload = format!(
        r#"{{
  "ok": true,
  "lab": "{lab}",
  "stack": "rust-cgi",
  "runtime": "Rust 2021 (x86_64-unknown-linux-musl)",
  "method": "{method}",
  "uri": "{uri}",
  "unix_timestamp": {unix_ts}
}}"#,
        lab = LAB_SLUG,
        method = method,
        uri = uri.replace('"', "\\\""),
        unix_ts = unix_ts
    );

    if wants_json {
        print!("Status: 200 OK\r\n");
        print!("Content-Type: application/json; charset=utf-8\r\n");
        print!("Access-Control-Allow-Origin: *\r\n\r\n");
        println!("{}", json_payload);
        return;
    }

    print!("Status: 200 OK\r\n");
    print!("Content-Type: text/html; charset=utf-8\r\n\r\n");
    println!(
        r#"<!doctype html>
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
  <title>{slug} — Rust Static Musl CGI Lab | carlossilveira.dev</title>
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
    <div class="badge">RUST STATIC MUSL CGI STARTER // {slug}</div>
    <h1>{slug}</h1>
    <p>Zero-dependency Rust 2021 binary (<code>x86_64-unknown-linux-musl</code>). Append <a href="?format=json"><code>?format=json</code></a> for JSON output.</p>
    <pre>{json}</pre>
    <p><a href="/lab/">&larr; Back to carlossilveira.dev/lab/</a></p>
  </main>
</body>
</html>"#,
        slug = escape_html(LAB_SLUG),
        json = escape_html(&json_payload)
    );
}
