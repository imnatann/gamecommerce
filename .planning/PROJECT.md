# Project: GameCommerce

## Core Value

Marketplace digital game Indonesia yang memungkinkan buyer browse, checkout, bayar via Midtrans/Xendit, dan terima produk digital secara otomatis — end-to-end tanpa friction, dengan kepercayaan yang dibangun lewat KYC seller, escrow, dan sistem review.

## Project Identity

- **Name:** GameCommerce
- **Tagline:** One Stop Gaming Marketplace — Top Up, Trade, Play
- **Inspiration:** itemku.com, g2g.com
- **Market:** Indonesia (primary)
- **Repo:** /Users/tokaf/gamecommerce

## Description

Marketplace untuk produk digital game: top-up, game key, akun, item, voucher, joki, koin. Tiga peran utama (buyer, seller, admin) dengan alur transaksi penuh dari browse hingga auto-delivery produk instan.

## Target Runtime

- **Backend:** Laravel 12, PHP 8.3+
- **Frontend:** Blade + Alpine.js 3.14 + Tailwind CSS v4 + Vite 6
- **Database:** MySQL 8 (prod), SQLite (local dev)
- **Cache/Queue:** Redis + Laravel Horizon (3 queues: payments, notifications, indexing)
- **Search:** Meilisearch via Laravel Scout
- **WebSocket:** Laravel Reverb
- **Payment:** Midtrans (primary) + Xendit (secondary)
- **Storage:** S3-compatible (Cloudflare R2 / DO Spaces) via spatie/laravel-medialibrary
- **Auth:** Laravel Fortify + Socialite + Sanctum

## Primary Language

Bahasa Indonesia (id), fallback English (en). SetLocale middleware di web stack.

## Success Metric

MVP marketplace dapat memproses end-to-end transaction (browse → checkout → pay via Midtrans/Xendit → auto-deliver instant product → konfirmasi) tanpa error pada smoke test, dengan minimum 3 peran user (buyer/seller/admin) berfungsi penuh dan ≥80% test coverage untuk Actions/Services kritis (CreateOrderAction, ProcessPaymentAction, WalletService).

## Current State (as of 2026-05-08)

Codebase adalah early prototype dengan fondasi kuat:
- Laravel skeleton + 19 models + 7 enums + OrderStatus state machine
- spatie/permission + spatie/medialibrary terpasang
- Routes publik catalog, auth-protected buyer, API v1, admin/seller backoffice sudah ada
- Actions: CreateOrderAction, ProcessPaymentAction, HandleDeliveryAction, CreateDisputeAction, CreateReviewAction
- Services: PaymentManager+Midtrans (Xendit STUB), AutoDeliveryService, ManualDeliveryService, WalletService, MeilisearchService
- 24 migrations, 4 layouts, 18 components Blade, 6 pages, admin/seller views

**Known gaps yang harus diselesaikan di roadmap:**
1. Profile views (pages.profile.{index,orders,wallet,favorites}) tidak ada di disk
2. XenditService::callXenditApi throws RuntimeException — integrasi belum selesai
3. Mailables (OrderConfirmationMail, PaymentReceivedMail, DeliveryNotificationMail, NewOrderSellerMail, RefundNotificationMail) tidak ada
4. WhatsAppNotificationService adalah placeholder (Fonnte/Wablas belum terintegrasi)
5. Tidak ada App\Policies (ProductPolicy, OrderPolicy, SellerPolicy)
6. Auth named routes (login, register, logout, dll) tidak terdaftar di routes/web.php
7. /api/cart/count dan /api/voucher/apply belum ada di api.php
8. seller.balance.index route tidak sesuai (layout seller pakai 'seller.balance', route pakai 'seller.earnings')
9. AdminCategoryController tidak ada meski sitemap PLANNING.md memerlukannya
10. KYC seller verification flow tidak terhubung (route seller.kyc.verify dari EnsureKycVerified middleware)
11. Tidak ada Feature/Unit tests untuk Actions/Services kritis
12. Route /c/{category_slug}, /v/{keyword} belum terdaftar

## Constraints

| ID | Type | Summary |
|----|------|---------|
| CONSTRAINT-001 | nfr | PHP ^8.3, Laravel ^12.0 minimum |
| CONSTRAINT-002 | protocol | Blade + Alpine.js only — no SPA |
| CONSTRAINT-003 | schema | Tailwind CSS v4 @theme directive format |
| CONSTRAINT-004 | schema | gc-* CSS custom property namespace |
| CONSTRAINT-005 | protocol | OrderStatus transitions via canTransitionTo() only |
| CONSTRAINT-006 | api-contract | Data access via Repository interfaces |
| CONSTRAINT-007 | protocol | Complex ops via Action classes with DB::transaction |
| CONSTRAINT-008 | protocol | 3 named queues: payments (high), notifications (normal), indexing (low) |
| CONSTRAINT-009 | nfr | Defined cache TTLs per FRAMEWORK.md caching strategy |
| CONSTRAINT-010 | nfr | OWASP security baseline — full checklist before production |
| CONSTRAINT-011 | api-contract | API controllers under Api/V1/ namespace |
| CONSTRAINT-012 | nfr | Min 44px touch targets (mobile-first, 70%+ mobile traffic) |
| CONSTRAINT-013 | nfr | LCP <2.5s, FID <100ms, CLS <0.1, CSS <50KB, JS <30KB |
| CONSTRAINT-014 | nfr | WCAG 2.1 Level AA, Lighthouse a11y >90 |
| CONSTRAINT-015 | schema | 24-migration sequence — new migrations respect FK dependency order |

## Decisions

All decisions are SPEC-implied (no ADRs locked). Can be overridden by future ADR.

| ID | Decision | Source |
|----|----------|--------|
| DECISION-001 | Laravel 12 / PHP 8.3+ | FRAMEWORK.md |
| DECISION-002 | Blade + Alpine.js SSR, no SPA | FRAMEWORK.md |
| DECISION-003 | Tailwind CSS v4 + gc-* design system, dark mode default | theme.md |
| DECISION-004 | MySQL 8 prod / SQLite local dev | FRAMEWORK.md + PLANNING.md |
| DECISION-005 | Meilisearch via Laravel Scout (Algolia deferred) | FRAMEWORK.md |
| DECISION-006 | Redis queues + Laravel Horizon, 3 named queues | FRAMEWORK.md |
| DECISION-007 | Laravel Reverb for WebSocket | FRAMEWORK.md |
| DECISION-008 | Fortify + Socialite + Sanctum auth | FRAMEWORK.md |
| DECISION-009 | Midtrans (primary) + Xendit (secondary) payment | FRAMEWORK.md |
| DECISION-010 | Repository + Service + Action patterns; enum state machines | FRAMEWORK.md |
| DECISION-011 | S3-compatible storage via spatie/laravel-medialibrary | PLANNING.md |
| DECISION-012 | Lucide Icons | theme.md |

## Key Decisions Log

| Phase | Decision | Rationale |
|-------|----------|-----------|
| - | - | - |

*(Updated as decisions are made during execution)*
