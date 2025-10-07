<?php

namespace Fickrr\Models;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Currencies extends Model
{
    
  
  public static function viewCurrency()
  {

    $value=DB::table('currencies')->orderBy('currency_order', 'asc')->get(); 
    return $value;
	
  }		
	
  
  
  public static function saveCurrency($data)
  {
   
      DB::table('currencies')->insert($data);
     
 
  }
  
  public static function singleCurrency($token)
  {
     $value=DB::table('currencies')->where('currency_token', '=', $token)->first();
	 return $value;
  }
  
  public static function removeDefaultCurrency($token,$record)
  {
    
	DB::table('currencies')
      ->where('currency_token','!=', $token)
      ->update($record);
  
  }
  
  
   public static function updateCurrency($token,$data){
    DB::table('currencies')
      ->where('currency_token', $token)
      ->update($data);
  }
  
  
    
  
  public static function deleteCurrency($token)
  {
     DB::table('currencies')->where('currency_token', $token)->where('currency_code', '!=', 'USD')->delete();
  }
  
  
  public static function getCurrency($code)
  {

    $value=DB::table('currencies')->where('currency_code', '=', $code)->first(); 
    return $value;
	
  }	
  
  public static function CheckCurrencyCount($code)
  {

    $get=DB::table('currencies')->where('currency_code', '=', $code)->get(); 
	$value = $get->count(); 
    return $value;
    
	
  }
  
  
  public static function defaultCurrencyCount()
  {

    $get=DB::table('currencies')->where('currency_default', '=', 1)->where('currency_status', '=', 1)->get(); 
	$value = $get->count(); 
    return $value;
    
	
  }
  
  public static function defaultCurrency()
  {

    $value=DB::table('currencies')->where('currency_default', '=', 1)->where('currency_status', '=', 1)->first(); 
    return $value;
	
  }
  
  public static function allCurrency()
  {

    $value=DB::table('currencies')->where('currency_status', '=', 1)->orderBy('currency_order', 'asc')->get(); 
    return $value;
	
  }
  
}
