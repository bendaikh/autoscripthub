<?php

namespace Fickrr\Services\Physical;

class ShippingCalculator
{
    /**
     * @param  array<int, array<string, mixed>>  $lines
     * @return array{subtotal: float, shipping_fee: float, total: float, weight_grams: int}
     */
    public function quote(array $lines): array
    {
        $subtotal = 0.0;
        $itemShipping = 0.0;
        $weight = 0;

        foreach ($lines as $line) {
            $qty = max(1, (int) ($line['quantity'] ?? 1));
            $subtotal += ((float) ($line['unit_price'] ?? 0)) * $qty;

            $requires = array_key_exists('requires_shipping', $line) ? (bool) $line['requires_shipping'] : true;
            if (!$requires) {
                continue;
            }

            $itemShipping += ((float) ($line['shipping_fee'] ?? 0)) * $qty;
            $weight += ((int) ($line['weight_grams'] ?? 0)) * $qty;
        }

        $fee = (float) config('store.shipping.flat_fee', 0) + $itemShipping;
        $freeOver = config('store.shipping.free_over');
        if ($freeOver !== null && $freeOver !== '' && $subtotal >= (float) $freeOver) {
            $fee = 0.0;
        }

        $subtotal = round($subtotal, 2);
        $fee = round(max(0, $fee), 2);

        return [
            'subtotal' => $subtotal,
            'shipping_fee' => $fee,
            'total' => round($subtotal + $fee, 2),
            'weight_grams' => $weight,
        ];
    }
}
