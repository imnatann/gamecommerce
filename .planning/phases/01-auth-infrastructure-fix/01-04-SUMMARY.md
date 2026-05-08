---
phase: 01-auth-infrastructure-fix
plan: "04"
subsystem: seller-kyc
tags: [kyc, seller, controller, form-request, blade, routes, medialibrary]
dependency_graph:
  requires:
    - 01-01  # Test scaffold
    - 01-02  # Policies + Gate registration
    - 01-03  # EnsureNotBanned middleware
  provides:
    - KycController (index + store)
    - KycSubmissionRequest (validation)
    - seller.kyc.verify route (resolves EnsureKycVerified gap)
    - seller.kyc.store route
    - KYC Blade view
  affects:
    - app/Http/Middleware/EnsureKycVerified.php (Route::has('seller.kyc.verify') now returns true)
tech_stack:
  added: []
  patterns:
    - FormRequest for complex file upload validation
    - Auth facade (not auth() helper) — consistent with SettingsController pattern
    - spatie/medialibrary addMediaFromRequest() for file storage
    - Alpine.js inline x-data for file preview components
    - @push('scripts') not @section('scripts')
key_files:
  created:
    - app/Http/Requests/Seller/KycSubmissionRequest.php
    - app/Http/Controllers/Seller/KycController.php
    - resources/views/seller/kyc/verify.blade.php
  modified:
    - routes/seller.php
decisions:
  - "FormRequest used for KYC despite PATTERNS.md noting Seller controllers avoid it — file upload validation complexity justifies deviation"
  - "KYC routes placed before Route::get('/dashboard') to ensure they load first and are accessible to unverified sellers"
  - "User::registerMediaCollections() kyc collections owned by Plan 05 — Plan 04 only consumes them at runtime to avoid Wave 1 race condition"
metrics:
  duration: "~6 min"
  completed: "2026-05-08"
  tasks_completed: 3
  files_created: 3
  files_modified: 1
---

# Phase 1 Plan 04: KycController + KycSubmissionRequest + Routes + Blade View Summary

KYC seller verification flow — KycController, FormRequest validasi NIK+file, dua KYC routes, dan Blade form view dengan Alpine.js file preview.

## What Was Built

**Critical gap fixed:** `Route::has('seller.kyc.verify')` di `EnsureKycVerified.php` line 31 sebelumnya selalu `false` karena route belum terdaftar. Unverified seller di-redirect ke halaman yang tidak ada. Plan ini mendaftarkan route `seller.kyc.verify` sehingga redirect loop teratasi.

### Files Created

| File | Purpose |
|------|---------|
| `app/Http/Requests/Seller/KycSubmissionRequest.php` | Validates KYC payload: NIK 16-digit, file uploads, bank fields; authorize() blocks verified sellers |
| `app/Http/Controllers/Seller/KycController.php` | index() renders view, store() uploads via medialibrary + sets kyc_status=pending |
| `resources/views/seller/kyc/verify.blade.php` | KYC form: @csrf, multipart, Alpine file preview, status banners, bank + NPWP sections |

### Files Modified

| File | Change |
|------|--------|
| `routes/seller.php` | Added KycController import + 2 KYC routes before dashboard route |

## Commits

| Task | Commit | Message |
|------|--------|---------|
| 1 | `3229c9b` | feat(01-04): KycSubmissionRequest + KycController |
| 2 | `465a432` | feat(01-04): register KYC routes in seller.php |
| 3 | `74ee50e` | feat(01-04): KYC Blade view seller/kyc/verify |

## Verification Results

```
GET|HEAD  seller/kyc ....... seller.kyc.verify › Seller\KycController@index
POST      seller/kyc ........ seller.kyc.store › Seller\KycController@store
```

- `php -l KycSubmissionRequest.php` → No syntax errors
- `php -l KycController.php` → No syntax errors
- `grep -c "seller.kyc.store" verify.blade.php` → 1

## Deviations from Plan

### Justified Deviations

**1. [FormRequest usage] — PATTERNS.md menyatakan Seller controllers tidak menggunakan FormRequest**
- **Reason:** KYC submission memerlukan validasi kompleks (file upload mime+size, NIK size:16, multiple required fields). FormRequest lebih bersih dan testable.
- **Impact:** Deviation yang diakui di plan action section, tidak mengubah arsitektur.

### No Auto-fix Deviations

Plan executed as written. Tidak ada bug, missing dependencies, atau blocking issues ditemukan.

## Threat Surface Compliance

Semua threat mitigations dari `<threat_model>` diimplementasikan:

| Threat ID | Mitigation | Status |
|-----------|-----------|--------|
| T-01-04-01 | `mimes:jpg,jpeg,png` + medialibrary server-side mime detection | Implemented |
| T-01-04-02 | `max:5120` (5MB) di FormRequest | Implemented |
| T-01-04-03 | `authorize()` cek `!isKycVerified()` | Implemented |
| T-01-04-04 | NIK di meta JSON, tidak di-expose ke API publik | Accepted |
| T-01-04-05 | `@csrf` directive di form | Implemented |

## Known Stubs

Tidak ada stubs. KycController::store() wires ke medialibrary collections (`kyc_id_photo`, `kyc_selfie`) yang akan didaftarkan di Plan 05. Runtime dependency — bukan compile-time.

## Self-Check: PASSED

- `app/Http/Requests/Seller/KycSubmissionRequest.php` — FOUND
- `app/Http/Controllers/Seller/KycController.php` — FOUND
- `resources/views/seller/kyc/verify.blade.php` — FOUND
- routes/seller.php modified — FOUND
- Commit `3229c9b` — FOUND
- Commit `465a432` — FOUND
- Commit `74ee50e` — FOUND
- `seller.kyc.verify` route resolves — VERIFIED via route:list
