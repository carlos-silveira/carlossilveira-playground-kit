# Go HTTP/CGI Hybrid Starter (`go-cgi`) — `carlossilveira.dev/lab/__LAB_SLUG__/`

Zero-dependency Go starter using `net/http/cgi` with automatic fallback to `http.ListenAndServe` for local development.

## Compilation & Spaceship Deployment

- Cross-compiled in GitHub Actions with `CGO_ENABLED=0 GOOS=linux GOARCH=amd64 go build -ldflags="-s -w" -o index.cgi .`
- Executed natively by LiteSpeed CGI on `server58.shared.spaceship.host` (`chmod 755 index.cgi`).

## Local Development

```bash
go run main.go
# Open http://localhost:8080
```
