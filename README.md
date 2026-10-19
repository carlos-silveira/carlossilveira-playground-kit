# Carlos Silveira — Multi-Stack Playground Kit (`carlossilveira.dev/lab/`)

[![Live Lab Hub](https://img.shields.io/badge/Live_Hub-carlossilveira.dev%2Flab%2F-00b4ff?style=flat-square)](https://carlossilveira.dev/lab/)
[![Account](https://img.shields.io/badge/GitHub-carlos--silveira-02040a?style=flat-square)](https://github.com/carlos-silveira)

Starter templates, CloudLinux multi-runtime wrappers, and a zero-friction CLI generator (`./scripts/new-lab.sh`) for spinning up polyglot web applications and experiments at **`https://carlossilveira.dev/lab/<slug>/`** on Spaceship Web Hosting Essential (`server58.shared.spaceship.host`).

---

## Live `/lab/` Hub & Built-in Runtime Demos (`public/`)

The `public/` directory deploys directly to `/home/bfjydpuzth/carlossilveira.dev/lab/` and includes:

| Endpoint | Runtime on `server58` | Description |
| :--- | :--- | :--- |
| [`/lab/`](https://carlossilveira.dev/lab/) | Static HTML5 + CSS `light-dark()` | Interactive Multi-Language Playground Hub, Runtime Matrix & CLI Generator Builder. |
| [`/lab/php-runtime/`](https://carlossilveira.dev/lab/php-runtime/) | LiteSpeed PHP 8.3 (`/opt/alt/php83/usr/bin/php`) | Live PHP 8.3 Runtime Inspector, OPcache/JIT Telemetry & CPU/Hash Benchmark (`?format=json` supported). |
| [`/lab/python-wsgi/`](https://carlossilveira.dev/lab/python-wsgi/) | CloudLinux Python 3.12 (`/opt/alt/python312/bin/python3`) | Live Python 3.12 CGI/WSGI Micro-App & JSON Telemetry API (`?format=json` supported). |
| [`/lab/ruby-rack/`](https://carlossilveira.dev/lab/ruby-rack/) | CloudLinux Ruby 3.3 (`/opt/alt/ruby33/bin/ruby`) | Live Ruby 3.3 CGI/Rack Micro-App & Fiber/GC Inspector (`?format=json` supported). |

---

## Starter Templates (`starters/`)

| Starter | Stack | Execution Model on Spaceship `server58` |
| :--- | :--- | :--- |
| [`starters/php-api`](./starters/php-api) | PHP 8.3 / 8.5 | Native LiteSpeed SAPI with clean `.htaccess` front-controller routing and JSON/HTML response negotiation. |
| [`starters/python-django`](./starters/python-django) | Python 3.12 (WSGI / Django-ready) | Dual Phusion Passenger (`passenger_wsgi.py`) + executable CGI bridge (`index.cgi` via `/opt/alt/python312/bin/python3`). |
| [`starters/nextjs-react`](./starters/nextjs-react) | Next.js 15 / React 19 | Pre-built in GitHub Actions (`output: 'export'`, `basePath: '/lab/<slug>'`) to bypass CloudLinux CageFS Wasm memory limits. |
| [`starters/go-cgi`](./starters/go-cgi) | Go 1.23 (`net/http/cgi`) | Hybrid HTTP/CGI binary cross-compiled in GitHub Actions (`GOOS=linux GOARCH=amd64 CGO_ENABLED=0`) and executed via `.htaccess` CGI. |
| [`starters/rust-cgi`](./starters/rust-cgi) | Rust 2021 (`x86_64-unknown-linux-musl`) | Zero-dependency static Linux binary compiled in GitHub Actions and served at native speed via `.htaccess` CGI. |
| [`starters/ruby-rack`](./starters/ruby-rack) | Ruby 3.3 (Rack / Sinatra-ready) | Dual Phusion Passenger (`config.ru`) + executable CGI bridge (`index.cgi` via `/opt/alt/ruby33/bin/ruby`). |
| [`starters/java-graalvm`](./starters/java-graalvm) | Java 21 (GraalVM Native Image) | Ahead-of-time compiled via `native-image --static --libc=musl` in GitHub Actions into an instant-startup Linux CGI binary. |

---

## Quick Start: Spin Up a New Experiment in 10 Seconds

Run `./scripts/new-lab.sh <stack> <slug>` from this repository:

```bash
./scripts/new-lab.sh go-cgi url-shortener
```

What `new-lab.sh` automates:
1. Copies `starters/<stack>` into `/Users/carlossilveira/Documents/Personal/carlossilveiradev/lab-<slug>`.
2. Replaces template placeholders (`__LAB_SLUG__`, `__LAB_STACK__`) across configs, code, and `.htaccess`.
3. Copies `.agents/skills/` (`techy-design-language` + `spaceship-multi-runtime`), `AGENTS.md`, and initializes `docs/AI_ACTIVITY.md`.
4. Initializes Git with local identity (`Carlos Silveira <carlos@personal.me>`) and remote `git@github-personal:carlos-silveira/lab-<slug>.git`.
5. Generates `.github/workflows/deploy.yml` configured to build and deploy directly to `/home/bfjydpuzth/carlossilveira.dev/lab/<slug>/`.
