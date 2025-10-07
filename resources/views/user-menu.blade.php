<aside class="col-lg-4">
            <div class="cz-sidebar-static h-100 border-right">
              <h6>{{ __('Badges') }}</h6>
              @if($user['user']->user_type == 'vendor')
            <div class="item-details badge-size" data-aos="fade-up" data-aos-delay="200">
                                    <ul>
                                        @if($sold_amount >= $badges['setting']->author_sold_level_six)
                                        <div class="sidebar-card card--metadata">
                                            <div>
                                                    <img class="lazy single-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->power_elite_author_icon }}"  border="0" title="{{ $badges['setting']->author_sold_level_six_label }} : {{ __('Sold more than') }} {{ $allsettings->site_currency }} {{ $badges['setting']->author_sold_level_six }}+ {{ __('ON') }} {{ $allsettings->site_title }}"> {{ $badges['setting']->author_sold_level_six_label }}
                                            </div>
                                            
                                        </div> 
                                        @endif
                                         @if($user['user']->country_badge == 1)
                                        @if($country['view']->country_badges != "")
                                        <li>
                                          <img class="lazy icon-badges" width="30" height="30" src="{{ url('/') }}/public/storage/flag/{{ $country['view']->country_badges }}"  border="0" title="{{ __('Located in') }} {{ $country['view']->country_name }}">  
                                        </li>
                                        @endif
                                        @endif
                                        @if($featured_count->has($user['user']->id) ? count($featured_count[$user['user']->id]) : 0 != 0)
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->featured_item_icon }}"  border="0" title="{{ __('Featured Item: Had an item featured on') }} {{ $allsettings->site_title }}">
                                        </li>
                                        @endif
                                        @if($free_count->has($user['user']->id) ? count($free_count[$user['user']->id]) : 0 != 0)
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->free_item_icon }}"  border="0"  title="{{ __('Free Item : Contributed a free file of this item') }}">
                                        </li>
                                        @endif
                                        @if($tren_count->has($user['user']->id) ? count($tren_count[$user['user']->id]) : 0 != 0)
                                         <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->trends_icon }}"  border="0"  title="{{ __('Trendsetter: Had an item that was trending') }}">
                                        </li>
                                        @endif
                                        @if($user['user']->exclusive_author == 1)
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->exclusive_author_icon }}"  border="0" title="{{ __('Exclusive Author: Sells items exclusively on') }} {{ $allsettings->site_title }}">
                                        </li>
                                        @endif
                                        @if($year == 1)
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->one_year_icon }}"  border="0" title="{{ $year }} {{ __('Years of Membership: Has been part of the') }} {{ $allsettings->site_title }} {{ __('Community for over') }} {{ $year }} {{ __('years') }}">
                                        </li>
                                        @endif
                                        @if($year == 2)
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->two_year_icon }}"  border="0"  title="{{ $year }} {{ __('Years of Membership: Has been part of the') }} {{ $allsettings->site_title }} {{ __('Community for over') }} {{ $year }} {{ __('years') }}">
                                        </li>
                                        @endif
                                        
                                        @if($year == 3)
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->three_year_icon }}"  border="0"  title="{{ $year }} {{ __('Years of Membership: Has been part of the') }} {{ $allsettings->site_title }} {{ __('Community for over') }} {{ $year }} {{ __('years') }}">
                                        </li>
                                        @endif
                                        @if($year == 4)
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->four_year_icon }}"  border="0"  title="{{ $year }} {{ __('Years of Membership: Has been part of the') }} {{ $allsettings->site_title }} {{ __('Community for over') }} {{ $year }} {{ __('years') }}">
                                        </li>
                                        @endif
                                        @if($year == 5)
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->five_year_icon }}"  border="0"  title="{{ $year }} {{ __('Years of Membership: Has been part of the') }} {{ $allsettings->site_title }} {{ __('Community for over') }} {{ $year }} {{ __('years') }}">
                                        </li>
                                        @endif 
                                        @if($year == 6)
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->six_year_icon }}"  border="0"  title="{{ $year }} {{ __('Years of Membership: Has been part of the') }} {{ $allsettings->site_title }} {{ __('Community for over') }} {{ $year }} {{ __('years') }}">
                                        </li>
                                        @endif
                                        @if($year == 7)
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->seven_year_icon }}"  border="0"  title="{{ $year }} {{ __('Years of Membership: Has been part of the') }} {{ $allsettings->site_title }} {{ __('Community for over') }} {{ $year }} {{ __('years') }}">
                                        </li>
                                        @endif
                                        @if($year == 8)
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->eight_year_icon }}"  border="0"  title="{{ $year }} {{ __('Years of Membership: Has been part of the') }} {{ $allsettings->site_title }} {{ __('Community for over') }} {{ $year }} {{ __('years') }}">
                                        </li>
                                        @endif
                                        @if($year == 9)
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->nine_year_icon }}"  border="0"  title="{{ $year }} {{ __('Years of Membership: Has been part of the') }} {{ $allsettings->site_title }} {{ __('Community for over') }} {{ $year }} {{ __('years') }}">
                                        </li>
                                        @endif
                                        @if($year >= 10)
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->ten_year_icon }}"  border="0"  title="@if($year >= 10) 10+ @else {{ $year }} @endif {{ __('Years of Membership: Has been part of the') }} {{ $allsettings->site_title }} {{ __('Community for over') }} @if($year >= 10) 10+ @else {{ $year }} @endif {{ __('years') }}">
                                        </li>
                                        @endif
                                        @if($sold_amount >= $badges['setting']->author_sold_level_one && $badges['setting']->author_sold_level_two > $sold_amount) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_sold_level_one_icon }}"  border="0"  title="{{ __('Author Level') }} 1: {{ __('Has sold') }} {{ $allsettings->site_currency }} {{ $badges['setting']->author_sold_level_one }}+ {{ __('ON') }} {{ $allsettings->site_title }}">
                                        </li>
                                        @endif
                                        
                                        @if($sold_amount >= $badges['setting']->author_sold_level_two &&  $badges['setting']->author_sold_level_three > $sold_amount) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_sold_level_two_icon }}"  border="0"  title="{{ __('Author Level') }} 2: {{ __('Has sold') }} {{ $allsettings->site_currency }} {{ $badges['setting']->author_sold_level_two }}+ {{ __('ON') }} {{ $allsettings->site_title }}">
                                        </li>
                                        @endif
                                        @if($sold_amount >= $badges['setting']->author_sold_level_three &&  $badges['setting']->author_sold_level_four > $sold_amount) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->	author_sold_level_three_icon }}"  border="0"  title="{{ __('Author Level') }} 3: {{ __('Has sold') }} {{ $allsettings->site_currency }} {{ $badges['setting']->author_sold_level_three }}+ {{ __('ON') }} {{ $allsettings->site_title }}">
                                        </li>
                                        @endif
                                        @if($sold_amount >= $badges['setting']->author_sold_level_four &&  $badges['setting']->author_sold_level_five > $sold_amount) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_sold_level_four_icon }}"  border="0"  title="{{ __('Author Level') }} 4: {{ __('Has sold') }} {{ $allsettings->site_currency }} {{ $badges['setting']->author_sold_level_four }}+ {{ __('ON') }} {{ $allsettings->site_title }}">
                                        </li>
                                        @endif
                                        @if($sold_amount >= $badges['setting']->author_sold_level_five &&  $badges['setting']->author_sold_level_six > $sold_amount) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_sold_level_five_icon }}"  border="0"  title="{{ __('Author Level') }} 5: {{ __('Has sold') }} {{ $allsettings->site_currency }} {{ $badges['setting']->author_sold_level_five }}+ {{ __('ON') }} {{ $allsettings->site_title }}">
                                        </li>
                                        @endif
                                        @if($sold_amount >= $badges['setting']->author_sold_level_six) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_sold_level_six_icon }}"  border="0"  title="{{ __('Author Level') }} 6: {{ __('Has sold') }} {{ $allsettings->site_currency }} {{ $badges['setting']->author_sold_level_six }}+ {{ __('ON') }} {{ $allsettings->site_title }}">
                                        </li>
                                        @endif
                                        @if($sold_amount >= $badges['setting']->author_sold_level_six)
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->power_elite_author_icon }}"  border="0"  title="{{ $badges['setting']->author_sold_level_six_label }} : Sold more than {{ $allsettings->site_currency }} {{ $badges['setting']->author_sold_level_six }}+ {{ __('ON') }} {{ $allsettings->site_title }}">
                                        </li>
                                        @endif
                                        @if($collect_amount >= $badges['setting']->author_collect_level_one && $badges['setting']->author_collect_level_two > $collect_amount) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_collect_level_one_icon }}"  border="0"  title="{{ __('Collector Level') }} 1: {{ __('Has collected') }} {{ $badges['setting']->author_collect_level_one }}+ {{ __('items on') }} {{ $allsettings->site_title }}">
                                        </li>
                                        @endif
                                        @if($collect_amount >= $badges['setting']->author_collect_level_two && $badges['setting']->author_collect_level_three > $collect_amount) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_collect_level_two_icon }}"  border="0"  title="{{ __('Collector Level') }} 2: {{ __('Has collected') }} {{ $badges['setting']->author_collect_level_two }}+ {{ __('items on') }} {{ $allsettings->site_title }}">
                                        </li>
                                        @endif
                                        @if($collect_amount >= $badges['setting']->author_collect_level_three && $badges['setting']->author_collect_level_four > $collect_amount) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_collect_level_three_icon }}"  border="0"  title="{{ __('Collector Level') }} 3: {{ __('Has collected') }} {{ $badges['setting']->author_collect_level_three }}+ {{ __('items on') }} {{ $allsettings->site_title }}">
                                        </li>
                                        @endif
                                        @if($collect_amount >= $badges['setting']->author_collect_level_four && $badges['setting']->author_collect_level_five > $collect_amount) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_collect_level_four_icon }}"  border="0"  title="{{ __('Collector Level') }} 4: {{ __('Has collected') }} {{ $badges['setting']->author_collect_level_four }}+ {{ __('items on') }} {{ $allsettings->site_title }}">
                                        </li>
                                        @endif
                                        @if($collect_amount >= $badges['setting']->author_collect_level_five && $badges['setting']->author_collect_level_six > $collect_amount) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_collect_level_five_icon }}"  border="0"  title="{{ __('Collector Level') }} 5: {{ __('Has collected') }} {{ $badges['setting']->author_collect_level_five }}+ {{ __('items on') }} {{ $allsettings->site_title }}">
                                        </li>
                                        @endif
                                        @if($collect_amount >= $badges['setting']->author_collect_level_six) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_collect_level_six_icon }}"  border="0"  title="{{ __('Collector Level') }} 6: {{ __('Has collected') }} {{ $badges['setting']->author_collect_level_six }}+ {{ __('items on') }} {{ $allsettings->site_title }}">
                                        </li>
                                        @endif
                                        @if($referral_count >= $badges['setting']->author_referral_level_one && $badges['setting']->author_referral_level_two > $referral_count) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_referral_level_one_icon }}"  border="0"  title="{{ __('Affiliate Level') }} 1: {{ __('Has referred') }} {{ $badges['setting']->author_referral_level_one }}+ {{ __('Members') }}">
                                        </li>
                                        @endif
                                        @if($referral_count >= $badges['setting']->author_referral_level_two && $badges['setting']->author_referral_level_three > $referral_count) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_referral_level_two_icon }}"  border="0"  title="{{ __('Affiliate Level') }} 2: {{ __('Has referred') }} {{ $badges['setting']->author_referral_level_two }}+ {{ __('Members') }}">
                                        </li>
                                        @endif
                                        @if($referral_count >= $badges['setting']->author_referral_level_three && $badges['setting']->author_referral_level_four > $referral_count) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_referral_level_three_icon }}"  border="0"  title="{{ __('Affiliate Level') }} 3: {{ __('Has referred') }} {{ $badges['setting']->author_referral_level_three }}+ {{ __('Members') }}">
                                        </li>
                                        @endif
                                        @if($referral_count >= $badges['setting']->author_referral_level_four && $badges['setting']->author_referral_level_five > $referral_count) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_referral_level_four_icon }}"  border="0"  title="{{ __('Affiliate Level') }} 4: {{ __('Has referred') }} {{ $badges['setting']->author_referral_level_four }}+ {{ __('Members') }}">
                                        </li>
                                        @endif
                                        @if($referral_count >= $badges['setting']->author_referral_level_five && $badges['setting']->author_referral_level_six > $referral_count) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_referral_level_five_icon }}"  border="0" title="{{ __('Affiliate Level') }} 5: {{ __('Has referred') }} {{ $badges['setting']->author_referral_level_five }}+ {{ __('Members') }}">
                                        </li>
                                        @endif
                                        @if($referral_count >= $badges['setting']->author_referral_level_six) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_referral_level_six_icon }}"  border="0" title="{{ __('Affiliate Level') }} 6: {{ __('Has referred') }} {{ $badges['setting']->author_referral_level_six }}+ {{ __('Members') }}">
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                                @endif
              @if($user['user']->profile_heading != "")
              <div data-aos="fade-up" data-aos-delay="200">
              <hr class="my-4">
              <h6>{{ __('About') }}</h6>
              <p class="font-size-ms text-muted">
              <b>{{ $user['user']->profile_heading }}</b><br/>
              {{ $user['user']->about }}
              </p>
              </div>
              @endif
              <div data-aos="fade-up" data-aos-delay="200">
              <hr class="my-4">
              <h6>{{ __('Social Links') }}</h6>
              @if($user['user']->facebook_url != "")
              <a class="social-btn sb-facebook sb-outline sb-sm mr-2 mb-2" href="{{ $user['user']->facebook_url }}" target="_blank"><i class="dwg-facebook"></i></a>
              @endif
              @if($user['user']->twitter_url != "")
              <a class="social-btn sb-twitter sb-outline sb-sm mr-2 mb-2" href="{{ $user['user']->twitter_url }}" target="_blank"><i class="dwg-twitter"></i></a>
              @endif
              @if($user['user']->instagram_url != "")
              <a class="social-btn sb-instagram sb-outline sb-sm mr-2 mb-2" href="{{ $user['user']->instagram_url }}" target="_blank"><i class="dwg-instagram"></i></a>
              @endif
              @if($user['user']->linkedin_url != "")
              <a class="social-btn sb-linkedin sb-outline sb-sm mr-2 mb-2" href="{{ $user['user']->linkedin_url }}" target="_blank"><i class="dwg-linkedin"></i></a>
              @endif
              @if($user['user']->pinterest_url != "")
              <a class="social-btn sb-pinterest sb-outline sb-sm mr-2 mb-2" href="{{ $user['user']->pinterest_url }}" target="_blank"><i class="dwg-pinterest"></i></a>
              @endif
              </div>
              @if($addition_settings->affiliate_referral == 1)
              @if (Auth::check())
              @if($addition_settings->subscription_mode == 0)
              <div data-aos="fade-up" data-aos-delay="200">
                  <hr class="my-4">
                  <h6 class="pb-1">{{ __('Affiliate Referral Url') }}</h6>
                  <div class="form-group">
                      <input type="text" value="{{ URL::to('/') }}/?ref={{ $user['user']->id }}" id="myInput" class="form-control" readonly="readonly">
                    </div>
                    <a href="javascript:void(0)" onclick="myFunction()" class="btn btn-primary btn-sm">{{ __('Copy Url') }}</a>
               </div>
              @else
              @if(Auth::user()->user_subscr_date >= date('Y-m-d'))
                  <div data-aos="fade-up" data-aos-delay="200">
                  <hr class="my-4">
                  <h6 class="pb-1">{{ __('Affiliate Referral Url') }}</h6>
                  <div class="form-group">
                      <input type="text" value="{{ URL::to('/') }}/?ref={{ $user['user']->id }}" id="myInput" class="form-control" readonly="readonly">
                    </div>
                    <a href="javascript:void(0)" onclick="myFunction()" class="btn btn-primary btn-sm">{{ __('Copy Url') }}</a>
                  </div> 
                  @endif 
              @endif    
              @endif
              @endif
              <div data-aos="fade-up" data-aos-delay="200">
              <hr class="my-4">
              <ul class="profile-menu">
                <li>
                  <a href="{{ url('/user') }}/{{ $user['user']->username }}"><span class="dwg-home"></span> {{ __('Profile') }}</a>
                </li>
                @if($user['user']->user_type == 'vendor')
                <li>
                <a href="{{ url('/user-reviews') }}/{{ $user['user']->username }}"><span class="dwg-star"></span> {{ __('Customer Reviews') }}</a>
                </li>
                @endif
                <li>
                <a href="{{ url('/user-followers') }}/{{ $user['user']->username }}"><span class="dwg-user"></span> {{ __('Followers') }} ({{ $followercount }})</a>
                </li>
                <li>
                <a href="{{ url('/user-following') }}/{{ $user['user']->username }}"><span class="dwg-user"></span> {{ __('Followings') }} ({{ $followingcount }})</a>
                </li>
              </ul>
              </div>
              @if (Auth::check())
              @if($user['user']->username != Auth::user()->username)
              <div data-aos="fade-up" data-aos-delay="200">
              <hr class="my-4">
              <h6 class="pb-1">{{ __('Email') }} {{ $user['user']->username }}</h6>
              <form action="{{ route('user') }}" class="setting_form" id="item_form" method="post" enctype="multipart/form-data">
              {{ csrf_field() }}
              <div class="form-group">
                  <textarea name="message" class="form-control" id="author-message" rows="6" placeholder="{{ __('Your message...') }}" data-bvalidator="required"></textarea>
                  <input type="hidden" name="from_email" value="{{ Auth::user()->email }}" />
                  <input type="hidden" name="from_name" value="{{ Auth::user()->name }}" />
                  <input type="hidden" name="to_email" value="{{ $user['user']->email }}" />
                  <input type="hidden" name="to_name" value="{{ $user['user']->name }}" />
                  <input type="hidden" name="to_id" value="{{ $user['user']->id }}" />
                </div>
                @if($addition_settings->site_google_recaptcha == 1)
                <div class="form-group{{ $errors->has('g-recaptcha-response') ? ' has-error' : '' }}">
                            {!! RecaptchaV3::field('register') !!}
                                @if ($errors->has('g-recaptcha-response'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                                    </span>
                                @endif
                        </div>
                @endif
                <button class="btn btn-primary btn-sm btn-block" type="submit">{{ __('send message') }}</button>
              </form>
              </div>
              @endif
              @endif
              @if(Auth::guest())
              <div data-aos="fade-up" data-aos-delay="200">
              <hr class="my-4">
              <h6 class="pb-1">{{ __('Email') }} {{ $user['user']->username }}</h6>
              <p class="font-size-ms text-muted">
                  {{ __('Please') }} <a href="{{ url('/login') }}">{{ __('Sign In') }}</a> {{ __('to contact this author.') }}
              </p>
              </div>
              @endif 
            </div>
          </aside>