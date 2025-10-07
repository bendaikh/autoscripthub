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
          <h1 class="h3 mb-0 text-white">{{ __('Sales') }}</h1>
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
          <section class="col-lg-8 pt-lg-4 pb-4 mb-3">
            <div class="pt-2 px-4 pl-lg-0 pr-xl-5">
              <h2 class="h3 py-2 text-center text-sm-left">{{ __('Sales') }}</h2>
              <div class="row mx-n2 pt-2">
                <div class="col-md-4 col-sm-12 px-2 mb-4">
                  <div class="bg-secondary h-100 rounded-lg p-4 text-center">
                    <h3 class="font-size-sm text-muted">{{ __('total sales') }}</h3>
                    <p class="h2 mb-2">{{ Helper::plan_format($allsettings->site_currency_position,$total_sale,$allsettings->site_currency_symbol) }}</p>
                  </div>
                </div>
                <div class="col-md-4 col-sm-12 px-2 mb-4">
                  <div class="bg-secondary h-100 rounded-lg p-4 text-center">
                    <h3 class="font-size-sm text-muted">{{ __('Total Purchases') }}</h3>
                    <p class="h2 mb-2">{{ Helper::plan_format($allsettings->site_currency_position,$purchase_sale,$allsettings->site_currency_symbol) }}</p>
                  </div>
                </div>
                <div class="col-md-4 col-sm-12 px-2 mb-4">
                  <div class="bg-secondary h-100 rounded-lg p-4 text-center">
                    <h3 class="font-size-sm text-muted">{{ __('Total Credited') }}</h3>
                    <p class="h2 mb-2">{{ Helper::plan_format($allsettings->site_currency_position,$credit_amount,$allsettings->site_currency_symbol) }}</p>
                  </div>
                </div>
                <div class="col-md-4 col-sm-12 px-2 mb-4">
                  <div class="bg-secondary h-100 rounded-lg p-4 text-center">
                    <h3 class="font-size-sm text-muted">{{ __('Total Withdraw') }}</h3>
                    <p class="h2 mb-2">{{ Helper::plan_format($allsettings->site_currency_position,$drawal_amount,$allsettings->site_currency_symbol) }}</p>
                  </div>
                </div>
                @if($addition_settings->affiliate_referral == 1)
                <div class="col-md-4 col-sm-12 px-2 mb-4">
                  <div class="bg-secondary h-100 rounded-lg p-4 text-center">
                    <h3 class="font-size-sm text-muted">{{ __('Referral Commission') }}</h3>
                    <p class="h2 mb-2">{{ Helper::plan_format($allsettings->site_currency_position,Auth::user()->referral_amount,$allsettings->site_currency_symbol) }}</p>
                  </div>
                </div>
                <div class="col-md-4 col-sm-12 px-2 mb-4">
                  <div class="bg-secondary h-100 rounded-lg p-4 text-center">
                    <h3 class="font-size-sm text-muted">{{ __('Total Referrals') }}</h3>
                    <p class="h2 mb-2">{{ Auth::user()->referral_count }}</p>
                  </div>
                </div>
               @endif 
              </div>
              <div class="row mx-n2">
                <div class="col-md-12">
                        <div class="statement_table table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Order ID') }}</th>
                                        <th>{{ __('Payment Type') }}</th>
                                        <th>{{ __('Price') }}</th>
                                        <th>{{ __('Earnings') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="listShow">
                                @foreach($orderData['item'] as $item)
                                    <tr class="prod-item">
                                        <td>{{ date("d M Y", strtotime($item->payment_date)) }}</td>
                                        <td class="author">{{ $item->purchase_token }}</td>
                                        <td class="type">
                                            {{ $item->payment_type }}
                                        </td>
                                        <td>{{ Helper::plan_format($allsettings->site_currency_position,$item->total,$item->currency_type) }}</td>
                                        <td class="earning theme-color">{{ Helper::plan_format($allsettings->site_currency_position,$item->vendor_amount,$item->currency_type) }}</td>
                                        <td>
                                            <a href="{{ URL::to('/sales') }}/{{ $item->purchase_token }}" class="btn btn-success btn-sm">{{ __('view') }}</a>
                                        </td>
                                    </tr>
                                @endforeach 
                               </tbody>
                            </table>
                            <div class="pagination-area">
                           <div class="turn-page" id="itempager"></div>
                        </div>
                       </div>
                    </div>
               </div>
            </div>
          </section>
        </div>
      </div>
    </div>