<?php

namespace Fickrr\Http\Controllers;

use Illuminate\Http\Request;
use Fickrr\Models\Members;
use Fickrr\Models\Settings;
use Fickrr\Models\Items;
use Fickrr\Models\Blog;
use Fickrr\Models\Category;
use Fickrr\Models\Comment;
use Fickrr\Models\Pages;
use Fickrr\Models\Attribute;
use Fickrr\Models\SubCategory;
use Fickrr\Models\Subscription;
use Fickrr\Models\EmailTemplate;
use Fickrr\Models\Currencies;
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
use Twocheckout;
use Twocheckout_Charge;
use Twocheckout_Error;
use Paystack;
use IyzipayBootstrap;
use GuzzleHttp\Client;
use CoinGate\CoinGate;
use Carbon\Carbon;
use MercadoPago;
use Razorpay\Api\Api;
use DGvai\SSLCommerz\SSLCommerz;
use PDF;
use Lunaweb\RecaptchaV3\Facades\RecaptchaV3;
use Mollie\Laravel\Facades\Mollie;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
//use Spatie\Sitemap\SitemapGenerator;
use Midtrans;
use Stripe\StripeClient;
use Cashfree;
use Helper;


class CommonController extends Controller
{
    
	
	
	
    
	
	public function cookie_translate($id)
	{
	
	  Cookie::queue(Cookie::make('translate', $id, 3000));
      return Redirect::back()->withCookie('translate');
	  
	}
	
	public function custom_settings($field)
	{
	   $custom = Settings::customSettings();
		return $custom->$field;
	
	}
	
	public function cookie_currency($id)
	{
	
	  Cookie::queue(Cookie::make('multicurrency', $id, 3000));
      /*return Redirect::route('index')->withCookie('translate');*/
	  return redirect()->back()->withCookie('multicurrency');
	  
	}
	
	public function generate_license($suffix = null) 
	{
    // Default tokens contain no "ambiguous" characters: 1,i,0,o
    if(isset($suffix)){
        // Fewer segments if appending suffix
        $num_segments = 3;
        $segment_chars = 6;
    }else{
        $num_segments = 4;
        $segment_chars = 5;
    }
    $tokens = '0123456789abcdefghijklmnopqrstuvwxyz';
    $license_string = '';
    // Build Default License String
    for ($i = 0; $i < $num_segments; $i++) {
        $segment = '';
        for ($j = 0; $j < $segment_chars; $j++) {
            $segment .= $tokens[rand(0, strlen($tokens)-1)];
        }
        $license_string .= $segment;
        if ($i < ($num_segments - 1)) {
            $license_string .= '-';
        }
    }
    // If provided, convert Suffix
    if(isset($suffix)){
        if(is_numeric($suffix)) {   // Userid provided
            $license_string .= '-'.strtoupper(base_convert($suffix,10,36));
        }else{
            $long = sprintf("%u\n", ip2long($suffix),true);
            if($suffix === long2ip($long) ) {
                $license_string .= '-'.strtoupper(base_convert($long,10,36));
            }else{
                $license_string .= '-'.strtoupper(str_ireplace(' ','-',$suffix));
            }
        }
    }
    return $license_string;
   }
   
   
	
	public function go_checkout(Request $request)
	{
	   $sid = 1;
	   $setting['setting'] = Settings::editGeneral($sid);
	   $additional['setting'] = Settings::editAdditional();
	   $session_id = Session::getId();
	   $codes = $request->input('qty');
	   $names = $request->input('ord_id');
	   $pricetag = $request->input('price');
	   $item_user_id_tag = $request->input('item_user_id');
	   $currency_type = $request->input('currency_type');
	   $currency_type_code = $request->input('currency_type_code');
	   $file_type = $request->input('serial');
	   $order_status = 'pending';
	   $chckin_data = Items::SessionCheck($session_id);
		  if($chckin_data != 0)
		  {
		  $sessiondata = Items::singleSession($session_id);
		   $coupon_type = $sessiondata->coupon_type;
		   $coupon_value = $sessiondata->coupon_value;
		   $coupon_key = $sessiondata->coupon_key;
		   $coupon_id = $sessiondata->coupon_id;
		   $coupon_code = $sessiondata->coupon_code;
		  }
		  else
		  {
		   $coupon_type = "";
		   $coupon_value = "";
		   $coupon_key = "";
		   $coupon_id = "";
		   $coupon_code = "";
		  }
	   
	   foreach( $codes as $index => $code ) 
	   {
	      if($code > 0)
		  {
		        $quantity = $code;
				$order_id = $names[$index];
				$price = $pricetag[$index] * $code;
				$item_user_id = $item_user_id_tag[$index];
				$serial = $file_type[$index];
				$item_price = $pricetag[$index];
				
				if($coupon_type == "fixed")
				{
				     $coupon_price = $price - $coupon_value;
				}
				else if($coupon_type == "percentage")
				{
				   $percent = ($coupon_value * $price) / 100;
				   $coupon_price = $price - $percent;
				}
				else
				{
				   $coupon_price = 0;
				}
				$getmember['views'] = Members::singlevendorData($item_user_id);
	            $user_exclusive_type = $getmember['views']->exclusive_author;
				if($additional['setting']->subscription_mode == 0)
			    {
				   if($user_exclusive_type == 1)
				   {
				   $commission = ($setting['setting']->site_exclusive_commission * $price) / 100;
				   }
				   else
				   {
				   $commission = ($setting['setting']->site_non_exclusive_commission * $price) / 100;
				   }
				   $vendor_amount = $price - $commission;
				   $admin_amount = $commission;
				}
				else
				{
				   
				   $count_mode = Settings::checkuserSubscription($item_user_id);
				   if($count_mode == 1)
				   {
					 $vendor_amount = $price;
					 $admin_amount = 0;
				   }
				   else
				   {
					   if($user_exclusive_type == 1)
					   {
					   $commission = ($setting['setting']->site_exclusive_commission * $price) / 100;
					   }
					   else
					   {
					   $commission = ($setting['setting']->site_non_exclusive_commission * $price) / 100;
					   }
					   $vendor_amount = $price - $commission;
					   $admin_amount = $commission;
				   }
				}
				
	   			$updatedata = array('coupon_key' => $coupon_key, 'coupon_id' => $coupon_id, 'coupon_code' => $coupon_code, 'coupon_type' => $coupon_type, 'coupon_value' => $coupon_value, 'discount_price' => $coupon_price,'item_serial_stock' => $quantity, 'vendor_amount' => $vendor_amount, 'admin_amount' => $admin_amount, 'total_price' => $price, 'currency_type' => $currency_type, 'currency_type_code' => $currency_type_code, 'item_price' => $item_price);        
				
				Items::checkOrder($order_id,$session_id,$order_status,$updatedata);
				
				
	   			
		  }
		  else
		  {
		     return redirect()->back()->with('error', 'Invalid Quantity');
		  }		
	   }
	  return redirect('checkout');	   
	
	}
	
	
	public function add_to_cart($slug)
	{
	   if(!empty(Cookie::get('multicurrency')))
	   {
	   $multicurrency = Cookie::get('multicurrency');
	   }
	   else
	   {
		   $default_count = Currencies::defaultCurrencyCount();
		   if($default_count == 0)
		   { 
			  $multicurrency = "USD";
		   }
		   else
		   {
		     $newcurrency = Currencies::defaultCurrency();
		     $multicurrency =  $newcurrency->currency_code;
		   }
		}   	  
	   $currency = Currencies::getCurrency($multicurrency);
	   $currency_symbol = $currency->currency_symbol;
	   $currency_rate = $currency->currency_rate;
	   
	  $checkitem = Items::singleitemCount($slug);
	  if($checkitem != 0)
	  { 
	          $item['view'] = Items::singleitemData($slug);
			  if($item['view']->file_type == 'serial')
			  {
			     if($item['view']->item_delimiter == 'comma')
                {
                	
					$result_count = substr_count($item['view']->item_serials_list, ","); 
                }
                else
                {
                    $result_count = substr_count($item['view']->item_serials_list, "\n");
                }
				if($result_count != 0)
				{
				   
				   $item_price = $this->price_info($item['view']->item_flash,$item['view']->regular_price);
					  $item_id = $item['view']->item_id;
					  $session_id = Session::getId();
					  $item_name = $item['view']->item_name;
					  $item_user_id = $item['view']->user_id;
					  $item_token = $item['view']->item_token;
					  $additional['setting'] = Settings::editAdditional();
					  $getmember['views'] = Members::singlevendorData($item_user_id);
					  $user_exclusive_type = $getmember['views']->exclusive_author;
					  $regular_license = $additional['setting']->regular_license;
					  $pricetag = $item_price * $currency_rate;
					  $price = round($pricetag,2);
					  $license = 'regular';
					  $start_date = date('Y-m-d');
					  $end_date = date('Y-m-d', strtotime($regular_license));
					  $order_status = 'pending';
					  $sid = 1;
					  $setting['setting'] = Settings::editGeneral($sid);
					  if($additional['setting']->subscription_mode == 0)
					  {
						   if($user_exclusive_type == 1)
						   {
						   $commission = ($setting['setting']->site_exclusive_commission * $price) / 100;
						   }
						   else
						   {
						   $commission = ($setting['setting']->site_non_exclusive_commission * $price) / 100;
						   }
						   $vendor_amount = $price - $commission;
						   $admin_amount = $commission;
						}
						else
						{
						   
						   $count_mode = Settings::checkuserSubscription($item_user_id);
						   if($count_mode == 1)
						   {
							 $vendor_amount = $price;
							 $admin_amount = 0;
						   }
						   else
						   {
							   if($user_exclusive_type == 1)
							   {
							   $commission = ($setting['setting']->site_exclusive_commission * $price) / 100;
							   }
							   else
							   {
							   $commission = ($setting['setting']->site_non_exclusive_commission * $price) / 100;
							   }
							   $vendor_amount = $price - $commission;
							   $admin_amount = $commission;
						   }
						}   
					   
					   
					   $getcount  = Items::getorderCount($item_id,$session_id,$order_status);
					   
					   $savedata = array('session_id' => $session_id, 'item_id' => $item_id, 'item_name' => $item_name, 'item_user_id' => $item_user_id, 'item_token' => $item_token, 'license' => $license, 'start_date' => $start_date, 'end_date' => $end_date, 'item_price' => $price, 'vendor_amount' => $vendor_amount, 'admin_amount' => $admin_amount, 'total_price' => $price, 'order_status' => $order_status, 'item_serial_stock' => 1, 'currency_type' => $currency_symbol, 'currency_type_code' => $multicurrency, 'item_single_price' => $item_price);
					   
					   
					   $updatedata = array('license' => $license, 'start_date' => $start_date, 'end_date' => $end_date, 'item_price' => $price, 'total_price' => $price, 'item_serial_stock' => 1, 'currency_type' => $currency_symbol, 'currency_type_code' => $multicurrency, 'item_single_price' => $item_price);
					   
					   
					   if($additional['setting']->subscription_mode == 0)
					   {
						   if($getcount == 0)
						   {
							  Items::savecartData($savedata);
							 
							  return redirect('cart')->with('success','Item has been added to cart'); 
						   }
						   else
						   {
							  Items::updatecartData($item_id,$session_id,$order_status,$updatedata);
							  
							  return redirect('cart')->with('success','Item has been updated to cart'); 
						   }
					   }
					   else
					   {
						  Items::deletecartremove($session_id);
						  Items::savecartData($savedata);
						  return redirect('cart')->with('success','Item has been updated to cart'); 
					   }
				   
				   
				 }
				 else
				 {
				    return redirect()->back()->with('error', 'License / Serial Key is out of stock'); 
				 } 
				 
			  }
			  else
			  {
					  $item_price = $this->price_info($item['view']->item_flash,$item['view']->regular_price);
					  $item_id = $item['view']->item_id;
					  $session_id = Session::getId();
					  $item_name = $item['view']->item_name;
					  $item_user_id = $item['view']->user_id;
					  $item_token = $item['view']->item_token;
					  $additional['setting'] = Settings::editAdditional();
					  $getmember['views'] = Members::singlevendorData($item_user_id);
					  $user_exclusive_type = $getmember['views']->exclusive_author;
					  $regular_license = $additional['setting']->regular_license;
					  $pricetag = $item_price * $currency_rate;
					  $price = round($pricetag,2);
					  $license = 'regular';
					  $start_date = date('Y-m-d');
					  $end_date = date('Y-m-d', strtotime($regular_license));
					  $order_status = 'pending';
					  $sid = 1;
					  $setting['setting'] = Settings::editGeneral($sid);
					  if($additional['setting']->subscription_mode == 0)
					  {
						   if($user_exclusive_type == 1)
						   {
						   $commission = ($setting['setting']->site_exclusive_commission * $price) / 100;
						   }
						   else
						   {
						   $commission = ($setting['setting']->site_non_exclusive_commission * $price) / 100;
						   }
						   $vendor_amount = $price - $commission;
						   $admin_amount = $commission;
						}
						else
						{
						   
						   $count_mode = Settings::checkuserSubscription($item_user_id);
						   if($count_mode == 1)
						   {
							 $vendor_amount = $price;
							 $admin_amount = 0;
						   }
						   else
						   {
							   if($user_exclusive_type == 1)
							   {
							   $commission = ($setting['setting']->site_exclusive_commission * $price) / 100;
							   }
							   else
							   {
							   $commission = ($setting['setting']->site_non_exclusive_commission * $price) / 100;
							   }
							   $vendor_amount = $price - $commission;
							   $admin_amount = $commission;
						   }
						}   
					   
					   
					   $getcount  = Items::getorderCount($item_id,$session_id,$order_status);
					   
					   $savedata = array('session_id' => $session_id, 'item_id' => $item_id, 'item_name' => $item_name, 'item_user_id' => $item_user_id, 'item_token' => $item_token, 'license' => $license, 'start_date' => $start_date, 'end_date' => $end_date, 'item_price' => $price, 'vendor_amount' => $vendor_amount, 'admin_amount' => $admin_amount, 'total_price' => $price, 'order_status' => $order_status, 'item_serial_stock' => 1, 'currency_type' => $currency_symbol, 'currency_type_code' => $multicurrency, 'item_single_price' => $item_price);
					   
					   
					   $updatedata = array('license' => $license, 'start_date' => $start_date, 'end_date' => $end_date, 'item_price' => $price, 'total_price' => $price, 'item_serial_stock' => 1, 'currency_type' => $currency_symbol, 'currency_type_code' => $multicurrency, 'item_single_price' => $item_price);
					   
					   
					   if($additional['setting']->subscription_mode == 0)
					   {
						   if($getcount == 0)
						   {
							  Items::savecartData($savedata);
							 
							  return redirect('cart')->with('success','Item has been added to cart'); 
						   }
						   else
						   {
							  Items::updatecartData($item_id,$session_id,$order_status,$updatedata);
							  
							  return redirect('cart')->with('success','Item has been updated to cart'); 
						   }
					   }
					   else
					   {
						  Items::deletecartremove($session_id);
						  Items::savecartData($savedata);
						  return redirect('cart')->with('success','Item has been updated to cart'); 
					   }
				
				}	    
			   
	   }
	   else
	   {
	         return redirect()->back()->with('error', 'Invalid Product Data');
	   }
	  
	
	
	}
	
	
	public function view_cart(Request $request)
	{
	  if(!empty(Cookie::get('multicurrency')))
	   {
	   $multicurrency = Cookie::get('multicurrency');
	   }
	   else
	   {
		   $default_count = Currencies::defaultCurrencyCount();
		   if($default_count == 0)
		   { 
			  $multicurrency = "USD";
		   }
		   else
		   {
		     $newcurrency = Currencies::defaultCurrency();
		     $multicurrency =  $newcurrency->currency_code;
		   }
		}   	  
	   $currency = Currencies::getCurrency($multicurrency);
	   $currency_symbol = $currency->currency_symbol;
	   $currency_rate = $currency->currency_rate;
	  $qty = $request->input('qty');
	  $file_type = $request->input('file_type');
	  $item_delimiter = $request->input('item_delimiter');
	  $item_token = $request->input('item_token');
	  $item['view'] = Items::edititemData($item_token);
	  $item_serials_list = $item['view']->item_serials_list;
	  $item_price = $request->input('item_price');
	  $session_id = Session::getId();
	  $item_id = $request->input('item_id');
	  $item_name = $request->input('item_name');
	  $item_user_id = $request->input('item_user_id');
	  $additional['setting'] = Settings::editAdditional();
	  $getmember['views'] = Members::singlevendorData($item_user_id);
	  $user_exclusive_type = $getmember['views']->exclusive_author;
	  
	  $regular_license = $additional['setting']->regular_license;
	  $extended_license = $additional['setting']->extended_license;
	  
	  $chckin_data = Items::SessionCheck($session_id);
	  if($chckin_data != 0)
	  {
	  $sessiondata = Items::singleSession($session_id);
	   $coupon_type = $sessiondata->coupon_type;
	   $coupon_value = $sessiondata->coupon_value;
	   $coupon_key = $sessiondata->coupon_key;
	   $coupon_id = $sessiondata->coupon_id;
	   $coupon_code = $sessiondata->coupon_code;
	  }
	  else
	  {
	   $coupon_type = "";
	   $coupon_value = "";
	   $coupon_key = "";
	   $coupon_id = "";
	   $coupon_code = "";
	  }
	  $split = explode("_", $item_price);
       $itemprice = base64_decode($split[0]);  
	   $pricetag = base64_decode($split[0]) * $currency_rate;
	   $againtag = $pricetag * $qty;
	   $single_item_price =  base64_decode($split[0]);
	   $price = round($againtag,2);
       $license = $split[1];
	   if($license == 'regular')
	   {
	     $start_date = date('Y-m-d');
		 $end_date = date('Y-m-d', strtotime($regular_license));
	   }
	   else if($license == 'extended')
	   {
	     $start_date = date('Y-m-d');
		 $end_date = date('Y-m-d', strtotime($extended_license));
	   }
	   
	   $order_status = 'pending';
	   
	   $sid = 1;
	   $setting['setting'] = Settings::editGeneral($sid);
	   
	   if($coupon_type == "fixed")
	   {
		   $coupon_price = $price - $coupon_value;
	   }
	   else if($coupon_type == "percentage")
	   {
			$percent = ($coupon_value * $price) / 100;
			$coupon_price = $price - $percent;
	   }
	   else
	   {
		    $coupon_price = 0;
	   }
	   
	   if($additional['setting']->subscription_mode == 0)
	   {
		   if($user_exclusive_type == 1)
		   {
		   $commission = ($setting['setting']->site_exclusive_commission * $price) / 100;
		   }
		   else
		   {
		   $commission = ($setting['setting']->site_non_exclusive_commission * $price) / 100;
		   }
		   $vendor_amount = $price - $commission;
		   $admin_amount = $commission;
		}
		else
		{
		   
		   $count_mode = Settings::checkuserSubscription($item_user_id);
		   if($count_mode == 1)
		   {
		     $vendor_amount = $price;
			 $admin_amount = 0;
		   }
		   else
		   {
		       if($user_exclusive_type == 1)
			   {
			   $commission = ($setting['setting']->site_exclusive_commission * $price) / 100;
			   }
			   else
			   {
			   $commission = ($setting['setting']->site_non_exclusive_commission * $price) / 100;
			   }
			   $vendor_amount = $price - $commission;
			   $admin_amount = $commission;
		   }
		}   
	   
	   
	   $getcount  = Items::getorderCount($item_id,$session_id,$order_status);
	   
	   $savedata = array('session_id' => $session_id, 'item_id' => $item_id, 'item_name' => $item_name, 'item_user_id' => $item_user_id, 'item_token' => $item_token, 'license' => $license, 'start_date' => $start_date, 'end_date' => $end_date, 'item_price' => $itemprice, 'vendor_amount' => $vendor_amount, 'admin_amount' => $admin_amount, 'total_price' => $price, 'order_status' => $order_status, 'coupon_key' => $coupon_key, 'coupon_id' => $coupon_id, 'coupon_code' => $coupon_code, 'coupon_type' => $coupon_type, 'coupon_value' => $coupon_value, 'discount_price' => $coupon_price, 'item_serial_stock' => $qty, 'currency_type' => $currency_symbol, 'currency_type_code' => $multicurrency, 'item_single_price' => $single_item_price);
	   
	   
	   $updatedata = array('license' => $license, 'start_date' => $start_date, 'end_date' => $end_date, 'item_price' => $itemprice, 'total_price' => $price, 'vendor_amount' => $vendor_amount, 'admin_amount' => $admin_amount, 'coupon_key' => $coupon_key, 'coupon_id' => $coupon_id, 'coupon_code' => $coupon_code, 'coupon_type' => $coupon_type, 'coupon_value' => $coupon_value, 'discount_price' => $coupon_price, 'item_serial_stock' => $qty, 'currency_type' => $currency_symbol, 'currency_type_code' => $multicurrency, 'item_single_price' => $single_item_price);
	   
	   if($file_type == 'serial')
	   {
	      
		       if($item_delimiter == 'comma')
                {
                	$result_count = substr_count($item_serials_list, ",");
                }
                else
                {
                    $result_count = substr_count($item_serials_list, "\n");
                }
				if($result_count != 0)
				{
				  
				    if($additional['setting']->subscription_mode == 0)
				    {
					   if($getcount == 0)
					   {
						  Items::savecartData($savedata);
						 
						  return redirect('cart')->with('success','Item has been added to cart'); 
					   }
					   else
					   {
						  Items::updatecartData($item_id,$session_id,$order_status,$updatedata);
						  
						  return redirect('cart')->with('success','Item has been updated to cart'); 
					   }
				    }
				    else
				    {
					  Items::deletecartremove($session_id);
					  Items::savecartData($savedata);
					  return redirect('cart')->with('success','Item has been updated to cart'); 
				    } 
				  
				}
				else
				{
				   return redirect()->back()->with('error', 'License / Serial Key is out of stock');     
				}
	   
	   }
	   else
	   {
			   if($additional['setting']->subscription_mode == 0)
			   {
				   if($getcount == 0)
				   {
					  Items::savecartData($savedata);
					 
					  return redirect('cart')->with('success','Item has been added to cart'); 
				   }
				   else
				   {
					  Items::updatecartData($item_id,$session_id,$order_status,$updatedata);
					  
					  return redirect('cart')->with('success','Item has been updated to cart'); 
				   }
			   }
			   else
			   {
				  Items::deletecartremove($session_id);
				  Items::savecartData($savedata);
				  return redirect('cart')->with('success','Item has been updated to cart'); 
			   } 
	   
	   }
	  
	
	}
	
	
	public function remove_coupon($remove,$coupon)
	{  
	   $session_id = Session::getId();
	   $data = array('coupon_id' => '', 'coupon_code' => '', 'coupon_type' => '', 'coupon_value' => '', 'discount_price' => 0);
	   Items::removeCoupon($coupon,$session_id,$data);
	   return redirect()->back()->with('success', 'Coupon Removed Successfully.');
	}	
	
	public function view_coupon(Request $request)
	{
	   $allsettings = Settings::allSettings();
	   $coupon = $request->input('coupon');
	   $session_id = Session::getId();
	   $coupon_key = uniqid();
	   $check_coupon = Items::checkCoupon($coupon);
	   if($check_coupon == 1)
	   {
	      $single = Items::singleCoupon($coupon);
	      $coupondata['get'] = Items::getCoupon($coupon,$session_id);
		  foreach($coupondata['get'] as $couponview)
		  {
		     $order_id = $couponview->ord_id;
			 $coupon_id = $single->coupon_id;
			 $coupon_code = $single->coupon_code;
			 $coupon_type = $single->discount_type;
			 $coupon_value = $single->coupon_value;
			 $price = $couponview->item_price * $couponview->item_serial_stock;
			/* $price = $couponview->item_price;*/
			 if($coupon_type == 'percentage')
			 {
			 $discount = ($coupon_value * $price) / 100;
			 $discount_price = $price - $discount;
			 $data = array('coupon_key' => $coupon_key, 'coupon_id' => $coupon_id, 'coupon_code' => $coupon_code, 'coupon_type' => $coupon_type, 'coupon_value' => $coupon_value, 'discount_price' => $discount_price);
			 Items::updateCoupon($order_id,$data);
			 }
			 else
			 {
			    if($coupon_value < $price)
				{
			     $discount = $coupon_value;
				 $discount_price = $price - $discount;
				 $data = array('coupon_key' => $coupon_key, 'coupon_id' => $coupon_id, 'coupon_code' => $coupon_code, 'coupon_type' => $coupon_type, 'coupon_value' => $coupon_value, 'discount_price' => $discount_price);
			 Items::updateCoupon($order_id,$data);
				}
				else
				{
				 $discount = 0; 
				 return redirect()->back()->with('error', 'Invalid Coupon Code or Expired');
				}
			 }
			
		  }
		  return redirect()->back()->with('success', 'Coupon Added Successfully.');
	   }
	   else
	   {
	      return redirect()->back()->with('error', 'Invalid Coupon Code or Expired');
	   }
	
	}
	
	public function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
    }
	
	public function coinbase_checkout(Request $request)
    {   
	    $encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
	    $additional['setting'] = Settings::editAdditional();
        $postdata = file_get_contents("php://input");
        $res = json_decode($postdata);
        $ord_token = $res->event->data->metadata->trx;
		$check_details = Items::getcheckoutData($ord_token);
		$count_mode = Settings::checkuserSubscription($check_details->item_user_id);
		$vendor_details = Members::singlevendorData($check_details->item_user_id);
        if($additional['setting']->subscription_mode == 0)
		{
		   $coinbase_secret_key = $additional['setting']->coinbase_secret_key;
		}
		else
		{
		   if($count_mode == 1)
		   { 
		      $coinbase_secret_key = $vendor_details->user_coinbase_secret_key;
		   }
		   else
		   {
		      $coinbase_secret_key = $additional['setting']->coinbase_secret_key;
		   }
		}
        $headers = apache_request_headers();
        $sentSign = $headers['x-cc-webhook-signature'];
        $sig = hash_hmac('sha256', $postdata, $coinbase_secret_key);
        if ($sentSign == $sig) {
            if ($res->event->type == 'charge:confirmed' && $check_details->payment_status == 'pending') 
			{
			    
				return redirect('/coinbase/'.$encrypter->encrypt($ord_token));
                
            }
        }
    }
	
	
	public function split_name($name) 
	{
       $parts = array();

      while ( strlen( trim($name)) > 0 ) {
        $name = trim($name);
        $string = preg_replace('#.*\s([\w-]*)$#', '$1', $name);
        $parts[] = $string;
        $name = trim( preg_replace('#'.preg_quote($string,'#').'#', '', $name ) );
    }

    if (empty($parts)) {
        return false;
    }

    $parts = array_reverse($parts);
    $name = array();
    $name['first_name'] = $parts[0];
    $name['middle_name'] = (isset($parts[2])) ? $parts[1] : '';
    $name['last_name'] = (isset($parts[2])) ? $parts[2] : ( isset($parts[1]) ? $parts[1] : '');
    
    return $name;
   }
	
	public function view_checkout(Request $request)
	{
	   $encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
	   $sid = 1;
	   $setting['setting'] = Settings::editGeneral($sid);
	   $cart_count = Items::getcartCount();
	   $additional['setting'] = Settings::editAdditional();
	   $free_subscr_download_item = $additional['setting']->subscr_download_items;
	   $additional_view = Settings::editAdditional();
	   $cart['item'] = Items::getcartData();
	   $mobile['item'] = Items::getcartData();
	   $purchase_code = $this->generate_license();
	   $session_id = Session::getId();
	   if(!empty(Cookie::get('referral')))
		 {
	      $referral_by = decrypt(Cookie::get('referral'));
		  $referral_payout = 'pending'; 
         }
		 else
		 {
		  $referral_by = "";
		  $referral_payout = "";
		 }
	   if($additional['setting']->subscription_mode == 1)
		{ 
		$download_limit = $free_subscr_download_item;
		}
		else
		{
		$download_limit = 1000;
		}	 
	   if(Auth::guest())
	    {
			 $email = $request->input('email');
			 $password = bcrypt($request->input('password'));
			 $user_auth_token = base64_encode($request->input('password'));
			 $pass = trim($request->input('password'));
			 $request->validate([
								
								'email' => 'required|email',
								'password' => ['required', 'min:6'],
								
								
								
			 ]);
			 $rules = array(
					
					'email' => ['required', 'email', 'max:255', Rule::unique('users') -> where(function($sql){ $sql->where('drop_status','=','no');})],
					
			 );
			 
			 $messsages = array(
				  
			);
			 
			$validator = Validator::make($request->all(), $rules,$messsages);
			
			if ($validator->fails()) 
			{
			 //$failedRules = $validator->failed();
			 //return back()->withErrors($validator);
			 
			 return redirect()->back()->with('error', 'Email address already exists');
			 
			} 
			else
			{
			  
			   $user_token = $this->generateRandomString();
			   $register_url = URL::to('/user-verify/').'/'.$user_token;
			   $name = strstr($email, '@', true);
			   $username = strstr($email, '@', true);
			   $user_type = 'customer';
			   $earnings = 0;
			   $verified = 1;
			   $data = array('name' => $name, 'username' => $username, 'email' => $email, 'user_type' => $user_type, 'password' => $password, 'earnings' => $earnings, 'verified' => $verified, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'user_token' => $user_token, 'referral_by' => $referral_by, 'referral_payout' => $referral_payout, 'user_auth_token' => $user_auth_token, 'register_url' => $register_url, 'user_subscr_download_item' => $download_limit);
			   Members::insertData($data);
			   $field = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
			   if (Auth::attempt(array($field => $email, 'password' =>  $pass, 'verified' => 1, 'drop_status' => 'no' )))
			   {
				Session::setId($session_id);
				$updata = array('user_id' => auth()->user()->id); 
				Items::changeOrder($session_id,$updata);
				$user_id = auth()->user()->id;
				$userdata = Members::logindataUser($user_id);
				$spliter = $this->split_name($userdata->name);
				$order_firstname = $spliter['first_name'];
				$order_lastname = $spliter['last_name'];
				$order_email = $userdata->email;
				$user_email = $userdata->email;
				$user_token = $userdata->user_token;
				$buyer_wallet_amount = $userdata->earnings;
				$country_id = $userdata->country;
			   }
			   else
			   {
				 return redirect()->back()->with('error', 'These credentials do not match our records.');
			   }
			  
			  
		   
			}
		 
	   
	   }
	   else
	   {
	   $user_id = Auth::user()->id;
	   $userdata = Members::logindataUser($user_id);
	   $spliter = $this->split_name($userdata->name);
	   $order_firstname = $spliter['first_name'];
	   $order_lastname = $spliter['last_name'];
	   $order_email = $userdata->email;
	   $user_email = $userdata->email;
	   $user_token = $userdata->user_token;
	   $buyer_wallet_amount = $userdata->earnings;
	   $country_id = $userdata->country;
	   }
	   $token = $request->input('token');
	   $purchase_token = rand(111111,999999);
	   $order_id = $request->input('order_id');
	   $item_prices = $encrypter->decrypt($request->input('item_prices'));
	   
	   $item_user_id = $request->input('item_user_id');
	   
	   
	   $vat_price = $encrypter->decrypt($request->input('vat_price')); 
	   
	   $processing_fee = $encrypter->decrypt($request->input('processing_fee'));
	   $amount = $encrypter->decrypt($request->input('amount'));
	   $discount_fee = $encrypter->decrypt($request->input('discount'));
	   $final_amount = $amount + $processing_fee + $vat_price;
	    
	   $pind_amount = $final_amount - $discount_fee;	
	   $final_amount = round($pind_amount,2);
	   
	   /* default */
	   $default_vat_price = $encrypter->decrypt($request->input('default_vat_price')); 
	   
	   $default_processing_fee = $encrypter->decrypt($request->input('default_processing_fee'));
	   $default_amount = $encrypter->decrypt($request->input('default_amount'));
	   $default_discount_fee = $encrypter->decrypt($request->input('default_discount'));
	   $default_final_amount = $default_amount + $default_processing_fee + $default_vat_price;
	    
	   $default_pind_amount = $default_final_amount - $default_discount_fee;	
	   $default_final_amount = round($default_pind_amount,2);
	   
	   /* default */
	   $payment_method = $request->input('payment_method');
	   $website_url = $request->input('website_url');
	   $payment_date = date('Y-m-d');
	   $payment_status = 'pending';
	   $reference = $request->input('reference');
	   $currency_type = $encrypter->decrypt($request->input('currency_type'));
	   $currency_type_code = $encrypter->decrypt($request->input('currency_type_code'));
	   $item_single_prices = $encrypter->decrypt($request->input('item_single_prices'));
	   $currency_rate = $encrypter->decrypt($request->input('currency_rate'));
	   
	   
	   
	   /* subscription code */
	   
	   if($cart_count != 0)
	   {
		  $last_order = Items::lastorderData();
		  $item_user_id = $last_order->item_user_id;
		  $count_mode = Settings::checkuserSubscription($item_user_id);
		  $vendor_details = Members::singlevendorData($item_user_id);
		  if($additional['setting']->subscription_mode == 0)
		  {
			  $paypal_email = $setting['setting']->paypal_email;
	          $paypal_mode = $setting['setting']->paypal_mode;
			  if($paypal_mode == 1)
			   {
				 $paypal_url = "https://www.paypal.com/cgi-bin/webscr";
			   }
			   else
			   {
				 $paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
			   }
			   $vendor_amount = $encrypter->decrypt($request->input('vendor_amount'));
	           $admin_amount = $encrypter->decrypt($request->input('admin_amount'));
			   
			   $stripe_type = $setting['setting']->stripe_type;
			   $stripe_mode = $setting['setting']->stripe_mode;
			   if($stripe_mode == 0)
			   {
				 $stripe_publish_key = $setting['setting']->test_publish_key;
				 $stripe_secret_key = $setting['setting']->test_secret_key;
			   }
			   else
			   {
				 $stripe_publish_key = $setting['setting']->live_publish_key;
				 $stripe_secret_key = $setting['setting']->live_secret_key;
			   }
			   
			   
			   $razorpay_key = $additional_view->razorpay_key;
			   
			   $payhere_mode = $additional_view->payhere_mode;
			   if($payhere_mode == 1)
			   {
			     $payhere_url = 'https://www.payhere.lk/pay/checkout';
			   }
			   else
			   {
			   $payhere_url = 'https://sandbox.payhere.lk/pay/checkout';
			   }
			   $payhere_merchant_id = $additional['setting']->payhere_merchant_id;
			   
			    $MERCHANT_KEY = $additional_view->payu_merchant_key; // add your id
				$SALT = $additional_view->payu_salt_key; // add your id
				if($additional_view->payumoney_mode == 1)
				{
				$PAYU_BASE_URL = "https://secure.payu.in";
				}
				else
				{
				$PAYU_BASE_URL = "https://test.payu.in";
				}
				
				$two_checkout_mode = $setting['setting']->two_checkout_mode;
	   			$two_checkout_account = $setting['setting']->two_checkout_account;
	   			$two_checkout_publishable = $setting['setting']->two_checkout_publishable;
	   			$two_checkout_private = $setting['setting']->two_checkout_private;
				
				/* iyzico */
				$iyzico_api_key = $additional['setting']->iyzico_api_key;
				$iyzico_secret_key = $additional['setting']->iyzico_secret_key;
				$iyzico_mode = $additional['setting']->iyzico_mode;
				if($iyzico_mode == 0)
				{
				   $iyzico_url = 'https://sandbox-api.iyzipay.com';
				}
				else
				{
				   $iyzico_url = 'https://api.iyzipay.com';
				}
				$iyzico_success_url = $website_url.'/iyzico-success/admin-'.$purchase_token;
				/* iyzico */
				
				/* flutterwave */
				$flutterwave_public_key = $additional['setting']->flutterwave_public_key;
				$flutterwave_secret_key = $additional['setting']->flutterwave_secret_key;
				/* flutterwave */
				
				/* coingate */
				$coingate_mode = $additional['setting']->coingate_mode;
	   			if($coingate_mode == 0)
	   			{
	      			$coingate_mode_status = "sandbox";
	   			}
	   			else
	   			{
	      			$coingate_mode_status = "live";
	   			}
	   			$coingate_auth_token = $additional['setting']->coingate_auth_token;
	  			$coingate_callback = $website_url.'/coingate';
				/* coingate */
				
				/* ipay */
				$ipay_mode = $additional['setting']->ipay_mode;
	   			$ipay_vendor_id = $additional['setting']->ipay_vendor_id;
				$ipay_hash_key = $additional['setting']->ipay_hash_key;
	  			$ipay_callback = $website_url.'/ipay';
				$ipay_url = 'https://payments.ipayafrica.com/v3/ke';
				/* ipay */
				
				/* payfast */
			   $payfast_mode = $additional['setting']->payfast_mode;
			   $payfast_merchant_id = $additional['setting']->payfast_merchant_id;
			   $payfast_merchant_key = $additional['setting']->payfast_merchant_key;
			   if($payfast_mode == 1)
			   {
				 $payfast_url = "https://www.payfast.co.za/eng/process";
			   }
			   else
			   {
				 $payfast_url = "https://sandbox.payfast.co.za/eng/process";
			   }
			   
			   /* payfast */
			   
			   /* coinpayments */
			   $coinpayments_merchant_id = $additional['setting']->coinpayments_merchant_id;
			   /* coinpayments */
				
			   /* mercadopago */
			   
			   $mercadopago_client_id = $additional['setting']->mercadopago_client_id;
	           $mercadopago_client_secret = $additional['setting']->mercadopago_client_secret;
			   $mercadopago_mode = $additional['setting']->mercadopago_mode;
			   $mercadopago_success = $website_url.'/mercadopago-success/'.$purchase_token;
			   $mercadopago_failure = $website_url.'/failure/';
			   $mercadopago_pending = $website_url.'/pending/';	
				/* mercadopago */
				
				/* instamojo */
			   $instamojo_success_url = $website_url.'/instamojo-success/'.$purchase_token;
			   if($additional['setting']->instamojo_mode == 1)
			   {
				 $instamojo_payment_link = 'https://instamojo.com/api/1.1/payment-requests/';
			   }
			   else
			   { 
				  $instamojo_payment_link = 'https://test.instamojo.com/api/1.1/payment-requests/';
			   }
			   $instamojo_api_key = $additional['setting']->instamojo_api_key;
			   $instamojo_auth_token = $additional['setting']->instamojo_auth_token;
			   /* instamojo */
			   
			   /* aamarpay */
			   $aamarpay_mode = $additional['setting']->aamarpay_mode;
			   $aamarpay_store_id = $additional['setting']->aamarpay_store_id;
			   $aamarpay_signature_key = $additional['setting']->aamarpay_signature_key;
			   if($aamarpay_mode == 1)
			   {
				  $aamarpay_url = "http://secure.aamarpay.com/index.php";
			   }
			   else
			   {
				  $aamarpay_url = "https://sandbox.aamarpay.com/index.php";
			   }
			   $aamarpay_success_url = $website_url.'/aamarpay/'.$purchase_token;
			   $aamarpay_cancel_url = $website_url.'/aamarpay/'.$purchase_token;
			   $aamarpay_failed_url = $website_url.'/aamarpay/'.$purchase_token;
			   /* aamarpay */
			   
			   /* mollie */
			   if($additional['setting']->mollie_api_key != "")
			   {
			   Mollie::api()->setApiKey($additional['setting']->mollie_api_key);
			   }
			   session()->put('mollie_type','admin');
			   session()->put('mollie_user_id',1);
			   /* mollie */ 	
			   
			   /* robokassa */
				$shop_identifier = $additional['setting']->shop_identifier;
				$robokassa_password_1 = $additional['setting']->robokassa_password_1;
				/* robokassa */
				
				/* midtrans */
				$midtrans_mode = $additional['setting']->midtrans_mode;
				$midtrans_server_key = $additional['setting']->midtrans_server_key;
				$midtrans_success = $website_url.'/midtrans-success/'.$purchase_token;
				if($midtrans_mode == 0)
				{
				   $midtrans_mode_status = false;
				   $midtrans_trans_url = "https://app.sandbox.midtrans.com/snap/v2/vtweb/";
				}
				else
				{
				   $midtrans_mode_status = true;
				   $midtrans_trans_url = "https://app.midtrans.com/snap/v2/vtweb/";
				}
				/* midtrans */
				/* coinbase */
				$coinbase_api_key = $additional['setting']->coinbase_api_key;
				$coinbase_success = $website_url.'/coinbase/'.$encrypter->encrypt($purchase_token);
				$coinbase_webhooks = $website_url.'/webhooks/coinbase-checkout';
				/* coinbase */
				
				/* cashfree */
				$cashfree_api_key = $additional['setting']->cashfree_api_key;
				$cashfree_api_secret = $additional['setting']->cashfree_api_secret;
				$cashfree_mode = $additional['setting']->cashfree_mode;
				$cashfree_success = $website_url.'/cashfree-success/admin-'.$purchase_token.'/';
				/* cashfree */
				
				/* nowpayments */
			   $nowpayments_api_key = $additional['setting']->nowpayments_api_key;
			   $nowpayments_ipn_secret = $additional['setting']->nowpayments_ipn_secret;
			   $nowpayments_mode = $additional['setting']->nowpayments_mode;
			   $nowpayments_success = $website_url.'/nowpayments/'.$encrypter->encrypt($purchase_token);
			   /* nowpayments */
			   
			   /* dodopayments */
			   $dodopayments_success = $website_url.'/checkout-dodopayments/'.$encrypter->encrypt($purchase_token);
			   /* dodopayments */
				
			   $get_payment = explode(',', $setting['setting']->payment_option);
	           
		  }
		  else
		  {
			 if($count_mode == 1)
			 {
				$paypal_email = $vendor_details->user_paypal_email;
	            $paypal_mode = $vendor_details->user_paypal_mode;
				  if($paypal_mode == 1)
				   {
					 $paypal_url = "https://www.paypal.com/cgi-bin/webscr";
				   }
				   else
				   {
					 $paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
				   }
				   $vendor_amount = $encrypter->decrypt($request->input('vendor_amount')) + $encrypter->decrypt($request->input('admin_amount'));
	               $admin_amount = 0;
				   
				   $stripe_type = $vendor_details->user_stripe_type;
				   $stripe_mode = $vendor_details->user_stripe_mode;
				   if($stripe_mode == 0)
				   {
					 $stripe_publish_key = $vendor_details->user_test_publish_key;
					 $stripe_secret_key = $vendor_details->user_test_secret_key;
				   }
				   else
				   {
					 $stripe_publish_key = $vendor_details->user_live_publish_key;
					 $stripe_secret_key = $vendor_details->user_live_secret_key;
				   }
				   
			   
				   $razorpay_key = $vendor_details->user_razorpay_key;
				   
				   $payhere_mode = $vendor_details->user_payhere_mode;
				   if($payhere_mode == 1)
				   {
					 $payhere_url = 'https://www.payhere.lk/pay/checkout';
				   }
				   else
				   {
				   $payhere_url = 'https://sandbox.payhere.lk/pay/checkout';
				   }
				   $payhere_merchant_id = $vendor_details->user_payhere_merchant_id;
				   
				   $MERCHANT_KEY = $vendor_details->user_payu_merchant_key; // add your id
					$SALT = $vendor_details->user_payu_salt_key; // add your id
					if($vendor_details->user_payumoney_mode == 1)
					{
					$PAYU_BASE_URL = "https://secure.payu.in";
					}
					else
					{
					$PAYU_BASE_URL = "https://test.payu.in";
					}
					
					$two_checkout_mode = $vendor_details->user_two_checkout_mode;
	   				$two_checkout_account = $vendor_details->user_two_checkout_account;
	   				$two_checkout_publishable = $vendor_details->user_two_checkout_publishable;
	   				$two_checkout_private = $vendor_details->user_two_checkout_private;
					
					/* iyzico */
					$iyzico_api_key = $vendor_details->user_iyzico_api_key;
					$iyzico_secret_key = $vendor_details->user_iyzico_secret_key;
					$iyzico_mode = $vendor_details->user_iyzico_mode;
					if($iyzico_mode == 0)
					{
					   $iyzico_url = 'https://sandbox-api.iyzipay.com';
					}
					else
					{
					   $iyzico_url = 'https://api.iyzipay.com';
					}
					$iyzico_success_url = $website_url.'/iyzico-success/vendor-'.$purchase_token;
					/* iyzico */
					
					/* flutterwave */
					$flutterwave_public_key = $vendor_details->user_flutterwave_public_key;
					$flutterwave_secret_key = $vendor_details->user_flutterwave_secret_key;
					/* flutterwave */
					
					/* coingate */
					$coingate_mode = $vendor_details->user_coingate_mode;
					if($coingate_mode == 0)
					{
						$coingate_mode_status = "sandbox";
					}
					else
					{
						$coingate_mode_status = "live";
					}
					$coingate_auth_token = $vendor_details->user_coingate_auth_token;
					$coingate_callback = $website_url.'/coingate-success';
					/* coingate */
					
					/* ipay */
					$ipay_mode = $vendor_details->user_ipay_mode;
	   				$ipay_vendor_id = $vendor_details->user_ipay_vendor_id;
					$ipay_hash_key = $vendor_details->user_ipay_hash_key;
	  				$ipay_callback = $website_url.'/ipay';
					$ipay_url = 'https://payments.ipayafrica.com/v3/ke';
					/* ipay */
					
					/* payfast */
				   $payfast_mode = $vendor_details->user_payfast_mode;
				   $payfast_merchant_id = $vendor_details->user_payfast_merchant_id;
				   $payfast_merchant_key = $vendor_details->user_payfast_merchant_key;
				   if($payfast_mode == 1)
				   {
					 $payfast_url = "https://www.payfast.co.za/eng/process";
				   }
				   else
				   {
					 $payfast_url = "https://sandbox.payfast.co.za/eng/process";
				   }
				   
				   /* payfast */
				   
				   /* coinpayments */
				   $coinpayments_merchant_id = $vendor_details->user_coinpayments_merchant_id;
				   /* coinpayments */
				   
				   
				   /* mercadopago */
				   $mercadopago_client_id = $vendor_details->user_mercadopago_client_id;
				   $mercadopago_client_secret = $vendor_details->user_mercadopago_client_secret;
				   $mercadopago_mode = $vendor_details->user_mercadopago_mode;
				   $mercadopago_success = $website_url.'/mercadopago-success/'.$purchase_token;
				   $mercadopago_failure = $website_url.'/failure/';
				   $mercadopago_pending = $website_url.'/pending/';
				   
				   /* mercadopago */
				   
				   /* instamojo */
				   $instamojo_success_url = $website_url.'/instamojo-success/'.$purchase_token;
				   if($vendor_details->user_instamojo_mode == 1)
				   {
					 $instamojo_payment_link = 'https://instamojo.com/api/1.1/payment-requests/';
				   }
				   else
				   { 
					  $instamojo_payment_link = 'https://test.instamojo.com/api/1.1/payment-requests/';
				   }
				   $instamojo_api_key = $vendor_details->user_instamojo_api_key;
				   $instamojo_auth_token = $vendor_details->user_instamojo_auth_token;
				   /* instamojo */
				   
				   /* aamarpay */
				   $aamarpay_mode = $vendor_details->user_aamarpay_mode;
				   $aamarpay_store_id = $vendor_details->user_aamarpay_store_id;
				   $aamarpay_signature_key = $vendor_details->user_aamarpay_signature_key;
				   if($aamarpay_mode == 1)
				   {
					  $aamarpay_url = "http://secure.aamarpay.com/index.php";
				   }
				   else
				   {
					  $aamarpay_url = "https://sandbox.aamarpay.com/index.php";
				   }
				   $aamarpay_success_url = $website_url.'/aamarpay/'.$purchase_token;
				   $aamarpay_cancel_url = $website_url.'/aamarpay/'.$purchase_token;
				   $aamarpay_failed_url = $website_url.'/aamarpay/'.$purchase_token;
				   /* aamarpay */
				   
				   /* mollie */
				    if($vendor_details->user_mollie_api_key != "")
					{
			   		Mollie::api()->setApiKey($vendor_details->user_mollie_api_key);
					}
					session()->put('mollie_type','vendor');
					session()->put('mollie_user_id',$vendor_details->id);
					/* mollie */ 
					
					/* robokassa */
					$shop_identifier = $vendor_details->user_shop_identifier;
					$robokassa_password_1 = $vendor_details->user_robokassa_password_1;
					/* robokassa */
			        /* midtrans */
					$midtrans_mode = $vendor_details->user_midtrans_mode;
					$midtrans_server_key = $vendor_details->user_midtrans_server_key;
					$midtrans_success = $website_url.'/midtrans-success/'.$purchase_token;
					if($midtrans_mode == 0)
					{
					   $midtrans_mode_status = false;
					   $midtrans_trans_url = "https://app.sandbox.midtrans.com/snap/v2/vtweb/";
					}
					else
					{
					   $midtrans_mode_status = true;
					   $midtrans_trans_url = "https://app.midtrans.com/snap/v2/vtweb/";
					}
					/* midtrans */
					/* coinbase */
					$coinbase_api_key = $vendor_details->user_coinbase_api_key;
					$coinbase_success = $website_url.'/coinbase/'.$encrypter->encrypt($purchase_token);
					$coinbase_webhooks = $website_url.'/webhooks/coinbase-checkout';
					/* coinbase */
					
					/* cashfree */
					$cashfree_api_key = $vendor_details->user_cashfree_api_key;
					$cashfree_api_secret = $vendor_details->user_cashfree_api_secret;
					$cashfree_mode = $vendor_details->user_cashfree_mode;
					$cashfree_success = $website_url.'/cashfree-success/vendor-'.$purchase_token.'/';
					/* cashfree */
					
					/* nowpayments */
				   $nowpayments_api_key = $vendor_details->user_nowpayments_api_key;
				   $nowpayments_ipn_secret = $vendor_details->user_nowpayments_ipn_secret;
				   $nowpayments_mode = $vendor_details->user_nowpayments_mode;
				   $nowpayments_success = $website_url.'/nowpayments/'.$encrypter->encrypt($purchase_token);
				   /* nowpayments */
					
				   $get_payment = explode(',', $vendor_details->user_payment_option);
			 }
			 else
			 {
				  $paypal_email = $setting['setting']->paypal_email;
				  $paypal_mode = $setting['setting']->paypal_mode;
				  if($paypal_mode == 1)
				   {
					 $paypal_url = "https://www.paypal.com/cgi-bin/webscr";
				   }
				   else
				   {
					 $paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
				   }
				   $vendor_amount = $encrypter->decrypt($request->input('vendor_amount'));
	               $admin_amount = $encrypter->decrypt($request->input('admin_amount'));
				   
				   $stripe_type = $setting['setting']->stripe_type;
				   $stripe_mode = $setting['setting']->stripe_mode;
				   if($stripe_mode == 0)
				   {
					 $stripe_publish_key = $setting['setting']->test_publish_key;
					 $stripe_secret_key = $setting['setting']->test_secret_key;
				   }
				   else
				   {
					 $stripe_publish_key = $setting['setting']->live_publish_key;
					 $stripe_secret_key = $setting['setting']->live_secret_key;
				   }
				   
				   
				   $razorpay_key = $additional_view->razorpay_key;
				   
				   $payhere_mode = $additional_view->payhere_mode;
				   if($payhere_mode == 1)
				   {
					 $payhere_url = 'https://www.payhere.lk/pay/checkout';
				   }
				   else
				   {
				   $payhere_url = 'https://sandbox.payhere.lk/pay/checkout';
				   }
				   $payhere_merchant_id = $additional_view->payhere_merchant_id;
				   
				   $MERCHANT_KEY = $additional_view->payu_merchant_key; // add your id
					$SALT = $additional_view->payu_salt_key; // add your id
					if($additional_view->payumoney_mode == 1)
					{
					$PAYU_BASE_URL = "https://secure.payu.in";
					}
					else
					{
					$PAYU_BASE_URL = "https://test.payu.in";
					}
					
					$two_checkout_mode = $setting['setting']->two_checkout_mode;
	   			    $two_checkout_account = $setting['setting']->two_checkout_account;
	   			    $two_checkout_publishable = $setting['setting']->two_checkout_publishable;
	   			    $two_checkout_private = $setting['setting']->two_checkout_private;
				
					
					/* iyzico */
					$iyzico_api_key = $additional['setting']->iyzico_api_key;
					$iyzico_secret_key = $additional['setting']->iyzico_secret_key;
					$iyzico_mode = $additional['setting']->iyzico_mode;
					if($iyzico_mode == 0)
					{
					   $iyzico_url = 'https://sandbox-api.iyzipay.com';
					}
					else
					{
					   $iyzico_url = 'https://api.iyzipay.com';
					}
					$iyzico_success_url = $website_url.'/iyzico-success/admin-'.$purchase_token;
					/* iyzico */
					
					/* flutterwave */
					$flutterwave_public_key = $additional['setting']->flutterwave_public_key;
					$flutterwave_secret_key = $additional['setting']->flutterwave_secret_key;
					/* flutterwave */
					
					/* coingate */
					$coingate_mode = $additional['setting']->coingate_mode;
					if($coingate_mode == 0)
					{
						$coingate_mode_status = "sandbox";
					}
					else
					{
						$coingate_mode_status = "live";
					}
					$coingate_auth_token = $additional['setting']->coingate_auth_token;
					$coingate_callback = $website_url.'/coingate';
					/* coingate */
					
					/* ipay */
					$ipay_mode = $additional['setting']->ipay_mode;
	   				$ipay_vendor_id = $additional['setting']->ipay_vendor_id;
					$ipay_hash_key = $additional['setting']->ipay_hash_key;
	  				$ipay_callback = $website_url.'/ipay';
					$ipay_url = 'https://payments.ipayafrica.com/v3/ke';
					/* ipay */
					
					/* payfast */
				   $payfast_mode = $additional['setting']->payfast_mode;
				   $payfast_merchant_id = $additional['setting']->payfast_merchant_id;
				   $payfast_merchant_key = $additional['setting']->payfast_merchant_key;
				   if($payfast_mode == 1)
				   {
					 $payfast_url = "https://www.payfast.co.za/eng/process";
				   }
				   else
				   {
					 $payfast_url = "https://sandbox.payfast.co.za/eng/process";
				   }
				   
				   /* payfast */
				   
				   /* coinpayments */
				   $coinpayments_merchant_id = $additional['setting']->coinpayments_merchant_id;
				   /* coinpayments */
				   
				   /* mercadopago */
			   
				   $mercadopago_client_id = $additional['setting']->mercadopago_client_id;
				   $mercadopago_client_secret = $additional['setting']->mercadopago_client_secret;
				   $mercadopago_mode = $additional['setting']->mercadopago_mode;
				   $mercadopago_success = $website_url.'/mercadopago-success/'.$purchase_token;
				   $mercadopago_failure = $website_url.'/failure/';
				   $mercadopago_pending = $website_url.'/pending/';	
					/* mercadopago */
					
					/* instamojo */
				   $instamojo_success_url = $website_url.'/instamojo-success/'.$purchase_token;
				   if($additional['setting']->instamojo_mode == 1)
				   {
					 $instamojo_payment_link = 'https://instamojo.com/api/1.1/payment-requests/';
				   }
				   else
				   { 
					  $instamojo_payment_link = 'https://test.instamojo.com/api/1.1/payment-requests/';
				   }
				   $instamojo_api_key = $additional['setting']->instamojo_api_key;
				   $instamojo_auth_token = $additional['setting']->instamojo_auth_token;
				   /* instamojo */
				   /* aamarpay */
				   $aamarpay_mode = $additional['setting']->aamarpay_mode;
				   $aamarpay_store_id = $additional['setting']->aamarpay_store_id;
				   $aamarpay_signature_key = $additional['setting']->aamarpay_signature_key;
				   if($aamarpay_mode == 1)
				   {
					  $aamarpay_url = "http://secure.aamarpay.com/index.php";
				   }
				   else
				   {
					  $aamarpay_url = "https://sandbox.aamarpay.com/index.php";
				   }
				   $aamarpay_success_url = $website_url.'/aamarpay/'.$purchase_token;
				   $aamarpay_cancel_url = $website_url.'/aamarpay/'.$purchase_token;
				   $aamarpay_failed_url = $website_url.'/aamarpay/'.$purchase_token;
				   /* aamarpay */
				   
				   /* mollie */
				   if($additional['setting']->mollie_api_key != "")
				   {
			       Mollie::api()->setApiKey($additional['setting']->mollie_api_key);
				   }
				   session()->put('mollie_type','admin');
				   session()->put('mollie_user_id',1);
			       /* mollie */ 
				   /* robokassa */
					$shop_identifier = $additional['setting']->shop_identifier;
					$robokassa_password_1 = $additional['setting']->robokassa_password_1;
					/* robokassa */
					
					/* midtrans */
					$midtrans_mode = $additional['setting']->midtrans_mode;
					$midtrans_server_key = $additional['setting']->midtrans_server_key;
					$midtrans_success = $website_url.'/midtrans-success/'.$purchase_token;
					if($midtrans_mode == 0)
					{
					   $midtrans_mode_status = false;
					   $midtrans_trans_url = "https://app.sandbox.midtrans.com/snap/v2/vtweb/";
					}
					else
					{
					   $midtrans_mode_status = true;
					   $midtrans_trans_url = "https://app.midtrans.com/snap/v2/vtweb/";
					}
					/* midtrans */
					/* coinbase */
					$coinbase_api_key = $additional['setting']->coinbase_api_key;
					$coinbase_success = $website_url.'/coinbase/'.$encrypter->encrypt($purchase_token);
					$coinbase_webhooks = $website_url.'/webhooks/coinbase-checkout';
					/* coinbase */
					
					/* cashfree */
					$cashfree_api_key = $additional['setting']->cashfree_api_key;
					$cashfree_api_secret = $additional['setting']->cashfree_api_secret;
					$cashfree_mode = $additional['setting']->cashfree_mode;
					$cashfree_success = $website_url.'/cashfree-success/admin-'.$purchase_token.'/';
					/* cashfree */
					
					/* nowpayments */
				    $nowpayments_api_key = $additional['setting']->nowpayments_api_key;
				    $nowpayments_ipn_secret = $additional['setting']->nowpayments_ipn_secret;
				    $nowpayments_mode = $additional['setting']->nowpayments_mode;
				    $nowpayments_success = $website_url.'/nowpayments/'.$encrypter->encrypt($purchase_token);
				    /* nowpayments */
					
				   $get_payment = explode(',', $setting['setting']->payment_option);
			 }
		  }
	  }
	  /* VAT */
	  $country_details = Members::countryCheck($country_id);
	  if($country_details != 0)
	  {
	      $data_views = Members::countryDATA($country_id);
		  $country_percent = $data_views->vat_price;
	  }
	  else
	  {
	    $country_percent = $additional['setting']->default_vat_price;
	  } 
	   /* VAT */
	  $totaldata = array('cart' => $cart, 'cart_count' => $cart_count, 'get_payment' => $get_payment, 'mobile' => $mobile, 'stripe_type' => $stripe_type, 'country_percent' => $country_percent);
	  /* subscription code */ 
	   
	   
	   
	   $getcount  = Items::getcheckoutCount($purchase_token,$user_id,$payment_status);
	   
	   $savedata = array('purchase_token' => $purchase_token, 'order_ids' => $order_id, 'item_prices' => $item_prices, 'item_user_id' => $item_user_id, 'user_id' => $user_id, 'total' => $amount, 'vendor_amount' => $vendor_amount, 'admin_amount' => $admin_amount, 'processing_fee' => $processing_fee, 'payment_type' => $payment_method, 'payment_date' => $payment_date, 'order_firstname' => $order_firstname, 'order_lastname' => $order_lastname,  'order_email' => $order_email, 'payment_status' => $payment_status, 'vat_price' => $vat_price, 'currency_type' => $currency_type, 'currency_type_code' => $currency_type_code, 'item_single_prices' => $item_single_prices);
	   
	   $updatedata = array('order_ids' => $order_id, 'item_prices' => $item_prices, 'item_user_id' => $item_user_id, 'total' => $amount, 'vendor_amount' => $vendor_amount, 'admin_amount' => $admin_amount, 'processing_fee' => $processing_fee, 'payment_type' => $payment_method, 'payment_date' => $payment_date, 'order_firstname' => $order_firstname, 'order_lastname' => $order_lastname, 'order_email' => $order_email, 'vat_price' => $vat_price, 'currency_type' => $currency_type, 'currency_type_code' => $currency_type_code, 'item_single_prices' => $item_single_prices);
	   
	   
	   /* settings */
	   
	   
	   $site_currency = $currency_type_code;
	   
	   $success_url = $website_url.'/success/'.$purchase_token;
	   $cancel_url = $website_url.'/cancel';
	   $payhere_success_url = $website_url.'/payhere-success/'.$purchase_token;
	   $payfast_success_url = $website_url.'/payfast-success/'.$purchase_token;
	   $coinpayments_success_url = $website_url.'/coinpayments-success/'.$purchase_token;
	   $mollie_success_url = $website_url.'/mollie';
	   
	   /* settings */
	   
	   
	   if($getcount == 0)
	   {
	      Items::savecheckoutData($savedata);
		  
		  $order_loop = explode(',',$order_id);
		  $item_names = "";
		  foreach($order_loop as $order)
		  {
		    $single_order = Items::getorderData($order);
			$buyer_id = $single_order->item_user_id;
			$buyer_info['view'] = Members::singlebuyerData($buyer_id);
			$buyer_type = $buyer_info['view']->exclusive_author;
			$count_mode = Settings::checkuserSubscription($buyer_id);
			$item_pricce = $single_order->item_single_price * $currency_rate;
			$total_pricce = $item_pricce * $single_order->item_serial_stock;
			$another_price = $single_order->item_single_price * $single_order->item_serial_stock;
			if($single_order->coupon_type == 'fixed')
			{
			    $discount_price = $single_order->coupon_value;
			}
			else if($single_order->coupon_type == 'percentage')
			{
				$discount_price = ($total_pricce * $single_order->coupon_value) / 100;
			}
			else
			{
			   $discount_price = "";
			}
			if($additional['setting']->subscription_mode == 0)
		    {
				if($buyer_type == 1)
				{
				$commission =($total_pricce * $setting['setting']->site_exclusive_commission) / 100;
				$new_commission =($another_price * $setting['setting']->site_exclusive_commission) / 100;
				}
				else
				{
				$commission =($total_pricce * $setting['setting']->site_non_exclusive_commission) / 100;
				$new_commission =($another_price * $setting['setting']->site_non_exclusive_commission) / 100;
				}
				$amount_price = $commission;
				$vendor_price = $total_pricce - $commission;
				$item_single_admin_price = $new_commission;
				$item_single_vendor_price =  $another_price - $new_commission;
			}
			else
			{
			    if($count_mode == 1)
				{
				    
				  	$vendor_price = $total_pricce;
				  	$amount_price = 0;
					$item_single_vendor_price = $another_price;
					$item_single_admin_price = 0;
				  
				}
				else
				{
				    
					if($buyer_type == 1)
					{
					$commission =($total_pricce * $setting['setting']->site_exclusive_commission) / 100;
					$new_commission =($another_price * $setting['setting']->site_exclusive_commission) / 100;
					}
					else
					{
					$commission =($total_pricce * $setting['setting']->site_non_exclusive_commission) / 100;
					$new_commission =($another_price * $setting['setting']->site_non_exclusive_commission) / 100;
					}
					$amount_price = $commission;
					$vendor_price = $total_pricce - $commission;
					$item_single_admin_price = $new_commission;
					$item_single_vendor_price =  $another_price - $new_commission;
				}
			}	
			$orderdata = array('purchase_code' => $purchase_code, 'purchase_token' => $purchase_token, 'payment_type' => $payment_method, 'currency_type' => $currency_type, 'currency_type_code' => $currency_type_code, 'item_price' => $item_pricce, 'total_price' => $total_pricce, 'vendor_amount' => $vendor_price, 'admin_amount' => $amount_price, 'discount_price' => $discount_price, 'item_single_vendor_price' => $item_single_vendor_price, 'item_single_admin_price' => $item_single_admin_price);
		    //$orderdata = array('purchase_code' => $purchase_code, 'purchase_token' => $purchase_token, 'payment_type' => $payment_method, 'vendor_amount' => $vendor_price, 'admin_amount' => $amount_price, 'total_price' => $amount);
			Items::singleorderupData($order,$orderdata);
			$item['name'] = Items::singleorderData($order);
			$item_names .= $item['name']->item_name;
					   
		  }
		  $item_names_data = rtrim($item_names,',');
		  
		  
		  if($payment_method == 'paypal')
		  {
		     
			 $paypal = '<form method="post" id="paypal_form" action="'.$paypal_url.'">
			  <input type="hidden" value="_xclick" name="cmd">
			  <input type="hidden" value="'.$paypal_email.'" name="business">
			  <input type="hidden" value="'.$item_names_data.'" name="item_name">
			  <input type="hidden" value="'.$purchase_token.'" name="item_number">
			  <input type="hidden" value="'.$final_amount.'" name="amount">
			  <input type="hidden" value="'.$site_currency.'" name="currency_code">
			  <input type="hidden" value="'.$success_url.'" name="return">
			  <input type="hidden" value="'.$cancel_url.'" name="cancel_return">
			  		  
			</form>';
			$paypal .= '<script>window.paypal_form.submit();</script>';
			echo $paypal;
					 
			 
		  }
		  else if($payment_method == 'nowpayments')
		  {
			     // Specify the accepted cryptocurrencies
					// Validate the form input
				$validator = Validator::make($request->all(), [
					/*'amount' => 'required|numeric',
					'currency' => 'required|in:BTC,ETH,LTC', */
				]);
		
				if ($validator->fails()) {
					return redirect()->back()->withErrors($validator)->withInput();
				}
		
				// Prepare data for the payment request
				if($nowpayments_mode == 0)
				{
				   $nowpayments_payment_url = 'https://api-sandbox.nowpayments.io/v1/payment/';
				   $nowpayments_invoice_url = 'https://api-sandbox.nowpayments.io/v1/invoice';
				   $nowpayment_redirect = 'https://sandbox.nowpayments.io/payment?iid=';
				}
				else
				{
				   $nowpayments_payment_url = 'https://api.nowpayments.io/v1/payment/';
				   $nowpayments_invoice_url = 'https://api.nowpayments.io/v1/invoice';
				   $nowpayment_redirect = 'https://nowpayments.io/payment?iid=';
				}
		        
				// Make a request to NowPayments API using GuzzleHTTP // https://api-sandbox.nowpayments.io/v1 //https://api.nowpayments.io/v1/payment
				$client = new Client();
				$response = $client->post($nowpayments_payment_url, [
					'json' => [
						'price_amount' => $final_amount,
						'price_currency' => $site_currency,
						'pay_currency' => "BTC", 
						//'ipn_callback_url' => "https://nowpayments.io",
  						'order_id'=> $purchase_token,
  					    'order_description' => $item_names_data,
						// Add other parameters as needed
					],
					'headers' => [
						'x-api-key' => $nowpayments_api_key,
					],
				]);
		
				$responseBody = json_decode($response->getBody(), true);
				 //dd($responseBody);
				 
				 $response2 = $client->post($nowpayments_invoice_url, [
					'json' => [
						
						'price_amount' => $responseBody['price_amount'],
						'price_currency' => $site_currency, 
						'order_id' => $responseBody['order_id'],
	                    'order_description' => $item_names_data,
						'ipn_callback_url' => "https://nowpayments.io",
  						'success_url'=> $nowpayments_success,
                        'cancel_url' => $cancel_url,
						// Add other parameters as needed
					],
					'headers' => [
						'x-api-key' => $nowpayments_api_key,
					],
				]);
		
				$responseBody2 = json_decode($response2->getBody(), true);
				 
				//dd($responseBody2);
				 
				//$paymentLink = $responseBody['payment_url'];
				
				return redirect($nowpayment_redirect.$responseBody2['id'].'&paymentId='.$responseBody['payment_id']);
		       
				//return view('payment-success', ['paymentLink' => $paymentLink]);
						
		  }
		  else if($payment_method == 'dodopayments')
		  {
	      // Dodo Payments Integration
	      $dodopayments_mode = config('services.dodopayments.mode', 'test');
	      $dodopayments_api_key = config('services.dodopayments.api_key');
	      
	      if (empty($dodopayments_api_key)) {
	          return redirect()->back()->with('unsuccess', __('DodoPayments API key is not configured. Please check your settings.'));
	      }
	      
	      // Initialize Dodo Payments Service
	      $dodoService = new \Fickrr\Services\DodoPaymentsService(
	          $dodopayments_api_key,
	          $dodopayments_mode
	      );
		      
          // Build Dodo Checkout Session request using product_cart as per docs
          $defaultProductId = config('services.dodopayments.product_id');
          if (empty($defaultProductId)) {
              // Auto-create a Dodo product for this order if not configured
              $createdProduct = $dodoService->createProduct($final_amount, $site_currency, $item_names_data, 'digital_products');
              if (!$createdProduct || empty($createdProduct['product_id'])) {
                  return redirect()->back()->with('unsuccess', __('Failed to create Dodo product for checkout.'));
              }
              $defaultProductId = $createdProduct['product_id'];
          }

          $checkoutData = [
              'product_cart' => [
                  [
                      'product_id' => $defaultProductId,
                      'quantity' => 1,
                  ]
              ],
              'customer' => [
                  'email' => $order_email,
                  'name' => trim($order_firstname . ' ' . $order_lastname),
              ],
              'return_url' => $dodopayments_success,
              'metadata' => [
                  'purchase_token' => (string)$purchase_token,
                  'items' => (string)$item_names_data,
                  'site_currency' => (string)$site_currency,
                  'final_amount' => (string)$final_amount,
                  'payment_type' => 'checkout'
              ]
          ];

          $response = $dodoService->createCheckoutSession($checkoutData);

          if ($response && (isset($response['checkout_url']) || isset($response['url']))) {
              $redirectUrl = $response['checkout_url'] ?? $response['url'];
              return redirect($redirectUrl);
          }
          return redirect()->back()->with('unsuccess', __('Payment initialization failed. Please try again.'));
		  }
		  else if($payment_method == 'cashfree')
		  {
		      
			if($site_currency != 'INR')
			{
			   /* currency conversion */
			   $check_currency = Currencies::CheckCurrencyCount('INR');
			   if($check_currency != 0)
			   {
				   $currency_data = Currencies::getCurrency('INR');
	               $default_currency_rate = $currency_data->currency_rate;
				   $default_price_value = $default_final_amount * $default_currency_rate;
				   $price_amount = round($default_price_value,2);
			   }
			   else
			   {
				     return redirect()->back()->with('error', "Cashfree need 'INR' currency. Please contact administrator");
			   }
			   /* currency conversion */
		   }
		   else
		   {
			   $price_amount = $final_amount;
		   }
			$phone = "9999999999";  
			\Cashfree\Cashfree::$XClientId = $cashfree_api_key;
			\Cashfree\Cashfree::$XClientSecret = $cashfree_api_secret;
			if($cashfree_mode == 0)
			{
			\Cashfree\Cashfree::$XEnvironment = Cashfree\Cashfree::$SANDBOX; //$SANDBOX $PRODUCTION
			$keymode = "sandbox";
			}
			else
			{
			\Cashfree\Cashfree::$XEnvironment = Cashfree\Cashfree::$PRODUCTION;
			$keymode = "production";
			}
			$order_id = "order_".$purchase_token;
			$cashfree = new \Cashfree\Cashfree();
			$x_api_version = "2023-08-01";
			$create_order_request = new \Cashfree\Model\CreateOrderRequest();
			$create_order_request->setOrderAmount($price_amount);
			$create_order_request->setOrderCurrency("INR");
			$create_order_request->setOrderId($order_id);
			$customer_details = new \Cashfree\Model\CustomerDetails();
			$order_meta = new \Cashfree\Model\OrderMeta();
			$customer_details->setCustomerId('customer_'.$user_id);
			$customer_details->setCustomerPhone($phone);
			$customer_details->setCustomerName($order_firstname);
			$customer_details->setCustomerEmail($order_email);
			$order_meta->setReturnUrl($cashfree_success.'?order_id='.$order_id);
			$create_order_request->setCustomerDetails($customer_details);
			$create_order_request->setOrderMeta($order_meta);
			$create_order_request->setOrderNote($item_names_data);
			try {
				$result = $cashfree->PGCreateOrder($x_api_version, $create_order_request);
				//dd($result[0]['order_id']);
				
				//dd($result[0]['payment_session_id']);
				
				$cashfree ='<script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>';
		    $cashfree .= '<script>
		    window.onload = function(){
            const cashfree = Cashfree({
                mode: "'.$keymode.'", 
            });
            
                let checkoutOptions = {
                    paymentSessionId: "'.$result[0]['payment_session_id'].'",
                    redirectTarget: "_self",
                };
                cashfree.checkout(checkoutOptions);
            
			}
           </script>'; 
		    echo $cashfree;
				
				
			} catch (Exception $e) {
				echo 'Exception when calling PGCreateOrder: ', $e->getMessage(), PHP_EOL;
			}
		  

		  }
		  else if($payment_method == 'coinbase')
		  {
		      
			    $url = 'https://api.commerce.coinbase.com/charges';
				$array = [
					'name' => $item_names_data,
					'description' => $item_names_data,
					'local_price' => [
						'amount' => $final_amount,
						'currency' => $site_currency
					],
					'metadata' => [
						'trx' => $purchase_token
					],
					'pricing_type' => "fixed_price",
					'notification_url' => $coinbase_webhooks,
					'redirect_url' => $coinbase_success,
					'cancel_url' => $cancel_url
				];
		
				$yourjson = json_encode($array);
				$ch = curl_init();
				$apiKey = $coinbase_api_key;
				$header = array();
				$header[] = 'Content-Type: application/json';
				$header[] = 'X-CC-Api-Key: ' . "$apiKey";
				$header[] = 'X-CC-Version: 2018-03-22';
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $yourjson);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$result = curl_exec($ch);
				curl_close($ch);
		        $result = json_decode($result);
                if ($result->data->id != '') 
				{
				   return redirect($result->data->hosted_url);
				}
				else
				{
				   return redirect($cancel_url);
				}
		  
		  }
		  else if($payment_method == 'midtrans')
		  {
		        
				if($site_currency != 'IDR')
				 {
				   /* currency conversion */
				   $check_currency = Currencies::CheckCurrencyCount('IDR');
				   if($check_currency != 0)
				   {
				   $currency_data = Currencies::getCurrency('IDR');
	               $default_currency_rate = $currency_data->currency_rate;
				   $default_price_value = $default_final_amount * $default_currency_rate;
				   $price_amount = round($default_price_value,2);
				   }
				   else
				   {
				     return redirect()->back()->with('error', "Midtrans need 'IDR' currency. Please contact administrator");
				   }
				   /* currency conversion */
				 }
				 else
				 {
				   $price_amount = $final_amount;
				 }
				 
				 
				 
				 $finpr = round($price_amount,2);
				    $partamt = $finpr * 100;
				    $myamount = str_replace([',', '.'], ['', ''], $partamt);
					
			    Midtrans\Config::$serverKey = $midtrans_server_key;
				// Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
				Midtrans\Config::$isProduction = $midtrans_mode_status;
				// Set sanitization on (default)
				Midtrans\Config::$isSanitized = true;
				// Set 3DS transaction for credit card to true
				Midtrans\Config::$is3ds = true;
				
				$params = array(
					'transaction_details' => array(
						'order_id' => $purchase_token,
						'gross_amount' => $myamount,
					),
					'customer_details' => array(
						'first_name' => $order_firstname,
						'last_name' => $order_firstname,
						'email' => $order_email,
						
					),
					'callbacks' => array
					(
					  'finish' => $midtrans_success
					),
					
				);
				
				$snapToken = Midtrans\Snap::getSnapToken($params);
		        return redirect($midtrans_trans_url.$snapToken);
		
			  
		  }
		  else if($payment_method == 'robokassa')
		  {
		  
		      if($site_currency != 'RUB')
			 {
			   
			   /* currency conversion */
			   $check_currency = Currencies::CheckCurrencyCount('RUB');
			   if($check_currency != 0)
			   {
				   $currency_data = Currencies::getCurrency('RUB');
	               $default_currency_rate = $currency_data->currency_rate;
				   $default_price_value = $default_final_amount * $default_currency_rate;
				   $price_amount = round($default_price_value,2);
			   }
			   else
			   {
				     return redirect()->back()->with('error', "Robokassa need 'RUB' currency. Please contact administrator");
			   }
			   /* currency conversion */
			 }
			 else
			 {
			   $price_amount = $final_amount;
			 }
			 $mrh_login = $shop_identifier;
			 $mrh_pass1 = $robokassa_password_1;
			 $inv_id = 0;
             $inv_desc = $item_names_data;
             $out_summ = $price_amount;
             $shp_item = "1";
             $in_curr = "";
			 $culture = "en";
			 session()->put('purchase_token',$purchase_token);
			 session()->put('robokassa_type','checkout');
             $crc  = md5("$mrh_login:$out_summ:$inv_id:$mrh_pass1:Shp_item=$shp_item");
		     $robokassa = '<form method="post" id="robokassa_form" action="https://auth.robokassa.ru/Merchant/Index.aspx">
			  <input type="hidden" value="'.$mrh_login.'" name="MerchantLogin">
			  <input type="hidden" value="'.$out_summ.'" name="OutSum">
			  <input type="hidden" value="'.$inv_id.'" name="InvId">
			  <input type="hidden" value="'.$inv_desc.'" name="Description">
			  <input type="hidden" value="'.$crc.'" name="SignatureValue">
			  <input type="hidden" value="'.$shp_item.'" name="Shp_item">
			  <input type="hidden" value="'.$in_curr.'" name="IncCurrLabel">
			  <input type="hidden" value="'.$culture.'" name="Culture">		  
			</form>';
			$robokassa .= '<script>window.robokassa_form.submit();</script>';
			echo $robokassa;
			 
		  
		  
		  }
		  else if($payment_method == 'mollie')
	      {
		     
			   
			 $price_amount = ''.sprintf('%0.2f', round($final_amount,2)).'';
			 $payment = Mollie::api()->payments()->create([
			'amount' => [
				'currency' => $site_currency, // Type of currency you want to send
				'value' => $price_amount, // You must send the correct number of decimals, thus we enforce the use of strings
			],
			'description' => $item_names_data, 
			'redirectUrl' => $mollie_success_url, // after the payment completion where you to redirect
			]);
			
			$payment = Mollie::api()->payments()->get($payment->id);
			
			session()->put('payment_id',$payment->id);
			session()->put('purchase_token',$purchase_token);
		
			// redirect customer to Mollie checkout page
			return redirect($payment->getCheckoutUrl(), 303);
			 
		  }
		  else if($payment_method == 'aamarpay')
		  {
		     $aamarpay = '<form method="post" id="aamarpay_form" action="'.$aamarpay_url.'">
			  <input type="hidden" name="store_id" value="'.$aamarpay_store_id.'">
              <input type="hidden" name="signature_key" value="'.$aamarpay_signature_key.'">
			  <input type="hidden" name="tran_id" value="'.$purchase_token.'">
			  <input type="hidden" name="amount" value="'.$final_amount.'">
			  <input type="hidden" name="currency" value="'.$site_currency.'">
			  <input type="hidden" name="cus_name" value="'.$order_firstname.'">
			  <input type="hidden" name="cus_email" value="'.$order_email.'">
			  <input type="hidden" name="cus_add1" value="'.$order_email.'">
			  <input type="hidden" name="cus_phone" value="'.$order_email.'">
			  <input type="hidden" name="desc" value="'.$item_names_data.'">
			  <input type="hidden" name="success_url" value="'.$aamarpay_success_url.'">
              <input type="hidden" name="fail_url" value= "'.$aamarpay_failed_url.'">
              <input type="hidden" name="cancel_url" value= "'.$aamarpay_cancel_url.'">
			 
			  		  
			</form>';
			$aamarpay .= '<script>window.aamarpay_form.submit();</script>';
			echo $aamarpay; 
		    
		  }
		  else if($payment_method == 'instamojo')
		  {
		       if($site_currency != 'INR')
			   {
			   /* currency conversion */
			   $check_currency = Currencies::CheckCurrencyCount('INR');
			   if($check_currency != 0)
			   {
				   $currency_data = Currencies::getCurrency('INR');
	               $default_currency_rate = $currency_data->currency_rate;
				   $default_price_value = $default_final_amount * $default_currency_rate;
				   $price_amount = round($default_price_value,2);
			   }
			   else
			   {
				     return redirect()->back()->with('error', "Instamojo need 'INR' currency. Please contact administrator");
			   }
			   /* currency conversion */
			   }
			   else
			   {
			   $price_amount = $final_amount;
			   }
			    $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $instamojo_payment_link);
				curl_setopt($ch, CURLOPT_HEADER, FALSE);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
				curl_setopt($ch, CURLOPT_HTTPHEADER,
							array("X-Api-Key:".$instamojo_api_key,
								  "X-Auth-Token:".$instamojo_auth_token));
				$payload = Array(
					'purpose' => $item_names_data,
					'amount' => $price_amount,
					//'phone' => '9876543210',
					'buyer_name' => $order_firstname,
					'redirect_url' => $instamojo_success_url,
					'send_email' => true,
					//'webhook' => $instamojo_success_url,
					//'send_sms' => false,
					'email' => $order_email,
					'allow_repeated_payments' => false
				);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
				$response = curl_exec($ch);
				curl_close($ch);
				$response = json_decode($response); 
				
				return redirect($response->payment_request->longurl);
				
				
		  
		  }
		  else if($payment_method == 'sslcommerz')
		  {
		      if($site_currency != 'BDT')
			   {
			   
			   
			   /* currency conversion */
			   $check_currency = Currencies::CheckCurrencyCount('BDT');
			   if($check_currency != 0)
			   {
				   $currency_data = Currencies::getCurrency('BDT');
	               $default_currency_rate = $currency_data->currency_rate;
				   $default_price_value = $default_final_amount * $default_currency_rate;
				   $price_amount = round($default_price_value,2);
			   }
			   else
			   {
				     return redirect()->back()->with('error', "Sslcommerz need 'BDT' currency. Please contact administrator");
			   }
			   /* currency conversion */
			   }
			   else
			   {
			   $price_amount = $final_amount;
			   }
		      $sslc = new SSLCommerz();
				$sslc->amount($price_amount)
					->trxid($purchase_token)
					->product($item_names_data)
					->customer($order_firstname,$order_email)
					->setUrl([route('sslcommerz.successpage'), route('sslcommerz.failurepage'), route('sslcommerz.cancelpage'), route('sslcommerz.ipnpage')])
					->setCurrency('BDT');
				return $sslc->make_payment();
				//BDT

        /**
         * 
         *  USE:  $sslc->make_payment(true) FOR CHECKOUT INTEGRATION
         * 
         * */
		  }
		  else if($payment_method == 'mercadopago')
		  {
		    
		    if($site_currency != 'BRL')
			 {
			   /* currency conversion */
			   $check_currency = Currencies::CheckCurrencyCount('BRL');
			   if($check_currency != 0)
			   {
				   $currency_data = Currencies::getCurrency('BRL');
	               $default_currency_rate = $currency_data->currency_rate;
				   $default_price_value = $default_final_amount * $default_currency_rate;
				   $price_amount = round($default_price_value,2);
			   }
			   else
			   {
				     return redirect()->back()->with('error', "Mercadopago need 'BRL' currency. Please contact administrator");
			   }
			   /* currency conversion */
			   
			 }
			 else
			 {
			   $price_amount = $final_amount;
			 }
			include(app_path() . '/mercadopago/autoload.php');
			 MercadoPago\SDK::setAccessToken($mercadopago_client_secret);
			 $preference = new MercadoPago\Preference();
             $item = new MercadoPago\Item();
             $item->title = $item_names_data;
             $item->quantity = 1;
             $item->unit_price = $price_amount;
		     $item->id = $purchase_token;
             $item->currency_id = "BRL";
             $preference->items = array($item);
             $preference->back_urls = array(
				"success" => $mercadopago_success,
				"failure" => $mercadopago_failure,
				"pending" => $mercadopago_pending
			);
            $preference->payment_methods = array(
				"excluded_payment_types" => array(
				array("id" => "ticket")   
				) );
            $preference->auto_return = "approved";
            $preference->save();
			if($mercadopago_mode == 1)
			{
			return redirect($preference->init_point);
			}
			else
			{
			return redirect($preference->sandbox_init_point);
			}
			 
			
		    
		  
		  }
		  /* coinpayments */
		  else if($payment_method == 'coinpayments')
		  {
		     $coinpayments = '<form action="https://www.coinpayments.net/index.php" method="post" id="coinpayments_form">
								<input type="hidden" name="cmd" value="_pay">
								<input type="hidden" name="reset" value="1">
								<input type="hidden" name="merchant" value="'.$coinpayments_merchant_id.'">
								<input type="hidden" name="item_name" value="'.$item_names_data.'">	
								<input type="hidden" name="item_desc" value="'.$item_names_data.'">
								<input type="hidden" name="item_number" value="'.$purchase_token.'">
								<input type="hidden" name="currency" value="'.$site_currency.'">
								<input type="hidden" name="amountf" value="'.$final_amount.'">
								<input type="hidden" name="want_shipping" value="0">
								<input type="hidden" name="success_url" value="'.$coinpayments_success_url.'">	
								<input type="hidden" name="cancel_url" value="'.$cancel_url.'">	
							</form>';
			$coinpayments .= '<script>window.coinpayments_form.submit();</script>';
			echo $coinpayments;				
		  }
		  /* coinpayments */
		  /* payfast */
		  else if($payment_method == 'payfast')
		  {
		     if($site_currency != 'ZAR')
			   {
			   /* currency conversion */
			   $check_currency = Currencies::CheckCurrencyCount('ZAR');
			   if($check_currency != 0)
			   {
				   $currency_data = Currencies::getCurrency('ZAR');
	               $default_currency_rate = $currency_data->currency_rate;
				   $default_price_value = $default_final_amount * $default_currency_rate;
				   $price_amount = round($default_price_value,2);
			   }
			   else
			   {
				     return redirect()->back()->with('error', "Payfast need 'ZAR' currency. Please contact administrator");
			   }
			   /* currency conversion */
			   
			   }
			   else
			   {
			   $price_amount = $final_amount;
			   }
			 $payfast = '<form method="post" id="payfast_form" action="'.$payfast_url.'">
			  <input type="hidden" name="merchant_id" value="'.$payfast_merchant_id.'">
   			  <input type="hidden" name="merchant_key" value="'.$payfast_merchant_key.'">
   			  <input type="hidden" name="amount" value="'.$price_amount.'">
   			  <input type="hidden" name="item_name" value="'.$item_names_data.'">
			  <input type="hidden" name="item_description" value="'.$item_names_data.'">
			  <input type="hidden" name="name_first" value="'.$order_firstname.'">
			  <input type="hidden" name="name_last" value="'.$order_firstname.'">
			  <input type="hidden" name="email_address" value="'.$order_email.'">
			  <input type="hidden" name="m_payment_id" value="'.$purchase_token.'">
              <input type="hidden" name="email_confirmation" value="1">
              <input type="hidden" name="confirmation_address" value="'.$order_email.'"> 
              <input type="hidden" name="return_url" value="'.$payfast_success_url.'">
			  <input type="hidden" name="cancel_url" value="'.$cancel_url.'">
			  <input type="hidden" name="notify_url" value="'.$cancel_url.'">
			</form>';
			$payfast .= '<script>window.payfast_form.submit();</script>';
			echo $payfast;
					 
			 
		  }
		  
		  /* payfast */
		  else if($payment_method == 'ipay')
		  {
		  
		  	 if($site_currency != 'KES')
			   {
		       /* currency conversion */
			   $check_currency = Currencies::CheckCurrencyCount('KES');
			   if($check_currency != 0)
			   {
				   $currency_data = Currencies::getCurrency('KES');
	               $default_currency_rate = $currency_data->currency_rate;
				   $default_price_value = $default_final_amount * $default_currency_rate;
				   $price_amount = round($default_price_value,2);
			   }
			   else
			   {
				     return redirect()->back()->with('error', "iPay need 'KES' currency. Please contact administrator");
			   }
			   /* currency conversion */
			   
			   }
			   else
			   {
			   $price_amount = $final_amount;
			   }
		     $fields = array("live"=> $ipay_mode, // 0
                    "oid"=> $purchase_token,
                    "inv"=> $purchase_token,
                    "ttl"=> $price_amount,
                    "tel"=> "000000000000",
                    "eml"=> $order_email,
                    "vid"=> $ipay_vendor_id, // demo
                    "curr"=> "KES",
                    "cbk"=> $ipay_callback,
                    "cst"=> "1",
                    "crl"=> "2"
                    );
            
			$datastring =  $fields['live'].$fields['oid'].$fields['inv'].$fields['ttl'].$fields['tel'].$fields['eml'].$fields['vid'].$fields['curr'].$fields['cbk'].$fields['cst'].$fields['crl'];		
			 $hashkey = $ipay_hash_key; // demoCHANGED
			 $generated_hash = hash_hmac('sha1',$datastring , $hashkey);
			 
			 $ipay = '<form method="post" id="ipay_form" action="'.$ipay_url.'">';
			 foreach ($fields as $key => $value) 
			 { 
			  $ipay .= '<input type="hidden" value="'.$value.'" name="'.$key.'">';
			 } 
			$ipay .= '<input name="hsh" type="hidden" value="'.$generated_hash.'">';  		  
			$ipay .= '</form>';
			$ipay .= '<script>window.ipay_form.submit();</script>';
			echo $ipay;
					 
			 
		  }
		  else if($payment_method == 'coingate')
		  {
		  
		     \CoinGate\CoinGate::config(array(
					'environment'               => $coingate_mode_status, // sandbox OR live
					'auth_token'                => $coingate_auth_token,
					'curlopt_ssl_verifypeer'    => TRUE // default is false
					 ));
					 
			  $post_params = array(
			       'id'                => $purchase_token,
                   'order_id'          => $purchase_token,
                   'price_amount'      => $final_amount,
                   'price_currency'    => $site_currency,
                   'receive_currency'  => $site_currency,
                   'callback_url'      => $coingate_callback,
                   'cancel_url'        => $cancel_url,
                   'success_url'       => $coingate_callback,
                   'title'             => $item_names_data,
                   'description'       => $item_names_data
				   
               );
                
				$order = \CoinGate\Merchant\Order::create($post_params);
				
				if ($order) {
					//echo $order->status;
					
					Cache::put('coingate_id', $order->id, now()->addDays(1));
					Cache::put('purchase_id', $order->order_id, now()->addDays(1));
					//echo $order->id;
					return redirect($order->payment_url);
					
					
				} else {
					return redirect($cancel_url);
				}
					  //return view('test');
	  		 
			 
		  }
		  else if($payment_method == 'twocheckout')
		  {
		    
			$two_checkout = '<form method="post" id="two_checkout_form" action="https://www.2checkout.com/checkout/purchase">
			  <input type="hidden" name="sid" value="'.$two_checkout_account.'" />
			  <input type="hidden" name="mode" value="2CO" />
			  <input type="hidden" name="li_0_type" value="product" />
			  <input type="hidden" name="li_0_name" value="'.$item_names_data.'" />
			  <input type="hidden" name="li_0_price" value="'.$final_amount.'" />
			  <input type="hidden" name="currency_code" value="'.$site_currency.'" />
			  <input type="hidden" name="merchant_order_id" value="'.$purchase_token.'" />';
			  if($two_checkout_mode == 0)
			  {
			  $two_checkout .= '<input type="hidden" name="card_holder_name" value="John Doe" />
			                 <input type="hidden" name="demo" value="Y" />';
			  
			  }
			  $two_checkout .= '<input type="hidden" name="street_address" value="" />
			  <input type="hidden" name="city" value="" />
			  <input type="hidden" name="state" value="" />
			  <input type="hidden" name="zip" value="" />
			  <input type="hidden" name="country" value="" />
			  <input type="hidden" name="x_receipt_link_url" value="product_item" />
			  <input type="hidden" name="email" value="'.$order_email.'" />
			  </form>';
			$two_checkout .= '<script>window.two_checkout_form.submit();</script>';
			echo $two_checkout;
			
			/*$record = array('final_amount' => $final_amount, 'purchase_token' => $purchase_token, 'payment_method' => $payment_method, 'item_names_data' => $item_names_data, 'site_currency' => $site_currency, 'website_url' => $website_url, 'two_checkout_private' => $two_checkout_private, 'two_checkout_account' => $two_checkout_account, 'two_checkout_mode' => $two_checkout_mode, 'token' => $token, 'two_checkout_publishable' => $two_checkout_publishable);
       return view('order-confirm')->with($record);*/
			
		  }
		  else if($payment_method == 'payumoney')
		  {
		        if($site_currency != 'INR')
			   {
		       /* currency conversion */
			   $check_currency = Currencies::CheckCurrencyCount('INR');
			   if($check_currency != 0)
			   {
				   $currency_data = Currencies::getCurrency('INR');
	               $default_currency_rate = $currency_data->currency_rate;
				   $default_price_value = $default_final_amount * $default_currency_rate;
				   $price_amount = round($default_price_value,2);
			   }
			   else
			   {
				     return redirect()->back()->with('error', "Payumoney need 'INR' currency. Please contact administrator");
			   }
			   /* currency conversion */
			   
			   }
			   else
			   {
			   $price_amount = $final_amount;
			   }
		        $action = '';
				$txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
				$posted = array();
				$posted = array(
					'key' => $MERCHANT_KEY,
					'txnid' => $txnid,
					'amount' => $price_amount,
					'udf1' => $purchase_token,
					'firstname' => $order_firstname,
					'email' => $order_email,
					'productinfo' => $item_names_data,
					'surl' => $website_url.'/payu_success',
					'furl' => $website_url.'/cancel',
					'service_provider' => 'payu_paisa',
				);
				$payu_success = $website_url.'/payu_success';
				
				if(empty($posted['txnid'])) {
					$txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
				} 
				else 
				{
					$txnid = $posted['txnid'];
				}
				$hash = '';
				$hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
				if(empty($posted['hash']) && sizeof($posted) > 0) {
					$hashVarsSeq = explode('|', $hashSequence);
					$hash_string = '';  
					foreach($hashVarsSeq as $hash_var) {
						$hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
						$hash_string .= '|';
					}
					$hash_string .= $SALT;
				
					$hash = strtolower(hash('sha512', $hash_string));
					$action = $PAYU_BASE_URL . '/_payment';
				} 
				elseif(!empty($posted['hash'])) 
				{
					$hash = $posted['hash'];
					$action = $PAYU_BASE_URL . '/_payment';
				}
				$paymoney = '<form action="'.$action.'" method="post" name="payumoney_form">
            <input type="hidden" name="key" value="'.$MERCHANT_KEY.'" />
            <input type="hidden" name="hash" value="'.$hash.'"/>
            <input type="hidden" name="txnid" value="'.$txnid.'" />
			<input type="hidden" name="udf1" value="'.$purchase_token.'" />
            <input type="hidden" name="amount" value="'.$price_amount.'" />
            <input type="hidden" name="firstname" id="firstname" value="'.$order_firstname.'" />
            <input type="hidden" name="email" id="email" value="'.$order_email.'" />
            <input type="hidden" name="productinfo" value="'.$item_names_data.'">
            <input type="hidden" name="surl" value="'.$payu_success.'" />
            <input type="hidden" name="furl" value="'.$cancel_url.'" />
            <input type="hidden" name="service_provider" value="payu_paisa"  />
			</form>';
			/*if(!$hash) {*/
            $paymoney .= '<script>window.payumoney_form.submit();</script>';
			/*}*/
			echo $paymoney;

		  }
		  
		  
		  else if($payment_method == 'payhere')
		  {
		      if($site_currency != 'LKR')
			   {
		       /* currency conversion */
			   $check_currency = Currencies::CheckCurrencyCount('LKR');
			   if($check_currency != 0)
			   {
				   $currency_data = Currencies::getCurrency('LKR');
	               $default_currency_rate = $currency_data->currency_rate;
				   $default_price_value = $default_final_amount * $default_currency_rate;
				   $price_amount = round($default_price_value,2);
			   }
			   else
			   {
				     return redirect()->back()->with('error', "PayHere need 'LKR' currency. Please contact administrator");
			   }
			   /* currency conversion */
			   
			   }
			   else
			   {
			   $price_amount = $final_amount;
			   }
		      $payhere = '<form method="post" action="'.$payhere_url.'" id="payhere_form">   
							<input type="hidden" name="merchant_id" value="'.$payhere_merchant_id.'">
							<input type="hidden" name="return_url" value="'.$payhere_success_url.'">
							<input type="hidden" name="cancel_url" value="'.$cancel_url.'">
							<input type="hidden" name="notify_url" value="'.$cancel_url.'">  
							<input type="hidden" name="order_id" value="'.$purchase_token.'">
							<input type="hidden" name="items" value="'.$item_names_data.'"><br>
							<input type="hidden" name="currency" value="LKR">
							<input type="hidden" name="amount" value="'.$price_amount.'">  
							
							<input type="hidden" name="first_name" value="'.$order_firstname.'">
							<input type="hidden" name="last_name" value="'.$order_lastname.'"><br>
							<input type="hidden" name="email" value="'.$order_email.'">
							<input type="hidden" name="phone" value="'.$order_firstname.'"><br>
							<input type="hidden" name="address" value="'.$order_firstname.'">
							<input type="hidden" name="city" value="'.$order_firstname.'">
							<input type="hidden" name="country" value="'.$order_firstname.'">
							  
						</form>'; 
						$payhere .= '<script>window.payhere_form.submit();</script>';
			            echo $payhere;
		  }
		  
		  else if($payment_method == 'paystack')
		  {
		       if($site_currency != 'NGN')
			   {
		       
			   /* currency conversion */
			   $check_currency = Currencies::CheckCurrencyCount('NGN');
			   if($check_currency != 0)
			   {
				   $currency_data = Currencies::getCurrency('NGN');
	               $default_currency_rate = $currency_data->currency_rate;
				   $default_price_value = $default_final_amount * $default_currency_rate;
				   $price_amount = round($default_price_value,2);
				   $price_amount = $price_amount * 100;
			   }
			   else
			   {
				     return redirect()->back()->with('error', "PayStack need ".$setting['setting']->paystack_default_currency." currency. Please contact administrator");
			   }
			   /* currency conversion */
			   
			   }
			   else
			   {
			   $price_amount = $final_amount * 100;
			   }
		       $callback = $website_url.'/paystack';
			   $csf_token = csrf_token();
			   
			   $paystack = '<form method="post" id="stack_form" action="'.route('paystack').'">
					  <input type="hidden" name="_token" value="'.$csf_token.'">
					  <input type="hidden" name="email" value="'.$user_email.'" >
					  <input type="hidden" name="order_id" value="'.$purchase_token.'">
					  <input type="hidden" name="amount" value="'.$price_amount.'">
					  <input type="hidden" name="quantity" value="1">
					  <input type="hidden" name="currency" value="'.$setting['setting']->paystack_default_currency.'">
					  <input type="hidden" name="reference" value="'.$reference.'">
					  <input type="hidden" name="callback_url" value="'.$callback.'">
					  <input type="hidden" name="metadata" value="'.$purchase_token.'">
					  <input type="hidden" name="key" value="'.$setting['setting']->paystack_secret_key.'">
					</form>';
					$paystack .= '<script>window.stack_form.submit();</script>';
					echo $paystack;
			 
		  }
		  else if($payment_method == 'flutterwave')
		  {
		      if($site_currency != 'NGN')
			   {
		       
			   /* currency conversion */
			   $check_currency = Currencies::CheckCurrencyCount('NGN');
			   if($check_currency != 0)
			   {
				   $currency_data = Currencies::getCurrency('NGN');
	               $default_currency_rate = $currency_data->currency_rate;
				   $default_price_value = $default_final_amount * $default_currency_rate;
				   $price_amount = round($default_price_value,2);
				   
			   }
			   else
			   {
				     return redirect()->back()->with('error', "Flutterwave need ".$setting['setting']->flutterwave_default_currency." currency. Please contact administrator");
			   }
			   /* currency conversion */
			   
			   }
			   else
			   {
			   $price_amount = $final_amount;
			   }
		       $flutterwave_callback = $website_url.'/flutterwave';
			   $phone_number = "";
			   $csf_token = csrf_token();
			   $flutterwave = '<form method="post" id="flutterwave_form" action="https://checkout.flutterwave.com/v3/hosted/pay">
	          <input type="hidden" name="public_key" value="'.$flutterwave_public_key.'" />
	          <input type="hidden" name="customer[email]" value="'.$user_email.'" >
			  <input type="hidden" name="customer[phone_number]" value="'.$phone_number.'" />
			  <input type="hidden" name="customer[name]" value="'.$order_firstname.'" />
			  <input type="hidden" name="tx_ref" value="'.$purchase_token.'" />
			  <input type="hidden" name="amount" value="'.$price_amount.'">
			  <input type="hidden" name="currency" value="'.$setting['setting']->flutterwave_default_currency.'">
			  <input type="hidden" name="meta[token]" value="'.$csf_token.'">
			  <input type="hidden" name="redirect_url" value="'.$flutterwave_callback.'">
			</form>';
			$flutterwave .= '<script>window.flutterwave_form.submit();</script>';
			echo $flutterwave;
			   
			   
			   
		  }
		  
		  else if($payment_method == 'iyzico')
		  {
		       if($site_currency != 'TRY')
			   {
		       /* currency conversion */
			   $check_currency = Currencies::CheckCurrencyCount('TRY');
			   if($check_currency != 0)
			   {
				   $currency_data = Currencies::getCurrency('TRY');
	               $default_currency_rate = $currency_data->currency_rate;
				   $default_price_value = $default_final_amount * $default_currency_rate;
				   $price_amount = round($default_price_value,2);
				   
			   }
			   else
			   {
				     return redirect()->back()->with('error', "Iyzico need 'TRY' currency. Please contact administrator");
			   }
			   /* currency conversion */
			   
			   }
			   else
			   {
			   $price_amount = number_format((float)$final_amount, 2, '.', '');
			   }
			 
		     $endpoint = $website_url."/app/iyzipay-php/iyzico.php";
			 $client = new Client(['base_uri' => $endpoint]);
             $api_key = $iyzico_api_key;
			 $secret_key = $iyzico_secret_key;
			 $iyzi_url = $iyzico_url;
			 $purchased_token = $purchase_token;
			 $amount = $price_amount;
			 $userids = $user_id;
			 $usernamer = $order_firstname;
             $response = $client->request('GET', $endpoint, ['query' => [
				'iyzico_api_key' => $api_key, 
				'iyzico_secret_key' => $secret_key,
				'iyzico_url' => $iyzi_url,
				'purchase_token' => $purchased_token,
				'price_amount' => $amount,
				'user_id' => $userids,
				'username' => $usernamer,
				'email' => $user_email,
				'user_token' => $user_token,
				'item_name' => $item_names_data,
				'iyzico_success_url' => $iyzico_success_url,
				
			]]);
        
            echo $response->getBody();
            //$iyzico_api_key $iyzico_secret_key $iyzico_url
			 
			 
			/*$client = new \GuzzleHttp\Client();
		    $endpoint = $website_url."/iyzico.php";
			$txnid = 1234;
			$amount = 112;
			$email = 2212;
			$phone = "0000000000";
			$response = $client->request('GET', $endpoint, ['query' => [
				'txnid' => $txnid, 
				'amount' => $amount,
				'email' => $email,
				'phone' => $phone,
			]]);
			$contents = json_decode($response->getBody()->getContents(),true);
		    $view_data = json_encode($contents);
			dd($response->getBody());*/
			
		  
		  }
		  
		  else if($payment_method == 'razorpay')
		  {
		       $additional['settings'] = Settings::editAdditional();
			   if($site_currency != 'INR')
			   {
		       /* currency conversion */
			   $check_currency = Currencies::CheckCurrencyCount('INR');
			   if($check_currency != 0)
			   {
				   $currency_data = Currencies::getCurrency('INR');
	               $default_currency_rate = $currency_data->currency_rate;
				   $default_price_value = $default_final_amount * $default_currency_rate;
				   $price_amount = round($default_price_value,2);
				   $price_amount = $price_amount * 100;
			   }
			   else
			   {
				     return redirect()->back()->with('error', "Razorpay need 'INR' currency. Please contact administrator");
			   }
			   /* currency conversion */
			   
			   }
			   else
			   {
			   $price_amount = $final_amount * 100;
			   }
			   $csf_token = csrf_token();
			   $logo_url = $website_url.'/public/storage/settings/'.$setting['setting']->site_logo;
			   $script_url = $website_url.'/resources/views/theme/js/vendor.min.js';
			   $callback = $website_url.'/razorpay';
			   $razorpay = '
			   <script type="text/javascript" src="'.$script_url.'"></script>
			   <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
			   <script>
				var options = {
					"key": "'.$razorpay_key.'",
					"amount": "'.$price_amount.'", 
					"currency": "INR",
					"name": "'.$item_names_data.'",
					"description": "'.$purchase_token.'",
					"image": "'.$logo_url.'",
					"callback_url": "'.$callback.'",
					"prefill": {
						"name": "'.$order_firstname.'",
						"email": "'.$order_email.'"
						
					},
					"notes": {
						"address": "'.$order_firstname.'"
						
						
					},
					"theme": {
						"color": "'.$setting['setting']->site_theme_color.'"
					}
				};
				var rzp1 = new Razorpay(options);
				rzp1.on("payment.failed", function (response){
						alert(response.error.code);
						alert(response.error.description);
						alert(response.error.source);
						alert(response.error.step);
						alert(response.error.reason);
						alert(response.error.metadata);
				});
				
				$(window).on("load", function() {
					 rzp1.open();
					e.preventDefault();
					});
				</script>';
				echo $razorpay;
					
					
		  }
		  else if($payment_method == 'localbank')
		  {
		    $bank_details = $setting['setting']->local_bank_details;
		    $bank_data = array('purchase_token' => $purchase_token, 'bank_details' => $bank_details);
	        return view('bank-details')->with($bank_data);
		  }
		  else if($payment_method == 'wallet')
		  {
		      if($buyer_wallet_amount >= $final_amount)
			   {    
			        $purchased_token = $purchase_token;
		    		$payment_status = 'completed';
					$orderdata = array('order_status' => $payment_status);
					$checkoutdata = array('payment_status' => $payment_status);
					Items::singleordupdateData($purchased_token,$orderdata);
					Items::singlecheckoutData($purchased_token,$checkoutdata);
					$token = $purchased_token;
					$checking = Items::getcheckoutData($token);
					/* customer email */
					$currency = $site_currency;
					$admin_name = $setting['setting']->sender_name;
					$admin_email = $setting['setting']->sender_email;
					$customer['info'] = Members::singlevendorData($checking->user_id);
					$buyer_name = $customer['info']->name;
					$buyer_email = $customer['info']->email;
					$amount = $checking->total;
					$order_id = $checking->purchase_token;
					$payment_type = $checking->payment_type;
					$payment_date = $checking->payment_date;
					$payment_status = $checking->payment_status;
					$record_customer = array('buyer_name' => $buyer_name, 'buyer_email' => $buyer_email, 'amount' => $amount, 'order_id' => $order_id, 'currency' => $currency, 'payment_type' => $payment_type, 'payment_date' => $payment_date, 'payment_status' => $payment_status);
							
														  $checktemp = EmailTemplate::checkTemplate(21);
														  if($checktemp != 0)
														  {
														  $template_view['mind'] = EmailTemplate::viewTemplate(21);
														  $template_subject = $template_view['mind']->et_subject;
														  }
														  else
														  {
														  $template_subject = "Item Purchase Notifications";
														  }
														  
							Mail::send('item_purchase_mail', $record_customer , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
												$message->to($buyer_email, $buyer_name)
														->subject($template_subject);
												$message->from($admin_email,$admin_name);
											});
					/* customer email */
					$order_id = $checking->order_ids;
					$order_loop = explode(',',$order_id);
					 
					 $earn_wallet = $buyer_wallet_amount - $default_final_amount;
					$walet_data = array('earnings' => $earn_wallet); 
					Members::updateData($user_token,$walet_data); 
					$item_full_names = "";
					  foreach($order_loop as $order)
					  {
						
						$getitem['item'] = Items::getorderData($order);
						$item_full_names .= $getitem['item']->item_name.',';
						$token = $getitem['item']->item_token;
						$item['display'] = Items::solditemData($token);
						$item_sold = $item['display']->item_sold + 1;
						$item_token = $token;
						/* serials key */
						if($item['display']->file_type == 'serial')
						{
						  if($item['display']->item_delimiter == 'comma')
						  {
							  $spilit_value = explode(",", $item['display']->item_serials_list);
							  $split = "";
							  for ($i=0; $i < $getitem['item']->item_serial_stock; $i++) 
							  {
								   $split .= $spilit_value[$i].",";
							  }
							  $first_key = rtrim($split, ",");
							  $take_key = $split;
							  $balance_key = ltrim($item['display']->item_serials_list, $take_key);
						  }
						  else
						  {
							 $spilit_value=explode( "\n", $item['display']->item_serials_list);
							 $split = "";
							  for ($i=0; $i < $getitem['item']->item_serial_stock; $i++) 
							  {
								   $split .= $spilit_value[$i]."\n";
							  }
							  $changekey = str_replace("\n",",",$split);
							  $first_key = rtrim($changekey, ",");
							  $take_key = $split;
							  $balance_key = ltrim($item['display']->item_serials_list, $take_key);
							
						   }
						  
						}
						else
						{
						   $first_key = "";
						   $balance_key = "";
						}
						
						$key_data = array('item_order_serial_key' => $first_key);
						Items::updateCoupon($order,$key_data);
						$data = array('item_sold' => $item_sold, 'item_serials_list' => $balance_key);
						Items::updateitemData($item_token,$data);
						/* serials key */ 
						/* manual payment verification : OFF */
						if($setting['setting']->payment_verification == 0)
						{
						   
							  $ordered['data'] = Items::singleorderData($order);
							  $user_id = $ordered['data']->user_id;
							  $item_user_id = $ordered['data']->item_user_id;
							  $vendor_amount = $ordered['data']->vendor_amount;
							  $total_price = $ordered['data']->total_price;
							  $admin_amount = $ordered['data']->admin_amount;
							  
							  $vendor['info'] = Members::singlevendorData($item_user_id);
							  $user_token = $vendor['info']->user_token;
							  $to_name = $vendor['info']->name;
							  $to_email = $vendor['info']->email;
							  $vendor_earning = $vendor['info']->earnings + $ordered['data']->item_single_vendor_price;
							  $record = array('earnings' => $vendor_earning);
							  Members::updatepasswordData($user_token, $record);
							  
							  $admin['info'] = Members::adminData();
							  $admin_token = $admin['info']->user_token;
							  $admin_earning = $admin['info']->earnings + $ordered['data']->item_single_admin_price;
							  $admin_record = array('earnings' => $admin_earning);
							  Members::updateadminData($admin_token, $admin_record);
							  
							  $orderdata = array('approval_status' => 'payment released to vendor');
							  Items::singleorderupData($order,$orderdata);
							  $check_email_support = Members::getuserSubscription($vendor['info']->id);
							  if($check_email_support == 1)
							  {
								  $record_data = array('to_name' => $to_name, 'to_email' => $to_email, 'vendor_amount' => $vendor_amount, 'currency' => $currency);
								  /* email template code */
								  $checktemp = EmailTemplate::checkTemplate(18);
								  if($checktemp != 0)
								  {
								  $template_view['mind'] = EmailTemplate::viewTemplate(18);
								  $template_subject = $template_view['mind']->et_subject;
								  }
								  else
								  {
								  $template_subject = "New Payment Approved";
								  }
								  /* email template code */
								  Mail::send('admin.vendor_payment_mail', $record_data , function($message) use ($admin_name, $admin_email, $to_name, $to_email, $template_subject) {
											$message->to($to_email, $to_name)
													->subject($template_subject);
											$message->from($admin_email,$admin_name);
										});
							  }			
						 
						   
						}
						/* manual payment verification : OFF */
						
						
					  }
					  /* referral per sale earning */
					    $additional_setting = Settings::editAdditional();
						if($additional_setting->affiliate_referral == 1)
						{
							$logged_id = $user_id;
							$buyer_details = Members::singlebuyerData($logged_id);
							$referral_by = $buyer_details->referral_by;
							
							/* new code */
							$calprice = $checking->total;
							if($additional_setting->per_sale_referral_commission_type == 'percentage')
							{
							$per_sale_commission = ($additional_setting->per_sale_referral_commission * $calprice) / 100;
							}
							else
							{
							$per_sale_commission = $additional_setting->per_sale_referral_commission;
							}
							$referral_commission = $per_sale_commission;
							/* new code */
							$check_referral = Members::referralCheck($referral_by);
							  if($check_referral != 0)
							  {
								  $referred['display'] = Members::referralUser($referral_by);
								  $wallet_amount = $referred['display']->earnings + $referral_commission;
								  $referral_amount = $referred['display']->referral_amount + $referral_commission;
								  $update_data = array('earnings' => $wallet_amount, 'referral_amount' => $referral_amount);
								  Members::updateReferral($referral_by,$update_data);
							   } 
						/* referral per sale earning */	
					   }
					if($this->custom_settings('author_key') == Helper::Key_Owner())
					{   
					/* Notification Message */
					$item_check_names = rtrim($item_full_names,',');
					$fin_itemnames = mb_substr($item_check_names, 0, 30, 'UTF-8').'...';
					$noti_date = date('Y-m-d H:i:s');
					$noti_sender_id = $checking->user_id;
					$noti_receiver_id = 1;
					$noti_message = '<strong style="text-transform:capitalize;">'.$customer['info']->name.'</strong> bought product on <strong>'.$fin_itemnames.'</strong>';
					$savedata = array('noti_date' => $noti_date, 'noti_sender_id' => $noti_sender_id, 'noti_receiver_id' => $noti_receiver_id, 'noti_message' => $noti_message, 'noti_type' => 'buying-item', 'noti_type_id' => $purchased_token);
					Items::saveNotification($savedata);
					/* Notification Message */
					
					/* Seller Notification Message */
					$noti_date_2 = date('Y-m-d H:i:s');
					$noti_sender_id_2 = $checking->item_user_id;
					$noti_receiver_id_2 = 1;
					$seller['info'] = Members::singlevendorData($checking->item_user_id);
					$noti_message_2 = '<strong style="text-transform:capitalize;">'.$seller['info']->name.'</strong> sold product on <strong>'.$fin_itemnames.'</strong>';
					$savedata_2 = array('noti_date' => $noti_date_2, 'noti_sender_id' => $noti_sender_id_2, 'noti_receiver_id' => $noti_receiver_id_2, 'noti_message' => $noti_message_2, 'noti_type' => 'selling-item', 'noti_type_id' => $purchased_token);
					Items::saveNotification($savedata_2);
					/* Seller Notification Message */ 
					}   	
					return redirect('success');
			   }
			   else
			   {
			      return redirect()->back()->with('error', 'Please check your wallet balance amount');
			   }
		  }
		  /* stripe code */
		  else if($payment_method == 'stripe')
		  {
		       if($stripe_type == "intents") // Intents API
			   {
					if($site_currency == 'INR')
					{
						$finpr = round($final_amount,2);
						$partamt = $finpr * 100;
						$myamount = str_replace([',', '.'], ['', ''], $partamt);
					}
					else
					{
					    $finpr = round($final_amount,2);
						$myamount = $finpr * 100;
					}	      
					\Stripe\Stripe::setApiKey($stripe_secret_key);
					$customer = \Stripe\Customer::create(array( 
					'name' => $order_firstname.' '.$order_lastname,
					'description' => $item_names_data,        
					'email' => $order_email,
					"address" => ["city" => "", "country" => "", "line1" => $order_email, "line2" => "", "postal_code" => "", "state" => ""],
					'shipping' => [
						  'name' => $order_firstname.' '.$order_lastname,
						  'address' => [
							'country' => 'us',
							'state' => '',
							'city' => '',
							'line1' => $order_email,
							'line2' => '',
							'postal_code' => ''
						  ]
						]
					));
        		    $payment_intent = \Stripe\PaymentIntent::create([
						'description' => $item_names_data,
						'amount' => $myamount,
						'currency' => $site_currency,
						'customer' => $customer->id,
						'metadata' => [
						'order_id' => $purchase_token
					    ],
						'shipping' => [
							'name' => $order_firstname.' '.$order_lastname,
							'address' => [
							  'line1' => $order_email,
							  'postal_code' => '',
							  'city' => '',
							  'state' => '',
							  'country' => 'us',
							],
						  ],
						'payment_method_types' => ['card'],
					]);
		            $intent = $payment_intent->client_secret;
				  
			       $data = array('cart' => $cart, 'cart_count' => $cart_count, 'get_payment' => $get_payment, 'mobile' => $mobile, 'stripe_publish' => $stripe_publish_key, 'stripe_secret' => $stripe_secret_key, 'country_percent' => $country_percent, 'stripe_type' => $stripe_type, 'intent' => $intent, 'item_names_data' => $item_names_data, 'myamount' => $myamount, 'final_amount' => $final_amount, 'site_currency' => $site_currency, 'order_firstname' => $order_firstname, 'order_lastname' => $order_lastname, 'item_user_id' => $item_user_id, 'purchase_token' => $purchase_token);
	   
	   
	              return view('stripe')->with($data); 

             
			  }
			  else // Charge API
			  {
			     
				 $stripe = array(
					"secret_key"      => $stripe_secret_key,
					"publishable_key" => $stripe_publish_key
				);
			 
				\Stripe\Stripe::setApiKey($stripe['secret_key']);
			 
				$customer = \Stripe\Customer::create(array( 
					'name' => $order_firstname.' '.$order_lastname,
					'description' => $item_names_data,        
					'email' => $order_email, 
					'source'  => $token,
					'customer' => $order_email, 
					"address" => ["city" => "", "country" => "", "line1" => $order_email, "line2" => "", "postal_code" => "", "state" => ""],
					'shipping' => [
						  'name' => $order_firstname.' '.$order_lastname,
						  'address' => [
							'country' => 'us',
							'state' => '',
							'city' => '',
							'line1' => $order_email,
							'line2' => '',
							'postal_code' => ''
						  ]
						]
	
                ));
			    
				if($site_currency == 'INR')
				{
				$finpr = round($final_amount,2);
				$partamt = $finpr * 100;
				$myamount = str_replace([',', '.'], ['', ''], $partamt);
				}
				else
				{
				$finpr = round($final_amount,2);
				$myamount = $finpr * 100;
				}
				$item_name = $item_names_data;
				$item_price = $myamount;
				$currency = $site_currency;
				$order_id = $purchase_token;
			    
				
				$charge = \Stripe\Charge::create(array(
					'customer' => $customer->id,
					'amount'   => $item_price,
					'currency' => $currency,
					'description' => $item_name,
					'metadata' => array(
						'order_id' => $order_id
					)
				));
			 
				
				$chargeResponse = $charge->jsonSerialize();
			 
				
				if($chargeResponse['paid'] == 1 && $chargeResponse['captured'] == 1) 
				{
			 
					
										
					$payment_token = $chargeResponse['balance_transaction'];
					$payment_status = 'completed';
					$purchased_token = $order_id;
					$orderdata = array('payment_token' => $payment_token, 'order_status' => $payment_status);
					$checkoutdata = array('payment_token' => $payment_token, 'payment_status' => $payment_status);
					Items::singleordupdateData($purchased_token,$orderdata);
					Items::singlecheckoutData($purchased_token,$checkoutdata);
					
					$token = $purchased_token;
					$check['display'] = Items::getcheckoutData($token);
					$customer['info'] = Members::singlevendorData($check['display']->user_id);
					$order_id = $check['display']->order_ids;
					$order_loop = explode(',',$order_id);
					  $item_full_names = "";
					  foreach($order_loop as $order)
					  {
						
						$getitem['item'] = Items::getorderData($order);
						$item_full_names .= $getitem['item']->item_name.',';
						$token = $getitem['item']->item_token;
						$item['display'] = Items::solditemData($token);
						$item_sold = $item['display']->item_sold + 1;
						$item_token = $token;
						/* serials key */
						if($item['display']->file_type == 'serial')
						{
							if($item['display']->item_delimiter == 'comma')
						  {
							  $spilit_value = explode(",", $item['display']->item_serials_list);
							  $split = "";
							  for ($i=0; $i < $getitem['item']->item_serial_stock; $i++) 
							  {
								   $split .= $spilit_value[$i].",";
							  }
							  $first_key = rtrim($split, ",");
							  $take_key = $split;
							  $balance_key = ltrim($item['display']->item_serials_list, $take_key);
						  }
						  else
						  {
							 $spilit_value=explode( "\n", $item['display']->item_serials_list);
							 $split = "";
							  for ($i=0; $i < $getitem['item']->item_serial_stock; $i++) 
							  {
								   $split .= $spilit_value[$i]."\n";
							  }
							  $changekey = str_replace("\n",",",$split);
							  $first_key = rtrim($changekey, ",");
							  $take_key = $split;
							  $balance_key = ltrim($item['display']->item_serials_list, $take_key);
							
						   }
							  
						}
						else
						{
						   $first_key = "";
						   $balance_key = "";
						}
						$key_data = array('item_order_serial_key' => $first_key);
						Items::updateCoupon($order,$key_data);
						$data = array('item_sold' => $item_sold, 'item_serials_list' => $balance_key);
						Items::updateitemData($item_token,$data);
						/* serials key */ 
                        /* subscription code */
						$additional['setting'] = Settings::editAdditional();
						$ordered['data'] = Items::singleorderData($order);
						$item_user_id = $ordered['data']->item_user_id;
						$vendor['info'] = Members::singlevendorData($item_user_id);
						$to_name = $vendor['info']->name;
						$to_email = $vendor['info']->email;
						$vendor_amount = $ordered['data']->vendor_amount;
						$admin_name = $setting['setting']->sender_name;
						$admin_email = $setting['setting']->sender_email;
						$currency = $site_currency;
						$count_mode = Settings::checkuserSubscription($item_user_id);
						/* manual payment verification : OFF */
						if($additional['setting']->subscription_mode == 0)
						{
							if($setting['setting']->payment_verification == 0)
							{
							   
								  
								  $user_id = $ordered['data']->user_id;
								  
								  $total_price = $ordered['data']->total_price;
								  $admin_amount = $ordered['data']->admin_amount;
								  
								  
								  $user_token = $vendor['info']->user_token;
								  
								  $vendor_earning = $vendor['info']->earnings + $vendor_amount;
								  $record = array('earnings' => $vendor_earning);
								  Members::updatepasswordData($user_token, $record);
								  
								  $admin['info'] = Members::adminData();
								  $admin_token = $admin['info']->user_token;
								  $admin_earning = $admin['info']->earnings + $admin_amount;
								  $admin_record = array('earnings' => $admin_earning);
								  Members::updateadminData($admin_token, $admin_record);
								  
								  $orderdata = array('approval_status' => 'payment released to vendor');
								  Items::singleorderupData($order,$orderdata);
							 
							}
							
							$record_data = array('to_name' => $to_name, 'to_email' => $to_email, 'vendor_amount' => $vendor_amount, 'currency' => $currency);
							/* email template code */
								  $checktemp = EmailTemplate::checkTemplate(18);
								  if($checktemp != 0)
								  {
								  $template_view['mind'] = EmailTemplate::viewTemplate(18);
								  $template_subject = $template_view['mind']->et_subject;
								  }
								  else
								  {
								  $template_subject = "New Payment Approved";
								  }
								  /* email template code */
							Mail::send('admin.vendor_payment_mail', $record_data , function($message) use ($admin_name, $admin_email, $to_name, $to_email, $template_subject) {
												$message->to($to_email, $to_name)
														->subject($template_subject);
												$message->from($admin_email,$admin_name);
											});
						}	
						else
						{
						   if($count_mode == 1)
						   {
							   
								  $orderdata = array('approval_status' => 'payment released to vendor');
								  Items::singleorderupData($order,$orderdata);
								  $check_email_support = Members::getuserSubscription($vendor['info']->id);
								  if($check_email_support == 1)
								  {
									  $record_data = array('to_name' => $to_name, 'to_email' => $to_email, 'vendor_amount' => $vendor_amount, 'currency' => $currency);
									  /* email template code */
								  $checktemp = EmailTemplate::checkTemplate(18);
								  if($checktemp != 0)
								  {
								  $template_view['mind'] = EmailTemplate::viewTemplate(18);
								  $template_subject = $template_view['mind']->et_subject;
								  }
								  else
								  {
								  $template_subject = "New Payment Approved";
								  }
								  /* email template code */
									  Mail::send('admin.vendor_payment_mail', $record_data , function($message) use ($admin_name, $admin_email, $to_name, $to_email, $template_subject) {
												$message->to($to_email, $to_name)
														->subject($template_subject);
												$message->from($admin_email,$admin_name);
											});
								  }
						   }
						   else
						   {
							  
							  if($setting['setting']->payment_verification == 0)
							 {
								  $user_id = $ordered['data']->user_id;
								  
								  $total_price = $ordered['data']->total_price;
								  $admin_amount = $ordered['data']->admin_amount;
								  
								  
								  $user_token = $vendor['info']->user_token;
								  
								  $vendor_earning = $vendor['info']->earnings + $vendor_amount;
								  $record = array('earnings' => $vendor_earning);
								  Members::updatepasswordData($user_token, $record);
								  
								  $admin['info'] = Members::adminData();
								  $admin_token = $admin['info']->user_token;
								  $admin_earning = $admin['info']->earnings + $admin_amount;
								  $admin_record = array('earnings' => $admin_earning);
								  Members::updateadminData($admin_token, $admin_record);
								  
								  $orderdata = array('approval_status' => 'payment released to vendor');
								  Items::singleorderupData($order,$orderdata);
								  
								  
								  $check_email_support = Members::getuserSubscription($vendor['info']->id);
								  if($check_email_support == 1)
								  {
									  $record_data = array('to_name' => $to_name, 'to_email' => $to_email, 'vendor_amount' => $vendor_amount, 'currency' => $currency);
									  /* email template code */
									  $checktemp = EmailTemplate::checkTemplate(18);
									  if($checktemp != 0)
									  {
									  $template_view['mind'] = EmailTemplate::viewTemplate(18);
									  $template_subject = $template_view['mind']->et_subject;
									  }
									  else
									  {
									  $template_subject = "New Payment Approved";
									  }
									  /* email template code */
									  Mail::send('admin.vendor_payment_mail', $record_data , function($message) use ($admin_name, $admin_email, $to_name, $to_email, $template_subject) {
												$message->to($to_email, $to_name)
														->subject($template_subject);
												$message->from($admin_email,$admin_name);
											});
								  }
							 
							  }
							  
							  
						   }
						   
						   
						}	
						/* manual payment verification : OFF */
						/* subscription code */
												
						
					  }
					
					/* referral per sale earning */
					    $additional_setting = Settings::editAdditional();
					    if($additional_setting->affiliate_referral == 1)
						{
							$logged_id = Auth::user()->id;
							
							$buyer_details = Members::singlebuyerData($logged_id);
							$referral_by = $buyer_details->referral_by;
							
							
							
							$calprice = $check['display']->total;
							if($additional_setting->per_sale_referral_commission_type == 'percentage')
							{
							$per_sale_commission = ($additional_setting->per_sale_referral_commission * $calprice) / 100;
							}
							else
							{
							$per_sale_commission = $additional_setting->per_sale_referral_commission;
							}
							$referral_commission = $per_sale_commission;
							
							$check_referral = Members::referralCheck($referral_by);
							  if($check_referral != 0)
							  {
								  $referred['display'] = Members::referralUser($referral_by);
								  $wallet_amount = $referred['display']->earnings + $referral_commission;
								  $referral_amount = $referred['display']->referral_amount + $referral_commission;
								  $update_data = array('earnings' => $wallet_amount, 'referral_amount' => $referral_amount);
								  Members::updateReferral($referral_by,$update_data);
							   } 
					    }
					if($this->custom_settings('author_key') == Helper::Key_Owner())
					{   
					/* Notification Message */
					$item_check_names = rtrim($item_full_names,',');
					$fin_itemnames = mb_substr($item_check_names, 0, 30, 'UTF-8').'...';
					$noti_date = date('Y-m-d H:i:s');
					$noti_sender_id = $check['display']->user_id;
					$noti_receiver_id = 1;
					$noti_message = '<strong style="text-transform:capitalize;">'.$customer['info']->name.'</strong> bought product on <strong>'.$fin_itemnames.'</strong>';
					$savedata = array('noti_date' => $noti_date, 'noti_sender_id' => $noti_sender_id, 'noti_receiver_id' => $noti_receiver_id, 'noti_message' => $noti_message, 'noti_type' => 'buying-item', 'noti_type_id' => $purchased_token);
					Items::saveNotification($savedata);
					/* Notification Message */
					
					/* Seller Notification Message */
					$noti_date_2 = date('Y-m-d H:i:s');
					$noti_sender_id_2 = $check['display']->item_user_id;
					$noti_receiver_id_2 = 1;
					$seller['info'] = Members::singlevendorData($check['display']->item_user_id);
					$noti_message_2 = '<strong style="text-transform:capitalize;">'.$seller['info']->name.'</strong> sold product on <strong>'.$fin_itemnames.'</strong>';
					$savedata_2 = array('noti_date' => $noti_date_2, 'noti_sender_id' => $noti_sender_id_2, 'noti_receiver_id' => $noti_receiver_id_2, 'noti_message' => $noti_message_2, 'noti_type' => 'selling-item', 'noti_type_id' => $purchased_token);
					Items::saveNotification($savedata_2);
					/* Seller Notification Message */ 
					}  	
					$data_record = array('payment_token' => $payment_token);
					return view('success')->with($data_record);
					
					
				}
		     	 
				 
			  }
		  
		  }
		  /* stripe code */
		  
		 
		  
	   }
	   else
	   {
	   
	      Items::updatecheckoutData($purchase_token,$user_id,$payment_status,$updatedata);
		  $order_loop = explode(',',$order_id);
		  foreach($order_loop as $order)
		  {
		    $single_order = Items::getorderData($order);
			$buyer_id = $single_order->item_user_id;
			$buyer_info['view'] = Members::singlebuyerData($buyer_id);
			$buyer_type = $buyer_info['view']->exclusive_author;
			if($single_order->coupon_type == 'fixed')
			{
			$price_item = $single_order->item_price - $single_order->coupon_value;
			}
			else if($single_order->coupon_type == 'percentage')
			{
			$price_item = $single_order->discount_price;
			}
			else
			{
			$price_item = $single_order->item_price;
			}
			if($buyer_type == 1)
			{
			$commission =($price_item * $setting['setting']->site_exclusive_commission) / 100;
			}
			else
			{
			$commission =($price_item * $setting['setting']->site_non_exclusive_commission) / 100;
			}
			$amount_price = $commission;
			$vendor_price = $price_item - $commission;
		    $orderdata = array('purchase_token' => $purchase_token, 'payment_type' => $payment_method, 'vendor_amount' => $vendor_price, 'admin_amount' => $amount_price, 'total_price' => $price_item);
			Items::singleorderupData($order,$orderdata);
			$item['name'] = Items::singleorderData($order);
			$item_names .= $item['name']->item_name;

		   
		  }
		  $item_names_data = rtrim($item_names,',');
		  
		  
		  if($payment_method == 'paypal')
		  {
		     
			 $paypal = '<form method="post" id="paypal_form" action="'.$paypal_url.'">
			  <input type="hidden" value="_xclick" name="cmd">
			  <input type="hidden" value="'.$paypal_email.'" name="business">
			  <input type="hidden" value="'.$item_names_data.'" name="item_name">
			  <input type="hidden" value="'.$purchase_token.'" name="item_number">
			  <input type="hidden" value="'.$final_amount.'" name="amount">
			  <input type="hidden" value="'.$site_currency.'" name="currency_code">
			  <input type="hidden" value="'.$success_url.'" name="return">
			  <input type="hidden" value="'.$cancel_url.'" name="cancel_return">
			  		  
			</form>';
			$paypal .= '<script>window.paypal_form.submit();</script>';
			echo $paypal;
		 
		  }
		  else if($payment_method == 'payhere')
		  {
		      if($site_currency != 'LKR')
			   {
		       /* currency conversion */
			   $check_currency = Currencies::CheckCurrencyCount('LKR');
			   if($check_currency != 0)
			   {
				   $currency_data = Currencies::getCurrency('LKR');
	               $default_currency_rate = $currency_data->currency_rate;
				   $default_price_value = $default_final_amount * $default_currency_rate;
				   $price_amount = round($default_price_value,2);
				   $price_amount = $price_amount * 100;
			   }
			   else
			   {
				     return redirect()->back()->with('error', "Payhere need 'LKR' currency. Please contact administrator");
			   }
			   /* currency conversion */
			   
			   }
			   else
			   {
			   $price_amount = $final_amount * 100;
			   }
		      $payhere = '<form method="post" action="'.$payhere_url.'" id="payhere_form">   
							<input type="hidden" name="merchant_id" value="'.$payhere_merchant_id.'">
							<input type="hidden" name="return_url" value="'.$payhere_success_url.'">
							<input type="hidden" name="cancel_url" value="'.$cancel_url.'">
							<input type="hidden" name="notify_url" value="'.$cancel_url.'"> 
							<input type="hidden" name="order_id" value="'.$purchase_token.'">
							<input type="hidden" name="items" value="'.$item_names_data.'"><br>
							<input type="hidden" name="currency" value="LKR">
							<input type="hidden" name="amount" value="'.$price_amount.'">  
							
							<input type="hidden" name="first_name" value="'.$order_firstname.'">
							<input type="hidden" name="last_name" value="'.$order_lastname.'"><br>
							<input type="hidden" name="email" value="'.$order_email.'">
							<input type="hidden" name="phone" value="'.$order_firstname.'"><br>
							<input type="hidden" name="address" value="'.$order_firstname.'">
							<input type="hidden" name="city" value="'.$order_firstname.'">
							<input type="hidden" name="country" value="'.$order_firstname.'">
							  
						</form>'; 
						$payhere .= '<script>window.payhere_form.submit();</script>';
			            echo $payhere;
		  }
		  else if($payment_method == 'twocheckout')
		  {
		    
			$record = array('final_amount' => $final_amount, 'purchase_token' => $purchase_token, 'payment_method' => $payment_method, 'item_names_data' => $item_names_data, 'site_currency' => $site_currency, 'website_url' => $website_url, 'two_checkout_private' => $two_checkout_private, 'two_checkout_account' => $two_checkout_account, 'two_checkout_mode' => $two_checkout_mode, 'token' => $token, 'two_checkout_publishable' => $two_checkout_publishable);
       return view('order-confirm')->with($record);
			
		  }
		  else if($payment_method == 'paystack')
		  {
		       if($site_currency != 'NGN')
			   {
		       /* currency conversion */
			   $check_currency = Currencies::CheckCurrencyCount('NGN');
			   if($check_currency != 0)
			   {
				   $currency_data = Currencies::getCurrency('NGN');
	               $default_currency_rate = $currency_data->currency_rate;
				   $default_price_value = $default_final_amount * $default_currency_rate;
				   $price_amount = round($default_price_value,2);
				   $price_amount = $price_amount * 100;
			   }
			   else
			   {
				     return redirect()->back()->with('error', "Paystack need ".$setting['setting']->paystack_default_currency." currency. Please contact administrator");
			   }
			   /* currency conversion */
			   
			   }
			   else
			   {
			    $price_amount = $final_amount * 100; 
			   }
		       $callback = $website_url.'/paystack';
			   $csf_token = csrf_token();
			   $paystack = '<form method="post" id="stack_form" action="'.route('paystack').'">
					  <input type="hidden" name="_token" value="'.$csf_token.'">
					  <input type="hidden" name="email" value="'.$user_email.'" >
					  <input type="hidden" name="order_id" value="'.$purchase_token.'">
					  <input type="hidden" name="amount" value="'.$price_amount.'">
					  <input type="hidden" name="quantity" value="1">
					  <input type="hidden" name="currency" value="'.$setting['setting']->paystack_default_currency.'">
					  <input type="hidden" name="reference" value="'.$reference.'">
					  <input type="hidden" name="callback_url" value="'.$callback.'">
					  <input type="hidden" name="metadata" value="'.$purchase_token.'">
					  <input type="hidden" name="key" value="'.$setting['setting']->paystack_secret_key.'">
					</form>';
					$paystack .= '<script>window.stack_form.submit();</script>';
					echo $paystack;
			 
		  }
		  else if($payment_method == 'razorpay')
		  {
		       $additional['settings'] = Settings::editAdditional();
			   if($site_currency != 'INR')
			   {
		       /* currency conversion */
			   $check_currency = Currencies::CheckCurrencyCount('INR');
			   if($check_currency != 0)
			   {
				   $currency_data = Currencies::getCurrency('INR');
	               $default_currency_rate = $currency_data->currency_rate;
				   $default_price_value = $default_final_amount * $default_currency_rate;
				   $price_amount = round($default_price_value,2);
				   $price_amount = $price_amount * 100;
			   }
			   else
			   {
				     return redirect()->back()->with('error', "Razorpay need 'INR' currency. Please contact administrator");
			   }
			   /* currency conversion */
			   
			   }
			   else
			   {
			   $price_amount = $final_amount * 100;
			   }
			   $csf_token = csrf_token();
			   $logo_url = $website_url.'/public/storage/settings/'.$setting['setting']->site_logo;
			   $script_url = $website_url.'/resources/views/theme/js/vendor.min.js';
			   $callback = $website_url.'/razorpay';
			   $razorpay = '
			   <script type="text/javascript" src="'.$script_url.'"></script>
			   <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
			   <script>
				var options = {
					"key": "'.$razorpay_key.'",
					"amount": "'.$price_amount.'", 
					"currency": "INR",
					"name": "'.$item_names_data.'",
					"description": "'.$purchase_token.'",
					"image": "'.$logo_url.'",
					"callback_url": "'.$callback.'",
					"prefill": {
						"name": "'.$order_firstname.'",
						"email": "'.$order_email.'"
						
					},
					"notes": {
						"address": "'.$order_firstname.'"
						
						
					},
					"theme": {
						"color": "'.$setting['setting']->site_theme_color.'"
					}
				};
				var rzp1 = new Razorpay(options);
				rzp1.on("payment.failed", function (response){
						alert(response.error.code);
						alert(response.error.description);
						alert(response.error.source);
						alert(response.error.step);
						alert(response.error.reason);
						alert(response.error.metadata);
				});
				
				$(window).on("load", function() {
					 rzp1.open();
					e.preventDefault();
					});
				</script>';
				echo $razorpay;
					
					
		  }
		  else if($payment_method == 'localbank')
		  {
		    $bank_details = $setting['setting']->local_bank_details;
		    $bank_data = array('purchase_token' => $purchase_token, 'bank_details' => $bank_details);
	        return view('bank-details')->with($bank_data);
		  }
          /* stripe code */
		  else if($payment_method == 'stripe')
		  {
		     
			 			 
				$stripe = array(
					"secret_key"      => $stripe_secret_key,
					"publishable_key" => $stripe_publish_key
				);
			 
				\Stripe\Stripe::setApiKey($stripe['secret_key']);
			 
				
				
				
				$customer = \Stripe\Customer::create(array( 
					'name' => $order_firstname.' '.$order_lastname,
					'description' => $item_names_data,        
					'email' => $order_email, 
					'source'  => $token,
					'customer' => $order_email, 
					"address" => ["city" => "", "country" => "", "line1" => $item_names_data, "line2" => $item_names_data, "postal_code" => "", "state" => ""],
					'shipping' => [
						  'name' => $order_firstname.' '.$order_lastname,
						  'address' => [
							'country' => 'us',
							'state' => '',
							'city' => '',
							'line1' => $item_names_data,
							'line2' => '',
							'postal_code' => ''
						  ]
						]
	
                ));
			    
				if($site_currency == 'INR')
				{
				$finpr = round($final_amount,2);
				$partamt = $finpr * 100;
				$myamount = str_replace([',', '.'], ['', ''], $partamt);
				}
				else
				{
				$finpr = round($final_amount,2);
				$myamount = $finpr * 100;
				}
			 
				
				$item_name = $item_names_data;
				$item_price = $myamount;
				$currency = $site_currency;
				$order_id = $purchase_token;
			 
				
				$charge = \Stripe\Charge::create(array(
					'customer' => $customer->id,
					'amount'   => $item_price,
					'currency' => $currency,
					'description' => $item_name,
					'metadata' => array(
						'order_id' => $order_id
					)
				));
			 
				
				$chargeResponse = $charge->jsonSerialize();
			 
				
				if($chargeResponse['paid'] == 1 && $chargeResponse['captured'] == 1) 
				{
			 
					
										
					$payment_token = $chargeResponse['balance_transaction'];
					$payment_status = 'completed';
					$purchased_token = $order_id;
					$orderdata = array('payment_token' => $payment_token, 'order_status' => $payment_status);
					$checkoutdata = array('payment_token' => $payment_token, 'payment_status' => $payment_status);
					Items::singleordupdateData($purchased_token,$orderdata);
					Items::singlecheckoutData($purchased_token,$checkoutdata);
					
					$token = $purchased_token;
					$check['display'] = Items::getcheckoutData($token);
					$order_id = $check['display']->order_ids;
					$order_loop = explode(',',$order_id);
					  
					  foreach($order_loop as $order)
					  {
						
						$getitem['item'] = Items::getorderData($order);
						$token = $getitem['item']->item_token;
						$item['display'] = Items::solditemData($token);
						$item_sold = $item['display']->item_sold + 1;
						$item_token = $token; 
						$data = array('item_sold' => $item_sold);
					    Items::updateitemData($item_token,$data);
						/* manual payment verification : OFF */
						if($setting['setting']->payment_verification == 0)
						{
						   
							  $ordered['data'] = Items::singleorderData($order);
							  $user_id = $ordered['data']->user_id;
							  $item_user_id = $ordered['data']->item_user_id;
							  $vendor_amount = $ordered['data']->vendor_amount;
							  $total_price = $ordered['data']->total_price;
							  $admin_amount = $ordered['data']->admin_amount;
							  
							  $vendor['info'] = Members::singlevendorData($item_user_id);
							  $user_token = $vendor['info']->user_token;
							  $to_name = $vendor['info']->name;
							  $to_email = $vendor['info']->email;
							  $vendor_earning = $vendor['info']->earnings + $vendor_amount;
							  $record = array('earnings' => $vendor_earning);
							  Members::updatepasswordData($user_token, $record);
							  
							  $admin['info'] = Members::adminData();
							  $admin_token = $admin['info']->user_token;
							  $admin_earning = $admin['info']->earnings + $admin_amount;
							  $admin_record = array('earnings' => $admin_earning);
							  Members::updateadminData($admin_token, $admin_record);
							  
							  $orderdata = array('approval_status' => 'payment released to vendor');
							  Items::singleorderupData($order,$orderdata);
							  $admin_name = $setting['setting']->sender_name;
							  $admin_email = $setting['setting']->sender_email;
							  $currency = $site_currency;
							  $check_email_support = Members::getuserSubscription($vendor['info']->id);
							  if($check_email_support == 1)
							  {
								  $record_data = array('to_name' => $to_name, 'to_email' => $to_email, 'vendor_amount' => $vendor_amount, 'currency' => $currency);
								  /* email template code */
									  $checktemp = EmailTemplate::checkTemplate(18);
									  if($checktemp != 0)
									  {
									  $template_view['mind'] = EmailTemplate::viewTemplate(18);
									  $template_subject = $template_view['mind']->et_subject;
									  }
									  else
									  {
									  $template_subject = "New Payment Approved";
									  }
									  /* email template code */
								  Mail::send('admin.vendor_payment_mail', $record_data , function($message) use ($admin_name, $admin_email, $to_name, $to_email, $template_subject) {
											$message->to($to_email, $to_name)
													->subject($template_subject);
											$message->from($admin_email,$admin_name);
										});
							  }			
						 
						   
						}
						/* manual payment verification : OFF */
						
						
					  }
					/* referral per sale earning */
					    $additional_setting = Settings::editAdditional();
					    if($additional_setting->affiliate_referral == 1)
						{
							$logged_id = $user_id;
							$buyer_details = Members::singlebuyerData($logged_id);
							$referral_by = $buyer_details->referral_by;
							
							/* new code */
							$calprice = $check['display']->total;
							if($additional_setting->per_sale_referral_commission_type == 'percentage')
							{
							$per_sale_commission = ($additional_setting->per_sale_referral_commission * $calprice) / 100;
							}
							else
							{
							$per_sale_commission = $additional_setting->per_sale_referral_commission;
							}
							$referral_commission = $per_sale_commission;
							/* new code */
							$check_referral = Members::referralCheck($referral_by);
							  if($check_referral != 0)
							  {
								  $referred['display'] = Members::referralUser($referral_by);
								  $wallet_amount = $referred['display']->earnings + $referral_commission;
								  $referral_amount = $referred['display']->referral_amount + $referral_commission;
								  $update_data = array('earnings' => $wallet_amount, 'referral_amount' => $referral_amount);
								  Members::updateReferral($referral_by,$update_data);
							   } 
						/* referral per sale earning */	
					  }
					$data_record = array('payment_token' => $payment_token);
					return view('success')->with($data_record);
					
					
				}
		     
		  
		  }
		  /* stripe code */

		  
		  
	   }
	   return view('checkout')->with($totaldata);
	
	
	}
	
	
	public function remove_cart_item($ordid)
	{
	  
	   $ord_id = base64_decode($ordid); 
	   Items::deletecartdata($ord_id);
	  
	  return redirect()->back()->with('success', 'Cart item removed');
	  
	}
	
	public function remove_clear_item()
	{
	  
	   $session_id = Session::getId();
	   Items::deletecartempty($session_id);
	  
	  return redirect()->back()->with('success', 'Cart items removed');
	  
	}
	
	
	
	public function show_checkout()
	{
	 
	  $cart['item'] = Items::getcartData();
	  $cart_count = Items::getcartCount();
	  $mobile['item'] = Items::getcartData();
	  $sid = 1;
	  $setting['setting'] = Settings::editGeneral($sid);
	  $additional['setting'] = Settings::editAdditional();
	  if($cart_count != 0)
	  {
		  $last_order = Items::lastorderData();
		  $item_user_id = $last_order->item_user_id;
		  $count_mode = Settings::checkuserSubscription($item_user_id);
		  $vendor_details = Members::singlevendorData($item_user_id);
		  if($additional['setting']->subscription_mode == 0)
		  {
			$get_payment = explode(',', $setting['setting']->payment_option);
			 $stripe_type = $setting['setting']->stripe_type;
			 $stripe_mode = $setting['setting']->stripe_mode;
			   if($stripe_mode == 0)
			   {
				 $stripe_publish = $setting['setting']->test_publish_key;
				 $stripe_secret = $setting['setting']->test_secret_key;
			   }
			   else
			   {
				 $stripe_publish = $setting['setting']->live_publish_key;
				 $stripe_secret = $setting['setting']->live_secret_key;
			   }
		  }
		  else
		  {
			 if($count_mode == 1)
			 {
				$get_payment = explode(',', $vendor_details->user_payment_option);
				   $stripe_type = $vendor_details->user_stripe_type;
				   $stripe_mode = $vendor_details->user_stripe_mode;
				   if($stripe_mode == 0)
				   {
					 $stripe_publish = $vendor_details->user_test_publish_key;
					 $stripe_secret = $vendor_details->user_test_secret_key;
				   }
				   else
				   {
					 $stripe_publish = $vendor_details->user_live_publish_key;
					 $stripe_secret = $vendor_details->user_live_secret_key;
				   }
			 }
			 else
			 {
				$get_payment = explode(',', $setting['setting']->payment_option);
				   $stripe_type = $setting['setting']->stripe_type;
				   $stripe_mode = $setting['setting']->stripe_mode;
				   if($stripe_mode == 0)
				   {
					 $stripe_publish = $setting['setting']->test_publish_key;
					 $stripe_secret = $setting['setting']->test_secret_key;
				   }
				   else
				   {
					 $stripe_publish = $setting['setting']->live_publish_key;
					 $stripe_secret = $setting['setting']->live_secret_key;
				   }
			 }
		  }
	  }
	  else
	  {
	     $get_payment = explode(',', $setting['setting']->payment_option);
		 $stripe_mode = $setting['setting']->stripe_mode;
		 $stripe_type = $setting['setting']->stripe_type;
			   if($stripe_mode == 0)
			   {
				 $stripe_publish = $setting['setting']->test_publish_key;
				 $stripe_secret = $setting['setting']->test_secret_key;
			   }
			   else
			   {
				 $stripe_publish = $setting['setting']->live_publish_key;
				 $stripe_secret = $setting['setting']->live_secret_key;
			   }
	  }
	  /* VAT */
	  if (Auth::check())
	  {
		  $country_id = Auth::user()->country;
		  $country_details = Members::countryCheck($country_id);
		  if($country_details != 0)
		  {
			  $data_views = Members::countryDATA($country_id);
			  $country_percent = $data_views->vat_price;
		  }
		  else
		  {
			$country_percent = $additional['setting']->default_vat_price;
		  } 
	   }
	   else
	   {
	      $country_percent = $additional['setting']->default_vat_price;
	   }	  
	   /* VAT */
	  $data = array('cart' => $cart, 'cart_count' => $cart_count, 'get_payment' => $get_payment, 'mobile' => $mobile, 'stripe_publish' => $stripe_publish, 'stripe_secret' => $stripe_secret, 'country_percent' => $country_percent, 'stripe_type' => $stripe_type);
	   
	   
	   return view('checkout')->with($data);
	 
	
	}
	
	public function show_cart()
	{
	  $session_id = Session::getId();
	  if(Auth::check())
	  { 
	  $user_id = Auth::user()->id;
	  $update_data = array('user_id' => $user_id); 
	  Items::changeOrder($session_id,$update_data);
	  }
	  $cart['item'] = Items::getcartData();
	  $cart_count = Items::getcartCount();
	   $data = array('cart' => $cart, 'cart_count' => $cart_count);
	   
	   return view('cart')->with($data);
	}
	
	public static function price_info($flash_var,$price_var) 
    {
	    $additional['settings'] = Settings::editAdditional();
	    if($flash_var == 1)
        {
		/*$varprice = ($setting['setting']->site_flash_sale_discount * $price_var) / 100;
        $price = round($varprice,2);*/
		
		$varprice = ($price_var / 100) * $additional['settings']->flash_sale_value;
        $pricess = $price_var - $varprice;
        $price = round($pricess,2);
		
        }
        else
        {
        $price = $price_var;
        }
		return $price;
	}
	
	
	
	public function upload(Request $request){
	
        /*$fileName=$request->file('file')->getClientOriginalName();
        $path=$request->file('file')->storeAs('uploads', $fileName, 'public');
        return response()->json(['location'=>"/storage/$path"]); */
		
		/*$url = URL::to("/");
		 $imgpath = request()->file('file')->store($url.'/public/storage/items/', 'public');
        return json_encode(['location' => $imgpath]); 
        
        /*$imgpath = request()->file('file')->store('uploads', 'public'); 
        return response()->json(['location' => "/storage/$imgpath"]);*/
		$image = $request->file('file');
			$img_name = time() . '.'.$image->getClientOriginalExtension();
			$destinationPath = public_path('/storage/items');
			$imagePath = $destinationPath. "/".  $img_name;
			$image->move($destinationPath, $img_name);
			$url = URL::to("/public/storage/items/".$img_name);
       return response()->json(['location' => $url]);
    }
	
	
	public function view_featured_items(Request $request)
	{
	   $sid = 1;
	   $setting['setting'] = Settings::editGeneral($sid);
	   $today = date("Y-m-d");
	   $site_item_per_page = $setting['setting']->site_item_per_page;
	   $additional['settings'] = Settings::editAdditional();
	   if($additional['settings']->subscription_mode == 1)
	   {
	   $items = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('users.user_subscr_date','>=',$today)->where('users.user_subscr_payment_status','=','completed')->where('items.item_status','=',1)->where('items.item_featured','=','yes')->where('items.drop_status','=','no')->orderBy('items.item_id', 'desc')->paginate($site_item_per_page);
	   }
	   else
	   {
	   $items = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.item_status','=',1)->where('items.item_featured','=','yes')->where('items.drop_status','=','no')->orderBy('items.item_id', 'desc')->paginate($site_item_per_page);
	   }
	   if ($request->ajax()) {
    		$view = view('featured-data',compact('items'))->render();
            return response()->json(['html'=>$view]);
        }
	   
	   return view('featured-items',compact('items'));
	}
	
	
	
	public function view_subscriber_downloads(Request $request)
	{
	   $sid = 1;
	   $setting['setting'] = Settings::editGeneral($sid);
	   $today = date("Y-m-d");
	   $site_item_per_page = $setting['setting']->site_item_per_page;
	   $additional['settings'] = Settings::editAdditional();
	   
	   $items = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('users.user_subscr_date','>=',$today)->where('users.user_subscr_payment_status','=','completed')->where('items.item_status','=',1)->where('items.subscription_item','=',1)->where('items.free_download','=',0)->where('items.drop_status','=','no')->orderBy('items.item_id', 'desc')->paginate($site_item_per_page);
	   
	   if ($request->ajax()) {
    		$view = view('featured-data',compact('items'))->render();
            return response()->json(['html'=>$view]);
        }
	   
	   return view('subscriber-downloads',compact('items'));
	}
	
	
	public function view_new_items(Request $request)
	{
	   $sid = 1;
	   $setting['setting'] = Settings::editGeneral($sid);
	   $today = date("Y-m-d");
	   $additional['settings'] = Settings::editAdditional();
	   $site_item_per_page = $setting['setting']->site_item_per_page;
	   if($additional['settings']->subscription_mode == 1)
	   {
	   $items = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('users.user_subscr_date','>=',$today)->where('users.user_subscr_payment_status','=','completed')->where('items.item_status','=',1)->where('items.drop_status','=','no')->orderBy('items.item_id', 'desc')->paginate($site_item_per_page);
	   }
	   else
	   {
	   $items = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.item_status','=',1)->where('items.drop_status','=','no')->orderBy('items.item_id', 'desc')->paginate($site_item_per_page);
	   }
	   if ($request->ajax()) {
    		$view = view('featured-data',compact('items'))->render();
            return response()->json(['html'=>$view]);
        }
	   
	   return view('new-releases',compact('items'));
	   
	}
	
	
	public function view_popular_items(Request $request)
	{
	   $sid = 1;
	   $setting['setting'] = Settings::editGeneral($sid);
	   $today = date("Y-m-d");
	   $additional['settings'] = Settings::editAdditional();
	   $site_item_per_page = $setting['setting']->site_item_per_page;
	   if($additional['settings']->subscription_mode == 1)
	   {
	   $items = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('users.user_subscr_date','>=',$today)->where('users.user_subscr_payment_status','=','completed')->where('items.item_status','=',1)->where('items.drop_status','=','no')->orderBy('items.item_liked', 'desc')->paginate($site_item_per_page);
	   }
	   else
	   {
	   $items = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.item_status','=',1)->where('items.drop_status','=','no')->orderBy('items.item_liked', 'desc')->paginate($site_item_per_page);
	   }
	   if ($request->ajax()) {
    		$view = view('featured-data',compact('items'))->render();
            return response()->json(['html'=>$view]);
        }
	   
	   return view('popular-items',compact('items'));
	   
	}
	
	
	public function view_tags($type,$slug,Request $request)
	{
	   $nslug = str_replace("-"," ",$slug);
	   $sid = 1;
	   $setting['setting'] = Settings::editGeneral($sid);
	   $today = date("Y-m-d");
	   $additional['settings'] = Settings::editAdditional();
	   $site_item_per_page = $setting['setting']->site_item_per_page;
	   if($additional['settings']->subscription_mode == 1)
	   {
	   $items = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('users.user_subscr_date','>=',$today)->where('users.user_subscr_payment_status','=','completed')->where('items.item_status','=',1)->where('items.item_tags', 'LIKE', "%$nslug%")->where('items.drop_status','=','no')->orderBy('items.item_id', 'desc')->paginate($site_item_per_page);
	   }
	   else
	   {
	   $items = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.item_status','=',1)->where('items.item_tags', 'LIKE', "%$nslug%")->where('items.drop_status','=','no')->orderBy('items.item_id', 'desc')->paginate($site_item_per_page);
	   }
	   if ($request->ajax()) {
    		$view = view('featured-data',compact('items'))->render();
            return response()->json(['html'=>$view]);
        }
	   $data = array('setting' => $setting, 'items' => $items, 'slug' => $slug);
	   return view('tag')->with($data);
	}
	
	public function view_flash_items(Request $request)
	{
	  $today = date("Y-m-d");
	   $additional['settings'] = Settings::editAdditional();
	   $sid = 1;
	  $setting['setting'] = Settings::editGeneral($sid);
	  $site_item_per_page = $setting['setting']->site_item_per_page;
	   if($additional['settings']->subscription_mode == 1)
	   {
	  $items = Items::with('ratings')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('users.user_subscr_date','>=',$today)->where('users.user_subscr_payment_status','=','completed')->where('items.item_status','=',1)->where('items.item_flash','=',1)->where('items.drop_status','=','no')->orderBy('items.item_id', 'desc')->paginate($site_item_per_page);
	  }
	  else
	  {
	   $items = Items::with('ratings')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.item_status','=',1)->where('items.item_flash','=',1)->where('items.drop_status','=','no')->orderBy('items.item_id', 'desc')->paginate($site_item_per_page);
	  }
	  
	  
	  if($setting['setting']->site_flash_end_date < date("Y-m-d"))
	  {
	     $data = array('item_flash' => 0);
		 Items::updateFlash($data);
	  }
	  if ($request->ajax()) {
    		$view = view('flash-data',compact('items'))->render();
            return response()->json(['html'=>$view]);
        }
	  return view('flash-sale',[ 'items' => $items, 'setting' => $setting]);
	  
	}
	
	
	
	public function view_user($slug,Request $request)
	{
	   $sid = 1;
	   $setting['setting'] = Settings::editGeneral($sid);
	   $site_item_per_page = $setting['setting']->site_item_per_page;
	   $check_user = Members::getInuserCount($slug);
	   if($check_user != 0)
	   {
	   $user['user'] = Members::getInuser($slug);
	   $user_id = $user['user']->id;
	   
	   /* badges */
	   $sid = 1;
	   $badges['setting'] = Settings::editBadges($sid);
	   $sold['item'] = Items::SoldAmount($user_id);
	   $sold_amount = 0;
	   foreach($sold['item'] as $iter)
	   {
			$sold_amount += $iter->total_price;
	   }
	   $country['view'] = Settings::editCountry($user['user']->country);
	   $membership = date('m/d/Y',strtotime($user['user']->created_at));
	  $membership_date = explode("/", $membership);
      $year = (date("md", date("U", mktime(0, 0, 0, $membership_date[0], $membership_date[1], $membership_date[2]))) > date("md")
		? ((date("Y") - $membership_date[2]) - 1)
		: (date("Y") - $membership_date[2]));
	  
	   $collect_amount = Items::CollectedAmount($user_id);
	   $referral_count = $user['user']->referral_count;
	   /* badges */
	   
	   $itemData['item'] = Items::with('ratings')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.user_id','=',$user_id)->where('items.item_status','=',1)->where('items.drop_status','=','no')->orderBy('items.item_id', 'desc')->get();
	   
	   $items = Items::with('ratings')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.user_id','=',$user_id)->where('items.item_status','=',1)->where('items.drop_status','=','no')->orderBy('items.item_id', 'desc')->paginate($site_item_per_page);
	   
	   
	   $since = date("F Y", strtotime($user['user']->created_at));
	   
	   $getitemcount = Items::getuseritemCount($user_id);
	   
	   $getsalecount = Items::getsaleitemCount($user_id);
	   
	   if (Auth::check())
		{
		$followcheck = Items::getfollowuserCheck($user_id);
		}
		else
		{
		 $followcheck = 0;
		}
	   
	   $followingcount = Items::getfollowingCount($user_id);
	   
	   $followercount = Items::getfollowerCount($user_id);
	   
		  $getreview  = Items::getreviewData($user_id);
		  if($getreview !=0)
		  {
			  $review['view'] = Items::getreviewRecord($user_id);
			  $top = 0;
			  $bottom = 0;
			  foreach($review['view'] as $review)
			  {
				 if($review->rating == 1) { $value1 = $review->rating*1; } else { $value1 = 0; }
				 if($review->rating == 2) { $value2 = $review->rating*2; } else { $value2 = 0; }
				 if($review->rating == 3) { $value3 = $review->rating*3; } else { $value3 = 0; }
				 if($review->rating == 4) { $value4 = $review->rating*4; } else { $value4 = 0; }
				 if($review->rating == 5) { $value5 = $review->rating*5; } else { $value5 = 0; }
				 
				 $top += $value1 + $value2 + $value3 + $value4 + $value5;
				 $bottom += $review->rating;
				 
			  }
			  if(!empty(round($top/$bottom)))
			  {
				$count_rating = round($top/$bottom);
			  }
			  else
			  {
				$count_rating = 0;
			  }
			  
			  
			  
		  }
		  else
		  {
			$count_rating = 0;
			$bottom = 0;
		  }
	   
	   $featured_count = Items::getfeaturedUser($user_id);
	   $free_count = Items::getfreeUser($user_id);
	   $tren_count = Items::getTrendUser($user_id);
	   if ($request->ajax()) {
    		$view = view('user-data',compact('items'))->render();
            return response()->json(['html'=>$view]);
        }
	   $data = array('user' => $user, 'since' => $since, 'items' => $items, 'getitemcount' => $getitemcount, 'getsalecount' => $getsalecount, 'count_rating' => $count_rating, 'bottom' => $bottom, 'getreview' => $getreview, 'followcheck' => $followcheck, 'followingcount' => $followingcount, 'followercount' => $followercount, 'badges' => $badges, 'sold_amount' => $sold_amount, 'country' => $country, 'year' => $year, 'collect_amount' => $collect_amount, 'referral_count' => $referral_count, 'featured_count' => $featured_count, 'free_count' => $free_count, 'tren_count' => $tren_count, 'itemData' => $itemData);
	   return view('user')->with($data);
	   }
	   else
	   {
	   return redirect('/404');
	   }
	}
	
	
	
	public function view_all_items()
	{
	  /*$itemData['item'] = Items::allitemData();*/
	  $method = "get";
	  $today = date("Y-m-d");
	  $additional['settings'] = Settings::editAdditional();
	  $sid = 1;
	  $setting['setting'] = Settings::editGeneral($sid);
	  
	  if($additional['settings']->subscription_mode == 1)
	  {
	      if($additional['settings']->shop_search_type == 'normal')
		  {
			  $itemData['item'] = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list','items.item_type_cat_id','items.item_category_parent')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('users.user_subscr_date','>=',$today)->where('users.user_subscr_payment_status','=','completed')->where('items.item_status','=',1)->where('items.drop_status','=','no')->orderBy('items.item_id', 'asc')->paginate($setting['setting']->site_item_per_page);
		  }
		  else
		  {
		      $itemData['item'] = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list','items.item_type_cat_id','items.item_category_parent')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('users.user_subscr_date','>=',$today)->where('users.user_subscr_payment_status','=','completed')->where('items.item_status','=',1)->where('items.drop_status','=','no')->orderBy('items.item_id', 'asc')->get();
		  } 
	  }
	  else
	  {
	      if($additional['settings']->shop_search_type == 'normal')
		  {
			  $itemData['item'] = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list','items.item_type_cat_id','items.item_category_parent')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.item_status','=',1)->where('items.drop_status','=','no')->orderBy('items.item_id', 'asc')->paginate($setting['setting']->site_item_per_page);
		  }
		  else
		  {
		     $itemData['item'] = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list','items.item_type_cat_id','items.item_category_parent')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.item_status','=',1)->where('items.drop_status','=','no')->orderBy('items.item_id', 'asc')->get();
		  }	  
	  }
	  $catData['item'] = Items::getitemcatData();
	  $category['view'] = Category::with('SubCategory')->where('category_status','=','1')->where('drop_status','=','no')->orderBy('menu_order','asc')->get();
	  $getWell['type'] = Items::gettypeStatus();
	  $minprice['price'] = Items::minpriceData();
	  $maxprice['price'] = Items::maxpriceData();
	  $seo_keyword = $setting['setting']->site_keywords;
	  $seo_desc = $setting['setting']->site_desc;
	  return view('shop',[ 'minprice' => $minprice, 'maxprice' => $maxprice, 'getWell' => $getWell, 'itemData' => $itemData, 'catData' => $catData, 'category' => $category, 'method' => $method, 'seo_keyword' => $seo_keyword, 'seo_desc' => $seo_desc]);
	  
	}
	
	
	public function view_free_items(Request $request)
	{
	    $sid = 1;
	   $setting['setting'] = Settings::editGeneral($sid);
	    $today = date("Y-m-d");
	   $additional['settings'] = Settings::editAdditional();
	   $site_item_per_page = $setting['setting']->site_item_per_page;
	   $items = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.item_status','=',1)->where('items.free_download','=',1)->where('items.drop_status','=','no')->orderBy('items.item_id', 'desc')->paginate($site_item_per_page);
	  
	  /*if($additional['settings']->subscription_mode == 1)
	   {
	  $free['items'] = Items::with('ratings')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('users.user_subscr_date','>=',$today)->where('items.item_status','=',1)->where('items.free_download','=',1)->where('items.drop_status','=','no')->orderBy('items.item_id', 'desc')->get();
	  }
	  else
	  {
	  $free['items'] = Items::with('ratings')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.item_status','=',1)->where('items.free_download','=',1)->where('items.drop_status','=','no')->orderBy('items.item_id', 'desc')->get();
	  }*/
	  
	  
	  /*if($setting['setting']->site_free_end_date < date("Y-m-d"))
	  {
	     $data = array('free_download' => 0);
		 Items::updateFree($data);
	  }*/
	  if ($request->ajax()) {
    		$view = view('free-data',compact('items'))->render();
            return response()->json(['html'=>$view]);
        }
	   
	   return view('free-items',compact('items'));
	  
	  
	}
	
	
	public function login_as_vendor($user_token)
	{
	   $encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
	   $vendor_token   = $encrypter->decrypt($user_token);
	      
		  if(Auth::check())
		  {
		     if(Auth::user()->id == 1)
			 {
				  Auth::logout();
				  $count_data = Members::logData($vendor_token);
				  $vendor_details = Members::editData($vendor_token);
				  $email = $vendor_details->email;
				  $password = base64_decode($vendor_details->user_auth_token);
				  
				  if (Auth::attempt(['email' => $email, 'password' => $password, 'verified' => 1, 'drop_status' => 'no'])) 
				  {
					 return redirect('/profile-settings');
				  }
				  else
				  {
					 return redirect('/login')->with('error', 'These credentials do not match our records.');
				  }
			 }
			 else
			 {
			    return redirect('/login')->with('error', 'These credentials do not match our records.');
			 }	  
		  }
		  else
		  {
		     return redirect('/404');
		  }	  
		  
	   
	
	   
	}
	
	public function upgrade_bank_details()
	{
	   return view('upgrade-bank-details');
	  
	}
	
	public function view_subscription()
	{
	 $subscription['view'] = Subscription::viewSubscription();
	 $data = array('subscription' => $subscription);  
	 return view('subscription')->with($data);
	}
	
	public function view_start_selling()
	{
	  $setting['setting'] = Settings::editSelling();
	  $data = array('setting' => $setting);
	  return view('start-selling')->with($data);
	}
	
	
	public function view_preview($item_slug)
	{
	   $item['item'] = Items::singleitemData($item_slug);
	   $data = array('item' => $item);
	   return view('preview')->with($data);
	}
	public function autoComplete(Request $request) {
	    
        $query = $request->get('term','');
        
        //$products=Items::autoSearch($query);
		   $today = date("Y-m-d");
		   $additional['settings'] = Settings::editAdditional();
		   if($additional['settings']->subscription_mode == 1)
		   {
			
			$products = Items::with('ratings')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('users.user_subscr_date','>=',$today)->where('users.user_subscr_payment_status','=','completed')->where('items.item_name', 'LIKE', '%'. $query. '%')->where('items.item_status','=',1)->where('items.drop_status','=','no')->orderBy('items.item_name', 'asc')->get();
		   }
		   else
		   {
		   $products=Items::autoSearch($query);
		   }	
        $data=array();
        foreach ($products as $product) {
                $data[]=array('value'=>$product->item_name,'id'=>$product->item_id);
        }
        if(count($data))
             return $data;
        else
            return ['value'=>'No Result Found','id'=>''];
    }
	
	public function not_found()
	{
	  return view('404');
	}
	
	
    public function view_index()
	{
	   
	   //Storage::disk('s3')->delete('https://codecanor.s3.us-east-2.amazonaws.com/wed-jan-19-2022-1-19-pm97277.zip');
	   $today = date("Y-m-d");
	   $sid = 1;
	   $setting['setting'] = Settings::editGeneral($sid);
	   $additional['settings'] = Settings::editAdditional();
	   $blog['data'] = Blog::homeblogData($setting['setting']->home_blog_post);
	   $comments = Blog::getgroupcommentData();
	   $review['data'] = Items::homereviewsData();
	   $totalmembers = Members::getmemberData();
	   $totalsales = Items::totalsaleitemCount();
	   $totalfiles = Items::totalfileItems();
	   $total['earning'] = Items::totalearningCount();
	   if($additional['settings']->subscription_mode == 1)
	   {
	   $featured['items'] = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('users.user_subscr_date','>=',$today)->where('users.user_subscr_payment_status','=','completed')->where('items.item_status','=',1)->where('items.item_featured','=','yes')->where('items.drop_status','=','no')->orderBy('items.item_id', 'desc')->take($setting['setting']->home_featured_items)->get();
	   $popular['items'] = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('users.user_subscr_date','>=',$today)->where('users.user_subscr_payment_status','=','completed')->where('items.item_status','=',1)->where('items.drop_status','=','no')->orderBy('items.item_liked', 'desc')->take($setting['setting']->home_popular_items)->get();
	   $flash['items'] = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('users.user_subscr_date','>=',$today)->where('users.user_subscr_payment_status','=','completed')->where('items.item_status','=',1)->where('items.drop_status','=','no')->where('items.item_flash','=',1)->orderBy('items.item_id', 'desc')->take($setting['setting']->home_flash_items)->get();
	   /*$free['items'] = Items::with('ratings')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('users.user_subscr_date','>=',$today)->where('items.item_status','=',1)->where('items.drop_status','=','no')->where('items.free_download','=',1)->orderBy('items.item_id', 'desc')->take($setting['setting']->home_free_items)->get();*/
	   $newest['items'] = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('users.user_subscr_date','>=',$today)->where('users.user_subscr_payment_status','=','completed')->where('items.item_status','=',1)->where('items.drop_status','=','no')->orderBy('items.item_id', 'desc')->take($setting['setting']->site_newest_files)->get();
	   }
	   else
	   {
	   $featured['items'] = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.item_status','=',1)->where('items.item_featured','=','yes')->where('items.drop_status','=','no')->orderBy('items.item_id', 'desc')->take($setting['setting']->home_featured_items)->get();
	   $popular['items'] = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.item_status','=',1)->where('items.drop_status','=','no')->orderBy('items.item_liked', 'desc')->take($setting['setting']->home_popular_items)->get();
	   $flash['items'] = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.item_status','=',1)->where('items.drop_status','=','no')->where('items.item_flash','=',1)->orderBy('items.item_id', 'desc')->take($setting['setting']->home_flash_items)->get();
	   
	   $newest['items'] = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.item_status','=',1)->where('items.drop_status','=','no')->orderBy('items.item_id', 'desc')->take($setting['setting']->site_newest_files)->get();
	   }
	   $free['items'] = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.item_status','=',1)->where('items.drop_status','=','no')->where('items.free_download','=',1)->orderBy('items.item_id', 'desc')->take($setting['setting']->home_free_items)->get();
	   $totalearning = 0;
	   foreach($total['earning'] as $earning)
	   {
	     $totalearning += $earning->total_price;
	   } 
	   
	   $category['view'] = Category::with('SubCategory')->where('category_status','=','1')->where('drop_status','=','no')->orderBy('menu_order','asc')->get();
	   $data = array('blog' => $blog, 'comments' => $comments, 'review' => $review, 'totalmembers' => $totalmembers, 'totalsales' => $totalsales, 'totalfiles' => $totalfiles, 'totalearning' => $totalearning, 'featured' => $featured, 'newest' => $newest, 'free' => $free, 'popular' => $popular, 'flash' => $flash, 'category' => $category);
	  //SitemapGenerator::create(URL::to('/'))->writeToFile('sitemap.xml');
	  
	  return view('index')->with($data);
	}
	
	public function payment_cancel()
	{
	  return view('cancel');
	}
	
	public function payment_failure()
	{
	  return view('failure');
	} 
    
	public function payment_pending()
	{
	  return view('pending');
	}
	
    public function user_verify($user_token)
    {
	    
	    $allsettings = Settings::allSettings();
        $data = array('verified'=>'1');
		$user['user'] = Members::verifyuserData($user_token, $data);
		$additional = Settings::editAdditional();
		if($additional->affiliate_referral == 1)
		{
			$check_ref = Members::refCount($user_token);
			if($check_ref != 0)
			{
				$user_data = Members::editData($user_token);
				$referral_by = $user_data->referral_by;
				
				  $referral_commission = $allsettings->site_referral_commission;
				  $check_referral = Members::referralCheck($referral_by);
				  if($check_referral != 0)
				  {
					  $referred['display'] = Members::referralUser($referral_by);
					  $wallet_amount = $referred['display']->earnings + $referral_commission;
					  $referral_amount = $referred['display']->referral_amount + $referral_commission;
					  $referral_count = $referred['display']->referral_count + 1;
					  
					  $update_data = array('earnings' => $wallet_amount, 'referral_amount' => $referral_amount, 'referral_count' => $referral_count);
					  Members::updateReferral($referral_by,$update_data);
				   }
				   $again_data = array('referral_payout' => 'completed');
				   Members::updateData($user_token,$again_data);
				
			}	
		}
		return redirect('login')->with('success','Your e-mail is verified. You can now login.');
    }


   /* public function user_verify($user_token)
    {
        $data = array('verified'=>'1');
		$user['user'] = Members::verifyuserData($user_token, $data);
		
		return redirect('login')->with('success','Your e-mail is verified. You can now login.');
    }*/
	
	public function view_forgot()
	{
	   return view('forgot');
	}
	
	public function view_contact()
	{
	   return view('contact');
	}
	
	
	public function view_reset($token)
	{
	  $data = array('token' => $token);
	  return view('reset')->with($data);
	}
	
	
	public function view_unfollow($unfollow,$my_id,$follow_id)
	{
	  Items::unFollow($my_id,$follow_id);
	  return redirect()->back();
	  
	}
	
	public function view_free_item($download,$item_token)
	{
	 
	 if(Auth::check())
	 {
	  $today_date = date('Y-m-d');
	  
	  $download_count_checks = Members::checkdownloadDate(Auth::user()->id,$today_date);
	  if(Auth::user()->user_subscr_download_item > $download_count_checks->user_today_download_limit)
	  {
		  $token = base64_decode($item_token);
		  $allsettings = Settings::allSettings();
		  /* wasabi */
			   $wasabi_access_key_id = $allsettings->wasabi_access_key_id;
			   $wasabi_secret_access_key = $allsettings->wasabi_secret_access_key;
			   $wasabi_default_region = $allsettings->wasabi_default_region;
			   $wasabi_bucket = $allsettings->wasabi_bucket;
			   $wasabi_endpoint = 'https://s3.'.$wasabi_default_region.'.wasabisys.com';
			   $raw_credentials = array(
									'credentials' => [
										'key' => $wasabi_access_key_id,
										'secret' => $wasabi_secret_access_key
									],
									'endpoint' => $wasabi_endpoint, 
									'region' => $wasabi_default_region, 
									'version' => 'latest',
									'use_path_style_endpoint' => true
								);
				$s3 = S3Client::factory($raw_credentials);
			    /* wasabi */
		  $item['data'] = Items::edititemData($token);
		  $item_count = $item['data']->download_count + 1;
		  $data = array('download_count' => $item_count);
		  Items::updateitemData($token,$data);
	      
		  $downoad_count = Auth::user()->user_today_download_limit + 1;
		  $up_level_download = array('user_today_download_limit' => $downoad_count);
		  Members::updateReferral(Auth::user()->id,$up_level_download);
		  $tempsplit= explode('.',$item['data']->item_file);
          $extension = end($tempsplit);
		  if($item['data']->file_type == 'file')
		  {
			  if($allsettings->site_s3_storage == 1)
			  {
			  $myFile = Storage::disk('s3')->url($item['data']->item_file);
			  $newName = uniqid().time().'.'.$extension;
			  header("Cache-Control: public");
			  header("Content-Description: File Transfer");
			  header("Content-Disposition: attachment; filename=" . basename($newName));
			  header("Content-Type: application/octet-stream");
			  return readfile($myFile);	
			  }
			  else if($allsettings->site_s3_storage == 2)
			  {
			    $result = $s3->getObject(['Bucket' => $wasabi_bucket,'Key' => $item['data']->item_file]);
	            $myFile = $result["@metadata"]["effectiveUri"];
				$newName = uniqid().time().'.'.$extension;
				header("Cache-Control: public");
				header("Content-Description: File Transfer");
				header("Content-Disposition: attachment; filename=" . basename($newName));
				header("Content-Type: application/octet-stream");
				return readfile($myFile);
			  }
			  else if($allsettings->site_s3_storage == 3)
			  {
				$myFile = Storage::disk('dropbox')->url($item['data']->item_file);
				$newName = uniqid().time().'.'.$extension;
				header("Cache-Control: public");
				header("Content-Description: File Transfer");
				header("Content-Disposition: attachment; filename=" . basename($newName));
				header("Content-Type: application/octet-stream");
				return readfile($myFile);
			  }
			  else if($allsettings->site_s3_storage == 4)
			  {
			    $filename = $item['data']->item_file;
				$dir = '/';
				$recursive = false; 
				$contents = collect(Storage::disk('google')->listContents($dir, $recursive));
				$file = $contents
				->where('type', '=', 'file') 
				->where('filename', '=', pathinfo($filename, PATHINFO_FILENAME))
				->where('extension', '=', pathinfo($filename, PATHINFO_EXTENSION))
				->first(); 
				$display_product_file = Storage::disk('google')->get($file['path']);
			    //return $display_product_file;
				return response($display_product_file, 200)
						  ->header('Content-Type', $file['mimetype'])
						  ->header('Content-disposition', "attachment; filename=$filename");
						
			  }
			  else
			  {
			  
			  $filename = public_path().'/storage/items/'.$item['data']->item_file;
			  $headers = ['Content-Type: application/octet-stream'];
			  $new_name = uniqid().time().'.'.$extension;
			  return response()->download($filename,$new_name,$headers);
			  }
			  
		  }
		  else if($item['data']->file_type == 'serial')
		  {
		      if($item['data']->item_serials_list == "")
			  {
			     return redirect()->back()->with('error', 'License / Serial Key is out of stock'); 
			  }
			  else
			  {
				  if($item['data']->item_delimiter == 'comma')
				  {
					  $spilit_value = explode(",", $item['data']->item_serials_list);
					  $first_key = $spilit_value[0];
					  $take_key = $spilit_value[0].",";
					  $balance_key = ltrim($item['data']->item_serials_list, $take_key);
					  
				  }
				  else
				  {
					 $spilit_value=explode( "\n", $item['data']->item_serials_list);
					 $first_key = $spilit_value[0];
					 $take_key = $spilit_value[0]."\n";
					 $balance_key = ltrim($item['data']->item_serials_list, $take_key);
					 
				  }
				  $pdf_filename = $item['data']->item_slug.'-serial-key'.'.pdf';
				  $serial_key = $first_key;
				  $mydata = ['serial_key' => $serial_key];
				  $pdf = PDF::loadView('serial_view', $mydata);
				  $record_data = array('item_serials_list' => $balance_key);
				  Items::updateitemData($token,$record_data);
				  return $pdf->download($pdf_filename);
			  }
			  
			  
		  }
		  else
		  {
			  
			  
			  return redirect($item['data']->item_file_link);
			  
		  }
		  
		  
		}
		else
		{
		   return redirect()->back()->with('error', 'Sorry! Today your download limit reached. please check your profile page');
		}  	
		  
	  }
	  else
	  {
	     return redirect('/404');
	  }  
	  
	 
	
	}
	
	
	
	
	public function view_follow($my_id,$follow_id)
	{
	   $user_id = $follow_id;
	   $followcheck = Items::getfollowuserCheck($user_id);
	   $data = array('follower_user_id' => $my_id, 'following_user_id' => $follow_id);
	   if($followcheck == 0)
	   {
	       Items::saveFollow($data);
	   }
	   else
	   {
	      return redirect()->back();
	   }
	   return redirect()->back();
	   
	}
	
	
	public function view_top_authors() 
	{
	  $sid = 1;
	  $setting['setting'] = Settings::editGeneral($sid);
	  
	  //$users = Items::with('ratings')->select('users.id','users.username','users.created_at','users.referral_count','users.user_photo','users.user_document_verified','users.country_badge','users.exclusive_author','country.country_id','country.country_name','country.country_badges','item_order.item_user_id')->leftjoin('users', 'users.id', '=', 'items.user_id')->leftJoin('item_order','item_order.item_user_id','users.id')->leftJoin('country','country.country_id','users.country')->where('users.drop_status','=','no')->where('users.id','!=',1)->orderByRaw('users.user_document_verified DESC')->groupBy('item_order.item_user_id')->get();
	  //$users = Members::topAuthor();
	  
	  $itemData['item'] = Items::with('ratings')
		       ->select('users.id','users.username','users.name','users.created_at','users.referral_count','users.user_photo','users.user_document_verified','users.country_badge','users.exclusive_author','country.country_id','country.country_name','country.country_badges')
			   ->join('users', 'users.id', '=', 'items.user_id')
			   
			   ->join('country','country.country_id','users.country')
			   ->where('users.drop_status','=','no')
			   ->where('users.id','!=',1)
			   ->orderByRaw('users.user_document_verified DESC')
			   ->groupBy('users.id')
			   ->paginate($setting['setting']->site_item_per_page);
			   
	  $count_items = Items::getgroupItems();
	  $count_sale = Items::getgroupSale();
	  $sid = 1;
	   $badges['setting'] = Settings::editBadges($sid);
	   $category['view'] = Category::with('SubCategory')->where('category_status','=','1')->where('drop_status','=','no')->orderBy('menu_order','asc')->get();
	   $popular['items'] = Items::with('ratings')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.item_status','=',1)->where('items.drop_status','=','no')->orderBy('items.item_views', 'desc')->take(5)->get();
	  $data = array('itemData' => $itemData,'count_items' => $count_items, 'count_sale' => $count_sale, 'badges' => $badges, 'category' => $category, 'popular' => $popular);
	  return view('top-authors')->with($data);
	}
	
	
	
	public function view_user_reviews($slug)
	{
	   $check_user = Members::getInuserCount($slug);
	   if($check_user != 0)
	   {
	   $user['user'] = Members::getInuser($slug);
	   $user_id = $user['user']->id;
	   
	   /* badges */
	   $sid = 1;
	   $badges['setting'] = Settings::editBadges($sid);
	   $sold['item'] = Items::SoldAmount($user_id);
	   $sold_amount = 0;
	   foreach($sold['item'] as $iter)
	   {
			$sold_amount += $iter->total_price;
	   }
	   $country['view'] = Settings::editCountry($user['user']->country);
	   $membership = date('m/d/Y',strtotime($user['user']->created_at));
	  $membership_date = explode("/", $membership);
      $year = (date("md", date("U", mktime(0, 0, 0, $membership_date[0], $membership_date[1], $membership_date[2]))) > date("md")
		? ((date("Y") - $membership_date[2]) - 1)
		: (date("Y") - $membership_date[2]));
	  
	   $collect_amount = Items::CollectedAmount($user_id);
	   $referral_count = $user['user']->referral_count;
	   /* badges */
	   
	   
	   $itemData['item'] = Items::with('ratings')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.user_id','=',$user_id)->where('items.item_status','=',1)->where('items.drop_status','=','no')->orderBy('items.item_id', 'desc')->get();
	   
	   $since = date("F Y", strtotime($user['user']->created_at));
	   
	   $getitemcount = Items::getuseritemCount($user_id);
	   
	   $getsalecount = Items::getsaleitemCount($user_id);
	   
	   
	   
		  $getreview  = Items::getreviewData($user_id);
		  if($getreview !=0)
		  {
			  $review['view'] = Items::getreviewRecord($user_id);
			  $top = 0;
			  $bottom = 0;
			  foreach($review['view'] as $review)
			  {
				 if($review->rating == 1) { $value1 = $review->rating*1; } else { $value1 = 0; }
				 if($review->rating == 2) { $value2 = $review->rating*2; } else { $value2 = 0; }
				 if($review->rating == 3) { $value3 = $review->rating*3; } else { $value3 = 0; }
				 if($review->rating == 4) { $value4 = $review->rating*4; } else { $value4 = 0; }
				 if($review->rating == 5) { $value5 = $review->rating*5; } else { $value5 = 0; }
				 
				 $top += $value1 + $value2 + $value3 + $value4 + $value5;
				 $bottom += $review->rating;
				 
			  }
			  if(!empty(round($top/$bottom)))
			  {
				$count_rating = round($top/$bottom);
			  }
			  else
			  {
				$count_rating = 0;
			  }
			  
			  
			  
		  }
		  else
		  {
			$count_rating = 0;
			$bottom = 0;
		  }
	   
	    $ratingview['list'] = Items::getreviewUser($user_id);
		$countreview = Items::getreviewCountUser($user_id);
		
		if (Auth::check())
		{
		$followcheck = Items::getfollowuserCheck($user_id);
		}
		else
		{
		 $followcheck = 0;
		}
		
		$followingcount = Items::getfollowingCount($user_id);
		
		$followercount = Items::getfollowerCount($user_id);
		
		$featured_count = Items::getfeaturedUser($user_id);
	   $free_count = Items::getfreeUser($user_id);
	   $tren_count = Items::getTrendUser($user_id);
		
	   $data = array('user' => $user, 'since' => $since, 'itemData' => $itemData, 'getitemcount' => $getitemcount, 'getsalecount' => $getsalecount, 'count_rating' => $count_rating, 'bottom' => $bottom, 'ratingview' => $ratingview, 'countreview' => $countreview, 'getreview' => $getreview, 'followcheck' => $followcheck, 'followingcount' =>  $followingcount, 'followercount' => $followercount, 'badges' => $badges, 'sold_amount' => $sold_amount, 'country' => $country, 'year' => $year, 'collect_amount' => $collect_amount, 'referral_count' => $referral_count, 'featured_count' => $featured_count, 'free_count' => $free_count, 'tren_count' => $tren_count);
	   return view('user-reviews')->with($data);
	   }
	   else
	   {
	   return redirect('/404');
	   }
	
	}
	
	
	public function view_user_followers($slug)
	{
	   $check_user = Members::getInuserCount($slug);
	   if($check_user != 0)
	   {
	   $user['user'] = Members::getInuser($slug);
	   $user_id = $user['user']->id;
	   
	   /* badges */
	   $sid = 1;
	   $badges['setting'] = Settings::editBadges($sid);
	   $sold['item'] = Items::SoldAmount($user_id);
	   $sold_amount = 0;
	   foreach($sold['item'] as $iter)
	   {
			$sold_amount += $iter->total_price;
	   }
	   $country['view'] = Settings::editCountry($user['user']->country);
	   $membership = date('m/d/Y',strtotime($user['user']->created_at));
	  $membership_date = explode("/", $membership);
      $year = (date("md", date("U", mktime(0, 0, 0, $membership_date[0], $membership_date[1], $membership_date[2]))) > date("md")
		? ((date("Y") - $membership_date[2]) - 1)
		: (date("Y") - $membership_date[2]));
	  
	   $collect_amount = Items::CollectedAmount($user_id);
	   $referral_count = $user['user']->referral_count;
	   /* badges */
	   
	   
	   $itemData['item'] = Items::with('ratings')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.user_id','=',$user_id)->where('items.item_status','=',1)->where('items.drop_status','=','no')->orderBy('items.item_id', 'desc')->get();
	   
	   $since = date("F Y", strtotime($user['user']->created_at));
	   
	   $getitemcount = Items::getuseritemCount($user_id);
	   
	   $getsalecount = Items::getsaleitemCount($user_id);
	   
	   
	   
		  $getreview  = Items::getreviewData($user_id);
		  if($getreview !=0)
		  {
			  $review['view'] = Items::getreviewRecord($user_id);
			  $top = 0;
			  $bottom = 0;
			  foreach($review['view'] as $review)
			  {
				 if($review->rating == 1) { $value1 = $review->rating*1; } else { $value1 = 0; }
				 if($review->rating == 2) { $value2 = $review->rating*2; } else { $value2 = 0; }
				 if($review->rating == 3) { $value3 = $review->rating*3; } else { $value3 = 0; }
				 if($review->rating == 4) { $value4 = $review->rating*4; } else { $value4 = 0; }
				 if($review->rating == 5) { $value5 = $review->rating*5; } else { $value5 = 0; }
				 
				 $top += $value1 + $value2 + $value3 + $value4 + $value5;
				 $bottom += $review->rating;
				 
			  }
			  if(!empty(round($top/$bottom)))
			  {
				$count_rating = round($top/$bottom);
			  }
			  else
			  {
				$count_rating = 0;
			  }
			  
			  
			  
		  }
		  else
		  {
			$count_rating = 0;
			$bottom = 0;
		  }
	   
	    $ratingview['list'] = Items::getreviewUser($user_id);
		$countreview = Items::getreviewCountUser($user_id);
		
		if (Auth::check())
		{
		$followcheck = Items::getfollowuserCheck($user_id);
		
		}
		else
		{
		 $followcheck = 0;
		 
		}
		$followingcount = Items::getfollowingCount($user_id);
		
		$followercount = Items::getfollowerCount($user_id);
		
		$viewfollowing['view'] = Items::getfollowerView($user_id);
		
		$featured_count = Items::getfeaturedUser($user_id);
	   $free_count = Items::getfreeUser($user_id);
	   $tren_count = Items::getTrendUser($user_id);
		//$viewfollowing['view'] = Follow::with('followers')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('follow.following_user_id','=',$user_id)->orderBy('follow.fid', 'desc')->get();
		
	   $data = array('user' => $user, 'since' => $since, 'itemData' => $itemData, 'getitemcount' => $getitemcount, 'getsalecount' => $getsalecount, 'count_rating' => $count_rating, 'bottom' => $bottom, 'ratingview' => $ratingview, 'countreview' => $countreview, 'getreview' => $getreview, 'followcheck' => $followcheck, 'followingcount' =>  $followingcount, 'followercount' => $followercount, 'viewfollowing' => $viewfollowing, 'badges' => $badges, 'sold_amount' => $sold_amount, 'country' => $country, 'year' => $year, 'collect_amount' => $collect_amount, 'referral_count' => $referral_count, 'featured_count' => $featured_count, 'free_count' => $free_count, 'tren_count' => $tren_count);
	   return view('user-followers')->with($data); 
	   }
	   else
	   {
	   return redirect('/404');
	   }
	  
	}
	
	
	
	public function view_user_following($slug)
	{
	   $check_user = Members::getInuserCount($slug);
	   if($check_user != 0)
	   {
	   $user['user'] = Members::getInuser($slug);
	   $user_id = $user['user']->id;
	   
	   /* badges */
	   $sid = 1;
	   $badges['setting'] = Settings::editBadges($sid);
	   $sold['item'] = Items::SoldAmount($user_id);
	   $sold_amount = 0;
	   foreach($sold['item'] as $iter)
	   {
			$sold_amount += $iter->total_price;
	   }
	   $country['view'] = Settings::editCountry($user['user']->country);
	   $membership = date('m/d/Y',strtotime($user['user']->created_at));
	  $membership_date = explode("/", $membership);
      $year = (date("md", date("U", mktime(0, 0, 0, $membership_date[0], $membership_date[1], $membership_date[2]))) > date("md")
		? ((date("Y") - $membership_date[2]) - 1)
		: (date("Y") - $membership_date[2]));
	  
	   $collect_amount = Items::CollectedAmount($user_id);
	   $referral_count = $user['user']->referral_count;
	   /* badges */
	   
	   
	   $itemData['item'] = Items::with('ratings')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.user_id','=',$user_id)->where('items.item_status','=',1)->where('items.drop_status','=','no')->orderBy('items.item_id', 'desc')->get();
	   
	   $since = date("F Y", strtotime($user['user']->created_at));
	   
	   $getitemcount = Items::getuseritemCount($user_id);
	   
	   $getsalecount = Items::getsaleitemCount($user_id);
	   
	   
	   
		  $getreview  = Items::getreviewData($user_id);
		  if($getreview !=0)
		  {
			  $review['view'] = Items::getreviewRecord($user_id);
			  $top = 0;
			  $bottom = 0;
			  foreach($review['view'] as $review)
			  {
				 if($review->rating == 1) { $value1 = $review->rating*1; } else { $value1 = 0; }
				 if($review->rating == 2) { $value2 = $review->rating*2; } else { $value2 = 0; }
				 if($review->rating == 3) { $value3 = $review->rating*3; } else { $value3 = 0; }
				 if($review->rating == 4) { $value4 = $review->rating*4; } else { $value4 = 0; }
				 if($review->rating == 5) { $value5 = $review->rating*5; } else { $value5 = 0; }
				 
				 $top += $value1 + $value2 + $value3 + $value4 + $value5;
				 $bottom += $review->rating;
				 
			  }
			  if(!empty(round($top/$bottom)))
			  {
				$count_rating = round($top/$bottom);
			  }
			  else
			  {
				$count_rating = 0;
			  }
			  
			  
			  
		  }
		  else
		  {
			$count_rating = 0;
			$bottom = 0;
		  }
	   
	    $ratingview['list'] = Items::getreviewUser($user_id);
		$countreview = Items::getreviewCountUser($user_id);
		
		if (Auth::check())
		{
		$followcheck = Items::getfollowuserCheck($user_id);
		
		}
		else
		{
		 $followcheck = 0;
		 
		}
		$followingcount = Items::getfollowingCount($user_id);
		
		$followercount = Items::getfollowerCount($user_id);
		
		$viewfollowing['view'] = Items::getfollowingView($user_id);
		
		$featured_count = Items::getfeaturedUser($user_id);
	   $free_count = Items::getfreeUser($user_id);
	   $tren_count = Items::getTrendUser($user_id);
		//$viewfollowing['view'] = Follow::with('followers')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('follow.following_user_id','=',$user_id)->orderBy('follow.fid', 'desc')->get();
		
	   $data = array('user' => $user, 'since' => $since, 'itemData' => $itemData, 'getitemcount' => $getitemcount, 'getsalecount' => $getsalecount, 'count_rating' => $count_rating, 'bottom' => $bottom, 'ratingview' => $ratingview, 'countreview' => $countreview, 'getreview' => $getreview, 'followcheck' => $followcheck, 'followingcount' =>  $followingcount, 'followercount' => $followercount, 'viewfollowing' => $viewfollowing, 'badges' => $badges, 'sold_amount' => $sold_amount, 'country' => $country, 'year' => $year, 'collect_amount' => $collect_amount, 'referral_count' => $referral_count, 'featured_count' => $featured_count, 'free_count' => $free_count, 'tren_count' => $tren_count);
	   return view('user-following')->with($data); 
	   }
	   else
	   {
	   return redirect('/404');
	   }
	  
	}
	
	
	
	
	
	
	
	
	public function send_message(Request $request)
	{
	  $message_text = $request->input('message');
	  $from_email = $request->input('from_email');
	  $from_name = $request->input('from_name');
	  $to_email = $request->input('to_email');
	  $to_name = $request->input('to_name');
	  $user_id = $request->input('to_id');
	  $check_email_support = Members::getuserSubscription($user_id);
	  $sid = 1;
	  $setting['setting'] = Settings::editGeneral($sid);
	  $admin_email = $setting['setting']->sender_email;
	  $additional['settings'] = Settings::editAdditional();
	  if($additional['settings']->site_google_recaptcha == 1)
	  {
			 $request->validate([
								'message' => 'required',
								'g-recaptcha-response' => 'required|recaptchav3:register,0.5'
								
								
			 ]);
	  }
	  else
	  {
		    $request->validate([
								'message' => 'required',
			]);
	  }
	  $rules = array(
				
				
	     );
		 
		 $messsages = array(
		      
	    );
		 
	 $validator = Validator::make($request->all(), $rules,$messsages);
	 if ($validator->fails()) 
	 {
		 $failedRules = $validator->failed();
		 return back()->withErrors($validator);
	 } 
	 else
	 {
	
		  if($check_email_support == 1)
		  {
				
			$record = array('message_text' => $message_text, 'from_name' => $from_name, 'from_email' => $from_email);
			/* email template code */
				  $checktemp = EmailTemplate::checkTemplate(3);
				  if($checktemp != 0)
				  {
				  $template_view['mind'] = EmailTemplate::viewTemplate(3);
				  $template_subject = $template_view['mind']->et_subject;
				  }
				  else
				  {
				  $template_subject = "New message received";
				  }
				  /* email template code */
			Mail::send('user_mail', $record, function($message) use ($from_name, $from_email, $to_email, $to_name, $admin_email, $template_subject) {
				$message->to($to_email, $to_name)
						->subject($template_subject);
				$message->from($admin_email,$from_name);
			});
			
		   }	
		   
			return redirect()->back()->with('success','Your message has been sent successfully');     
	   }	
	
	
	}
	
	
	
	public function update_reset(Request $request)
	{
	
	   $user_token = $request->input('user_token');
	   $password = bcrypt($request->input('password'));
	   $password_confirmation = $request->input('password_confirmation');
	   $data = array("user_token" => $user_token);
	   $value = Members::verifytokenData($data);
	   $user['user'] = Members::gettokenData($user_token);
	   if($value)
	   {
	   
	      $request->validate([
							'password' => 'required|confirmed|min:6',
							
           ]);
		 $rules = array(
				
				
	     );
		 
		 $messsages = array(
		      
	    );
		 
		$validator = Validator::make($request->all(), $rules,$messsages);
		
		if ($validator->fails()) 
		{
		 $failedRules = $validator->failed();
		 return back()->withErrors($validator);
		} 
		else
		{
		   
		   $record = array('password' => $password);
           Members::updatepasswordData($user_token, $record);
           return redirect('login')->with('success','Your new password updated successfully. Please login now.');
		
		}
	   
	   
	   }
	   else
	   {
              
			  return redirect()->back()->with('error', 'These credentials do not match our records.');
       }
	   
	   
	
	}
	
	
	
	public function update_forgot(Request $request)
	{
	   $email = $request->input('email');
	   
	   $data = array("email"=>$email);
 
       $value = Members::verifycheckData($data);
	   $user['user'] = Members::getemailData($email);
       
	   if($value)
	   {
			
		$user_token = $user['user']->user_token;
		$name = $user['user']->name;
		$sid = 1;
		$setting['setting'] = Settings::editGeneral($sid);
		
		$from_name = $setting['setting']->sender_name;
        $from_email = $setting['setting']->sender_email;
		$forgot_url = URL::to('/reset/').'/'.$user_token;
		$record = array('user_token' => $user_token, 'forgot_url' => $forgot_url);
		/* email template code */
	          $checktemp = EmailTemplate::checkTemplate(4);
			  if($checktemp != 0)
			  {
			  $template_view['mind'] = EmailTemplate::viewTemplate(4);
			  $template_subject = $template_view['mind']->et_subject;
			  }
			  else
			  {
			  $template_subject = "Forgot Password";
			  }
			  /* email template code */
		Mail::send('forgot_mail', $record, function($message) use ($from_name, $from_email, $email, $name, $user_token, $forgot_url, $template_subject) {
			$message->to($email, $name)
					->subject($template_subject);
			$message->from($from_email,$from_name);
		});
 
         return redirect('forgot')->with('success','We have e-mailed your password reset link!');     
			  
       }
	   else
	   {
              
			  return redirect()->back()->with('error', 'These credentials do not match our records.');
       }
	   
	  
	   
	   
	   
	}
	
	/* shop */
	
	
	public function view_all_list_items()
	{
	  $itemData['item'] = Items::with('ratings')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.item_status','=',1)->where('items.drop_status','=','no')->orderBy('items.item_id', 'asc')->get();
	  $catData['item'] = Items::getitemcatData();
	  
	  return view('shop-list',[ 'itemData' => $itemData, 'catData' => $catData]);
	  
	}
	
	
	
	
	public function view_category_types($type,$slug)
	{
	  $method = "get";
	  $sid = 1;
      $setting['setting'] = Settings::editGeneral($sid);
	  $today = date("Y-m-d");
	  $additional['settings'] = Settings::editAdditional();
	  if($additional['settings']->subscription_mode == 1)
	  { 
			  if($type == 'item-type')
			  {
			      if($additional['settings']->shop_search_type == 'normal')
				  {
				  $itemData['item'] = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list','items.item_type_cat_id')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('users.user_subscr_date','>=',$today)->where('users.user_subscr_payment_status','=','completed')->where('items.item_status','=',1)->where('items.drop_status','=','no')->where('items.item_type','=',$slug)->orderBy('items.item_id', 'desc')->paginate($setting['setting']->site_item_per_page);
				  }
				  else
				  {
				    $itemData['item'] = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list','items.item_type_cat_id')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('users.user_subscr_date','>=',$today)->where('users.user_subscr_payment_status','=','completed')->where('items.item_status','=',1)->where('items.drop_status','=','no')->where('items.item_type','=',$slug)->orderBy('items.item_id', 'desc')->get();
				  }
				  $seo_keyword = $setting['setting']->site_keywords;
				  $seo_desc = $setting['setting']->site_desc;
			  }
			  else
			  {
				if($type == 'category')
				{
				   $check_data = Category::getcategoryCheck($slug);
				   if($check_data != 0)
				   {
				   $category_data = Category::getcategorysingle($slug);
				   $category_id = $category_data->cat_id;
					   if($additional['settings']->shop_search_type == 'normal')
					   {
					   $itemData['item'] = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list','items.item_type_cat_id')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('users.user_subscr_date','>=',$today)->where('users.user_subscr_payment_status','=','completed')->where('items.item_status','=',1)->where('items.drop_status','=','no')->where('items.item_category_parent','=',$category_id)->orderBy('items.item_id', 'desc')->paginate($setting['setting']->site_item_per_page);
					   }
					   else
					   {
					   $itemData['item'] = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list','items.item_type_cat_id')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('users.user_subscr_date','>=',$today)->where('users.user_subscr_payment_status','=','completed')->where('items.item_status','=',1)->where('items.drop_status','=','no')->where('items.item_category_parent','=',$category_id)->orderBy('items.item_id', 'desc')->get();
					   }
					  if($category_data->category_allow_seo == 1)
					  { 
					  $seo_keyword = $category_data->category_seo_keyword;
				      $seo_desc = $category_data->category_seo_desc;
					  }
					  else
					  {
					  $seo_keyword = $setting['setting']->site_keywords;
				      $seo_desc = $setting['setting']->site_desc;
					  }
				   }
				   else
				   {
				     return view('/404');
				   }
				}
				else
				{
				  $check_data = Category::getsubcategoryCheck($slug);
				  if($check_data != 0)
				  {
				  $category_data = Category::getsubcategorysingle($slug);
				  $category_id = $category_data->subcat_id;
				    if($additional['settings']->shop_search_type == 'normal')
					{
					  $itemData['item'] = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list','items.item_type_cat_id')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('users.user_subscr_date','>=',$today)->where('users.user_subscr_payment_status','=','completed')->where('items.item_status','=',1)->where('items.drop_status','=','no')->where('items.item_category_type','=',$type)->where('items.item_category','=',$category_id)->orderBy('items.item_id', 'desc')->paginate($setting['setting']->site_item_per_page);
					 }
					 else
					 {
					   $itemData['item'] = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list','items.item_type_cat_id')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('users.user_subscr_date','>=',$today)->where('users.user_subscr_payment_status','=','completed')->where('items.item_status','=',1)->where('items.drop_status','=','no')->where('items.item_category_type','=',$type)->where('items.item_category','=',$category_id)->orderBy('items.item_id', 'desc')->get();
					 }
					 if($category_data->subcategory_allow_seo == 1)
					 {
					 $seo_keyword = $category_data->subcategory_seo_keyword;
				     $seo_desc = $category_data->subcategory_seo_desc;
					 }
					 else
					 {
					 $seo_keyword = $setting['setting']->site_keywords;
				     $seo_desc = $setting['setting']->site_desc;
					 }
					 
				  }
				   else
				   {
				     return view('/404');
				   }
				}
				
			  }
	   }
	   else
	   {
	         if($type == 'item-type')
			  {
			      if($additional['settings']->shop_search_type == 'normal')
				  {
					  $itemData['item'] = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list','items.item_type_cat_id')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.item_status','=',1)->where('items.drop_status','=','no')->where('items.item_type','=',$slug)->orderBy('items.item_id', 'desc')->paginate($setting['setting']->site_item_per_page);
				  }
				  else
				  {
				     	$itemData['item'] = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list','items.item_type_cat_id')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.item_status','=',1)->where('items.drop_status','=','no')->where('items.item_type','=',$slug)->orderBy('items.item_id', 'desc')->get();

				  }
				  $seo_keyword = $setting['setting']->site_keywords;
				  $seo_desc = $setting['setting']->site_desc;	  
			  }
			  else
			  {
				if($type == 'category')
				{
				   $check_data = Category::getcategoryCheck($slug);
				   if($check_data != 0)
				   {
				   $category_data = Category::getcategorysingle($slug);
				   $category_id = $category_data->cat_id;
				     	if($additional['settings']->shop_search_type == 'normal')
				     	{
					   $itemData['item'] = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list','items.item_type_cat_id')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.item_status','=',1)->where('items.drop_status','=','no')->where('items.item_category_parent','=',$category_id)->orderBy('items.item_id', 'desc')->paginate($setting['setting']->site_item_per_page);
					   }
					   else
					   {
					   $itemData['item'] = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list','items.item_type_cat_id')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.item_status','=',1)->where('items.drop_status','=','no')->where('items.item_category_parent','=',$category_id)->orderBy('items.item_id', 'desc')->get();
					   }
					  if($category_data->category_allow_seo == 1)
					  { 
					  $seo_keyword = $category_data->category_seo_keyword;
				      $seo_desc = $category_data->category_seo_desc;
					  }
					  else
					  {
					  $seo_keyword = $setting['setting']->site_keywords;
				      $seo_desc = $setting['setting']->site_desc;
					  }
				   }
				   else
				   {
				     return view('/404');
				   }
				}
				else
				{
				  $check_data = Category::getsubcategoryCheck($slug);
				  if($check_data != 0)
				  {
				  $category_data = Category::getsubcategorysingle($slug);
				  $category_id = $category_data->subcat_id;
				    if($additional['settings']->shop_search_type == 'normal')
				    {
					  $itemData['item'] = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list','items.item_type_cat_id')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.item_status','=',1)->where('items.drop_status','=','no')->where('items.item_category_type','=',$type)->where('items.item_category','=',$category_id)->orderBy('items.item_id', 'desc')->paginate($setting['setting']->site_item_per_page);
					 }
					 else
					 {
					   $itemData['item'] = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list','items.item_type_cat_id')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.item_status','=',1)->where('items.drop_status','=','no')->where('items.item_category_type','=',$type)->where('items.item_category','=',$category_id)->orderBy('items.item_id', 'desc')->get();
					 }
					 if($category_data->subcategory_allow_seo == 1)
					 {
					 $seo_keyword = $category_data->subcategory_seo_keyword;
				     $seo_desc = $category_data->subcategory_seo_desc;
					 }
					 else
					 {
					 $seo_keyword = $setting['setting']->site_keywords;
				     $seo_desc = $setting['setting']->site_desc;
					 } 
				  }
				  else
				  {
				     return view('/404');
				  }
				}
				
			  }
	   
	   
	   }	  
	  
	  $catData['item'] = Items::getitemcatData();
	  $category['view'] = Category::with('SubCategory')->where('category_status','=','1')->where('drop_status','=','no')->orderBy('menu_order','asc')->get();
	  $getWell['type'] = Items::gettypeStatus();
	  $minprice['price'] = Items::minpriceData();
	  $maxprice['price'] = Items::maxpriceData();
	  return view('shop',[ 'minprice' => $minprice, 'maxprice' => $maxprice, 'getWell' => $getWell, 'itemData' => $itemData, 'catData' => $catData, 'category' => $category, 'method' => $method, 'seo_keyword' => $seo_keyword, 'seo_desc' => $seo_desc]);
	  
	}
	
	
	
	
	
	
	public function view_shop_items(Request $request)
	{
	  $method = "post";
	  $today = date("Y-m-d");
	  $additional['settings'] = Settings::editAdditional();
	  $product_item = $request->input('product_item');
	  $sid = 1;
	  $setting['setting'] = Settings::editGeneral($sid);
	  
	  Cache::put('search_keyword', $product_item, now()->addDays(1));
	  $search_keyword = Cache::get('search_keyword');
	  
	  if(!empty($request->input('category_names')))
	   {
	      
		  $category_no = "";
		  $category_ids = "";
		  
		  foreach($request->input('category_names') as $category_value)
		  {
		     $category_no .= $category_value.',';
			 if(!empty($category_value))
			 {
			 $pieces = explode("_", $category_value);
			 $category_ids .= $pieces[1].',';
			 }
			 else
			 {
			 $category_ids .= "";
			 }
		  }
		  $category_names = rtrim($category_no,",");
		  $category_id = rtrim($category_ids,",");
		  
	   }
	   else
	   {
	     $category_names = "";
		 $category_id = "";
	   }
	  
	  if(!empty($request->input('item_type')))
	   {
	      
		  $itemtype = "";
		  foreach($request->input('item_type') as $item_type)
		  {
		     $itemtype .= $item_type.',';
		  }
		  $item_types = rtrim($itemtype,",");
		  
	   }
	   else
	   {
	     $item_types = "";
	   } 
	  if(!empty($request->input('orderby')))
	  { 
	  $orderby = $request->input('orderby');
	  }
	  else
	  {
	  $orderby = "desc";
	  }
	  $min_price = $request->input('min_price');
	  $max_price = $request->input('max_price'); 
	  if($search_keyword != "" ||  $orderby != "" || $min_price != "" || $max_price != "" || $item_types != "" || $category_names != "")
	  {
	  
	  
		  if($additional['settings']->subscription_mode == 1)
		  {
			  $itemData['item'] = Items::with('ratings')
			                  ->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list') 
							  ->leftjoin('users', 'users.id', '=', 'items.user_id')
							  ->where('users.user_subscr_date','>=',$today)
							  ->where('users.user_subscr_payment_status','=','completed')
							  ->where('items.item_status','=',1)
							  ->where('items.drop_status','=','no')
							  ->where(function ($query) use ($search_keyword,$orderby,$min_price,$max_price,$item_types,$category_names,$category_id) { 
							  $query->where('items.item_name', 'LIKE', "%$search_keyword%");
							  if ($min_price != "" || $max_price != "")
							  {
							  $query->where('items.regular_price', '>=', $min_price);
							  $query->where('items.regular_price', '<=', $max_price);
							  }
							  if ($item_types != "")
							  {
							  $query->whereRaw('FIND_IN_SET(items.item_type,"'.$item_types.'")');
							  }
							  if ($category_names != "")
							  {
							      $pieces = explode("_", $category_names);
								  if($pieces[0] == 'category')
								  {
							  		$query->whereRaw('FIND_IN_SET(items.item_category_parent,"'.$category_id.'")');
								  }
								  else
								  {
								    $query->whereRaw('FIND_IN_SET(items.item_type_cat_id,"'.$category_names.'")');
								  }	
							  
							  }
							  })->orderBy('items.regular_price', $orderby)->get();
			}
			else
			{
				$itemData['item'] = Items::with('ratings')
				              ->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list')
							  ->leftjoin('users', 'users.id', '=', 'items.user_id')
							  ->where('items.item_status','=',1)
							  ->where('items.drop_status','=','no')
							  ->where(function ($query) use ($search_keyword,$orderby,$min_price,$max_price,$item_types,$category_names,$category_id) { 
							  $query->where('items.item_name', 'LIKE', "%$search_keyword%");
							  if ($min_price != "" || $max_price != "")
							  {
							  $query->where('items.regular_price', '>=', $min_price);
							  $query->where('items.regular_price', '<=', $max_price);
							  }
							  if ($item_types != "")
							  {
							  $query->whereRaw('FIND_IN_SET(items.item_type,"'.$item_types.'")');
							  }
							  if ($category_names != "")
							  {
							      $pieces = explode("_", $category_names);
								  if($pieces[0] == 'category')
								  {
							  		$query->whereRaw('FIND_IN_SET(items.item_category_parent,"'.$category_id.'")');
								  }
								  else
								  {
								    $query->whereRaw('FIND_IN_SET(items.item_type_cat_id,"'.$category_names.'")');
								  }	
							  
							  }
							  })->orderBy('items.regular_price', $orderby)->get();
			}				  
							  
						  
	  }
	  else
	  {
	   	
		  if($additional['settings']->subscription_mode == 1)
		  {   
	       $itemData['item'] = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('users.user_subscr_date','>=',$today)->where('users.user_subscr_payment_status','=','completed')->where('items.item_status','=',1)->where('items.drop_status','=','no')->orderBy('items.item_id', 'asc')->get();     
		  }
		  else
		  {
		    $itemData['item'] = Items::with('ratings')->select('items.item_id','items.item_liked','items.item_slug','items.item_preview','items.item_name','items.item_type','items.item_type_id','users.user_photo','users.username','users.user_document_verified','items.updated_item','items.item_sold','items.free_download','items.item_flash','items.regular_price','items.item_token','items.user_id','items.file_type','items.item_delimiter','items.item_serials_list')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.item_status','=',1)->where('items.drop_status','=','no')->orderBy('items.item_id', 'asc')->get();
		  } 
	   
	  }
	 	 
	 
	$category['view'] = Category::with('SubCategory')->where('category_status','=','1')->where('drop_status','=','no')->orderBy('menu_order','asc')->get();
	$type = "";
	$meta_keyword = "";
	$meta_desc = "";
	$getWell['type'] = Items::gettypeStatus();
	$minprice['price'] = Items::minpriceData();
	$maxprice['price'] = Items::maxpriceData();
	$seo_keyword = $setting['setting']->site_keywords;
	$seo_desc = $setting['setting']->site_desc;
	return view('shop',[ 'minprice' => $minprice, 'maxprice' => $maxprice, 'getWell' => $getWell, 'itemData' => $itemData, 'category' => $category, 'type' => $type, 'meta_keyword' => $meta_keyword, 'meta_desc' => $meta_desc, 'method' => $method, 'seo_keyword' => $seo_keyword, 'seo_desc' => $seo_desc]);
	}
	
	
	
	
	
	/*public function view_shop_items(Request $request)
	{
	  
	 if(!empty($request->input('product_item')))
	 {
	 $product_item = $request->input('product_item');
	 
	 $itemData['item'] = Items::with('ratings')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.item_status','=',1)->where('items.drop_status','=','no')->where('items.item_name', 'LIKE', "%$product_item%")->orderBy('items.item_id', 'desc')->get();
	   
	 } 
	 else if(!empty($request->input('category')))
	 {
	 
	 $category = $request->input('category');
	 $split = explode("_", $category);
	 $cat_id = $split[1];
	 $cat_name = $split[0];
	 
	 
	 $itemData['item'] = Items::with('ratings')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.item_status','=',1)->where('items.drop_status','=','no')->where('items.item_category','=',$cat_id)->where('items.item_category_type','=',$cat_name)->orderBy('items.item_id', 'desc')->get();
	 }
	 else if(!empty($request->input('product_item')) && !empty($request->input('category')))
	 {
	    $product_item = $request->input('product_item');
		$category = $request->input('category');
		 $split = explode("_", $category);
		 $cat_id = $split[1];
		 $cat_name = $split[0];
		 $itemData['item'] = Items::with('ratings')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.item_status','=',1)->where('items.drop_status','=','no')->where('items.item_name', 'LIKE', "%$product_item%")->where('items.item_category','=',$cat_id)->where('items.item_category_type','=',$cat_name)->orderBy('items.item_id', 'desc')->get();
	 }
	 else
	 {
	   $itemData['item'] = Items::with('ratings')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.item_status','=',1)->where('items.drop_status','=','no')->orderBy('items.item_id', 'desc')->get();;
	 }
	 
	 $catData['item'] = Items::getitemcatData();
	
	return view('shop',[ 'itemData' => $itemData, 'catData' => $catData]);
	}*/
	/* shop */
	
	
	/* item */
	
	
	public function view_single_item($item_slug)
	{
	  
	  $sid = 1;
	  $badges['setting'] = Settings::editBadges($sid);
	  $check_item_available = Items::singleitemCount($item_slug);
	  if($check_item_available != 0)
	  {
	  $check_if_item = Items::AgainData($item_slug);
	  
	  if($check_if_item != 0)
	  {
	      $item['item'] = Items::singleitemData($item_slug);
		  $view_count = $item['item']->item_views + 1;
		  $count_data = array('item_views' => $view_count);
		  $item_id = $item['item']->item_id;
		  Items::updatefavouriteData($item_id,$count_data);
		  $membership = date('m/d/Y',strtotime($item['item']->created_at));
		  $membership_date = explode("/", $membership);
		  $year = (date("md", date("U", mktime(0, 0, 0, $membership_date[0], $membership_date[1], $membership_date[2]))) > date("md")
			? ((date("Y") - $membership_date[2]) - 1)
			: (date("Y") - $membership_date[2]));
		  
		  $token = $item['item']->item_token;
		  $trends = Items::trendsCount($token);
		  $item_cat_id = $item['item']->item_category;
		  $item_user_id = $item['item']->user_id;
		  $item_cat_type = $item['item']->item_category_type;
		  $country['view'] = Settings::editCountry($item['item']->country);
		  
		  $sold['item'] = Items::SoldAmount($item_user_id);
		  $sold_amount = 0;
		  foreach($sold['item'] as $iter)
		  {
			$sold_amount += $iter->total_price;
		  }
		  $collect_amount = Items::CollectedAmount($item_user_id);
		  $referral_count = $item['item']->referral_count;
		  
		  
		  
		  if($item_cat_type == 'category')
		  {
			 $category['name'] = Category::getsinglecatData($item_cat_id);
			 $category_name = $category['name']->category_name;
		  }
		  else if($item_cat_type == 'subcategory')
		  {
			$category['name'] = SubCategory::getsinglesubcatData($item_cat_id);
			$category_name = $category['name']->subcategory_name;
		  }
		  else
		  {
			$category_name = "";
		  }
		  
		  $item_tags = explode(',',$item['item']->item_tags);
		  
		  $getcount  = Items::getimagesCount($token);
		  $item_image['item'] = Items::getsingleimagesData($token);
		  $item_allimage = Items::getimagesData($token);
		  $itemData['item'] = Items::with('ratings')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.item_status','=',1)->where('items.drop_status','=','no')->where('items.user_id','=',$item_user_id)->where('items.item_id','!=',$item_id)->orderBy('items.item_id', 'asc')->take(3)->get();
		  
		  if (Auth::check()) 
		  {
		  $checkif_purchased = Items::ifpurchaseCount($token);
		  }
		  else
		  {
			$checkif_purchased = 0;
		  }
		  
		  $getreview  = Items::getreviewCount($item_id);
		  if($getreview !=0)
		  {
			  $review['view'] = Items::getreviewView($item_id);
			  $top = 0;
			  $bottom = 0;
			  foreach($review['view'] as $review)
			  {
				 if($review->rating == 1) { $value1 = $review->rating*1; } else { $value1 = 0; }
				 if($review->rating == 2) { $value2 = $review->rating*2; } else { $value2 = 0; }
				 if($review->rating == 3) { $value3 = $review->rating*3; } else { $value3 = 0; }
				 if($review->rating == 4) { $value4 = $review->rating*4; } else { $value4 = 0; }
				 if($review->rating == 5) { $value5 = $review->rating*5; } else { $value5 = 0; }
				 
				 $top += $value1 + $value2 + $value3 + $value4 + $value5;
				 $bottom += $review->rating;
				 
			  }
			  if(!empty(round($top/$bottom)))
			  {
				$count_rating = round($top/$bottom);
			  }
			  else
			  {
				$count_rating = 0;
			  }
			  
			  
			  
		  }
		  else
		  {
			$count_rating = 0;
		  }
		  
		  $getreviewdata['view']  = Items::getreviewItems($item_id);
		  
			  
		  $comment['view'] = Comment::with('ReplyComment')->leftjoin('users', 'users.id', '=', 'item_comments.comm_user_id')->where('item_comments.comm_item_id','=',$item_id)->orderBy('comm_id', 'asc')->get();
		  /*$comment['view'] = Comment::with('ReplyComment')->leftjoin('users', 'users.id', '=', 'item_comments.comm_user_id')->leftJoin('item_order','item_order.item_id','item_comments.comm_item_id')->where('item_comments.comm_item_id','=',$item_id)->orderBy('comm_id', 'asc')->get();*/
		  
		  $comment_count = $comment['view']->count();
		  
		   
		   $viewattribute['details'] = Attribute::getattributeViews($token);
		   $setting['setting'] = Settings::editGeneral($sid);
		  $page_slug = $setting['setting']->item_support_link;
		  $page['view'] = Pages::editpageData($page_slug);
		  
		  $related['items'] = Items::with('ratings')->leftjoin('users', 'users.id', '=', 'items.user_id')->where('items.item_status','=',1)->where('items.drop_status','=','no')->where('items.user_id','=',$item_user_id)->where('items.item_id','!=',$item_id)->orderBy('items.item_id', 'desc')->inRandomOrder()->take(4)->get();
		  if($this->custom_settings('author_key') == Helper::Key_Owner())
		  {
		  $other_market_link = Blog::selectedmarketData($item['item']->item_token);
		  }
		  else
		  {
		  $other_market_link = 0;
		  }
		  $data = array('item' => $item, 'getcount' => $getcount, 'item_image' => $item_image, 'item_allimage' => $item_allimage, 'category_name' => $category_name, 'item_tags' => $item_tags, 'itemData' => $itemData, 'checkif_purchased' => $checkif_purchased, 'getreview' => $getreview, 'count_rating' => $count_rating, 'getreviewdata' => $getreviewdata, 'comment' => $comment, 'comment_count' => $comment_count, 'badges' => $badges, 'country' => $country, 'trends' => $trends, 'year' => $year, 'sold_amount' => $sold_amount, 'collect_amount' => $collect_amount, 'referral_count' => $referral_count, 'viewattribute' => $viewattribute, 'item_slug' => $item_slug, 'page' => $page, 'related' => $related, 'check_if_item' => $check_if_item, 'other_market_link' => $other_market_link);
		 }
		 else
		 {
		    $data = array('check_if_item' => $check_if_item);
		 } 
	     return view('item')->with($data);
		 }
		 else
		 {
		 return redirect('/404');
		 }
	}
	
	
	/* item */
	
	
	/* contact */
	
	public function update_contact(Request $request)
	{
	
	  $from_name = $request->input('from_name');
	  $from_email = $request->input('from_email');
	  $from_phone = $request->input('from_phone');
	  $subject = $request->input('subject');
	  $message_text = $request->input('message_text');
	  $sid = 1;
	  $setting['setting'] = Settings::editGeneral($sid);
	  $admin_name = $setting['setting']->sender_name;
	  $admin_email = $setting['setting']->sender_email;
	  $additional['settings'] = Settings::editAdditional();
	  $record = array('from_name' => $from_name, 'from_email' => $from_email, 'from_phone' => $from_phone, 'subject' => $subject, 'message_text' => $message_text, 'contact_date' => date('Y-m-d'));
	  $contact_count = Items::getcontactCount($from_email);
	  if($contact_count == 0)
	  {
	     if($additional['settings']->site_google_recaptcha == 1)
		 {
			 $request->validate([
								'from_name' => 'required',
								'from_email' => 'required|email',
								'message_text' => 'required',
								'g-recaptcha-response' => 'required|recaptchav3:register,0.5'
								
								
			 ]);
		 }
		 else
		 {
		    $request->validate([
								'from_name' => 'required',
								'from_email' => 'required|email',
								'message_text' => 'required',
								
								
								
			 ]);
		 }
		 $rules = array(
				
				
	     );
		 
		 $messsages = array(
		      
	    );
		 
		$validator = Validator::make($request->all(), $rules,$messsages);
		
		if ($validator->fails()) 
		{
		 $failedRules = $validator->failed();
		 return back()->withErrors($validator);
		} 
		else
		{
	  
	  
			  Items::saveContact($record);
			  /* email template code */
	          $checktemp = EmailTemplate::checkTemplate(3);
			  if($checktemp != 0)
			  {
			  $template_view['mind'] = EmailTemplate::viewTemplate(3);
			  $template_subject = $template_view['mind']->et_subject;
			  }
			  else
			  {
			  $template_subject = "Contact Us";
			  }
			  /* email template code */
			 // Mail::send('contact_mail', $record, function($message) use ($admin_name, $admin_email, $from_email, $from_name, $template_subject) {
				// 		$message->to($admin_email, $admin_name)
				// 				->subject($template_subject);
				// 		$message->from($from_email,$from_name);
				// 	});
			  return redirect()->back()->with('success','Your message has been sent successfully');
			  
		}	  
			  
	  }
	  else
	  {
	  return redirect()->back()->with('error','Sorry! Your message already sent');
	  }
	  
	  
	
	}
	
	/* contact */
	
	
	/* newsletter */
	
	public function generateRandomString($length = 25) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
	return $randomString;
    }
	
	
	public function activate_newsletter($token)
	{
	   
	   $check = Members::checkNewsletter($token);
	   if($check == 1)
	   {
	      
		  $data = array('news_status' => 1);
		
		  Members::updateNewsletter($token,$data);
		  
		  return redirect('/newsletter')->with('success', 'Thank You! Your subscription has been confirmed!');
		  
	   }
	   else
	   {
	       return redirect('/newsletter')->with('error', 'This email address already subscribed');
	   }
	
	}
	
	
	public function view_newsletter()
	{
	 
	  return view('newsletter');
	
	}
	
	
	public function update_newsletter(Request $request)
	{
	
	   $news_email = $request->input('news_email');
	   $news_status = 0;
	   $news_token = $this->generateRandomString();
	   
	   $request->validate([
							
							'news_email' => 'required|email',
							
							
							
         ]);
		 $rules = array(
		 
		      'news_email' => ['required',  Rule::unique('newsletter') -> where(function($sql){ $sql->where('news_status','=',0);})],
								
	     );
		 
		 $messsages = array(
		      
	    );
		 
		$validator = Validator::make($request->all(), $rules,$messsages);
		
		if ($validator->fails()) 
		{
		 $failedRules = $validator->failed();
		 /*return back()->withErrors($validator);*/
		 return redirect()->back()->with('error', 'This email address already subscribed.');
		} 
		else
		{
		
		
		$data = array('news_email' => $news_email, 'news_token' => $news_token, 'news_status' => $news_status);
		
		Members::savenewsletterData($data);
		
		$sid = 1;
		$setting['setting'] = Settings::editGeneral($sid);
		
		$from_name = $setting['setting']->sender_name;
        $from_email = $setting['setting']->sender_email;
		$activate_url = URL::to('/newsletter').'/'.$news_token;
		
		$record = array('activate_url' => $activate_url);
		/* email template code */
	          $checktemp = EmailTemplate::checkTemplate(6);
			  if($checktemp != 0)
			  {
			  $template_view['mind'] = EmailTemplate::viewTemplate(6);
			  $template_subject = $template_view['mind']->et_subject;
			  }
			  else
			  {
			  $template_subject = "Newsletter Signup";
			  }
			  /* email template code */
		Mail::send('newsletter_mail', $record, function($message) use ($from_name, $from_email, $news_email, $template_subject) {
			$message->to($news_email)
					->subject($template_subject);
			$message->from($from_email,$from_name);
		});
		
			   
		return redirect()->back()->with('success', 'Your email address subscribed. You will receive a confirmation email.');
		
		}
	   
	
	}
	
	
	
	/* newsletter */
	public function view_verify()
	{
	   $checkverify = 0;
	   $data = array('checkverify' => $checkverify);
	   return view('verify')->with($data);
	}
	
	public function update_verify(Request $request)
	{
	   $purchase_code = $request->input('purchase_code');
	   
	   $checkverify = Items::checkVerify($purchase_code);
       
	   if($checkverify != 0)
	   {
			
		 $sold = Items::possibleVerify($purchase_code);
         $data = array('sold' => $sold, 'checkverify' => $checkverify);
		 return view('verify')->with($data);
             
			  
       }
	   else
	   {
              
			  return redirect()->back()->with('error', 'Sorry, This is not a valid purchase code or this user have not purchased any of items.');
       }
	   
	  
	}
	
}
