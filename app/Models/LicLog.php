<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicLog extends Model
{
    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'license_id',
        'user_id',
        'level',
        'message',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the license that owns the log.
     */
    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    /**
     * Get the user that owns the log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log an info message.
     */
    public static function info(string $message, ?int $licenseId = null, ?int $userId = null): void
    {
        self::create([
            'license_id' => $licenseId,
            'user_id' => $userId,
            'level' => 'info',
            'message' => $message,
        ]);
    }

    /**
     * Log a debug message.
     */
    public static function debug(string $message, ?int $licenseId = null, ?int $userId = null): void
    {
        self::create([
            'license_id' => $licenseId,
            'user_id' => $userId,
            'level' => 'debug',
            'message' => $message,
        ]);
    }

    /**
     * Log an error message.
     */
    public static function error(string $message, ?int $licenseId = null, ?int $userId = null): void
    {
        self::create([
            'license_id' => $licenseId,
            'user_id' => $userId,
            'level' => 'error',
            'message' => $message,
        ]);
    }
}
