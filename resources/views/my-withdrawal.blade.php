<div class="page-title-overlap pt-4" style="background-image: url('{{ url('/') }}/public/storage/settings/{{ $allsettings->site_banner }}');">
      <div class="container d-lg-flex justify-content-between py-2 py-lg-3">
        <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-light flex-lg-nowrap justify-content-center justify-content-lg-star">
              <li class="breadcrumb-item"><a class="text-nowrap" href="{{ URL::to('/') }}"><i class="dwg-home"></i>{{ __('Home') }}</a></li>
              <li class="breadcrumb-item text-nowrap active" aria-current="page">{{ __('Withdrawal') }}</li>
            </ol>
          </nav>
        </div>
        <div class="order-lg-1 pr-lg-4 text-center text-lg-left">
          <h1 class="h3 mb-0 text-white">{{ __('Withdrawal') }}</h1>
        </div>
      </div>
    </div>
<div class="container mb-5 pb-3">
      <div class="bg-light box-shadow-lg rounded-lg overflow-hidden">
        <div class="row">
          <!-- Sidebar-->
          <aside class="col-lg-4">
          <div class="d-block d-lg-none p-4">
            <a class="btn btn-outline-accent d-block" href="#account-menu" data-toggle="collapse"><i class="dwg-menu mr-2"></i>{{ __('Account menu') }}</a></div>
           @include('dashboard-menu')
          </aside>
          <!-- Content-->
          <section class="col-lg-8 pt-lg-4 pb-4 mb-3">
            <div class="pt-2 px-4 pl-lg-0 pr-xl-5">
              <h2 class="h4 py-2 text-center text-sm-left">{{ __('Minimum withdrawal amount is') }} <span class="link-color">{{ Helper::plan_format($allsettings->site_currency_position,$allsettings->site_minimum_withdrawal,$allsettings->site_currency_symbol) }}</span></h2>
              <form action="{{ route('withdrawal') }}" id="withdrawal_form" method="post" id="newsample_form" enctype="multipart/form-data">
             {{ csrf_field() }}
              <div class="row mx-n2 py-2">
                <div class="col-sm-6 px-2 mb-4">
                  <div class="bg-secondary h-100 rounded-lg p-4">
                    <h3 class="h5">{{ __('Withdrawal Options') }}</h3>
                    <div class="options">
                                @php $no = 1; @endphp
                                            @foreach($withdraw_option as $withdraw) 
                                            <div class="custom-radio">
                                                <input type="radio" id="withdrawal-{{ $withdraw }}" name="withdrawal" value="{{ $withdraw }}" data-bvalidator="required">
                                                <label for="withdrawal-{{ $withdraw }}" class="mustcaps">
                                                    {{ Helper::WithNameGet($withdraw) }}</label>
                                            </div>
                                            @php $no++; @endphp
                                            @endforeach
                                           <div class="row form-group" id="ifpaypal">
                                                <div class="col-md-12 mb-3 mb-md-0">
                                                  <label class="font-weight-bold" for="phone">{{ __('Paypal Email ID') }}</label>
                                                    <input type="text" id="paypal_email" name="paypal_email" class="form-control" data-bvalidator="email,required">
                                                </div>
                                           </div> 
                                           <div class="row form-group" id="ifstripe">
                                                      <div class="col-md-12 mb-3 mb-md-0">
                                                        <label class="font-weight-bold" for="phone">{{ __('Stripe Email ID') }}</label>
                                                        <input type="text" id="stripe_email" name="stripe_email" class="form-control" data-bvalidator="email,required">
                                                </div>
                                            </div> 
                                            <div class="row form-group" id="ifpaystack">
                                                      <div class="col-md-12 mb-3 mb-md-0">
                                                        <label class="font-weight-bold" for="phone">{{ __('Paystack Email ID') }}</label>
                                                        <input type="text" id="paystack_email" name="paystack_email" class="form-control" data-bvalidator="email,required">
                                                </div>
                                            </div>
                                            <div class="row form-group" id="ifpayfast">
                                                      <div class="col-md-12 mb-3 mb-md-0">
                                                        <label class="font-weight-bold" for="phone">{{ __('PayFast Email ID') }}</label>
                                                        <input type="text" id="payfast_email" name="payfast_email" class="form-control" data-bvalidator="email,required">
                                                </div>
                                            </div>
                                            <div class="row form-group" id="ifpaytm">
                                                      <div class="col-md-12 mb-3 mb-md-0">
                                                        <label class="font-weight-bold" for="phone">{{ __('Paytm Number') }}</label>
                                                        <input type="text" id="paytm_no" name="paytm_no" class="form-control" data-bvalidator="required">
                                                </div>
                                            </div>
                                            <div class="row form-group" id="ifupi">
                                                      <div class="col-md-12 mb-3 mb-md-0">
                                                        <label class="font-weight-bold" for="phone">{{ __('UPI ID') }}</label>
                                                        <input type="text" id="upi_id" name="upi_id" class="form-control" data-bvalidator="required">
                                                </div>
                                            </div>
                                            <div class="row form-group" id="ifskrill">
                                                      <div class="col-md-12 mb-3 mb-md-0">
                                                        <label class="font-weight-bold" for="phone">{{ __('Skrill Email ID') }}</label>
                                                        <input type="text" id="skrill_email" name="skrill_email" class="form-control" data-bvalidator="email,required">
                                                </div>
                                            </div>
                                            <div class="row form-group" id="iflocalbank">
                                                      <div class="col-md-12 mb-3 mb-md-0">
                                                        <label class="font-weight-bold" for="phone">{{ __('Bank Details') }}</label>
                                                        <textarea id="bank_details" name="bank_details" class="form-control" data-bvalidator="required"></textarea>
                                                        <small><strong>{{ __('example') }}:</strong><br/>
                                                        {{ __('Bank Name') }} : {{ __('Test Bank') }}<br/>
                                                        {{ __('Branch Name') }} : {{ __('Test Branch') }}<br/>
                                                        {{ __('Branch Code') }} : 00000<br/>
                                                        {{ __('IFSC Code') }} : 63632EF</small>
                                                </div>
                                            </div>
                                            <div class="row form-group" id="ifcrypto">
                                                      <div class="col-md-12 mb-3 mb-md-0">
                                                        <label class="font-weight-bold" for="phone">{{ __('Crypto Address') }}</label>
                                                        <textarea id="crypto_address" name="crypto_address" class="form-control" data-bvalidator="required"></textarea><br/>
                                                        <label class="font-weight-bold">{{ __('Instruction') }} :</label><br/>
                                                        <small>{{ __('Drop Your Crypto Name + Crypto Address') }}</small><br/><br/>
                                                        <label class="font-weight-bold captal-letter">{{ __('example') }} :</label><br/>
                                                        <small>BTC: 1BK19vqb2uRwPUm6PjMmMyfJgcBa9YEtpG</small><br/><br/>
                                                        <label class="font-weight-bold captal-letter">{{ __('Note') }} :</label><br/>
                                                        <small>{{ __('Support') }} (BTC, LTC, ETH)</small>
                                                </div>
                                            </div>
                                        </div>
                  </div>
                </div>
                <div class="col-sm-6 px-2 mb-4">
                  <div class="bg-secondary h-100 rounded-lg p-4">
                    <h3 class="h5">{{ __('Withdraw Amount') }}</h3>
                    <div class="d-flex flex-wrap align-items-center py-1 mb-2">
                    <p class="subtitle">{{ __('How much amount would you like to Withdraw?') }}</p>
                    <div class="options">
                                 <div>
                                  <label>
                                        <span class="circle"></span>{{ __('Available balance') }}
                                                    <span class="bold">{{ Helper::plan_format($allsettings->site_currency_position,Auth::user()->earnings,$allsettings->site_currency_symbol) }} </span>
                                                </label>
                                            </div>
                                            <input type="hidden" name="available_balance" value="{{ base64_encode(Auth::user()->earnings) }}">
                                            <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                                            <input type="hidden" name="user_token" value="{{ Auth::user()->user_token }}">
                                            <div class="row form-group" id="ifstripe">
                                                      <div class="col-md-12 mb-3 mb-md-0">
                                                        <label class="font-weight-bold" for="phone">{{ $allsettings->site_currency }}</label>
                                                    <input type="text" id="rlicense" name="get_amount" class="form-control" data-bvalidator="number,min[{{ $allsettings->site_minimum_withdrawal }}],max[{{ Auth::user()->earnings }}],required">
                                                </div>
                                            </div>
                                       </div>
                                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">{{ __('Submit Withdrawal') }}</button>
                  </div>
                </div>
              </div>
              </form>
              <h3 class="h5 pb-2">{{ __('Withdrawal History') }}</h3>
              <div class="table-responsive">
                <table class="table table-fixed font-size-sm mb-0">
                  <thead>
                    <tr>
                       <th>{{ __('Date') }}</th>
                       <th>{{ __('Withdrawal Options') }}</th>
                       <th>{{ __('Email') }} / {{ __('Bank Details') }} / {{ __('UPI') }} / {{ __('Paytm') }} / {{ __('Crypto Address') }}</th>
                       <th>{{ __('Amount') }}</th>
                       <th>{{ __('Status') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($itemData['item'] as $withdrawal)
                                        <tr>
                                            <td>{{ date('d M Y', strtotime($withdrawal->wd_date)) }}</td>
                                            <td>{{ $withdrawal->withdraw_type }}</td>
                                            <td class="wrapped">
                                            @if($withdrawal->paypal_email != ""){{ $withdrawal->paypal_email }}@endif
                                            @if($withdrawal->stripe_email != ""){{ $withdrawal->stripe_email }}@endif
                                            @if($withdrawal->paystack_email != ""){{ $withdrawal->paystack_email }}@endif
                                            @if($withdrawal->payfast_email != ""){{ $withdrawal->payfast_email }}@endif
                                            @if($withdrawal->skrill_email != ""){{ $withdrawal->skrill_email }}@endif
                                            @if($withdrawal->upi_id != ""){{ $withdrawal->upi_id }}@endif
                                            @if($withdrawal->paytm_no != ""){{ $withdrawal->paytm_no }}@endif
                                            @if($withdrawal->bank_details != "") @php echo nl2br($withdrawal->bank_details); @endphp @endif
                                            @if($withdrawal->crypto_address != ""){{ $withdrawal->crypto_address }}@endif
                                            </td>
                                            <td class="bold">{{ Helper::plan_format($allsettings->site_currency_position,$withdrawal->wd_amount,$allsettings->site_currency_symbol) }}</td>
                                           <td><span class="@if($withdrawal->wd_status == 'pending') wpending @else wpaid @endif">{{ $withdrawal->wd_status }}</span>
                                       </td>
                                    </tr>
                          @endforeach
                  </tbody>
                </table>
              </div>
             </div>
          </section>
        </div>
      </div>
    </div>