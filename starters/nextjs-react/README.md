# Next.js / React Starter (`nextjs-react`) — `carlossilveira.dev/lab/nextjs-demo/`

Next.js 15 / React 19 starter configured for subfolder deployment at `https://carlossilveira.dev/lab/<slug>/`.

## Spaceship CloudLinux Architecture

- **Build Step**: Always run `npm ci && npm run build` in **GitHub Actions** (`ubuntu-latest`) and sync `out/` to `/home/bfjydpuzth/carlossilveira.dev/lab/<slug>/`.
- **Why**: CloudLinux CageFS virtual memory caps cause Node 18+ WebAssembly compilers (SWC/Turbopack) to hit OOM if built directly on shared hosting.
- **Node Runtime on `server58`**:
  - CLI: `/opt/alt/alt-nodejs22/root/usr/bin/node`
  - Background SSR fallback (if needed): `/opt/alt/alt-nodejs16/root/usr/bin/node`
