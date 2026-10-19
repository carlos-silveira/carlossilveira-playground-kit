export const metadata = {
  title: 'Next.js Lab Starter | carlossilveira.dev',
  description: 'Next.js static export starter running under /lab/nextjs-demo on Spaceship.',
};

export default function RootLayout({ children }) {
  return (
    <html lang="en">
      <head>
        <meta name="color-scheme" content="light dark" />
      </head>
      <body
        style={{
          margin: 0,
          fontFamily: 'Inter, -apple-system, BlinkMacSystemFont, sans-serif',
          background: 'light-dark(#f8f6f6, #02040a)',
          color: 'light-dark(#0a0f1c, #f8fafc)',
          padding: '3rem 1.5rem',
        }}
      >
        {children}
      </body>
    </html>
  );
}
