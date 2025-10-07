<!DOCTYPE HTML>
<html lang="en">
<head>
<title>{{ $allsettings->site_title }} - {{ __('Order Details') }}</title>
@include('meta')
@include('style')
</head>
<body>
@include('header')
@if(Auth::user()->user_type == 'vendor')
<div class="page-title-overlap pt-4" style="background-image: url('{{ url('/') }}/public/storage/settings/{{ $allsettings->site_banner }}');">
      <div class="container d-lg-flex justify-content-between py-2 py-lg-3">
        <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb flex-lg-nowrap justify-content-center justify-content-lg-star">
              <li class="breadcrumb-item"><a class="text-nowrap" href="{{ URL::to('/') }}"><i class="dwg-home"></i>{{ __('Sales') }}</a></li>
              <li class="breadcrumb-item text-nowrap active" aria-current="page">{{ __('Sales') }}</li>
              </li>
             </ol>
          </nav>
        </div>
        <div class="order-lg-1 pr-lg-4 text-center text-lg-left">
          <h1 class="h3 mb-0 text-white">{{ __('Order Details') }}</h1>
        </div>
      </div>
    </div>
<div class="container mb-5 pb-3">
      <div class="bg-light box-shadow-lg rounded-lg overflow-hidden">
        <div class="row">
          <aside class="col-lg-4">
            <div class="d-block d-lg-none p-4">
            <a class="btn btn-outline-accent d-block" href="#account-menu" data-toggle="collapse"><i class="dwg-menu mr-2"></i>{{ __('Account menu') }}</a></div>
            @if(Auth::user()->id != 1)
            @include('dashboard-menu')
            @endif
          </aside>
          <section class="col-lg-8 pt-lg-4 pb-4 mb-3" id="printable">
            <div class="pt-2 px-4 pl-lg-0 pr-xl-5">
              <div class="row mx-n2 pt-2">
              <div class="col pull-left">
                                <div class="dashboard__title">
                                    <h3>{{ __('Order Details') }}</h3>
                                </div>
                            </div>
                <div class="col pull-right">
                   <a href="javascript:void(0);" class="btn btn-success btn-sm theme-button print">{{ __('Print') }}</a>
                </div>
              </div>
              <div class="row mt-3 pt-3 mb-3">
                    <div class="invoice_logo col pull-left">
                                    <img class="lazy" width="200" height="56" src="{{ url('/') }}/public/storage/settings/{{ $allsettings->site_logo }}"  alt="">
                                </div>
                                <div class="info col pull-right">
                                    <h4>{{ __('Order info') }}</h4>
                                    <p>{{ __('Order') }} #{{ $checkout['view']->purchase_token }}</p>
                                </div>
                        </div>
                        <hr/>
                        <div class="row mt-3 pt-3">
                                <div class="address col pull-left">
                                    <h5 class="bold">{{ $checkout['view']->order_firstname }} {{ $checkout['view']->order_lastname }}</h5>
                                    <p>{{ $checkout['view']->order_address }}</p>
                                    <p>{{ $checkout['view']->order_city }}, {{ $checkout['view']->order_zipcode }}</p>
                                    <p>{{ $checkout['view']->order_country }}</p>
                                </div>
                                <div class="date_info col pull-right">
                                    <p>
                                     <span>{{ __('Purchased Date') }} : </span>{{ date("d M Y", strtotime($checkout['view']->payment_date)) }}</p>
                                     <p class="status">
                                     <span>{{ __('Status') }} : </span><span @if($checkout['view']->payment_status == 'completed') class="badge badge-success" @else class="badge badge-danger" @endif>{{ $checkout['view']->payment_status }}</span></p>
                                </div>
                                </div>
                         <div class="row mt-3 pt-3">       
                         <div class="invoice">
                            <div class="invoice__detail">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Purchase Date') }}</th>
                                                <th>{{ __('Expiry Date') }}</th>
                                                <th>{{ __('Item') }} & {{ __('Buyer') }}</th>
                                                <th>{{ __('Payment Type') }}</th>
                                                <th>{{ __('Price') }}</th>
                                                <th>{{ __('Earnings') }}</th>
                                                <th>{{ __('Payment Status') }}</th>
                                                <?php /*?><th>{{ __('Conversation') }}</th><?php */?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @php $earn = 0; @endphp
                                        @foreach($order['view'] as $order)
                                        <tr>
                                                <td>{{ date("d M Y", strtotime($order->start_date)) }}</td>
                                                <td>{{ date("d M Y", strtotime($order->end_date)) }}</td>
                                                <td class="detail">
                                                    <a href="{{ URL::to('/item') }}/{{ $order->item_slug }}" class="theme-color">
													@if($addition_settings->item_name_limit != 0)
												    {{ mb_substr($order->item_name,0,$addition_settings->item_name_limit,'utf-8').'...' }}
												    @else
												    {{ $order->item_name }}	  
												    @endif
													</a> {{ __('by') }} <a href="{{ URL::to('/user') }}/{{ $order->username }}" class="theme-color">{{ $order->username }}</a>
                                                </td>
                                                <td>{{ $order->payment_type }}</td>
                                                <td>{{ Helper::plan_format($allsettings->site_currency_position,$order->item_price,$currency_sign) }}</td>
                                                <td>{{ Helper::plan_format($allsettings->site_currency_position,$order->vendor_amount,$currency_sign) }}</td>
                                                <td @if($order->approval_status == 'payment released to buyer') class="red-clr" @else class="green-clr" @endif>{{ $order->approval_status }}</td>
                                                <?php /*?><td><a href="{{ url('/conversation-to-buyer') }}/{{ $order->username }}/{{ $encrypter->encrypt($order->ord_id) }}" class="btn btn-primary btn-sm">Start Conversation ({{ $countdata->has($order->ord_id) ? count($countdata[$order->ord_id]) : 0 }})</a></td><?php */?>
                                            </tr>
                                            @php $earn += $order->vendor_amount; @endphp
                                        @endforeach    
                                       </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                   </div>
                   <hr/>
                   <div class="row mt-3 pt-3">
                   <div class="pricing_info col pull-right">
                        <p>{{ __('Sub Total') }} : {{ Helper::plan_format($allsettings->site_currency_position,$earn,$currency_sign) }}</p>
                        <p class="bold">{{ __('Total') }} : {{ Helper::plan_format($allsettings->site_currency_position,$earn,$currency_sign) }}</p>
                  </div>
                  </div>
               </div>
          </section>
        </div>
      </div>
    </div>
    @else
    @include('not-found')
    @endif
@include('footer')
@include('script')
</body>
</html>