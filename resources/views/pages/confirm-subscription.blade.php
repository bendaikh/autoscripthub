<!DOCTYPE HTML>
<html lang="en">
<head>
<title>{{ $allsettings->site_title }} - {{ __('Subscription Upgrade') }}</title>
@include('meta')
@include('style')
</head>
<body>
@include('header')
<section class="bg-position-center-top" style="background-image: url('{{ url('/') }}/public/storage/settings/{{ $allsettings->site_banner }}');">
      <div class="py-4">
        <div class="container d-lg-flex justify-content-between py-2 py-lg-3">
        <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb flex-lg-nowrap justify-content-center justify-content-lg-star">
              <li class="breadcrumb-item"><a class="text-nowrap" href="{{ URL::to('/') }}"><i class="dwg-home"></i>{{ __('Home') }}</a></li>
              <li class="breadcrumb-item text-nowrap active" aria-current="page">{{ __('Subscription Upgrade') }}</li>
            </ol>
          </nav>
        </div>
        <div class="order-lg-1 pr-lg-4 text-center text-lg-left">
          <h1 class="h3 mb-0 text-white">{{ __('Subscription Upgrade') }}</h1>
        </div>
      </div>
      </div>
    </section>
<div class="faq-section section-padding subscribe-details" data-aos="fade-up" data-aos-delay="200">
		<div class="container py-5 mt-md-2 mb-2">
			<div class="row">
         <div class="col-sm-6 col-md-7 col-lg-7 subscribe-details">
            <div class="mb-3">
                <h4 class="mb-3">{{ __('Subscription Details') }}</h4>
                <div class="card-body">
                    <p><label>{{ __('Subscription Name') }} :</label> {{ $subscr['view']->subscr_name }}</p>
                    <p><label>{{ __('Price') }} :</label> {{ Helper::price_format($allsettings->site_currency_position,$subscr['view']->subscr_price,$currency_symbol,$multicurrency) }}</p>
                    <p><label>{{ __('Duration') }} :</label> {{ $subscr['view']->subscr_duration }}</p>
                    @if($subscr['view']->subscr_item_level == 'limited')
                    <p><label>{{ __('No of Items') }} :</label> {{ $subscr['view']->subscr_item }} {{ __('Items') }}</p>
                    @else
                    <p><label>{{ __('No of Items') }} :</label> {{ __('Unlimited') }}</p>
                    @endif
                    @if($subscr['view']->subscr_space_level == 'limited')
                    <p><label>{{ __('Available Space') }} :</label> {{ $subscr['view']->subscr_space }}{{ $subscr['view']->subscr_space_type }}</p>
                    @else
                    <p><label>{{ __('Available Space') }} :</label> {{ __('Unlimited') }}</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-5 col-lg-5">
            <div>
                <h4 class="mb-3">{{ __('Select Payment Method') }}
                </h4>
                <div class="card-body">
                    <form action="{{ route('confirm-subscription') }}" class="needs-validation" id="checkout_form" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                    @php $no = 1; @endphp
                        @foreach($get_payment as $payment)
                        <div class="lebel">
                           <input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="auto-width" value="{{ $payment }}" @if($no == 1) checked @endif data-bvalidator="required">
                           <label for="opt1-{{ $payment }}" >{{ $payment }} @if($payment == 'wallet') ({{ Helper::price_format($allsettings->site_currency_position,Auth::user()->earnings,$currency_symbol,$multicurrency) }}) @endif</label>      
                        </div>
                        @if($payment == 'stripe')
                        @if($stripe_type == 'charges')
                                <div class="row" id="ifYes" style="display:none;">
                                  <div class="col-md-12 mb-3">
                                        <div class="stripebox">
                                        <label for="card-element">{{ __('Credit or debit card') }}</label>
                                        <div id="card-element">
                                        </div>
                                        <div id="card-errors" role="alert"></div>
                                        </div>
                                    </div>    
                                </div> 
                                @endif       
                                @endif
                                @php $no++; @endphp
                                @endforeach
                                <input type="hidden" name="website_url" value="{{ url('/') }}">
                                <input type="hidden" name="user_subscr_id" value="{{ $encrypter->encrypt($subscr['view']->subscr_id) }}">
                                <input type="hidden" name="user_subscr_type" value="{{ $encrypter->encrypt($subscr['view']->subscr_name) }}">
                                <input type="hidden" name="user_subscr_date" value="{{ $encrypter->encrypt($subscr['view']->subscr_duration) }}">
                                <input type="hidden" name="user_subscr_item_level" value="{{ $encrypter->encrypt($subscr['view']->subscr_item_level) }}">
                                <input type="hidden" name="user_subscr_item" value="{{ $encrypter->encrypt($subscr['view']->subscr_item) }}">
                                <input type="hidden" name="user_subscr_download_item" value="{{ $encrypter->encrypt($subscr['view']->subscr_download_item) }}">
                                <input type="hidden" name="user_subscr_space_level" value="{{ $encrypter->encrypt($subscr['view']->subscr_space_level) }}">
                                <input type="hidden" name="user_subscr_space" value="{{ $encrypter->encrypt($subscr['view']->subscr_space) }}">
                                <input type="hidden" name="user_subscr_space_type" value="{{ $encrypter->encrypt($subscr['view']->subscr_space_type) }}">
                                <input type="hidden" name="token" class="token">
                                <input type="hidden" name="reference" value="{{ Paystack::genTranxRef() }}">
                                <input type="hidden" name="multicurrency" value="{{ $encrypter->encrypt($multicurrency) }}">
                         <div class="mx-auto">
                        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button></div>
                    </form>
                </div>
            </div>
        </div>
        </div>
      </div>
	</div>
@include('footer')
@include('script')
@if($view_name == 'confirm-subscription')
<!-- stripe code -->
<script src="https://js.stripe.com/v3/"></script>
@if($allsettings->stripe_type == "intents")
<script type="text/javascript">
$(function () {
'use strict';
		$("#ifYes").hide();
        $('#stripe').click(function(){
            var value = "stripe";
            $("input[name=payment_method][value=" + value + "]").prop('checked', true);
		
            if ($("#opt1-stripe").is(":checked")) {
                $("#ifYes").show();

} else {
                $("#ifYes").hide();
            }
        });
    });
</script>
@else
<script type="text/javascript">
$(function () {
'use strict';
$("#ifYes").hide();
        $("input[name='payment_method']").click(function () {
		
            if ($("#opt1-stripe").is(":checked")) {
                $("#ifYes").show();
				
				/* stripe code */
				
				var stripe = Stripe('{{ $stripe_publish_key }}');
   
				var elements = stripe.elements();
					
				var style = {
				base: {
					color: '#32325d',
					lineHeight: '18px',
					fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
					fontSmoothing: 'antialiased',
					fontSize: '14px',
					'::placeholder': {
					color: '#aab7c4'
					}
				},
				invalid: {
					color: '#fa755a',
					iconColor: '#fa755a'
				}
				};
			 
				
				var card = elements.create('card', {style: style, hidePostalCode: true});
			 
				
				card.mount('#card-element');
			 
			   
				card.addEventListener('change', function(event) {
					var displayError = document.getElementById('card-errors');
					if (event.error) {
						displayError.textContent = event.error.message;
					} else {
						displayError.textContent = '';
					}
				});
			 
				
				var form = document.getElementById('checkout_form');
				form.addEventListener('submit', function(event) {
					/*event.preventDefault();*/
			        if ($("#opt1-stripe").is(":checked")) { event.preventDefault(); }
					stripe.createToken(card).then(function(result) {
					
						if (result.error) {
						
						var errorElement = document.getElementById('card-errors');
						errorElement.textContent = result.error.message;
						
						
						} else {
							
							document.querySelector('.token').value = result.token.id;
							 
							document.getElementById('checkout_form').submit();
						}
						/*document.querySelector('.token').value = result.token.id;
							 
							document.getElementById('checkout_form').submit();*/
						
					});
				});
							
						
			/* stripe code */	
				
				
				
            } else {
                $("#ifYes").hide();
            }
        });
    });
	

</script>
@endif
<!-- stripe code -->
@endif
</body>
</html>