# AGENTS.md — Next.js / React Lab (`__LAB_SLUG__`)

- **Repository**: `carlos-silveira/lab-__LAB_SLUG__` (`git@github-personal:carlos-silveira/lab-__LAB_SLUG__.git`)
- **Git Identity**: `Carlos Silveira <carlos@personal.me>`
- **Target Path on `server58`**: `/home/bfjydpuzth/carlossilveira.dev/lab/__LAB_SLUG__/`
- **Build Rule**: NEVER run `npm run build` on `server58` via SSH; always build in GitHub Actions (`output: 'export'`, `basePath: '/lab/__LAB_SLUG__'`). If a persistent Node daemon is required on `server58`, use `/opt/alt/alt-nodejs16/root/usr/bin/node` to avoid CageFS Wasm memory limits.
- **Design System**: Read `.agents/skills/techy-design-language/SKILL.md`.
- **Audit Log**: Document every AI change in `docs/AI_ACTIVITY.md`.
