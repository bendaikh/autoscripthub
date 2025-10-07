<?php

namespace Fickrr\Models;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Fickrr\Models\Items;

class Members extends Model
{

     
	  public static function topAuthor()
	  {
		
		$value=Items::with('ratings')
		       ->select('users.id','users.username','users.name','users.created_at','users.referral_count','users.user_photo','users.user_document_verified','users.country_badge','users.exclusive_author','country.country_id','country.country_name','country.country_badges')
			   ->join('users', 'users.id', '=', 'items.user_id')
			   
			   ->join('country','country.country_id','users.country')
			   ->where('users.drop_status','=','no')
			   ->where('users.id','!=',1)
			   ->orderByRaw('users.user_document_verified DESC')
			   ->groupBy('users.id')
			   ->get(); 
		return $value;
		
	  }
	 
	  public static function getuserSubscription($user_id)
	  {
	  $today = date('Y-m-d');
	  $get=DB::table('users')->leftJoin('subscription','subscription.subscr_id','users.user_subscr_id')->where('subscription.subscr_status','=',1)->where('subscription.subscr_drop_status','=','no')->where('users.id','=',$user_id)->where('users.user_subscr_date','>',$today)->where('subscription.subscr_email_support','=',1)->get();
	  $value = $get->count(); 
	  return $value;
	  
	  }
	  
	  
	  public static function getdirectSubscription($user_id)
	  {
	  $today = date('Y-m-d');
	  $get=DB::table('users')->leftJoin('subscription','subscription.subscr_id','users.user_subscr_id')->where('subscription.subscr_drop_status','=','no')->where('users.id','=',$user_id)->where('subscription.subscr_payment_mode','=',1)->get();
	  $value = $get->count(); 
	  return $value;
	  
	  }
	 
	 /* administrator */
		public static function getadminData()
	  {
	    $sid = 1;
		$setting['setting'] = Settings::editGeneral($sid);
		$site_item_per_page = $setting['setting']->site_item_per_page;
		$value=DB::table('users')->where('user_type','=','admin')->where('id','!=',1)->where('drop_status','=','no')->orderBy('id', 'desc')->paginate($site_item_per_page); 
		return $value;
		
	  }
	  
	  public static function searchadministrators($search)
	  {
		$sid = 1;
		$setting['setting'] = Settings::editGeneral($sid);
		$site_item_per_page = $setting['setting']->site_item_per_page;
		$value=DB::table('users')
			   ->where('user_type','=','admin')
			   ->where('id','!=',1)
			   ->where('drop_status','=','no')
			   ->where(function ($query) use ($search) { 
			   $query->where('name', 'LIKE', "%$search%");
			   $query->orWhere('email', 'LIKE', "%$search%");
			   })->orderBy('id', 'desc')
			   ->paginate($site_item_per_page); 
		return $value;
		
	  }	
	  
	  public static function logindataUser($log_id)
	  {
            $value = DB::table('users')->where('id', $log_id)->first();
	        return $value;
      }
	  
	/* administrator */

     /* newsletter */
	 
	 public static function savenewsletterData($data)
	  {
	   
		  DB::table('newsletter')->insert($data);
		 
	 
	  }
	  
	  
	  public static function checkNewsletter($token)
	  {
	  $get=DB::table('newsletter')->where('news_token','=',$token)->where('news_status','=',0)->get();
	  $value = $get->count();  
		return $value;
	  
	  }
	  
	  
	  public static function updateNewsletter($token,$data){
		DB::table('newsletter')
		  ->where('news_token', $token)
		  ->update($data);
	  }
  
  
  /* newsletter */
  
  
  
  
  /* referral */
   public static function referralUser($referral_by)
  {

    $value=DB::table('users')->where('id', $referral_by)->where('verified', 1)->first(); 
    return $value;
	
  }
  
  public static function referralCheck($referral_by)
  {

    $get=DB::table('users')->where('id', $referral_by)->where('verified', 1)->get(); 
    $value = $get->count(); 
    return $value;
	
  }
  
  public static function countryCheck($country_id)
  {

    $get=DB::table('country')->where('country_id', $country_id)->get(); 
    $value = $get->count(); 
    return $value;
	
  }
  
  public static function countryDATA($country_id)
  {

    $value=DB::table('country')->where('country_id', $country_id)->first(); 
    return $value;
	
  }
  
  public static function checkdownloadDate($user_id,$today_date)
  {

    $value=DB::table('users')->where('id', $user_id)->where('user_today_download_date', $today_date)->first(); 
    return $value;
	
  }
  
  
  public static function updateReferral($referral_by,$update_data){
    DB::table('users')
      ->where('id', $referral_by)
      ->update($update_data);
  }
  
  /* referral */
  
  
    
	/* customer */
	
	public static function insertData($data){
   
      DB::table('users')->insert($data);
     
 
    }

  public static function updateData($token,$data){
    DB::table('users')
      ->where('user_token', $token)
      ->update($data);
  }
  
  public static function editData($token){
    $value = DB::table('users')
      ->where('user_token', $token)
      ->first();
	return $value;
  }
  
  
  public static function getuserData()
  {
    $sid = 1;
	$setting['setting'] = Settings::editGeneral($sid);
	$site_item_per_page = $setting['setting']->site_item_per_page;
    $value=DB::table('users')->where('user_type','=','customer')->where('drop_status','=','no')->orderBy('id', 'desc')->paginate($site_item_per_page); 
    return $value;
	
  }
  
  public static function searchcustomers($search)
  {
    $sid = 1;
	$setting['setting'] = Settings::editGeneral($sid);
	$site_item_per_page = $setting['setting']->site_item_per_page;
    $value=DB::table('users')
	       ->where('user_type','=','customer')
		   ->where('drop_status','=','no')
	       ->where(function ($query) use ($search) { 
		   $query->where('name', 'LIKE', "%$search%");
		   $query->orWhere('email', 'LIKE', "%$search%");
		   })->orderBy('id', 'desc')
		   ->paginate($site_item_per_page); 
    return $value;
	
  }	
  
  
  public static function deleteData($token,$data){
    
	$image = DB::table('users')->where('user_token', $token)->first();
        $file= $image->user_photo;
        $filename = public_path().'/storage/users/'.$file;
        File::delete($filename);
	
	DB::table('users')
      ->where('user_token', $token)
      ->update($data);
	
  }
  
  public static function droPhoto($token)
  {
     $image = DB::table('users')->where('user_token', $token)->first();
        $file= $image->user_photo;
        $filename = public_path().'/storage/users/'.$file;
        File::delete($filename);
  }
  
  
  public static function droBanner($token)
  {
     $image = DB::table('users')->where('user_token', $token)->first();
        $file= $image->user_banner;
        $filename = public_path().'/storage/users/'.$file;
        File::delete($filename);
  }
  
  /* customer */
  
  
  /* vendor */
  
  public static function allvendorData()
  {
	
		$value=DB::table('users')->where('user_type','=','vendor')->where('drop_status','=','no')->orderBy('id', 'desc')->get(); 
		return $value;
		
  }
  
  
  public static function getvendorData()
  {
    $sid = 1;
	$setting['setting'] = Settings::editGeneral($sid);
	$site_item_per_page = $setting['setting']->site_item_per_page;
    $value=DB::table('users')->where('user_type','=','vendor')->where('drop_status','=','no')->orderBy('id', 'desc')->paginate($site_item_per_page);
    return $value;
	
  }
  
  public static function searchvendors($search)
  {
    $sid = 1;
	$setting['setting'] = Settings::editGeneral($sid);
	$site_item_per_page = $setting['setting']->site_item_per_page;
    $value=DB::table('users')
	       ->where('user_type','=','vendor')
		   ->where('drop_status','=','no')
	       ->where(function ($query) use ($search) { 
		   $query->where('name', 'LIKE', "%$search%");
		   $query->orWhere('email', 'LIKE', "%$search%");
		   })->orderBy('id', 'desc')
		   ->paginate($site_item_per_page); 
    return $value;
	
  }	
  
    /* vendor */
	
	
	
	/* edit profile */
	
	
  
  
  public static function editprofileData($token){
    $value = DB::table('users')
      ->where('id', $token)
      ->first();
	return $value;
  }
  
  
  public static function editUser($slug){
    $value = DB::table('users')
      ->where('username', $slug)
      ->first();
	return $value;
  }
  
  public static function getInuser($slug){
    $value = DB::table('users')
      ->where('username', $slug)
	  ->where('drop_status', 'no')
      ->first();
	return $value;
  }
  
  public static function getInuserCount($slug){
    $get = DB::table('users')
      ->where('username', $slug)
	  ->where('drop_status', 'no')
      ->get();
	$value = $get->count(); 
    return $value;
  }
  
  public static function adminData(){
    $value = DB::table('users')
      ->where('id', 1)
      ->first();
	return $value;
  }
  
  
  public static function updateprofileData($token,$data){
    DB::table('users')
      ->where('id', $token)
      ->update($data);
  }
  
  
  public static function droprofilePhoto($token)
  {
     $image = DB::table('users')->where('id', $token)->first();
        $file= $image->user_photo;
        $filename = public_path().'/storage/users/'.$file;
        File::delete($filename);
  }
	
	/* edit profile */
	
	
	/* verify user */
	
	public static function verifyuserData($user_token,$data){
    DB::table('users')
      ->where('user_token', $user_token)
      ->update($data);
  }
  
  
  public static function refCount($token){
    $get = DB::table('users')
      ->where('user_token', $token)
	  ->where('drop_status', 'no')
	  ->where('referral_by', '!=', 0)
	  ->where('referral_payout', 'pending')
      ->get();
	$value = $get->count(); 
    return $value;
  }
  
  /* verify user */
  
  
  /* verify user available or not */
  
  
  public static function verifycheckData($data){
    $value=DB::table('users')->where('email', $data['email'])->where('drop_status', 'no')->get();
    if($value->count() != 0){
      return 1;
     }else{
       return 0;
     }
	
  }
  
  
  public static function getemailData($email){
    $value = DB::table('users')
      ->where('email', $email)
	  ->where('drop_status', 'no')
      ->first();
	return $value;
  }
  
  
  
  public static function verifytokenData($data){
    $value=DB::table('users')->where('user_token', $data['user_token'])->where('drop_status', 'no')->get();
    if($value->count() != 0){
      return 1;
     }else{
       return 0;
     }
	
  }
  
  
  
  public static function gettokenData($user_token){
    $value = DB::table('users')
      ->where('user_token', $user_token)
	  ->where('drop_status', 'no')
      ->first();
	return $value;
  }
  
  
   public static function updatepasswordData($user_token, $record){
    DB::table('users')
      ->where('user_token', $user_token)
      ->update($record);
  }
  
  
  public static function updateadminData($admin_token, $admin_record){
    DB::table('users')
      ->where('user_token', $admin_token)
      ->update($admin_record);
  }
  
  /* verify user available or not */
  
  
  /* single user get */
  
  public static function singlevendorData($item_user_id){
    $value = DB::table('users')
      ->where('id', $item_user_id)
      ->first();
	return $value;
  }
  
  
  public static function singlebuyerData($user_id){
    $value = DB::table('users')
      ->where('id', $user_id)
      ->first();
	return $value;
  }
  
  
  
  public static function updatevendorRecord($vendor_token, $record_vendor){
    DB::table('users')
      ->where('user_token', $vendor_token)
      ->update($record_vendor);
  }
  
  /* single user get */
  
  
  /* total members */
  
  public static function getmemberData()
  {

    $get=DB::table('users')->where('user_type','=','vendor')->where('drop_status','=','no')->orderBy('id', 'desc')->get();
	$value = $get->count(); 
    return $value;
	
  }
  
  public static function footermemberData()
  {

    $get=DB::table('users')->where('id','!=',1)->where('drop_status','=','no')->orderBy('id', 'desc')->get();
	$value = $get->count(); 
    return $value;
	
  }
  
  
  public static function logData($token)
  {

    $get=DB::table('users')->where('user_token','=',$token)->where('drop_status','=','no')->where('verified','=',1)->get();
	$value = $get->count(); 
    return $value;
	
  }
  
  /* total members */
	
	
	public static function getcontactCount($from_email)
  {
    
    $get=DB::table('contact')->where('from_email','=',$from_email)->get(); 
	$value = $get->count();
    return $value;
	
  }	
  
  public static function saveContact($record)
  {
   
      DB::table('contact')->insert($record);
     
 
  }
  
  
  /* message */
  
  /*public static function firstcheckCount($user_id)
  {
    
    $get=DB::table('item_checkout')
	     ->where('item_user_id','=',$user_id)
		 ->orWhere('user_id','=',$user_id)
		 ->get(); 
	$value = $get->count();
    return $value;
	
  }	*/
  
  public static function otherUserCount($user_id)
  {
    
    $get=DB::table('users')
	     ->join('item_checkout', function ($join) {
            $join->on('item_checkout.item_user_id', '=', 'users.id')->orOn('item_checkout.user_id', '=', 'users.id');
          })
		 ->where('users.user_type','!=','admin')
		 ->where('users.id','!=',$user_id)
		 ->where('users.user_message_permission','=',1)
		 ->where('users.drop_status','=','no')
		 ->where('item_checkout.item_user_id','=',$user_id)
		 ->orWhere('item_checkout.user_id','=',$user_id)
		 ->orderBy('item_checkout.chout_id', 'desc')
		 ->groupBy('users.id')
		 ->get(); 
	$value = $get->count();
    return $value;
	
  }	
  
  public static function otherUserData($user_id)
  {
	
		$value=DB::table('users')
		       ->join('item_checkout', function ($join) {
               $join->on('item_checkout.item_user_id', '=', 'users.id')->orOn('item_checkout.user_id', '=', 'users.id');
               })
		       ->where('users.user_type','!=','admin')
			   ->where('users.id','!=',$user_id)
			   ->where('users.user_message_permission','=',1)
			   ->where('users.drop_status','=','no')
			   ->where('item_checkout.item_user_id','=',$user_id)
		       ->orWhere('item_checkout.user_id','=',$user_id)
			   ->orderBy('item_checkout.chout_id', 'desc')
			   ->groupBy('users.id')
			   ->get(); 
		return $value;
		
  }
  
  public static function otherUserSingle($user_id)
  {
	
		$value=DB::table('users')
		       ->join('item_checkout', function ($join) {
               $join->on('item_checkout.item_user_id', '=', 'users.id')->orOn('item_checkout.user_id', '=', 'users.id');
               })
			   ->where('users.user_type','!=','admin')
			   ->where('users.id','!=',$user_id)
			   ->where('users.user_message_permission','=',1)
			   ->where('users.drop_status','=','no')
			   ->where('item_checkout.item_user_id','=',$user_id)
		       ->orWhere('item_checkout.user_id','=',$user_id)
			   ->orderBy('item_checkout.chout_id', 'desc')
			   ->groupBy('users.id')
			   ->first(); 
		return $value;
		
  }
  
  
  public static function totalreferralEarnings()
  {
	
		$value=DB::table('users')->where('drop_status','=','no')->orderBy('id', 'desc')->get(); 
		return $value;
		
  }
  
  public static function totaladminreferralEarnings()
  {
	
		$value=DB::table('users')->where('id','=',1)->where('drop_status','=','no')->first(); 
		return $value;
		
  }
  
  
  public static function totalpayout()
  {
	
		$value=DB::table('item_withdrawal')->where('wd_status','=','paid')->get(); 
		return $value;
		
  }
  
  public static function totalrefund()
  {
	
		$get=DB::table('item_refund')->orderBy('refund_id', 'desc')->get();
		$value = $get->count();
        return $value;
		
  }
  
  /* visitors */
  public static function visitorCheck($ipaddress,$date)
  {

    $get=DB::table('visitors')->where('visit_user_ip', $ipaddress)->where('visit_date', $date)->get(); 
    $value = $get->count(); 
    return $value;
	
  }
  public static function singleVisitor($ipaddress,$date)
  {
	
		$value=DB::table('visitors')->where('visit_user_ip', $ipaddress)->where('visit_date', $date)->first(); 
		return $value;
		
  }
  public static function updateVisitor($ipaddress,$date, $data){
    DB::table('visitors')
      ->where('visit_user_ip', $ipaddress)
	  ->where('visit_date', $date)
      ->update($data);
  }
  public static function saveVisitor($data)
  {
   
      DB::table('visitors')->insert($data);
     
 
  }
  public static function Visitors()
  {
	
		$get=DB::table('visitors')->get();
		$value = $get->count();
        return $value;
		
  }
  public static function SelectedVisitors($from,$to)
  {
	
		$get=DB::table('visitors')
		    ->where('visit_date', '>=', $from)
            ->where('visit_date', '<=', $to)
			->get();
		$value = $get->count();
        return $value;
		
  }
  public static function SelectedMembers($from,$to)
  {

    $get=DB::table('users')
	     ->where('user_type','=','vendor')
		 ->where('drop_status','=','no')
		 ->where('created_at', '>=', $from)
         ->where('created_at', '<=', $to)
		 ->get();
	$value = $get->count(); 
    return $value;
	
  }
  public static function totalnewVendors()
  {
    $from = date("Y-m-d", strtotime ( '-3 month' ));
	$to = date("Y-m-d");
    $get=DB::table('users')
	     ->where('user_type','=','vendor')
		 ->where('drop_status','=','no')
		 ->where('created_at', '>=', $from)
		 ->where('created_at', '<=', $to)
		 ->get();
	$value = $get->count(); 
    return $value;
	
  }
  public static function Selectedtotalnewvendors($from,$to)
  {
    $get=DB::table('users')
	     ->where('user_type','=','vendor')
		 ->where('drop_status','=','no')
		 ->where('created_at', '>=', $from)
		 ->where('created_at', '<=', $to)
		 ->get();
	$value = $get->count(); 
    return $value;
	
  }
  /* visitors */
  
  
  
  
}
