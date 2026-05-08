<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia, MustVerifyEmail
{
    use HasApiTokens, HasFactory, HasRoles, InteractsWithMedia, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'avatar',
        'bio',
        'role',
        'meta',
        'kyc_status',
        'kyc_verified_at',
        'kyc_rejection_reason',
        'is_banned',
        'banned_at',
        'ban_reason',
        'email_verified_at',
        'phone_verified_at',
        'last_activity_at',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $appends = [
        'role',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'kyc_verified_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
            'is_banned' => 'boolean',
            'banned_at' => 'datetime',
            'meta' => 'array',
            'password' => 'hashed',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')->singleFile();
        $this->addMediaCollection('kyc_id_photo')->singleFile();
        $this->addMediaCollection('kyc_selfie')->singleFile();
    }

    public function getRoleAttribute(?string $value): ?string
    {
        return $value
            ?? $this->roles->first()?->name
            ?? UserRole::BUYER->value;
    }

    public function getKycStatusAttribute(?string $value): ?string
    {
        return $value;
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'seller_id');
    }

    public function ordersAsBuyer(): HasMany
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    public function orders(): HasMany
    {
        return $this->ordersAsBuyer();
    }

    public function ordersAsSeller(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'seller_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function disputesAsBuyer(): HasMany
    {
        return $this->hasMany(Dispute::class, 'buyer_id');
    }

    public function disputesAsSeller(): HasMany
    {
        return $this->hasMany(Dispute::class, 'seller_id');
    }

    public function disputes(): HasMany
    {
        return $this->disputesAsBuyer();
    }

    public function isSeller(): bool
    {
        return $this->hasPlatformRole([
            UserRole::SELLER,
            UserRole::ADMIN,
            UserRole::SUPER_ADMIN,
        ]);
    }

    public function isAdmin(): bool
    {
        return $this->hasPlatformRole([
            UserRole::ADMIN,
            UserRole::SUPER_ADMIN,
        ]);
    }

    public function isBuyer(): bool
    {
        return $this->hasPlatformRole([
            UserRole::BUYER,
            UserRole::ADMIN,
            UserRole::SUPER_ADMIN,
        ]);
    }

    public function isKycVerified(): bool
    {
        return ($this->attributes['kyc_status'] ?? null) === 'verified';
    }

    public function hasPlatformRole(array|string|UserRole $roles): bool
    {
        $roleNames = collect(is_array($roles) ? $roles : [$roles])
            ->map(fn (string|UserRole $role) => $role instanceof UserRole ? $role->value : $role)
            ->all();

        if (in_array($this->role, $roleNames, true)) {
            return true;
        }

        try {
            return $this->hasAnyRole($roleNames);
        } catch (\Throwable) {
            return false;
        }
    }

    public function assignPlatformRole(string|UserRole $role): self
    {
        $roleName = $role instanceof UserRole ? $role->value : $role;

        $this->forceFill(['role' => $roleName])->save();

        Role::findOrCreate($roleName, $this->getDefaultGuardName());
        $this->syncRoles([$roleName]);

        return $this;
    }

    public function isBanned(): bool
    {
        return $this->is_banned || (bool) data_get($this->meta, 'banned', false);
    }
}
