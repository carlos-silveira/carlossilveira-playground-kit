/**
 * Next.js Configuration for Spaceship Shared Hosting (`carlossilveira.dev/lab/*`)
 *
 * CRITICAL: Always run `npm run build` inside GitHub Actions (never on cPanel)
 * to avoid CloudLinux CageFS WebAssembly virtual memory limits.
 *
 * @type {import('next').NextConfig}
 */
const nextConfig = {
  output: 'export',
  basePath: '/lab/nextjs-demo',
  trailingSlash: true,
  images: {
    unoptimized: true,
  },
};

export default nextConfig;
