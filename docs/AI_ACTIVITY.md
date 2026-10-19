# AI Activity Log — `carlossilveira-playground-kit`

All AI agent actions and modifications in this repository are recorded below in reverse-chronological order.

## 2026-10-19 — Initial Repository Architecture, Multi-Language Starters, CLI & Live `/lab/` Hub
- **Agent**: Antigravity Subagent (`81167431-e739-4fa8-8ae9-5aaf00d93022`)
- **Git Identity & Remote**: Initialized repository with local author `Carlos Silveira <carlos@personal.me>` and remote `git@github-personal:carlos-silveira/carlossilveira-playground-kit.git`.
- **Documentation & Agent Skills**:
  - Created `AGENTS.md`, `GEMINI.md`, `README.md`, and `docs/AI_ACTIVITY.md`.
  - Created `.agents/skills/techy-design-language/SKILL.md` (TechyNews × Human Who Codes design system with zero-FOUC `<meta name="color-scheme" content="light dark">` + `:has()` and `light-dark()` tokens).
  - Created `.agents/skills/spaceship-multi-runtime/SKILL.md` documenting exact CloudLinux paths on `server58.shared.spaceship.host` (`bfjydpuzth`): PHP 8.3 (`/opt/alt/php83/usr/bin/php`), Python 3.12 (`/opt/alt/python312/bin/python3`), Node.js 22/16 (`/opt/alt/alt-nodejs22/root/usr/bin/node` & `/opt/alt/alt-nodejs16/root/usr/bin/node`), Ruby 3.3 (`/opt/alt/ruby33/bin/ruby`), Go (`GOOS=linux GOARCH=amd64 CGO_ENABLED=0`), Rust (`x86_64-unknown-linux-musl`), and Java 21 GraalVM (`native-image --static --libc=musl`).
- **Starter Templates (`starters/`)**:
  - `starters/php-api/`: PHP 8.3 LiteSpeed starter (`index.php`, `.htaccess`, `README.md`, `AGENTS.md`).
  - `starters/python-django/`: Python 3.12 WSGI/CGI/Django starter (`passenger_wsgi.py`, `app.py`, `index.cgi`, `requirements.txt`, `.htaccess`, `README.md`, `AGENTS.md`).
  - `starters/nextjs-react/`: Next.js 15 / React 19 static export starter (`package.json`, `next.config.mjs` with `basePath: '/lab/nextjs-demo'` and `output: 'export'`, `app/layout.jsx`, `app/page.jsx`, `README.md`, `AGENTS.md`).
  - `starters/go-cgi/`: Go 1.23 hybrid HTTP/CGI starter (`main.go`, `go.mod`, `.htaccess`, `.github/workflows/deploy.yml`, `README.md`, `AGENTS.md`).
  - `starters/rust-cgi/`: Rust 2021 static musl CGI starter (`src/main.rs`, `Cargo.toml`, `.htaccess`, `.github/workflows/deploy.yml`, `README.md`, `AGENTS.md`).
  - `starters/ruby-rack/`: Ruby 3.3 Rack/CGI starter (`config.ru`, `index.cgi`, `Gemfile`, `.htaccess`, `README.md`, `AGENTS.md`).
  - `starters/java-graalvm/`: Java 21 GraalVM native binary starter (`Main.java`, `.htaccess`, `.github/workflows/deploy.yml`, `README.md`, `AGENTS.md`).
- **Scaffolding CLI (`scripts/new-lab.sh`)**:
  - Built executable `./scripts/new-lab.sh <stack> <slug>` script that scaffolds `/Users/carlossilveira/Documents/Personal/carlossilveiradev/lab-<slug>`, injects `.agents/skills/`, `AGENTS.md`, `GEMINI.md`, `docs/AI_ACTIVITY.md`, `.github/workflows/deploy.yml`, and configures Git identity + `github-personal` remote.
- **Deployable `/lab/` Hub & Live Demos (`public/`)**:
  - `public/index.html`: Interactive Multi-Language Playground Hub with live JSON API prober, CLI command builder, runtime matrix, and compliant two-state theme toggle.
  - `public/php-runtime/index.php` + `.htaccess`: Live PHP 8.3 Runtime Inspector & SHA-256/Array Benchmark.
  - `public/python-wsgi/index.cgi` + `passenger_wsgi.py` + `.htaccess`: Live Python 3.12 WSGI/CGI Telemetry & Benchmark micro-app.
  - `public/ruby-rack/index.cgi` + `config.ru` + `.htaccess`: Live Ruby 3.3 Runtime & GC/YJIT Inspector micro-app.
