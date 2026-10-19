import java.time.Instant;
import java.util.Optional;

/**
 * Carlos Silveira Lab Starter — Java 21 GraalVM Native Image CGI App
 * Target URL: https://carlossilveira.dev/lab/__LAB_SLUG__/
 * Compiled ahead-of-time via GraalVM `native-image` in GitHub Actions into a standalone Linux binary.
 */
public final class Main {
    private static final String LAB_SLUG = "__LAB_SLUG__";

    private static String escapeHtml(String input) {
        return input
            .replace("&", "&amp;")
            .replace("<", "&lt;")
            .replace(">", "&gt;")
            .replace("\"", "&quot;");
    }

    public static void main(String[] args) {
        String method = Optional.ofNullable(System.getenv("REQUEST_METHOD")).orElse("GET");
        String uri = Optional.ofNullable(System.getenv("REQUEST_URI")).orElse("/lab/" + LAB_SLUG + "/");
        String query = Optional.ofNullable(System.getenv("QUERY_STRING")).orElse("");
        String accept = Optional.ofNullable(System.getenv("HTTP_ACCEPT")).orElse("");
        String timestamp = Instant.now().toString();

        boolean wantsJson = query.contains("format=json")
            || accept.contains("application/json")
            || uri.endsWith("/api");

        String json = String.format(
            "{\n" +
            "  \"ok\": true,\n" +
            "  \"lab\": \"%s\",\n" +
            "  \"stack\": \"java-graalvm\",\n" +
            "  \"runtime\": \"Java %s (GraalVM Native Image)\",\n" +
            "  \"method\": \"%s\",\n" +
            "  \"uri\": \"%s\",\n" +
            "  \"timestamp\": \"%s\"\n" +
            "}",
            LAB_SLUG,
            System.getProperty("java.version", "21"),
            method,
            uri.replace("\"", "\\\""),
            timestamp
        );

        if (wantsJson) {
            System.out.print("Status: 200 OK\r\n");
            System.out.print("Content-Type: application/json; charset=utf-8\r\n");
            System.out.print("Access-Control-Allow-Origin: *\r\n\r\n");
            System.out.println(json);
            return;
        }

        System.out.print("Status: 200 OK\r\n");
        System.out.print("Content-Type: text/html; charset=utf-8\r\n\r\n");
        System.out.println("<!doctype html>");
        System.out.println("<html lang=\"en\">");
        System.out.println("<head>");
        System.out.println("  <meta charset=\"utf-8\">");
        System.out.println("  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">");
        System.out.println("  <meta name=\"color-scheme\" content=\"light dark\">");
        System.out.println("  <title>" + escapeHtml(LAB_SLUG) + " — Java 21 GraalVM Lab | carlossilveira.dev</title>");
        System.out.println("  <style>");
        System.out.println("    :root { color-scheme: light dark; --bg: light-dark(#f8f6f6, #02040a); --card: light-dark(#ffffff, #0a0f1c); --border: light-dark(rgba(0,0,0,0.08), rgba(255,255,255,0.08)); --text: light-dark(#0a0f1c, #f8fafc); --accent: #00b4ff; }");
        System.out.println("    body { margin: 0; font-family: Inter, -apple-system, sans-serif; background: var(--bg); color: var(--text); padding: 3rem 1.5rem; }");
        System.out.println("    .wrap { max-width: 760px; margin: 0 auto; }");
        System.out.println("    .badge { font-family: 'JetBrains Mono', monospace; font-size: 10px; font-weight: 800; letter-spacing: 0.2em; text-transform: uppercase; color: var(--accent); }");
        System.out.println("    h1 { font-size: 2.5rem; font-weight: 900; letter-spacing: -0.04em; margin: 0.5rem 0 1rem; }");
        System.out.println("    pre { background: var(--card); border: 1px solid var(--border); border-radius: 1rem; padding: 1.25rem; overflow-x: auto; font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; }");
        System.out.println("    a { color: var(--accent); text-decoration: none; font-weight: 700; }");
        System.out.println("  </style>");
        System.out.println("</head>");
        System.out.println("<body>");
        System.out.println("  <main class=\"wrap\">");
        System.out.println("    <div class=\"badge\">JAVA 21 GRAALVM NATIVE IMAGE // " + escapeHtml(LAB_SLUG) + "</div>");
        System.out.println("    <h1>" + escapeHtml(LAB_SLUG) + "</h1>");
        System.out.println("    <p>Ahead-of-time compiled Java 21 native binary. Append <a href=\"?format=json\"><code>?format=json</code></a> for JSON output.</p>");
        System.out.println("    <pre>" + escapeHtml(json) + "</pre>");
        System.out.println("    <p><a href=\"/lab/\">&larr; Back to carlossilveira.dev/lab/</a></p>");
        System.out.println("  </main>");
        System.out.println("</body>");
        System.out.println("</html>");
    }
}
