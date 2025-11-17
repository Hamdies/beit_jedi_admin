@extends('layouts.vendor.app')

@section('title',translate('messages.dashboard'))

@push('css_or_js')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap');

    :root {
        --primary-navy: #2C3E6F;
        --primary-navy-light: #3d5278;
        --primary-navy-dark: #1f2d4d;
        --accent-yellow: #F4D03F;
        --bg-light: #f8f9fa;
        --text-dark: #1a1a1a;
        --text-muted: #6b7280;
    }
    
    body,
    .modern-dashboard {
        font-family: 'Cairo', -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .modern-dashboard {
        background: #f5f7fa;
        min-height: 100vh;
        padding: 2rem 0;
    }
    
    .modern-page-header {
        background: white;
        border-radius: 24px;
        padding: 2.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        margin-bottom: 2rem;
        border: 1px solid rgba(44, 62, 111, 0.06);
        animation: slideDown 0.5s ease-out;
        position: relative;
        overflow: hidden;
    }
    
    .modern-page-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-navy) 0%, var(--accent-yellow) 100%);
    }
    
    .modern-page-title {
        font-size: 2.25rem;
        font-weight: 800;
        color: var(--text-dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 1rem;
        letter-spacing: -0.5px;
    }
    
    .modern-page-icon {
        width: 56px;
        height: 56px;
        background: linear-gradient(135deg, var(--primary-navy) 0%, var(--primary-navy-light) 100%);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 28px;
        box-shadow: 0 4px 12px rgba(44, 62, 111, 0.2);
    }
    
    .modern-subtitle {
        color: var(--text-muted);
        font-size: 1rem;
        margin-top: 0.5rem;
        font-weight: 500;
    }
    
    /* Modern Alert Styling */
    .hide-warning {
        border-radius: 16px !important;
        border: 1px solid rgba(239, 68, 68, 0.1) !important;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.08) !important;
        margin-bottom: 1.5rem !important;
        animation: slideDown 0.5s ease-out;
    }
    
    /* Chart Improvements */
    .chartjs-custom {
        background: transparent;
        padding: 0;
    }
    
    /* Smooth Scrolling */
    html {
        scroll-behavior: smooth;
    }
    
    /* Custom Select Styling */
    .custom-select {
        transition: all 0.3s ease;
    }
    
    .custom-select:focus {
        border-color: var(--primary-navy);
        box-shadow: 0 0 0 3px rgba(44, 62, 111, 0.1);
        outline: none;
    }

    /* RTL friendly dropdown for order stats */
    .order-stats-select {
        direction: rtl;
        text-align: right;
    }
    .order-stats-select option {
        direction: rtl;
        text-align: right;
    }
    
    /* Card Hover Effects */
    .card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .order-stats-filter {
        margin-left: 0;
    }

    .yearly-stats-summary {
        margin-left: auto;
    }
    
    /* Badge Styling */
    .badge-soft-primary {
        background: linear-gradient(135deg, rgba(44, 62, 111, 0.1) 0%, rgba(244, 208, 63, 0.1) 100%);
        color: var(--primary-navy);
        font-weight: 600;
        border: 1px solid rgba(44, 62, 111, 0.1);
    }
    
    /* Responsive Typography */
    @media (max-width: 768px) {
        .modern-page-title {
            font-size: 1.75rem;
        }
        .modern-page-icon {
            width: 48px;
            height: 48px;
            font-size: 24px;
        }
        .modern-page-header {
            padding: 1.5rem;
        }
    }
</style>
@endpush

@section('content')
    <div class="content container-fluid modern-dashboard">
        @if(auth('vendor')->check())
        <!-- Modern Page Header -->
        <div class="modern-page-header">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div>
                    <h1 class="modern-page-title">
                        <div class="modern-page-icon">
                            <i class="tio-dashboard-outlined"></i>
                        </div>
                        <span>لوحة التحكم</span>
                    </h1>
                    <p class="modern-subtitle mb-0">متابعة شاملة لأداء المطعم وطلبات العملاء</p>
                </div>
                <div class="text-right">
                    <span class="badge badge-soft-primary px-3 py-2" style="font-size: 0.9rem;">
                        <i class="tio-restaurant mr-1"></i>
                        {{ \App\CentralLogics\Helpers::get_restaurant_data()->name }}
                    </span>
                </div>
            </div>
        </div>
        <!-- End Modern Page Header -->


            @if ( Session::get('stock_out_reminder_close_btn') !== true && isset($out_out_count) && $out_out_count  > 99 )
                    <div class="alert __alert-4 m-0 py-1 px-2 hide-warning" role="alert">
                        <div class="alert-inner">
                            <img class="rounded mr-1"  width="25" src="{{ dynamicAsset('/public/assets/admin/img/invalid-icon.png') }}" alt="">
                            <div class="cont">
                                <h4 class="mb-2">{{ translate('Warning!') }} </h4>{{  translate('There_isn’t_enough_quantity_on_stock._Please_check_products_in_product_list') }}<a  data-id="stock_out_reminder_close_btn" id="hide-warning"  class="text-primary text-underline add-to-session">{{ translate('remind_me_later') }}</a>  &nbsp; &nbsp; <a href="{{ route('vendor.food.stockOutList') }}" class="text-primary text-underline">{{ translate('Click_To_View') }}</a>
                            </div>
                        </div>
                            <button class="position-absolute right-0 top-50 py-2 px-2 bg-transparent border-0 outline-none shadow-none" id="hide-warning-btn"  type="button">
                                <i class="tio-clear fz--18"></i>
                            </button>
                    </div>
            @elseif ( Session::get('stock_out_reminder_close_btn') !== true && isset($out_out_count) && $out_out_count  <= 99 &&  $out_out_count  > 1 )
                    <div class="alert __alert-4 m-0 py-1 px-2 hide-warning max-w-450px" role="alert">
                        <div class="alert-inner">
                            <img class="rounded mr-1"  width="25" src="{{ dynamicAsset('/public/assets/admin/img/invalid-icon.png') }}" alt="">
                            <div class="cont">
                                <h4 class="mb-2">{{ translate('Warning!') }} </h4>{{  ( $out_out_count -1).'+ '.  translate('more_products_have_out_of_stock.') }}
                                <br>
                                <a data-id="stock_out_reminder_close_btn" id="hide-warning"  class="text-primary text-underline add-to-session">{{ translate('remind_me_later') }}</a>  &nbsp; &nbsp; <a href="{{ route('vendor.food.stockOutList') }}" class="text-primary text-underline">{{ translate('Click_To_View') }}</a>
                            </div>
                        </div>
                        <button class="position-absolute right-0 top-50 py-2 px-2 bg-transparent border-0 outline-none shadow-none" id="hide-warning-btn"  type="button">
                            <i class="tio-clear fz--18"></i>
                        </button>
                    </div>

                     @elseif ( Session::get('stock_out_reminder_close_btn') !== true && isset($out_out_count)  &&  $out_out_count  == 1  && isset($food))

                     <div class="alert __alert-4 m-0 py-1 px-2 hide-warning max-w-450px" role="alert">
                        <div class="alert-inner">
                            <img class="aspect-1-1 mr-1 object--contain rounded" width="100" src="{{ $food?->image_full_url ?? dynamicAsset('/public/assets/admin/img/100x100/food-default-image.png') }}" alt="">
                            <div class="cont">
                                <h4 class="mb-2">{{ $food?->name }} </h4>{{  translate('This product is out of stock.') }}
                                <br>
                                <a
                                data-id="stock_out_reminder_close_btn" id="hide-warning"  class="text-primary text-underline add-to-session">{{ translate('remind_me_later') }}</a>  &nbsp; &nbsp; <a href="{{ route('vendor.food.stockOutList') }}" class="text-primary text-underline">{{ translate('Click_To_View') }}</a>
                            </div>
                        </div>
                        <button class="position-absolute right-0 top-50 py-2 px-2 bg-transparent border-0 outline-none shadow-none" id="hide-warning-btn"  type="button">
                            <i class="tio-clear fz--18"></i>
                        </button>
                    </div>

                @endif




        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card border-0" style="border-radius: 24px; overflow: hidden; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02); border: 1px solid rgba(44, 62, 111, 0.06);">
                    <div class="card-header bg-white border-0 p-4" style="border-bottom: 1px solid rgba(0,0,0,0.03);">
                        <div class="d-flex flex-wrap justify-content-between align-items-center w-100">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, rgba(44, 62, 111, 0.1) 0%, rgba(244, 208, 63, 0.1) 100%); border-radius: 14px; display: flex; align-items: center; justify-content: center;">
                                    <i class="tio-chart-bar-1" style="color: #2C3E6F; font-size: 24px;"></i>
                                </div>
                                <div>
                                    <h4 class="mb-0 font-weight-bold" style="color: #1a1a1a; font-size: 1.35rem; letter-spacing: -0.3px;">
                                        إحصائيات الطلبات
                                    </h4>
                                    <p class="mb-0 mt-1" style="color: #9ca3af; font-size: 0.875rem;">تابِع أداء طلبات مطعمك بشكل يومي وواضح</p>
                                </div>
                            </div>
                            <div class="order-stats-filter">
                                <select class="custom-select order-stats-select order_stats_update" name="statistics_type" style="border-radius: 12px; border: 1px solid rgba(44, 62, 111, 0.12); padding: 0.625rem 1.25rem; min-width: 220px; font-weight: 500; color: #2C3E6F;">
                                    <option value="overall" {{$params['statistics_type'] == 'overall'?'selected':''}}>
                                        إجمالي الإحصائيات
                                    </option>
                                    <option value="today" {{$params['statistics_type'] == 'today'?'selected':''}}>
                                        إحصائيات اليوم
                                    </option>
                                    <option value="this_month" {{$params['statistics_type'] == 'this_month'?'selected':''}}>
                                        إحصائيات هذا الشهر
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4" id="order_stats">
                            @include('vendor-views.partials._dashboard-order-stats',['data'=>$data])
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-12 mb-3">
                <!-- Ultra Modern Chart Card -->
                <div class="card border-0" style="border-radius: 24px; overflow: hidden; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02); border: 1px solid rgba(44, 62, 111, 0.06);">
                    <div class="card-header bg-white border-0 p-4" style="border-bottom: 1px solid rgba(0,0,0,0.03);">
                        <div class="d-flex flex-wrap align-items-center w-100">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, rgba(44, 62, 111, 0.1) 0%, rgba(244, 208, 63, 0.1) 100%); border-radius: 14px; display: flex; align-items: center; justify-content: center;">
                                    <i class="tio-chart-bar-4" style="color: #2C3E6F; font-size: 24px;"></i>
                                </div>
                                <div>
                                    <h4 class="mb-0 font-weight-bold" style="color: #1a1a1a; font-size: 1.35rem; letter-spacing: -0.3px;">
                                        الإحصائيات السنوية
                                    </h4>
                                    <p class="mb-0 mt-1" style="color: #9ca3af; font-size: 0.875rem;">نظرة عامة على أداء المطعم خلال العام</p>
                                </div>
                            </div>
                            <div class="yearly-stats-summary d-flex flex-wrap gap-3 align-items-center">
                                @php($amount=array_sum($earning))
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width: 12px; height: 12px; background: #2C3E6F; border-radius: 50%;"></div>
                                    <span style="color: #6b7280; font-size: 0.875rem; font-weight: 500;">إجمالي الأرباح</span>
                                    <span class="font-weight-bold" style="color: #2C3E6F; font-size: 1rem;">{{\App\CentralLogics\Helpers::format_currency(array_sum($earning))}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Body -->
                    <div class="card-body p-4" style="background: #fafbfc;">

                        <!-- Bar Chart -->
                        <div class="d-flex align-items-center">
                            <div class="chart--extension">
                              {{ \App\CentralLogics\Helpers::currency_symbol() }}({{translate('messages.currency')}})
                            </div>
                            <div class="chartjs-custom w-75 flex-grow-1">
                                <canvas id="updatingData" class="h-20rem" data-hs-chartjs-options='{
                                    "type": "bar",
                                    "data": {
                                        "labels": ["{{ translate('messages.Jan') }}","{{ translate('messages.Feb') }}","{{ translate('messages.Mar') }}","{{ translate('messages.April') }}","{{ translate('messages.May') }}","{{ translate('messages.Jun') }}","{{ translate('messages.Jul') }}","{{ translate('messages.Aug') }}","{{ translate('messages.Sep') }}","{{ translate('messages.Oct') }}","{{ translate('messages.Nov') }}","{{ translate('messages.Dec') }}"],
                                        "datasets": [{
                                        "data": [{{$earning[1]}},{{$earning[2]}},{{$earning[3]}},{{$earning[4]}},{{$earning[5]}},{{$earning[6]}},{{$earning[7]}},{{$earning[8]}},{{$earning[9]}},{{$earning[10]}},{{$earning[11]}},{{$earning[12]}}],
                                        "backgroundColor": "#2C3E6F",
                                        "hoverBackgroundColor": "#3d5278",
                                        "borderColor": "#2C3E6F"
                                    }]
                                    },
                                    "options": {
                                    "scales": {
                                        "yAxes": [{
                                        "gridLines": {
                                            "color": "#e7eaf3",
                                            "drawBorder": false,
                                            "zeroLineColor": "#e7eaf3"
                                        },
                                        "ticks": {
                                            "beginAtZero": true,
                                            "stepSize": {{ceil($amount/10000)*2000}},
                                            "fontSize": 12,
                                            "fontColor": "#97a4af",
                                            "fontFamily": "Open Sans, sans-serif",
                                            "padding": 10
                                        }
                                        }],
                                        "xAxes": [{
                                        "gridLines": {
                                            "display": false,
                                            "drawBorder": false
                                        },
                                        "ticks": {
                                            "fontSize": 12,
                                            "fontColor": "#97a4af",
                                            "fontFamily": "Open Sans, sans-serif",
                                            "padding": 5
                                        },
                                        "categoryPercentage": 0.3,
                                        "maxBarThickness": "10"
                                        }]
                                    },
                                    "cornerRadius": 5,
                                    "tooltips": {
                                        "prefix": " ",
                                        "hasIndicator": true,
                                        "mode": "index",
                                        "intersect": false
                                    },
                                    "hover": {
                                        "mode": "nearest",
                                        "intersect": true
                                    }
                                    }
                                }'></canvas>
                            </div>
                        </div>
                        <!-- End Bar Chart -->
                    </div>
                    <!-- End Body -->
                </div>
                <!-- End Card -->
            </div>

            <div class="col-lg-6">
                <!-- Ultra Modern Card -->
                <div class="card border-0 h-100" style="border-radius: 24px; overflow: hidden; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02); border: 1px solid rgba(44, 62, 111, 0.06);" id="top-selling-foods-view">
                    @include('vendor-views.partials._top-selling-foods',['top_sell'=>$data['top_sell']])
                </div>
                <!-- End Card -->
            </div>

            <div class="col-lg-6">
                <!-- Ultra Modern Card -->
                <div class="card border-0 h-100" style="border-radius: 24px; overflow: hidden; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02); border: 1px solid rgba(44, 62, 111, 0.06);" id="top-rated-foods-view">
                    @include('vendor-views.partials._most-rated-foods',['most_rated_foods'=>$data['most_rated_foods']])
                </div>
                <!-- End Card -->
            </div>


        </div>
        <!-- End Row -->
        @else
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">{{translate('messages.welcome')}}, {{auth('vendor_employee')->user()->f_name}}.</h1>
                    <p class="page-header-text">{{translate('messages.employee_welcome_message')}}</p>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        @endif
    </div>
@endsection

@push('script')
    <script src="{{dynamicAsset('public/assets/admin')}}/vendor/chart.js/dist/Chart.min.js"></script>
    <script src="{{dynamicAsset('public/assets/admin')}}/vendor/chart.js.extensions/chartjs-extensions.js"></script>
    <script
        src="{{dynamicAsset('public/assets/admin')}}/vendor/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js"></script>
@endpush


@push('script_2')
    <script>
        $('#free-trial-modal').modal('show');
        // INITIALIZATION OF CHARTJS
        // =======================================================
        Chart.plugins.unregister(ChartDataLabels);

        $('.js-chart').each(function () {
            $.HSCore.components.HSChartJS.init($(this));
        });

        let updatingChart = $.HSCore.components.HSChartJS.init($('#updatingData'));

        $('.order_stats_update').on('change',function (){
            let type = $(this).val();
            order_stats_update(type);
        })

        function order_stats_update(type) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('vendor.dashboard.order-stats')}}',
                data: {
                    statistics_type: type
                },
                beforeSend: function () {
                    $('#loading').show()
                },
                success: function (data) {
                    insert_param('statistics_type',type);
                    $('#order_stats').html(data.view)
                },
                complete: function () {
                    $('#loading').hide()
                }
            });
        }

        function insert_param(key, value) {
            key = encodeURIComponent(key);
            value = encodeURIComponent(value);
            // kvp looks like ['key1=value1', 'key2=value2', ...]
            let kvp = document.location.search.substr(1).split('&');
            let i = 0;

            for (; i < kvp.length; i++) {
                if (kvp[i].startsWith(key + '=')) {
                    let pair = kvp[i].split('=');
                    pair[1] = value;
                    kvp[i] = pair.join('=');
                    break;
                }
            }
            if (i >= kvp.length) {
                kvp[kvp.length] = [key, value].join('=');
            }
            // can return this or...
            let params = kvp.join('&');
            // change url page with new params
            window.history.pushState('page2', 'Title', '{{url()->current()}}?' + params);
        }

                $(document).on('click', '.add-to-session', function () {
                    var session_data = $(this).data("id");
                    $.ajax({
                        url: '{{ route('vendor.food.addToSession') }}',
                        method: 'POST',
                        data: {
                            value: session_data,
                            _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {

                            }
                        });
                });

                $(document).on('click', '#hide-warning', function () {
                $('.hide-warning').hide();
                });
                $(document).on('click', '#hide-warning-btn', function () {
                $('.hide-warning').hide();
                });


    </script>
@endpush
