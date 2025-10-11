<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en">
<!--<![endif]-->

<head>
    
    <?php echo $__env->make('admin.stylesheet', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    
</head>

<body>


   <?php echo $__env->make('admin.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!-- Right Panel -->
    <?php if(in_array('dashboard',$avilable)): ?>
    <div id="right-panel" class="right-panel">

       
                       <?php echo $__env->make('admin.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                       

        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1><?php echo e(__('Dashboard')); ?></h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li class="active"><a href="<?php echo e(url('/')); ?>/admin/reports" class="blue-color"><?php echo e(__('More Reports')); ?> <span class="fa fa-angle-double-right"></span></a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <?php echo $__env->make('admin.warning', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> 
        <div class="content mt-3">

            

        <div class="col-sm-12 mb-4">
        <div class="card-group">
            <div class="card col-md-6 no-padding ">
                <div class="card-body">
                    <div class="h1 text-muted text-right mb-4">
                        <i class="fa fa-users"></i>
                    </div>

                    <div class="h4 mb-0">
                        <span class="count"><?php echo e($totalvendor); ?></span>
                    </div>

                    <small class="text-muted text-uppercase font-weight-bold"><?php echo e(__('Total Vendors')); ?></small>
                    <div class="progress progress-xs mt-3 mb-0 bg-flat-color-1" style="width: 40%; height: 5px;"></div>
                </div>
            </div>
            <div class="card col-md-6 no-padding ">
                <div class="card-body">
                    <div class="h1 text-muted text-right mb-4">
                        <i class="fa fa fa-file"></i>
                    </div>
                    <div class="h4 mb-0">
                        <span class="count"><?php echo e($totalpages); ?></span>
                    </div>
                    <small class="text-muted text-uppercase font-weight-bold"><?php echo e(__('Total Pages')); ?></small>
                    <div class="progress progress-xs mt-3 mb-0 bg-flat-color-2" style="width: 40%; height: 5px;"></div>
                </div>
            </div>
            <div class="card col-md-6 no-padding ">
                <div class="card-body">
                    <div class="h1 text-muted text-right mb-4">
                        <i class="fa fa-cart-plus"></i>
                    </div>
                    <div class="h4 mb-0">
                        <span class="count"><?php echo e($totalorder); ?></span>
                    </div>
                    <small class="text-muted text-uppercase font-weight-bold"><?php echo e(__('Total Orders')); ?></small>
                    <div class="progress progress-xs mt-3 mb-0 bg-flat-color-3" style="width: 40%; height: 5px;"></div>
                </div>
            </div>
            <div class="card col-md-6 no-padding ">
                <div class="card-body">
                    <div class="h1 text-muted text-right mb-4">
                        <i class="fa fa-server"></i>
                    </div>
                    <div class="h4 mb-0">
                        <span class="count"><?php echo e($totalitems); ?></span>
                    </div>
                    <small class="text-muted text-uppercase font-weight-bold"><?php echo e(__('Total Items')); ?></small>
                    <div class="progress progress-xs mt-3 mb-0 bg-flat-color-4" style="width: 40%; height: 5px;"></div>
                </div>
            </div>
            
            <div class="card col-md-6 no-padding ">
                <div class="card-body">
                    <div class="h1 text-muted text-right mb-4">
                        <i class="fa fa-comments-o"></i>
                    </div>
                    <div class="h4 mb-0">
                        <span class="count"><?php echo e($itemcomment); ?></span>
                    </div>
                    <small class="text-muted text-uppercase font-weight-bold"><?php echo e(__('Total Item Comments')); ?></small>
                    <div class="progress progress-xs mt-3 mb-0 bg-flat-color-1" style="width: 40%; height: 5px;"></div>
                </div>
            </div>
            
            <div class="card col-md-6 no-padding ">
                <div class="card-body">
                    <div class="h1 text-muted text-right mb-4">
                        <i class="fa fa-newspaper-o"></i>
                    </div>
                    <div class="h4 mb-0"><span class="count"><?php echo e($totalpost); ?></span></div>
                    <small class="text-muted text-uppercase font-weight-bold"><?php echo e(__('Total Post')); ?></small>
                    <div class="progress progress-xs mt-3 mb-0 bg-flat-color-5" style="width: 40%; height: 5px;"></div>
                </div>
            </div>
            
            
                        
        </div>
        
        
        
        <div class="card-group">
            <?php if($addition_settings->affiliate_referral == 1): ?>
            <div class="card col-lg-2 col-md-6 no-padding bg-flat-color-1">
                <div class="card-body">
                    <div class="h1 text-muted text-right mb-4">
                        <i class="fa fa-money text-light"></i>
                    </div>

                    <div class="h4 mb-0 text-light">
                        <span><?php echo e(Helper::plan_format($allsettings->site_currency_position,$total_referral_earnings,$allsettings->site_currency_symbol)); ?></span>
                    </div>
                    <small class="text-uppercase font-weight-bold text-light"><?php echo e(__('Total Referral Earnings')); ?></small>
                    <div class="progress progress-xs mt-3 mb-0 bg-light" style="width: 40%; height: 5px;"></div>
                </div>
            </div>
            
            <div class="card col-lg-2 col-md-6 no-padding no-shadow">
                <div class="card-body bg-flat-color-5">
                    <div class="h1 text-right text-light mb-4">
                        <i class="fa fa-user-plus"></i>
                    </div>
                    <div class="h4 mb-0 text-light">
                        <span class="count"><?php echo e($total_referrals); ?></span>
                    </div>
                    <small class="text-uppercase font-weight-bold text-light"><?php echo e(__('Total Referrals')); ?></small>
                    <div class="progress progress-xs mt-3 mb-0 bg-light" style="width: 40%; height: 5px;"></div>
                </div>
            </div>
            <div class="card col-lg-2 col-md-6 no-padding no-shadow">
                <div class="card-body bg-flat-color-2">
                    <div class="h1 text-muted text-right mb-4">
                        <i class="fa fa-money text-light"></i>
                    </div>
                    <div class="h4 mb-0 text-light">
                        <span><?php echo e(Helper::plan_format($allsettings->site_currency_position,$admin_total_referral->referral_amount,$allsettings->site_currency_symbol)); ?></span>
                    </div>
                    <small class="text-uppercase font-weight-bold text-light"><?php echo e(__('Admin Referral Earnings')); ?></small>
                    <div class="progress progress-xs mt-3 mb-0 bg-light" style="width: 40%; height: 5px;"></div>
                </div>
            </div>
            <?php endif; ?>
            <div class="card col-lg-2 col-md-6 no-padding no-shadow">
                <div class="card-body bg-flat-color-4">
                    <div class="h1 text-light text-right mb-4">
                        <i class="fa fa-credit-card"></i>
                    </div>
                    <div class="h4 mb-0 text-light">
                        <span><?php echo e(Helper::plan_format($allsettings->site_currency_position,$payouts,$allsettings->site_currency_symbol)); ?></span>
                    </div>
                    <small class="text-light text-uppercase font-weight-bold"><?php echo e(__('Total Withdrawal Payouts')); ?></small>
                    <div class="progress progress-xs mt-3 mb-0 bg-light" style="width: 40%; height: 5px;"></div>
                </div>
            </div>
            <div class="card col-lg-2 col-md-6 no-padding no-shadow">
                <div class="card-body bg-flat-color-3">
                    <div class="h1 text-right mb-4">
                        <i class="fa fa-money text-light"></i>
                    </div>
                    <div class="h4 mb-0 text-light">
                        <span><?php echo e(Helper::plan_format($allsettings->site_currency_position,$admin_total_referral->earnings,$allsettings->site_currency_symbol)); ?></span>
                    </div>
                    <small class="text-light text-uppercase font-weight-bold"><?php echo e(__('Admin Total Earnings')); ?></small>
                    <div class="progress progress-xs mt-3 mb-0 bg-light" style="width: 40%; height: 5px;"></div>
                </div>
            </div>
            
            
            <div class="card col-lg-2 col-md-6 no-padding no-shadow">
                <div class="card-body bg-flat-color-1">
                    <div class="h1 text-light text-right mb-4">
                        <i class="fa fa-undo"></i>
                    </div>
                    <div class="h4 mb-0 text-light">
                        <span class="count"><?php echo e($refunds); ?></span>
                    </div>
                    <small class="text-light text-uppercase font-weight-bold"><?php echo e(__('Total Refund Request')); ?></small>
                    <div class="progress progress-xs mt-3 mb-0 bg-light" style="width: 40%; height: 5px;"></div>
                </div>
            </div>
        </div>
        
        
    </div>
      
         <div class="col-sm-8 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="mb-3"><?php echo e(__('Order Sales')); ?> </h4>
                                <canvas id="team-chart"></canvas>
                            </div>
                        </div>
                    </div>   
          <div class="col-sm-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="mb-3"><?php echo e(__('Items')); ?> </h4>
                                <canvas id="pieChart"></canvas>
                            </div>
                        </div><!-- /# card -->
                    </div>

        </div> <!-- .content -->
    </div><!-- /#right-panel -->
    <?php else: ?>
    <?php echo $__env->make('admin.denied', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
    <!-- Right Panel -->

    <?php echo $__env->make('admin.javascript', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script type="text/javascript">
	( function ( $ ) {
    'use strict';

   
    var ctx = document.getElementById( "team-chart" );
    ctx.height = 150;
    var myChart = new Chart( ctx, {
        type: 'line',
        data: {
            labels: [ "<?php echo e($sixth_day); ?>", "<?php echo e($fifth_day); ?>", "<?php echo e($fourth_day); ?>", "<?php echo e($third_day); ?>", "<?php echo e($second_day); ?>", "<?php echo e($first_day); ?>", "<?php echo e($today); ?>" ],
            type: 'line',
            defaultFontFamily: 'Montserrat',
            datasets: [ {
                data: [ <?php echo e($view7); ?> , <?php echo e($view6); ?>, <?php echo e($view5); ?>, <?php echo e($view4); ?> , <?php echo e($view3); ?> , <?php echo e($view2); ?> , <?php echo e($view1); ?> ],
                label: "sale",
                backgroundColor: 'rgba(0,103,255,.15)',
                borderColor: 'rgba(0,103,255,0.5)',
                borderWidth: 3.5,
                pointStyle: 'circle',
                pointRadius: 5,
                pointBorderColor: 'transparent',
                pointBackgroundColor: 'rgba(0,103,255,0.5)',
                    }, ]
        },
        options: {
            responsive: true,
            tooltips: {
                mode: 'index',
                titleFontSize: 12,
                titleFontColor: '#000',
                bodyFontColor: '#000',
                backgroundColor: '#fff',
                titleFontFamily: 'Montserrat',
                bodyFontFamily: 'Montserrat',
                cornerRadius: 3,
                intersect: false,
            },
            legend: {
                display: false,
                position: 'top',
                labels: {
                    usePointStyle: true,
                    fontFamily: 'Montserrat',
                },


            },
            scales: {
                xAxes: [ {
                    display: true,
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    scaleLabel: {
                        display: false,
                        labelString: '<?php echo e(__('Month')); ?>'
                    }
                        } ],
                yAxes: [ {
                    display: true,
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    scaleLabel: {
                        display: true,
                        labelString: '<?php echo e(__('Sales')); ?>'
                    }
                        } ]
            },
            title: {
                display: false,
            }
        }
    } );


    //pie chart
    var ctx = document.getElementById( "pieChart" );
    ctx.height = 330;
    var myChart = new Chart( ctx, {
        type: 'pie',
        data: {
            datasets: [ {
                data: [ <?php echo e($approved); ?>, <?php echo e($unapproved); ?>, <?php echo e($rejected); ?> ],
                backgroundColor: [
                                    "rgba(6, 163, 61, 1)",
                                    "rgba(255, 193, 7, 1)",
                                    "rgba(226, 27, 26, 1)"
                                    
                                ],
                hoverBackgroundColor: [
                                    "rgba(6, 163, 61, 0.7)",
                                    "rgba(255, 193, 7, 0.7)",
                                    "rgba(226, 27, 26, 0.7)"
                                    
                                ]

                            } ],
            labels: [
                            "<?php echo e(__('Approved')); ?>",
                            "<?php echo e(__('UnApproved')); ?>",
                            "<?php echo e(__('Rejected')); ?>"
                        ]
        },
        options: {
            responsive: true
        }
    } );

    

} )( jQuery );

</script>
</body>
</html><?php /**PATH C:\laragon\www\autoscripthub\resources\views/admin/index.blade.php ENDPATH**/ ?>