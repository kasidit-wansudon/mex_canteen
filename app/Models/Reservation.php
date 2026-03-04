<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'meal_plan_id',
        'user_id',
        'visitor_id',
        'reservation_date',
        'meal_type',
        'reservation_type',
        'visitor_count',
        'pickup_for_staff_code',
        'pickup_for_user_id',
        'meal_count',
        'qr_code_token',
        'qr_expiry_time',
        'status',
        'collected_at',
        'cancelled_at',
        'remark',
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'qr_expiry_time' => 'datetime',
        'collected_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function mealPlan(): BelongsTo
    {
        return $this->belongsTo(MealPlan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    public function pickupForUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pickup_for_user_id');
    }

    public function mealCollection(): HasOne
    {
        return $this->hasOne(MealCollection::class);
    }

    public function collectionLogs(): HasMany
    {
        return $this->hasMany(CollectionLog::class);
    }

    public function isEditable(): bool
    {
        if (in_array($this->status, ['cancelled', 'collected'], true)) {
            return false;
        }

        if ($this->mealPlan && $this->mealPlan->reservation_close_at) {
            return now()->lessThanOrEqualTo($this->mealPlan->reservation_close_at);
        }

        return true;
    }
}
