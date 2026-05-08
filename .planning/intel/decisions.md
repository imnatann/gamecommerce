# Decisions Intel

No ADR documents were present in this ingest set. The following architectural decisions are implied by SPEC-precedence sources and are recorded here for traceability. They are NOT locked — they carry SPEC authority only and can be overridden by a future ADR.

---

## DECISION-001: Backend Framework
source: /Users/tokaf/gamecommerce/FRAMEWORK.md
status: proposed (SPEC-implied, not locked)
scope: backend runtime
decision: Laravel 12 on PHP 8.3+ is the application framework.
rationale: Stated throughout FRAMEWORK.md composer.json and architecture patterns.

---

## DECISION-002: Frontend Rendering Strategy
source: /Users/tokaf/gamecommerce/FRAMEWORK.md
status: proposed (SPEC-implied, not locked)
scope: frontend rendering
decision: Server-side rendering via Laravel Blade with Alpine.js for reactivity and Vite for asset bundling. No SPA framework.
rationale: FRAMEWORK.md directory structure, package.json, and view layer confirm Blade+Alpine.js stack.

---

## DECISION-003: CSS Framework and Design System
source: /Users/tokaf/gamecommerce/theme.md
status: proposed (SPEC-implied, not locked)
scope: UI styling
decision: Tailwind CSS v4 with a custom shadcn-inspired design system (gc-* token namespace). Dark mode is the default theme.
rationale: theme.md section 11 (Tailwind CSS v4 Configuration) and section 10 (Dark/Light Mode Toggle — "Default: Dark mode").

---

## DECISION-004: Primary Database
source: /Users/tokaf/gamecommerce/FRAMEWORK.md, /Users/tokaf/gamecommerce/PLANNING.md
status: proposed (SPEC-implied, not locked)
scope: primary datastore
decision: MySQL 8 in production. SQLite is acceptable for local development only.
rationale: PLANNING.md §6 tech stack specifies MySQL 8. README.md explicitly scopes SQLite to local dev.

---

## DECISION-005: Search Engine
source: /Users/tokaf/gamecommerce/FRAMEWORK.md, /Users/tokaf/gamecommerce/PLANNING.md
status: proposed (SPEC-implied, not locked)
scope: full-text search
decision: Meilisearch via Laravel Scout (laravel/scout ^10.8). Algolia listed as alternative in PLANNING.md §2.12 but FRAMEWORK.md service layer only defines MeilisearchService — Meilisearch wins under SPEC precedence.
rationale: FRAMEWORK.md composer.json + MeilisearchService class; PLANNING.md §2.12.

---

## DECISION-006: Queue and Background Jobs
source: /Users/tokaf/gamecommerce/FRAMEWORK.md
status: proposed (SPEC-implied, not locked)
scope: async job processing
decision: Redis-backed queues managed by Laravel Horizon. Three named queues: payments (high), notifications (normal), indexing (low).
rationale: FRAMEWORK.md Queue & Job Architecture section.

---

## DECISION-007: Real-time / WebSocket
source: /Users/tokaf/gamecommerce/FRAMEWORK.md, /Users/tokaf/gamecommerce/PLANNING.md
status: proposed (SPEC-implied, not locked)
scope: real-time communication
decision: Laravel Reverb (laravel/reverb ^1.0) for WebSocket broadcasting.
rationale: FRAMEWORK.md composer.json; PLANNING.md §6 tech stack.

---

## DECISION-008: Authentication
source: /Users/tokaf/gamecommerce/FRAMEWORK.md, /Users/tokaf/gamecommerce/PLANNING.md
status: proposed (SPEC-implied, not locked)
scope: authentication layer
decision: Laravel Fortify + Laravel Socialite. API sessions via Laravel Sanctum (EnsureFrontendRequestsAreStateful middleware).
rationale: FRAMEWORK.md middleware stack + composer.json; PLANNING.md §6 tech stack.

---

## DECISION-009: Payment Gateways
source: /Users/tokaf/gamecommerce/FRAMEWORK.md, /Users/tokaf/gamecommerce/PLANNING.md
status: proposed (SPEC-implied, not locked)
scope: payment processing
decision: Midtrans (primary, midtrans/midtrans-php ^2.6 in composer.json) and Xendit (secondary) via abstracted PaymentService. Supported methods: QRIS, bank transfer, e-wallets (GoPay, OVO, DANA, ShopeePay), credit card, convenience store (Alfamart, Indomaret).
rationale: FRAMEWORK.md composer.json + PaymentService/MidtransService/XenditService; PLANNING.md §2.4.

---

## DECISION-010: Architecture Patterns
source: /Users/tokaf/gamecommerce/FRAMEWORK.md
status: proposed (SPEC-implied, not locked)
scope: application architecture
decision: Repository Pattern + Service Layer + Action Pattern for complex operations. Enum-based state machines for OrderStatus transitions.
rationale: FRAMEWORK.md Architecture Patterns section with PHP code contracts.

---

## DECISION-011: Object Storage
source: /Users/tokaf/gamecommerce/PLANNING.md
status: proposed (PRD-implied, not locked)
scope: file/media storage
decision: S3-compatible storage (Cloudflare R2 or DigitalOcean Spaces). spatie/laravel-medialibrary ^11.0 handles media management.
rationale: PLANNING.md §6 tech stack; FRAMEWORK.md composer.json.

---

## DECISION-012: Icon Library
source: /Users/tokaf/gamecommerce/theme.md
status: proposed (SPEC-implied, not locked)
scope: UI iconography
decision: Lucide Icons (consistent with shadcn/ui).
rationale: theme.md §8 Icon System.
