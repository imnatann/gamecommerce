---
phase: 01-auth-infrastructure-fix
plan: "02"
subsystem: auth
tags: [laravel-policy, gate, spatie-permission, rbac, authorization]

requires:
  - phase: 01-auth-infrastructure-fix
    provides: User model dengan isSeller(), isKycVerified(), hasPlatformRole(), isAdmin(), isBuyer()

provides:
  - ProductPolicy dengan viewAny/view (publik), create/update (seller+KYC), delete (seller+ownership), toggleActive
  - OrderPolicy dengan view (buyer OR seller-with-item OR admin), updateStatus (seller OR admin), dispute (buyer+status)
  - SellerPolicy dengan manage/accessPanel (isSeller), submitKyc (seller+!KYC+!pending)
  - AppServiceProvider Gate::policy untuk Product dan Order
  - Gate::define untuk manage-seller-panel, access-seller-panel, submit-kyc
  - Gate::before super_admin bypass

affects:
  - 01-auth-infrastructure-fix
  - seller-controllers
  - product-controllers
  - order-controllers

tech-stack:
  added: []
  patterns:
    - "Gate::policy registration di AppServiceProvider::boot() (bukan AuthServiceProvider)"
    - "Gate::before() return null untuk non-super-admin agar policy normal dievaluasi"
    - "BackedEnum-safe status comparison di OrderPolicy::dispute()"
    - "getRawOriginal() untuk bypass accessor di SellerPolicy::submitKyc()"

key-files:
  created:
    - app/Policies/ProductPolicy.php
    - app/Policies/OrderPolicy.php
    - app/Policies/SellerPolicy.php
  modified:
    - app/Providers/AppServiceProvider.php

key-decisions:
  - "Gate::before() return null (bukan false) untuk non-super-admin agar policy chain tetap berjalan"
  - "SellerPolicy::submitKyc menggunakan getRawOriginal('kyc_status') untuk bypass accessor yang map 'verified'->'approved'"
  - "OrderPolicy::dispute menggunakan BackedEnum-safe comparison (instanceof check) untuk handle status enum maupun string"
  - "viewAny dan view di ProductPolicy menggunakan ?User (nullable) karena route publik tidak require auth"

patterns-established:
  - "Policy-1: Publik policy methods menggunakan ?User $user (nullable) bukan User"
  - "Policy-2: Seller ownership check selalu $user->id === $model->seller_id (strict)"
  - "Policy-3: Admin bypass dilakukan via Gate::before() bukan hardcode di setiap policy method"

requirements-completed:
  - REQ-auth-rbac
  - REQ-nfr-security

duration: 8min
completed: 2026-05-08
---

# Phase 1 Plan 02: Policies + AppServiceProvider Gate Registration Summary

**3 Laravel Policy class (ProductPolicy, OrderPolicy, SellerPolicy) + Gate registration di AppServiceProvider dengan super_admin bypass via Gate::before()**

## Performance

- **Duration:** 8 min
- **Started:** 2026-05-08T00:00:00Z
- **Completed:** 2026-05-08T00:08:00Z
- **Tasks:** 2
- **Files modified:** 4

## Accomplishments

- Buat ProductPolicy dengan aturan ownership seller+KYC, viewAny/view publik (nullable User)
- Buat OrderPolicy dengan akses multi-role (buyer, seller-via-items, admin) dan dispute guard
- Buat SellerPolicy dengan guard double-submit KYC via getRawOriginal()
- Update AppServiceProvider: Gate::policy x2, Gate::define x3, Gate::before super_admin bypass

## Task Commits

1. **Task 1: ProductPolicy dan OrderPolicy** - `43c22e3` (feat)
2. **Task 2: SellerPolicy dan AppServiceProvider** - `7b8d561` (feat)

## Files Created/Modified

- `app/Policies/ProductPolicy.php` - Authorization rules untuk Product model (6 methods)
- `app/Policies/OrderPolicy.php` - Authorization rules untuk Order model (3 methods)
- `app/Policies/SellerPolicy.php` - Non-model seller panel authorization (3 methods)
- `app/Providers/AppServiceProvider.php` - Gate::policy, Gate::define, Gate::before registration

## Decisions Made

- `Gate::before()` return `null` (bukan `false`) untuk non-super-admin — memastikan policy chain normal tetap dieksekusi
- `SellerPolicy::submitKyc` menggunakan `getRawOriginal('kyc_status')` karena accessor `getKycStatusAttribute` mengubah `'verified'` -> `'approved'`; perlu raw value `'pending'` untuk guard double-submit
- `OrderPolicy::dispute` menggunakan `instanceof \BackedEnum` check untuk handle status sebagai enum maupun string (future-safe)
- `viewAny` dan `view` di ProductPolicy menggunakan `?User $user` (nullable) karena produk bisa dilihat tanpa login

## Deviations from Plan

None - plan executed exactly as written.

## Issues Encountered

None.

## Threat Surface Scan

Semua mitigations dari threat_model plan diimplementasikan:

| Threat ID | Mitigasi |
|-----------|---------|
| T-01-02-01 | ProductPolicy::update() cek `$user->id === $product->seller_id` |
| T-01-02-02 | OrderPolicy::view() query DB langsung via `items()->where('seller_id', ...)` |
| T-01-02-03 | Gate::before() super_admin bypass disengaja, accepted risk |
| T-01-02-04 | SellerPolicy::submitKyc() cek `getRawOriginal('kyc_status') !== 'pending'` |

## Known Stubs

None.

## User Setup Required

None - tidak ada konfigurasi eksternal diperlukan.

## Next Phase Readiness

- Semua `$this->authorize()` calls di controller sekarang akan diproses oleh policy yang benar
- Gate::before() aktif — super_admin bisa akses semua resource
- Siap untuk Plan 03 (middleware chain) dan Plan 04 (controller authorization integration)

## Self-Check: PASSED

- `app/Policies/ProductPolicy.php` - FOUND
- `app/Policies/OrderPolicy.php` - FOUND
- `app/Policies/SellerPolicy.php` - FOUND
- `app/Providers/AppServiceProvider.php` - FOUND (modified)
- Commit `43c22e3` - FOUND
- Commit `7b8d561` - FOUND

---
*Phase: 01-auth-infrastructure-fix*
*Completed: 2026-05-08*
