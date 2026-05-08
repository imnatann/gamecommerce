# Constraints Intel

Sources: FRAMEWORK.md (SPEC, precedence=1), theme.md (SPEC, precedence=1)
Both SPECs share precedence=1; no internal ordering between them. No ADRs present to override.

---

## CONSTRAINT-001: PHP and Laravel Version Floor
source: /Users/tokaf/gamecommerce/FRAMEWORK.md
type: nfr
title: Minimum runtime versions
content: PHP ^8.3 required. Laravel ^12.0 required. All dependencies must be compatible with this floor.
Key packages:
  - laravel/fortify ^1.20
  - laravel/socialite ^5.14
  - laravel/scout ^10.8
  - laravel/horizon ^5.20
  - laravel/reverb ^1.0
  - laravel/telescope ^5.0
  - spatie/laravel-permission ^6.0
  - spatie/laravel-activitylog ^4.7
  - spatie/laravel-medialibrary ^11.0
  - midtrans/midtrans-php ^2.6
  - intervention/image ^3.0

---

## CONSTRAINT-002: Frontend Stack Lock
source: /Users/tokaf/gamecommerce/FRAMEWORK.md
type: protocol
title: No SPA framework — Blade + Alpine.js only
content: Frontend must use Laravel Blade for server-side rendering. Alpine.js ^3.14 for reactivity (including @alpinejs/persist and @alpinejs/focus plugins). Vite ^6.0 with laravel-vite-plugin ^1.2 for asset bundling. No React, Vue, or other SPA frameworks.

---

## CONSTRAINT-003: Tailwind CSS Version
source: /Users/tokaf/gamecommerce/theme.md §11
type: schema
title: Tailwind CSS v4 configuration format required
content: CSS must use Tailwind CSS v4 @theme directive format (not v3 tailwind.config.js values). @import "tailwindcss" at top of app.css. Custom tokens registered under --color-gc-*, --font-family-*, --radius-gc-*, --shadow-gc-*, --animate-gc-* namespaces. Purge relies on v4 content scanning.

---

## CONSTRAINT-004: Design Token Namespace
source: /Users/tokaf/gamecommerce/theme.md §2
type: schema
title: gc-* CSS custom property namespace
content: All design tokens must use the --gc-* prefix. Color system: --gc-primary (#6C3FE8), --gc-accent (#00E8A2), --gc-warning (#F59E0B), --gc-error (#EF4444), --gc-info (#3B82F6). Dark theme is default (data-theme="dark" on <html>). Theme switching via Alpine.js + localStorage.

---

## CONSTRAINT-005: OrderStatus State Machine
source: /Users/tokaf/gamecommerce/FRAMEWORK.md §Architecture Patterns
type: protocol
title: OrderStatus transitions are enforced by enum canTransitionTo()
content: Valid transitions:
  PENDING → PAID | CANCELLED
  PAID → PROCESSING | REFUNDED
  PROCESSING → DELIVERED | DISPUTED
  DELIVERED → COMPLETED | DISPUTED
  DISPUTED → COMPLETED | REFUNDED
  All other transitions are forbidden. No direct status writes bypassing this method.

---

## CONSTRAINT-006: Repository Pattern Contract
source: /Users/tokaf/gamecommerce/FRAMEWORK.md §Architecture Patterns
type: api-contract
title: Data access must go through Repository interfaces
content: ProductRepositoryInterface must expose: search(string $query, array $filters): LengthAwarePaginator, findByGame(int $gameId, string $productType): Collection, findCheapest(int $gameProductId): ?Product, getPopular(int $limit): Collection. Direct Eloquent queries in controllers are a violation of this constraint.

---

## CONSTRAINT-007: Action Pattern for Transactional Operations
source: /Users/tokaf/gamecommerce/FRAMEWORK.md §Architecture Patterns
type: protocol
title: Complex multi-step operations must use Action classes with DB::transaction
content: CreateOrderAction exemplifies the pattern: resolveCart → createOrder → applyVoucher → deductStock → event(OrderCreated) — all within a single DB::transaction. Order creation and similar multi-model operations must follow this pattern.

---

## CONSTRAINT-008: Queue Architecture
source: /Users/tokaf/gamecommerce/FRAMEWORK.md §Queue & Job Architecture
type: protocol
title: Three named queues with defined priorities
content:
  - payments queue: high priority, $tries=3 (ProcessPaymentJob)
  - notifications queue: normal priority (SendOrderNotificationJob)
  - indexing queue: low priority (UpdateProductSearchIndexJob)
  Scheduled jobs: UpdatePopularProducts (every hour), CancelExpiredOrders (every 5 min), ProcessAutoDelivery (every minute).

---

## CONSTRAINT-009: Caching TTLs
source: /Users/tokaf/gamecommerce/FRAMEWORK.md §Caching Strategy
type: nfr
title: Defined cache key TTLs must be respected
content:
  games.popular → 3600s
  games.list → 1800s
  products.game.{gameSlug} → 900s
  products.cheapest.{gameProductId} → 600s
  banners.active → 1800s
  seller.products.{sellerId} → 300s
  Cache invalidation: Game::saved() clears games.*, Product::saved() clears products.* and games.popular.

---

## CONSTRAINT-010: Security Baseline
source: /Users/tokaf/gamecommerce/FRAMEWORK.md §Security Checklist
type: nfr
title: Minimum security controls required before production
content: HTTPS forced, CSRF on all forms, XSS via Blade auto-escaping + CSP headers, SQL injection prevention via Eloquent ORM only (no raw queries with user input), rate limiting on API and login endpoints, FormRequest validation on all mutations, file upload validation (mime + size + virus scan), session cookies (secure + httponly + same-site), password bcrypt min 8 chars, 2FA via Fortify, KYC for sellers, Cloudflare Turnstile/R2 for bot protection.

---

## CONSTRAINT-011: API Versioning
source: /Users/tokaf/gamecommerce/FRAMEWORK.md §Project Structure
type: api-contract
title: API controllers namespaced under Api/V1/
content: All API controllers live under app/Http/Controllers/Api/V1/. ApiBaseController.php is the base class. Routes are split across routes/api.php, routes/seller.php, routes/admin.php. This enables future V2 without breaking V1 clients.

---

## CONSTRAINT-012: Touch Target Minimum
source: /Users/tokaf/gamecommerce/theme.md §1
type: nfr
title: Minimum touch target 44px
content: All interactive elements (buttons, links, nav items) must have a minimum touch target of 44px. Mobile-first — 70%+ expected traffic from mobile. gc-bottom-nav-item provides mobile navigation. Bottom navigation is hidden on md+ breakpoints.

---

## CONSTRAINT-013: Performance Budgets
source: /Users/tokaf/gamecommerce/theme.md §14
type: nfr
title: Lighthouse and Core Web Vitals budgets
content:
  LCP < 2.5s
  FID < 100ms
  CLS < 0.1
  TTI < 3.5s
  CSS bundle < 50KB gzipped (Tailwind purge)
  JS bundle < 30KB gzipped (Alpine.js ~15KB + app code)
  Images: WebP/AVIF via CDN transformation

---

## CONSTRAINT-014: Accessibility Floor
source: /Users/tokaf/gamecommerce/theme.md §13
type: nfr
title: WCAG 2.1 Level AA accessibility requirements
content: Color contrast ≥ 4.5:1 (normal text), ≥ 3:1 (large text). focus-visible:ring-2 on all interactive elements. Alt text on all product images. aria-label on icon-only buttons. Skip-to-content link. Keyboard-navigable dropdowns and modals. role="status" for live regions. role="alert" for flash messages. prefers-reduced-motion media query supported. Semantic HTML (nav, main, article, aside). Lighthouse a11y > 90.

---

## CONSTRAINT-015: Database Migration Ordering
source: /Users/tokaf/gamecommerce/FRAMEWORK.md §Project Structure
type: schema
title: 22-migration sequence defines schema dependency order
content: Migrations numbered 0001–0022. Dependency order: users → games → categories → game_products → products → orders → order_items → payments → wallets → wallet_transactions → reviews → wishlists → carts → cart_items → vouchers → voucher_usages → banners → disputes → dispute_messages → chat_messages → notifications → scout_indexes. New migrations must respect these foreign key dependencies.
