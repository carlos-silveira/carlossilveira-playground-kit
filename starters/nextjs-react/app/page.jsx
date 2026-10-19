export default function LabHomePage() {
  return (
    <main style={{ maxWidth: '680px', margin: '0 auto' }}>
      <div
        style={{
          fontSize: '13px',
          lineHeight: 1.4,
          fontWeight: 600,
          color: '#0071e3',
          marginBottom: '8px',
        }}
      >
        Next.js &amp; React Starter
      </div>
      <h1
        style={{
          fontSize: '48px',
          lineHeight: 1.1,
          fontWeight: 600,
          letterSpacing: '-0.025em',
          margin: '0 0 16px',
        }}
      >
        __LAB_SLUG__
      </h1>
      <div
        style={{
          background: 'light-dark(rgba(255, 255, 255, 0.8), rgba(29, 29, 31, 0.8))',
          backdropFilter: 'blur(20px)',
          borderRadius: '18px',
          border: '1px solid light-dark(rgba(0, 0, 0, 0.05), rgba(255, 255, 255, 0.08))',
          boxShadow: '0 4px 24px rgba(0, 0, 0, 0.06)',
          padding: '24px',
          marginBottom: '24px',
        }}
      >
        <p style={{ margin: 0, color: '#86868b' }}>
          Pre-built in GitHub Actions with <code>output: &apos;export&apos;</code> and deployed to{' '}
          <code>/lab/__LAB_SLUG__/</code>.
        </p>
      </div>
      <p style={{ margin: 0 }}>
        <a
          href="/lab/"
          style={{
            display: 'inline-flex',
            alignItems: 'center',
            background: '#0071e3',
            color: '#ffffff',
            padding: '12px 24px',
            borderRadius: '980px',
            fontSize: '14px',
            fontWeight: 500,
            minHeight: '44px',
            textDecoration: 'none',
          }}
        >
          &larr; Back to Engineering Lab
        </a>
      </p>
    </main>
  );
}
