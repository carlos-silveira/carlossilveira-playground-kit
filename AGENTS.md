# AGENTS.md — Carlos Silveira Multi-Stack Playground Kit (`carlossilveira.dev/lab/`)

> **READ THIS FIRST.** You are working in `carlossilveira-playground-kit`, the multi-language starter repository, CLI scaffolding generator, and `/lab/` hub for **[carlossilveira.dev/lab/](https://carlossilveira.dev/lab/)**.

## 1. Repository Architecture

| Path | Purpose |
| :--- | :--- |
| `public/` | Deployable `/lab/` Hub (`index.html`) and live out-of-the-box language runtime micro-apps (`php-runtime/`, `python-wsgi/`, `ruby-rack/`). |
| `starters/` | Multi-language starter templates for `php-api`, `python-django`, `nextjs-react`, `go-cgi`, `rust-cgi`, `ruby-rack`, and `java-graalvm`. |
| `scripts/new-lab.sh` | Executable CLI (`./scripts/new-lab.sh <stack> <slug>`) that scaffolds a new `/lab/<slug>` experiment repository with Git, skills, and CI/CD pre-configured. |
| `.agents/skills/` | Reusable AI Agent Skills (`techy-design-language` and `spaceship-multi-runtime`) automatically copied into every new lab repo. |
| `docs/AI_ACTIVITY.md` | Reverse-chronological log of all AI-driven changes in this repository. |

---

## 2. Mandatory Git & GitHub Rules (`carlos-silveira` ONLY)

1. **Always use the `github-personal` SSH host alias** for remotes:
   ```bash
   git remote set-url origin git@github-personal:carlos-silveira/carlossilveira-playground-kit.git
   ```
2. **Always set local Git author identity** before committing:
   ```bash
   git config user.name "Carlos Silveira"
   git config user.email "carlos@personal.me"
   ```
3. **Never push to `carlos-silveira-h`** (work account).

---

## 3. CloudLinux & LiteSpeed Runtime Matrix (`server58.shared.spaceship.host`)

- **SSH Target**: `spaceship-carlos` (`bfjydpuzth@server58.shared.spaceship.host -p 21098`)
- **Lab Root**: `/home/bfjydpuzth/carlossilveira.dev/lab/`
- **Exact Runtime Paths**:
  - **PHP**: `/opt/alt/php83/usr/bin/php` (also `php84`, `php85`) — Native LiteSpeed SAPI
  - **Python**: `/opt/alt/python312/bin/python3` (also `python311`, `python313`) — Phusion Passenger WSGI (`passenger_wsgi.py`) or CGI (`#!/opt/alt/python312/bin/python3`)
  - **Node.js**: `/opt/alt/alt-nodejs22/root/usr/bin/node` (compile/build in GitHub Actions to avoid CageFS Wasm memory limits; use `/opt/alt/alt-nodejs16/root/usr/bin/node` if running persistent Passenger SSR)
  - **Ruby**: `/opt/alt/ruby33/bin/ruby` — Phusion Passenger Rack (`config.ru`) or CGI (`#!/opt/alt/ruby33/bin/ruby`)
  - **Go**: Compile static Linux `amd64` binary in GitHub Actions (`GOOS=linux GOARCH=amd64 CGO_ENABLED=0`) and serve via `.htaccess` CGI (`Options +ExecCGI`, `AddHandler cgi-script .cgi`)
  - **Rust**: Compile static Linux binary in GitHub Actions (`x86_64-unknown-linux-musl`) and serve via `.htaccess` CGI
  - **Java**: Compile GraalVM Native Image (`native-image --static --libc=musl`) in GitHub Actions to a standalone Linux CGI binary, or route via CNAME to an external container.

---

## 4. Design System & Modern Web Standards

All UI templates and frontends must follow `.agents/skills/techy-design-language/SKILL.md`:
- **Dark Canvas**: `#02040a` (surfaces `#0a0f1c`, `rgba(255,255,255,0.02)`–`0.05`, border `rgba(255,255,255,0.08)`)
- **Light Canvas**: `#f8f6f6` (cards `#ffffff`, border `rgba(0,0,0,0.08)`)
- **Primary Accent**: `#00b4ff` (Electric Cyan) + `#2b7cee` (Royal Blue)
- **Color Scheme Toggle**: Use `<meta name="color-scheme" content="light dark">`, CSS `light-dark()`, and `:root:has(> head > meta[name="color-scheme"][content="..."])` without FOUC.

---

## 5. AI Activity Trail Mandate

Every AI agent modification in this repository **must** be logged in `docs/AI_ACTIVITY.md` in reverse-chronological order.
