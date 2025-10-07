<?php

namespace Fickrr\Helpers;

use Cookie;
use Illuminate\Support\Facades\Crypt;
use Fickrr\Models\Languages;
use Fickrr\Models\Items;
use Fickrr\Models\Settings;
use Fickrr\Models\Members;
use Fickrr\Models\Blog;
use URL;
use File;
//use Storage;
use Fickrr\Models\Chat;
use Fickrr\Models\EmailTemplate;
use Fickrr\Models\Currencies;
use Auth;
use League\Flysystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Log;

class Helper {

    
	public static function Current_Version()
	{
	    $version = 'v4.2';
		return $version;
	}
	
	public static function version_no()
	{
	    $version = 'user_license_4_2';
		return $version;
	}
	
	
    public static function Image_Path($image_name,$empty_name) 
    {
	  
	   $allsettings = Settings::allSettings();
	   $url = URL::to("/");
	   if($allsettings->site_s3_storage == 1)
	   {
	      
	      $image_path = Storage::disk('s3')->url($image_name);
		  
	   }
	   else if($allsettings->site_s3_storage == 2)
	   {
	      Log::info("Fetching from Wasabi");
	      $wasabi_access_key_id = $allsettings->wasabi_access_key_id;
		  $wasabi_secret_access_key = $allsettings->wasabi_secret_access_key;
		  $wasabi_default_region = $allsettings->wasabi_default_region;
		  $wasabi_bucket = $allsettings->wasabi_bucket;
		  $wasabi_endpoint = 'https://s3.'.$wasabi_default_region.'.wasabisys.com';
		  $noimage_url = URL::to('/public/img/'.$empty_name);
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
			$bucket = $wasabi_bucket;
            $key = $image_name;
			
			try {
    
                  $result = $s3->getObject(['Bucket' => $bucket,'Key' => $key]);
	              $image_path = $result["@metadata"]["effectiveUri"];
                  } catch (S3Exception $e) {
                  $image_path = $noimage_url;
                  }
	        
		  
	   }
	   else if($allsettings->site_s3_storage == 3)
	   {
	      $image_path = Storage::disk('dropbox')->url($image_name);
	      
	      
	   }
	   else if($allsettings->site_s3_storage == 4)
	   {
	      $filename = $image_name;
          $dir = '/';
          $recursive = false; 
          $contents = collect(Storage::disk('google')->listContents($dir, $recursive));
          $file = $contents
                  ->where('filename', '=', pathinfo($filename, PATHINFO_FILENAME))
                  ->where('extension', '=', pathinfo($filename, PATHINFO_EXTENSION))
                  ->first();
				  
			
          $image_path = Storage::disk('google')->url($file['path']);
		
		  
		  //$image_path = "https://drive.google.com/uc?id=".$file['path']."&export=media";
		  
		  
		  
	      
	   }
	   else
	   {
	      
	      $image_path = $url.'/public/storage/items/'.$image_name;
	   }
	   return $image_path;
	   
	
	}
	
	
    
    
	
	public static function MsgCount($from,$to)
	{
	    $chck = Chat::ConvCount($from,$to);
		return $chck;
	}
	
		
	public static function WithNameGet($key)
	{
	    $chck = Items::keyWithMethod($key);
		return $chck->withdrawal_name;
	}
	
	public static function ItemTypeIdGetData($id)
	{
	    $count = Items::typeIDCount($id);
		if($count != 0)
		{
	    $chck = Items::typeIDdata($id);
		return $chck->item_type_name;
		}
		else
		{
		return "";
		}
	}
	
	public static function timeAgo($time_ago)
	{
		$cur_time 	= time();
		$time_elapsed 	= $cur_time - $time_ago;
		$seconds 	= $time_elapsed ;
		$minutes 	= round($time_elapsed / 60 );
		$hours 		= round($time_elapsed / 3600);
		$days 		= round($time_elapsed / 86400 );
		$weeks 		= round($time_elapsed / 604800);
		$months 	= round($time_elapsed / 2600640 );
		$years 		= round($time_elapsed / 31207680 );
		// Seconds
		if($seconds <= 60){
			echo "$seconds seconds ago";
		}
		//Minutes
		else if($minutes <=60){
			if($minutes==1){
				echo "one minute ago";
			}
			else{
				echo "$minutes minutes ago";
			}
		}
		//Hours
		else if($hours <=24){
			if($hours==1){
				echo "an hour ago";
			}else{
				echo "$hours hours ago";
			}
		}
		//Days
		else if($days <= 7){
			if($days==1){
				echo "yesterday";
			}else{
				echo "$days days ago";
			}
		}
		//Weeks
		else if($weeks <= 4.3){
			if($weeks==1){
				echo "a week ago";
			}else{
				echo "$weeks weeks ago";
			}
		}
		//Months
		else if($months <=12){
			if($months==1){
				echo "a month ago";
			}else{
				echo "$months months ago";
			}
		}
		//Years
		else{
			if($years==1){
				echo "one year ago";
			}else{
				echo "$years years ago";
			}
		}
	}

	
	public static function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
	
	public static function uploaded_item($user_id)
	{
	   $check_user_count = Items::checkItemUser($user_id);
	   return $check_user_count;
	}
	
	public static function available_space($user_id)
	{
	    $check_user_count = Items::checkItemUser($user_id);
		if($check_user_count != 0)
		{
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
			$item['display'] = Items::getItemStorage($user_id);
			$occupy_size = 0;
			if($allsettings->site_s3_storage == 1)
			{
			   foreach($item['display'] as $item)
			   {   
			       
			       if(!empty($item->item_file))
				   {
					   if (Storage::disk('s3')->exists($item->item_file)) 
					   {
						   $occupy_size += Storage::disk('s3')->size($item->item_file);
						   
					   }
					   else
					   {
						  $occupy_size += 0;
					   }
				   }
				   else
				   {
				      $occupy_size += 0;
				   }
				   if(!empty($item->video_file))
				   {
					   if (Storage::disk('s3')->exists($item->video_file)) 
					   {
						   
						   $occupy_size += Storage::disk('s3')->size($item->video_file);
						   
					   } 
					   else
					   {
						   $occupy_size += 0;
					   } 
				   } 
				   else
				   {
				      $occupy_size += 0;
				   }
			   }
			}
			else if($allsettings->site_s3_storage == 2)
			{
			   foreach($item['display'] as $item)
			   {   
			       
			       if(!empty($item->item_file))
				   {
					   
					   try 
					   {  
					      $result = $s3->getObject([ 'Bucket' => $wasabi_bucket, 'Key' => $item->item_file]);
		                  $occupy_size +=  $result["ContentLength"];
		               } 
					   catch (S3Exception $e) 
					   {
                           $occupy_size += 0;
                       }
					   
					   
				   }
				   else
				   {
				      $occupy_size += 0;
				   }
				   if(!empty($item->video_file))
				   {
					   
					   try 
					   {  
					      $result = $s3->getObject([ 'Bucket' => $wasabi_bucket, 'Key' => $item->video_file]);
		                  $occupy_size +=  $result["ContentLength"];
		               } 
					   catch (S3Exception $e) 
					   {
                           $occupy_size += 0;
                       }
					    
				   } 
				   else
				   {
				      $occupy_size += 0;
				   }
			   }
			}
			else
			{
			   foreach($item['display'] as $item)
			   {
			        $filepath = public_path('storage/items/'.$item->item_file);
					$videopath =  public_path('storage/items/'.$item->video_file);
					if(!empty($item->item_file))
				    {
						if (file_exists($filepath)) 
						{
						   $occupy_size += File::size(public_path('storage/items/'.$item->item_file));
						}
						else
						{
						   $occupy_size += 0;
						}
					}
					else
					{
					   $occupy_size += 0;
					}	
					if(!empty($item->video_file))
				    {
					   if (file_exists($videopath)) 
						{
						   $occupy_size += File::size(public_path('storage/items/'.$item->video_file));
						}
						else
						{
						   $occupy_size += 0;
						}
					  
					}
					else
					{
					   $occupy_size += 0;
					}
			   }
			   		
			}
	    }
		else
		{
		  $occupy_size = 0;  
		} 
		return $occupy_size;
		
	}
	
	public static function count_rating($rate_var) 
    {
	   
	    if(count($rate_var) != 0)
        {
           $top = 0;
           $bottom = 0;
           foreach($rate_var as $view)
           { 
              if($view->rating == 1){ $value1 = $view->rating*1; } else { $value1 = 0; }
              if($view->rating == 2){ $value2 = $view->rating*2; } else { $value2 = 0; }
              if($view->rating == 3){ $value3 = $view->rating*3; } else { $value3 = 0; }
              if($view->rating == 4){ $value4 = $view->rating*4; } else { $value4 = 0; }
              if($view->rating == 5){ $value5 = $view->rating*5; } else { $value5 = 0; }
              $top += $value1 + $value2 + $value3 + $value4 + $value5;
              $bottom += $view->rating;
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
	    
	    
		return $count_rating;
        
    }
	
	public static function price_info($flash_var,$price_var) 
    {
	    $additional = Settings::editAdditional();
	    if($flash_var == 1)
        {
		
			/*$varprice = ($setting['setting']->site_flash_sale_discount * $price_var) / 100;
			$price = round($varprice,2);*/
			$varprice = ($price_var / 100) * $additional->flash_sale_value;
            $pricess = $price_var - $varprice;
            $price = round($pricess,2);
			
			/*}*/
        }
        else
        {
        $price = round($price_var,2);
        }
		return $price;
	}
	
	
	
	
	
	public static function Email_Subject($id)
	{
	   $checktemp = EmailTemplate::checkTemplate($id);
	   if($checktemp != 0)
	   { 
	      $template_view = EmailTemplate::viewTemplate($id);
		  return $template_view->et_subject;
	   } 
	   
	}
	
	public static function Email_Content($id,$search,$replace)
	{
	   $checktemp = EmailTemplate::checkTemplate($id);
	   if($checktemp != 0)
	   { 
	      $template_view = EmailTemplate::viewTemplate($id);
		  return str_replace($search,$replace,$template_view->et_content);
	   } 
	   
	}
	
	public static function if_purchased($token)
	{
	   if (Auth::check()) 
	   {
		  $checkif_purchased = Items::ifpurchaseCount($token);
	   }
	   else
	   {
			$checkif_purchased = 0;
	   }
	  return $checkif_purchased;
	   
	}
	
	
	public static function member_count()
	{
	   $member_count = Members::footermemberData();
	   return $member_count;
	}
	
	public static function rating_count($item_user_id)
	{
	   $details = Items::topReviews($item_user_id);
	   return $details->rating;
	} 
	
	/*public static function price_format($position,$price,$type) 
    {
	    $allsettings = Settings::allSettings();
		if($type == "symbol") { $pre = $allsettings->site_currency_symbol; } else { $pre = $allsettings->site_currency; }
	    if($position == "left")
        {
        $price_info = $pre.round($price,2);
        }
        else
        {
        $price_info = round($price,2).$pre;
        }
		return $price_info;
	}*/
	
	public static function price_format($position,$price,$symbol,$multicurrency) 
    {
	    
		if($multicurrency != "")
		{
			$currency = Currencies::getCurrency($multicurrency);
			$priceval = $currency->currency_rate * $price;
		}
		else
		{
		   $priceval = $price;
		}	
			if($position == "left")
			{
			//$price_info = $symbol.round($priceval,2);
			$price_info = $symbol.round($priceval);
			}
			else
			{
			//$price_info = round($priceval,2).$symbol;
			$price_info = round($priceval).$symbol;
			}
		
		return $price_info;
	}
	public static function price_value($price,$multicurrency)
	{
	
	    if($multicurrency != "")
		{
			$currency = Currencies::getCurrency($multicurrency);
			$priceval = $currency->currency_rate * $price;
		}
		else
		{
		   $priceval = $price;
		}	
			
		//$price_info = round($priceval,2);
		$price_info = round($priceval);	
		
		return $price_info;
	
	}
	public static function plan_format($position,$price,$symbol) 
    {
	    
		
		    $priceval = $price;
		    if($position == "left")
			{
			//$price_info = $symbol.round($priceval,2);
			$price_info = $symbol.round($priceval);
			}
			else
			{
			//$price_info = round($priceval,2).$symbol;
			$price_info = round($priceval).$symbol;
			}
		
		return $price_info;
	}
	public static function Item_Type_Count($slug)
	{
	    $count = Items::item_type_by_count($slug);
		return $count;
	}
	public static function Category_Type_Count($id,$slug)
	{
	    $count = Items::category_type_by_count($id,$slug);
		return $count;
	}
	
	public static function key_no()
	{
	    $return = 'RXh0ZW5kZWQgTGljZW5zZQ==';
		return $return;
	}
	public static function Key_Owner()
	{
	   $return = 1;
	   return $return;
	}
	
	public static function market_data($item_token,$im_id)
	{
	   
	   $data = Blog::single_market_details($item_token,$im_id);
	   if(!empty($data->iom_market_link))
	   {
	   return $data->iom_market_link;
	   }
	   else
	   {
	   return "";
	   }
	   
	}
	public static function id_get_image_market($im_id)
	{
	  $data = Blog::single_market($im_id);
	  return $data->im_logo;
	}
	
	public static function Notification_Count($user_id)
	{
	   $display = Items::countNotify($user_id);
	   return $display;
	}
	public static function Notification_NoCount($user_id)
	{
	   $display = Items::NocountNotify($user_id);
	   return $display;
	}
	public static function Notification_Display_Limit($user_id)
	{
	   $display = Items::NotifyDisplayLimit($user_id);
	   return $display;
	}
	
	public static function Timeer_Ago( $time )
	{
		$time_difference = time() - $time;
	
		if( $time_difference < 1 ) { return 'less than 1 second ago'; }
		$condition = array( 12 * 30 * 24 * 60 * 60 =>  'year',
					30 * 24 * 60 * 60       =>  'month',
					24 * 60 * 60            =>  'day',
					60 * 60                 =>  'hour',
					60                      =>  'minute',
					1                       =>  'second'
		);
	
		foreach( $condition as $secs => $str )
		{
			$d = $time_difference / $secs;
	
			if( $d >= 1 )
			{
				$t = round( $d );
				return 'about ' . $t . ' ' . $str . ( $t > 1 ? 's' : '' ) . ' ago';
			}
		}
	}
	
	public static function IDtoFetchImage($type,$id,$sender_id)
	{
	    if($type == 'selling-item' or $type == 'buying-item' or $type == 'deposit' or $type == 'withdrawal-request')
		{  
		   $display = Members::singlevendorData($sender_id);
		   if(!empty($display->user_photo))
		   {
		   $img_url = url('/').'/public/storage/users/'.$display->user_photo;
		   }
		   else
		   {
		     $img_url = url('/').'/resources/views/theme/img/no-image.jpg';
		   }
		}
		else if($type == 'update-item' or $type == 'create-item')
		{
		   $item_details = Items::itemDetails($id);
		   if(!empty($item_details->item_preview))
		   {
		   $img_url = url('/').'/public/storage/items/'.$item_details->item_preview;
		   }
		   else
		   {
		     $img_url = url('/').'/resources/views/theme/img/no-image.jpg';
		   }
		}
		else
		{
		   $img_url = url('/').'/resources/views/theme/img/no-image.jpg';
		}
		
		 return $img_url;
		 
	}
	
	
	
}