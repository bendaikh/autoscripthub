<!DOCTYPE HTML>
<html lang="en">
<head>
<title>{{ $allsettings->site_title }} - Test</title>
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
              <li class="breadcrumb-item text-nowrap active" aria-current="page">Test</li>
            </ol>
          </nav>
        </div>
        <div class="order-lg-1 pr-lg-4 text-center text-lg-left">
          <h1 class="h3 mb-0 text-white">Test</h1>
        </div>
      </div>
      </div>
    </section>
<div class="container py-5 mt-md-2 mb-2">
      
      <form id="payment-form">
             <input type="text" value="{{ $data['name'] }}"  id="name" name="name" required>
             <br>
            <input type="email" value="{{ $data['email'] }}" id="email" name="email" required>
            <br>
            
             
             <div id="card-element">

             </div>

             <button id="submit" class="paynow">Pay Now</button>

             <div id="card-errors" style="color: red;"></div>
             <div id="card-thank" style="color: green;"></div>
             <div id="card-message" style="color: green;"></div>
             <div id="card-success" style="color: green;font-weight:bolder"></div>
        </form>
      
    </div>
@include('footer')
@include('script')
<script src="https://js.stripe.com/v3/"></script>
<script type="text/javascript">
        
        $('#card-success').text('');
        $('#card-errors').text('');
        var stripe = Stripe('{{ $allsettings->test_publish_key }}');
        var elements = stripe.elements();
        $('#submit').prop('disabled', true);
        // Set up Stripe.js and Elements to use in checkout form
        var style = {
          base: {
            color: "#32325d",
          }
        };

        var card = elements.create("card", { style: style });
        card.mount("#card-element");


        card.addEventListener('change', ({error}) => {
          const displayError = document.getElementById('card-errors');
          if (error) {
            displayError.textContent = error.message;
            $('#submit').prop('disabled', true);

          } else {
            displayError.textContent = '';
            $('#submit').prop('disabled', false);

          }
        });

        var form = document.getElementById('payment-form');
        
        form.addEventListener('submit', function(ev) {
        $('.loading').css('display','block');

          ev.preventDefault();
          //cardnumber,exp-date,cvc
          stripe.confirmCardPayment('{{ $data["client_secret"] }}', {
            payment_method: {
              card: card,
              billing_details: {
                name: '{{ $data["name"] }}',
                email: '{{ $data["email"] }}'
              }
            },
            setup_future_usage: 'off_session'
          }).then(function(result) {
            $('.loading').css('display','none');
           
            if (result.error) {
            
              $('#card-errors').text(result.error.message);
            
            } else {
              if (result.paymentIntent.status === 'succeeded') {

            
                $('#card-success').text("payment successfully completed.");
				//window.location.href = "{{url('/success')}}";
              console.log(card);
                // setTimeout(
                //   function(){ window.location.href = "{{url('/success')}}"; 
                // }, 2000);
              }
              return false;
            }
          });
        });
    </script>
</body>
</html>