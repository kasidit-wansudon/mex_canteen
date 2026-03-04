<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MealPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'meal_date',
        'meal_type',
        'menu_item_1',
        'menu_item_2',
        'menu_item_3',
        'reservation_open_at',
        'reservation_close_at',
        'collection_start_at',
        'collection_end_at',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'meal_date' => 'date',
        'reservation_open_at' => 'datetime',
        'reservation_close_at' => 'datetime',
        'collection_start_at' => 'datetime',
        'collection_end_at' => 'datetime',
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
