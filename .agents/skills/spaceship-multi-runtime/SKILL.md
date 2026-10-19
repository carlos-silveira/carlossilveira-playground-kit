---
name: spaceship-multi-runtime
description: CloudLinux, LiteSpeed, Phusion Passenger, and CGI multi-language execution reference for server58.shared.spaceship.host (cPanel user bfjydpuzth) powering https://carlossilveira.dev/lab/. Activate this skill whenever creating or debugging PHP, Python, Node.js, Ruby, Go, Rust, or Java apps deployed to Spaceship shared hosting.
---

# Spaceship Multi-Runtime Execution Guide (`server58.shared.spaceship.host`)

> **MANDATORY**: Consult this skill whenever configuring `.htaccess`, shebang lines (`#!`), Phusion Passenger entrypoints, or GitHub Actions build workflows for `https://carlossilveira.dev/lab/<slug>/`.

## 1. Host Environment Specifications

- **Server**: `server58.shared.spaceship.host` (`66.29.148.179`) — CloudLinux 8/9 + LiteSpeed Enterprise Web Server + CageFS
- **Architecture**: `x86_64` (`amd64` GNU/Linux)
- **cPanel User**: `bfjydpuzth`
- **SSH Alias**: `spaceship-carlos` (`ssh spaceship-carlos` -> port `21098`)
- **Document Root**: `/home/bfjydpuzth/carlossilveira.dev`
- **Playground Root**: `/home/bfjydpuzth/carlossilveira.dev/lab/<slug>/`
- **WordPress Pass-Through**: The root `/home/bfjydpuzth/carlossilveira.dev/.htaccess` explicitly bypasses WordPress rewrite rules for `/lab/`, `/tools/`, and `/_deploy/`.

---

## 2. Exact CloudLinux Binary Paths on `server58`

| Language / Runtime | Primary Binary Path | Additional / Alternate Paths | Execution Mode on LiteSpeed |
| :--- | :--- | :--- | :--- |
| **PHP 8.3 (Default)** | `/opt/alt/php83/usr/bin/php` | `/opt/alt/php84/usr/bin/php`, `/opt/alt/php85/usr/bin/php` | Native LiteSpeed SAPI (`index.php`) |
| **Python 3.12** | `/opt/alt/python312/bin/python3` | `/opt/alt/python311/bin/python3`, `/opt/alt/python313/bin/python3` | Phusion Passenger (`passenger_wsgi.py`) OR CGI (`#!/opt/alt/python312/bin/python3`) |
| **Node.js 22 / 16** | `/opt/alt/alt-nodejs22/root/usr/bin/node` | `/opt/alt/alt-nodejs16/root/usr/bin/node` | Static Export in GitHub Actions (preferred) or Passenger Node 16 SSR |
| **Ruby 3.3** | `/opt/alt/ruby33/bin/ruby` | `/opt/alt/ruby33/bin/bundle`, `/opt/alt/ruby33/bin/rackup` | Phusion Passenger (`config.ru`) OR CGI (`#!/opt/alt/ruby33/bin/ruby`) |
| **Go (Golang)** | Compiled in GitHub Actions | `GOOS=linux GOARCH=amd64 CGO_ENABLED=0` | `.htaccess` CGI (`index.cgi` with `chmod 755`) |
| **Rust** | Compiled in GitHub Actions | `--target x86_64-unknown-linux-musl` | `.htaccess` CGI (`index.cgi` with `chmod 755`) |
| **Java 21 (GraalVM)** | Compiled in GitHub Actions | `native-image --static --libc=musl` | `.htaccess` CGI (`index.cgi` with `chmod 755`) or CNAME to external container |

---

## 3. Per-Runtime Configuration Blueprints

### A. PHP (`php-api`)
- LiteSpeed executes `.php` natively with zero overhead.
- Route clean URLs to `index.php` via `.htaccess`:
  ```apache
  DirectoryIndex index.php
  RewriteEngine On
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule ^(.*)$ index.php [QSA,L]
  ```

### B. Python 3.12 (`python-django` / WSGI / CGI)
- **Zero-Setup Instant Execution (CGI + WSGI hybrid)**:
  - Use `#!/opt/alt/python312/bin/python3` as line 1 of `index.cgi` (`chmod 755 index.cgi`) with `wsgiref.handlers.CGIHandler().run(application)`. This works immediately on deployment without manual cPanel Setup Python App registration.
  - Also provide `passenger_wsgi.py` exporting `application` so the app can be switched to persistent Phusion Passenger at any time.

### C. Node.js / Next.js (`nextjs-react`)
- **CRITICAL GOTCHA**: CloudLinux CageFS enforces virtual memory limits that cause WebAssembly allocations inside Node 18/20/22 (`npm run build`, SWC, Turbopack) to fail with `WebAssembly.instantiate(): Out of memory` if run directly on the shared server.
- **Solution**: **ALWAYS run `npm ci && npm run build` inside GitHub Actions** (`ubuntu-latest`) and deploy the static export (`out/`) or pre-built bundle via `rsync`/SSH.
- Set `basePath: '/lab/<slug>'` and `output: 'export'` in `next.config.mjs`.

### D. Go (`go-cgi`) & Rust (`rust-cgi`)
- Cross-compile a static Linux `amd64` binary named `index.cgi` in GitHub Actions:
  - **Go**: `CGO_ENABLED=0 GOOS=linux GOARCH=amd64 go build -ldflags="-s -w" -o index.cgi .`
  - **Rust**: `cargo build --release --target x86_64-unknown-linux-musl && cp target/x86_64-unknown-linux-musl/release/app index.cgi`
- Configure `.htaccess` in `/lab/<slug>/`:
  ```apache
  Options +ExecCGI -Indexes
  AddHandler cgi-script .cgi
  DirectoryIndex index.cgi
  RewriteEngine On
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule ^(.*)$ index.cgi/$1 [QSA,L]
  ```
- Ensure executable permissions on deploy: `chmod 755 index.cgi`.

### E. Ruby 3.3 (`ruby-rack`)
- Provide `index.cgi` (`#!/opt/alt/ruby33/bin/ruby`, `chmod 755`) for zero-registration execution on LiteSpeed, alongside `config.ru` + `Gemfile` for Phusion Passenger Rack mode.

### F. Java 21 GraalVM (`java-graalvm`)
- Because standard JVM startups exceed shared hosting memory limits, compile Java 21 source code in GitHub Actions using GraalVM `native-image` (`--no-fallback -H:+ReportExceptionStackTraces`) into a standalone Linux `x86_64` binary `index.cgi` (`chmod 755`), or proxy/CNAME heavy stateful services to an external container.
