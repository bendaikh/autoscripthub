<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en">
<!--<![endif]-->

<head>
    
    @include('admin.stylesheet')
</head>

<body>
    
    @include('admin.navigation')

    <!-- Right Panel -->
    @if(in_array('subscription',$avilable))
    @if($addition_settings->subscription_mode == 1)
    <div id="right-panel" class="right-panel">

        
                       @include('admin.header')
                       @if($demo_mode == 'on')
                     @include('admin.demo-mode')
                     @else
                     <form action="{{ route('admin.subscription') }}" method="post" id="setting_form" enctype="multipart/form-data">
                     {{ csrf_field() }}
                     @endif

        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>{{ __('Subscription') }}</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <a href="{{ url('/admin/add-subscription') }}" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> {{ __('Add Pack') }}</a>
                            <input type="submit" value="Delete All" name="action" class="btn btn-danger btn-sm ml-1" id="checkBtn" onClick="return confirm('Are you sure you want to delete?');">
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        
       @include('admin.warning')
        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">

                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">{{ __('Subscription') }}</strong>
                            </div>
                            <div class="card-body">
                                <table id="example" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="selectAll"></th>
                                            <th>{{ __('Sno') }}</th>
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('Price') }}</th>
                                            <th>{{ __('Duration') }}</th>
                                            <th>{{ __('Display Order') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @php $no = 1; @endphp
                                    @foreach($subscription['view'] as $subscription)
                                        <tr class="allChecked">
                                            <td><input type="checkbox" name="subscr_id[]" value="{{ $subscription->subscr_id }}"/></td>
                                            <td>{{ $no }}</td>
                                            <td width="200">{{ $subscription->subscr_name }} </td>
                                           <td>{{ Helper::plan_format($allsettings->site_currency_position,$subscription->subscr_price,$allsettings->site_currency_symbol) }} </td>
                                           <td>{{ $subscription->subscr_duration }} </td>
                                           <td>{{ $subscription->subscr_order }} </td>
                                            <td>@if($subscription->subscr_status == 1) <span class="badge badge-success">{{ __('Active') }}</span> @else <span class="badge badge-danger">{{ __('InActive') }}</span> @endif</td>
                                            <td><a href="edit-subscription/{{ $subscription->subscr_id }}" class="btn btn-success btn-sm"><i class="fa fa-edit"></i>&nbsp; {{ __('Edit') }}</a> 
                                            @if($demo_mode == 'on') 
                                            <a href="demo-mode" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;{{ __('Delete') }}</a>
                                            @else 
                                            <a href="subscription/{{ $subscription->subscr_id }}" class="btn btn-danger btn-sm" onClick="return confirm('{{ __('Are you sure you want to delete') }}?');"><i class="fa fa-trash"></i>&nbsp;{{ __('Delete') }}</a>@endif</td>
                                        </tr>
                                        
                                        @php $no++; @endphp
                                   @endforeach     
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </form>
                </div>
                
                
                
                <div class="row">
                <div class="col-md-12">
                  <div class="card">
                            <div class="card-header">
                                <strong class="card-title">{{ __('Free Subscription (Default Registration)') }}</strong><br/><small>({{ __('default registration vendor to assign free subscription package') }})</small>
                            </div>
                             <div class="card-body">
                 @if($demo_mode == 'on')
                                 @include('admin.demo-mode')
                                 @else
                                 <form action="{{ route('admin.free-subscription') }}" method="post" id="category_form" enctype="multipart/form-data">
                                 {{ csrf_field() }}
                                 @endif
                                  
                                 <div class="col-md-6">
                                 
                                   <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">Free Subscription? <span class="require">*</span></label>
                                                <select name="free_subscription" class="form-control" required>
                                                <option value=""></option>
                                                <option value="1" @if($addition_settings->free_subscription == 1) selected @endif>{{ __('Enable') }}</option>
                                                <option value="0" @if($addition_settings->free_subscription == 0) selected @endif>{{ __('Disable') }}</option>
                                                </select>
                                            </div>
                                   
                                            <div class="form-group">
                                                <label for="name" class="control-label mb-1">{{ __('Upload') }} {{ __('Limited No of Items') }} <span class="require">*</span></label>
                                                <input id="subscr_items" name="subscr_items" type="text" class="form-control" data-bvalidator="required,digit,min[1]" value="{{ $addition_settings->free_subscr_item }}">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="name" class="control-label mb-1">{{ __('Download') }} {{ __('Limited No of Items') }} ({{ __('Per Day') }}) - ({{ __('Customer') }}/{{ __('Vendor') }})<span class="require">*</span></label>
                                                <input id="subscr_download_items" name="subscr_download_items" type="text" class="form-control" data-bvalidator="required,digit,min[1]" value="{{ $addition_settings->subscr_download_items }}">
                                            </div>
                                      </div>
                                      
                                      <div class="col-md-6"> 
                                      <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"> {{ __('Duration') }} <span class="require">*</span></label>
                                                <select name="subscr_duration" class="form-control" data-bvalidator="required">
                                                <option value=""></option>
                                                @php  
                                                for($i=1;$i<=365;$i++){ 
                                                if($i==1){ $day_text = "day"; } else { $day_text = "days"; }
                                                $dates = $i.' '.$day_text;
                                                @endphp
                                                <option value="{{ $dates }}" @if($addition_settings->free_subscr_duration == $dates) selected @endif>{{ $dates }}</option>
                                                @php } @endphp
                                                </select>
                                                
                                            </div>     
                                             
                                            <div class="form-group">
                                                <label for="name" class="control-label mb-1">{{ __('Storage Space') }} (MB) <span class="require">*</span></label>
                                                <input id="subscr_spaces" name="subscr_spaces" type="text" class="form-control" data-bvalidator="required,digit,min[1]" value="{{ $addition_settings->free_subscr_space }}">
                                            </div> 
                                        </div>
                                        
                                        <input type="hidden" name="user_subscr_type" value="Free">
                                        <input type="hidden" name="user_subscr_price" value="0">
                                        <input type="hidden" name="sid" value="{{ $sid }}">
                                        <div class="col-md-12">    
                                
                                                        <button type="submit" name="submit" class="btn btn-primary btn-sm">
                                                            <i class="fa fa-dot-circle-o"></i> {{ __('Submit') }}
                                                        </button>
                                                       
                                                 
                                                 </div>   
                     </form>
                     </div>
                     </div>                   
                </div>
                </div>
                
                <div class="row">
                <div class="col-md-12">
                  <div class="card">
                            <div class="card-header">
                                <strong class="card-title">Subscription Content</strong>
                            </div>
                             <div class="card-body">
                                 @if($demo_mode == 'on')
                                 @include('admin.demo-mode')
                                 @else
                                 <form action="{{ route('admin.upsubscription') }}" method="post" id="order_form" enctype="multipart/form-data">
                                 {{ csrf_field() }}
                                 @endif
                                  
                                 <div class="col-md-6">
                                 
                                   
                                  <div class="form-group">
                                                <label for="name" class="control-label mb-1">{{ __('Title') }} </label>
                                                <input id="subscription_title" name="subscription_title" type="text" class="form-control" value="{{ $addition_settings->subscription_title }}">
                                            </div> 
                                            <div class="form-group">
                                                <label for="name" class="control-label mb-1">{{ __('Description') }} </label>
                                                <textarea name="subscription_desc" id="summary-ckeditor" rows="6" class="form-control">{{ html_entity_decode($addition_settings->subscription_desc) }}</textarea>
                                                
                                            </div> 
                                      </div>
                                      
                                      <div class="col-md-6">      
                                            
                                             
                                        </div>
                                        
                                        
                                        <div class="col-md-12">    
                                
                                                        <button type="submit" name="submit" class="btn btn-primary btn-sm">
                                                            <i class="fa fa-dot-circle-o"></i> {{ __('Submit') }}
                                                        </button>
                                                       
                                                 
                                                 </div>   
                     </form>
                     </div>
                     </div>                   
                </div>
                </div>
                
            </div><!-- .animated -->
        </div><!-- .content -->


    </div><!-- /#right-panel -->
    @else
    @include('admin.404')
    @endif
    @else
    @include('admin.denied')
    @endif
    <!-- Right Panel -->


   @include('admin.javascript')
   <script type="text/javascript">
      $(document).ready(function () { 
    var oTable = $('#example').dataTable({
        stateSave: true
    });

    var allPages = oTable.fnGetNodes();

    $('body').on('click', '#selectAll', function () {
        if ($(this).hasClass('allChecked')) {
            $('input[type="checkbox"]', allPages).prop('checked', false);
        } else {
            $('input[type="checkbox"]', allPages).prop('checked', true);
        }
        $(this).toggleClass('allChecked');
    })
});

      

$(document).ready(function () {
    $('#checkBtn').click(function() {
      checked = $("input[type=checkbox]:checked").length;

      if(!checked) {
        alert("You must check at least one checkbox.");
        return false;
      }

    });
	
	
	
	});

</script>

</body>

</html>
