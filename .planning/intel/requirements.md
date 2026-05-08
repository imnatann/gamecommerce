# Requirements Intel

source-doc: /Users/tokaf/gamecommerce/PLANNING.md
type: PRD
confidence: high

All requirements derive from PLANNING.md. Phase assignments follow the document's own phasing.

---

## Phase 1 — MVP (Week 1–8)

### REQ-auth-register
source: /Users/tokaf/gamecommerce/PLANNING.md §2.1
scope: authentication
description: Users can register via email, Google OAuth, or phone number.
acceptance: Registration form accepts email+password, Google social login (Socialite), and phone number. Email verification flow required post-registration.

### REQ-auth-login
source: /Users/tokaf/gamecommerce/PLANNING.md §2.1
scope: authentication
description: Users can log in via email/password, social login, or OTP.
acceptance: Login supports email/password, Google social login, and OTP via phone. Session established on success; error states for invalid credentials.

### REQ-auth-2fa
source: /Users/tokaf/gamecommerce/PLANNING.md §2.1
scope: authentication
description: Users can enable two-factor authentication (2FA).
acceptance: 2FA via Fortify. Route: /auth/2fa. Must be enforced on seller and admin roles (KYC-adjacent).

### REQ-auth-profile
source: /Users/tokaf/gamecommerce/PLANNING.md §2.1
scope: user management
description: Authenticated users can manage their profile.
acceptance: Update avatar, username, bio. API: PUT /api/user/profile. Web: /profile.

### REQ-auth-rbac
source: /Users/tokaf/gamecommerce/PLANNING.md §2.1
scope: authorization
description: Role-based access control with four roles: Buyer, Seller, Admin, Super Admin.
acceptance: Middleware gates: EnsureBuyer, EnsureSeller, EnsureAdmin, EnsureKycVerified. Roles enforced on all seller/admin routes.

### REQ-auth-kyc
source: /Users/tokaf/gamecommerce/PLANNING.md §2.1
scope: seller verification
description: Sellers must complete KYC verification before listing products.
acceptance: KYC status tracked on users table (kyc_status field). EnsureKycVerified middleware blocks unverified sellers from product CRUD.

### REQ-wallet
source: /Users/tokaf/gamecommerce/PLANNING.md §2.1
scope: wallet
description: Users have an internal wallet with balance, transaction history, and withdrawal capability.
acceptance: Wallet balance shown on profile. Transactions logged (type in/out, balance_before, balance_after). Top-up via POST /api/user/wallet/topup. Withdrawal via POST /api/seller/withdraw. Web: /profile/wallet.

### REQ-catalog-categories
source: /Users/tokaf/gamecommerce/PLANNING.md §2.2
scope: product catalog
description: Products are organized under game categories: top-up, game key, item, akun, voucher, joki, koin game.
acceptance: Categories table with slug, type, parent_id for hierarchy. Category navigation available on all catalog pages.

### REQ-catalog-listing
source: /Users/tokaf/gamecommerce/PLANNING.md §2.2
scope: product catalog
description: Game listing pages show products filterable by type, region, server, price, rating, and sortable by cheapest/popular/newest/best-rating.
acceptance: Route /g/{game_slug}/{product_type}. Filter params accepted via query string. Pagination on results. ProductRepositoryInterface.search() powers the listing.

### REQ-catalog-detail
source: /Users/tokaf/gamecommerce/PLANNING.md §2.2
scope: product catalog
description: Product detail page shows description, server info, delivery method, variants, reviews, seller info, and trust badges.
acceptance: Route /d/{product_slug}/{product_id}. Page renders without media relations if sparse (as per README.md). Server, region, delivery_type visible.

### REQ-catalog-variants
source: /Users/tokaf/gamecommerce/PLANNING.md §2.2
scope: product catalog
description: Products support variants by region, quantity, and delivery method.
acceptance: GameProduct table stores type and required_info. Product detail allows variant selection before add-to-cart.

### REQ-catalog-reviews
source: /Users/tokaf/gamecommerce/PLANNING.md §2.2
scope: product catalog
description: Products have buyer reviews with star ratings and optional comments.
acceptance: Reviews table with rating, comment, images, is_anonymous. Avg rating displayed on product card and detail. POST /api/products/{id}/reviews. Review creation requires completed order.

### REQ-search-fulltext
source: /Users/tokaf/gamecommerce/PLANNING.md §2.3
scope: search
description: Full-text search across game names and product names.
acceptance: GET /api/search. Powered by Meilisearch via Scout. Returns ranked results. Indexes built via migration 0022_add_scout_indexes.php.

### REQ-search-autocomplete
source: /Users/tokaf/gamecommerce/PLANNING.md §2.3
scope: search
description: Search bar provides real-time autocomplete suggestions.
acceptance: Alpine.js searchAutocomplete component (min 2 chars to trigger). Fetches /api/search?q=...&limit=8. Dropdown shown in .gc-search-dropdown. Route: /search.

### REQ-search-filters
source: /Users/tokaf/gamecommerce/PLANNING.md §2.3
scope: search
description: Search results filterable by price, region, delivery method, and rating; sortable by cheapest/popular/newest/best-rating.
acceptance: Filter params accepted on GET /api/products and GET /search. Sort options: cheapest, popular, newest, best rating.

### REQ-cart
source: /Users/tokaf/gamecommerce/PLANNING.md §2.4
scope: transaction
description: Buyers can add products to cart and perform bulk purchase.
acceptance: POST /api/cart/add, GET /api/cart, DELETE /api/cart/{id}. Cart count updated live via Alpine.js cartManager. Web: /cart. CartItem stores product_id, quantity.

### REQ-checkout
source: /Users/tokaf/gamecommerce/PLANNING.md §2.4
scope: transaction
description: Buyers complete purchases through an instant checkout flow with payment method selection.
acceptance: Web: /checkout. POST /api/orders creates order, POST /api/payments initiates gateway. Order confirmation shown. Payment methods: QRIS, bank transfer, e-wallets, credit card, convenience store.

### REQ-payment-gateway
source: /Users/tokaf/gamecommerce/PLANNING.md §2.4
scope: payment
description: Payment processed via Midtrans and/or Xendit.
acceptance: PaymentService abstracts gateway selection. Midtrans primary (composer dependency present). Xendit secondary. PaymentReceived event triggers order status update.

### REQ-order-tracking
source: /Users/tokaf/gamecommerce/PLANNING.md §2.4
scope: transaction
description: Buyers can track order status through pending → paid → processing → delivered → completed states.
acceptance: OrderStatus enum enforces valid transitions (canTransitionTo). Route /order/{order_id}. API: GET /api/user/orders/{id}. Status displayed via .gc-badge-processing / .gc-badge-delivered components.

### REQ-auto-delivery
source: /Users/tokaf/gamecommerce/PLANNING.md §2.4
scope: delivery
description: Digital products are delivered automatically after payment confirmation.
acceptance: ProcessAutoDelivery job runs every minute. AutoDeliveryService handles API-based delivery. OrderCreated event triggers ProcessAutoDeliveryListener. delivery_type field on products distinguishes auto vs manual.

### REQ-manual-delivery
source: /Users/tokaf/gamecommerce/PLANNING.md §2.4
scope: delivery
description: Account trading and item products support manual delivery by sellers.
acceptance: PUT /api/seller/orders/{id}/deliver marks order delivered. ManualDeliveryService handles flow. Seller receives notification via NotifySellerListener.

### REQ-dispute
source: /Users/tokaf/gamecommerce/PLANNING.md §2.4
scope: dispute
description: Buyers can raise disputes/complaints on orders.
acceptance: Disputes table with reason, status, resolution. DisputeCreated event notifies admin and seller. API: PUT /api/admin/disputes/{id} for resolution. Order transitions to DISPUTED state.

### REQ-homepage
source: /Users/tokaf/gamecommerce/PLANNING.md §2.5
scope: homepage
description: Homepage renders hero carousel, category chips, popular games grid, product showcase sections, trust badges, and payment method showcase.
acceptance: Route / (home). All sections render server-side via Blade. Flash sale section includes countdown timer (Alpine.js countdownTimer component). SEO-friendly footer present.

### REQ-seller-dashboard
source: /Users/tokaf/gamecommerce/PLANNING.md §2.6
scope: seller
description: Verified sellers can manage product listings, view/process orders, track earnings, and configure auto-delivery.
acceptance: Routes under /seller/*. Product CRUD: GET/POST/PUT/DELETE /api/seller/products. Order management: GET /api/seller/orders, PUT /api/seller/orders/{id}/deliver. Earnings: GET /api/seller/earnings. Withdrawal: POST /api/seller/withdraw. KYC required (EnsureKycVerified middleware).

---

## Phase 2 — Growth (Week 9–16)

### REQ-recommendations
source: /Users/tokaf/gamecommerce/PLANNING.md §2.7
scope: personalization
description: Platform shows personalized product recommendations based on user history.
acceptance: Recently viewed products tracked. Wishlist/favorites managed. POST /api/user/favorites/{productId} toggles. Web: /profile/favorites.

### REQ-seo-pages
source: /Users/tokaf/gamecommerce/PLANNING.md §2.7
scope: SEO
description: SEO-optimized category landing pages and game-specific landing pages exist.
acceptance: Routes /c/{category_slug} and /v/{keyword}. Structured data, meta tags, sitemap present. Blade SSR ensures crawlability. PLANNING.md §8 NFR: SEO requirement.

### REQ-social-reviews
source: /Users/tokaf/gamecommerce/PLANNING.md §2.8
scope: community
description: Users can leave reviews with photo proof; sellers have trust levels; users can follow sellers.
acceptance: Reviews support image attachments (spatie/laravel-medialibrary). Seller ratings tracked. Follow relationship modeled. Referral system tracks invite-friend discounts.

### REQ-vouchers
source: /Users/tokaf/gamecommerce/PLANNING.md §2.8
scope: promotions
description: Discount voucher/coupon system with code, type, discount amount, min purchase, max uses, and expiry.
acceptance: Vouchers table with all fields. Applied at checkout via voucher_code. CreateOrderAction.applyVoucher() handles application. Admin CRUD: /api/admin/vouchers.

### REQ-flash-sale
source: /Users/tokaf/gamecommerce/PLANNING.md §2.8
scope: promotions
description: Time-limited flash sale sections on homepage with countdown timer.
acceptance: Flash sale products displayed on homepage. Alpine.js countdownTimer component counts down to sale end. .gc-badge-hot badge marks flash sale items.

### REQ-notifications
source: /Users/tokaf/gamecommerce/PLANNING.md §2.9
scope: notifications
description: Users receive order status and promotional notifications via push, email, in-app, and WhatsApp.
acceptance: EmailNotificationService, PushNotificationService, WhatsAppNotificationService in service layer. In-app notification center (notifications table). Real-time updates via Laravel Reverb WebSocket. Notification bell component in navbar.

### REQ-admin-panel
source: /Users/tokaf/gamecommerce/PLANNING.md §2.10
scope: admin
description: Admin panel provides analytics dashboard, game/category management, product moderation, dispute resolution, user management, banner management, and payment reconciliation.
acceptance: Routes under /admin/*. API endpoints under /api/admin/*. AdminDashboardController, AdminGameController, AdminUserController, AdminDisputeController, AdminBannerController. Protected by EnsureAdmin middleware.

---

## Phase 3 — Scale (Week 17–24)

### REQ-chat
source: /Users/tokaf/gamecommerce/PLANNING.md §2.11
scope: communication
description: Real-time buyer-seller chat system.
acceptance: ChatMessage model. Laravel Reverb WebSocket channel. Chat accessible from product detail page ("Chat Seller" button per theme.md §7 product detail layout).

### REQ-escrow
source: /Users/tokaf/gamecommerce/PLANNING.md §2.11
scope: high-value trading
description: Escrow/trading system for high-value account trades.
acceptance: Escrow system holds funds until delivery confirmed. Security checklist item in FRAMEWORK.md. OrderStatus state machine handles escrow states.

### REQ-pwa
source: /Users/tokaf/gamecommerce/PLANNING.md §2.11
scope: mobile
description: Progressive Web App support.
acceptance: PWA manifest, service worker for offline/caching. Mobile-first design already enforced by theme.md.

### REQ-mobile-api
source: /Users/tokaf/gamecommerce/PLANNING.md §2.11
scope: mobile
description: Mobile app API ready for React Native/Flutter consumption.
acceptance: Stateless API design (PLANNING.md §8 NFR). Versioned under /api/V1/*. Sanctum auth compatible with mobile tokens.

### REQ-performance-infra
source: /Users/tokaf/gamecommerce/PLANNING.md §2.12
scope: infrastructure
description: Redis caching, CDN optimization, Meilisearch, rate limiting, and monitoring.
acceptance: Redis cache with defined TTLs per FRAMEWORK.md caching strategy. Cloudflare CDN. Rate limiting on API throttle and login. Sentry + Laravel Telescope monitoring.

---

## Non-Functional Requirements

### REQ-nfr-performance
source: /Users/tokaf/gamecommerce/PLANNING.md §8
scope: performance
description: Page load < 2s, API response < 200ms.
acceptance: LCP < 2.5s, FID < 100ms, CLS < 0.1, TTI < 3.5s per theme.md §14. Server-rendered Blade, CDN images, skeleton loaders.

### REQ-nfr-security
source: /Users/tokaf/gamecommerce/PLANNING.md §8, /Users/tokaf/gamecommerce/FRAMEWORK.md
scope: security
description: OWASP Top 10 compliance, CSP headers, input validation, XSS/CSRF protection.
acceptance: All items in FRAMEWORK.md Security Checklist satisfied. HTTPS forced in production. FormRequest validation on all mutations.

### REQ-nfr-availability
source: /Users/tokaf/gamecommerce/PLANNING.md §8
scope: availability
description: 99.9% uptime target.
acceptance: Horizontal scaling ready, stateless API, Redis queues for async resilience.

### REQ-nfr-localization
source: /Users/tokaf/gamecommerce/PLANNING.md §8
scope: localization
description: Indonesian (ID) as primary language, English (EN) as secondary.
acceptance: SetLocale middleware in web stack. Laravel localization files for ID/EN.

### REQ-nfr-accessibility
source: /Users/tokaf/gamecommerce/PLANNING.md §8, /Users/tokaf/gamecommerce/theme.md §13
scope: accessibility
description: WCAG 2.1 Level AA compliance.
acceptance: focus-visible rings on all interactive elements, color contrast ≥ 4.5:1 (normal text), alt text on images, aria-label on icon-only buttons, keyboard-navigable dropdowns, prefers-reduced-motion support, Lighthouse a11y > 90.
