<?php


/* admin panel */


Route::group(['middleware' => ['is_admin', 'HtmlMinifier', 'cache', 'XSS']], function () {
    
	Route::get('/admin', 'Admin\AdminController@admin')->middleware('cacheable:5');
	
	
	/* administrator */
	Route::get('/admin/administrator', 'Admin\MembersController@administrator');
	Route::get('/admin/add-administrator', 'Admin\MembersController@add_administrator')->name('admin.add-administrator');
	Route::post('/admin/add-administrator', 'Admin\MembersController@save_administrator');
	Route::get('/admin/administrator/{token}', 'Admin\MembersController@delete_administrator');
	Route::get('/admin/edit-administrator/{token}', 'Admin\MembersController@edit_administrator')->name('admin.edit-administrator');
	Route::post('/admin/edit-administrator', ['as' => 'admin.edit-administrator','uses'=>'Admin\MembersController@update_administrator']);
	Route::post('/admin/administrator', ['as' => 'admin.administrator','uses'=>'Admin\MembersController@search_administrators']);
	Route::post('/admin/delete-administrator', ['as' => 'admin.delete-administrator','uses'=>'Admin\MembersController@all_delete_administrator']);
	/* administrator */
	
	/* customer */
	Route::get('/admin/customer', 'Admin\MembersController@customer');
	Route::get('/admin/add-customer', 'Admin\MembersController@add_customer')->name('admin.add-customer');
	Route::post('/admin/add-customer', 'Admin\MembersController@save_customer');
	Route::get('/admin/customer/{token}', 'Admin\MembersController@delete_customer');
	Route::get('/admin/edit-customer/{token}', 'Admin\MembersController@edit_customer')->name('admin.edit-customer');
	Route::post('/admin/edit-customer', ['as' => 'admin.edit-customer','uses'=>'Admin\MembersController@update_customer']);
	Route::get('/admin/customer/{token}/{subcr_id}', 'Admin\MembersController@upgrade_customer');
	Route::post('/admin/customer', ['as' => 'admin.customer','uses'=>'Admin\MembersController@search_customers']);
	Route::post('/admin/delete-customer', ['as' => 'admin.delete-customer','uses'=>'Admin\MembersController@all_delete_customer']);
	/* customer */
	
	
	/* vendor */
	Route::get('/admin/vendor', 'Admin\MembersController@vendor');
	Route::get('/admin/add-vendor', 'Admin\MembersController@add_vendor')->name('admin.add-vendor');
	Route::post('/admin/add-vendor', 'Admin\MembersController@save_customer');
	Route::get('/admin/vendor/{token}', 'Admin\MembersController@delete_customer');
	Route::get('/admin/edit-vendor/{token}', 'Admin\MembersController@edit_vendor')->name('admin.edit_vendor');
	Route::post('/admin/edit-vendor', ['as' => 'admin.edit-vendor','uses'=>'Admin\MembersController@update_customer']);
	Route::get('/admin/subscription-payment-details/{token}', 'Admin\MembersController@subscription_payment_details');
	Route::post('/admin/vendor', ['as' => 'admin.vendor','uses'=>'Admin\MembersController@search_vendors']);
	/* vendor */
	
	/* media settings */
	
	Route::get('/admin/media-settings', 'Admin\SettingsController@media_settings');
	Route::post('/admin/media-settings', ['as' => 'admin.media-settings','uses'=>'Admin\SettingsController@update_media_settings']);
		
	/* media settings */
	Route::get('/admin/item-features', 'Admin\SettingsController@item_features');
	Route::post('/admin/item-features', ['as' => 'admin.item-features','uses'=>'Admin\SettingsController@update_item_features']);
	/* item features */
	
	/* item features */
	
	/* limitation settings */
	Route::get('/admin/limitation-settings', 'Admin\SettingsController@limitation_settings');
	Route::post('/admin/limitation-settings', ['as' => 'admin.limitation-settings','uses'=>'Admin\SettingsController@update_limitation_settings']);
	/* limitation settings */
	
	/* color settings */
	
	Route::get('/admin/font-color-settings', 'Admin\SettingsController@font_color_settings');
	Route::post('/admin/font-color-settings', ['as' => 'admin.font-color-settings','uses'=>'Admin\SettingsController@update_font_color_settings']);
	
	/* color settings */
	
	/* currency settings */
	Route::get('/admin/currency-settings', 'Admin\SettingsController@currency_settings');
	Route::post('/admin/currency-settings', ['as' => 'admin.currency-settings','uses'=>'Admin\SettingsController@update_currency_settings']);
	/* currency settings */
	
	
	/* edit profile */
	
	Route::get('/admin/edit-profile', 'Admin\MembersController@edit_profile');
	Route::post('/admin/edit-profile', ['as' => 'admin.edit-profile','uses'=>'Admin\MembersController@update_profile']);
	/* edit profile */
	
	
	/* settings */
	
	Route::get('/admin/general-settings', 'Admin\SettingsController@general_settings');
	Route::post('/admin/general-settings', ['as' => 'admin.general-settings','uses'=>'Admin\SettingsController@update_general_settings']);
	
	
	Route::get('/admin/license', 'Admin\AdminController@license_check')->middleware('cacheable:5');
	Route::post('/admin/license', ['as' => 'admin.license','uses'=>'Admin\AdminController@verify_purchase']);
	Route::get('/admin/email-settings', 'Admin\SettingsController@email_settings');
	Route::post('/admin/email-settings', ['as' => 'admin.email-settings','uses'=>'Admin\SettingsController@update_email_settings']);
	Route::get('/admin/country-settings', 'Admin\SettingsController@country_settings');
	Route::get('/admin/add-country', 'Admin\SettingsController@add_country')->name('admin.add-country');
	Route::post('/admin/add-country', 'Admin\SettingsController@save_country');
	Route::get('/admin/country-settings/{cid}', 'Admin\SettingsController@delete_country');
	Route::post('/admin/vat', ['as' => 'admin.vat','uses'=>'Admin\SettingsController@vat_update']);
	Route::get('/admin/edit-country/{cid}', 'Admin\SettingsController@edit_country')->name('admin.edit-country');
	Route::post('/admin/edit-country', ['as' => 'admin.edit-country','uses'=>'Admin\SettingsController@update_country']);
	Route::get('/admin/payment-settings', 'Admin\SettingsController@payment_settings');
	Route::post('/admin/payment-settings', ['as' => 'admin.payment-settings','uses'=>'Admin\SettingsController@update_payment_settings']);
	Route::get('/admin/social-settings', 'Admin\SettingsController@social_settings');
	Route::post('/admin/social-settings', ['as' => 'admin.social-settings','uses'=>'Admin\SettingsController@update_social_settings']);
	Route::get('/admin/highlights', 'Admin\SettingsController@highlights');
	Route::post('/admin/highlights', ['as' => 'admin.highlights','uses'=>'Admin\SettingsController@update_highlights']);
	
	Route::get('/admin/badges-settings', 'Admin\SettingsController@badges_settings');
	Route::post('/admin/badges-settings', ['as' => 'admin.badges-settings','uses'=>'Admin\SettingsController@update_badges_settings']);
	
	Route::get('/admin/start-selling', 'Admin\SettingsController@start_selling');
	Route::post('/admin/start-selling', ['as' => 'admin.start-selling','uses'=>'Admin\SettingsController@update_start_selling']);
	
	Route::get('/admin/pwa-settings', 'Admin\SettingsController@pwa_settings');
	Route::post('/admin/pwa-settings', ['as' => 'admin.pwa-settings','uses'=>'Admin\SettingsController@update_pwa_settings']);
	/* settings */
	
	/* demo mode */
	Route::post('/admin/demo-mode', ['as' => 'admin.demo-mode','uses'=>'Admin\SettingsController@update_demo_mode']);
	Route::get('/admin/demo-mode', 'Admin\SettingsController@demo_mode');
	/* demo mode */
	
	
	/* subscription */
	
	Route::get('/admin/subscription', 'Admin\SubscriptionController@subscription');
	Route::get('/admin/add-subscription', 'Admin\SubscriptionController@add_subscription')->name('admin.add-subscription');
	Route::post('/admin/add-subscription', 'Admin\SubscriptionController@save_subscription');
	Route::get('/admin/subscription/{subscr_id}', 'Admin\SubscriptionController@delete_subscription');
	Route::get('/admin/edit-subscription/{subscr_id}', 'Admin\SubscriptionController@edit_subscription')->name('admin.edit-subscription');
	Route::post('/admin/edit-subscription', ['as' => 'admin.edit-subscription','uses'=>'Admin\SubscriptionController@update_subscription']);
	Route::post('/admin/free-subscription', ['as' => 'admin.free-subscription','uses'=>'Admin\SubscriptionController@save_free_subscription']);
	Route::post('/admin/upsubscription', ['as' => 'admin.upsubscription','uses'=>'Admin\SubscriptionController@subscription_content']);
	Route::post('/admin/subscription', ['as' => 'admin.subscription','uses'=>'Admin\SubscriptionController@all_delete_subscription']);
	/* subscription */
	
	
	/* pages */
	
	Route::get('/admin/pages', 'Admin\PagesController@pages');
	Route::get('/admin/add-page', 'Admin\PagesController@add_page')->name('admin.add-page');
	Route::post('/admin/add-page', 'Admin\PagesController@save_page');
	Route::get('/admin/pages/{page_id}', 'Admin\PagesController@delete_pages');
	Route::get('/admin/edit-page/{page_id}', 'Admin\PagesController@edit_page')->name('admin.edit-page');
	Route::post('/admin/edit-page', ['as' => 'admin.edit-page','uses'=>'Admin\PagesController@update_page']);
	Route::post('/admin/pages', ['as' => 'admin.pages','uses'=>'Admin\PagesController@all_delete_pages']); 
	/* pages */
	
	/* notification */
	Route::get('/admin/notifications', 'Admin\ItemController@view_notify_details');
	Route::get('/admin/notifications-delete/{noti_id}', 'Admin\ItemController@remove_notify_details');
	Route::get('/admin/notifications/{token}', 'Admin\ItemController@update_notify_data');
	/* notification */
	
	/* deposit */
	Route::get('/admin/deposit', 'Admin\CommonController@view_deposit');
	Route::get('/admin/add-deposit', 'Admin\CommonController@add_deposit')->name('admin.add-deposit');
	Route::post('/admin/add-deposit', 'Admin\CommonController@save_deposit');
	Route::get('/admin/deposit/{deposit_id}', 'Admin\CommonController@delete_deposit');
	Route::get('/admin/edit-deposit/{deposit_id}', 'Admin\CommonController@edit_deposit')->name('admin.edit-deposit');
	Route::post('/admin/edit-deposit', ['as' => 'admin.update-deposit','uses'=>'Admin\CommonController@update_deposit']);
	
	Route::get('/admin/deposit-details', 'Admin\CommonController@deposit_details');
	Route::get('/admin/deposit-details/{deposit_id}', 'Admin\CommonController@delete_deposit_details');
	Route::get('/admin/deposit-payment/{ord_id}', 'Admin\ItemController@complete_payment');
	Route::post('/admin/deposit', ['as' => 'admin.deposit','uses'=>'Admin\CommonController@all_delete_deposit']);
	Route::post('/admin/deposit-details', ['as' => 'admin.deposit-details','uses'=>'Admin\CommonController@all_delete_deposit_details']);  
	/* deposit */
	
		
	/* category */
	
	Route::get('/admin/category', 'Admin\CategoryController@category');
	Route::get('/admin/add-category', 'Admin\CategoryController@add_category')->name('admin.add-category');
	Route::post('/admin/add-category', 'Admin\CategoryController@save_category');
	Route::get('/admin/category/{cat_id}', 'Admin\CategoryController@delete_category');
	Route::get('/admin/edit-category/{cat_id}', 'Admin\CategoryController@edit_category')->name('admin.edit-category');
	Route::post('/admin/edit-category', ['as' => 'admin.edit-category','uses'=>'Admin\CategoryController@update_category']);
	Route::post('/admin/category', ['as' => 'admin.category','uses'=>'Admin\CategoryController@all_delete_category']);
	/* category */
	
	
	/* subcategory */
	
	Route::get('/admin/sub-category', 'Admin\CategoryController@subcategory');
	Route::get('/admin/add-subcategory', 'Admin\CategoryController@add_subcategory')->name('admin.add-subcategory');
	Route::post('/admin/add-subcategory', 'Admin\CategoryController@save_subcategory');
	Route::get('/admin/sub-category/{subcat_id}', 'Admin\CategoryController@delete_subcategory');
	Route::get('/admin/edit-subcategory/{cat_id}', 'Admin\CategoryController@edit_subcategory')->name('admin.edit-subcategory');
	Route::post('/admin/edit-subcategory', ['as' => 'admin.edit-subcategory','uses'=>'Admin\CategoryController@update_subcategory']);
	Route::post('/admin/sub-category', ['as' => 'admin.sub-category','uses'=>'Admin\CategoryController@all_delete_subcategory']);
	/* subcategory */
	
	
	
	/* blog */
	
	Route::get('/admin/blog-category', 'Admin\BlogController@blog_category');
	Route::get('/admin/add-blog-category', 'Admin\BlogController@add_blog_category')->name('admin.add-blog-category');
	Route::post('/admin/add-blog-category', 'Admin\BlogController@save_blog_category');
	Route::get('/admin/blog-category/{blog_cat_id}', 'Admin\BlogController@delete_blog_category');
	Route::get('/admin/edit-blog-category/{blog_cat_id}', 'Admin\BlogController@edit_blog_category')->name('admin.edit-blog-category');
	Route::post('/admin/edit-blog-category', ['as' => 'admin.edit-blog-category','uses'=>'Admin\BlogController@update_blog_category']);
	Route::post('/admin/blog-category', ['as' => 'admin.blog-category','uses'=>'Admin\BlogController@all_delete_blog_category']); 
	/* blog */
	
	
	
	/* post */
	
	Route::get('/admin/post', 'Admin\BlogController@posts');
	Route::get('/admin/add-post', 'Admin\BlogController@add_post')->name('admin.add-post');
	Route::post('/admin/add-post', 'Admin\BlogController@save_post');
	Route::get('/admin/post/{post_id}', 'Admin\BlogController@delete_post');
	Route::get('/admin/edit-post/{post_id}', 'Admin\BlogController@edit_post')->name('admin.edit-post');
	Route::post('/admin/edit-post', ['as' => 'admin.edit-post','uses'=>'Admin\BlogController@update_post']);
	Route::post('/admin/post', ['as' => 'admin.post','uses'=>'Admin\BlogController@all_delete_post']); 
	
	/* post */
	
	/* comment */
	Route::get('/admin/comment/{post_id}', 'Admin\BlogController@comments');
	Route::get('/admin/comment/{delete}/{comment_id}', 'Admin\BlogController@delete_comment');
	Route::get('/admin/comment/update-status/{status}/{comment_id}', 'Admin\BlogController@comment_status');
	/* comment */
	
	/* items */
	Route::post('/admin/upload', 'Admin\CommonController@upload');
	
	Route::get('/admin/items', 'Admin\ItemController@view_items')->middleware('cacheable:5');
	Route::post('/admin/items', ['as' => 'admin.items','uses'=>'Admin\ItemController@search_items']);
	Route::post('/admin/trashs', ['as' => 'admin.trashs','uses'=>'Admin\ItemController@trash_items']);
	Route::get('/admin/items-data', 'Admin\ItemController@getItems')->name('admin.items-data');
	Route::get('/admin/upload-item/{itemtype}', 'Admin\ItemController@upload_item');
	Route::post('/admin/upload-item', ['as' => 'admin.upload-item','uses'=>'Admin\ItemController@save_items']);
	Route::get('/admin/edit-item/{token}', 'Admin\ItemController@edit_item')->middleware('cacheable:5');
    Route::get('/admin/edit-item/{dropimg}/{token}', 'Admin\ItemController@drop_image_item');
    Route::post('/admin/edit-item', ['as' => 'admin.edit-item','uses'=>'Admin\ItemController@update_items']);
	Route::get('/admin/items/{token}', 'Admin\ItemController@delete_item_request');
	Route::get('/admin/items/{featured}/{token}', 'Admin\ItemController@featured_item_request');
	Route::post('/admin/fileupload', ['as' => 'admin.fileupload','uses'=>'Admin\ItemController@fileupload']);
	Route::post('/admin/file-delete', ['as' => 'admin.file-delete','uses'=>'Admin\ItemController@filedestroy']);
	Route::get('/admin/download/{token}', 'Admin\ItemController@file_download');
	
	Route::get('/admin/trash-items', 'Admin\ItemController@view_trash_items')->middleware('cacheable:5');
	Route::get('/admin/restore-items/{token}', 'Admin\ItemController@view_restore_items');
	Route::get('/admin/delete-items/{token}', 'Admin\ItemController@permanent_delete_items');
	Route::post('/admin/trash-items', ['as' => 'admin.trash-items','uses'=>'Admin\ItemController@search_trash_items']);
	Route::post('/admin/all-trash', ['as' => 'admin.all-trash','uses'=>'Admin\ItemController@all_delete_complete']);
	Route::get('/admin/item-comment/{token}', 'Admin\ItemController@item_comments');
	Route::get('/admin/comment-delete/{comm_id}', 'Admin\ItemController@delete_comment');
	/* items */
	
	/* product import & export */
	Route::get('/admin/products-import-export', 'Admin\ImportExportController@view_products_import_export');
	Route::post('/admin/products-import-export', ['as' => 'admin.products-import-export','uses'=>'Admin\ImportExportController@products_import']);
	Route::get('/admin/products-import-export/{type}', 'Admin\ImportExportController@download_products_export');
	/* product import & export */
	
	/* orders */
	
	Route::get('/admin/orders', 'Admin\ItemController@view_orders')->middleware('cacheable:5');
	Route::post('/admin/orders', ['as' => 'admin.orders','uses'=>'Admin\ItemController@search_orders']);
	Route::get('/admin/order-details/{token}', 'Admin\ItemController@view_order_single');
	Route::get('/admin/order-details/{ord_id}/{user_type}', 'Admin\ItemController@view_payment_approval');
	Route::get('/admin/more-info/{token}', 'Admin\ItemController@view_more_info');
	Route::get('/admin/orders/{ord_id}', 'Admin\ItemController@complete_orders');
	Route::get('/admin/orders/{delete}/{ord_id}', 'Admin\ItemController@delete_orders');
	Route::post('/admin/orders-delete', ['as' => 'admin.orders-delete','uses'=>'Admin\ItemController@all_delete_orders']);
	/* orders */
	
	
	/* promotion */
	Route::get('/admin/promotion', 'Admin\SettingsController@promotion_settings');
	Route::post('/admin/promotion', ['as' => 'admin.promotion','uses'=>'Admin\SettingsController@update_promotion_settings']);
	/* promotion */
	
	/* refund request */
	
	Route::get('/admin/refund', 'Admin\ItemController@view_refund')->middleware('cacheable:5');
	Route::get('/admin/refund/{ord_id}/{refund_id}/{user_type}', 'Admin\ItemController@view_payment_refund');
	Route::get('/admin/refund/{refund_id}', 'Admin\ItemController@delete_refund');
	Route::post('/admin/refund', ['as' => 'admin.refund','uses'=>'Admin\ItemController@all_delete_refund']);
	/* refund request */
	
	
	/* rating */
	
	Route::get('/admin/rating', 'Admin\ItemController@view_rating')->middleware('cacheable:5');
	Route::get('/admin/rating/{rating_id}', 'Admin\ItemController@rating_delete');
	Route::get('/admin/edit-rating/{rating_id}', 'Admin\ItemController@edit_rating');
	Route::post('/admin/edit-rating', ['as' => 'admin.edit-rating','uses'=>'Admin\ItemController@update_rating']);
	Route::post('/admin/rating', ['as' => 'admin.rating','uses'=>'Admin\ItemController@all_delete_rating']); 
	/* rating */
	
	
	/* item type */
	Route::get('/admin/item-type', 'Admin\ItemController@view_item_type');
	Route::get('/admin/item-type/{id}/{status}', 'Admin\ItemController@view_item_type_status');
	Route::get('/admin/add-item-type', 'Admin\ItemController@view_add_item_type');
	Route::post('/admin/add-item-type', ['as' => 'admin.add-item-type','uses'=>'Admin\ItemController@save_add_item_type']);
	Route::get('/admin/item-type/{id}', 'Admin\ItemController@view_item_type_delete');
	Route::get('/admin/edit-item-type/{id}', 'Admin\ItemController@edit_item_type');
	Route::post('/admin/edit-item-type', ['as' => 'admin.edit-item-type','uses'=>'Admin\ItemController@update_edit_item_type']);
	Route::post('/admin/item-type', ['as' => 'admin.item-type','uses'=>'Admin\ItemController@all_delete_item_type']);
	/* item type */
	
	/* preferred settings */
	Route::get('/admin/preferred-settings', 'Admin\SettingsController@preferred_settings');
	Route::post('/admin/preferred-settings', ['as' => 'admin.preferred-settings','uses'=>'Admin\SettingsController@update_preferred_settings']);
	/* preferred settings */
	
	/* reports */
	Route::get('/admin/reports', 'Admin\ReportsController@view_reports');
	Route::post('/admin/reports', ['as' => 'admin.reports','uses'=>'Admin\ReportsController@search_report']);
	/* reports */
	
	/* attributes */
	/*Route::get('/admin/attributes/{type_id}', 'Admin\ItemController@view_attribute');
	Route::post('/admin/attributes', ['as' => 'admin.attributes','uses'=>'Admin\ItemController@update_attribute']);*/
	
	/* attributes */
	
	/* attributes */
  Route::get('/admin/attributes', 'Admin\AttributeController@attribute')->middleware('cacheable:5');
  Route::get('/admin/add-attribute', 'Admin\AttributeController@add_attribute')->name('admin.add-attribute');
  Route::post('/admin/add-attribute', 'Admin\AttributeController@save_attribute');
  Route::get('/admin/attributes/{attr_id}', 'Admin\AttributeController@delete_attribute');
  Route::get('/admin/edit-attribute/{attr_id}', 'Admin\AttributeController@edit_attribute')->name('admin.edit-attribute');
  Route::post('/admin/edit-attribute', ['as' => 'admin.edit-attribute','uses'=>'Admin\AttributeController@update_attribute']);
  Route::post('/admin/attributes', ['as' => 'admin.attributes','uses'=>'Admin\AttributeController@all_delete_attribute']);
  /* attributes */
	
	
	/* withdrawal */
	
	Route::get('/admin/withdrawal', 'Admin\ItemController@view_withdrawal')->middleware('cacheable:5');
	Route::get('/admin/withdrawal/{wd_id}/{wd_user_id}', 'Admin\ItemController@view_withdrawal_update');
	Route::get('/admin/withdrawal/{wd_id}', 'Admin\ItemController@delete_withdrawal');
	Route::get('/admin/withdrawal-methods', 'Admin\ItemController@withdrawal_methods');
	Route::get('/admin/add-withdrawal-methods', 'Admin\ItemController@add_withdrawal_methods');
	Route::post('/admin/add-withdrawal-methods', ['as' => 'admin.add-withdrawal-methods','uses'=>'Admin\ItemController@save_withdrawal_methods']);
	Route::get('/admin/edit-withdrawal-methods/{wm_id}', 'Admin\ItemController@edit_withdrawal_methods');
	Route::post('/admin/edit-withdrawal-methods', ['as' => 'admin.edit-withdrawal-methods','uses'=>'Admin\ItemController@update_withdrawal_methods']);
	Route::post('/admin/withdrawal', ['as' => 'admin.withdrawal','uses'=>'Admin\ItemController@all_delete_withdrawal']); 
	/* withdrawal */
	
	
	/* currencies */
	Route::get('/admin/currencies', 'Admin\CurrencyController@view_currencies');
	Route::get('/admin/add-currency', 'Admin\CurrencyController@add_currency')->name('admin.add-currency');
	Route::post('/admin/add-currency', 'Admin\CurrencyController@save_currency');
	Route::get('/admin/currencies/{token}', 'Admin\CurrencyController@delete_currency');
	Route::get('/admin/edit-currency/{token}', 'Admin\CurrencyController@edit_currency')->name('admin.edit-currency');
	Route::post('/admin/edit-currency', ['as' => 'admin.edit-currency','uses'=>'Admin\CurrencyController@update_currency']);
	Route::post('/admin/currencies', ['as' => 'admin.currencies','uses'=>'Admin\CurrencyController@all_delete_currencies']);
	/* currencies */
	
	/* contact */
	Route::get('/admin/contact', 'Admin\CommonController@view_contact')->middleware('cacheable:5');
	Route::get('/admin/contact/{id}', 'Admin\CommonController@view_contact_delete');
	Route::get('/admin/add-contact', 'Admin\CommonController@view_add_contact');
	Route::post('/admin/add-contact', ['as' => 'admin.add-contact','uses'=>'Admin\CommonController@update_contact']);
	Route::post('/admin/contact', ['as' => 'admin.contact','uses'=>'Admin\CommonController@all_delete_contact']);
	/* contact */
	
	/* newsletter */
	Route::get('/admin/newsletter', 'Admin\CommonController@view_newsletter')->middleware('cacheable:5');
	Route::get('/admin/newsletter/{id}', 'Admin\CommonController@view_newsletter_delete');
	Route::get('/admin/send-updates', 'Admin\CommonController@view_send_updates');
	Route::post('/admin/send-updates', ['as' => 'admin.send-updates','uses'=>'Admin\CommonController@send_updates']);
	Route::post('/admin/newsletter', ['as' => 'admin.newsletter','uses'=>'Admin\CommonController@all_delete_newsletter']);
	/* newsletter */
	
	/* message */
	Route::get('/admin/conversation/{slug}', 'Admin\ChatController@view_message_users');
	Route::get('/admin/messages/{slug}/{logid}', 'Admin\ChatController@view_message_conversation');
	Route::get('/admin/messages/{id}', 'Admin\ChatController@view_message_delete');
	/* message */
	
	/* clear cache */
	Route::get('/admin/clear-cache', 'Admin\CommonController@delete_cache')->middleware('cacheable:5');
	
	/* clear cache */
	
	/* upgrade */
	Route::get('/admin/upgrade', 'Admin\CommonController@view_upgrade');
	Route::post('/admin/upgrade', ['as' => 'admin.upgrade','uses'=>'Admin\CommonController@upgrade_version']);
	/* upgrade */
	
	/* coupon */
	Route::get('/admin/coupons', 'Admin\CouponController@view_coupon')->middleware('cacheable:5');
	Route::get('/admin/add-coupon', 'Admin\CouponController@add_coupon')->name('admin.add-coupon');
	Route::post('/admin/add-coupon', 'Admin\CouponController@save_coupon');
	Route::get('/admin/coupons/{coupon_id}', 'Admin\CouponController@delete_coupon');
	Route::get('/admin/edit-coupon/{coupon_id}', 'Admin\CouponController@edit_coupon')->name('admin.edit-coupon');
	Route::post('/admin/edit-coupon', ['as' => 'admin.edit-coupon','uses'=>'Admin\CouponController@update_coupon']);
	Route::post('/admin/coupons', ['as' => 'admin.coupons','uses'=>'Admin\CouponController@all_delete_coupons']);
	/* coupon */
	
	/* email template */
	Route::get('/admin/email-template', 'Admin\EmailController@email_template')->middleware('cacheable:5');
	Route::get('/admin/add-email-template', 'Admin\EmailController@add_email_template')->name('admin.add-email-template');
	Route::post('/admin/add-email-template', 'Admin\EmailController@save_email_template');
	Route::get('/admin/edit-email-template/{et_id}', 'Admin\EmailController@edit_email_template')->name('admin.edit-email-template');
	Route::post('/admin/edit-email-template', ['as' => 'admin.edit-email-template','uses'=>'Admin\EmailController@update_email_template']);
	/* email template */
	
	/* other market */
	Route::get('/admin/other-market', 'Admin\AdminController@view_market');
	Route::get('/admin/add-other-market', 'Admin\AdminController@add_other_market')->name('admin.add-other-market');
	Route::post('/admin/add-other-market', 'Admin\AdminController@save_other_market');
	Route::get('/admin/other-market/{im_id}', 'Admin\AdminController@delete_market');
	Route::get('/admin/edit-other-market/{im_id}', 'Admin\AdminController@edit_other_market')->name('admin.edit-other-market');
	Route::post('/admin/edit-other-market', ['as' => 'admin.edit-other-market','uses'=>'Admin\AdminController@update_other_market']);
	/* other market */
	
	/* ads */
	Route::get('/admin/ads', 'Admin\SettingsController@view_ads')->middleware('cacheable:5');
	Route::post('/admin/ads', ['as' => 'admin.ads','uses'=>'Admin\SettingsController@update_ads']);
	/* ads */
	
	Route::get('/admin/backup', 'Admin\BackupController@index');
	Route::get('/admin/backup/create', 'Admin\BackupController@create');
	Route::get('/admin/backup/download/{file_name}', 'Admin\BackupController@download');
	Route::get('/admin/backup/delete/{file_name}', 'Admin\BackupController@delete');
	Route::post('/admin/backup', ['as' => 'admin.backup','uses'=>'Admin\BackupController@backup']);




});


/* admin panel */

Route::group(['middleware' => ['HtmlMinifier', 'cache', 'XSS']], function () {

Route::get('/language/{locale}', function ($locale) {
    app()->setLocale($locale);
    session()->put('locale', $locale);

    return redirect()->back();
});


Route::get('/', 'CommonController@view_index')->middleware('cacheable:5');
Route::get('/index', 'CommonController@view_index')->middleware('cacheable:5');
Route::post('/index', ['as' => 'index','uses'=>'CommonController@update_video'])->middleware('cacheable:5');
/* language */

Route::get('/translate/{translate}', 'CommonController@cookie_translate');

/* language */

/* currency */
Route::get('/currency/{multicurrency}', 'CommonController@cookie_currency');
/* currency */

Auth::routes();


Route::get('/logout', 'Admin\CommonController@logout');

Route::get('login/{provider}', 'Auth\LoginController@redirectToProvider');
Route::get('login/{provider}/callback', 'Auth\LoginController@handleProviderCallback');
Route::get('searchajax',array('as'=>'searchajax','uses'=>'CommonController@autoComplete'));
/* profile settings */

Route::get('/profile-settings', 'ProfileController@view_profile_settings');
Route::post('/profile-settings', ['as' => 'profile-settings','uses'=>'ProfileController@update_profile']);

/* profile settings */


/* sitemap */
Route::get('/sitemap.xml', 'SitemapController@index');
Route::get('/sitemap.xml/items', 'SitemapController@items');
Route::get('/sitemap.xml/category', 'SitemapController@category');
Route::get('/sitemap.xml/subcategory', 'SitemapController@subcategory');
Route::get('/sitemap.xml/pages', 'SitemapController@pages');
Route::get('/sitemap.xml/blog', 'SitemapController@blog');
Route::get('/sitemap.xml/users', 'SitemapController@users');
/* sitemap */


/* item pages */
Route::get('/featured-items', 'CommonController@view_featured_items')->middleware('cacheable:5');
Route::get('/free-items', 'CommonController@view_free_items')->middleware('cacheable:5');
Route::get('/new-releases', 'CommonController@view_new_items')->middleware('cacheable:5');
Route::get('/popular-items', 'CommonController@view_popular_items')->middleware('cacheable:5');
Route::get('/tag/{item}/{slug}', 'CommonController@view_tags')->middleware('cacheable:5');
Route::get('/subscriber-downloads', 'CommonController@view_subscriber_downloads')->middleware('cacheable:5');
/* item pages */



/* user */

Route::get('/user/{slug}', 'CommonController@view_user');
Route::get('/user-reviews/{slug}', 'CommonController@view_user_reviews');
Route::get('/user-followers/{slug}', 'CommonController@view_user_followers');
Route::get('/user-following/{slug}', 'CommonController@view_user_following');

Route::post('/user', ['as' => 'user','uses'=>'CommonController@send_message']);
Route::get('/user/{myid}/{followid}', 'CommonController@view_follow');
Route::get('/user/{unfollow}/{myid}/{followid}', 'CommonController@view_unfollow');
/* user */


/* top authors */
Route::get('/top-authors', 'CommonController@view_top_authors');
/* top authors */

Route::get('/start-selling', 'CommonController@view_start_selling');

/* email verification */

Route::get('/user-verify/{user_token}', 'CommonController@user_verify');

/* email verification */


/* item */
Route::post('/upload', 'CommonController@upload');

Route::post('/fileupload', ['as' => 'fileupload','uses'=>'ItemController@fileupload']);
Route::get('/upload-item/{itemtype}', 'ItemController@upload_item');
Route::post('/upload-item', ['as' => 'upload-item','uses'=>'ItemController@save_items']);

Route::get('/manage-item', 'ItemController@manage_item')->middleware('cacheable:5');
Route::get('/manage-item/{token}', 'ItemController@delete_item_request');
Route::get('/edit-item/{token}', 'ItemController@edit_item')->middleware('cacheable:5');
Route::get('/edit-item/{dropimg}/{token}', 'ItemController@drop_image_item');
Route::post('/edit-item', ['as' => 'edit-item','uses'=>'ItemController@update_items']);
Route::post('/file-delete', ['as' => 'file-delete','uses'=>'ItemController@filedestroy']);
Route::get('/item/{slug}', 'CommonController@view_single_item')->middleware('cacheable:5');
Route::get('/item/{id}/{favorite}/{liked}', 'ItemController@view_favorite_item');
Route::get('/item/{download}/{token}', 'CommonController@view_free_item');

Route::post('/post-comment', ['as' => 'post-comment','uses'=>'ItemController@add_post_comment']);
Route::post('/reply-post-comment', ['as' => 'reply-post-comment','uses'=>'ItemController@reply_post_comment']);

/* item */

/* subscription */

Route::get('/subscription', 'CommonController@view_subscription');
Route::get('/confirm-subscription/{id}', 'ProfileController@upgrade_subscription');
Route::post('/confirm-subscription', ['as' => 'confirm-subscription','uses'=>'ProfileController@update_subscription']);
Route::get('/subscription-success/{ord_token}', 'ProfileController@paypal_success');
Route::post('/subscription-paystack', ['as' => 'subscription-paystack','uses'=>'ProfileController@redirectToGateway']);
Route::get('/subscription-paystack', 'ProfileController@handleGatewayCallback');
Route::post('/subscription-razorpay', ['as' => 'subscription-razorpay','uses'=>'ProfileController@razorpay_payment']);

Route::get('/subscription-payfast/{ord_token}', 'ProfileController@payfast_success');
Route::get('/subscription-coinpayments/{ord_token}', 'ProfileController@coinpayments_success');
Route::get('/subscription-instamojo/{ord_token}', 'ProfileController@instamojo_success');
Route::post('/subscription-aamarpay/{ord_token}', 'ProfileController@aamarpay_success');
Route::get('/subscription-mercadopago/{ord_token}', 'ProfileController@mercadopago_success');
Route::get('/subscription-midtrans/{ord_token}', 'ProfileController@midtrans_success');
Route::get('/subscription-coinbase/{ord_token}', 'ProfileController@coinbase_success');
Route::post('/paytm/status', 'ProfileController@paymentCallback');
Route::get('/subscription-cashfree/', 'ProfileController@cashfree_success');
Route::get('/subscription-nowpayments/{ord_token}', 'ProfileController@nowpayments_success');
/* subscription */


/* preview */
Route::get('/preview/{slug}', 'CommonController@view_preview');
/* preview */


/* favourites */
Route::get('/favourites', 'ItemController@favourites_item')->middleware('cacheable:5');
Route::get('/favourites/{fav_id}/{item_id}', 'ItemController@remove_favourites_item');
/* favourites */

/* coinbase webhook */
Route::post('/webhooks/coinbase-checkout', 'CommonController@coinbase_checkout');
Route::post('/webhooks/coinbase-deposit', 'ItemController@coinbase_deposit');
Route::post('/webhooks/coinbase-subscription', 'ProfileController@coinbase_subscription');
/* coinbase webhook */


/* deposit */
Route::get('/deposit', 'ItemController@view_deposit')->middleware('cacheable:5');
Route::post('/deposit', ['as' => 'deposit','uses'=>'ItemController@show_deposit']);
Route::get('/deposit-success/{ord_token}', 'ItemController@deposit_success');

Route::post('/deposit-paystack', ['as' => 'deposit-paystack','uses'=>'ItemController@deposit_redirectToGateway']);
Route::get('/deposit-paystack', 'ItemController@deposit_handleGatewayCallback');
Route::post('/deposit-razorpay', ['as' => 'razorpay','uses'=>'ItemController@deposit_razorpay_payment']);
Route::get('/deposit-payhere-success/{ord_token}', 'ItemController@deposit_payhere_success');
Route::post ('/deposit_payu_success', 'ItemController@deposit_payu_success');
Route::post('/deposit-iyzico-success/{ord_token}', ['as' => 'deposit-iyzico-success','uses'=>'ItemController@deposit_iyzico_success']);
Route::get('/deposit-flutterwave', 'ItemController@deposit_flutterwaveCallback');
Route::get('/deposit-coingate', 'ItemController@deposit_coingate_success');
Route::get('/deposit-ipay', 'ItemController@deposit_ipay_success'); 
Route::get('/deposit-payfast-success/{ord_token}', 'ItemController@deposit_payfast_success');
Route::get('/deposit-coinpayments-success/{ord_token}', 'ItemController@deposit_coinpayments_success');
Route::post('/deposit-sslcommerz-success','ItemController@deposit_sslcommerz_successpage')->name('deposit.sslcommerz');
Route::get('/deposit-instamojo-success/{ord_token}', 'ItemController@deposit_instamojo_success');
Route::post('/deposit-aamarpay/{ord_token}', 'ItemController@deposit_aamarpay_success');
Route::get('/deposit-mollie', 'ItemController@deposit_mollie_success');
Route::get('/deposit-mercadopago/{ord_token}', 'ItemController@deposit_mercadopago_success');
Route::get('/deposit-midtrans/{ord_token}', 'ItemController@deposit_midtrans_success');
Route::get('/deposit-coinbase/{ord_token}', 'ItemController@deposit_coinbase_success');
Route::get('/deposit-cashfree/', 'ItemController@deposit_cashfree_success');
Route::get('/deposit-nowpayments/{ord_token}', 'ItemController@deposit_nowpayments_success');
/* deposit */


/* forgot */

Route::get('/forgot', 'CommonController@view_forgot');
Route::post('/forgot', ['as' => 'forgot','uses'=>'CommonController@update_forgot']);
Route::get('/reset/{user_token}', 'CommonController@view_reset');
Route::post('/reset', ['as' => 'reset','uses'=>'CommonController@update_reset']);
Route::get('/customer/{user_token}', 'CommonController@login_as_vendor');
/* forgot */

/* verify */
Route::get('/verify', 'CommonController@view_verify');
Route::post('/verify', ['as' => 'verify','uses'=>'CommonController@update_verify']);
/* verify */


/* shop */

Route::get('/shop', 'CommonController@view_all_items')->middleware('cacheable:5');
Route::post('/shop', ['as' => 'shop','uses'=>'CommonController@view_shop_items'])->middleware('cacheable:5');
Route::get('/shop/{type}/{slug}', 'CommonController@view_category_types')->middleware('cacheable:5');
Route::get('/upgrade-bank-details', 'CommonController@upgrade_bank_details');
/* shop */


/* pages */
Route::get('/404', 'CommonController@not_found');


/* pages */


/* flash sale */

Route::get('/flash-sale', 'CommonController@view_flash_items')->middleware('cacheable:5');
Route::get('/free-items', 'CommonController@view_free_items')->middleware('cacheable:5');
/* flash sale */


/* coupon */
Route::get('/coupon', 'CouponController@view_coupon')->middleware('cacheable:5');
Route::get('/add-coupon', 'CouponController@add_coupon')->name('add-coupon');
Route::post('/add-coupon', 'CouponController@save_coupon');
Route::get('/coupon/{coupon_id}', 'CouponController@delete_coupon');
Route::get('/edit-coupon/{coupon_id}', 'CouponController@edit_coupon')->name('edit-coupon');
Route::post('/edit-coupon', ['as' => 'edit-coupon','uses'=>'CouponController@update_coupon']);
/* coupon */


/* blog */

Route::get('/blog', 'BlogController@view_blog')->middleware('cacheable:5');
Route::get('/single/{slug}', 'BlogController@view_single')->middleware('cacheable:5');
Route::get('/blog/{category}/{id}/{slug}', 'BlogController@view_category_blog')->middleware('cacheable:5');
Route::post('/single', ['as' => 'single','uses'=>'BlogController@insert_comment'])->middleware('cacheable:5');
Route::get('/blog/{tag}', 'BlogController@view_tags');
/* blog */


/* shop */

/*Route::get('/shop', 'CommonController@view_all_items');
Route::get('/shop/{filter}', 'CommonController@view_filter_items');
Route::get('/shop/{item_type}/{slug}', 'CommonController@view_item_type');
Route::get('/shop/{type}/{id}/{slug}', 'CommonController@view_category_items');
Route::post('/shop', ['as' => 'shop','uses'=>'CommonController@view_shop_items']);
Route::get('/shop-list', 'CommonController@view_all_list_items');*/







/* comment */



/* comment */


/* contact support */

Route::post('/support', ['as' => 'support','uses'=>'ItemController@contact_support']);

/* contact support */


/* cart */
Route::get('/cart', 'CommonController@show_cart');
Route::get('/cart/{ord_id}', 'CommonController@remove_cart_item');
Route::get('/clear-cart', 'CommonController@remove_clear_item');
Route::get('/add-to-cart/{slug}', 'CommonController@add_to_cart');
Route::post('/cart', ['as' => 'cart','uses'=>'CommonController@view_cart']);
Route::post('/coupon', ['as' => 'coupon','uses'=>'CommonController@view_coupon']);
Route::get('/cart/{remove}/{coupon}', 'CommonController@remove_coupon');

/* cart */


/* checkout */
Route::get('/checkout', 'CommonController@show_checkout');
Route::post('/checkout', ['as' => 'checkout','uses'=>'CommonController@view_checkout']);
Route::post('/2checkout', ['as' => '2checkout','uses'=>'ItemController@confirm_2checkout']);
Route::post('/gocheckout', ['as' => 'gocheckout','uses'=>'CommonController@go_checkout']);
Route::post('/paystack', ['as' => 'paystack','uses'=>'ItemController@redirectToGateway']);
Route::get('/paystack', 'ItemController@handleGatewayCallback');
/* checkout */

/* razorpay */
Route::post('/razorpay', ['as' => 'razorpay','uses'=>'ItemController@razorpay_payment']);
/* razorpay */

/* iyzico */
Route::post('/iyzico-success/{ord_token}', ['as' => 'iyzico-success','uses'=>'ItemController@iyzico_success']);
Route::post('/subscription-iyzico/{ord_token}', ['as' => 'subscription-iyzico','uses'=>'ProfileController@iyzico_success']);
/* iyzico */

/* flutterwave */
Route::get('/flutterwave', 'ItemController@flutterwaveCallback');
Route::get('/subscription-flutterwave', 'ProfileController@flutterwaveCallback');
/* flutterwave */

/* coingate */
Route::get('/coingate', 'ItemController@coingate_success'); // admin callback
Route::get('/coingate-success', 'ItemController@coingate_callback'); // vendor callback 
Route::get('/subscription-coingate', 'ProfileController@coingateCallback');
/* coingate */

/* mollie */
Route::get('/subscription-mollie', 'ProfileController@mollieCallback');
/* mollie */

/* robokassa */
Route::get('/robokassa-success', 'ProfileController@robokassaCallback');
/* robokassa */

/* payhere */
Route::get('/payhere-success/{ord_token}', 'ItemController@payhere_success');
Route::get('/subscription-payhere/{ord_token}', 'ProfileController@payhere_success');
/* payhere */

/* midtrans */
Route::get('/midtrans-success/{ord_token}', 'ItemController@midtrans_success');
/* midtrans */

/* cashfree */
Route::get('/cashfree-success/{type}/', 'ItemController@cashfree_success');
/* cashfree */

/* payfast */
Route::get('/payfast-success/{ord_token}', 'ItemController@payfast_success');
/* payfast */

/* payumoney */
Route::post ('/payu_success', 'ItemController@payu_success');
Route::post ('/payu_subscription', 'ProfileController@payu_success');
/* payumoney */


/* ipay */
Route::get('/ipay', 'ItemController@ipay_success'); // admin callback
Route::get('/subscription-ipay', 'ProfileController@ipay_success');
/* ipay */


/* sslcommerz */

Route::post('/subscription-sslcommerz','ProfileController@sslcommerz_success')->name('sslcommerz.success');
Route::post('/subscription-sslcommerz-failure','ProfileController@sslcommerz_failure')->name('sslcommerz.failure');
Route::post('/subscription-sslcommerz-cancel','ProfileController@sslcommerz_cancel')->name('sslcommerz.cancel');
Route::post('/subscription-sslcommerz-ipn','ProfileController@sslcommerz_ipn')->name('sslcommerz.ipn');

Route::post('/sslcommerz-success','ItemController@sslcommerz_successpage')->name('sslcommerz.successpage');
Route::post('/sslcommerz-failure','ProfileController@sslcommerz_failure')->name('sslcommerz.failurepage');
Route::post('/sslcommerz-cancel','ProfileController@sslcommerz_cancel')->name('sslcommerz.cancelpage');
Route::post('/sslcommerz-ipn','ProfileController@sslcommerz_ipn')->name('sslcommerz.ipnpage');
/* sslcommerz */


/* success */
Route::get('/success/{ord_token}', 'ItemController@paypal_success');
Route::get('/cancel', 'CommonController@payment_cancel');
Route::get('/failure', 'CommonController@payment_failure');
Route::get('/pending', 'CommonController@payment_pending');
Route::get('/success', 'ItemController@view_success');
Route::get('/2checkout-success', 'ItemController@two_checkout_success');
Route::get('/coinpayments-success/{ord_token}', 'ItemController@coinpayments_success');
Route::get('/instamojo-success/{ord_token}', 'ItemController@instamojo_success');
Route::post('/aamarpay/{ord_token}', 'ItemController@aamarpay_success');
Route::get('/mollie', 'ItemController@mollie_success');
Route::get('/coinbase/{ord_token}', 'ItemController@coinbase_success');
Route::get('/nowpayments/{ord_token}', 'ItemController@nowpayments_success');
/* success */


/* mercadopago */
Route::get('/mercadopago-success/{ord_token}', 'ItemController@mercadopago_success');
/* mercadopago */

/* stripe */
Route::get('/stripe-success/{item_user_id}/{purchase_token}', 'ItemController@stripe_callback'); // vendor callback 
/* stripe */


/* purchases */

Route::get('/purchases', 'ItemController@view_purchases');
Route::get('/purchases/{token}/{order_id}', 'ItemController@purchases_download');
Route::post('/purchases', ['as' => 'purchases','uses'=>'ItemController@rating_purchases']);
Route::get('/invoice/{product_token}/{order_id}', 'ItemController@invoice_download');
Route::get('/download/{token}', 'ItemController@file_download');
/* purchases */

/* sales */

Route::get('/sales', 'ItemController@view_sales');
Route::get('/sales/{token}', 'ItemController@view_order_details');
Route::post('/refund', ['as' => 'refund','uses'=>'ItemController@refund_request']);

/* sales */


/* withdrawal */

Route::get('/withdrawal', 'ItemController@view_withdrawal');
Route::post('/withdrawal', ['as' => 'withdrawal','uses'=>'ItemController@withdrawal_request']);

/* withdrawal */



/* contact */

Route::get('/contact', 'CommonController@view_contact');
Route::post('/contact', ['as' => 'contact','uses'=>'CommonController@update_contact']);
/* contact */


/* newsletter */
	Route::post('/newsletter', ['as' => 'newsletter','uses'=>'CommonController@update_newsletter']);
	Route::get('/newsletter/{token}', 'CommonController@activate_newsletter');
	Route::get('/newsletter', 'CommonController@view_newsletter');
	/* newsletter */
	

/* conversation */
Route::get('/conversation-to-vendor/{to_slug}/{order_id}', 'ChatController@view_conversation');
Route::post('/conversation', ['as' => 'conversation','uses'=>'ChatController@conversation_message']);
Route::get('/conversation/{id}', 'ChatController@delete_conversation');
Route::get('/conversation-to-buyer/{to_slug}/{order_id}', 'ChatController@view_buyer_conversation');
/* conversation */


/* messages */
Route::get('/messages', 'ChatController@view_messages');
Route::get('/messages/{to_slug}', 'ChatController@view_message_conversation');
Route::get('/messages/{name}/{to_slug}', 'ChatController@new_message_conversation');
Route::post('/messages', ['as' => 'messages','uses'=>'ChatController@chat_message']);
Route::get('/messages/{drop}/{id}', 'ChatController@view_message_delete');
/* messages */
Route::get('/stripe','StripeController@stripe_index');
Route::post('/stripe', ['as' => 'stripe','uses'=>'StripeController@afterpayment']);
Route::get('/stripe-subscription','StripeController@stripe_subscription_index');
Route::get('/subscription-stripe/{order_id}', 'ProfileController@subscription_stripe');
Route::get('/deposit-stripe-success/{order_id}', 'ItemController@deposit_stripe_success');


Route::get('/{page_slug}', 'PageController@view_page');


Route::get('/offline', function () {
    return view('vendor.laravelpwa.offline');
});



});
