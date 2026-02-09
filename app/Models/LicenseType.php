<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicenseType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    /**
     * Get active license types for dropdown.
     */
    public static function getActiveTypes()
    {
        return self::where('active', true)->orderBy('name')->get();
    }

    /**
     * Get license types as key-value array.
     */
    public static function getTypesArray()
    {
        return self::where('active', true)
            ->orderBy('name')
            ->pluck('name', 'code')
            ->toArray();
    }
}
