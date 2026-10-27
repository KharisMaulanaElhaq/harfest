<?php

namespace App\Enums;

enum UserRole: string
{
    case User = 'user';
    case Admin = 'admin';
    case Staff = 'staff';

    public function label(): string
    {
        return match ($this) {
            self::User => 'Pengguna',
            self::Admin => 'Admin',
            self::Staff => 'Staff',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::User => 'blue',
            self::Admin => 'purple',
            self::Staff => 'amber',
        };
    }
}
