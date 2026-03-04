<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollectionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'scanner_user_id',
        'scanner_staff_code',
        'qr_code_token',
        'scan_result',
        'failure_reason',
        'meal_count',
        'scan_payload',
        'scanned_at',
    ];

    protected $casts = [
        'scan_payload' => 'array',
        'scanned_at' => 'datetime',
    ];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function scanner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanner_user_id');
    }
}
