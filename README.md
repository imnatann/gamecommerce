# GameCommerce

GameCommerce is a Laravel marketplace prototype for digital game commerce: top-up products, game keys, vouchers, accounts, items, and seller-managed services.

## Current Public Web Surface

The registered public catalog routes are:

| Method | Path | Name | Purpose |
| --- | --- | --- | --- |
| GET | `/` | `home` | Homepage with hero banners, game shortcuts, product sections, and payment badges |
| GET | `/search` | `search` | Catalog search and filtering by product type, game/category keyword, price, and sort |
| GET | `/g/{slug}` | `game.show` | Game landing page with products for the selected game |
| GET | `/d/{slug}/{id}` | `product.show` | Product detail page |

Authenticated buyer routes currently registered in `routes/web.php` include `cart`, `cart.add`, `checkout`, `checkout.process`, `order.status`, `profile.orders`, and `profile.favorites`.

## Local Development

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run dev
php artisan serve
```

The default local database is SQLite at `database/database.sqlite`.

## Notes For This Snapshot

- `PLANNING.md` describes a larger target sitemap. The currently wired public routes are the smaller MVP route set listed above.
- The public catalog controllers avoid optional media/catalog relations when they are not required, so public pages can render against a sparse seed database.
- Auth Blade views exist, but named web routes for `login`, `register`, and `logout` are not currently registered in `routes/web.php`.

## Verification

Run the PHP test suite:

```bash
php artisan test
```

Useful public smoke checks:

```bash
php artisan route:list --except-vendor
php artisan test --filter=ExampleTest
```
