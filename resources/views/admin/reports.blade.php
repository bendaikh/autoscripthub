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
    @if(in_array('reports',$avilable))
    <div id="right-panel" class="right-panel">

       
                       @include('admin.header')
                       

        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>{{ __('Reports') }}</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li class="active"></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.warning')
        <div class="content mt-3">

            

        <div class="col-sm-12 mb-4">
        <div class="card-group">
            <div class="card col-md-6 no-padding ">
                <div class="card-body">
                    <div class="h1 text-muted text-right mb-4">
                        <i class="fa fa-user"></i>
                    </div>

                    <div class="h4 mb-0">
                        <span class="count">{{ $visitorcount }}</span>
                    </div>

                    <small class="text-muted text-uppercase font-weight-bold">{{ __('Total Visit') }}</small>
                    <div class="progress progress-xs mt-3 mb-0 bg-flat-color-6" style="width: 40%; height: 5px;"></div>
                </div>
            </div> <?php /*?>fa fa fa-file<?php */?>
            <div class="card col-md-6 no-padding ">
                <div class="card-body">
                    <div class="h1 text-muted text-right mb-4">
                        <i class="fa fa-users"></i>
                    </div>
                    <div class="h4 mb-0">
                        <span class="count">{{ $totalvendor }}</span>
                    </div>
                    <small class="text-muted text-uppercase font-weight-bold">{{ __('Total Vendors') }}</small>
                    <div class="progress progress-xs mt-3 mb-0 bg-flat-color-7" style="width: 40%; height: 5px;"></div>
                </div>
            </div>
            <div class="card col-md-6 no-padding ">
                <div class="card-body">
                    <div class="h1 text-muted text-right mb-4">
                        <i class="fa fa-users"></i>
                    </div>

                    <div class="h4 mb-0">
                        <span class="count">{{ $totalnewvendor }}</span>
                    </div>

                    <small class="text-muted text-uppercase font-weight-bold">{{ __('Total New Vendors') }}</small>
                    <div class="progress progress-xs mt-3 mb-0 bg-flat-color-7" style="width: 40%; height: 5px;"></div>
                </div>
            </div>
            <div class="card col-md-6 no-padding ">
                <div class="card-body">
                    <div class="h1 text-muted text-right mb-4">
                        <i class="fa fa-server"></i>
                    </div>
                    <div class="h4 mb-0">
                        <span class="count">{{ $totalitems }}</span>
                    </div>
                    <small class="text-muted text-uppercase font-weight-bold">{{ __('Total Items') }}</small>
                    <div class="progress progress-xs mt-3 mb-0 bg-flat-color-3" style="width: 40%; height: 5px;"></div>
                </div>
            </div>
            <div class="card col-md-6 no-padding ">
                <div class="card-body">
                    <div class="h1 text-muted text-right mb-4">
                        <i class="fa fa-server"></i>
                    </div>

                    <div class="h4 mb-0">
                        <span class="count">{{ $totalnewitems }}</span>
                    </div>

                    <small class="text-muted text-uppercase font-weight-bold">{{ __('Total New Items') }}</small>
                    <div class="progress progress-xs mt-3 mb-0 bg-flat-color-3" style="width: 40%; height: 5px;"></div>
                </div>
            </div>
            
            
            
            
            
                        
        </div>
        
        
        
        
        
        
    </div>
      <div class="col-sm-12 mb-4">
        <div class="card-group">
             <?php /*?>fa fa fa-file<?php */?>
            <div class="card col-md-6 no-padding ">
                <div class="card-body">
                    <div class="h1 text-muted text-right mb-4">
                        <i class="fa fa-shopping-cart"></i>
                    </div>
                    <div class="h4 mb-0">
                        <span class="count">{{ $totalorder }}</span>
                    </div>
                    <small class="text-muted text-uppercase font-weight-bold">{{ __('Total Orders') }}</small>
                    <div class="progress progress-xs mt-3 mb-0 bg-flat-color-8" style="width: 40%; height: 5px;"></div>
                </div>
            </div>
            <div class="card col-md-6 no-padding ">
                <div class="card-body">
                    <div class="h1 text-muted text-right mb-4">
                        <i class="fa fa-shopping-cart"></i>
                    </div>
                    <div class="h4 mb-0">
                        <span class="count">{{ $totalneworder }}</span>
                    </div>
                    <small class="text-muted text-uppercase font-weight-bold">{{ __('Total New Orders') }}</small>
                    <div class="progress progress-xs mt-3 mb-0 bg-flat-color-8" style="width: 40%; height: 5px;"></div>
                </div>
            </div>
            <div class="card col-md-6 no-padding ">
                <div class="card-body">
                    <div class="h1 text-muted text-right mb-4">
                        <i class="fa fa fa-check"></i>
                    </div>
                    <div class="h4 mb-0">
                        <span class="count">{{ $successorder }}</span>
                    </div>
                    <small class="text-muted text-uppercase font-weight-bold">{{ __('Total Successful Order') }}</small>
                    <div class="progress progress-xs mt-3 mb-0 bg-flat-color-5" style="width: 40%; height: 5px;"></div>
                </div>
            </div>
            
            <div class="card col-md-6 no-padding ">
                <div class="card-body">
                    <div class="h1 text-muted text-right mb-4">
                        <i class="fa fa-close"></i>
                    </div>
                    <div class="h4 mb-0">
                        <span class="count">{{ $failedorder }}</span>
                    </div>
                    <small class="text-muted text-uppercase font-weight-bold">{{ __('Total Not Successful Order') }}</small>
                    <div class="progress progress-xs mt-3 mb-0 bg-flat-color-4" style="width: 40%; height: 5px;"></div>
                </div>
            </div>
            
            <div class="card col-md-6 no-padding ">
                <div class="card-body">
                    <div class="h1 text-muted text-right mb-4">
                        <i class="fa fa-money"></i>
                    </div>
                    <div class="h4 mb-0"><span class="count">{{ $totalamount }}</span></div>
                    <small class="text-muted text-uppercase font-weight-bold">{{ __('Total Amount') }} ({{ $allsettings->site_currency_symbol }})</small>
                    <div class="progress progress-xs mt-3 mb-0 bg-flat-color-1" style="width: 40%; height: 5px;"></div>
                </div>
            </div>
            
            
            
            
                        
        </div>
        
        
        
        
        
        
    </div>
         <div class="col-sm-4 mb-4">
         <div class="card">
                            <div class="card-body">
                                <h4 class="mb-3">{{ __('Orders') }} </h4>
                                <canvas id="pieChart"></canvas>
                            </div>
                        </div>
                        
                    </div>   
          <div class="col-sm-8 mb-4">
                        <div class="row">
                     
                    <div class="col-md-12">
                       <h4 class="mb-3">{{ __('Search') }} </h4>
                        
                        
                      
                        <div class="card">
                           @if($demo_mode == 'on')
                           @include('admin.demo-mode')
                           @else
                           <form action="{{ route('admin.reports') }}" method="post" id="setting_form" enctype="multipart/form-data">
                           {{ csrf_field() }}
                           @endif
                           <?php /*?><div class="col-md-4">
                            
                            <div class="card-body">
                                
                                <div id="pay-invoice">
                                    <div class="card-body">
                                           
                                          <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Choose') }}</label>
                                                <select name="types" class="form-control">
                                                <option value="0" >{{ __('All') }}</option>
                                                <option value="1">{{ __('Total Visit') }}</option>
                                                <option value="2">{{ __('Total New Vendor') }}</option>
                                                <option value="3">{{ __('Total New Items') }}</option>
                                                <option value="4">{{ __('Total Successful Order') }}</option>
                                                <option value="5">{{ __('Total Not Successful Order') }}</option>
                                                <option value="6">{{ __('Total Amount') }}</option>
                                                </select>
                                            </div>    
                                        
                                    </div>
                                </div>

                            </div>
                            </div><?php */?>
                            
                            
                            
                             <div class="col-md-6">
                             
                             
                             <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                             
                             
                                                
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('From Date') }}</label>
                                                <input id="from_date" name="from_date" type="text" value="{{ $from_date }}" class="form-control noscroll_textarea">
                                            </div> 
                                            
                                           
                             
                             
                             </div>
                                </div>

                            </div>
                             
                             
                             
                             </div>
                             <div class="col-md-6">
                             
                             
                             <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                             
                             
                                                
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('To Date') }}</label>
                                                <input id="to_date" name="to_date" type="text" value="{{ $to_date }}" class="form-control noscroll_textarea">
                                            </div> 
                                            
                                          
                             
                             
                             </div>
                                </div>

                            </div>
                             
                             
                             
                             </div>
                             <div class="col-md-12 no-padding">
                             <div class="card-footer">
                                 <button type="submit" name="submit" class="btn btn-primary btn-sm"><i class="fa fa-dot-circle-o"></i> {{ __('Search') }}</button>
                                 <button type="reset" class="btn btn-danger btn-sm"><i class="fa fa-ban"></i> {{ __('Reset') }} </button>
                             </div>
                             
                             </div>
                             
                            
                            </form>
                            
                                                    
                                                    
                                                 
                            
                        </div> 

                     
                    
                    
                    </div>
                    

                </div><!-- /# card -->
                    </div>

        </div> <!-- .content -->
    </div><!-- /#right-panel -->
    @else
    @include('admin.denied')
    @endif
    <!-- Right Panel -->

    @include('admin.javascript')
    
    <script type="text/javascript">
	( function ( $ ) {
    'use strict';

   
    

    var ctx = document.getElementById( "pieChart" );
    ctx.height = 330;
    var myChart = new Chart( ctx, {
        type: 'pie',
        data: {
            datasets: [ {
                data: [ {{ $successorder }}, {{ $failedorder }} ],
                backgroundColor: [
                                    "rgba(6, 163, 61, 1)",
                                    "rgba(226, 27, 26, 1)"
                                    
                                ],
                hoverBackgroundColor: [
                                    "rgba(6, 163, 61, 0.7)",
                                    "rgba(226, 27, 26, 0.7)"
                                    
                                ]

                            } ],
            labels: [
                            "{{ __('Successful') }}",
                            "{{ __('Not Successful') }}"
                        ]
        },
        options: {
            responsive: true
        }
    } );

    

} )( jQuery );

</script>
</body>
</html>