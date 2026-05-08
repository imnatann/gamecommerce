# Context Intel

source: /Users/tokaf/gamecommerce/README.md
type: DOC
confidence: high

---

## Topic: Project Identity
source: /Users/tokaf/gamecommerce/README.md

GameCommerce is a Laravel marketplace prototype for digital game commerce. Product categories in scope: top-up products, game keys, vouchers, accounts, items, and seller-managed services (joki/boosting). Inspired by itemku.com and g2g.com. Indonesian market primary. Tagline: "One Stop Gaming Marketplace — Top Up, Trade, Play".

---

## Topic: Current Implementation State (as of snapshot)
source: /Users/tokaf/gamecommerce/README.md

The codebase is an early prototype. The currently wired public routes are a subset of the full PLANNING.md sitemap:

  GET /         → home (named: home)
  GET /search   → search (named: search)
  GET /g/{slug} → game landing page (named: game.show)
  GET /d/{slug}/{id} → product detail (named: product.show)

Authenticated buyer routes currently wired in routes/web.php: cart, cart.add, checkout, checkout.process, order.status, profile.orders, profile.favorites.

Auth Blade views exist but named web routes for login, register, and logout are NOT currently registered in routes/web.php. This is a known gap relative to PLANNING.md target.

---

## Topic: Local Development Setup
source: /Users/tokaf/gamecommerce/README.md

Steps:
  composer install
  npm install
  cp .env.example .env
  php artisan key:generate
  php artisan migrate --seed
  npm run dev
  php artisan serve

Default local database: SQLite at database/database.sqlite. (Production target is MySQL 8 per PLANNING.md §6.)

Test suite: php artisan test
Smoke checks: php artisan route:list --except-vendor, php artisan test --filter=ExampleTest

---

## Topic: Implementation Gap — Routes vs Target Sitemap
source: /Users/tokaf/gamecommerce/README.md

README.md explicitly notes: "PLANNING.md describes a larger target sitemap. The currently wired public routes are the smaller MVP route set listed above." This is an acknowledged delta, not a conflict. The full route surface defined in PLANNING.md §3 is the target; current state is partial implementation.

Public catalog controllers are intentionally sparse: they avoid optional media/catalog relations when not required, so public pages render against a sparse seed database.

---

## Topic: Business Model Summary
source: /Users/tokaf/gamecommerce/PLANNING.md

Revenue streams:
  - Commission per transaction: 3–8% from seller on each sale
  - Featured listing: seller pays for product highlight/premium placement
  - Banner ads: CPM/CPC on homepage and category pages
  - Subscription seller: monthly/yearly seller plan with reduced fees
  - Boosting service fee: 5–10% additional fee on joki/boosting orders

User roles: Buyer, Seller (requires KYC), Admin, Super Admin (full access + analytics + config).

---

## Topic: Project Timeline Summary
source: /Users/tokaf/gamecommerce/PLANNING.md

Phase 1 Foundation (Week 1–8): Auth, DB schema, models, migrations → Product catalog, game pages, search → Cart, checkout, payment → Seller dashboard, order management, homepage.
Phase 2 Polish (Week 9–16): Reviews, wishlist, notifications → Admin panel, dispute system → Vouchers, flash sale, SEO pages → Testing, performance tuning.
Phase 3 Scale (Week 17–24): Chat, escrow, real-time → PWA, mobile API → Monitoring, analytics, optimization.

---

## Topic: Localization Context
source: /Users/tokaf/gamecommerce/PLANNING.md §8

Primary language: Indonesian (ID). Secondary: English (EN). All UI copy in source files uses Indonesian for user-facing strings (e.g., "Beli Sekarang", "Transaksi Aman", "Garansi Uang Kembali"). SetLocale middleware handles locale switching.

---

## Topic: Payment Methods (Indonesian Market)
source: /Users/tokaf/gamecommerce/PLANNING.md §2.4

Supported payment methods targeting Indonesian consumers:
  - QRIS (universal QR standard)
  - Bank transfer (BCA and others implied)
  - E-wallets: GoPay, OVO, DANA, ShopeePay
  - Credit card
  - Convenience store: Alfamart, Indomaret
