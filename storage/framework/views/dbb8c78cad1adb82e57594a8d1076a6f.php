<!DOCTYPE HTML>
<html lang="en">
<head>
<title><?php echo e($allsettings->site_title); ?> - <?php echo e(__('My Cart')); ?></title>
<?php echo $__env->make('meta', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('style', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.css'>
</head>
<body>
<?php echo $__env->make('header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php if($cart_count != 0): ?> 
<div class="page-title-overlap pt-4" style="background-image: url('<?php echo e(url('/')); ?>/public/storage/settings/<?php echo e($allsettings->site_banner); ?>');">
      <div class="container d-lg-flex justify-content-between py-2 py-lg-3">
        <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb flex-lg-nowrap justify-content-center justify-content-lg-star">
              <li class="breadcrumb-item"><a class="text-nowrap" href="<?php echo e(URL::to('/')); ?>"><i class="dwg-home"></i><?php echo e(__('Home')); ?></a></li>
              <li class="breadcrumb-item text-nowrap active" aria-current="page"><?php echo e(__('My Cart')); ?></li>
              </li>
             </ol>
          </nav>
        </div>
        <div class="order-lg-1 pr-lg-4 text-center text-lg-left">
          <h1 class="h3 mb-0 text-white"><?php echo e(__('My Cart')); ?></h1>
        </div>
      </div>
    </div>
<div class="container mb-5 pb-3">
      <div class="bg-light box-shadow-lg rounded-lg overflow-hidden">
      
        <div class="row">
          <!-- Content-->
          <?php if($cart_count != 0): ?>
          <section class="col-lg-8 pt-2 pt-lg-4 pb-4 mb-3">
            <div class="pt-2 px-4 pr-lg-0 pl-xl-5">
              <!-- Header-->
              <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom pb-3">
                <div class="py-1"><a class="btn btn-outline-accent btn-sm" href="<?php echo e(url('/shop')); ?>"><i class="dwg-arrow-left mr-1 ml-n1"></i><?php echo e(__('Back to shopping')); ?></a></div>
                <div class="d-none d-sm-block py-1 font-size-ms"><?php echo e(__('You have')); ?> <?php echo e($cart_count); ?> <?php echo e(__('products in your cart')); ?></div>
                <div class="py-1"><a class="btn btn-outline-danger btn-sm" href="<?php echo e(url('/clear-cart')); ?>" onClick="return confirm('<?php echo e(__('Are you sure you want to delete?')); ?>');"><i class="dwg-close font-size-xs mr-1 ml-n1"></i><?php echo e(__('Clear cart')); ?></a></div>
              </div>
              <form id="contact_form" name="subtotal-form" method="post" action="<?php echo e(route('gocheckout')); ?>">
              <?php echo e(csrf_field()); ?>

          <table class="basket-tbl">
          <tr><td colspan="5" class="height-10"></td></tr>
          <div class="linepara">
          <?php 
          $subtotal = 0;
          $coupon_code = ""; 
          $new_price = 0;
          $coupon_type = "";
          $coupon_value = 0;
          ?>
          <?php $__currentLoopData = $cart['item']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cart): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php
          if($cart->discount_price != 0)
          {
               $price = $cart->discount_price;
               $new_price += $cart->discount_price;
               $coupon_code = $cart->coupon_code;
               $coupon_type = $cart->coupon_type;
               $coupon_value = $cart->coupon_value;
          }
          else
          {
             $price = $cart->item_price;
             $new_price += $cart->item_price;
             $coupon_type = $cart->coupon_type;
             $coupon_value = $cart->coupon_value;
          }
          ?> 
            <tr>
              <td class="product" style="width:40%;">
                <?php if($cart->item_thumbnail!=''): ?>
              <img class="lazy rounded-lg" width="70" height="54" src="<?php echo e(Helper::Image_Path($cart->item_thumbnail,'no-image.png')); ?>"  alt="<?php echo e($cart->item_name); ?>">
              <?php else: ?>
              <img class="lazy rounded-lg" width="70" height="54" src="<?php echo e(url('/')); ?>/public/img/no-image.png"  alt="<?php echo e($cart->item_name); ?>">
              <?php endif; ?>
              <label class="para product-title d-inline-block"><a href="<?php echo e(url('/item')); ?>/<?php echo e($cart->item_slug); ?>"><?php echo e($cart->item_name); ?></a></label><br/>
              <a class="text-accent font-size-ms" href="<?php echo e(url('/item')); ?>/<?php echo e($cart->item_slug); ?>"><?php echo e($cart->license); ?> <?php if($cart->license == 'regular'): ?> ( <?php echo e($addition_settings->regular_license); ?> <?php if($cart->item_support == 1): ?> <?php echo e(__('Support')); ?> <?php else: ?> <?php echo e(__('not support')); ?> <?php endif; ?>) <?php elseif($cart->license == 'extended'): ?> ( <?php echo e($addition_settings->extended_license); ?> <?php if($cart->item_support == 1): ?> <?php echo e(__('Support')); ?> <?php else: ?> <?php echo e(__('not support')); ?> <?php endif; ?>) <?php endif; ?></a>
              </td>
              <td style="width:20%;">
                <div class="currency">
                  <?php /*?>@if($cart->discount_price != 0)
                  <span class="pricemove">{{ $currency_symbol }}</span><input type="text" name="price[]" id="coster" class="price" value="{{ $price }}" readonly>
                  @else
                  <span class="pricemove">{{ $currency_symbol }}</span><input type="text" name="price[]" id="coster" class="price" value="{{ $cart->item_price }}" readonly>
                  @endif<?php */?>
                  <?php if($allsettings->site_currency_position == "left"): ?>
                  <span class="pricemove"><?php echo e($currency_symbol); ?></span><input type="text" name="price[]" id="coster" class="price" value="<?php echo e(Helper::price_value($cart->item_single_price,$multicurrency)); ?>" readonly> 
                  <?php else: ?>
                  <span class="pricemove"><input type="text" name="price[]" id="coster-right" class="price" value="<?php echo e(Helper::price_value($cart->item_single_price,$multicurrency)); ?>" readonly><?php echo e($currency_symbol); ?></span> 
                  <?php endif; ?>
                </div>
              </td>
              <?php if($cart->file_type == 'serial'): ?>
              <?php
              if($cart->item_delimiter == 'comma')
              {
                $result_count = substr_count($cart->item_serials_list, ","); 
              }
              else
              {
                $result_count = substr_count($cart->item_serials_list, "\n");
              }
              ?>
              <td style="width:20%;">
              <label class="form-label" for="quantity1"><?php echo e(__('Stock')); ?></label>
                <div class="qty">
                  <input class="qty" type="text"  name="qty[]" id="qty" value="<?php echo e($cart->item_serial_stock); ?>" data-bvalidator="required,min[1],max[<?php echo e($result_count); ?>]">
                </div>
              </td>
              <input type="hidden" name="serial[]" value="yes">
              <?php else: ?>
              <td style="width:20%; visibility:hidden;">
                <div class="">
                <input class="qty" type="text"  name="qty[]" id="qty" value="1" min="1">
                </div>
              </td>
              <input type="hidden" name="serial[]" value="no">  
             <?php endif; ?>
              <td style="width:20%;">
                <div class="currency">
                 <?php if($allsettings->site_currency_position == "left"): ?>
                  <span class="pricemove"><?php echo e($currency_symbol); ?></span><input type="text" name="cost" class="cost" id="coster" value="" readonly>
                  <?php else: ?>
                  <span class="pricemove"><input type="text" name="cost" class="cost" id="coster-right" value="" readonly><?php echo e($currency_symbol); ?></span>
                  <?php endif; ?>
                </div>
              </td>
              <td style="width:10%;">
                <a href="<?php echo e(url('/cart')); ?>/<?php echo e(base64_encode($cart->ord_id)); ?>" onClick="return confirm('<?php echo e(__('Are you sure you want to delete?')); ?>');"><i class="dwg-close"></i>
                </a>
              </td>
            </tr>
            <input type="hidden" name="ord_id[]" value="<?php echo e($cart->ord_id); ?>">
            <input type="hidden" name="item_user_id[]" value="<?php echo e($cart->item_user_id); ?>">
            
            <tr><td colspan="5" class="height-50"></td></tr>
            <?php $subtotal += $cart->item_price; ?>
           <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
           <input type="hidden" name="currency_type" value="<?php echo e($currency_symbol); ?>">
           <input type="hidden" name="currency_type_code" value="<?php echo e($multicurrency); ?>">
           </div>
        </table>
        <div class="float-right">
        <?php if($addition_settings->guest_checkout == 1): ?>
        <button class="btn btn-primary" type="submit"><i class="dwg-locked font-size-lg mr-2"></i><?php echo e(__('Proceed To Checkout')); ?></button>
        <?php else: ?>
        <?php if(Auth::guest()): ?>
        <a class="btn btn-primary" href="<?php echo e(URL::to('/login')); ?>"><i class="dwg-locked font-size-lg mr-2"></i><?php echo e(__('Proceed To Checkout')); ?></a>
        <?php else: ?>
        <button class="btn btn-primary" type="submit"><i class="dwg-locked font-size-lg mr-2"></i><?php echo e(__('Proceed To Checkout')); ?></button>
        <?php endif; ?>
        <?php endif; ?>
        </div>
      </form>
      </div>
            
          </section>
              <!-- Product-->
              
          <!-- Sidebar-->
          <aside class="col-lg-4">
            <hr class="d-lg-none">
            <div class="cz-sidebar-static h-100 ml-auto border-left">
              <ul class="list-unstyled font-size-sm pt-3 pb-2 border-bottom">
                  <li class="d-flex justify-content-between align-items-center"><span class="mr-2"><?php echo e(__('Cart Subtotal')); ?></span>
                  <span class="text-right"><input type="text" name="subtotal" class="subtotal" id="cart_subtotal" value="<?php echo e($subtotal); ?>" readonly></span></li>
                  <?php
                  if($addition_settings->site_extra_fee_type == 'fixed')
                  {
                     $extra_fee = $allsettings->site_extra_fee;
                  }
                  else
                  {
                     $extra_fee = ($allsettings->site_extra_fee * $subtotal) / 100;
                  }
                  ?>
                  <?php if($extra_fee != 0): ?>
                  <li class="d-flex justify-content-between align-items-center"><span class="mr-2"><?php echo e(__('Processing Fee')); ?></span><span class="text-right"><input type="text" name="extra_fee" class="extra_fee" id="cart_subtotal" value="<?php echo e(Helper::price_value($extra_fee,$multicurrency)); ?>" readonly></span></li>
                  <?php endif; ?>
                  <?php if($coupon_code != ""): ?>
                  <?php 
                  $coupon_discount = $subtotal - $new_price;
                  $final = $new_price+$extra_fee; 
                  ?> 
                  <li class="d-flex justify-content-between align-items-center"><span class="mr-2"><?php echo e(__('Discount Price')); ?></span><span class="text-right"><strong class="green">(<?php echo e($coupon_code); ?>)</strong> <a href="<?php echo e(URL::to('/cart/')); ?>/remove/<?php echo e($coupon_code); ?>" class="red fs14" onClick="return confirm('<?php echo e(__('Are you sure you want to delete?')); ?>');" title="<?php echo e(__('Remove')); ?>"><i class="dwg-close font-size-xs"></i></a></span><input type="text" name="coupon_discount" class="coupon_discount" id="cart_subtotal" value="<?php echo e($coupon_discount); ?>" style="max-width:100px;" readonly></span></li>
                  <?php else: ?>
                  <?php $final = $subtotal+$extra_fee; ?>
                  <?php endif; ?>
                </ul>
              <div class="text-center mb-4 pb-3 border-bottom">
                <h2 class="h6 mb-3 pb-1 text-center textmoves"><?php echo e(__('Total')); ?></h2>
                <h3 class="font-weight-normal text-center"><input type="text" name="total" class="total" id="cart_total" value="<?php echo e($final); ?>" readonly></h3>
              </div>
              <form action="<?php echo e(route('coupon')); ?>" class="setting_form" id="coupon_form" method="post" enctype="multipart/form-data">
              <?php echo e(csrf_field()); ?> 
              <div class="text-center mb-4 pb-3 border-bottom">
                <h2 class="h6 mb-3 pb-1"><?php echo e(__('Coupon Code')); ?></h2>
                  <div class="form-group">
                    <input class="form-control" type="text" placeholder="<?php echo e(__('Coupon Code')); ?>" id="coupon" name="coupon" required>
                  </div>
                  <button class="btn btn-secondary btn-block" type="submit"><?php echo e(__('Apply Coupon')); ?></button>
              </div>
            </div>
          </aside>
          <?php endif; ?>
        </div>
        </form>
      </div>
    </div>
    <?php else: ?>
    <section class="bg-position-center-top" style="background-image: url('<?php echo e(url('/')); ?>/public/storage/settings/<?php echo e($allsettings->site_banner); ?>');">
      <div class="py-4">
        <div class="container d-lg-flex justify-content-between py-2 py-lg-3">
        <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb flex-lg-nowrap justify-content-center justify-content-lg-star">
              <li class="breadcrumb-item"><a class="text-nowrap" href="<?php echo e(URL::to('/')); ?>"><i class="dwg-home"></i><?php echo e(__('Home')); ?></a></li>
              <li class="breadcrumb-item text-nowrap active" aria-current="page"><?php echo e(__('My Cart')); ?></li>
            </ol>
          </nav>
        </div>
        <div class="order-lg-1 pr-lg-4 text-center text-lg-left">
          <h1 class="h3 mb-0 text-white"><?php echo e(__('My Cart')); ?></h1>
        </div>
      </div>
      </div>
    </section>
<div class="container py-5 mt-md-2 mb-2">
      <div class="row">
        <div class="col-lg-12">
          <div class="font-size-md"><?php echo e(__('Your cart is empty!')); ?></div>
         </div>
      </div>
    </div>    
    <?php endif; ?>
<?php echo $__env->make('footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php if($cart_count != 0): ?> 
<script type="text/javascript">
$(function() {

  $(".qty").append('<div class="outer-button"><div class="inc button"><i class="fa fa-plus"></i></div><div class="dec button"><i class="fa fa-minus"></i></div></div>');

  calculate();
  
  $(".button").on("click", function() {

    var $button = $(this);
    var oldQty = $button.parent().parent().find("input").val();

    if ($button.html() == '<i class="fa fa-plus"></i>') {
      var newQty = parseFloat(oldQty) + 1;
    } else {
      // Don't allow decrementing less than zero
      if (oldQty > 0) {
        var newQty = parseFloat(oldQty) - 1;
      } else {
        newQty = 0;
      }
    }

    $button.parent().parent().find("input").val(newQty);
    calculate();
  });
      
  function calculate() {
      $(".basket-tbl tr").each(function() {
        var priceVal = $(this).find('input.price').val();
        var qtyVal = $(this).find("input.qty").val();
		
		var costVal = (priceVal * qtyVal);
        $(this).find('input.cost').val((costVal).toFixed(2));
      });
      var types = "<?php echo e($allsettings->site_currency_position); ?>";
      var subtotalVal = 0;
      $('.cost').each(function() {
        subtotalVal += parseFloat($(this).val());
      });
	  if(types == "left")
	  {
      $('.subtotal').val("<?php echo e($currency_symbol); ?>"+parseFloat(subtotalVal).toFixed(2));
	  }
	  else
	  {
	  $('.subtotal').val(parseFloat(subtotalVal).toFixed(2)+"<?php echo e($currency_symbol); ?>");
	  }
      var extra_fee_type = "<?php echo e($addition_settings->site_extra_fee_type); ?>";
	  var extra_fee_price = "<?php echo e(Helper::price_value($allsettings->site_extra_fee,$multicurrency)); ?>";
      if(extra_fee_type == 'fixed')
      {
         if(types == "left")
		 {            
		 $(".extra_fee").val("<?php echo e($currency_symbol); ?>"+parseFloat(extra_fee_price).toFixed(2));
		 }
		 else
		 {
		 $(".extra_fee").val(parseFloat(extra_fee_price).toFixed(2)+"<?php echo e($currency_symbol); ?>");
		 }
		 var vatVal = parseFloat(extra_fee_price).toFixed(2);
      }
      else
      {
	      if(types == "left")
		  {
          $(".extra_fee").val("<?php echo e($currency_symbol); ?>"+((subtotalVal / 100) * extra_fee_price).toFixed(2));
		  }
		  else
		  {
		  $(".extra_fee").val(((subtotalVal / 100) * extra_fee_price).toFixed(2)+"<?php echo e($currency_symbol); ?>");
		  }
		  var vatVal = ((subtotalVal / 100) * extra_fee_price).toFixed(2);
      }
	  
	  var coupon_type = "<?php echo e($coupon_type); ?>";
	  var coupon_value = "<?php echo e($coupon_value); ?>";
	  if(coupon_type == 'fixed')
	  {
	    if(types == "left")
		{
	    $(".coupon_discount").val(" - <?php echo e($currency_symbol); ?>"+parseFloat(coupon_value).toFixed(2));
		}
		else
		{
		$(".coupon_discount").val(" - "+parseFloat(coupon_value).toFixed(2)+"<?php echo e($currency_symbol); ?>");
		}
		var Coupon = parseFloat(coupon_value).toFixed(2);
	  }
	  else if(coupon_type == 'percentage')
      {
	     if(types == "left")
		 {
	     $(".coupon_discount").val(" - <?php echo e($currency_symbol); ?>"+((subtotalVal / 100) * coupon_value).toFixed(2));
		 }
		 else
		 {
		 $(".coupon_discount").val(" - "+((subtotalVal / 100) * coupon_value).toFixed(2)+"<?php echo e($currency_symbol); ?>");
		 }
		 var Coupon = ((subtotalVal / 100) * coupon_value).toFixed(2);
	  }
	  else
	  {
	    var Coupon = 0;
	  }
      
	   
      var SubTotal = parseFloat(subtotalVal) - parseFloat(Coupon);
	  var total = (parseFloat(SubTotal) + parseFloat(vatVal));
	  if(types == "left")
	  {
      $(".total").val("<?php echo e($currency_symbol); ?>"+parseFloat(total).toFixed(2));
	  }
	  else
	  {
	  $(".total").val(parseFloat(total).toFixed(2)+"<?php echo e($currency_symbol); ?>");
	  }
  }


  $(".fa-trash-o").click(function() {
    $(this).parent().parent().remove();
    calculate();
  });


    // $("#subtotal-form").submit(function(e) {

    //     var url = "";

    //     $.ajax({
    //            type: "POST",
    //            url: url,
    //            data: $("#subtotal-form").serializeArray(),
    //            success: function(data)
    //            {
    //                alert("All done!");
    //            }
    //          });

    //     e.preventDefault();
    // });

	

});
</script>
<?php endif; ?>
</body>
</html><?php /**PATH C:\laragon\www\autoscripthub\resources\views/pages/cart.blade.php ENDPATH**/ ?>