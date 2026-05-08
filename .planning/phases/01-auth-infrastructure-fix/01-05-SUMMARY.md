---
phase: 01-auth-infrastructure-fix
plan: "05"
subsystem: locale-switching + user-kyc-model
tags: [locale, kyc, media-collections, refactor]
dependency_graph:
  requires: []
  provides:
    - locale.switch route (POST /locale/{locale})
    - LocaleController::switch() — session-only locale switching
    - User::isKycVerified() — canonical 'verified' DB value check
    - User::registerMediaCollections() — kyc_id_photo + kyc_selfie collections
  affects:
    - app/Http/Middleware/SetLocale.php (reads session('locale') written by LocaleController)
    - Plan 04 KycController (consumes kyc_id_photo / kyc_selfie collections)
    - SellerPolicy::submitKyc() (uses isKycVerified(), behavior now correct)
tech_stack:
  added: []
  patterns:
    - Session-only locale switching via POST route + whitelist validation
    - Raw attribute access via $this->attributes[] for accessor-bypass on unsaved models
key_files:
  created:
    - app/Http/Controllers/Web/LocaleController.php
  modified:
    - routes/web.php
    - app/Models/User.php
decisions:
  - Used attributes['kyc_status'] instead of getRawOriginal('kyc_status') — getRawOriginal() returns null on unsaved/factory-make models; attributes[] is reliable for both saved and unsaved instances
  - abort(404) for unsupported locales (not redirect with error) — consistent with plan spec
metrics:
  duration: "~5 minutes"
  completed: "2026-05-08"
  tasks_completed: 2
  files_modified: 3
---

# Phase 01 Plan 05: LocaleController + User KYC Accessor Refactor + Media Collections Summary

Session-only locale switching endpoint (id/en whitelist) and User model KYC accessor refactored to remove verified→approved remapping, with kyc_id_photo/kyc_selfie media collections added for Plan 04 consumption.

## Tasks Completed

| Task | Name | Commit | Files |
|------|------|--------|-------|
| 1 | LocaleController + locale.switch route | 8449908 | app/Http/Controllers/Web/LocaleController.php, routes/web.php |
| 2 | User KYC accessor refactor + media collections | 0e3f67a | app/Models/User.php |

## Deviations from Plan

### Auto-fixed Issues

**1. [Rule 1 - Bug] Used attributes[] instead of getRawOriginal() in isKycVerified()**
- **Found during:** Task 2 verification (tinker)
- **Issue:** Plan specified `$this->getRawOriginal('kyc_status') === 'verified'`, but `getRawOriginal()` returns `null` on unsaved/factory models because the "original" array is only populated after a DB read or `syncOriginal()`. Tinker test showed `verified->isKycVerified: false`.
- **Fix:** Changed to `($this->attributes['kyc_status'] ?? null) === 'verified'` — reads the raw stored attribute value, bypassing the accessor, works on both saved DB models and unsaved factory instances.
- **Files modified:** app/Models/User.php (isKycVerified method)
- **Commit:** 0e3f67a

## Verification Results

- `php artisan route:list --name=locale.switch` — route registered: `POST locale/{locale} ... locale.switch`
- `php -l LocaleController.php` — no syntax errors
- `php -l User.php` — no syntax errors
- Tinker verification:
  - `verified->isKycVerified: true` ✓
  - `approved->isKycVerified: false` ✓
  - `pending->isKycVerified: false` ✓
  - `null->isKycVerified: false` ✓
- `grep -rn "'approved'" app/` — no remaining references

## Known Stubs

None.

## Threat Flags

None — all T-01-05-xx mitigations applied:
- T-01-05-01: Whitelist `in_array($locale, ['id','en'])` with `abort(404)` implemented
- T-01-05-03: Route parameter bound as PHP string, no filesystem ops
- T-01-05-04: Refactor to attributes[] eliminates verified/approved ambiguity

## Self-Check: PASSED

- app/Http/Controllers/Web/LocaleController.php — FOUND
- routes/web.php — FOUND (locale.switch route present)
- app/Models/User.php — FOUND (all 3 changes applied)
- Commit 8449908 — FOUND
- Commit 0e3f67a — FOUND
