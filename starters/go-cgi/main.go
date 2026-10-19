package main

import (
	"encoding/json"
	"fmt"
	"html"
	"log"
	"net/http"
	"net/http/cgi"
	"os"
	"runtime"
	"strings"
	"time"
)

const labSlug = "__LAB_SLUG__"

type Payload struct {
	OK        bool   `json:"ok"`
	Lab       string `json:"lab"`
	Stack     string `json:"stack"`
	Runtime   string `json:"runtime"`
	Arch      string `json:"arch"`
	Mode      string `json:"mode"`
	Method    string `json:"method"`
	URI       string `json:"uri"`
	Timestamp string `json:"timestamp"`
}

func handler(w http.ResponseWriter, r *http.Request) {
	mode := "standalone-http"
	if os.Getenv("GATEWAY_INTERFACE") != "" {
		mode = "litespeed-cgi"
	}

	payload := Payload{
		OK:        true,
		Lab:       labSlug,
		Stack:     "go-cgi",
		Runtime:   runtime.Version(),
		Arch:      fmt.Sprintf("%s/%s", runtime.GOOS, runtime.GOARCH),
		Mode:      mode,
		Method:    r.Method,
		URI:       r.URL.RequestURI(),
		Timestamp: time.Now().UTC().Format(time.RFC3339),
	}

	wantsJSON := r.URL.Query().Get("format") == "json" ||
		strings.Contains(r.Header.Get("Accept"), "application/json") ||
		strings.HasSuffix(r.URL.Path, "/api")

	if wantsJSON {
		w.Header().Set("Content-Type", "application/json; charset=utf-8")
		w.Header().Set("Access-Control-Allow-Origin", "*")
		enc := json.NewEncoder(w)
		enc.SetIndent("", "  ")
		_ = enc.Encode(payload)
		return
	}

	rawJSON, _ := json.MarshalIndent(payload, "", "  ")
	w.Header().Set("Content-Type", "text/html; charset=utf-8")
	fmt.Fprintf(w, `<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="color-scheme" content="light dark">
  <script>
    try {
      document.querySelector('meta[name="color-scheme"]').content = localStorage.getItem("color-scheme") ?? "light dark";
    } catch (_) {}
  </script>
  <title>%s — Go Hybrid CGI/HTTP Lab | carlossilveira.dev</title>
  <style>
    :root {
      color-scheme: light dark;
      --bg: light-dark(#f8f6f6, #02040a);
      --card: light-dark(#ffffff, #0a0f1c);
      --border: light-dark(rgba(0,0,0,0.08), rgba(255,255,255,0.08));
      --text: light-dark(#0a0f1c, #f8fafc);
      --accent: #00b4ff;
    }
    :root:has(> head > meta[name="color-scheme"][content="dark"]) { color-scheme: dark; }
    :root:has(> head > meta[name="color-scheme"][content="light"]) { color-scheme: light; }
    body {
      margin: 0;
      font-family: Inter, -apple-system, BlinkMacSystemFont, sans-serif;
      background: var(--bg);
      color: var(--text);
      padding: 3rem 1.5rem;
    }
    .wrap { max-width: 760px; margin: 0 auto; }
    .badge {
      font-family: 'JetBrains Mono', monospace;
      font-size: 10px;
      font-weight: 800;
      letter-spacing: 0.2em;
      text-transform: uppercase;
      color: var(--accent);
    }
    h1 { font-size: 2.5rem; font-weight: 900; letter-spacing: -0.04em; margin: 0.5rem 0 1rem; }
    pre {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 1rem;
      padding: 1.25rem;
      overflow-x: auto;
      font-family: 'JetBrains Mono', monospace;
      font-size: 0.85rem;
    }
    a { color: var(--accent); text-decoration: none; font-weight: 700; }
  </style>
</head>
<body>
  <main class="wrap">
    <div class="badge">GO HYBRID CGI/HTTP STARTER // %s</div>
    <h1>%s</h1>
    <p>Compiled as a zero-dependency Linux <code>amd64</code> static binary. Append <a href="?format=json"><code>?format=json</code></a> for JSON output.</p>
    <pre>%s</pre>
    <p><a href="/lab/">&larr; Back to carlossilveira.dev/lab/</a></p>
  </main>
</body>
</html>`, html.EscapeString(labSlug), html.EscapeString(labSlug), html.EscapeString(labSlug), html.EscapeString(string(rawJSON)))
}

func main() {
	mux := http.NewServeMux()
	mux.HandleFunc("/", handler)

	// Automatically detect CGI environment on Spaceship LiteSpeed vs local standalone server
	if os.Getenv("GATEWAY_INTERFACE") != "" {
		if err := cgi.Serve(mux); err != nil {
			log.Fatal(err)
		}
		return
	}

	port := os.Getenv("PORT")
	if port == "" {
		port = "8080"
	}
	log.Printf("Starting Go standalone HTTP server on http://localhost:%s", port)
	log.Fatal(http.ListenAndServe(":"+port, mux))
}
