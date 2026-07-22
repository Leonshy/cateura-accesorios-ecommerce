<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingSetting extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'free_shipping_enabled', 'free_shipping_min_amount',
        'store_pickup_enabled', 'envio_propio_enabled', 'zones',
        'aex_enabled', 'aex_api_user', 'aex_api_password',
        'aex_environment', 'aex_is_validated', 'aex_webhook_url',
    ];

    protected $casts = [
        'zones'                 => 'array',
        'free_shipping_enabled' => 'boolean',
        'store_pickup_enabled'  => 'boolean',
        'envio_propio_enabled'  => 'boolean',
        'aex_enabled'           => 'boolean',
        'aex_is_validated'      => 'boolean',
    ];

    public static function getDefault(): self
    {
        return static::firstOrCreate(['id' => 'default']);
    }

    public function calculateShipping(string $department, string $city, float $subtotal): array
    {
        $zones = $this->zones ?? [];

        foreach ($zones as $zone) {
            $zoneId   = $zone['departmentId'] ?? '';
            $isActive = (bool) ($zone['active'] ?? false);

            if (strtolower($zoneId) !== strtolower($department) || !$isActive) {
                continue;
            }

            $baseCost         = (float) ($zone['price'] ?? 0);
            $baseDelivery     = $zone['deliveryTime'] ?? '';
            $baseFreeEligible = (bool) ($zone['freeShippingEligible'] ?? true);

            $inactiveCities = array_map('strtolower', $zone['inactiveCities'] ?? []);
            if (in_array(strtolower($city), $inactiveCities)) {
                return ['cost' => 0, 'delivery_time' => '', 'free' => false, 'unavailable' => true];
            }

            foreach ($zone['customRates'] ?? [] as $rate) {
                $districts = array_map('strtolower', $rate['districtIds'] ?? []);
                if (in_array(strtolower($city), $districts)) {
                    $freeEligible = (bool) ($rate['freeShippingEligible'] ?? $baseFreeEligible);
                    $free = $freeEligible
                        && $this->free_shipping_enabled
                        && $subtotal >= $this->free_shipping_min_amount;

                    return [
                        'cost'          => $free ? 0 : (float) ($rate['price'] ?? 0),
                        'delivery_time' => $rate['deliveryTime'] ?? $baseDelivery,
                        'free'          => $free,
                    ];
                }
            }

            $free = $baseFreeEligible
                && $this->free_shipping_enabled
                && $subtotal >= $this->free_shipping_min_amount;

            return [
                'cost'          => $free ? 0 : $baseCost,
                'delivery_time' => $baseDelivery,
                'free'          => $free,
            ];
        }

        return ['cost' => 0, 'delivery_time' => '', 'free' => false, 'unavailable' => true];
    }

    public function getActiveDepartmentIds(): array
    {
        return collect($this->zones ?? [])
            ->filter(fn ($z) => (bool) ($z['active'] ?? false))
            ->pluck('departmentId')
            ->filter()
            ->values()
            ->toArray();
    }
}
