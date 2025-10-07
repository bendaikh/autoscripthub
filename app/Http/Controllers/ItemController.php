<?php

namespace Fickrr\Http\Controllers;

use Illuminate\Http\Request;
use Fickrr\Models\Settings;
use Fickrr\Models\Members;
use Fickrr\Models\Items;
use Fickrr\Models\Attribute;
use Fickrr\Models\Category;
use Fickrr\Models\SubCategory;
use Fickrr\Models\Chat;
use Fickrr\Models\Subscription;
use Fickrr\Models\EmailTemplate;
use Fickrr\Models\Blog;
use Fickrr\Models\Deposit;
use Fickrr\Models\Currencies;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
/*use Intervention\Image\Image;*/
use Illuminate\Support\Facades\File;
use Auth;
use Mail;
use URL;
use Image;
use Storage;
use Twocheckout;
use Twocheckout_Charge;
use Twocheckout_Error;
use Paystack;
use PDF;
use Illuminate\Support\Str;
use IyzipayBootstrap;
use GuzzleHttp\Client;
use CoinGate\CoinGate;
use Cache;
use Session;
use Carbon\Carbon;
use MercadoPago;
use Razorpay\Api\Api;
use DGvai\SSLCommerz\SSLCommerz;
use Mollie\Laravel\Facades\Mollie;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Midtrans;
use Cookie;
use Cashfree;
use Helper;

class ItemController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
	 
	 
	 public function __construct()
    {
        $this->middleware('auth');
		
    }
	public function custom_settings($field)
	{
	   $custom = Settings::customSettings();
		return $custom->$field;
	
	}
	
	public function site_currency()
	{
	    if(!empty(Cookie::get('multicurrency')))
		{
		  
		   $multicurrency = Cookie::get('multicurrency');
		   $currency = Currencies::getCurrency($multicurrency);
		   $site_currency = $currency->currency_code;
		  
		}
		else
		{
		  $default_count = Currencies::defaultCurrencyCount();
		  if($default_count == 0)
		  { 
		  $site_currency = "USD";
		  }
		  else
		  {
		  $newcurrency = Currencies::defaultCurrency();
		  $multicurrency =  $newcurrency->currency_code;
		  $currency = Currencies::getCurrency($multicurrency);
		  $site_currency = $currency->currency_code;
		  }
		 
		}
	  return $site_currency;	
	  
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
	
	public function img_slug($string)
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
	
	
	public static function Image_Path($image_name,$empty_name) 
    {
	  
	   $allsettings = Settings::allSettings();
	   $url = URL::to("/");
	   if($allsettings->site_s3_storage == 1)
	   {
	      $image_path = Storage::disk('s3')->url($image_name);
		  /*if(Storage::disk('s3')->exists($image_name))
          {
               
          }
          else
          {
             $image_path = $url.'/public/img/'.$empty_name;
          }*/
	   }
	   else if($allsettings->site_s3_storage == 2)
	   {
	      
		  
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
	      /*if(Storage::disk('dropbox')->exists($image_name))
          {
               
          }
          else
          {
             $image_path = $url.'/public/img/'.$empty_name;
          }*/
	      
	   }
	   else if($allsettings->site_s3_storage == 4)
	   {
	      $filename = $image_name;
          $dir = '/';
          $recursive = false; // Get subdirectories also?
          $contents = collect(Storage::disk('google')->listContents($dir, $recursive));
          $file = $contents
                  ->where('filename', '=', pathinfo($filename, PATHINFO_FILENAME))
                  ->where('extension', '=', pathinfo($filename, PATHINFO_EXTENSION))
                  ->first(); 
		  if(!empty($file['path']))
		  {		  
          $image_path = Storage::disk('google')->url($file['path']);
		  }
		  else
		  {
		  $image_path = $url.'/public/img/'.$empty_name;  
		  }
	      
	   }
	   else
	   {
	      $image_path = $url.'/public/storage/items/'.$image_name;
	   }
	   return $image_path;
	
	}
	
	
	public function fileupload(Request $request)
	{
	 $logged_id = Auth::user()->id;
	 $additional = Settings::editAdditional();
	 $item_token = $request->input('item_token');
	 if(!empty($item_token))
	 {
	 $edit['item'] = Items::edititemData($item_token);
	 $item_image['item'] = Items::getimagesData($item_token);
	 }
     $session_id = Session::getId();
	 $allsettings = Settings::allSettings();
     $watermark = $allsettings->site_watermark;
     $url = URL::to("/");
	 
	 $checkvalidation   = str_replace(".","",$additional->item_file_extension);
	 $validextensions = explode(',', $checkvalidation);
     if($request->hasFile('file')) 
	 {

       
       $destinationPath = public_path('/storage/items');

       // Create directory if not exists
       if (!file_exists($destinationPath)) {
          mkdir($destinationPath, 0755, true);
       }

       // Get file extension
       $extension = $request->file('file')->getClientOriginalExtension();

       // Valid extensions
       //$validextensions = array("jpeg","jpg","png","webp","zip","mp4","mp3");

       // Check extension
       if(in_array(strtolower($extension), $validextensions))
	   {

         // Rename file 
         $original = $request->file('file')->getClientOriginalName();
		 // Uploading file to given path
         //$request->file('file')->move($destinationPath, $fileName);
		 
		       $image = $request->file('file');
		       $img_name = $this->img_slug(Carbon::now()->toDayDateTimeString()).rand(11111, 99999) .'.' . $extension;
			   
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
			   
			   
		       if($allsettings->watermark_option == 1)
		       {
		            
					
					if($allsettings->site_s3_storage == 1)
				  	{
					 	
					 	    if($extension == "jpeg" or $extension == "jpg" or $extension == "png" or $extension == "webp")
			  	  		   {
    			  	  		$imagePath = $destinationPath. "/".  $img_name;
        					$image->move($destinationPath, $img_name);
        					$watermarkImg=Image::make(public_path('/storage/settings/'.$watermark));
        					$img=Image::make(public_path('/storage/items/'.$img_name));
        					if($additional->watermark_repeat == 1)
								{
									$img->insert(public_path('/storage/settings/'.$watermark),'center');
									$wmarkWidth=$watermarkImg->width();
									$wmarkHeight=$watermarkImg->height();
						
									$imgWidth=$img->width();
									$imgHeight=$img->height();
						
									$x=0;
									$y=0;
									while($y<=$imgHeight){
										$img->insert(public_path('/storage/settings/'.$watermark),'top-left',$x,$y);
										$x+=$wmarkWidth;
										if($x>=$imgWidth){
											$x=0;
											$y+=$wmarkHeight;
										}
									}
								}
								else
					            {
								
								   if($additional->watermark_position == 'center')
								   {
									  $img->insert(public_path('/storage/settings/'.$watermark), $additional->watermark_position, 0, 0);
								   }
								   else
								   {
									 $img->insert(public_path('/storage/settings/'.$watermark), $additional->watermark_position, 10, 10);
								   }
								
								}
        					$img->save(base_path('public/storage/items/'.$img_name),$additional->image_quality);
        					Storage::disk('s3')->put($img_name, file_get_contents(public_path('/storage/items/'.$img_name)));
        					File::delete(public_path('/storage/items/'.$img_name));
			  	  		    }
			  	  		    else
			  	  		    {
			  	  		       Storage::disk('s3')->put($img_name, file_get_contents($image), 'public');
			  	  		    }
			                $fileName = $img_name;
					  }
					  else if($allsettings->site_s3_storage == 2)
			  	  	 {
					 
					       if($extension == "jpeg" or $extension == "jpg" or $extension == "png" or $extension == "webp")
			  	  		   {
    			  	  		$imagePath = $destinationPath. "/".  $img_name;
        					$image->move($destinationPath, $img_name);
        					$watermarkImg=Image::make(public_path('/storage/settings/'.$watermark));
        					$img=Image::make(public_path('/storage/items/'.$img_name));
        					if($additional->watermark_repeat == 1)
								{
									$img->insert(public_path('/storage/settings/'.$watermark),'center');
									$wmarkWidth=$watermarkImg->width();
									$wmarkHeight=$watermarkImg->height();
						
									$imgWidth=$img->width();
									$imgHeight=$img->height();
						
									$x=0;
									$y=0;
									while($y<=$imgHeight){
										$img->insert(public_path('/storage/settings/'.$watermark),'top-left',$x,$y);
										$x+=$wmarkWidth;
										if($x>=$imgWidth){
											$x=0;
											$y+=$wmarkHeight;
										}
									}
								}
								else
					            {
								
								   if($additional->watermark_position == 'center')
								   {
									  $img->insert(public_path('/storage/settings/'.$watermark), $additional->watermark_position, 0, 0);
								   }
								   else
								   {
									 $img->insert(public_path('/storage/settings/'.$watermark), $additional->watermark_position, 10, 10);
								   }
								
								}
        					$img->save(base_path('public/storage/items/'.$img_name),$additional->image_quality);
							/* wasabi */
							$s3->putObject([ 'Bucket' => $wasabi_bucket, 'Key' => $img_name, 'SourceFile' => public_path('/storage/items/').$img_name,'ACL' => 'public-read']);
							File::delete(public_path('/storage/items/'.$img_name));
							/* wasabi */
			                
        					
			  	  		    }
			  	  		    else
			  	  		    {
			  	  		       
							   /* wasabi */
							   $s3->putObject([ 'Bucket' => $wasabi_bucket, 'Key' => $img_name, 'SourceFile' => $image,'ACL' => 'public-read']);
							   /* wasabi */
							   
			  	  		    }
			                $fileName = $img_name;
			         }
					 else if($allsettings->site_s3_storage == 3)
			  	  	{
					       if($extension == "jpeg" or $extension == "jpg" or $extension == "png" or $extension == "webp")
			  	  		   {
    			  	  		$imagePath = $destinationPath. "/".  $img_name;
        					$image->move($destinationPath, $img_name);
        					$watermarkImg=Image::make(public_path('/storage/settings/'.$watermark));
        					$img=Image::make(public_path('/storage/items/'.$img_name));
        					if($additional->watermark_repeat == 1)
								{
									$img->insert(public_path('/storage/settings/'.$watermark),'center');
									$wmarkWidth=$watermarkImg->width();
									$wmarkHeight=$watermarkImg->height();
						
									$imgWidth=$img->width();
									$imgHeight=$img->height();
						
									$x=0;
									$y=0;
									while($y<=$imgHeight){
										$img->insert(public_path('/storage/settings/'.$watermark),'top-left',$x,$y);
										$x+=$wmarkWidth;
										if($x>=$imgWidth){
											$x=0;
											$y+=$wmarkHeight;
										}
									}
								}
								else
					            {
								
								   if($additional->watermark_position == 'center')
								   {
									  $img->insert(public_path('/storage/settings/'.$watermark), $additional->watermark_position, 0, 0);
								   }
								   else
								   {
									 $img->insert(public_path('/storage/settings/'.$watermark), $additional->watermark_position, 10, 10);
								   }
								
								}
        					$img->save(base_path('public/storage/items/'.$img_name),$additional->image_quality);
        					Storage::disk('dropbox')->put($img_name, file_get_contents(public_path('/storage/items/'.$img_name)));
        					File::delete(public_path('/storage/items/'.$img_name));
			  	  		    }
			  	  		    else
			  	  		    {
			  	  		       Storage::disk('dropbox')->put($img_name, file_get_contents($image), '');
			  	  		    }
			                $fileName = $img_name;
					}
					else if($allsettings->site_s3_storage == 4)
			  	   {
				           if($extension == "jpeg" or $extension == "jpg" or $extension == "png" or $extension == "webp")
			  	  		   {
    			  	  		$imagePath = $destinationPath. "/".  $img_name;
        					$image->move($destinationPath, $img_name);
        					$watermarkImg=Image::make(public_path('/storage/settings/'.$watermark));
        					$img=Image::make(public_path('/storage/items/'.$img_name));
        					if($additional->watermark_repeat == 1)
								{
									$img->insert(public_path('/storage/settings/'.$watermark),'center');
									$wmarkWidth=$watermarkImg->width();
									$wmarkHeight=$watermarkImg->height();
						
									$imgWidth=$img->width();
									$imgHeight=$img->height();
						
									$x=0;
									$y=0;
									while($y<=$imgHeight){
										$img->insert(public_path('/storage/settings/'.$watermark),'top-left',$x,$y);
										$x+=$wmarkWidth;
										if($x>=$imgWidth){
											$x=0;
											$y+=$wmarkHeight;
										}
									}
								}
								else
					            {
								
								   if($additional->watermark_position == 'center')
								   {
									  $img->insert(public_path('/storage/settings/'.$watermark), $additional->watermark_position, 0, 0);
								   }
								   else
								   {
									 $img->insert(public_path('/storage/settings/'.$watermark), $additional->watermark_position, 10, 10);
								   }
								
								}
        					$img->save(base_path('public/storage/items/'.$img_name),$additional->image_quality);
        					Storage::disk('google')->put($img_name, file_get_contents(public_path('/storage/items/'.$img_name)));
        					File::delete(public_path('/storage/items/'.$img_name));
			  	  		    }
			  	  		    else
			  	  		    {
			  	  		       Storage::disk('google')->put($img_name, file_get_contents($image), '');
			  	  		    }
			                $fileName = $img_name;
				    }
					else
					{
					       if($extension == "jpeg" or $extension == "jpg" or $extension == "png" or $extension == "webp")
							{
							$imagePath = $destinationPath. "/".  $img_name;
							$image->move($destinationPath, $img_name);
							$watermarkImg=Image::make(public_path('/storage/settings/'.$watermark));
							$img=Image::make(public_path('/storage/items/'.$img_name));
							if($additional->watermark_repeat == 1)
								{
									$img->insert(public_path('/storage/settings/'.$watermark),'center');
									$wmarkWidth=$watermarkImg->width();
									$wmarkHeight=$watermarkImg->height();
						
									$imgWidth=$img->width();
									$imgHeight=$img->height();
						
									$x=0;
									$y=0;
									while($y<=$imgHeight){
										$img->insert(public_path('/storage/settings/'.$watermark),'top-left',$x,$y);
										$x+=$wmarkWidth;
										if($x>=$imgWidth){
											$x=0;
											$y+=$wmarkHeight;
										}
									}
								}
								else
					            {
								
								   if($additional->watermark_position == 'center')
								   {
									  $img->insert(public_path('/storage/settings/'.$watermark), $additional->watermark_position, 0, 0);
								   }
								   else
								   {
									 $img->insert(public_path('/storage/settings/'.$watermark), $additional->watermark_position, 10, 10);
								   }
								
								}
							$img->save(base_path('public/storage/items/'.$img_name),$additional->image_quality);
							}
							else
							{
						     $imagePath = $destinationPath. "/".  $img_name;
						     $image->move($destinationPath, $img_name);
					        }
						    $fileName = $img_name;
					}
					
					
			}
			else
			{
			     
				 if($allsettings->site_s3_storage == 1)
				{
				    if($extension == "jpeg" or $extension == "jpg" or $extension == "png" or $extension == "webp")
				    {
				    $imagePath = $destinationPath. "/".  $img_name;
					$image->move($destinationPath, $img_name);
					$img=Image::make(public_path('/storage/items/'.$img_name));
					$img->save(base_path('public/storage/items/'.$img_name),$additional->image_quality);
					Storage::disk('s3')->put($img_name, file_get_contents(public_path('/storage/items/'.$img_name)));
        			File::delete(public_path('/storage/items/'.$img_name));
					}
					else
				    {
				     Storage::disk('s3')->put($img_name, file_get_contents($image), 'public');
					}	 
				    $fileName = $img_name;
				}
				else if($allsettings->site_s3_storage == 2)
				{
					 if($extension == "jpeg" or $extension == "jpg" or $extension == "png" or $extension == "webp")
				    {
				    $imagePath = $destinationPath. "/".  $img_name;
					$image->move($destinationPath, $img_name);
					$img=Image::make(public_path('/storage/items/'.$img_name));
					$img->save(base_path('public/storage/items/'.$img_name),$additional->image_quality);
					$s3->putObject([ 'Bucket' => $wasabi_bucket, 'Key' => $img_name, 'SourceFile' => public_path('/storage/items/').$img_name,'ACL' => 'public-read']);
        			File::delete(public_path('/storage/items/'.$img_name));
					}
					else
				    {
				     $s3->putObject([ 'Bucket' => $wasabi_bucket, 'Key' => $img_name, 'SourceFile' => $image,'ACL' => 'public-read']);
					}	 
				    $fileName = $img_name;
				}
			    else if($allsettings->site_s3_storage == 3)
			  	{
				    if($extension == "jpeg" or $extension == "jpg" or $extension == "png" or $extension == "webp")
				    {
				    $imagePath = $destinationPath. "/".  $img_name;
					$image->move($destinationPath, $img_name);
					$img=Image::make(public_path('/storage/items/'.$img_name));
					$img->save(base_path('public/storage/items/'.$img_name),$additional->image_quality);
					Storage::disk('dropbox')->put($img_name, file_get_contents(public_path('/storage/items/'.$img_name)));
        			File::delete(public_path('/storage/items/'.$img_name));
					}
					else
				    {
				     Storage::disk('dropbox')->put($img_name, file_get_contents($image), '');
					}
					$fileName = $img_name;
				}
				else if($allsettings->site_s3_storage == 4)
			  	{
				     if($extension == "jpeg" or $extension == "jpg" or $extension == "png" or $extension == "webp")
				    {
				    $imagePath = $destinationPath. "/".  $img_name;
					$image->move($destinationPath, $img_name);
					$img=Image::make(public_path('/storage/items/'.$img_name));
					$img->save(base_path('public/storage/items/'.$img_name),$additional->image_quality);
					Storage::disk('google')->put($img_name, file_get_contents(public_path('/storage/items/'.$img_name)));
        			File::delete(public_path('/storage/items/'.$img_name));
					}
					else
				    {
				     Storage::disk('google')->put($img_name, file_get_contents($image), '');
					}
					$fileName = $img_name;
				}
				else
			    {
					    
					   if($extension == "jpeg" or $extension == "jpg" or $extension == "png" or $extension == "webp")
				       {
						$imagePath = $destinationPath. "/".  $img_name;
						$image->move($destinationPath, $img_name);
						$img=Image::make(public_path('/storage/items/'.$img_name));
						$img->save(base_path('public/storage/items/'.$img_name),$additional->image_quality);
					   }
					   else
					   {	
						$imagePath = $destinationPath. "/".  $img_name;
						$image->move($destinationPath, $img_name);
					   }
					   $fileName = $img_name;	
			    }	
			   			
			        
			}		
		  
		 
		 $data = array('original_file_name' => $original,'item_file_name' => $fileName, 'user_id' => $logged_id, 'session_id' => $session_id);
         Items::proddataSave($data); 
		 $getdata['first'] = Items::getProdutData($session_id); // item thumbnail
		 $getdata['second'] = Items::getProdutData($session_id);  // item preview
		 $getdata['third'] = Items::getProdutZip($session_id);   // item file
		 $getdata['four'] = Items::getProdutData($session_id);   // screenshot gallery
		 $getdata['five'] = Items::getProdutMP4($session_id);   // video file
		 $getdata['six'] = Items::getProdutMP3($session_id);   // audio file
		 $record = '<div class="row">
		           <div class="col-sm-6"> 
				   <div class="form-group">
                    <label for="site_desc" class="control-label mb-1">Upload Thumbnail (Size : 80x80px) <span class="require">*</span></label><br/>
                    <select name="item_thumbnail2" id="item_thumbnail2" class="form-control">
                    <option value=""></option>';
                    foreach($getdata['first'] as $get)
					{
					   $record .= '<option value="'.$get->item_file_name.'">'.$get->original_file_name.'</option>';
					}
                    $record .= '</select>';
                   if(!empty($item_token))
				   {
						if($edit['item']->item_thumbnail!='')
						{
							$record .='<img class="lazy" width="80" height="80" src="'.$this->Image_Path($edit['item']->item_thumbnail,'no-image.png').'" data-original="'.$this->Image_Path($edit['item']->item_thumbnail,'no-image.png').'" alt="'.$edit['item']->item_name.'">';
						}
						else
						{
							$record .='<img class="lazy" width="80" height="80" src="'.url('/').'/public/img/no-image.png" data-original="'.url('/').'/public/img/no-image.png" alt="'.$edit['item']->item_name.'">';
						}
                   } 
				   $record .= '</div></div>';
		 $record .= '<div class="col-sm-6">
		            <div class="form-group">
                    <label for="site_desc" class="control-label mb-1">Upload Preview (Size : 361x230px) <span class="require">*</span></label><br/>
                    <select name="item_preview2" id="item_preview2" class="form-control">
                    <option value=""></option>';
                    foreach($getdata['second'] as $get)
					{
					   $record .= '<option value="'.$get->item_file_name.'">'.$get->original_file_name.'</option>';
					}
                    $record .= '</select>';
                   if(!empty($item_token))
				   {
						if($edit['item']->item_preview!='')
						{
							$record .='<img class="lazy" width="80" height="80" src="'.$this->Image_Path($edit['item']->item_preview,'no-image.png').'" data-original="'.$this->Image_Path($edit['item']->item_preview,'no-image.png').'" alt="'.$edit['item']->item_name.'">';
						}
						else
						{
							$record .='<img class="lazy" width="80" height="80" src="'.url('/').'/public/img/no-image.png" data-original="'.url('/').'/public/img/no-image.png" alt="'.$edit['item']->item_name.'">';
						}
                   } 
				   $record .= '</div></div>';
				   if($additional->show_screenshots == 1)
									 {		
                                     $record .= '<div class="col-sm-6">
									            <div class="form-group">
                                                <label for="customer_earnings" class="control-label mb-1">Upload Screenshots (multiple) (Size : 750x430px)</label>
                                                <select id="item_screenshot2" name="item_screenshot[]" class="form-control" multiple>';
												foreach($getdata['four'] as $get)
												{
												$record .= '<option value="'.$get->item_file_name.'">'.$get->original_file_name.'</option>';
												}
                                                $record .= '</select>';
												if(!empty($item_token))
											   {
											      $alerttext = 'Are you sure you want to delete?';
											      foreach($item_image['item'] as $item)
												  {
												      $record .= '<div class="item-img" style="float:left;margin:10px;"><img class="lazy" width="80" height="80" src="'.$this->Image_Path($item->item_image,'no-image.png').'" data-original="'.$this->Image_Path($item->item_image,'no-image.png').'" alt="'.$item->item_image.'">';
													  $record .='<a href="'.url('/edit-item').'/dropimg/'.base64_encode($item->itm_id).'" onClick="return confirm("'.$alerttext.'");" class="drop-icon"><span class="dwg-trash drop-icon"></span></a></div>';
													  
												  }
											   }
                                             $record .= '<div class="clearfix"></div></div></div>';
		                            }
									if($additional->show_video == 1)
									{
									$record .='<div class="col-sm-6">
									            <div class="form-group">
                                                <label for="name" class="control-label mb-1">Preview Type (optional)</label>
                                               <select name="video_preview_type2" id="video_preview_type2" class="form-control">
                                                <option value=""></option>';
												if(!empty($item_token))
												{
												   if($edit['item']->video_preview_type == 'youtube')
												   {
												   $record .= '<option value="youtube" selected>Youtube</option>
                                                               <option value="mp4">MP4</option>
															   <option value="mp3">MP3</option>';
												   }
												   else if($edit['item']->video_preview_type == 'mp4')
												   {
													 $record .= '<option value="youtube">Youtube</option>
                                                                 <option value="mp4" selected>MP4</option>
																 <option value="mp3">MP3</option>';
												   }
												   else if($edit['item']->video_preview_type == 'mp3')
												   {
													 $record .= '<option value="youtube">Youtube</option>
                                                                 <option value="mp4">MP4</option>
																 <option value="mp3" selected>MP3</option>';
												   }
												   else
												   {
												      $record .= '<option value="youtube">Youtube</option>
                                                                 <option value="mp4">MP4</option>
																 <option value="mp3">MP3</option>';
												   }
												}
												else
												{
												    $record .= '<option value="youtube">Youtube</option>
                                                                <option value="mp4">MP4</option>
																<option value="mp3">MP3</option>';
												}
                                                    
                                                $record .= '</select>
                                            </div></div>'; 
											if(!empty($item_token))
								             {
												   if($edit['item']->video_preview_type == 'youtube')
												   {
												   $record .= '<div class="col-sm-6 display-block" id="youtube"><div class="form-group">';
												   }
												   else if($edit['item']->video_preview_type == 'mp4')
												   {
													 $record .= '<div class="col-sm-6 display-none" id="youtube"><div class="form-group">';
												   }
												   else if($edit['item']->video_preview_type == 'mp3')
												   {
													 $record .= '<div class="col-sm-6 display-none" id="youtube"><div class="form-group">';
												   }
												   else
												   {
													  $record .= '<div class="col-sm-6 display-none" id="youtube"><div class="form-group">';
												   }
												}
												else
												{			
												$record .= '<div class="col-sm-6 display-none" id="youtube"><div class="form-group">';
												}		   
									            if(!empty($item_token))
												 {
												   if(!empty($edit['item']->video_url))
												   {
												   $video_linked = $edit['item']->video_url;
												   }
												   else
												   {
													  $video_linked = "";
												   }
												 }
												 else
												 {
												  $video_linked = "";
												 } 
                                                $record .='<label for="name" class="control-label mb-1">Youtube Video URL <span class="require">*</span></label>
                                                
                                                <input type="text" id="video_url2" name="video_url2" class="form-control" value="'.$video_linked.'" data-bvalidator="required">
                                                 <small>(example : https://www.youtube.com/watch?v=C0DPdy98e4c)</small>
                                            </div></div>';
									if(!empty($item_token))
								             {
												   if($edit['item']->video_preview_type == 'youtube')
												   {
												   $record .= '<div class="col-sm-6 display-none" id="mp4"><div  class="form-group">';
												   }
												   else if($edit['item']->video_preview_type == 'mp4')
												   {
													 $record .= '<div class="col-sm-6 display-block" id="mp4"><div class="form-group">';
												   }
												   else if($edit['item']->video_preview_type == 'mp3')
												   {
													 $record .= '<div class="col-sm-6 display-none" id="mp4"><div class="form-group">';
												   }
												   else
												   {
													  $record .= '<div class="col-sm-6 display-none" id="mp4"><div class="form-group">';
												   }
												}
												else
												{			
												$record .= '<div class="col-sm-6 display-none" id="mp4"><div class="form-group">';
												}		
									
                                    $record .='<label for="site_desc" class="control-label mb-1">Upload MP4 Video <span class="require">*</span></label><br/>
                                                <select id="video_file2" name="video_file2" class="form-control">
												<option value=""></option>';
                                                foreach($getdata['five'] as $get)
												{
                                                $record .= '<option value="'.$get->item_file_name.'">'.$get->original_file_name.'</option>';
												}
                                                $record .= '</select>';
												if(!empty($item_token))
											    {
											     if($edit['item']->video_file!='')
												 {
												    $record .= '<span class="require">'.$edit['item']->video_file.'</span>';
												 }
                                               }
									$record .= '</div></div>';		   
									if(!empty($item_token))
								             {
												   if($edit['item']->video_preview_type == 'youtube')
												   {
												   $record .= '<div class="col-sm-6 display-none" id="mp3"><div  class="form-group">';
												   }
												   else if($edit['item']->video_preview_type == 'mp4')
												   {
													 $record .= '<div class="col-sm-6 display-none" id="mp3"><div class="form-group">';
												   }
												   else if($edit['item']->video_preview_type == 'mp3')
												   {
													 $record .= '<div class="col-sm-6 display-block" id="mp3"><div class="form-group">';
												   }
												   else
												   {
													  $record .= '<div class="col-sm-6 display-none" id="mp3"><div class="form-group">';
												   }
												}
												else
												{			
												$record .= '<div class="col-sm-6 display-none" id="mp3"><div class="form-group">';
												}		   
									$record .='<label for="site_desc" class="control-label mb-1">Upload MP3<span class="require">*</span></label><br/>
                                                <select id="audio_file2" name="audio_file2" class="form-control">
												<option value=""></option>';
                                                foreach($getdata['six'] as $get)
												{
                                                $record .= '<option value="'.$get->item_file_name.'">'.$get->original_file_name.'</option>';
												}
                                                $record .= '</select>';
												if(!empty($item_token))
											    {
											     if($edit['item']->audio_file!='')
												 {
												    $record .= '<span class="require">'.$edit['item']->audio_file.'</span>';
												 }
                                               }		   
                                             $record .= '</div></div>';				
									}
									$record .= '<div class="col-sm-6">
				                 <div class="form-group">
                                                <label for="name" class="control-label mb-1">Upload Main File Type <span class="require">*</span></label>
                                               <select name="file_type2" id="file_type2" class="form-control" data-bvalidator="required">
                                                <option value=""></option>';
												if(!empty($item_token))
												{
												   if($edit['item']->file_type == 'file')
												   {
												   $record .= '<option value="file" selected>File</option>
												               <option value="link">Link / URL</option>
															   <option value="serial">License Keys / Serial Numbers</option>';
												   }
												   else if($edit['item']->file_type == 'link')
												   {
													 $record .= '<option value="file">File</option>
												                 <option value="link" selected>Link / URL</option>
																 <option value="serial">License Keys / Serial Numbers</option>';
												   }
												   else
												   {
												      $record .= '<option value="file">File</option>
												                  <option value="link">Link / URL</option>
																  <option value="serial" selected>License Keys / Serial Numbers</option>';
												   }
												}
												else
												{
												    $record .= '<option value="file">File</option>
												            <option value="link">Link / URL</option>
															<option value="serial">License Keys / Serial Numbers</option>';
												}
                                                $record .= '</select></div></div>';
								if(!empty($item_token))
								{
								   if($edit['item']->file_type == 'file')
								   {
								   $record .= '<div class="col-sm-6 display-block" id="main_file"><div class="form-group">';
								   }
								   else if($edit['item']->file_type == 'link')
								   {
								     $record .= '<div class="col-sm-6 display-none" id="main_file"><div class="form-group">';
								   }
								   else if($edit['item']->file_type == 'serial')
								   {
								     $record .= '<div  class="col-sm-6 display-none" id="main_file"><div class="form-group">';
								   }
								   else
								   {
								      $record .= '<div class="col-sm-6" id="main_file"><div class="form-group">';
								   }
								}
								else
								{			
								$record .= '<div class="col-sm-6" id="main_file"><div class="form-group">';
								}			
				   
                               $record .= '<label for="customer_earnings" class="control-label mb-1">Upload Main File<span class="require">*</span></label>
                                                <select name="item_file2" id="item_file2" class="form-control">
                                                <option value=""></option>';
												foreach($getdata['third'] as $get)
												{
												$record .= '<option value="'.$get->item_file_name.'">'.$get->original_file_name.'</option>';
												}
                                                $record .= '</select>';
											 if(!empty($item_token))
											 {
											     if($edit['item']->item_file!='')
												 {
												    $record .= '<span class="require">'.$edit['item']->item_file.'</span>';
												 }
                                             }
											 $record .= '</div></div>';
											 if(!empty($item_token))
								             {
												   if($edit['item']->file_type == 'file')
												   {
												   $record .= '<div class="col-sm-6 display-none" id="main_link"><div  class="form-group">';
												   }
												   else if($edit['item']->file_type == 'link')
												   {
													 $record .= '<div class="col-sm-6 display-block" id="main_link"><div class="form-group">';
												   }
												   else if($edit['item']->file_type == 'serial')
												   {
													 $record .= '<div class="col-sm-6 display-none" id="main_link"><div class="form-group">';
												   }
												   else
												   {
													  $record .= '<div class="col-sm-6" id="main_link"><div class="form-group">';
												   }
												}
												else
												{			
												$record .= '<div class="col-sm-6" id="main_link"><div class="form-group">';
												}
												if(!empty($item_token))
												 {
												   if(!empty($edit['item']->item_file_link))
												   {
												   $item_file_linked = $edit['item']->item_file_link;
												   }
												   else
												   {
													  $item_file_linked = "";
												   }
												 }
												 else
												 {
												  $item_file_linked = "";
												 }
                                                $record .= '<label for="name" class="control-label mb-1">Main File Link/URL <span class="require">*</span></label>
                                                <input type="text" id="item_file_link2" name="item_file_link2" class="form-control" value="'.$item_file_linked.'" data-bvalidator="required,url">
                                              </div>  
                                            </div></div>';
								    $record .= '<script>
									        $("#hint_comma").hide();
	                                        $("#hint_line").hide();	
											$("#file_type2").on("change", function() {
												  if ( this.value == "file")
												  {
													$("#main_file").show();
													$("#main_link").hide();
													$("#main_delimiter").hide();
													$("#main_serials").hide();
												  }
												  else if(this.value == "link")
												  {
													$("#main_file").hide();
													$("#main_link").show();
													$("#main_delimiter").hide();
													$("#main_serials").hide();
												  }
												  else if(this.value == "serial")
												  {
													$("#main_file").hide();
													$("#main_link").hide();
													$("#main_delimiter").show();
													$("#main_serials").show();
													$("#free_download option[value=0]").prop("selected", true); 
		                                            $("#item_support option[value=1]").prop("selected", true);
												  }
												  else
												  {
													$("#main_file").hide();
													$("#main_link").hide();
													$("#main_delimiter").hide();
													$("#main_serials").hide();
												  }
												});
												$("#item_delimiter1").on("change", function() {
												  if ( this.value == "comma")
												  {
													 $("#hint_comma").show();
													 $("#hint_line").hide();
												  }
												  else if ( this.value == "newline")
												  {
													 $("#hint_comma").hide();
													 $("#hint_line").show();
												  }
												  else
												  {
													 $("#hint_comma").hide();
													 $("#hint_line").hide();
												  }
												 });	
											$("#video_preview_type2").on("change", function() {
												  if ( this.value == "youtube")
												  
												  {
													 $("#youtube").show();
													 $("#mp4").hide();
													 $("#mp3").hide();
												  }	
												  else if ( this.value == "mp4")
												  {
													 $("#mp4").show();
													 $("#youtube").hide();
													 $("#mp3").hide();
												  }
												  else if ( this.value == "mp3")
												  {
													 $("#mp3").show();
													 $("#youtube").hide();
													 $("#mp4").hide();
												  }
												  else
												  {
													  $("#mp4").hide();
													  $("#youtube").hide();
													  $("#mp3").hide();
												  }
												  
												 });
											
											</script>';
		 return response()->json(['success' => true, 'record' => $record]);

        }

      }
    }
	
	
	
	public function favourites_item()
	{
	   $fav['item'] = Items::getfavitemData();
	   $data = array('fav' => $fav);
	   
	   return view('favourites')->with($data);
	}
	
	
	public function invoice_download($product_token,$order_id)
	{
	    $logged = Auth::user()->id;
		$check_purchased = Items::checkPurchased($logged,$product_token);
		if($check_purchased != 0)
		{
		  $item['data'] = Items::solditemData($product_token);
		  $order_details = Items::singleorderData($order_id);
		  $pdf_filename = $order_details->ord_id.'-'.$order_details->purchase_token.'-'.$item['data']->item_slug.'.pdf';
		  $product_slug = $item['data']->item_slug;
		  $product_id = $item['data']->item_id;
		  $user_id = $order_details->user_id;
		  $currency_type = $order_details->currency_type;
		  $user_details = Members::singlebuyerData($user_id);
		  $data = ['order_id' => $order_details->purchase_token, 'purchase_id' => $order_details->purchase_code, 'purchase_date' => $order_details->start_date, 'expiry_date' => $order_details->end_date, 'license' => $order_details->license, 'product_name' => $order_details->item_name, 'product_slug' => $product_slug, 'payment_token' => $order_details->payment_token, 'payment_type' => $order_details->payment_type, 'product_price' => $order_details->item_price, 'username' => $user_details->username, 'product_id' => $product_id, 'currency_type' => $currency_type ];
        
        $pdf = PDF::loadView('pdf_view', $data);  
        return $pdf->download($pdf_filename);
	    
		}
		else
		{
		  return redirect('404');
		}
	}
	
	
	
	
	public function remove_favourites_item($favid,$itemid)
	{
	    $fav_id = base64_decode($favid);
		$item_id = base64_decode($itemid);
		Items::dropFavitem($fav_id);
		$get['item'] = Items::selecteditemData($item_id);
		$liked = $get['item']->item_liked - 1;
		$record = array('item_liked' => $liked);
		Items::updatefavouriteData($item_id,$record);
	    return redirect()->back()->with('success', 'Item removed to favorite');
	}
	
	
	public function view_favorite_item($itemid,$favorite,$liked)
	{  
	   $item_id = base64_decode($itemid);
	   $like = base64_decode($liked) + 1;
	   $log_user = Auth::user()->id;
	   $getcount  = Items::getfavouriteCount($item_id,$log_user);
	   if($getcount == 0)
	   {
	      $data = array ('item_id' => $item_id, 'user_id' => $log_user);
		  Items::savefavouriteData($data);
		  $record = array('item_liked' => $like);
		  Items::updatefavouriteData($item_id,$record);
		  return redirect()->back()->with('success', 'Item added to favorite');
		  
	   }
	   else
	   {
	     return redirect()->back()->with('error', 'Sorry Item already added to favorite');
	   }
	  
	
	}
	
	
	public function view_withdrawal()
	{
	  $sid = 1;
	  $setting['setting'] = Settings::editGeneral($sid);
	  $withdraw_option = explode(',', $setting['setting']->withdraw_option);
	  $itemData['item'] = Items::getdrawalData();
	  $data = array('withdraw_option' => $withdraw_option, 'itemData' => $itemData);
	  
	  return view('withdrawal')->with($data);
	}
	
	
	
	public function add_post_comment(Request $request)
	{
	    $comm_text = $request->input('comm_text');
		$comm_user_id = $request->input('comm_user_id');
		$comm_item_user_id = $request->input('comm_item_user_id');
		$comm_item_id = $request->input('comm_item_id');
		$item_url = $request->input('comm_item_url');
		
		$comm_date = date('Y-m-d H:i:s');
		$comment_data = array('comm_user_id' => $comm_user_id, 'comm_item_user_id' => $comm_item_user_id, 'comm_item_id' => $comm_item_id, 'comm_text' => $comm_text, 'comm_date' => $comm_date);
		Items::savecommentData($comment_data);
		$item_user_id = $comm_item_user_id;
		$user_id = $comm_user_id;
		$getvendor['user'] = Members::singlevendorData($item_user_id);
		$getbuyer['user'] = Members::singlebuyerData($user_id);
		$check_email_support = Members::getuserSubscription($item_user_id);
		if($getvendor['user']->item_comment_email == 1)
		{
		   if($check_email_support == 1)
		   {
				$from_name = $getbuyer['user']->name;
				$from_email = $getbuyer['user']->email;
				
				$to_name = $getvendor['user']->name;
				$to_email = $getvendor['user']->email;
				
				$record = array('item_url' => $item_url, 'from_name' => $from_name, 'from_email' => $from_email, 'comm_text' => $comm_text);
				/* email template code */
	          	$checktemp = EmailTemplate::checkTemplate(2);
			  	if($checktemp != 0)
			  	{
			  	$template_view['mind'] = EmailTemplate::viewTemplate(2);
			  	$template_subject = $template_view['mind']->et_subject;
			  	}
			  	else
			  	{
			  	$template_subject = "New Comment Received";
			  	}
			  	/* email template code */
				Mail::send('comment_mail', $record, function($message) use ($from_email, $from_name, $to_name, $to_email, $template_subject) {
						$message->to($to_email, $to_name)
								->subject($template_subject);
						$message->from($from_email,$from_name);
					});
			 }		
		}	
		
		return redirect()->back();
		
	}
	
	
	
	public function reply_post_comment(Request $request)
	{
	    $comm_text = $request->input('comm_text');
		$comm_user_id = $request->input('comm_user_id');
		$comm_item_user_id = $request->input('comm_item_user_id');
		$comm_item_id = $request->input('comm_item_id');
		$comm_id = $request->input('comm_id');
		$item_url = $request->input('comm_item_url');
		$comm_date = date('Y-m-d H:i:s');
		$comment_data = array('comm_user_id' => $comm_user_id, 'comm_item_user_id' => $comm_item_user_id, 'comm_item_id' => $comm_item_id, 'comm_id' => $comm_id, 'comm_text' => $comm_text, 'comm_date' => $comm_date);
		Items::replycommentData($comment_data);
		
		$item_user_id = $comm_item_user_id;
		$user_id = $comm_user_id;
		$getvendor['user'] = Members::singlevendorData($item_user_id);
		$getbuyer['user'] = Members::singlebuyerData($user_id);
		$check_email_support = Members::getuserSubscription($item_user_id);
		if($getvendor['user']->item_comment_email == 1)
		{
		    if($check_email_support == 1)
		    {
				$from_name = $getbuyer['user']->name;
				$from_email = $getbuyer['user']->email;
				
				$to_name = $getvendor['user']->name;
				$to_email = $getvendor['user']->email;
				
				$record = array('item_url' => $item_url, 'from_name' => $from_name, 'from_email' => $from_email, 'comm_text' => $comm_text);
				/* email template code */
	          	$checktemp = EmailTemplate::checkTemplate(2);
			  	if($checktemp != 0)
			  	{
			  	$template_view['mind'] = EmailTemplate::viewTemplate(2);
			  	$template_subject = $template_view['mind']->et_subject;
			  	}
			  	else
			  	{
			  	$template_subject = "New Comment Received";
			  	}
			  	/* email template code */
				Mail::send('comment_mail', $record, function($message) use ($from_email, $from_name, $to_name, $to_email, $template_subject) {
						$message->to($to_email, $to_name)
								->subject($template_subject);
						$message->from($from_email,$from_name);
					});
			}		
		}
		
		
		
		return redirect()->back()->with('success', 'Your comment has been sent');
		
	}
	
	
	public function withdrawal_request(Request $request)
	{
	   $withdrawal = $request->input('withdrawal');
	   $paypal_email = $request->input('paypal_email');
	   $stripe_email = $request->input('stripe_email');
	   $available_balance = base64_decode($request->input('available_balance'));
	   $get_amount = $request->input('get_amount');
	   $user_id = $request->input('user_id');
	   $token = $request->input('user_token');
	   $wd_data = date('Y-m-d');
	   $wd_status = "pending";
	   $paystack_email = $request->input('paystack_email');
	   $payfast_email = $request->input('payfast_email');
	   $bank_details = $request->input('bank_details');
	   
	   $paytm_no = $request->input('paytm_no');
	   $upi_id = $request->input('upi_id');
	   $skrill_email = $request->input('skrill_email');
	   $crypto_address = $request->input('crypto_address');
	   
	   $drawal_data = array('wd_user_id' => $user_id, 'withdraw_type' => $withdrawal, 'paypal_email' => $paypal_email, 'stripe_email' => $stripe_email, 'wd_amount' => $get_amount, 'wd_status' => $wd_status, 'wd_date' => $wd_data, 'bank_details' => $bank_details, 'paystack_email' => $paystack_email, 'payfast_email' => $payfast_email, 'skrill_email' => $skrill_email, 'upi_id' => $upi_id, 'paytm_no' => $paytm_no, 'crypto_address' => $crypto_address);
	   if($available_balance >= $get_amount)
	   {
	     Items::savedrawalData($drawal_data);
		 $less_amount = $available_balance - $get_amount;
		 $data = array('earnings' => $less_amount);
		 Members::updateData($token,$data);
		 if($this->custom_settings('author_key') == Helper::Key_Owner())
		 {
		 /* Notification Message */
		 $insertedId = Items::LastdrawalData();
		 $noti_date = date('Y-m-d H:i:s');
		 $noti_sender_id = $user_id;
		 $noti_receiver_id = 1;
		 $getvendor['user'] = Members::singlebuyerData($user_id);
		 $noti_message = 'New withdrawal request send by <strong>'.$getvendor['user']->name.'</strong>';
		 $savedata = array('noti_date' => $noti_date, 'noti_sender_id' => $noti_sender_id, 'noti_receiver_id' => $noti_receiver_id, 'noti_message' => $noti_message, 'noti_type' => 'withdrawal-request', 'noti_type_id' => $insertedId->wd_id);
		 Items::saveNotification($savedata);
		 /* Notification Message */
		 }
		 return redirect()->back()->with('success', 'Your withdrawal request has been sent');
	   }
	   else
	   {
	     return redirect()->back()->with('error', 'Sorry Please check your available balance');
	   }
	   
	   
	   
	}
	
	
	public function edit_item($token)
	{
	 
		
		
		
		$edit['item'] = Items::edititemData($token);
		$type_id = $edit['item']->item_type_id;
		$getcount  = Items::getimagesCount($token);
		$item_image['item'] = Items::getimagesData($token);
		$cat_name = $edit['item']->item_category_type; 
        $cat_id = $edit['item']->item_category;
		
		$type_name = Items::slugItemtype($type_id);
		$typer_id = $type_name->item_type_id;
		$attribute['fields'] = Attribute::againAttribute($typer_id,$token);
		if(count($attribute['fields']) != 0)
		{
		 $attri_field['display'] = Attribute::againAttribute($typer_id,$token);
		}
		else
		{
		  $attri_field['display'] = Attribute::selectedAttribute($typer_id);
		}
		/* get user storge size */
		
		$additional_settings = Settings::editAdditional();
		$allsettings = Settings::allSettings();
		if($additional_settings->subscription_mode == 1)
		{
		   if(Auth::user()->user_subscr_space_level == 'limited')
		   {
				if($allsettings->site_s3_storage == 0)
				{
				    $occupy_size = $this->changeType($this->available_space(Auth::user()->id), 'B' ,'KB');
					if(Auth::user()->user_subscr_space_type == 'MB')
					{ 
					$maxsize = $this->changeType(Auth::user()->user_subscr_space, 'MB', 'KB'); 
					} 
					elseif(Auth::user()->user_subscr_space_type == 'GB')
					{
					$maxsize = $this->changeType(Auth::user()->user_subscr_space, 'GB', 'KB');
					}
					else
					{
					$maxsize = $this->changeType(Auth::user()->user_subscr_space, 'TB', 'KB');
					}
					if($occupy_size < $maxsize)
					{
					  $overall_size = $maxsize - $occupy_size;
					  $round_size = round($overall_size);
					  $balance_storage = $this->changeType($round_size, 'KB', 'MB');
					  $available_storage = round($balance_storage); // mb
					}
					else
					{
					   $overall_size = $maxsize;
					   $round_size = round($overall_size);
					   $balance_storage = $this->changeType($round_size, 'KB', 'MB');
					   $available_storage = round($balance_storage); // mb
					}
				}
				else
				{
				   $available_storage = 100000; // mb
				}	
			}
			else
			{
			   $available_storage = 100000; // mb
			}
		}
		else
		{
		   $available_storage = 100000; // mb
		}		
				
		/* get user storge size */
		$session_id = Session::getId();
		$getdata1['first'] = Items::getProdutData($session_id);
	    $getdata2['second'] = Items::getProdutData($session_id);
	    $getdata3['third'] = Items::getProdutZip($session_id);
		$getdata4['four'] = Items::getProdutData($session_id);
		$getdata5['five'] = Items::getProdutMP4($session_id);
		$getdata6['six'] = Items::getProdutMP3($session_id);
		$item_token = $token;
		$re_categories['menu'] = Category::with('SubCategory')->where('category_status','=','1')->where('drop_status','=','no')->orderBy('menu_order',$allsettings->menu_categories_order)->get();
		if($this->custom_settings('author_key') == Helper::Key_Owner())
		{
		$other_market = Blog::getothermarketData();
		}
		else
		{
		$other_market = 0;
		}
		$data = array(  're_categories' => $re_categories,  'edit' => $edit, 'token' => $token, 'item_image' => $item_image, 'getcount' => $getcount, 'cat_id' => $cat_id, 'cat_name' => $cat_name, 'type_name' => $type_name, 'attri_field' => $attri_field, 'attribute' => $attribute, 'typer_id' => $typer_id, 'available_storage' => $available_storage, 'getdata1' => $getdata1, 'getdata2' => $getdata2, 'getdata3' => $getdata3, 'getdata4' => $getdata4, 'getdata5' => $getdata5, 'item_token' => $item_token, 'getdata6' => $getdata6, 'other_market' => $other_market);
	  
	   if($edit['item']->user_id == Auth::user()->id)
	   { 
	   return view('edit-item')->with($data);
	   }
	   else
	   {
	    return view('404');
	   }
	    
	}
	
	
	public function drop_image_item($dropimg,$token)
	{
	   
	   $token = base64_decode($token); 
	   Items::deleteimgdata($token);
	  
	  return redirect()->back()->with('success', 'Delete successfully.');
	
	}
	
	
	
	
	public function manage_item()
	{
	 
	  
	  $itemData['item'] = Items::getmanageitemData();
	  $encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
	  $viewitem['type'] = Items::gettypeItem();
	  return view('manage-item',[ 'itemData' => $itemData, 'encrypter' => $encrypter, 'viewitem' => $viewitem]);
	}
	
	
	public function delete_item_request($token)
	{
	   $encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
	   $token_key   = $encrypter->decrypt($token);
	   
	  $check_data = Items::checkDels($token_key);
	  if($check_data == 1)
	  {
	  $data = array('drop_status'=>'yes', 'item_status' => 0);
	  
      /*Items::deleteData($token_key,$data);*/
	  Items::admindeleteData($token_key,$data);
	  
	  return redirect()->back()->with('success', 'Your Item Deleted Successfully.');
	  }
	  else
	  {
	    return redirect('404');
	  }
	  
	  
	
	}
	

    
    public function upload_item($itemtype)
    {
	    
		$encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
	    $type_id   = $encrypter->decrypt($itemtype);
		$itemWell['type'] = Items::gettypeItem();       
		$attribute['fields'] = Attribute::selectedAttribute($type_id);
		$type_name = Items::viewItemtype($type_id);
		/* get user storge size */
		
		$additional_settings = Settings::editAdditional();
		$allsettings = Settings::allSettings();
		if($additional_settings->subscription_mode == 1)
		{
		   if(Auth::user()->user_subscr_space_level == 'limited')
		   {
				if($allsettings->site_s3_storage == 0)
				{
				    $occupy_size = $this->changeType($this->available_space(Auth::user()->id), 'B' ,'KB');
					if(Auth::user()->user_subscr_space_type == 'MB')
					{ 
					$maxsize = $this->changeType(Auth::user()->user_subscr_space, 'MB', 'KB'); 
					} 
					elseif(Auth::user()->user_subscr_space_type == 'GB')
					{
					$maxsize = $this->changeType(Auth::user()->user_subscr_space, 'GB', 'KB');
					}
					else
					{
					$maxsize = $this->changeType(Auth::user()->user_subscr_space, 'TB', 'KB');
					}
					if($occupy_size < $maxsize)
					{
					  $overall_size = $maxsize - $occupy_size;
					  $round_size = round($overall_size);
					  $balance_storage = $this->changeType($round_size, 'KB', 'MB');
					  $available_storage = round($balance_storage); 
					}
					else
					{
					   $overall_size = $maxsize;
					   $round_size = round($overall_size);
					   $balance_storage = $this->changeType($round_size, 'KB', 'MB');
					   $available_storage = round($balance_storage); 
					}
				}
				else
				{
				  $available_storage = 100000; 
				}	
			}
			else
			{
			   $available_storage = 100000; 
			}
		}
		else
		{
		   $available_storage = 100000; 
		}		
				
		/* get user storge size */
		$session_id = Session::getId();
		$getdata1['first'] = Items::getProdutData($session_id);
	    $getdata2['second'] = Items::getProdutData($session_id);
	    $getdata3['third'] = Items::getProdutZip($session_id);
		$getdata4['four'] = Items::getProdutData($session_id);
		$getdata5['five'] = Items::getProdutMP4($session_id);
		$getdata6['six'] = Items::getProdutMP3($session_id);
		$item_token = "";
		$re_categories['menu'] = Category::with('SubCategory')->where('category_status','=','1')->where('drop_status','=','no')->orderBy('menu_order',$allsettings->menu_categories_order)->get();
		if($this->custom_settings('author_key') == Helper::Key_Owner())
		{
		$other_market = Blog::getothermarketData();
		}
		else
		{
		$other_market = 0;
		}
		$data = array('re_categories' => $re_categories, 'itemWell' => $itemWell, 'attribute' => $attribute, 'type_id' => $type_id, 'type_name' => $type_name, 'available_storage' => $available_storage, 'getdata1' => $getdata1, 'getdata2' => $getdata2, 'getdata3' => $getdata3, 'getdata4' => $getdata4, 'getdata5' => $getdata5, 'getdata6' => $getdata6, 'item_token' => $item_token, 'other_market' => $other_market);
        return view('upload-item')->with($data);
    }
	
	
	
	public function generateRandomString($length = 25) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
    }
	
	
	
	public function update_items(Request $request)
	{
	   $session_id = Session::getId();
	   $item_name = $request->input('item_name');
	   $additional['settings'] = Settings::editAdditional();
	   if($additional['settings']->site_url_rewrite == 1)
	   {
	   $item_slug = $this->seo_slug($item_name);
	   }
	   else
	   {
	   $item_slug = $this->non_seo_slug($item_name);
	   }
	   $item_token = $request->input('item_token');
	   $item_desc = htmlentities($request->input('item_desc'));
	   $seller_refund_term = $request->input('seller_refund_term');
	   $item_category = $request->input('item_category');
	   $split = explode("_", $item_category);
         
       $cat_id = $split[1];
	   $cat_name = $split[0];
	   if($cat_name == 'subcategory')
	   {
	      $fet = Category::editsubcategoryData($cat_id);
		  $parent_category_id = $fet->cat_id;
	   }
	   else
	   {
	      $parent_category_id = $cat_id;
	   }
	   
	   $item_type = $request->input('item_type');
	   $type_id = $request->input('type_id');
	   $demo_url = $request->input('demo_url');
	   $item_tags = $request->input('item_tags');
	   $item_shortdesc = $request->input('item_shortdesc');
	   $free_download = $request->input('free_download');
	   
	   if($free_download == 1)
	   {
	   $regular_price = 0;
	   $extended_price = 0;
	   }
	   else
	   {
		   if(!empty($request->input('regular_price')))
		   {
		   $regular_price = $request->input('regular_price');
		   }
		   else
		   {
		   $regular_price = 0;
		   }
		   if(!empty($request->input('extended_price')))
		   {
		   $extended_price = $request->input('extended_price');
		   }
		   else
		   {
			$extended_price = 0;
		   }
	   }
	   
	   if(!empty($request->input('future_update')))
	   {
	   $future_update = $request->input('future_update');
	   }
	   else
	   {
	   $future_update = 0;
	   }
	   
	   if(!empty($request->input('item_support')))
	   {
	   $item_support = $request->input('item_support');
	   }
	   else
	   {
	   $item_support = 0;
	   }
	   
	   
	   $user_id = $request->input('user_id');
	   $item_flash_request = $request->input('item_flash_request');
	   $allsettings = Settings::allSettings();
	   $item_approval = $allsettings->item_approval;
	   $additional_settings = Settings::editAdditional();
	   
	   $seller_money_back = $request->input('seller_money_back');
	   if(!empty($request->input('seller_money_back_days')))
	   {
	   $seller_money_back_days = $request->input('seller_money_back_days');
	   }
	   else
	   {
	   $seller_money_back_days = 0;
	   }
	   
	   $item_allow_seo = $request->input('item_allow_seo');
	   if($request->input('item_seo_keyword') != "")
		 {
		 $item_seo_keyword = $request->input('item_seo_keyword');
		 }
		 else
		 {
		 $item_seo_keyword = "";
		 }
		 if($request->input('item_seo_desc') != "")
		 {
		 $item_seo_desc = $request->input('item_seo_desc');
		 }
		 else
		 {
		 $item_seo_desc = "";
		 }
		if(!empty($request->input('item_delimiter')))
	   {
	   $item_delimiter = $request->input('item_delimiter');
	   }
	   else
	   {
	   $item_delimiter = $request->input('save_item_delimiter');  
	   }
	   
	   if($item_delimiter == 'comma')
	   {
	      $data_list = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $request->input('item_serials_list'));
		  $item_serials_list = $data_list.",";
	   }
	   else
	   {
	      $data_list = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $request->input('item_serials_list'));
	      $item_serials_list = $data_list."\n";
	   }
	    
	   
	   if($item_approval == 1)
	   {
	      $item_status = 1;
		  $item_approve_status = "Your item updated successfully.";
	   }
	   else
	   {
	      $item_status = 0;
		  $item_approve_status = "Thanks for your submission. Once admin will approved your item. will publish on our marketplace.";
	   }
	   
	   $watermark = $allsettings->site_watermark;
	   $image_size = $allsettings->site_max_image_size;
	   $file_size = $allsettings->site_max_file_size;
	   $url = URL::to("/");
	   
	   
	   
	   
	   if(!empty($request->input('item_thumbnail1')))
	   {
		 $item_thumbnail = $request->input('item_thumbnail1');
		 $field_value = 'item_thumbnail';
		 Items::singleDroper($field_value,$item_token);
	   }
	   else if(!empty($request->input('item_thumbnail2')))
	   {
		 $item_thumbnail = $request->input('item_thumbnail2');
		 $field_value = 'item_thumbnail';
		 Items::singleDroper($field_value,$item_token);
	   }
	   else
	   {
	     $item_thumbnail = $request->input('save_thumbnail');
	   }
	   if(!empty($request->input('item_preview1')))
	   {
		 $item_preview = $request->input('item_preview1');
		 $field_value = 'item_preview';
		 Items::singleDroper($field_value,$item_token);
	   }
	   else if(!empty($request->input('item_preview2')))
	   {
		 $item_preview = $request->input('item_preview2');
		 $field_value = 'item_preview';
		 Items::singleDroper($field_value,$item_token);
	   }
	   else
	   {
	     $item_preview = $request->input('save_preview');
	   }
	   if(!empty($request->input('item_file1')))
	   {
		 $item_file = $request->input('item_file1');
		 $field_value = 'item_file';
		 Items::fineDroper($field_value,$item_token);
	   }
	   else if(!empty($request->input('item_file2')))
	   {
		 $item_file = $request->input('item_file2');
		 $field_value = 'item_file';
		 Items::fineDroper($field_value,$item_token);
	   }
	   else
	   {
	     $item_file = $request->input('save_file');
	   }
	   if(!empty($request->input('file_type1')))
	   {
	   $file_type = $request->input('file_type1');
	   }
	   else if(!empty($request->input('file_type2')))
	   {
	   $file_type = $request->input('file_type2');
	   }
	   else
	   {
	   $file_type = $request->input('save_file_type');
	   }
	   
	   
	   if(!empty($request->input('item_file_link1')))
	   {
	   $item_file_link = $request->input('item_file_link1');
	   }
	   else
	   {
	   $item_file_link = $request->input('item_file_link2');;  
	   }
	   if(!empty($request->input('item_screenshot')))
	   {
	      
		  $gallery1 = "";
		  foreach($request->input('item_screenshot') as $gallery)
		  {
		     $gallery1 .= $gallery.',';
		  }
		  $item_screenshot = rtrim($gallery1,",");
		  
	   }
	   else
	   {
	     $item_screenshot = "";
	   }
	   if(!empty($request->input('video_preview_type1')))
	   {
	   $video_preview_type = $request->input('video_preview_type1');
	   }
	   else if(!empty($request->input('video_preview_type2')))
	   {
	   $video_preview_type = $request->input('video_preview_type2');
	   }
	   else
	   {
	   $video_preview_type = "";
	   }
	   if(!empty($request->input('video_url1')))
	   {
	   $video_url = $request->input('video_url1');
	   }
	   else if(!empty($request->input('video_url2')))
	   {
	   $video_url = $request->input('video_url2');
	   }
	   else
	   {
	     $video_url = $request->input('save_video_url');
	   } 
	   if(!empty($request->input('video_file1')))
	   {
	   $video_file = $request->input('video_file1');
	   $field_value = 'video_file';
	   Items::fineDroper($field_value,$item_token);
	   }
	   else if(!empty($request->input('video_file2')))
	   {
	   $video_file = $request->input('video_file2');
	   $field_value = 'video_file';
	   Items::fineDroper($field_value,$item_token);
	   }
	   else
	   {
	   $video_file = $request->input('save_video_file');  
	   }
	   
	   if(!empty($request->input('audio_file1')))
	   {
	   $audio_file = $request->input('audio_file1');
	   $field_value = 'audio_file';
	   Items::fineDroper($field_value,$item_token);
	   }
	   else if(!empty($request->input('audio_file2')))
	   {
	   $audio_file = $request->input('audio_file2');
	   $field_value = 'audio_file';
	   Items::fineDroper($field_value,$item_token);
	   }
	   else
	   {
	   $audio_file = $request->input('save_audio_file');  
	   }
	   
	    if($free_download == 1)
		{
		$subscription_item = 0;
		} 
		else
		{
		$subscription_item = $request->input('subscription_item'); 
		}
	  
	  $item_reviewer = $request->input('item_reviewer');
	  
	  
	  
	  $request->validate([
									'item_name' => 'required',
									'item_desc' => 'required',
									
									
				 ]);
	  $rules = array(
				
				'item_name' => ['required', 'max:100', Rule::unique('items') ->ignore($item_token, 'item_token') -> where(function($sql){ $sql->where('drop_status','=','no');})],
				
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
		    
		  
		  
		   
		 $updated_item = date('Y-m-d H:i:s'); 
		
		
		    $data = array('item_name' => $item_name, 'item_desc' => $item_desc, 'item_thumbnail' => $item_thumbnail, 'item_preview' => $item_preview, 'item_file' => $item_file, 'file_type' => $file_type, 'item_file_link' => $item_file_link, 'item_category' =>$cat_id, 'item_category_parent' => $parent_category_id, 'item_category_type' => $cat_name, 'item_type' => $item_type, 'regular_price' => $regular_price, 'extended_price' => $extended_price, 'demo_url' => $demo_url, 'item_tags' => $item_tags, 'item_status' => $item_status, 'item_shortdesc' => $item_shortdesc, 'free_download' => $free_download, 'item_slug' => $item_slug, 'video_url' => $video_url, 'future_update' => $future_update, 'item_support' => $item_support, 'updated_item' => $updated_item, 'item_flash_request' => $item_flash_request, 'video_preview_type' => $video_preview_type, 'video_file' => $video_file, 'item_type_cat_id' => $item_category, 'seller_refund_term' => $seller_refund_term, 'seller_money_back' => $seller_money_back, 'seller_money_back_days' => $seller_money_back_days, 'item_allow_seo' => $item_allow_seo, 'item_seo_keyword' => $item_seo_keyword, 'item_seo_desc' => $item_seo_desc, 'audio_file' => $audio_file, 'item_type_id' => $type_id, 'subscription_item' => $subscription_item, 'item_delimiter' => $item_delimiter, 'item_serials_list' => $item_serials_list, 'item_reviewer' => $item_reviewer);
			
		    Items::updateitemData($item_token,$data);
			
			Items::dropAttribute($item_token);
			
			$attribute['fields'] = Attribute::selectedAttribute($type_id);
			   foreach($attribute['fields'] as $attribute_field)
			   {
				   $multiple = $request->input('attributes_'.$attribute_field->attr_id);
				   if(isset($multiple))
				   {
					   if(count($multiple) != 0)
					   {
						   $attributes = "";
						   foreach($multiple as $browser)
						   {
							 $attributes .= $browser.',';
							 
						   }
						   $attributes_values = rtrim($attributes,",");
						   $data = array( 'item_token' => $item_token, 'attribute_id' => $attribute_field->attr_id, 'item_attribute_label' => $attribute_field->attr_label, 'item_attribute_values' => $attributes_values);
						   Items::saveAttribute($data);
					  }	 
				  }  
			   }
			
			if(!empty($item_screenshot))
			{   
				$var=explode(',',$item_screenshot);
				foreach($var as $row)
				{
					$imgdata = array('item_token' => $item_token, 'item_image' => $row);
					Items::saveitemImages($imgdata);
				}
			}	
			Items::forceDATA($session_id);
            if($this->custom_settings('author_key') == Helper::Key_Owner())
			{
			if(!empty($request->input('iom_market_link')))
			{
			    $codes = $request->input('im_id');
				$url = $request->input('iom_market_link');
				foreach( $codes as $index => $code ) 
				{
				   $checkMarketdata = Blog::checkMarketDetails($item_token,$code);
				   if($checkMarketdata != 0)
				   {
				   $market_data = array('iom_market_link' => $url[$index]);
				   Blog::upMarketDetails($item_token,$code,$market_data);
				   }
				   else
				   {
				   $market_data = array('iom_item_token' => $item_token, 'im_id' => $code, 'iom_market_link' => $url[$index]);
				   Blog::saveMarketDetails($market_data);
				   }
				   
				} 
			}
		 }
         $getvendor['user'] = Members::singlebuyerData($user_id);
		 $token = $request->input('item_token');
		 $itemdata['data'] = Items::edititemData($token);
		 $item_id = $itemdata['data']->item_id;
		 if($this->custom_settings('author_key') == Helper::Key_Owner())
		 {
		 /* Notification Message */
		 $fin_itemnames = mb_substr($item_name, 0, 30, 'UTF-8').'...';
		 $noti_date = date('Y-m-d H:i:s');
		 $noti_sender_id = $user_id;
		 $noti_receiver_id = 1;
		 $noti_message = '<strong style="text-transform:capitalize;">'.$getvendor['user']->name.'</strong> updated on product <strong>'.$fin_itemnames.'</strong>';
		 $savedata = array('noti_date' => $noti_date, 'noti_sender_id' => $noti_sender_id, 'noti_receiver_id' => $noti_receiver_id, 'noti_message' => $noti_message, 'noti_type' => 'update-item', 'noti_type_id' => $item_id);
		 Items::saveNotification($savedata);
		 /* Notification Message */
		 }
		 $item_slug = $itemdata['data']->item_slug;
		 $item_url = URL::to('/item').'/'.$item_slug;
		 $check_email_support = Members::getuserSubscription($user_id);
		 if($getvendor['user']->item_update_email == 1)
		 {
		    if($check_email_support == 1)
			{
				  $sid = 1;
				  $setting['setting'] = Settings::editGeneral($sid);
				  $admin_name = $setting['setting']->sender_name;
				  $admin_email = $setting['setting']->sender_email;
				  $record = array('item_url' => $item_url);
				  $checkdata['order'] = Items::getorderStatus($item_id,$user_id);
				  foreach($checkdata['order'] as $order)
				  { 
				  $to_name = $order->username;
				  $to_email = $order->email;
				  /* email template code */
	          		$checktemp = EmailTemplate::checkTemplate(5);
			  		if($checktemp != 0)
			  		{
			  		$template_view['mind'] = EmailTemplate::viewTemplate(5);
			  		$template_subject = $template_view['mind']->et_subject;
			  		}
			  		else
			  		{
			  		$template_subject = "Item Update Notifications";
			  		}
			  	/* email template code */
				  Mail::send('item_update_mail', $record, function($message) use ($admin_name, $admin_email, $to_email, $to_name, $template_subject) {
						$message->to($to_email, $to_name)
								->subject($template_subject);
						$message->from($admin_email,$admin_name);
					});
			  }
			  		
		  }	
			
			
			
		 
		 }     
			
			
			return redirect('/manage-item')->with('success', $item_approve_status);
		
		
		}
	   
	   
	   
	   
	   
	   
	   
	
	}
	
	
	
	public function changeType($size, $from, $to){
        $arr = ['B', 'KB', 'MB', 'GB', 'TB'];
        $tSayi = array_search($to, $arr);
        $eSayi = array_search($from, $arr);
        $pow = $eSayi - $tSayi;
        /*return $size * pow(1024, $pow) . ' ' . $to;*/
		return $size * pow(1024, $pow);
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
	
	public function save_items(Request $request)
	{
	   $session_id = Session::getId();
	   $item_delimiter = $request->input('item_delimiter');
	   if($item_delimiter == 'comma')
	   {
	      $data_list = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $request->input('item_serials_list'));
		  $item_serials_list = $data_list.",";
	   }
	   else
	   {
	      $data_list = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $request->input('item_serials_list'));
	      $item_serials_list = $data_list."\n";
	   }
	   $item_name = $request->input('item_name');
	   $additional['settings'] = Settings::editAdditional();
	   if($additional['settings']->site_url_rewrite == 1)
	   {
	   $item_slug = $this->seo_slug($item_name);
	   }
	   else
	   {
	   $item_slug = $this->non_seo_slug($item_name);
	   }
	   $item_desc = htmlentities($request->input('item_desc'));
	   $seller_refund_term = $request->input('seller_refund_term');
	   $item_category = $request->input('item_category');
	   
       $split = explode("_", $item_category);
         
       $cat_id = $split[1];
	   $cat_name = $split[0];
	   if($cat_name == 'subcategory')
	   {
	      $fet = Category::editsubcategoryData($cat_id);
		  $parent_category_id = $fet->cat_id;
	   }
	   else
	   {
	      $parent_category_id = $cat_id;
	   }
	   
	   
	   $item_type = $request->input('item_type');
	   $type_id = $request->input('type_id');
	   $demo_url = $request->input('demo_url');
	   $item_tags = $request->input('item_tags');
	   
	   $item_shortdesc = $request->input('item_shortdesc');
	   $free_download = $request->input('free_download');
	   $video_url = $request->input('video_url');
	   $created_item = date('Y-m-d H:i:s');
	   $updated_item = date('Y-m-d H:i:s');
	   
	   
	   
	   if(!empty($request->input('regular_price')))
	   {
	   $regular_price = $request->input('regular_price');
	   }
	   else
	   {
	   $regular_price = 0;
	   }
	   if(!empty($request->input('extended_price')))
	   {
	   $extended_price = $request->input('extended_price');
	   }
	   else
	   {
	    $extended_price = 0;
	   }
	   
	   if(!empty($request->input('future_update')))
	   {
	   $future_update = $request->input('future_update');
	   }
	   else
	   {
	   $future_update = 0;
	   }
	   
	   if(!empty($request->input('item_support')))
	   {
	   $item_support = $request->input('item_support');
	   }
	   else
	   {
	   $item_support = 0;
	   }
	   $item_flash_request = $request->input('item_flash_request');
	   
	   $user_id = $request->input('user_id');
	   $item_token = $this->generateRandomString();
	   $allsettings = Settings::allSettings();
	   $item_approval = $allsettings->item_approval;
	   
	   
	   if(!empty($request->input('item_thumbnail1')))
	   {
		 $item_thumbnail = $request->input('item_thumbnail1');
	   }
	   else
	   {
		 $item_thumbnail = $request->input('item_thumbnail2');
	   }
	   if(!empty($request->input('item_preview1')))
	   {
		 $item_preview = $request->input('item_preview1');
	   }
	   else
	   {
		 $item_preview = $request->input('item_preview2');
	   }
	   if(!empty($request->input('item_file1')))
	   {
		 $item_file = $request->input('item_file1');
	   }
	   else
	   {
		 $item_file = $request->input('item_file2');
	   }
	   if(!empty($request->input('file_type1')))
	   {
		 $file_type = $request->input('file_type1');
	   }
	   else
	   {
		 $file_type = $request->input('file_type2');
	   }
	   if(!empty($request->input('item_file_link1')))
	   {
	   $item_file_link = $request->input('item_file_link1');
	   }
	   else
	   {
	   $item_file_link = $request->input('item_file_link2');;  
	   }
	   if(!empty($request->input('item_screenshot')))
	   {
	      
		  $gallery1 = "";
		  foreach($request->input('item_screenshot') as $gallery)
		  {
		     $gallery1 .= $gallery.',';
		  }
		  $item_screenshot = rtrim($gallery1,",");
		  
	   }
	   else
	   {
	     $item_screenshot = "";
	   }
	   if(!empty($request->input('video_preview_type1')))
	   {
	   $video_preview_type = $request->input('video_preview_type1');
	   }
	   else
	   {
	   $video_preview_type = $request->input('video_preview_type2');
	   }
	   
	   if(!empty($request->input('video_url1')))
	   {
	   $video_url = $request->input('video_url1');
	   }
	   else
	   {
	   $video_url = $request->input('video_url2');
	   }
	    
	   if(!empty($request->input('video_file1')))
	   {
	   $video_file = $request->input('video_file1');
	   }
	   else
	   {
	   $video_file = $request->input('video_file2');
	   }
	   
	   if(!empty($request->input('audio_file1')))
	   {
	   $audio_file = $request->input('audio_file1');
	   }
	   else
	   {
	   $audio_file = $request->input('audio_file2');
	   }	
	   
	   $item_allow_seo = $request->input('item_allow_seo');
	   if($request->input('item_seo_keyword') != "")
		 {
		 $item_seo_keyword = $request->input('item_seo_keyword');
		 }
		 else
		 {
		 $item_seo_keyword = "";
		 }
		 if($request->input('item_seo_desc') != "")
		 {
		 $item_seo_desc = $request->input('item_seo_desc');
		 }
		 else
		 {
		 $item_seo_desc = "";
		 }
	   
	   if($item_approval == 1)
	   {
	      $item_status = 1;
		  $item_approve_status = "Your item updated successfully.";
	   }
	   else
	   {
	      $item_status = 0;
		  $item_approve_status = "Thanks for your submission. Once admin will approved your item. will publish on our marketplace.";
	   }
	   
	   $seller_money_back = $request->input('seller_money_back');
	   if(!empty($request->input('seller_money_back_days')))
	   {
	   $seller_money_back_days = $request->input('seller_money_back_days');
	   }
	   else
	   {
	   $seller_money_back_days = 0;
	   }
	   
	   if($free_download == 1)
		{
		$subscription_item = 0;
		} 
		else
		{
		$subscription_item = $request->input('subscription_item'); 
		}
	   
	   $item_reviewer = $request->input('item_reviewer'); 
	   
	   $watermark = $allsettings->site_watermark;
	   $image_size = $allsettings->site_max_image_size;
	   $file_size = $allsettings->site_max_file_size;
	   $url = URL::to("/");
	   /*$occupy_size = $this->changeType($this->available_space(Auth::user()->id), 'B' ,'KB');*/
	   $additional_settings = Settings::editAdditional();
	   $request->validate([
									'item_name' => 'required',
									'item_desc' => 'required',
									
									
				 ]);
	   
		 $rules = array(
				
				'item_name' => ['required', 'max:100', Rule::unique('items') -> where(function($sql){ $sql->where('drop_status','=','no');})],
				
				
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
		   
		  
		    
			  
			
		  $data = array('user_id' => $user_id, 'item_token' => $item_token, 'item_name' => $item_name, 'item_desc' => $item_desc, 'item_thumbnail' => $item_thumbnail, 'item_preview' => $item_preview, 'item_file' => $item_file, 'file_type' => $file_type, 'item_file_link' => $item_file_link, 'item_category' =>$cat_id, 'item_category_parent' => $parent_category_id,  'item_category_type' => $cat_name, 'item_type' => $item_type, 'regular_price' => $regular_price, 'extended_price' => $extended_price, 'demo_url' => $demo_url, 'item_tags' => $item_tags, 'item_status' => $item_status, 'item_shortdesc' => $item_shortdesc, 'free_download' => $free_download, 'item_slug' => $item_slug, 'video_url' => $video_url, 'future_update' => $future_update, 'item_support' => $item_support, 'created_item' => $created_item, 'updated_item' => $updated_item, 'item_flash_request' => $item_flash_request, 'video_preview_type' => $video_preview_type, 'video_file' => $video_file, 'item_type_cat_id' => $item_category, 'seller_refund_term' => $seller_refund_term, 'seller_money_back' => $seller_money_back, 'seller_money_back_days' => $seller_money_back_days, 'item_allow_seo' => $item_allow_seo, 'item_seo_keyword' => $item_seo_keyword, 'item_seo_desc' => $item_seo_desc, 'audio_file' => $audio_file, 'item_type_id' => $type_id, 'subscription_item' => $subscription_item, 'item_delimiter' => $item_delimiter, 'item_serials_list' => $item_serials_list, 'item_reviewer' => $item_reviewer);
			
			
              
			$user_item_count = Items::checkItemUser(Auth::user()->id); 
		  if(Auth::user()->user_subscr_item_level == 'limited')
		  {
		     if(Auth::user()->user_subscr_item > $user_item_count)
			 {  
			  
		           Items::saveitemData($data);
			       $attribute['fields'] = Attribute::selectedAttribute($type_id);
				   foreach($attribute['fields'] as $attribute_field)
				   {
					   if($request->input('attributes_'.$attribute_field->attr_id))
					   {
						$multiple = $request->input('attributes_'.$attribute_field->attr_id);
						if(count($multiple) != 0)
						{
						   $attributes = "";
						   foreach($multiple as $browser)
						   {
							 $attributes .= $browser.',';
							 
						   }
						   $attributes_values = rtrim($attributes,",");
						   $data = array( 'item_token' => $item_token, 'attribute_id' => $attribute_field->attr_id, 'item_attribute_label' => $attribute_field->attr_label, 'item_attribute_values' => $attributes_values);
						   Items::saveAttribute($data);
						}	
					  }   
				   }
			}
			else
			{
			  return redirect()->back()->with('error', 'Sorry! Your items limit reached.');
			}
		}
		else
		{
		     Items::saveitemData($data);
		     $attribute['fields'] = Attribute::selectedAttribute($type_id);
				   foreach($attribute['fields'] as $attribute_field)
				   {
					   if($request->input('attributes_'.$attribute_field->attr_id))
					   {
						$multiple = $request->input('attributes_'.$attribute_field->attr_id);
						if(count($multiple) != 0)
						{
						   $attributes = "";
						   foreach($multiple as $browser)
						   {
							 $attributes .= $browser.',';
							 
						   }
						   $attributes_values = rtrim($attributes,",");
						   $data = array( 'item_token' => $item_token, 'attribute_id' => $attribute_field->attr_id, 'item_attribute_label' => $attribute_field->attr_label, 'item_attribute_values' => $attributes_values);
						   Items::saveAttribute($data);
						}	
					  }   
				   }
		}	
		if(!empty($item_screenshot))
			{
				$var=explode(',',$item_screenshot);
				foreach($var as $row)
				{
					$imgdata = array('item_token' => $item_token, 'item_image' => $row);
					Items::saveitemImages($imgdata);
				}
		}
		Items::forceDATA($session_id);
		if($this->custom_settings('author_key') == Helper::Key_Owner())
		{
			if(!empty($request->input('iom_market_link')))
			{
			    $codes = $request->input('im_id');
				$url = $request->input('iom_market_link');
				foreach( $codes as $index => $code ) 
				{
				   $market_data = array('iom_item_token' => $item_token, 'im_id' => $code, 'iom_market_link' => $url[$index]);
				   Blog::saveMarketDetails($market_data);
				   
				} 
			}
		}
		/* Notification Message */
		if($this->custom_settings('author_key') == Helper::Key_Owner())
		{
		$fin_itemnames = mb_substr($item_name, 0, 30, 'UTF-8').'...';
		$noti_date = date('Y-m-d H:i:s');
		$noti_sender_id = $user_id;
		$noti_receiver_id = 1;
		$getvendor['user'] = Members::singlebuyerData($user_id);
		$itemdata['data'] = Items::edititemData($item_token);
		$item_id = $itemdata['data']->item_id;	
		$noti_message = '<strong style="text-transform:capitalize;">'.$getvendor['user']->name.'</strong> created new product <strong>'.$fin_itemnames.'</strong>';
		$savedata = array('noti_date' => $noti_date, 'noti_sender_id' => $noti_sender_id, 'noti_receiver_id' => $noti_receiver_id, 'noti_message' => $noti_message, 'noti_type' => 'create-item', 'noti_type_id' => $item_id);
		Items::saveNotification($savedata);
		}
		/* Notification Message */	
		return redirect('/manage-item')->with('success', $item_approve_status);
		
		
		}
	   
	   
	
	}
	
	
	
	
	
	public function contact_support(Request $request)
	{
	   $support_subject = $request->input('support_subject');
	   $support_msg = $request->input('support_msg');
	   $to_email = $request->input('to_address');
	   $from_email = $request->input('from_address');
	   $to_name = $request->input('to_name');
	   $from_name = $request->input('from_name');
	   $item_url = $request->input('item_url');
	   $user_id = $request->input('to_id');
	   $check_email_support = Members::getuserSubscription($user_id);
	   
	    $sid = 1;
		$setting['setting'] = Settings::editGeneral($sid);
		
		$admin_name = $setting['setting']->sender_name;
        $admin_email = $setting['setting']->sender_email;
		
		$record = array('to_name' => $to_name, 'from_name' => $from_name, 'from_email' => $from_email, 'item_url' => $item_url, 'support_msg' => $support_msg, 'support_subject' => $support_subject);
		/* email template code */
	          	$checktemp = EmailTemplate::checkTemplate(10);
			  	if($checktemp != 0)
			  	{
			  	$template_view['mind'] = EmailTemplate::viewTemplate(10);
			  	$template_subject = $template_view['mind']->et_subject;
			  	}
			  	else
			  	{
			  	$template_subject = "Contact Support";
			  	}
			  	/* email template code */
		Mail::send('support_mail', $record, function($message) use ($admin_name, $admin_email, $to_email, $from_email, $to_name, $from_name, $template_subject) {
			$message->to($admin_email, $admin_name)
					->subject($template_subject);
			$message->from($from_email,$from_name);
		});
		
		if($check_email_support == 1)
	    {
			Mail::send('support_mail', $record, function($message) use ($admin_name, $admin_email, $to_email, $from_email, $to_name, $from_name, $template_subject) {
				$message->to($to_email, $to_name)
						->subject($template_subject);
				$message->from($from_email,$from_name);
			});
		}
	   
	  return redirect()->back()->with('success', 'Thank You! Your message sent successfully'); 
	  
	
	}
	
	
	
	
	
	/* checkout */
	
	
	
	public function confirm_2checkout(Request $request)
	{
	
	   $token = $request->input('token');
	   $user_id = $request->input('user_id');
	   $user_name = $request->input('user_name');
	   $user_email = $request->input('user_email');
	   $product_names = $request->input('product_names');
	   $amount = base64_decode($request->input('amount'));
	   $site_currency = $request->input('site_currency');
	   $two_checkout_private = $request->input('two_checkout_private');
	   $two_checkout_account = $request->input('two_checkout_account');
	   $two_checkout_mode = $request->input('two_checkout_mode');
	   $purchase_token = $request->input('purchase_token');
	   $user_phone = rand(444444,999999);
	   $sid = 1;
	   $setting['setting'] = Settings::editGeneral($sid);
	   
	   include(app_path() . '\2Checkout\Twocheckout.php'); 
	        Twocheckout::sellerId('fdsafdsafd'); 
						Twocheckout::privateKey('fdsafdafdsa'); 
		                
						try { 
		$merchantOrderID 	= 1;
            $charge = Twocheckout_Charge::auth(array(
                "merchantOrderId" => $merchantOrderID,
                "token"      	  => $token,
                "currency"   	  => $site_currency,
                "total"      	  => $amount,
                "billingAddr" 	  => array(
                    "name" 		  => "Jenny Doe",
                    "addrLine1"   => "house no 245 street no 8 Lahore",
                    "city" 		  => "Pakistan",
                    "state" 	  => "punjab",
                    "zipCode" 	  => "54000",
                    "country" 	  => "Pakistan",
                    "email" 	  => "abc@gmail.com",
                    //"phoneNumber" => $post_data['phoneNumber']
                ),
				"demo"=> true,
            ));
			
			if ($charge['response']['responseCode'] == 'APPROVED') 
			{
				$statusMsg = "";
				$statusMsg = '<h2>Thanks for your Order!</h2>';
				$statusMsg .= '<h4>The transaction was successful. Order details are given below:</h4>';
				$statusMsg .= "<p>Response Code: ".$charge['response']['responseCode']."</p>";
				$statusMsg .= "<p>Order ID: ".$charge['response']['orderNumber']."</p>";
				$statusMsg .= "<p>Transaction ID: ".$charge['response']['transactionId']."</p>";
				$statusMsg .= "<p>Order Total: ".$charge['response']['total']." ".CURRENCY_CODE."</p>";
			}
			else
			{
				$statusMsg = "";
				$statusMsg = '<h2>OOPS! Transaction Faild</h2>';
				$statusMsg .= '<h4>The transaction was not successful. Response code is given below:</h4>';
				$statusMsg .= "<p>Response Code: ".$charge['response']['responseCode']."</p>";
			}
			
			return $statusMsg; 
			
        }catch(Exception $e) { 
            return "<p>API Errors: {$e->getMessage()}</p>";
        }
		                //Twocheckout::sandbox($two_checkout_mode);
						//Twocheckout::verifySSL(false); 
						/*try {
							$charge = Twocheckout_Charge::auth(array(
								"merchantOrderId" => $purchase_token,
								"token"      => $token,
								"currency"   => $site_currency,
								"total"      => $amount,
								"billingAddr" => array(
									"name" => $user_name,
									"addrLine1" => $user_name,
									"city" => $user_name,
									"state" => "US",
									"zipCode" => $user_phone,
									"country" => "US",
									"email" => $user_email
									//"phoneNumber" => $user_phone
								),
								"demo"=> true,
							));
							
						if ($charge['response']['responseCode'] == 'APPROVED')
			            {
						
							$payment_token = $charge['response']['transactionId'];
							 $payment_status = 'completed';
							$purchased_token = $purchase_token;
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
							/*if($setting['setting']->payment_verification == 0)
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
								  $currency = $this->site_currency();
								  $check_email_support = Members::getuserSubscription($vendor['info']->id);
								  if($check_email_support == 1)
	                              {
									  $record_data = array('to_name' => $to_name, 'to_email' => $to_email, 'vendor_amount' => $vendor_amount, 'currency' => $currency);
									  Mail::send('admin.vendor_payment_mail', $record_data , function($message) use ($admin_name, $admin_email, $to_name, $to_email) {
												$message->to($to_email, $to_name)
														->subject('New Payment Approved');
												$message->from($admin_email,$admin_name);
											});
							      }
							   
							}
							/* manual payment verification : OFF */
							
							
						 /* }
						
						
						$data_record = array('payment_token' => $payment_token);
						return view('success')->with($data_record);
					
					
				   }
				   else
				   {
						   return redirect('/cancel');
				   }
			} 
			catch (Twocheckout_Error $e)
			{
						
				print $e->getMessage();
				
			}*/
	
	
	}
	
	
	public function deposit_razorpay_payment(Request $request)
    {
	    $sid = 1;
	    $setting['setting'] = Settings::editGeneral($sid);
		$additional['settings'] = Settings::editAdditional();
        $input = $request->all();

        $api = new Api($additional['settings']->razorpay_key, $additional['settings']->razorpay_secret);

        $payment = $api->payment->fetch($input['razorpay_payment_id']);
        
        $user_id = Auth::user()->id;

        //dd($paymentDetails);
         //print_r($paymentDetails);
		if(count($input)  && !empty($input['razorpay_payment_id'])) 
		{
		   
		            $payment_token = $input['razorpay_payment_id'];
					$purchased_token = $payment->description;
					/* deposite details */
					$payment_date = date('Y-m-d');
					$payment_status = 'completed';
					$updatedata = array('payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_status' => $payment_status);
					Deposit::upDepositdata($purchased_token,$updatedata);
					/* deposite details */
					$additional_view = Settings::editAdditional();
					$amount = $additional_view->deposit_amount;
					$payment_type = $additional_view->deposit_type;
					$user_token = Auth::user()->user_token;
					$buyer_details = Members::editData($user_token);
					$wallet = $buyer_details->earnings + $amount;
					$data = array('earnings' => $wallet);
					Members::updateData($user_token,$data);
					/* currency */
					$deposit_details = Deposit::displaydepositDetails($purchased_token);
					$currency = Currencies::getCurrency($deposit_details->currency_type_code);
					$currency_symbol = $currency->currency_symbol;
					$currency_rate = $currency->currency_rate;
					$amount = $amount * $currency_rate;
					$currency = $currency->currency_symbol;
					/* currency */
					$buyer['info'] = Members::singlevendorData(Auth::user()->id);	
					$buyer_name = $buyer['info']->name;
					$buyer_email = $buyer['info']->email;
					
					$admin_name = $setting['setting']->sender_name;
					$admin_email = $setting['setting']->sender_email;
					$buyer_data = array('buyer_name' => $buyer_name, 'buyer_email' => $buyer_email, 'currency' => $currency, 'amount' => $amount,  'payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_type' => $payment_type);
					/* email template code */
	          		$checktemp = EmailTemplate::checkTemplate(19);
					if($checktemp != 0)
					{
					$template_view['mind'] = EmailTemplate::viewTemplate(19);
					$template_subject = $template_view['mind']->et_subject;
					}
					else
					{
					$template_subject = "New Deposit Details";
					}
			  	    /* email template code */
					Mail::send('deposit_mail', $buyer_data , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
						$message->to($buyer_email, $buyer_name)
							->subject($template_subject);
						$message->from($admin_email,$admin_name);
					});
						
					$clean = array('deposit_amount' => '', 'deposit_type' => '');
					Settings::updateAdditionData($clean);
					$data_record = array('payment_token' => $payment_token);
					if($this->custom_settings('author_key') == Helper::Key_Owner())
					{
					/* Notification Message */
					$deposit_details = Deposit::displaydepositDetails($purchased_token);
					$noti_date = date('Y-m-d H:i:s');
					$noti_sender_id = $buyer['info']->id;
					$noti_receiver_id = 1;
					$noti_message = 'New deposit received send by <strong>'.$buyer['info']->name.'</strong>';
					$savedata = array('noti_date' => $noti_date, 'noti_sender_id' => $noti_sender_id, 'noti_receiver_id' => $noti_receiver_id, 'noti_message' => $noti_message, 'noti_type' => 'deposit', 'noti_type_id' => $deposit_details->dd_id);
					Items::saveNotification($savedata);
					/* Notification Message */
					}
					return view('success')->with($data_record);
     
		}
		else
		{
		  return redirect('/cancel');
		}
		
        
    }
	
	
	public function stripe_callback($user_id,$purchase_token,Request $request)
    {
	    $encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
	    $item_user_id   = $encrypter->decrypt($user_id);
	    $sid = 1;
	    $setting['setting'] = Settings::editGeneral($sid);
	    $count_mode = Settings::checkuserSubscription($item_user_id);
		$vendor_details = Members::singlevendorData($item_user_id);
		$additional['setting'] = Settings::editAdditional();
		
	    $website_url = URL::to('/');
		$site_currency = $this->site_currency(); 
        
                    $payment_token = '';
				    $purchased_token = $encrypter->decrypt($purchase_token);
				    $payment_status = 'completed';
					$orderdata = array('payment_token' => $payment_token, 'order_status' => $payment_status);
					$checkoutdata = array('payment_token' => $payment_token, 'payment_status' => $payment_status);
					Items::singleordupdateData($purchased_token,$orderdata);
					Items::singlecheckoutData($purchased_token,$checkoutdata);
					
					$token = $purchased_token;
					$check['display'] = Items::getcheckoutData($token);
					/* customer email */
					$currency = $this->site_currency();
					$admin_name = $setting['setting']->sender_name;
					$admin_email = $setting['setting']->sender_email;
					$customer['info'] = Members::singlevendorData($check['display']->user_id);
					$buyer_name = $customer['info']->name;
					$buyer_email = $customer['info']->email;
					$amount = $check['display']->total;
					$order_id = $check['display']->purchase_token;
					$payment_type = $check['display']->payment_type;
					$payment_date = $check['display']->payment_date;
					$payment_status = $check['display']->payment_status;
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
														  /* email template code */
							Mail::send('item_purchase_mail', $record_customer , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
												$message->to($buyer_email, $buyer_name)
														->subject($template_subject);
												$message->from($admin_email,$admin_name);
											});
					/* customer email */
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
						
						
						$additional['setting'] = Settings::editAdditional();
						$ordered['data'] = Items::singleorderData($order);
						$item_user_id = $ordered['data']->item_user_id;
						$vendor['info'] = Members::singlevendorData($item_user_id);
						$to_name = $vendor['info']->name;
						$to_email = $vendor['info']->email;
						$vendor_amount = $ordered['data']->vendor_amount;
						$count_mode = Settings::checkuserSubscription($item_user_id);
						
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
									  
									  Mail::send('admin.vendor_payment_mail', $record_data , function($message) use ($admin_name, $admin_email, $to_name, $to_email, $template_subject) {
												$message->to($to_email, $to_name)
														->subject($template_subject);
												$message->from($admin_email,$admin_name);
											});
								  }
							 
							  }
							  
							  
						   }
						   
						   
						}	
						
												
						
					  }
					
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
	
	
	public function razorpay_payment(Request $request)
    {
	    $sid = 1;
	    $setting['setting'] = Settings::editGeneral($sid);
		$additional['settings'] = Settings::editAdditional();
        $input = $request->all();

        $api = new Api($additional['settings']->razorpay_key, $additional['settings']->razorpay_secret);

        $payment = $api->payment->fetch($input['razorpay_payment_id']);
        
        $user_id = Auth::user()->id;

        //dd($paymentDetails);
         //print_r($paymentDetails);
		if(count($input)  && !empty($input['razorpay_payment_id'])) 
		{
		
		 $payment_token = $input['razorpay_payment_id'];
		 $purchased_token = $payment->description;
		 $payment_status = 'completed';
		 $orderdata = array('payment_token' => $payment_token, 'order_status' => $payment_status);
		 $checkoutdata = array('payment_token' => $payment_token, 'payment_status' => $payment_status);
		 Items::singleordupdateData($purchased_token,$orderdata);
		 Items::singlecheckoutData($purchased_token,$checkoutdata);
		 $token = $purchased_token;
		 $check['display'] = Items::getcheckoutData($token);
		 /* customer email */
					$currency = $this->site_currency();
					$admin_name = $setting['setting']->sender_name;
					$admin_email = $setting['setting']->sender_email;
					$customer['info'] = Members::singlevendorData($check['display']->user_id);
					$buyer_name = $customer['info']->name;
					$buyer_email = $customer['info']->email;
					$amount = $check['display']->total;
					$order_id = $check['display']->purchase_token;
					$payment_type = $check['display']->payment_type;
					$payment_date = $check['display']->payment_date;
					$payment_status = $check['display']->payment_status;
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
														  /* email template code */
							Mail::send('item_purchase_mail', $record_customer , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
												$message->to($buyer_email, $buyer_name)
														->subject($template_subject);
												$message->from($admin_email,$admin_name);
											});
					/* customer email */
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
		else
		{
		  return redirect('/cancel');
		}
		
        
    }
	
	public function deposit_redirectToGateway()
    {
        return Paystack::getAuthorizationUrl()->redirectNow();
    }
	
	public function deposit_handleGatewayCallback()
    {
        $paymentDetails = Paystack::getPaymentData();
		$sid = 1;
	    $setting['setting'] = Settings::editGeneral($sid);

        //dd($paymentDetails);
         //print_r($paymentDetails);
		if (array_key_exists('data', $paymentDetails) && array_key_exists('status', $paymentDetails['data']) && ($paymentDetails['data']['status'] === 'success')) 
		{
	      
		            $payment_token = $paymentDetails['data']['reference'];
					$purchase_token = $paymentDetails['data']['metadata'];
					/* deposite details */
					$payment_date = date('Y-m-d');
					$payment_status = 'completed';
					$updatedata = array('payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_status' => $payment_status);
					Deposit::upDepositdata($purchase_token,$updatedata);
					/* deposite details */
					$additional_view = Settings::editAdditional();
					$amount = $additional_view->deposit_amount;
					$payment_type = $additional_view->deposit_type;
					$user_token = Auth::user()->user_token;
					$buyer_details = Members::editData($user_token);
					$wallet = $buyer_details->earnings + $amount;
					$data = array('earnings' => $wallet);
					Members::updateData($user_token,$data);
					/* currency */
					$deposit_details = Deposit::displaydepositDetails($purchase_token);
					$currency = Currencies::getCurrency($deposit_details->currency_type_code);
					$currency_symbol = $currency->currency_symbol;
					$currency_rate = $currency->currency_rate;
					$amount = $amount * $currency_rate;
					$currency = $currency->currency_symbol;
					/* currency */
					$buyer['info'] = Members::singlevendorData(Auth::user()->id);	
					$buyer_name = $buyer['info']->name;
					$buyer_email = $buyer['info']->email;
					
					$admin_name = $setting['setting']->sender_name;
					$admin_email = $setting['setting']->sender_email;
					$buyer_data = array('buyer_name' => $buyer_name, 'buyer_email' => $buyer_email, 'currency' => $currency, 'amount' => $amount,  'payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_type' => $payment_type);
					/* email template code */
	          		$checktemp = EmailTemplate::checkTemplate(19);
					if($checktemp != 0)
					{
					$template_view['mind'] = EmailTemplate::viewTemplate(19);
					$template_subject = $template_view['mind']->et_subject;
					}
					else
					{
					$template_subject = "New Deposit Details";
					}
			  	    /* email template code */
					Mail::send('deposit_mail', $buyer_data , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
						$message->to($buyer_email, $buyer_name)
							->subject($template_subject);
						$message->from($admin_email,$admin_name);
					});
						
					$clean = array('deposit_amount' => '', 'deposit_type' => '');
					Settings::updateAdditionData($clean);
					$data_record = array('payment_token' => $payment_token);
					if($this->custom_settings('author_key') == Helper::Key_Owner())
					{
					/* Notification Message */
					$deposit_details = Deposit::displaydepositDetails($purchase_token);
					$noti_date = date('Y-m-d H:i:s');
					$noti_sender_id = $buyer['info']->id;
					$noti_receiver_id = 1;
					$noti_message = 'New deposit received send by <strong>'.$buyer['info']->name.'</strong>';
					$savedata = array('noti_date' => $noti_date, 'noti_sender_id' => $noti_sender_id, 'noti_receiver_id' => $noti_receiver_id, 'noti_message' => $noti_message, 'noti_type' => 'deposit', 'noti_type_id' => $deposit_details->dd_id);
					Items::saveNotification($savedata);
					/* Notification Message */
					}
					return view('success')->with($data_record);

		  
		}
		else
		{
		  return redirect('/cancel');
		}
		
    }
		 
	
	
	public function redirectToGateway()
    {
        return Paystack::getAuthorizationUrl()->redirectNow();
    }
	
	public function handleGatewayCallback()
    {
        $paymentDetails = Paystack::getPaymentData();
		$sid = 1;
	    $setting['setting'] = Settings::editGeneral($sid);

        //dd($paymentDetails);
         //print_r($paymentDetails);
		if (array_key_exists('data', $paymentDetails) && array_key_exists('status', $paymentDetails['data']) && ($paymentDetails['data']['status'] === 'success')) 
		{
		 // echo "Transaction was successful - ".$paymentDetails['data']['reference']. ' - '.$paymentDetails['data']['metadata'];
		 
		 $payment_token = $paymentDetails['data']['reference'];
		 $purchase_token = $paymentDetails['data']['metadata'];
		 $payment_status = 'completed';
		 $purchased_token = $purchase_token;
		 $orderdata = array('payment_token' => $payment_token, 'order_status' => $payment_status);
		 $checkoutdata = array('payment_token' => $payment_token, 'payment_status' => $payment_status);
		 Items::singleordupdateData($purchased_token,$orderdata);
		 Items::singlecheckoutData($purchased_token,$checkoutdata);
		 $token = $purchased_token;
		 $check['display'] = Items::getcheckoutData($token);
		 /* customer email */
					$currency = $this->site_currency();
					$admin_name = $setting['setting']->sender_name;
					$admin_email = $setting['setting']->sender_email;
					$customer['info'] = Members::singlevendorData($check['display']->user_id);
					$buyer_name = $customer['info']->name;
					$buyer_email = $customer['info']->email;
					$amount = $check['display']->total;
					$order_id = $check['display']->purchase_token;
					$payment_type = $check['display']->payment_type;
					$payment_date = $check['display']->payment_date;
					$payment_status = $check['display']->payment_status;
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
														  /* email template code */
							Mail::send('item_purchase_mail', $record_customer , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
												$message->to($buyer_email, $buyer_name)
														->subject($template_subject);
												$message->from($admin_email,$admin_name);
											});
					/* customer email */
		 $order_id = $check['display']->order_ids;
		 $order_loop = explode(',',$order_id);
		 foreach($order_loop as $order)
		 {
							
							$getitem['item'] = Items::getorderData($order);
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
						/* manual payment verification : OFF */
											
		}
		/* referral per sale earning */
		$additional_setting = Settings::editAdditional();
		if($additional_setting->affiliate_referral == 1)
		{
			$logged_id = Auth::user()->id;
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
		else
		{
		  return redirect('/cancel');
		}
		
    }
	
	
	public function view_success()
	{
	  return view('success');
	}
	
	public function two_checkout_success(Request $request)
	{
	$payment_token = $request->input('invoice_id');
	$purchased_token = $request->input('merchant_order_id');
	$x_receipt_link_url = $request->input('x_receipt_link_url');
	$done_approval_status = $request->input('credit_card_processed');
	if($x_receipt_link_url == 'subscription')
	{
	   if($done_approval_status == "Y")
	   { 
	    $subscr_id = Auth::user()->user_subscr_id;
		$subscr['view'] = Subscription::editsubData($subscr_id);
		$subscri_date = $subscr['view']->subscr_duration;
		$user_subscr_item_level = $subscr['view']->subscr_item_level;
		$user_subscr_item = $subscr['view']->subscr_item;
		$user_subscr_download_item = $subscr['view']->subscr_download_item;
		$user_subscr_space_level = $subscr['view']->subscr_space_level;
		$user_subscr_space = $subscr['view']->subscr_space;
		$user_subscr_space_type = $subscr['view']->subscr_space_type;
		$user_subscr_type = $subscr['view']->subscr_name;
		$subscr_value = "+".$subscri_date;
		$subscr_date = date('Y-m-d', strtotime($subscr_value));
		$user_id = Auth::user()->id;
		$payment_status = 'completed';
		if(Auth::user()->user_type == 'customer')
		{
			$user_type = 'vendor';
		}
		else
		{
			$user_type = Auth::user()->user_type;
		}
		$checkoutdata = array('user_subscr_type' => $user_subscr_type, 'user_subscr_date' => $subscr_date, 'user_subscr_item_level' => $user_subscr_item_level, 'user_subscr_item' => $user_subscr_item, 'user_subscr_download_item' => $user_subscr_download_item, 'user_subscr_space_level' => $user_subscr_space_level, 'user_subscr_space' => $user_subscr_space, 'user_subscr_space_type' => $user_subscr_space_type, 'user_type' => $user_type, 'user_subscr_payment_status' => $payment_status);
		Subscription::confirmsubscriData($user_id,$checkoutdata);
		/* subscription email */
		$sid = 1;
		$setting['setting'] = Settings::editGeneral($sid);
		$currency = $this->site_currency();
		$subscr_price = $subscr['view']->subscr_price;
		$subscri_date = $subscr['view']->subscr_duration;
		$admin_name = $setting['setting']->sender_name;
		$admin_email = $setting['setting']->sender_email;
		$buyer_name = Auth::user()->name;
		$buyer_email = Auth::user()->email;
		$buyer_data = array('user_subscr_type' => $user_subscr_type, 'user_subscr_date' => $subscr_date, 'subscr_duration' =>  $subscri_date, 'subscr_price' => $subscr_price, 'currency' => $currency); 
		/* email template code */
		$checktemp = EmailTemplate::checkTemplate(20);
		if($checktemp != 0)
		{
			$template_view['mind'] = EmailTemplate::viewTemplate(20);
			$template_subject = $template_view['mind']->et_subject;
		}
		else
		{
			$template_subject = "Subscription Upgrade";
		}
		/* email template code */
		Mail::send('subscription_mail', $buyer_data , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
						$message->to($buyer_email, $buyer_name)
						->subject($template_subject);
						$message->from($admin_email,$admin_name);
		});
		/* subscription email */
		$result_data = array('payment_token' => $payment_token);
		return view('success')->with($result_data);
	  }
	  else
	  {
	     return redirect('/cancel');
	  }

	
	}
	else if($x_receipt_link_url == 'deposit_item')
	{
	    $sid = 1;
        $setting['setting'] = Settings::editGeneral($sid);
		/* deposite details */
		$payment_date = date('Y-m-d');
		$payment_status = 'completed';
		$updatedata = array('payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_status' => $payment_status);
		Deposit::upDepositdata($purchased_token,$updatedata);
		/* deposite details */
	    $additional_view = Settings::editAdditional();
		$amount = $additional_view->deposit_amount;
		$payment_type = $additional_view->deposit_type;
		$user_token = Auth::user()->user_token;
		$buyer_details = Members::editData($user_token);
		$wallet = $buyer_details->earnings + $amount;
		$data = array('earnings' => $wallet);
		Members::updateData($user_token,$data);
		/* currency */
		$deposit_details = Deposit::displaydepositDetails($purchased_token);
		$currency = Currencies::getCurrency($deposit_details->currency_type_code);
		$currency_symbol = $currency->currency_symbol;
		$currency_rate = $currency->currency_rate;
		$amount = $amount * $currency_rate;
		$currency = $currency->currency_symbol;
		/* currency */
		$buyer['info'] = Members::singlevendorData(Auth::user()->id);	
		$buyer_name = $buyer['info']->name;
		$buyer_email = $buyer['info']->email;
		
		$admin_name = $setting['setting']->sender_name;
		$admin_email = $setting['setting']->sender_email;
		$buyer_data = array('buyer_name' => $buyer_name, 'buyer_email' => $buyer_email, 'currency' => $currency, 'amount' => $amount,  'payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_type' => $payment_type);
		/* email template code */
	    $checktemp = EmailTemplate::checkTemplate(19);
		if($checktemp != 0)
		{
			$template_view['mind'] = EmailTemplate::viewTemplate(19);
			$template_subject = $template_view['mind']->et_subject;
		}
		else
		{
			$template_subject = "New Deposit Details";
		}
		/* email template code */
		Mail::send('deposit_mail', $buyer_data , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
			$message->to($buyer_email, $buyer_name)
							->subject($template_subject);
						$message->from($admin_email,$admin_name);
		});
						
		$clean = array('deposit_amount' => '', 'deposit_type' => '');
		Settings::updateAdditionData($clean);
		$data_record = array('payment_token' => $payment_token);
		if($this->custom_settings('author_key') == Helper::Key_Owner())
		{
		/* Notification Message */
		$deposit_details = Deposit::displaydepositDetails($purchased_token);
		$noti_date = date('Y-m-d H:i:s');
		$noti_sender_id = $buyer['info']->id;
		$noti_receiver_id = 1;
		$noti_message = 'New deposit received send by <strong>'.$buyer['info']->name.'</strong>';
		$savedata = array('noti_date' => $noti_date, 'noti_sender_id' => $noti_sender_id, 'noti_receiver_id' => $noti_receiver_id, 'noti_message' => $noti_message, 'noti_type' => 'deposit', 'noti_type_id' => $deposit_details->dd_id);
		Items::saveNotification($savedata);
		/* Notification Message */
		}
		return view('success')->with($data_record);

	}
	else if($x_receipt_link_url == 'product_item')
	{
	
			$sid = 1;
			$setting['setting'] = Settings::editGeneral($sid);
			$payment_status = 'completed';
			$check_purchased_data = Items::checkoutLevel($purchased_token);
			if($check_purchased_data == 0)
			{
				   if($done_approval_status == "Y")
				   { 
					$orderdata = array('payment_token' => $payment_token, 'order_status' => $payment_status);
					$checkoutdata = array('payment_token' => $payment_token, 'payment_status' => $payment_status);
					Items::singleordupdateData($purchased_token,$orderdata);
					Items::singlecheckoutData($purchased_token,$checkoutdata);
					
					$token = $purchased_token;
					$check['display'] = Items::getcheckoutData($token);
					/* customer email */
					$currency = $this->site_currency();
					$admin_name = $setting['setting']->sender_name;
					$admin_email = $setting['setting']->sender_email;
					$customer['info'] = Members::singlevendorData($check['display']->user_id);
					$buyer_name = $customer['info']->name;
					$buyer_email = $customer['info']->email;
					$amount = $check['display']->total;
					$order_id = $check['display']->purchase_token;
					$payment_type = $check['display']->payment_type;
					$payment_date = $check['display']->payment_date;
					$payment_status = $check['display']->payment_status;
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
														  /* email template code */
							Mail::send('item_purchase_mail', $record_customer , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
												$message->to($buyer_email, $buyer_name)
														->subject($template_subject);
												$message->from($admin_email,$admin_name);
											});
					/* customer email */
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
					   $result_data = array('payment_token' => $payment_token);
					   return view('success')->with($result_data);
					  }
					  else
					  {
					  return redirect('/cancel');
					  } 
				}
				else
				{
				$result_data = array('payment_token' => $payment_token);
				return view('success')->with($result_data);
				}
				
	     }
		  else
		  {
			 return redirect('/cancel');
		  }
	}
	
	
	/* ipay */
	public function deposit_ipay_success(Request $request)
	{
	  
	$sid = 1;
	$setting['setting'] = Settings::editGeneral($sid);
	$payment_token = $request->input('txncd');
	$payment_status = 'completed';
	$purchased_token = $request->input('id');
	$ipay_status = $request->input('status');
		if($ipay_status == 'aei7p7yrx4ae34') // success
		{
		   
		    /* deposite details */
			$payment_date = date('Y-m-d');
			$payment_status = 'completed';
			$updatedata = array('payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_status' => $payment_status);
			Deposit::upDepositdata($purchased_token,$updatedata);
			/* deposite details */
		    $additional_view = Settings::editAdditional();
			$amount = $additional_view->deposit_amount;
			$payment_type = $additional_view->deposit_type;
			$user_token = Auth::user()->user_token;
			$buyer_details = Members::editData($user_token);
			$wallet = $buyer_details->earnings + $amount;
			$data = array('earnings' => $wallet);
			Members::updateData($user_token,$data);
			/* currency */
			$deposit_details = Deposit::displaydepositDetails($purchased_token);
			$currency = Currencies::getCurrency($deposit_details->currency_type_code);
			$currency_symbol = $currency->currency_symbol;
			$currency_rate = $currency->currency_rate;
			$amount = $amount * $currency_rate;
			$currency = $currency->currency_symbol;
			/* currency */
			$buyer['info'] = Members::singlevendorData(Auth::user()->id);	
			$buyer_name = $buyer['info']->name;
			$buyer_email = $buyer['info']->email;
			$admin_name = $setting['setting']->sender_name;
			$admin_email = $setting['setting']->sender_email;
			$buyer_data = array('buyer_name' => $buyer_name, 'buyer_email' => $buyer_email, 'currency' => $currency, 'amount' => $amount,  'payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_type' => $payment_type);
			/* email template code */
			$checktemp = EmailTemplate::checkTemplate(19);
			if($checktemp != 0)
			{
			   $template_view['mind'] = EmailTemplate::viewTemplate(19);
			   $template_subject = $template_view['mind']->et_subject;
			}
			else
			{
			   $template_subject = "New Deposit Details";
			}
			/* email template code */
			Mail::send('deposit_mail', $buyer_data , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
				$message->to($buyer_email, $buyer_name)
				   ->subject($template_subject);
				$message->from($admin_email,$admin_name);
			});
			$clean = array('deposit_amount' => '', 'deposit_type' => '');
			Settings::updateAdditionData($clean);
			$data_record = array('payment_token' => $payment_token);
			if($this->custom_settings('author_key') == Helper::Key_Owner())
			{
			/* Notification Message */
			$deposit_details = Deposit::displaydepositDetails($purchased_token);
			$noti_date = date('Y-m-d H:i:s');
			$noti_sender_id = $buyer['info']->id;
			$noti_receiver_id = 1;
			$noti_message = 'New deposit received send by <strong>'.$buyer['info']->name.'</strong>';
			$savedata = array('noti_date' => $noti_date, 'noti_sender_id' => $noti_sender_id, 'noti_receiver_id' => $noti_receiver_id, 'noti_message' => $noti_message, 'noti_type' => 'deposit', 'noti_type_id' => $deposit_details->dd_id);
			Items::saveNotification($savedata);
			/* Notification Message */
			}
			return view('success')->with($data_record);

		   
		}
		else
	   	{
	       return view('cancel');
	   	}
	  
	}
	
	public function sslcommerz_successpage(Request $request)
	{
	
	    $validate = SSLCommerz::validate_payment($request);
        if($validate)
        {
		   $sid = 1;
	       $setting['setting'] = Settings::editGeneral($sid);
		   $payment_token = $request->bank_tran_id;
		   $purchased_token = $request->input('tran_id');
		   $sslcommerz_status = $request->input('status');
		   $payment_status = 'completed';
		   if($sslcommerz_status == 'VALID') // success
		   {
		      
			    $orderdata = array('payment_token' => $payment_token, 'order_status' => $payment_status);
				$checkoutdata = array('payment_token' => $payment_token, 'payment_status' => $payment_status);
				Items::singleordupdateData($purchased_token,$orderdata);
				Items::singlecheckoutData($purchased_token,$checkoutdata);
				
				$token = $purchased_token;
				$check['display'] = Items::getcheckoutData($token);
				/* customer email */
					$currency = $this->site_currency();
					$admin_name = $setting['setting']->sender_name;
					$admin_email = $setting['setting']->sender_email;
					$customer['info'] = Members::singlevendorData($check['display']->user_id);
					$buyer_name = $customer['info']->name;
					$buyer_email = $customer['info']->email;
					$amount = $check['display']->total;
					$order_id = $check['display']->purchase_token;
					$payment_type = $check['display']->payment_type;
					$payment_date = $check['display']->payment_date;
					$payment_status = $check['display']->payment_status;
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
														  /* email template code */
							Mail::send('item_purchase_mail', $record_customer , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
												$message->to($buyer_email, $buyer_name)
														->subject($template_subject);
												$message->from($admin_email,$admin_name);
											});
					/* customer email */
				$order_id = $check['display']->order_ids;
				$order_loop = explode(',',$order_id);
				foreach($order_loop as $order)
				{
									
					$getitem['item'] = Items::getorderData($order);
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
				$result_data = array('payment_token' => $payment_token);
				return view('success')->with($result_data);
				
			  
			  
		   }
		   else
		   {
				return view('cancel');
	       }
		
		}
		else
		{
				return view('cancel');
	    }
	
	}
	
	
	public function ipay_success(Request $request)
	{
	  
	$sid = 1;
	$setting['setting'] = Settings::editGeneral($sid);
	$payment_token = $request->input('txncd');
	$payment_status = 'completed';
	$purchased_token = $request->input('id');
	$ipay_status = $request->input('status');
	if($ipay_status == 'aei7p7yrx4ae34') // success
	{
		$orderdata = array('payment_token' => $payment_token, 'order_status' => $payment_status);
		$checkoutdata = array('payment_token' => $payment_token, 'payment_status' => $payment_status);
		Items::singleordupdateData($purchased_token,$orderdata);
		Items::singlecheckoutData($purchased_token,$checkoutdata);
		
		$token = $purchased_token;
		$check['display'] = Items::getcheckoutData($token);
		/* customer email */
					$currency = $this->site_currency();
					$admin_name = $setting['setting']->sender_name;
					$admin_email = $setting['setting']->sender_email;
					$customer['info'] = Members::singlevendorData($check['display']->user_id);
					$buyer_name = $customer['info']->name;
					$buyer_email = $customer['info']->email;
					$amount = $check['display']->total;
					$order_id = $check['display']->purchase_token;
					$payment_type = $check['display']->payment_type;
					$payment_date = $check['display']->payment_date;
					$payment_status = $check['display']->payment_status;
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
														  /* email template code */
							Mail::send('item_purchase_mail', $record_customer , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
												$message->to($buyer_email, $buyer_name)
														->subject($template_subject);
												$message->from($admin_email,$admin_name);
											});
					/* customer email */
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
		$result_data = array('payment_token' => $payment_token);
		return view('success')->with($result_data);
		
	   }
	   else
	   {
	       return view('cancel');
	   }
	  
	}
	
	/* ipay */
	
	public function paypal_success($ord_token, Request $request)
	{
	$sid = 1;
	$setting['setting'] = Settings::editGeneral($sid);
	$payment_token = $request->input('tx');
	$payment_status = 'completed';
	$purchased_token = $ord_token;
	$orderdata = array('payment_token' => $payment_token, 'order_status' => $payment_status);
	$checkoutdata = array('payment_token' => $payment_token, 'payment_status' => $payment_status);
	Items::singleordupdateData($purchased_token,$orderdata);
	Items::singlecheckoutData($purchased_token,$checkoutdata);
	
	$token = $purchased_token;
	$check['display'] = Items::getcheckoutData($token);
	/* customer email */
	$currency = $this->site_currency();
	$admin_name = $setting['setting']->sender_name;
	$admin_email = $setting['setting']->sender_email;
	$customer['info'] = Members::singlevendorData($check['display']->user_id);
	$buyer_name = $customer['info']->name;
	$buyer_email = $customer['info']->email;
	$amount = $check['display']->total;
	$order_id = $check['display']->purchase_token;
	$payment_type = $check['display']->payment_type;
	$payment_date = $check['display']->payment_date;
	$payment_status = $check['display']->payment_status;
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
										  /* email template code */
			Mail::send('item_purchase_mail', $record_customer , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
								$message->to($buyer_email, $buyer_name)
										->subject($template_subject);
								$message->from($admin_email,$admin_name);
							});
	/* customer email */
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
	$result_data = array('payment_token' => $payment_token);
	return view('success')->with($result_data);
	
	}
	
	
	
	public function mercadopago_success($ord_token, Request $request)
	{
	  
	  $sid = 1;
	$setting['setting'] = Settings::editGeneral($sid);
	$pay_status = $request->input('status');
	if($pay_status == 'approved')
    {
		$payment_token = $request->input('payment_id');
	    $payment_status = 'completed';
	    $purchased_token = $ord_token;
	    $orderdata = array('payment_token' => $payment_token, 'order_status' => $payment_status);
		$checkoutdata = array('payment_token' => $payment_token, 'payment_status' => $payment_status);	
	    Items::singleordupdateData($purchased_token,$orderdata);
	    Items::singlecheckoutData($purchased_token,$checkoutdata);
	
	$token = $purchased_token;
	$check['display'] = Items::getcheckoutData($token);
	/* customer email */
					$currency = $this->site_currency();
					$admin_name = $setting['setting']->sender_name;
					$admin_email = $setting['setting']->sender_email;
					$customer['info'] = Members::singlevendorData($check['display']->user_id);
					$buyer_name = $customer['info']->name;
					$buyer_email = $customer['info']->email;
					$amount = $check['display']->total;
					$order_id = $check['display']->purchase_token;
					$payment_type = $check['display']->payment_type;
					$payment_date = $check['display']->payment_date;
					$payment_status = $check['display']->payment_status;
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
														  /* email template code */
							Mail::send('item_purchase_mail', $record_customer , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
												$message->to($buyer_email, $buyer_name)
														->subject($template_subject);
												$message->from($admin_email,$admin_name);
											});
					/* customer email */
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
	$payment_token = "";	
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
	$result_data = array('payment_token' => $payment_token);
	return view('success')->with($result_data);
	
	}
	else
	{
	   return view('failure');
	}
	
		
	}
	
	/* coinpayments */
	public function deposit_coinpayments_success($ord_token, Request $request)
	{
		$sid = 1;
		$setting['setting'] = Settings::editGeneral($sid);
		$payment_token = '';
		/* deposite details */
		$payment_date = date('Y-m-d');
		$payment_status = 'completed';
		$updatedata = array('payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_status' => $payment_status);
		Deposit::upDepositdata($ord_token,$updatedata);
		/* deposite details */
		$additional_view = Settings::editAdditional();
		$amount = $additional_view->deposit_amount;
		$payment_type = $additional_view->deposit_type;
		$user_token = Auth::user()->user_token;
		$buyer_details = Members::editData($user_token);
		$wallet = $buyer_details->earnings + $amount;
		$data = array('earnings' => $wallet);
		Members::updateData($user_token,$data);
		/* currency */
		$deposit_details = Deposit::displaydepositDetails($ord_token);
		$currency = Currencies::getCurrency($deposit_details->currency_type_code);
		$currency_symbol = $currency->currency_symbol;
		$currency_rate = $currency->currency_rate;
		$amount = $amount * $currency_rate;
		$currency = $currency->currency_symbol;
		/* currency */
		$buyer['info'] = Members::singlevendorData(Auth::user()->id);	
		$buyer_name = $buyer['info']->name;
		$buyer_email = $buyer['info']->email;
		$admin_name = $setting['setting']->sender_name;
		$admin_email = $setting['setting']->sender_email;
		$buyer_data = array('buyer_name' => $buyer_name, 'buyer_email' => $buyer_email, 'currency' => $currency, 'amount' => $amount,  'payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_type' => $payment_type);
		/* email template code */
		$checktemp = EmailTemplate::checkTemplate(19);
		if($checktemp != 0)
		{
		   $template_view['mind'] = EmailTemplate::viewTemplate(19);
		   $template_subject = $template_view['mind']->et_subject;
		}
		else
		{
		   $template_subject = "New Deposit Details";
		}
		/* email template code */
		Mail::send('deposit_mail', $buyer_data , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
			$message->to($buyer_email, $buyer_name)
			   ->subject($template_subject);
			$message->from($admin_email,$admin_name);
		});
		$clean = array('deposit_amount' => '', 'deposit_type' => '');
		Settings::updateAdditionData($clean);
		$data_record = array('payment_token' => $payment_token);
		if($this->custom_settings('author_key') == Helper::Key_Owner())
		{
		/* Notification Message */
		$deposit_details = Deposit::displaydepositDetails($ord_token);
		$noti_date = date('Y-m-d H:i:s');
		$noti_sender_id = $buyer['info']->id;
		$noti_receiver_id = 1;
		$noti_message = 'New deposit received send by <strong>'.$buyer['info']->name.'</strong>';
		$savedata = array('noti_date' => $noti_date, 'noti_sender_id' => $noti_sender_id, 'noti_receiver_id' => $noti_receiver_id, 'noti_message' => $noti_message, 'noti_type' => 'deposit', 'noti_type_id' => $deposit_details->dd_id);
		Items::saveNotification($savedata);
		/* Notification Message */
		}
		return view('success')->with($data_record);

	}
	
	public function coinpayments_success($ord_token, Request $request)
	{
	$sid = 1;
	$setting['setting'] = Settings::editGeneral($sid);
	$payment_token = '';
	$payment_status = 'completed';
	$purchased_token = $ord_token;
	$orderdata = array('payment_token' => $payment_token, 'order_status' => $payment_status);
	$checkoutdata = array('payment_token' => $payment_token, 'payment_status' => $payment_status);
	Items::singleordupdateData($purchased_token,$orderdata);
	Items::singlecheckoutData($purchased_token,$checkoutdata);
	
	$token = $purchased_token;
	$check['display'] = Items::getcheckoutData($token);
	/* customer email */
					$currency = $this->site_currency();
					$admin_name = $setting['setting']->sender_name;
					$admin_email = $setting['setting']->sender_email;
					$customer['info'] = Members::singlevendorData($check['display']->user_id);
					$buyer_name = $customer['info']->name;
					$buyer_email = $customer['info']->email;
					$amount = $check['display']->total;
					$order_id = $check['display']->purchase_token;
					$payment_type = $check['display']->payment_type;
					$payment_date = $check['display']->payment_date;
					$payment_status = $check['display']->payment_status;
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
														  /* email template code */
							Mail::send('item_purchase_mail', $record_customer , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
												$message->to($buyer_email, $buyer_name)
														->subject($template_subject);
												$message->from($admin_email,$admin_name);
											});
					/* customer email */
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
	    } 
    /* referral per sale earning */	
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
	$result_data = array('payment_token' => $payment_token);
	return view('success')->with($result_data);
	
	}
	
	
	/* coinpayments */
	
	/* stripe */
	public function deposit_stripe_success($orderid,Request $request)
    {   
	  
	    $sid = 1;
		$setting['setting'] = Settings::editGeneral($sid);
		$encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
	    $ord_token   = $encrypter->decrypt($orderid);
	    $payment_token = '';
		/* deposite details */
		$payment_date = date('Y-m-d');
		$payment_status = 'completed';
		$updatedata = array('payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_status' => $payment_status);
		Deposit::upDepositdata($ord_token,$updatedata);
		/* deposite details */
	    $additional_view = Settings::editAdditional();
		$amount = $additional_view->deposit_amount;
		$payment_type = $additional_view->deposit_type;
		$user_token = Auth::user()->user_token;
		$buyer_details = Members::editData($user_token);
		$wallet = $buyer_details->earnings + $amount;
		$data = array('earnings' => $wallet);
		Members::updateData($user_token,$data);
		/* currency */
		$deposit_details = Deposit::displaydepositDetails($ord_token);
		$currency = Currencies::getCurrency($deposit_details->currency_type_code);
		$currency_symbol = $currency->currency_symbol;
		$currency_rate = $currency->currency_rate;
		$amount = $amount * $currency_rate;
		$currency = $currency->currency_symbol;
		/* currency */
		$buyer['info'] = Members::singlevendorData(Auth::user()->id);	
		$buyer_name = $buyer['info']->name;
		$buyer_email = $buyer['info']->email;
		$admin_name = $setting['setting']->sender_name;
		$admin_email = $setting['setting']->sender_email;
		$buyer_data = array('buyer_name' => $buyer_name, 'buyer_email' => $buyer_email, 'currency' => $currency, 'amount' => $amount,  'payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_type' => $payment_type);
		/* email template code */
		$checktemp = EmailTemplate::checkTemplate(19);
		if($checktemp != 0)
		{
		   $template_view['mind'] = EmailTemplate::viewTemplate(19);
		   $template_subject = $template_view['mind']->et_subject;
		}
		else
		{
		   $template_subject = "New Deposit Details";
		}
		/* email template code */
		Mail::send('deposit_mail', $buyer_data , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
			$message->to($buyer_email, $buyer_name)
			   ->subject($template_subject);
			$message->from($admin_email,$admin_name);
		});
		$clean = array('deposit_amount' => '', 'deposit_type' => '');
		Settings::updateAdditionData($clean);
		$data_record = array('payment_token' => $payment_token);
		if($this->custom_settings('author_key') == Helper::Key_Owner())
		{
		/* Notification Message */
		$deposit_details = Deposit::displaydepositDetails($ord_token);
		$noti_date = date('Y-m-d H:i:s');
		$noti_sender_id = $buyer['info']->id;
		$noti_receiver_id = 1;
		$noti_message = 'New deposit received send by <strong>'.$buyer['info']->name.'</strong>';
		$savedata = array('noti_date' => $noti_date, 'noti_sender_id' => $noti_sender_id, 'noti_receiver_id' => $noti_receiver_id, 'noti_message' => $noti_message, 'noti_type' => 'deposit', 'noti_type_id' => $deposit_details->dd_id);
		Items::saveNotification($savedata);
		/* Notification Message */
		}
		return view('success')->with($data_record);
		  
	}
	/* stripe */
	
	/* aamarpay */
	
	public function deposit_aamarpay_success($ord_token, Request $request)
	{
		$sid = 1;
		$setting['setting'] = Settings::editGeneral($sid);
		$pay_status = $request->input('pay_status');
	
		if($pay_status == 'Successful')
		{
		$payment_token = $request->input('pg_txnid');
		/* deposite details */
		$payment_date = date('Y-m-d');
		$payment_status = 'completed';
		$updatedata = array('payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_status' => $payment_status);
		Deposit::upDepositdata($ord_token,$updatedata);
		/* deposite details */
	    $additional_view = Settings::editAdditional();
		$amount = $additional_view->deposit_amount;
		$payment_type = $additional_view->deposit_type;
		$user_token = Auth::user()->user_token;
		$buyer_details = Members::editData($user_token);
		$wallet = $buyer_details->earnings + $amount;
		$data = array('earnings' => $wallet);
		Members::updateData($user_token,$data);
		/* currency */
		$deposit_details = Deposit::displaydepositDetails($ord_token);
		$currency = Currencies::getCurrency($deposit_details->currency_type_code);
		$currency_symbol = $currency->currency_symbol;
		$currency_rate = $currency->currency_rate;
		$amount = $amount * $currency_rate;
		$currency = $currency->currency_symbol;
		/* currency */
		$buyer['info'] = Members::singlevendorData(Auth::user()->id);	
		$buyer_name = $buyer['info']->name;
		$buyer_email = $buyer['info']->email;
		$admin_name = $setting['setting']->sender_name;
		$admin_email = $setting['setting']->sender_email;
		$buyer_data = array('buyer_name' => $buyer_name, 'buyer_email' => $buyer_email, 'currency' => $currency, 'amount' => $amount,  'payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_type' => $payment_type);
		/* email template code */
		$checktemp = EmailTemplate::checkTemplate(19);
		if($checktemp != 0)
		{
		   $template_view['mind'] = EmailTemplate::viewTemplate(19);
		   $template_subject = $template_view['mind']->et_subject;
		}
		else
		{
		   $template_subject = "New Deposit Details";
		}
		/* email template code */
		Mail::send('deposit_mail', $buyer_data , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
			$message->to($buyer_email, $buyer_name)
			   ->subject($template_subject);
			$message->from($admin_email,$admin_name);
		});
		$clean = array('deposit_amount' => '', 'deposit_type' => '');
		Settings::updateAdditionData($clean);
		$data_record = array('payment_token' => $payment_token);
		if($this->custom_settings('author_key') == Helper::Key_Owner())
		{
		/* Notification Message */
		$deposit_details = Deposit::displaydepositDetails($ord_token);
		$noti_date = date('Y-m-d H:i:s');
		$noti_sender_id = $buyer['info']->id;
		$noti_receiver_id = 1;
		$noti_message = 'New deposit received send by <strong>'.$buyer['info']->name.'</strong>';
		$savedata = array('noti_date' => $noti_date, 'noti_sender_id' => $noti_sender_id, 'noti_receiver_id' => $noti_receiver_id, 'noti_message' => $noti_message, 'noti_type' => 'deposit', 'noti_type_id' => $deposit_details->dd_id);
		Items::saveNotification($savedata);
		/* Notification Message */
		}
		return view('success')->with($data_record);
		}
		else
		{
		return view('cancel');
		}
		
	}
	
	
	/* aamarpay */
	
	
	/* instamojo */
	
	public function deposit_instamojo_success($ord_token, Request $request)
	{
		$sid = 1;
		$setting['setting'] = Settings::editGeneral($sid);
		$payment_token = $request->input('payment_id');
		$gateway_status = $request->input('payment_status');
	
		if($gateway_status == 'Credit')
		{
		/* deposite details */
		$payment_date = date('Y-m-d');
		$payment_status = 'completed';
		$updatedata = array('payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_status' => $payment_status);
		Deposit::upDepositdata($ord_token,$updatedata);
		/* deposite details */
	    $additional_view = Settings::editAdditional();
		$amount = $additional_view->deposit_amount;
		$payment_type = $additional_view->deposit_type;
		$user_token = Auth::user()->user_token;
		$buyer_details = Members::editData($user_token);
		$wallet = $buyer_details->earnings + $amount;
		$data = array('earnings' => $wallet);
		Members::updateData($user_token,$data);
		/* currency */
		$deposit_details = Deposit::displaydepositDetails($ord_token);
		$currency = Currencies::getCurrency($deposit_details->currency_type_code);
		$currency_symbol = $currency->currency_symbol;
		$currency_rate = $currency->currency_rate;
		$amount = $amount * $currency_rate;
		$currency = $currency->currency_symbol;
		/* currency */
		$buyer['info'] = Members::singlevendorData(Auth::user()->id);	
		$buyer_name = $buyer['info']->name;
		$buyer_email = $buyer['info']->email;
		$admin_name = $setting['setting']->sender_name;
		$admin_email = $setting['setting']->sender_email;
		$buyer_data = array('buyer_name' => $buyer_name, 'buyer_email' => $buyer_email, 'currency' => $currency, 'amount' => $amount,  'payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_type' => $payment_type);
		/* email template code */
		$checktemp = EmailTemplate::checkTemplate(19);
		if($checktemp != 0)
		{
		   $template_view['mind'] = EmailTemplate::viewTemplate(19);
		   $template_subject = $template_view['mind']->et_subject;
		}
		else
		{
		   $template_subject = "New Deposit Details";
		}
		/* email template code */
		Mail::send('deposit_mail', $buyer_data , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
			$message->to($buyer_email, $buyer_name)
			   ->subject($template_subject);
			$message->from($admin_email,$admin_name);
		});
		$clean = array('deposit_amount' => '', 'deposit_type' => '');
		Settings::updateAdditionData($clean);
		$data_record = array('payment_token' => $payment_token);
		if($this->custom_settings('author_key') == Helper::Key_Owner())
		{
		/* Notification Message */
		$deposit_details = Deposit::displaydepositDetails($ord_token);
		$noti_date = date('Y-m-d H:i:s');
		$noti_sender_id = $buyer['info']->id;
		$noti_receiver_id = 1;
		$noti_message = 'New deposit received send by <strong>'.$buyer['info']->name.'</strong>';
		$savedata = array('noti_date' => $noti_date, 'noti_sender_id' => $noti_sender_id, 'noti_receiver_id' => $noti_receiver_id, 'noti_message' => $noti_message, 'noti_type' => 'deposit', 'noti_type_id' => $deposit_details->dd_id);
		Items::saveNotification($savedata);
		/* Notification Message */
		}
		return view('success')->with($data_record);
		}
		else
		{
		return view('cancel');
		}
		
	}
	
	
	public function instamojo_success($ord_token, Request $request)
	{
	$sid = 1;
	$setting['setting'] = Settings::editGeneral($sid);
	$payment_token = $request->input('payment_id');
	$gateway_status = $request->input('payment_status');
	
	if($gateway_status == 'Credit')
	{
	$payment_status = 'completed';
	$purchased_token = $ord_token;
	$orderdata = array('payment_token' => $payment_token, 'order_status' => $payment_status);
	$checkoutdata = array('payment_token' => $payment_token, 'payment_status' => $payment_status);
	Items::singleordupdateData($purchased_token,$orderdata);
	Items::singlecheckoutData($purchased_token,$checkoutdata);
	
	$token = $purchased_token;
	$check['display'] = Items::getcheckoutData($token);
	/* customer email */
					$currency = $this->site_currency();
					$admin_name = $setting['setting']->sender_name;
					$admin_email = $setting['setting']->sender_email;
					$customer['info'] = Members::singlevendorData($check['display']->user_id);
					$buyer_name = $customer['info']->name;
					$buyer_email = $customer['info']->email;
					$amount = $check['display']->total;
					$order_id = $check['display']->purchase_token;
					$payment_type = $check['display']->payment_type;
					$payment_date = $check['display']->payment_date;
					$payment_status = $check['display']->payment_status;
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
														  /* email template code */
							Mail::send('item_purchase_mail', $record_customer , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
												$message->to($buyer_email, $buyer_name)
														->subject($template_subject);
												$message->from($admin_email,$admin_name);
											});
					/* customer email */
	$order_id = $check['display']->order_ids;
	$order_loop = explode(',',$order_id);
	foreach($order_loop as $order)
	{
						
		$getitem['item'] = Items::getorderData($order);
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
	   }	   
    /* referral per sale earning */	
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
	$result_data = array('payment_token' => $payment_token);
	return view('success')->with($result_data);
	}
	else
	{
	return view('cancel');
	}
	
	}
	
	
	/* instamojo */
	
	
	/* mollie */
	
	public function mollie_success()
	{
	$sid = 1;
	$setting['setting'] = Settings::editGeneral($sid);
	$additional['setting'] = Settings::editAdditional();
	$ord_token = session()->get('purchase_token');
	$user_data = Members::singlevendorData(session()->get('mollie_user_id'));
	if(session()->get('mollie_type') == 'admin')
	{
	Mollie::api()->setApiKey($additional['setting']->mollie_api_key);
	}
	else
	{
	Mollie::api()->setApiKey($user_data->user_mollie_api_key);
	}
	$payment = Mollie::api()->payments()->get(session()->get('payment_id'));
	
	$payment_token = session()->get('payment_id');
	if($payment->status == "paid")
	{
	$payment_status = 'completed';
	$purchased_token = $ord_token;
	$orderdata = array('payment_token' => $payment_token, 'order_status' => $payment_status);
	$checkoutdata = array('payment_token' => $payment_token, 'payment_status' => $payment_status);
	Items::singleordupdateData($purchased_token,$orderdata);
	Items::singlecheckoutData($purchased_token,$checkoutdata);
	
	$token = $purchased_token;
	$check['display'] = Items::getcheckoutData($token);
	/* customer email */
					$currency = $this->site_currency();
					$admin_name = $setting['setting']->sender_name;
					$admin_email = $setting['setting']->sender_email;
					$customer['info'] = Members::singlevendorData($check['display']->user_id);
					$buyer_name = $customer['info']->name;
					$buyer_email = $customer['info']->email;
					$amount = $check['display']->total;
					$order_id = $check['display']->purchase_token;
					$payment_type = $check['display']->payment_type;
					$payment_date = $check['display']->payment_date;
					$payment_status = $check['display']->payment_status;
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
														  /* email template code */
							Mail::send('item_purchase_mail', $record_customer , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
												$message->to($buyer_email, $buyer_name)
														->subject($template_subject);
												$message->from($admin_email,$admin_name);
											});
					/* customer email */
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
	   }	   
    /* referral per sale earning */	
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
	$result_data = array('payment_token' => $payment_token);
	session()->forget('purchase_token');
    session()->forget('payment_id');
	session()->forget('mollie_type');
	session()->forget('mollie_user_id');
	return view('success')->with($result_data);
	}
	else
	{
	session()->forget('purchase_token');
    session()->forget('payment_id');
	session()->forget('mollie_type');
	session()->forget('mollie_user_id');
	return view('cancel');
	}
	
	}
	
	/* mollie */
	
	
	/* aamarpay */
	
	public function aamarpay_success($ord_token, Request $request)
	{
	$sid = 1;
	$setting['setting'] = Settings::editGeneral($sid);
	$pay_status = $request->input('pay_status');
	
	if($pay_status == 'Successful')
	{
	$payment_token = $request->input('pg_txnid');
	$payment_status = 'completed';
	$purchased_token = $ord_token;
	$orderdata = array('payment_token' => $payment_token, 'order_status' => $payment_status);
	$checkoutdata = array('payment_token' => $payment_token, 'payment_status' => $payment_status);
	Items::singleordupdateData($purchased_token,$orderdata);
	Items::singlecheckoutData($purchased_token,$checkoutdata);
	
	$token = $purchased_token;
	$check['display'] = Items::getcheckoutData($token);
	/* customer email */
					$currency = $this->site_currency();
					$admin_name = $setting['setting']->sender_name;
					$admin_email = $setting['setting']->sender_email;
					$customer['info'] = Members::singlevendorData($check['display']->user_id);
					$buyer_name = $customer['info']->name;
					$buyer_email = $customer['info']->email;
					$amount = $check['display']->total;
					$order_id = $check['display']->purchase_token;
					$payment_type = $check['display']->payment_type;
					$payment_date = $check['display']->payment_date;
					$payment_status = $check['display']->payment_status;
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
														  /* email template code */
							Mail::send('item_purchase_mail', $record_customer , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
												$message->to($buyer_email, $buyer_name)
														->subject($template_subject);
												$message->from($admin_email,$admin_name);
											});
					/* customer email */
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
	   }	   
    /* referral per sale earning */	
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
	$result_data = array('payment_token' => $payment_token);
	return view('success')->with($result_data);
	}
	else
	{
	return view('cancel');
	}
	
	}
	
	
	/* aamarpay */
	
	
	public function deposit_sslcommerz_successpage(Request $request)
	{
	     
		$validate = SSLCommerz::validate_payment($request);
        if($validate)
        {
		   $sid = 1;
	       $setting['setting'] = Settings::editGeneral($sid);
		   $payment_token = $request->bank_tran_id;
		   $purchased_token = $request->input('tran_id');
		   $sslcommerz_status = $request->input('status');
		   $payment_status = 'completed';
		   if($sslcommerz_status == 'VALID') // success
		   {
	                
	                /* deposite details */
					$payment_date = date('Y-m-d');
					$payment_status = 'completed';
					$updatedata = array('payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_status' => $payment_status);
					Deposit::upDepositdata($purchased_token,$updatedata);
					/* deposite details */
	                $additional_view = Settings::editAdditional();
					$amount = $additional_view->deposit_amount;
					$payment_type = $additional_view->deposit_type;
					$user_token = Auth::user()->user_token;
					$buyer_details = Members::editData($user_token);
					$wallet = $buyer_details->earnings + $amount;
					$data = array('earnings' => $wallet);
					Members::updateData($user_token,$data);
					/* currency */
					$deposit_details = Deposit::displaydepositDetails($purchased_token);
					$currency = Currencies::getCurrency($deposit_details->currency_type_code);
					$currency_symbol = $currency->currency_symbol;
					$currency_rate = $currency->currency_rate;
					$amount = $amount * $currency_rate;
					$currency = $currency->currency_symbol;
					/* currency */
					$buyer['info'] = Members::singlevendorData(Auth::user()->id);	
					$buyer_name = $buyer['info']->name;
					$buyer_email = $buyer['info']->email;
					$admin_name = $setting['setting']->sender_name;
					$admin_email = $setting['setting']->sender_email;
					$buyer_data = array('buyer_name' => $buyer_name, 'buyer_email' => $buyer_email, 'currency' => $currency, 'amount' => $amount,  'payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_type' => $payment_type);
					/* email template code */
	          		$checktemp = EmailTemplate::checkTemplate(19);
					if($checktemp != 0)
					{
					$template_view['mind'] = EmailTemplate::viewTemplate(19);
					$template_subject = $template_view['mind']->et_subject;
					}
					else
					{
					$template_subject = "New Deposit Details";
					}
			  	    /* email template code */
					Mail::send('deposit_mail', $buyer_data , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
						$message->to($buyer_email, $buyer_name)
							->subject($template_subject);
						$message->from($admin_email,$admin_name);
					});
						
					$clean = array('deposit_amount' => '', 'deposit_type' => '');
					Settings::updateAdditionData($clean);
					$data_record = array('payment_token' => $payment_token);
					if($this->custom_settings('author_key') == Helper::Key_Owner())
					{
					/* Notification Message */
					$deposit_details = Deposit::displaydepositDetails($purchased_token);
					$noti_date = date('Y-m-d H:i:s');
					$noti_sender_id = $buyer['info']->id;
					$noti_receiver_id = 1;
					$noti_message = 'New deposit received send by <strong>'.$buyer['info']->name.'</strong>';
					$savedata = array('noti_date' => $noti_date, 'noti_sender_id' => $noti_sender_id, 'noti_receiver_id' => $noti_receiver_id, 'noti_message' => $noti_message, 'noti_type' => 'deposit', 'noti_type_id' => $deposit_details->dd_id);
					Items::saveNotification($savedata);
					/* Notification Message */
					}
					return view('success')->with($data_record);
			}
		   else
		   {
				return view('cancel');
	       }
		
		}
		else
		{
				return view('cancel');
	    }
					
    }
	
	
	public function deposit_payu_success(Request $request)
	{
	                $sid = 1;
	                $setting['setting'] = Settings::editGeneral($sid);
	                $payment_token = $request->input('txnid');
					$purchased_token = $request->input('udf1');
					/* deposite details */
					$payment_date = date('Y-m-d');
					$payment_status = 'completed';
					$updatedata = array('payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_status' => $payment_status);
					Deposit::upDepositdata($purchased_token,$updatedata);
					/* deposite details */
	                $additional_view = Settings::editAdditional();
					$amount = $additional_view->deposit_amount;
					$payment_type = $additional_view->deposit_type;
					$user_token = Auth::user()->user_token;
					$buyer_details = Members::editData($user_token);
					$wallet = $buyer_details->earnings + $amount;
					$data = array('earnings' => $wallet);
					Members::updateData($user_token,$data);
					/* currency */
					$deposit_details = Deposit::displaydepositDetails($purchased_token);
					$currency = Currencies::getCurrency($deposit_details->currency_type_code);
					$currency_symbol = $currency->currency_symbol;
					$currency_rate = $currency->currency_rate;
					$amount = $amount * $currency_rate;
					$currency = $currency->currency_symbol;
					/* currency */
					
					$buyer['info'] = Members::singlevendorData(Auth::user()->id);	
					$buyer_name = $buyer['info']->name;
					$buyer_email = $buyer['info']->email;
					$admin_name = $setting['setting']->sender_name;
					$admin_email = $setting['setting']->sender_email;
					$buyer_data = array('buyer_name' => $buyer_name, 'buyer_email' => $buyer_email, 'currency' => $currency, 'amount' => $amount,  'payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_type' => $payment_type);
					/* email template code */
	          		$checktemp = EmailTemplate::checkTemplate(19);
					if($checktemp != 0)
					{
					$template_view['mind'] = EmailTemplate::viewTemplate(19);
					$template_subject = $template_view['mind']->et_subject;
					}
					else
					{
					$template_subject = "New Deposit Details";
					}
			  	    /* email template code */
					Mail::send('deposit_mail', $buyer_data , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
						$message->to($buyer_email, $buyer_name)
							->subject($template_subject);
						$message->from($admin_email,$admin_name);
					});
						
					$clean = array('deposit_amount' => '', 'deposit_type' => '');
					Settings::updateAdditionData($clean);
					$data_record = array('payment_token' => $payment_token);
					if($this->custom_settings('author_key') == Helper::Key_Owner())
					{
					/* Notification Message */
					$deposit_details = Deposit::displaydepositDetails($purchased_token);
					$noti_date = date('Y-m-d H:i:s');
					$noti_sender_id = $buyer['info']->id;
					$noti_receiver_id = 1;
					$noti_message = 'New deposit received send by <strong>'.$buyer['info']->name.'</strong>';
					$savedata = array('noti_date' => $noti_date, 'noti_sender_id' => $noti_sender_id, 'noti_receiver_id' => $noti_receiver_id, 'noti_message' => $noti_message, 'noti_type' => 'deposit', 'noti_type_id' => $deposit_details->dd_id);
					Items::saveNotification($savedata);
					/* Notification Message */
					}
					return view('success')->with($data_record);
    }
	
	public function payu_success(Request $request)
	{
	$sid = 1;
	$setting['setting'] = Settings::editGeneral($sid);
	$payment_token = $request->input('txnid');
	$payment_status = 'completed';
	$purchased_token = $request->input('udf1');
	$orderdata = array('payment_token' => $payment_token, 'order_status' => $payment_status);
	$checkoutdata = array('payment_token' => $payment_token, 'payment_status' => $payment_status);
	Items::singleordupdateData($purchased_token,$orderdata);
	Items::singlecheckoutData($purchased_token,$checkoutdata);
	
	$token = $purchased_token;
	$check['display'] = Items::getcheckoutData($token);
	/* customer email */
					$currency = $this->site_currency();
					$admin_name = $setting['setting']->sender_name;
					$admin_email = $setting['setting']->sender_email;
					$customer['info'] = Members::singlevendorData($check['display']->user_id);
					$buyer_name = $customer['info']->name;
					$buyer_email = $customer['info']->email;
					$amount = $check['display']->total;
					$order_id = $check['display']->purchase_token;
					$payment_type = $check['display']->payment_type;
					$payment_date = $check['display']->payment_date;
					$payment_status = $check['display']->payment_status;
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
														  /* email template code */
							Mail::send('item_purchase_mail', $record_customer , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
												$message->to($buyer_email, $buyer_name)
														->subject($template_subject);
												$message->from($admin_email,$admin_name);
											});
					/* customer email */
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
	   }	   
    /* referral per sale earning */	
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
	$result_data = array('payment_token' => $payment_token);
	return view('success')->with($result_data);
	
	}
	
	public function deposit_flutterwaveCallback(Request $request)
	{
		$sid = 1;
		$setting['setting'] = Settings::editGeneral($sid);
		$payment_token = $request->input('transaction_id');
		$ord_token = $request->input('tx_ref');
		$pay_status = $request->input('status');
		if ($pay_status == 'successful') 
		{
		    /* deposite details */
			$payment_date = date('Y-m-d');
			$payment_status = 'completed';
			$updatedata = array('payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_status' => $payment_status);
			Deposit::upDepositdata($ord_token,$updatedata);
			/* deposite details */
		    $additional_view = Settings::editAdditional();
			$amount = $additional_view->deposit_amount;
			$payment_type = $additional_view->deposit_type;
			$user_token = Auth::user()->user_token;
			$buyer_details = Members::editData($user_token);
			$wallet = $buyer_details->earnings + $amount;
			$data = array('earnings' => $wallet);
			Members::updateData($user_token,$data);
			/* currency */
			$deposit_details = Deposit::displaydepositDetails($ord_token);
			$currency = Currencies::getCurrency($deposit_details->currency_type_code);
			$currency_symbol = $currency->currency_symbol;
			$currency_rate = $currency->currency_rate;
			$amount = $amount * $currency_rate;
			$currency = $currency->currency_symbol;
			/* currency */
			$buyer['info'] = Members::singlevendorData(Auth::user()->id);	
			$buyer_name = $buyer['info']->name;
			$buyer_email = $buyer['info']->email;
			$admin_name = $setting['setting']->sender_name;
			$admin_email = $setting['setting']->sender_email;
			$buyer_data = array('buyer_name' => $buyer_name, 'buyer_email' => $buyer_email, 'currency' => $currency, 'amount' => $amount,  'payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_type' => $payment_type);
			/* email template code */
			$checktemp = EmailTemplate::checkTemplate(19);
			if($checktemp != 0)
			{
			   $template_view['mind'] = EmailTemplate::viewTemplate(19);
			   $template_subject = $template_view['mind']->et_subject;
			}
			else
			{
			   $template_subject = "New Deposit Details";
			}
			/* email template code */
			Mail::send('deposit_mail', $buyer_data , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
				$message->to($buyer_email, $buyer_name)
				   ->subject($template_subject);
				$message->from($admin_email,$admin_name);
			});
			$clean = array('deposit_amount' => '', 'deposit_type' => '');
			Settings::updateAdditionData($clean);
			$data_record = array('payment_token' => $payment_token);
			if($this->custom_settings('author_key') == Helper::Key_Owner())
			{
			/* Notification Message */
			$deposit_details = Deposit::displaydepositDetails($ord_token);
			$noti_date = date('Y-m-d H:i:s');
			$noti_sender_id = $buyer['info']->id;
			$noti_receiver_id = 1;
			$noti_message = 'New deposit received send by <strong>'.$buyer['info']->name.'</strong>';
			$savedata = array('noti_date' => $noti_date, 'noti_sender_id' => $noti_sender_id, 'noti_receiver_id' => $noti_receiver_id, 'noti_message' => $noti_message, 'noti_type' => 'deposit', 'noti_type_id' => $deposit_details->dd_id);
			Items::saveNotification($savedata);
			/* Notification Message */
			}
			return view('success')->with($data_record);

		}
		else
		{
			   return view('cancel');
		}
			
    }
	
	
	public function flutterwaveCallback(Request $request)
	{
	$sid = 1;
	$setting['setting'] = Settings::editGeneral($sid);
	$payment_token = $request->input('transaction_id');
	$ord_token = $request->input('tx_ref');
	$pay_status = $request->input('status');
	if ($pay_status == 'successful') 
	{
	
			
			$payment_status = 'completed';
			$purchased_token = $ord_token;
			$orderdata = array('payment_token' => $payment_token, 'order_status' => $payment_status);
			$checkoutdata = array('payment_token' => $payment_token, 'payment_status' => $payment_status);
			Items::singleordupdateData($purchased_token,$orderdata);
			Items::singlecheckoutData($purchased_token,$checkoutdata);
			
			$token = $purchased_token;
			$check['display'] = Items::getcheckoutData($token);
			/* customer email */
					$currency = $this->site_currency();
					$admin_name = $setting['setting']->sender_name;
					$admin_email = $setting['setting']->sender_email;
					$customer['info'] = Members::singlevendorData($check['display']->user_id);
					$buyer_name = $customer['info']->name;
					$buyer_email = $customer['info']->email;
					$amount = $check['display']->total;
					$order_id = $check['display']->purchase_token;
					$payment_type = $check['display']->payment_type;
					$payment_date = $check['display']->payment_date;
					$payment_status = $check['display']->payment_status;
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
														  /* email template code */
							Mail::send('item_purchase_mail', $record_customer , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
												$message->to($buyer_email, $buyer_name)
														->subject($template_subject);
												$message->from($admin_email,$admin_name);
											});
					/* customer email */
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
			   }	   
			/* referral per sale earning */	
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
			$result_data = array('payment_token' => $payment_token);
			return view('success')->with($result_data);
		}
		else
		{
		   return view('cancel');
		}
			
	}
	
	
	public function deposit_mollie_success()
	{
	   $sid = 1;
	   $setting['setting'] = Settings::editGeneral($sid);
	   $additional['setting'] = Settings::editAdditional();
	   Mollie::api()->setApiKey($additional['setting']->mollie_api_key);
	   $payment = Mollie::api()->payments()->get(session()->get('payment_id'));
	   $ord_token = session()->get('purchase_token');
	   $payment_token = session()->get('payment_id');
	   if($payment->status == "paid")
	   {
			     
					/* deposite details */
					$payment_date = date('Y-m-d');
					$payment_status = 'completed';
					$updatedata = array('payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_status' => $payment_status);
					Deposit::upDepositdata($ord_token,$updatedata);
					/* deposite details */
				    $additional_view = Settings::editAdditional();
					$amount = $additional_view->deposit_amount;
					$payment_type = $additional_view->deposit_type;
					$user_token = Auth::user()->user_token;
					$buyer_details = Members::editData($user_token);
					$wallet = $buyer_details->earnings + $amount;
					$data = array('earnings' => $wallet);
					Members::updateData($user_token,$data);
					/* currency */
					$deposit_details = Deposit::displaydepositDetails($ord_token);
					$currency = Currencies::getCurrency($deposit_details->currency_type_code);
					$currency_symbol = $currency->currency_symbol;
					$currency_rate = $currency->currency_rate;
					$amount = $amount * $currency_rate;
					$currency = $currency->currency_symbol;
					/* currency */
					$buyer['info'] = Members::singlevendorData(Auth::user()->id);	
					$buyer_name = $buyer['info']->name;
					$buyer_email = $buyer['info']->email;
					$admin_name = $setting['setting']->sender_name;
					$admin_email = $setting['setting']->sender_email;
					$buyer_data = array('buyer_name' => $buyer_name, 'buyer_email' => $buyer_email, 'currency' => $currency, 'amount' => $amount,  'payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_type' => $payment_type);
					/* email template code */
					$checktemp = EmailTemplate::checkTemplate(19);
					if($checktemp != 0)
					{
					   $template_view['mind'] = EmailTemplate::viewTemplate(19);
					   $template_subject = $template_view['mind']->et_subject;
					}
					else
					{
					   $template_subject = "New Deposit Details";
					}
					/* email template code */
					Mail::send('deposit_mail', $buyer_data , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
						$message->to($buyer_email, $buyer_name)
						   ->subject($template_subject);
						$message->from($admin_email,$admin_name);
					});
					$clean = array('deposit_amount' => '', 'deposit_type' => '');
					Settings::updateAdditionData($clean);
					$data_record = array('payment_token' => $payment_token);
					if($this->custom_settings('author_key') == Helper::Key_Owner())
					{
					/* Notification Message */
					$deposit_details = Deposit::displaydepositDetails($ord_token);
					$noti_date = date('Y-m-d H:i:s');
					$noti_sender_id = $buyer['info']->id;
					$noti_receiver_id = 1;
					$noti_message = 'New deposit received send by <strong>'.$buyer['info']->name.'</strong>';
					$savedata = array('noti_date' => $noti_date, 'noti_sender_id' => $noti_sender_id, 'noti_receiver_id' => $noti_receiver_id, 'noti_message' => $noti_message, 'noti_type' => 'deposit', 'noti_type_id' => $deposit_details->dd_id);
					Items::saveNotification($savedata);
					/* Notification Message */
					}
					session()->forget('purchase_token');
                    session()->forget('payment_id');
					return view('success')->with($data_record);

			   
			 }
			 else
			 {
			   session()->forget('purchase_token');
               session()->forget('payment_id');
			   return view('cancel');
			 }
	
			 
			
		
	}
	
	
	
	public function deposit_coingate_success()
	{
	
	   $ord_token = Cache::get('coingate_id');
	   $purchase_id = Cache::get('purchase_id');
	   $sid = 1;
	   $setting['setting'] = Settings::editGeneral($sid);
	   $additional['setting'] = Settings::editAdditional();
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
	   \CoinGate\CoinGate::config(array(
					'environment'               => $coingate_mode_status, // sandbox OR live
					'auth_token'                => $coingate_auth_token,
					'curlopt_ssl_verifypeer'    => TRUE // default is false
					 ));
	   try 
	   {
         $order = \CoinGate\Merchant\Order::find($ord_token);
     
		if ($order) 
		{
		  //echo $order->status;
		 /*var_dump($order); 
		 echo $order->status;
		 echo $order->id;
		 echo $order->order_id;
		 echo $order->payment_address;*/
		 //dd($order); //sara
			  $payment_token = $order->payment_address;
			  $ord_token = $order->order_id;
			 if($order->status == 'paid')
			 {
			     
					/* deposite details */
					$payment_date = date('Y-m-d');
					$payment_status = 'completed';
					$updatedata = array('payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_status' => $payment_status);
					Deposit::upDepositdata($ord_token,$updatedata);
					/* deposite details */
				    $additional_view = Settings::editAdditional();
					$amount = $additional_view->deposit_amount;
					$payment_type = $additional_view->deposit_type;
					$user_token = Auth::user()->user_token;
					$buyer_details = Members::editData($user_token);
					$wallet = $buyer_details->earnings + $amount;
					$data = array('earnings' => $wallet);
					Members::updateData($user_token,$data);
					/* currency */
					$deposit_details = Deposit::displaydepositDetails($ord_token);
					$currency = Currencies::getCurrency($deposit_details->currency_type_code);
					$currency_symbol = $currency->currency_symbol;
					$currency_rate = $currency->currency_rate;
					$amount = $amount * $currency_rate;
					$currency = $currency->currency_symbol;
					/* currency */
					$buyer['info'] = Members::singlevendorData(Auth::user()->id);	
					$buyer_name = $buyer['info']->name;
					$buyer_email = $buyer['info']->email;
					$admin_name = $setting['setting']->sender_name;
					$admin_email = $setting['setting']->sender_email;
					$buyer_data = array('buyer_name' => $buyer_name, 'buyer_email' => $buyer_email, 'currency' => $currency, 'amount' => $amount,  'payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_type' => $payment_type);
					/* email template code */
					$checktemp = EmailTemplate::checkTemplate(19);
					if($checktemp != 0)
					{
					   $template_view['mind'] = EmailTemplate::viewTemplate(19);
					   $template_subject = $template_view['mind']->et_subject;
					}
					else
					{
					   $template_subject = "New Deposit Details";
					}
					/* email template code */
					Mail::send('deposit_mail', $buyer_data , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
						$message->to($buyer_email, $buyer_name)
						   ->subject($template_subject);
						$message->from($admin_email,$admin_name);
					});
					$clean = array('deposit_amount' => '', 'deposit_type' => '');
					Settings::updateAdditionData($clean);
					$data_record = array('payment_token' => $payment_token);
					if($this->custom_settings('author_key') == Helper::Key_Owner())
					{
					/* Notification Message */
					$deposit_details = Deposit::displaydepositDetails($ord_token);
					$noti_date = date('Y-m-d H:i:s');
					$noti_sender_id = $buyer['info']->id;
					$noti_receiver_id = 1;
					$noti_message = 'New deposit received send by <strong>'.$buyer['info']->name.'</strong>';
					$savedata = array('noti_date' => $noti_date, 'noti_sender_id' => $noti_sender_id, 'noti_receiver_id' => $noti_receiver_id, 'noti_message' => $noti_message, 'noti_type' => 'deposit', 'noti_type_id' => $deposit_details->dd_id);
					Items::saveNotification($savedata);
					/* Notification Message */
					}
					return view('success')->with($data_record);

			   
			 }
			 else
			 {
			   return view('cancel');
			 }
	
			 
			
		}
		else 
		{
		  echo 'Order not found';
		}
	} catch (Exception $e) {
		  echo $e->getMessage(); // BadCredentials Not found App by Access-Key
		}
	}
	
	
	public function coingate_success()
	{
	
	   $ord_token = Cache::get('coingate_id');
	   $purchase_id = Cache::get('purchase_id');
	   $sid = 1;
	   $setting['setting'] = Settings::editGeneral($sid);
	   $additional['setting'] = Settings::editAdditional();
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
	   \CoinGate\CoinGate::config(array(
					'environment'               => $coingate_mode_status, // sandbox OR live
					'auth_token'                => $coingate_auth_token,
					'curlopt_ssl_verifypeer'    => TRUE // default is false
					 ));
	   try 
	   {
         $order = \CoinGate\Merchant\Order::find($ord_token);
     
		if ($order) 
		{
		  //echo $order->status;
		 /*var_dump($order); 
		 echo $order->status;
		 echo $order->id;
		 echo $order->order_id;
		 echo $order->payment_address;*/
		 //dd($order); //sara
			$payment_token = $order->payment_address;
			$ord_token = $order->order_id;
			if($order->status == 'paid')
			{
		
				
				$payment_status = 'completed';
				$purchased_token = $ord_token;
				$orderdata = array('payment_token' => $payment_token, 'order_status' => $payment_status);
				$checkoutdata = array('payment_token' => $payment_token, 'payment_status' => $payment_status);
				Items::singleordupdateData($purchased_token,$orderdata);
				Items::singlecheckoutData($purchased_token,$checkoutdata);
				
				$token = $purchased_token;
				$check['display'] = Items::getcheckoutData($token);
				/* customer email */
					$currency = $this->site_currency();
					$admin_name = $setting['setting']->sender_name;
					$admin_email = $setting['setting']->sender_email;
					$customer['info'] = Members::singlevendorData($check['display']->user_id);
					$buyer_name = $customer['info']->name;
					$buyer_email = $customer['info']->email;
					$amount = $check['display']->total;
					$order_id = $check['display']->purchase_token;
					$payment_type = $check['display']->payment_type;
					$payment_date = $check['display']->payment_date;
					$payment_status = $check['display']->payment_status;
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
														  /* email template code */
							Mail::send('item_purchase_mail', $record_customer , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
												$message->to($buyer_email, $buyer_name)
														->subject($template_subject);
												$message->from($admin_email,$admin_name);
											});
					/* customer email */
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
				  }	   
				/* referral per sale earning */	
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
				$result_data = array('payment_token' => $payment_token);
				return view('success')->with($result_data);
			 }
			 else
			 {
			   return view('cancel');
			 }
	
			 
			
		}
		else 
		{
		  echo 'Order not found';
		}
	} catch (Exception $e) {
		  echo $e->getMessage(); // BadCredentials Not found App by Access-Key
		}
	}
	
	
	public function coingate_callback()
	{
	
	   $ord_token = Cache::get('coingate_id');
	   $purchase_id = Cache::get('purchase_id');
	   $seller_details = Items::singleorderToken($purchase_id);
		 $seller_id = $seller_details->item_user_id;
		 $vendor_details = Members::singlevendorData($seller_id);
	   $sid = 1;
	   $setting['setting'] = Settings::editGeneral($sid);
	   
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
	   \CoinGate\CoinGate::config(array(
					'environment'               => $coingate_mode_status, // sandbox OR live
					'auth_token'                => $coingate_auth_token,
					'curlopt_ssl_verifypeer'    => TRUE // default is false
					 ));
	   try 
	   {
         $order = \CoinGate\Merchant\Order::find($ord_token);
     
		if ($order) 
		{
		  //echo $order->status;
		 /*var_dump($order); 
		 echo $order->status;
		 echo $order->id;
		 echo $order->order_id;
		 echo $order->payment_address;*/
		 //dd($order); //sara
			$payment_token = $order->payment_address;
			$ord_token = $order->order_id;
			if($order->status == 'paid')
			{
		
				
				$payment_status = 'completed';
				$purchased_token = $ord_token;
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
					$currency = $this->site_currency();
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
				  }	   
				/* referral per sale earning */	
				$result_data = array('payment_token' => $payment_token);
				return view('success')->with($result_data);
			 }
			 else
			 {
			   return view('cancel');
			 }
	
			 
			
		}
		else 
		{
		  echo 'Order not found';
		}
	} catch (Exception $e) {
		  echo $e->getMessage(); // BadCredentials Not found App by Access-Key
		}
	}
	
	
	
	public function deposit_iyzico_success($ord_token, Request $request)
	{
	
	  
	  $purchase_token = $ord_token;
	  include(app_path() . '/iyzipay-php/IyzipayBootstrap.php');
	  IyzipayBootstrap::init();
	  $options = new \Iyzipay\Options();
	  $additional['setting'] = Settings::editAdditional();
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
      $options->setApiKey($iyzico_api_key);
	  $options->setSecretKey($iyzico_secret_key);
	  $options->setBaseUrl($iyzico_url);
	  
	  # create request class
	  $request = new \Iyzipay\Request\RetrieveCheckoutFormRequest();
	  $request->setLocale(\Iyzipay\Model\Locale::TR);
	  $request->setConversationId($purchase_token);
	  $request->setToken($_REQUEST['token']);
      # make request
      $checkoutForm = \Iyzipay\Model\CheckoutForm::retrieve($request, $options);
      # print result
      //echo $checkoutForm->getPaymentStatus();
      //echo $checkoutForm->getPaymentId();
	  

	$sid = 1;
	$setting['setting'] = Settings::editGeneral($sid);
	$payment_token = $checkoutForm->getPaymentId();
	$payment_status = 'completed';
	$purchased_token = $purchase_token;
	$iyzico_status = $checkoutForm->getPaymentStatus();
	if($iyzico_status == 'SUCCESS')
	{
	                /* deposite details */
					$payment_date = date('Y-m-d');
					$payment_status = 'completed';
					$updatedata = array('payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_status' => $payment_status);
					Deposit::upDepositdata($purchased_token,$updatedata);
					/* deposite details */
	                $additional_view = Settings::editAdditional();
					$amount = $additional_view->deposit_amount;
					$payment_type = $additional_view->deposit_type;
					$user_token = Auth::user()->user_token;
					$buyer_details = Members::editData($user_token);
					$wallet = $buyer_details->earnings + $amount;
					$data = array('earnings' => $wallet);
					Members::updateData($user_token,$data);
					/* currency */
					$deposit_details = Deposit::displaydepositDetails($purchased_token);
					$currency = Currencies::getCurrency($deposit_details->currency_type_code);
					$currency_symbol = $currency->currency_symbol;
					$currency_rate = $currency->currency_rate;
					$amount = $amount * $currency_rate;
					$currency = $currency->currency_symbol;
					/* currency */
					$buyer['info'] = Members::singlevendorData(Auth::user()->id);	
					$buyer_name = $buyer['info']->name;
					$buyer_email = $buyer['info']->email;
					$admin_name = $setting['setting']->sender_name;
					$admin_email = $setting['setting']->sender_email;
					$buyer_data = array('buyer_name' => $buyer_name, 'buyer_email' => $buyer_email, 'currency' => $currency, 'amount' => $amount,  'payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_type' => $payment_type);
					/* email template code */
	          		$checktemp = EmailTemplate::checkTemplate(19);
					if($checktemp != 0)
					{
					$template_view['mind'] = EmailTemplate::viewTemplate(19);
					$template_subject = $template_view['mind']->et_subject;
					}
					else
					{
					$template_subject = "New Deposit Details";
					}
			  	    /* email template code */
					Mail::send('deposit_mail', $buyer_data , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
						$message->to($buyer_email, $buyer_name)
							->subject($template_subject);
						$message->from($admin_email,$admin_name);
					});
						
					$clean = array('deposit_amount' => '', 'deposit_type' => '');
					Settings::updateAdditionData($clean);
					$data_record = array('payment_token' => $payment_token);
					if($this->custom_settings('author_key') == Helper::Key_Owner())
					{
					/* Notification Message */
					$deposit_details = Deposit::displaydepositDetails($purchased_token);
					$noti_date = date('Y-m-d H:i:s');
					$noti_sender_id = $buyer['info']->id;
					$noti_receiver_id = 1;
					$noti_message = 'New deposit received send by <strong>'.$buyer['info']->name.'</strong>';
					$savedata = array('noti_date' => $noti_date, 'noti_sender_id' => $noti_sender_id, 'noti_receiver_id' => $noti_receiver_id, 'noti_message' => $noti_message, 'noti_type' => 'deposit', 'noti_type_id' => $deposit_details->dd_id);
					Items::saveNotification($savedata);
					/* Notification Message */
					}
					return view('success')->with($data_record);

	}
	else
		{
		   return view('cancel');
		}	
	
	}
	
	
	public function iyzico_success($ord_token, Request $request)
	{
	
	  $split = explode("-", $ord_token);
	  $payment_level = $split[0];
	  $purchase_token = $split[1];
	  include(app_path() . '/iyzipay-php/IyzipayBootstrap.php');
	  IyzipayBootstrap::init();
	  $options = new \Iyzipay\Options();
	  if($payment_level == 'admin')
	  {
	  $additional['setting'] = Settings::editAdditional();
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
      $options->setApiKey($iyzico_api_key);
	  $options->setSecretKey($iyzico_secret_key);
	  $options->setBaseUrl($iyzico_url);
	  }
	  else
	  {
	     $seller_details = Items::singleorderToken($purchase_token);
		 $seller_id = $seller_details->item_user_id;
		 $vendor_details = Members::singlevendorData($seller_id);
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
	     $options->setApiKey($iyzico_api_key);
	     $options->setSecretKey($iyzico_secret_key);
	     $options->setBaseUrl($iyzico_url);
	  }
	  # create request class
	  $request = new \Iyzipay\Request\RetrieveCheckoutFormRequest();
	  $request->setLocale(\Iyzipay\Model\Locale::TR);
	  $request->setConversationId($purchase_token);
	  $request->setToken($_REQUEST['token']);
      # make request
      $checkoutForm = \Iyzipay\Model\CheckoutForm::retrieve($request, $options);
      # print result
      //echo $checkoutForm->getPaymentStatus();
      //echo $checkoutForm->getPaymentId();
	  

	$sid = 1;
	$setting['setting'] = Settings::editGeneral($sid);
	$payment_token = $checkoutForm->getPaymentId();
	$payment_status = 'completed';
	$purchased_token = $purchase_token;
	$iyzico_status = $checkoutForm->getPaymentStatus();
	if($iyzico_status == 'SUCCESS')
	{
			$orderdata = array('payment_token' => $payment_token, 'order_status' => $payment_status);
			$checkoutdata = array('payment_token' => $payment_token, 'payment_status' => $payment_status);
			Items::singleordupdateData($purchased_token,$orderdata);
			Items::singlecheckoutData($purchased_token,$checkoutdata);
			
			$token = $purchased_token;
			$check['display'] = Items::getcheckoutData($token);
			/* customer email */
					$currency = $this->site_currency();
					$admin_name = $setting['setting']->sender_name;
					$admin_email = $setting['setting']->sender_email;
					$customer['info'] = Members::singlevendorData($check['display']->user_id);
					$buyer_name = $customer['info']->name;
					$buyer_email = $customer['info']->email;
					$amount = $check['display']->total;
					$order_id = $check['display']->purchase_token;
					$payment_type = $check['display']->payment_type;
					$payment_date = $check['display']->payment_date;
					$payment_status = $check['display']->payment_status;
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
														  /* email template code */
							Mail::send('item_purchase_mail', $record_customer , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
												$message->to($buyer_email, $buyer_name)
														->subject($template_subject);
												$message->from($admin_email,$admin_name);
											});
					/* customer email */
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
						
				$additional['setting'] = Settings::editAdditional();
				$ordered['data'] = Items::singleorderData($order);
				$item_user_id = $ordered['data']->item_user_id;
				$vendor['info'] = Members::singlevendorData($item_user_id);
				$to_name = $vendor['info']->name;
				$to_email = $vendor['info']->email;
				$vendor_amount = $ordered['data']->vendor_amount;
				$count_mode = Settings::checkuserSubscription($item_user_id);
				
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
				
				
				
				
			}
			
			    $additional_setting = Settings::editAdditional();
			    if($additional_setting->affiliate_referral == 1)
				{
					$logged_id = Auth::user()->id;
					$buyer_details = Members::singlebuyerData($logged_id);
					$referral_by = $buyer_details->referral_by;
					
					$referral_commission = $additional_setting->per_sale_referral_commission;
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
			$result_data = array('payment_token' => $payment_token);
			return view('success')->with($result_data); 
		
		}
		else
		{
		   return view('cancel');
		}	
	
	}
	
	
	public function deposit_payhere_success($ord_token, Request $request)
	{
	  $sid = 1;
      $setting['setting'] = Settings::editGeneral($sid);
	  $payment_token = '';
	  /* deposite details */
	  $payment_date = date('Y-m-d');
	  $payment_status = 'completed';
	  $updatedata = array('payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_status' => $payment_status);
	  Deposit::upDepositdata($ord_token,$updatedata);
	  /* deposite details */
	  $additional_view = Settings::editAdditional();
	  $amount = $additional_view->deposit_amount;
	  $payment_type = $additional_view->deposit_type;
	  $user_token = Auth::user()->user_token;
	  $buyer_details = Members::editData($user_token);
	  $wallet = $buyer_details->earnings + $amount;
	  $data = array('earnings' => $wallet);
	  Members::updateData($user_token,$data);
	  /* currency */
	  $deposit_details = Deposit::displaydepositDetails($ord_token);
	  $currency = Currencies::getCurrency($deposit_details->currency_type_code);
	  $currency_symbol = $currency->currency_symbol;
	  $currency_rate = $currency->currency_rate;
	  $amount = $amount * $currency_rate;
	  $currency = $currency->currency_symbol;
	  /* currency */
      $buyer['info'] = Members::singlevendorData(Auth::user()->id);	
	  $buyer_name = $buyer['info']->name;
	  $buyer_email = $buyer['info']->email;
	  $admin_name = $setting['setting']->sender_name;
	  $admin_email = $setting['setting']->sender_email;
	  $buyer_data = array('buyer_name' => $buyer_name, 'buyer_email' => $buyer_email, 'currency' => $currency, 'amount' => $amount,  'payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_type' => $payment_type);
					/* email template code */
	  $checktemp = EmailTemplate::checkTemplate(19);
	  if($checktemp != 0)
	  {
		$template_view['mind'] = EmailTemplate::viewTemplate(19);
		$template_subject = $template_view['mind']->et_subject;
	  }
	  else
	  {
		$template_subject = "New Deposit Details";
	  }
	  /* email template code */
	  Mail::send('deposit_mail', $buyer_data , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
		$message->to($buyer_email, $buyer_name)
							->subject($template_subject);
						$message->from($admin_email,$admin_name);
	  });
	  $clean = array('deposit_amount' => '', 'deposit_type' => '');
	  Settings::updateAdditionData($clean);
	  $data_record = array('payment_token' => $payment_token);
	  if($this->custom_settings('author_key') == Helper::Key_Owner())
	  {
	  /* Notification Message */
	  $deposit_details = Deposit::displaydepositDetails($ord_token);
	  $noti_date = date('Y-m-d H:i:s');
	  $noti_sender_id = $buyer['info']->id;
	  $noti_receiver_id = 1;
	  $noti_message = 'New deposit received send by <strong>'.$buyer['info']->name.'</strong>';
	  $savedata = array('noti_date' => $noti_date, 'noti_sender_id' => $noti_sender_id, 'noti_receiver_id' => $noti_receiver_id, 'noti_message' => $noti_message, 'noti_type' => 'deposit', 'noti_type_id' => $deposit_details->dd_id);
	  Items::saveNotification($savedata);
	  /* Notification Message */
	  }
      return view('success')->with($data_record);
      }
	
	
	public function payhere_success($ord_token, Request $request)
	{
	$sid = 1;
	$setting['setting'] = Settings::editGeneral($sid);
	
	$payment_status = 'completed';
	$purchased_token = $ord_token;
	$orderdata = array( 'order_status' => $payment_status);
	$checkoutdata = array( 'payment_status' => $payment_status);
	Items::singleordupdateData($purchased_token,$orderdata);
	Items::singlecheckoutData($purchased_token,$checkoutdata);
	
	$token = $purchased_token;
	$check['display'] = Items::getcheckoutData($token);
	/* customer email */
					$currency = $this->site_currency();
					$admin_name = $setting['setting']->sender_name;
					$admin_email = $setting['setting']->sender_email;
					$customer['info'] = Members::singlevendorData($check['display']->user_id);
					$buyer_name = $customer['info']->name;
					$buyer_email = $customer['info']->email;
					$amount = $check['display']->total;
					$order_id = $check['display']->purchase_token;
					$payment_type = $check['display']->payment_type;
					$payment_date = $check['display']->payment_date;
					$payment_status = $check['display']->payment_status;
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
														  /* email template code */
							Mail::send('item_purchase_mail', $record_customer , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
												$message->to($buyer_email, $buyer_name)
														->subject($template_subject);
												$message->from($admin_email,$admin_name);
											});
					/* customer email */
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
		}    
    /* referral per sale earning */
	$payment_token = "";
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
	$result_data = array('payment_token' => $payment_token);
	return view('success')->with($result_data);
	
	}
	
	
	/* cashfree */
	
	public function cashfree_success($type,Request $request)
	{
	 
	  $split = explode("-", $type);
	  $payment_level = $split[0];
	  $purchase_token = $split[1];
	  if($payment_level == 'admin')
	  {
	  $additional['setting'] = Settings::editAdditional();
	  $cashfree_api_key = $additional['setting']->cashfree_api_key;
	  $cashfree_api_secret = $additional['setting']->cashfree_api_secret;
	  $cashfree_mode = $additional['setting']->cashfree_mode;
	  }
	  else
	  {
	     $seller_details = Items::singleorderToken($purchase_token);
		 $seller_id = $seller_details->item_user_id;
		 $vendor_details = Members::singlevendorData($seller_id);
		 $cashfree_api_key = $vendor_details->user_cashfree_api_key;
		 $cashfree_api_secret = $vendor_details->user_cashfree_api_secret;
		 $cashfree_mode = $vendor_details->user_cashfree_mode;
	  }
	\Cashfree\Cashfree::$XClientId = $cashfree_api_key;
	\Cashfree\Cashfree::$XClientSecret = $cashfree_api_secret;
	if($cashfree_mode == 0)
	{
	\Cashfree\Cashfree::$XEnvironment = Cashfree\Cashfree::$SANDBOX; //$SANDBOX $PRODUCTION
	}
	else
	{
	\Cashfree\Cashfree::$XEnvironment = Cashfree\Cashfree::$PRODUCTION;
	}
	$cashfree = new \Cashfree\Cashfree();
	$x_api_version = "2023-08-01";
	try {
		$response = $cashfree->PGFetchOrder($x_api_version, $request->get('order_id'));
		$pieces = explode("_", $request->get('order_id'));
	    $purchased_token = $pieces[1];
		$payment_token = '';
	    if($response[0]['order_status'] == 'PAID')
		{
		
		    $sid = 1;
			$setting['setting'] = Settings::editGeneral($sid);
			$payment_status = 'completed';
			$orderdata = array( 'order_status' => $payment_status);
			$checkoutdata = array( 'payment_status' => $payment_status);
			Items::singleordupdateData($purchased_token,$orderdata);
			Items::singlecheckoutData($purchased_token,$checkoutdata);
			
			$token = $purchased_token;
			$check['display'] = Items::getcheckoutData($token);
			/* customer email */
							$currency = $this->site_currency();
							$admin_name = $setting['setting']->sender_name;
							$admin_email = $setting['setting']->sender_email;
							$customer['info'] = Members::singlevendorData($check['display']->user_id);
							$buyer_name = $customer['info']->name;
							$buyer_email = $customer['info']->email;
							$amount = $check['display']->total;
							$order_id = $check['display']->purchase_token;
							$payment_type = $check['display']->payment_type;
							$payment_date = $check['display']->payment_date;
							$payment_status = $check['display']->payment_status;
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
																  /* email template code */
									Mail::send('item_purchase_mail', $record_customer , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
														$message->to($buyer_email, $buyer_name)
																->subject($template_subject);
														$message->from($admin_email,$admin_name);
													});
							/* customer email */
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
			   }	   
			/* referral per sale earning */
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
			$payment_token = "";	
			$result_data = array('payment_token' => $payment_token);
			return view('success')->with($result_data);

		}
		else
		{
		   return view('cancel');
		}
		
		
	  } catch (Exception $e) {
		echo 'Exception when calling PGFetchOrder: ', $e->getMessage(), PHP_EOL;
	 }	
	
		
	}
	
	/* cashfree */
	
	
	/* midtrans */
	
	public function midtrans_success($ord_token, Request $request)
	{
	$transaction_status = $request->input('transaction_status');
	$order_identity = $request->input('order_id');
	if($transaction_status == 'capture')
	{
		$sid = 1;
		$setting['setting'] = Settings::editGeneral($sid);
		
		$payment_status = 'completed';
		$purchased_token = $order_identity;
		$orderdata = array( 'order_status' => $payment_status);
		$checkoutdata = array( 'payment_status' => $payment_status);
		Items::singleordupdateData($purchased_token,$orderdata);
		Items::singlecheckoutData($purchased_token,$checkoutdata);
		
		$token = $purchased_token;
		$check['display'] = Items::getcheckoutData($token);
		/* customer email */
						$currency = $this->site_currency();
						$admin_name = $setting['setting']->sender_name;
						$admin_email = $setting['setting']->sender_email;
						$customer['info'] = Members::singlevendorData($check['display']->user_id);
						$buyer_name = $customer['info']->name;
						$buyer_email = $customer['info']->email;
						$amount = $check['display']->total;
						$order_id = $check['display']->purchase_token;
						$payment_type = $check['display']->payment_type;
						$payment_date = $check['display']->payment_date;
						$payment_status = $check['display']->payment_status;
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
															  /* email template code */
								Mail::send('item_purchase_mail', $record_customer , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
													$message->to($buyer_email, $buyer_name)
															->subject($template_subject);
													$message->from($admin_email,$admin_name);
												});
						/* customer email */
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
		   }	   
		/* referral per sale earning */
		$payment_token = "";	
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
		$result_data = array('payment_token' => $payment_token);
		return view('success')->with($result_data);
	}
	else
	{
	   return redirect('/cancel');
	}
	
	}
	
	
	public function deposit_midtrans_success($ord_token, Request $request)
	{
	$transaction_status = $request->input('transaction_status');
	$order_identity = $request->input('order_id');
	if($transaction_status == 'capture')
	{
		$sid = 1;
		$setting['setting'] = Settings::editGeneral($sid);
		$payment_token = "";
		/* deposite details */
		$payment_date = date('Y-m-d');
		$payment_status = 'completed';
		$updatedata = array('payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_status' => $payment_status);
		Deposit::upDepositdata($order_identity,$updatedata);
		/* deposite details */
		$additional_view = Settings::editAdditional();
		$amount = $additional_view->deposit_amount;
		$payment_type = $additional_view->deposit_type;
		$user_token = Auth::user()->user_token;
		$buyer_details = Members::editData($user_token);
		$old_earning = $buyer_details->earnings;
		$wallet = $old_earning + $amount;
		$data = array('earnings' => $wallet);
		Members::updateData($user_token,$data);
		/* currency */
		$deposit_details = Deposit::displaydepositDetails($order_identity);
		$currency = Currencies::getCurrency($deposit_details->currency_type_code);
		$currency_symbol = $currency->currency_symbol;
		$currency_rate = $currency->currency_rate;
		$amount = $amount * $currency_rate;
		$currency = $currency->currency_symbol;
		/* currency */
		$buyer['info'] = Members::singlevendorData(Auth::user()->id);	
		$buyer_name = $buyer['info']->name;
		$buyer_email = $buyer['info']->email;
		$admin_name = $setting['setting']->sender_name;
		$admin_email = $setting['setting']->sender_email;
		$buyer_data = array('buyer_name' => $buyer_name, 'buyer_email' => $buyer_email, 'currency' => $currency, 'amount' => $amount,  'payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_type' => $payment_type);
		/* email template code */
		$checktemp = EmailTemplate::checkTemplate(19);
		if($checktemp != 0)
		{
		   $template_view['mind'] = EmailTemplate::viewTemplate(19);
		   $template_subject = $template_view['mind']->et_subject;
		}
		else
		{
		   $template_subject = "New Deposit Details";
		}
		/* email template code */
		Mail::send('deposit_mail', $buyer_data , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
			$message->to($buyer_email, $buyer_name)
			   ->subject($template_subject);
			$message->from($admin_email,$admin_name);
		});
		$clean = array('deposit_amount' => '', 'deposit_type' => '');
		Settings::updateAdditionData($clean);
		$data_record = array('payment_token' => $payment_token);
		if($this->custom_settings('author_key') == Helper::Key_Owner())
		{
		/* Notification Message */
		$deposit_details = Deposit::displaydepositDetails($ord_token);
		$noti_date = date('Y-m-d H:i:s');
		$noti_sender_id = $buyer['info']->id;
		$noti_receiver_id = 1;
		$noti_message = 'New deposit received send by <strong>'.$buyer['info']->name.'</strong>';
		$savedata = array('noti_date' => $noti_date, 'noti_sender_id' => $noti_sender_id, 'noti_receiver_id' => $noti_receiver_id, 'noti_message' => $noti_message, 'noti_type' => 'deposit', 'noti_type_id' => $deposit_details->dd_id);
		Items::saveNotification($savedata);
		/* Notification Message */
		}
		return view('success')->with($data_record);
	}
	else
	{
	   return redirect('/cancel');
	}
		
		
	}
	
	/* midtrans */
	
	
	public function deposit_payfast_success($ord_token, Request $request)
	{
	$sid = 1;
	$setting['setting'] = Settings::editGeneral($sid);
	$payment_token = "";
	/* deposite details */
	$payment_date = date('Y-m-d');
	$payment_status = 'completed';
	$updatedata = array('payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_status' => $payment_status);
	Deposit::upDepositdata($ord_token,$updatedata);
	/* deposite details */
	$additional_view = Settings::editAdditional();
	$amount = $additional_view->deposit_amount;
	$payment_type = $additional_view->deposit_type;
	$user_token = Auth::user()->user_token;
	$buyer_details = Members::editData($user_token);
	$old_earning = $buyer_details->earnings;
	$wallet = $old_earning + $amount;
	$data = array('earnings' => $wallet);
	Members::updateData($user_token,$data);
	/* currency */
	$deposit_details = Deposit::displaydepositDetails($ord_token);
	$currency = Currencies::getCurrency($deposit_details->currency_type_code);
	$currency_symbol = $currency->currency_symbol;
	$currency_rate = $currency->currency_rate;
	$amount = $amount * $currency_rate;
	$currency = $currency->currency_symbol;
	/* currency */
	$buyer['info'] = Members::singlevendorData(Auth::user()->id);	
	$buyer_name = $buyer['info']->name;
	$buyer_email = $buyer['info']->email;
	$admin_name = $setting['setting']->sender_name;
	$admin_email = $setting['setting']->sender_email;
	$buyer_data = array('buyer_name' => $buyer_name, 'buyer_email' => $buyer_email, 'currency' => $currency, 'amount' => $amount,  'payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_type' => $payment_type);
	/* email template code */
	$checktemp = EmailTemplate::checkTemplate(19);
	if($checktemp != 0)
	{
	   $template_view['mind'] = EmailTemplate::viewTemplate(19);
	   $template_subject = $template_view['mind']->et_subject;
	}
	else
	{
	   $template_subject = "New Deposit Details";
	}
	/* email template code */
	Mail::send('deposit_mail', $buyer_data , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
		$message->to($buyer_email, $buyer_name)
		   ->subject($template_subject);
		$message->from($admin_email,$admin_name);
	});
	$clean = array('deposit_amount' => '', 'deposit_type' => '');
	Settings::updateAdditionData($clean);
	$data_record = array('payment_token' => $payment_token);
	if($this->custom_settings('author_key') == Helper::Key_Owner())
	{
	/* Notification Message */
	$deposit_details = Deposit::displaydepositDetails($ord_token);
	$noti_date = date('Y-m-d H:i:s');
	$noti_sender_id = $buyer['info']->id;
	$noti_receiver_id = 1;
	$noti_message = 'New deposit received send by <strong>'.$buyer['info']->name.'</strong>';
	$savedata = array('noti_date' => $noti_date, 'noti_sender_id' => $noti_sender_id, 'noti_receiver_id' => $noti_receiver_id, 'noti_message' => $noti_message, 'noti_type' => 'deposit', 'noti_type_id' => $deposit_details->dd_id);
	Items::saveNotification($savedata);
	/* Notification Message */
	}
	return view('success')->with($data_record);
	}
	
	public function payfast_success($ord_token, Request $request)
	{
	$sid = 1;
	$setting['setting'] = Settings::editGeneral($sid);
	
	$payment_status = 'completed';
	$purchased_token = $ord_token;
	$orderdata = array( 'order_status' => $payment_status);
	$checkoutdata = array( 'payment_status' => $payment_status);
	Items::singleordupdateData($purchased_token,$orderdata);
	Items::singlecheckoutData($purchased_token,$checkoutdata);
	
	$token = $purchased_token;
	$check['display'] = Items::getcheckoutData($token);
	/* customer email */
					$currency = $this->site_currency();
					$admin_name = $setting['setting']->sender_name;
					$admin_email = $setting['setting']->sender_email;
					$customer['info'] = Members::singlevendorData($check['display']->user_id);
					$buyer_name = $customer['info']->name;
					$buyer_email = $customer['info']->email;
					$amount = $check['display']->total;
					$order_id = $check['display']->purchase_token;
					$payment_type = $check['display']->payment_type;
					$payment_date = $check['display']->payment_date;
					$payment_status = $check['display']->payment_status;
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
														  /* email template code */
							Mail::send('item_purchase_mail', $record_customer , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
												$message->to($buyer_email, $buyer_name)
														->subject($template_subject);
												$message->from($admin_email,$admin_name);
											});
					/* customer email */
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
    /* referral per sale earning */
	$payment_token = "";	
	$result_data = array('payment_token' => $payment_token);
	return view('success')->with($result_data);
	
	}

	/* checkout */
	
	
	/* purchases */
	
	public function view_purchases()
	{
	  $orderData['item'] = Items::getuserOrders();
	  $order['purchase'] = Items::getpurchaseCheckout();
	  
	  $purchase_sale = 0;
	  foreach($order['purchase'] as $item)
	  {
	    $purchase_sale += $item->total;
	  }
	  	  
	  $drawal['view'] = Items::getdrawalView();
	  $drawal_amount = 0;
	  foreach($drawal['view'] as $drawal)
	  {
	    $drawal_amount += $drawal->wd_amount;
	  }
	  $countdata = Chat::getgroupchatData();
	  return view('purchases',[ 'orderData' => $orderData, 'purchase_sale' => $purchase_sale, 'drawal_amount' => $drawal_amount, 'countdata' => $countdata]); 
	 
	}
	
	
	
	public function purchases_download($token,$order_id)
	{
	    $today_date = date('Y-m-d');
		$download_count_checks = Members::checkdownloadDate(Auth::user()->id,$today_date);
		if(Auth::user()->user_subscr_download_item > $download_count_checks->user_today_download_limit)
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
				$logged_id = Auth::user()->id;
				$check_sold = Items::checkSold($token,$logged_id);
				
				
				$downoad_count = Auth::user()->user_today_download_limit + 1;
		        $up_level_download = array('user_today_download_limit' => $downoad_count);
		        Members::updateReferral(Auth::user()->id,$up_level_download);
				
				if($check_sold != 0)
				{
					$item['data'] = Items::solditemData($token);
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
					    ->where('filename', '=', pathinfo($filename, PATHINFO_FILENAME))
					    ->where('extension', '=', pathinfo($filename, PATHINFO_EXTENSION))
					    ->first(); 
					    $display_product_file = Storage::disk('google')->download($file['path']);
			            return $display_product_file;
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
					   
					   $order_details = Items::singleorderData($order_id);
		               $pdf_filename = $order_details->ord_id.'-'.$order_details->purchase_token.'-'.$item['data']->item_slug.'-serial-key'.'.pdf';
		  			   $serial_keys = $order_details->item_order_serial_key;
					   $serial_key = str_replace(",", "\n", $serial_keys);
		               $data = ['serial_key' => $serial_key];
                       $pdf = PDF::loadView('serial_view', $data);  
                       return $pdf->download($pdf_filename);
					}
					else
					{
					   return redirect($item['data']->item_file_link);
					}
				}
				else
				{
				return redirect('404');
				}
		}
		else
		{
		   return redirect()->back()->with('error', 'Sorry! Today your download limit reached. please check your profile page');
		}		
	}
	
	
	public function rating_purchases(Request $request)
	{
	  $item_id = $request->input('item_id');
	  $item_token = $request->input('item_token');
	  $user_id = $request->input('user_id');
	  $item_user_id = $request->input('item_user_id');
	  $rating = $request->input('rating');
	  $ord_id = $request->input('ord_id');
	  $rating_reason = $request->input('rating_reason');
	  $item_url = $request->input('item_url');
	  $rating_date = date('Y-m-d H:i:s');
	  
	  $rating_comment = $request->input('rating_comment');
	  $rating_count = Items::checkRating($item_token,$user_id);
	  
	  $savedata = array('or_item_id' => $item_id, 'order_id' => $ord_id, 'or_item_token' => $item_token, 'or_user_id' => $user_id, 'or_item_user_id' => $item_user_id, 'rating' => $rating, 'rating_reason' => $rating_reason, 'rating_comment' => $rating_comment, 'rating_date' => $rating_date); 
	  
	  $updata = array('rating' => $rating, 'rating_reason' => $rating_reason, 'rating_comment' => $rating_comment, 'rating_date' => $rating_date); 
	  
	  if($rating_count == 0)
	  {
	  
	    Items::saveRating($savedata);
		$userto['data'] = Members::singlevendorData($item_user_id);
		$userfrom['data'] = Members::singlebuyerData($user_id);
		$to_email = $userto['data']->email;
		$to_name  = $userto['data']->name;
		$buyer_review = $userto['data']->buyer_review_email;
		if($buyer_review == 1)
		{
		  $from_email = $userfrom['data']->email;
		  $from_name = $userfrom['data']->name;
		  $sid = 1;
		  $setting['setting'] = Settings::editGeneral($sid);
		  $admin_name = $setting['setting']->sender_name;
		  $admin_email = $setting['setting']->sender_email;
		  $check_email_support = Members::getuserSubscription($item_user_id);
		  if($check_email_support == 1)
		  {
			  $record = array('to_name' => $to_name, 'from_name' => $from_name, 'from_email' => $from_email, 'item_url' => $item_url, 'rating' => $rating, 'rating_reason' => $rating_reason, 'rating_comment' => $rating_comment);
			  /* email template code */
	          $checktemp = EmailTemplate::checkTemplate(7);
			  if($checktemp != 0)
			  {
			  $template_view['mind'] = EmailTemplate::viewTemplate(7);
			  $template_subject = $template_view['mind']->et_subject;
			  }
			  else
			  {
			  $template_subject = "Item Rating Received";
			  }
			  /* email template code */
			  Mail::send('rating_mail', $record, function($message) use ($admin_name, $admin_email, $to_email, $from_email, $to_name, $from_name, $template_subject) {
					$message->to($to_email, $to_name)
							->subject($template_subject);
					$message->from($from_email,$from_name);
				});
		   }		
		
	    }
		
		
		 
	  }
	  else
	  {
	     Items::updateRating($item_token,$user_id,$updata);
	  }
	  
	  return redirect('purchases')->with('success','Rating has been updated');
	
	}
	
	/* purchases */
	
	
	/* sales */
	
	public function view_sales()
	{
	  $orderData['item'] = Items::getuserCheckout();
	  
	  $total_sale = 0;
	  
	  foreach($orderData['item'] as $item)
	  {
	    //$total_sale += $item->total;
		$total_payment = explode(',', $item->item_single_prices);
		foreach($total_payment as $payment)
		{
		  $total_sale += (int) $payment;
		}
	  }
	  
	  $order['purchase'] = Items::getpurchaseCheckout();
	  
	  $purchase_sale = 0;
	  foreach($order['purchase'] as $item)
	  {
	    //$purchase_sale += $item->total;
		$total_payment = explode(',', $item->item_single_prices);
		foreach($total_payment as $payment)
		{
		  $purchase_sale += $payment;
		}
		
	  }  
	  
	  $credit['order'] = Items::getcreditOrder(); 
	  
	  $credit_amount = 0;
	  foreach($credit['order'] as $order)
	  {
	    $credit_amount += $order->item_single_vendor_price;
	  }
	  
	  
	  $drawal['view'] = Items::getdrawalView();
	  $drawal_amount = 0;
	  foreach($drawal['view'] as $drawal)
	  {
	    $drawal_amount += $drawal->wd_amount;
	  }
	  
	  return view('sales',[ 'orderData' => $orderData, 'total_sale' => $total_sale, 'purchase_sale' => $purchase_sale, 'credit_amount' => $credit_amount, 'drawal_amount' => $drawal_amount]); 
	 
	}
	
	
	
	public function view_order_details($token)
	{
	  $checkout['view'] = Items::singlecheckoutView($token);
	  $order['view'] = Items::getorderView($token);
	  $countdata = Chat::getgroupchatData();
	  $currency_sign = $checkout['view']->currency_type;
	  return view('order-details',[ 'checkout' => $checkout, 'order' => $order, 'countdata' => $countdata, 'currency_sign' => $currency_sign]);
	}
	
	
	/* sales */
	
	
	
	/* refund */
	
	public function refund_request(Request $request)
	{
	  $item_id = $request->input('item_id');
	  $item_token = $request->input('item_token');
	  $purchased_token = $request->input('purchased_token');
	  $user_id = $request->input('user_id');
	  $item_user_id = $request->input('item_user_id');
	  $ord_id = $request->input('ord_id');
	  $ref_refund_reason = $request->input('refund_reason');
	  $ref_refund_comment = $request->input('refund_comment');
	  $item_url = $request->input('item_url');
	  $refund_count = Items::checkRefund($item_token,$user_id);
	  
	  $savedata = array('ref_item_id' => $item_id, 'ref_order_id' => $ord_id, 'ref_item_token' => $item_token, 'ref_purchased_token' => $purchased_token,  'ref_user_id' => $user_id, 'ref_item_user_id' => $item_user_id, 'ref_refund_reason' => $ref_refund_reason, 'ref_refund_comment' => $ref_refund_comment); 
	  
	  
	  
	  if($refund_count == 0)
	  {
	    Items::saveRefund($savedata);
		$userfrom['data'] = Members::singlebuyerData($user_id);
		$from_email = $userfrom['data']->email;
		$from_name = $userfrom['data']->name;
		$sid = 1;
		$setting['setting'] = Settings::editGeneral($sid);
		$admin_name = $setting['setting']->sender_name;
		$admin_email = $setting['setting']->sender_email;
		$record = array('from_name' => $from_name, 'from_email' => $from_email, 'item_url' => $item_url, 'ref_refund_reason' => $ref_refund_reason, 'ref_refund_comment' => $ref_refund_comment);
		/* email template code */
	          $checktemp = EmailTemplate::checkTemplate(8);
			  if($checktemp != 0)
			  {
			  $template_view['mind'] = EmailTemplate::viewTemplate(8);
			  $template_subject = $template_view['mind']->et_subject;
			  }
			  else
			  {
			  $template_subject = "Refund Request Received";
			  }
			  /* email template code */
		Mail::send('refund_mail', $record, function($message) use ($admin_name, $admin_email, $from_email, $from_name, $template_subject) {
				$message->to($admin_email, $admin_name)
						->subject($template_subject);
				$message->from($from_email,$from_name);
			});
		
		
		
	    return redirect('purchases')->with('success','Your refund request has been sent successfully');
	  }
	  else
	  {
	     
		 return redirect('purchases')->with('error','Sorry! Your refund request already sent');
	  }
	  
	  
	  
	
	}
	
	/* refund */
	
	
	/* destroyed */
	
	public function filedestroy(Request $request)
    {
	   
	   $filename =  $request->get('filename');
	   Items::fileDeleted($filename);
	   
	   $allsettings = Settings::allSettings();
        $watermark = $allsettings->site_watermark;
        $url = URL::to("/");
	    $item_token = $request->get('item_token');
		$logged_id = Auth::user()->id;
	    $additional = Settings::editAdditional();
		if(!empty($item_token))
	 	{
	 		$edit['item'] = Items::edititemData($item_token);
	 		$item_image['item'] = Items::getimagesData($item_token);
	 	}
		$session_id = Session::getId();
		$getdata['first'] = Items::getProdutData($session_id); 
		 $getdata['second'] = Items::getProdutData($session_id);  
		 $getdata['third'] = Items::getProdutZip($session_id);   
		 $getdata['four'] = Items::getProdutData($session_id);   
		 $getdata['five'] = Items::getProdutMP4($session_id);
		 $getdata['six'] = Items::getProdutMP3($session_id);
		 $record = '<div class="row">
		           <div class="col-sm-6"> 
				   <div class="form-group">
                    <label for="site_desc" class="control-label mb-1">Upload Thumbnail (Size : 80x80px) <span class="require">*</span></label><br/>
                    <select name="item_thumbnail2" id="item_thumbnail2" class="form-control">
                    <option value=""></option>';
                    foreach($getdata['first'] as $get)
					{
					   $record .= '<option value="'.$get->item_file_name.'">'.$get->original_file_name.'</option>';
					}
                    $record .= '</select>';
                   if(!empty($item_token))
				   {
						if($edit['item']->item_thumbnail!='')
						{
							$record .='<img class="lazy" width="80" height="80" src="'.$this->Image_Path($edit['item']->item_thumbnail,'no-image.png').'" data-original="'.$this->Image_Path($edit['item']->item_thumbnail,'no-image.png').'" alt="'.$edit['item']->item_name.'">';
						}
						else
						{
							$record .='<img class="lazy" width="80" height="80" src="'.url('/').'/public/img/no-image.png" data-original="'.url('/').'/public/img/no-image.png" alt="'.$edit['item']->item_name.'">';
						}
                   } 
				   $record .= '</div></div>';
		 $record .= '<div class="col-sm-6">
		            <div class="form-group">
                    <label for="site_desc" class="control-label mb-1">Upload Preview (Size : 361x230px) <span class="require">*</span></label><br/>
                    <select name="item_preview2" id="item_preview2" class="form-control">
                    <option value=""></option>';
                    foreach($getdata['second'] as $get)
					{
					   $record .= '<option value="'.$get->item_file_name.'">'.$get->original_file_name.'</option>';
					}
                    $record .= '</select>';
                   if(!empty($item_token))
				   {
						if($edit['item']->item_preview!='')
						{
							$record .='<img class="lazy" width="80" height="80" src="'.$this->Image_Path($edit['item']->item_preview,'no-image.png').'" data-original="'.$this->Image_Path($edit['item']->item_preview,'no-image.png').'" alt="'.$edit['item']->item_name.'">';
						}
						else
						{
							$record .='<img class="lazy" width="80" height="80" src="'.url('/').'/public/img/no-image.png" data-original="'.url('/').'/public/img/no-image.png" alt="'.$edit['item']->item_name.'">';
						}
                   } 
				   $record .= '</div></div>';
				   
									 if($additional->show_screenshots == 1)
									 {		
                                     $record .= '<div class="col-sm-6">
									            <div class="form-group">
                                                <label for="customer_earnings" class="control-label mb-1">Upload Screenshots (multiple) (Size : 750x430px)</label>
                                                <select id="item_screenshot2" name="item_screenshot[]" class="form-control" multiple>';
												foreach($getdata['four'] as $get)
												{
												$record .= '<option value="'.$get->item_file_name.'">'.$get->original_file_name.'</option>';
												}
                                                $record .= '</select>';
												if(!empty($item_token))
											   {
											      $alerttext = 'Are you sure you want to delete?';
											      foreach($item_image['item'] as $item)
												  {
												      $record .= '<div class="item-img" style="float:left;margin:10px;"><img class="lazy" width="80" height="80" src="'.$this->Image_Path($item->item_image,'no-image.png').'" data-original="'.$this->Image_Path($item->item_image,'no-image.png').'" alt="'.$item->item_image.'">';
													  $record .='<a href="'.url('/edit-item').'/dropimg/'.base64_encode($item->itm_id).'" onClick="return confirm("'.$alerttext.'");" class="drop-icon"><span class="dwg-trash drop-icon"></span></a></div>';
													  
												  }
											   }
                                             $record .= '<div class="clearfix"></div></div></div>';
		                            }
									if($additional->show_video == 1)
									{
									$record .='<div class="col-sm-6">
									            <div class="form-group">
                                                <label for="name" class="control-label mb-1">Preview Type (optional)</label>
                                               <select name="video_preview_type2" id="video_preview_type2" class="form-control">
                                                <option value=""></option>';
												if(!empty($item_token))
												{
												   if($edit['item']->video_preview_type == 'youtube')
												   {
												   $record .= '<option value="youtube" selected>Youtube</option>
                                                               <option value="mp4">MP4</option>
															   <option value="mp3">MP3</option>';
												   }
												   else if($edit['item']->video_preview_type == 'mp4')
												   {
													 $record .= '<option value="youtube">Youtube</option>
                                                                 <option value="mp4" selected>MP4</option>
																 <option value="mp3">MP3</option>';
												   }
												   else if($edit['item']->video_preview_type == 'mp3')
												   {
													 $record .= '<option value="youtube">Youtube</option>
                                                                 <option value="mp4">MP4</option>
																 <option value="mp3" selected>MP3</option>';
												   }
												   else
												   {
												      $record .= '<option value="youtube">Youtube</option>
                                                                 <option value="mp4">MP4</option>
																 <option value="mp3">MP3</option>';
												   }
												}
												else
												{
												    $record .= '<option value="youtube">Youtube</option>
                                                                <option value="mp4">MP4</option>
																<option value="mp3">MP3</option>';
												}
                                                    
                                                $record .= '</select>
                                            </div></div>'; 
											if(!empty($item_token))
								             {
												   if($edit['item']->video_preview_type == 'youtube')
												   {
												   $record .= '<div class="col-sm-6 display-block" id="youtube"><div class="form-group">';
												   }
												   else if($edit['item']->video_preview_type == 'mp4')
												   {
													 $record .= '<div class="col-sm-6 display-none" id="youtube"><div class="form-group">';
												   }
												   else if($edit['item']->video_preview_type == 'mp3')
												   {
													 $record .= '<div class="col-sm-6 display-none" id="youtube"><div class="form-group">';
												   }
												   else
												   {
													  $record .= '<div class="col-sm-6 display-none" id="youtube"><div class="form-group">';
												   }
												}
												else
												{			
												$record .= '<div class="col-sm-6 display-none" id="youtube"><div class="form-group">';
												}		   
									            if(!empty($item_token))
												 {
												   if(!empty($edit['item']->video_url))
												   {
												   $video_linked = $edit['item']->video_url;
												   }
												   else
												   {
													  $video_linked = "";
												   }
												 }
												 else
												 {
												  $video_linked = "";
												 } 
                                                $record .='<label for="name" class="control-label mb-1">Youtube Video URL <span class="require">*</span></label>
                                                
                                                <input type="text" id="video_url2" name="video_url2" class="form-control" value="'.$video_linked.'" data-bvalidator="required">
                                                 <small>(example : https://www.youtube.com/watch?v=C0DPdy98e4c)</small>
                                            </div></div>';
									if(!empty($item_token))
								             {
												   if($edit['item']->video_preview_type == 'youtube')
												   {
												   $record .= '<div class="col-sm-6 display-none" id="mp4"><div  class="form-group">';
												   }
												   else if($edit['item']->video_preview_type == 'mp4')
												   {
													 $record .= '<div class="col-sm-6 display-block" id="mp4"><div class="form-group">';
												   }
												   else if($edit['item']->video_preview_type == 'mp3')
												   {
													 $record .= '<div class="col-sm-6 display-none" id="mp4"><div class="form-group">';
												   }
												   else
												   {
													  $record .= '<div class="col-sm-6 display-none" id="mp4"><div class="form-group">';
												   }
												}
												else
												{			
												$record .= '<div class="col-sm-6 display-none" id="mp4"><div class="form-group">';
												}		
									
                                    $record .='<label for="site_desc" class="control-label mb-1">Upload MP4 Video <span class="require">*</span></label><br/>
                                                <select id="video_file2" name="video_file2" class="form-control">
												<option value=""></option>';
                                                foreach($getdata['five'] as $get)
												{
                                                $record .= '<option value="'.$get->item_file_name.'">'.$get->original_file_name.'</option>';
												}
                                                $record .= '</select>';
												if(!empty($item_token))
											    {
											     if($edit['item']->video_file!='')
												 {
												    $record .= '<span class="require">'.$edit['item']->video_file.'</span>';
												 }
                                               }
									$record .= '</div></div>';		   
									if(!empty($item_token))
								             {
												   if($edit['item']->video_preview_type == 'youtube')
												   {
												   $record .= '<div class="col-sm-6 display-none" id="mp3"><div  class="form-group">';
												   }
												   else if($edit['item']->video_preview_type == 'mp4')
												   {
													 $record .= '<div class="col-sm-6 display-none" id="mp3"><div class="form-group">';
												   }
												   else if($edit['item']->video_preview_type == 'mp3')
												   {
													 $record .= '<div class="col-sm-6 display-block" id="mp3"><div class="form-group">';
												   }
												   else
												   {
													  $record .= '<div class="col-sm-6 display-none" id="mp3"><div class="form-group">';
												   }
												}
												else
												{			
												$record .= '<div class="col-sm-6 display-none" id="mp3"><div class="form-group">';
												}		   
									$record .='<label for="site_desc" class="control-label mb-1">Upload MP3<span class="require">*</span></label><br/>
                                                <select id="audio_file2" name="audio_file2" class="form-control">
												<option value=""></option>';
                                                foreach($getdata['six'] as $get)
												{
                                                $record .= '<option value="'.$get->item_file_name.'">'.$get->original_file_name.'</option>';
												}
                                                $record .= '</select>';
												if(!empty($item_token))
											    {
											     if($edit['item']->audio_file!='')
												 {
												    $record .= '<span class="require">'.$edit['item']->audio_file.'</span>';
												 }
                                               }		   
                                             $record .= '</div></div>';				
									}
									$record .= '<div class="col-sm-6">
				                 <div class="form-group">
                                                <label for="name" class="control-label mb-1">Upload Main File Type <span class="require">*</span></label>
                                               <select name="file_type2" id="file_type2" class="form-control" data-bvalidator="required">
                                                <option value=""></option>';
												if(!empty($item_token))
												{
												   if($edit['item']->file_type == 'file')
												   {
												   $record .= '<option value="file" selected>File</option>
												               <option value="link">Link / URL</option>
															   <option value="serial">License Keys / Serial Numbers</option>';
												   }
												   else if($edit['item']->file_type == 'link')
												   {
													 $record .= '<option value="file">File</option>
												                 <option value="link" selected>Link / URL</option>
																 <option value="serial">License Keys / Serial Numbers</option>';
												   }
												   else
												   {
												      $record .= '<option value="file">File</option>
												                  <option value="link">Link / URL</option>
																  <option value="serial" selected>License Keys / Serial Numbers</option>';
												   }
												}
												else
												{
												    $record .= '<option value="file">File</option>
												            <option value="link">Link / URL</option>
															<option value="serial">License Keys / Serial Numbers</option>';
												}
                                                $record .= '</select></div></div>';
								if(!empty($item_token))
								{
								   if($edit['item']->file_type == 'file')
								   {
								   $record .= '<div class="col-sm-6 display-block" id="main_file"><div class="form-group">';
								   }
								   else if($edit['item']->file_type == 'link')
								   {
								     $record .= '<div class="col-sm-6 display-none" id="main_file"><div class="form-group">';
								   }
								   else if($edit['item']->file_type == 'serial')
								   {
								     $record .= '<div  class="col-sm-6 display-none" id="main_file"><div class="form-group">';
								   }
								   else
								   {
								      $record .= '<div class="col-sm-6" id="main_file"><div class="form-group">';
								   }
								}
								else
								{			
								$record .= '<div class="col-sm-6" id="main_file"><div class="form-group">';
								}			
				   
                               $record .= '<label for="customer_earnings" class="control-label mb-1">Upload Main File<span class="require">*</span></label>
                                                <select name="item_file2" id="item_file2" class="form-control">
                                                <option value=""></option>';
												foreach($getdata['third'] as $get)
												{
												$record .= '<option value="'.$get->item_file_name.'">'.$get->original_file_name.'</option>';
												}
                                                $record .= '</select>';
											 if(!empty($item_token))
											 {
											     if($edit['item']->item_file!='')
												 {
												    $record .= '<span class="require">'.$edit['item']->item_file.'</span>';
												 }
                                             }
											 $record .= '</div></div>';
											 if(!empty($item_token))
								             {
												   if($edit['item']->file_type == 'file')
												   {
												   $record .= '<div class="col-sm-6 display-none" id="main_link"><div  class="form-group">';
												   }
												   else if($edit['item']->file_type == 'link')
												   {
													 $record .= '<div class="col-sm-6 display-block" id="main_link"><div class="form-group">';
												   }
												   else if($edit['item']->file_type == 'serial')
												   {
													 $record .= '<div class="col-sm-6 display-none" id="main_link"><div  class="form-group">';
												   }
												   else
												   {
													  $record .= '<div class="col-sm-6" id="main_link"><div class="form-group">';
												   }
												}
												else
												{			
												$record .= '<div class="col-sm-6" id="main_link"><div class="form-group">';
												}
												if(!empty($item_token))
												 {
												   if(!empty($edit['item']->item_file_link))
												   {
												   $item_file_linked = $edit['item']->item_file_link;
												   }
												   else
												   {
													  $item_file_linked = "";
												   }
												 }
												 else
												 {
												  $item_file_linked = "";
												 }
                                                $record .= '<label for="name" class="control-label mb-1">Main File Link/URL <span class="require">*</span></label>
                                                <input type="text" id="item_file_link2" name="item_file_link2" class="form-control" value="'.$item_file_linked.'" data-bvalidator="required,url">
                                              </div>  
                                            </div></div>';
								    $record .= '<script>
									        $("#hint_comma").hide();
	                                        $("#hint_line").hide();
											$("#file_type2").on("change", function() {
												  if ( this.value == "file")
												  {
													$("#main_file").show();
													$("#main_link").hide();
													$("#main_delimiter").hide();
													$("#main_serials").hide();
												  }
												  else if(this.value == "link")
												  {
													$("#main_file").hide();
													$("#main_link").show();
													$("#main_delimiter").hide();
													$("#main_serials").hide();
												  }
												  else if(this.value == "serial")
												  {
													$("#main_file").hide();
													$("#main_link").hide();
													$("#main_delimiter").show();
													$("#main_serials").show();
													$("#free_download option[value=0]").prop("selected", true); 
		                                            $("#item_support option[value=1]").prop("selected", true);
												  }
												  else
												  {
													$("#main_file").hide();
													$("#main_link").hide();
													$("#main_delimiter").hide();
													$("#main_serials").hide();
												  }
												});	
												$("#item_delimiter1").on("change", function() {
												  if ( this.value == "comma")
												  {
													 $("#hint_comma").show();
													 $("#hint_line").hide();
												  }
												  else if ( this.value == "newline")
												  {
													 $("#hint_comma").hide();
													 $("#hint_line").show();
												  }
												  else
												  {
													 $("#hint_comma").hide();
													 $("#hint_line").hide();
												  }
												 });
											$("#video_preview_type2").on("change", function() {
												  if ( this.value == "youtube")
												  
												  {
													 $("#youtube").show();
													 $("#mp4").hide();
													 $("#mp3").hide();
												  }	
												  else if ( this.value == "mp4")
												  {
													 $("#mp4").show();
													 $("#youtube").hide();
													 $("#mp3").hide();
												  }
												  else if ( this.value == "mp3")
												  {
													 $("#mp3").show();
													 $("#youtube").hide();
													 $("#mp4").hide();
												  }
												  else
												  {
													  $("#mp4").hide();
													  $("#youtube").hide();
													  $("#mp3").hide();
												  }
												  
												 });
											
											</script>';
		 return response()->json(['success' => true, 'record' => $record]);

		 
	
	}
	
	/* destroyed */
	
	public function file_download($token)
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
			$logged_id = Auth::user()->id;
			$checkif = Items::checkInSeller($token,$logged_id);
			if($checkif != 0)
			{	
				$item['data'] = Items::selleritemData($token,$logged_id);
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
			 else
			 {
				return redirect($item['data']->item_file_link);
			 }
		}
		else
		{
		    return redirect('404');
		}		
				
	}
	
	
	public function view_deposit()
	{
	  $sid = 1;
	  $setting['setting'] = Settings::editGeneral($sid);
	  $get_payment = explode(',', $setting['setting']->payment_option);
	  $deposit['view'] = Deposit::viewdepositData();
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
	  return view('deposit',['deposit' => $deposit, 'get_payment' => $get_payment, 'stripe_publish' => $stripe_publish, 'stripe_type' => $stripe_type]);
	}
	
	/* coinbase */
	
	public function coinbase_success($ordtoken, Request $request)
	{
	$encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
	$ord_token   = $encrypter->decrypt($ordtoken);
	$sid = 1;
	$setting['setting'] = Settings::editGeneral($sid);
	$payment_token = '';
	$payment_status = 'completed';
	$purchased_token = $ord_token;
	$orderdata = array('payment_token' => $payment_token, 'order_status' => $payment_status);
	$checkoutdata = array('payment_token' => $payment_token, 'payment_status' => $payment_status);
	Items::singleordupdateData($purchased_token,$orderdata);
	Items::singlecheckoutData($purchased_token,$checkoutdata);
	
	$token = $purchased_token;
	$check['display'] = Items::getcheckoutData($token);
	/* customer email */
					$currency = $this->site_currency();
					$admin_name = $setting['setting']->sender_name;
					$admin_email = $setting['setting']->sender_email;
					$customer['info'] = Members::singlevendorData($check['display']->user_id);
					$buyer_name = $customer['info']->name;
					$buyer_email = $customer['info']->email;
					$amount = $check['display']->total;
					$order_id = $check['display']->purchase_token;
					$payment_type = $check['display']->payment_type;
					$payment_date = $check['display']->payment_date;
					$payment_status = $check['display']->payment_status;
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
														  /* email template code */
							Mail::send('item_purchase_mail', $record_customer , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
												$message->to($buyer_email, $buyer_name)
														->subject($template_subject);
												$message->from($admin_email,$admin_name);
											});
					/* customer email */
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
	   }	   
    /* referral per sale earning */	
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
	$result_data = array('payment_token' => $payment_token);
	return view('success')->with($result_data);
	
	}
	
	/* nowpayments */
	
	public function nowpayments_success($ordtoken, Request $request)
	{
	$encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
	$ord_token   = $encrypter->decrypt($ordtoken);
	$sid = 1;
	$setting['setting'] = Settings::editGeneral($sid);
	$payment_token = '';
	$payment_status = 'completed';
	$purchased_token = $ord_token;
	$orderdata = array('payment_token' => $payment_token, 'order_status' => $payment_status);
	$checkoutdata = array('payment_token' => $payment_token, 'payment_status' => $payment_status);
	Items::singleordupdateData($purchased_token,$orderdata);
	Items::singlecheckoutData($purchased_token,$checkoutdata);
	
	$token = $purchased_token;
	$check['display'] = Items::getcheckoutData($token);
	/* customer email */
					$currency = $this->site_currency();
					$admin_name = $setting['setting']->sender_name;
					$admin_email = $setting['setting']->sender_email;
					$customer['info'] = Members::singlevendorData($check['display']->user_id);
					$buyer_name = $customer['info']->name;
					$buyer_email = $customer['info']->email;
					$amount = $check['display']->total;
					$order_id = $check['display']->purchase_token;
					$payment_type = $check['display']->payment_type;
					$payment_date = $check['display']->payment_date;
					$payment_status = $check['display']->payment_status;
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
														  /* email template code */
							Mail::send('item_purchase_mail', $record_customer , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
												$message->to($buyer_email, $buyer_name)
														->subject($template_subject);
												$message->from($admin_email,$admin_name);
											});
					/* customer email */
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
	   }	   
    /* referral per sale earning */	
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
	$result_data = array('payment_token' => $payment_token);
	return view('success')->with($result_data);
	
	}
	
	
	public function deposit_nowpayments_success($ordtoken, Request $request)
	{
	    $encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
	    $ord_token   = $encrypter->decrypt($ordtoken);
		$sid = 1;
		$setting['setting'] = Settings::editGeneral($sid);
		$payment_token = "";
		$payment_date = date('Y-m-d');
		$payment_status = 'completed';
		$updatedata = array('payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_status' => $payment_status);
		Deposit::upDepositdata($ord_token,$updatedata);
		
	    $additional_view = Settings::editAdditional();
		$amount = $additional_view->deposit_amount;
		$payment_type = $additional_view->deposit_type;
		$user_token = Auth::user()->user_token;
		$buyer_details = Members::editData($user_token);
		$wallet = $buyer_details->earnings + $amount;
		$data = array('earnings' => $wallet);
		Members::updateData($user_token,$data);
		/* currency */
		$deposit_details = Deposit::displaydepositDetails($ord_token);
		$currency = Currencies::getCurrency($deposit_details->currency_type_code);
		$currency_symbol = $currency->currency_symbol;
		$currency_rate = $currency->currency_rate;
		$amount = $amount * $currency_rate;
		$currency = $currency->currency_symbol;
		/* currency */
		$buyer['info'] = Members::singlevendorData(Auth::user()->id);	
		$buyer_name = $buyer['info']->name;
		$buyer_email = $buyer['info']->email;
		$admin_name = $setting['setting']->sender_name;
		$admin_email = $setting['setting']->sender_email;
		$buyer_data = array('buyer_name' => $buyer_name, 'buyer_email' => $buyer_email, 'currency' => $currency, 'amount' => $amount,  'payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_type' => $payment_type);
		
		$checktemp = EmailTemplate::checkTemplate(19);
		if($checktemp != 0)
		{
		   $template_view['mind'] = EmailTemplate::viewTemplate(19);
		   $template_subject = $template_view['mind']->et_subject;
		}
		else
		{
		   $template_subject = "New Deposit Details";
		}
		
		Mail::send('deposit_mail', $buyer_data , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
			$message->to($buyer_email, $buyer_name)
			   ->subject($template_subject);
			$message->from($admin_email,$admin_name);
		});
		$clean = array('deposit_amount' => '', 'deposit_type' => '');
		Settings::updateAdditionData($clean);
		$data_record = array('payment_token' => $payment_token);
		if($this->custom_settings('author_key') == Helper::Key_Owner())
		{
		/* Notification Message */
		$deposit_details = Deposit::displaydepositDetails($ord_token);
		$noti_date = date('Y-m-d H:i:s');
		$noti_sender_id = $buyer['info']->id;
		$noti_receiver_id = 1;
		$noti_message = 'New deposit received send by <strong>'.$buyer['info']->name.'</strong>';
		$savedata = array('noti_date' => $noti_date, 'noti_sender_id' => $noti_sender_id, 'noti_receiver_id' => $noti_receiver_id, 'noti_message' => $noti_message, 'noti_type' => 'deposit', 'noti_type_id' => $deposit_details->dd_id);
		Items::saveNotification($savedata);
		/* Notification Message */
		}
		return view('success')->with($data_record);
		
		
	}
	
	/* nowpayments */ 
	
	
	
	public function deposit_coinbase_success($ordtoken, Request $request)
	{
	    $encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
	    $ord_token   = $encrypter->decrypt($ordtoken);
		$sid = 1;
		$setting['setting'] = Settings::editGeneral($sid);
		$payment_token = "";
		$payment_date = date('Y-m-d');
		$payment_status = 'completed';
		$updatedata = array('payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_status' => $payment_status);
		Deposit::upDepositdata($ord_token,$updatedata);
		
	    $additional_view = Settings::editAdditional();
		$amount = $additional_view->deposit_amount;
		$payment_type = $additional_view->deposit_type;
		$user_token = Auth::user()->user_token;
		$buyer_details = Members::editData($user_token);
		$wallet = $buyer_details->earnings + $amount;
		$data = array('earnings' => $wallet);
		Members::updateData($user_token,$data);
		/* currency */
		$deposit_details = Deposit::displaydepositDetails($ord_token);
		$currency = Currencies::getCurrency($deposit_details->currency_type_code);
		$currency_symbol = $currency->currency_symbol;
		$currency_rate = $currency->currency_rate;
		$amount = $amount * $currency_rate;
		$currency = $currency->currency_symbol;
		/* currency */
		$buyer['info'] = Members::singlevendorData(Auth::user()->id);	
		$buyer_name = $buyer['info']->name;
		$buyer_email = $buyer['info']->email;
		$admin_name = $setting['setting']->sender_name;
		$admin_email = $setting['setting']->sender_email;
		$buyer_data = array('buyer_name' => $buyer_name, 'buyer_email' => $buyer_email, 'currency' => $currency, 'amount' => $amount,  'payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_type' => $payment_type);
		
		$checktemp = EmailTemplate::checkTemplate(19);
		if($checktemp != 0)
		{
		   $template_view['mind'] = EmailTemplate::viewTemplate(19);
		   $template_subject = $template_view['mind']->et_subject;
		}
		else
		{
		   $template_subject = "New Deposit Details";
		}
		
		Mail::send('deposit_mail', $buyer_data , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
			$message->to($buyer_email, $buyer_name)
			   ->subject($template_subject);
			$message->from($admin_email,$admin_name);
		});
		$clean = array('deposit_amount' => '', 'deposit_type' => '');
		Settings::updateAdditionData($clean);
		$data_record = array('payment_token' => $payment_token);
		if($this->custom_settings('author_key') == Helper::Key_Owner())
		{
		/* Notification Message */
		$deposit_details = Deposit::displaydepositDetails($ord_token);
		$noti_date = date('Y-m-d H:i:s');
		$noti_sender_id = $buyer['info']->id;
		$noti_receiver_id = 1;
		$noti_message = 'New deposit received send by <strong>'.$buyer['info']->name.'</strong>';
		$savedata = array('noti_date' => $noti_date, 'noti_sender_id' => $noti_sender_id, 'noti_receiver_id' => $noti_receiver_id, 'noti_message' => $noti_message, 'noti_type' => 'deposit', 'noti_type_id' => $deposit_details->dd_id);
		Items::saveNotification($savedata);
		/* Notification Message */
		}
		return view('success')->with($data_record);
		
		
	}
	
	
	/* coinbase */
	
	
	/* mercadopago */
	
	public function deposit_mercadopago_success($ord_token, Request $request)
	{
		$sid = 1;
		$setting['setting'] = Settings::editGeneral($sid);
		$pay_status = $request->input('status');
	    if($pay_status == 'approved')
		{
		$payment_token = $request->input('payment_id');
		
		$payment_date = date('Y-m-d');
		$payment_status = 'completed';
		$updatedata = array('payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_status' => $payment_status);
		Deposit::upDepositdata($ord_token,$updatedata);
		
	    $additional_view = Settings::editAdditional();
		$amount = $additional_view->deposit_amount;
		$payment_type = $additional_view->deposit_type;
		$user_token = Auth::user()->user_token;
		$buyer_details = Members::editData($user_token);
		$wallet = $buyer_details->earnings + $amount;
		$data = array('earnings' => $wallet);
		Members::updateData($user_token,$data);
		/* currency */
		$deposit_details = Deposit::displaydepositDetails($ord_token);
		$currency = Currencies::getCurrency($deposit_details->currency_type_code);
		$currency_symbol = $currency->currency_symbol;
		$currency_rate = $currency->currency_rate;
		$amount = $amount * $currency_rate;
		$currency = $currency->currency_symbol;
		/* currency */
		$buyer['info'] = Members::singlevendorData(Auth::user()->id);	
		$buyer_name = $buyer['info']->name;
		$buyer_email = $buyer['info']->email;
		$admin_name = $setting['setting']->sender_name;
		$admin_email = $setting['setting']->sender_email;
		$buyer_data = array('buyer_name' => $buyer_name, 'buyer_email' => $buyer_email, 'currency' => $currency, 'amount' => $amount,  'payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_type' => $payment_type);
		
		$checktemp = EmailTemplate::checkTemplate(19);
		if($checktemp != 0)
		{
		   $template_view['mind'] = EmailTemplate::viewTemplate(19);
		   $template_subject = $template_view['mind']->et_subject;
		}
		else
		{
		   $template_subject = "New Deposit Details";
		}
		
		Mail::send('deposit_mail', $buyer_data , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
			$message->to($buyer_email, $buyer_name)
			   ->subject($template_subject);
			$message->from($admin_email,$admin_name);
		});
		$clean = array('deposit_amount' => '', 'deposit_type' => '');
		Settings::updateAdditionData($clean);
		$data_record = array('payment_token' => $payment_token);
		if($this->custom_settings('author_key') == Helper::Key_Owner())
		{
		/* Notification Message */
		$deposit_details = Deposit::displaydepositDetails($ord_token);
		$noti_date = date('Y-m-d H:i:s');
		$noti_sender_id = $buyer['info']->id;
		$noti_receiver_id = 1;
		$noti_message = 'New deposit received send by <strong>'.$buyer['info']->name.'</strong>';
		$savedata = array('noti_date' => $noti_date, 'noti_sender_id' => $noti_sender_id, 'noti_receiver_id' => $noti_receiver_id, 'noti_message' => $noti_message, 'noti_type' => 'deposit', 'noti_type_id' => $deposit_details->dd_id);
		Items::saveNotification($savedata);
		/* Notification Message */
		}
		return view('success')->with($data_record);
		}
		else
		{
		return view('failure');
		}
		
	}
	
	/* mercadopago */
	
	public function coinbase_deposit(Request $request)
    {   
	    $encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
	    $additional['setting'] = Settings::editAdditional();
        $postdata = file_get_contents("php://input");
        $res = json_decode($postdata);
        $ord_token = $res->event->data->metadata->trx;
		$deposit_details = Deposit::displaydepositDetails($ord_token);
		$coinbase_secret_key = $additional['setting']->coinbase_secret_key;
		$headers = apache_request_headers();
        $sentSign = $headers['x-cc-webhook-signature'];
        $sig = hash_hmac('sha256', $postdata, $coinbase_secret_key);
        if ($sentSign == $sig) {
            if ($res->event->type == 'charge:confirmed' && $deposit_details->payment_status == 'pending') 
			{
			    
				return redirect('/deposit-coinbase/'.$encrypter->encrypt($ord_token));
                
            }
        }
    }
	
	public function show_deposit(Request $request)
	{
	   $encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
	   $currency_type = $encrypter->decrypt($request->input('currency_type'));
	   $currency_type_code = $encrypter->decrypt($request->input('currency_type_code'));
	   $currency_rate = $encrypter->decrypt($request->input('currency_rate'));
	   
	   $sid = 1;
	   $setting['setting'] = Settings::editGeneral($sid);
	   $additional_view = Settings::editAdditional();
	   $get_payment = explode(',', $setting['setting']->payment_option);
	   $totaldata = array('get_payment' => $get_payment);
	   $order_firstname = $request->input('order_firstname');
	   $order_email = $request->input('order_email');
	   $token = $request->input('token');
	   $purchase_token = rand(111111,999999);
	   $user_id = Auth::user()->id;
	   
	   $fin_amount = $encrypter->decrypt($request->input('amount'));
	   $pieces = explode("_",$fin_amount);
	   $final_amount = $pieces[0] * $currency_rate;
	   $final_amount = round($final_amount,2);
	   /* currency conversion */
	   $default_final_amount = $pieces[0];
	   $default_final_amount = round($default_final_amount,2);
	   /* currency conversion */
	   $total_amount = $pieces[0] + $pieces[1];
	   $total_amount = round($total_amount,2);
	   $bonus_amount = $pieces[1] * $currency_rate;
	   $bonus_amount = round($bonus_amount,2);
	   $deposit_single_price = round($pieces[0],2);
	   $deposit_single_bonus = round($pieces[1],2);
	   $user_email = Auth::user()->email;
	   $user_token = Auth::user()->user_token;
	   $user_id = Auth::user()->id;
	   $payment_method = $request->input('payment_method');
	   $website_url = $request->input('website_url');
	   $payment_date = date('Y-m-d');
	   $reference = $request->input('reference');
	   
	   
	   if($payment_method != "localbank")
	   {
	   $dataview = array('deposit_amount' => $total_amount, 'deposit_type' => $payment_method); 
	   Settings::updateAdditionData($dataview);
	   }
	   
	   $deposit['view'] = Deposit::viewdepositData();
	   
	   /* settings */
	   
	   $paypal_email = $setting['setting']->paypal_email;
	   $paypal_mode = $setting['setting']->paypal_mode;
	   $site_currency = $currency_type_code;
	   if($paypal_mode == 1)
	   {
	     $paypal_url = "https://www.paypal.com/cgi-bin/webscr";
	   }
	   else
	   {
	     $paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
	   }
	   $success_url = $website_url.'/deposit-success/'.$purchase_token;
	   $cancel_url = $website_url.'/cancel';
	   
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
	   
	   
	   $two_checkout_mode = $setting['setting']->two_checkout_mode;
	   $two_checkout_account = $setting['setting']->two_checkout_account;
	   $two_checkout_publishable = $setting['setting']->two_checkout_publishable;
	   $two_checkout_private = $setting['setting']->two_checkout_private;
	   
	  
	   
	   /* settings */
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
       $payhere_success_url = $website_url.'/deposit-payhere-success/'.$purchase_token;
	   
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
		/* flutterwave */
	   $flutterwave_public_key = $additional_view->flutterwave_public_key;
	   $flutterwave_secret_key = $additional_view->flutterwave_secret_key;
	   /* flutterwave */
	   /* coingate */
	   $coingate_mode = $additional_view->coingate_mode;
	   if($coingate_mode == 0)
	   {
	       $coingate_mode_status = "sandbox";
	   }
	   else
	   {
	       $coingate_mode_status = "live";
	   }
	   $coingate_auth_token = $additional_view->coingate_auth_token;
	   $coingate_callback = $website_url.'/deposit-coingate';
	   /* coingate */
	   /* ipay */
	   $ipay_mode = $additional_view->ipay_mode;
	   $ipay_vendor_id = $additional_view->ipay_vendor_id;
	   $ipay_hash_key = $additional_view->ipay_hash_key;
	   $ipay_callback = $website_url.'/deposit-ipay';
	   $ipay_url = 'https://payments.ipayafrica.com/v3/ke';
	   /* ipay */
	   
	   /* iyzico */
	   $iyzico_api_key = $additional_view->iyzico_api_key;
	   $iyzico_secret_key = $additional_view->iyzico_secret_key;
	   $iyzico_mode = $additional_view->iyzico_mode;
	   if($iyzico_mode == 0)
	   {
		  $iyzico_url = 'https://sandbox-api.iyzipay.com';
	   }
	   else
	   {
		  $iyzico_url = 'https://api.iyzipay.com';
	   }
	   $iyzico_success_url = $website_url.'/deposit-iyzico-success/'.$purchase_token;
	   /* iyzico */
	   /* payfast */
		$payfast_mode = $additional_view->payfast_mode;
		$payfast_merchant_id = $additional_view->payfast_merchant_id;
		$payfast_merchant_key = $additional_view->payfast_merchant_key;
		if($payfast_mode == 1)
		{
			$payfast_url = "https://www.payfast.co.za/eng/process";
		}
		else
		{
			$payfast_url = "https://sandbox.payfast.co.za/eng/process";
		}
		$payfast_success_url = $website_url.'/deposit-payfast-success/'.$purchase_token;	   
		/* payfast */
		/* coinpayments */
		$coinpayments_merchant_id = $additional_view->coinpayments_merchant_id;
		/* coinpayments */
		$coinpayments_success_url = $website_url.'/deposit-coinpayments-success/'.$purchase_token;
		/* instamojo */
		$instamojo_success_url = $website_url.'/deposit-instamojo-success/'.$purchase_token;
		if($additional_view->instamojo_mode == 1)
		{
			$instamojo_payment_link = 'https://instamojo.com/api/1.1/payment-requests/';
		}
		else
		{ 
			$instamojo_payment_link = 'https://test.instamojo.com/api/1.1/payment-requests/';
		}
		$instamojo_api_key = $additional_view->instamojo_api_key;
		$instamojo_auth_token = $additional_view->instamojo_auth_token;
		/* instamojo */
		/* aamarpay */
		$aamarpay_mode = $additional_view->aamarpay_mode;
		$aamarpay_store_id = $additional_view->aamarpay_store_id;
		$aamarpay_signature_key = $additional_view->aamarpay_signature_key;
		if($aamarpay_mode == 1)
		{
		   $aamarpay_url = "http://secure.aamarpay.com/index.php";
		}
		else
		{
		   $aamarpay_url = "https://sandbox.aamarpay.com/index.php";
		}
		$aamarpay_success_url = $website_url.'/deposit-aamarpay/'.$purchase_token;
		$aamarpay_cancel_url = $website_url.'/deposit-aamarpay/'.$purchase_token;
		$aamarpay_failed_url = $website_url.'/deposit-aamarpay/'.$purchase_token;
		/* aamarpay */
		
		/* mollie */
		if($additional_view->mollie_api_key != "")
		{
		Mollie::api()->setApiKey($additional_view->mollie_api_key);
		}
		$mollie_success_url = $website_url.'/deposit-mollie';
		/* mollie */
		
		/* robokassa */
		$shop_identifier = $additional_view->shop_identifier;
		$robokassa_password_1 = $additional_view->robokassa_password_1;
		/* robokassa */
		
		/* mercadopago */
		$mercadopago_client_id = $additional_view->mercadopago_client_id;
	   	$mercadopago_client_secret = $additional_view->mercadopago_client_secret;
	   	$mercadopago_mode = $additional_view->mercadopago_mode;
	   	$mercadopago_success = $website_url.'/deposit-mercadopago/'.$purchase_token;
	   	$mercadopago_failure = $website_url.'/failure';
	   	$mercadopago_pending = $website_url.'/pending';
	    /* mercadopago */
		
		/* midtrans */
		$midtrans_mode = $additional_view->midtrans_mode;
		$midtrans_server_key = $additional_view->midtrans_server_key;
		$midtrans_success = $website_url.'/deposit-midtrans/'.$purchase_token;
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
		$coinbase_api_key = $additional_view->coinbase_api_key;
		$coinbase_success = $website_url.'/deposit-coinbase/'.$encrypter->encrypt($purchase_token);
		$coinbase_webhooks = $website_url.'/webhooks/coinbase-deposit';
		/* coinbase */
		
		/* cashfree */
	   $cashfree_api_key = $additional_view->cashfree_api_key;
	   $cashfree_api_secret = $additional_view->cashfree_api_secret;
	   $cashfree_mode = $additional_view->cashfree_mode;
	   $cashfree_success = $website_url.'/deposit-cashfree/';
	   /* cashfree */
		
	   /* nowpayments */
	   $nowpayments_api_key = $additional_view->nowpayments_api_key;
	   $nowpayments_ipn_secret = $additional_view->nowpayments_ipn_secret;
	   $nowpayments_mode = $additional_view->nowpayments_mode;
	   $nowpayments_success = $website_url.'/deposit-nowpayments/'.$encrypter->encrypt($purchase_token);
	   /* nowpayments */	
		
	   
	   $stripe_publish = $stripe_publish_key;
	   $razorpay_key = $additional_view->razorpay_key;
	   $totaldata = array('deposit' => $deposit, 'get_payment' => $get_payment, 'stripe_publish' => $stripe_publish);
	   $item_names_data = "Deposit";
	   $payment_status = 'pending';
	   
	   $getcount = Deposit::getdepositCount($purchase_token,$user_id,$payment_status);
	   
	   $savedata = array('user_id' => $user_id, 'purchase_token' => $purchase_token, 'payment_type' => $payment_method, 'payment_date' => $payment_date, 'deposit_price' => $final_amount, 'deposit_bonus' => $bonus_amount, 'payment_status' => $payment_status, 'currency_type' => $currency_type, 'currency_type_code' => $currency_type_code, 'deposit_single_price' => $deposit_single_price, 'deposit_single_bonus' => $deposit_single_bonus);
	   
	   $updatedata = array('payment_type' => $payment_method, 'payment_date' => $payment_date, 'deposit_price' => $final_amount, 'deposit_bonus' => $bonus_amount, 'currency_type' => $currency_type, 'currency_type_code' => $currency_type_code, 'deposit_single_price' => $deposit_single_price, 'deposit_single_bonus' => $deposit_single_bonus);
	   
	   if($getcount == 0)
	   {
	   Deposit::saveDepositDetails($savedata);
	   }
	   else
	   {
	    Deposit::updatedepositData($purchase_token,$user_id,$payment_status,$updatedata);
	   }
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
					//'amount' => 'required|numeric',
					//'currency' => 'required|in:BTC,ETH,LTC',
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
		  else if($payment_method == 'localbank')
		  {
		    $bank_details = $setting['setting']->local_bank_details;
		    $bank_data = array('purchase_token' => $purchase_token, 'bank_details' => $bank_details);
	        return view('bank-details')->with($bank_data);
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
			 session()->put('robokassa_type','deposit');
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
					->setUrl([route('deposit.sslcommerz'), route('sslcommerz.failurepage'), route('sslcommerz.cancelpage'), route('sslcommerz.ipnpage')])
					->setCurrency('BDT');
				return $sslc->make_payment();
				//BDT

        /**
         * 
         *  USE:  $sslc->make_payment(true) FOR CHECKOUT INTEGRATION
         * 
         * */
		  }
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
                    "eml"=> Auth::user()->email,
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
		       $flutterwave_callback = $website_url.'/deposit-flutterwave';
			   $phone_number = "";
			   $csf_token = csrf_token();
			   $flutterwave = '<form method="post" id="flutterwave_form" action="https://checkout.flutterwave.com/v3/hosted/pay">
	          <input type="hidden" name="public_key" value="'.$flutterwave_public_key.'" />
	          <input type="hidden" name="customer[email]" value="'.$user_email.'" >
			  <input type="hidden" name="customer[phone_number]" value="'.$phone_number.'" />
			  <input type="hidden" name="customer[name]" value="'.Auth::user()->name.'" />
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
			 $userids = Auth::user()->id;
			 $usernamer = Auth::user()->name;
             $response = $client->request('GET', $endpoint, ['query' => [
				'iyzico_api_key' => $api_key, 
				'iyzico_secret_key' => $secret_key,
				'iyzico_url' => $iyzi_url,
				'purchase_token' => $purchased_token,
				'price_amount' => $amount,
				'user_id' => $userids,
				'username' => $usernamer,
				'email' => $user_email,
				'user_token' => Auth::user()->user_token,
				'item_name' => $item_names_data,
				'iyzico_success_url' => $iyzico_success_url,
				
			]]);
        
            echo $response->getBody();
            
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
					'surl' => $website_url.'/deposit_payu_success',
					'furl' => $website_url.'/cancel',
					'service_provider' => 'payu_paisa',
				);
				$payu_success = $website_url.'/deposit_payu_success';
				
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
							<input type="hidden" name="last_name" value="'.$order_firstname.'"><br>
							<input type="hidden" name="email" value="'.$order_email.'">
							<input type="hidden" name="phone" value="'.$order_firstname.'"><br>
							<input type="hidden" name="address" value="'.$order_firstname.'">
							<input type="hidden" name="city" value="'.$order_firstname.'">
							<input type="hidden" name="country" value="'.$order_firstname.'">
							  
						</form>'; 
						$payhere .= '<script>window.payhere_form.submit();</script>';
			            echo $payhere;
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
			   $callback = $website_url.'/deposit-razorpay';
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
		       $callback = $website_url.'/deposit-paystack';
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
			  <input type="hidden" name="x_receipt_link_url" value="deposit_item" />
			  <input type="hidden" name="email" value="'.Auth::user()->email.'" />
			  </form>';
			$two_checkout .= '<script>window.two_checkout_form.submit();</script>';
			echo $two_checkout;
			
			/*$record = array('final_amount' => $final_amount, 'purchase_token' => $purchase_token, 'payment_method' => $payment_method, 'item_names_data' => $item_names_data, 'site_currency' => $site_currency, 'website_url' => $website_url, 'two_checkout_private' => $two_checkout_private, 'two_checkout_account' => $two_checkout_account, 'two_checkout_mode' => $two_checkout_mode, 'token' => $token, 'two_checkout_publishable' => $two_checkout_publishable);
       return view('order-confirm')->with($record);*/
			
		  }
		  /* stripe code */
		  else if($payment_method == 'stripe')
		  {
		     if($setting['setting']->stripe_type == 'intents') // Intents API
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
					'name' => $order_firstname,
					'description' => $item_names_data,        
					'email' => $order_email,
					"address" => ["city" => "", "country" => "", "line1" => $order_email, "line2" => "", "postal_code" => "", "state" => ""],
					'shipping' => [
						  'name' => $order_firstname,
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
							'name' => $order_firstname,
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
				  
			       $data = array('stripe_publish' => $stripe_publish_key, 'stripe_secret' => $stripe_secret_key, 'intent' => $intent, 'myamount' => $myamount, 'final_amount' => $final_amount, 'site_currency' => $site_currency, 'purchase_token' => $purchase_token);
	   
	   
	              return view('stripe-deposit')->with($data); 

			}
			else  // Charges API
			{
			  
			    $stripe = array(
					"secret_key"      => $stripe_secret_key,
					"publishable_key" => $stripe_publish_key
				);
			 
				\Stripe\Stripe::setApiKey($stripe['secret_key']);
			 
				$customer = \Stripe\Customer::create(array( 
					'name' => $order_firstname.' '.$order_firstname,
					'description' => $item_names_data,        
					'email' => $order_email, 
					'source'  => $token,
					'customer' => $order_email, 
					"address" => ["city" => "", "country" => "", "line1" => $item_names_data, "line2" => $item_names_data, "postal_code" => "", "state" => ""],
					'shipping' => [
						  'name' => $order_firstname.' '.$order_firstname,
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
					$additional_view = Settings::editAdditional();
					$amount = $additional_view->deposit_amount;
					$payment_type = $additional_view->deposit_type;
					$user_token = Auth::user()->user_token;
					$buyer_details = Members::editData($user_token);
					$wallet = $buyer_details->earnings + $amount;
					$data = array('earnings' => $wallet);
					Members::updateData($user_token,$data);
					$currency = $this->site_currency();
					$buyer['info'] = Members::singlevendorData(Auth::user()->id);	
					$buyer_name = $buyer['info']->name;
					$buyer_email = $buyer['info']->email;
					$payment_date = date('Y-m-d');
					$admin_name = $setting['setting']->sender_name;
					$admin_email = $setting['setting']->sender_email;
					$buyer_data = array('buyer_name' => $buyer_name, 'buyer_email' => $buyer_email, 'currency' => $currency, 'amount' => $amount,  'payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_type' => $payment_type);
					/* email template code */
	          		$checktemp = EmailTemplate::checkTemplate(19);
					if($checktemp != 0)
					{
					$template_view['mind'] = EmailTemplate::viewTemplate(19);
					$template_subject = $template_view['mind']->et_subject;
					}
					else
					{
					$template_subject = "New Deposit Details";
					}
			  	    /* email template code */
					Mail::send('deposit_mail', $buyer_data , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
						$message->to($buyer_email, $buyer_name)
							->subject($template_subject);
						$message->from($admin_email,$admin_name);
					});
						
					$clean = array('deposit_amount' => '', 'deposit_type' => '');
					Settings::updateAdditionData($clean);
					$data_record = array('payment_token' => $payment_token);
					return view('success')->with($data_record);
					
					
				}
			
			}
			
				 
		  
		  }
		  /* stripe code */

	   return view('deposit')->with($totaldata);
	
	
	}
	
	
	/* cashfree */
	public function deposit_cashfree_success(Request $request)
	{
	$sid = 1;
	$setting['setting'] = Settings::editGeneral($sid);
	$additional['setting'] = Settings::editAdditional();
	$cashfree_api_key = $additional['setting']->cashfree_api_key;
	$cashfree_api_secret = $additional['setting']->cashfree_api_secret;
	$cashfree_mode = $additional['setting']->cashfree_mode;
	\Cashfree\Cashfree::$XClientId = $cashfree_api_key;
	\Cashfree\Cashfree::$XClientSecret = $cashfree_api_secret;
	if($cashfree_mode == 0)
	{
	\Cashfree\Cashfree::$XEnvironment = Cashfree\Cashfree::$SANDBOX; //$SANDBOX $PRODUCTION
	}
	else
	{
	\Cashfree\Cashfree::$XEnvironment = Cashfree\Cashfree::$PRODUCTION;
	}
	$cashfree = new \Cashfree\Cashfree();
	$x_api_version = "2023-08-01";
	try {
		$response = $cashfree->PGFetchOrder($x_api_version, $request->get('order_id'));
		$pieces = explode("_", $request->get('order_id'));
	    $ord_token = $pieces[1];
		$payment_token = '';
	    if($response[0]['order_status'] == 'PAID')
		{
		    $payment_date = date('Y-m-d');
			$payment_status = 'completed';
			$updatedata = array('payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_status' => $payment_status);
			Deposit::upDepositdata($ord_token,$updatedata);
			
			$additional_view = Settings::editAdditional();
			$amount = $additional_view->deposit_amount;
			$payment_type = $additional_view->deposit_type;
			$user_token = Auth::user()->user_token;
			$buyer_details = Members::editData($user_token);
			$wallet = $buyer_details->earnings + $amount;
			$data = array('earnings' => $wallet);
			Members::updateData($user_token,$data);
			/* currency */
			$deposit_details = Deposit::displaydepositDetails($ord_token);
			$currency = Currencies::getCurrency($deposit_details->currency_type_code);
			$currency_symbol = $currency->currency_symbol;
			$currency_rate = $currency->currency_rate;
			$amount = $amount * $currency_rate;
			$currency = $currency->currency_symbol;
			/* currency */
			$buyer['info'] = Members::singlevendorData(Auth::user()->id);	
			$buyer_name = $buyer['info']->name;
			$buyer_email = $buyer['info']->email;
			$admin_name = $setting['setting']->sender_name;
			$admin_email = $setting['setting']->sender_email;
			$buyer_data = array('buyer_name' => $buyer_name, 'buyer_email' => $buyer_email, 'currency' => $currency, 'amount' => $amount,  'payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_type' => $payment_type);
			
			$checktemp = EmailTemplate::checkTemplate(19);
			if($checktemp != 0)
			{
			   $template_view['mind'] = EmailTemplate::viewTemplate(19);
			   $template_subject = $template_view['mind']->et_subject;
			}
			else
			{
			   $template_subject = "New Deposit Details";
			}
			
			Mail::send('deposit_mail', $buyer_data , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
				$message->to($buyer_email, $buyer_name)
				   ->subject($template_subject);
				$message->from($admin_email,$admin_name);
			});
			$clean = array('deposit_amount' => '', 'deposit_type' => '');
			Settings::updateAdditionData($clean);
			$data_record = array('payment_token' => $payment_token);
			if($this->custom_settings('author_key') == Helper::Key_Owner())
			{
			/* Notification Message */
			$deposit_details = Deposit::displaydepositDetails($ord_token);
			$noti_date = date('Y-m-d H:i:s');
			$noti_sender_id = $buyer['info']->id;
			$noti_receiver_id = 1;
			$noti_message = 'New deposit received send by <strong>'.$buyer['info']->name.'</strong>';
			$savedata = array('noti_date' => $noti_date, 'noti_sender_id' => $noti_sender_id, 'noti_receiver_id' => $noti_receiver_id, 'noti_message' => $noti_message, 'noti_type' => 'deposit', 'noti_type_id' => $deposit_details->dd_id);
			Items::saveNotification($savedata);
			/* Notification Message */
			}
			return view('success')->with($data_record);
        }
		else
		{
		   return view('cancel');
		}
		
		
	  } catch (Exception $e) {
		echo 'Exception when calling PGFetchOrder: ', $e->getMessage(), PHP_EOL;
	 }	
	
		
	}
		
	/* cashfree */
	
	
	public function deposit_success($ord_token, Request $request)
	{
	
	$sid = 1;
	$setting['setting'] = Settings::editGeneral($sid);
	$payment_token = $request->input('tx');
	/* deposite details */
	$payment_date = date('Y-m-d');
	$payment_status = 'completed';
	$updatedata = array('payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_status' => $payment_status);
	Deposit::upDepositdata($ord_token,$updatedata);
	/* deposite details */
	$additional_view = Settings::editAdditional();
	$amount = $additional_view->deposit_amount;
	$payment_type = $additional_view->deposit_type;
	$user_token = Auth::user()->user_token;
	$buyer_details = Members::editData($user_token);
	$wallet = $buyer_details->earnings + $amount;
	$data = array('earnings' => $wallet);
	Members::updateData($user_token,$data);
	
	/* currency */
	$deposit_details = Deposit::displaydepositDetails($ord_token);
	$currency = Currencies::getCurrency($deposit_details->currency_type_code);
	$currency_symbol = $currency->currency_symbol;
	$currency_rate = $currency->currency_rate;
	$amount = $amount * $currency_rate;
	$currency = $currency->currency_symbol;
	/* currency */
	$buyer['info'] = Members::singlevendorData(Auth::user()->id);	
	$buyer_name = $buyer['info']->name;
	$buyer_email = $buyer['info']->email;
	
	$admin_name = $setting['setting']->sender_name;
	$admin_email = $setting['setting']->sender_email;
	$buyer_data = array('buyer_name' => $buyer_name, 'buyer_email' => $buyer_email, 'currency' => $currency, 'amount' => $amount,  'payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_type' => $payment_type);
	/* email template code */
	$checktemp = EmailTemplate::checkTemplate(19);
	if($checktemp != 0)
	{
		$template_view['mind'] = EmailTemplate::viewTemplate(19);
		$template_subject = $template_view['mind']->et_subject;
	}
	else
	{
		$template_subject = "New Deposit Details";
	}
	/* email template code */
	Mail::send('deposit_mail', $buyer_data , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
		$message->to($buyer_email, $buyer_name)
		->subject($template_subject);
		$message->from($admin_email,$admin_name);
	});
	$clean = array('deposit_amount' => '', 'deposit_type' => '');
	Settings::updateAdditionData($clean);
	$result_data = array('payment_token' => $payment_token);
	if($this->custom_settings('author_key') == Helper::Key_Owner())
	{
	/* Notification Message */
	$deposit_details = Deposit::displaydepositDetails($ord_token);
	$noti_date = date('Y-m-d H:i:s');
	$noti_sender_id = $buyer['info']->id;
	$noti_receiver_id = 1;
	$noti_message = 'New deposit received send by <strong>'.$buyer['info']->name.'</strong>';
	$savedata = array('noti_date' => $noti_date, 'noti_sender_id' => $noti_sender_id, 'noti_receiver_id' => $noti_receiver_id, 'noti_message' => $noti_message, 'noti_type' => 'deposit', 'noti_type_id' => $deposit_details->dd_id);
	Items::saveNotification($savedata);
	/* Notification Message */
	}
	return view('success')->with($result_data);
	
	}
	
	
	
	
}
