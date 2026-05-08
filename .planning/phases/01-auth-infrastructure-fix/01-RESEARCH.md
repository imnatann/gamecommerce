# Phase 1: Auth Infrastructure Fix - Research

**Researched:** 2026-05-08
**Domain:** Laravel 12 + Fortify 1.37 auth routing, Spatie permission policies, KYC seller flow, ban middleware, locale switching
**Confidence:** HIGH

---

## Summary

Fase ini memperbaiki infrastruktur auth yang sudah hampir lengkap tapi belum terhubung. Fortify sudah terpasang dan service provider-nya terdaftar di `bootstrap/providers.php` — namun masalah kritis terdeteksi: **Fortify mendaftarkan routes dengan prefix `auth/` (dari `config/fortify.php`), sehingga named routes seperti `login` merujuk ke `/auth/login`, bukan `/login`**. Ini adalah sumber utama semua 404 di view Blade yang menggunakan `route('login')`.

Middleware gate (EnsureBuyer/Seller/Admin/KycVerified) sudah ada dan terdaftar di `bootstrap/app.php`. Yang hilang: (1) `app/Policies/` direktori + 3 policy class, (2) route `seller.kyc.verify` belum ada di `routes/seller.php`, (3) ban-check middleware belum ada, (4) locale switcher endpoint belum ada.

**Primary recommendation:** Jangan override Fortify routes secara manual. Biarkan Fortify auto-register dengan `$registersRoutes = true` (default). Yang perlu dilakukan: verifikasi prefix `auth` sesuai ekspektasi view, tambahkan routes custom yang Fortify tidak sediakan (KYC, locale switch, ban), dan buat 3 policy class.

---

<phase_requirements>
## Phase Requirements

| ID | Description | Research Support |
|----|-------------|------------------|
| REQ-auth-routes | Named routes login, register, logout, dll wired | Fortify auto-registers semua dengan prefix `auth/` — verified di vendor source |
| REQ-auth-register | Register flow bekerja | Fortify RegisteredUserController + view sudah ada, route otomatis |
| REQ-auth-login | Login flow bekerja | Fortify AuthenticatedSessionController + LoginRequest sudah ada |
| REQ-auth-2fa | 2FA Google2FA berfungsi | Fortify TwoFactorAuthenticatedSessionController + view sudah ada |
| REQ-auth-rbac | Spatie roles + Policies | Middleware ada, 3 policy class perlu dibuat + didaftarkan di AppServiceProvider |
| REQ-auth-kyc | KYC seller route | route `seller.kyc.verify` belum di seller.php — perlu SellerKycController + route |
| REQ-nfr-security | CSRF, rate limiting, hashing, session | Sudah dikonfigurasi di Fortify + Laravel defaults; perlu verifikasi config |
| REQ-nfr-localization | Bahasa Indonesia primary | SetLocale middleware sudah ada; perlu locale switcher endpoint |
</phase_requirements>

---

## Architectural Responsibility Map

| Capability | Primary Tier | Secondary Tier | Rationale |
|------------|-------------|----------------|-----------|
| Auth route registration | Backend (Fortify ServiceProvider) | — | Fortify auto-registers via `configureRoutes()` saat boot |
| Login/register/logout handling | Backend (Fortify Controllers) | — | Fortify controllers handle semua auth actions |
| 2FA challenge | Backend (Fortify) | — | TwoFactorAuthenticatedSessionController |
| Role enforcement | Backend Middleware | — | EnsureBuyer/Seller/Admin/KycVerified di middleware stack |
| Policy authorization | Backend (Gate/Policy) | — | AppServiceProvider mendaftarkan policy, Gate::check di controller |
| KYC verification form | Backend (Web Controller) | Blade view | SellerKycController + form Blade |
| Ban enforcement | Backend Middleware | — | EnsureBanned middleware di web stack |
| Locale switching | Backend (Web route) | Session | Endpoint POST /locale → session('locale') |

---

## Standard Stack

### Core (Sudah Terpasang)
| Library | Version | Purpose | Status |
|---------|---------|---------|--------|
| laravel/fortify | ^1.37 (actual di composer.json: ^1.20, installed 1.37) | Auth routing, controllers | Terpasang, auto-registers |
| spatie/laravel-permission | ^6.0 | RBAC roles/permissions | Terpasang, User model sudah HasRoles |
| laravel/sanctum | (terpasang) | API session statefulness | Terpasang di bootstrap/app.php |

### Fortify Route Registration - VERIFIED [VERIFIED: vendor/laravel/fortify/src/FortifyServiceProvider.php:217-228]

Fortify mendaftarkan routes **secara otomatis** saat service provider boot:

```php
// vendor/laravel/fortify/src/FortifyServiceProvider.php:217-228
protected function configureRoutes()
{
    if (Fortify::$registersRoutes) {  // default: true (Fortify.php:49)
        Route::group([
            'namespace' => 'Laravel\Fortify\Http\Controllers',
            'domain' => config('fortify.domain', null),
            'prefix' => config('fortify.prefix'),  // 'auth' dari config/fortify.php:18
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/routes.php');
        });
    }
}
```

**Kesimpulan:** Fortify SUDAH mendaftarkan routes secara otomatis karena `FortifyServiceProvider` terdaftar di `bootstrap/providers.php:11`. Tidak perlu `Fortify::routes()` manual.

---

## Fortify Default Routes — Tabel Lengkap

[VERIFIED: vendor/laravel/fortify/routes/routes.php]

Semua routes berada di bawah prefix `auth/` (dari `config/fortify.php` line 18: `'prefix' => 'auth'`).

| Named Route | HTTP Verb | URL (dengan prefix) | Guard | Notes |
|-------------|-----------|---------------------|-------|-------|
| `login` | GET | `/auth/login` | guest | View: auth.login |
| `login.store` | POST | `/auth/login` | guest | **TIDAK** bernama `login` untuk POST |
| `logout` | POST | `/auth/logout` | auth | Blade harus `method="POST"` |
| `register` | GET | `/auth/register` | guest | View: auth.register |
| `register.store` | POST | `/auth/register` | guest | |
| `password.request` | GET | `/auth/forgot-password` | guest | View: auth.forgot-password |
| `password.reset` | GET | `/auth/reset-password/{token}` | guest | |
| `password.email` | POST | `/auth/forgot-password` | guest | |
| `password.update` | POST | `/auth/reset-password` | guest | |
| `verification.notice` | GET | `/auth/email/verify` | auth | View: auth.verify-email |
| `verification.verify` | GET | `/auth/email/verify/{id}/{hash}` | auth+signed | |
| `verification.send` | POST | `/auth/email/verification-notification` | auth | |
| `two-factor.login` | GET | `/auth/two-factor-challenge` | guest | View: auth.two-factor |
| `two-factor.login.store` | POST | `/auth/two-factor-challenge` | guest | |
| `two-factor.enable` | POST | `/auth/user/two-factor-authentication` | auth+password.confirm | |
| `two-factor.disable` | DELETE | `/auth/user/two-factor-authentication` | auth | |
| `two-factor.confirm` | POST | `/auth/user/confirmed-two-factor-authentication` | auth | |
| `two-factor.qr-code` | GET | `/auth/user/two-factor-qr-code` | auth | |
| `two-factor.recovery-codes` | GET/POST | `/auth/user/two-factor-recovery-codes` | auth | |
| `password.confirm` | GET | `/auth/user/confirm-password` | auth | View: auth.confirm-password |
| `user-profile-information.update` | PUT | `/auth/user/profile-information` | auth | |
| `user-password.update` | PUT | `/auth/user/password` | auth | |

**CRITICAL: `logout` adalah POST, bukan GET.** Blade view harus menggunakan:
```html
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Logout</button>
</form>
```

**CRITICAL: `login` (named route) hanya terdaftar untuk GET** (view). POST login menggunakan `login.store`. Middleware `auth` yang mencari `route('login')` untuk redirect bekerja karena Laravel menggunakan named route `login` untuk unauthenticated redirect.

---

## Architecture Patterns

### System Architecture Diagram

```
Browser Request
     │
     ▼
[web middleware group]
     │ SetLocale (session locale detection)
     │ TrackLastActivity
     │ VerifyCsrfToken (built-in)
     │
     ├──► /auth/* ──► [Fortify Controllers] ──► User model + Spatie Roles
     │         (auto-registered, prefix='auth')
     │
     ├──► /seller/* ──► [auth+seller middleware] ──► [kyc middleware optional]
     │         ──► SellerKycController (NEW: seller.kyc.verify)
     │
     ├──► /admin/* ──► [auth+admin middleware] ──► Admin Controllers
     │
     └──► /* (public) ──► Public Controllers
     
[Policy Layer]
     Gate::check('update', $product)
          │
          ├──► ProductPolicy::update() ── isSeller() && $product->seller_id === $user->id
          ├──► OrderPolicy::view() ────── buyer_id or seller_id match
          └──► SellerPolicy::manage() ─── isSeller() && isKycVerified()

[Ban Check — baru]
EnsureBanned middleware ──► $user->isBanned() ──► logout + redirect
```

### Recommended Project Structure (Tambahan Phase 1)

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Seller/
│   │       └── SellerKycController.php    # BARU
│   └── Middleware/
│       └── EnsureNotBanned.php            # BARU
├── Policies/                              # BARU (direktori)
│   ├── ProductPolicy.php
│   ├── OrderPolicy.php
│   └── SellerPolicy.php
resources/
└── views/
    └── seller/
        └── kyc/
            └── verify.blade.php           # BARU (form KYC)
routes/
├── web.php                                # Tambah: locale switcher
└── seller.php                             # Tambah: seller.kyc.verify
```

---

## Don't Hand-Roll

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| Auth routing | Custom auth controller routing | Fortify auto-registration | Fortify sudah handle semua edge cases (rate limiting, guards, signed URLs) |
| Password hashing | Manual bcrypt | Laravel's `password` cast pada User model (sudah ada) | User.php:68 sudah `'password' => 'hashed'` |
| CSRF | Manual token validation | Laravel's built-in `VerifyCsrfToken` | Sudah aktif di web middleware |
| Role checks | Custom role logic | Spatie `hasRole()` / User model methods yang sudah ada | `isSeller()`, `isAdmin()`, `isBuyer()` sudah di User.php |
| 2FA secret generation | Manual TOTP | Fortify's `TwoFactorAuthenticationProvider` | Sudah terkonfigurasi via Fortify features |

**Key insight:** Hampir semua auth infrastructure sudah ada — ini adalah pekerjaan wiring, bukan building.

---

## Policies — Pola Rekomendasi

[VERIFIED: vendor/spatie/laravel-permission, ASSUMED: method-level authorization rules]

Policies didaftarkan di `AppServiceProvider` (Laravel 12 tidak lagi memerlukan AuthServiceProvider terpisah):

```php
// app/Providers/AppServiceProvider.php — boot()
use Illuminate\Support\Facades\Gate;
use App\Models\Product;
use App\Models\Order;
use App\Policies\ProductPolicy;
use App\Policies\OrderPolicy;
use App\Policies\SellerPolicy;

Gate::policy(Product::class, ProductPolicy::class);
Gate::policy(Order::class, OrderPolicy::class);
// SellerPolicy tidak terikat ke model — daftarkan via Gate::define
Gate::define('manage-seller-panel', [SellerPolicy::class, 'manage']);
```

### ProductPolicy
```php
// app/Policies/ProductPolicy.php
class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // semua user bisa lihat list produk
    }

    public function view(User $user, Product $product): bool
    {
        return true; // publik
    }

    public function create(User $user): bool
    {
        return $user->isSeller() && $user->isKycVerified();
    }

    public function update(User $user, Product $product): bool
    {
        return $user->isSeller()
            && $user->isKycVerified()
            && $user->id === $product->seller_id;
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->isSeller()
            && $user->id === $product->seller_id;
    }

    public function toggleActive(User $user, Product $product): bool
    {
        return $this->update($user, $product);
    }
}
```

### OrderPolicy
```php
// app/Policies/OrderPolicy.php
class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        return $user->id === $order->buyer_id
            || $order->items()->where('seller_id', $user->id)->exists()
            || $user->isAdmin();
    }

    public function updateStatus(User $user, Order $order): bool
    {
        // Seller boleh update status item mereka
        return $order->items()->where('seller_id', $user->id)->exists()
            || $user->isAdmin();
    }

    public function dispute(User $user, Order $order): bool
    {
        return $user->id === $order->buyer_id
            && in_array($order->status->value, ['delivered', 'processing']);
    }
}
```

### SellerPolicy
```php
// app/Policies/SellerPolicy.php
class SellerPolicy
{
    public function manage(User $user): bool
    {
        return $user->isSeller(); // KYC check dilakukan oleh EnsureKycVerified middleware
    }

    public function accessPanel(User $user): bool
    {
        return $user->isSeller();
    }

    public function submitKyc(User $user): bool
    {
        return $user->isSeller()
            && ! $user->isKycVerified()
            && $user->kyc_status !== 'pending';
    }
}
```

---

## KYC Seller Route + Controller Pattern

[ASSUMED: form fields KYC — standar Indonesia marketplace]

### Route (tambah ke routes/seller.php)

```php
// routes/seller.php — tambahkan SEBELUM route lain, tanpa 'kyc' middleware
Route::get('/kyc', [\App\Http\Controllers\Seller\SellerKycController::class, 'show'])
    ->name('kyc.verify');

Route::post('/kyc', [\App\Http\Controllers\Seller\SellerKycController::class, 'submit'])
    ->name('kyc.submit');
```

**PERHATIAN:** Route `seller.kyc.verify` harus accessible oleh seller yang BELUM kyc-verified. Jangan masukkan ke dalam middleware group `kyc`. Di `bootstrap/app.php` seller route group menggunakan `['auth', 'seller']` saja — tidak ada `kyc` di sini, jadi ini sudah benar.

`EnsureKycVerified.php:31-33` menggunakan `Route::has('seller.kyc.verify')` — route name ini HARUS persis `seller.kyc.verify` (prefix `seller.` dari bootstrap/app.php + nama `kyc.verify` dari route definition).

### SellerKycController Stub

```php
// app/Http/Controllers/Seller/SellerKycController.php
namespace App\Http\Controllers\Seller;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SellerKycController extends Controller
{
    public function show(Request $request)
    {
        return view('seller.kyc.verify', [
            'user' => $request->user(),
        ]);
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'full_name'     => 'required|string|max:255',
            'id_number'     => 'required|string|size:16',    // NIK KTP 16 digit
            'id_photo'      => 'required|image|max:5120|mimes:jpg,jpeg,png',
            'selfie_photo'  => 'required|image|max:5120|mimes:jpg,jpeg,png',
            'bank_name'     => 'required|string|max:100',
            'bank_account'  => 'required|string|max:50',
            'bank_holder'   => 'required|string|max:255',
            // NPWP opsional untuk MVP
            'npwp_number'   => 'nullable|string|max:20',
        ]);

        $user = $request->user();

        // Upload via spatie/medialibrary (sudah ada di User model)
        $user->addMediaFromRequest('id_photo')
            ->toMediaCollection('kyc_id_photo');

        $user->addMediaFromRequest('selfie_photo')
            ->toMediaCollection('kyc_selfie');

        $user->update([
            'kyc_status' => 'pending',
            'meta' => array_merge($user->meta ?? [], [
                'kyc_full_name'    => $validated['full_name'],
                'kyc_id_number'    => $validated['id_number'],
                'kyc_bank_name'    => $validated['bank_name'],
                'kyc_bank_account' => $validated['bank_account'],
                'kyc_bank_holder'  => $validated['bank_holder'],
                'kyc_npwp'         => $validated['npwp_number'] ?? null,
            ]),
        ]);

        return redirect()->route('seller.dashboard')
            ->with('status', 'Verifikasi KYC Anda sedang diproses.');
    }
}
```

**KYC Fields (Indonesia marketplace standard):** [ASSUMED — berdasarkan itemku.com/g2g.com pattern]
- NIK KTP (16 digit) — wajib
- Foto KTP — wajib
- Foto selfie dengan KTP — wajib
- Data rekening bank — wajib untuk withdrawal
- NPWP — opsional di MVP, wajib untuk seller dengan omzet >Rp 4.8M/tahun (regulasi DJP)

---

## Ban Check Middleware

[VERIFIED: User.php:204-207 — `isBanned()` method sudah ada]

```php
// app/Http/Middleware/EnsureNotBanned.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotBanned
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isBanned()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Akun Anda telah dibanned. Alasan: ' . ($user->ban_reason ?? 'Pelanggaran kebijakan.'),
                ], 403);
            }

            return redirect()->route('login')
                ->with('error', 'Akun Anda telah dinonaktifkan. Hubungi support jika ini kesalahan.');
        }

        return $next($request);
    }
}
```

**Integrasi:** Daftarkan di `bootstrap/app.php` sebagai web middleware (append), setelah `SetLocale`:

```php
// bootstrap/app.php — dalam withMiddleware()
$middleware->web(append: [
    SetLocale::class,
    TrackLastActivity::class,
    \App\Http\Middleware\EnsureNotBanned::class, // TAMBAH
]);

$middleware->alias([
    // ... existing aliases ...
    'ban.check' => \App\Http\Middleware\EnsureNotBanned::class,
]);
```

**User model sudah memiliki:** `is_banned` (boolean cast, line 65), `banned_at` (datetime, line 66), `ban_reason` (string, line 35), `isBanned()` method (line 204-207).

---

## Locale Switcher Endpoint

[VERIFIED: SetLocale.php — session-based, supportedLocales = ['id', 'en']]

```php
// routes/web.php — tambahkan route ini
Route::post('/locale', function (\Illuminate\Http\Request $request) {
    $locale = $request->input('locale', 'id');
    $supported = ['id', 'en'];

    if (! in_array($locale, $supported)) {
        $locale = 'id';
    }

    session()->put('locale', $locale);
    app()->setLocale($locale);

    return redirect()->back()->withInput();
})->name('locale.switch')->middleware('web');
```

**Atau gunakan controller terpisah jika perlu:**

```php
// Blade usage:
<form method="POST" action="{{ route('locale.switch') }}">
    @csrf
    <input type="hidden" name="locale" value="en">
    <button type="submit">EN</button>
</form>
```

**Pattern SetLocale.php:** Middleware sudah membaca `session('locale')` sebagai prioritas pertama (line 14) — jadi hanya perlu write ke session.

---

## Common Pitfalls

### Pitfall 1: Fortify Route Prefix Mismatch
**What goes wrong:** View Blade memanggil `route('login')` yang resolve ke `/auth/login`, namun developer mengira akan ke `/login`.
**Why it happens:** `config/fortify.php:18` menetapkan `'prefix' => 'auth'`.
**How to avoid:** Blade views sudah di `resources/views/auth/` — pastikan form `action="{{ route('login') }}"` tidak hardcode URL. Semua `route('xxx')` calls akan resolve dengan benar.
**Warning signs:** 404 pada form submission; `php artisan route:list | grep login` tidak menampilkan `/login` tapi `/auth/login`.

### Pitfall 2: Logout Menggunakan GET bukan POST
**What goes wrong:** Blade menampilkan link `<a href="{{ route('logout') }}">` yang menghasilkan GET request — Fortify logout route hanya menerima POST.
**Why it happens:** [VERIFIED: vendor/laravel/fortify/routes/routes.php:48] Logout adalah `Route::post()`.
**How to avoid:** Selalu gunakan `<form method="POST">` dengan `@csrf` untuk logout.
**Warning signs:** 405 Method Not Allowed pada klik logout.

### Pitfall 3: KYC Route Berada di Dalam kyc Middleware Group
**What goes wrong:** Seller yang belum KYC tidak bisa mengakses halaman KYC verify karena middleware `kyc` memblok mereka.
**Why it happens:** Route `seller.kyc.verify` dimasukkan ke dalam grup yang menggunakan `'kyc'` middleware.
**How to avoid:** Route KYC harus di luar middleware `kyc`. Di `bootstrap/app.php` seller group hanya pakai `['auth', 'seller']` — route KYC sudah aman jika ditambah ke `seller.php` tanpa middleware tambahan.
**Warning signs:** Redirect loop: `EnsureKycVerified` redirect ke `seller.kyc.verify`, tapi `seller.kyc.verify` juga requires `kyc`.

### Pitfall 4: Policy Tidak Terdaftar
**What goes wrong:** `$this->authorize('update', $product)` di controller lempar `AuthorizationException` bahkan untuk admin.
**Why it happens:** Policy tidak didaftarkan di `AppServiceProvider::boot()`.
**How to avoid:** Tambahkan `Gate::policy()` calls di `AppServiceProvider::boot()`. Atau gunakan `Gate::before()` untuk super admin bypass.
**Warning signs:** `403 This action is unauthorized` untuk semua users termasuk admin.

### Pitfall 5: Spatie HasRoles dan `role` Attribute Collision
**What goes wrong:** `$user->role` mengembalikan nilai tidak terduga karena ada dua sumber: kolom database dan Spatie roles.
**Why it happens:** User model memiliki `getRoleAttribute()` (line 77-82) yang menggabungkan keduanya, tapi `$appends = ['role']` bisa konflik dengan Spatie trait.
**How to avoid:** Gunakan `$user->hasPlatformRole()` (User.php:175-190) atau metode `isSeller()`/`isAdmin()`/`isBuyer()` daripada akses langsung ke `$user->role` string.
**Warning signs:** `$user->role` mengembalikan null meski user punya Spatie role.

### Pitfall 6: `login.store` vs `login` Route Name
**What goes wrong:** Form POST login menggunakan `route('login')` — ini mengarah ke GET (view), bukan POST handler.
**Why it happens:** [VERIFIED: routes.php:42-46] Fortify mendaftarkan POST login dengan nama `login.store`, bukan `login`.
**How to avoid:** Form action untuk login POST harus `route('login')` — Fortify menangani ini correctly karena POST ke `/auth/login` akan diarahkan ke `login.store` handler. Di Blade, `action="{{ route('login') }}"` dengan `method="POST"` akan bekerja karena URL yang sama, berbeda nama route.
**Warning signs:** Verifikasi dengan `php artisan route:list`.

---

## Breaking Changes di File Existing

### 1. `routes/web.php` — Tambahan Wajib

```php
// TAMBAHKAN di routes/web.php:
// Locale switcher (sebelum Route::fallback)
Route::post('/locale', ...)->name('locale.switch');
```

Tidak ada perubahan breaking — hanya append.

### 2. `routes/seller.php` — Tambahan Wajib

```php
// TAMBAHKAN di routes/seller.php (tanpa 'kyc' middleware tambahan):
Route::get('/kyc', [SellerKycController::class, 'show'])->name('kyc.verify');
Route::post('/kyc', [SellerKycController::class, 'submit'])->name('kyc.submit');
```

### 3. `app/Providers/AppServiceProvider.php` — Tambahan Policy Registration

```php
// boot() method — tambahkan Gate::policy registrations
```

### 4. `bootstrap/app.php` — Tambahan Ban Middleware

```php
// withMiddleware — tambahkan EnsureNotBanned ke web stack dan alias
```

### 5. `app/Providers/FortifyServiceProvider.php` — TIDAK PERLU DIUBAH

Provider sudah lengkap. Semua view sudah didaftarkan (login, register, forgot-password, reset-password, verify-email, confirm-password, two-factor-challenge di line 29-39). Rate limiters sudah dikonfigurasi (line 41-49).

---

## Runtime State Inventory

Fase ini adalah wiring/greenfield (menambah kode baru), bukan rename/refactor. Tidak ada stored data yang perlu dimigrasikan.

| Category | Items Found | Action Required |
|----------|-------------|-----------------|
| Stored data | Tidak ada — user KYC columns sudah di migration (kyc_status, kyc_verified_at, is_banned, ban_reason) | Tidak ada migrasi baru diperlukan |
| Live service config | Tidak ada external service config terdampak | — |
| OS-registered state | Tidak ada | — |
| Secrets/env vars | Tidak ada rename | — |
| Build artifacts | Tidak ada | — |

---

## Environment Availability

| Dependency | Required By | Available | Version | Fallback |
|------------|------------|-----------|---------|----------|
| PHP 8.3+ | Laravel 12 | [ASSUMED: ya, sesuai CONSTRAINT-001] | 8.3+ | — |
| Composer | Package deps | [ASSUMED: ya] | — | — |
| laravel/fortify | Auth routing | Terpasang | ^1.37 | — |
| spatie/laravel-permission | RBAC | Terpasang | ^6.0 | — |
| spatie/laravel-medialibrary | KYC file upload | Terpasang | ^11.0 | Store path manual |
| MySQL/SQLite | DB | [ASSUMED: SQLite local] | — | — |

---

## Validation Architecture

### Test Framework
| Property | Value |
|----------|-------|
| Framework | PHPUnit 11 + PestPHP 2.34 (dari composer.json) |
| Config file | `/Users/tokaf/gamecommerce/phpunit.xml` |
| Quick run command | `php artisan test --filter Auth` |
| Full suite command | `php artisan test` |

### Phase Requirements → Test Map

| Req ID | Behavior | Test Type | Automated Command | File Exists? |
|--------|----------|-----------|-------------------|-------------|
| REQ-auth-routes | Named routes `login`, `register`, `logout`, dll resolve tanpa error | Feature | `php artisan test --filter AuthRoutesTest` | ❌ Wave 0 |
| REQ-auth-register | POST `/auth/register` membuat user baru | Feature | `php artisan test --filter RegisterTest` | ❌ Wave 0 |
| REQ-auth-login | POST `/auth/login` memulai session | Feature | `php artisan test --filter LoginTest` | ❌ Wave 0 |
| REQ-auth-2fa | GET `/auth/two-factor-challenge` menampilkan view | Feature | `php artisan test --filter TwoFactorTest` | ❌ Wave 0 |
| REQ-auth-rbac | Seller middleware blocks non-seller | Feature | `php artisan test --filter MiddlewareTest` | ❌ Wave 0 |
| REQ-auth-rbac | ProductPolicy::update() hanya izinkan pemilik | Unit | `php artisan test --filter ProductPolicyTest` | ❌ Wave 0 |
| REQ-auth-kyc | GET `/seller/kyc` accessible tanpa kyc | Feature | `php artisan test --filter SellerKycTest` | ❌ Wave 0 |
| REQ-nfr-localization | POST `/locale` mengubah session locale | Feature | `php artisan test --filter LocaleSwitcherTest` | ❌ Wave 0 |

### Wave 0 Gaps
- [ ] `tests/Feature/Auth/AuthRoutesTest.php` — covers REQ-auth-routes
- [ ] `tests/Feature/Auth/RegisterTest.php` — covers REQ-auth-register
- [ ] `tests/Feature/Auth/LoginTest.php` — covers REQ-auth-login
- [ ] `tests/Feature/Auth/TwoFactorTest.php` — covers REQ-auth-2fa
- [ ] `tests/Feature/Auth/MiddlewareTest.php` — covers REQ-auth-rbac (middleware)
- [ ] `tests/Unit/Policies/ProductPolicyTest.php` — covers REQ-auth-rbac (policies)
- [ ] `tests/Feature/Seller/SellerKycTest.php` — covers REQ-auth-kyc
- [ ] `tests/Feature/LocaleSwitcherTest.php` — covers REQ-nfr-localization

---

## Security Domain

Security enforcement: ENABLED (ASVS Level 1)

### Applicable ASVS Categories

| ASVS Category | Applies | Standard Control |
|---------------|---------|-----------------|
| V2 Authentication | YES | Laravel Fortify (login, register, password reset, 2FA) |
| V3 Session Management | YES | Laravel session (secure + httponly + same-site via config/session.php) |
| V4 Access Control | YES | EnsureBuyer/Seller/Admin middleware + Spatie HasRoles + Policy layer |
| V5 Input Validation | YES | FormRequest (LoginRequest, RegisterRequest sudah ada); KYC form perlu validation |
| V6 Cryptography | YES | Password cast 'hashed' (bcrypt) di User.php:68; 2FA via Google2FA |

### Known Threat Patterns

| Pattern | STRIDE | Standard Mitigation |
|---------|--------|---------------------|
| Brute-force login | Denial of Service | RateLimiter::for('login') 5/menit per email+IP — SUDAH ada di FortifyServiceProvider:41-44 |
| CSRF pada form auth | Tampering | Laravel VerifyCsrfToken middleware — aktif di web group |
| Session fixation | Elevation of Privilege | `Auth::logout()` + `session()->invalidate()` + `regenerateToken()` di EnsureNotBanned |
| Insecure file upload (KYC) | Tampering | Mime + size validation di SellerKycController; spatie/medialibrary handles storage |
| Privilege escalation | Elevation of Privilege | Policy layer: `seller_id` ownership check di ProductPolicy::update() |
| Banned user session persistence | Auth bypass | EnsureNotBanned middleware invalidates session saat akses |
| 2FA brute-force | Denial of Service | RateLimiter::for('two-factor') 5/menit per session ID — SUDAH ada di FortifyServiceProvider:46-48 |

---

## Code Examples

### Mendaftarkan Policy di AppServiceProvider
```php
// app/Providers/AppServiceProvider.php
// Source: [VERIFIED: Laravel 12 docs pattern — Gate::policy in AppServiceProvider::boot()]
use Illuminate\Support\Facades\Gate;
use App\Models\Product;
use App\Models\Order;
use App\Policies\ProductPolicy;
use App\Policies\OrderPolicy;
use App\Policies\SellerPolicy;

public function boot(): void
{
    Gate::policy(Product::class, ProductPolicy::class);
    Gate::policy(Order::class, OrderPolicy::class);
    // Super admin bypass — sebelum policy check
    Gate::before(function ($user, $ability) {
        if ($user->hasPlatformRole('super_admin')) {
            return true;
        }
    });
}
```

### Menggunakan Policy di Controller
```php
// Source: [ASSUMED: Laravel standard Gate usage pattern]
// Di SellerProductController:
public function update(Request $request, Product $product)
{
    $this->authorize('update', $product);
    // ... update logic
}

// Atau dengan Gate facade:
if (Gate::denies('update', $product)) {
    abort(403);
}
```

### Verifikasi Fortify Routes
```bash
# Source: [VERIFIED: artisan command]
php artisan route:list --name=login
php artisan route:list --name=logout
php artisan route:list --name=register
php artisan route:list --name=two-factor
```

---

## State of the Art

| Old Approach | Current Approach | When Changed | Impact |
|--------------|------------------|--------------|--------|
| `AuthServiceProvider` untuk policy | `AppServiceProvider::boot()` + `Gate::policy()` | Laravel 11+ | Tidak perlu file terpisah |
| `Kernel.php` untuk middleware | `bootstrap/app.php` `withMiddleware()` | Laravel 11 | Sudah diimplementasikan di proyek ini |
| `Route::post('logout', ...)` manual | Fortify auto-register | Fortify 1.x | Tidak perlu define manual |
| `HasRoles` hanya dari Spatie | Hybrid: kolom `role` + Spatie roles | Proyek ini | `hasPlatformRole()` handles kedua sumber |

---

## Assumptions Log

| # | Claim | Section | Risk if Wrong |
|---|-------|---------|---------------|
| A1 | KYC fields (NIK, KTP photo, selfie, bank account) adalah standar MVP — NPWP opsional | KYC Controller | Perlu approval dari product owner — regulasi bisa mensyaratkan NPWP lebih awal |
| A2 | PHP 8.3+ tersedia di dev environment | Environment Availability | Fase tidak bisa dieksekusi jika versi lebih rendah |
| A3 | SQLite digunakan untuk local dev (sesuai PROJECT.md) | Environment Availability | MySQL-specific queries bisa gagal di SQLite |
| A4 | `User.locale` attribute tidak ada di fillable/migration (SetLocale fallback ke `$request->user()?->locale`) | SetLocale analysis | Jika kolom `locale` tidak di users table, fallback ke preferred language — perilaku OK tapi bisa inconsistent |
| A5 | `seller_id` di Order model adalah `buyer_id` (order milik buyer, bukan seller) | OrderPolicy | Perlu verifikasi Order model — policy harus cek order items seller_id, bukan order.seller_id |

---

## Open Questions (RESOLVED)

1. **User.locale kolom — RESOLVED**
   - Verified: kolom `locale` TIDAK ada di `database/migrations/0001_01_01_000000_create_users_table.php` maupun di `2026_05_08_111351_align_core_gamecommerce_schema.php`. `SetLocale.php:14` `$request->user()?->locale` selalu null untuk semua user, sehingga middleware fallback ke `session('locale')` lalu `getPreferredLanguage()`. PERILAKU INI OK untuk Phase 1.
   - Resolusi: TIDAK menambahkan kolom `locale` di Phase 1 (out-of-scope). Plan 05 menambahkan endpoint `POST /locale/{locale}` yang menulis ke `session('locale')` — sudah cukup untuk REQ-nfr-localization. Database persistence bisa ditambah di phase berikutnya jika diperlukan.

2. **Order model seller_id — RESOLVED**
   - Verified: `Order` model tidak punya `seller_id`. `OrderItem` punya `seller_id` (denormalized snapshot dari `Product.seller_id`). `User::ordersAsSeller()` mengembalikan `HasMany OrderItem`, bukan `Order`.
   - Resolusi: `OrderPolicy::view()` mengecek dua jalur — (a) `$order->buyer_id === $user->id` untuk buyer, (b) `$order->items()->where('seller_id', $user->id)->exists()` untuk seller. Diimplementasikan di Plan 02 (OrderPolicy). Eager loading items direkomendasikan untuk performa.

3. **KYC status `verified` vs `approved` — RESOLVED**
   - Verified: DB menyimpan `'verified'` (lihat seeders, AdminUserController, UserController). Accessor `getKycStatusAttribute` mengubah ke `'approved'` saat dibaca — legacy quirk. `isKycVerified()` cek `['verified', 'approved']` sebagai workaround.
   - Resolusi: **Canonical value adalah `'verified'` (DB)**. Plan 05 menghapus accessor remapping dan mengubah `isKycVerified()` ke `getRawOriginal('kyc_status') === 'verified'`. Plan-checker mengkonfirmasi tidak ada consumer yang bergantung pada nilai `'approved'` — semua existing references (EnsureKycVerified.php, AdminUserController.php, UserController.php) menggunakan `'verified'`.

---

## Sources

### Primary (HIGH confidence)
- `vendor/laravel/fortify/src/FortifyServiceProvider.php` — verifikasi configureRoutes() auto-registration
- `vendor/laravel/fortify/routes/routes.php` — tabel lengkap route names dan HTTP verbs
- `vendor/laravel/fortify/src/Fortify.php:49` — `$registersRoutes = true` default
- `bootstrap/providers.php` — konfirmasi FortifyServiceProvider terdaftar
- `app/Models/User.php` — isBanned(), isKycVerified(), hasPlatformRole() method signatures
- `app/Http/Middleware/EnsureKycVerified.php` — konfirmasi route name yang dibutuhkan
- `config/fortify.php` — prefix 'auth', features enabled
- `bootstrap/app.php` — middleware aliases dan seller/admin route group config

### Secondary (MEDIUM confidence)
- Spatie laravel-permission v6 docs — Gate::policy pattern untuk Laravel 12
- Laravel 12 AppServiceProvider pattern untuk policy registration (AuthServiceProvider tidak lagi diperlukan)

### Tertiary (LOW confidence)
- KYC field requirements (A1) — berdasarkan itemku.com/g2g.com observation, bukan official regulation

---

## Metadata

**Confidence breakdown:**
- Standard stack: HIGH — semua dependencies verified di vendor/
- Fortify route behavior: HIGH — langsung dari vendor source code
- Architecture: HIGH — dari existing code analysis
- Policy patterns: MEDIUM — Laravel 12 pattern, Spatie v6 verified
- KYC fields: LOW — assumed dari marketplace convention
- Pitfalls: HIGH — dari code analysis langsung

**Research date:** 2026-05-08
**Valid until:** 2026-06-08 (30 hari — stack stable)
