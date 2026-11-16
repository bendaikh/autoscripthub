<?php

namespace Fickrr\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Fickrr\Http\Controllers\Controller;
use Fickrr\Models\LandingCustomer;
use Fickrr\Models\Settings;

class LandingCustomerController extends Controller
{
    /**
     * Display all landing page customers
     */
    public function index()
    {
        $data['customers'] = LandingCustomer::orderBy('last_purchase_at', 'desc')->get();
        $sid = 1;
        $data['setting'] = Settings::editGeneral($sid);
        // recent orders, with the bonuses each one received
        $data['orders'] = \DB::table('landing_page_orders as o')
            ->leftJoin('landing_pages as lp', 'lp.lp_id', '=', 'o.lp_id')
            ->orderByDesc('o.id')->limit(150)
            ->get(['o.*', 'lp.lp_title']);
        
        return view('admin.landing-customers.index', $data);
    }

    /**
     * Resend the purchase email (download page + bonus links) of a paid order.
     */
    public function resend($id)
    {
        $order = \DB::table('landing_page_orders')->where('id', (int) $id)->first();
        if (!$order || $order->payment_status !== 'completed') {
            \Session::flash('error', 'Only paid orders can be resent.');
            return redirect()->route('admin.landing-customers');
        }
        \Fickrr\Support\LandingDelivery::recordBonuses($order);
        if (\Fickrr\Support\LandingDelivery::sendEmail($order)) {
            \Session::flash('success', 'Purchase email resent to ' . $order->customer_email . '.');
        } else {
            \Session::flash('error', 'The email could not be sent (check the mail settings). Download page: ' . url('/landing/success/' . $order->order_token));
        }
        return redirect()->route('admin.landing-customers');
    }

    /**
     * Delete customer
     */
    public function delete($id)
    {
        $customer = LandingCustomer::find($id);
        
        if ($customer) {
            $customer->delete();
            \Session::flash('success', 'Customer deleted successfully!');
        } else {
            \Session::flash('error', 'Customer not found');
        }

        return redirect()->route('admin.landing-customers');
    }
}
