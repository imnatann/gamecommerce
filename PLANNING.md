# 🎮 GameCommerce - Game Marketplace Platform

## Project Overview

**Nama Project:** GameCommerce
**Deskripsi:** Platform marketplace game digital terbesar dan terpercaya untuk jual beli top-up game, game key, akun, item, voucher, dan jasa joki — terinspirasi dari itemku.com & g2g.com.

**Tagline:** "One Stop Gaming Marketplace — Top Up, Trade, Play"

---

## 1. Business Model

### Revenue Streams
| Stream | Deskripsi | Estimasi |
|--------|-----------|----------|
| Commission per transaction | Potongan dari setiap transaksi penjual | 3-8% |
| Featured listing | Penjual bayar untuk highlight produk | Premium placement |
| Banner ads | Iklan di homepage & halaman kategori | CPM/CPC |
| Subscription seller | Paket langganan penjual (low fee) | Monthly/yearly |
| Boosting service fee | Fee tambahan dari jasa joki | 5-10% |

### User Roles
- **Buyer** — Membeli produk game (top-up, key, item, akun)
- **Seller** — Menjual produk game setelah verifikasi
- **Admin** — Manage platform, dispute resolution, content
- **Super Admin** — Full access, analytics, configuration

---

## 2. Feature Modules

### Phase 1 — MVP (Week 1-8)

#### 2.1 Authentication & User Management
- [ ] Register (email, Google, phone)
- [ ] Login (email/password, social login, OTP)
- [ ] Profile management (avatar, username, bio)
- [ ] Wallet system (balance, history, withdrawal)
- [ ] Role-based access control (buyer/seller/admin)
- [ ] KYC verification untuk seller
- [ ] Two-factor authentication (2FA)

#### 2.2 Product Catalog
- [ ] Game categories (top-up, game key, item, akun, voucher, joki, koin game)
- [ ] Game listing page (search, filter, sort)
- [ ] Product detail page (description, server info, instant delivery info)
- [ ] Product variants (region, quantity, delivery method)
- [ ] Product reviews & ratings
- [ ] Product comparison

#### 2.3 Search & Discovery
- [ ] Full-text search (game name, product name)
- [ ] Autocomplete search bar
- [ ] Popular searches (trending)
- [ ] Category-based navigation
- [ ] Filter (price, region, delivery method, rating)
- [ ] Sort (cheapest, popular, newest, best rating)

#### 2.4 Transaction System
- [ ] Cart system (add to cart, bulk purchase)
- [ ] Instant checkout flow
- [ ] Order confirmation & payment selection
- [ ] Payment gateway integration (Midtrans/Xendit)
- [ ] Payment methods: QRIS, bank transfer, e-wallet (GoPay, OVO, DANA, ShopeePay), CC, convenience store (Alfamart, Indomaret)
- [ ] Order tracking (pending, processing, completed, failed)
- [ ] Auto-delivery for digital products (API-based)
- [ ] Manual delivery system (for account trading, items)
- [ ] Dispute/complaint system

#### 2.5 Homepage
- [ ] Hero carousel/banner (promos, new games)
- [ ] Quick category links (Top Up, Game Key, Akun, Voucher, dll.)
- [ ] Popular games grid
- [ ] Product showcase sections (new, hot deals, recommended)
- [ ] Game key section (featured deals)
- [ ] Trust badges (secure transaction, money-back guarantee, 24/7 support)
- [ ] Payment method showcase
- [ SEO-friendly footer

#### 2.6 Seller Dashboard
- [ ] Product listing (CRUD)
- [ ] Order management (incoming, processing, shipped)
- [ ] Revenue/earnings overview
- [ ] Withdrawal system
- [ ] Auto-delivery configuration
- [ ] Rating & review management

### Phase 2 — Growth (Week 9-16)

#### 2.7 Advanced Search & Personalization
- [ ] Personalized recommendations (based on history)
- [ ] Recently viewed products
- [ ] Wishlist/favorites
- [ ] Game-specific landing pages
- [ ] SEO-optimized category pages

#### 2.8 Social & Community
- [ ] User reviews with photo proof
- [ ] Seller ratings & trust level
- [ ] Follow favorite sellers
- [ ] Discount voucher/coupon system
- [ ] Flash sale system
- [ ] Referral system (invite friends, get discount)

#### 2.9 Notification System
- [ ] Push notification (browser)
- [ ] Email notification (order status, promo)
- [ ] In-app notification center
- [ ] WhatsApp notification integration
- [ ] Real-time order status updates (WebSocket/broadcasting)

#### 2.10 Admin Panel
- [ ] Dashboard analytics (sales, users, transactions)
- [ ] Game & category management
- [ ] Product moderation
- [ ] Dispute resolution
- [ ] User management (ban, verify, restrict)
- [ ] Banner/promo management
- [ ] Payment reconciliation

### Phase 3 — Scale (Week 17-24)

#### 2.11 Advanced Features
- [ ] Chat system (buyer-seller real-time chat)
- [ ] Escrow/trading system for high-value accounts
- [ ] API-based auto top-up (direct integration with game publishers)
- [ ] Multi-region/currency support
- [ ] PWA (Progressive Web App)
- [ ] Mobile app API (React Native/Flutter ready)
- [ ] Affiliate system
- [ ] Blog/content management
- [ ] Streaming section (game streaming integration)

#### 2.12 Performance & Infrastructure
- [ ] Redis caching layer
- [ ] CDN optimization (Cloudflare/Images)
- [ ] Queue system for async jobs (Redis Horizon)
- [ ] Search engine (Meilisearch/Algolia)
- [ ] Rate limiting & bot protection
- [ ] Monitoring (Sentry, Laravel Telescope)

---

## 3. Page Map & Sitemap

```
/ (Homepage)
/search (Search Results)
/g/{game_slug}/{product_type} (Game Product Listing)
/d/{product_slug}/{product_id} (Product Detail)
/cart (Shopping Cart)
/checkout (Checkout)
/order/{order_id} (Order Detail & Status)
/profile (User Profile)
/profile/wallet (Wallet & Transactions)
/profile/orders (Order History)
/profile/favorites (Wishlist)
/seller/dashboard (Seller Dashboard)
/seller/products (Product Management)
/seller/orders (Seller Orders)
/seller/withdraw (Withdrawal)
/admin (Admin Dashboard)
/admin/games (Game Management)
/admin/categories (Category Management)
/admin/users (User Management)
/admin/orders (Order Management)
/admin/disputes (Dispute Management)
/admin/banners (Banner Management)
/admin/vouchers (Voucher Management)
/c/{category_slug} (Category Page)
/v/{keyword} (SEO Landing Pages)
/blog (Blog)
/help (Help Center)
/auth/login
/auth/register
/auth/forgot-password
/auth/verify-email
/auth/2fa
```

---

## 4. Database Schema (Core Entities)

```
┌─────────────┐   ┌──────────────┐   ┌──────────────┐
│   users      │   │   games       │   │  categories   │
├─────────────┤   ├──────────────┤   ├──────────────┤
│ id          │   │ id           │   │ id           │
│ name        │   │ name         │   │ name         │
│ email       │   │ slug         │   │ slug         │
│ phone       │   │ icon         │   │ type         │
│ password    │   │ banner       │   │ sort_order   │
│ role        │   │ is_active    │   │ parent_id    │
│ avatar      │   │ category_id  │   └──────────────┘
│ balance     │   │ region       │
│ email_verified│  │ sort_order   │   ┌──────────────┐
│ phone_verified│  │ meta         │   │  products     │
│ kyc_status  │   └──────────────┘   ├──────────────┤
│ created_at  │                      │ id           │
└─────────────┘   ┌──────────────┐   │ seller_id    │
                  │ game_products│   │ game_id      │
                  ├──────────────┤   │ category_id  │
                  │ id           │   │ name         │
                  │ game_id      │   │ slug         │
                  │ type (topup, │   │ description  │
                  │   key,item,  │   │ price        │
                  │   akun,voucher│  │ stock        │
                  │   joki,koin) │   │ server       │
                  │ name         │   │ region       │
                  │ slug         │   │ delivery_type │
                  │ required_info│   │ is_active    │
                  │ sort_order   │   │ sold_count   │
                  │ is_active    │   │ avg_rating   │
                  └──────────────┘   │ created_at   │
                                    └──────────────┘

┌──────────────┐   ┌──────────────┐   ┌──────────────┐
│   orders     │   │  order_items  │   │   payments    │
├──────────────┤   ├──────────────┤   ├──────────────┤
│ id           │   │ id           │   │ id           │
│ buyer_id     │   │ order_id     │   │ order_id     │
│ total_amount │   │ product_id   │   │ method       │
│ status       │   │ quantity     │   │ gateway       │
│ notes        │   │ price        │   │ amount       │
│ created_at   │   │ delivery_data│   │ status       │
└──────────────┘   │ status       │   │ paid_at      │
                   └──────────────┘   └──────────────┘

┌──────────────┐   ┌──────────────┐   ┌──────────────┐
│   reviews    │   │   wallets     │   │  vouchers     │
├──────────────┤   ├──────────────┤   ├──────────────┤
│ id           │   │ id           │   │ id           │
│ user_id      │   │ user_id     │   │ code         │
│ product_id   │   │ type (in/out)│   │ type         │
│ rating       │   │ amount      │   │ discount      │
│ comment      │   │ balance_before│  │ min_purchase │
│ images       │   │ balance_after│  │ max_uses     │
│ is_anonymous │   │ description │   │ expires_at   │
│ created_at   │   │ created_at   │   │ is_active    │
└──────────────┘   └──────────────┘   └──────────────┘

┌──────────────┐   ┌──────────────┐
│  disputes    │   │   banners    │
├──────────────┤   ├──────────────┤
│ id           │   │ id           │
│ order_id     │   │ title        │
│ buyer_id     │   │ image        │
│ seller_id    │   │ link         │
│ reason       │   │ position     │
│ status       │   │ sort_order   │
│ resolution   │   │ is_active    │
│ created_at   │   │ starts_at    │
└──────────────┘   │ ends_at      │
                   └──────────────┘
```

---

## 5. API Endpoints (Key Routes)

### Public
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/games` | List all games |
| GET | `/api/games/{slug}` | Game detail with products |
| GET | `/api/products` | Search & filter products |
| GET | `/api/products/{id}` | Product detail |
| GET | `/api/categories` | List categories |
| GET | `/api/banners` | Active banners |
| GET | `/api/search` | Full-text search |

### Auth
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/register` | Register |
| POST | `/api/auth/login` | Login |
| POST | `/api/auth/logout` | Logout |
| POST | `/api/auth/forgot-password` | Reset password |
| POST | `/api/auth/verify-email` | Verify email |
| POST | `/api/auth/2fa` | 2FA verify |

### Buyer
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/user/profile` | Profile |
| PUT | `/api/user/profile` | Update profile |
| GET | `/api/user/wallet` | Wallet balance |
| POST | `/api/user/wallet/topup` | Top up wallet |
| GET | `/api/user/orders` | Order list |
| GET | `/api/user/orders/{id}` | Order detail |
| POST | `/api/orders` | Create order |
| POST | `/api/cart/add` | Add to cart |
| GET | `/api/cart` | View cart |
| DELETE | `/api/cart/{id}` | Remove from cart |
| POST | `/api/products/{id}/reviews` | Submit review |
| POST | `/api/user/favorites/{productId}` | Toggle favorite |

### Seller
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/seller/products` | List seller products |
| POST | `/api/seller/products` | Create product |
| PUT | `/api/seller/products/{id}` | Update product |
| DELETE | `/api/seller/products/{id}` | Delete product |
| GET | `/api/seller/orders` | Incoming orders |
| PUT | `/api/seller/orders/{id}/deliver` | Mark as delivered |
| GET | `/api/seller/earnings` | Earnings overview |
| POST | `/api/seller/withdraw` | Request withdrawal |

### Admin
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/dashboard` | Analytics dashboard |
| GET | `/api/admin/users` | User management |
| PUT | `/api/admin/users/{id}` | Update user status |
| GET | `/api/admin/disputes` | Dispute list |
| PUT | `/api/admin/disputes/{id}` | Resolve dispute |
| CRUD | `/api/admin/games` | Game management |
| CRUD | `/api/admin/banners` | Banner management |
| CRUD | `/api/admin/vouchers` | Voucher management |

---

## 6. Tech Stack Summary

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 12 (PHP 8.3+) |
| Frontend | Blade + Alpine.js + Vite |
| CSS Framework | Tailwind CSS 4 + Custom shadcn Theme |
| Database | MySQL 8 + Redis |
| Search | Meilisearch (via Scout) |
| Queue | Redis + Laravel Horizon |
| Real-time | Laravel Reverb (WebSocket) |
| Payment | Midtrans / Xendit |
| Storage | S3-compatible (Cloudflare R2/DO Spaces) |
| Auth | Laravel Fortify + Socialite |
| Monitoring | Sentry + Laravel Telescope |

---

## 7. Project Timeline

| Phase | Week | Deliverables |
|-------|------|-------------|
| **Phase 1: Foundation** | 1-2 | Auth, DB schema, models, migrations |
| | 3-4 | Product catalog, game pages, search |
| | 5-6 | Cart, checkout, payment integration |
| | 7-8 | Seller dashboard, order management, homepage |
| **Phase 2: Polish** | 9-10 | Reviews, wishlist, notifications |
| | 11-12 | Admin panel, dispute system |
| | 13-14 | Vouchers, flash sale, SEO pages |
| | 15-16 | Testing, bug fixes, performance tuning |
| **Phase 3: Scale** | 17-20 | Chat, escrow, real-time updates |
| | 21-22 | PWA, mobile API |
| | 23-24 | Monitoring, analytics, optimization |

---

## 8. Non-Functional Requirements

- **Performance:** Page load < 2s, API response < 200ms
- **Security:** OWASP Top 10 compliance, CSP headers, input validation, XSS/CSRF protection
- **Scalability:** Horizontal scaling ready, stateless API design
- **Availability:** 99.9% uptime target
- **Localization:** ID (Indonesian) as primary, EN as secondary
- **SEO:** SSR with Blade, structured data, sitemap, meta tags
- **Accessibility:** WCAG 2.1 Level AA compliance target