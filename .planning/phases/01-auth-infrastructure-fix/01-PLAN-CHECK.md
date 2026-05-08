# Plan Check Report — Phase 1: Auth Infrastructure Fix

**Verdict: REVISE**
**Checker:** gsd-plan-checker
**Date:** 2026-05-08
**Plans verified:** 6 (01-01 … 01-06)
**Issues:** 2 blocker, 4 warning

---

## Summary

Plan set secara keseluruhan solid — TDD wave structure tepat, requirement coverage lengkap, dependency graph acyclic. Dua blocker memerlukan perbaikan sebelum eksekusi: shared file conflict pada `app/Models/User.php` di wave 1 (Plans 04 dan 05 concurrently modify) dan Open Questions di RESEARCH.md belum di-mark RESOLVED.

---

## Requirement Coverage

| REQ ID | Plans | Covering Tasks | Status |
|--------|-------|----------------|--------|
| REQ-auth-routes | 01-01 (T1), 01-06 (T1) | LoginTest, LogoutTest, AuthRoutesTest | COVERED |
| REQ-auth-register | 01-01 (T1) | RegisterTest | COVERED |
| REQ-auth-login | 01-01 (T1), 01-03 (T1,T2) | LoginTest, BanCheckTest, EnsureNotBanned | COVERED |
| REQ-auth-2fa | 01-01 (T1) | TwoFactorTest | COVERED |
| REQ-auth-rbac | 01-01 (T2), 01-02 (T1,T2) | ProductPolicyTest, SellerPolicyTest, OrderPolicyTest, Policies, Gate registration | COVERED |
| REQ-auth-kyc | 01-01 (T2), 01-04 (T1,T2,T3) | KycSubmissionTest, KycController, KYC routes, KYC view | COVERED |
| REQ-nfr-security | 01-02 (T1,T2), 01-03 (T1,T2), 01-04 (T1) | Policies, EnsureNotBanned, KycSubmissionRequest | COVERED |
| REQ-nfr-localization | 01-01 (T2), 01-05 (T1) | LocaleSwitchTest, LocaleController, locale route | COVERED |

**Result: All 8 REQ IDs covered.**

---

## Blockers

### BLOCKER 1: Shared file conflict — `app/Models/User.php` dimodifikasi oleh dua plan paralel di Wave 1

```yaml
issue:
  dimension: dependency_correctness
  severity: blocker
  plans: ["01-04", "01-05"]
  description: >
    Plans 01-04 (Task 1) dan 01-05 (Task 2) keduanya memodifikasi app/Models/User.php
    di Wave 1 (depends_on: []). Plan 04 menambahkan media collections ke
    registerMediaCollections(). Plan 05 merefactor getKycStatusAttribute() dan
    isKycVerified(). Eksekusi paralel menyebabkan race condition / merge conflict.
    Catatan: app/Models/User.php tidak ada di files_modified Plan 04's frontmatter
    padahal task body-nya memang memodifikasi file tersebut — inkonsistensi tambahan.
  fix_hint: >
    Opsi A (recommended): Pindahkan modifikasi User.php dari Plan 04 ke Plan 05.
    Plan 05 sudah owns User.php — tambahkan task registerMediaCollections() di sana.
    Plan 04 fokus hanya pada Controller, Request, Route, View.
    Opsi B: Buat Plan 04 depends_on Plan 05 (atau sebaliknya), tapi ini mengurangi
    paralelisme. Opsi A lebih clean.
    Juga: tambahkan app/Models/User.php ke files_modified Plan 05 frontmatter.
```

### BLOCKER 2: RESEARCH.md Open Questions belum di-mark RESOLVED

```yaml
issue:
  dimension: research_resolution
  severity: blocker
  file: "01-RESEARCH.md"
  description: >
    RESEARCH.md memiliki section "## Open Questions" (line 741) tanpa suffix "(RESOLVED)".
    Tiga pertanyaan terdaftar di sana:
    1. User.locale kolom — apakah ada di users table?
    2. Order model seller_id — Policy harus cek via items?
    3. KYC status 'verified' vs 'approved' — nilai mana yang canonical?
    Plans secara implisit menjawab semua pertanyaan (Plan 05 resolve #3, Plan 02 resolve #2,
    Plan 05 juga menyebutkan locale workaround). Namun heading tidak di-mark sesuai
    konvensi Dimension 11.
  fix_hint: >
    Edit 01-RESEARCH.md: ubah "## Open Questions" menjadi "## Open Questions (RESOLVED)"
    dan tambahkan resolusi inline untuk setiap pertanyaan:
    1. RESOLVED: Kolom locale tidak ada — SetLocale fallback ke session sudah cukup untuk Phase 1.
    2. RESOLVED: OrderPolicy cek via order->items()->where('seller_id') — diimplementasikan di Plan 02.
    3. RESOLVED: Canonical value adalah 'verified' (DB). Accessor remapping dihapus di Plan 05.
```

---

## Warnings

### WARNING 1: PATTERNS.md mendefinisikan file name berbeda (CheckBanned vs EnsureNotBanned)

```yaml
issue:
  dimension: pattern_compliance
  severity: warning
  plan: "01-03"
  description: >
    PATTERNS.md File Classification table mendefinisikan file middleware sebagai
    "app/Http/Middleware/CheckBanned.php" (role-match dengan EnsureKycVerified).
    Plan 03 membuat "app/Http/Middleware/EnsureNotBanned.php" — naming convention
    berbeda. EnsureNotBanned lebih konsisten dengan proyek (EnsureKycVerified,
    EnsureSeller, EnsureAdmin semua prefix "Ensure"). Naming Plan lebih baik,
    tapi PATTERNS.md tidak di-update, sehingga checklist pattern compliance
    tidak match.
  fix_hint: >
    Update PATTERNS.md File Classification table: ganti "CheckBanned.php" menjadi
    "EnsureNotBanned.php". Tidak perlu mengubah Plan 03 — naming-nya sudah benar.
```

### WARNING 2: FormRequest deviation dari PATTERNS.md anti-pattern #4

```yaml
issue:
  dimension: pattern_compliance
  severity: warning
  plan: "01-04"
  task: 1
  description: >
    PATTERNS.md anti-pattern #4 eksplisit: "Do not create FormRequest classes —
    Seller controllers use inline $request->validate([...]), no FormRequest pattern exists".
    Plan 04 Task 1 membuat KycSubmissionRequest (FormRequest class) dan mengakuinya
    sebagai "deviation yang justified". Deviasi justified secara teknis (validasi kompleks
    + file upload), tapi executor harus tahu ini merupakan penyimpangan dari pola proyek
    yang ada — dan perlu documented agar konsisten di masa depan.
  fix_hint: >
    Deviation ini acceptable untuk KYC complexity. Tambahkan catatan di PATTERNS.md
    bahwa FormRequest diizinkan untuk form dengan file upload kompleks — atau simpan
    sebagai documented exception di Plan 04 (sudah ada di action text, cukup).
    Tidak perlu blok eksekusi.
```

### WARNING 3: REQ-auth-rbac middleware aspect tidak punya dedicated test

```yaml
issue:
  dimension: requirement_coverage
  severity: warning
  plan: "01-01"
  description: >
    RESEARCH.md Validation Architecture (line 621) mendefinisikan MiddlewareTest.php
    sebagai Wave 0 requirement untuk REQ-auth-rbac: "Seller middleware blocks non-seller".
    Plan 01-01 membuat BanCheckTest (covers EnsureNotBanned) tapi tidak ada test untuk
    EnsureSeller, EnsureAdmin, atau EnsureBuyer middleware behavior. RBAC via policies
    ter-cover (ProductPolicyTest, SellerPolicyTest), tapi middleware gates sendiri
    tidak di-test secara eksplisit.
  fix_hint: >
    Opsi A (recommended): Tambahkan test cases ke BanCheckTest.php atau buat
    MiddlewareTest.php baru yang verifikasi:
    - non-seller get 403 saat akses /seller/* route
    - non-admin get 403 saat akses /admin/* route
    Opsi B: Accept gap ini — middleware tests akan covered di phase berikutnya
    saat rute seller/admin dibangun lebih lengkap.
```

### WARNING 4: Plan 04 frontmatter `files_modified` tidak deklarasikan `app/Models/User.php`

```yaml
issue:
  dimension: task_completeness
  severity: warning
  plan: "01-04"
  task: 1
  description: >
    Task 1 Plan 04 memodifikasi app/Models/User.php (tambah kyc_id_photo dan kyc_selfie
    ke registerMediaCollections()), tapi app/Models/User.php tidak ada di frontmatter
    files_modified Plan 04. Ini inkonsistensi yang menyebabkan tooling tidak bisa
    detect shared file conflict secara otomatis, dan ini akar masalah Blocker 1.
  fix_hint: >
    Setelah Blocker 1 diperbaiki (modifikasi User.php dipindah ke Plan 05),
    warning ini hilang dengan sendirinya. Jika tetap di Plan 04, tambahkan
    app/Models/User.php ke files_modified.
```

---

## Dimension-by-Dimension Results

| Dimension | Status | Notes |
|-----------|--------|-------|
| 1. Requirement Coverage | PASS | Semua 8 REQ ID covered |
| 2. Task Completeness | PASS | Semua task punya files/action/verify/done; verify commands automated |
| 3. Dependency Correctness | FAIL (Blocker 1) | Wave 1 parallel User.php conflict |
| 4. Key Links Planned | PASS | Wiring antar artifact terdefinisi di key_links |
| 5. Scope Sanity | PASS | Max 3 task/plan, file count dalam threshold |
| 6. Verification Derivation | PASS | must_haves truths user-observable, artifacts mapped |
| 7. Context Compliance | N/A | Tidak ada CONTEXT.md |
| 8. Nyquist Compliance | PASS | Setiap task punya automated verify command |
| 9. Cross-Plan Data Contracts | PASS | Tidak ada conflicting transforms |
| 10. CLAUDE.md Compliance | N/A | Tidak ada CLAUDE.md di working directory |
| 11. Research Resolution | FAIL (Blocker 2) | Open Questions belum RESOLVED |
| 12. Pattern Compliance | WARNING | CheckBanned vs EnsureNotBanned name mismatch; FormRequest deviation |

---

## Spec Compliance: KYC Status Accessor Refactor

**Pertanyaan:** Apakah menghapus `'verified'→'approved'` remapping di Plan 05 breaking untuk consumer?

**Hasil analisis codebase:**

| Consumer | Current behavior | After Plan 05 | Breaking? |
|----------|-----------------|---------------|-----------|
| `EnsureKycVerified.php:21` | `match ($user->kyc_status)` — via accessor | `kyc_status` returns `'verified'` raw. Match sudah handle `'pending'`, `'rejected'`, `default`. User verified sudah lolos `isKycVerified()` check sebelum match dieksekusi. | **TIDAK BREAKING** |
| `AdminUserController.php:64` | Validasi `in:pending,verified,rejected` | Tidak berubah — tidak ada `'approved'` di whitelist validasi | **TIDAK BREAKING** |
| `AdminUserController.php:104` | `$user->update(['kyc_status' => 'verified'])` | Set ke `'verified'` — sudah canonical | **TIDAK BREAKING** |
| `UserController.php:49` | `$user->update(['kyc_status' => 'verified'])` | Set ke `'verified'` — sudah canonical | **TIDAK BREAKING** |
| `User::isKycVerified()` | Checks `['verified', 'approved']` | Plan 05 mengubah ke `getRawOriginal('kyc_status') === 'verified'` | **TIDAK BREAKING** (lebih strict, lebih correct) |

**Kesimpulan:** Refactor aman. Tidak ada consumer yang bergantung pada nilai `'approved'`.

---

## Wave Structure Assessment

```
Wave 1 (parallel — setelah fix Blocker 1):
  01-01: Test scaffold (10 test files) — write-only ke tests/
  01-02: Policies + AppServiceProvider — write ke app/Policies/ + app/Providers/
  01-03: EnsureNotBanned + bootstrap/app.php
  01-04: KycController + Request + routes/seller.php + view  [User.php REMOVED setelah fix]
  01-05: LocaleController + routes/web.php + User.php (semua User.php edits di sini)

Wave 2:
  01-06: AuthRoutesTest + full test suite + human checkpoint
        depends_on: 01-01, 01-02, 01-03, 01-04, 01-05 ✓
```

**Setelah fix Blocker 1:** Tidak ada shared file antara plan paralel wave 1.

---

## Fix Checklist (untuk planner)

- [ ] **BLOCKER 1**: Pindahkan `User::registerMediaCollections()` edit dari Plan 04 Task 1 ke Plan 05.
  - Edit Plan 04: hapus `app/Models/User.php` dari task 1 files + action
  - Edit Plan 05: tambahkan task atau merge ke Task 2 (`app/Models/User.php`), tambahkan ke frontmatter `files_modified`
- [ ] **BLOCKER 2**: Edit `01-RESEARCH.md`: ubah `## Open Questions` → `## Open Questions (RESOLVED)`, tambahkan resolusi inline
- [ ] **WARNING 1**: Update PATTERNS.md: `CheckBanned.php` → `EnsureNotBanned.php`
- [ ] **WARNING 3**: Pertimbangkan tambah MiddlewareTest atau document sebagai accepted gap

---

## Recommendation

**REVISE** — 2 blocker harus diperbaiki sebelum eksekusi. Fix Blocker 1 (5 menit edit Plan 04 + Plan 05) dan Blocker 2 (2 menit edit RESEARCH.md). Setelah fix, plans siap dieksekusi.

---

## Re-verification (Pass 2)

**Date:** 2026-05-08
**Verdict: PASS**

### Fix Verification

**BLOCKER 1 — Wave 1 race condition pada app/Models/User.php: RESOLVED**

Verified di 01-04-PLAN.md:
- `files_modified` frontmatter: `app/Models/User.php` tidak ada (hanya 4 file: KycController, KycSubmissionRequest, routes/seller.php, verify.blade.php)
- Task 1 `<files>`: hanya `app/Http/Requests/Seller/KycSubmissionRequest.php` dan `app/Http/Controllers/Seller/KycController.php`
- Action Task 1 memiliki catatan eksplisit: "CATATAN — User.php media collections: Plan 05 yang menambahkan `kyc_id_photo` dan `kyc_selfie` ke `User::registerMediaCollections()`. Plan 04 mengasumsikan kedua collections sudah terdaftar saat KycController::store() dijalankan."
- Cross-reference note juga ada di `<behavior>`: "DEPENDENCY: User::registerMediaCollections() penambahan kyc_id_photo + kyc_selfie collections diowned oleh Plan 05"

Verified di 01-05-PLAN.md:
- `files_modified` frontmatter: `app/Models/User.php` ada
- Task 2 action berisi Perubahan 3 yang eksplisit: `registerMediaCollections()` ditambahkan `addMediaCollection('kyc_id_photo')->singleFile()` dan `addMediaCollection('kyc_selfie')->singleFile()`
- `success_criteria` mencantumkan: "User::registerMediaCollections() berisi addMediaCollection('kyc_id_photo')->singleFile() dan addMediaCollection('kyc_selfie')->singleFile()"
- `must_haves.truths` mencantumkan: "User::registerMediaCollections() berisi kyc_id_photo dan kyc_selfie collections"

Wave 1 parallel safety — file ownership setelah fix:

| Plan | Wave | Files (User.php?) |
|------|------|-------------------|
| 01-01 | 1 | EnsureNotBanned.php, AppServiceProvider.php — tidak ada User.php |
| 01-02 | 1 | app/Policies/* — tidak ada User.php |
| 01-03 | 1 | test files — tidak ada User.php |
| 01-04 | 1 | KycController, KycSubmissionRequest, seller.php, verify.blade.php — tidak ada User.php |
| 01-05 | 1 | LocaleController, web.php, **User.php** — User.php dimiliki solo oleh Plan 05 |

Tidak ada konflik. Plan 05 adalah satu-satunya plan wave 1 yang menyentuh User.php.

**BLOCKER 2 — Open Questions tidak di-mark RESOLVED: RESOLVED**

Verified di 01-RESEARCH.md line 741:
- Heading: `## Open Questions (RESOLVED)` — suffix RESOLVED ada
- Pertanyaan 1 (User.locale kolom): heading "**User.locale kolom — RESOLVED**" + paragraf resolusi lengkap
- Pertanyaan 2 (Order model seller_id): heading "**Order model seller_id — RESOLVED**" + paragraf resolusi lengkap
- Pertanyaan 3 (KYC status verified vs approved): heading "**KYC status `verified` vs `approved` — RESOLVED**" + paragraf resolusi lengkap

### Remaining Warnings (dari Pass 1 — tidak diubah)

WARNING 1 (PATTERNS.md filename mismatch `CheckBanned.php` vs `EnsureNotBanned.php`) dan WARNING 3 (tidak ada MiddlewareTest) tidak menghambat eksekusi. Tetap sebagai warning yang belum di-address, namun tidak memerlukan revisi sebelum eksekusi.

### Verdict

**PASS** — kedua blocker terperbaiki dengan benar. Plans siap dieksekusi.

Jalankan: `/gsd-execute-phase 01`
