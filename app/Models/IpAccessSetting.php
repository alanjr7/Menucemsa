<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IpAccessSetting extends Model
{
    use HasFactory;

    protected $fillable = ['mode', 'is_active'];

    public const MODE_ALL = 'all';
    public const MODE_SPECIFIC = 'specific';

    public static function getCurrentMode(): string
    {
        $setting = self::first();
        return $setting?->mode ?? self::MODE_ALL;
    }

    public static function isSpecificMode(): bool
    {
        return self::getCurrentMode() === self::MODE_SPECIFIC;
    }

    public static function setMode(string $mode): void
    {
        self::updateOrCreate(
            ['id' => 1],
            ['mode' => $mode, 'is_active' => true]
        );
    }
}
