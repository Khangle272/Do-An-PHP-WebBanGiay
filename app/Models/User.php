<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

#[Fillable(['name', 'email', 'phone', 'address', 'password', 'is_admin'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function isCurrentlyLoggedIn(): bool
    {
        $sessionLifetime = (int) config('session.lifetime') * 60;

        $activeWebSession = DB::table('sessions')
            ->where('user_id', $this->id)
            ->where('last_activity', '>=', now()->getTimestamp() - $sessionLifetime)
            ->exists();

        $activeApiToken = DB::table('personal_access_tokens')
            ->where('tokenable_type', static::class)
            ->where('tokenable_id', $this->id)
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->exists();

        return $activeWebSession || $activeApiToken;
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }
}
