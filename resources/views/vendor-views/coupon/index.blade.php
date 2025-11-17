@extends('layouts.vendor.app')

@section('title',translate('add_new_coupon'))

@push('css_or_js')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap');

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
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
    }
    .coupon-header-left {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .coupon-header-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background: linear-gradient(135deg, #2C3E6F 0%, #3d5278 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 26px;
        box-shadow: 0 6px 18px rgba(44,62,111,0.35);
    }
    .coupon-header-title {
        font-size: 1.6rem;
        font-weight: 800;
        letter-spacing: -0.4px;
        margin-bottom: 0.15rem;
    }
    .coupon-header-subtitle {
        font-size: 0.9rem;
        color: #9ca3af;
        margin: 0;
    }
    .coupon-header-badge {
        font-size: 0.85rem;
        padding: 0.55rem 1rem;
        border-radius: 999px;
        background: linear-gradient(135deg, rgba(244,208,63,0.12) 0%, rgba(44,62,111,0.06) 100%);
        color: #2C3E6F;
        border: 1px solid rgba(44,62,111,0.15);
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
    }
    .coupon-form-card,
    .coupon-list-card {
        border-radius: 24px;
        border: 1px solid rgba(44,62,111,0.06);
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .coupon-form-card .form-group label.input-label {
        font-weight: 600;
        font-size: 0.85rem;
        color: #4b5563;
    }
    .coupon-form-card .form-control {
        border-radius: 10px;
        border-color: #e5e7eb;
    }
    .coupon-form-card .form-control:focus {
        border-color: #2C3E6F;
        box-shadow: 0 0 0 3px rgba(44,62,111,0.08);
    }
    .coupon-generate-label {
        font-size: 0.78rem;
        color: #2563eb;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    .coupon-generate-label i {
        font-size: 0.95rem;
    }
    .coupon-section-title {
        font-size: 0.9rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #9ca3af;
        margin-bottom: 0.1rem;
    }
    .coupon-section-subtitle {
        font-size: 0.85rem;
        color: #6b7280;
        margin-bottom: 0.9rem;
    }
    .coupon-list-card .card-header {
        border-bottom: 1px solid rgba(0,0,0,0.03);
        padding: 1.2rem 1.5rem;
    }
    .coupon-search-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .coupon-search-wrapper h5.card-title {
        margin-bottom: 0;
        font-weight: 700;
        letter-spacing: -0.2px;
    }
    .coupon-search-wrapper .input--group .form-control {
        border-radius: 999px 0 0 999px;
    }
    .coupon-search-wrapper .btn.btn--secondary {
        border-radius: 0 999px 999px 0;
    }
    .coupon-search-input {
        direction: rtl;
        text-align: right;
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
        <!-- Page Header -->
        <div class="coupon-header-card">
            <div class="coupon-header-left">
                <div class="coupon-header-icon">
                    <i class="tio-add-circle-outlined"></i>
                </div>
                <div>
                    <h1 class="coupon-header-title">إضافة كوبون جديد</h1>
                    <p class="coupon-header-subtitle">أنشئ عروضاً خاصة وكوبونات مخصّصة لعملائك بسهولة</p>
                </div>
            </div>
            <div>
                <span class="coupon-header-badge">
                    <i class="tio-gift"></i>
                    إدارة الكوبونات
                </span>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="card mb-3 coupon-form-card">
            <div class="card-body">
                <form action="{{route('vendor.coupon.store')}}" method="post">
                    @csrf
                    @php($language=\App\Models\BusinessSetting::where('key','language')->first())
                    @php($language = $language->value ?? null)
                    @php($default_lang = str_replace('_', '-', app()->getLocale()))
                    <div class="row">
                        <div class="col-12 mb-2">
                            <h6 class="coupon-section-title">المعلومات الأساسية</h6>
                            <p class="coupon-section-subtitle">قم بتحديد اسم الكوبون واللغة التي سيظهر بها</p>
                        </div>
                        <div class="col-12">
                            @if ($language)
                            <ul class="nav nav-tabs mb-3 border-0">
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
                            <div class="lang_form" id="default-form">
                                <div class="form-group">
                                    <label class="input-label"
                                        for="default_title">عنوان الكوبون (افتراضي)
                                    </label>
                                    <input type="text" name="title[]" id="default_title"
                                        class="form-control remove-data" placeholder="{{ translate('messages.new_coupon') }}"

                                         >
                                </div>
                                <input type="hidden" name="lang[]" value="default">
                            </div>
                                @foreach (json_decode($language) as $lang)
                                    <div class="d-none lang_form"
                                        id="{{ $lang }}-form">
                                        <div class="form-group">
                                            <label class="input-label"
                                                for="{{ $lang }}_title">عنوان الكوبون ({{ strtoupper($lang) }})
                                            </label>
                                            <input type="text" name="title[]" id="{{ $lang }}_title"
                                                class="form-control remove-data" placeholder="{{ translate('messages.new_coupon') }}"
                                                 >
                                        </div>
                                        <input type="hidden" name="lang[]" value="{{ $lang }}">
                                    </div>
                                @endforeach
                            @else
                                <div id="default-form">
                                    <div class="form-group">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">عنوان الكوبون (افتراضي)</label>
                                        <input type="text" name="title[]" class="form-control remove-data"
                                            placeholder="{{ translate('messages.new_coupon') }}" >
                                    </div>
                                    <input type="hidden" name="lang[]" value="default">
                                </div>
                            @endif
                        </div>
                        <div class="col-12 mt-3 mb-2">
                            <h6 class="coupon-section-title">تفاصيل الكوبون</h6>
                            <p class="coupon-section-subtitle">اضبط نوع الكوبون، الكود، وحدود الاستخدام والتواريخ</p>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">نوع الكوبون</label>
                                <select id="coupon_type" name="coupon_type" class="form-control coupon_type_change">
                                    <option value="default">افتراضي</option>
                                    @if (($restaurant_data->restaurant_model == 'commission' && $restaurant_data->self_delivery_system == 1) ||($restaurant_data->restaurant_model == 'subscription' &&
                                        isset($restaurant_data->restaurant_sub) && $restaurant_data->restaurant_sub->self_delivery == 1))
                                    <option value="free_delivery">توصيل مجاني</option>
                                    @endif
                            </select>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6">
                            <div class="form-group">
                                <div class="d-flex justify-content-between">
                                    <label class="input-label" for="exampleFormControlInput1">رمز الكوبون</label>
                                    <label class="input-label generate-code coupon-generate-label" id="generate_code"><i class="tio-magic-wand"></i>توليد رمز عشوائي</label>
                                </div>
                                <input id="coupon_code" type="text" name="code" class="form-control"
                                       placeholder="{{\Illuminate\Support\Str::random(8)}}" required maxlength="100">
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">حد الاستخدام لنفس العميل</label>
                                <input type="number" name="limit" id="coupon_limit" class="form-control" placeholder="{{ translate('messages.Ex :') }} 10" max="100">
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">تاريخ البدء</label>
                                <input type="date" name="start_date" class="form-control" id="date_from" required>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">تاريخ الانتهاء</label>
                                <input type="date" name="expire_date" class="form-control" id="date_to" required>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">نوع الخصم</label>
                                <select name="discount_type" class="form-control" id="discount_type">
                                    <option value="amount">
                                            مبلغ ثابت ({{\App\CentralLogics\Helpers::currency_symbol()}})
                                    </option>
                                    <option value="percent"> نسبة مئوية (%)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">قيمة الخصم </label>
                                <input type="number" step="0.01" min="1" max="999999999999.99" name="discount" id="discount" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="form-group">
                                <label class="input-label" for="max_discount">الحد الأقصى للخصم</label>
                                <input type="number" step="0.01" min="0" value="0" max="999999999999.99" name="max_discount" id="max_discount" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">الحد الأدنى لقيمة الطلب</label>
                                <input id="min_purchase" type="number" step="0.01" name="min_purchase" value="0" min="0" max="999999999999.99" class="form-control"
                                    placeholder="100">
                            </div>
                        </div>
                    </div>
                    <div class="btn--container justify-content-end">
                        <button id="reset_btn" type="button" class="btn btn--reset">إعادة تعيين</button>
                        <button type="submit" class="btn btn--primary">حفظ الكوبون</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card coupon-list-card mt-3">
            <div class="card-header py-2">
                <div class="search--button-wrapper">
                    <h5 class="card-title">قائمة الكوبونات<span class="badge badge-soft-dark ml-2" id="itemCount">{{$coupons->total()}}</span></h5>
                    <form method="get">

                        <!-- Search -->
                        <div class="input--group input-group input-group-merge input-group-flush">
                            <input id="datatableSearch" type="search" name="search" class="form-control coupon-search-input" placeholder="مثال: بحث باسم الكوبون أو الكود" aria-label="بحث في الكوبونات">
                            <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                        </div>
                        <!-- End Search -->
                    </form>
                </div>
            </div>
            <!-- Table -->
            <div class="table-responsive datatable-custom" id="table-div">
                <table id="columnSearchDatatable"
                        class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                        data-hs-datatables-options='{
                        "order": [],
                        "orderCellsTop": true,

                        "entries": "#datatableEntries",
                        "isResponsive": false,
                        "isShowPaging": false,
                        "paging":false,
                        }'>
                    <thead class="thead-light">
                    <tr>
                        <th>م</th>
                        <th>عنوان الكوبون</th>
                        <th>الرمز</th>
                        <th>النوع</th>
                        <th>إجمالي مرات الاستخدام</th>
                        <th>الحد الأدنى للطلب</th>
                        <th>الحد الأقصى للخصم</th>
                        <th>
                            <div class="text-center">
                                قيمة الخصم
                            </div>
                        </th>
                        <th>نوع الخصم</th>
                        <th>تاريخ البدء</th>
                        <th>تاريخ الانتهاء</th>
                        <th>الحالة</th>
                        <th class="text-center">إجراء</th>
                    </tr>
                    </thead>

                    <tbody id="set-rows">
                    @foreach($coupons as $key=>$coupon)
                        <tr>
                            <td>{{$key+$coupons->firstItem()}}</td>
                            <td>
                            <span class="d-block font-size-sm text-body">
                                {{Str::limit($coupon['title'],15,'...')}}
                            </span>
                            </td>
                            <td>{{$coupon['code']}}</td>
                            <td>{{translate('messages.'.$coupon->coupon_type)}}</td>
                            <td>{{$coupon->total_uses}}</td>
                            <td>
                                <div class="text-right mw-87px">
                                    {{\App\CentralLogics\Helpers::format_currency($coupon['min_purchase'])}}
                                </div>
                            </td>
                            <td>
                                <div class="text-right mw-87px">
                                    {{\App\CentralLogics\Helpers::format_currency($coupon['max_discount'])}}
                                </div>
                            </td>
                            <td>
                                <div class="text-center">
                                    {{$coupon['discount']}}
                                </div>
                            </td>
                            @if ($coupon['discount_type'] == 'percent')
                            <td>{{ translate('messages.percent')}}</td>
                            @elseif ($coupon['discount_type'] == 'amount')
                            <td>{{ translate('messages.amount')}}</td>
                            @else
                            <td>{{$coupon['discount_type']}}</td>
                            @endif

                            <td>{{$coupon['start_date']}}</td>
                            <td>{{$coupon['expire_date']}}</td>
                            <td>
                                <label class="toggle-switch toggle-switch-sm" for="couponCheckbox{{$coupon->id}}">
                                    <input type="checkbox" data-url="{{route('vendor.coupon.status',[$coupon['id'],$coupon->status?0:1])}}" class="toggle-switch-input redirect-url" id="couponCheckbox{{$coupon->id}}" {{$coupon->status?'checked':''}}>
                                    <span class="toggle-switch-label">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a class="btn btn-sm btn--primary btn-outline-primary action-btn" href="{{route('vendor.coupon.update',[$coupon['id']])}}" title="{{translate('messages.edit_coupon')}}"><i class="tio-edit"></i>
                                    </a>
                                    <a class="btn btn-sm btn--danger btn-outline-danger action-btn form-alert" href="javascript:" data-id="coupon-{{$coupon['id']}}" data-message="{{ translate('Want_to_delete_this_coupon_?') }}" title="{{translate('messages.delete_coupon')}}"><i class="tio-delete-outlined"></i>
                                    </a>
                                    <form action="{{route('vendor.coupon.delete',[$coupon['id']])}}"
                                    method="post" id="coupon-{{$coupon['id']}}">
                                    @csrf @method('delete')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @if(count($coupons) === 0)
                <div class="empty--data py-5">
                            <img src="{{dynamicAsset('/public/assets/admin/img/empty.png')}}" alt="public">
                            <h5>
                        لا توجد بيانات حتى الآن
                            </h5>
                        </div>
                @endif
                <div class="page-area px-4 pb-3">
                    <div class="d-flex align-items-center justify-content-end">
                        <div>
                            {!! $coupons->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Table -->
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
        $('#discount_type').on('change', function() {
         if($('#discount_type').val() == 'amount')
            {
                $('#max_discount').attr("readonly","true");
                $('#max_discount').val(0);
            }
            else
            {
                $('#max_discount').removeAttr("readonly");
            }
        });

        $('#date_from').attr('min',(new Date()).toISOString().split('T')[0]);
        $('#date_to').attr('min',(new Date()).toISOString().split('T')[0]);

            // INITIALIZATION OF DATATABLES
            // =======================================================
            let datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'), {
                select: {
                    style: 'multi',
                    classMap: {
                        checkAll: '#datatableCheckAll',
                        counter: '#datatableCounter',
                        counterInfo: '#datatableCounterInfo'
                    }
                },
                language: {
                    zeroRecords: '<div class="text-center p-4">' +
                    '<img class="w-7rem mb-3" src="{{dynamicAsset('public/assets/admin/svg/illustrations/sorry.svg')}}" alt="Image Description">' +
                    '<p class="mb-0">{{ translate('No_data_to_show') }}</p>' +
                    '</div>'
                }
            });

            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                let select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });

        $(".coupon_type_change").on("change", function () {
            let coupon_type = $(this).val();
            if(coupon_type=='first_order')
            {
                $('#coupon_limit').val(1);
                $('#coupon_limit').attr("readonly","true");
                $('#customer_wise').hide();
            }
            else{
                $('#coupon_limit').val('');
                $('#coupon_limit').removeAttr("readonly");
                $('#customer_wise').show();
            }

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
        })

        $('#dataSearch').on('submit', function (e) {
            e.preventDefault();
            let formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('vendor.coupon.search')}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#table-div').html(data.view);
                    $('#itemCount').html(data.count);
                    $('.page-area').hide();
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });

        $('#reset_btn').click(function(){
            $('.remove-data').val('');
            $('#coupon_title').val('');
            $('#coupon_code').val(null);
            $('#coupon_limit').val(null);
            $('#date_from').val(null);
            $('#date_to').val(null);
            $('#discount_type').val('amount');
            $('#discount').val(null);
            $('#max_discount').val(0);
            $('#min_purchase').val(0);
            $('#select_customer').val(null).trigger('change');
        })

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
