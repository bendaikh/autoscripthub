<?php

namespace Fickrr\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Fickrr\Models\Settings;
use Fickrr\Models\Members;
use Fickrr\Models\Items;
use Fickrr\Models\Attribute;
use Fickrr\Models\Category;
use Fickrr\Models\SubCategory;
use Fickrr\Models\Languages;
use Fickrr\Models\Currencies;
use Fickrr\Models\Deposit;
use Fickrr\Models\EmailTemplate;
use Fickrr\Models\Blog;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
/*use Intervention\Image\Image;*/
use Illuminate\Support\Facades\File;
use Fickrr\Http\Controllers\Controller;
use Auth;
use Mail;
use URL;
use Image;
use Storage;
use Illuminate\Support\Str;
use Session;
use Carbon\Carbon;
use DataTables;
use Cookie;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use PDF;
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
	
	public function custom()
	{
	    $dw_v = Helper::version_no();
		$custom = Settings::customSettings();
		return $custom->$dw_v;
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
	
	public function complete_payment($ord_id)
	{ 
	    $ord_token = base64_decode($ord_id);
	    $sid = 1;
		$setting['setting'] = Settings::editGeneral($sid);
		$payment_token = "";
		/* deposite details */
		$payment_date = date('Y-m-d');
		$payment_status = 'completed';
		$updatedata = array('payment_token' => $payment_token, 'payment_date' => $payment_date, 'payment_status' => $payment_status);
		Deposit::upDepositdata($ord_token,$updatedata);
		/* deposite details */
		$deposit_details = Deposit::displaydepositDetails($ord_token);
		$amount = $deposit_details->deposit_price + $deposit_details->deposit_bonus;
		$payment_type = 'localbank';
		$user_token = 	$deposit_details->user_id;
		$buyer_details = Members::logindataUser($user_token);
		$wallet = $buyer_details->earnings + $amount;
		$data = array('earnings' => $wallet);
		Members::updateReferral($user_token,$data);
		/* currency */
		$deposit_details = Deposit::displaydepositDetails($ord_token);
		$currency = Currencies::getCurrency($deposit_details->currency_type_code);
		$currency_symbol = $currency->currency_symbol;
		$currency_rate = $currency->currency_rate;
		$amount = $amount * $currency_rate;
		$currency = $currency->currency_symbol;
		/* currency */
		$buyer['info'] = Members::singlevendorData($deposit_details->user_id);	
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
		return redirect()->back()->with('success', 'Payment details has been completed');	
		
		
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
	
	public function edit_withdrawal_methods($wm_id)
	{
	  $withdrawal_methods = Items::editWithMethod($wm_id); 
	 $data = array('withdrawal_methods' => $withdrawal_methods); 
	 if($this->custom() != 0)
	 {
	 return view('admin.edit-withdrawal-methods')->with($data);
	 }
	 else
	 {
		  return redirect('/admin/license');
	 }
	
	}
	
	public function withdrawal_methods()
	{
	  $withdrawal_methods = Items::getWithMethod(); 
	 $data = array('withdrawal_methods' => $withdrawal_methods);
	 if($this->custom() != 0)
	 { 
	 return view('admin.withdrawal-methods')->with($data);
	 }
	 else
	 {
		  return redirect('/admin/license');
	 }
	}
	
	public function add_withdrawal_methods()
	{
	 if($this->custom() != 0)
	 { 
	 return view('admin.add-withdrawal-methods');
	 }
	 else
	 {
		  return redirect('/admin/license');
	 }
	  
	}
	
	
	public function update_withdrawal_methods(Request $request)
	{
	
	     $withdrawal_name = $request->input('withdrawal_name');
		 
		 $withdrawal_order = $request->input('withdrawal_order');
		 $withdrawal_status = $request->input('withdrawal_status');
		 $wm_id = $request->input('wm_id');
		 
		 $request->validate([
							'withdrawal_name' => 'required',
							
							
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
		
		
		 
		$data = array('withdrawal_name' => $withdrawal_name, 'withdrawal_order' => $withdrawal_order, 'withdrawal_status' => $withdrawal_status);
       
        Items::updateWithMethod($wm_id, $data);
            
 
       } 
     
       return redirect('/admin/withdrawal-methods')->with('success', 'Update successfully.');
	
	}
	
	public function save_withdrawal_methods(Request $request)
	{
	   
	     $withdrawal_name = $request->input('withdrawal_name');
		 $withdrawal_key = $request->input('withdrawal_key');
		 $withdrawal_order = $request->input('withdrawal_order');
		 $withdrawal_status = $request->input('withdrawal_status');
		 $request->validate([
							'withdrawal_name' => 'required',
							'withdrawal_key' => 'required',
							
         ]);
		 $rules = array(
				'withdrawal_key' => ['required', 'max:255', Rule::unique('withdrawal_methods') -> where(function($sql){ $sql->where('wm_id','!=','');})],
				
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
		
		
		 
		$data = array('withdrawal_name' => $withdrawal_name, 'withdrawal_key' => $withdrawal_key, 'withdrawal_order' => $withdrawal_order, 'withdrawal_status' => $withdrawal_status);
        Items::saveWithMethod($data);
        
            
 
       } 
     
       return redirect('/admin/withdrawal-methods')->with('success', 'Insert successfully.');
	
	}
	
	public function search_trash_items(Request $request)
	{
	  $viewitem['type'] = Items::gettypeItem();
	  $encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
	  if(!empty($request->input('search')))
	   {
	      
		  
		  $search = $request->input('search');
		  $itemData['item'] = Items::searchtrashItem($search);
		  
	   }
	   else
	   {
	     $itemData['item'] = Items::getTrashItem();
		 $search = "";
	  
	   }
	  $data = array('itemData' => $itemData, 'viewitem' => $viewitem, 'encrypter' => $encrypter, 'search' => $search);
	  if($this->custom() != 0)
	  { 
	  return view('admin.trash-items')->with($data);
	  }
	  else
	  {
		  return redirect('/admin/license');
	  }
	}
	
	public function search_items(Request $request)
	{
	  $viewitem['type'] = Items::gettypeItem();
	  $encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
	  if(!empty($request->input('search')))
	   {
	      
		  
		  $search = $request->input('search');
		  $itemData['item'] = Items::searchentireItem($search);
		  
	   }
	   else
	   {
	     $itemData['item'] = Items::getentireItem();
		 $search = "";
	  
	   }
	  $data = array('itemData' => $itemData, 'viewitem' => $viewitem, 'encrypter' => $encrypter, 'search' => $search);
	  if($this->custom() != 0)
	  { 
	  return view('admin.items')->with($data);
	  }
	  else
	  {
		  return redirect('/admin/license');
	  }
	}
	
	public static function translation($id,$code,$keyword) 
    {
	    $view_language['details'] = Languages::viewLanguage();
		$language_token = base64_encode($id);
		foreach($view_language['details'] as $viewdata)
		{
		   if($code == 'en')
		   {
			    $check_en = Languages::en_Translate_check($id,$code);
				if($check_en == 0)
				{
				$endata = array('keyword_id' => $id, 'keyword_token' => $language_token,  'keyword_label' => $keyword, 'keyword_text' => $keyword, 'language_code' => $code, 'keyword_parent' => 0); 
				Languages::SaveData($endata);
				}
		   }
		   else
		   {
		        $check_other = Languages::other_Translate_check($id,$code);
				if($check_other == 0)
				{
				$endata = array('keyword_id' => uniqid(), 'keyword_token' => $language_token,  'keyword_label' => $keyword, 'keyword_text' => $keyword, 'language_code' => $code, 'keyword_parent' => $id); 
				Languages::SaveData($endata);
				}
		   }	
		}
	    if($code == 'en')
		{
		   $tran_value['view'] = Languages::en_Translate($id,$code);
		}
		else
		{
		  $tran_value['view'] = Languages::other_Translate($id,$code);
		}
		return $tran_value['view']->keyword_text;
        
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
       //$validextensions = array($additional->item_re_file_extension);

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
		 $record = '<div class="form-group">
                    <label for="site_desc" class="control-label mb-1">Upload Thumbnail (Size : 80x80px) <span class="require">*</span> - (jpeg,jpg,png,webp)</label><br/>
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
							$record .='<img src="'.$this->Image_Path($edit['item']->item_thumbnail,'no-image.png').'" alt="'.$edit['item']->item_name.'" class="item-thumb">';
						}
						else
						{
							$record .='<img src="'.url('/').'/public/img/no-image.png" alt="'.$edit['item']->item_name.'" class="item-thumb">';
						}
                   } 
				   $record .= '</div>';
		 $record .= '<div class="form-group">
                    <label for="site_desc" class="control-label mb-1">Upload Preview (Size : 361x230px) <span class="require">*</span> - (jpeg,jpg,png,webp)</label><br/>
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
							$record .='<img src="'.$this->Image_Path($edit['item']->item_preview,'no-image.png').'" alt="'.$edit['item']->item_name.'" class="item-thumb">';
						}
						else
						{
							$record .='<img src="'.url('/').'/public/img/no-image.png" alt="'.$edit['item']->item_name.'" class="item-thumb">';
						}
                   } 
				   $record .= '</div>';
				   
									if($additional->show_screenshots == 1)
									 {		
                                     $record .= '<div class="form-group">
                                                <label for="customer_earnings" class="control-label mb-1">Upload Screenshots (multiple) (Size : 750x430px) - (jpeg,jpg,png,webp)</label>
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
												      $record .= '<div class="item-img"><img src="'.$this->Image_Path($item->item_image,'no-image.png').'" alt="'.$item->item_image.'" class="item-thumb">';
													  $record .='<a href="'.url('/admin/edit-item').'/dropimg/'.base64_encode($item->itm_id).'" onClick="return confirm("'.$alerttext.'");" class="drop-icon"><span class="ti-trash drop-icon"></span></a></div>';
													  
												  }
											   }
                                             $record .= '<div class="clearfix"></div></div>';
		                            }
									if($additional->show_video == 1)
									{
									$record .='<div class="form-group">
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
                                            </div>'; 
									         if(!empty($item_token))
								             {
												   if($edit['item']->video_preview_type == 'youtube')
												   {
												   $record .= '<div id="youtube" class="form-group display-block">';
												   }
												   else if($edit['item']->video_preview_type == 'mp4')
												   {
													 $record .= '<div id="youtube" class="form-group display-none">';
												   }
												   else if($edit['item']->video_preview_type == 'mp3')
												   {
													 $record .= '<div id="youtube" class="form-group display-none">';
												   }
												   else
												   {
													  $record .= '<div id="youtube" class="form-group display-none">';
												   }
												}
												else
												{			
												$record .= '<div id="youtube" class="form-group display-none">';
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
                                            </div>';
									if(!empty($item_token))
								             {
												   if($edit['item']->video_preview_type == 'youtube')
												   {
												   $record .= '<div id="mp4" class="form-group display-none">';
												   }
												   else if($edit['item']->video_preview_type == 'mp4')
												   {
													 $record .= '<div id="mp4" class="form-group display-block">';
												   }
												   else if($edit['item']->video_preview_type == 'mp3')
												   {
													 $record .= '<div id="mp4" class="form-group display-none">';
												   }
												   else
												   {
													  $record .= '<div id="mp4" class="form-group display-none">';
												   }
												}
												else
												{			
												$record .= '<div id="mp4" class="form-group display-none">';
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
                                             $record .= '</div>';				
									
									             if(!empty($item_token))
								                {
												   if($edit['item']->video_preview_type == 'youtube')
												   {
												   $record .= '<div id="mp3" class="form-group display-none">';
												   }
												   else if($edit['item']->video_preview_type == 'mp4')
												   {
													 $record .= '<div id="mp3" class="form-group display-none">';
												   }
												   else if($edit['item']->video_preview_type == 'mp3')
												   {
													 $record .= '<div id="mp3" class="form-group display-block">';
												   }
												   else
												   {
													  $record .= '<div id="mp3" class="form-group display-none">';
												   }
												}
												else
												{			
												$record .= '<div id="mp3" class="form-group display-none">';
												}		
									
                                           $record .='<label for="site_desc" class="control-label mb-1">Upload MP3 <span class="require">*</span></label><br/>
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
                                             $record .= '</div>';
											 
											 				
									}
									$record .= '<div class="form-group">
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
                                                $record .= '</select></div>';
								if(!empty($item_token))
								{
								   if($edit['item']->file_type == 'file')
								   {
								   $record .= '<div id="main_file" class="form-group display-block">';
								   }
								   else if($edit['item']->file_type == 'link')
								   {
								     $record .= '<div id="main_file" class="form-group display-none">';
									 
								   }
								   else if($edit['item']->file_type == 'serial')
								   {
								     $record .= '<div id="main_file" class="form-group display-none">';
								   }
								   else
								   {
								      $record .= '<div id="main_file" class="form-group">';
								   }
								}
								else
								{			
								$record .= '<div id="main_file" class="form-group">';
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
											 $record .= '</div>';
											 if(!empty($item_token))
								             {
												   if($edit['item']->file_type == 'file')
												   {
												   $record .= '<div id="main_link" class="form-group display-none">';
												   }
												   else if($edit['item']->file_type == 'link')
												   {
													 $record .= '<div id="main_link" class="form-group display-block">';
												   }
												   else if($edit['item']->file_type == 'serial')
												   {
													 $record .= '<div id="main_link" class="form-group display-none">';
												   }
												   else
												   {
													  $record .= '<div id="main_link" class="form-group">';
												   }
												}
												else
												{			
												$record .= '<div id="main_link" class="form-group">';
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
                                                $record .= '<label for="name" class="control-label mb-1">
                                                    Main File Link/URL <span class="require">*</span>
                                                    <button type="button" id="add-version-link-btn" class="btn btn-primary btn-sm" style="margin-left: 10px; padding: 2px 8px; font-size: 18px;" title="Add another version">
                                                      ➕
                                                    </button>
                                                </label>
                                                <div id="version-links-container">
                                                    <div class="version-link-block" style="margin-bottom: 15px; padding: 15px; border: 1px solid #e0e0e0; border-radius: 4px; background-color: #f9f9f9;">
                                                      <div class="row">
                                                        <div class="col-sm-5">
                                                          <div class="form-group">
                                                            <input type="text" name="version_names[]" class="form-control" placeholder="Version name" style="font-size: 14px;">
                                                          </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                          <div class="form-group">
                                                            <input type="text" name="version_links[]" class="form-control" placeholder="File link / URL" data-bvalidator="required,url" value="'.$item_file_linked.'" style="font-size: 14px;">
                                                          </div>
                                                        </div>
                                                        <div class="col-sm-1" style="text-align: center;">
                                                          <button type="button" class="btn btn-danger btn-sm remove-version-btn" style="margin-top: 0px; padding: 6px 10px; font-size: 18px;" title="Remove this version">
                                                            🗑️
                                                          </button>
                                                        </div>
                                                      </div>
                                                    </div>
                                                </div>
                                                
                                            </div>';
									
									
									
									
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
	
	
	
	
	
	public function view_attribute($type_id)
	{
	   $edit['item'] = Items::viewItemtype($type_id);
	   $data = array('edit' => $edit);
	   if($this->custom() != 0)
	   {  
	   return view('admin.attributes')->with($data);
	   }
	   else
	   {
		  return redirect('/admin/license');
	   }
	}
	
	
	public function all_delete_item_type(Request $request)
	{
	   $data = array('item_type_drop_status'=>'yes');
	   $item_type_id = $request->input('item_type_id');
	   foreach($item_type_id as $id)
	   {
	      Items::deleteItemtype($id,$data);
	   }
	   return redirect()->back()->with('success','Delete successfully.');
	
	}
	
	
	public function view_item_type_delete($id)
	{
	    $data = array('item_type_drop_status'=>'yes');
	  
        
      Items::deleteItemtype($id,$data);
	  
	  return redirect()->back()->with('success', 'Delete successfully.');
	}
	
	
	public function edit_item_type($id)
	{
	   $edit['item_type'] = Items::viewItemtype($id);
	   $data = array('edit' => $edit);
	   if($this->custom() != 0)
	   { 
	   return view('admin.edit-item-type')->with($data);
	   }
	   else
	   {
		  return redirect('/admin/license');
	   }
	
	}
	public function search_orders(Request $request)
	{
	 
	  if(!empty($request->input('search')))
	   {
	      
		  
		  $search = $request->input('search');
		  $itemData['item'] = Items::searchentireOrder($search);
		  
	   }
	   else
	   {
	     $itemData['item'] = Items::getorderItem();
		 $search = "";
	  
	   }
	  
	  $data = array('itemData' => $itemData, 'search' => $search);
	  if($this->custom() != 0)
	  { 
	  return view('admin.orders')->with($data);
	  }
	  else
	  {
		  return redirect('/admin/license');
	  }
	}
	
	public function item_comments($token)
	{
	  $commentData = Items::getcommentData($token);
	  if($this->custom() != 0)
	  {
	  return view('admin.item-comment',[ 'commentData' => $commentData]);
	  }
	  else
	  {
		  return redirect('/admin/license');
	  }
	}
	
	public function delete_comment($comm_id)
	{

      
	  
      Items::deleteCommentdata($comm_id);
	  
	  return redirect()->back()->with('success', 'Delete successfully.');

    
  }
	
	public function view_items()
	{
	  
	  $itemData['item'] = Items::getentireItem();
	  $viewitem['type'] = Items::gettypeItem();
	  $encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
	  $search = "";
	  $comments = Items::getcountcommentData();
	  $data = array('itemData' => $itemData, 'viewitem' => $viewitem, 'encrypter' => $encrypter, 'search' => $search, 'comments' => $comments);
	  if($this->custom() != 0)
	  { 
	  return view('admin.items')->with($data);
	  }
	  else
	  {
		  return redirect('/admin/license');
	  }
	}
	
	 public function view_orders()
	{
	   
	   $itemData['item'] = Items::getorderItem();
	   $search = '';
	   $data = array('itemData' => $itemData,'search' => $search);
	   if($this->custom() != 0)
	   {
	   return view('admin.orders')->with($data);
	   }
	   else
	   {
		  return redirect('/admin/license');
	   }
	}
	
	/*public function view_items()
	{
	  $viewitem['type'] = Items::gettypeItem();
	  $encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
	  $data = array('viewitem' => $viewitem, 'encrypter' => $encrypter);
	  return view('admin.items')->with($data);
	}*/
	
	
	public function getItems(Request $request)
    { 
	    
    	$data = Items::getentireItem();
        return DataTables::of($data)
		    ->addColumn('Item Image', function($data) {
                if($data->item_thumbnail != ''){
                    return '<img src="'.$this->Image_Path($data->item_thumbnail,'no-image.png').'" alt="'.$data->item_name.'" height="50" width="50">';
                }else{
                    return '<img src="'.url('/').'/public/img/no-image.png" alt="'.$data->item_name.'" height="50" width="50">';
                }
            })
			->addColumn('Item Name', function($data) {
			   
			     return '<a href="'.url('/').'/item/'.$data->item_slug.'" target="_blank" class="black-color">'.mb_substr($data->item_name, 0, 50, 'UTF-8').'</a>';
			})
			->addColumn('Vendor', function($data) {
			   
			     return '<a href="'.url('/').'/user/'.$data->username.'" target="_blank" class="black-color">'.$data->username.'</a>';
			})
			->addColumn('Featured Item', function($data) {
			    if(!empty(Cookie::get('translate')))
				{
				$translate = Cookie::get('translate');
				   
				}
				else
				{
				  $default_count = Languages::defaultLanguageCount();
				  if($default_count == 0)
				  { 
				  $translate = "en";
				  
				  }
				  else
				  {
				  $default['lang'] = Languages::defaultLanguage();
				  $translate =  $default['lang']->language_code;
				  
				  }
				 
				}
				$feature_data = "";
                if($data->item_featured == 'no'){
                   $feature_data .= '<span class="badge badge-danger">'.$this->translation(2971,$translate,'').'</span>';
					
                }else{
				
                    $feature_data .= '<span class="badge badge-success">'.$this->translation(2970,$translate,'').'</span>';
					
                }
				$feature_data .= '&nbsp;<a href="'.url('/').'/admin/items/'.$data->item_featured.'/'.$data->item_token.'" style="font-size:12px; color:#0000FF; text-decoration:underline;">'.$this->translation(5916,$translate,'').'</a>';
				return $feature_data;
				
            }) 
			->addColumn('Free Item', function($data) {
			    if(!empty(Cookie::get('translate')))
				{
				$translate = Cookie::get('translate');
				   
				}
				else
				{
				  $default_count = Languages::defaultLanguageCount();
				  if($default_count == 0)
				  { 
				  $translate = "en";
				  
				  }
				  else
				  {
				  $default['lang'] = Languages::defaultLanguage();
				  $translate =  $default['lang']->language_code;
				  
				  }
				 
				}
				
                if($data->free_download == 1){
                    return '<span class="badge badge-success">'.$this->translation(2970,$translate,'').'</span>';
					
                }else{
				
                    return '<span class="badge badge-danger">'.$this->translation(2971,$translate,'').'</span>';
					
                }
				
				
            })
			->addColumn('Flash Request', function($data) {
			    if(!empty(Cookie::get('translate')))
				{
				$translate = Cookie::get('translate');
				   
				}
				else
				{
				  $default_count = Languages::defaultLanguageCount();
				  if($default_count == 0)
				  { 
				  $translate = "en";
				  
				  }
				  else
				  {
				  $default['lang'] = Languages::defaultLanguage();
				  $translate =  $default['lang']->language_code;
				  
				  }
				 
				}
				
                if($data->item_flash_request == 1)
				{
				   if($data->item_flash == 0)
				   {
                    return '<span class="badge badge-danger">'.$this->translation(5475,$translate,'').'</span>';
					
                   }else{
				
                    return '<span class="badge badge-success">'.$this->translation(5232,$translate,'').'</span>';
					
                    }
				}
				else
				{
				   return '<span>---</span>';
				}	
				
				
            })
            ->addColumn('Actions', function($data) {
			    $additional = Settings::editAdditional();
		        $demo_mode = $additional->demo_mode;
			    if(!empty(Cookie::get('translate')))
				{
				$translate = Cookie::get('translate');
				   
				}
				else
				{
				  $default_count = Languages::defaultLanguageCount();
				  if($default_count == 0)
				  { 
				  $translate = "en";
				  
				  }
				  else
				  {
				  $default['lang'] = Languages::defaultLanguage();
				  $translate =  $default['lang']->language_code;
				  
				  }
				 
				}
                $button = '<div class="action-buttons-cell text-center" style="white-space: nowrap;">';
				$button .= '<a href="'.url('/').'/admin/edit-item/'.$data->item_token.'" class="btn btn-success btn-sm" title="'.$this->translation(2923,$translate,'Edit').'" data-toggle="tooltip"><i class="fa fa-edit"></i></a> ';
				
				// Add Installation Video button
				$installVideoUrl = isset($data->installation_video_url) ? $data->installation_video_url : '';
				$button .= '<button type="button" class="btn btn-info btn-sm" onclick="openInstallVideoModal(\''.$data->item_token.'\', \''.addslashes($data->item_name).'\', \''.$installVideoUrl.'\')" title="'.$this->translation('Installation Video',$translate,'Installation Video').'" data-toggle="tooltip"><i class="fa fa-youtube-play"></i></button> ';
				
				if($demo_mode == 'on')
				{
				  $button .= '<a href="demo-mode" class="btn btn-danger btn-sm" title="'.$this->translation('614dc152e18b4',$translate,'Delete').'" data-toggle="tooltip"><i class="fa fa-trash"></i></a>';
				}
				else
				{
				  $button .= '<a href="'.url('/').'/admin/items/'.$data->item_token.'" class="btn btn-danger btn-sm" onClick="return confirm(\''.$this->translation('614dc5af68305',$translate,'Are you sure you want to remove?').'\')" title="'.$this->translation('614dc152e18b4',$translate,'Delete').'" data-toggle="tooltip"><i class="fa fa-trash"></i></a>';
				}
				$button .= '</div>';
				return $button;
					
					
            })
            ->addColumn('Status', function($data) {
                
				if(!empty(Cookie::get('translate')))
				{
				$translate = Cookie::get('translate');
				   
				}
				else
				{
				  $default_count = Languages::defaultLanguageCount();
				  if($default_count == 0)
				  { 
				  $translate = "en";
				  
				  }
				  else
				  {
				  $default['lang'] = Languages::defaultLanguage();
				  $translate =  $default['lang']->language_code;
				  
				  }
				 
				}
				
                if($data->item_status == 1){
                    return '<span class="badge badge-success">'.$this->translation(5232,$translate,'').'</span>';
					
                }else if($data->item_status == 2){
				
                    return '<span class="badge badge-danger">'.$this->translation(5235,$translate,'').'</span>';
					
                }
				else
				{
				  return '<span class="badge badge-warning">'.$this->translation(3092,$translate,'').'</span>';
				}
				
            })->escapeColumns(['operations'])
            ->rawColumns(['Item Image','Actions','Status'])
            ->make(true);
    }
	
	
	
	
	
	public function view_trash_items()
	{
	  
	  $itemData['item'] = Items::getTrashItem();
	  $viewitem['type'] = Items::gettypeItem();
	  $encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
	  $search = "";
	  $data = array('itemData' => $itemData, 'viewitem' => $viewitem, 'encrypter' => $encrypter, 'search' => $search);
	  if($this->custom() != 0)
	  {
	  return view('admin.trash-items')->with($data);
	  }
	  else
	  {
		  return redirect('/admin/license');
	  }
	}
	
	
	public function view_restore_items($token)
	{
	
	  $data = array('drop_status'=>'no');
	  
      Items::admindeleteData($token,$data);
	  
	  return redirect('/admin/items')->with('success', 'Item Restored Successfully.');
	
	}
	
	
	public function permanent_delete_items($token)
	{
	
	  Items::forceDeleted($token);
	  
	  return redirect('/admin/items')->with('success', 'Item Permanently Deleted Successfully.');
	
	}
	
	
	public function all_delete_complete(Request $request)
	{
	   
	   $item_id = $request->input('item_id');
	   foreach($item_id as $id)
	   {
	      
		  Items::forceDeleted($id);
	   }
	   return redirect()->back()->with('success','Item Permanently Deleted Successfully.');
	
	}
	
	
	public function update_edit_item_type(Request $request)
	{
	
	     $item_type_name = $request->input('item_type_name');
		 $additional['settings'] = Settings::editAdditional();
		 if($additional['settings']->site_url_rewrite == 1)
		 {
		   $item_type_slug = $this->seo_slug($item_type_name);
		 }
		 else
		 {
		   $item_type_slug = $this->non_seo_slug($item_type_name);
		 }
		 $item_type_status = $request->input('item_type_status');
		 $item_type_id = $request->input('item_type_id');
		 
		 $request->validate([
							'item_type_name' => 'required',
							'item_type_status' => 'required',
							
         ]);
		 $rules = array(
		 
		        'item_type_name' => ['required', 'max:255', Rule::unique('item_type') ->ignore($item_type_id, 'item_type_id') -> where(function($sql){ $sql->where('item_type_drop_status','=','no');})],
				
				
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
		
		
		 
		$data = array('item_type_name' => $item_type_name, 'item_type_slug' => $item_type_slug, 'item_type_status' => $item_type_status);
       
        Items::editItemtype($item_type_id, $data);
            
 
       } 
     
       return redirect('/admin/item-type')->with('success', 'Update successfully.');
	
	}
	
	public function save_add_item_type(Request $request)
	{
	   
	     $item_type_name = $request->input('item_type_name');
		 $additional['settings'] = Settings::editAdditional();
		 if($additional['settings']->site_url_rewrite == 1)
		 {
		   $item_type_slug = $this->seo_slug($item_type_name);
		 }
		 else
		 {
		   $item_type_slug = $this->non_seo_slug($item_type_name);
		 }
		 $item_type_status = $request->input('item_type_status');
		 $request->validate([
							'item_type_name' => 'required',
							'item_type_status' => 'required',
							
         ]);
		 $rules = array(
				'item_type_name' => ['required', 'max:255', Rule::unique('item_type') -> where(function($sql){ $sql->where('item_type_drop_status','=','no');})],
				
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
		
		
		 
		$data = array('item_type_name' => $item_type_name, 'item_type_slug' => $item_type_slug, 'item_type_status' => $item_type_status);
        Items::insertItemtype($data);
        
            
 
       } 
     
       return redirect('/admin/item-type')->with('success', 'Insert successfully.');
	
	}
	
	
	
	public function view_order_single($token)
	{
	  $itemData['item'] = Items::adminorderItem($token);
	  $data = array('itemData' => $itemData);
	  if($this->custom() != 0)
	  {
	  return view('admin.order-details')->with($data);
	  }
	  else
	  {
		  return redirect('/admin/license');
	  }
	}
	
	
	public function view_more_info($token)
	{
	   $itemData['item'] = Items::getsingleOrder($token);
	   $data = array('itemData' => $itemData);
	   if($this->custom() != 0)
	   {
	   return view('admin.more-info')->with($data);
	   }
	   else
	   {
			  return redirect('/admin/license');
	   }
	 
	}
	
	public function view_item_type()
	{
	 
	  $itemData['item'] = Items::gettypeItemNew();
	  $data = array('itemData' => $itemData);
	  if($this->custom() != 0)
	  {
	  return view('admin.item-type')->with($data);
	  }
	  else
	  {
			  return redirect('/admin/license');
	  }
	
	}
	
	
	public function view_add_item_type()
	{
	 
	  if($this->custom() != 0)
	  {
	  return view('admin.add-item-type');
	  }
	  else
	  {
			  return redirect('/admin/license');
	  }
	
	}
	
	
	public function view_item_type_status($id,$status)
	{
	  if($status == 0)
	  {
	     $data = array("item_type_status" => 1);
	     Items::updateitemType($id,$data);
	  }
	  else
	  {
	     $data = array("item_type_status" => 0);
	     Items::updateitemType($id,$data);
	  }
	 
	  return redirect()->back()->with('success','Item type has been updated'); 
	}
	
	
	public function view_refund()
	{
	  
	  $itemData['item'] = Items::getrefundItem();
	   $data = array('itemData' => $itemData);
	   if($this->custom() != 0)
	   {
	   return view('admin.refund')->with($data);
	   }
	   else
	   {
				  return redirect('/admin/license');
	   }
	}
	
	
	public function view_rating()
	{
	   $itemData['item'] = Items::getratingItem();
	   $data = array('itemData' => $itemData);
	   if($this->custom() != 0)
	   {
	   return view('admin.rating')->with($data);
	   }
	   else
	   {
				  return redirect('/admin/license');
	   }
	}
	
	public function edit_rating($rating_id)
	{
	   $rating = Items::singleratingItem($rating_id);
	   $data = array('rating' => $rating);
	   if($this->custom() != 0)
	   {
	   return view('admin.edit-rating')->with($data);
	   }
	   else
	   {
				  return redirect('/admin/license');
	   }
	}
	
	
	public function update_rating(Request $request)
	{
	        
	 $rating_id = $request->input('rating_id');
	 $rating = $request->input('rating');
	 $rating_comment = $request->input('rating_comment');
	 $rating_reason = $request->input('rating_reason');
         $rating_date = date('Y-m-d H:i:s');
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
		
		$updata = array('rating' => $rating, 'rating_reason' => $rating_reason, 'rating_comment' => $rating_comment, 'rating_date' => $rating_date); 
		Items::updateratingData($rating_id,$updata);
        return redirect('/admin/rating')->with('success', 'Update successfully.');
            
 
       } 
	 
	 
	 
       
	
	
	}
	
	public function all_delete_rating(Request $request)
	{
	   
	   $rating_id = $request->input('rating_id');
	   foreach($rating_id as $id)
	   {
	      Items::dropRating($id);
	   }
	   return redirect()->back()->with('success','Delete successfully.');
	
	}
  
	
	public function rating_delete($rating_id)
	{
	   Items::dropRating($rating_id);
	   return redirect()->back()->with('success','Item rating has been removed'); 
	 
	}
	
	
	public function view_withdrawal()
	{
	  $itemData['item'] = Items::getwithdrawalData();
	   $data = array('itemData' => $itemData);
	   if($this->custom() != 0)
	   {
	   return view('admin.withdrawal')->with($data);
	   }
	   else
	   {
		  return redirect('/admin/license');
	   }
	}
	
	public function all_delete_withdrawal(Request $request)
	{
	   
	   $wd_id = $request->input('wd_id');
	   foreach($wd_id as $id)
	   {
	       $withdrawal = Items::singledrawalData($id);
		   $wd_amount = $withdrawal->wd_amount;
		   $userdata = Members::logindataUser($withdrawal->wd_user_id);
		   $earnings = $userdata->earnings + $wd_amount;
		   $data = array('earnings' => $earnings); 
		   Members::updateprofileData($withdrawal->wd_user_id,$data);
		   Items::deleteWithdraw($id);
	   }
	   return redirect()->back()->with('success','Delete successfully.');
	
	}
	
	public function delete_withdrawal($wd_id)
	{
	   $withdrawal = Items::singledrawalData($wd_id);
	   $wd_amount = $withdrawal->wd_amount;
	   $userdata = Members::logindataUser($withdrawal->wd_user_id);
	   $earnings = $userdata->earnings + $wd_amount;
	   $data = array('earnings' => $earnings); 
	   Members::updateprofileData($withdrawal->wd_user_id,$data);
	   Items::deleteWithdraw($wd_id);
	   return redirect()->back()->with('success','Withdrawal request has been deleted'); 
	
	}
	
	
	public function view_withdrawal_update($wd_id,$user_id)
	{
	         $drawal_data = array('wd_status' => 'paid');
			 Items::updatedrawalData($wd_id,$user_id,$drawal_data);
			 
	         $buyer['info'] = Members::singlebuyerData($user_id);
			 $user_token = $buyer['info']->user_token;
			 $to_name = $buyer['info']->name;
			 $to_email = $buyer['info']->email;
			 $sid = 1;
			$setting['setting'] = Settings::editGeneral($sid);
			$admin_name = $setting['setting']->sender_name;
			$admin_email = $setting['setting']->sender_email;
			$currency = $setting['setting']->site_currency;
			$with['data'] = Items::singledrawalData($wd_id);
			$wd_amount = $with['data']->wd_amount;
			
			$data = array('to_name' => $to_name, 'to_email' => $to_email, 'wd_amount' => $wd_amount, 'currency' => $currency);
			/* email template code */
	          $checktemp = EmailTemplate::checkTemplate(17);
			  if($checktemp != 0)
			  {
			  $template_view['mind'] = EmailTemplate::viewTemplate(17);
			  $template_subject = $template_view['mind']->et_subject;
			  }
			  else
			  {
			  $template_subject = "Payment Withdrawal Request Accepted";
			  }
			  /* email template code */
			Mail::send('admin.user_withdrawal_mail', $data , function($message) use ($admin_name, $admin_email, $to_name, $to_email, $template_subject) {
					$message->to($to_email, $to_name)
							->subject($template_subject);
					$message->from($admin_email,$admin_name);
				});
	   return redirect()->back()->with('success','Payment withdrawal request has been completed'); 			
	   
	}
	
	
	
	public function view_payment_refund($ord_id,$refund_id,$user_type)
	{
	  $order = $ord_id; 
	  $ordered['data'] = Items::singleorderData($order);
	  $user_id = $ordered['data']->user_id;
	  $item_user_id = $ordered['data']->item_user_id;
	  $vendor_amount = $ordered['data']->vendor_amount;
	  $total_price = $ordered['data']->total_price;
	  $admin_amount = $ordered['data']->admin_amount;
	  $approval_status = $ordered['data']->approval_status;
	  
	  
	  if($user_type == "buyer")
	  {
	  
	      if($approval_status == "")
		  {
		  
		    $buyer['info'] = Members::singlebuyerData($user_id);
			 $user_token = $buyer['info']->user_token;
			 $to_name = $buyer['info']->name;
			 $to_email = $buyer['info']->email;
			 $buyer_earning = $buyer['info']->earnings + $ordered['data']->item_single_vendor_price + $ordered['data']->item_single_admin_price;
			 $record = array('earnings' => $buyer_earning);
			 Members::updatepasswordData($user_token, $record);
			 
			$orderdata = array('approval_status' => 'payment released to buyer');
			$refundata = array('ref_refund_approval' => 'accepted');
			Items::singleorderupData($order,$orderdata);
			Items::refundupData($refund_id,$refundata);
			Items::deleteRating($ord_id);
			
			
			
			$sid = 1;
			$setting['setting'] = Settings::editGeneral($sid);
			$admin_name = $setting['setting']->sender_name;
			$admin_email = $setting['setting']->sender_email;
			$currency = $ordered['data']->currency_type_code;
			$data = array('to_name' => $to_name, 'to_email' => $to_email, 'total_price' => $total_price, 'currency' => $currency);
			/* email template code */
	          $checktemp = EmailTemplate::checkTemplate(13);
			  if($checktemp != 0)
			  {
			  $template_view['mind'] = EmailTemplate::viewTemplate(13);
			  $template_subject = $template_view['mind']->et_subject;
			  }
			  else
			  {
			  $template_subject = "Payment Refund Accepted";
			  }
			  /* email template code */
			Mail::send('admin.buyer_refund_mail', $data , function($message) use ($admin_name, $admin_email, $to_name, $to_email, $template_subject) {
					$message->to($to_email, $to_name)
							->subject($template_subject);
					$message->from($admin_email,$admin_name);
				});
				
				
			
			return redirect()->back()->with('success','Payment released to buyer'); 
		
		  
		     
		  }
		  else if($approval_status == 'payment released to buyer')
		  {
		     $refundata = array('ref_refund_approval' => 'accepted');
			 Items::refundupData($refund_id,$refundata);
			 Items::deleteRating($ord_id);
		     return redirect()->back()->with('success','Payment released to buyer');
		  }
		  else if($approval_status == 'payment released to vendor')
		  {
		  
		     $buyer['info'] = Members::singlebuyerData($user_id);
			 $user_token = $buyer['info']->user_token;
			 $to_name = $buyer['info']->name;
			 $to_email = $buyer['info']->email;
			 $buyer_earning = $buyer['info']->earnings + $ordered['data']->item_single_vendor_price + $ordered['data']->item_single_admin_price;
			 $record = array('earnings' => $buyer_earning);
			 Members::updatepasswordData($user_token, $record);
			 
			$orderdata = array('approval_status' => 'payment released to buyer');
			$refundata = array('ref_refund_approval' => 'accepted');
			Items::singleorderupData($order,$orderdata);
			Items::refundupData($refund_id,$refundata);
			Items::deleteRating($ord_id);
			
			
			 $vendor['info'] = Members::singlevendorData($item_user_id);
			 $vendor_token = $vendor['info']->user_token;
			 $to_name = $vendor['info']->name;
			 $to_email = $vendor['info']->email;
			 $vendor_earning = $vendor['info']->earnings - $ordered['data']->item_single_vendor_price;
			 $record_vendor = array('earnings' => $vendor_earning);
			 Members::updatevendorRecord($vendor_token, $record_vendor);
			 
			 
			 $admin['info'] = Members::adminData();
			 $admin_token = $admin['info']->user_token;
			 $admin_earning = $admin['info']->earnings - $ordered['data']->item_single_admin_price;
			 $admin_record = array('earnings' => $admin_earning);
			 Members::updateadminData($admin_token, $admin_record);
			
			
			
			$sid = 1;
			$setting['setting'] = Settings::editGeneral($sid);
			$admin_name = $setting['setting']->sender_name;
			$admin_email = $setting['setting']->sender_email;
			$currency = $ordered['data']->currency_type_code;
			$data = array('to_name' => $to_name, 'to_email' => $to_email, 'total_price' => $total_price, 'currency' => $currency);
			/* email template code */
	          $checktemp = EmailTemplate::checkTemplate(13);
			  if($checktemp != 0)
			  {
			  $template_view['mind'] = EmailTemplate::viewTemplate(13);
			  $template_subject = $template_view['mind']->et_subject;
			  }
			  else
			  {
			  $template_subject = "Payment Refund Accepted";
			  }
			  /* email template code */
			Mail::send('admin.buyer_refund_mail', $data , function($message) use ($admin_name, $admin_email, $to_name, $to_email, $template_subject) {
					$message->to($to_email, $to_name)
							->subject($template_subject);
					$message->from($admin_email,$admin_name);
				});
				
				
			
			return redirect()->back()->with('success','Payment released to buyer'); 
		
		  
		  
		    
		  }
	  
	  
	  
	  }
	  if($user_type == "vendor")
	  {
	         
			 $buyer['info'] = Members::singlebuyerData($user_id);
			 $user_token = $buyer['info']->user_token;
			 $to_name = $buyer['info']->name;
			 $to_email = $buyer['info']->email;
			 $sid = 1;
			$setting['setting'] = Settings::editGeneral($sid);
			$admin_name = $setting['setting']->sender_name;
			$admin_email = $setting['setting']->sender_email;
			$currency = $ordered['data']->currency_type_code;
			$refundata = array('ref_refund_approval' => 'declined');
			 Items::refundupData($refund_id,$refundata);
			$data = array('to_name' => $to_name, 'to_email' => $to_email, 'total_price' => $total_price, 'currency' => $currency);
			/* email template code */
	          $checktemp = EmailTemplate::checkTemplate(11);
			  if($checktemp != 0)
			  {
			  $template_view['mind'] = EmailTemplate::viewTemplate(11);
			  $template_subject = $template_view['mind']->et_subject;
			  }
			  else
			  {
			  $template_subject = "Payment Refund Declined";
			  }
			  /* email template code */
			Mail::send('admin.buyer_declined_mail', $data , function($message) use ($admin_name, $admin_email, $to_name, $to_email, $template_subject) {
					$message->to($to_email, $to_name)
							->subject($template_subject);
					$message->from($admin_email,$admin_name);
				});
			 
			  
	         
		    return redirect()->back()->with('success','Your refund request is declined');
	  
	  } 
	  
	  
	  
	  
	
	
	}
	
	
	
	
	public function view_payment_approval($ord_id,$user_type)
	{
	  $order = $ord_id; 
	  $ordered['data'] = Items::singleorderData($order);
	  $user_id = $ordered['data']->user_id;
	  $item_user_id = $ordered['data']->item_user_id;
	  $vendor_amount = $ordered['data']->vendor_amount;
	  $total_price = $ordered['data']->total_price;
	  $admin_amount = $ordered['data']->admin_amount;
	  
	  if($user_type == "vendor")
	  {
	     
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
		 
		$sid = 1;
		$setting['setting'] = Settings::editGeneral($sid);
		$admin_name = $setting['setting']->sender_name;
		$admin_email = $setting['setting']->sender_email;
		$currency = $ordered['data']->currency_type_code;
		$check_email_support = Members::getuserSubscription($item_user_id);
		if($check_email_support == 1)
		{
			$data = array('to_name' => $to_name, 'to_email' => $to_email, 'vendor_amount' => $vendor_amount, 'currency' => $currency);
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
			Mail::send('admin.vendor_payment_mail', $data , function($message) use ($admin_name, $admin_email, $to_name, $to_email, $template_subject) {
					$message->to($to_email, $to_name)
							->subject($template_subject);
					$message->from($admin_email,$admin_name);
				});
		}	
			
		
		return redirect()->back()->with('success','Payment released to vendor'); 
		 
	  }
	  else if($user_type == "buyer")
	  {
	     
		 $buyer['info'] = Members::singlebuyerData($user_id);
		 $user_token = $buyer['info']->user_token;
		 $to_name = $buyer['info']->name;
		 $to_email = $buyer['info']->email;
		 $buyer_earning = $buyer['info']->earnings + $ordered['data']->item_single_vendor_price + $ordered['data']->item_single_admin_price;
		 $record = array('earnings' => $buyer_earning);
		 Members::updatepasswordData($user_token, $record);
		 
		$orderdata = array('approval_status' => 'payment released to buyer');
		Items::singleorderupData($order,$orderdata);
		Items::deleteRating($ord_id);
		
		$sid = 1;
		$setting['setting'] = Settings::editGeneral($sid);
		$admin_name = $setting['setting']->sender_name;
		$admin_email = $setting['setting']->sender_email;
		$currency = $ordered['data']->currency_type_code;
		$data = array('to_name' => $to_name, 'to_email' => $to_email, 'total_price' => $total_price, 'currency' => $currency);
		/* email template code */
	          $checktemp = EmailTemplate::checkTemplate(12);
			  if($checktemp != 0)
			  {
			  $template_view['mind'] = EmailTemplate::viewTemplate(12);
			  $template_subject = $template_view['mind']->et_subject;
			  }
			  else
			  {
			  $template_subject = "Payment Approval Cancelled";
			  }
			  /* email template code */
		Mail::send('admin.buyer_payment_mail', $data , function($message) use ($admin_name, $admin_email, $to_name, $to_email, $template_subject) {
				$message->to($to_email, $to_name)
						->subject($template_subject);
				$message->from($admin_email,$admin_name);
			});
			
			
		
		return redirect()->back()->with('success','Payment released to buyer'); 
		
		 
	  }
	  
	  
	  
	
	
	}
	
	
	public function all_delete_orders(Request $request)
	{
	   
	   $purchase_token = $request->input('purchase_token');
	   foreach($purchase_token as $id)
	   {
	      Items::deleteEntire($id);
	   }
	   return redirect()->back()->with('success','Delete successfully.');
	
	}
	
	public function delete_orders($delete,$ord_id)
	{
	   $encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
	   $order_id   = $encrypter->decrypt($ord_id);
	   Items::deleteEntire($order_id);
	   return redirect()->back()->with('success','Order has been deleted'); 
	
	}
	
	
	public function all_delete_refund(Request $request)
	{
	   
	   $refund_id = $request->input('refund_id');
	   foreach($refund_id as $id)
	   {
	      Items::deleteRefund($id);
	   }
	   return redirect()->back()->with('success','Delete successfully.');
	
	}
  
	
	public function delete_refund($refund_id)
	{
	   
	   Items::deleteRefund($refund_id);
	   return redirect()->back()->with('success','Refund request has been deleted'); 
	
	}
	
	
	
	public function complete_orders($ord_id)
	{
	  $sid = 1;
	  $setting['setting'] = Settings::editGeneral($sid);
	  $purchase_token = base64_decode($ord_id);
	  $purchased_token = $purchase_token;
		    		$payment_status = 'completed';
					$orderdata = array('order_status' => $payment_status);
					$checkoutdata = array('payment_status' => $payment_status);
					Items::singleordupdateData($purchased_token,$orderdata);
					Items::singlecheckoutData($purchased_token,$checkoutdata);
					$token = $purchased_token;
					$checking = Items::getcheckoutData($token);
					/* customer email */
					$currency = $this->site_currency();
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
														  /* email template code */
							Mail::send('item_purchase_mail', $record_customer , function($message) use ($admin_name, $admin_email, $buyer_name, $buyer_email, $template_subject) {
												$message->to($buyer_email, $buyer_name)
														->subject($template_subject);
												$message->from($admin_email,$admin_name);
											});
					/* customer email */
					$order_id = $checking->order_ids;
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
						
					  }
					  /* referral per sale earning */
					    $additional_setting = Settings::editAdditional();
						if($additional_setting->affiliate_referral == 1)
						{
							$logged_id = $checking->user_id;
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
		return redirect()->back()->with('success', 'Payment details has been completed');			  
	
	}
	
	
	
	
	
	
	public function edit_item($token)
	{
	 
	    $allsettings = Settings::allSettings();
		$edit['item'] = Items::edititemData($token);
		$type_id = $edit['item']->item_type_id;
		$getcount  = Items::getimagesCount($token);
		$item_image['item'] = Items::getimagesData($token);
		$cat_name = $edit['item']->item_category_type; 
        $cat_id = $edit['item']->item_category;
		$session_id = Session::getId();
		$getdata1['first'] = Items::getProdutData($session_id);
	    $getdata2['second'] = Items::getProdutData($session_id);
	    $getdata3['third'] = Items::getProdutZip($session_id);
		$getdata4['four'] = Items::getProdutData($session_id);
		$getdata5['five'] = Items::getProdutMP4($session_id);
		$getdata6['six'] = Items::getProdutMP3($session_id);
		$getvendor['view'] = Members::getvendorData();
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
		$item_token = $token;
		$getWell['type'] = Items::gettypeStatus();
		if($this->custom_settings('author_key') == Helper::Key_Owner())
		{
		$other_market = Blog::getothermarketData();
		}
		else
		{
		$other_market = 0;
		}
		
		// Decode version links if available
		$versionLinks = [];
		if(!empty($edit['item']->item_file_link)) {
		    $decoded = json_decode($edit['item']->item_file_link, true);
		    if(is_array($decoded) && isset($decoded[0]['version'])) {
		        // It's version data
		        $versionLinks = $decoded;
		    }
		}
		
		$re_categories['menu'] = Category::with('SubCategory')->where('category_status','=','1')->where('drop_status','=','no')->orderBy('menu_order',$allsettings->menu_categories_order)->get();
		$data = array(  're_categories' => $re_categories, 'getWell' => $getWell, 'edit' => $edit, 'token' => $token, 'item_image' => $item_image, 'getcount' => $getcount, 'cat_id' => $cat_id, 'cat_name' => $cat_name,  'getvendor' => $getvendor, 'type_name' => $type_name, 'attri_field' => $attri_field, 'attribute' => $attribute, 'typer_id' => $typer_id, 'getdata1' => $getdata1, 'getdata2' => $getdata2, 'getdata3' => $getdata3, 'getdata4' => $getdata4, 'getdata5' => $getdata5, 'getdata6' => $getdata6, 'item_token' => $item_token, 'other_market' => $other_market, 'versionLinks' => $versionLinks);
	   if($this->custom() != 0)
	   { 
	   return view('admin.edit-item')->with($data);
	   }
	   else
	   {
			  return redirect('/admin/license');
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
	 
	  
	  $itemData['item'] = Items::getitemData();
	  if($this->custom() != 0)
	  { 
	  return view('manage-item',[ 'itemData' => $itemData]);
	  }
	  else
	  {
			  return redirect('/admin/license');
	  }
	}
	
	
	public function featured_item_request($featured,$item_token)
	{
	   if($featured == 'yes')
	   {
	     $featured_text = 'no';
	   }
	   else
	   {
	     $featured_text = 'yes';
	   }
	   $data = array('item_featured'=> $featured_text);
	   
	   Items::updateitemData($item_token,$data);
	   
	   return redirect()->back();

	}
	
	/**
	 * Get item versions via AJAX
	 */
	public function getItemVersions($item_token)
	{
	    $item = Items::edititemData($item_token);
	    
	    if(!$item) {
	        return response()->json([
	            'success' => false,
	            'message' => 'Item not found'
	        ]);
	    }
	    
	    $versions = [];
	    
	    if(!empty($item->item_file_link)) {
	        $decoded = json_decode($item->item_file_link, true);
	        if(is_array($decoded) && isset($decoded[0]['version'])) {
	            // It's version data
	            $versions = $decoded;
	        } else {
	            // Old format - single URL
	            $versions = [
	                [
	                    'version' => '',
	                    'url' => $item->item_file_link
	                ]
	            ];
	        }
	    }
	    
	    return response()->json([
	        'success' => true,
	        'versions' => $versions
	    ]);
	}
	
	/**
	 * Save item versions via AJAX
	 */
	public function saveItemVersions(Request $request)
	{
	    $item_token = $request->input('item_token');
	    $versions = $request->input('versions', []);
	    
	    if(empty($item_token)) {
	        return response()->json([
	            'success' => false,
	            'message' => 'Item token is required'
	        ]);
	    }
	    
	    $item = Items::edititemData($item_token);
	    
	    if(!$item) {
	        return response()->json([
	            'success' => false,
	            'message' => 'Item not found'
	        ]);
	    }
	    
	    // Validate versions
	    if(empty($versions) || !is_array($versions)) {
	        return response()->json([
	            'success' => false,
	            'message' => 'Please provide at least one version'
	        ]);
	    }
	    
	    // Store as JSON
	    $item_file_link = json_encode($versions);
	    
	    $data = array('item_file_link' => $item_file_link);
	    Items::updateitemData($item_token, $data);
	    
	    return response()->json([
	        'success' => true,
	        'message' => 'Versions saved successfully'
	    ]);
	}
	
	public function saveInstallationVideo(Request $request)
	{
	    $item_token = $request->input('item_token');
	    $installation_video_url = $request->input('installation_video_url');
	    
	    if(empty($item_token)) {
	        return response()->json([
	            'success' => false,
	            'message' => 'Item token is required'
	        ]);
	    }
	    
	    $item = Items::edititemData($item_token);
	    
	    if(!$item) {
	        return response()->json([
	            'success' => false,
	            'message' => 'Item not found'
	        ]);
	    }
	    
	    // Update the installation video URL
	    $data = array('installation_video_url' => $installation_video_url);
	    Items::updateitemData($item_token, $data);
	    
	    return response()->json([
	        'success' => true,
	        'message' => 'Installation video URL saved successfully'
	    ]);
	}
	
	
	public function delete_item_request($token)
	{
	
	  $data = array('drop_status'=>'yes', 'item_status' => 0);
	  
      Items::admindeleteData($token,$data);
	  
	  return redirect()->back()->with('success', 'Item Removed Successfully.');
	
	}
	
	
	public function trash_items(Request $request)
	{
	   $data = array('drop_status'=>'yes', 'item_status' => 0);
	   $item_id = $request->input('item_id');
	   foreach($item_id as $id)
	   {
	      
		  Items::admindeleteData($id,$data);
	   }
	   return redirect()->back()->with('success','Item Removed Successfully.');
	
	}
	

    
    public function upload_item($itemtype)
    {
	    $allsettings = Settings::allSettings();
	    $encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
	    $type_id   = $encrypter->decrypt($itemtype);
		$session_id = Session::getId();
		$getdata1['first'] = Items::getProdutData($session_id);
	    $getdata2['second'] = Items::getProdutData($session_id);
	    $getdata3['third'] = Items::getProdutZip($session_id);
		$getdata4['four'] = Items::getProdutData($session_id);
		$getdata5['five'] = Items::getProdutMP4($session_id);
		$getdata6['six'] = Items::getProdutMP3($session_id);
		$itemWell['type'] = Items::gettypeItem();       
		$getvendor['view'] = Members::allvendorData();
		$attribute['fields'] = Attribute::selectedAttribute($type_id);
		$type_name = Items::viewItemtype($type_id);
		$item_token = "";
		if($this->custom_settings('author_key') == Helper::Key_Owner())
		{
		$other_market = Blog::getothermarketData();
		}
		else
		{
		$other_market = 0;
		}
		$re_categories['menu'] = Category::with('SubCategory')->where('category_status','=','1')->where('drop_status','=','no')->orderBy('menu_order',$allsettings->menu_categories_order)->get();
		$data = array('re_categories' => $re_categories, 'getvendor' => $getvendor, 'itemWell' => $itemWell, 'attribute' => $attribute, 'type_id' => $type_id, 'type_name' => $type_name, 'getdata1' => $getdata1, 'getdata2' => $getdata2, 'getdata3' => $getdata3, 'getdata4' => $getdata4, 'getdata5' => $getdata5, 'getdata6' => $getdata6, 'item_token' => $item_token, 'other_market' => $other_market);
        if($this->custom() != 0)
	    { 
		return view('admin.upload-item')->with($data);
		}
	    else
	    {
		  return redirect('/admin/license');
	    }
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
	
	/*public function item_slug($string){
		   $slug=preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
		   return $slug;
    }*/
	
	public function item_slug($string)
	{
	    
		$string=preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
		$string=strtolower($string);
		return $string;	
    
	}
	
	
	
	public function update_attribute(Request $request)
	{
	   if(!empty($request->input('compatible_browsers')))
	   {
	   $compatible_browsers = $request->input('compatible_browsers');
	   }
	   else
	   {
	   $compatible_browsers = $request->input('save_compatible_browsers');
	   }
	   if(!empty($request->input('package_includes_two')))
	   {
	   $package_includes_two = $request->input('package_includes_two');
	   }
	   else
	   {
	   $package_includes_two = $request->input('save_package_includes_two');
	   }
	   if(!empty($request->input('layout')))
	   {
	   $layout = $request->input('layout');
	   }
	   else
	   {
	   $layout = $request->input('save_layout');
	   }
	   if(!empty($request->input('package_includes_three')))
	   {
	   $package_includes_three = $request->input('package_includes_three');
	   }
	   else
	   {
	   $package_includes_three = $request->input('save_package_includes_three');
	   }
	   if(!empty($request->input('package_includes_four')))
	   {
	   $package_includes_four = $request->input('package_includes_four');
	   }
	   else
	   {
	   $package_includes_four = $request->input('save_package_includes_four');
	   }
	   if(!empty($request->input('package_includes')))
	   {
	   $package_includes = $request->input('package_includes');
	   }
	   else
	   {
	   $package_includes = $request->input('save_package_includes');
	   }
	   if(!empty($request->input('columns')))
	   {
	   $columns = $request->input('columns');
	   }
	   else
	   {
	   $columns = $request->input('save_columns');
	   }
	   if(!empty($request->input('cs_version')))
	   {
	   $cs_version = $request->input('cs_version');
	   }
	   else
	   {
	   $cs_version = $request->input('save_cs_version');
	   }
	   $attr_id = $request->input('attr_id');
	   
	   $data = array('compatible_browsers' => $compatible_browsers, 'package_includes' => $package_includes, 'package_includes_two' => $package_includes_two, 'columns' => $columns, 'layout' => $layout, 'package_includes_three' => $package_includes_three, 'cs_version' => $cs_version, 'package_includes_four' => $package_includes_four);
			
	   Items::updateAttribute($attr_id,$data);
	   
	   return redirect()->back()->with('success', 'Update successfully');
	   
	
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
	   $item_flash = $request->input('item_flash');
	   
	   
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
	   $allsettings = Settings::allSettings();
	   $item_approval = $allsettings->item_approval;
	   $item_status = $request->input('item_status');
	   $item_approve_status = "Item updated successfully.";
	   
	   $seller_money_back = $request->input('seller_money_back');
	   if(!empty($request->input('seller_money_back_days')))
	   {
	   $seller_money_back_days = $request->input('seller_money_back_days');
	   }
	   else
	   {
	   $seller_money_back_days = 0;
	   }
	   
	   
	   
	   $allsettings = Settings::allSettings();
	   $image_size = $allsettings->site_max_image_size;
	   $file_size = $allsettings->site_max_file_size;
	   $watermark = $allsettings->site_watermark;
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
	   
	   
	   // Process version links
	   if(!empty($request->input('version_links'))) {
	       $versionNames = $request->input('version_names', []);
	       $versionLinks = $request->input('version_links', []);
	       
	       // Build version data array
	       $versionData = [];
	       foreach($versionLinks as $index => $url) {
	           if(!empty($url)) {
	               $versionData[] = [
	                   'version' => isset($versionNames[$index]) ? $versionNames[$index] : '',
	                   'url' => $url
	               ];
	           }
	       }
	       
	       // Store as JSON in item_file_link
	       if(!empty($versionData)) {
	           $item_file_link = json_encode($versionData);
	       } else {
	           $item_file_link = '';
	       }
	   }
	   else if(!empty($request->input('item_file_link1')))
	   {
	   $item_file_link = $request->input('item_file_link1');
	   }
	   else
	   {
	   $item_file_link = $request->input('item_file_link2');;  
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
	   
	   // Installation video URL
	   $installation_video_url = $request->input('installation_video_url');
	    
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
								/*'seller_refund_term' => 'required',
								'item_thumbnail' => 'mimes:jpeg,jpg,png|max:'.$image_size,
								'item_preview' => 'mimes:jpeg,jpg,png|max:'.$image_size,
								'item_file' => 'mimes:zip|max:'.$file_size,
								'item_screenshot.*' => 'image|mimes:jpeg,jpg,png|max:'.$image_size,*/
								
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
		
		
		    $data = array('item_name' => $item_name, 'item_desc' => $item_desc, 'item_thumbnail' => $item_thumbnail, 'item_preview' => $item_preview, 'item_file' => $item_file, 'file_type' => $file_type, 'item_file_link' => $item_file_link, 'item_category' =>$cat_id, 'item_category_parent' => $parent_category_id, 'item_category_type' => $cat_name, 'item_type' => $item_type, 'regular_price' => $regular_price, 'extended_price' => $extended_price, 'demo_url' => $demo_url, 'item_tags' => $item_tags, 'item_status' => $item_status, 'item_shortdesc' => $item_shortdesc, 'free_download' => $free_download, 'item_slug' => $item_slug, 'video_url' => $video_url, 'installation_video_url' => $installation_video_url, 'future_update' => $future_update, 'item_support' => $item_support, 'updated_item' => $updated_item, 'item_flash' => $item_flash, 'video_preview_type' => $video_preview_type, 'video_file' => $video_file, 'item_type_cat_id' => $item_category, 'seller_refund_term' => $seller_refund_term, 'seller_money_back' => $seller_money_back, 'seller_money_back_days' => $seller_money_back_days, 'item_allow_seo' => $item_allow_seo, 'item_seo_keyword' => $item_seo_keyword, 'item_seo_desc' => $item_seo_desc, 'audio_file' => $audio_file, 'item_type_id' => $type_id, 'subscription_item' => $subscription_item, 'item_delimiter' => $item_delimiter, 'item_serials_list' => $item_serials_list, 'item_reviewer' => $item_reviewer);
			
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
			 $item_slug = $itemdata['data']->item_slug;
			 $item_url = URL::to('/item').'/'.$item_slug;
		   $check_email_support = Members::getuserSubscription($user_id);
		   if($getvendor['user']->item_review_email == 1)
		   {
		       
			   $sid = 1;
			  $setting['setting'] = Settings::editGeneral($sid);
			  $admin_name = $setting['setting']->sender_name;
			  $admin_email = $setting['setting']->sender_email;
			  $record = array('item_url' => $item_url, 'item_name' => $item_name);
			  $to_name = $getvendor['user']->name;
			  $to_email = $getvendor['user']->email;
			  if($check_email_support == 1)
			  {
				   if($item_status == 1)
				   {
					  
					   /* email template code */
					  $checktemp = EmailTemplate::checkTemplate(15);
					  if($checktemp != 0)
					  {
					  $template_view['mind'] = EmailTemplate::viewTemplate(15);
					  $template_subject = $template_view['mind']->et_subject;
					  }
					  else
					  {
					  $template_subject = "Item Review Notifications";
					  }
					  /* email template code */ 
					   Mail::send('admin.item_review_mail', $record, function($message) use ($admin_name, $admin_email, $to_email, $to_name, $template_subject) {
							$message->to($to_email, $to_name)
									->subject($template_subject);
							$message->from($admin_email,$admin_name);
						});
					  
				   }
				   if($item_status == 2)
				   {
				   
					/*$reject_data = array('drop_status' => 'yes');
					Items::rejectitemData($item_token,$reject_data);*/ 
					/* email template code */
					  $checktemp = EmailTemplate::checkTemplate(14);
					  if($checktemp != 0)
					  {
					  $template_view['mind'] = EmailTemplate::viewTemplate(14);
					  $template_subject = $template_view['mind']->et_subject;
					  }
					  else
					  {
					  $template_subject = "Item Rejected Notifications";
					  }
					  /* email template code */
					Mail::send('admin.item_rejected_mail', $record, function($message) use ($admin_name, $admin_email, $to_email, $to_name, $template_subject) {
							$message->to($to_email, $to_name)
									->subject($template_subject);
							$message->from($admin_email,$admin_name);
						});
				   
				   }
			   
			  } 
       
           }  
			
			
			return redirect('/admin/items')->with('success', $item_approve_status);
		
		
		}
	   
	   
	   
	   
	   
	   
	   
	
	}
	
	
	
	
	
	public function save_items(Request $request)
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
	   $created_item = date('Y-m-d H:i:s');
	   $updated_item = date('Y-m-d H:i:s');
	   
	   $seller_money_back = $request->input('seller_money_back');
	   if(!empty($request->input('seller_money_back_days')))
	   {
	   $seller_money_back_days = $request->input('seller_money_back_days');
	   }
	   else
	   {
	   $seller_money_back_days = 0;
	   }
	   
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
	   
	   $user_id = $request->input('user_id');
	   $item_token = $this->generateRandomString();
	   $allsettings = Settings::allSettings();
	   $item_approval = $allsettings->item_approval;
	   $item_status = $request->input('item_status');
	   $item_approve_status = "Item inserted successfully.";
	   
	   
	     
	   
	   $allsettings = Settings::allSettings();
	   $image_size = $allsettings->site_max_image_size;
	   $file_size = $allsettings->site_max_file_size;
	   $watermark = $allsettings->site_watermark;
	   $url = URL::to("/");
	   
	   
	   
	   
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
	   
	   // Process version links
	   if(!empty($request->input('version_links'))) {
	       $versionNames = $request->input('version_names', []);
	       $versionLinks = $request->input('version_links', []);
	       
	       // Build version data array
	       $versionData = [];
	       foreach($versionLinks as $index => $url) {
	           if(!empty($url)) {
	               $versionData[] = [
	                   'version' => isset($versionNames[$index]) ? $versionNames[$index] : '',
	                   'url' => $url
	               ];
	           }
	       }
	       
	       // Store as JSON in item_file_link
	       if(!empty($versionData)) {
	           $item_file_link = json_encode($versionData);
	       } else {
	           $item_file_link = '';
	       }
	   }
	   else if(!empty($request->input('item_file_link1')))
	   {
	   $item_file_link = $request->input('item_file_link1');
	   }
	   else
	   {
	   $item_file_link = $request->input('item_file_link2');  
	   }
	   
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
	   
	   // Installation video URL
	   $installation_video_url = $request->input('installation_video_url');
	    
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
								/*'seller_refund_term' => 'required',
								'item_thumbnail' => 'required|mimes:jpeg,jpg,png|max:'.$image_size,
								'item_preview' => 'required|mimes:jpeg,jpg,png|max:'.$image_size,
								'item_file' => 'mimes:zip|required|max:'.$file_size,
								'video_file' => 'mimes:mp4|max:'.$file_size,
								'item_screenshot.*' => 'image|mimes:jpeg,jpg,png|max:'.$image_size,*/
								
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
		    
		     
		 
		    $data = array('user_id' => $user_id, 'item_token' => $item_token, 'item_name' => $item_name, 'item_desc' => $item_desc, 'item_thumbnail' => $item_thumbnail, 'item_preview' => $item_preview, 'item_file' => $item_file, 'file_type' => $file_type, 'item_file_link' => $item_file_link, 'item_category' =>$cat_id, 'item_category_parent' => $parent_category_id, 'item_category_type' => $cat_name, 'item_type' => $item_type, 'regular_price' => $regular_price, 'extended_price' => $extended_price, 'demo_url' => $demo_url, 'item_tags' => $item_tags, 'item_status' => $item_status, 'item_shortdesc' => $item_shortdesc, 'free_download' => $free_download, 'item_slug' => $item_slug, 'video_url' => $video_url, 'installation_video_url' => $installation_video_url, 'future_update' => $future_update, 'item_support' => $item_support, 'created_item' => $created_item, 'updated_item' => $updated_item, 'video_preview_type' => $video_preview_type, 'video_file' => $video_file, 'item_type_cat_id' => $item_category, 'seller_refund_term' => $seller_refund_term, 'seller_money_back' => $seller_money_back, 'seller_money_back_days' => $seller_money_back_days, 'item_allow_seo' => $item_allow_seo, 'item_seo_keyword' => $item_seo_keyword, 'item_seo_desc' => $item_seo_desc, 'audio_file' => $audio_file, 'item_type_id' => $type_id, 'subscription_item' => $subscription_item, 'item_delimiter' => $item_delimiter, 'item_serials_list' => $item_serials_list, 'item_reviewer' => $item_reviewer);
			
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
			return redirect('/admin/items')->with('success', $item_approve_status);
		
		
		}
	   
	   
	   
	   
	   
	   
	   
	
	}
	
	
	/* sales */
	
	public function view_sales()
	{
	  $orderData['item'] = Items::getuserCheckout();
	  
	  $total_sale = 0;
	  foreach($orderData['item'] as $item)
	  {
	    $total_sale += $item->total;
	  }
	  
	  $order['purchase'] = Items::getpurchaseCheckout();
	  
	  $purchase_sale = 0;
	  foreach($order['purchase'] as $item)
	  {
	    $purchase_sale += $item->total;
	  }
	  if($this->custom() != 0)
	  {
	  return view('sales',[ 'orderData' => $orderData, 'total_sale' => $total_sale, 'purchase_sale' => $purchase_sale]); 
	  }
	  else
	  {
		  return redirect('/admin/license');
	  }
	 
	}
	
	
	
	public function view_order_details($token)
	{
	  $checkout['view'] = Items::singlecheckoutView($token);
	  $order['view'] = Items::getorderView($token);
	  if($this->custom() != 0)
	  {
	  return view('order-details',[ 'checkout' => $checkout, 'order' => $order]);
	  }
	  else
	  {
		  return redirect('/admin/license');
	  }
	}
	
	
	/* sales */
	
	
	
	/* refund */
	
	public function refund_request(Request $request)
	{
	  $item_id = $request->input('item_id');
	  $item_token = $request->input('item_token');
	  $user_id = $request->input('user_id');
	  $item_user_id = $request->input('item_user_id');
	  $ord_id = $request->input('ord_id');
	  $ref_refund_reason = $request->input('refund_reason');
	  $ref_refund_comment = $request->input('refund_comment');
	  $item_url = $request->input('item_url');
	  $refund_count = Items::checkRefund($item_token,$user_id);
	  
	  $savedata = array('ref_item_id' => $item_id, 'ref_order_id' => $ord_id, 'ref_item_token' => $item_token, 'ref_user_id' => $user_id, 'ref_item_user_id' => $item_user_id, 'ref_refund_reason' => $ref_refund_reason, 'ref_refund_comment' => $ref_refund_comment); 
	  
	  
	  
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
		$session_id = Session::getId();
		
	    $allsettings = Settings::allSettings();
		Items::fileDeleted($filename);
		
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
		
		$getdata['first'] = Items::getProdutData($session_id); 
		 $getdata['second'] = Items::getProdutData($session_id);  
		 $getdata['third'] = Items::getProdutZip($session_id);   
		 $getdata['four'] = Items::getProdutData($session_id);   
		 $getdata['five'] = Items::getProdutMP4($session_id); 
		 $getdata['six'] = Items::getProdutMP3($session_id);   
		 $record = '<div class="form-group">
                    <label for="site_desc" class="control-label mb-1">Upload Thumbnail (Size : 80x80px) <span class="require">*</span> - (jpeg,jpg,png,webp)</label><br/>
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
							$record .='<img src="'.$this->Image_Path($edit['item']->item_thumbnail,'no-image.png').'" alt="'.$edit['item']->item_name.'" class="item-thumb">';
						}
						else
						{
							$record .='<img src="'.url('/').'/public/img/no-image.png" alt="'.$edit['item']->item_name.'" class="item-thumb">';
						}
                   } 
				   $record .= '</div>';
		 $record .= '<div class="form-group">
                    <label for="site_desc" class="control-label mb-1">Upload Preview (Size : 361x230px) <span class="require">*</span> - (jpeg,jpg,png,webp)</label><br/>
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
							$record .='<img src="'.$this->Image_Path($edit['item']->item_preview,'no-image.png').'" alt="'.$edit['item']->item_name.'" class="item-thumb">';
						}
						else
						{
							$record .='<img src="'.url('/').'/public/img/no-image.png" alt="'.$edit['item']->item_name.'" class="item-thumb">';
						}
                   } 
				   $record .= '</div>';
				   														
				   
                               								
									 if($additional->show_screenshots == 1)
									 {		
                                     $record .= '<div class="form-group">
                                                <label for="customer_earnings" class="control-label mb-1">Upload Screenshots (multiple) (Size : 750x430px) - (jpeg,jpg,png,webp)</label>
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
												      $record .= '<div class="item-img"><img src="'.$this->Image_Path($item->item_image,'no-image.png').'" alt="'.$item->item_image.'" class="item-thumb">';
													  $record .='<a href="'.url('/admin/edit-item').'/dropimg/'.base64_encode($item->itm_id).'" onClick="return confirm("'.$alerttext.'");" class="drop-icon"><span class="ti-trash drop-icon"></span></a></div>';
													  
												  }
											   }
                                             $record .= '<div class="clearfix"></div></div>';
		                            }
									if($additional->show_video == 1)
									{
									$record .='<div class="form-group">
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
                                            </div>'; 
									         if(!empty($item_token))
								             {
												   if($edit['item']->video_preview_type == 'youtube')
												   {
												   $record .= '<div id="youtube" class="form-group display-block">';
												   }
												   else if($edit['item']->video_preview_type == 'mp4')
												   {
													 $record .= '<div id="youtube" class="form-group display-none">';
												   }
												   else if($edit['item']->video_preview_type == 'mp3')
												   {
													 $record .= '<div id="youtube" class="form-group display-none">';
												   }
												   else
												   {
													  $record .= '<div id="youtube" class="form-group display-none">';
												   }
												}
												else
												{			
												$record .= '<div id="youtube" class="form-group display-none">';
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
                                            </div>';
									if(!empty($item_token))
								             {
												   if($edit['item']->video_preview_type == 'youtube')
												   {
												   $record .= '<div id="mp4" class="form-group display-none">';
												   }
												   else if($edit['item']->video_preview_type == 'mp4')
												   {
													 $record .= '<div id="mp4" class="form-group display-block">';
												   }
												   else if($edit['item']->video_preview_type == 'mp3')
												   {
													 $record .= '<div id="mp4" class="form-group display-none">';
												   }
												   else
												   {
													  $record .= '<div id="mp4" class="form-group display-none">';
												   }
												}
												else
												{			
												$record .= '<div id="mp4" class="form-group display-none">';
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
											   $record .= '</div>';
									if(!empty($item_token))
								                {
												   if($edit['item']->video_preview_type == 'youtube')
												   {
												   $record .= '<div id="mp3" class="form-group display-none">';
												   }
												   else if($edit['item']->video_preview_type == 'mp4')
												   {
													 $record .= '<div id="mp3" class="form-group display-none">';
												   }
												   else if($edit['item']->video_preview_type == 'mp3')
												   {
													 $record .= '<div id="mp3" class="form-group display-block">';
												   }
												   else
												   {
													  $record .= '<div id="mp3" class="form-group display-none">';
												   }
												}
												else
												{			
												$record .= '<div id="mp3" class="form-group display-none">';
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
                                             $record .= '</div>';				
									}
									$record .= '<div class="form-group">
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
                                                $record .= '</select></div>';
								if(!empty($item_token))
								{
								   if($edit['item']->file_type == 'file')
								   {
								   $record .= '<div id="main_file" class="form-group display-block">';
								   }
								   else if($edit['item']->file_type == 'link')
								   {
								     $record .= '<div id="main_file" class="form-group display-none">';
									 
								   }
								   else if($edit['item']->file_type == 'serial')
								   {
								     $record .= '<div id="main_file" class="form-group display-none">';
								   }
								   else
								   {
								      $record .= '<div id="main_file" class="form-group">';
								   }
								}
								else
								{			
								$record .= '<div id="main_file" class="form-group">';
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
											 $record .= '</div>';
											 if(!empty($item_token))
								             {
												   if($edit['item']->file_type == 'file')
												   {
												   $record .= '<div id="main_link" class="form-group display-none">';
												   }
												   else if($edit['item']->file_type == 'link')
												   {
													 $record .= '<div id="main_link" class="form-group display-block">';
												   }
												   else if($edit['item']->file_type == 'serial')
												   {
													 $record .= '<div id="main_link" class="form-group display-none">';
												   }
												   else
												   {
													  $record .= '<div id="main_link" class="form-group">';
												   }
												}
												else
												{			
												$record .= '<div id="main_link" class="form-group">';
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
                                                
                                            </div>';

									
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
												$("#delimiter1").on("change", function() {
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
				  /*if($item['data']->item_delimiter == 'comma')
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
					 
				  }*/
				  $pdf_filename = $item['data']->item_slug.'-serial-key'.'.pdf';
				  $serial_key = $item['data']->item_serials_list;
				  $mydata = ['serial_key' => $serial_key];
				  $pdf = PDF::loadView('serial_view', $mydata);
				  //$record_data = array('item_serials_list' => $balance_key);
				  //Items::updateitemData($token,$record_data);
				  return $pdf->download($pdf_filename);
			  }
			  
			  
		     }
			 else
			 {
				return redirect($item['data']->item_file_link);
			 }
				
				
	}
	
	public function update_notify_data($token)
	{
	   $encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
	   $noti_id   = $encrypter->decrypt($token);
	   $notify_details = Items::singleNotify($noti_id);
	   $data = array('noti_status' => 'read');
	   Items::updateNotify($noti_id,$data);
	   if($notify_details->noti_type == 'create-item' or $notify_details->noti_type == 'update-item')
	   {
	      $item_details = Items::itemDetails($notify_details->noti_type_id);
	      $redirect_url = URL::to("/").'/admin/edit-item/'.$item_details->item_token;
		  return redirect($redirect_url);
		  
	   }
	   else if($notify_details->noti_type == 'withdrawal-request')
	   {
	      $redirect_url = URL::to("/").'/admin/withdrawal';
		  return redirect($redirect_url);
	   }
	   else if($notify_details->noti_type == 'deposit')
	   {
	      $redirect_url = URL::to("/").'/admin/deposit-details';
		  return redirect($redirect_url);
	   }
	   else if($notify_details->noti_type == 'buying-item' or $notify_details->noti_type == 'selling-item')
	   {
	      $redirect_url = URL::to("/").'/admin/orders';
		  return redirect($redirect_url);
	   }
	   else
	   {
	   return redirect(URL::to("/admin/notifications"));
	   }
	
	}
	
	public function view_notify_details()
	{
	   
	   $notifydata = Items::NotifyDisplayAll();
	   $data = array('notifydata' => $notifydata);
	   return view('admin.notifications')->with($data);
	}
	
	public function remove_notify_details($noti_id)
	{
	    $notid = base64_decode($noti_id);
		$notification = Items::singleNotify($notid);
		Items::dropNotify($notid);
		return redirect()->back()->with('success','Notification details has been removed');
		
	}
	
	
}
