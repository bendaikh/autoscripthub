<div class="page-title-overlap pt-4" style="background-image: url('{{ url('/') }}/public/storage/settings/{{ $allsettings->site_banner }}');">
      <div class="container d-lg-flex justify-content-between py-2 py-lg-3">
        <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb flex-lg-nowrap justify-content-center justify-content-lg-star">
              <li class="breadcrumb-item"><a class="text-nowrap" href="{{ URL::to('/') }}"><i class="dwg-home"></i>{{ __('Home') }}</a></li>
              <li class="breadcrumb-item text-nowrap active" aria-current="page">{{ __('Add Coupon') }}</li>
              </li>
             </ol>
          </nav>
        </div>
        <div class="order-lg-1 pr-lg-4 text-center text-lg-left">
          <h1 class="h3 mb-0 text-white">{{ __('Add Coupon') }}</h1>
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
          <section class="col-lg-8 pt-lg-4 pb-4 mb-3 coupon">
            <div class="pt-2 px-4 pl-lg-0 pr-xl-5">
              <h2 class="h3 pt-2 pb-4 mb-0 text-center text-sm-left border-bottom">{{ __('Add Coupon') }}</h2>
              <form action="{{ route('add-coupon') }}" class="needs-validation mt-3 pt-3" id="profile_form" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-fn">{{ __('Coupon Code') }} <span class="require">*</span></label>
                  <input id="coupon_code" name="coupon_code" type="text" class="form-control" data-bvalidator="required">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-ln">{{ __('Value') }} <span class="require">*</span></label>
                 <input id="coupon_value" name="coupon_value" type="text" class="form-control" data-bvalidator="required,min[1]">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Start Date') }} <span class="require">*</span></label>
                  <input id="coupon_start_date" name="coupon_start_date" type="text" class="form-control" autocomplete="off" data-bvalidator="required">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('End Date') }} <span class="require">*</span></label>
                  <input id="coupon_end_date" name="coupon_end_date" type="text" class="form-control" autocomplete="off" data-bvalidator="required">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Discount Type') }} <span class="require">*</span></label>
                  <select name="discount_type" class="form-control" data-bvalidator="required">
                         <option value=""></option>
                         <option value="percentage">{{ __('Percentage') }}</option>
                         <option value="fixed">{{ __('Fixed') }}</option>
                         </select>
                </div>
              </div> 
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Status') }} <span class="require">*</span></label>
                  <select name="coupon_status" class="form-control" data-bvalidator="required">
                        <option value=""></option>
                         <option value="1">{{ __('Active') }}</option>
                         <option value="0">{{ __('InActive') }}</option>
                         </select>
                </div>
              </div>
              <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
              <div class="col-12">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                  <button class="btn btn-primary mt-3 mt-sm-0" type="submit">{{ __('Submit') }}</button>
                </div>
              </div>
            </div>
          </form>
          </div>
          </section>
        </div>
      </div>
    </div>