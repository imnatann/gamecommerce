<?php

namespace App\Services\Wallet;

use App\Enums\WalletTransactionType;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\WalletLockedException;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalletService
{
    public function deposit(User $user, float $amount, string $description, array $metadata = []): WalletTransaction
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Deposit amount must be positive');
        }

        return DB::transaction(function () use ($user, $amount, $description, $metadata) {
            $wallet = $this->getOrCreateWallet($user);

            $wallet->lockForUpdate();

            $wallet->increment('balance', $amount);
            $wallet->increment('available_balance', $amount);

            $transaction = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => WalletTransactionType::DEPOSIT->value,
                'amount' => $amount,
                'balance_before' => $wallet->balance - $amount,
                'balance_after' => $wallet->balance,
                'description' => $description,
                'metadata' => $metadata,
            ]);

            $this->clearWalletCache($user);

            Log::info('Wallet deposit', [
                'user_id' => $user->id,
                'amount' => $amount,
                'transaction_id' => $transaction->id,
            ]);

            return $transaction;
        });
    }

    public function withdraw(User $user, float $amount, string $description, array $metadata = []): WalletTransaction
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Withdrawal amount must be positive');
        }

        return DB::transaction(function () use ($user, $amount, $description, $metadata) {
            $wallet = $this->getOrCreateWallet($user);

            $wallet->lockForUpdate();

            if ($wallet->available_balance < $amount) {
                throw new InsufficientBalanceException(
                    "Insufficient balance. Available: {$wallet->available_balance}, Requested: {$amount}"
                );
            }

            $wallet->decrement('balance', $amount);
            $wallet->decrement('available_balance', $amount);

            $transaction = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => WalletTransactionType::WITHDRAWAL->value,
                'amount' => $amount,
                'balance_before' => $wallet->balance + $amount,
                'balance_after' => $wallet->balance,
                'description' => $description,
                'metadata' => $metadata,
            ]);

            $this->clearWalletCache($user);

            Log::info('Wallet withdrawal', [
                'user_id' => $user->id,
                'amount' => $amount,
                'transaction_id' => $transaction->id,
            ]);

            return $transaction;
        });
    }

    public function transfer(User $from, User $to, float $amount, string $description = '', array $metadata = []): WalletTransaction
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Transfer amount must be positive');
        }

        if ($from->id === $to->id) {
            throw new \InvalidArgumentException('Cannot transfer to the same wallet');
        }

        return DB::transaction(function () use ($from, $to, $amount, $description, $metadata) {
            $fromWallet = $this->getOrCreateWallet($from);
            $toWallet = $this->getOrCreateWallet($to);

            $fromWallet->lockForUpdate();
            $toWallet->lockForUpdate();

            if ($fromWallet->available_balance < $amount) {
                throw new InsufficientBalanceException(
                    "Insufficient balance. Available: {$fromWallet->available_balance}, Requested: {$amount}"
                );
            }

            $fromBalanceBefore = $fromWallet->balance;
            $toBalanceBefore = $toWallet->balance;

            $fromWallet->decrement('balance', $amount);
            $fromWallet->decrement('available_balance', $amount);
            $toWallet->increment('balance', $amount);
            $toWallet->increment('available_balance', $amount);

            $transaction = WalletTransaction::create([
                'wallet_id' => $fromWallet->id,
                'type' => WalletTransactionType::TRANSFER_OUT->value,
                'amount' => $amount,
                'balance_before' => $fromBalanceBefore,
                'balance_after' => $fromWallet->balance,
                'description' => $description ?: "Transfer to user #{$to->id}",
                'metadata' => array_merge($metadata, [
                    'recipient_wallet_id' => $toWallet->id,
                    'recipient_user_id' => $to->id,
                ]),
                'related_wallet_id' => $toWallet->id,
            ]);

            WalletTransaction::create([
                'wallet_id' => $toWallet->id,
                'type' => WalletTransactionType::TRANSFER_IN->value,
                'amount' => $amount,
                'balance_before' => $toBalanceBefore,
                'balance_after' => $toWallet->balance,
                'description' => $description ?: "Transfer from user #{$from->id}",
                'metadata' => array_merge($metadata, [
                    'sender_wallet_id' => $fromWallet->id,
                    'sender_user_id' => $from->id,
                ]),
                'related_wallet_id' => $fromWallet->id,
            ]);

            $this->clearWalletCache($from);
            $this->clearWalletCache($to);

            Log::info('Wallet transfer', [
                'from_user_id' => $from->id,
                'to_user_id' => $to->id,
                'amount' => $amount,
                'transaction_id' => $transaction->id,
            ]);

            return $transaction;
        });
    }

    public function freeze(User $user, float $amount, string $reason, array $metadata = []): WalletTransaction
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Freeze amount must be positive');
        }

        return DB::transaction(function () use ($user, $amount, $reason, $metadata) {
            $wallet = $this->getOrCreateWallet($user);

            $wallet->lockForUpdate();

            if ($wallet->is_locked) {
                throw new WalletLockedException('Wallet is locked');
            }

            if ($wallet->available_balance < $amount) {
                throw new InsufficientBalanceException(
                    "Insufficient available balance. Available: {$wallet->available_balance}, Requested: {$amount}"
                );
            }

            $wallet->decrement('available_balance', $amount);
            $wallet->increment('frozen_amount', $amount);

            $transaction = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => WalletTransactionType::FREEZE->value,
                'amount' => $amount,
                'balance_before' => $wallet->balance,
                'balance_after' => $wallet->balance,
                'description' => $reason,
                'metadata' => $metadata,
            ]);

            $this->clearWalletCache($user);

            Log::info('Wallet frozen', [
                'user_id' => $user->id,
                'amount' => $amount,
                'reason' => $reason,
            ]);

            return $transaction;
        });
    }

    public function unfreeze(User $user, float $amount, string $reason, array $metadata = []): WalletTransaction
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Unfreeze amount must be positive');
        }

        return DB::transaction(function () use ($user, $amount, $reason, $metadata) {
            $wallet = $this->getOrCreateWallet($user);

            $wallet->lockForUpdate();

            if ($wallet->frozen_amount < $amount) {
                throw new \InvalidArgumentException(
                    "Cannot unfreeze more than frozen. Frozen: {$wallet->frozen_amount}, Requested: {$amount}"
                );
            }

            $wallet->increment('available_balance', $amount);
            $wallet->decrement('frozen_amount', $amount);

            $transaction = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => WalletTransactionType::UNFREEZE->value,
                'amount' => $amount,
                'balance_before' => $wallet->balance,
                'balance_after' => $wallet->balance,
                'description' => $reason,
                'metadata' => $metadata,
            ]);

            $this->clearWalletCache($user);

            Log::info('Wallet unfrozen', [
                'user_id' => $user->id,
                'amount' => $amount,
                'reason' => $reason,
            ]);

            return $transaction;
        });
    }

    public function refund(User $user, float $amount, string $reason, array $metadata = []): WalletTransaction
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Refund amount must be positive');
        }

        return DB::transaction(function () use ($user, $amount, $reason, $metadata) {
            $wallet = $this->getOrCreateWallet($user);

            $wallet->lockForUpdate();

            $wallet->increment('balance', $amount);
            $wallet->increment('available_balance', $amount);

            $transaction = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => WalletTransactionType::REFUND->value,
                'amount' => $amount,
                'balance_before' => $wallet->balance - $amount,
                'balance_after' => $wallet->balance,
                'description' => $reason,
                'metadata' => $metadata,
            ]);

            $this->clearWalletCache($user);

            Log::info('Wallet refund', [
                'user_id' => $user->id,
                'amount' => $amount,
                'reason' => $reason,
            ]);

            return $transaction;
        });
    }

    public function getBalance(User $user): array
    {
        return Cache::remember("wallet.balance.{$user->id}", 30, function () use ($user) {
            $wallet = $this->getOrCreateWallet($user);

            return [
                'balance' => (float) $wallet->balance,
                'available_balance' => (float) $wallet->available_balance,
                'frozen_amount' => (float) $wallet->frozen_amount,
                'is_locked' => $wallet->is_locked,
            ];
        });
    }

    public function getTransactionHistory(User $user, string $type = null, int $perPage = 15): array
    {
        $wallet = $this->getOrCreateWallet($user);

        $query = WalletTransaction::where('wallet_id', $wallet->id)
            ->orderByDesc('created_at');

        if ($type) {
            $query->where('type', $type);
        }

        return $query->paginate($perPage)->toArray();
    }

    private function getOrCreateWallet(User $user): Wallet
    {
        return Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'available_balance' => 0, 'frozen_amount' => 0, 'is_locked' => false]
        );
    }

    private function clearWalletCache(User $user): void
    {
        Cache::forget("wallet.balance.{$user->id}");
    }
}