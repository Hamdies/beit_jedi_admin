@extends('layouts.vendor.app')

@section('title','تعديل الكوبون')

@push('css_or_js')
<style>
    .coupon-page-modern {
        background: #f5f7fa;
        padding: 1.75rem 0 1.5rem;
    }
    .coupon-header-card {
        border-radius: 24px;
        background: #ffffff;
        padding: 1.75rem 2rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        border: 1px solid rgba(44,62,111,0.06);
        margin-bottom: 1.25rem;
    }
    .coupon-header-title {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: .25rem;
        color: #1b3056;
    }
    .coupon-header-subtitle {
        font-size: .9rem;
        color: #6b7280;
        margin-bottom: 0;
    }
    .coupon-section-title {
        font-size: 1rem;
        font-weight: 600;
        color: #1b3056;
        margin-bottom: .35rem;
    }
    .coupon-section-subtitle {
        font-size: .82rem;
        color: #9ca3af;
        margin-bottom: 1rem;
    }
    .coupon-edit-card {
        border-radius: 24px;
        border: 1px solid rgba(44,62,111,0.06);
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.04);
    }
    .coupon-edit-card .form-control,
    .coupon-edit-card .custom-select,
    .coupon-edit-card select.form-control {
        border-radius: 999px;
    }
    .coupon-edit-card .form-group label {
        font-size: .85rem;
        color: #374151;
        margin-bottom: .35rem;
    }
    .coupon-edit-card .btn.btn--primary {
        border-radius: 999px;
        padding-inline: 1.75rem;
    }
    .coupon-edit-card .btn.btn--reset {
        border-radius: 999px;
    }
    @media (max-width: 768px) {
        .coupon-header-card {
            padding: 1.25rem 1.5rem;
        }
    }
</style>
@endpush

@section('content')
@php($restaurant_data = \App\CentralLogics\Helpers::get_restaurant_data())

    <div class="content container-fluid coupon-page-modern">
        <div class="coupon-header-card d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div class="mb-2 mb-md-0">
                <h1 class="coupon-header-title d-flex align-items-center gap-2">
                    <i class="tio-edit ml-1"></i>
                    تعديل الكوبون
                </h1>
                <p class="coupon-header-subtitle">قم بتحديث بيانات الكوبون الحالي بسهولة واضبط تفاصيله كما يناسب عروضك.</p>
            </div>
        </div>
        <div class="card coupon-edit-card">
            <div class="card-body">
                <form action="{{route('vendor.coupon.update',[$coupon['id']])}}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            @php($language=\App\Models\BusinessSetting::where('key','language')->first())
                            @php($language = $language->value ?? null)
                            @php($default_lang = str_replace('_', '-', app()->getLocale()))
                            @if($language)
                                <div class="js-nav-scroller hs-nav-scroller-horizontal">
                                    <ul class="nav nav-tabs mb-3">
                                        <li class="nav-item">
                                            <a class="nav-link lang_link active"
                                            href="#"
                                            id="default-link">الافتراضي</a>
                                        </li>
                                        @foreach (json_decode($language) as $lang)
                                            <li class="nav-item">
                                                <a class="nav-link lang_link"
                                                    href="#"
                                                    id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="lang_form" id="default-form">
                                    <div class="form-group">
                                        <label class="input-label" for="default_title">عنوان الكوبون (افتراضي)</label>
                                        <input type="text"  name="title[]" id="default_title" class="form-control" placeholder="اكتب عنوان الكوبون" value="{{$coupon->getRawOriginal('title')}}"  >
                                    </div>
                                    <input type="hidden" name="lang[]" value="default">
                                </div>
                                @foreach(json_decode($language) as $lang)
                                    <?php
                                        if(count($coupon['translations'])){
                                            $translate = [];
                                            foreach($coupon['translations'] as $t)
                                            {
                                                if($t->locale == $lang && $t->key=="title"){
                                                    $translate[$lang]['title'] = $t->value;
                                                }
                                            }
                                        }
                                    ?>
                                    <div class="d-none lang_form" id="{{$lang}}-form">
                                        <div class="form-group">
                                            <label class="input-label" for="{{$lang}}_title">عنوان الكوبون ({{strtoupper($lang)}})</label>
                                            <input type="text" name="title[]" id="{{$lang}}_title" class="form-control" placeholder="اكتب عنوان الكوبون" value="{{$translate[$lang]['title']??''}}"  >
                                        </div>
                                        <input type="hidden" name="lang[]" value="{{$lang}}">
                                    </div>
                                @endforeach
                            @else
                            <div id="default-form">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">عنوان الكوبون (افتراضي)</label>
                                    <input type="text" name="title[]" class="form-control" placeholder="اكتب عنوان الكوبون" value="{{$coupon['title']}}" maxlength="100" >
                                </div>
                                <input type="hidden" name="lang[]" value="default">
                            </div>
                            @endif
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">نوع الكوبون</label>
                                <select id="coupon_type" name="coupon_type" class="form-control coupon_type_change">
                                    @if (($restaurant_data->restaurant_model == 'commission' && $restaurant_data->self_delivery_system == 1) ||($restaurant_data->restaurant_model == 'subscription' &&
                                        isset($restaurant_data->restaurant_sub) && $restaurant_data->restaurant_sub->self_delivery == 1))
                                    <option value="free_delivery" {{$coupon['coupon_type']=='free_delivery'?'selected':''}}>توصيل مجاني</option>
                                    @endif
                                    <option value="default" {{$coupon['coupon_type']=='default'?'selected':''}}>افتراضي</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="form-group">
                                <div class="d-flex justify-content-between">
                                    <label class="input-label" for="exampleFormControlInput1">رمز الكوبون</label>
                                    <label class="input-label generate-code" id="generate_code"><i class="tio-hand-draw"></i>توليد رمز عشوائي</label>
                                </div>
                                <input id="coupon_code" type="text" name="code" class="form-control" value="{{$coupon['code']}}"
                                       placeholder="{{\Illuminate\Support\Str::random(8)}}" required maxlength="100">
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="form-group">
                                <label class="input-label" for="limit">حد الاستخدام لنفس العميل</label>
                                <input type="number" name="limit" id="coupon_limit" value="{{$coupon['limit']}}" class="form-control" max="100"
                                        placeholder="مثال: 10">
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="form-group">
                                <label class="input-label" for="">تاريخ البدء</label>
                                <input type="date" name="start_date" class="form-control" id="date_from" placeholder="اختر تاريخ البدء" value="{{date('Y-m-d',strtotime($coupon['start_date']))}}">
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="form-group">
                                <label class="input-label" for="date_to">تاريخ الانتهاء</label>
                                <input type="date" name="expire_date" class="form-control" placeholder="اختر تاريخ الانتهاء" id="date_to" value="{{date('Y-m-d',strtotime($coupon['expire_date']))}}"
                                        data-hs-flatpickr-options='{
                                        "dateFormat": "Y-m-d"
                                    }'>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="form-group">
                                <label class="input-label" for="discount_type">نوع الخصم</label>
                                <select name="discount_type" id="discount_type" class="form-control" {{$coupon['coupon_type']=='free_delivery'?'disabled':''}}>
                                    <option value="amount" {{$coupon['discount_type']=='amount'?'selected':''}}>
                                        مبلغ ثابت ({{ \App\CentralLogics\Helpers::currency_symbol() }} أو العملة)
                                    </option>
                                    <option value="percent" {{$coupon['discount_type']=='percent'?'selected':''}}>
                                        نسبة مئوية (%)
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="form-group">
                                <label class="input-label" for="discount">قيمة الخصم</label>
                                <input type="number" id="discount" min="1" max="999999999999.99" step="0.01" value="{{$coupon['discount']}}"
                                        name="discount" class="form-control" required {{$coupon['coupon_type']=='free_delivery'?'readonly':''}}>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">الحد الأقصى للخصم</label>
                                <input type="number" min="0" max="999999999999.99" step="0.01"
                                        value="{{$coupon['max_discount']}}" name="max_discount" id="max_discount" class="form-control" {{$coupon['coupon_type']=='free_delivery' || $coupon['discount_type']=='amount' ?'readonly':''}}>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">الحد الأدنى لقيمة الطلب</label>
                                <input id="min_purchase" type="number" name="min_purchase" step="0.01" value="{{$coupon['min_purchase']}}"
                                        min="0" max="999999999999.99" class="form-control"
                                        placeholder="100">
                            </div>
                        </div>
                    </div>
                    <div class="btn--container justify-content-end mt-3">
                        <button id="reset_btn" type="button" class="btn btn--reset location-reload">إعادة تعيين</button>
                        <button type="submit" class="btn btn--primary">تحديث الكوبون</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        "use strict";
        $("#date_from").on("change", function () {
            $('#date_to').attr('min',$(this).val());
        });

        $("#date_to").on("change", function () {
            $('#date_from').attr('max',$(this).val());
        });
        $(document).on('ready', function () {
            $('#date_from').attr('max','{{date("Y-m-d",strtotime($coupon["expire_date"]))}}');
            $('#date_to').attr('min','{{date("Y-m-d",strtotime($coupon["start_date"]))}}');

            // INITIALIZATION OF FLATPICKR
            // =======================================================
            $('.js-flatpickr').each(function () {
                $.HSCore.components.HSFlatpickr.init($(this));
            });
        });


        $(document).on('change', '.coupon_type_change', function () {
            let coupon_type = $(this).val();
            if(coupon_type=='free_delivery')
            {
                $('#discount_type').attr("disabled","true");
                $('#discount_type').val("").trigger( "change" );
                $('#max_discount').val(0);
                $('#max_discount').attr("readonly","true");
                $('#discount').val(0);
                $('#discount').attr("readonly","true");
            }
            else{
                $('#max_discount').removeAttr("readonly");
                $('#discount_type').removeAttr("disabled");
                $('#discount').removeAttr("readonly");
                $('#discount_type').attr("required","true");
            }
        });

        $(document).on('change', '#discount_type', function () {
            let discount_type = $(this).val();
            if(discount_type == 'amount')
            {
                $('#max_discount').attr("readonly","true").val(0);
            }
            else{
                $('#max_discount').removeAttr("readonly").attr("required","true");
            }
        });

        $(document).ready(function() {
            $('#generate_code').click(function() {
                generateUniqueCode();
            });

            function generateUniqueCode() {
                let code = generateRandomCode();
                checkCodeExists(code, function(exists) {
                    if (exists) {
                        generateUniqueCode();
                    } else {
                        $('#coupon_code').val(code);
                    }
                });
            }

            function generateRandomCode() {
                let length = 8;
                let result = '';
                let characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                let charactersLength = characters.length;
                for (let i = 0; i < length; i++) {
                    result += characters.charAt(Math.floor(Math.random() * charactersLength));
                }
                return result;
            }

            function checkCodeExists(code, callback) {
                $.ajax({
                    url: '{{ route('vendor.coupon.check.code') }}',
                    method: 'get',
                    data: { code: code },
                    success: function(response) {
                        callback(response.exists);
                    },
                    error: function() {
                        callback(false);
                    }
                });
            }
        });

    </script>
@endpush
