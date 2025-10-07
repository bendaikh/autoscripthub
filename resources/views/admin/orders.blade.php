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
    @if(in_array('items',$avilable)) 
    <div id="right-panel" class="right-panel">

        
                       @include('admin.header')
                       

        
        
         @include('admin.warning')


        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">
                    <div class="col-md-3 ml-auto" align="right">
                    <form action="{{ route('admin.orders') }}" method="post" id="setting_form" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-group">
                    <input id="search" name="search" type="text" class="move-bars" value="{{ $search }}" placeholder="{{ __('Order ID') }} OR {{ __('Buyer') }}">
                    
                    <button type="submit" name="submit" class="btn btn-primary btn-sm">
                    <i class="fa fa-dot-circle-o"></i> Search
                    </button>
                    
                    </div>
                    </form>
                    </div>
                    @if($demo_mode == 'on')
                     @include('admin.demo-mode')
                     @else
                     <form action="{{ route('admin.orders-delete') }}" method="post" id="setting_form2" enctype="multipart/form-data">
                     {{ csrf_field() }}
                     @endif
                    <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>{{ __('Orders') }}</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <input type="submit" value="Delete All" name="action" class="btn btn-danger btn-sm ml-1" id="checkBtn" onClick="return confirm('Are you sure you want to delete?');">
                </div>
            </div>
        </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">{{ __('Orders') }}</strong>
                            </div>
                            <div class="card-body">
                            <div class="table-responsive">
                                <table id="example" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="selectAll"></th>
                                            <th>{{ __('Sno') }}</th>
                                            <th>{{ __('Order ID') }}</th>
                                            <th>{{ __('Buyer') }}</th>
                                            <th>{{ __('Vendor Amount') }}</th>
                                            <th>{{ __('Admin Amount') }}</th>
                                            <th>{{ __('Processing Fee') }}</th>
                                            <th>VAT Fee</th>
                                            <th>{{ __('Payment Type') }}</th>
                                            <th>{{ __('Payment ID') }}</th>
                                            <th>{{ __('Complete Payment? (Localbank Only)') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @php $no = 1; @endphp
                                    @foreach($itemData['item'] as $order)
                                        <tr class="allChecked">
                                            <td><input type="checkbox" name="purchase_token[]" value="{{ $order->purchase_token }}"/></td>
                                            <td>{{ $no }}</td>
                                            <td>{{ $order->purchase_token }} </td>
                                            <td><a href="{{ URL::to('/user') }}/{{ $order->username }}" target="_blank" class="blue-color">{{ $order->username }}</a></td>
                                            <td>{{ Helper::plan_format($allsettings->site_currency_position,$order->vendor_amount,$order->currency_type) }} </td>
                                            <td>{{ Helper::plan_format($allsettings->site_currency_position,$order->admin_amount,$order->currency_type) }}</td>
                                            <td>{{ Helper::plan_format($allsettings->site_currency_position,$order->processing_fee,$order->currency_type) }}</td>
                                            <td>{{ Helper::plan_format($allsettings->site_currency_position,$order->vat_price,$order->currency_type) }}</td>
                                            <td>{{ $order->payment_type }}</td>
                                            <td>@if($order->payment_token != ""){{ $order->payment_token }}@else <span>---</span> @endif</td>
                                            <td>@if($order->payment_type == 'localbank' && $order->payment_status == 'pending') <a href="{{ url('/admin') }}/orders/{{ base64_encode($order->purchase_token) }}" class="blue-color"onClick="return confirm('{{ __('Are you sure click to complete payment') }}?');">{{ __('Click to Complete Payment') }}?</a> @else <span>---</span> @endif</td>
                                            <td>
                                            <a href="{{ url('/admin') }}/order-details/{{ $order->purchase_token }}" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i>&nbsp; {{ __('View More') }}</a>
                                            @if($demo_mode == 'on') 
                                            <a href="{{ url('/admin') }}/demo-mode" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp; {{ __('Delete') }}</a>
                                            @else
                                            <a href="{{ url('/admin') }}/orders/delete/{{ $encrypter->encrypt($order->purchase_token) }}" class="btn btn-danger btn-sm" onClick="return confirm('{{ __('Are you sure you want to delete') }}?');"><i class="fa fa-trash"></i>&nbsp;{{ __('Delete') }}</a>
                                            @endif
                                            </td>
                                        </tr>
                                        
                                        @php $no++; @endphp
                                   @endforeach     
                                        
                                    </tbody>
                                </table>
                                <div>
                                {{ $itemData['item']->links('pagination::bootstrap-4') }}
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
</form>
 
                </div>
            </div>
        </div>
  
    
    </div>
    @else
    @include('admin.denied')
    @endif
    


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
