<?php

namespace Fickrr\Http\Controllers;

use Fickrr\Services\Physical\CheckoutValidationException;
use Fickrr\Services\Physical\InsufficientStockException;
use Fickrr\Services\Physical\PhysicalOrderService;
use Fickrr\Services\Physical\ShippingCalculator;
use Fickrr\Services\Physical\StripeChargePolicy;
use Fickrr\Support\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

class PhysicalCheckoutController extends Controller
{
    public function store(Request $request, PhysicalOrderService $orders)
    {
        if (!Store::isPhysical()) {
            return redirect('/checkout');
        }

        $lines = [];
        foreach ((array) $request->input('lines', []) as $line) {
            if (!is_array($line)) {
                continue;
            }
            $lines[] = [
                'item_id' => $line['item_id'] ?? null,
                'variant_id' => $line['variant_id'] ?? null,
                'quantity' => $line['quantity'] ?? 1,
            ];
        }

        try {
            $order = $orders->place($request->all(), $lines, [
                'source' => 'checkout',
                'user_id' => Auth::check() ? Auth::id() : null,
                'currency' => config('store.currency', 'USD'),
            ]);
        } catch (CheckoutValidationException $e) {
            return redirect()->back()->withErrors($e->errors)->withInput();
        } catch (InsufficientStockException $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }

        $this->clearPendingCart();

        return redirect()->route('physical.order.show', ['orderNumber' => $order->order_number])
            ->with('success', __('Order received. We will ship it to the address you entered.'));
    }

    public function show(string $orderNumber, PhysicalOrderService $orders, ShippingCalculator $shipping)
    {
        $order = $orders->findByNumber($orderNumber);
        if (!$order) {
            abort(404);
        }

        return view('pages.physical-order', [
            'order' => $order,
            'items' => $orders->items((int) $order->id),
            'shipping' => $shipping,
        ]);
    }

    /**
     * Stripe return is inert unless production has explicitly enabled charges.
     * It never trusts a success query string on its own.
     */
    public function stripeReturn(Request $request, PhysicalOrderService $orders)
    {
        if (!StripeChargePolicy::chargesEnabled()) {
            abort(404);
        }

        $sessionId = (string) $request->query('session_id', '');
        $secret = (string) config('store.stripe_secret');
        if ($sessionId === '' || $secret === '') {
            abort(404);
        }

        \Stripe\Stripe::setApiKey($secret);
        $session = \Stripe\Checkout\Session::retrieve($sessionId);
        $orderNumber = $session->metadata->order_number ?? $session->client_reference_id ?? null;
        if (!$orderNumber || ($session->payment_status ?? '') !== 'paid') {
            return redirect('/')->with('error', __('Payment was not completed. No charge was recorded as paid.'));
        }

        $order = $orders->findByNumber((string) $orderNumber);
        if (!$order) {
            abort(404);
        }

        if ($order->fulfillment_status === 'pending') {
            DB::table('physical_orders')->where('id', $order->id)->update([
                'payment_status' => 'paid',
                'payment_method' => 'stripe',
                'updated_at' => now(),
            ]);
            $orders->transition((int) $order->id, 'confirmed');
        }

        return redirect()->route('physical.order.show', ['orderNumber' => $order->order_number]);
    }

    private function clearPendingCart(): void
    {
        if (!Schema::hasTable('item_order')) {
            return;
        }

        DB::table('item_order')
            ->where('session_id', Session::getId())
            ->where('order_status', 'pending')
            ->delete();
    }
}
