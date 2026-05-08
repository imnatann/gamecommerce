# 🏗️ GameCommerce - Framework Architecture

## Project Structure

```
gamecommerce/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── V1/
│   │   │   │   │   ├── Auth/
│   │   │   │   │   │   ├── LoginController.php
│   │   │   │   │   │   ├── RegisterController.php
│   │   │   │   │   │   ├── ForgotPasswordController.php
│   │   │   │   │   │   └── TwoFactorController.php
│   │   │   │   │   ├── Catalog/
│   │   │   │   │   │   ├── GameController.php
│   │   │   │   │   │   ├── ProductController.php
│   │   │   │   │   │   └── CategoryController.php
│   │   │   │   │   ├── Buyer/
│   │   │   │   │   │   ├── CartController.php
│   │   │   │   │   │   ├── OrderController.php
│   │   │   │   │   │   ├── ReviewController.php
│   │   │   │   │   │   └── WishlistController.php
│   │   │   │   │   ├── Seller/
│   │   │   │   │   │   ├── SellerProductController.php
│   │   │   │   │   │   ├── SellerOrderController.php
│   │   │   │   │   │   └── SellerEarningController.php
│   │   │   │   │   └── Admin/
│   │   │   │   │       ├── AdminDashboardController.php
│   │   │   │   │       ├── AdminGameController.php
│   │   │   │   │       ├── AdminUserController.php
│   │   │   │   │       ├── AdminDisputeController.php
│   │   │   │   │       └── AdminBannerController.php
│   │   │   │   └── ApiBaseController.php
│   │   │   └── Web/
│   │   │       ├── HomeController.php
│   │   │       ├── GamePageController.php
│   │   │       ├── ProductPageController.php
│   │   │       ├── SearchController.php
│   │   │       ├── CheckoutController.php
│   │   │       ├── ProfileController.php
│   │   │       └── SellerPageController.php
│   │   ├── Middleware/
│   │   │   ├── EnsureBuyer.php
│   │   │   ├── EnsureSeller.php
│   │   │   ├── EnsureAdmin.php
│   │   │   ├── EnsureKycVerified.php
│   │   │   └── SetLocale.php
│   │   └── Requests/
│   │       ├── Auth/
│   │       │   ├── LoginRequest.php
│   │       │   └── RegisterRequest.php
│   │       ├── Product/
│   │       │   ├── StoreProductRequest.php
│   │       │   └── UpdateProductRequest.php
│   │       └── Order/
│   │           ├── CreateOrderRequest.php
│   │           └── DisputeRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Game.php
│   │   ├── GameProduct.php
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── Payment.php
│   │   ├── Review.php
│   │   ├── Wallet.php
│   │   ├── WalletTransaction.php
│   │   ├── Wishlist.php
│   │   ├── Cart.php
│   │   ├── CartItem.php
│   │   ├── Voucher.php
│   │   ├── VoucherUsage.php
│   │   ├── Banner.php
│   │   ├── Dispute.php
│   │   └── ChatMessage.php
│   ├── Services/
│   │   ├── Payment/
│   │   │   ├── PaymentService.php
│   │   │   ├── MidtransService.php
│   │   │   └── XenditService.php
│   │   ├── Delivery/
│   │   │   ├── AutoDeliveryService.php
│   │   │   └── ManualDeliveryService.php
│   │   ├── Search/
│   │   │   └── MeilisearchService.php
│   │   ├── Wallet/
│   │   │   └── WalletService.php
│   │   └── Notification/
│   │       ├── EmailNotificationService.php
│   │       ├── PushNotificationService.php
│   │       └── WhatsAppNotificationService.php
│   ├── Repositories/
│   │   ├── Contracts/
│   │   │   ├── GameRepositoryInterface.php
│   │   │   ├── ProductRepositoryInterface.php
│   │   │   └── OrderRepositoryInterface.php
│   │   ├── GameRepository.php
│   │   ├── ProductRepository.php
│   │   └── OrderRepository.php
│   ├── Enums/
│   │   ├── UserRole.php
│   │   ├── OrderStatus.php
│   │   ├── PaymentStatus.php
│   │   ├── ProductType.php
│   │   ├── DeliveryType.php
│   │   ├── DisputeStatus.php
│   │   └── WalletTransactionType.php
│   ├── Events/
│   │   ├── OrderCreated.php
│   │   ├── PaymentReceived.php
│   │   ├── ProductDelivered.php
│   │   └── DisputeCreated.php
│   ├── Listeners/
│   │   ├── SendOrderNotification.php
│   │   ├── ProcessAutoDelivery.php
│   │   └── UpdateProductStock.php
│   ├── Policies/
│   │   ├── ProductPolicy.php
│   │   ├── OrderPolicy.php
│   │   └── SellerPolicy.php
│   └── Providers/
│       ├── AppServiceProvider.php
│       ├── RepositoryServiceProvider.php
│       └── PaymentServiceProvider.php
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── app.blade.php
│   │   │   ├── auth.blade.php
│   │   │   ├── seller.blade.php
│   │   │   └── admin.blade.php
│   │   ├── components/
│   │   │   ├── game-card.blade.php
│   │   │   ├── product-card.blade.php
│   │   │   ├── category-chip.blade.php
│   │   │   ├── rating-stars.blade.php
│   │   │   ├── price-tag.blade.php
│   │   │   ├── search-bar.blade.php
│   │   │   ├── trust-badge.blade.php
│   │   │   ├── payment-method-grid.blade.php
│   │   │   ├── seller-badge.blade.php
│   │   │   ├── order-status-badge.blade.php
│   │   │   ├── flash-sale-timer.blade.php
│   │   │   └── notification-bell.blade.php
│   │   ├── pages/
│   │   │   ├── home.blade.php
│   │   │   ├── game.blade.php
│   │   │   ├── product.blade.php
│   │   │   ├── search.blade.php
│   │   │   ├── checkout.blade.php
│   │   │   ├── order-status.blade.php
│   │   │   ├── profile/
│   │   │   │   ├── index.blade.php
│   │   │   │   ├── orders.blade.php
│   │   │   │   ├── wallet.blade.php
│   │   │   │   └── favorites.blade.php
│   │   │   └── seller/
│   │   │       ├── dashboard.blade.php
│   │   │       ├── products.blade.php
│   │   │       ├── orders.blade.php
│   │   │       └── earnings.blade.php
│   │   └── admin/
│   │       ├── dashboard.blade.php
│   │       ├── games.blade.php
│   │       ├── users.blade.php
│   │       ├── orders.blade.php
│   │       └── disputes.blade.php
│   ├── css/
│   │   └── app.css (Tailwind + shadcn custom)
│   └── js/
│       ├── app.js
│       ├── components/
│       │   ├── search-autocomplete.js
│       │   ├── cart-count.js
│       │   ├── countdown-timer.js
│       │   ├── image-gallery.js
│       │   └── copy-button.js
│       └── pages/
│           ├── checkout.js
│           └── seller-product-form.js
├── database/
│   ├── migrations/
│   │   ├── 0001_create_users_table.php
│   │   ├── 0002_create_games_table.php
│   │   ├── 0003_create_categories_table.php
│   │   ├── 0004_create_game_products_table.php
│   │   ├── 0005_create_products_table.php
│   │   ├── 0006_create_orders_table.php
│   │   ├── 0007_create_order_items_table.php
│   │   ├── 0008_create_payments_table.php
│   │   ├── 0009_create_wallets_table.php
│   │   ├── 0010_create_wallet_transactions_table.php
│   │   ├── 0011_create_reviews_table.php
│   │   ├── 0012_create_wishlists_table.php
│   │   ├── 0013_create_carts_table.php
│   │   ├── 0014_create_cart_items_table.php
│   │   ├── 0015_create_vouchers_table.php
│   │   ├── 0016_create_voucher_usages_table.php
│   │   ├── 0017_create_banners_table.php
│   │   ├── 0018_create_disputes_table.php
│   │   ├── 0019_create_dispute_messages_table.php
│   │   ├── 0020_create_chat_messages_table.php
│   │   ├── 0021_create_notifications_table.php
│   │   └── 0022_add_scout_indexes.php
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   │   ├── UserSeeder.php
│   │   ├── GameSeeder.php
│   │   ├── CategorySeeder.php
│   │   └── BannerSeeder.php
│   └── factories/
│       ├── UserFactory.php
│       ├── GameFactory.php
│       ├── ProductFactory.php
│       └── OrderFactory.php
├── routes/
│   ├── web.php
│   ├── api.php
│   ├── seller.php
│   └── admin.php
├── config/
│   └── gamecommerce.php
├── tests/
│   ├── Feature/
│   │   ├── Auth/
│   │   ├── Catalog/
│   │   ├── Order/
│   │   ├── Seller/
│   │   └── Admin/
│   └── Unit/
│       ├── Services/
│       └── Repositories/
├── composer.json
├── package.json
├── vite.config.js
├── tailwind.config.js
└── theme.md
```

---

## Architecture Patterns

### 1. Repository Pattern
```php
interface ProductRepositoryInterface
{
    public function search(string $query, array $filters): LengthAwarePaginator;
    public function findByGame(int $gameId, string $productType): Collection;
    public function findCheapest(int $gameProductId): ?Product;
    public function getPopular(int $limit): Collection;
}
```

### 2. Service Layer Pattern
```php
class OrderService
{
    public function createOrder(User $buyer, array $items, ?string $voucherCode): Order;
    public function processPayment(Order $order, string $paymentMethod): Payment;
    public function handleDelivery(Order $order): void;
    public function completeOrder(Order $order): void;
}
```

### 3. Action Pattern (for complex operations)
```php
class CreateOrderAction
{
    public function execute(CreateOrderRequest $request): Order
    {
        return DB::transaction(function () use ($request) {
            $cart = $this->resolveCart($request);
            $order = $this->createOrder($cart, $request->user());
            $this->applyVoucher($order, $request->voucher_code);
            $this->deductStock($cart);
            event(new OrderCreated($order));
            return $order;
        });
    }
}
```

### 4. Enum-based State Machines
```php
enum OrderStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case PROCESSING = 'processing';
    case DELIVERED = 'delivered';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
    case DISPUTED = 'disputed';

    public function canTransitionTo(self $next): bool
    {
        return match($this) {
            self::PENDING => in_array($next, [self::PAID, self::CANCELLED]),
            self::PAID => in_array($next, [self::PROCESSING, self::REFUNDED]),
            self::PROCESSING => in_array($next, [self::DELIVERED, self::DISPUTED]),
            self::DELIVERED => in_array($next, [self::COMPLETED, self::DISPUTED]),
            self::DISPUTED => in_array($next, [self::COMPLETED, self::REFUNDED]),
            default => false,
        };
    }
}
```

---

## Key Configuration

### composer.json (key dependencies)
```json
{
    "require": {
        "php": "^8.3",
        "laravel/framework": "^12.0",
        "laravel/fortify": "^1.20",
        "laravel/socialite": "^5.14",
        "laravel/scout": "^10.8",
        "laravel/horizon": "^5.20",
        "laravel/reverb": "^1.0",
        "laravel/telescope": "^5.0",
        "midtrans/midtrans-php": "^2.6",
        "wildside/useressence": "*",
        "spatie/laravel-permission": "^6.0",
        "spatie/laravel-activitylog": "^4.7",
        "spatie/laravel-medialibrary": "^11.0",
        "spatie/laravel-sluggable": "^3.6",
        "intervention/image": "^3.0"
    },
    "require-dev": {
        "phpunit/phpunit": "^11.0",
        "pestphp/pest": "^2.34",
        "laravel/pint": "^1.0",
        "mockery/mockery": "^1.6"
    }
}
```

### package.json (key dependencies)
```json
{
    "dependencies": {
        "alpinejs": "^3.14",
        "@alpinejs/persist": "^3.14",
        "@alpinejs/focus": "^3.14",
        "axios": "^1.7"
    },
    "devDependencies": {
        "tailwindcss": "^4.0",
        "@tailwindcss/vite": "^4.0",
        "vite": "^6.0",
        "laravel-vite-plugin": "^1.2"
    }
}
```

---

## Middleware Stack

```php
// app/Http/Kernel.php or bootstrap/app.php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\SetLocale::class,
        \App\Http\Middleware\TrackLastActivity::class,
    ]);

    $middleware->api(append: [
        \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    ]);

    $middleware->alias([
        'buyer' => \App\Http\Middleware\EnsureBuyer::class,
        'seller' => \App\Http\Middleware\EnsureSeller::class,
        'admin' => \App\Http\Middleware\EnsureAdmin::class,
        'kyc' => \App\Http\Middleware\EnsureKycVerified::class,
    ]);
});
```

---

## Queue & Job Architecture

```php
// High priority — orders & payments
class ProcessPaymentJob implements ShouldQueue
{
    public int $tries = 3;
    public string $queue = 'payments';
}

// Normal priority — notifications
class SendOrderNotificationJob implements ShouldQueue
{
    public string $queue = 'notifications';
}

// Low priority — analytics, SEO
class UpdateProductSearchIndexJob implements ShouldQueue
{
    public string $queue = 'indexing';
}

// Scheduled jobs
class UpdatePopularProducts implements ShouldQueue
{
    // Run every hour — cache popular products
}

class CancelExpiredOrders implements ShouldQueue
{
    // Run every 5 minutes — cancel unpaid orders > 30 min
}

class ProcessAutoDelivery implements ShouldQueue
{
    // Run every minute — deliver auto-delivery products
}
```

---

## Event System

```
OrderCreated ─────┬──► SendOrderNotificationListener
                  ├──► ProcessAutoDeliveryListener
                  └──► UpdateProductStockListener

PaymentReceived ──┬──► UpdateOrderStatusListener
                   ├──► SendPaymentConfirmationListener
                   └──► NotifySellerListener

ProductDelivered ──┬──► RequestReviewListener
                    └──► UpdateProductSoldCountListener

DisputeCreated ────► NotifyAdminAndSellerListener

ReviewCreated ────┬──► UpdateProductRatingListener
                   └──► UpdateSellerRatingListener
```

---

## Caching Strategy

```php
// Cache keys & TTL
Cache::remember('games.popular', 3600, fn() => Game::with('products')->popular()->get());
Cache::remember('games.list', 1800, fn() => Game::with('category', 'icon')->orderBy('sort_order')->get());
Cache::remember("products.game.{$gameSlug}", 900, fn() => /* ... */);
Cache::remember("products.cheapest.{$gameProductId}", 600, fn() => /* ... */);
Cache::remember('banners.active', 1800, fn() => Banner::active()->ordered()->get());
Cache::remember("seller.products.{$sellerId}", 300, fn() => /* ... */);

// Cache invalidation on model events
// Game::saved() → clear 'games.*'
// Product::saved() → clear 'products.*', 'games.popular'
// Order::completed() → clear 'orders.stats.*'
```

---

## Security Checklist

- [ ] Force HTTPS in production
- [ ] CSRF protection on all forms
- [ ] XSS protection (Blade auto-escaping + CSP headers)
- [ ] SQL injection prevention (Eloquent ORM only)
- [ ] Rate limiting (api throttle, login throttle)
- [ ] Input validation (FormRequest classes)
- [ ] File upload validation (mime, size, virus scan)
- [ ] Content Security Policy headers
- [ ] CORS configuration
- [ ] Session security (secure, httponly, same-site cookies)
- [ ] Password hashing (bcrypt, min 8 chars)
- [ ] 2FA support (Fortify)
- [ ]KYC verification for sellers
- [ ] Escrow system for high-value trades
- [ ] Bot protection (Cloudflare Turnstile/R2)

---

## Testing Strategy

```bash
# Unit tests — Services, Repositories, Enums
php artisan test --testsuite=Unit

# Feature tests — API endpoints, web pages
php artisan test --testsuite=Feature

# Integration tests — Payment flow, delivery flow
php artisan test --testsuite=Integration

# Browser tests — Laravel Dusk
php artisan dusk

# Load testing — k6 / JMeter
k6 run tests/load/order-flow.js
```

### Key Test Scenarios
1. Guest can browse games & products
2. User can register & login
3. Buyer can add to cart & checkout
4. Payment flow (success, fail, timeout)
5. Auto-delivery triggers after payment
6. Manual delivery flow (seller ships)
7. Dispute creation & resolution
8. Seller can CRUD products
9. Search returns relevant results
10. Wallet top-up & withdrawal