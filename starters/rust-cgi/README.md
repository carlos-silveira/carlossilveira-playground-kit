# Rust CGI/HTTP Starter (`rust-cgi`) — `carlossilveira.dev/lab/__LAB_SLUG__/`

Sub-millisecond Rust CGI starter compiled to a standalone static Linux binary (`x86_64-unknown-linux-musl`) for Spaceship Web Hosting (`server58.shared.spaceship.host`).

## Compilation & Deployment

- Built in GitHub Actions with `cargo build --release --target x86_64-unknown-linux-musl`
- Stripped and size-optimized (`opt-level = "z"`, `lto = true`) into `index.cgi` (`chmod 755`).
