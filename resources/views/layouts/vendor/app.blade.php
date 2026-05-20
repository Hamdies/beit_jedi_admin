<!DOCTYPE html>
<?php
    if (env('APP_MODE') == 'demo') {
        $site_direction = session()->get('site_direction_vendor');
    }else{
        $site_direction = session()->has('vendor_site_direction')?session()->get('vendor_site_direction'):'ltr';
    }
    $country=\App\Models\BusinessSetting::where('key','country')->first();
            $countryCode= strtolower($country?$country->value:'auto');
?>
<html dir="{{ $site_direction }}" lang="{{ str_replace('_', '-', app()->getLocale()) }}"  class="{{ $site_direction === 'rtl'?'active':'' }}"><head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Title -->
    <title>@yield('title')</title>
    <!-- Favicon -->
    @php($logo=\App\Models\BusinessSetting::where(['key'=>'icon'])->first()->value)
    <link rel="shortcut icon" href="">
    <link rel="icon" type="image/x-icon" href="{{dynamicStorage('storage/app/public/business/'.$logo??'')}}">
    <!-- Font -->
    <link href="{{dynamicAsset('public/assets/admin/css/fonts.css')}}" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif !important;
        }
        .swal2-popup {
            direction: rtl;
            text-align: center;
            border-radius: 24px !important;
            padding: 2rem 2.25rem !important;
            font-family: 'Cairo', -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif !important;
        }
        .swal2-title {
            font-size: 1.25rem !important;
            font-weight: 700 !important;
        }
        .swal2-actions .swal2-confirm,
        .swal2-actions .swal2-cancel {
            min-width: 120px;
            border-radius: 999px !important;
        }
        .swal2-actions .swal2-confirm {
            background-color: #f9735b !important;
            border-color: #f9735b !important;
        }
        .swal2-actions .swal2-cancel {
            background-color: #e5e7eb !important;
            color: #111827 !important;
        }

        /* ───── New-order modal ───── */
        .bj-new-order-modal { direction: rtl; }
        .bj-new-order-modal .modal-dialog { max-width: 460px; }
        .bj-new-order-modal .bj-no-card {
            border: 0;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(20, 20, 30, .35);
            background: #fffdfa;
            font-family: 'Cairo', sans-serif;
        }
        .bj-no-head {
            position: relative;
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 18px 20px 16px;
            background: linear-gradient(135deg, #171717 0%, #2a2520 100%);
            color: #fff;
        }
        .bj-no-pulse {
            flex: 0 0 48px;
            width: 48px; height: 48px;
            border-radius: 14px;
            background: rgba(201, 169, 107, .18);
            color: #f5d99e;
            display: grid; place-items: center;
            font-size: 22px;
            position: relative;
        }
        .bj-no-pulse::after {
            content: '';
            position: absolute; inset: -4px;
            border-radius: 18px;
            border: 2px solid rgba(201, 169, 107, .5);
            animation: bjNoPulse 1.6s ease-out infinite;
        }
        @keyframes bjNoPulse {
            0% { transform: scale(1); opacity: .8; }
            100% { transform: scale(1.35); opacity: 0; }
        }
        .bj-no-head-text { flex: 1; min-width: 0; }
        .bj-no-eyebrow {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .12em;
            color: #c9a96b;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .bj-no-title {
            font-size: 17px;
            font-weight: 800;
            line-height: 1.3;
            color: #fff;
        }
        .bj-no-close {
            position: absolute;
            top: 14px; inset-inline-start: 14px;
            background: rgba(255,255,255,.08);
            border: 0;
            color: #fff;
            width: 32px; height: 32px;
            border-radius: 50%;
            display: grid; place-items: center;
            cursor: pointer;
            transition: background .15s;
        }
        .bj-no-close:hover { background: rgba(255,255,255,.18); }

        .bj-no-body { padding: 18px 20px 8px; }
        .bj-no-meta {
            display: grid;
            gap: 10px;
            background: #f6f0e6;
            border-radius: 14px;
            padding: 14px 16px;
        }
        .bj-no-meta-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 10px;
            font-size: 14px;
        }
        .bj-no-meta-label {
            color: #6b6258;
            font-weight: 700;
        }
        .bj-no-meta-value {
            color: #171717;
            font-weight: 900;
            font-size: 16px;
        }
        .bj-no-meta-row--total {
            padding-top: 10px;
            border-top: 1px solid rgba(23,23,23,.1);
        }
        .bj-no-meta-row--total .bj-no-meta-value {
            font-size: 22px;
            color: #171717;
        }

        .bj-no-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            padding: 16px 20px 18px;
        }
        .bj-no-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 46px;
            padding: 0 16px;
            border-radius: 12px;
            font-family: 'Cairo', sans-serif;
            font-size: 14px;
            font-weight: 800;
            border: 0;
            cursor: pointer;
            transition: transform .1s, background .15s, box-shadow .15s;
        }
        .bj-no-btn:active { transform: translateY(1px); }
        .bj-no-btn--primary {
            background: #171717;
            color: #fff;
            box-shadow: 0 6px 18px rgba(23,23,23,.25);
        }
        .bj-no-btn--primary:hover { background: #000; color: #fff; }
        .bj-no-btn--ghost {
            background: #fff;
            color: #171717;
            border: 1.5px solid #171717;
        }
        .bj-no-btn--ghost:hover { background: #f6f0e6; }
        .bj-no-btn--text {
            grid-column: 1 / -1;
            background: transparent;
            color: #6b6258;
            min-height: 36px;
            font-weight: 700;
            font-size: 13px;
        }
        .bj-no-btn--text:hover { color: #171717; }
    </style>
    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{dynamicAsset('public/assets/admin/css/vendor.min.css')}}">
    <link rel="stylesheet" href="{{dynamicAsset('public/assets/admin/vendor/icon-set/style.css')}}">
    <!-- CSS Front Template -->
    <link rel="stylesheet" href="{{dynamicAsset('public/assets/admin/css/owl.min.css')}}">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin/css/theme.minc619.css?v=1.0') }}">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin/css/style.css') }}">
    <link  rel="stylesheet" href="{{dynamicAsset('/public/assets/admin/plugins/lightbox/css/lightbox.css')}}">
    <!-- Provider Panel Update CSS -->
    <link rel="stylesheet" href="{{dynamicAsset('public/assets/admin/css/vendor.css')}}">
    <link rel="stylesheet" href="{{dynamicAsset('public/assets/admin/intltelinput/css/intlTelInput.css')}}">

    @stack('css_or_js')

    <script src="{{dynamicAsset('public/assets/admin/vendor/hs-navbar-vertical-aside/hs-navbar-vertical-aside-mini-cache.js')}}"></script>
    <link rel="stylesheet" href="{{dynamicAsset('public/assets/admin/css/toastr.css')}}">
</head>

<body class="footer-offset bj-shell">

    @if (env('APP_MODE')=='demo')
    <div class="direction-toggle">
        <i class="tio-settings"></i>
        <span></span>
    </div>
    @endif

    <div id="pre--loader" class="pre--loader">
    </div>
{{--loader--}}
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div id="loading" class="initial-hidden">
                <div class="loading--1">
                    <img width="200" src="{{dynamicAsset('public/assets/admin/img/loader.gif')}}">
                </div>
            </div>
        </div>
    </div>
</div>
{{--loader--}}

<!-- Builder -->
@include('layouts.vendor.partials._front-settings')
<!-- End Builder -->

<div class="bj-app-shell">
<!-- Sidebar -->
@include('layouts.vendor.partials._sidebar')
<!-- End Sidebar -->

<main id="content" role="main" class="main pointer-event" style="min-width:0;background:#faf7f2;">
    <!-- Content -->
@yield('content')
<!-- End Content -->

    <!-- Footer -->
@include('layouts.vendor.partials._footer')
<!-- End Footer -->

    <div class="modal fade bj-new-order-modal" id="popup-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bj-no-card">
                <div class="modal-body p-0">
                    <div class="bj-no-head">
                        <div class="bj-no-pulse"><i class="tio-shopping-cart-outlined"></i></div>
                        <div class="bj-no-head-text">
                            <div class="bj-no-eyebrow">طلب جديد</div>
                            <div class="bj-no-title">لديك طلب جديد بانتظار التحضير</div>
                        </div>
                        <button type="button" class="bj-no-close" data-dismiss="modal" aria-label="إغلاق">
                            <i class="tio-clear"></i>
                        </button>
                    </div>

                    <div class="bj-no-body">
                        <div class="bj-no-meta">
                            <div class="bj-no-meta-row">
                                <span class="bj-no-meta-label">رقم الطلب</span>
                                <span class="bj-no-meta-value" id="bj-no-order-id">—</span>
                            </div>
                            <div class="bj-no-meta-row">
                                <span class="bj-no-meta-label">العميل</span>
                                <span class="bj-no-meta-value" id="bj-no-customer">—</span>
                            </div>
                            <div class="bj-no-meta-row bj-no-meta-row--total">
                                <span class="bj-no-meta-label">المجموع</span>
                                <span class="bj-no-meta-value" id="bj-no-amount">—</span>
                            </div>
                        </div>
                    </div>

                    <div class="bj-no-actions">
                        <button type="button" class="bj-no-btn bj-no-btn--primary" id="bj-no-print">
                            <i class="tio-print"></i> طباعة الآن
                        </button>
                        <button type="button" class="bj-no-btn bj-no-btn--ghost check-order">
                            <i class="tio-visible"></i> عرض الطلب
                        </button>
                        <button type="button" class="bj-no-btn bj-no-btn--text" data-dismiss="modal">
                            تجاهل
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="popup-modal-msg">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="text-center">
                                <h2 class="8a8a8a">
                                    <i class="tio-messages"></i> {{translate('messages.message_description')}}
                                </h2>
                                <hr>
                                <button class="btn btn-primary check-message">{{translate('messages.Ok, let me check')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>
</div>{{-- /.bj-app-shell --}}
    <div class="modal fade" id="toggle-modal">
        <div class="modal-dialog status-warning-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true" class="tio-clear"></span>
                    </button>
                </div>
                <div class="modal-body pb-5 pt-0">
                    <div class="max-349 mx-auto mb-20">
                        <div>
                            <div class="text-center">
                                <img id="toggle-image" alt="" class="mb-20">
                                <h5 class="modal-title" id="toggle-title"></h5>
                            </div>
                            <div class="text-center" id="toggle-message">
                            </div>
                        </div>
                        <div class="btn--container justify-content-center">
                            <button type="button" id="toggle-ok-button" class="btn btn--primary min-w-120 confirm-Toggle" data-dismiss="modal" >{{translate('Ok')}}</button>
                            <button id="reset_btn" type="reset" class="btn btn--cancel min-w-120" data-dismiss="modal">
                                {{translate("Cancel")}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="toggle-status-modal">
        <div class="modal-dialog status-warning-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true" class="tio-clear"></span>
                    </button>
                </div>
                <div class="modal-body pb-5 pt-0">
                    <div class="max-349 mx-auto mb-20">
                        <div>
                            <div class="text-center">
                                <img id="toggle-status-image" alt="" class="mb-20">
                                <h5 class="modal-title" id="toggle-status-title"></h5>
                            </div>
                            <div class="text-center" id="toggle-status-message">
                            </div>
                        </div>
                        <div class="btn--container justify-content-center">
                            <button type="button" id="toggle-status-ok-button" class="btn btn--primary min-w-120 confirm-Status-Toggle" data-dismiss="modal" >{{translate('Ok')}}</button>
                            <button id="reset_btn" type="reset" class="btn btn--cancel min-w-120" data-dismiss="modal">
                                {{translate("Cancel")}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="new-dynamic-submit-model">
        <div class="modal-dialog modal-dialog-centered status-warning-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true" class="tio-clear"></span>
                    </button>
                </div>
                <div class="modal-body pb-5 pt-0">
                    <div class="max-349 mx-auto mb-20">
                        <div>
                            <div class="text-center">
                                <img id="image-src" class="mb-20">
                                <h5 class="modal-title" id="toggle-title"></h5>
                            </div>
                            <div class="text-center" id="toggle-message">
                                <h3 id="modal-title"></h3>
                                <div id="modal-text"></div>
                            </div>

                            </div>
                            <div class="mb-4 d-none" id="note-data">
                                <textarea class="form-control" placeholder="{{ translate('your_note_here') }}" id="get-text-note" cols="5" ></textarea>
                            </div>
                        <div class="btn--container justify-content-center">
                            <div id="hide-buttons">
                                <button data-dismiss="modal" id="cancel_btn_text" class="btn btn-outline-secondary min-w-120" >{{translate("Not_Now")}}</button> &nbsp;
                                <button type="button" id="new-dynamic-ok-button" class="btn btn-outline-danger confirm-model min-w-120">{{translate('Yes')}}</button>
                            </div>

                            <button data-dismiss="modal"  type="button" id="new-dynamic-ok-button-show" class="btn btn--primary  d-none min-w-120">{{translate('Okay')}}</button>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="{{dynamicAsset('public/assets/admin/js/custom.js')}}"></script>
<script src="{{dynamicAsset('public/assets/admin/js/jquery.min.js')}}"></script>

    <script>
        "use strict";
    setTimeout(hide_loader, 1000);
        function hide_loader(){
        $('#pre--loader').removeClass("pre--loader");;
    }
</script>
<script src="{{dynamicAsset('public/assets/admin/js/firebase.min.js')}}"></script>

<!-- JS Implementing Plugins -->

@stack('script')
<script src="{{dynamicAsset('public/assets/admin/js/vendor.min.js')}}"></script>
<script src="{{dynamicAsset('public/assets/admin/js/theme.min.js')}}"></script>
<script src="{{dynamicAsset('public/assets/admin/js/sweet_alert.js')}}"></script>
<script src="{{dynamicAsset('public/assets/admin/js/toastr.js')}}"></script>
<script src="{{dynamicAsset('public/assets/admin/js/owl.min.js')}}"></script>
<script src="{{ dynamicAsset('/public/assets/admin/plugins/lightbox/js/lightbox.min.js')}}"></script>

<script src="{{dynamicAsset('public/assets/admin/intltelinput/js/intlTelInput.min.js')}}"></script>

{!! Toastr::message() !!}

@if ($errors->any())
    <script>
            "use strict";
        @foreach($errors->all() as $error)
        toastr.error('{{translate($error)}}', Error, {
            CloseButton: true,
            ProgressBar: true
        });
        @endforeach
    </script>
@endif

<script>
    "use strict";

    $('.blinkings').on('mouseover', ()=> $('.blinkings').removeClass('active'))
    $('.blinkings').addClass('open-shadow')
    setTimeout(() => {
        $('.blinkings').removeClass('active')
    }, 10000);
    setTimeout(() => {
        $('.blinkings').removeClass('open-shadow')
    }, 5000);

    $(function(){
        var owl = $('.single-item-slider');
        owl.owlCarousel({
            autoplay: false,
            items:1,
            onInitialized  : counter,
            onTranslated : counter,
            autoHeight: true,
            dots: true
        });

        function counter(event) {
            var element   = event.target;         // DOM element, in this example .owl-carousel
                var items     = event.item.count;     // Number of items
                var item      = event.item.index + 1;     // Position of the current item

            // it loop is true then reset counter from 1
            if(item > items) {
                item = item - items
            }
            $('.slide-counter').html(+item+"/"+items)
        }
    });
    $(document).on('ready', function(){
        $(".direction-toggle").on("click", function () {
            if($('html').hasClass('active')){
                $('html').removeClass('active')
                setDirection(1);
            }else {
                setDirection(0);
                $('html').addClass('active')
            }
        });
        if ($('html').attr('dir') == "rtl") {
            $(".direction-toggle").find('span').text('Toggle LTR')
        } else {
            $(".direction-toggle").find('span').text('Toggle RTL')
        }

        function setDirection(status) {
            if (status == 1) {
                $("html").attr('dir', 'ltr');
                $(".direction-toggle").find('span').text('Toggle RTL')
            } else {
                $("html").attr('dir', 'rtl');
                $(".direction-toggle").find('span').text('Toggle LTR')
            }
            $.get({
                    url: '{{ route('vendor.business-settings.site_direction_vendor') }}',
                    dataType: 'json',
                    data: {
                        status: status,
                    },
                    success: function() {
                        alert(ok);
                    },

                });
            }
        });

    $(document).on('ready', function () {
        if (window.localStorage.getItem('hs-builder-popover') === null) {
            $('#builderPopover').popover('show')
                .on('shown.bs.popover', function () {
                    $('.popover').last().addClass('popover-dark')
                });

            $(document).on('click', '#closeBuilderPopover', function () {
                window.localStorage.setItem('hs-builder-popover', true);
                $('#builderPopover').popover('dispose');
            });
        } else {
            $('#builderPopover').on('show.bs.popover', function () {
                return false
            });
        }

        // BUILDER TOGGLE INVOKER
        // =======================================================
        $('.js-navbar-vertical-aside-toggle-invoker').click(function () {
            $('.js-navbar-vertical-aside-toggle-invoker i').tooltip('hide');
        });


        // INITIALIZATION OF NAVBAR VERTICAL NAVIGATION
        // =======================================================
        var sidebar = $('.js-navbar-vertical-aside').hsSideNav();


        // INITIALIZATION OF TOOLTIP IN NAVBAR VERTICAL MENU
        // =======================================================
        $('.js-nav-tooltip-link').tooltip({boundary: 'window'})

        $(".js-nav-tooltip-link").on("show.bs.tooltip", function (e) {
            if (!$("body").hasClass("navbar-vertical-aside-mini-mode")) {
                return false;
            }
        });


        // INITIALIZATION OF UNFOLD
        // =======================================================
        $('.js-hs-unfold-invoker').each(function () {
            var unfold = new HSUnfold($(this)).init();
        });


        // INITIALIZATION OF FORM SEARCH
        // =======================================================
        $('.js-form-search').each(function () {
            new HSFormSearch($(this)).init()
        });


        // INITIALIZATION OF SELECT2
        // =======================================================
        $('.js-select2-custom').each(function () {
            var select2 = $.HSCore.components.HSSelect2.init($(this));
        });


        // INITIALIZATION OF DATERANGEPICKER
        // =======================================================
        $('.js-daterangepicker').daterangepicker();

        $('.js-daterangepicker-times').daterangepicker({
            timePicker: true,
            startDate: moment().startOf('hour'),
            endDate: moment().startOf('hour').add(32, 'hour'),
            locale: {
                format: 'M/DD hh:mm A'
            }
        });

        var start = moment();
        var end = moment();

        function cb(start, end) {
            $('#js-daterangepicker-predefined .js-daterangepicker-predefined-preview').html(start.format('MMM D') + ' - ' + end.format('MMM D, YYYY'));
        }

        $('#js-daterangepicker-predefined').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);

        cb(start, end);

        $('.js-clipboard').each(function () {
            var clipboard = $.HSCore.components.HSClipboard.init(this);
        });
    });
</script>

@stack('script_2')
    <script src="{{dynamicAsset('public/assets/admin/js/view-pages/common.js')}}"></script>
    <script src="{{dynamicAsset('public/assets/admin/js/keyword-highlighted.js')}}"></script>
<audio id="myAudio">
    <source src="{{dynamicAsset('public/assets/admin/sound/notification.mp3')}}" type="audio/mpeg">
</audio>

<script>
        "use strict";
    let audio = document.getElementById("myAudio");

    function playAudio() {
        audio.play();
    }

    function pauseAudio() {
        audio.pause();
    }


    function route_alert(route, message) {
        Swal.fire({
            title: '{{ translate('messages.Are you sure ?') }}',
            text: message,
            type: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#FC6A57',
            cancelButtonText: '{{ translate('messages.No') }}',
            confirmButtonText: '{{ translate('messages.Yes') }}',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                location.href = route;
            }
        })
    }

    $('.form-alert').on('click',function (){
        let id = $(this).data('id')
        let message = $(this).data('message')
        Swal.fire({
            title: '{{ translate('messages.Are you sure?') }}',
            text: message,
            type: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#FC6A57',
            cancelButtonText: '{{ translate('messages.no') }}',
            confirmButtonText: '{{ translate('messages.Yes') }}',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                $('#'+id).submit()
            }
        })
    })


    @php($fcm_credentials = \App\CentralLogics\Helpers::get_business_settings('fcm_credentials'))
    var firebaseConfig = {
        apiKey: "{{isset($fcm_credentials['apiKey']) ? $fcm_credentials['apiKey'] : ''}}",
        authDomain: "{{isset($fcm_credentials['authDomain']) ? $fcm_credentials['authDomain'] : ''}}",
        projectId: "{{isset($fcm_credentials['projectId']) ? $fcm_credentials['projectId'] : ''}}",
        storageBucket: "{{isset($fcm_credentials['storageBucket']) ? $fcm_credentials['storageBucket'] : ''}}",
        messagingSenderId: "{{isset($fcm_credentials['messagingSenderId']) ? $fcm_credentials['messagingSenderId'] : ''}}",
        appId: "{{isset($fcm_credentials['appId']) ? $fcm_credentials['appId'] : ''}}",
        measurementId: "{{isset($fcm_credentials['measurementId']) ? $fcm_credentials['measurementId'] : ''}}"
    };

    @if (isset($fcm_credentials['apiKey']) && is_string($fcm_credentials['apiKey']) && strlen($fcm_credentials['apiKey'])  > 3 )
        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();
    @endif

        function startFCM() {
            messaging
                .requestPermission()
                .then(function() {
                    return messaging.getToken();
                })
                .then(function(token) {
                    // console.log('FCM Token:', token);
                    @php($restaurant_id=\App\CentralLogics\Helpers::get_restaurant_id())
                    // Send the token to your backend to subscribe to topic
                    subscribeTokenToBackend(token, 'restaurant_panel_{{$restaurant_id}}_message');
                }).catch(function(error) {
                console.error('Error getting permission or token:', error);
            });
        }

        function subscribeTokenToBackend(token, topic) {
            fetch('{{url('/')}}/subscribeToTopic', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ token: token, topic: topic })
            }).then(response => {
                if (response.status < 200 || response.status >= 400) {
                    return response.text().then(text => {
                        throw new Error(`Error subscribing to topic: ${response.status} - ${text}`);
                    });
                }
                console.log(`Subscribed to "${topic}"`);
            }).catch(error => {
                console.error('Subscription error:', error);
            });
        }


    function getUrlParameter(sParam) {
        var sPageURL = window.location.search.substring(1);
        var sURLVariables = sPageURL.split('&');
        for (var i = 0; i < sURLVariables.length; i++) {
            var sParameterName = sURLVariables[i].split('=');
            if (sParameterName[0] === sParam) {
                return sParameterName[1];
            }
        }
    }

    function converationList() {
        var tab = getUrlParameter('tab');
        $.ajax({
            url: "{{ route('vendor.message.list') }}"+ '?tab=' + tab,
            success: function(data) {
                $('#conversation-list').empty();
                $('#admin-conversation-list').empty();
                $("#conversation-list").append(data.html);
                $("#admin-conversation-list").append(data.admin_html);
                var user_id = getUrlParameter('user');
                $('.customer-list').removeClass('conv-active');
                $('#customer-' + user_id).addClass('conv-active');
            }
        })
    }

    function conversationView() {
        var conversation_id = getUrlParameter('conversation');
        var user_id = getUrlParameter('user');
        var url= '{{url('/')}}/restaurant-panel/message/view/'+conversation_id+'/' + user_id;
        $.ajax({
            url: url,
            success: function(data) {
                $('#view-conversation').html(data.view);
            }
        })
    }
    @php($order_notification_type = \App\Models\BusinessSetting::where('key', 'order_notification_type')->first())
    @php($order_notification_type = $order_notification_type ? $order_notification_type->value : 'firebase')
    var order_type = 'all';


    // ── New-order modal state ──
    var bjLatestOrderId = null;
    var bjInvoiceTpl = '{{ url('/restaurant-panel/order/generate-invoice') }}/__X__';

    function bjFmtAmount(n){
        if (n == null || isNaN(parseFloat(n))) return '—';
        return 'ج.م ' + parseFloat(n).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
    function bjFillNewOrderModal(d){
        d = d || {};
        bjLatestOrderId = d.latest_order_id || d.order_id || null;
        $('#bj-no-order-id').text(bjLatestOrderId ? '#' + bjLatestOrderId : '—');
        $('#bj-no-customer').text(d.latest_customer || d.customer || 'عميل جديد');
        $('#bj-no-amount').text(bjFmtAmount(d.latest_amount || d.amount));
    }
    function bjShowNewOrderModal(d){
        bjFillNewOrderModal(d);
        $('#popup-modal').appendTo('body').modal('show');
    }

    $(document).on('click', '#bj-no-print', function(){
        if (!bjLatestOrderId) return;
        var w = window.open(bjInvoiceTpl.replace('__X__', bjLatestOrderId), '_blank');
        if (w) {
            w.addEventListener('load', function(){
                try { w.focus(); w.print(); } catch(e){}
            });
        }
    });

    @if (isset($fcm_credentials['apiKey']) && is_string($fcm_credentials['apiKey']) && strlen($fcm_credentials['apiKey'])  > 3 )

    messaging.onMessage(function (payload) {
        console.log(payload.data);

        if(payload.data.order_id && payload.data.type === 'new_order'){
            @if(\App\CentralLogics\Helpers::employee_module_permission_check('order') && $order_notification_type == 'firebase')
            order_type = payload.data.order_type
            playAudio();
            bjShowNewOrderModal({
                latest_order_id: payload.data.order_id,
                latest_customer: payload.data.customer_name,
                latest_amount: payload.data.order_amount
            });
            @endif
        }else if(payload.data.type === 'message'){
            var conversation_id = getUrlParameter('conversation');
            var user_id = getUrlParameter('user');
            var url= '{{url('/')}}/restaurant-panel/message/view/'+conversation_id+'/' + user_id;
            $.ajax({
                url: url,
                success: function(data) {
                    $('#view-conversation').html(data.view);
                }
            })
            toastr.success('{{ translate('messages.New message arrived') }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });

            if($('#conversation-list').scrollTop() == 0){
                converationList();
            }
        }
    });

    @endif

    @if(\App\CentralLogics\Helpers::employee_module_permission_check('order') && $order_notification_type == 'manual')
        var bjLastShownOrderId = null;
        setInterval(function () {
            $.get({
                url: '{{route('vendor.get-restaurant-data')}}',
                dataType: 'json',
                success: function (response) {
                    let data = response.data || {};
                    var hasNew = (data.new_pending_order > 0) || (data.new_confirmed_order > 0);
                    if (!hasNew) return;
                    // Don't re-spam the same order on every poll
                    if (data.latest_order_id && data.latest_order_id === bjLastShownOrderId) return;
                    order_type = data.new_pending_order > 0 ? 'pending' : 'confirmed';
                    bjLastShownOrderId = data.latest_order_id || null;
                    playAudio();
                    bjShowNewOrderModal(data);
                },
            });
        }, 10000);
        @endif

        $(document).on('click', '.check-order', function () {
            location.href = '{{url('/')}}/restaurant-panel/order/list/all';
        });
        $(document).on('click', '.check-message', function () {
            var tab = getUrlParameter('tab');
            location.href = '{{ route('vendor.message.list') }}'+ '?tab=' + tab;
        });


    @if (isset($fcm_credentials['apiKey']) && is_string($fcm_credentials['apiKey']) && strlen($fcm_credentials['apiKey'])  > 3 )
        startFCM();
        @endif
    converationList();

    if(getUrlParameter('conversation')){
        conversationView();
    }

    $(document).on('click', '.call-demo', function () {
            @if(env('APP_MODE') =='demo')
            toastr.info('{{ translate('Update option is disabled for demo!') }}', {
                CloseButton: true,
                ProgressBar: true
            });
            @endif
        });


    if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) document.write('<script src="{{dynamicAsset('public/assets/admin')}}/vendor/babel-polyfill/polyfill.min.js"><\/script>');

    $(window).on('load', ()=> $('.pre--loader').fadeOut(600))

    $('.log-out').on('click',function (){
        Swal.fire({
        title: '{{ translate('Do you want to logout?') }}',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonColor: '#FC6A57',
        cancelButtonColor: '#363636',
        confirmButtonText: `{{ translate('yes')}}`,
        cancelButtonText: `{{ translate('cancel')}}`,
        }).then((result) => {
        if (result.value) {
            location.href='{{route('logout')}}';
            } else{
            Swal.fire('{{ translate('messages.canceled') }}', '', 'info')
            }
        })
    });


    const inputs = document.querySelectorAll('input[type="tel"]');
            inputs.forEach(input => {
                window.intlTelInput(input, {
                    initialCountry: "{{$countryCode}}",
                    utilsScript: "{{ dynamicAsset('public/assets/admin/intltelinput/js/utils.js') }}",
                    autoInsertDialCode: true,
                    nationalMode: false,
                    formatOnDisplay: false,
                    strictMode: true,
                    // allowDropdown: false,
                    @if (\App\Models\BusinessSetting::where('key', 'country_picker_status')->first()?->value  != 1)
                    onlyCountries: ["{{$countryCode}}"],
                    @endif
                });
            });


            function keepNumbersAndPlus(inputString) {
                let regex = /[0-9+]/g;
                let filteredString = inputString.match(regex);
            return filteredString ? filteredString.join('') : '';
            }

            $(document).on('keyup', 'input[type="tel"]', function () {
                $(this).val(keepNumbersAndPlus($(this).val()));
                });



        //search option
        $(document).ready(function () {
            $('#searchForm input[name="search"]').keyup(function () {
                var searchKeyword = $(this).val().trim();

                if (searchKeyword.length >= 1) {
                    $.ajax({
                        type: 'POST',
                        url: $('#searchForm').attr('action'),
                        data: {search: searchKeyword, _token: $('input[name="_token"]').val()},
                        success: function (response) {
                            if (response.length === 0) {
                                $('#searchResults').html('<div class="fs-16 fw-500 mb-2">' + @json(translate('Search Result')) + '</div>' +
                                    '<div class="search-list h-300 d-flex flex-column gap-2 justify-content-center align-items-center fs-16">' +
                                    '<img width="30" src="' + @json(dynamicAsset('/public/assets/admin/img/modal/no-search-found.png')) + '" alt="">' + ' ' +
                                    @json(translate('No result found')) +
                                        '</div>');

                            } else {
                                var resultHtml = '';
                                response.forEach(function (route) {
                                    var fullRouteWithKeyword = route.fullRoute + '?keyword=' + encodeURIComponent(searchKeyword);
                                    resultHtml += '<a href="' + fullRouteWithKeyword + '" class="search-list-item d-flex flex-column" data-route-name="' + route.routeName + '" data-route-uri="' + route.URI + '" data-route-full-url="' + route.fullRoute + '" aria-current="true">';
                                    resultHtml += '<h5>' + route.routeName + '</h5>';
                                    resultHtml += '<p class="text-muted fs-12 mb-0">' + route.URI + '</p>';
                                    resultHtml += '</a>';
                                });
                                $('#searchResults').html('<div class="fs-16 fw-500 mb-2">' + @json(translate('Search Result')) + '</div>' + '<div class="search-list d-flex flex-column">' + resultHtml + '</div>');

                                $('.search-list-item').click(function () {
                                    var routeName = $(this).data('route-name');
                                    var routeUri = $(this).data('route-uri');
                                    var routeFullUrl = $(this).data('route-full-url');

                                    $.ajax({
                                        type: 'POST',
                                        url: '{{ route('vendor.store.clicked.route') }}',
                                        data: {
                                            routeName: routeName,
                                            routeUri: routeUri,
                                            routeFullUrl: routeFullUrl,
                                            searchKeyword: searchKeyword,
                                            _token: $('input[name="_token"]').val()
                                        },
                                        success: function (response) {
                                            console.log(response.message);
                                        },
                                        error: function (xhr, status, error) {
                                            console.error(xhr.responseText);
                                        }
                                    });
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                } else {
                    $('#searchResults').html('<div class="text-center text-muted py-5">{{translate('Write a minimum of one characters.')}}.</div>');
                }
            });
        });

        document.addEventListener('keydown', function(event) {
            if (event.ctrlKey && event.key === 'k') {
                event.preventDefault();
                document.getElementById('modalOpener').click();
            }
        });

        $(document).ready(function () {
            $("#staticBackdrop").on("shown.bs.modal", function () {
                $(this).find("#searchForm input[type=search]").val('');
                $('#searchResults').html('<div class="text-center text-muted py-5">{{translate('Loading recent searches')}}...</div>');
                $(this).find("#searchForm input[type=search]").focus();

                $.ajax({
                    type: 'GET',
                    url: '{{ route('vendor.recent.search') }}',
                    success: function (response) {
                        if (response.length === 0) {
                            $('#searchResults').html('<div class="text-center text-muted py-5">{{translate('It appears that you have not yet searched.')}}.</div>');
                        } else {
                            var resultHtml = '';
                            response.forEach(function (route) {
                                resultHtml += '<a href="' + route.route_full_url + '" class="search-list-item d-flex flex-column" data-route-name="' + route.route_name + '" data-route-uri="' + route.route_uri + '" data-route-full-url="' + route.route_full_url + '" aria-current="true">';
                                resultHtml += '<h5>' + route.route_name + '</h5>';
                                resultHtml += '<p class="text-muted fs-12  mb-0">' + route.route_uri + '</p>';
                                resultHtml += '</a>';
                            });
                            $('#searchResults').html('<div class="recent-search fs-16 fw-500 animate">' +
                                @json(translate('Recent Search')) + '<div class="search-list d-flex flex-column mt-2">' + resultHtml + '</div></div>');

                            $('.search-list-item').click(function () {
                                var routeName = $(this).data('route-name');
                                var routeUri = $(this).data('route-uri');
                                var routeFullUrl = $(this).data('route-full-url');
                                var searchKeyword = $('input[type=search]').val().trim();

                                $.ajax({
                                    type: 'POST',
                                    url: '{{ route('vendor.store.clicked.route') }}',
                                    data: {
                                        routeName: routeName,
                                        routeUri: routeUri,
                                        routeFullUrl: routeFullUrl,
                                        searchKeyword: searchKeyword,
                                        _token: $('input[name="_token"]').val()
                                    },
                                    success: function (response) {
                                        console.log(response.message);
                                    },
                                    error: function (xhr, status, error) {
                                        console.error(xhr.responseText);
                                    }
                                });
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                        $('#searchResults').html('<div class="text-center text-muted py-5">{{translate('Error loading recent searches')}}.</div>');
                    }
                });
            });
        });

        $("#staticBackdrop").on("hidden.bs.modal", function () {
            $('#searchResults').empty();
        });

        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('search', function() {
            if (!this.value.trim()) {
                $('#searchResults').html('<div class="text-center text-muted py-5"></div>');
            }
        });

        $('#searchForm').submit(function (event) {
            event.preventDefault();
        });


</script>


</body>
</html>
