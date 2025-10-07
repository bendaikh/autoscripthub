<!DOCTYPE HTML>
<html lang="en">
<head>
<title>{{ $allsettings->site_title }} - {{ __('Messages') }}</title>
@include('meta')
@include('style')
<meta name="csrf-token" content="{{ csrf_token() }}" />
</head>
<body>
@include('header')
@if(Auth::user()->user_type != 'admin')
@if($addition_settings->conversation_mode == 1)
<div class="container py-5 mt-md-2 mb-2">
      <div class="row">
      <div class="col-lg-12" data-aos="fade-up" data-aos-delay="200">
         @if($check_other_user != 0)
            <div class="card">
            <div class="row g-0">
				<div class="col-12 col-lg-5 col-xl-3 border-right">
                    @foreach($other_user['details'] as $user)
                    @if($user->id != Auth::user()->id)
                    <a href="javascript:void(0);" data-id="{{ $user->username }}" @if($slug == $user->username) class="list-group-item list-group-item-action border-0 active userlink" @else class="list-group-item list-group-item-action border-0 userlink" @endif>
						<div class="d-flex align-items-start">
							@if($user->user_photo!='')
                              <img class="lazy rounded-circle mr-1" width="40" height="40" src="{{ url('/') }}/public/storage/users/{{ $user->user_photo }}"  alt="{{ $user->username }}">
                              @else
                              <img class="lazy rounded-circle mr-1" width="40" height="40" src="{{ url('/') }}/public/img/no-user.png"  alt="{{ $user->username }}"/>
                              @endif
							<div class="flex-grow-1 ml-3">
								{{ $user->username }}
                                @if($user->user_type == 'vendor')
								<div class="small"><span class="badge badge-success">{{ __('Vendor') }}</span></div>
                                @endif
                                @if($user->user_type == 'customer')
								<div class="small"><span class="badge badge-warning">{{ __('Customer') }}</span></div>
                                @endif
							</div>
						</div>
					</a>
                    @endif
					@endforeach
					<hr class="d-block d-lg-none mt-1 mb-0">
				</div>
				<div class="col-12 col-lg-7 col-xl-9">
					<div class="position-relative">
                      <div id='loader' style='display: none;' align="center">
                          <img width='60' height='60' class="lazy sloader" src='{{ url('/') }}/public/storage/settings/{{ $allsettings->site_loader_image }}'>
                      </div>
                      <div id="display_message">
                      </div>
                      <div class="chat-messages p-4" id="hide_message">
                       @if($chck != 0)   
						@foreach($chat['message'] as $chat)
                         <div class="chat-message-left pb-4">
							 <div align="center">
							  @if($chat->user_photo!='')
                              <img class="lazy rounded-circle mr-1" width="60" height="60" src="{{ url('/') }}/public/storage/users/{{ $chat->user_photo }}"   alt="{{ $chat->username }}">
                              @else
                              <img class="lazy rounded-circle mr-1" width="60" height="60" src="{{ url('/') }}/public/img/no-user.png"  alt="{{ $chat->username }}"/>
                              @endif
							  <div class="text-muted small text-nowrap mt-2">{{ Helper::timeAgo(strtotime($chat->conver_date)) }}</div>
                              </div>
							  <div class="flex-shrink-1 bg-light rounded py-2 px-3 ml-3">
									<div class="font-weight-bold mb-1">{{ $chat->username }}</div>
									@joypixels($chat->conver_text)<br/>
                                </div>
                            </div>
                      @endforeach
                      @endif
						</div>
					</div>
                    <form method="post" id="checkout_form" enctype="multipart/form-data" action=""  class="media-body needs-validation ml-3 messageform">
                    @csrf
                    <div class="flex-grow-0 py-3 px-4 border-top emoji">
						<div class="input-group">
                            <input type="hidden" name="conver_user_id" value="{{ Auth::user()->id }}">
                            <input type="hidden" name="conver_seller_id" id="conver_seller_id" value="{{ $last_user }}">
                            <input type="hidden" name="conver_url" value="{{ url('/messages') }}">
		<input type="text" class="form-control" name="conver_text" id="conver_text" placeholder="{{ __('Type your message') }}" data-emojiable="true"data-emoji-input="unicode" required>
							<button class="btn btn-primary btn-submit" type="submit">{{ __('Send') }}</button>
                        </div>
					</div>
                   </form> 
				</div>
			</div>
            </div>
            @else
            <div class="row g-0">
              <div class="col-lg-12" data-aos="fade-up" data-aos-delay="200" align="center">
              <h4>{{ __('No user found!') }}</h4><br/>
             </div>
            </div>
            @endif
        </div>
      </div>
    </div>
    @else
    @include('not-found')
    @include('footer')
    @endif
    @else
        @include('not-found')
        @include('footer')
   @endif
@include('script')
<script src="{{ asset('theme/emojione/emojione.picker.js') }}"></script>
<script  type="text/javascript">
$( "#conver_text" ).emojionePicker();
</script>
<script type="text/javascript">
$(document).ready(function() {
       $('.userlink').click(function(e) {
	      e.preventDefault();
		  var id = $(this).data("id");
    	  var token = $("meta[name='csrf-token']").attr("content");
            $('.userlink').removeClass('active');

        var $this = $(this);
        if (!$this.hasClass('active')) {
            $this.addClass('active');
        }
			$.ajax(
			{
				
				url: '{{url("messages")}}/'+id,
				type: 'GET',
				data: {
					"id": id,
					"_token": token,
				},
				beforeSend: function()
				{
					$("#loader").show();
					
				},
				success: function (data){
				    
					$('#hide_message').hide();
					/*$('.messageform').hide();*/
					$('#display_message').html(data.record);
					$("#conver_seller_id").val(data.last_user);
				},
				complete:function(data)
				{
					$("#loader").hide();
					
				}
			});
		  
       });
    });
   $(document).ready(function() {
   $('.chat-messages').scrollTop($('.chat-messages')[0].scrollHeight);   
    $(".btn-submit").click(function(e){
  
        e.preventDefault();
   
        var conver_user_id = $("input[name=conver_user_id]").val();
		var conver_seller_id = $("input[name=conver_seller_id]").val();
		var conver_url = $("input[name=conver_url]").val();
		var conver_text = $("input[name=conver_text]").val();
        var _token = $("input[name='_token']").val();
		if($('#conver_text').val() == '')
		{
           $('#conver_text').css("border", "1px solid #FE696A");
        }
		else
		{
		$.ajax({
                url: '{{url("messages")}}',
                type:'POST',
				dataType: 'json',
                data:{_token:_token, conver_user_id:conver_user_id, conver_seller_id:conver_seller_id, conver_url:conver_url, conver_text:conver_text},
                success: function(data) {
				
                    if($.isEmptyObject(data.error)){
					   if (data.success == undefined)
					   {
                        alert("Invalid Data");
					    }
						else
						{
						$('.emoji-wysiwyg-editor img').hide();
						$('.emoji-wysiwyg-editor').val("");
						$('#conver_text').val("");
						$('#hide_message').hide();
						$('#display_message').html(data.record);
						
						
					
						}
                    }else{
					    
                        printErrorMsg(data.error);
						
                    }
                }
            });
		}	
		
        });
  
    });
</script>
</body>
</html>