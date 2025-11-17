
@extends('layouts.vendor.app')

@section('title', 'تقرير الطلبات')

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid" style="background:#f5f7fa; padding:1.75rem 0 1.5rem;">
        <!-- Page Header -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3" style="border-radius:24px; background:#ffffff; padding:1.5rem 1.75rem; box-shadow:0 1px 3px rgba(15,23,42,0.04); border:1px solid rgba(44,62,111,0.06);">
            <div class="d-flex align-items-center">
                <span class="page-header-icon mr-2">
                    <img src="{{dynamicAsset('/public/assets/admin/img/report/new/order_report.png')}}" class="w--22" alt="">
                </span>
                <div>
                    <h1 class="mb-1" style="font-size:1.3rem; font-weight:700; color:#1b3056;">تقرير الطلبات</h1>
                    <p class="mb-0" style="font-size:.9rem; color:#6b7280;">تابِع أداء الطلبات حسب الحالة والفترة الزمنية، وصدّر التقارير عند الحاجة.</p>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <div class="card mb-20" style="border-radius:24px; border:1px solid rgba(44,62,111,0.06); box-shadow:0 10px 25px rgba(15,23,42,0.03);">
            <div class="card-body">
                <h4 class="mb-3" style="font-weight:600; color:#1b3056;">بحث في بيانات الطلبات</h4>
                <form  method="get">

                    <div class="row g-3">
                        <div class="col-sm-6 col-md-3">
                            <select class="form-control set-filter" name="filter"
                                    data-url="{{ url()->full() }}" data-filter="filter">
                                <option value="all_time" {{ isset($filter) && $filter == 'all_time' ? 'selected' : '' }}>
                                    كل الفترات</option>
                                <option value="this_year" {{ isset($filter) && $filter == 'this_year' ? 'selected' : '' }}>
                                    هذا العام</option>
                                <option value="previous_year"
                                    {{ isset($filter) && $filter == 'previous_year' ? 'selected' : '' }}>
                                    العام السابق</option>
                                <option value="this_month"
                                    {{ isset($filter) && $filter == 'this_month' ? 'selected' : '' }}>
                                    هذا الشهر</option>
                                <option value="this_week" {{ isset($filter) && $filter == 'this_week' ? 'selected' : '' }}>
                                    هذا الأسبوع</option>
                                <option value="custom" {{ isset($filter) && $filter == 'custom' ? 'selected' : '' }}>
                                    مخصص</option>
                            </select>
                        </div>
                        @if (isset($filter) && $filter == 'custom')
                         <div class="col-sm-6 col-md-3">
                            <input type="date" name="from" id="from_date" class="form-control"
                                placeholder="تاريخ البداية"
                                value={{ isset($from) ? $from  : '' }} required>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <input type="date" name="to" id="to_date" class="form-control"
                                placeholder="تاريخ النهاية"
                                value={{ isset($to) ? $to  : '' }} required>
                        </div>
                        @endif
                        <div class="col-sm-6 col-md-3 ml-auto">
                            <button type="submit"
                                class="btn btn-primary btn-block">تطبيق الفلتر</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="mb-20">
            <div class="row g-2">
                <div class="col-sm-6 col-lg-4">
                    <a class="order--card h-100" href="#">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                <img src="{{dynamicAsset('/public/assets/admin/img/order-icons/schedule.png')}}" alt="dashboard" class="oder--card-icon">
                                <span>الطلبات المجدولة</span>
                            </h6>
                            <span class="card-title" style="--base-clr:#0661CB">
                                {{ $total_scheduled_count }}
                            </span>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <a class="order--card h-100" href="#">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                <img src="{{dynamicAsset('/public/assets/admin/img/order-icons/pending.png')}}" alt="dashboard" class="oder--card-icon">
                                <span>الطلبات المعلقة</span>
                            </h6>
                            <span class="card-title" style="--base-clr:#0661CB">
                                {{ $total_pending_count }}
                            </span>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <a class="order--card h-100" href="#">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                <img src="{{dynamicAsset('/public/assets/admin/img/order-icons/accepted.png')}}" alt="dashboard" class="oder--card-icon">
                                <span>طلبات تم قبولها</span>
                            </h6>
                            <span class="card-title" style="--base-clr:#0661CB">
                                {{ $total_accepted_count }}
                            </span>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <a class="order--card h-100" href="#">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                <img src="{{dynamicAsset('/public/assets/admin/img/order-icons/processing.png')}}" alt="dashboard" class="oder--card-icon">
                                <span>طلبات قيد التحضير</span>
                            </h6>
                            <span class="card-title" style="--base-clr:#00AA6D">
                                {{ $total_progress_count }}
                            </span>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <a class="order--card h-100" href="#">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                <img src="{{dynamicAsset('/public/assets/admin/img/order-icons/on-the-way.png')}}" alt="dashboard" class="oder--card-icon">
                                <span>الطلب في الطريق</span>
                            </h6>
                            <span class="card-title" style="--base-clr:#00AA6D">
                                {{ $total_on_the_way_count }}
                            </span>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <a class="order--card h-100" href="#">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                <img src="{{dynamicAsset('/public/assets/admin/img/order-icons/delivered.png')}}" alt="dashboard" class="oder--card-icon">
                                <span>طلبات مكتملة</span>
                            </h6>
                            <span class="card-title" style="--base-clr:#00AA6D">
                                {{$total_delivered_count}}
                            </span>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <a class="order--card h-100" href="#">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                <img src="{{dynamicAsset('/public/assets/admin/img/order-icons/canceled.png')}}" alt="dashboard" class="oder--card-icon">
                                <span>طلبات ملغاة</span>
                            </h6>
                            <span class="card-title" style="--base-clr:#FF7500">
                                {{ $total_canceled_count }}
                            </span>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <a class="order--card h-100" href="#">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                <img src="{{dynamicAsset('/public/assets/admin/img/order-icons/failed.png')}}" alt="dashboard" class="oder--card-icon">
                                <span>فشل في الدفع</span>
                            </h6>
                            <span class="card-title" style="--base-clr:#FF7500">
                                {{ $total_failed_count }}
                            </span>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <a class="order--card h-100" href="#">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                <img src="{{dynamicAsset('/public/assets/admin/img/order-icons/refunded.png')}}" alt="dashboard" class="oder--card-icon">
                                <span>طلبات مسترجعة</span>
                            </h6>
                            <span class="card-title" style="--base-clr:#FF7500">
                                {{ $total_refunded_count }}
                            </span>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- End Stats -->
        <!-- Card -->
        <div class="card mt-3" style="border-radius:24px; border:1px solid rgba(44,62,111,0.06); box-shadow:0 10px 25px rgba(15,23,42,0.03);">
            <!-- Header -->
            <div class="card-header border-0 py-2">
                <div class="search--button-wrapper">
                    <h3 class="card-title">
                        إجمالي الطلبات <span
                            class="badge badge-soft-secondary" id="countItems">{{ $orders->total() }}</span>
                    </h3>
                    <form  class="search-form">
                        <!-- Search -->
                        <div class="input--group input-group input-group-merge input-group-flush">
                            <input name="search" type="search" class="form-control"  value="{{ request()->search ?? null }}"  placeholder="ابحث برقم الطلب">
                            <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                        </div>
                        <!-- End Search -->
                    </form>
                    <!-- Static Export Button -->
                    <div class="hs-unfold ml-3">
                        <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle btn export-btn font--sm"
                            href="javascript:;"
                            data-hs-unfold-options="{
                                &quot;target&quot;: &quot;#usersExportDropdown&quot;,
                                &quot;type&quot;: &quot;css-animation&quot;
                            }"
                            data-hs-unfold-target="#usersExportDropdown" data-hs-unfold-invoker="">
                            <i class="tio-download-to mr-1"></i> تصدير
                        </a>

                        <div id="usersExportDropdown"
                            class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right hs-unfold-content-initialized hs-unfold-css-animation animated hs-unfold-reverse-y hs-unfold-hidden">

                            <span class="dropdown-header">خيارات التنزيل</span>
                            <a id="export-excel" class="dropdown-item"
                                href="{{ route('vendor.report.order-report-export', ['type' => 'excel', request()->getQueryString()]) }}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ dynamicAsset('public/assets/admin/svg/components/excel.svg') }}"
                                    alt="Image Description">
                                ملف Excel
                            </a>
                            <a id="export-csv" class="dropdown-item"
                                href="{{ route('vendor.report.order-report-export', ['type' => 'csv', request()->getQueryString()]) }}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ dynamicAsset('public/assets/admin/svg/components/placeholder-csv-format.svg') }}"
                                    alt="Image Description">
                                ملف CSV
                            </a>

                        </div>
                    </div>
                    <!-- Static Export Button -->
                </div>
            </div>
            <!-- End Header -->

            <!-- Body -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-borderless middle-align __txt-14px">
                        <thead class="thead-light white--space-false">
                            <tr>
                                <th class="border-top border-bottom word-nobreak">م</th>
                                <th class="border-top border-bottom word-nobreak">رقم الطلب</th>
                                <th class="border-top border-bottom word-nobreak">المطعم</th>
                                <th class="border-top border-bottom word-nobreak">اسم العميل</th>
                                <th class="border-top border-bottom word-nobreak">إجمالي قيمة العناصر</th>
                                <th class="border-top border-bottom word-nobreak">خصم العناصر</th>
                                <th class="border-top border-bottom word-nobreak">خصم الكوبون</th>
                                <th class="border-top border-bottom word-nobreak">خصم الإحالة</th>
                                <th class="border-top border-bottom word-nobreak">إجمالي الخصومات</th>
                                <th class="border-top border-bottom text-center">الضريبة</th>
                                <th class="border-top border-bottom word-nobreak">رسوم التوصيل</th>
                                <th class="border-top border-bottom text-center">{{ \App\CentralLogics\Helpers::get_business_data('additional_charge_name')??'رسوم إضافية' }}</th>
                                <th class="border-top border-bottom word-nobreak">رسوم التغليف الإضافية</th>
                                <th class="border-top border-bottom word-nobreak text-right">إجمالي الطلب</th>
                                <th class="border-top border-bottom word-nobreak">المبلغ المستلم بواسطة</th>
                                <th class="border-top border-bottom word-nobreak">طريقة الدفع</th>
                                <th class="border-top border-bottom word-nobreak">حالة الطلب</th>
                                <th class="border-top border-bottom text-center">إجراءات
                                </th>
                            </tr>
                        </thead>
                        <tbody id="set-rows">
                            @foreach ($orders as $key => $order)
                                <tr class="status-{{ $order['order_status'] }} class-all">
                                    <td class="">
                                        {{ $key + $orders->firstItem() }}
                                    </td>
                                    <td class="table-column-pl-0">
                                            <a
                                            href="{{ route('vendor.order.details', ['id' => $order['id']]) }}">#{{ $order['id'] }}</a>
                                    </td>
                                    <td  class="text-capitalize">
                                        @if($order->restaurant)
                                            {{Str::limit($order->restaurant->name,25,'...')}}
                                        @else
                                            <label class="badge badge-danger">{{ translate('messages.invalid') }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($order->customer)
                                            <a class="text-body text-capitalize"
                                                href="#">
                                                <strong>{{ $order->customer['f_name'] . ' ' . $order->customer['l_name'] }}</strong>
                                            </a>
                                        @elseif($order->is_guest)
                                             <?php
                                        $customer_details = json_decode($order['delivery_address'],true);
                                    ?>
                                            <strong>{{$customer_details['contact_person_name']}}</strong>
                                            <div>{{$customer_details['contact_person_number']}}</div>
                                        @else
                                            <label class="badge badge-danger">{{ translate('messages.invalid_customer_data') }}</label>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-right mw--85px">
                                            <div>
                                                {{ \App\CentralLogics\Helpers::number_format_short($order['order_amount']- $order['additional_charge'] - $order['dm_tips']-$order['total_tax_amount'] - $order['extra_packaging_amount']  -$order['delivery_charge']+$order['coupon_discount_amount'] + $order['restaurant_discount_amount'] + $order['ref_bonus_amount']) }}
                                            </div>
                                            @if ($order->payment_status == 'paid')
                                                <strong class="text-success">
                                                    {{ translate('messages.paid') }}
                                                </strong>
                                            @else
                                                <strong class="text-danger">
                                                    {{ translate('messages.unpaid') }}
                                                </strong>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center mw--85px">
                                        {{ \App\CentralLogics\Helpers::number_format_short($order->details()->sum(DB::raw('discount_on_food * quantity'))) }}
                                    </td>
                                    <td class="text-center mw--85px">
                                        {{ \App\CentralLogics\Helpers::number_format_short($order['coupon_discount_amount']) }}
                                    </td>
                                    <td class="text-center mw--85px">
                                        {{ \App\CentralLogics\Helpers::number_format_short($order['ref_bonus_amount']) }}
                                    </td>
                                    <td class="text-center mw--85px">
                                        {{ \App\CentralLogics\Helpers::number_format_short($order['coupon_discount_amount'] + $order['restaurant_discount_amount'] + $order['ref_bonus_amount']) }}
                                    </td>
                                    <td class="text-center mw--85px white-space-nowrap">
                                        {{ \App\CentralLogics\Helpers::number_format_short($order['total_tax_amount']) }}
                                    </td>
                                    <td class="text-center mw--85px">
                                        {{ \App\CentralLogics\Helpers::number_format_short($order['delivery_charge']) }}
                                    </td>
                                    <td class="text-center mw--85px">
                                        {{ \App\CentralLogics\Helpers::number_format_short($order['additional_charge']) }}
                                    </td>
                                    <td class="text-center mw--85px">
                                        {{ \App\CentralLogics\Helpers::number_format_short($order['extra_packaging_amount']) }}
                                    </td>
                                    <td>
                                        <div class="text-right mw--85px">
                                            <div>
                                                {{ \App\CentralLogics\Helpers::number_format_short($order['order_amount']) }}
                                            </div>
                                            @if ($order->payment_status == 'paid')
                                                <strong class="text-success">
                                                    {{ translate('messages.paid') }}
                                                </strong>
                                            @else
                                                <strong class="text-danger">
                                                    {{ translate('messages.unpaid') }}
                                                </strong>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center mw--85px text-capitalize">
                                        {{isset($order->transaction) ? translate(str_replace('_', ' ', $order->transaction->received_by))  : translate('messages.not_received_yet')}}
                                    </td>
                                    <td class="text-center mw--85px text-capitalize">
                                            {{ translate(str_replace('_', ' ', $order['payment_method'])) }}
                                    </td>
                                    <td class="text-center mw--85px text-capitalize">
                                        @if($order['order_status']=='pending')
                                                <span class="badge badge-soft-info">
                                                  {{translate('messages.pending')}}
                                                </span>
                                            @elseif($order['order_status']=='confirmed')
                                                <span class="badge badge-soft-info">
                                                  {{translate('messages.confirmed')}}
                                                </span>
                                            @elseif($order['order_status']=='processing')
                                                <span class="badge badge-soft-warning">
                                                  {{translate('messages.processing')}}
                                                </span>
                                            @elseif($order['order_status']=='picked_up')
                                                <span class="badge badge-soft-warning">
                                                  {{translate('messages.out_for_delivery')}}
                                                </span>
                                            @elseif($order['order_status']=='delivered')
                                                <span class="badge badge-soft-success">
                                                  {{$order?->order_type == 'dine_in' ? translate('messages.Completed') : translate('messages.delivered')}}
                                                </span>
                                            @elseif($order['order_status']=='failed')
                                                <span class="badge badge-soft-danger">
                                                  {{translate('messages.payment_failed')}}
                                                </span>
                                            @elseif($order['order_status']=='handover')
                                                <span class="badge badge-soft-danger">
                                                  {{translate('messages.handover')}}
                                                </span>
                                            @elseif($order['order_status']=='canceled')
                                                <span class="badge badge-soft-danger">
                                                  {{translate('messages.canceled')}}
                                                </span>
                                            @elseif($order['order_status']=='accepted')
                                                <span class="badge badge-soft-danger">
                                                  {{translate('messages.accepted')}}
                                                </span>
                                            @else
                                                <span class="badge badge-soft-danger">
                                                  {{translate(str_replace('_',' ',$order['order_status']))}}
                                                </span>
                                            @endif

                                    </td>


                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <a class="ml-2 btn btn-sm btn--warning btn-outline-warning action-btn"
                                                href="{{ route('vendor.order.details', ['id' => $order['id']]) }}">
                                                <i class="tio-invisible"></i>
                                            </a>
                                            <a class="ml-2 btn btn-sm btn--primary btn-outline-primary action-btn"
                                                href="{{ route('vendor.order.generate-invoice', ['id' => $order['id']]) }}">
                                                <i class="tio-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- End Table -->


            </div>
            <!-- End Body -->
            @if (count($orders) !== 0)
                <hr>
            @endif
            <div class="page-area px-4 pb-3">
                {!! $orders->links() !!}
            </div>
            @if (count($orders) === 0)
                <div class="empty--data text-center">
                    <img src="{{ dynamicAsset('/public/assets/admin/svg/illustrations/sorry.svg') }}" alt="public">
                    <h5>
                        لا توجد طلبات في هذه الفترة
                    </h5>
                </div>
            @endif
        </div>
        <!-- End Card -->
    </div>
@endsection

@push('script')
@endpush

@push('script_2')
    <script src="{{ dynamicAsset('public/assets/admin') }}/vendor/chart.js/dist/Chart.min.js"></script>
    <script src="{{ dynamicAsset('public/assets/admin') }}/vendor/chartjs-chart-matrix/dist/chartjs-chart-matrix.min.js">
    </script>
    <script src="{{ dynamicAsset('public/assets/admin') }}/js/hs.chartjs-matrix.js"></script>
    <script src="{{dynamicAsset('public/assets/admin')}}/js/view-pages/vendor/report.js"></script>

    <script>
        $(document).on('ready', function() {



            $('.js-data-example-ajax-2').select2({
                ajax: {
                    url: '{{ url('/') }}/admin/customer/select-list',
                    data: function(params) {
                        return {
                            q: params.term, // search term
                            all:true,
                            @if (isset($zone))
                                zone_ids: [{{ $zone->id }}],
                            @endif
                            @if (request('restaurant_id'))
                                restaurant_id: {{ request('restaurant_id') }},
                            @endif
                            page: params.page
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    __port: function(params, success, failure) {
                        let $request = $.ajax(params);

                        $request.then(success);
                        $request.fail(failure);

                        return $request;
                    }
                }
            });
        });
    </script>
@endpush

