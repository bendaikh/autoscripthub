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
    @if(in_array('dashboard',$avilable))
    <div id="right-panel" class="right-panel">

       
                       @include('admin.header')
                       

        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>{{ __('Dashboard') }}</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li class="active"><a href="{{ url('/') }}/admin/reports" class="blue-color">{{ __('More Reports') }} <span class="fa fa-angle-double-right"></span></a></li>
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
                        <i class="fa fa-users"></i>
                    </div>

                    <div class="h4 mb-0">
                        <span class="count">{{ $totalvendor }}</span>
                    </div>

                    <small class="text-muted text-uppercase font-weight-bold">{{ __('Total Vendors') }}</small>
                    <div class="progress progress-xs mt-3 mb-0 bg-flat-color-1" style="width: 40%; height: 5px;"></div>
                </div>
            </div>
            <div class="card col-md-6 no-padding ">
                <div class="card-body">
                    <div class="h1 text-muted text-right mb-4">
                        <i class="fa fa fa-file"></i>
                    </div>
                    <div class="h4 mb-0">
                        <span class="count">{{ $totalpages }}</span>
                    </div>
                    <small class="text-muted text-uppercase font-weight-bold">{{ __('Total Pages') }}</small>
                    <div class="progress progress-xs mt-3 mb-0 bg-flat-color-2" style="width: 40%; height: 5px;"></div>
                </div>
            </div>
            <div class="card col-md-6 no-padding ">
                <div class="card-body">
                    <div class="h1 text-muted text-right mb-4">
                        <i class="fa fa-cart-plus"></i>
                    </div>
                    <div class="h4 mb-0">
                        <span class="count">{{ $successorder ?? 0 }}</span>
                    </div>
                    <small class="text-muted text-uppercase font-weight-bold" title="{{ __('Completed payments only') }}">{{ __('Paid Orders') }}</small>
                    <div class="small text-muted">{{ $pendingorder ?? 0 }} {{ __('awaiting payment') }}</div>
                    <div class="progress progress-xs mt-3 mb-0 bg-flat-color-3" style="width: 40%; height: 5px;"></div>
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
                    <div class="progress progress-xs mt-3 mb-0 bg-flat-color-4" style="width: 40%; height: 5px;"></div>
                </div>
            </div>
            
            <div class="card col-md-6 no-padding ">
                <div class="card-body">
                    <div class="h1 text-muted text-right mb-4">
                        <i class="fa fa-comments-o"></i>
                    </div>
                    <div class="h4 mb-0">
                        <span class="count">{{ $itemcomment }}</span>
                    </div>
                    <small class="text-muted text-uppercase font-weight-bold">{{ __('Total Item Comments') }}</small>
                    <div class="progress progress-xs mt-3 mb-0 bg-flat-color-1" style="width: 40%; height: 5px;"></div>
                </div>
            </div>
            
            <div class="card col-md-6 no-padding ">
                <div class="card-body">
                    <div class="h1 text-muted text-right mb-4">
                        <i class="fa fa-newspaper-o"></i>
                    </div>
                    <div class="h4 mb-0"><span class="count">{{ $totalpost }}</span></div>
                    <small class="text-muted text-uppercase font-weight-bold">{{ __('Total Post') }}</small>
                    <div class="progress progress-xs mt-3 mb-0 bg-flat-color-5" style="width: 40%; height: 5px;"></div>
                </div>
            </div>
            
            
                        
        </div>
        
        
        
        <div class="card-group">
            @if($addition_settings->affiliate_referral == 1)
            <div class="card col-lg-2 col-md-6 no-padding bg-flat-color-1">
                <div class="card-body">
                    <div class="h1 text-muted text-right mb-4">
                        <i class="fa fa-money text-light"></i>
                    </div>

                    <div class="h4 mb-0 text-light">
                        <span>{{ Helper::plan_format($allsettings->site_currency_position,$total_referral_earnings,$allsettings->site_currency_symbol) }}</span>
                    </div>
                    <small class="text-uppercase font-weight-bold text-light">{{ __('Total Referral Earnings') }}</small>
                    <div class="progress progress-xs mt-3 mb-0 bg-light" style="width: 40%; height: 5px;"></div>
                </div>
            </div>
            
            <div class="card col-lg-2 col-md-6 no-padding no-shadow">
                <div class="card-body bg-flat-color-5">
                    <div class="h1 text-right text-light mb-4">
                        <i class="fa fa-user-plus"></i>
                    </div>
                    <div class="h4 mb-0 text-light">
                        <span class="count">{{ $total_referrals }}</span>
                    </div>
                    <small class="text-uppercase font-weight-bold text-light">{{ __('Total Referrals') }}</small>
                    <div class="progress progress-xs mt-3 mb-0 bg-light" style="width: 40%; height: 5px;"></div>
                </div>
            </div>
            <div class="card col-lg-2 col-md-6 no-padding no-shadow">
                <div class="card-body bg-flat-color-2">
                    <div class="h1 text-muted text-right mb-4">
                        <i class="fa fa-money text-light"></i>
                    </div>
                    <div class="h4 mb-0 text-light">
                        <span>{{ Helper::plan_format($allsettings->site_currency_position,$admin_total_referral->referral_amount,$allsettings->site_currency_symbol) }}</span>
                    </div>
                    <small class="text-uppercase font-weight-bold text-light">{{ __('Admin Referral Earnings') }}</small>
                    <div class="progress progress-xs mt-3 mb-0 bg-light" style="width: 40%; height: 5px;"></div>
                </div>
            </div>
            @endif
            <div class="card col-lg-2 col-md-6 no-padding no-shadow">
                <div class="card-body bg-flat-color-4">
                    <div class="h1 text-light text-right mb-4">
                        <i class="fa fa-credit-card"></i>
                    </div>
                    <div class="h4 mb-0 text-light">
                        <span>{{ Helper::plan_format($allsettings->site_currency_position,$payouts,$allsettings->site_currency_symbol) }}</span>
                    </div>
                    <small class="text-light text-uppercase font-weight-bold">{{ __('Total Withdrawal Payouts') }}</small>
                    <div class="progress progress-xs mt-3 mb-0 bg-light" style="width: 40%; height: 5px;"></div>
                </div>
            </div>
            <div class="card col-lg-2 col-md-6 no-padding no-shadow">
                <div class="card-body bg-flat-color-3">
                    <div class="h1 text-right mb-4">
                        <i class="fa fa-money text-light"></i>
                    </div>
                    <div class="h4 mb-0 text-light">
                        <span>{{ Helper::plan_format($allsettings->site_currency_position,$admin_total_referral->earnings,$allsettings->site_currency_symbol) }}</span>
                    </div>
                    <small class="text-light text-uppercase font-weight-bold">{{ __('Admin Total Earnings') }}</small>
                    <div class="progress progress-xs mt-3 mb-0 bg-light" style="width: 40%; height: 5px;"></div>
                </div>
            </div>
            
            
            <div class="card col-lg-2 col-md-6 no-padding no-shadow">
                <div class="card-body bg-flat-color-1">
                    <div class="h1 text-light text-right mb-4">
                        <i class="fa fa-undo"></i>
                    </div>
                    <div class="h4 mb-0 text-light">
                        <span class="count">{{ $refunds }}</span>
                    </div>
                    <small class="text-light text-uppercase font-weight-bold">{{ __('Total Refund Request') }}</small>
                    <div class="progress progress-xs mt-3 mb-0 bg-light" style="width: 40%; height: 5px;"></div>
                </div>
            </div>
        </div>
        
        
    </div>
      
        @if(!empty($visits))
        <div class="col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                        <h4 class="mb-2">{{ __('Website visits') }}</h4>
                        <form method="get" action="{{ url('/admin') }}" class="form-inline">
                            <select name="visit_range" class="form-control form-control-sm mr-2 mb-2" onchange="if(this.value!=='custom'){this.form.submit();}">
                                @foreach(['today' => __('Today'), 'yesterday' => __('Yesterday'), '7' => __('Last 7 days'), '30' => __('Last 30 days'), 'month' => __('This month'), 'custom' => __('Custom')] as $key => $label)
                                <option value="{{ $key }}" {{ ($visits['preset'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <input type="date" name="visit_from" value="{{ $visits['from'] }}" class="form-control form-control-sm mr-2 mb-2">
                            <input type="date" name="visit_to" value="{{ $visits['to'] }}" class="form-control form-control-sm mr-2 mb-2">
                            <button type="submit" class="btn btn-primary btn-sm mb-2">{{ __('Apply') }}</button>
                        </form>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="text-muted text-uppercase small font-weight-bold">{{ __('Visits') }}</div>
                            <div class="h3 mb-0">{{ number_format($visits['total_visits']) }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="text-muted text-uppercase small font-weight-bold">{{ __('Unique visitors') }}</div>
                            <div class="h3 mb-0">{{ number_format($visits['total_uniques']) }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="text-muted text-uppercase small font-weight-bold">{{ __('On the site now') }}</div>
                            <div class="h3 mb-0" id="hv-live-count">{{ number_format($visits['realtime']['count']) }}</div>
                            <div class="small text-muted">{{ __('Active in the last :minutes minutes', ['minutes' => $visits['realtime']['minutes']]) }}</div>
                        </div>
                    </div>
                    <canvas id="visits-chart" height="120"></canvas>
                    <div class="row mt-4">
                        <div class="col-lg-5 mb-3">
                            <h5 class="mb-3">{{ __('Realtime') }}</h5>
                            <div id="hv-live-list">
                                @forelse($visits['realtime']['visitors'] as $visitor)
                                <div class="d-flex justify-content-between border-bottom py-2">
                                    <span>{{ $visitor['flag'] }} {{ $visitor['path'] }}</span>
                                    <span class="text-muted">{{ $visitor['name'] }}</span>
                                </div>
                                @empty
                                <p class="text-muted mb-0">{{ __('No active visitors.') }}</p>
                                @endforelse
                            </div>
                        </div>
                        <div class="col-lg-7 mb-3">
                            <h5 class="mb-3">{{ __('Countries') }}</h5>
                            <table class="table table-sm mb-2">
                                <thead>
                                    <tr>
                                        <th>{{ __('Country') }}</th>
                                        <th class="text-right">{{ __('Visits') }}</th>
                                        <th class="text-right">{{ __('Unique') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="hv-country-body">
                                    @forelse($visits['countries'] as $country)
                                    <tr>
                                        <td>
                                            @if($country['code'] !== '')
                                            <span class="flag-icon flag-icon-{{ strtolower($country['code']) }}"></span>
                                            @endif
                                            {{ $country['flag'] }} {{ $country['name'] }}
                                            <div class="progress mt-1" style="height:6px;">
                                                <div class="progress-bar" style="width: {{ $country['percent'] }}%; background:#7c3aed;"></div>
                                            </div>
                                        </td>
                                        <td class="text-right">{{ number_format($country['visits']) }}</td>
                                        <td class="text-right">{{ number_format($country['uniques']) }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="3" class="text-muted">{{ __('No visits in this range.') }}</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <p class="text-muted small mb-0">{{ __('IP geolocation by DB-IP') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="col-sm-12 mb-4">
            <div class="card">
                <div class="card-body text-muted">{{ __('Visit analytics will appear after the site visits migration runs.') }}</div>
            </div>
        </div>
        @endif

         <div class="col-sm-8 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="mb-3">{{ __('Order Sales') }} </h4>
                                <canvas id="team-chart"></canvas>
                                @if(($view1 + $view2 + $view3 + $view4 + $view5 + $view6 + $view7) == 0)
                                <p class="text-muted small mb-0 mt-2"><i class="fa fa-info-circle"></i> {{ __('No paid orders in the last 7 days.') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>   
          <div class="col-sm-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="mb-3">{{ __('Items') }} </h4>
                                <canvas id="pieChart"></canvas>
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

   
    var ctx = document.getElementById( "team-chart" );
    ctx.height = 150;
    var myChart = new Chart( ctx, {
        type: 'line',
        data: {
            labels: [ "{{ $sixth_day }}", "{{ $fifth_day }}", "{{ $fourth_day }}", "{{ $third_day }}", "{{ $second_day }}", "{{ $first_day }}", "{{ $today }}" ],
            type: 'line',
            defaultFontFamily: 'Montserrat',
            datasets: [ {
                data: [ {{ $view7 }} , {{ $view6 }}, {{ $view5 }}, {{ $view4 }} , {{ $view3 }} , {{ $view2 }} , {{ $view1 }} ],
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
                        labelString: '{{ __('Month') }}'
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
                        labelString: '{{ __('Sales') }}'
                    }
                        } ]
            },
            title: {
                display: false,
            }
        }
    } );


    @if(!empty($visits))
    var visitCtx = document.getElementById('visits-chart');
    if (visitCtx) {
        visitCtx.height = 120;
        new Chart(visitCtx, {
            type: 'line',
            data: {
                labels: @json($visits['labels']),
                datasets: [{
                    label: "{{ __('Visits') }}",
                    data: @json($visits['visits']),
                    borderColor: 'rgba(124,58,237,0.9)',
                    backgroundColor: 'rgba(124,58,237,0.15)',
                    borderWidth: 3,
                    pointRadius: 3,
                    fill: true
                }, {
                    label: "{{ __('Unique visitors') }}",
                    data: @json($visits['uniques']),
                    borderColor: 'rgba(14,165,233,0.9)',
                    backgroundColor: 'rgba(14,165,233,0.08)',
                    borderWidth: 3,
                    pointRadius: 3,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                legend: { display: true },
                scales: {
                    xAxes: [{ gridLines: { display: false } }],
                    yAxes: [{ ticks: { beginAtZero: true, precision: 0 } }]
                }
            }
        });
    }
    (function () {
        var list = document.getElementById('hv-live-list');
        var count = document.getElementById('hv-live-count');
        if (!list || !count) return;
        function paint(data) {
            count.textContent = data.count || 0;
            if (!data.visitors || !data.visitors.length) {
                list.innerHTML = '<p class="text-muted mb-0">{{ __('No active visitors.') }}</p>';
                return;
            }
            list.innerHTML = data.visitors.map(function (visitor) {
                var path = String(visitor.path || '').replace(/[&<>"]/g, function (ch) {
                    return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[ch];
                });
                var name = String(visitor.name || '').replace(/[&<>"]/g, function (ch) {
                    return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[ch];
                });
                return '<div class="d-flex justify-content-between border-bottom py-2"><span>' + (visitor.flag || '') + ' ' + path + '</span><span class="text-muted">' + name + '</span></div>';
            }).join('');
        }
        function refresh() {
            if (document.hidden) return;
            fetch('{{ url('/admin/visits/live') }}', { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (response) { return response.json(); })
                .then(paint)
                .catch(function () {});
        }
        window.setInterval(refresh, 20000);
    })();
    @endif

    //pie chart
    var ctx = document.getElementById( "pieChart" );
    ctx.height = 330;
    var myChart = new Chart( ctx, {
        type: 'pie',
        data: {
            datasets: [ {
                data: [ {{ $approved }}, {{ $unapproved }}, {{ $rejected }} ],
                borderWidth: 0,
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
                            "{{ __('Approved') }}",
                            "{{ __('UnApproved') }}",
                            "{{ __('Rejected') }}"
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