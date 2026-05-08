<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->alignUsers();
        $this->alignGames();
        $this->alignProducts();
        $this->alignCartItems();
        $this->alignOrders();
        $this->alignOrderItems();
        $this->alignPayments();
        $this->alignWallets();
        $this->alignWalletTransactions();
        $this->backfillProductDenormalizedColumns();
    }

    public function down(): void
    {
        $this->dropColumnsIfPresent('wallet_transactions', [
            'user_id',
            'order_id',
            'metadata',
            'related_wallet_id',
        ]);

        $this->dropColumnsIfPresent('wallets', [
            'available_balance',
            'frozen_amount',
            'is_locked',
        ]);

        $this->dropColumnsIfPresent('payments', [
            'gateway_transaction_id',
            'snap_token',
            'payment_method',
            'payload',
            'raw_notification',
        ]);

        $this->dropColumnsIfPresent('order_items', [
            'total',
            'server',
            'region',
            'delivery_type',
        ]);

        $this->dropColumnsIfPresent('orders', [
            'order_number',
            'subtotal_amount',
            'final_amount',
            'expires_at',
            'paid_at',
        ]);

        $this->dropColumnsIfPresent('cart_items', [
            'price',
            'server',
            'region',
        ]);

        $this->dropColumnsIfPresent('products', [
            'game_id',
            'category_id',
            'product_type',
            'required_info',
            'is_featured',
            'is_hot_deal',
        ]);

        $this->dropColumnsIfPresent('games', [
            'category_id',
        ]);

        $this->dropColumnsIfPresent('users', [
            'role',
            'kyc_status',
            'kyc_verified_at',
            'banned_at',
            'ban_reason',
            'two_factor_secret',
            'two_factor_recovery_codes',
            'last_activity_at',
        ]);
    }

    private function alignUsers(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('buyer')->after('password')->index();
            }

            if (! Schema::hasColumn('users', 'kyc_status')) {
                $table->string('kyc_status')->default('pending')->after('role')->index();
            }

            if (! Schema::hasColumn('users', 'kyc_verified_at')) {
                $table->timestamp('kyc_verified_at')->nullable()->after('kyc_status');
            }

            if (! Schema::hasColumn('users', 'banned_at')) {
                $table->timestamp('banned_at')->nullable()->after('kyc_verified_at');
            }

            if (! Schema::hasColumn('users', 'ban_reason')) {
                $table->string('ban_reason')->nullable()->after('banned_at');
            }

            if (! Schema::hasColumn('users', 'two_factor_secret')) {
                $table->text('two_factor_secret')->nullable()->after('remember_token');
            }

            if (! Schema::hasColumn('users', 'two_factor_recovery_codes')) {
                $table->text('two_factor_recovery_codes')->nullable()->after('two_factor_secret');
            }

            if (! Schema::hasColumn('users', 'last_activity_at')) {
                $table->timestamp('last_activity_at')->nullable()->after('two_factor_recovery_codes');
            }
        });
    }

    private function alignGames(): void
    {
        Schema::table('games', function (Blueprint $table) {
            if (! Schema::hasColumn('games', 'category_id')) {
                $table->unsignedBigInteger('category_id')->nullable()->after('category')->index();
            }
        });
    }

    private function alignProducts(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'game_id')) {
                $table->unsignedBigInteger('game_id')->nullable()->after('game_product_id')->index();
            }

            if (! Schema::hasColumn('products', 'category_id')) {
                $table->unsignedBigInteger('category_id')->nullable()->after('game_id')->index();
            }

            if (! Schema::hasColumn('products', 'product_type')) {
                $table->string('product_type')->nullable()->after('description')->index();
            }

            if (! Schema::hasColumn('products', 'required_info')) {
                $table->json('required_info')->nullable()->after('delivery_data');
            }

            if (! Schema::hasColumn('products', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('is_active')->index();
            }

            if (! Schema::hasColumn('products', 'is_hot_deal')) {
                $table->boolean('is_hot_deal')->default(false)->after('is_featured')->index();
            }
        });
    }

    private function alignCartItems(): void
    {
        try {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->dropUnique('cart_items_cart_id_product_id_unique');
            });
        } catch (Throwable) {
            // The early baseline may already use non-unique cart item lookup indexes.
        }

        Schema::table('cart_items', function (Blueprint $table) {
            if (! Schema::hasColumn('cart_items', 'price')) {
                $table->unsignedBigInteger('price')->default(0)->after('quantity');
            }

            if (! Schema::hasColumn('cart_items', 'server')) {
                $table->string('server')->nullable()->after('price');
            }

            if (! Schema::hasColumn('cart_items', 'region')) {
                $table->string('region')->nullable()->after('server');
            }

            $table->index(['cart_id', 'product_id', 'server', 'region'], 'cart_items_lookup_idx');
        });
    }

    private function alignOrders(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'order_number')) {
                $table->string('order_number')->nullable()->after('id')->unique();
            }

            if (! Schema::hasColumn('orders', 'subtotal_amount')) {
                $table->unsignedBigInteger('subtotal_amount')->default(0)->after('buyer_id');
            }

            if (! Schema::hasColumn('orders', 'final_amount')) {
                $table->unsignedBigInteger('final_amount')->default(0)->after('discount_amount');
            }

            if (! Schema::hasColumn('orders', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('ip_address');
            }

            if (! Schema::hasColumn('orders', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('expires_at');
            }
        });
    }

    private function alignOrderItems(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('order_items', 'total')) {
                $table->unsignedBigInteger('total')->default(0)->after('price');
            }

            if (! Schema::hasColumn('order_items', 'server')) {
                $table->string('server')->nullable()->after('total');
            }

            if (! Schema::hasColumn('order_items', 'region')) {
                $table->string('region')->nullable()->after('server');
            }

            if (! Schema::hasColumn('order_items', 'delivery_type')) {
                $table->string('delivery_type')->nullable()->after('region')->index();
            }
        });
    }

    private function alignPayments(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'gateway_transaction_id')) {
                $table->string('gateway_transaction_id')->nullable()->after('gateway')->index();
            }

            if (! Schema::hasColumn('payments', 'snap_token')) {
                $table->text('snap_token')->nullable()->after('gateway_transaction_id');
            }

            if (! Schema::hasColumn('payments', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('method');
            }

            if (! Schema::hasColumn('payments', 'payload')) {
                $table->json('payload')->nullable()->after('gateway_response');
            }

            if (! Schema::hasColumn('payments', 'raw_notification')) {
                $table->json('raw_notification')->nullable()->after('payload');
            }
        });
    }

    private function alignWallets(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            if (! Schema::hasColumn('wallets', 'available_balance')) {
                $table->unsignedBigInteger('available_balance')->default(0)->after('balance');
            }

            if (! Schema::hasColumn('wallets', 'frozen_amount')) {
                $table->unsignedBigInteger('frozen_amount')->default(0)->after('available_balance');
            }

            if (! Schema::hasColumn('wallets', 'is_locked')) {
                $table->boolean('is_locked')->default(false)->after('frozen_balance');
            }
        });
    }

    private function alignWalletTransactions(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('wallet_transactions', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('wallet_id')->index();
            }

            if (! Schema::hasColumn('wallet_transactions', 'order_id')) {
                $table->unsignedBigInteger('order_id')->nullable()->after('user_id')->index();
            }

            if (! Schema::hasColumn('wallet_transactions', 'metadata')) {
                $table->json('metadata')->nullable()->after('reference_id');
            }

            if (! Schema::hasColumn('wallet_transactions', 'related_wallet_id')) {
                $table->unsignedBigInteger('related_wallet_id')->nullable()->after('metadata')->index();
            }
        });
    }

    private function backfillProductDenormalizedColumns(): void
    {
        DB::statement(<<<'SQL'
            UPDATE products
            SET game_id = (
                SELECT game_id
                FROM game_products
                WHERE game_products.id = products.game_product_id
            )
            WHERE game_id IS NULL
        SQL);

        DB::statement(<<<'SQL'
            UPDATE products
            SET product_type = (
                SELECT type
                FROM game_products
                WHERE game_products.id = products.game_product_id
            )
            WHERE product_type IS NULL
        SQL);

        DB::statement('UPDATE wallets SET available_balance = balance WHERE available_balance = 0 AND balance > 0');
        DB::statement('UPDATE orders SET subtotal_amount = total_amount WHERE subtotal_amount = 0');
        DB::statement('UPDATE orders SET final_amount = total_amount WHERE final_amount = 0');
        DB::statement('UPDATE order_items SET total = price * quantity WHERE total = 0');
    }

    private function dropColumnsIfPresent(string $table, array $columns): void
    {
        $existing = array_values(array_filter(
            $columns,
            fn (string $column): bool => Schema::hasColumn($table, $column)
        ));

        if ($existing === []) {
            return;
        }

        Schema::table($table, function (Blueprint $table) use ($existing) {
            $table->dropColumn($existing);
        });
    }
};
