<?php

namespace Fickrr\Services\Physical;

use Fickrr\Mail\PhysicalOrderMail;
use Fickrr\Support\Store;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class PhysicalOrderNotifier
{
    /**
     * @param  array<int, object>  $items
     */
    public function confirmation(object $order, array $items): void
    {
        $this->send('confirmation', $order, $items);
    }

    /**
     * @param  array<int, object>  $items
     */
    public function shipped(object $order, array $items): void
    {
        $this->send('shipped', $order, $items);
    }

    /**
     * @param  array<int, object>  $items
     */
    private function send(string $kind, object $order, array $items): void
    {
        $email = trim((string) ($order->customer_email ?? ''));
        if ($email === '') {
            return;
        }

        try {
            Mail::to($email)->send(new PhysicalOrderMail($kind, [
                'order' => $order,
                'items' => $items,
                'storeName' => Store::name(),
                'storeUrl' => Store::url(),
                'supportEmail' => Store::supportEmail(),
            ]));

            $admin = trim((string) config('store.admin_email'));
            if ($admin !== '' && strcasecmp($admin, $email) !== 0) {
                Mail::to($admin)->send(new PhysicalOrderMail($kind, [
                    'order' => $order,
                    'items' => $items,
                    'storeName' => Store::name(),
                    'storeUrl' => Store::url(),
                    'supportEmail' => Store::supportEmail(),
                    'adminCopy' => true,
                ]));
            }
        } catch (Throwable $e) {
            Log::warning('Physical order email was not sent: ' . $e->getMessage(), [
                'order' => $order->order_number ?? null,
                'kind' => $kind,
            ]);
        }
    }
}
