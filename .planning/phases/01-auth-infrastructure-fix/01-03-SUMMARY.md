---
phase: 01-auth-infrastructure-fix
plan: "03"
subsystem: auth-middleware
tags: [middleware, ban, session-invalidation, security]
dependency_graph:
  requires: []
  provides: [EnsureNotBanned middleware, ban.check alias]
  affects: [bootstrap/app.php, web middleware stack]
tech_stack:
  added: []
  patterns: [dual-response JSON/redirect, session invalidation trinity, null-safe user check]
key_files:
  created:
    - app/Http/Middleware/EnsureNotBanned.php
  modified:
    - bootstrap/app.php
decisions:
  - EnsureNotBanned appended AFTER TrackLastActivity (intentional — locale/activity tracking not needed for ban redirect)
  - Flash key 'error' (not 'warning') — ban is hard block
  - JSON response key 'message' — consistent with all other middleware
  - Null user passes through — middleware does not block guests
metrics:
  duration: ~5 min
  completed: 2026-05-08
  tasks_completed: 2
  files_created: 1
  files_modified: 1
---

# Phase 1 Plan 03: EnsureNotBanned Middleware Summary

EnsureNotBanned middleware bans enforcement via session invalidation trinity (logout + invalidate + regenerateToken) dengan dual JSON/redirect response, didaftarkan ke web middleware group global.

## Tasks Completed

| Task | Name | Commit | Files |
|------|------|--------|-------|
| 1 | Buat EnsureNotBanned middleware | 4e2fb32 | app/Http/Middleware/EnsureNotBanned.php |
| 2 | Daftarkan di bootstrap/app.php | c12de38 | bootstrap/app.php |

## What Was Built

**EnsureNotBanned.php** — middleware yang:
- Null-check dulu: tamu (unauthenticated) pass through tanpa diblokir
- Banned user: `Auth::logout()` + `session()->invalidate()` + `session()->regenerateToken()` (ASVS V4 session invalidation)
- API/JSON → 403 `{'message': '...'}` 
- Web → `redirect()->route('login')->with('error', '...')`

**bootstrap/app.php** — tiga perubahan:
1. Import `use App\Http\Middleware\EnsureNotBanned;`
2. `EnsureNotBanned::class` di `web(append:)` — berjalan pada semua web request
3. `'ban.check' => EnsureNotBanned::class` di alias array

## Deviations from Plan

None — plan executed exactly as written.

## Threat Model Coverage

| Threat ID | Status |
|-----------|--------|
| T-01-03-01 Auth bypass via session persistence | MITIGATED — session invalidation trinity |
| T-01-03-02 DoS via banned check | ACCEPTED — boolean check, no N+1 |
| T-01-03-03 Information disclosure via ban reason | ACCEPTED — generic message, ban_reason hidden |
| T-01-03-04 API banned check elevation | MITIGATED — `is('api/*')` check enforces 403 JSON |

## Known Stubs

None.

## Threat Flags

None — no new network endpoints, auth paths, or schema changes introduced.

## Self-Check: PASSED

- app/Http/Middleware/EnsureNotBanned.php: FOUND
- bootstrap/app.php contains EnsureNotBanned (3 occurrences): FOUND
- Commit 4e2fb32: FOUND
- Commit c12de38: FOUND
- php artisan config:clear: PASSED (no exception)
