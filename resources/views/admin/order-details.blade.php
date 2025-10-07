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
                       

        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>{{ __('Order Details') }}</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <a href="{{ url('/admin/orders') }}" class="btn btn-success btn-sm"><i class="fa fa-chevron-left"></i> {{ __('Back') }}</a>
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
                                <strong class="card-title">{{ __('Order Details') }}</strong>
                            </div>
                            <div class="card-body">
                                <table id="bootstrap-data-table-export" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Sno') }}</th>
                                            <th>{{ __('Item Name') }}</th>
                                            <th>{{ __('Vendor') }}</th>
                                            <th>{{ __('Purchased Date') }}</th>
                                            <th>{{ __('Coupon Code') }}</th>
                                            <th>{{ __('Coupon Type') }}</th>
                                            <th>{{ __('Discount Amount') }}</th>
                                            <th>{{ __('Vendor Amount') }}</th>
                                            <th>{{ __('Admin Amount') }}</th>
                                            <th>{{ __('Total Amount') }}</th>
                                            <th>{{ __('Payment Status') }}</th>
                                            <th>{{ __('Payment Approval') }}?</th>
                                            <th>{{ __('More Info') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @php $no = 1; @endphp
                                    @foreach($itemData['item'] as $order)
                                        <tr>
                                            <td>{{ $no }}</td>
                                            <td>{{ $order->item_name }} </td>
                                            <td><a href="{{ URL::to('/user') }}/{{ $order->username }}" target="_blank" class="blue-color">{{ $order->username }}</a></td>
                                            
                                            <td>{{ date('d F Y', strtotime($order->start_date)) }} </td>
                                            @if($order->coupon_code != "")
                                            <td>{{ $order->coupon_code }} </td>
                                            @else
                                            <td align="center">-</td>
                                            @endif
                                            @if($order->coupon_type != "")
                                            <td>{{ $order->coupon_type }} </td>
                                            @else
                                            <td align="center">-</td>
                                            @endif
                                            @if($order->coupon_type != "")
                                            @if($order->coupon_type == 'fixed')
                                            <td>{{ Helper::plan_format($allsettings->site_currency_position,$order->coupon_value,$order->currency_type) }} </td>
                                            @else
                                            @php
                                            $equ = $order->item_price - $order->discount_price;
                                            @endphp
                                            <td>{{ Helper::plan_format($allsettings->site_currency_position,$equ,$order->currency_type) }} </td>
                                            @endif
                                            @else
                                            <td align="center">-</td>
                                            @endif
                                            <td>{{ Helper::plan_format($allsettings->site_currency_position,$order->vendor_amount,$order->currency_type) }} </td>
                                            <td>{{ Helper::plan_format($allsettings->site_currency_position,$order->admin_amount,$order->currency_type) }}</td>
                                            <td>{{ Helper::plan_format($allsettings->site_currency_position,$order->total_price,$order->currency_type) }}</td>
                                            <td>@if($order->order_status == 'completed') <span class="badge badge-success">{{ __('Completed') }}</span> @else <span class="badge badge-danger">{{ __('Pending') }}</span> @endif</td>
                                            <td>
                                            @if($order->approval_status == '')
                                            <a href="{{ URL::to('/admin/order-details') }}/{{ $order->ord_id }}/vendor" class="btn btn-success btn-sm" title="{{ __('payment released to vendor') }}" onClick="return confirm('{{ __('Are you sure you will payment released to vendor') }}?');"><i class="fa fa-money"></i>&nbsp; {{ __('Waiting for approval') }}</a> 
                                            <a href="{{ URL::to('/admin/order-details') }}/{{ $order->ord_id }}/buyer" class="btn btn-danger btn-sm" title="{{ __('payment released to buyer') }}" onClick="return confirm('{{ __('Are you sure you will payment released to buyer') }}?');"><i class="fa fa-close"></i>&nbsp;{{ __('Cancel Approval') }}</a>
                                            
                                            @else
                                            {{ $order->approval_status }}
                                            @endif
                                            </td>
                                            <td><a href="{{ URL::to('/admin/more-info') }}/{{ $order->purchase_token }}" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i>&nbsp; {{ __('More Info') }}</a></td>
                                        </tr>
                                        
                                        @php $no++; @endphp
                                   @endforeach     
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

 
                </div>
            </div>
        </div>


    </div>
    @else
    @include('admin.denied')
    @endif
    


   @include('admin.javascript')


</body>

</html>
