import java.time.Instant;
import java.util.Optional;

/**
 * Carlos Silveira Lab Starter — Java 21 GraalVM Native Image App
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
        System.out.println("  <title>" + escapeHtml(LAB_SLUG) + " — Java 21 GraalVM | Carlos Silveira Lab</title>");
        System.out.println("  <style>");
        System.out.println("    :root { color-scheme: light dark; --font-sans: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'SF Pro Text', 'Segoe UI', sans-serif; --font-mono: 'SF Mono', ui-monospace, Menlo, Monaco, Consolas, monospace; --text-hero: 600 48px/1.1 var(--font-sans); --text-body: 400 17px/1.5 var(--font-sans); --text-caption: 400 13px/1.4 var(--font-sans); --bg-primary: light-dark(#ffffff, #000000); --bg-secondary: light-dark(#f5f5f7, #1d1d1f); --text-primary: light-dark(#1d1d1f, #f5f5f7); --text-secondary: #86868b; --accent: #0071e3; --accent-link: light-dark(#0071e3, #2997ff); --card-bg: light-dark(rgba(255,255,255,0.8), rgba(29,29,31,0.8)); --card-border: light-dark(rgba(0,0,0,0.05), rgba(255,255,255,0.08)); }");
        System.out.println("    body { margin: 0; font: var(--text-body); background: var(--bg-secondary); color: var(--text-primary); padding: 48px 24px 96px; -webkit-font-smoothing: antialiased; }");
        System.out.println("    .wrap { max-width: 680px; margin: 0 auto; }");
        System.out.println("    .eyebrow { font: var(--text-caption); font-weight: 600; color: var(--accent-link); margin-bottom: 8px; }");
        System.out.println("    h1 { font: var(--text-hero); letter-spacing: -0.025em; margin: 0 0 16px; }");
        System.out.println("    p { color: var(--text-secondary); margin: 0 0 24px; }");
        System.out.println("    .card { background: var(--card-bg); backdrop-filter: blur(20px); border-radius: 18px; border: 1px solid var(--card-border); box-shadow: 0 4px 24px rgba(0,0,0,0.06); padding: 24px; margin-bottom: 24px; }");
        System.out.println("    pre { margin: 0; overflow-x: auto; font-family: var(--font-mono); font-size: 13px; line-height: 1.55; }");
        System.out.println("    .btn-primary { background: var(--accent); color: #ffffff; padding: 12px 24px; border-radius: 980px; font-size: 14px; font-weight: 500; min-height: 44px; text-decoration: none; display: inline-flex; align-items: center; margin-right: 16px; }");
        System.out.println("    a.link { color: var(--accent-link); text-decoration: none; font: var(--text-caption); font-weight: 500; }");
        System.out.println("  </style>");
        System.out.println("</head>");
        System.out.println("<body>");
        System.out.println("  <main class=\"wrap\">");
        System.out.println("    <div class=\"eyebrow\">Java 21 GraalVM Native Image</div>");
        System.out.println("    <h1>" + escapeHtml(LAB_SLUG) + "</h1>");
        System.out.println("    <p>Ahead-of-time compiled Java 21 native binary.</p>");
        System.out.println("    <div class=\"card\"><pre>" + escapeHtml(json) + "</pre></div>");
        System.out.println("    <div><a class=\"btn-primary\" href=\"?format=json\">View JSON Response</a><a class=\"link\" href=\"/lab/\">&larr; Back to Engineering Lab</a></div>");
        System.out.println("  </main>");
        System.out.println("</body>");
        System.out.println("</html>");
    }
}
