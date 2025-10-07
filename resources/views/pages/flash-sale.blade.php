<!DOCTYPE HTML>
<html lang="en">
<head>
<title>{{ $allsettings->site_title }} - {{ __('Flash Sale') }}</title>
@include('meta')
@include('style')
</head>
<body>
@include('header')
<section class="bg-position-center-top" style="background-image: url('{{ url('/') }}/public/storage/settings/{{ $allsettings->site_banner }}');">
      <div class="container d-flex flex-column">
        <div class="row mt-auto">
        <div class="col-lg-8 col-sm-12 text-center mx-auto">
        <h2 class="mb-4 pt-5 title-page text-white">{{ __('Flash Sale') }}</h2>
        <h3 class="lead mb-5 text-white">{{ __('For only a short period of time you can grab these files with') }} {{ $addition_settings->flash_sale_value }}% {{ __('discount') }}</h3>
    </div>
</div>
<div class="row">
    <div class="col-lg-7 col-md-7 col-sm-12 mx-auto text-center mb-3 pb-3">
        <div class="countdown-timer">
                        <ul id="example">
                                <li class="pt-2 pb-1 mb-2"><span class="days">00</span><div>{{ __('days') }}</div></li>
                                <li class="pt-2 pb-1 mb-2"><span class="hours">00</span><div>{{ __('hours') }}</div></li>
                                <li class="pt-2 pb-1 mb-2"><span class="minutes">00</span><div>{{ __('minutes') }}</div></li>
		                        <li class="pt-2 pb-1 mb-2"><span class="seconds">00</span><div>{{ __('seconds') }}</div></li>
                           </ul>
               </div>
        </div>
    </div> 
</div>
</section>
<div class="container py-5 mt-md-2 mb-2">
       @if(in_array('flash-sale',$top_ads))
      <div class="row">
          <div class="col-lg-12 mb-4" align="center">
             @php echo html_entity_decode($addition_settings->top_ads); @endphp
          </div>
       </div>   
       @endif
      <div class="row pt-2 mx-n2 flash-sale" id="post-data">
        @include('flash-data')
       </div>
       <div class="ajax-load text-center" style="display:none">
	   <p><img class="lazy" width="24" height="24" src="{{ url('/') }}/resources/views/theme/img/loader.gif"> {{ __('Loading More Items') }}</p>
      </div>
      @if(in_array('flash-sale',$bottom_ads))
       <div class="row">
          <div class="col-lg-12 mb-2" align="center">
             @php echo html_entity_decode($addition_settings->bottom_ads); @endphp
          </div>
       </div>   
       @endif
    </div>
@include('footer')
@include('script')
@if(!empty($allsettings->site_flash_end_date))
	<script type="text/javascript">
            $('#example').countdown({
                date: '{{ date("m/d/Y H:i:s", strtotime($allsettings->site_flash_end_date)) }}',
                offset: -8,
                day: '{{ __('Day') }}',
                days: '{{ __('days') }}'
            }, function () {
			'use strict';
                
            });
    </script>
    @endif
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