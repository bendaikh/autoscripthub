<?php

namespace Fickrr\Models;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Deposit extends Model
{
    
	
	
	
	/* deposit */
	
	public static function getdepositDetails()
  {

    $value=DB::table('deposit_details')->select('deposit_details.*','users.id','users.username')->join('users','users.id','deposit_details.user_id')->orderBy('deposit_details.dd_id', 'desc')->get(); 
    return $value;
	
  }
	
   public static function getdepositData()
  {

    $value=DB::table('deposit')->orderBy('dep_id', 'desc')->get(); 
    return $value;
	
  }
  
  public static function viewdepositData()
  {

    $value=DB::table('deposit')->where('deposit_status', '=', 1)->orderBy('deposit_price', 'asc')->get(); 
    return $value;
	
  }
      
  
  public static function insertDeposit($data){
   
      DB::table('deposit')->insert($data);
     
 
    }
	
	public static function saveDepositDetails($data){
   
      DB::table('deposit_details')->insert($data);
     
 
    }
	
	public static function getdepositCount($purchase_token,$user_id,$payment_status)
  {

    $get=DB::table('deposit_details')->where('purchase_token','=', $purchase_token)->where('user_id','=', $user_id)->where('payment_status','=', $payment_status)->get();
	$value = $get->count(); 
    return $value;
	
  }
  
  public static function updatedepositData($purchase_token,$user_id,$payment_status,$updatedata)
  {
    DB::table('deposit_details')
      ->where('purchase_token', $purchase_token)
	  ->where('user_id', $user_id)
	  ->where('payment_status', $payment_status)
      ->update($updatedata);
  }
  
  public static function upDepositdata($purchase_token,$updatedata)
  {
    DB::table('deposit_details')
      ->where('purchase_token', $purchase_token)
	  ->update($updatedata);
  }
  
  public static function displaydepositDetails($token)
  {

    $value=DB::table('deposit_details')->where('purchase_token','=',$token)->first(); 
    return $value;
	
  }
  
  public static function deleteDeposit($deposit_id){
    
	
	DB::table('deposit')->where('dep_id', '=', $deposit_id)->delete();
	
  }
  
  public static function deleteDepositDetails($deposit_id){
    
	
	DB::table('deposit_details')->where('dd_id', '=', $deposit_id)->delete();
	
  }
  
  public static function editDeposit($deposit_id){
    $value = DB::table('deposit')
      ->where('dep_id', '=', $deposit_id)
      ->first();
	return $value;
  }
  
  
  public static function updateDeposit($deposit_id, $data){
    DB::table('deposit')
      ->where('dep_id', '=', $deposit_id)
      ->update($data);
  }
  
    
  
  /* coupon */
  
  
  
	
	
	
	
	
  
  
  
  
}
