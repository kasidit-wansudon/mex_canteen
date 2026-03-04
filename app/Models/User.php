<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'staff_code',
        'staff_name',
        'email',
        'staff_type',
        'role',
        'account_status',
        'password',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'account_status' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function mealCollections(): HasMany
    {
        return $this->hasMany(MealCollection::class, 'collector_user_id');
    }

    public function collectionLogs(): HasMany
    {
        return $this->hasMany(CollectionLog::class, 'scanner_user_id');
    }
}
