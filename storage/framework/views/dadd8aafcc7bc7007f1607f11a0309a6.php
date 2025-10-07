<?php if($addition_settings->theme_font_family != ""): ?>
<style type="text/css">
@import url("https://fonts.googleapis.com/css?family=<?php echo e($addition_settings->theme_font_family); ?>");
html, body, div,  applet, object,
iframe, p, blockquote,
pre, a, abbr, acronym, address, big, cite,
code, del, dfn, em, img, ins, kbd, q, s, samp,
small, strike, strong, sub, sup, tt, var, b,
u, center, dl, dt, dd, ol, ul, li, fieldset,
form, label, legend, table, caption, tbody,
tfoot, thead, tr, th, td, article, aside,
canvas, details, embed, figure, figcaption,
footer, header, hgroup, menu, nav, output, ruby,
section, summary, time, mark, audio, video {
		
		font-family: '<?php echo e(str_replace("+"," ",$addition_settings->theme_font_family)); ?>', sans-serif !important;
		font-size: inherit;
		line-height:inherit;
}
</style>
<?php endif; ?>
<style type="text/css">
.theme-primary
{
background:<?php echo e($allsettings->site_header_color); ?> !important;
}
.text-color
{
color:<?php echo e($allsettings->site_theme_color); ?>;
}
.text-color:hover
{
color:<?php echo e($allsettings->site_button_hover); ?>;
}
.btn-primary
{
color:#fff;background-color:<?php echo e($allsettings->site_button_color); ?>;border-color:<?php echo e($allsettings->site_button_color); ?>;box-shadow:none;
}
.stylised-play, .stylised-pause { width: 166px; height: 72px; background-color: <?php echo e($allsettings->site_button_color); ?>; position: absolute; left: 0; text-align: center; padding-top: 24px; padding-left: 20px; cursor: pointer; }
.stylised-restart { background-color: <?php echo e($allsettings->site_button_color); ?>;}
.stylised-play:active, .stylised-pause:active, .stylised-restart:active { background-color: <?php echo e($allsettings->site_button_color); ?>; }
.stylised-time-progress { height: 72px; background-color: <?php echo e($allsettings->site_button_color); ?>; cursor: pointer; }
.term_text h1
{
color:<?php echo e($allsettings->site_button_color); ?> !important;
text-align:center !important;
font-size:30px;
border:3px solid #E3E9EF;
padding:20px;
border-radius:5px;
}
.btn-primary:hover
{
color:#fff;background-color:<?php echo e($allsettings->site_button_hover); ?>;border-color:<?php echo e($allsettings->site_button_hover); ?>;
}
.btn-primary:focus,.btn-primary.focus
{
color:#fff;background-color:<?php echo e($allsettings->site_button_hover); ?>;border-color:<?php echo e($allsettings->site_button_hover); ?>;box-shadow:none,0 0 0 0 rgba(254,128,128,0.5);
}
.widget-list-link:hover,.jplist-selected
{
color:<?php echo e($allsettings->site_button_color); ?> !important;
}
.ui-widget-header
{
background:<?php echo e($allsettings->site_button_color); ?> !important;
}
.ui-widget-content
{
border:1px solid <?php echo e($allsettings->site_button_color); ?> !important;
}
.link-color
{
color:<?php echo e($allsettings->site_button_color); ?> !important;
}
.form-control:focus{color:#4b566b;background-color:#fff;border-color:<?php echo e($allsettings->site_button_color); ?> !important;outline:0;}
.bg-primary{background-color:<?php echo e($allsettings->site_button_color); ?> !important}a.bg-primary:hover,a.bg-primary:focus,button.bg-primary:hover,button.bg-primary:focus{background-color:<?php echo e($allsettings->site_button_hover); ?> !important}
.btn-primary:not(:disabled):not(.disabled):active,.btn-primary:not(:disabled):not(.disabled).active,.show>.btn-primary.dropdown-toggle{color:#fff;background-color:<?php echo e($allsettings->site_button_hover); ?>;border-color:#fe2a2b}
.navbar-light .navbar-nav .show>.nav-link,.navbar-light .navbar-nav .active>.nav-link,.navbar-light .navbar-nav .nav-link.show,.navbar-light .navbar-nav .nav-link.active{color:<?php echo e($allsettings->site_button_color); ?>;}
.navbar-light .navbar-nav .nav-link:hover,.navbar-light .navbar-nav .nav-link:focus{color:<?php echo e($allsettings->site_button_color); ?>;}
a:hover{color:<?php echo e($allsettings->site_button_color); ?>;text-decoration:none}
.dropdown-item:hover,.dropdown-item:focus{color:<?php echo e($allsettings->site_button_color); ?>;text-decoration:none;background-color:rgba(0,0,0,0)}.dropdown-item.active,.dropdown-item:active{color:<?php echo e($allsettings->site_button_color); ?>;text-decoration:none;background-color:rgba(0,0,0,0)}
.text-primary{color:<?php echo e($allsettings->site_button_color); ?> !important}
.dropdown-menu li:hover>.dropdown-item{color:<?php echo e($allsettings->site_button_color); ?>}.dropdown-menu .active>.dropdown-item{color:<?php echo e($allsettings->site_button_color); ?>}
.navbar-light .nav-item:hover .nav-link:not(.disabled),.navbar-light .nav-item:hover .nav-link:not(.disabled)>i{color:<?php echo e($allsettings->site_button_color); ?>;}.navbar-light .nav-item.active .nav-link:not(.disabled)>i,.navbar-light .nav-item.show .nav-link:not(.disabled)>i,.navbar-light .nav-item.dropdown .nav-link:focus:not(.disabled)>i{color:<?php echo e($allsettings->site_button_color); ?>;}
.bg-dark{background-color:<?php echo e($allsettings->site_header_color); ?> !important}a.bg-dark:hover,a.bg-dark:focus,button.bg-dark:hover,button.bg-dark:focus{background-color:<?php echo e($allsettings->site_header_color); ?> !important}
.topbar-dark .topbar-text>i,.topbar-dark .topbar-link>i{color:<?php echo e($allsettings->site_button_color); ?>}
.navbar-tool .navbar-tool-label{position:absolute;top:-.3125rem;right:-.3125rem;width:1.25rem;height:1.25rem;border-radius:50%;background-color:<?php echo e($allsettings->site_button_color); ?>;color:#fff;font-size:.75rem;font-weight:500;text-align:center;line-height:1.25rem}
.btn-outline-accent{color:#ffffff; background:<?php echo e($allsettings->site_button_color); ?>}
.btn-outline-accent:hover{color:#fff;background-color:<?php echo e($allsettings->site_theme_color); ?>;border-color:<?php echo e($allsettings->site_theme_color); ?>}
.product-title>a:hover{color:<?php echo e($allsettings->site_button_color); ?>}
.text-accent{color:<?php echo e($allsettings->site_theme_color); ?> !important;}
.carprice
{
width:60px;
}
#coster
{
color:<?php echo e($allsettings->site_theme_color); ?> !important;
border:0px;
cursor:default;
width:80px;

}
#coster-right
{
color:<?php echo e($allsettings->site_theme_color); ?> !important;
border:0px;
cursor:default;
width:80px;
text-align:right;

}
#cart_subtotal
{

border:0px;
cursor:default;
text-align:right;
}
.textmoves
{
margin-left:30px;
}
#cart_total
{

border:0px;
cursor:default;
text-align:center;



}
.fa-plus,
.fa-minus {
  font-size: 14px !important;
  color: #fff;
  padding: 2px;
  font-weight:normal !important;
  background-color:<?php echo e($allsettings->site_theme_color); ?> !important;
  
}
#coster:focus,#cart_subtotal:focus,#cart_total:focus
{
outline: none;
}
table.basket-tbl td input.qty,
table.result-tbl td input.qty {
  width: 30%;
  background-color: #ffffff;
  border: 1px solid #999999;
  height: 42px;
  margin-right: 2%;
  float: left;
  text-align: right;
  padding-right: 5px;
}
table.basket-tbl .outer-button,
table.result-tbl .outer-button {
  width: 68%;
  float: right;
  margin-top: -2px;
}
.rreadonly:focus{
        outline: none;
		
		
    }
.bg-faded-accent{background-color:rgba(78,84,200,0.1) !important}
.price-old
{
color:#999999;
font-size:14px;
}
.turn-page .turn-ul li:hover, .turn-page .turn-ul li:active {
  background: <?php echo e($allsettings->site_button_color); ?> !important;
  color: #fff;
}
.turn-page .turn-ul li.on {
  background: <?php echo e($allsettings->site_button_color); ?> !important;
  color: #fff;
}
.btn-wishlist:hover{color:<?php echo e($allsettings->site_button_color); ?>}
.flash-sale .price-badge { background:#DE2F2F; color:#FFFFFF; }
.widget-product-title:hover>a{color:<?php echo e($allsettings->site_button_color); ?>}
.blog-entry-title>a:hover{color:<?php echo e($allsettings->site_button_color); ?>}
.breadcrumb-item>a:hover{color:<?php echo e($allsettings->site_button_color); ?>;text-decoration:none}
.homeblog img
{
height:240px;
object-fit:cover;
width:100%;
}
.bg-darker{background-color:<?php echo e($allsettings->site_footer_color); ?> !important}
a{color:<?php echo e($allsettings->site_button_color); ?>;text-decoration:none;background-color:transparent}
.display-404{color:#fff;font-size:10rem;text-shadow:-0.0625rem 0 <?php echo e($allsettings->site_button_color); ?>,0 0.0625rem <?php echo e($allsettings->site_button_color); ?>,0.0625rem 0 <?php echo e($allsettings->site_button_color); ?>,0 -0.0625rem <?php echo e($allsettings->site_button_color); ?>}
.red
{
width: 100%;
margin-top: .25rem;
font-size: 80%;
color: #f34770;
}
.countdown-timer ul li
{
list-style:none;
display:inline-block;
background:<?php echo e($allsettings->site_theme_color); ?>;
color:#FFFFFF;
text-align:center;
min-width:70px;
opacity:0.6;
}
.ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default
{
border: 0px !important;
    border-radius: 50%;
    box-shadow: 0 0.125rem 0.5625rem -0.125rem rgb(0 0 0 / 25%);

    

}
.ui-state-hover, .ui-widget-content .ui-state-hover, .ui-widget-header .ui-state-hover, .ui-state-focus, .ui-widget-content .ui-state-focus, .ui-widget-header .ui-state-focus
{
border: 0px !important;
}
.range-price {
    border: 0;
    color: <?php echo e($allsettings->site_button_color); ?> !important;
    font-weight: bold;
    font-size: 16px;
    margin-bottom: 14px;
}
.lato{}.jplist-panel .jplist-pagination{cursor:pointer;float:right;line-height:30px}
.jplist-panel .jplist-pagination button{list-style: none;
    float: left;
    width: 38px;
    height: 36px;
    line-height: 36px;
    text-align: center;
    border: 1px solid #54667a;
    margin-left: -1px;
    cursor: pointer;
    background: #fff;}
	.jplist-label 
    { 
    height: 36px !important;
    line-height: 38px !important;
	border: 1px solid #54667a !important;
    margin-left: -1px;
    cursor: pointer !important;
    background: #fff !important;
	border-radius:0px !important;
	font-size:13px;
	padding-left:5px;
	padding-right:5px;
	float:right;
	color:<?php echo e($allsettings->site_theme_color); ?>;
	}
.jplist-panel button { border-radius:0px !important; box-shadow:0px !important; text-shadow:none !important; margin:10px 5px 0 0 !important; }	
.jplist-panel .jplist-pagination .jplist-current{color: #fff; background:<?php echo e($allsettings->site_button_color); ?> !important;}
.jplist-panel .jplist-pagination .jplist-pagingprev,.jplist-panel .jplist-pagination .jplist-pagingmid,.jplist-panel .jplist-pagination .jplist-pagingnext{float:left}.jplist-panel .jplist-pagination .jplist-pagingprev button,.jplist-panel .jplist-pagination .jplist-pagingnext button{font-size:20px;}.jplist-one-page{display:none}.jplist-empty{display:none}
.customlable {
    float: right !important;
}
.btn-outline-accent:focus, .btn-outline-accent.focus
{
background-color:<?php echo e($allsettings->site_button_color); ?> !important;
}
.nav-tabs .nav-link.active,.nav-tabs .nav-item.show .nav-link{color:<?php echo e($allsettings->site_button_color); ?>;background-color:rgba(0,0,0,0);border-color:<?php echo e($allsettings->site_button_color); ?>}
.nav-tabs .nav-link.active::before{background-color:<?php echo e($allsettings->site_button_color); ?>}
.nav-tabs .nav-link:hover{color:<?php echo e($allsettings->site_button_color); ?>}
.custom-control-input:checked ~ .custom-control-label::before{color:#fff;border-color:<?php echo e($allsettings->site_button_color); ?>;background-color:<?php echo e($allsettings->site_button_color); ?>;box-shadow:none}
.btn-primary.btn-shadow{}
.review_tag {
  background: <?php echo e($allsettings->site_button_color); ?>;
  -webkit-border-radius: 200px;
          border-radius: 200px;
  line-height: 30px;
  padding: 0 12px;
  color: #fff;
  font-weight: 500;
  margin-left: 10px;
}
.custom_radio
{
color: #fff;
border-color: <?php echo e($allsettings->site_button_color); ?>;
background-color: <?php echo e($allsettings->site_button_color); ?>;
box-shadow: none;
}
.nav-link-style:hover{color:<?php echo e($allsettings->site_button_color); ?>;}
.cz-range-slider-ui .noUi-connect{background-color:<?php echo e($allsettings->site_button_color); ?>}
.dropdown-content a:hover
{
color:<?php echo e($allsettings->site_theme_color); ?>;
}
.single-price-item:hover {
	border: 5px solid <?php echo e($allsettings->site_button_color); ?>;
}
.single-price-item p b {
	font-size: 35px;
	color: <?php echo e($allsettings->site_theme_color); ?>;
	margin-right: 10px;
}
.single-price-item h5:before {
	position: absolute;
	content: "";
	width: 54px;
	height: 2px;
	bottom: -10px;
	left: 0;
	right: 0;
	background: <?php echo e($allsettings->site_theme_color); ?>;
	margin: 0 auto;
}
.main-btn {
	display: inline-block;
	background: <?php echo e($allsettings->site_button_color); ?>;
	color: #ffffff;
	font-size: 16px;
	font-weight: 500;
	letter-spacing: 1px;
	text-transform: capitalize;
	padding: 14px 28px;
	text-align: center;
	vertical-align: middle;
	cursor: pointer;
	border-radius: 5px;
	-webkit-transition: .3s;
	transition: .3s;
}
.main-btn:hover {
	background-color: <?php echo e($allsettings->site_button_hover); ?>;
	color: #fff;
}
#boxradio input[type=radio]:checked + label 
{
  
  border: 1px solid <?php echo e($allsettings->site_button_color); ?> !important;
  
}
#boxradio input[type=radio]:checked + label::after {
  color: #3d3f43;
  border: 2px solid <?php echo e($allsettings->site_button_color); ?> !important;
  content: url("<?php echo e(asset('resources/views/assets/icon.png')); ?>");
  position: absolute;
  top: -25px;
  left: 50%;
  transform: translateX(-50%);
  height: 50px;
  width: 50px;
  line-height: 70px;
  text-align: center;
  border-radius: 50%;
  background: white;
  box-shadow: 0px 2px 5px -2px rgba(0, 0, 0, 0.25);
}
.ajax-load
{
  width: 100%;
}
.textleft
{
text-align:left;
}
@media only screen and (max-width: 43em) {
table.basket-tbl .outer-button {
    margin-top: 0;
  }
}
.height-50
{
height:50px;

}
.height-10
{
height:10px;

}
.pricemove
{
display:inline-block;
color:<?php echo e($allsettings->site_theme_color); ?> !important;
}  
.stockqty .bvalidator-red-tooltip
{
left:-200px !important;
}
.stockqty .bvalidator-red-tooltip .bvalidator-red-arrow
{
right:15px !important;
left:auto !important;
}
.demo .ui-widget.ui-widget-content
{
overflow-y:inherit !important;
overflow-x:inherit !important;
max-height:5px !important;
}
.ui-slider-horizontal .ui-slider-handle
{
top:-0.6em !important;
}
.mobile_content .menu_icon {
  background: <?php echo e($allsettings->site_button_color); ?> !important;
  padding: 0 20px;
  line-height: 60px;
  color: white;
  display: none;
  font-size: 15px;
  cursor: pointer;
}
.dropdowns {
  position: absolute;
  min-width: 271px;
  background: #fff;
  /*padding: 19px 30px;*/
  z-index: 9;
  visibility: hidden;
  opacity: 0;
  -webkit-transition: 0.3s ease;
  -o-transition: 0.3s ease;
  transition: 0.3s ease;
  border-top: 1px solid <?php echo e($allsettings->site_button_color); ?> !important;
  -webkit-border-radius: 0 0 4px 4px;
          border-radius: 0 0 4px 4px;
  -webkit-box-shadow: 0 5px 40px rgba(82, 85, 90, 0.2);
          box-shadow: 0 5px 40px rgba(82, 85, 90, 0.2);
  /* dropdown menu */
}
.dropdowns:before {
  content: '';
  position: absolute;
  border-left: 10px solid transparent;
  border-right: 10px solid transparent;
  border-bottom: 10px solid <?php echo e($allsettings->site_button_color); ?> !important;
  bottom: 100%;
}
.dropdowns.dropdown--author ul li a:hover {
  
  color: <?php echo e($allsettings->site_button_color); ?> !important;
}
.dropdowns
{
min-width:200px !important;
}
.mainmenu__menu .navbar-nav > li:hover > a{
color:#fff !important;
}
.mainmenu__menu .navbar-nav > li:hover {
  color:#fff !important;
  background:<?php echo e($allsettings->site_button_color); ?> !important;
}
.author__notification_area ul li .notification_count.purch {
  background: <?php echo e($allsettings->site_button_color); ?> !important;
}
.radius-left:hover,.radius-right:hover
{
background:<?php echo e($allsettings->site_button_color); ?> !important;
color:#fff;
}
.login-btn .radius-right
{
left:-10px;
}
.autor__info .ammount {
  color: <?php echo e($allsettings->site_button_color); ?> !important;
  font-size: 15px;
  font-weight: 400;
}
.author__avatar img
{
max-width:44px;
}
.dropdowns.dropdown--cart .cart_area .cart_action .go_cart {
  
  background: #333333 !important;
}
.dropdowns.dropdown--cart .cart_area .cart_action .go_checkout {
    background: <?php echo e($allsettings->site_button_color); ?> !important;
}
.dropdowns.dropdown--cart .cart_area .cart_action a {
  width: 50%;
  float: left;
  display: block;
  text-align: center;
  padding: 23px 25px;
  color: #fff;
}
.product__action p
{
color: <?php echo e($allsettings->site_button_color); ?> !important;
}
.theme-color
{
color:<?php echo e($allsettings->site_button_color); ?> !important;
}
#my-welcome-message .header {
background:<?php echo e($custom_settings->pr_head_bg_color); ?> !important;
}
#my-welcome-message h3,#fvpp-close
{
color:<?php echo e($custom_settings->pr_head_color); ?> !important;
}
</style>
<?php if($addition_settings->site_custom_css != ""): ?>
<style type="text/css">
<?php echo e($addition_settings->site_custom_css); ?>

</style>
<?php endif; ?><?php /**PATH C:\laragon\www\autoscripthub\resources\views/dynamic.blade.php ENDPATH**/ ?>