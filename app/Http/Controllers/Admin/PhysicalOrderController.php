<?php

namespace Fickrr\Http\Controllers\Admin;

use Fickrr\Http\Controllers\Controller;
use Fickrr\Services\Physical\InvalidStatusTransitionException;
use Fickrr\Services\Physical\InsufficientStockException;
use Fickrr\Services\Physical\PhysicalOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PhysicalOrderController extends Controller
{
    public function index(Request $request)
    {
        $status = (string) $request->query('status', '');
        $query = DB::table('physical_orders')->orderByDesc('id');
        if (in_array($status, PhysicalOrderService::STATUSES, true)) {
            $query->where('fulfillment_status', $status);
        }

        $data['orders'] = $query->paginate(30)->withQueryString();
        $data['status'] = $status;
        $data['statuses'] = PhysicalOrderService::STATUSES;

        return view('admin.physical-orders.index', $data);
    }

    public function show(int $id, PhysicalOrderService $orders)
    {
        $data['order'] = $orders->find($id);
        $data['items'] = $orders->items($id);
        $data['statuses'] = $this->nextStatuses($data['order']->fulfillment_status);

        return view('admin.physical-orders.show', $data);
    }

    public function update(Request $request, int $id, PhysicalOrderService $orders)
    {
        $request->validate([
            'fulfillment_status' => 'required|in:' . implode(',', PhysicalOrderService::STATUSES),
            'tracking_number' => 'nullable|string|max:120',
            'admin_note' => 'nullable|string|max:2000',
        ]);

        try {
            $orders->transition(
                $id,
                (string) $request->input('fulfillment_status'),
                $request->input('tracking_number'),
                $request->input('admin_note')
            );
        } catch (InsufficientStockException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (InvalidStatusTransitionException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', __('Order status updated.'));
    }

    /**
     * @return array<int, string>
     */
    private function nextStatuses(string $current): array
    {
        $map = [
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['shipped', 'cancelled'],
            'shipped' => ['delivered', 'returned'],
            'delivered' => ['returned'],
            'cancelled' => [],
            'returned' => [],
        ];

        return $map[$current] ?? [];
    }
}
