<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsPricing extends Model
{
    use HasFactory;

    // Explicit table name to match migration
    protected $table = 'sms_pricing';

    protected $fillable = [
        'country_code',
        'carrier',
        'inbound_cost',
        'outbound_cost',
        'currency',
        'is_active'
    ];

    protected $casts = [
        'inbound_cost' => 'decimal:4',
        'outbound_cost' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    // Get pricing for a specific country and carrier
    public static function getPricing($countryCode, $carrier = null)
    {
        return static::where('country_code', $countryCode)
            ->where('carrier', $carrier)
            ->where('is_active', true)
            ->first();
    }

    // Get default US pricing
    public static function getDefaultPricing()
    {
        return static::where('country_code', 'US')
            ->where('carrier', null)
            ->where('is_active', true)
            ->first();
    }

    // Calculate cost for a message
    public function calculateCost($messageLength = 1)
    {
        // Twilio charges per message segment (160 characters = 1 segment)
        $segments = ceil($messageLength / 160);
        return $this->outbound_cost * $segments;
    }

    // Get formatted cost
    public function getFormattedCost($cost = null)
    {
        $cost = $cost ?? $this->outbound_cost;
        return '$' . number_format($cost, 4);
    }
}


