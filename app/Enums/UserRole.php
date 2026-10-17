<?php

namespace App\Enums;

enum UserRole: string
{
    case User = 'user';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::User => 'Pengguna',
            self::Admin => 'Admin',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::User => 'blue',
            self::Admin => 'purple',
        };
    }
}
