# AGENTS.md — Carlos Silveira Multi-Stack Playground Kit (`carlossilveira.dev/lab/`)

> **READ THIS FIRST.** You are working in `carlossilveira-playground-kit`, the multi-language starter repository, CLI scaffolding generator, and `/lab/` hub for **[carlossilveira.dev/lab/](https://carlossilveira.dev/lab/)**.

## 1. Repository Architecture

| Path | Purpose |
| :--- | :--- |
| `public/` | Deployable `/lab/` Hub (`index.html`) and live out-of-the-box language runtime micro-apps (`php-runtime/`, `python-wsgi/`, `ruby-rack/`, `go-runtime/`). |
| `starters/` | Multi-language starter templates for `php-api`, `python-django`, `nextjs-react`, `go-cgi`, `rust-cgi`, `ruby-rack`, and `java-graalvm`. |
| `scripts/new-lab.sh` | Executable CLI (`./scripts/new-lab.sh <stack> <slug>`) that scaffolds a new `/lab/<slug>` experiment repository with Git, skills, and CI/CD pre-configured. |
| `.agents/skills/` | Reusable AI Agent Skills (`apple-ui-design` and `spaceship-multi-runtime`) automatically copied into every new lab repo. |
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

## 4. Mandatory Design System: Apple UI Design System (`.agents/skills/apple-ui-design/SKILL.md`)

**STRICT RULE**: Do NOT use "hacker / terminal / cyberpunk / skynet" aesthetics (no pulsing neon dots, no `//` or `[01]` prefixes, no `NODE_58` / `SYS_READY` jargon, no dark-by-default neon cyan `#00b4ff` grids).
All UI templates and frontends must follow `.agents/skills/apple-ui-design/SKILL.md`:
- **Typography**:
  - `font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'SF Pro Text', 'Segoe UI', sans-serif;`
  - `--text-hero: 600 48px/1.1`
  - `--text-title: 600 32px/1.2`
  - `--text-body: 400 17px/1.5`
  - `--text-caption: 400 13px/1.4`
- **Color Philosophy**:
  - **Light Mode**: `--bg-primary: #ffffff`, `--bg-secondary: #f5f5f7`, `--text-primary: #1d1d1f`, `--text-secondary: #86868b`, `--accent: #0071e3`.
  - **Dark Mode**: `--bg-primary: #000000`, `--bg-secondary: #1d1d1f`, `--text-primary: #f5f5f7`, `--text-secondary: #86868b`, `--accent: #0071e3`.
- **Spacing Rhythm**: `4px / 8px / 16px / 24px / 48px / 96px`.
- **Cards**: `background: rgba(255,255,255,0.8)` (dark `rgba(29,29,31,0.8)`), `backdrop-filter: blur(20px)`, `border-radius: 18px`, `border: 1px solid rgba(0,0,0,0.05)`, `box-shadow: 0 4px 24px rgba(0,0,0,0.06)`.
- **Pill Buttons**: `background: #0071e3`, `color: white`, `padding: 12px 24px`, `border-radius: 980px`, `font-weight: 500`, `min-height: 44px`, `transition: all 0.2s ease`, `:hover { transform: scale(1.02); filter: brightness(1.1); }`.
- **Color Scheme Toggle**: Use `<meta name="color-scheme" content="light dark">`, CSS `light-dark()`, and `:root:has(> head > meta[name="color-scheme"][content="..."])` without FOUC.

---

## 5. AI Activity Trail Mandate

Every AI agent modification in this repository **must** be logged in `docs/AI_ACTIVITY.md` in reverse-chronological order.
