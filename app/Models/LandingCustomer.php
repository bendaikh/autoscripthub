<?php

namespace Fickrr\Models;

use Illuminate\Database\Eloquent\Model;

class LandingCustomer extends Model
{
    protected $table = 'landing_customers';
    
    protected $fillable = [
        'email',
        'name',
        'total_purchases',
        'total_spent',
        'first_purchase_at',
        'last_purchase_at',
    ];

    /**
     * Create or update customer record
     */
    public static function createOrUpdateCustomer($email, $amount)
    {
        $customer = self::where('email', $email)->first();

        if ($customer) {
            // Update existing customer
            $customer->total_purchases += 1;
            $customer->total_spent += $amount;
            $customer->last_purchase_at = now();
            $customer->save();
        } else {
            // Create new customer
            $customer = self::create([
                'email' => $email,
                'total_purchases' => 1,
                'total_spent' => $amount,
                'first_purchase_at' => now(),
                'last_purchase_at' => now(),
            ]);
        }

        return $customer;
    }
}
