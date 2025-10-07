<?php


Route::group(['middleware' => ['XSS']], function () {

/* 

Route::get('lang/{lang}', function ($locale){
    Session::put('lang', $locale);
	App::setLocale($locale);
    return redirect()->back();
});


 */

Route::get('/', 'CommonController@view_index');
Route::get('/index', 'CommonController@view_index');
Route::post('/index', ['as' => 'index','uses'=>'CommonController@update_video']);
Route::get('/download/{url}/{title}/{mime}/{ext}/{size}', 'CommonController@view_download');

Route::get('login/{provider}', 'Auth\LoginController@redirectToProvider');
Route::get('login/{provider}/callback', 'Auth\LoginController@handleProviderCallback');
Route::get('searchajax',array('as'=>'searchajax','uses'=>'CommonController@autoComplete'));
Auth::routes();

Route::get('/logout', 'Admin\CommonController@logout');



/* email verification */

Route::get('/user-verify/{user_token}', 'CommonController@user_verify');

/* email verification */


/* my profile */

Route::get('/my-profile', 'ProfileController@view_myprofile');
Route::post('/my-profile', ['as' => 'my-profile','uses'=>'ProfileController@update_myprofile']);

/* my profile */


/* blog */
Route::get('/blog', 'BlogController@view_blog');
Route::get('/single/{slug}', 'BlogController@view_single');
Route::get('/blog/{category}/{id}/{slug}', 'BlogController@view_category_blog');
Route::post('/single', ['as' => 'single','uses'=>'BlogController@insert_comment']);
Route::get('/blog/{tag}', 'BlogController@view_tags');
/* blog */



/* withdrawal request */
Route::get('/withdrawal', 'ProductController@view_withdrawal');
Route::post('/withdrawal', ['as' => 'withdrawal','uses'=>'ProductController@withdrawal_request']);

/* withdrawal request */



/* forgot */

Route::get('/forgot', 'CommonController@view_forgot');
Route::post('/forgot', ['as' => 'forgot','uses'=>'CommonController@update_forgot']);
Route::get('/reset/{user_token}', 'CommonController@view_reset');
Route::post('/reset', ['as' => 'reset','uses'=>'CommonController@update_reset']);

/* forgot */


/* homepage pages */
Route::get('/free-items', 'CommonController@view_free_items');
Route::get('/featured-items', 'CommonController@view_featured_items');
Route::get('/sale', 'CommonController@view_sale_items');
Route::get('/popular-items', 'CommonController@view_popular_items');
Route::get('/new-releases', 'CommonController@view_new_items');
/* homepage pages */



/* pages */

Route::get('/page/{page_slug}', 'PageController@view_page');

/* pages */


/* success */
Route::get('/success/{ord_token}', 'ProfileController@paypal_success');
Route::get('/cancel', 'ProfileController@payment_cancel');
/* success */


/* contact */

Route::get('/contact', 'CommonController@view_contact');
Route::post('/contact', ['as' => 'contact','uses'=>'CommonController@update_contact']);
/* contact */


/* item */
Route::get('/item/{slug}', 'CommonController@view_item');
Route::get('/item/{download}/{token}', 'CommonController@view_free_item');
Route::get('/item/{id}/{favorite}/{liked}', 'ProductController@view_favorite_item');
Route::post('/support', ['as' => 'support','uses'=>'ProductController@contact_support']);
Route::post('/post-comment', ['as' => 'post-comment','uses'=>'ProductController@add_post_comment']);
Route::post('/reply-post-comment', ['as' => 'reply-post-comment','uses'=>'ProductController@reply_post_comment']);
Route::get('/tag/{item}/{slug}', 'CommonController@view_tags');
/* item */


/* purchases */
Route::get('/404', 'CommonController@not_found');
Route::get('/my-purchases', 'ProductController@view_purchases');
Route::get('/my-purchases/{token}', 'ProductController@purchases_download');
Route::get('/invoice/{product_token}/{order_id}', 'ProductController@invoice_download');
Route::post('/my-purchases', ['as' => 'my-purchases','uses'=>'ProductController@rating_purchases']);
Route::post('/refund', ['as' => 'refund','uses'=>'ProductController@refund_request']);
/* purchases */


/* shop */

Route::get('/shop', 'CommonController@view_all_items');
Route::post('/shop', ['as' => 'shop','uses'=>'CommonController@view_shop_items']);
Route::get('/shop/{type}/{slug}', 'CommonController@view_category_items');

/* shop */



/* cart */
Route::get('/cart', 'ProductController@show_cart');
Route::get('/cart/{ord_id}', 'ProductController@remove_cart_item');
Route::post('/cart', ['as' => 'cart','uses'=>'ProductController@view_cart']);
Route::get('/clear-cart', 'ProductController@clear_cart');
/* cart */


/* checkout */
Route::get('/checkout', 'ProductController@show_checkout');
Route::post('/checkout', ['as' => 'checkout','uses'=>'ProductController@view_checkout']);
/* checkout */


/* success */
Route::get('/success/{ord_token}', 'ProductController@paypal_success');
Route::get('/cancel', 'CommonController@payment_cancel');
/* success */


/* favourites */
Route::get('/my-favourite', 'ProductController@favourites_item');
Route::get('/my-favourite/{fav_id}/{item_id}', 'ProductController@remove_favourites_item');
/* favourites */

/* paystack */
Route::post('/paystack', ['as' => 'paystack','uses'=>'ProductController@redirectToGateway']);
Route::get('/paystack', 'ProductController@handleGatewayCallback');
/* paystack */


/* razorpay */
Route::post('/razorpay', ['as' => 'razorpay','uses'=>'ProductController@razorpay_payment']);
/* razorpay */


});

/* admin panel */


    Route::group(['middleware' => ['is_admin', 'XSS']], function () {
    Route::get('/admin', 'Admin\AdminController@admin');
	
	
	/* customer */
	Route::get('/admin/customer', 'Admin\MembersController@customer');
	Route::get('/admin/add-customer', 'Admin\MembersController@add_customer')->name('admin.add-customer');
	Route::post('/admin/add-customer', 'Admin\MembersController@save_customer');
	Route::get('/admin/customer/{token}', 'Admin\MembersController@delete_customer');
	Route::get('/admin/edit-customer/{token}', 'Admin\MembersController@edit_customer')->name('admin.edit-customer');
	Route::post('/admin/edit-customer', ['as' => 'admin.edit-customer','uses'=>'Admin\MembersController@update_customer']);
	/* customer */
	
	
	/* administrator */
	Route::get('/admin/administrator', 'Admin\MembersController@administrator');
	Route::get('/admin/add-administrator', 'Admin\MembersController@add_administrator')->name('admin.add-administrator');
	Route::post('/admin/add-administrator', 'Admin\MembersController@save_administrator');
	Route::get('/admin/administrator/{token}', 'Admin\MembersController@delete_administrator');
	Route::get('/admin/edit-administrator/{token}', 'Admin\MembersController@edit_administrator')->name('admin.edit-administrator');
	Route::post('/admin/edit-administrator', ['as' => 'admin.edit-administrator','uses'=>'Admin\MembersController@update_administrator']);
	/* administrator */
	
	/* country */
	Route::get('/admin/country-settings', 'Admin\SettingsController@country_settings');
	Route::get('/admin/add-country', 'Admin\SettingsController@add_country')->name('admin.add-country');
	Route::post('/admin/add-country', 'Admin\SettingsController@save_country');
	Route::get('/admin/country-settings/{cid}', 'Admin\SettingsController@delete_country');
	Route::get('/admin/edit-country/{cid}', 'Admin\SettingsController@edit_country')->name('admin.edit-country');
	Route::post('/admin/edit-country', ['as' => 'admin.edit-country','uses'=>'Admin\SettingsController@update_country']);
    /* country */

		
	/* edit profile */
	
	Route::get('/admin/edit-profile', 'Admin\MembersController@edit_profile');
	Route::post('/admin/edit-profile', ['as' => 'admin.edit-profile','uses'=>'Admin\MembersController@update_profile']);
	/* edit profile */
	
	
	/* general settings */
	
	Route::get('/admin/general-settings', 'Admin\SettingsController@general_settings');
	Route::post('/admin/general-settings', ['as' => 'admin.general-settings','uses'=>'Admin\SettingsController@update_general_settings']);
		
	/* general settings */
	
	
	/* media settings */
	
	Route::get('/admin/media-settings', 'Admin\SettingsController@media_settings');
	Route::post('/admin/media-settings', ['as' => 'admin.media-settings','uses'=>'Admin\SettingsController@update_media_settings']);
		
	/* media settings */
	
	
	/* email settings */
	
	Route::get('/admin/email-settings', 'Admin\SettingsController@email_settings');
	Route::post('/admin/email-settings', ['as' => 'admin.email-settings','uses'=>'Admin\SettingsController@update_email_settings']);
	
	/* email settings */
	
	/* currency settings */
	Route::get('/admin/currency-settings', 'Admin\SettingsController@currency_settings');
	Route::post('/admin/currency-settings', ['as' => 'admin.currency-settings','uses'=>'Admin\SettingsController@update_currency_settings']);
	/* currency settings */
	
	
	/* preferred settings */
	Route::get('/admin/preferred-settings', 'Admin\SettingsController@preferred_settings');
	Route::post('/admin/preferred-settings', ['as' => 'admin.preferred-settings','uses'=>'Admin\SettingsController@update_preferred_settings']);
	/* preferred settings */
	
	
	/* limitation settings */
	Route::get('/admin/limitation-settings', 'Admin\SettingsController@limitation_settings');
	Route::post('/admin/limitation-settings', ['as' => 'admin.limitation-settings','uses'=>'Admin\SettingsController@update_limitation_settings']);
	/* limitation settings */
	
	
	/* social settings */
	
	Route::get('/admin/social-settings', 'Admin\SettingsController@social_settings');
	Route::post('/admin/social-settings', ['as' => 'admin.social-settings','uses'=>'Admin\SettingsController@update_social_settings']);
	
	/* social settings */
	
	
	/* color settings */
	
	Route::get('/admin/color-settings', 'Admin\SettingsController@color_settings');
	Route::post('/admin/color-settings', ['as' => 'admin.color-settings','uses'=>'Admin\SettingsController@update_color_settings']);
	
	/* color settings */
	
	
	
	/* payment settings */
	
	Route::get('/admin/payment-settings', 'Admin\SettingsController@payment_settings');
	Route::post('/admin/payment-settings', ['as' => 'admin.payment-settings','uses'=>'Admin\SettingsController@update_payment_settings']);
	
	/* payment settings */
	
	
	
	/* demo mode */
	Route::post('/admin/demo-mode', ['as' => 'admin.demo-mode','uses'=>'Admin\SettingsController@update_demo_mode']);
	Route::get('/admin/demo-mode', 'Admin\SettingsController@demo_mode');
	/* demo mode */
	
	
	
	/* category */
	
	Route::get('/admin/category', 'Admin\CategoryController@category');
	Route::get('/admin/add-category', 'Admin\CategoryController@add_category')->name('admin.add-category');
	Route::post('/admin/add-category', 'Admin\CategoryController@save_category');
	Route::get('/admin/category/{cat_id}', 'Admin\CategoryController@delete_category');
	Route::get('/admin/edit-category/{cat_id}', 'Admin\CategoryController@edit_category')->name('admin.edit-category');
	Route::post('/admin/edit-category', ['as' => 'admin.edit-category','uses'=>'Admin\CategoryController@update_category']);
	/* category */
	
	
	/* development */
	
	Route::get('/admin/development', 'Admin\ProductController@view_development');
	Route::get('/admin/add-development', 'Admin\ProductController@add_development')->name('admin.add-development');
	Route::post('/admin/add-development', 'Admin\ProductController@save_development');
	Route::get('/admin/development/{logo_id}', 'Admin\ProductController@delete_development');
	Route::get('/admin/edit-development/{logo_id}', 'Admin\ProductController@edit_development')->name('admin.edit-development');
	Route::post('/admin/edit-development', ['as' => 'admin.edit-development','uses'=>'Admin\ProductController@update_development']);
	
	/* development */
	
	
	
	
	/* products */
	
	Route::get('/admin/products', 'Admin\ProductController@view_products');
	Route::get('/admin/add-product', 'Admin\ProductController@add_product')->name('admin.add-product');
	Route::post('/admin/add-product', 'Admin\ProductController@save_product');
	Route::get('/admin/products/{product_token}', 'Admin\ProductController@delete_product');
	Route::get('/admin/edit-product/{product_token}', 'Admin\ProductController@edit_product')->name('admin.edit-product');
	Route::post('/admin/edit-product', ['as' => 'admin.edit-product','uses'=>'Admin\ProductController@update_product']);
	Route::get('/admin/edit-product/{dropimg}/{token}', 'Admin\ProductController@drop_image_product');
	/* products */
	
	
	/* product import & export */
	Route::get('/admin/products-import-export', 'Admin\ImportExportController@view_products_import_export');
	Route::post('/admin/products-import-export', ['as' => 'admin.products-import-export','uses'=>'Admin\ImportExportController@products_import']);
	Route::get('/admin/products-import-export/{type}', 'Admin\ImportExportController@download_products_export');
	/* product import & export */
	
	
	/* orders */
	
	Route::get('/admin/orders', 'Admin\ProductController@view_orders');
	Route::get('/admin/order-details/{token}', 'Admin\ProductController@view_order_single');
	Route::get('/admin/more-info/{token}', 'Admin\ProductController@view_more_info');
	
	/* orders */
	
	
	/* rating */
	
	Route::get('/admin/rating', 'Admin\ProductController@view_rating');
	Route::get('/admin/rating/{rating_id}', 'Admin\ProductController@rating_delete');
	/* rating */
	
	
	/* refund request */
	
	Route::get('/admin/refund', 'Admin\ProductController@view_refund');
	Route::get('/admin/refund/{ord_id}/{refund_id}/{user_type}', 'Admin\ProductController@view_payment_refund');
	/* refund request */
	
	
	/* package includes */
	
	Route::get('/admin/package-includes', 'Admin\ProductController@view_package_includes');
	Route::get('/admin/add-package-includes', 'Admin\ProductController@add_package_includes')->name('admin.add-package-includes');
	Route::post('/admin/add-package-includes', 'Admin\ProductController@save_package_includes');
	Route::get('/admin/package-includes/{package_id}', 'Admin\ProductController@delete_package_includes');
	Route::get('/admin/edit-package-includes/{package_id}', 'Admin\ProductController@edit_package_includes')->name('admin.edit-package-includes');
	Route::post('/admin/edit-package-includes', ['as' => 'admin.edit-package-includes','uses'=>'Admin\ProductController@update_package_includes']);
	
	/* package includes */
	
	
	
	/* Compatible Browsers */
	
	Route::get('/admin/compatible-browsers', 'Admin\ProductController@view_compatible_browsers');
	Route::get('/admin/add-compatible-browsers', 'Admin\ProductController@add_compatible_browsers')->name('admin.add-compatible-browsers');
	Route::post('/admin/add-compatible-browsers', 'Admin\ProductController@save_compatible_browsers');
	Route::get('/admin/compatible-browsers/{browser_id}', 'Admin\ProductController@delete_compatible_browsers');
	Route::get('/admin/edit-compatible-browsers/{browser_id}', 'Admin\ProductController@edit_compatible_browsers')->name('admin.edit-compatible-browsers');
	Route::post('/admin/edit-compatible-browsers', ['as' => 'admin.edit-compatible-browsers','uses'=>'Admin\ProductController@update_compatible_browsers']);
	
	/* Compatible Browsers */
	
	
	/* blog */
	
	Route::get('/admin/blog-category', 'Admin\BlogController@blog_category');
	Route::get('/admin/add-blog-category', 'Admin\BlogController@add_blog_category')->name('admin.add-blog-category');
	Route::post('/admin/add-blog-category', 'Admin\BlogController@save_blog_category');
	Route::get('/admin/blog-category/{blog_cat_id}', 'Admin\BlogController@delete_blog_category');
	Route::get('/admin/edit-blog-category/{blog_cat_id}', 'Admin\BlogController@edit_blog_category')->name('admin.edit-blog-category');
	Route::post('/admin/edit-blog-category', ['as' => 'admin.edit-blog-category','uses'=>'Admin\BlogController@update_blog_category']);
	
	/* blog */
	
	
	
	/* post */
	
	Route::get('/admin/post', 'Admin\BlogController@posts');
	Route::get('/admin/add-post', 'Admin\BlogController@add_post')->name('admin.add-post');
	Route::post('/admin/add-post', 'Admin\BlogController@save_post');
	Route::get('/admin/post/{post_id}', 'Admin\BlogController@delete_post');
	Route::get('/admin/edit-post/{post_id}', 'Admin\BlogController@edit_post')->name('admin.edit-post');
	Route::post('/admin/edit-post', ['as' => 'admin.edit-post','uses'=>'Admin\BlogController@update_post']);
	
	/* post */
	
	
	/* comment */
	Route::get('/admin/comment/{post_id}', 'Admin\BlogController@comments');
	Route::get('/admin/comment/{delete}/{comment_id}', 'Admin\BlogController@delete_comment');
	Route::get('/admin/comment/update-status/{status}/{comment_id}', 'Admin\BlogController@comment_status');
	/* comment */
	
	
	
	
	/* pages */
	
	Route::get('/admin/pages', 'Admin\PagesController@pages');
	Route::get('/admin/add-page', 'Admin\PagesController@add_page')->name('admin.add-page');
	Route::post('/admin/add-page', 'Admin\PagesController@save_page');
	Route::get('/admin/pages/{page_id}', 'Admin\PagesController@delete_pages');
	Route::get('/admin/edit-page/{page_id}', 'Admin\PagesController@edit_page')->name('admin.edit-page');
	Route::post('/admin/edit-page', ['as' => 'admin.edit-page','uses'=>'Admin\PagesController@update_page']);
	
	/* pages */
	
	
	/* withdrawal */
	
	Route::get('/admin/withdrawal', 'Admin\ProductController@view_withdrawal');
	Route::get('/admin/withdrawal/{wd_id}/{wd_user_id}', 'Admin\ProductController@view_withdrawal_update');
	/* withdrawal */
	
			
	/* contact */
	Route::get('/admin/contact', 'Admin\CommonController@view_contact');
	Route::get('/admin/contact/{id}', 'Admin\CommonController@view_contact_delete');
	Route::get('/admin/add-contact', 'Admin\CommonController@view_add_contact');
	Route::post('/admin/add-contact', ['as' => 'admin.add-contact','uses'=>'Admin\CommonController@update_contact']);
	/* contact */
	
	
	/* newsletter */
	Route::get('/admin/newsletter', 'Admin\CommonController@view_newsletter');
	Route::get('/admin/newsletter/{id}', 'Admin\CommonController@view_newsletter_delete');
	Route::get('/admin/send-updates', 'Admin\CommonController@view_send_updates');
	Route::post('/admin/send-updates', ['as' => 'admin.send-updates','uses'=>'Admin\CommonController@send_updates']);
	/* newsletter */
	
	
	
});


/* admin panel */