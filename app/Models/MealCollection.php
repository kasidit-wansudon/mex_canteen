<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MealCollection extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'collector_user_id',
        'collector_staff_code',
        'collector_name',
        'collected_meal_count',
        'collection_method',
        'collected_at',
        'remark',
    ];

    protected $casts = [
        'collected_at' => 'datetime',
    ];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function collector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collector_user_id');
    }
}
