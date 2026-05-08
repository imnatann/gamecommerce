# 🎨 GameCommerce — Theme Design System

> Custom shadcn-inspired theme built on Tailwind CSS 4 for Laravel Blade + Alpine.js

---

## 1. Design Philosophy

### Principles
1. **Gaming-First** — Dark, immersive UI that feels like a gaming platform, not a generic marketplace
2. **Speed Perception** — Instant feedback, skeleton loaders, optimistic updates
3. **Mobile-First** — 70%+ traffic from mobile; touch targets 44px minimum
4. **Trust Signals** — Badges, verified seller icons, secure payment indicators everywhere
5. **Information Dense** — Game marketplaces have lots of data; show it cleanly without clutter

### Brand Personality
- **Bold** — Not shy, stands out
- **Trustworthy** — Security badges, reviews visible
- **Fun** — Game-related, playful micro-interactions
- **Fast** — Zero lag feel, instant UI responses

---

## 2. Color System

### Primary Palette

```css
:root {
  /* ─── Primary ─── */
  --gc-primary: #6C3FE8;           /* Electric Purple — Main CTA, links, active states */
  --gc-primary-hover: #8B5CF6;    /* Lighter purple — Hover on primary */
  --gc-primary-active: #5B21B6;   /* Deeper purple — Press/active */
  --gc-primary-50: #F5F3FF;
  --gc-primary-100: #EDE9FE;
  --gc-primary-200: #DDD6FE;
  --gc-primary-300: #C4B5FD;
  --gc-primary-400: #A78BFA;
  --gc-primary-500: #8B5CF6;
  --gc-primary-600: #7C3AED;
  --gc-primary-700: #6D28D9;
  --gc-primary-800: #5B21B6;
  --gc-primary-900: #4C1D95;
  --gc-primary-950: #2E1065;

  /* ─── Accent / Gaming ─── */
  --gc-accent: #00E8A2;           /* Neon Green — Sale badges, success, live indicators */
  --gc-accent-hover: #34D399;
  --gc-accent-50: #ECFDF5;
  --gc-accent-100: #D1FAE5;
  --gc-accent-200: #A7F3D0;
  --gc-accent-300: #6EE7B7;
  --gc-accent-400: #34D399;
  --gc-accent-500: #10B981;
  --gc-accent-600: #059669;
  --gc-accent-700: #047857;

  /* ─── Warning / Sale ─── */
  --gc-warning: #F59E0B;          /* Amber — Discounts, sale badges */
  --gc-warning-hover: #D97706;
  --gc-warning-50: #FFFBEB;
  --gc-warning-100: #FEF3C7;
  --gc-warning-500: #F59E0B;
  --gc-warning-600: #D97706;

  /* ─── Error ─── */
  --gc-error: #EF4444;            /* Red — Errors, out of stock, disputes */
  --gc-error-hover: #DC2626;
  --gc-error-50: #FEF2F2;
  --gc-error-500: #EF4444;
  --gc-error-600: #DC2626;

  /* ─── Info ─── */
  --gc-info: #3B82F6;             /* Blue — Info states, processing */
  --gc-info-50: #EFF6FF;
  --gc-info-500: #3B82F6;
  --gc-info-600: #2563EB;
}
```

### Dark Theme (Default for Gaming)

```css
:root[data-theme="dark"] {
  /* ─── Surfaces ─── */
  --gc-bg: #0A0A0F;              /* Deepest bg */
  --gc-bg-card: #13131A;          /* Card bg */
  --gc-bg-elevated: #1A1A25;      /* Elevated surfaces (dropdowns, modals) */
  --gc-bg-hover: #22222F;         /* Hover on cards/rows */
  --gc-bg-active: #2A2A3A;        /* Active/pressed */
  --gc-bg-subtle: #16161F;        /* Subtle bg strips */

  /* ─── Borders ─── */
  --gc-border: #2A2A3A;
  --gc-border-hover: #3A3A4A;

  /* ─── Text ─── */
  --gc-text: #F1F1F6;             /* Primary text */
  --gc-text-secondary: #A1A1B5;   /* Muted text */
  --gc-text-tertiary: #6B6B80;    /* Placeholder/hint */
  --gc-text-inverse: #0A0A0F;     /* Text on primary bg */

  /* ─── Special ─── */
  --gc-glow: rgba(108, 63, 232, 0.15);    /* Primary glow effect */
  --gc-glow-accent: rgba(0, 232, 162, 0.15); /* Accent glow */
}

:root[data-theme="light"] {
  /* ─── Surfaces ─── */
  --gc-bg: #F8F8FC;
  --gc-bg-card: #FFFFFF;
  --gc-bg-elevated: #FFFFFF;
  --gc-bg-hover: #F1F1F6;
  --gc-bg-active: #E8E8F0;
  --gc-bg-subtle: #F1F1F6;

  /* ─── Borders ─── */
  --gc-border: #E2E2EA;
  --gc-border-hover: #D0D0DA;

  /* ─── Text ─── */
  --gc-text: #1A1A2E;
  --gc-text-secondary: #6B6B80;
  --gc-text-tertiary: #A1A1B5;
  --gc-text-inverse: #FFFFFF;

  /* ─── Special ─── */
  --gc-glow: rgba(108, 63, 232, 0.08);
  --gc-glow-accent: rgba(0, 232, 162, 0.08);
}
```

---

## 3. Typography

```css
:root {
  /* Font families */
  --gc-font-sans: 'Inter', system-ui, -apple-system, sans-serif;
  --gc-font-display: 'Outfit', var(--gc-font-sans);   /* Headings — slightly rounded */
  --gc-font-mono: 'JetBrains Mono', 'Fira Code', monospace;  /* Prices, codes */

  /* Font sizes (fluid with clamp) */
  --gc-text-xs: clamp(0.6875rem, 0.65rem + 0.1vw, 0.75rem);     /* 11-12px */
  --gc-text-sm: clamp(0.75rem, 0.7rem + 0.15vw, 0.875rem);       /* 12-14px */
  --gc-text-base: clamp(0.875rem, 0.82rem + 0.2vw, 1rem);        /* 14-16px */
  --gc-text-lg: clamp(1rem, 0.94rem + 0.25vw, 1.125rem);         /* 16-18px */
  --gc-text-xl: clamp(1.125rem, 1rem + 0.3vw, 1.25rem);          /* 18-20px */
  --gc-text-2xl: clamp(1.25rem, 1.1rem + 0.5vw, 1.5rem);         /* 20-24px */
  --gc-text-3xl: clamp(1.5rem, 1.2rem + 0.8vw, 1.875rem);        /* 24-30px */
  --gc-text-4xl: clamp(1.875rem, 1.4rem + 1.2vw, 2.25rem);       /* 30-36px */
  --gc-text-5xl: clamp(2.25rem, 1.6rem + 1.8vw, 3rem);           /* 36-48px */

  /* Line heights */
  --gc-leading-tight: 1.2;
  --gc-leading-snug: 1.35;
  --gc-leading-normal: 1.5;
  --gc-leading-relaxed: 1.65;

  /* Font weights */
  --gc-font-normal: 400;
  --gc-font-medium: 500;
  --gc-font-semibold: 600;
  --gc-font-bold: 700;
  --gc-font-extrabold: 800;
}
```

### Typography Scale Usage

| Element | Size | Weight | Color | Usage |
|---------|------|--------|-------|-------|
| Hero Title | 5xl | Extrabold | --gc-text | "Top Up Game Termurah" |
| Page Title | 4xl | Bold | --gc-text | "Mobile Legends" |
| Section Title | 3xl | Bold | --gc-text | "Top Up Game" |
| Card Title | 2xl | Semibold | --gc-text | Product name |
| Card Subtitle | xl | Medium | --gc-text-secondary | Game name |
| Body | base | Normal | --gc-text | Descriptions |
| Price | lg/xl | Bold | --gc-accent | "Rp 50.000" |
| Price Strike | base | Normal | --gc-text-tertiary | "Rp 100.000" |
| Badge | xs | Semibold | White | "DISKON 50%" |
| Meta | sm | Normal | --gc-text-secondary | "481 terjual" |

---

## 4. Spacing & Layout

```css
:root {
  /* Spacing scale (4px base) */
  --gc-space-0: 0;
  --gc-space-1: 0.25rem;   /* 4px */
  --gc-space-2: 0.5rem;    /* 8px */
  --gc-space-3: 0.75rem;   /* 12px */
  --gc-space-4: 1rem;      /* 16px */
  --gc-space-5: 1.25rem;   /* 20px */
  --gc-space-6: 1.5rem;    /* 24px */
  --gc-space-8: 2rem;      /* 32px */
  --gc-space-10: 2.5rem;   /* 40px */
  --gc-space-12: 3rem;     /* 48px */
  --gc-space-16: 4rem;     /* 64px */
  --gc-space-20: 5rem;     /* 80px */
  --gc-space-24: 6rem;     /* 96px */

  /* Layout */
  --gc-container-sm: 640px;
  --gc-container-md: 768px;
  --gc-container-lg: 1024px;
  --gc-container-xl: 1280px;
  --gc-container-2xl: 1440px;

  /* Breakpoints (for reference, actual media queries) */
  --gc-bp-xs: 475px;
  --gc-bp-sm: 640px;
  --gc-bp-md: 768px;
  --gc-bp-lg: 1024px;
  --gc-bp-xl: 1280px;
  --gc-bp-2xl: 1536px;
}
```

### Grid System
```
Desktop (≥1024px):  12-column grid, 24px gutter, max-w-1440px
Tablet (768-1023):   8-column grid, 20px gutter
Mobile (<768px):     4-column grid, 16px gutter, full bleed cards
```

---

## 5. Component Library (shadcn Pattern)

### 5.1 Buttons

```css
/* Base button */
.gc-btn {
  @apply inline-flex items-center justify-center gap-2
         font-medium transition-all duration-200 ease-out
         rounded-xl cursor-pointer select-none
         focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2
         disabled:opacity-50 disabled:cursor-not-allowed
         active:scale-[0.98];
}

/* Variants */
.gc-btn-primary {
  @apply gc-btn bg-[var(--gc-primary)] text-white
         hover:bg-[var(--gc-primary-hover)] active:bg-[var(--gc-primary-active)]
         focus-visible:ring-[var(--gc-primary)]
         shadow-[0_0_20px_var(--gc-glow)];
}

.gc-btn-accent {
  @apply gc-btn bg-[var(--gc-accent)] text-[var(--gc-primary-900)]
         hover:bg-[var(--gc-accent-hover)]
         font-semibold
         shadow-[0_0_20px_var(--gc-glow-accent)];
}

.gc-btn-outline {
  @apply gc-btn border-2 border-[var(--gc-border)] text-[var(--gc-text)]
         hover:border-[var(--gc-primary)] hover:text-[var(--gc-primary)]
         hover:bg-[var(--gc-primary-50)];
}

.gc-btn-ghost {
  @apply gc-btn text-[var(--gc-text-secondary)]
         hover:bg-[var(--gc-bg-hover)] hover:text-[var(--gc-text)];
}

.gc-btn-destructive {
  @apply gc-btn bg-[var(--gc-error)] text-white
         hover:bg-[var(--gc-error-hover)];
}

/* Sizes */
.gc-btn-xs   { padding: 6px 12px; font-size: var(--gc-text-xs); }
.gc-btn-sm   { padding: 8px 16px; font-size: var(--gc-text-sm); }
.gc-btn-md   { padding: 10px 20px; font-size: var(--gc-text-base); }
.gc-btn-lg   { padding: 14px 28px; font-size: var(--gc-text-lg); }
.gc-btn-xl   { padding: 18px 36px; font-size: var(--gc-text-xl); }

/* Full width */
.gc-btn-full { @apply w-full; }

/* Icon button */
.gc-btn-icon { @apply gc-btn p-2 rounded-lg aspect-square; }
```

### 5.2 Cards

```css
.gc-card {
  @apply bg-[var(--gc-bg-card)] rounded-2xl border border-[var(--gc-border)]
         transition-all duration-200 overflow-hidden;
}

.gc-card-hover:hover {
  @apply border-[var(--gc-border-hover)]
         shadow-lg shadow-[var(--gc-glow)]
         translate-y-[-2px];
}

.gc-card-product {
  @apply gc-card gc-card-hover group cursor-pointer;
}

.gc-card-product .gc-product-image {
  @apply aspect-[3/4] bg-[var(--gc-bg-subtle)] overflow-hidden;
}

.gc-card-product .gc-product-image img {
  @apply w-full h-full object-cover transition-transform duration-300;
}

.gc-card-product:hover .gc-product-image img {
  @apply scale-105;
}

.gc-card-game {
  @apply gc-card gc-card-hover text-center p-3;
}

.gc-card-game:hover {
  @apply shadow-md shadow-[var(--gc-glow)];
}
```

### 5.3 Badges & Labels

```css
.gc-badge {
  @apply inline-flex items-center gap-1 px-2 py-0.5
         rounded-md text-[var(--gc-text-xs)] font-semibold
         uppercase tracking-wide;
}

.gc-badge-discount {
  @apply gc-badge bg-[var(--gc-warning)] text-[var(--gc-primary-900)];
}

.gc-badge-new {
  @apply gc-badge bg-[var(--gc-accent)] text-[var(--gc-primary-900)];
}

.gc-badge-hot {
  @apply gc-badge bg-[var(--gc-error)] text-white;
}

.gc-badge-verified {
  @apply gc-badge bg-[var(--gc-primary)] text-white;
}

.gc-badge-stock-low {
  @apply gc-badge bg-[var(--gc-warning-50)] text-[var(--gc-warning-600)]
         border border-[var(--gc-warning)];
}

.gc-badge-out-of-stock {
  @apply gc-badge bg-[var(--gc-error-50)] text-[var(--gc-error)];
}

.gc-badge-processing {
  @apply gc-badge bg-[var(--gc-info-50)] text-[var(--gc-info)];
}

.gc-badge-delivered {
  @apply gc-badge bg-[var(--gc-accent-50)] text-[var(--gc-accent-600)];
}
```

### 5.4 Price Display

```css
.gc-price {
  @apply font-bold text-[var(--gc-accent)] font-[var(--gc-font-display)];
}

.gc-price-strike {
  @apply line-through text-[var(--gc-text-tertiary)]
         text-[var(--gc-text-sm)] font-normal;
}

.gc-price-compact {
  @apply gc-price text-[var(--gc-text-lg)];
}

.gc-price-large {
  @apply gc-price text-[var(--gc-text-2xl)];
}

.gc-price-display {
  @apply gc-price text-[var(--gc-text-3xl)];
}
```

### 5.5 Game Icon Grid

```css
.gc-game-grid {
  @apply grid grid-cols-5 gap-3
         sm:grid-cols-5 md:grid-cols-8 lg:grid-cols-10;
}

.gc-game-icon {
  @apply flex flex-col items-center gap-2 p-3
         rounded-xl transition-all duration-200 cursor-pointer
         hover:bg-[var(--gc-bg-hover)];
}

.gc-game-icon-img {
  @apply w-12 h-12 md:w-14 md:h-14 rounded-xl
         bg-[var(--gc-bg-subtle)] object-cover;
}

.gc-game-icon-name {
  @apply text-[var(--gc-text-xs)] md:text-[var(--gc-text-sm)]
         text-[var(--gc-text-secondary)] text-center
         truncate w-full;
}

.gc-game-icon:hover .gc-game-icon-name {
  @apply text-[var(--gc-primary)];
}
```

### 5.6 Product Card (Primary Listing Component)

```css
.gc-product-card {
  @apply gc-card-product relative flex flex-col;
}

/* Discount badge — top-left */
.gc-product-card .gc-discount-badge {
  @apply absolute top-2 left-2 z-10;
}

/* Wishlist button — top-right */
.gc-product-card .gc-wishlist-btn {
  @apply absolute top-2 right-2 z-10
         w-8 h-8 flex items-center justify-center
         rounded-full bg-[var(--gc-bg-card)]/80 backdrop-blur-sm
         text-[var(--gc-text-tertiary)]
         hover:text-[var(--gc-error)] hover:bg-[var(--gc-bg-card)]
         transition-all;
}

/* Image section */
.gc-product-card .gc-product-img-section {
  @apply relative aspect-[3/4] bg-[var(--gc-bg-subtle)] overflow-hidden;
}

/* Content section */
.gc-product-card .gc-product-content {
  @apply p-3 flex flex-col gap-1.5 flex-1;
}

.gc-product-card .gc-product-game-name {
  @apply text-[var(--gc-text-xs)] text-[var(--gc-text-tertiary)] truncate;
}

.gc-product-card .gc-product-name {
  @apply text-[var(--gc-text-sm)] font-medium text-[var(--gc-text)]
         line-clamp-2 leading-snug;
}

.gc-product-card .gc-product-seller {
  @apply flex items-center gap-1.5;
}

.gc-product-card .gc-product-seller-name {
  @apply text-[var(--gc-text-xs)] text-[var(--gc-text-secondary)] truncate;
}

.gc-product-card .gc-product-footer {
  @apply mt-auto pt-2 flex items-end justify-between;
}

.gc-product-card .gc-product-sold {
  @apply text-[var(--gc-text-xs)] text-[var(--gc-text-tertiary)];
}

.gc-product-card .gc-product-rating {
  @apply flex items-center gap-1 text-[var(--gc-text-xs)];
}
```

### 5.7 Search Bar

```css
.gc-search {
  @apply relative w-full max-w-2xl;
}

.gc-search-input {
  @apply w-full h-12 pl-12 pr-12
         bg-[var(--gc-bg-card)] border border-[var(--gc-border)]
         rounded-2xl text-[var(--gc-text-base)] text-[var(--gc-text)]
         placeholder:text-[var(--gc-text-tertiary)]
         focus:outline-none focus:border-[var(--gc-primary)]
         focus:ring-2 focus:ring-[var(--gc-primary)] focus:ring-opacity-20
         transition-all;
}

.gc-search-icon {
  @apply absolute left-4 top-1/2 -translate-y-1/2
         text-[var(--gc-text-tertiary)] w-5 h-5;
}

.gc-search-dropdown {
  @apply absolute top-full left-0 right-0 mt-2
         bg-[var(--gc-bg-elevated)] border border-[var(--gc-border)]
         rounded-xl shadow-2xl shadow-black/20
         max-h-80 overflow-y-auto z-50;
}

.gc-search-result-item {
  @apply flex items-center gap-3 p-3 cursor-pointer
         hover:bg-[var(--gc-bg-hover)] transition-colors;
}
```

### 5.8 Navbar

```css
.gc-navbar {
  @apply sticky top-0 z-50
         bg-[var(--gc-bg)]/95 backdrop-blur-md
         border-b border-[var(--gc-border)];
}

.gc-navbar-inner {
  @apply flex items-center justify-between
         max-w-[var(--gc-container-2xl)] mx-auto px-4 h-16;
}

.gc-nav-link {
  @apply text-[var(--gc-text-sm)] font-medium text-[var(--gc-text-secondary)]
         hover:text-[var(--gc-primary)] transition-colors
         px-3 py-2 rounded-lg
         hover:bg-[var(--gc-primary-50)];
}

.gc-nav-link-active {
  @apply text-[var(--gc-primary)] bg-[var(--gc-primary-50)];
}
```

### 5.9 Trust Section

```css
.gc-trust-grid {
  @apply grid grid-cols-1 md:grid-cols-3 gap-4 p-6;
}

.gc-trust-item {
  @apply flex items-center gap-4 p-4 rounded-xl
         bg-[var(--gc-bg-card)] border border-[var(--gc-border)];
}

.gc-trust-icon {
  @apply w-12 h-12 rounded-full flex items-center justify-center
         bg-[var(--gc-primary-50)] text-[var(--gc-primary)];
}

.gc-trust-title {
  @apply text-[var(--gc-text-sm)] font-semibold text-[var(--gc-text)];
}

.gc-trust-desc {
  @apply text-[var(--gc-text-xs)] text-[var(--gc-text-secondary)];
}
```

---

## 6. Special Effects & Animations

### Glow Effects (gaming aesthetic)
```css
.gc-glow {
  box-shadow: 0 0 20px var(--gc-glow),
              0 0 60px rgba(108, 63, 232, 0.05);
}

.gc-glow-accent {
  box-shadow: 0 0 20px var(--gc-glow-accent),
              0 0 60px rgba(0, 232, 162, 0.05);
}

.gc-glow-text {
  text-shadow: 0 0 20px var(--gc-glow);
}

/* Shimmer loading */
.gc-shimmer {
  @apply bg-gradient-to-r from-[var(--gc-bg-subtle)] via-[var(--gc-bg-hover)] to-[var(--gc-bg-subtle)];
  background-size: 200% 100%;
  animation: gc-shimmer 1.5s ease-in-out infinite;
}

@keyframes gc-shimmer {
  0%   { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

/* Pulse animation for live indicators */
.gc-pulse-live {
  @apply relative;
}

.gc-pulse-live::before {
  content: '';
  @apply absolute -top-1 -right-1 w-3 h-3
         bg-[var(--gc-accent)] rounded-full;
  animation: gc-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes gc-pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

/* Hover card lift */
.gc-hover-lift {
  @apply transition-all duration-300 ease-out;
}

.gc-hover-lift:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2),
              0 0 24px var(--gc-glow);
}

/* Skeleton component */
.gc-skeleton {
  @apply rounded-lg bg-[var(--gc-bg-subtle)] animate-pulse;
}

.gc-skeleton-text {
  @apply gc-skeleton h-4 w-3/4;
}

.gc-skeleton-image {
  @apply gc-skeleton aspect-[3/4];
}

.gc-skeleton-card {
  @apply gc-skeleton h-64 rounded-2xl;
}
```

---

## 7. Page Layouts

### Homepage Layout
```
┌──────────────────────────────────────────────────────┐
│                    NAVBAR (sticky)                      │
│  [Logo] [Search Bar............] [Cart] [Auth]        │
├──────────────────────────────────────────────────────┤
│                    HERO BANNER                         │
│  ┌──────────────────────────────────────────────────┐ │
│  │  Carousel/Banner (promos, new games)             │ │
│  └──────────────────────────────────────────────────┘ │
├──────────────────────────────────────────────────────┤
│                CATEGORY CHIPS (horizontal scroll)     │
│  [Top Up] [Game Key] [Akun] [Voucher] [Item] [Joki] │
├──────────────────────────────────────────────────────┤
│                QUICK NAV ICONS (horizontal scroll)    │
│  [🎮Top Up] [🔑Key] [👤Akun] [🎫Voucher] [💎Coin] │
├──────────────────────────────────────────────────────┤
│                ⚡ FLASH SALE SECTION                   │
│  [Timer]  [Product] [Product] [Product] [Product]    │
├──────────────────────────────────────────────────────┤
│                🔥 TOP UP GAME (grid 5col)             │
│  [ML] [FF] [GI] [PUBG] [Valorant] [HSR] [Roblox]   │
├──────────────────────────────────────────────────────┤
│                🎮 GAME KEY SECTION                    │
│  [Product Cards --- horizontal scrollable]             │
├──────────────────────────────────────────────────────┤
│                🏷️ VOUCHER SECTION                     │
│  [Game Icons --- horizontal scrollable]                │
├──────────────────────────────────────────────────────┤
│                🛡️ TRUST SECTION                        │
│  [Secure] [Guarantee] [24/7 Support]                  │
├──────────────────────────────────────────────────────┤
│                💳 PAYMENT METHODS                      │
│  [QRIS] [BCA] [GoPay] [OVO] [DANA] [ShopeePay]...   │
├──────────────────────────────────────────────────────┤
│                    FOOTER                              │
│  [About] [Help] [Blog] [Payment] [Socials]           │
└──────────────────────────────────────────────────────┘
```

### Game/Product Listing Page
```
┌──────────────────────────────────────────────────────┐
│  [Game Icon] Game Name → Product Type                │
├──────────────────────────────────────────────────────┤
│  [Left Sidebar]          │  [Product Grid]            │
│  Product Type:           │  ┌────┐ ┌────┐ ┌────┐    │
│  ○ Top Up                │  │Card│ │Card│ │Card│    │
│  ● Game Key              │  └────┘ └────┘ └────┘    │
│  ○ Akun                  │  ┌────┐ ┌────┐ ┌────┐    │
│  ○ Item                  │  │Card│ │Card│ │Card│    │
│  ○ Voucher               │  └────┘ └────┘ └────┘    │
│  ─────────────           │                            │
│  Server:                 │  [Load More / Pagination]   │
│  [All] [SEA] [Global]    │                            │
│  ─────────────           │                            │
│  Sort:                   │                            │
│  [Cheapest▼] [Popular]  │                            │
│  [Newest] [Rating]       │                            │
└──────────────────────────────────────────────────────┘
```

### Product Detail Page
```
┌──────────────────────────────────────────────────────┐
│  [Breadcrumb: Home > Game > Product Type > Name]     │
├──────────────────────────────────────────────────────┤
│  ┌──────────────┐  Product Name                       │
│  │             │  Game Name • Server • Region          │
│  │   Product   │  ⭐ 4.94 (481 sold)                   │
│  │   Image     │  ──────────────                      │
│  │             │  [Price: Rp 773.400] ←─ accent       │
│  │             │  [Strike: Rp 910.000] ←─ muted      │
│  └──────────────┘  [Discount: 15% OFF]               │
│                      ──────────────                    │
│  Server:     [id_server▼]                             │
│  Quantity:   [- 1 +]                                  │
│  ────────────────────────                             │
│  [🛒 BELI SEKARANG]     [♡ Wishlist]                  │
│  ────────────────────────                             │
│  🔒 Transaksi Aman  │  💰 Garansi Uang Kembali        │
│  ⚡ Instan Deliver  │  🛡️ Proteksi Pembeli            │
├──────────────────────────────────────────────────────┤
│  Seller: [Avatar] ShopName ⭐ 4.9 | 500+ sold        │
│  [Visit Shop] [Chat Seller]                            │
├──────────────────────────────────────────────────────┤
│  📝 Description                                       │
│  ──────────────                                       │
│  Lorem ipsum game product description...               │
├──────────────────────────────────────────────────────┤
│  ⭐ Reviews (481)                                      │
│  [★★★★☆] 4.94 average                                │
│  [Review 1] [Review 2] [Review 3]                    │
├──────────────────────────────────────────────────────┤
│  Related Products                                     │
│  [Card] [Card] [Card] [Card] [Card]                  │
└──────────────────────────────────────────────────────┘
```

---

## 8. Icon System

Using **Lucide Icons** (consistent with shadcn/ui):

```css
/* Icon sizes */
.gc-icon-xs  { width: 14px; height: 14px; }
.gc-icon-sm  { width: 16px; height: 16px; }
.gc-icon-md  { width: 20px; height: 20px; }
.gc-icon-lg  { width: 24px; height: 24px; }
.gc-icon-xl  { width: 32px; height: 32px; }
.gc-icon-2xl { width: 48px; height: 48px; }
```

### Icon Mapping
| Concept | Lucide Icon | Usage |
|---------|-------------|-------|
| Search | `search` | Search bar |
| Cart | `shopping-cart` | Cart icon |
| Heart | `heart` / `heart-solid` | Wishlist |
| Star | `star` | Ratings |
| Shield | `shield-check` | Trust |
| Zap | `zap` | Flash sale, instant |
| Tag | `tag` | Discount |
| Gamepad | `gamepad-2` | Gaming category |
| Key | `key` | Game key |
| User | `user` | Account |
| Clock | `clock` | Delivery time |
| Globe | `globe` | Region |
| Server | `server` | Server |
| Wallet | `wallet` | Balance |
| Credit Card | `credit-card` | Payment |
| Message | `message-circle` | Chat |
| Chevrons | `chevron-right/left` | Navigation |
| Filter | `sliders-horizontal` | Filter panel |
| Sort | `arrow-up-down` | Sort |
| Verified | `badge-check` | Verified seller |
| Package | `package` | Orders |
| Trending | `trending-up` | Popular/hot |
| Lock | `lock` | Secure |

---

## 9. Responsive Breakpoints & Strategies

```css
/* Mobile-first approach */
/* xs: default (<640px) — 1 column product grid, stacked layouts */
/* sm: ≥640px — 2 column product grid */
/* md: ≥768px — sidebar visible, 3 column grid */
/* lg: ≥1024px — full layout, 4 column grid */
/* xl: ≥1280px — max container, 5 column grid */
/* 2xl: ≥1536px — spacious, secondary nav */

/* Product grid responsive */
.gc-product-grid {
  @apply grid grid-cols-2 gap-3
         sm:grid-cols-2 sm:gap-4
         md:grid-cols-3 md:gap-4
         lg:grid-cols-4 lg:gap-5
         xl:grid-cols-5 xl:gap-5;
}

/* Game icon grid responsive */
.gc-game-grid {
  @apply grid grid-cols-5 gap-2
         sm:grid-cols-6
         md:grid-cols-8
         lg:grid-cols-10
         xl:grid-cols-12;
}

/* Hide on mobile, show desktop */
.gc-hide-mobile { @apply hidden md:block; }
.gc-hide-desktop { @apply block md:hidden; }

/* Bottom navigation mobile */
.gc-bottom-nav {
  @apply fixed bottom-0 left-0 right-0 z-50
         bg-[var(--gc-bg-card)] border-t border-[var(--gc-border)]
         md:hidden;
}

.gc-bottom-nav-item {
  @apply flex flex-col items-center gap-0.5 py-2 px-3
         text-[var(--gc-text-xs)] text-[var(--gc-text-secondary)]
         hover:text-[var(--gc-primary)];
}

.gc-bottom-nav-item-active {
  @apply text-[var(--gc-primary)];
}
```

---

## 10. Dark/Light Mode Toggle

```html
<!-- Theme toggle component -->
<button x-data="{ dark: localStorage.getItem('theme') === 'dark' || !localStorage.getItem('theme') }"
        @click="dark = !dark; localStorage.setItem('theme', dark ? 'dark' : 'light'); document.documentElement.setAttribute('data-theme', dark ? 'dark' : 'light')"
        class="gc-btn-icon gc-btn-ghost">
    <svg x-show="dark" class="gc-icon-md text-yellow-400">
        <!-- Sun icon for switching to light -->
        <use href="#icon-sun"/>
    </svg>
    <svg x-show="!dark" class="gc-icon-md text-[var(--gc-text-secondary)]">
        <!-- Moon icon for switching to dark -->
        <use href="#icon-moon"/>
    </svg>
</button>
```

Default: **Dark mode** (gaming platforms feel more immersive in dark)

---

## 11. Tailwind CSS v4 Configuration

```css
/* resources/css/app.css */
@import "tailwindcss";

@theme {
  /* Brand colors */
  --color-gc-primary: #6C3FE8;
  --color-gc-primary-hover: #8B5CF6;
  --color-gc-primary-active: #5B21B6;
  --color-gc-accent: #00E8A2;
  --color-gc-accent-hover: #34D399;
  --color-gc-warning: #F59E0B;
  --color-gc-error: #EF4444;
  --color-gc-info: #3B82F6;

  /* Surfaces — dark */
  --color-gc-bg: #0A0A0F;
  --color-gc-card: #13131A;
  --color-gc-elevated: #1A1A25;
  --color-gc-hover: #22222F;
  --color-gc-border: #2A2A3A;

  /* Text */
  --color-gc-text: #F1F1F6;
  --color-gc-text-secondary: #A1A1B5;
  --color-gc-text-tertiary: #6B6B80;

  /* Font families */
  --font-family-display: 'Outfit', system-ui, sans-serif;
  --font-family-mono: 'JetBrains Mono', monospace;

  /* Border radius */
  --radius-gc-sm: 0.5rem;
  --radius-gc-md: 0.75rem;
  --radius-gc-lg: 1rem;
  --radius-gc-xl: 1.25rem;
  --radius-gc-2xl: 1.5rem;
  --radius-gc-full: 9999px;

  /* Shadows */
  --shadow-gc-glow: 0 0 20px rgba(108, 63, 232, 0.15);
  --shadow-gc-glow-accent: 0 0 20px rgba(0, 232, 162, 0.15);
  --shadow-gc-card: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.06);
  --shadow-gc-card-hover: 0 10px 25px rgba(0, 0, 0, 0.2), 0 0 24px rgba(108, 63,232, 0.1);

  /* Animations */
  --animate-gc-shimmer: gc-shimmer 1.5s ease-in-out infinite;
  --animate-gc-pulse: gc-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
  --animate-gc-fade-in: gc-fade-in 0.3s ease-out;
  --animate-gc-slide-up: gc-slide-up 0.3s ease-out;
  --animate-gc-scale-in: gc-scale-in 0.2s ease-out;
}

@keyframes gc-shimmer {
  0%   { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

@keyframes gc-pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

@keyframes gc-fade-in {
  from { opacity: 0; }
  to   { opacity: 1; }
}

@keyframes gc-slide-up {
  from { opacity: 0; transform: translateY(8px); }
  to   { opacity: 1; transform: translateY(0); }
}

@keyframes gc-scale-in {
  from { opacity: 0; transform: scale(0.95); }
  to   { opacity: 1; transform: scale(1); }
}
```

---

## 12. Alpine.js Components

```javascript
// resources/js/app.js

// Search autocomplete
Alpine.data('searchAutocomplete', () => ({
    query: '',
    results: [],
    isOpen: false,
    async search() {
        if (this.query.length < 2) { this.isOpen = false; return; }
        const res = await fetch(`/api/search?q=${encodeURIComponent(this.query)}&limit=8`);
        this.results = await res.json();
        this.isOpen = this.results.length > 0;
    },
    select(item) { window.location.href = item.url; },
    close() { setTimeout(() => this.isOpen = false, 200); }
}));

// Flash sale countdown
Alpine.data('countdownTimer', (endTime) => ({
    endTime: new Date(endTime).getTime(),
    days: 0, hours: 0, minutes: 0, seconds: 0,
    tick() {
        const diff = this.endTime - Date.now();
        if (diff <= 0) return;
        this.days = Math.floor(diff / 86400000);
        this.hours = Math.floor((diff % 86400000) / 3600000);
        this.minutes = Math.floor((diff % 3600000) / 60000);
        this.seconds = Math.floor((diff % 60000) / 1000);
    },
    init() { setInterval(() => this.tick(), 1000); this.tick(); }
}));

// Cart management
Alpine.data('cartManager', () => ({
    items: [],
    count: 0,
    total: 0,
    async add(productId, quantity = 1) {
        const res = await fetch('/api/cart/add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ product_id: productId, quantity })
        });
        const data = await res.json();
        this.count = data.cart_count;
        this.total = data.cart_total;
        this.showToast('Added to cart!');
    },
    showToast(message) { /* toast notification */ }
}));
```

---

## 13. Accessibility (a11y) Guidelines

- All interactive elements must have visible focus rings (`focus-visible:ring-2`)
- Color contrast ratio ≥ 4.5:1 for normal text, ≥ 3:1 for large text
- Alt text on all product images
- `aria-label` on icon-only buttons
- Skip-to-content link
- Keyboard-navigable dropdowns & modals
- `role="status"` for live regions (cart count, search results)
- `role="alert"` for flash messages
- Reduced motion support: `@media (prefers-reduced-motion: reduce)`
- Proper heading hierarchy (h1 → h6)
- Semantic HTML elements (`<nav>`, `<main>`, `<article>`, `<aside>`)

---

## 14. Performance Budgets

| Metric | Target | Technique |
|--------|--------|-----------|
| LCP | < 2.5s | Server-rendered Blade, CDN images |
| FID | < 100ms | Alpine.js lazy, defer non-critical |
| CLS | < 0.1 | Image aspect ratios, skeleton loaders |
| TTI | < 3.5s | Code splitting, lazy hydration |
| Bundle CSS | < 50KB gzipped | Tailwind purge |
| Bundle JS | < 30KB gzipped | Alpine.js (15KB) + app code |
| Image format | WebP/AVIF | CDN transformation |

---

## 15. Checklist Before Development

- [ ] Setup Laravel 12 project with auth scaffolding
- [ ] Install Tailwind CSS 4 + configure custom theme
- [ ] Setup Alpine.js + Vite
- [ ] Create base layout (dark theme default)
- [ ] Build component library (buttons, cards, badges, etc.)
- [ ] Create homepage blade templates
- [ ] Implement search with Meilisearch
- [ ] Build product listing / detail pages
- [ ] Checkout flow with payment gateway
- [ ] Seller dashboard
- [ ] Admin panel
- [ ] Responsive testing on mobile
- [ ] Lighthouse audit (Performance > 90, A11y > 90)