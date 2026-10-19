export default function LabHomePage() {
  return (
    <main style={{ maxWidth: '760px', margin: '0 auto' }}>
      <div
        style={{
          fontFamily: 'JetBrains Mono, monospace',
          fontSize: '10px',
          fontWeight: 800,
          letterSpacing: '0.2em',
          textTransform: 'uppercase',
          color: '#00b4ff',
        }}
      >
        NEXT.JS + REACT LAB STARTER // __LAB_SLUG__
      </div>
      <h1 style={{ fontSize: '2.5rem', fontWeight: 900, letterSpacing: '-0.04em', margin: '0.5rem 0 1rem' }}>
        __LAB_SLUG__
      </h1>
      <p style={{ lineHeight: 1.65 }}>
        Pre-built in GitHub Actions with <code>output: &apos;export&apos;</code> and deployed to{' '}
        <code>/lab/__LAB_SLUG__/</code> on Spaceship Web Hosting.
      </p>
      <p>
        <a href="/lab/" style={{ color: '#00b4ff', textDecoration: 'none', fontWeight: 700 }}>
          &larr; Back to carlossilveira.dev/lab/
        </a>
      </p>
    </main>
  );
}
