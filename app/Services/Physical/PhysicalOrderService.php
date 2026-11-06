<?php

namespace Fickrr\Services\Physical;

use Fickrr\Support\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PhysicalOrderService
{
    public const STATUSES = [
        'pending',
        'confirmed',
        'shipped',
        'delivered',
        'cancelled',
        'returned',
    ];

    private const TRANSITIONS = [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['shipped', 'cancelled'],
        'shipped' => ['delivered', 'returned'],
        'delivered' => ['returned'],
        'cancelled' => [],
        'returned' => [],
    ];

    public function __construct(
        private InventoryService $inventory,
        private ShippingCalculator $shipping,
        private PhysicalOrderNotifier $notifier
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'min:2', 'max:120'],
            'customer_phone' => ['required', 'string', 'min:6', 'max:30', 'regex:/^[0-9+\-\s().]+$/'],
            'customer_email' => ['required', 'email', 'max:190'],
            'address' => ['required', 'string', 'min:5', 'max:500'],
            'city' => ['required', 'string', 'min:2', 'max:120'],
            'country' => ['required', 'string', 'min:2', 'max:80'],
            'payment_method' => ['required', 'in:stripe,paypal'],
        ];
    }

    /**
     * @param  array<string, mixed>  $input
     * @param  array<int, array<string, mixed>>  $lines
     */
    public function place(array $input, array $lines, array $context = []): object
    {
        if (($input['payment_method'] ?? '') === 'cod') {
            throw new CheckoutValidationException([
                'payment_method' => ['Cash on delivery is not available. Pay by card or PayPal.'],
            ]);
        }

        $customer = $this->validateCustomer($input);
        if ($lines === []) {
            throw new CheckoutValidationException([
                'lines' => ['Add at least one product.'],
            ]);
        }

        $resolved = [];
        foreach ($lines as $line) {
            $resolved[] = $this->resolveLine($line);
        }

        $quote = $this->shipping->quote($resolved);
        $now = now();
        $orderNumber = $this->makeOrderNumber();

        $orderId = DB::transaction(function () use ($customer, $resolved, $quote, $context, $now, $orderNumber) {
            $orderId = DB::table('physical_orders')->insertGetId([
                'order_number' => $orderNumber,
                'source' => $context['source'] ?? 'checkout',
                'user_id' => $context['user_id'] ?? null,
                'lp_id' => $context['lp_id'] ?? null,
                'landing_order_token' => $context['landing_order_token'] ?? null,
                'customer_name' => $customer['customer_name'],
                'customer_email' => $customer['customer_email'],
                'customer_phone' => $customer['customer_phone'],
                'address' => $customer['address'],
                'city' => $customer['city'],
                'country' => $customer['country'],
                'payment_method' => $customer['payment_method'],
                'payment_status' => 'pending',
                'fulfillment_status' => 'pending',
                'subtotal' => $quote['subtotal'],
                'shipping_fee' => $quote['shipping_fee'],
                'total' => $quote['total'],
                'currency' => $context['currency'] ?? config('store.currency', 'USD'),
                'stock_committed' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($resolved as $line) {
                DB::table('physical_order_items')->insert([
                    'physical_order_id' => $orderId,
                    'item_id' => $line['item_id'],
                    'variant_id' => $line['variant_id'],
                    'lp_id' => $line['lp_id'],
                    'sku' => $line['sku'],
                    'name' => $line['name'],
                    'variant_name' => $line['variant_name'],
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'shipping_fee' => $line['shipping_fee'],
                    'weight_grams' => $line['weight_grams'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            return $orderId;
        });

        if (!empty($context['landing_order_token']) && Schema::hasTable('landing_page_orders')) {
            $this->insertFiltered('landing_page_orders', [
                'lp_id' => $context['lp_id'] ?? null,
                'customer_email' => $customer['customer_email'],
                'customer_name' => $customer['customer_name'],
                'customer_phone' => $customer['customer_phone'],
                'address' => $customer['address'],
                'city' => $customer['city'],
                'country' => $customer['country'],
                'quantity' => $resolved[0]['quantity'] ?? 1,
                'shipping_fee' => $quote['shipping_fee'],
                'fulfillment_status' => 'pending',
                'variant_id' => $resolved[0]['variant_id'] ?? null,
                'physical_order_id' => $orderId,
                'stock_committed' => 0,
                'order_token' => $context['landing_order_token'],
                'amount' => $quote['total'],
                'currency' => $context['currency'] ?? config('store.currency', 'USD'),
                'payment_method' => $customer['payment_method'],
                'payment_status' => 'pending',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $order = $this->find($orderId);
        $this->notifier->confirmation($order, $this->items($orderId));

        return $order;
    }

    public function handleLandingRequest(Request $request, object $landingPage)
    {
        try {
            $order = $this->placeLanding($landingPage, $request->all());
        } catch (CheckoutValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage(), 'errors' => $e->errors], 422);
            }

            return redirect()->back()->withErrors($e->errors)->withInput();
        } catch (InsufficientStockException $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }

            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }

        $payment = app(OnlinePaymentService::class)->begin($order);
        $url = $payment['redirect'] ?: url('/landing/success/' . $order->landing_order_token);
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'redirect' => $url, 'paid' => false]);
        }

        return redirect()->away($url);
    }

    /**
     * @param  array<string, mixed>  $input
     */
    public function placeLanding(object $landingPage, array $input): object
    {
        $quantity = (int) ($input['quantity'] ?? 1);
        $token = 'LP-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6));

        return $this->place($input, [[
            'lp_id' => (int) $landingPage->lp_id,
            'variant_id' => $input['variant_id'] ?? null,
            'quantity' => $quantity,
        ]], [
            'source' => 'landing',
            'lp_id' => (int) $landingPage->lp_id,
            'landing_order_token' => $token,
            'currency' => $landingPage->lp_currency ?? config('store.currency', 'USD'),
        ]);
    }

    public function transition(int $orderId, string $status, ?string $trackingNumber = null, ?string $note = null): object
    {
        $status = strtolower(trim($status));
        if (!in_array($status, self::STATUSES, true)) {
            throw new InvalidStatusTransitionException('Unknown order status.');
        }

        $order = $this->find($orderId);
        $from = (string) $order->fulfillment_status;
        if ($from === $status) {
            return $order;
        }

        if ($status === 'confirmed' && ($order->payment_status ?? '') !== 'paid') {
            throw new InvalidStatusTransitionException('Online payment is required before this order can be confirmed.');
        }

        $allowed = self::TRANSITIONS[$from] ?? [];
        if (!in_array($status, $allowed, true)) {
            throw new InvalidStatusTransitionException('An order that is ' . $from . ' cannot move to ' . $status . '.');
        }

        DB::transaction(function () use ($order, $status, $trackingNumber, $note) {
            if ($status === 'confirmed') {
                $this->commitStock($order);
            }

            if (in_array($status, ['cancelled', 'returned'], true) && (int) $order->stock_committed === 1) {
                $this->releaseStock($order);
            }

            $update = [
                'fulfillment_status' => $status,
                'updated_at' => now(),
            ];

            if ($note !== null) {
                $update['admin_note'] = $note;
            }

            if ($status === 'confirmed') {
                $update['confirmed_at'] = now();
                $update['stock_committed'] = 1;
            }
            if ($status === 'shipped') {
                $update['shipped_at'] = now();
                $update['tracking_number'] = $trackingNumber !== null && trim($trackingNumber) !== ''
                    ? trim($trackingNumber)
                    : $order->tracking_number;
            }
            if ($status === 'delivered') {
                $update['delivered_at'] = now();
            }
            if ($status === 'cancelled') {
                $update['cancelled_at'] = now();
                $update['stock_committed'] = 0;
                $update['payment_status'] = 'cancelled';
            }
            if ($status === 'returned') {
                $update['returned_at'] = now();
                $update['stock_committed'] = 0;
                $update['payment_status'] = 'returned';
            }

            DB::table('physical_orders')->where('id', $order->id)->update($update);
            $this->syncLandingStatus($order, $status);
        });

        $fresh = $this->find($orderId);
        if ($status === 'shipped') {
            $this->notifier->shipped($fresh, $this->items($orderId));
        }

        return $fresh;
    }

    /**
     * Record a verified Stripe or PayPal payment, then confirm the order.
     * Stock is reduced only by that confirmation.
     */
    public function recordPayment(int $orderId, string $reference): object
    {
        $reference = trim($reference);
        if ($reference === '') {
            throw new CheckoutValidationException([
                'payment' => ['A payment reference from the card processor or PayPal is required.'],
            ]);
        }

        $order = $this->find($orderId);
        if (!in_array($order->payment_method, ['stripe', 'paypal'], true)) {
            throw new CheckoutValidationException([
                'payment_method' => ['Pay by card or PayPal before the order can be confirmed.'],
            ]);
        }

        if (($order->payment_status ?? '') !== 'paid') {
            $update = [
                'payment_status' => 'paid',
                'updated_at' => now(),
            ];
            if (Schema::hasColumn('physical_orders', 'payment_reference')) {
                $update['payment_reference'] = $reference;
            }
            DB::table('physical_orders')->where('id', $orderId)->update($update);
            $order = $this->find($orderId);
        }

        if ($order->fulfillment_status === 'pending') {
            return $this->transition($orderId, 'confirmed');
        }

        return $order;
    }

    public function find(int $orderId): object
    {
        $order = DB::table('physical_orders')->where('id', $orderId)->first();
        if (!$order) {
            throw new InvalidStatusTransitionException('Order not found.');
        }

        return $order;
    }

    public function findByNumber(string $orderNumber): ?object
    {
        return DB::table('physical_orders')->where('order_number', $orderNumber)->first();
    }

    /**
     * @return array<int, object>
     */
    public function items(int $orderId): array
    {
        return DB::table('physical_order_items')->where('physical_order_id', $orderId)->get()->all();
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            'returned' => 'Returned',
            default => ucfirst($status),
        };
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, string>
     */
    public function validateCustomer(array $input): array
    {
        $validator = validator($input, self::rules());
        if ($validator->fails()) {
            throw new CheckoutValidationException($validator->errors()->toArray());
        }

        $data = $validator->validated();
        $data['customer_name'] = trim($data['customer_name']);
        $data['customer_phone'] = trim($data['customer_phone']);
        $data['customer_email'] = trim($data['customer_email']);
        $data['address'] = trim($data['address']);
        $data['city'] = trim($data['city']);
        $data['country'] = trim($data['country']);

        return $data;
    }

    /**
     * @param  array<string, mixed>  $line
     * @return array<string, mixed>
     */
    private function resolveLine(array $line): array
    {
        $qty = (int) ($line['quantity'] ?? 1);
        if ($qty < 1 || $qty > 99) {
            throw new CheckoutValidationException([
                'quantity' => ['Quantity must be between 1 and 99.'],
            ]);
        }

        $variantId = !empty($line['variant_id']) ? (int) $line['variant_id'] : null;

        if (!empty($line['lp_id'])) {
            return $this->resolveLandingLine((int) $line['lp_id'], $variantId, $qty);
        }

        if (empty($line['item_id'])) {
            throw new CheckoutValidationException([
                'lines' => ['Each line needs a product.'],
            ]);
        }

        return $this->resolveItemLine((int) $line['item_id'], $variantId, $qty);
    }

    /**
     * @return array<string, mixed>
     */
    private function resolveItemLine(int $itemId, ?int $variantId, int $qty): array
    {
        $item = DB::table('items')->where('item_id', $itemId)->first();
        if (!$item || ($item->drop_status ?? 'no') === 'yes' || (int) ($item->item_status ?? 1) !== 1) {
            throw new CheckoutValidationException([
                'lines' => ['A product in this order is not available.'],
            ]);
        }

        $variant = $this->variantFor($variantId, $itemId, null);
        $track = $this->inventory->tracksItem($item);
        $available = $this->inventory->availableForItem($itemId, $variantId, $track);
        if ($available < $qty) {
            throw new InsufficientStockException('Not enough stock for ' . $item->item_name . '.');
        }

        return $this->linePayload(
            $item->item_name,
            $variant,
            $item->sku ?? null,
            $variant && $variant->price !== null ? (float) $variant->price : (float) $item->regular_price,
            (float) ($item->shipping_fee ?? 0),
            isset($item->weight_grams) ? (int) $item->weight_grams : null,
            $qty,
            $itemId,
            null,
            $variantId,
            (bool) ($item->requires_shipping ?? true)
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function resolveLandingLine(int $lpId, ?int $variantId, int $qty): array
    {
        $page = DB::table('landing_pages')->where('lp_id', $lpId)->first();
        if (!$page || (int) ($page->lp_status ?? 1) !== 1) {
            throw new CheckoutValidationException([
                'lines' => ['This product is not available.'],
            ]);
        }

        $catalogId = (int) ($page->lp_product_id ?? 0);
        if ($catalogId > 0) {
            return $this->resolveItemLine($catalogId, $variantId, $qty);
        }

        $variant = $this->variantFor($variantId, null, $lpId);
        $track = $this->inventory->tracksLanding($page);
        $available = $this->inventory->availableForLanding($lpId, $variantId, $track);
        if ($available < $qty) {
            throw new InsufficientStockException('Not enough stock for ' . $page->lp_title . '.');
        }

        return $this->linePayload(
            $page->lp_title,
            $variant,
            $page->lp_sku ?? null,
            $variant && $variant->price !== null ? (float) $variant->price : (float) $page->lp_price,
            (float) ($page->lp_shipping_fee ?? 0),
            isset($page->lp_weight_grams) ? (int) $page->lp_weight_grams : null,
            $qty,
            null,
            $lpId,
            $variantId,
            true
        );
    }

    private function variantFor(?int $variantId, ?int $itemId, ?int $lpId): ?object
    {
        if (!$variantId) {
            return null;
        }

        $query = DB::table('item_variants')->where('id', $variantId)->where('is_active', 1);
        if ($itemId) {
            $query->where('item_id', $itemId);
        }
        if ($lpId) {
            $query->where('lp_id', $lpId);
        }

        $variant = $query->first();
        if (!$variant) {
            throw new CheckoutValidationException([
                'variant_id' => ['Choose a valid product option.'],
            ]);
        }

        return $variant;
    }

    /**
     * @return array<string, mixed>
     */
    private function linePayload(string $name, ?object $variant, ?string $sku, float $unitPrice, float $shippingFee, ?int $weight, int $qty, ?int $itemId, ?int $lpId, ?int $variantId, bool $requiresShipping): array
    {
        if ($variant) {
            $sku = $variant->sku ?: $sku;
            if ($variant->weight_grams !== null) {
                $weight = (int) $variant->weight_grams;
            }
        }

        return [
            'name' => $name,
            'variant_name' => $variant->name ?? null,
            'sku' => $sku,
            'unit_price' => round($unitPrice, 2),
            'shipping_fee' => round($shippingFee, 2),
            'weight_grams' => $weight,
            'quantity' => $qty,
            'item_id' => $itemId,
            'lp_id' => $lpId,
            'variant_id' => $variantId,
            'requires_shipping' => $requiresShipping,
        ];
    }

    private function commitStock(object $order): void
    {
        if ((int) $order->stock_committed === 1) {
            return;
        }

        foreach ($this->items((int) $order->id) as $line) {
            if (!empty($line->lp_id) && empty($line->item_id)) {
                $page = DB::table('landing_pages')->where('lp_id', $line->lp_id)->first();
                $this->inventory->commitLanding(
                    (int) $line->lp_id,
                    $line->variant_id ? (int) $line->variant_id : null,
                    (int) $line->quantity,
                    $page ? $this->inventory->tracksLanding($page) : true
                );
                continue;
            }

            $item = DB::table('items')->where('item_id', $line->item_id)->first();
            $this->inventory->commitItem(
                (int) $line->item_id,
                $line->variant_id ? (int) $line->variant_id : null,
                (int) $line->quantity,
                $item ? $this->inventory->tracksItem($item) : true
            );
        }
    }

    private function releaseStock(object $order): void
    {
        if ((int) $order->stock_committed !== 1) {
            return;
        }

        foreach ($this->items((int) $order->id) as $line) {
            if (!empty($line->lp_id) && empty($line->item_id)) {
                $page = DB::table('landing_pages')->where('lp_id', $line->lp_id)->first();
                $this->inventory->releaseLanding(
                    (int) $line->lp_id,
                    $line->variant_id ? (int) $line->variant_id : null,
                    (int) $line->quantity,
                    $page ? $this->inventory->tracksLanding($page) : true
                );
                continue;
            }

            $item = DB::table('items')->where('item_id', $line->item_id)->first();
            $this->inventory->releaseItem(
                (int) $line->item_id,
                $line->variant_id ? (int) $line->variant_id : null,
                (int) $line->quantity,
                $item ? $this->inventory->tracksItem($item) : true
            );
        }
    }

    private function syncLandingStatus(object $order, string $status): void
    {
        if (empty($order->landing_order_token) || !Schema::hasTable('landing_page_orders')) {
            return;
        }

        $payment = 'pending';
        if (in_array($status, ['confirmed', 'shipped', 'delivered'], true) && ($order->payment_status ?? '') === 'paid') {
            $payment = 'completed';
        }
        if (in_array($status, ['cancelled', 'returned'], true)) {
            $payment = $status;
        }

        $this->insertFilteredUpdate('landing_page_orders', 'order_token', $order->landing_order_token, [
            'fulfillment_status' => $status,
            'payment_status' => $payment,
            'stock_committed' => in_array($status, ['confirmed', 'shipped', 'delivered'], true) ? 1 : 0,
            'updated_at' => now(),
        ]);
    }

    private function makeOrderNumber(): string
    {
        do {
            $number = 'ES-' . now()->format('ymd') . '-' . Str::upper(Str::random(8));
        } while (DB::table('physical_orders')->where('order_number', $number)->exists());

        return $number;
    }

    private function insertFiltered(string $table, array $data): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        $columns = array_flip(Schema::getColumnListing($table));
        DB::table($table)->insert(array_intersect_key($data, $columns));
    }

    private function insertFilteredUpdate(string $table, string $key, $value, array $data): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        $columns = array_flip(Schema::getColumnListing($table));
        DB::table($table)->where($key, $value)->update(array_intersect_key($data, $columns));
    }
}
