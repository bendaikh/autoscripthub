<header id="header" class="header">

            <div class="header-menu">

                <div class="col-sm-7">
                    <a id="menuToggle" class="menutoggle pull-left"><i class="fa fa fa-tasks"></i></a>
                    <div class="header-left">
                      @if($custom_settings->author_key == Helper::Key_Owner())
                      <div class="dropdown for-message">
                          <button class="btn btn-secondary dropdown-toggle" type="button" id="message" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-bell"></i>
                            <span class="count bg-danger">{{ Helper::Notification_Count(1) }}</span>
                          </button>
                          <div class="dropdown-menu" aria-labelledby="message" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 36px, 0px); top: 0px; left: 0px; will-change: transform;">
                            @if(Helper::Notification_NoCount(1) != 0)
                            @foreach(Helper::Notification_Display_Limit(1) as $display)
                            <a class="dropdown-item media" href="{{ URL::to('/admin/notifications') }}/{{ $encrypter->encrypt($display->noti_id) }}">
                                <span class="photo media-left"><img alt="avatar" src="{{ Helper::IDtoFetchImage($display->noti_type,$display->noti_type_id,$display->noti_sender_id) }}"></span>
                                <span class="message media-body">
                                    <span class="name float-left"><p>{!! html_entity_decode($display->noti_message) !!}</p></span>
                                    <p class="time">{{ Helper::Timeer_Ago(strtotime($display->noti_date)) }}</p>
                                </span>
                            </a>
                            @endforeach
                            <a href="{{ URL::to('/admin/notifications') }}" style="text-align:center;"><p class="red">{{ __('View All Notifications') }}</p></a>
                            @else
                            <a class="dropdown-item media" href="{{ URL::to('/admin/notifications') }}" style="text-align:center;">{{ __('View All Notifications') }}</a>
                            @endif
                          </div>
                        </div>
                       @endif 
                    </div>
                </div>

                <div class="col-sm-5">
                    <div class="user-area dropdown float-right">
                        <a href="javascript:void();" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            
                            
                        @if(Auth::user()->user_photo != '')
                        <img class="lazy user-avatar rounded-circle" width="32" height="32" src="{{ url('/') }}/public/storage/users/{{ Auth::user()->user_photo }}"  alt="{{ Auth::user()->name }}" />@else <img class="lazy user-avatar rounded-circle" width="32" height="32" src="{{ url('/') }}/public/img/no-user.png"  alt="{{ Auth::user()->name }}"/>  @endif
                        
                        </a>
<div class="user-menu dropdown-menu">
                            <a class="nav-link" href="{{ url('/admin/edit-profile') }}"><i class="fa fa-user"></i> {{ __('My Profile') }}</a>
                            @if(in_array('settings',$avilable)) 
                            <a class="nav-link" href="{{ url('/admin/general-settings') }}"><i class="fa fa-cog"></i> {{ __('Settings') }}</a>
                            @endif
                            <a class="nav-link" href="{{ url('/logout') }}"><i class="fa fa-power-off"></i> {{ __('Logout') }}</a>
                        </div>
                        </div>
                        @if($allsettings->multi_language == 1)
                       <div class="language-select dropdown" id="language-select">
                        <a class="dropdown-toggle" href="#" data-toggle="dropdown"  id="language" aria-haspopup="true" aria-expanded="true">
                            <span class="fa fa-language"></span> {{ $current_locale }}
                        </a>
                        <div class="dropdown-menu" aria-labelledby="language">
                            @foreach($available_locales as $locale_name => $available_locale)
                            <div class="dropdown-item">
                                <a href="{{ URL::to('/language') }}/{{ $available_locale }}">{{ $locale_name }}</a>
                            </div>
                            @endforeach
                        </div>
                        
                    </div>
                 @endif 
                    

                </div>
            </div>

        </header>
        
                    