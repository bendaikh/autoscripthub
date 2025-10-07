<div class="page-title-overlap pt-4" style="background-image: url('{{ url('/') }}/public/storage/settings/{{ $allsettings->site_banner }}');">
      <div class="container d-lg-flex justify-content-between py-2 py-lg-3">
        <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb flex-lg-nowrap justify-content-center justify-content-lg-star">
              <li class="breadcrumb-item"><a class="text-nowrap" href="{{ URL::to('/') }}"><i class="dwg-home"></i>{{ __('Home') }}</a></li>
              <li class="breadcrumb-item text-nowrap active" aria-current="page">{{ __('Purchases') }}</li>
              </li>
             </ol>
          </nav>
        </div>
        <div class="order-lg-1 pr-lg-4 text-center text-lg-left">
          <h1 class="h3 mb-0 text-white">{{ __('Purchases') }}</h1>
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
          @if(count($orderData['item']) != 0)
          <section class="col-lg-8 pt-lg-4 pb-4 mb-3">
            <div class="pt-2 px-4 pl-lg-0 pr-xl-5">
              <div class="row mx-n2 pt-2">
                @if(Auth::user()->user_type == 'customer')
                <div class="col-md-3 col-sm-12 px-2 mb-4">
                  <div class="bg-secondary h-100 rounded-lg p-4 text-center">
                    <h3 class="font-size-sm text-muted">{{ __('Total Purchases') }}</h3>
                    <p class="h2 mb-2">{{ Helper::plan_format($allsettings->site_currency_position,$purchase_sale,$currency_symbol) }}</p>
                  </div>
                </div>
                <div class="col-md-3 col-sm-12 px-2 mb-4">
                  <div class="bg-secondary h-100 rounded-lg p-4 text-center">
                    <h3 class="font-size-sm text-muted">{{ __('Total Withdraw') }}</h3>
                    <p class="h2 mb-2">{{ Helper::plan_format($allsettings->site_currency_position,$drawal_amount,$currency_symbol) }}</p>
                  </div>
                </div>
                <div class="col-md-3 col-sm-12 px-2 mb-4">
                  <div class="bg-secondary h-100 rounded-lg p-4 text-center">
                    <h3 class="font-size-sm text-muted">{{ __('Referral Commission') }}</h3>
                    <p class="h2 mb-2">{{ Helper::plan_format($allsettings->site_currency_position,Auth::user()->referral_amount,$currency_symbol) }}</p>
                  </div>
                </div>
                <div class="col-md-3 col-sm-12 px-2 mb-4">
                  <div class="bg-secondary h-100 rounded-lg p-4 text-center">
                    <h3 class="font-size-sm text-muted">{{ __('Total Referrals') }}</h3>
                    <p class="h2 mb-2">{{ Helper::plan_format($allsettings->site_currency_position,Auth::user()->referral_count,$currency_symbol) }}</p>
                  </div>
                </div>
                @endif
              </div>
              @foreach($orderData['item'] as $item)
              <div class="media d-block d-sm-flex align-items-center py-4 border-bottom prod-item">
              <a class="d-block mb-3 mb-sm-0 mr-sm-4 mx-auto" href="{{ url('/item') }}/{{ $item->item_slug }}" style="width: 12.5rem;">
              @if($item->item_thumbnail!='')
              <img class="lazy rounded-lg purchase-img" width="200" height="155" src="{{ Helper::Image_Path($item->item_thumbnail,'no-image.png') }}"  alt="{{ $item->item_name }}">
              @else
              <img class="lazy rounded-lg purchase-img" width="200" height="155" src="{{ url('/') }}/public/img/no-image.png"  alt="{{ $item->item_name }}">
              @endif
              </a>
                <div class="d-block mb-3 mb-sm-0 mr-sm-4 mx-auto">
                  <h3 class="h6 product-title mb-2"><a href="{{ url('/item') }}/{{ $item->item_slug }}">{{ $item->item_name }}</a></h3>
                  <div class="font-size-sm"><strong>{{ __('Price') }}:</strong> {{ Helper::plan_format($allsettings->site_currency_position,$item->item_price,$item->currency_type) }}</div>
                  <div class="d-flex align-items-center justify-content-center justify-content-sm-start">
                   @if($item->approval_status != 'payment released to customer')
                   @if($item->approval_status == 'payment released to vendor')
                    @if($item->rating != 0)
                    <a class="d-block text-muted text-center my-2" href="javascript:void(0);" data-toggle="modal" data-target="#myModal_{{ $item->ord_id }}">
                      <div class="star-rating">
                      @if($item->rating == 1)
                      <i class="sr-star dwg-star-filled active"></i>
                      <i class="sr-star dwg-star"></i>
                      <i class="sr-star dwg-star"></i>
                      <i class="sr-star dwg-star"></i>
                      <i class="sr-star dwg-star"></i>
                      @endif
                      @if($item->rating == 2)
                      <i class="sr-star dwg-star-filled active"></i>
                      <i class="sr-star dwg-star-filled active"></i>
                      <i class="sr-star dwg-star"></i>
                      <i class="sr-star dwg-star"></i>
                      <i class="sr-star dwg-star"></i>
                      @endif
                      @if($item->rating == 3)
                      <i class="sr-star dwg-star-filled active"></i>
                      <i class="sr-star dwg-star-filled active"></i>
                      <i class="sr-star dwg-star-filled active"></i>
                      <i class="sr-star dwg-star"></i>
                      <i class="sr-star dwg-star"></i>
                      @endif
                      @if($item->rating == 4)
                      <i class="sr-star dwg-star-filled active"></i>
                      <i class="sr-star dwg-star-filled active"></i>
                      <i class="sr-star dwg-star-filled active"></i>
                      <i class="sr-star dwg-star-filled active"></i>
                      <i class="sr-star dwg-star"></i>
                      @endif
                      @if($item->rating == 5)
                      <i class="sr-star dwg-star-filled active"></i>
                      <i class="sr-star dwg-star-filled active"></i>
                      <i class="sr-star dwg-star-filled active"></i>
                      <i class="sr-star dwg-star-filled active"></i>
                      <i class="sr-star dwg-star-filled active"></i>
                      @endif
                      </div>
                      <div class="font-size-xs">{{ __('Rate this product') }}</div>
                      </a>
                      @else
                      <a class="d-block text-muted text-center my-2" href="javascript:void(0);" data-toggle="modal" data-target="#myModal_{{ $item->ord_id }}">
                      <div class="star-rating">
                      <i class="sr-star dwg-star"></i>
                      <i class="sr-star dwg-star"></i>
                      <i class="sr-star dwg-star"></i>
                      <i class="sr-star dwg-star"></i>
                      <i class="sr-star dwg-star"></i>
                      </div>
                      <div class="font-size-xs">{{ __('Rate this product') }}</div>
                      </a>
                      @endif
                      @endif
                      @endif
                  </div>
                  @if($item->approval_status != 'payment released to customer')
                  <div class="d-flex mt-2 pt-2">
                  @if($item->item_order_serial_key != "")
                  <a href="{{ url('/purchases') }}/{{ $item->item_token }}/{{ $item->ord_id }}" class="btn btn-success btn-sm mr-3"><i class="dwg-download mr-1"></i>{{ __('Download Serial Key') }}</a>
                  @else
                  <a href="{{ url('/purchases') }}/{{ $item->item_token }}/{{ $item->ord_id }}" class="btn btn-success btn-sm mr-3"><i class="dwg-download mr-1"></i>{{ __('Download Item') }}</a>
                  @endif
                  <a href="{{ url('/invoice') }}/{{ $item->item_token }}/{{ $item->ord_id }}" class="btn btn-primary btn-sm mr-3"><i class="dwg-download mr-1"></i>{{ __('Invoice') }}</a><br/>
                  </div>
                  @endif
                </div>
                <div class="d-block mb-3 mb-sm-0 mr-sm-4 mx-auto">
                <div class="font-size-sm mb-1"><strong>{{ __('Order ID') }} : </strong> {{ $item->purchase_token }}</div>
                @if($item->item_order_serial_key != "")
                <div class="font-size-sm mb-1"><strong>{{ __('Purchased Serial Key') }} : </strong> {{ $item->item_serial_stock }}</div>
                @endif
                <?php /*?><div class="font-size-sm mb-1"><strong>{{ __('Purchase Id') }}:</strong> {{ $item->purchase_token }}</div><?php */?>
                <div class="font-size-sm mb-1"><strong>{{ __('Purchase Date') }} : </strong> {{ date("d F Y", strtotime($item->start_date)) }}</div>
                <div class="font-size-sm mb-1"><strong>{{ __('Expiry Date') }} : </strong> {{ date("d F Y", strtotime($item->end_date)) }}</div>
                <div class="font-size-sm mb-1"><strong>{{ __('License') }} : </strong> {{ $item->license }}</div>
                @php
                $moneyback_days = '+'.$item->seller_money_back_days.' days';
                $getdate = strtotime($item->start_date. $moneyback_days);
                $today_date = date('Y-m-d');
                $todays=strtotime($today_date);
                @endphp
                @if($addition_settings->refund_mode == 1)
                  @if($item->approval_status != 'payment released to customer')
                  @if($item->seller_money_back == 1)
                  @if($todays <= $getdate)
                  <div class="font-size-sm mb-1"><strong>{{ __('Refund Request') }}</strong> <a href="javascript:void(0);" data-toggle="modal" data-target="#refund_{{ $item->ord_id }}"> {{ __('Send Request') }}</a></div>
                  @endif
                  @endif
                  @endif
                @endif  
                  <?php /*?><a href="{{ url('/conversation-to-vendor') }}/{{ $item->username }}/{{ $encrypter->encrypt($item->ord_id) }}" class="btn btn-info btn-sm"><i class="dwg-chat mr-1"></i> {{ __('Start Conversation') }} ({{ $countdata->has($item->ord_id) ? count($countdata[$item->ord_id]) : 0 }})</a><?php */?>
                </div>
              </div>
              <div class="modal fade" id="myModal_{{ $item->ord_id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">{{ __('Rating this Item') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
       <form action="{{ route('purchases') }}" method="post" id="profile_form" enctype="multipart/form-data">
      {{ csrf_field() }} 
      <div class="modal-body">
                    <input type="hidden" name="item_id" value="{{ $item->item_id }}">
                    <input type="hidden" name="ord_id" value="{{ $item->ord_id }}">
                    <input type="hidden" name="item_token" value="{{ $item->item_token }}">
                    <input type="hidden" name="user_id" value="{{ $item->user_id }}">
                    <input type="hidden" name="item_user_id" value="{{ $item->item_user_id }}">
                    <input type="hidden" name="item_url" value="{{ url('/item') }}/{{ $item->item_slug }}">
          <div class="form-group">
            <label for="recipient-name" class="col-form-label">{{ __('Your Rating') }}</label>
            <select name="rating" class="form-control" required>
                                        <option value="1" @if($item->rating == 1) selected @endif>1</option>
                                        <option value="2" @if($item->rating == 2) selected @endif>2</option>
                                        <option value="3" @if($item->rating == 3) selected @endif>3</option>
                                        <option value="4" @if($item->rating == 4) selected @endif>4</option>
                                        <option value="5" @if($item->rating == 5) selected @endif>5</option>
                                    </select>
          </div>
          <div class="form-group">
            <label for="message-text" class="col-form-label">{{ __('Rating Reason') }}</label>
           <select name="rating_reason" class="form-control" required>
                                            <option value="Code Quality" @if($item->rating_reason == 'Code Quality') selected @endif>Code Quality</option>
                                            <option value="Design Quality" @if($item->rating_reason == 'Design Quality') selected @endif>{{ __('Design Quality') }}</option>
                                            <option value="Customizability" @if($item->rating_reason == 'Customizability') selected @endif>{{ __('Customizability') }}</option>
                                            <option value="Customer Support" @if($item->rating_reason == 'Customer Support') selected @endif>{{ __('Customer Support') }}</option>
                                            <option value="Performance" @if($item->rating_reason == 'Performance') selected @endif>{{ __('Performance') }}</option>
                                            <option value="Documentation Quality" @if($item->rating_reason == 'Documentation Quality') selected @endif>{{ __('Documentation Quality') }}</option>
                                            <option value="Feature Availability" @if($item->rating_reason == 'Feature Availability') selected @endif>{{ __('Feature Availability') }}</option>
                                            <option value="Flexibility" @if($item->rating_reason == 'Flexibility') selected @endif>{{ __('Flexibility') }}</option>
                                            <option value="Bugs" @if($item->rating_reason == 'Bugs') selected @endif>{{ __('Bugs') }}</option>
                                            <option value="Other" @if($item->rating_reason == 'Other') selected @endif>{{ __('Other') }}</option>
          </select>
          </div>
          <div class="form-group">
          <label for="message-text" class="col-form-label">{{ __('Comments') }}</label>
          <textarea name="rating_comment" id="rating_comment" class="form-control" required>{{ $item->rating_comment }}</textarea>
                            <p>{{ __('Your review will be ​publicly visible​ and the vendor may reply to your comments.') }}</p>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" class="btn btn-primary btn-sm">{{ __('Submit Rating') }}</button>
      </div>
      </form>
    </div>
  </div>
</div>
<div class="modal fade" id="refund_{{ $item->ord_id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">{{ __('Refund Request') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('refund') }}" method="post" id="profile_form" enctype="multipart/form-data">
      {{ csrf_field() }}
      <div class="modal-body">
          <input type="hidden" name="item_id" value="{{ $item->item_id }}">
                    <input type="hidden" name="ord_id" value="{{ $item->ord_id }}">
                    <input type="hidden" name="purchased_token" value="{{ $item->purchase_token }}">
                    <input type="hidden" name="item_token" value="{{ $item->item_token }}">
                    <input type="hidden" name="user_id" value="{{ $item->user_id }}">
                    <input type="hidden" name="item_user_id" value="{{ $item->item_user_id }}">
                    <input type="hidden" name="item_url" value="{{ url('/item') }}/{{ $item->item_slug }}">
          <div class="form-group">
            <label for="recipient-name" class="col-form-label">{{ __('Refund Reason') }}</label>
            <select name="refund_reason" class="form-control" required>
                             <option value="{{ __('Item is not as described or the item does not work the way it should') }}">{{ __('Item is not as described or the item does not work the way it should') }}</option>
                                            <option value="{{ __('Item has a security vulnerability') }}">{{ __('Item has a security vulnerability') }}</option>
                                            <option value="{{ __('Item support is promised but not provided') }}">{{ __('Item support is promised but not provided') }}</option>
                                            <option value="{{ __('Item support extension not used') }}">{{ __('Item support extension not used') }}</option>
                                            <option value="{{ __('Items that have not been downloaded') }}">{{ __('Items that have not been downloaded') }}</option>
                                        </select>
          </div>
          <div class="form-group">
            <label for="message-text" class="col-form-label">{{ __('Comments') }}</label>
            <textarea name="refund_comment" id="refund_comment" class="form-control" required></textarea>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" class="btn btn-primary btn-sm">{{ __('Submit Request') }}</button>
      </div>
      </form>
    </div>
  </div>
 </div>
     @endforeach
      <!-- Product-->
       </div>
       <div class="pagination-area">
        <div class="turn-page" id="itempager"></div>
         </div>
          </section>
          @else
          <section class="col-lg-8 pt-lg-4 pb-4 mb-3">
             <div class="pt-2 px-4 pl-lg-0 pr-xl-5" align="center">
             {{ __('No Data Found!') }}
             </div>
             </section>
              @endif
        </div>
      </div>
    </div>