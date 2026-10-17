<?php

namespace App\Enums;

enum EventStatus: string
{
    case Draft = 'draft';
    case Upcoming = 'upcoming';
    case OnSale = 'on_sale';
    case Finished = 'finished';

    /**
     * Indonesian label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Upcoming => 'Segera',
            self::OnSale => 'Dijual',
            self::Finished => 'Selesai',
        };
    }

    /**
     * Badge color name or CSS class.
     */
    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Upcoming => 'blue',
            self::OnSale => 'yellow',
            self::Finished => 'purple',
        };
    }
}
