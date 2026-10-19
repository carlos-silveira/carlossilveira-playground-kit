---
name: liquid-glass-design
description: Apple iOS 26 / macOS Tahoe Liquid Glass Web Design System for carlossilveira.dev. Enforces physical optical refraction, specular rim lighting, dynamic ambient mesh backgrounds, wide horizontal layouts (1380px), and 100% cross-app visual uniformity.
---

# Liquid Glass Web Design System (`carlossilveira.dev`)

This skill defines the **single unified design system** shared across **Blog (`carlossilveira.dev/`)**, **Tools (`carlossilveira.dev/tools/`)**, and **Labs (`carlossilveira.dev/lab/`)**.

## 1. Why Flat Glass Fails vs. True Liquid Glass
Flat white/gray cards on a flat `#f5f5f7` canvas look dated because `backdrop-filter` has nothing behind it to refract.
True **Liquid Glass** requires **three simultaneous layers**:
1. **Living Ambient Mesh Backdrop (`.lg-ambient-canvas`)**: Fixed, soft, multi-color luminous orbs (`#0071e3` sapphire, `#6366f1` indigo, `#06b6d4` cyan-aqua, `#ec4899` warm rose) moving slowly behind the page so translucent cards refract real color gradients as the user scrolls.
2. **Refractive Glass Material (`backdrop-filter: blur(28px) saturate(200%) contrast(1.05)`)**: High saturation + blur paired with an inline SVG `<filter id="lg-refraction">` (`feTurbulence` + `feDisplacementMap`).
3. **Dual Specular Rim Lighting (Bevel Physics)**:
   - Top/left overhead light reflection: `inset 0 1.5px 1px 0 rgba(255, 255, 255, 0.88)` (light mode) / `inset 0 1.5px 1px 0 rgba(255, 255, 255, 0.28)` (dark mode).
   - Inner prism rim: `inset 0 -1px 1px 0 rgba(255, 255, 255, 0.35), inset 0 0 18px 0 rgba(255, 255, 255, 0.18)`.
   - Interactive cursor spotlight sheen (`radial-gradient(480px circle at var(--mouse-x, 50%) var(--mouse-y, 0%), rgba(255,255,255,0.28), transparent 60%)`).

---

## 2. Mandatory Unified Typography & Layout

- **Google Fonts (MUST be loaded identically in Blog, Tools, and Labs)**:
  ```html
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
  ```
- **Font Variables**:
  - `--font-display: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;` (All headings, brand wordmark, big numbers/KPIs, buttons)
  - `--font-sans: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;` (Body copy, inputs, tooltips, FAQs)
- **Wide Horizontal Layout (NEVER narrow centered `840px` for pages)**:
  - `--container-wide: min(1380px, 94vw);`
  - Every page uses a wide horizontal grid (`1380px` max-width) with generous fluid columns (`12-column` or `repeat(auto-fill, minmax(380px, 1fr))`).

---

## 3. Master CSS Tokens (`:root`)

```css
:root {
  color-scheme: light dark;
  --font-display: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
  --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  --container-wide: min(1380px, 94vw);

  /* Light Mode Liquid Glass */
  --canvas-bg: #f4f7fb;
  --text-primary: #0f172a;
  --text-secondary: #475569;
  --text-muted: #64748b;

  --lg-surface: rgba(255, 255, 255, 0.58);
  --lg-surface-elevated: rgba(255, 255, 255, 0.76);
  --lg-surface-interactive: rgba(255, 255, 255, 0.88);
  --lg-border: rgba(255, 255, 255, 0.72);
  --lg-border-outer: rgba(15, 23, 42, 0.07);
  --lg-specular: inset 0 1.5px 1px 0 rgba(255, 255, 255, 0.92),
                 inset 0 -1px 1px 0 rgba(255, 255, 255, 0.4),
                 inset 0 0 20px 0 rgba(255, 255, 255, 0.25);
  --lg-shadow: 0 20px 50px -12px rgba(15, 23, 42, 0.1),
               0 4px 16px -2px rgba(15, 23, 42, 0.05);
  --lg-blur: blur(28px) saturate(200%) contrast(1.04);

  --accent-primary: #0066ff;
  --accent-secondary: #6366f1;
  --accent-aqua: #06b6d4;
  --accent-gradient: linear-gradient(135deg, #0066ff 0%, #5b5bf6 50%, #06b6d4 100%);
  --success: #10b981;
  --warning: #f59e0b;
  --danger: #ef4444;

  --radius-xl: 28px;
  --radius-lg: 22px;
  --radius-md: 16px;
  --radius-pill: 999px;
  --spring-easing: cubic-bezier(0.22, 1, 0.36, 1);
}
```

In Dark Mode (`@media (prefers-color-scheme: dark)` and `:root:has(> head > meta[name="color-scheme"][content="dark"])`):
```css
  --canvas-bg: #070b14;
  --text-primary: #f8fafc;
  --text-secondary: #cbd5e1;
  --text-muted: #94a3b8;

  --lg-surface: rgba(18, 25, 43, 0.52);
  --lg-surface-elevated: rgba(26, 35, 58, 0.68);
  --lg-surface-interactive: rgba(36, 48, 78, 0.82);
  --lg-border: rgba(255, 255, 255, 0.16);
  --lg-border-outer: rgba(255, 255, 255, 0.12);
  --lg-specular: inset 0 1.5px 1px 0 rgba(255, 255, 255, 0.28),
                 inset 0 -1px 1px 0 rgba(0, 0, 0, 0.45),
                 inset 0 0 24px 0 rgba(99, 102, 241, 0.08);
  --lg-shadow: 0 24px 60px -12px rgba(0, 0, 0, 0.55),
               0 4px 16px -2px rgba(0, 0, 0, 0.35);
```

---

## 4. Brand Logo System (3 Official Variants)

Every app must support and include the 3 official **Carlos Silveira** brand variants:
1. **`logo-mark` (Solo el Logo / Isotipo)**: Squircle de cristal líquido (`42x42`) con gradiente prismático (`#0066ff` → `#6366f1` → `#06b6d4`), bisel especular superior y monograma continuo **`CS`** entrelazado en blanco óptico.
2. **`logo-wordmark` (Puro Texto / Logotipo)**: `Carlos Silveira` en `Plus Jakarta Sans` ExtraBold (`800`), `letter-spacing: -0.04em`, donde `Silveira` lleva un sutil acento o punto líquido `.dev`.
3. **`logo-combined` (Combinado / Imagotipo)**: El isotipo `logo-mark` + el `logo-wordmark` lado a lado.
