<?php

namespace App\Services\Canteen;

use App\Models\Reservation;
use Illuminate\Support\Str;

class QrCodeService
{
    /**
     * Generate a unique token for reservation QR.
     *
     * @return string
     */
    public function generateToken()
    {
        return Str::upper(Str::random(40));
    }

    /**
     * Build readable payload for frontend and scanners.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return array<string, mixed>
     */
    public function buildPayload(Reservation $reservation)
    {
        return [
            'reservation_id' => $reservation->id,
            'token' => $reservation->qr_code_token,
            'meal_count' => (int) $reservation->meal_count,
            'valid_until' => optional($reservation->qr_expiry_time)->toDateTimeString(),
            'status' => $reservation->status,
        ];
    }

    /**
     * Determine if QR token is already expired.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return bool
     */
    public function isExpired(Reservation $reservation)
    {
        if (!$reservation->qr_expiry_time) {
            return false;
        }

        return now()->greaterThan($reservation->qr_expiry_time);
    }
}
