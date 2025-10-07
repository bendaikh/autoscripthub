<!DOCTYPE HTML>
<html lang="en">
<head>
<title>{{ $allsettings->site_title }} - {{ __('Profile') }}</title>
@include('meta')
@include('style')
@if($addition_settings->site_google_recaptcha == 1)
@if (Auth::check())
{!! RecaptchaV3::initJs() !!}
@endif
@endif
</head>
<body>
@include('header')
@include('user-box')
<div class="container mb-5 pb-3">
      <div class="bg-light box-shadow-lg rounded-lg overflow-hidden">
        <div class="row">
          <!-- Sidebar-->
          @include('user-menu')
          <!-- Content-->
          <section class="col-lg-8 pt-lg-4 pb-md-4">
            <div class="pt-2 px-4 pl-lg-0 pr-xl-5">
            <div class="profile-banner" data-aos="fade-up" data-aos-delay="200">
            @if($user['user']->user_banner != '')
            <img class="lazy" width="762" height="313" src="{{ url('/') }}/public/storage/users/{{ $user['user']->user_banner }}"  alt="{{ $user['user']->username }}">
            @else
            <img class="lazy" width="762" height="313" src="{{ url('/') }}/public/img/no-image.jpg"  alt="{{ $user['user']->username }}">
            @endif
            </div>
            @if($user['user']->user_type == 'vendor')
              <h2 class="h3 pt-2 pb-4 mb-4 text-center text-sm-left border-bottom">{{ __('Product Items') }}<span class="badge badge-secondary font-size-sm text-body align-middle ml-2">{{ count($itemData['item']) }}</span></h2>
              <div class="row pt-2 mx-n2 flash-sale" id="post-data">
                @include('user-data')
               </div>
               <div class="ajax-load text-center" style="display:none">
               <p><img class="lazy" width="24" height="24" src="{{ url('/') }}/resources/views/theme/img/loader.gif"> {{ __('Loading More Items') }}</p>
              </div>
           @endif
        </div>
        </section>
        </div>
      </div>
    </div>
@include('footer')
@include('script')
<script type="text/javascript">
	var page = 1;
	$(window).scroll(function() {
	    if($(window).scrollTop() + $(window).height() >= $(document).height()) {
	        page++;
	        loadMoreData(page);
	    }
	});

	function loadMoreData(page){
	  $.ajax(
	        {
	            url: '?page=' + page,
	            type: "get",
	            beforeSend: function()
	            {
	                $('.ajax-load').show();
	            }
	        })
	        .done(function(data)
	        {
	            if(data.html == " "){
	                $('.ajax-load').html("No more records found");
	                return;
	            }
	            $('.ajax-load').hide();
	            $("#post-data").append(data.html);
	        })
	        .fail(function(jqXHR, ajaxOptions, thrownError)
	        {
	              alert('server not responding...');
	        });
	}
</script>
</body>
</html>