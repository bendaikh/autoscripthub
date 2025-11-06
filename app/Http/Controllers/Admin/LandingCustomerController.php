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
        
        return view('admin.landing-customers.index', $data);
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
