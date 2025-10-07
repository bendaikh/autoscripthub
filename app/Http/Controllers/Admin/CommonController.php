<?php

namespace Fickrr\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Fickrr\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Fickrr\Models\Settings;
use Fickrr\Models\Pages;
use Fickrr\Models\Members;
use Fickrr\Models\Deposit;
use Fickrr\Models\EmailTemplate;
use Auth;
use Mail;
use Artisan;
use URL;
use DateTime;
use Illuminate\Support\Facades\File;
use Illuminate\Filesystem\Filesystem;
use Storage;
use Illuminate\Support\Facades\DB;
use Helper;
use ZipArchive;

class CommonController extends Controller
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
    
	public function logout(Request $request) {
	  Auth::logout();
	  return redirect('/login');
    }
	
	public function view_upgrade()
	{
	   if($this->custom() != 0)
	   {
	   return view('admin.upgrade');
	   }
	   else
	   {
		  return redirect('/admin/license');
	   }
	}
	
	public function upgrade_version(Request $request) 
	{
	  
	  
	   
 
       /*$image = $request->file('update_file');
	   $img_name = time() . uniqid().'.'.$image->getClientOriginalExtension();
	   $destinationPath = base_path('/public/storage/data/');
       $image->move($destinationPath, $img_name);
       return response()->json(['success'=>'Image Uploaded Successfully']);*/
	   
	    $this->validate($request, [
		 
		                    'envato_purchased_code' => 'required',
							'update_file' => 'mimes:zip',

        	]);
        
		$rules = array();
		$messages = array();
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails())
		{
			$failedRules = $validator->failed();
			return back()->withErrors($validator);
		}
		else
		{ 
			
			$purchased_code = $request->input('envato_purchased_code');
			$code= $purchased_code; 
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
					    if ($request->hasFile('update_file')) 
					    {
						$image = $request->file('update_file');
						$img_name = time() . uniqid().'.'.$image->getClientOriginalExtension();
						
						
						$destinationPath = base_path('/public/storage/data/');
						$imagePath = $destinationPath. "/".  $img_name;
						$image->move($destinationPath, $img_name);
						$addition_data = array('upgrade_files' => $img_name);
						Settings::updateAdditionData($addition_data);
						$data = array('author_key' => $key_val);
					    Settings::updateCustom($data);
						
						$zip = new ZipArchive;
						$zip->open(base_path('/public/storage/data/'.$img_name));
						$zip->extractTo(base_path('/'));
						Settings::deleteUpgrade();
						
						}
						
						return response()->json(['msg'=>'Success! Upgrade Done']);
						
						
			} 
			else 
			{  
						
						return response()->json(['msg'=>'FAILED: Invalid Purchase Code']);
			} 
			
			
			
		}	
	
	}
	
	public function delete_cache()
	{
	    Artisan::call('cache:clear');
		Artisan::call('view:clear');
		Artisan::call('config:cache');
		Artisan::call('optimize:clear');
		/*return redirect(admin/contact)->with('success','All cache data has been cleared');*/
		return redirect()->back()->with('success','All cache data has been cleared');
		
	}
	
	public function view_contact()
	{
	  
	  $contactData['view'] = Pages::getcontactData();
	  $data = array('contactData' => $contactData);
	  if($this->custom() != 0)
	  {
	  return view('admin.contact')->with($data);
	  }
	  else
	  {
		  return redirect('/admin/license');
	  }
	}
	
	
	public function all_delete_contact(Request $request)
	{
	   
	   $cid = $request->input('cid');
	   foreach($cid as $id)
	   {
	      Pages::deleteContact($id);
	   }
	   return redirect()->back()->with('success','Delete successfully.');
	
	}
	
	
	public function view_contact_delete($id)
	{
	   Pages::deleteContact($id);
	   return redirect()->back()->with('success','Contact details has been deleted');
	}
	
	public function view_add_contact()
	{
	    if($this->custom() != 0)
	    { 
	    return view('admin.add-contact');
		}
		else
		{
			  return redirect('/admin/license');
		}
	}
	
	public function update_contact(Request $request)
	{
	
	  $from_name = $request->input('from_name');
	  $from_email = $request->input('from_email');
	  $from_phone = $request->input('from_phone');
	  $subject = $request->input('subject');
	  $message_text = $request->input('message_text');
	  
	  $contact_count = Members::getcontactCount($from_email);
	  if($contact_count == 0)
	  {
	  $record = array('from_name' => $from_name, 'from_email' => $from_email, 'message_text' => $message_text, 'contact_date' => date('Y-m-d'), 'from_phone' => $from_phone, 'subject' => $subject);
	  Members::saveContact($record);
	  
	  return redirect('admin/contact')->with('success','Added successfully');
	  }
	  else
	  {
	  return redirect('admin/contact')->with('error','Sorry! Contact details already added');
	  }
	  
	  
	
	}
	
	/* newsletter */
	
	public function view_newsletter()
	{
	  
	  $newsData['view'] = Pages::getnewsletterData();
	  $data = array('newsData' => $newsData);
	  if($this->custom() != 0)
	  { 
	  return view('admin.newsletter')->with($data);
	  }
      else
	  {
			  return redirect('/admin/license');
	  }
	
	}
	
	
	public function all_delete_newsletter(Request $request)
	{
	   
	   $news_id = $request->input('news_id');
	   foreach($news_id as $id)
	   {
	      Pages::deleteNewsletter($id);
	   }
	   return redirect()->back()->with('success','Delete successfully.');
	
	}
	
	
	public function view_newsletter_delete($id)
	{
	   Pages::deleteNewsletter($id);
	   return redirect()->back()->with('success','Delete successfully.');
	}
	
	
	public function view_send_updates()
	{
	  $newsData['view'] = Pages::getactiveNewsletter();
	  $data = array('newsData' => $newsData);
	  if($this->custom() != 0)
	  { 
	  return view('admin.send-updates')->with($data);
	  }
      else
	  {
			  return redirect('/admin/license');
	  }
	}
	
	
	public function send_updates(Request $request)
	{
	   
	   
	   $news_heading = $request->input('news_heading');
	   $news_content = $request->input('news_content');
	   $news_email = $request->input('news_email');
	   
	     
         
		 $request->validate([
		 
							
					'news_heading' => 'required',
					'news_content' => 'required',		
							
							
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
		   
		   foreach($news_email as $to_email)
		   {
		     
			    $sid = 1;
				$setting['setting'] = Settings::editGeneral($sid);
				$from_name = $setting['setting']->sender_name;
				$from_email = $setting['setting']->sender_email;
				$record = array('news_heading' => $news_heading, 'news_content' => $news_content);
				/* email template code */
	              $checktemp = EmailTemplate::checkTemplate(16);
				  if($checktemp != 0)
				  {
				  $template_view['mind'] = EmailTemplate::viewTemplate(16);
				  $template_subject = $template_view['mind']->et_subject;
				  }
				  else
				  {
				  $template_subject = "Newsletter Updates";
				  }
			     /* email template code */
				Mail::send('admin.newsletter_update_mail', $record, function($message) use ($from_name, $from_email, $to_email, $template_subject) {
					$message->to($to_email)
							->subject($template_subject);
					$message->from($from_email,$from_name);
				});
		
		   
		   }
		
			
           return redirect()->back()->with('success', 'Your message has been sent successfully.');
            
 
        } 
     
	
	
	}
	
	
	
	/* newsletter */
	
	public function deposit_details()
	{
	   $depositData['view'] = Deposit::getdepositDetails();
	   $data = array('depositData' => $depositData);
	   if($this->custom() != 0)
	   {
	   return view('admin.deposit-details')->with($data);
	   }
	   else
	   {
		  return redirect('/admin/license');
	   }
	}
	
	public function view_deposit()
	{
	   $depositData['view'] = Deposit::getdepositData();
	   $data = array('depositData' => $depositData);
	   if($this->custom() != 0)
	   {
	   return view('admin.deposit')->with($data);
	   }
	   else
	   {
		  return redirect('/admin/license');
	   }
	}
	
	public function add_deposit()
	{
	   if($this->custom() != 0)
	   {
	   return view('admin.add-deposit');
	   }
	   else
	   {
		  return redirect('/admin/license');
	   }
	}
	
	
	public function save_deposit(Request $request)
	{
 
    
         $deposit_price = $request->input('deposit_price');
		 
		 $deposit_status = $request->input('deposit_status');
		 
		 if($request->input('deposit_bonus'))
		 {
		    $deposit_bonus = $request->input('deposit_bonus');
		 }
		 else
		 {
		   $deposit_bonus = 0;
		 }
		
		 
		 
         
		 $request->validate([
							'deposit_price' => 'required',
							'deposit_status' => 'required',
							
							
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
		
		
		 
		$data = array('deposit_price' => $deposit_price, 'deposit_status' => $deposit_status, 'deposit_bonus' => $deposit_bonus);
        Deposit::insertDeposit($data);
        return redirect('/admin/deposit')->with('success', 'Insert successfully.');
            
 
       } 
     
    
  }
  
  public function all_delete_deposit_details(Request $request)
	{
	   
	   $dd_id = $request->input('dd_id');
	   foreach($dd_id as $id)
	   {
	      Deposit::deleteDepositDetails($id);
	   }
	   return redirect()->back()->with('success','Delete successfully.');
	
	}
  
  public function delete_deposit_details($deposit_id){

      
	  
      Deposit::deleteDepositDetails($deposit_id);
	  
	  return redirect()->back()->with('success', 'Delete successfully.');

    
  }
  
  
  public function all_delete_deposit(Request $request)
	{
	   
	   $dep_id = $request->input('dep_id');
	   foreach($dep_id as $id)
	   {
	      Deposit::deleteDeposit($id);
	   }
	   return redirect()->back()->with('success','Delete successfully.');
	
	}
  
  public function delete_deposit($deposit_id){

      
	  
      Deposit::deleteDeposit($deposit_id);
	  
	  return redirect()->back()->with('success', 'Delete successfully.');

    
  }
  
  
   public function edit_deposit($deposit_id)
	{
	   
	   $edit['deposit'] = Deposit::editDeposit($deposit_id);
	   if($this->custom() != 0)
	   {
	   return view('admin.edit-deposit', [ 'edit' => $edit, 'deposit_id' => $deposit_id]);
	   }
	   else
	   {
		  return redirect('/admin/license');
	   }
	}
	
	
	public function update_deposit(Request $request)
	{
 
    
         $deposit_price = $request->input('deposit_price');
		 
		 $deposit_status = $request->input('deposit_status');
		 
		 if($request->input('deposit_bonus'))
		 {
		    $deposit_bonus = $request->input('deposit_bonus');
		 }
		 else
		 {
		   $deposit_bonus = 0;
		 }
		
		 $dep_id = $request->input('dep_id');
		 
         
		 $request->validate([
							'deposit_price' => 'required',
							'deposit_status' => 'required',
							
							
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
		
		
		 
		$data = array('deposit_price' => $deposit_price, 'deposit_status' => $deposit_status, 'deposit_bonus' => $deposit_bonus);
        Deposit::updateDeposit($dep_id, $data);
        return redirect('/admin/deposit')->with('success', 'Update successfully.');
            
 
       } 
     
    
  }
  
  
	
	
}
