# Requirements: GameCommerce

Source: /Users/tokaf/gamecommerce/PLANNING.md (PRD) + FRAMEWORK.md (SPEC)
Extracted: 2026-05-08
Total v1 requirements: 28 (Phase 1 MVP scope)
Total v2 requirements: 7 (Phase 2 Growth scope)
Total v3 requirements: 5 (Phase 3 Scale scope)
Non-functional: 5

---

## Milestone M1 — Foundation MVP (Phase 1–7)

### REQ-auth-register
scope: authentication
description: Users can register via email, Google OAuth, or phone number.
acceptance: Registration form accepts email+password, Google social login (Socialite), and phone number. Email verification flow required post-registration.
v1: true

### REQ-auth-login
scope: authentication
description: Users can log in via email/password, social login, or OTP.
acceptance: Login supports email/password, Google social login, and OTP via phone. Session established on success; error states for invalid credentials.
v1: true

### REQ-auth-2fa
scope: authentication
description: Users can enable two-factor authentication (2FA).
acceptance: 2FA via Fortify. Route: /auth/2fa. Enforced on seller and admin roles.
v1: true

### REQ-auth-profile
scope: user management
description: Authenticated users can manage their profile.
acceptance: Update avatar, username, bio. API: PUT /api/user/profile. Web: /profile. Views pages.profile.{index,orders,wallet,favorites} must exist.
v1: true

### REQ-auth-rbac
scope: authorization
description: Role-based access control with four roles: Buyer, Seller, Admin, Super Admin.
acceptance: Middleware gates: EnsureBuyer, EnsureSeller, EnsureAdmin, EnsureKycVerified. Roles enforced on all seller/admin routes. Policies: ProductPolicy, OrderPolicy, SellerPolicy.
v1: true

### REQ-auth-kyc
scope: seller verification
description: Sellers must complete KYC verification before listing products.
acceptance: KYC status tracked on users table. EnsureKycVerified middleware blocks unverified sellers. Route seller.kyc.verify wired and functional.
v1: true

### REQ-auth-routes
scope: authentication infrastructure
description: Auth named routes properly registered and Blade views functional.
acceptance: Named routes login, register, logout, password.request, password.update, verification.send, two-factor.login all registered. Auth forms submit without 404/route-not-found errors.
v1: true

### REQ-wallet
scope: wallet
description: Users have an internal wallet with balance, transaction history, and withdrawal capability.
acceptance: Wallet balance shown on /profile/wallet. Transactions logged. Top-up via POST /api/user/wallet/topup. Withdrawal via POST /api/seller/withdraw. seller.balance.index route resolves correctly.
v1: true

### REQ-catalog-categories
scope: product catalog
description: Products organized under game categories with admin management.
acceptance: Categories table with slug, type, parent_id. Category navigation on all catalog pages. Admin CRUD at /admin/categories with AdminCategoryController.
v1: true

### REQ-catalog-listing
scope: product catalog
description: Game listing pages show products filterable by type, region, server, price, rating, and sortable.
acceptance: Route /g/{game_slug}/{product_type}. Filter via query string. Pagination. ProductRepositoryInterface.search() powers listing. Route /c/{category_slug} wired.
v1: true

### REQ-catalog-detail
scope: product catalog
description: Product detail page shows description, server info, delivery method, variants, reviews, seller info, and trust badges.
acceptance: Route /d/{product_slug}/{product_id}. Renders without media relations if sparse. Server, region, delivery_type visible.
v1: true

### REQ-catalog-variants
scope: product catalog
description: Products support variants by region, quantity, and delivery method.
acceptance: GameProduct table stores type and required_info. Product detail allows variant selection before add-to-cart.
v1: true

### REQ-catalog-reviews
scope: product catalog
description: Products have buyer reviews with star ratings and optional comments.
acceptance: Reviews table with rating, comment, images, is_anonymous. Avg rating displayed. POST /api/products/{id}/reviews. Review requires completed order.
v1: true

### REQ-search-fulltext
scope: search
description: Full-text search across game names and product names.
acceptance: GET /api/search. Powered by Meilisearch via Scout. Returns ranked results.
v1: true

### REQ-search-autocomplete
scope: search
description: Search bar provides real-time autocomplete suggestions.
acceptance: Alpine.js searchAutocomplete component (min 2 chars). Fetches /api/search?q=...&limit=8. Dropdown in .gc-search-dropdown.
v1: true

### REQ-search-filters
scope: search
description: Search results filterable by price, region, delivery method, and rating; sortable.
acceptance: Filter params on GET /api/products and GET /search. Sort: cheapest, popular, newest, best rating.
v1: true

### REQ-cart
scope: transaction
description: Buyers can add products to cart and perform bulk purchase.
acceptance: POST /api/cart/add, GET /api/cart, DELETE /api/cart/{id}, GET /api/cart/count. Cart count updated live via Alpine.js cartManager. Web: /cart.
v1: true

### REQ-checkout
scope: transaction
description: Buyers complete purchases through an instant checkout flow with payment method selection.
acceptance: Web: /checkout. POST /api/orders creates order. POST /api/payments initiates gateway. /api/voucher/apply wired. Payment methods: QRIS, bank transfer, e-wallets, credit card, convenience store.
v1: true

### REQ-payment-gateway
scope: payment
description: Payment processed via Midtrans and Xendit.
acceptance: PaymentService abstracts gateway selection. Midtrans fully functional. XenditService::callXenditApi implemented (no RuntimeException stub). PaymentReceived event triggers order status update.
v1: true

### REQ-order-tracking
scope: transaction
description: Buyers can track order status through the full state machine.
acceptance: OrderStatus enum enforces valid transitions (canTransitionTo). Route /order/{order_id}. API: GET /api/user/orders/{id}. Status badges displayed.
v1: true

### REQ-auto-delivery
scope: delivery
description: Digital products are delivered automatically after payment confirmation.
acceptance: ProcessAutoDelivery job runs every minute. AutoDeliveryService handles API-based delivery. OrderCreated event triggers ProcessAutoDeliveryListener.
v1: true

### REQ-manual-delivery
scope: delivery
description: Account trading and item products support manual delivery by sellers.
acceptance: PUT /api/seller/orders/{id}/deliver marks order delivered. ManualDeliveryService handles flow. Seller receives notification.
v1: true

### REQ-dispute
scope: dispute
description: Buyers can raise disputes/complaints on orders.
acceptance: Disputes table with reason, status, resolution. DisputeCreated event notifies admin and seller. API: PUT /api/admin/disputes/{id}.
v1: true

### REQ-homepage
scope: homepage
description: Homepage renders hero carousel, category chips, popular games grid, product showcase, trust badges, payment method showcase.
acceptance: Route / renders all sections server-side. Flash sale section with countdown timer. SEO-friendly footer.
v1: true

### REQ-seller-dashboard
scope: seller
description: Verified sellers can manage product listings, view/process orders, track earnings, configure auto-delivery.
acceptance: Routes /seller/*. Product CRUD. Order management and delivery. Earnings and withdrawal. KYC required.
v1: true

### REQ-notifications-email
scope: notifications
description: Email notifications sent for key order events.
acceptance: Mailables exist and functional: OrderConfirmationMail, PaymentReceivedMail, DeliveryNotificationMail, NewOrderSellerMail, RefundNotificationMail. EmailNotificationService sends without exceptions.
v1: true

### REQ-admin-panel
scope: admin
description: Admin panel provides analytics dashboard, game/category management, product moderation, dispute resolution, user management, banner management.
acceptance: Routes /admin/*. AdminCategoryController at /admin/categories. All admin controllers functional. EnsureAdmin middleware enforced.
v1: true

### REQ-test-coverage
scope: quality
description: ≥80% test coverage for Actions/Services kritis.
acceptance: Feature and Unit tests exist for CreateOrderAction, ProcessPaymentAction, WalletService. php artisan test passes. No placeholder ExampleTest-only suite.
v1: true

---

## Milestone M2 — Growth (Phase 8–11)

### REQ-recommendations
scope: personalization
description: Platform shows personalized product recommendations based on user history.
acceptance: Recently viewed products tracked. Wishlist/favorites managed. POST /api/user/favorites/{productId} toggles. Web: /profile/favorites.
v1: false

### REQ-seo-pages
scope: SEO
description: SEO-optimized category landing pages and game-specific landing pages exist.
acceptance: Routes /c/{category_slug} and /v/{keyword}. Structured data, meta tags, sitemap. Blade SSR crawlable.
v1: false

### REQ-social-reviews
scope: community
description: Users can leave reviews with photo proof; sellers have trust levels; users can follow sellers.
acceptance: Reviews support image attachments. Seller ratings tracked. Follow relationship modeled. Referral system.
v1: false

### REQ-vouchers
scope: promotions
description: Discount voucher/coupon system.
acceptance: Vouchers table. Applied at checkout via /api/voucher/apply. CreateOrderAction.applyVoucher(). Admin CRUD: /api/admin/vouchers.
v1: false

### REQ-flash-sale
scope: promotions
description: Time-limited flash sale sections on homepage with countdown timer.
acceptance: Flash sale products on homepage. Alpine.js countdownTimer. .gc-badge-hot marks flash sale items.
v1: false

### REQ-notifications-full
scope: notifications
description: Users receive notifications via push, in-app, and WhatsApp.
acceptance: WhatsAppNotificationService real integration (Fonnte/Wablas). In-app notification center. Real-time via Laravel Reverb. Notification bell in navbar.
v1: false

### REQ-admin-advanced
scope: admin
description: Advanced admin features: payment reconciliation, full analytics, seller subscription management.
acceptance: Admin analytics dashboard with charts. Payment reconciliation reports. Seller plan management.
v1: false

---

## Milestone M3 — Scale (Phase 12–15)

### REQ-chat
scope: communication
description: Real-time buyer-seller chat system.
acceptance: ChatMessage model. Laravel Reverb WebSocket channel. Chat from product detail page.
v1: false

### REQ-escrow
scope: high-value trading
description: Escrow/trading system for high-value account trades.
acceptance: Escrow holds funds until delivery confirmed. OrderStatus state machine handles escrow states.
v1: false

### REQ-pwa
scope: mobile
description: Progressive Web App support.
acceptance: PWA manifest, service worker for offline/caching. Mobile-first design.
v1: false

### REQ-mobile-api
scope: mobile
description: Mobile app API ready for React Native/Flutter.
acceptance: Stateless API. Versioned /api/V1/*. Sanctum compatible with mobile tokens.
v1: false

### REQ-performance-infra
scope: infrastructure
description: Redis caching, CDN optimization, rate limiting, and monitoring.
acceptance: Redis cache with defined TTLs. Cloudflare CDN. Rate limiting. Sentry + Telescope monitoring.
v1: false

---

## Non-Functional Requirements

### REQ-nfr-performance
description: Page load < 2s, API response < 200ms.
acceptance: LCP <2.5s, FID <100ms, CLS <0.1, TTI <3.5s. CSS <50KB gzipped, JS <30KB gzipped.

### REQ-nfr-security
description: OWASP Top 10 compliance, CSP headers, input validation, XSS/CSRF protection.
acceptance: All FRAMEWORK.md Security Checklist items satisfied. FormRequest validation on all mutations.

### REQ-nfr-availability
description: 99.9% uptime target.
acceptance: Horizontal scaling ready, stateless API, Redis queues for async resilience.

### REQ-nfr-localization
description: Indonesian (ID) primary language, English (EN) secondary.
acceptance: SetLocale middleware. Laravel localization files for ID/EN.

### REQ-nfr-accessibility
description: WCAG 2.1 Level AA compliance.
acceptance: focus-visible rings, color contrast ≥4.5:1, alt text, aria-labels, keyboard-navigable, Lighthouse a11y >90.

---

## Traceability

| Requirement | Phase | Status |
|-------------|-------|--------|
| REQ-auth-routes | Phase 1 | Pending |
| REQ-auth-register | Phase 1 | Pending |
| REQ-auth-login | Phase 1 | Pending |
| REQ-auth-2fa | Phase 1 | Pending |
| REQ-auth-rbac | Phase 1 | Pending |
| REQ-auth-kyc | Phase 1 | Pending |
| REQ-auth-profile | Phase 2 | Pending |
| REQ-wallet | Phase 2 | Pending |
| REQ-catalog-categories | Phase 2 | Pending |
| REQ-homepage | Phase 2 | Pending |
| REQ-catalog-listing | Phase 3 | Pending |
| REQ-catalog-detail | Phase 3 | Pending |
| REQ-catalog-variants | Phase 3 | Pending |
| REQ-catalog-reviews | Phase 3 | Pending |
| REQ-search-fulltext | Phase 3 | Pending |
| REQ-search-autocomplete | Phase 3 | Pending |
| REQ-search-filters | Phase 3 | Pending |
| REQ-cart | Phase 4 | Pending |
| REQ-checkout | Phase 4 | Pending |
| REQ-payment-gateway | Phase 4 | Pending |
| REQ-order-tracking | Phase 4 | Pending |
| REQ-auto-delivery | Phase 5 | Pending |
| REQ-manual-delivery | Phase 5 | Pending |
| REQ-dispute | Phase 5 | Pending |
| REQ-notifications-email | Phase 5 | Pending |
| REQ-seller-dashboard | Phase 6 | Pending |
| REQ-admin-panel | Phase 6 | Pending |
| REQ-test-coverage | Phase 7 | Pending |
| REQ-recommendations | Phase 8 | Pending |
| REQ-seo-pages | Phase 8 | Pending |
| REQ-vouchers | Phase 9 | Pending |
| REQ-flash-sale | Phase 9 | Pending |
| REQ-social-reviews | Phase 10 | Pending |
| REQ-notifications-full | Phase 10 | Pending |
| REQ-admin-advanced | Phase 11 | Pending |
| REQ-chat | Phase 12 | Pending |
| REQ-escrow | Phase 12 | Pending |
| REQ-pwa | Phase 13 | Pending |
| REQ-mobile-api | Phase 13 | Pending |
| REQ-performance-infra | Phase 14 | Pending |
| REQ-nfr-performance | Phase 7 | Pending |
| REQ-nfr-security | Phase 1 | Pending |
| REQ-nfr-localization | Phase 1 | Pending |
| REQ-nfr-accessibility | Phase 7 | Pending |
| REQ-nfr-availability | Phase 14 | Pending |
