# AGENTS.md — Java 21 GraalVM Lab (`__LAB_SLUG__`)

- **Repository**: `carlos-silveira/lab-__LAB_SLUG__` (`git@github-personal:carlos-silveira/lab-__LAB_SLUG__.git`)
- **Git Identity**: `Carlos Silveira <carlos@personal.me>`
- **Target Path on `server58`**: `/home/bfjydpuzth/carlossilveira.dev/lab/__LAB_SLUG__/`
- **Compilation Rule**: Compile to a static Linux musl binary (`native-image --static --libc=musl Main index.cgi`) in GitHub Actions, or route heavy stateful JVM services to an external container via CNAME.
- **Design System**: Read `.agents/skills/apple-ui-design/SKILL.md`.
- **Audit Log**: Document every AI change in `docs/AI_ACTIVITY.md`.
