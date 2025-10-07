<?php

namespace Fickrr\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Fickrr\Http\Controllers\Controller;
use Session;
use Fickrr\Models\Subscription;
use Fickrr\Models\Settings;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Helper;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
		
    }
	
	public function custom()
	{
	    $dw_v = Helper::version_no();
		$custom = Settings::customSettings();
		return $custom->$dw_v;
	} 
	
	public function seo_slug($string)
	{
	    
		$spaceRepl = "-";
		$string = str_replace("&", "and", $string);
		$string = preg_replace("/[^a-zA-Z0-9 _-]/", "", $string);
		$string = preg_replace("/".$spaceRepl."+/", "", $string);
		$string = strtolower($string);
		$string = preg_replace("/[ ]+/", " ", $string);
		$string = str_replace(" ", $spaceRepl, $string);
		return $string;	
    
	}
	
	public function non_seo_slug($string)
	{
	    $spaceRepl = "-";
		$string = preg_replace("/[ ]+/", " ", $string);
        $string = str_replace(" ", $spaceRepl, $string);
        return $string;
    
	}
	
	
	public function subscription()
    {
	    $sid = 1;
		$setting['setting'] = Settings::editGeneral($sid);
      	$subscription['view'] = Subscription::getsubscriData();
		$additional = Settings::editAdditional();
		if($this->custom() != 0)
	    {
		return view('admin.subscription',[ 'subscription' => $subscription, 'setting' => $setting, 'sid' => $sid, 'additional' => $additional]);
		}
		else
		{
		  return redirect('/admin/license');
		}
    }
    
	
	public function add_subscription()
	{
	   $durations = array('1 Month','2 Month','3 Month','4 Month','5 Month','6 Month','1 Year','2 Year','3 Year','4 Year','5 Year');
	   $item_sale_type = array('limited' => 'Limited Items','unlimited' => 'Unlimited Items');
	   $storage_space = array('limited' => 'Limited Space','unlimited' => 'Unlimited Space');
	   $storage_space_type = array('MB','GB','TB');
	   if($this->custom() != 0)
	   {
	   return view('admin.add-subscription',[ 'durations' => $durations, 'item_sale_type' => $item_sale_type, 'storage_space' => $storage_space, 'storage_space_type' => $storage_space_type]);
	   }
	   else
	   {
		  return redirect('/admin/license');
	   }
	}
	
	
	public function subscription_content(Request $request)
	{
	
	  $subscription_title = $request->input('subscription_title');
	  
	  
	  $subscription_desc = htmlentities($request->input('subscription_desc'));
	  
	  $request->validate([
		                    
							
							
							
							
							
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
		
		  $data = array('subscription_title' => $subscription_title, 'subscription_desc' => $subscription_desc);
 
			Settings::updateAdditionData($data);
            return redirect()->back()->with('success', 'Update successfully.');
		
		
	    }
	  
	
	}
	
	public function save_free_subscription(Request $request)
	{
	
	  $subscr_duration = $request->input('subscr_duration');
	  $subscr_items = $request->input('subscr_items');
	  $subscr_spaces = $request->input('subscr_spaces');
	  $user_subscr_type = $request->input('user_subscr_type');
	  $user_subscr_price = $request->input('user_subscr_price');
	  $sid = $request->input('sid');
	  $free_subscription = $request->input('free_subscription');
	  $subscr_download_items = $request->input('subscr_download_items');
	  $request->validate([
		                    
							
							
							'subscr_duration' => 'required',
							'subscr_items' => 'required',
							'subscr_spaces' => 'required',
							
							
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
		
		  $data = array('free_subscr_duration' => $subscr_duration, 'free_subscr_item' => $subscr_items, 'free_subscr_space' => $subscr_spaces, 'free_subscr_type' => $user_subscr_type, 'free_subscr_price' => $user_subscr_price, 'free_subscription' => $free_subscription, 'subscr_download_items' => $subscr_download_items);
 
			Settings::updateAdditionData($data);
            return redirect()->back()->with('success', 'Update successfully.');
		
		
	    }
	  
	
	}
	
	public function save_subscription(Request $request)
	{
 
         
		 
		 $subscr_name = $request->input('subscr_name');
		 $additional['settings'] = Settings::editAdditional();
		 if($additional['settings']->site_url_rewrite == 1)
		 {
		   $subscr_slug = $this->seo_slug($subscr_name);
		 }
		 else
		 {
		   $subscr_slug = $this->non_seo_slug($subscr_name);
		 }
		 $subscr_price = $request->input('subscr_price');
		 $subscr_duration = $request->input('subscr_duration');
		 $subscr_item_level = $request->input('subscr_item_level');
		 if(!empty($request->input('subscr_item')))
		 {
		 $subscr_item = $request->input('subscr_item');
		 }
		 else
		 {
		 $subscr_item = 0;
		 }
		 $subscr_space_level = $request->input('subscr_space_level');
		 if(!empty($request->input('subscr_space')))
		 {
		 $subscr_space = $request->input('subscr_space');
		 }
		 else
		 {
		 $subscr_space = 0;
		 }
		 if(!empty($request->input('subscr_space_type')))
		 {
		 $subscr_space_type = $request->input('subscr_space_type');
		 }
		 else
		 {
		 $subscr_space_type = "";
		 }
		 if(!empty($request->input('subscr_order')))
		 {
		 $subscr_order = $request->input('subscr_order');
		 }
		 else
		 {
		 $subscr_order = 0;
		 }
		 $subscr_email_support = $request->input('subscr_email_support');
		 $subscr_payment_mode = $request->input('subscr_payment_mode');
		 $subscr_status = $request->input('subscr_status');
		 $subscr_download_item = $request->input('subscr_download_item');
		 
		 $highlight_pack = $request->input('highlight_pack');
		 $highlight_bg_color = $request->input('highlight_bg_color');
		 $highlight_text_color = $request->input('highlight_text_color');
		 $icon_color = $request->input('icon_color');
		 $button_bg_color = $request->input('button_bg_color');
		 $button_text_color = $request->input('button_text_color');
		 
		 $extra_info = $request->input('extra_info');
         
		 $request->validate([
		                    
							
							'subscr_price' => 'required',
							'subscr_duration' => 'required',
							'subscr_status' => 'required',
							'subscr_item_level' => 'required',
							'subscr_space_level' => 'required',
							
							
         ]);
		 $rules = array(
				'subscr_name' => ['required', 'max:255', Rule::unique('subscription') -> where(function($sql){ $sql->where('subscr_drop_status','=','no');})],
				'subscr_price' => ['required',  Rule::unique('subscription') -> where(function($sql){ $sql->where('subscr_drop_status','=','no');})],
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
		
		   
		 
		$data = array('subscr_name' => $subscr_name, 'subscr_slug' => $subscr_slug, 'subscr_price' => $subscr_price, 'subscr_duration' => $subscr_duration, 'subscr_item_level' => $subscr_item_level, 'subscr_item' => $subscr_item, 'subscr_space_level' => $subscr_space_level, 'subscr_space' => $subscr_space, 'subscr_space_type' => $subscr_space_type, 'subscr_order' => $subscr_order, 'subscr_email_support' => $subscr_email_support, 'subscr_payment_mode' => $subscr_payment_mode, 'subscr_status' => $subscr_status, 'subscr_download_item' => $subscr_download_item, 'highlight_pack' => $highlight_pack, 'highlight_bg_color' => $highlight_bg_color, 'highlight_text_color' => $highlight_text_color, 'icon_color' => $icon_color, 'button_bg_color' => $button_bg_color, 'button_text_color' => $button_text_color, 'extra_info' => $extra_info);
        Subscription::insertsubData($data);
        return redirect('/admin/subscription')->with('success', 'Insert successfully.');
            
 
       } 
     
    
  }
  
  public function all_delete_subscription(Request $request)
	{
	   $data = array('subscr_drop_status' => 'yes');
	   $subscr_id = $request->input('subscr_id');
	   foreach($subscr_id as $id)
	   {
	      Subscription::deleteSubscrdata($id,$data);
	   }
	   return redirect()->back()->with('success','Delete successfully.');
	
	}
  
  
  public function delete_subscription($subscr_id){

      
	  $data = array('subscr_drop_status' => 'yes');
      Subscription::deleteSubscrdata($subscr_id,$data);
	  
	  return redirect()->back()->with('success', 'Delete successfully.');

    
  }
  
  
  public function edit_subscription($subscr_id)
	{
	   $durations = array('1 Month','2 Month','3 Month','4 Month','5 Month','6 Month','1 Year','2 Year','3 Year','4 Year','5 Year');
	   $item_sale_type = array('limited' => 'Limited Items','unlimited' => 'Unlimited Items');
	   $edit['subscri'] = Subscription::editsubData($subscr_id);
	   $storage_space = array('limited' => 'Limited Space','unlimited' => 'Unlimited Space');
	   $storage_space_type = array('MB','GB','TB');
	   if($this->custom() != 0)
	   {
	   return view('admin.edit-subscription', [ 'edit' => $edit, 'subscr_id' => $subscr_id, 'durations' => $durations, 'item_sale_type' => $item_sale_type, 'storage_space' => $storage_space, 'storage_space_type' => $storage_space_type]);
	   }
	   else
	   {
		  return redirect('/admin/license');
	   }
	}
	
	
	
	public function update_subscription(Request $request)
	{
	   $subscr_id = $request->input('subscr_id');
	   $subscr_name = $request->input('subscr_name');
	   $additional['settings'] = Settings::editAdditional();
		 if($additional['settings']->site_url_rewrite == 1)
		 {
		   $subscr_slug = $this->seo_slug($subscr_name);
		 }
		 else
		 {
		   $subscr_slug = $this->non_seo_slug($subscr_name);
		 }
		 $subscr_price = $request->input('subscr_price');
		 $subscr_duration = $request->input('subscr_duration');
		 $subscr_item_level = $request->input('subscr_item_level');
		 if(!empty($request->input('subscr_item')))
		 {
		 $subscr_item = $request->input('subscr_item');
		 }
		 else
		 {
		 $subscr_item = 0;
		 }
		 $subscr_space_level = $request->input('subscr_space_level');
		 if(!empty($request->input('subscr_space')))
		 {
		 $subscr_space = $request->input('subscr_space');
		 }
		 else
		 {
		 $subscr_space = 0;
		 }
		 if(!empty($request->input('subscr_space_type')))
		 {
		 $subscr_space_type = $request->input('subscr_space_type');
		 }
		 else
		 {
		 $subscr_space_type = "";
		 }
		 if(!empty($request->input('subscr_order')))
		 {
		 $subscr_order = $request->input('subscr_order');
		 }
		 else
		 {
		 $subscr_order = 0;
		 }
		 $subscr_email_support = $request->input('subscr_email_support');
		 $subscr_payment_mode = $request->input('subscr_payment_mode');
		 $subscr_status = $request->input('subscr_status');
		 $subscr_download_item = $request->input('subscr_download_item');
		 
         
		 $highlight_pack = $request->input('highlight_pack');
		 $highlight_bg_color = $request->input('highlight_bg_color');
		 $highlight_text_color = $request->input('highlight_text_color');
		 $icon_color = $request->input('icon_color');
		 $button_bg_color = $request->input('button_bg_color');
		 $button_text_color = $request->input('button_text_color');
		 $extra_info = $request->input('extra_info');
		 
		 $request->validate([
		                    
							
							'subscr_price' => 'required',
							'subscr_duration' => 'required',
							'subscr_status' => 'required',
							'subscr_item_level' => 'required',
							'subscr_space_level' => 'required',
							
							
         ]);
		 $rules = array(
		         'subscr_name' => ['required', 'max:255', Rule::unique('subscription') ->ignore($subscr_id, 'subscr_id') -> where(function($sql){ $sql->where('subscr_drop_status','=','no');})],
				 'subscr_price' => ['required',  Rule::unique('subscription') ->ignore($subscr_id, 'subscr_id') -> where(function($sql){ $sql->where('subscr_drop_status','=','no');})],
				
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
		
		   
		 
		
		
		$data = array('subscr_name' => $subscr_name, 'subscr_slug' => $subscr_slug, 'subscr_price' => $subscr_price, 'subscr_duration' => $subscr_duration, 'subscr_item_level' => $subscr_item_level, 'subscr_item' => $subscr_item, 'subscr_space_level' => $subscr_space_level, 'subscr_space' => $subscr_space, 'subscr_space_type' => $subscr_space_type, 'subscr_order' => $subscr_order, 'subscr_email_support' => $subscr_email_support, 'subscr_payment_mode' => $subscr_payment_mode, 'subscr_status' => $subscr_status, 'subscr_download_item' => $subscr_download_item, 'highlight_pack' => $highlight_pack, 'highlight_bg_color' => $highlight_bg_color, 'highlight_text_color' => $highlight_text_color, 'icon_color' => $icon_color, 'button_bg_color' => $button_bg_color, 'button_text_color' => $button_text_color, 'extra_info' => $extra_info);
		
        Subscription::updatesubData($subscr_id,$data);
		if($this->custom() != 0)
	    {
        return redirect('/admin/subscription')->with('success', 'Update successfully.');
		}
		else
		{
		  return redirect('/admin/license');
		}
            
 
       } 
      
      
     
       
	
	
	}
	
  
	
	
	
}
