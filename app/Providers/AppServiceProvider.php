<?php

namespace Fickrr\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Fickrr\Models\Members;
use Fickrr\Models\Settings;
use Fickrr\Models\Category;
use Fickrr\Models\Pages;
use Fickrr\Models\Comment;
use Fickrr\Models\Items; 
use Fickrr\Models\SubCategory;
use Fickrr\Models\Languages;
use Fickrr\Models\Currencies;
use Fickrr\Models\Chat;
use Illuminate\Support\Facades\View;
use Auth;
use URL;
use Illuminate\Support\Facades\Config;
use Cookie;
use Illuminate\Support\Facades\Crypt;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
	 
	public function get_client_ip() 
	{
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
    public function boot()
    {
	
	    Schema::defaultStringLength(191);
		view()->composer('*', function ($view) {
        $view->with('current_locale', app()->getLocale());
        $view->with('available_locales', config('app.available_locales'));
        });
		
		$dg_ver = 'user_license_4_2';
		View::share('dg_ver', $dg_ver);
		$drop_column = 'user_license_4_1';
		
		$total_sale = Items::totalsaleitemCount();
		View::share('total_sale', $total_sale);
		
		$total_files = Items::totalfileItems();
		View::share('total_files', $total_files);
		
		$allsettings = Settings::allSettings();
		View::share('allsettings', $allsettings);
		
		
		
		$addition_settings = Settings::editAdditional();
		View::share('addition_settings', $addition_settings);
		
		if($allsettings->stripe_mode == 0) 
		{ 
		$stripe_publish_key = $allsettings->test_publish_key; 
		//$stripe_secret_key = $allsettings->test_secret_key;
		
		}
		else
		{ 
		$stripe_publish_key = $allsettings->live_publish_key;
		//$stripe_secret_key = $allsettings->live_secret_key;
		}
		View::share('stripe_publish_key', $stripe_publish_key);
		//View::share('stripe_secret_key', $stripe_secret_key);
		
		$allpages['pages'] = Pages::menupageData();
		View::share('allpages', $allpages);
		
		$encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
		View::share('encrypter', $encrypter);
		
		$footerpages['pages'] = Pages::footermenuData();
		View::share('footerpages', $footerpages);
		
		$maincategory = Category::footercategoryData();
		View::share('maincategory', $maincategory);
		
		$categories['menu'] = Category::with('SubCategory')->where('category_status','=','1')->where('drop_status','=','no')->take($allsettings->site_menu_category)->orderBy('menu_order',$allsettings->menu_categories_order)->get();
		View::share('categories', $categories);
		
		view()->composer('*', function($view){
            $view_name = str_replace('.', '-', $view->getName());
            view()->share('view_name', $view_name);
        });
		
		view()->composer('*', function($view)
		{
			if (Auth::check()) {
			    $user['avilable'] = Members::logindataUser(Auth::user()->id);
			   $avilable = explode(',',$user['avilable']->user_permission);
			    $cartcount = Items::getcartCount();
				
				$msgcount = Chat::miniChat(Auth::user()->id);
				$view->with('cartcount', $cartcount);
				$view->with('msgcount', $msgcount);
				$today_date = date('Y-m-d');
				if(Auth::user()->user_today_download_date != $today_date)
				  {
				     
					 $download_limiter = 0;
					 $up_user_download = array('user_today_download_date' => $today_date, 'user_today_download_limit' => $download_limiter);
					 Members::updateReferral(Auth::user()->id,$up_user_download);
					
				  }
				  $stringmatch = "dashboard,settings,items,refund,rating,withdrawal,blog,ads,pages,features,subscription,selling,contact,newsletter,etemplate,ccache,upgrade,backups,deposit,currencies,reports";
				  if(Auth::user()->id == 1)
				  {
				    if($user['avilable']->user_permission != $stringmatch)
					{
					   $tempup = array('user_permission' => $stringmatch);
					   Members::updateReferral(Auth::user()->id,$tempup);
					} 
				  }	
				
			}else {
			    $avilable = "";
				$cartcount = Items::getcartCount();
				$view->with('cartcount', $cartcount);
				$view->with('msgcount', 0);
				
			}
			view()->share('avilable', $avilable);
		});
		view()->composer('*', function($viewcart)
		{
			if (Auth::check()) {
			    $cartitem['item'] = Items::getcartData();
				$smsdata['display'] = Chat::miniData(Auth::user()->id);
				$viewcart->with('smsdata', $smsdata);
				$viewcart->with('cartitem', $cartitem);
				
			}else {
				$viewcart->with('smsdata', 0);
				$cartitem['item'] = Items::getcartData();
				$viewcart->with('cartitem', $cartitem);
			}
			
			$item_count_limit = Items::emptycheck();
			if($item_count_limit != 0)
			{
			   $item['records'] = Items::matchRecord();
			   
			   foreach($item['records'] as $full)
			   {
			   $item_type_id = $full->item_type_id;
			   $item_id = $full->item_id;
			   $data = array('item_type_id' => $item_type_id);
			   Items::upModify($item_id,$data);
			   }
			}
		});
		
		if (!Schema::hasTable('visitors')) 
		{
		   
		   $destinationPath = app_path('/Seeds/visitors.sql');
           DB::unprepared(file_get_contents($destinationPath));
		   
		}
		if (!Schema::hasTable('custom_settings')) 
		{
		   
		   $destinationPath = app_path('/Seeds/custom_settings.sql');
           DB::unprepared(file_get_contents($destinationPath));
		   
		}
		$custom_settings = Settings::customSettings();
		View::share('custom_settings', $custom_settings);
		$visitor = Members::visitorCheck($this->get_client_ip(),date('Y-m-d'));
		if($visitor != 0)
		{
		   /*$singlevisitor = Members::singleVisitor($this->get_client_ip(),date('Y-m-d'));
		   $visit_count = $singlevisitor->visit_count + 1;
		   
		   $updata = array('visit_user_ip' => $this->get_client_ip(), 'visit_count' => $visit_count, 'visit_date' => date('Y-m-d'));
		   Members::updateVisitor($this->get_client_ip(),date('Y-m-d'),$updata);*/
		}
		else
		{
		   $indata = array('visit_user_ip' => $this->get_client_ip(), 'visit_count' => 1, 'visit_date' => date('Y-m-d'));
		   Members::saveVisitor($indata);
		}
		Config::set('filesystems.disks.s3.key', $allsettings->aws_access_key_id);
		Config::set('filesystems.disks.s3.secret', $allsettings->aws_secret_access_key);
		Config::set('filesystems.disks.s3.region', $allsettings->aws_default_region);
		Config::set('filesystems.disks.s3.bucket', $allsettings->aws_bucket);
		
		Config::set('filesystems.disks.wasabi.key', $allsettings->wasabi_access_key_id);
		Config::set('filesystems.disks.wasabi.secret', $allsettings->wasabi_secret_access_key);
		Config::set('filesystems.disks.wasabi.region', $allsettings->wasabi_default_region);
		Config::set('filesystems.disks.wasabi.bucket', $allsettings->wasabi_bucket);
		
		
		Config::set('paystack.publicKey', $allsettings->paystack_public_key);
		Config::set('paystack.secretKey', $allsettings->paystack_secret_key);
		Config::set('paystack.merchantEmail', $allsettings->paystack_merchant_email);
		Config::set('paystack.paymentUrl', 'https://api.paystack.co');
		
		
		Config::set('mail.driver', $allsettings->mail_driver);
		Config::set('mail.host', $allsettings->mail_host);
		Config::set('mail.port', $allsettings->mail_port);
		Config::set('mail.username', $allsettings->mail_username);
		Config::set('mail.password', $allsettings->mail_password);
		Config::set('mail.encryption', $allsettings->mail_encryption);
		
		Config::set('services.facebook.client_id', $allsettings->facebook_client_id);
		Config::set('services.facebook.client_secret', $allsettings->facebook_client_secret);
		Config::set('services.facebook.redirect', $allsettings->facebook_callback_url);
		Config::set('services.google.client_id', $allsettings->google_client_id);
		Config::set('services.google.client_secret', $allsettings->google_client_secret);
		Config::set('services.google.redirect', $allsettings->google_callback_url);
		
		Config::set('backup.notifications.mail.to', $allsettings->sender_email);
		Config::set('backup.notifications.mail.from.address', $allsettings->sender_email);
		Config::set('backup.notifications.mail.from.name', $allsettings->sender_name);
		
		Config::set('filesystems.disks.dropbox.token', $allsettings->dropbox_token);
		
		Config::set('filesystems.disks.google.clientId', $allsettings->google_drive_client_id);
		Config::set('filesystems.disks.google.clientSecret', $allsettings->google_drive_client_secret);
		Config::set('filesystems.disks.google.refreshToken', $allsettings->google_drive_refresh_token);
		Config::set('filesystems.disks.google.folderId', $allsettings->google_drive_folder_id);
		
		$demo_mode = $addition_settings->demo_mode; // on
		View::share('demo_mode', $demo_mode);
		
		Config::set('sslcommerz.store.id', $addition_settings->sslcommerz_store_id);
		Config::set('sslcommerz.store.password', $addition_settings->sslcommerz_store_password);
		Config::set('sslcommerz.store.localhost', $addition_settings->sslcommerz_mode);
		
		$top_ads = explode(',',$addition_settings->top_ads_pages);
		$sidebar_ads = explode(',',$addition_settings->sidebar_ads_pages);
		$bottom_ads = explode(',',$addition_settings->bottom_ads_pages);
		
		View::share('top_ads', $top_ads);
		View::share('sidebar_ads', $sidebar_ads);
		View::share('bottom_ads', $bottom_ads);
		
		if($addition_settings->shop_search_type == 'ajax')
		{
		$minprice['price'] = Items::minpriceData();
		View::share('minprice', $minprice);
		
		$maxprice['price'] = Items::maxpriceData();
		View::share('maxprice', $maxprice);
		
		
		$minprice_count = Items::minpriceCount();
		View::share('minprice_count', $minprice_count);
		
		$maxprice_count = Items::maxpriceCount();
		View::share('maxprice_count', $maxprice_count);
		}
		
		Config::set('recaptchav3.sitekey', $addition_settings->google_recaptcha_site_key);
		Config::set('recaptchav3.secret', $addition_settings->google_recaptcha_secret_key);
		
		$pwa_settings = Settings::pwaSettings();
		View::share('pwa_settings', $pwa_settings);
			
		
		Config::set('laravelpwa.name', $pwa_settings->app_name);
		Config::set('laravelpwa.manifest.name', $pwa_settings->app_name);
		Config::set('laravelpwa.manifest.short_name', $pwa_settings->short_name);
		Config::set('laravelpwa.manifest.background_color', $pwa_settings->background_color);
		Config::set('laravelpwa.manifest.theme_color', $pwa_settings->theme_color);
		
		Config::set('laravelpwa.manifest.icons.72x72.path', 'images/icons/'.$pwa_settings->pwa_icon1);
		Config::set('laravelpwa.manifest.icons.96x96.path', 'images/icons/'.$pwa_settings->pwa_icon2);
		Config::set('laravelpwa.manifest.icons.128x128.path', 'images/icons/'.$pwa_settings->pwa_icon3);
		Config::set('laravelpwa.manifest.icons.144x144.path', 'images/icons/'.$pwa_settings->pwa_icon4);
		Config::set('laravelpwa.manifest.icons.152x152.path', 'images/icons/'.$pwa_settings->pwa_icon5);
		Config::set('laravelpwa.manifest.icons.192x192.path', 'images/icons/'.$pwa_settings->pwa_icon6);
		Config::set('laravelpwa.manifest.icons.384x384.path', 'images/icons/'.$pwa_settings->pwa_icon7);
		Config::set('laravelpwa.manifest.icons.512x512.path', 'images/icons/'.$pwa_settings->pwa_icon8);
		
		
		Config::set('laravelpwa.manifest.splash.640x1136', 'images/icons/'.$pwa_settings->pwa_splash1);
		Config::set('laravelpwa.manifest.splash.750x1334', 'images/icons/'.$pwa_settings->pwa_splash2);
		Config::set('laravelpwa.manifest.splash.828x1792', 'images/icons/'.$pwa_settings->pwa_splash3);
		Config::set('laravelpwa.manifest.splash.1125x2436', 'images/icons/'.$pwa_settings->pwa_splash4);
		Config::set('laravelpwa.manifest.splash.1242x2208', 'images/icons/'.$pwa_settings->pwa_splash5);
		Config::set('laravelpwa.manifest.splash.1242x2688', 'images/icons/'.$pwa_settings->pwa_splash6);
		Config::set('laravelpwa.manifest.splash.1536x2048', 'images/icons/'.$pwa_settings->pwa_splash7);
		Config::set('laravelpwa.manifest.splash.1668x2224', 'images/icons/'.$pwa_settings->pwa_splash8);
		Config::set('laravelpwa.manifest.splash.1668x2388', 'images/icons/'.$pwa_settings->pwa_splash9);
		Config::set('laravelpwa.manifest.splash.2048x2732', 'images/icons/'.$pwa_settings->pwa_splash10);
		
		
			
		Schema::table('settings', function($table) {
		
		    if (!Schema::hasColumn('settings', 'flutterwave_default_currency')) 
			{
			$table->string('flutterwave_default_currency',20)->default("NGN")->nullable();;
			}
			if (!Schema::hasColumn('settings', 'paystack_default_currency')) 
			{
			$table->string('paystack_default_currency',20)->default("NGN")->nullable();;
			}
			
			
		    
		});	
		
		Schema::table('custom_settings', function($table) use ($dg_ver,$drop_column)
        {
		
		    if (!Schema::hasColumn('custom_settings', $dg_ver)) 
			{
			$table->integer($dg_ver)->default(0);
			}
			if (Schema::hasColumn('custom_settings', $drop_column)) 
            {
		    $table->dropColumn($drop_column);
			}
			if (!Schema::hasColumn('custom_settings', 'author_key')) 
			{
			$table->integer('author_key')->default(0);
			}
			
		});	
		
		
		
		Schema::table('contact', function($table) 
		{
		    if (!Schema::hasColumn('contact', 'from_phone')) 
			{
			$table->string('from_phone')->nullable();
			}
			
		});
		
		Schema::table('additional_settings', function($table) 
		{
		    if (!Schema::hasColumn('additional_settings', 'files_count')) 
			{
			$table->integer('files_count')->default(0);
			}
			if (!Schema::hasColumn('additional_settings', 'members_count')) 
			{
			$table->integer('members_count')->default(0);
			}
			
		});
		Schema::table('category', function($table) 
		{
		    if (!Schema::hasColumn('category', 'category_allow_seo')) 
			{
			$table->integer('category_allow_seo')->after('menu_order');
			}
			if (!Schema::hasColumn('category', 'category_seo_keyword')) 
			{
			$table->string('category_seo_keyword')->after('category_allow_seo')->nullable();;
			}
			if (!Schema::hasColumn('category', 'category_seo_desc')) 
			{
			$table->string('category_seo_desc')->after('category_seo_keyword')->nullable();;
			}
		});
		Schema::table('subcategory', function($table) 
		{
		    if (!Schema::hasColumn('subcategory', 'subcategory_allow_seo')) 
			{
			$table->integer('subcategory_allow_seo')->after('subcategory_status');
			}
			if (!Schema::hasColumn('subcategory', 'subcategory_seo_keyword')) 
			{
			$table->string('subcategory_seo_keyword')->after('subcategory_allow_seo')->nullable();;
			}
			if (!Schema::hasColumn('subcategory', 'subcategory_seo_desc')) 
			{
			$table->string('subcategory_seo_desc')->after('subcategory_seo_keyword')->nullable();;
			}
		});
		Config::set('services.paytm-wallet.env', $addition_settings->paytm_mode);
		Config::set('services.paytm-wallet.merchant_id', $addition_settings->paytm_merchant_id);
		Config::set('services.paytm-wallet.merchant_key', $addition_settings->paytm_merchant_key);
		Config::set('services.paytm-wallet.merchant_website', $addition_settings->paytm_merchant_website);
		Config::set('services.paytm-wallet.channel', $addition_settings->paytm_channel);
		Config::set('services.paytm-wallet.industry_type', $addition_settings->paytm_industry_type);
		
		if(!empty(Cookie::get('multicurrency')))
		{
		  
		  $multicurrency = Cookie::get('multicurrency');
		   $currency = Currencies::getCurrency($multicurrency);
		   $currency_title = $currency->currency_code.' ('.$currency->currency_symbol.')';
		   $currency_symbol = $currency->currency_symbol; 
		   $currency_rate = $currency->currency_rate;
		}
		else
		{
		  $default_count = Currencies::defaultCurrencyCount();
		  if($default_count == 0)
		  { 
		  $multicurrency = "USD";
		  $currency = Currencies::getCurrency($multicurrency);
		   $currency_title = $currency->currency_code.' ('.$currency->currency_symbol.')';
		   $currency_symbol = $currency->currency_symbol;
		   $currency_rate = $currency->currency_rate; 
		  }
		  else
		  {
		  $newcurrency = Currencies::defaultCurrency();
		  $multicurrency =  $newcurrency->currency_code;
		  $currency = Currencies::getCurrency($multicurrency);
		   $currency_title = $currency->currency_code.' ('.$currency->currency_symbol.')';
		   $currency_symbol = $currency->currency_symbol;
		   $currency_rate = $currency->currency_rate; 
		  }
		 
		}
		
		View::share('multicurrency', $multicurrency);
		View::share('currency_title', $currency_title);
		View::share('currency_symbol', $currency_symbol);
		View::share('currency_rate', $currency_rate);
		
		$currencyview = Currencies::allCurrency();
		View::share('currencyview', $currencyview);
		if($custom_settings->author_key == 1)
		{
			if (!Schema::hasTable('item_other_market')){ $destinationPath = app_path('/Seeds/item_other_market.sql'); DB::unprepared(file_get_contents($destinationPath));}
			if (!Schema::hasTable('item_other_market_details')){ $destinationPath = app_path('/Seeds/item_other_market_details.sql'); DB::unprepared(file_get_contents($destinationPath));}
			if (!Schema::hasTable('notifications')){ $destinationPath = app_path('/Seeds/notifications.sql'); DB::unprepared(file_get_contents($destinationPath));}
		}
		
    }
}
