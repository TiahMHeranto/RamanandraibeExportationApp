<?php

namespace App\Enum;

enum ShipmentStatus: string
{
    case Draft = 'draft';
    case Confirmed = 'confirmed';
    case InTransit = 'in_transit';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Confirmed => 'Confirmed',
            self::InTransit => 'In transit',
            self::Delivered => 'Delivered',
            self::Cancelled => 'Cancelled',
        };
    }
}
