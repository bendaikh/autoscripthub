<?php

namespace Fickrr\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Fickrr\Http\Controllers\Controller;
use Fickrr\Models\Settings;
use Fickrr\Models\Items;
use Fickrr\Models\Members;
use Fickrr\Models\Pages;
use Fickrr\Models\Blog;
use Helper;
use Illuminate\Support\Facades\Validator;
use Image;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
		
    }
    public function admin()
    {
        
		$sid = 1;
		$setting['setting'] = Settings::editGeneral($sid);
		
		$today = date('d F Y');
		$first_day = date('d F Y', strtotime('-1 days'));
		$second_day = date('d F Y', strtotime('-2 days'));
		$third_day = date('d F Y', strtotime('-3 days'));
		$fourth_day = date('d F Y', strtotime('-4 days'));
		$fifth_day = date('d F Y', strtotime('-5 days'));
		$sixth_day = date('d F Y', strtotime('-6 days'));
		
		$data1 = date('Y-m-d');
		$data2 = date('Y-m-d', strtotime('-1 days'));
		$data3 = date('Y-m-d', strtotime('-2 days'));
		$data4 = date('Y-m-d', strtotime('-3 days'));
		$data5 = date('Y-m-d', strtotime('-4 days'));
		$data6 = date('Y-m-d', strtotime('-5 days'));
		$data7 = date('Y-m-d', strtotime('-6 days'));
		
		$view1 = Items::orderdataCheck($data1);
		$view2 = Items::orderdataCheck($data2);
		$view3 = Items::orderdataCheck($data3);
		$view4 = Items::orderdataCheck($data4);
		$view5 = Items::orderdataCheck($data5);
		$view6 = Items::orderdataCheck($data6);
		$view7 = Items::orderdataCheck($data7);
		
		$approved = Items::itemapprovedCheck(1);
		$unapproved = Items::itemapprovedCheck(0);
		$rejected = Items::itemapprovedCheck(2);
		$totalvendor = Members::getmemberData();
		$totalpages = Pages::totaldemoData();
		$totalorder = Items::totalorderData();
		$totalitems = Items::totalitemCheck();
		$totalpost = Blog::totalblogData();
		$itemcomment = Items::totalitemcommentCheck();
		$total_referral['earnings'] = Members::totalreferralEarnings();
		$total_referral_earnings = 0;
		$total_referrals = 0;
		foreach($total_referral['earnings'] as $total_referral_earn)
		{
		  $total_referral_earnings += $total_referral_earn->referral_amount;
		  $total_referrals += $total_referral_earn->referral_count;
		}
		$admin_total_referral = Members::totaladminreferralEarnings();
		$payouts = 0;
		$total_withdrawal['earnings'] = Members::totalpayout();
		foreach($total_withdrawal['earnings'] as $total_withdrawal)
		{
		   $payouts += $total_withdrawal->wd_amount;
		}
		$refunds = Members::totalrefund();
		
		if($this->custom() != 0)
		{
		return view('admin.index', [ 'setting' => $setting, 'today' => $today, 'first_day' => $first_day, 'second_day' => $second_day, 'third_day' => $third_day, 'fourth_day' => $fourth_day, 'fifth_day' => $fifth_day, 'sixth_day' => $sixth_day, 'view1' => $view1, 'view2' => $view2, 'view3' => $view3, 'view4' => $view4, 'view5' => $view5, 'view6' => $view6, 'view7' => $view7, 'approved' => $approved, 'unapproved' => $unapproved, 'rejected' => $rejected, 'totalvendor' => $totalvendor, 'totalpages' => $totalpages, 'totalorder' => $totalorder, 'totalitems' => $totalitems, 'totalpost' => $totalpost, 'itemcomment' => $itemcomment, 'total_referral_earnings' => $total_referral_earnings, 'admin_total_referral' => $admin_total_referral, 'total_referrals' => $total_referrals, 'payouts' => $payouts, 'refunds' => $refunds]);
		}
		else
		{
		return redirect('/admin/license');
		}
		
		
    }
	
	public function license_check()
	{
	   return view('admin.license');
	}
	public function custom()
	{
	    $dw_v = Helper::version_no();
		$custom = Settings::customSettings();
		return $custom->$dw_v;
	} 
	public function verify_purchase(Request $request)
	{
	        $dw_ver = Helper::version_no();
	        $username = $request->input('username');
	        $purchased_code = $request->input('purchased_code');
	        $code= $purchased_code; 
			$encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
			$key = Settings::productcode();
			$keyname = $encrypter->decrypt($key);
			$url = "https://api.envato.com/v3/market/author/sale?code=".$code;
			$curl = curl_init($url);
			$personal_token = "sS3y8m5fMdYZMWVbSPtI7LdJYmtC9F2O";
			$header = array();
			$header[] = 'Authorization: Bearer '.$personal_token;
			$header[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:41.0) Gecko/20100101 Firefox/41.0';
			$header[] = 'timeout: 20';
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_HTTPHEADER,$header);
			$envatoRes = curl_exec($curl);
			curl_close($curl);
			$envatoRes = json_decode($envatoRes);
			if($envatoRes->license == base64_decode(Helper::key_no())){ $key_val = 1; } else { $key_val = 0; }
			if (isset($envatoRes->item->name))
			{
				if($keyname == $envatoRes->item->id && $envatoRes->buyer == $username)
				{   
					$data = array($dw_ver => 1, 'author_key' => $key_val);
					Settings::updateCustom($data);
					return redirect('admin');
				}
				else
				{
				  return redirect('admin/license')->with('error','Sorry, This is not a valid username & purchase code');
				}
			}
			else
		    {
				  return redirect('admin/license')->with('error','Sorry, This is not a valid username & purchase code');
			}	
	
	}
	public function view_market()
	{
	
	    $market_logo = Blog::getothermarketData();
		return view('admin.other-market',[ 'market_logo' => $market_logo]);
	
	}
	
	public function add_other_market()
	{
	   
	   
	   
		return view('admin.add-other-market');
	}
	
	public function save_other_market(Request $request)
	{
 
    
         $im_market_name = $request->input('im_market_name');
		 
		
         $im_logo_order = $request->input('im_logo_order');
		 
		$allsettings = Settings::allSettings();
		  $image_size = $allsettings->site_max_image_size;
		 
		 $additional = Settings::editAdditional();
		 
         
		 $request->validate([
							'im_market_name' => 'required',
							'im_logo' => 'required|mimes:jpeg,jpg,png|max:'.$image_size,
							
							
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
		
		$post_date = date('Y-m-d');
		
		if ($request->hasFile('im_logo')) {
		     
				   
			$image = $request->file('im_logo');
			$img_name = time() . '.'.$image->getClientOriginalExtension();
			$destinationPath = public_path('/storage/settings');
			$imagePath = $destinationPath. "/".  $img_name;
			$image->move($destinationPath, $img_name);
			$img=Image::make(public_path('/storage/settings/'.$img_name));
			$img->save(base_path('public/storage/settings/'.$img_name),$additional->image_quality);
			$im_logo = $img_name;
		  }
		  else
		  {
		     $im_logo = "";
		  }
		 
		$data = array('im_market_name' => $im_market_name, 'im_logo' => $im_logo, 'im_logo_order' => $im_logo_order);
        Blog::saveMarket($data);
		return redirect('/admin/other-market')->with('success', 'Insert successfully.');
        
            
 
       } 
     
    
  }
  
  
  public function delete_market($im_id){

      
	  
      Blog::deleteMarketdata($im_id);
	  
	  return redirect()->back()->with('success', 'Delete successfully.');

    
  }
  
	public function edit_other_market($im_id)
	{
	   
	   $edit_market = Blog::editmarketData($im_id);
	   
	   return view('admin.edit-other-market', [ 'edit_market' => $edit_market]);
	}
	
	
	public function update_other_market(Request $request)
	{
 
    
         $im_market_name = $request->input('im_market_name');
		 
		
         $im_logo_order = $request->input('im_logo_order');
		 
		 $im_id = $request->input('im_id');
		 
		$allsettings = Settings::allSettings();
		  $image_size = $allsettings->site_max_image_size;
		 
		 $additional = Settings::editAdditional();
		 
         
		 $request->validate([
							'im_market_name' => 'required',
							'im_logo' => 'mimes:jpeg,jpg,png|max:'.$image_size,
							
							
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
		
		$post_date = date('Y-m-d');
		
		if ($request->hasFile('im_logo')) {
		     
		    Blog::dropMarketimage($im_id);			   
			$image = $request->file('im_logo');
			$img_name = time() . '.'.$image->getClientOriginalExtension();
			$destinationPath = public_path('/storage/settings');
			$imagePath = $destinationPath. "/".  $img_name;
			$image->move($destinationPath, $img_name);
			$img=Image::make(public_path('/storage/settings/'.$img_name));
			$img->save(base_path('public/storage/settings/'.$img_name),$additional->image_quality);
			$im_logo = $img_name;
		  }
		  else
		  {
		     $im_logo = $request->input('save_im_logo');
		  }
		 
		$data = array('im_market_name' => $im_market_name, 'im_logo' => $im_logo, 'im_logo_order' => $im_logo_order);
        Blog::updateMarketData($im_id, $data);
		return redirect('/admin/other-market')->with('success', 'Update successfully.');
        
            
 
       } 
     
    
  }
	
}
