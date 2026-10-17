<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PendingPayment = 'pending_payment';
    case WaitingVerification = 'waiting_verification';
    case Paid = 'paid';
    case Rejected = 'rejected';
    case Expired = 'expired';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PendingPayment => 'Menunggu Pembayaran',
            self::WaitingVerification => 'Menunggu Verifikasi',
            self::Paid => 'Lunas',
            self::Rejected => 'Ditolak',
            self::Expired => 'Kedaluwarsa',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PendingPayment => 'yellow',
            self::WaitingVerification => 'blue',
            self::Paid => 'green',
            self::Rejected => 'red',
            self::Expired => 'gray',
            self::Cancelled => 'gray',
        };
    }

    public function countsTowardQuota(): bool
    {
        return in_array($this, [
            self::PendingPayment,
            self::WaitingVerification,
            self::Paid,
        ], true);
    }

    /**
     * Return array of status string values that count toward ticket quota.
     *
     * @return array<int, string>
     */
    public static function countingStatuses(): array
    {
        return [
            self::PendingPayment->value,
            self::WaitingVerification->value,
            self::Paid->value,
        ];
    }
}
