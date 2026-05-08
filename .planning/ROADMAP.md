# Roadmap: GameCommerce

## Overview

Milestone M1 (Phase 1–7) membangun Foundation MVP: dari menutup gap infrastruktur auth dan routes yang sudah ada, lalu membangun lengkap catalog, transaksi penuh (cart → checkout → payment → delivery), hingga seller/admin panel dan test coverage yang memenuhi success metric. Milestone M2 (Phase 8–11) menambahkan growth features: SEO, voucher, social reviews, notifikasi lengkap. Milestone M3 (Phase 12–14) membangun fitur skala: chat real-time, escrow, PWA, dan monitoring infra.

## Milestones

- 🚧 **M1 — Foundation MVP** - Phase 1–7 (target: Week 1–8)
- 📋 **M2 — Growth** - Phase 8–11 (target: Week 9–16)
- 📋 **M3 — Scale** - Phase 12–14 (target: Week 17–24)

## Phases

- [ ] **Phase 1: Auth Infrastructure Fix** - Tutup semua gap infrastruktur auth: named routes, KYC flow, policies, middleware
- [ ] **Phase 2: Profile, Wallet & Category Foundation** - Profile views, wallet UI, category admin, homepage lengkap
- [ ] **Phase 3: Catalog & Search** - Game listing, product detail, variants, reviews, full-text search
- [ ] **Phase 4: Cart, Checkout & Payment** - Cart API, checkout flow, Midtrans + Xendit integration penuh
- [ ] **Phase 5: Delivery, Dispute & Email Notifications** - Auto-delivery, manual delivery, dispute system, mailables
- [ ] **Phase 6: Seller & Admin Dashboards** - Seller product/order/earnings management, admin panel lengkap dengan category CRUD
- [ ] **Phase 7: Testing & Quality Gate** - ≥80% test coverage untuk Actions/Services kritis, performance audit
- [ ] **Phase 8: Personalization & SEO** - Recommendations, wishlist, SEO landing pages (/c/, /v/)
- [ ] **Phase 9: Vouchers & Flash Sale** - Voucher/coupon system, flash sale dengan countdown
- [ ] **Phase 10: Social Reviews & Full Notifications** - Photo reviews, seller trust level, WhatsApp notifikasi
- [ ] **Phase 11: Advanced Admin** - Analytics dashboard, payment reconciliation, seller subscription
- [ ] **Phase 12: Chat & Escrow** - Real-time buyer-seller chat, escrow untuk high-value trade
- [ ] **Phase 13: PWA & Mobile API** - PWA manifest, service worker, stateless mobile API
- [ ] **Phase 14: Performance Infra & Monitoring** - Redis TTLs, CDN, rate limiting, Sentry + Telescope

## Phase Details

### Phase 1: Auth Infrastructure Fix
**Goal**: Semua infrastruktur auth berfungsi — named routes terdaftar, KYC flow terhubung, policies ada, middleware gates bekerja
**Depends on**: Nothing (first phase)
**Requirements**: REQ-auth-routes, REQ-auth-register, REQ-auth-login, REQ-auth-2fa, REQ-auth-rbac, REQ-auth-kyc, REQ-nfr-security, REQ-nfr-localization
**Success Criteria** (what must be TRUE):
  1. User dapat membuka /login, /register, /logout tanpa 404 atau route-not-found error
  2. User dapat register dengan email/password dan Google OAuth; email verification flow berjalan
  3. Seller yang belum KYC di-redirect ke halaman verifikasi KYC saat mencoba akses /seller/products
  4. Middleware EnsureAdmin, EnsureSeller, EnsureKycVerified memblokir akses tidak sah dengan response HTTP 403
  5. ProductPolicy, OrderPolicy, SellerPolicy ada dan di-register di AuthServiceProvider
**Plans**: TBD
**UI hint**: yes

### Phase 2: Profile, Wallet & Category Foundation
**Goal**: User dapat melihat dan mengelola profil, wallet, dan kategori produk tersedia untuk catalog
**Depends on**: Phase 1
**Requirements**: REQ-auth-profile, REQ-wallet, REQ-catalog-categories, REQ-homepage
**Success Criteria** (what must be TRUE):
  1. User dapat membuka /profile dan melihat data profil, update avatar/username/bio
  2. User dapat membuka /profile/wallet dan melihat saldo, riwayat transaksi
  3. Admin dapat membuat, edit, dan hapus kategori di /admin/categories
  4. Homepage (/) merender semua section: hero carousel, category chips, popular games grid, trust badges, payment showcase
  5. Route seller.balance.index resolve dan menampilkan halaman earnings seller
**Plans**: TBD
**UI hint**: yes

### Phase 3: Catalog & Search
**Goal**: Buyer dapat browse game catalog, lihat detail produk, dan mencari produk dengan filter
**Depends on**: Phase 2
**Requirements**: REQ-catalog-listing, REQ-catalog-detail, REQ-catalog-variants, REQ-catalog-reviews, REQ-search-fulltext, REQ-search-autocomplete, REQ-search-filters
**Success Criteria** (what must be TRUE):
  1. User dapat browse listing produk di /g/{game_slug} dengan filter type/region/server/price/rating dan sorting
  2. User dapat membuka detail produk di /d/{slug}/{id} dan melihat variants, server info, delivery type, seller info
  3. User dapat memilih variant produk sebelum add-to-cart di halaman detail
  4. Search bar memberikan autocomplete suggestion real-time saat mengetik ≥2 karakter
  5. Route /c/{category_slug} merender halaman landing kategori yang filterable
**Plans**: TBD
**UI hint**: yes

### Phase 4: Cart, Checkout & Payment
**Goal**: Buyer dapat menambah produk ke cart, checkout, dan membayar via Midtrans atau Xendit tanpa error
**Depends on**: Phase 3
**Requirements**: REQ-cart, REQ-checkout, REQ-payment-gateway, REQ-order-tracking
**Success Criteria** (what must be TRUE):
  1. Buyer dapat menambah produk ke cart; cart count terupdate live di navbar
  2. GET /api/cart/count dan POST /api/voucher/apply berfungsi tanpa 404
  3. Buyer dapat menyelesaikan checkout via Midtrans (QRIS, bank transfer, e-wallet) tanpa error
  4. XenditService::callXenditApi berfungsi (bukan RuntimeException stub); payment via Xendit dapat diproses
  5. Setelah payment berhasil, order status berpindah dari PENDING → PAID sesuai state machine
  6. Buyer dapat melihat status order di /order/{order_id} dengan badge status yang akurat
**Plans**: TBD
**UI hint**: yes

### Phase 5: Delivery, Dispute & Email Notifications
**Goal**: Produk terdeliver otomatis setelah bayar, seller bisa deliver manual, buyer bisa dispute, dan email notifikasi terkirim
**Depends on**: Phase 4
**Requirements**: REQ-auto-delivery, REQ-manual-delivery, REQ-dispute, REQ-notifications-email
**Success Criteria** (what must be TRUE):
  1. Produk dengan delivery_type=auto terdeliver dalam ≤1 menit setelah payment confirmed
  2. Seller dapat menandai order sebagai delivered via PUT /api/seller/orders/{id}/deliver untuk manual delivery
  3. Buyer dapat mengajukan dispute pada order; order status berubah ke DISPUTED
  4. Email OrderConfirmationMail terkirim ke buyer setelah order dibuat
  5. Email NewOrderSellerMail terkirim ke seller saat ada order baru
  6. Email PaymentReceivedMail, DeliveryNotificationMail, RefundNotificationMail ada dan functional
**Plans**: TBD

### Phase 6: Seller & Admin Dashboards
**Goal**: Seller dapat mengelola produk/order/earnings penuh, admin dapat moderasi dan kelola seluruh platform
**Depends on**: Phase 5
**Requirements**: REQ-seller-dashboard, REQ-admin-panel
**Success Criteria** (what must be TRUE):
  1. Seller dapat membuat, edit, hapus produk di /seller/products (hanya setelah KYC approved)
  2. Seller dapat melihat dan memproses order di /seller/orders, mengatur auto-delivery
  3. Seller dapat melihat earnings dan mengajukan withdrawal di /seller/earnings (atau /seller/balance)
  4. Admin dapat melihat dashboard di /admin/dashboard dengan summary metrics
  5. Admin dapat mengelola games, categories, users, vouchers, banners, dan disputes dari panel admin
  6. Admin dapat resolve dispute dan order status transisi ke COMPLETED atau REFUNDED
**Plans**: TBD
**UI hint**: yes

### Phase 7: Testing & Quality Gate
**Goal**: Codebase memiliki ≥80% test coverage untuk Actions/Services kritis dan smoke test end-to-end pass
**Depends on**: Phase 6
**Requirements**: REQ-test-coverage, REQ-nfr-performance, REQ-nfr-accessibility
**Success Criteria** (what must be TRUE):
  1. php artisan test berjalan tanpa failure (bukan hanya ExampleTest placeholder)
  2. Unit tests untuk CreateOrderAction, ProcessPaymentAction, WalletService ada dengan coverage ≥80%
  3. Feature test untuk end-to-end flow (browse → checkout → pay → auto-deliver → konfirmasi) pass
  4. Lighthouse performance score ≥80 dan a11y score ≥90 pada halaman utama (/, /search, /d/{slug}/{id})
  5. Semua 13 known gaps dari ingest (auth routes, missing views, Xendit stub, dll) terverifikasi closed
**Plans**: TBD

### Phase 8: Personalization & SEO
**Goal**: Platform merekomendasikan produk relevan kepada user dan memiliki halaman SEO yang crawlable
**Depends on**: Phase 7
**Requirements**: REQ-recommendations, REQ-seo-pages
**Success Criteria** (what must be TRUE):
  1. User dapat melihat "Recently Viewed" dan produk yang direkomendasikan di homepage/catalog
  2. User dapat toggle wishlist/favorites via POST /api/user/favorites/{productId}
  3. Route /c/{category_slug} merender halaman landing kategori dengan structured data (schema.org)
  4. Route /v/{keyword} merender halaman landing keyword dengan meta tags SEO yang benar
**Plans**: TBD
**UI hint**: yes

### Phase 9: Vouchers & Flash Sale
**Goal**: Buyer dapat menggunakan voucher diskon dan melihat flash sale dengan countdown timer
**Depends on**: Phase 8
**Requirements**: REQ-vouchers, REQ-flash-sale
**Success Criteria** (what must be TRUE):
  1. Buyer dapat memasukkan kode voucher di checkout dan diskon diterapkan secara otomatis
  2. Voucher dengan expiry/max-uses dibatasi dengan benar; kode invalid menampilkan pesan error
  3. Homepage menampilkan section flash sale dengan countdown timer yang akurat
  4. Admin dapat membuat, edit, dan menonaktifkan voucher di /admin/vouchers
**Plans**: TBD
**UI hint**: yes

### Phase 10: Social Reviews & Full Notifications
**Goal**: User dapat memberikan review dengan foto, seller punya trust level, dan notifikasi WhatsApp berfungsi
**Depends on**: Phase 9
**Requirements**: REQ-social-reviews, REQ-notifications-full
**Success Criteria** (what must be TRUE):
  1. Buyer dapat submit review dengan foto attachment setelah order completed
  2. Seller profile menampilkan average rating dan trust level badge
  3. User dapat follow seller dan menerima notifikasi saat seller menambah produk baru
  4. Notifikasi in-app muncul di notification bell navbar secara real-time via Reverb
  5. WhatsApp notifikasi terkirim via Fonnte/Wablas untuk event order kritis (bukan placeholder)
**Plans**: TBD
**UI hint**: yes

### Phase 11: Advanced Admin
**Goal**: Admin memiliki analytics dashboard, laporan rekonsiliasi payment, dan manajemen paket seller
**Depends on**: Phase 10
**Requirements**: REQ-admin-advanced
**Success Criteria** (what must be TRUE):
  1. Admin dapat melihat chart revenue, GMV, dan user growth di dashboard analytics
  2. Admin dapat mengunduh laporan rekonsiliasi payment per periode
  3. Admin dapat mengelola paket subscription seller (basic/premium) dengan fee tier yang berbeda
**Plans**: TBD
**UI hint**: yes

### Phase 12: Chat & Escrow
**Goal**: Buyer dan seller dapat berkomunikasi real-time dan melakukan high-value trade via escrow
**Depends on**: Phase 11
**Requirements**: REQ-chat, REQ-escrow
**Success Criteria** (what must be TRUE):
  1. Buyer dapat membuka chat dengan seller dari halaman detail produk
  2. Pesan chat terkirim dan diterima real-time via Laravel Reverb WebSocket
  3. Order high-value account trade menggunakan escrow — dana tertahan sampai delivery confirmed
  4. Seller dapat konfirmasi delivery dan dana escrow dilepas ke wallet seller
**Plans**: TBD

### Phase 13: PWA & Mobile API
**Goal**: Platform dapat diinstall sebagai PWA dan API siap dikonsumsi mobile app
**Depends on**: Phase 12
**Requirements**: REQ-pwa, REQ-mobile-api
**Success Criteria** (what must be TRUE):
  1. User dapat menginstall GameCommerce sebagai PWA dari browser mobile Chrome/Safari
  2. Halaman catalog dapat diakses offline (cached via service worker)
  3. API /api/V1/* mengembalikan response stateless yang compatible dengan Sanctum mobile token
  4. Semua API endpoint terdokumentasi (minimal via route:list) dan tidak memerlukan session state
**Plans**: TBD

### Phase 14: Performance Infra & Monitoring
**Goal**: Platform siap produksi dengan caching, CDN, rate limiting, dan monitoring lengkap
**Depends on**: Phase 13
**Requirements**: REQ-performance-infra, REQ-nfr-availability
**Success Criteria** (what must be TRUE):
  1. Cache Redis aktif untuk semua key yang didefinisikan FRAMEWORK.md (games.popular, banners.active, dll) dengan TTL yang benar
  2. Rate limiting aktif di login endpoint dan API throttle; request berlebih mendapat 429
  3. Sentry menangkap exceptions dari production; Telescope aktif di staging untuk debugging
  4. Uptime monitoring menunjukkan 99.9% availability target terpantau
**Plans**: TBD

## Progress

**Execution Order:** Phase 1 → 2 → 3 → 4 → 5 → 6 → 7 (M1 complete) → 8 → 9 → 10 → 11 (M2 complete) → 12 → 13 → 14 (M3 complete)

| Phase | Plans Complete | Status | Completed |
|-------|----------------|--------|-----------|
| 1. Auth Infrastructure Fix | 0/TBD | Not started | - |
| 2. Profile, Wallet & Category Foundation | 0/TBD | Not started | - |
| 3. Catalog & Search | 0/TBD | Not started | - |
| 4. Cart, Checkout & Payment | 0/TBD | Not started | - |
| 5. Delivery, Dispute & Email Notifications | 0/TBD | Not started | - |
| 6. Seller & Admin Dashboards | 0/TBD | Not started | - |
| 7. Testing & Quality Gate | 0/TBD | Not started | - |
| 8. Personalization & SEO | 0/TBD | Not started | - |
| 9. Vouchers & Flash Sale | 0/TBD | Not started | - |
| 10. Social Reviews & Full Notifications | 0/TBD | Not started | - |
| 11. Advanced Admin | 0/TBD | Not started | - |
| 12. Chat & Escrow | 0/TBD | Not started | - |
| 13. PWA & Mobile API | 0/TBD | Not started | - |
| 14. Performance Infra & Monitoring | 0/TBD | Not started | - |
