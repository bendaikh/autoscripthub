<?php

namespace Fickrr\Http\Controllers;

use Illuminate\Http\Request;
use Fickrr\Models\Members;
use Fickrr\Models\Settings;
use Fickrr\Models\Items;
use Fickrr\Models\EmailTemplate;
use Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Auth;
use Illuminate\Validation\Rule;
use URL;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\File;
use Redirect;
use Storage;
use Cache;
use Illuminate\Support\Facades\DB;
use Session;
use AmrShawky\LaravelCurrency\Facade\Currency;
use GuzzleHttp\Client;
use Carbon\Carbon;
use PDF;
use Stripe;

class StripeController extends Controller
{
    
	
	
	
	public function stripe_index()
	{
	  
	 return view('stripe');

    }

    
	
	public function afterPayment()
    {
        echo 'Payment Has been Received';
    }

	
	public function stripe_subscription_index()
	{
	  
	 return view('stripe-subscription');

    }
}
