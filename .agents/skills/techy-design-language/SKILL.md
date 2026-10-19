---
name: techy-design-language
description: TechyNews × Human Who Codes Design Language System for carlossilveira.dev and carlossilveira.dev/lab/ — exact color tokens, typography hierarchy, surface layers, component primitives, dark-mode-toggle patterns, and visual identity. Activate this skill whenever building or modifying any UI component, page, or layout in the carlossilveira.dev ecosystem.
---

# TechyNews × Human Who Codes Design Language (`carlossilveira.dev`)

> **MANDATORY**: Read this before writing ANY HTML, JSX, CSS, or Tailwind class in `carlossilveira.dev` or `carlossilveira.dev/lab/*`.

## Identity

`carlossilveira.dev` fuses **Human Who Codes' calm editorial clarity** with the **TechyNews editorial-terminal aesthetic**:

**"Bloomberg Terminal meets The New York Times editorial room."**

- **Editorial Brutalism**: Heavy `font-black` (900), tight `tracking-tighter` headlines paired with `font-light` (300/400) body copy.
- **Terminal Micro-Labels**: `JetBrains Mono`, `text-[10px] font-black uppercase tracking-[0.2em]` or `tracking-[0.3em]`, pulsing status indicators, runtime badges, and UTC/ISO timestamps.
- **Ink on Dark/Light Paper**: Near-black `#02040a` dark canvas and warm `#f8f6f6` light canvas using native CSS `light-dark()`.
- **Zero-FOUC Color Scheme Architecture**: Native `<meta name="color-scheme" content="light dark">` mapped to `:root` via `:has()` selector overrides.

---

## Color System

### Core Tokens

| Token | Hex / Value | Usage |
| :--- | :--- | :--- |
| `primary` | `#00b4ff` | Primary electric cyan accent, links, interactive states, terminal badges |
| `secondary` | `#2b7cee` | Royal blue secondary glow, selection highlights, CTA shadows (`rgba(43, 124, 238, 0.4)`) |
| `canvas-dark` | `#02040a` | Primary dark mode page canvas |
| `surface-dark` | `#0a0f1c` | Elevated dark mode panels, code blocks, modals |
| `canvas-light` | `#f8f6f6` | Warm editorial paper background in light mode |
| `surface-light` | `#ffffff` | Crisp card surface in light mode |

### Native CSS `light-dark()` + `:has()` Setup

Always declare `<meta name="color-scheme" content="light dark">` in `<head>` followed immediately by the inline FOUC-prevention script, and map it in CSS:

```html
<meta name="color-scheme" content="light dark">
<script>
try {
  document.querySelector('meta[name="color-scheme"]').content = localStorage.getItem("color-scheme") ?? "light dark";
} catch (_) {}
</script>
```

```css
:root {
  color-scheme: light dark;
  --bg-canvas: light-dark(#f8f6f6, #02040a);
  --bg-elevated: light-dark(#ffffff, #0a0f1c);
  --bg-card: light-dark(rgba(255, 255, 255, 0.78), rgba(255, 255, 255, 0.025));
  --bg-card-hover: light-dark(rgba(0, 0, 0, 0.03), rgba(255, 255, 255, 0.05));
  --border-subtle: light-dark(rgba(0, 0, 0, 0.08), rgba(255, 255, 255, 0.08));
  --border-strong: light-dark(rgba(0, 0, 0, 0.15), rgba(255, 255, 255, 0.14));
  --text-main: light-dark(#0a0f1c, #f8fafc);
  --text-muted: light-dark(#475569, #94a3b8);
  --text-dim: light-dark(#64748b, #64748b);
  --accent-primary: #00b4ff;
  --accent-secondary: #2b7cee;

  &:has(> head > meta[name="color-scheme"][content="dark"]) {
    color-scheme: dark;
  }
  &:has(> head > meta[name="color-scheme"][content="light"]) {
    color-scheme: light;
  }
}
```

### Semantic Status Colors

| Purpose | Color | Pattern |
| :--- | :--- | :--- |
| Live / Operational | `emerald-500` (`#10b981`) | `bg-emerald-500/10 text-emerald-500 border-emerald-500/20` |
| Warning / Hybrid | `amber-500` (`#f59e0b`) | `bg-amber-500/10 text-amber-500 border-amber-500/20` |
| Error / Critical | `rose-500` (`#f43f5e`) | `bg-rose-500/10 text-rose-500 border-rose-500/20` |
| Info / Runtime | `cyan-400` (`#00b4ff`) | `bg-[#00b4ff]/10 text-[#00b4ff] border-[#00b4ff]/20` |

---

## Typography

### Font Stack

- **UI & Headlines**: `'Inter', -apple-system, BlinkMacSystemFont, sans-serif`
- **Editorial Reading**: `'Charter', 'Georgia', serif` or `'Inter'` (720px–840px comfortable measure)
- **Monospace / Terminal Labels**: `'JetBrains Mono', 'Fira Code', monospace`

### Weight & Tracking Rules

- **Hero Headlines**: `font-weight: 900` (`font-black`), `letter-spacing: -0.04em` (`tracking-tighter`), `line-height: 0.95`
- **Section & Card Titles**: `font-weight: 900` (`font-black`), `letter-spacing: -0.03em`
- **Terminal Micro-Labels**: `font-family: 'JetBrains Mono'`, `font-size: 10px`, `font-weight: 800/900`, `text-transform: uppercase`, `letter-spacing: 0.2em` to `0.3em`
- **Body Copy**: `font-weight: 300` or `400` (`font-light`), `line-height: 1.65`
- **Anti-Pattern**: Never use `font-semibold` (600); stick to high-contrast weights (`300`, `700`, `900`).

---

## Component Primitives

### Cards
- Border radius: `1.5rem` (`24px`, `rounded-3xl`) for lab cards and panels; `1rem` (`16px`, `rounded-2xl`) for stat blocks and terminal boxes.
- Surface: `background: var(--bg-card); border: 1px solid var(--border-subtle);`
- Hover state: `border-color: rgba(0, 180, 255, 0.35); transform: translateY(-2px);`

### Buttons & CTAs
- **Primary Pill CTA**: `background: #00b4ff; color: #02040a; font-weight: 900; text-transform: uppercase; letter-spacing: 0.12em; font-size: 11px; border-radius: 9999px; box-shadow: 0 0 28px rgba(0, 180, 255, 0.35);`
- **Ghost Terminal Action**: `border: 1px solid var(--border-subtle); border-radius: 0.75rem; font-family: 'JetBrains Mono'; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.12em;`
