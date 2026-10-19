# GEMINI.md — AI Context for `carlossilveira-playground-kit`

See [`AGENTS.md`](./AGENTS.md) for the complete operational rules, Git identity requirements, Apple UI Design System guidelines, and CloudLinux runtime paths on `server58.shared.spaceship.host`.

## Quick Reference for Gemini / Antigravity Agents

1. **Skills**:
   - Read `.agents/skills/apple-ui-design/SKILL.md` before creating or modifying any HTML, CSS, or UI templates.
   - Read `.agents/skills/spaceship-multi-runtime/SKILL.md` before configuring `.htaccess`, CGI wrappers, Phusion Passenger files, or GitHub Actions workflows.
2. **Scaffolding New Experiments**:
   - Run `./scripts/new-lab.sh <stack> <slug>` to create `/Users/carlossilveira/Documents/Personal/carlossilveiradev/lab-<slug>` pre-wired with Git identity (`Carlos Silveira <carlos@personal.me>`), `github-personal` remote, and CI/CD deployment to `https://carlossilveira.dev/lab/<slug>/`.
3. **Audit Trail**:
   - Record every AI action in `docs/AI_ACTIVITY.md`.
