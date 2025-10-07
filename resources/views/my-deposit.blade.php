<div class="page-title-overlap pt-4" style="background-image: url('{{ url('/') }}/public/storage/settings/{{ $allsettings->site_banner }}');">
      <div class="container d-lg-flex justify-content-between py-2 py-lg-3">
        <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb flex-lg-nowrap justify-content-center justify-content-lg-star">
              <li class="breadcrumb-item"><a class="text-nowrap" href="{{ URL::to('/') }}"><i class="dwg-home"></i>{{ __('Home') }}</a></li>
              <li class="breadcrumb-item text-nowrap active" aria-current="page">{{ __('Deposit') }}</li>
              </li>
             </ol>
          </nav>
        </div>
        <div class="order-lg-1 pr-lg-4 text-center text-lg-left">
          <h1 class="h3 mb-0 text-white">{{ __('Deposit') }}</h1>
        </div>
      </div>
    </div>
<div class="container mb-5 pb-3">
      <div class="bg-light box-shadow-lg rounded-lg overflow-hidden">
        <div class="row">
          <!-- Sidebar-->
          <aside class="col-lg-4">
            <!-- Account menu toggler (hidden on screens larger 992px)-->
            <div class="d-block d-lg-none p-4">
            <a class="btn btn-outline-accent d-block" href="#account-menu" data-toggle="collapse"><i class="dwg-menu mr-2"></i>{{ __('Account menu') }}</a></div>
            <!-- Actual menu-->
            @if(Auth::user()->id != 1)
            @include('dashboard-menu')
            @endif
          </aside>
          <!-- Content-->
          <section class="@if(Auth::user()->id == 1) col-lg-12 pl-4 @else col-lg-8 @endif pt-lg-4 pb-4 mb-3">
            <div class="pt-2 px-4 pl-lg-0 pr-xl-5">
              <form action="{{ route('deposit') }}" class="setting_form" id="checkout_form" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input type="hidden" name="order_firstname" value="{{ Auth::user()->name }}"> 
        <input type="hidden" name="order_email" value="{{ Auth::user()->email }}">
        <input type="hidden" name="website_url" value="{{ url('/') }}">
        <input type="hidden" name="token" class="token">
        <input type="hidden" name="reference" value="{{ Paystack::genTranxRef() }}">
        <input type="hidden" name="currency_rate" value="{{ $encrypter->encrypt($currency_rate) }}">
        <input type="hidden" name="currency_type" value="{{ $encrypter->encrypt($currency_symbol) }}">
        <input type="hidden" name="currency_type_code" value="{{ $encrypter->encrypt($multicurrency) }}">
        <div class="dashboard_contents dashboard_statement_area newcontent">
            <div id="boxradio">
                 <section>
                   <div class="container">
                            <div class="row">
                            @php $q = 1; @endphp  
                            @foreach($deposit['view'] as $deposit)
                            @php $priceval = $deposit->deposit_price .'_'.$deposit->deposit_bonus; @endphp
                            <div class="col-lg-3 col-md-3">
                              <input type="radio" id="control_{{ $deposit->dep_id }}" name="amount" value="{{ $encrypter->encrypt($priceval) }}" @if($q==1) checked @endif>
                              <label for="control_{{ $deposit->dep_id }}">
                                <h2>{{ Helper::price_format($allsettings->site_currency_position,$deposit->deposit_price,$currency_symbol,$multicurrency) }}</h2>
                                <p>@if($deposit->deposit_bonus != 0) {{ '+'.Helper::price_value($deposit->deposit_bonus,$multicurrency) }} {{ $multicurrency }} {{ __('Bonus') }} @else {{ __('No Bonus') }}@endif</p>
                              </label>
                            </div>
                            @php $q++; @endphp
                            @endforeach
                            </div>
                            
                        </div>
                      </section>
                 </div>
            <!-- end /.container -->
        </div>
        <div class="dashboard_statement_area">
                 <div class="container">
                     <div class="row">
                         <div class="col-lg-12">
                         <div class="information_module payment_options">
                                <div class="toggle_title">
                                    <h4>{{ __('Select Payment Method') }}</h4>
                                </div>
                <div class="accordion mb-2" id="payment-method" role="tablist">
                @php $no = 1; @endphp
                @foreach($get_payment as $payment)
                @php 
                if($payment == '2checkout')
                {
                $payment = 'twocheckout';
                }
                else
                {
                $payment = $payment;
                }
                @endphp
                @if($payment != 'wallet')
                <div class="card">
                  <div class="card-header" role="tab">
                    <h3 class="accordion-heading"><a href="#{{ $payment }}" id="{{ $payment }}" data-toggle="collapse">{{ __('Pay with') }} @if($payment == 'twocheckout') {{ __('2Checkout') }} @else {{ $payment }} @endif<span class="accordion-indicator"><i data-feather="chevron-up"></i></span></a></h3>
                  </div>
                  <div class="collapse @if($no == 1) show @endif" id="{{ $payment }}" data-parent="#payment-method" role="tabpanel">
                  @if($payment == 'paypal')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" @if($no == 1) checked @endif data-bvalidator="required"> {{ __('PayPal') }}</span> - {{ __('the safer, easier way to pay') }}</p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with PayPal') }}</button>
                    </div>
                    @endif
                  @if($payment == 'stripe')
                    <div class="card-body font-size-sm custom-radio custom-control">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio"  value="{{ $payment }}" data-bvalidator="required"> {{ __('Stripe') }}</span> - {{ __('Credit or debit card') }}</p>
                      @if($allsettings->stripe_type == 'charges')
                      <div class="stripebox mb-3" id="ifYes" style="display:none;">
                        <label for="card-element">{{ __('Credit or debit card') }}</label>
                        <div id="card-element"></div>
                        <div id="card-errors" role="alert"></div>
                      </div>
                      @endif
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Stripe') }}</button>
                    </div> 
                    @endif
                    @if($payment == 'twocheckout')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('2Checkout') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with 2Checkout') }}</button>
                    </div>
                    @endif
                    @if($payment == 'paystack')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('PayStack') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with PayStack') }}</button>
                    </div>
                    @endif
                    @if($payment == 'razorpay')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('Razorpay') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Razorpay') }}</button>
                    </div>
                    @endif
                    @if($payment == 'payhere')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('Payhere') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Payhere') }}</button>
                    </div>
                    @endif
                    @if($payment == 'payumoney')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('Payumoney') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Payumoney') }}</button>
                    </div>
                    @endif
                    @if($payment == 'iyzico')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('Iyzico') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Iyzico') }}</button>
                    </div>
                    @endif
                    @if($payment == 'flutterwave')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('Flutterwave') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Flutterwave') }}</button>
                    </div>
                    @endif
                    @if($payment == 'coingate')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('Coingate') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Coingate') }}</button>
                    </div>
                    @endif
                    @if($payment == 'ipay')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('iPay') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with iPay') }}</button>
                    </div>
                    @endif
                    @if($payment == 'payfast')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('PayFast') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with PayFast') }}</button>
                    </div>
                    @endif
                    @if($payment == 'coinpayments')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('CoinPayments') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with CoinPayments') }}</button>
                    </div>
                    @endif
                    @if($payment == 'sslcommerz')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('SSLCommerz') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with SSLCommerz') }}</button>
                    </div>
                    @endif
                    @if($payment == 'instamojo')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('Instamojo') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Instamojo') }}</button>
                    </div>
                    @endif
                    @if($payment == 'aamarpay')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('Aamarpay') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Aamarpay') }}</button>
                    </div>
                    @endif
                    @if($payment == 'mollie')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('Mollie') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Mollie') }}</button>
                    </div>
                    @endif
                    @if($payment == 'robokassa')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('Robokassa') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Robokassa') }}</button>
                    </div>
                    @endif
                    @if($payment == 'mercadopago')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('Mercadopago') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Mercadopago') }}</button>
                    </div>
                    @endif
                    @if($payment == 'midtrans')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('Midtrans') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Midtrans') }}</button>
                    </div>
                    @endif
                    @if($payment == 'coinbase')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('Coinbase') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Coinbase') }}</button>
                    </div>
                    @endif
                    @if($payment == 'localbank')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('Localbank') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Localbank') }}</button>
                    </div>
                    @endif
                    @if($payment == 'cashfree')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('Cashfree') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Cashfree') }}</button>
                    </div>
                    @endif
                    @if($payment == 'nowpayments')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('NowPayments') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with NowPayments') }}</button>
                    </div>
                    @endif
                   </div>
                </div>
                @endif
                @php $no++; @endphp
                @endforeach
              </div>
              </div>
            </div>
           </div>
        </div>
        </div>
       </form>
        </div>
          </section>
        </div>
      </div>
    </div>