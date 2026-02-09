<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class License extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'device_id',
        'serial_number',
        'starts_at',
        'last_checked_date',
        'last_checked_device_id',
        'emergency',
        'expires_at',
        'license_type',
        'product_package',
        'user_enable',
        'max_connection_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'last_checked_date' => 'datetime',
            'expires_at' => 'datetime',
            'emergency' => 'boolean',
            'user_enable' => 'boolean',
            'max_connection_count' => 'integer',
        ];
    }

    /**
     * Get the user that owns the license.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the logs for the license.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(LicLog::class);
    }

    /**
     * Check if the license is valid.
     */
    public function isValid(): bool
    {
        if (!$this->user_enable) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Get days left until expiration.
     */
    public function getDaysLeft(): ?int
    {
        if (!$this->expires_at) {
            return null; // Lifetime license
        }

        return max(0, now()->diffInDays($this->expires_at, false));
    }

    /**
     * Check if license is expiring soon (less than 10 days).
     */
    public function isExpiringSoon(): bool
    {
        $daysLeft = $this->getDaysLeft();
        return $daysLeft !== null && $daysLeft < 10 && $daysLeft > 0;
    }
}
