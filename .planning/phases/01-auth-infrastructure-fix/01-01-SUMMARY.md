---
phase: 01-auth-infrastructure-fix
plan: "01"
subsystem: tests
tags: [test-scaffold, auth, policies, kyc, locale, wave-0]
dependency_graph:
  requires: []
  provides:
    - tests/Feature/Auth/LoginTest.php
    - tests/Feature/Auth/RegisterTest.php
    - tests/Feature/Auth/LogoutTest.php
    - tests/Feature/Auth/TwoFactorTest.php
    - tests/Feature/Auth/BanCheckTest.php
    - tests/Feature/Policies/ProductPolicyTest.php
    - tests/Feature/Policies/OrderPolicyTest.php
    - tests/Feature/Policies/SellerPolicyTest.php
    - tests/Feature/Seller/KycSubmissionTest.php
    - tests/Feature/LocaleSwitchTest.php
  affects: []
tech_stack:
  added: []
  patterns:
    - PHPUnit Feature test dengan RefreshDatabase
    - Gate::allows() untuk policy assertions
    - Storage::fake() untuk file upload tests
key_files:
  created:
    - tests/Feature/Auth/LoginTest.php
    - tests/Feature/Auth/RegisterTest.php
    - tests/Feature/Auth/LogoutTest.php
    - tests/Feature/Auth/TwoFactorTest.php
    - tests/Feature/Auth/BanCheckTest.php
    - tests/Feature/Policies/ProductPolicyTest.php
    - tests/Feature/Policies/OrderPolicyTest.php
    - tests/Feature/Policies/SellerPolicyTest.php
    - tests/Feature/Seller/KycSubmissionTest.php
    - tests/Feature/LocaleSwitchTest.php
  modified: []
decisions:
  - "Test scaffold dibuat sebagai Wave 0 Nyquist compliance — kontrak ditetapkan sebelum implementasi dimulai"
  - "Test menggunakan named routes (route('login'), route('logout'), dll) bukan hardcoded URLs"
  - "Gate::allows() digunakan di policy tests untuk konsistensi dengan cara Laravel mengevaluasi policies"
metrics:
  duration: "~8 menit"
  completed: "2026-05-08T07:46:13Z"
  tasks_completed: 2
  tasks_total: 2
  files_created: 10
  files_modified: 0
---

# Phase 01 Plan 01: Test Scaffold Summary

10 test scaffold files dibuat mendefinisikan kontrak auth — semua pass syntax check, siap untuk diisi implementasi di Plan 02-05.

## Tasks Completed

| Task | Name | Commit | Files |
|------|------|--------|-------|
| 1 | Auth flow test scaffolds | bbd7d95 | LoginTest, RegisterTest, LogoutTest, TwoFactorTest, BanCheckTest |
| 2 | Policy/KYC/Locale test scaffolds | 39319a4 | ProductPolicyTest, OrderPolicyTest, SellerPolicyTest, KycSubmissionTest, LocaleSwitchTest |

## What Was Built

Wave 0 test scaffold untuk Phase 1 auth infrastructure. Semua 10 file test mendefinisikan kontrak perilaku yang harus dipenuhi oleh implementasi Plans 02-05:

**Auth Tests (5 file):**
- `LoginTest` — page render, valid/invalid credentials, authenticated user redirect
- `RegisterTest` — page render, buyer role assignment, wallet creation, duplicate email validation
- `LogoutTest` — POST redirect ke login, GET 404/405
- `TwoFactorTest` — challenge page, enable/disable require auth
- `BanCheckTest` — banned user auto-logout+redirect, API 403

**Policy/KYC/Locale Tests (5 file):**
- `ProductPolicyTest` — seller ownership, KYC-unverified block, buyer deny
- `OrderPolicyTest` — buyer/seller item access, stranger deny
- `SellerPolicyTest` — manage-panel gate, submit-kyc allow/deny logic
- `KycSubmissionTest` — unverified seller page access, pending status update, NIK validation
- `LocaleSwitchTest` — session update, locale 404 untuk locale tidak didukung

## Deviations from Plan

None — plan executed exactly as written.

## Known Stubs

Semua test files adalah scaffolds yang akan fail (expected) sampai implementasi Plans 02-05 selesai. Ini bukan stubs — ini adalah Wave 0 kontrak intentional.

Routes yang belum ada dan akan menyebabkan test fail:
- `seller.kyc.verify` — dibuat di Plan 04
- `seller.kyc.store` — dibuat di Plan 04
- `locale.switch` — dibuat di Plan 05

Gates yang belum terdaftar:
- `manage-seller-panel` — didaftarkan di Plan 02
- `submit-kyc` — didaftarkan di Plan 02

## Self-Check: PASSED

- [x] tests/Feature/Auth/LoginTest.php — FOUND
- [x] tests/Feature/Auth/RegisterTest.php — FOUND
- [x] tests/Feature/Auth/LogoutTest.php — FOUND
- [x] tests/Feature/Auth/TwoFactorTest.php — FOUND
- [x] tests/Feature/Auth/BanCheckTest.php — FOUND
- [x] tests/Feature/Policies/ProductPolicyTest.php — FOUND
- [x] tests/Feature/Policies/OrderPolicyTest.php — FOUND
- [x] tests/Feature/Policies/SellerPolicyTest.php — FOUND
- [x] tests/Feature/Seller/KycSubmissionTest.php — FOUND
- [x] tests/Feature/LocaleSwitchTest.php — FOUND
- [x] Commit bbd7d95 — FOUND
- [x] Commit 39319a4 — FOUND
- [x] Semua 10 file pass `php -l` syntax check
