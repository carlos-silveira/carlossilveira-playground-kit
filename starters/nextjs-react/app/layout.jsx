export const metadata = {
  title: 'Next.js Lab Starter — Carlos Silveira',
  description: 'Next.js static export starter running under /lab/nextjs-demo.',
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
          fontFamily: "-apple-system, BlinkMacSystemFont, 'SF Pro Display', 'SF Pro Text', 'Segoe UI', sans-serif",
          fontSize: '17px',
          lineHeight: 1.5,
          background: 'light-dark(#f5f5f7, #000000)',
          color: 'light-dark(#1d1d1f, #f5f5f7)',
          padding: '48px 24px 96px',
          WebkitFontSmoothing: 'antialiased',
        }}
      >
        {children}
      </body>
    </html>
  );
}
