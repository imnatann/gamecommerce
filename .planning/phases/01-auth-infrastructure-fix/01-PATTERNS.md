# Phase 1: Auth Infrastructure Fix - Pattern Map

**Mapped:** 2026-05-08
**Files analyzed:** 8
**Analogs found:** 7 / 8

## File Classification

| New/Modified File | Role | Data Flow | Closest Analog | Match Quality |
|---|---|---|---|---|
| `app/Http/Middleware/CheckBanned.php` | middleware | request-response | `app/Http/Middleware/EnsureKycVerified.php` | exact |
| `app/Policies/ProductPolicy.php` | policy | request-response | none in codebase | no-analog |
| `app/Policies/OrderPolicy.php` | policy | request-response | none in codebase | no-analog |
| `app/Policies/SellerPolicy.php` | policy | request-response | none in codebase | no-analog |
| `app/Http/Controllers/Seller/KycController.php` | controller | CRUD | `app/Http/Controllers/Seller/SettingsController.php` | exact |
| `app/Http/Controllers/Web/LocaleController.php` | controller | request-response | `app/Http/Controllers/Web/HomeController.php` | role-match |
| `resources/views/seller/kyc/index.blade.php` | view | request-response | `resources/views/seller/settings.blade.php` | exact |
| `tests/Feature/Auth/*` | test | request-response | `tests/Feature/ExampleTest.php` | role-match |

---

## Pattern Assignments

### `app/Http/Middleware/CheckBanned.php` (middleware, request-response)

**Analog:** `app/Http/Middleware/EnsureKycVerified.php` (lines 1–49)

**Namespace + imports pattern** (lines 1–9):
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;
```

**Class + handle signature** (lines 11–13):
```php
class CheckBanned
{
    public function handle(Request $request, Closure $next): Response
    {
```

**Guard check + dual-response pattern** — copy structure from `EnsureKycVerified.php` lines 14–38:
```php
        $user = $request->user();

        if (! $user) {
            return $this->unauthenticated($request);
        }

        if ($user->is_banned) {       // adapt condition for ban flag
            $message = 'Akun Anda telah diblokir. Hubungi dukungan.';

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => $message], 403);
            }

            return redirect()->route('home')->with('error', $message);
        }

        return $next($request);
    }
```

**Private helpers** — copy verbatim from `EnsureKycVerified.php` lines 41–48:
```php
    private function unauthenticated(Request $request): Response
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(Route::has('login') ? route('login') : url('/auth/login'));
    }
```

**Key conventions:**
- Return type `: Response` on `handle()` — mandatory
- `$request->expectsJson() || $request->is('api/*')` — always both checks, this order
- JSON path returns `response()->json(['message' => ...], status)` — `message` key only, no `error`
- Web path uses `redirect()->with('warning', ...)` for soft blocks, `abort(403)` not used here
- No constructor injection — stateless, no dependencies

**Also compare:** `app/Http/Middleware/EnsureSeller.php` (lines 36–43) for the `forbidden()` private helper pattern using `abort(403, $message)` on web — choose based on whether ban should hard-abort or soft-redirect.

---

### `app/Http/Controllers/Seller/KycController.php` (controller, CRUD)

**Analog:** `app/Http/Controllers/Seller/SettingsController.php` (lines 1–56)

**Namespace + imports pattern** (lines 1–9):
```php
<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
```

**Class declaration** (line 10):
```php
class KycController extends Controller
{
```

**index() pattern** (lines 12–16) — no return type declared on simple view returns:
```php
    public function index()
    {
        $seller = Auth::user();
        return view('seller.kyc.index', compact('seller'));
    }
```

**store/update with validation pattern** (lines 18–39):
```php
    public function store(Request $request)
    {
        $seller = Auth::user();

        $validated = $request->validate([
            'id_type'   => 'required|string|in:ktp,sim,passport',
            'id_number' => 'required|string|max:20',
            'id_photo'  => 'required|image|max:4096',
        ]);

        if ($request->hasFile('id_photo')) {
            $validated['id_photo'] = $request->file('id_photo')->store('kyc', 'public');
        }

        $seller->update($validated);

        return back()->with('success', 'Dokumen KYC berhasil dikirim');
    }
```

**Key conventions:**
- `Auth::user()` — not `auth()->user()` — used consistently throughout Seller controllers
- `$request->validate([...])` inline — no FormRequest class used anywhere in Seller namespace
- File storage: `Storage::disk('public')->delete($old)` before re-upload (see SettingsController line 32)
- Flash messages: `back()->with('success', '...')` — Indonesian language strings
- No explicit return type on action methods (`index()`, `updateProfile()`) — omitted throughout
- Extends `App\Http\Controllers\Controller` (not namespaced further)

---

### `app/Http/Controllers/Web/LocaleController.php` (controller, request-response)

**Analog:** `app/Http/Controllers/Web/HomeController.php` (lines 1–7 for namespace/imports)

**Namespace pattern** (lines 1–4):
```php
<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
```

**Core pattern** — locale switch stores to session and redirects back:
```php
class LocaleController extends Controller
{
    public function switch(Request $request, string $locale): \Illuminate\Http\RedirectResponse
    {
        $supported = ['id', 'en'];

        if (! in_array($locale, $supported)) {
            abort(404);
        }

        session()->put('locale', $locale);

        return redirect()->back()->with('success', 'Bahasa berhasil diubah.');
    }
}
```

**Context:** `app/Http/Middleware/SetLocale.php` (lines 11–27) already reads from `session()->get('locale')` — the LocaleController just needs to write that key. No model update needed unless `$user->locale` persistence is required (see SetLocale line 16).

---

### `resources/views/seller/kyc/index.blade.php` (view, request-response)

**Analog:** `resources/views/seller/settings.blade.php` (lines 1–162)

**Layout extend + section pattern** (lines 1–3):
```blade
@extends('layouts.seller', ['title' => 'Verifikasi KYC'])

@section('content')
<div x-data="kycForm()" class="max-w-3xl">
```

**Page header pattern** (lines 6–9):
```blade
    <div class="mb-6">
        <h1 class="font-[var(--font-family-display)] text-2xl lg:text-3xl font-bold text-[var(--color-gc-text)]">Verifikasi KYC</h1>
        <p class="text-sm text-[var(--color-gc-text-secondary)] mt-1">Lengkapi verifikasi identitas Anda</p>
    </div>
```

**Card section pattern** (lines 15–16, 55–56, 81–82):
```blade
    <div class="gc-card p-6">
        <h2 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)] mb-4">...</h2>
```

**Form + CSRF pattern** (line 11):
```blade
    <form method="POST" action="{{ route('seller.kyc.store') }}" enctype="multipart/form-data" @submit.prevent="submitKyc()">
        @csrf
```

**File input pattern** (lines 22–31) — copy avatar upload pattern for KYC document upload:
```blade
        <div x-data="{ preview: '' }" class="relative">
            <input type="file" x-ref="docInput" @change="preview = URL.createObjectURL($event.target.files[0])"
                   name="id_photo" accept="image/*" class="hidden">
        </div>
```

**Submit button pattern** (lines 133–138):
```blade
        <div class="flex justify-end gap-3">
            <a href="{{ route('seller.dashboard') }}" class="gc-btn gc-btn-ghost gc-btn-md">Batal</a>
            <button type="submit" class="gc-btn gc-btn-primary gc-btn-md">Kirim Dokumen</button>
        </div>
```

**Alpine.js script pattern** (lines 142–161):
```blade
@push('scripts')
<script>
Alpine.data('kycForm', () => ({
    form: { ... },
    submitKyc() {
        this.$el.closest('form').submit();
    },
}));
</script>
@endpush
@endsection
```

**Key conventions:**
- CSS uses CSS custom properties via `var(--color-gc-*)` — no raw Tailwind color names
- Button classes: `gc-btn gc-btn-primary gc-btn-md` / `gc-btn gc-btn-ghost gc-btn-md`
- Card wrapper: `gc-card p-6` — project utility class
- Alpine component wraps entire content `<div x-data="componentName()">`
- `@push('scripts')` at bottom — not `@section('scripts')`

---

### `routes/seller.php` — Adding KYC Routes

**Analog:** `routes/seller.php` (lines 1–28) — existing file, add to it

**Import addition** at top (lines 1–9):
```php
use App\Http\Controllers\Seller\KycController;  // add this line
```

**Route group pattern** (lines 10–28) — add alongside existing routes:
```php
Route::get('/kyc', [KycController::class, 'index'])->name('kyc.index');
Route::post('/kyc', [KycController::class, 'store'])->name('kyc.store');
```

**Context:** Routes in `routes/seller.php` are mounted under a prefix group defined in `app/Providers/RouteServiceProvider.php` (or bootstrap/app.php). All names get `seller.` prefix — so `kyc.index` becomes `seller.kyc.index`, matching the fallback in `EnsureKycVerified.php` line 31.

---

### `routes/web.php` — Adding Locale Route

**Analog:** `routes/web.php` (lines 19–26) — existing `auth.` prefix group

**Pattern** — add a POST route outside auth middleware (locale can be switched unauthenticated):
```php
use App\Http\Controllers\Web\LocaleController;

Route::post('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');
```

---

### `tests/Feature/Auth/*` (test, request-response)

**Analog:** `tests/Feature/ExampleTest.php` (lines 1–21)

**Full test file structure** (lines 1–21):
```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_banned_user_is_redirected(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
```

**Key conventions:**
- `use RefreshDatabase;` — always present in Feature tests
- Extends `Tests\TestCase` — not `PHPUnit\Framework\TestCase`
- Method names: `test_snake_case_description(): void` — return type `: void` explicit
- Namespace: `Tests\Feature` (subdirectory tests go in `Tests\Feature\Auth` etc.)
- No `setUp()` call shown — add only when needed

---

### `app/Providers/FortifyServiceProvider.php` — Modifications

**Analog:** self — existing file (lines 1–50)

**boot() method pattern** (lines 23–49) — new wiring follows same closure pattern:
```php
Fortify::loginView(fn () => view('auth.login'));  // existing pattern
// New additions follow same fn () => view('...') style
```

**Key conventions:**
- All Fortify view bindings use short closures `fn () => view('...')`
- Only `boot()` is modified — `register()` stays empty
- RateLimiter bindings use `function (Request $request)` not short closure (lines 41–48)

---

## Shared Patterns

### Dual-response (JSON vs Web redirect)
**Source:** `app/Http/Middleware/EnsureKycVerified.php` lines 27–35, `app/Http/Middleware/EnsureSeller.php` lines 29–33
**Apply to:** All new middleware (`CheckBanned`)
```php
if ($request->expectsJson() || $request->is('api/*')) {
    return response()->json(['message' => $message], 403);
}
return redirect($route)->with('warning', $message);
```

### Flash message keys
**Source:** `app/Http/Controllers/Seller/SettingsController.php` lines 39, 54
**Apply to:** All Seller controllers
- Success: `->with('success', 'Indonesian string')` 
- Warning (middleware): `->with('warning', 'Indonesian string')`
- Error: `->with('error', 'Indonesian string')`

### Auth retrieval in Seller controllers
**Source:** `app/Http/Controllers/Seller/SettingsController.php` lines 14, 20
**Apply to:** `KycController`
```php
$seller = Auth::user();  // NOT auth()->user()
```

### Locale strings
**Source:** `app/Http/Middleware/EnsureKycVerified.php` lines 21–25, `app/Http/Controllers/Seller/SettingsController.php` lines 39, 54
**Apply to:** All new files — all user-facing strings are Indonesian (Bahasa Indonesia).

---

## No Analog Found

| File | Role | Data Flow | Reason |
|---|---|---|---|
| `app/Policies/ProductPolicy.php` | policy | request-response | No Policy classes exist anywhere in codebase — no `app/Policies/` directory |
| `app/Policies/OrderPolicy.php` | policy | request-response | Same — use Laravel Policy stub pattern |
| `app/Policies/SellerPolicy.php` | policy | request-response | Same — use Laravel Policy stub pattern |

**For Policies:** Use Laravel's standard Policy class structure. Register in `app/Providers/AuthServiceProvider.php` (or `AppServiceProvider::boot()` if no AuthServiceProvider). Anti-pattern: do NOT bypass `spatie/permission` role checks — if the project uses spatie roles, Policy `before()` hook must check roles via `$user->hasRole('admin')` before per-ability checks.

---

## Anti-Patterns to Avoid

1. **Do not use `auth()->user()`** in Seller controllers — all existing code uses `Auth::user()` (facade)
2. **Do not use `abort(403)`** in middleware for web requests — existing pattern soft-redirects with flash message instead (see `EnsureKycVerified` lines 31–35). `EnsureSeller` uses `abort(403)` only for non-auth forbidden — be consistent with whichever the `CheckBanned` middleware intends
3. **Do not add `error` key to JSON responses** — existing middleware uses `{'message': '...'}` only, not `{'error': '...'}`
4. **Do not create FormRequest classes** — Seller controllers use inline `$request->validate([...])`, no FormRequest pattern exists
5. **Do not bypass spatie/permission roles in Policies** — `before()` callback must still respect role hierarchy if spatie/permission is active
6. **Do not use `@section('scripts')`** in Blade views — use `@push('scripts')` (see settings.blade.php line 142)

---

## Metadata

**Analog search scope:** `app/Http/Middleware/`, `app/Http/Controllers/Seller/`, `app/Http/Controllers/Web/`, `resources/views/seller/`, `routes/`, `tests/Feature/`, `app/Providers/`
**Files scanned:** 11
**Pattern extraction date:** 2026-05-08
