<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AllowedIp extends Model
{
    use HasFactory;

    protected $fillable = ['ip_address', 'ip_type', 'description', 'is_active', 'created_by'];

    public const TYPE_SINGLE = 'single';
    public const TYPE_RANGE = 'range';

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function isIpAllowed(string $ip): bool
    {
        $allowedIps = self::where('is_active', true)->get();

        foreach ($allowedIps as $allowedIp) {
            if ($allowedIp->ip_type === self::TYPE_SINGLE) {
                if ($allowedIp->ip_address === $ip) {
                    return true;
                }
            } elseif ($allowedIp->ip_type === self::TYPE_RANGE) {
                if (self::ipInCidrRange($ip, $allowedIp->ip_address)) {
                    return true;
                }
            }
        }

        return false;
    }

    private static function ipInCidrRange(string $ip, string $cidr): bool
    {
        if (!str_contains($cidr, '/')) {
            return $ip === $cidr;
        }

        [$subnet, $mask] = explode('/', $cidr);
        $mask = (int) $mask;

        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);

        if ($ipLong === false || $subnetLong === false) {
            return false;
        }

        $maskLong = -1 << (32 - $mask);

        return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
    }

    public static function validateIpFormat(string $ip, string $type): bool
    {
        if ($type === self::TYPE_SINGLE) {
            return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
        }

        if (!str_contains($ip, '/')) {
            return false;
        }

        [$subnet, $mask] = explode('/', $ip);
        $mask = (int) $mask;

        if ($mask < 0 || $mask > 32) {
            return false;
        }

        return filter_var($subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
    }
}
