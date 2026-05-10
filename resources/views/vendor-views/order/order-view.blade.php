@php
$max_processing_time = explode('-', $order['restaurant']['delivery_time'])[0];
$c_address = null ;
if($order->delivery_address){
    $c_address = json_decode($order->delivery_address, true);
}
@endphp

@if($order->scheduled == 1)
    @section('scheduled')
@elseif( in_array($order->order_status, ['confirmed','accepted']) )
    @section('confirmed')
@else
    @section($order->order_status)
@endif
active
@endsection

@extends('layouts.vendor.app')

@section('title', 'تفاصيل الطلب #' . $order['id'])

@push('css_or_js')
<link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin/css/order-view.css') }}">
@endpush

@section('content')
<?php
$campaign_order = isset($order->details[0]->campaign) ? true : false;
$reasons = \App\Models\OrderCancelReason::where('status', 1)->where('user_type','restaurant')->get();
$subscription = isset($order->subscription_id) ? true : false;
$tax_included = 0;
$restaurant = \App\CentralLogics\Helpers::get_restaurant_data();

$total_addon_price = 0;
$restaurant_discount_amount = 0;
$product_price = 0;

foreach ($order->details as $detail) {
    if (isset($detail->food_id)) {
        $d = json_decode($detail->food_details, true);
        $amt = $detail['price'] * $detail['quantity'];
        $product_price += $amt;
        $restaurant_discount_amount += $detail['discount_on_food'] * $detail['quantity'];
    } elseif (isset($detail->item_campaign_id)) {
        $amt = $detail['price'] * $detail['quantity'];
        $product_price += $amt;
        $restaurant_discount_amount += $detail['discount_on_food'] * $detail['quantity'];
    }
    foreach (json_decode($detail['add_ons'], true) as $addon) {
        $total_addon_price += $addon['price'] * $addon['quantity'];
    }
}

$coupon_discount_amount = $order['coupon_discount_amount'];
$total_tax_amount = $order['total_tax_amount'];
$restaurant_discount_amount = $order['restaurant_discount_amount'];
$tax_included = \App\Models\BusinessSetting::where(['key'=>'tax_included'])->first()
    ? \App\Models\BusinessSetting::where(['key'=>'tax_included'])->first()->value : 0;
$additional_charge_status = \App\CentralLogics\Helpers::get_business_data('additional_charge_status') == 1 || $order['additional_charge'] > 0 ? 1 : 0;
$order_delivery_verification = (bool) \App\Models\BusinessSetting::where(['key' => 'order_delivery_verification'])->first()->value;

/* Status helpers */
$status = $order['order_status'];
if (isset($order->subscription) && $order->subscription->status != 'canceled') {
    $status = $order->subscription_log ? $order->subscription_log->order_status : $status;
}
$statusLabels = [
    'pending'    => 'قيد الانتظار',
    'confirmed'  => 'تم التأكيد',
    'accepted'   => 'تم القبول',
    'processing' => 'قيد الطهي',
    'handover'   => 'جاهز للتسليم',
    'picked_up'  => 'في الطريق',
    'delivered'  => $order->order_type == 'dine_in' ? 'مكتمل' : 'تم التوصيل',
    'canceled'   => 'ملغي',
    'failed'     => 'فشل',
];
$statusLabel = $statusLabels[$status] ?? translate(str_replace('_',' ',$status));

/* Visual stepper mapping (data model unchanged).
   Always 3 phases — matches what the restaurant can actually trigger.
   The rider's app updates `picked_up` independently (customer sees it via their own app);
   restaurant view doesn't expose that as a separate clickable step.
*/
$isTerminal = in_array($status, ['canceled','failed','refunded','refund_requested','refund_request_canceled']);
$isDelivery = !in_array($order['order_type'], ['dine_in','take_away']);

$phaseMap = [
    'pending'    => 1,
    'confirmed'  => 2,
    'accepted'   => 2,
    'processing' => 2,
    'handover'   => 3,
    'picked_up'  => 3,
    'delivered'  => 3,
];
if ($isDelivery) {
    $finalLabel = 'التوصيل';
} elseif ($order->order_type == 'dine_in') {
    $finalLabel = 'الاكتمال';
} else {
    $finalLabel = 'الاستلام';
}
$steps = [
    ['n' => 1, 'label' => 'القبول'],
    ['n' => 2, 'label' => 'التجهيز'],
    ['n' => 3, 'label' => $finalLabel],
];
$currentPhase = $phaseMap[$status] ?? 0;
$isFinalDone = $status === 'delivered';
$totalSteps = count($steps);

/* Hero status semantic class */
$statusClass = [
    'pending'    => 'is-pending',
    'confirmed'  => 'is-active',
    'accepted'   => 'is-active',
    'processing' => 'is-cooking',
    'handover'   => 'is-ready',
    'picked_up'  => 'is-en-route',
    'delivered'  => 'is-done',
    'canceled'   => 'is-canceled',
    'failed'     => 'is-canceled',
][$status] ?? '';
?>

<div class="content container-fluid ov-page" id="printableArea">

    {{-- ── Top bar ─────────────────────────────────────────── --}}
    <div class="ov-topbar">
        <div class="ov-topbar-right">
            <span class="ov-order-id">طلب #{{ $order['id'] }}</span>
            <span class="ov-order-date">{{ date('d M Y · ' . config('timeformat'), strtotime($order['created_at'])) }}</span>
            @if ($order->edited)
                <span class="ov-tag warn">معدّل</span>
            @endif
            @if ($subscription)
                <span class="ov-sub-badge"><i class="tio-refresh"></i> اشتراك</span>
            @endif
            @if ($campaign_order)
                <span class="ov-sub-badge gold"><i class="tio-star"></i> عرض</span>
            @endif
        </div>
        <div class="ov-topbar-nav">
            <a class="ov-nav-btn" href="{{ route('vendor.order.details', [$order['id'] - 1]) }}" title="الطلب السابق"><i class="tio-chevron-right"></i></a>
            <a class="ov-nav-btn" href="{{ route('vendor.order.details', [$order['id'] + 1]) }}" title="الطلب التالي"><i class="tio-chevron-left"></i></a>
            <button type="button" class="ov-kbd-btn d-none d-sm-inline-flex"
                    id="ov-kbd-toggle"
                    aria-expanded="false"
                    aria-controls="ov-kbd-panel"
                    title="اختصارات لوحة المفاتيح (?)">
                <span class="ov-kbd-glyph" aria-hidden="true">⌨︎</span>
                <span>اختصارات</span>
            </button>
            <a class="ov-print-btn d-none d-sm-inline-flex" href="{{ route('vendor.order.generate-invoice', [$order['id']]) }}">
                <i class="tio-print"></i> طباعة
            </a>
        </div>
    </div>

    {{-- ── Keyboard shortcuts panel ────────────────────────── --}}
    <div class="ov-kbd-panel" id="ov-kbd-panel" hidden>
        <div class="ov-card">
            <div class="ov-card-body">
                <div class="ov-kbd-row"><span class="ov-kbd-label">تنفيذ الإجراء التالي</span><span class="ov-kbd">Enter</span><span class="ov-kbd">Space</span></div>
                <div class="ov-kbd-row"><span class="ov-kbd-label">إلغاء الطلب</span><span class="ov-kbd">Esc</span></div>
                <div class="ov-kbd-row"><span class="ov-kbd-label">تعيين مندوب</span><span class="ov-kbd">D</span></div>
                <div class="ov-kbd-row"><span class="ov-kbd-label">طباعة الفاتورة</span><span class="ov-kbd">P</span></div>
                <div class="ov-kbd-row"><span class="ov-kbd-label">الطلب السابق / التالي</span><span class="ov-kbd">←</span><span class="ov-kbd">→</span></div>
                <div class="ov-kbd-row"><span class="ov-kbd-label">إظهار / إخفاء</span><span class="ov-kbd">?</span></div>
            </div>
        </div>
    </div>

    {{-- ── Hero card: status + summary + primary action ────── --}}
    <section class="ov-hero">
        <div class="ov-hero-top">
            <div class="ov-hero-left">
                <div class="ov-hero-status {{ $statusClass }}">
                    <span class="ov-dot"></span>
                    {{ $statusLabel }}
                </div>
                <h1 class="ov-hero-title">{{ \App\CentralLogics\Helpers::format_currency($order['order_amount']) }}</h1>
                <div class="ov-hero-meta">
                    <span class="ov-hero-meta-item">
                        <span class="k">الدفع</span>
                        @if ($order['payment_status'] == 'paid')
                            <span class="v paid">مدفوع · {{ translate(str_replace('_',' ',$order['payment_method'])) }}</span>
                        @elseif ($order['payment_status'] == 'partially_paid')
                            @if ($order->payments()->where('payment_status','unpaid')->exists())
                                <span class="v partial">جزئي · {{ translate(str_replace('_',' ',$order['payment_method'])) }}</span>
                            @else
                                <span class="v paid">مدفوع · {{ translate(str_replace('_',' ',$order['payment_method'])) }}</span>
                            @endif
                        @else
                            <span class="v unpaid">غير مدفوع · {{ translate(str_replace('_',' ',$order['payment_method'])) }}</span>
                        @endif
                    </span>
                    <span class="ov-hero-meta-item">
                        <span class="k">النوع</span>
                        <span class="v">{{ translate(str_replace('_',' ',$order['order_type'])) }}</span>
                    </span>
                    @if ($order->schedule_at && ($order->scheduled || $subscription))
                    <span class="ov-hero-meta-item">
                        <span class="k">مجدول</span>
                        <span class="v">{{ date('d M · ' . config('timeformat'), strtotime($order['schedule_at'])) }}</span>
                    </span>
                    @endif
                    @if ($c_address)
                    <button class="ov-map-btn" data-toggle="modal" data-target="#locationModal">
                        <i class="tio-poi-outlined"></i> عرض الموقع
                    </button>
                    @endif
                </div>
            </div>

            @if ($order['order_status'] != 'delivered' && $order['order_status'] != 'canceled' && $order['order_status'] != 'failed')
            <div class="ov-hero-actions">
                @include('vendor-views.order.partials._status-actions')
            </div>
            @endif
        </div>

        @if ($isTerminal)
            <div class="ov-banner {{ $status }}">
                <i class="tio-{{ $status === 'canceled' ? 'clear-circle' : 'warning-outlined' }}"></i>
                <span>{{ $statusLabel }}</span>
            </div>
        @else
            <div class="ov-progress" role="group" aria-label="مراحل الطلب">
                <div class="ov-progress-bar" aria-hidden="true">
                    @foreach ($steps as $s)
                        @php
                            $segCls = '';
                            if ($isFinalDone || $currentPhase > $s['n']) { $segCls = 'done'; }
                            elseif ($currentPhase === $s['n']) { $segCls = 'active'; }
                        @endphp
                        <div class="ov-progress-seg {{ $segCls }}"></div>
                    @endforeach
                </div>
                <div class="ov-progress-labels">
                    @foreach ($steps as $s)
                        @php
                            $labCls = '';
                            if ($isFinalDone || $currentPhase > $s['n']) { $labCls = 'done'; }
                            elseif ($currentPhase === $s['n']) { $labCls = 'active'; }
                        @endphp
                        <span class="ov-progress-label {{ $labCls }}" aria-current="{{ $labCls === 'active' ? 'step' : 'false' }}">{{ $s['label'] }}</span>
                    @endforeach
                </div>
            </div>
        @endif
    </section>

    {{-- ── Notes / instructions ────────────────────────────── --}}
    @if($order['order_note'])
    <div class="ov-note"><i class="tio-comment-outlined"></i><strong>ملاحظة الطلب:</strong>&nbsp;{{ $order['order_note'] }}</div>
    @endif
    @if ($order['delivery_instruction'])
    <div class="ov-note info"><i class="tio-info-outlined"></i><strong>تعليمات التوصيل:</strong>&nbsp;{{ translate($order->delivery_instruction) }}</div>
    @endif
    @if ($order['unavailable_item_note'])
    <div class="ov-note warn"><i class="tio-warning-outlined"></i><strong>إذا لم يتوفر:</strong>&nbsp;{{ translate($order->unavailable_item_note) }}</div>
    @endif

    {{-- ── Cancellation block (full-width when relevant) ───── --}}
    @if ($order->order_status == 'canceled')
    <section class="ov-card">
        <div class="ov-card-head">
            <h2 class="ov-card-title danger">تفاصيل الإلغاء</h2>
        </div>
        <div class="ov-card-body">
            <div class="ov-cancel">
                @if($order->cancellation_reason)
                <div class="row"><span class="k">السبب</span><span class="v">{{ $order->cancellation_reason }}</span></div>
                @endif
                @if($order->cancellation_note)
                <div class="row"><span class="k">ملاحظة</span><span class="v">{{ $order->cancellation_note }}</span></div>
                @endif
                <div class="row"><span class="k">بواسطة</span><span class="v">{{ translate($order->canceled_by) }}</span></div>
            </div>
            @if ($order->payment_status == 'paid' || $order->payment_status == 'partially_paid')
                @if ($order?->payments)
                    @foreach ($order->payments()->where('payment_status','paid')->get() as $pay_info)
                    <div class="ov-detail">
                        <span class="k">المبلغ المدفوع ({{ translate($pay_info->payment_method) }})</span>
                        <span class="v">{{ \App\CentralLogics\Helpers::format_currency($pay_info->amount) }}</span>
                    </div>
                    @endforeach
                @endif
                <div class="ov-detail">
                    <span class="k">المبلغ المُعاد للمحفظة</span>
                    @if ($order?->payments)
                        @php($amount = $order->payments()->where('payment_status','paid')->sum('amount'))
                        <span class="v paid">{{ \App\CentralLogics\Helpers::format_currency($amount) }}</span>
                    @else
                        <span class="v paid">{{ \App\CentralLogics\Helpers::format_currency($order->order_amount) }}</span>
                    @endif
                </div>
            @endif
        </div>
    </section>
    @endif

    <div class="ov-grid">

        {{-- ── COL 1: ITEMS + TOTALS ─────────────────────────── --}}
        <div class="ov-col-items">
            <section class="ov-card">
                <div class="ov-card-head">
                    <h2 class="ov-card-title">الأصناف</h2>
                    <a class="ov-card-action d-sm-none" href="{{ route('vendor.order.generate-invoice', [$order['id']]) }}">طباعة</a>
                </div>
                <ul class="ov-items">
                @foreach ($order->details as $key => $detail)
                    @if (isset($detail->food_id))
                        @php($detail->food = json_decode($detail->food_details, true))
                        @php($food = \App\Models\Food::where(['id' => $detail->food['id']])->first())
                        <li class="ov-item">
                            <a href="{{ route('vendor.food.view', $detail->food['id']) }}">
                                <img class="ov-item-img onerror-image"
                                     src="{{ $food['image_full_url'] ?? dynamicAsset('public/assets/admin/img/100x100/food-default-image.png') }}"
                                     data-onerror-image="{{ dynamicAsset('public/assets/admin/img/160x160/img2.jpg') }}"
                                     alt="">
                            </a>
                            <div class="ov-item-body">
                                <div class="ov-item-name">{{ Str::limit($detail->food['name'], 36, '…') }}</div>
                                <div class="ov-item-meta">
                                    <span class="qty">×{{ $detail['quantity'] }}</span>
                                    <span>{{ \App\CentralLogics\Helpers::format_currency($detail['price']) }} للوحدة</span>
                                    @if (count(json_decode($detail['variation'], true)) > 0)
                                        @foreach(json_decode($detail['variation'],true) as $variation)
                                            @if (isset($variation['name']) && isset($variation['values']))
                                                <span>{{ $variation['name'] }}:
                                                    @foreach ($variation['values'] as $value){{ $value['label'] }}@if (!$loop->last), @endif @endforeach
                                                </span>
                                            @break
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                                @php($addons = json_decode($detail['add_ons'], true))
                                @if (count($addons))
                                <div class="ov-item-addons">
                                    @foreach ($addons as $addon)
                                        <span class="ov-addon">{{ Str::limit($addon['name'],20,'…') }} ×{{ $addon['quantity'] }}</span>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            <div class="ov-item-price">{{ \App\CentralLogics\Helpers::format_currency($detail['price'] * $detail['quantity']) }}</div>
                        </li>
                    @elseif(isset($detail->item_campaign_id))
                        @php($detail->campaign = json_decode($detail->food_details, true))
                        <li class="ov-item">
                            <img class="ov-item-img onerror-image"
                                 src="{{ $detail->campaign['image_full_url'] ?? dynamicAsset('public/assets/admin/img/100x100/food-default-image.png') }}"
                                 data-onerror-image="{{ dynamicAsset('public/assets/admin/img/160x160/img2.jpg') }}"
                                 alt="">
                            <div class="ov-item-body">
                                <div class="ov-item-name">{{ Str::limit($detail->campaign['name'], 36, '…') }}</div>
                                <div class="ov-item-meta">
                                    <span class="qty">×{{ $detail['quantity'] }}</span>
                                    <span>{{ \App\CentralLogics\Helpers::format_currency($detail['price']) }} للوحدة</span>
                                </div>
                                @php($addons = json_decode($detail['add_ons'], true))
                                @if (count($addons))
                                <div class="ov-item-addons">
                                    @foreach ($addons as $addon)
                                        <span class="ov-addon">{{ Str::limit($addon['name'],20,'…') }} ×{{ $addon['quantity'] }}</span>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            <div class="ov-item-price">{{ \App\CentralLogics\Helpers::format_currency($detail['price'] * $detail['quantity']) }}</div>
                        </li>
                    @endif
                @endforeach
                </ul>

                {{-- Totals breakdown --}}
                <div class="ov-totals">
                    <div class="ov-total-row"><span>سعر الأصناف</span><span class="v">{{ \App\CentralLogics\Helpers::format_currency($product_price) }}</span></div>
                    @if ($total_addon_price > 0)
                    <div class="ov-total-row"><span>الإضافات</span><span class="v">{{ \App\CentralLogics\Helpers::format_currency($total_addon_price) }}</span></div>
                    @endif
                    @if ($restaurant_discount_amount > 0)
                    <div class="ov-total-row discount"><span>خصم المطعم</span><span class="v">− {{ \App\CentralLogics\Helpers::format_currency($restaurant_discount_amount) }}</span></div>
                    @endif
                    @if ($coupon_discount_amount > 0)
                    <div class="ov-total-row discount"><span>خصم القسيمة</span><span class="v">− {{ \App\CentralLogics\Helpers::format_currency($coupon_discount_amount) }}</span></div>
                    @endif
                    @if ($order['ref_bonus_amount'] > 0)
                    <div class="ov-total-row discount"><span>خصم الإحالة</span><span class="v">− {{ \App\CentralLogics\Helpers::format_currency($order['ref_bonus_amount']) }}</span></div>
                    @endif
                    @if ($order->tax_status == 'excluded' || $order->tax_status == null)
                    <div class="ov-total-row"><span>ضريبة القيمة المضافة</span><span class="v">{{ \App\CentralLogics\Helpers::format_currency($total_tax_amount) }}</span></div>
                    @endif
                    @if ($order['dm_tips'] > 0)
                    <div class="ov-total-row"><span>إكرامية المندوب</span><span class="v">{{ \App\CentralLogics\Helpers::format_currency($order['dm_tips']) }}</span></div>
                    @endif
                    <div class="ov-total-row"><span>رسوم التوصيل</span><span class="v">{{ \App\CentralLogics\Helpers::format_currency($order['delivery_charge']) }}</span></div>
                    @if ($additional_charge_status)
                    <div class="ov-total-row"><span>{{ \App\CentralLogics\Helpers::get_business_data('additional_charge_name') ?? translate('messages.additional_charge') }}</span><span class="v">{{ \App\CentralLogics\Helpers::format_currency($order['additional_charge']) }}</span></div>
                    @endif
                    @if ($order['extra_packaging_amount'] > 0)
                    <div class="ov-total-row"><span>تغليف إضافي</span><span class="v">{{ \App\CentralLogics\Helpers::format_currency($order['extra_packaging_amount']) }}</span></div>
                    @endif

                    @if ($order?->payments && count($order->payments))
                    <div class="ov-totals-divider"></div>
                    @foreach ($order->payments as $payment)
                    <div class="ov-total-row">
                        <span>
                            @if ($payment->payment_status == 'paid')
                                {{ $payment->payment_method == 'cash_on_delivery' ? 'دفع نقدي عند التوصيل' : 'دفع عبر ' . translate($payment->payment_method) }}
                            @else
                                مبلغ مستحق ({{ $payment->payment_method == 'cash_on_delivery' ? 'COD' : translate($payment->payment_method) }})
                            @endif
                        </span>
                        <span class="v">{{ \App\CentralLogics\Helpers::format_currency($payment->amount) }}</span>
                    </div>
                    @endforeach
                    @endif

                    <div class="ov-grand">
                        <span class="k">الإجمالي</span>
                        <span class="v">{{ \App\CentralLogics\Helpers::format_currency($order['order_amount']) }}</span>
                    </div>
                </div>

                @if ($order->bring_change_amount > 0)
                <div class="ov-change-alert">
                    <i class="tio-money"></i>
                    {{ translate('Please instruct the delivery man to collect ' . \App\CentralLogics\Helpers::format_currency($order->bring_change_amount) . ' in change upon delivery') }}
                </div>
                @endif
            </section>
        </div>

        {{-- ── COL 2: SIDEBAR (customer, address, delivery man, extras) ── --}}
        <div class="ov-col-details">

            {{-- Dine-in table data --}}
            @if ($order->order_type == 'dine_in')
            <section class="ov-card">
                <div class="ov-card-head"><h2 class="ov-card-title">بيانات الطاولة</h2></div>
                <div class="ov-card-body">
                    <form action="{{ route('vendor.order.add_dine_in_table_number', [$order['id']]) }}" method="post">
                        @method('PUT') @csrf
                        <div class="ov-field">
                            <label class="ov-field-label">{{ translate('Table_Number') }}</label>
                            <input type="text" class="ov-input" @readonly(in_array($order['order_status'],['failed','delivered','refund_requested','canceled','refunded','refund_request_canceled'])) maxlength="20" value="{{ $order?->OrderReference?->table_number }}" name="table_number" placeholder="مثال: 10">
                        </div>
                        <div class="ov-field">
                            <label class="ov-field-label">{{ translate('Token_Number') }}</label>
                            <input type="text" class="ov-input" @readonly(in_array($order['order_status'],['failed','delivered','refund_requested','canceled','refunded','refund_request_canceled'])) maxlength="20" value="{{ $order?->OrderReference?->token_number }}" name="token_number" placeholder="مثال: 32">
                        </div>
                        @if (!in_array($order['order_status'],['failed','delivered','refund_requested','canceled','refunded','refund_request_canceled']))
                        <button type="submit" class="ov-btn-primary ov-btn-primary--block">{{ translate('messages.Save') }}</button>
                        @endif
                    </form>
                </div>
            </section>
            @endif

            {{-- Cutlery indicator --}}
            <div class="ov-note {{ $order->cutlery ? 'success' : '' }}">
                <i class="tio-cutlery"></i>
                <strong>أدوات المائدة</strong>
                <span class="v">{{ $order->cutlery ? 'مطلوبة' : 'غير مطلوبة' }}</span>
            </div>

            {{-- Customer --}}
            <section class="ov-card">
                <div class="ov-card-head"><h2 class="ov-card-title">العميل</h2></div>
                <div class="ov-card-body">
                    @if ($order->customer && $order->is_guest == 0)
                    <div class="ov-person">
                        <img class="ov-person-img onerror-image"
                             src="{{ $order->customer?->image_full_url ?? dynamicAsset('public/assets/admin/img/160x160/img1.jpg') }}"
                             data-onerror-image="{{ dynamicAsset('public/assets/admin/img/160x160/img1.jpg') }}" alt="">
                        <div class="ov-person-text">
                            <div class="ov-person-name">{{ $order->customer['f_name'] . ' ' . $order->customer['l_name'] }}</div>
                            <div class="ov-person-sub">{{ $order->customer->orders_count }} طلب</div>
                        </div>
                    </div>
                    <div class="ov-detail">
                        <span class="k">الهاتف</span>
                        <span class="v"><a href="tel:{{ $order->customer['phone'] }}">{{ $order->customer['phone'] }}</a></span>
                    </div>
                    @if ($order->customer['email'])
                    <div class="ov-detail">
                        <span class="k">البريد</span>
                        <span class="v">{{ $order->customer['email'] }}</span>
                    </div>
                    @endif
                    @elseif($order->is_guest)
                    <span class="ov-tag success ov-tag--block">زائر</span>
                    @else
                    <p class="ov-empty">لم يُعثر على العميل</p>
                    @endif
                </div>
            </section>

            {{-- Delivery address --}}
            @if ($order->delivery_address)
            @php($address = json_decode($order->delivery_address, true))
            <section class="ov-card">
                <div class="ov-card-head"><h2 class="ov-card-title">{{ $order->order_type == 'dine_in' ? 'بيانات الطلب' : 'عنوان التوصيل' }}</h2></div>
                <div class="ov-card-body">
                    @if (isset($address))
                    <div class="ov-detail">
                        <span class="k">المستلم</span>
                        <span class="v">{{ $address['contact_person_name'] ?? '' }}</span>
                    </div>
                    <div class="ov-detail">
                        <span class="k">الاتصال</span>
                        <span class="v"><a href="tel:{{ $address['contact_person_number'] ?? '' }}">{{ $address['contact_person_number'] ?? '' }}</a></span>
                    </div>
                    @if ($order->order_type != 'dine_in')
                        @if (isset($address['road']) && $address['road'])
                        <div class="ov-detail"><span class="k">الشارع</span><span class="v">{{ $address['road'] }}</span></div>
                        @endif
                        @if (isset($address['house']) && $address['house'])
                        <div class="ov-detail"><span class="k">المبنى / الشقة</span><span class="v">{{ $address['house'] }}</span></div>
                        @endif
                        @if (isset($address['floor']) && $address['floor'])
                        <div class="ov-detail"><span class="k">الطابق</span><span class="v">{{ $address['floor'] }}</span></div>
                        @endif
                    @endif
                    @endif
                </div>
            </section>
            @endif

            {{-- Delivery man --}}
            @if (!in_array($order['order_type'],['dine_in','take_away']))
            <section class="ov-card">
                <div class="ov-card-head">
                    <h2 class="ov-card-title">المندوب</h2>
                    @if ($order->delivery_man && !in_array($order['order_status'], ['handover','delivered','refund_requested','canceled','refunded','refund_request_canceled']) && (isset($order->restaurant) && (($order->restaurant->restaurant_model == 'commission' && $order->restaurant->self_delivery_system) || ($order->restaurant->restaurant_model == 'subscription' && isset($order->restaurant->restaurant_sub) && $order->restaurant->restaurant_sub->self_delivery == 1))))
                    <button class="ov-card-action" data-toggle="modal" data-target="#myModal">تغيير</button>
                    @endif
                </div>
                <div class="ov-card-body">
                    @if ($order->delivery_man)
                    <div class="ov-person">
                        <img class="ov-person-img onerror-image"
                             src="{{ $order->delivery_man?->image_full_url ?? dynamicAsset('public/assets/admin/img/160x160/img3.jpg') }}"
                             data-onerror-image="{{ dynamicAsset('public/assets/admin/img/160x160/img3.jpg') }}" alt="">
                        <div class="ov-person-text">
                            <div class="ov-person-name">{{ $order->delivery_man['f_name'] . ' ' . $order->delivery_man['l_name'] }}</div>
                            <div class="ov-person-sub">{{ $order->delivery_man->orders_count }} طلب</div>
                        </div>
                    </div>
                    <div class="ov-detail">
                        <span class="k">الهاتف</span>
                        <span class="v"><a href="tel:{{ $order->delivery_man['phone'] }}">{{ $order->delivery_man['phone'] }}</a></span>
                    </div>
                    @php($dm_loc = $order->dm_last_location)
                    <div class="ov-detail">
                        <span class="k">آخر موقع</span>
                        @if (isset($dm_loc))
                            <span class="v"><a target="_blank" href="http://maps.google.com/maps?z=12&t=m&q=loc:{{ $dm_loc['latitude'] }}+{{ $dm_loc['longitude'] }}">{{ $dm_loc['location'] }}</a></span>
                        @else
                            <span class="v muted">غير متاح</span>
                        @endif
                    </div>
                    @else
                    <p class="ov-empty">لم يُعيَّن مندوب بعد</p>
                    @endif
                </div>
            </section>
            @endif

            {{-- Delivery proof --}}
            @if ($order->order_type != 'dine_in')
            <section class="ov-card">
                <div class="ov-card-head">
                    <h2 class="ov-card-title">إثبات التوصيل</h2>
                    @if (($restaurant->restaurant_model == 'commission' && $restaurant->self_delivery_system) || ($restaurant->restaurant_model == 'subscription' && $restaurant?->restaurant_sub?->self_delivery == 1))
                    <button class="ov-card-action" data-toggle="modal" data-target=".order-proof-modal">+ إضافة</button>
                    @endif
                </div>
                <div class="ov-card-body">
                    @php($data = isset($order->order_proof) ? json_decode($order->order_proof, true) : 0)
                    @if ($data)
                    <div class="ov-proof">
                        @foreach ($data as $key => $img)
                            @php($img = is_array($img) ? $img : ['img'=>$img,'storage'=>'public'])
                            <img class="onerror-image"
                                 data-toggle="modal" data-target="#imagemodal{{ $key }}"
                                 src="{{ \App\CentralLogics\Helpers::get_full_url('order',$img['img'],$img['storage']) }}"
                                 data-onerror-image="{{ dynamicAsset('public/assets/admin/img/160x160/img2.jpg') }}"
                                 alt="">
                            <div class="modal fade" id="imagemodal{{ $key }}" tabindex="-1" role="dialog">
                                <div class="modal-dialog"><div class="modal-content">
                                    <div class="modal-header"><h4 class="modal-title">إثبات التوصيل</h4><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                                    <div class="modal-body"><img src="{{ \App\CentralLogics\Helpers::get_full_url('order',$img['img'],$img['storage']) }}" class="w-100"></div>
                                    @php($storage = $img['storage'] ?? 'public')
                                    @php($file = $storage == 's3' ? base64_encode('order/'.$img['img']) : base64_encode('public/order/'.$img['img']))
                                    <div class="modal-footer">
                                        <a class="btn btn-primary btn-sm" href="{{ route('vendor.file-manager.download', [$file,$storage]) }}"><i class="tio-download"></i> تحميل</a>
                                    </div>
                                </div></div>
                            </div>
                        @endforeach
                    </div>
                    @else
                    <p class="ov-empty">لا توجد صور بعد</p>
                    @endif
                </div>
            </section>
            @endif

            {{-- Subscription info --}}
            @if (isset($order->subscription))
            @php($subStatus = $order->subscription->status)
            <div class="ov-note {{ $subStatus == 'active' ? 'success' : ($subStatus == 'paused' ? 'warn' : '') }}">
                <i class="tio-refresh"></i>
                <strong>الاشتراك</strong>
                <span class="v">{{ translate('messages.' . $subStatus) }}</span>
            </div>
            @endif

        </div>

    </div>{{-- /ov-grid --}}

</div>{{-- /ov-page --}}


{{-- ════════════════════════════════════════════════════════════
     MODALS (unchanged logic, cleaned markup)
════════════════════════════════════════════════════════════ --}}

{{-- Assign Delivery Man --}}
<div class="modal fade" id="myModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header ov-modal-rtl">
                <h4 class="modal-title">{{ $selected_delivery_man != [] ? translate('messages.Change_Delivery_Men') : translate('messages.assign_deliveryman') }}</h4>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5 my-2">
                        <ul class="list-group overflow-auto max-height-400">
                            @if ($selected_delivery_man != [])
                            <li class="list-group-item">
                                <div class="d-flex align-items-center gap-2 justify-content-between">
                                    <div class="dm_list_selected media gap-2" data-id="{{ $selected_delivery_man['id'] }}">
                                        <img class="avatar avatar-60 rounded-10 onerror-image"
                                             data-onerror-image="{{ dynamicAsset('public/assets/admin/img/160x160/img1.jpg') }}"
                                             src="{{ $selected_delivery_man['image_full_url'] ?? dynamicAsset('public/assets/admin/img/160x160/img1.jpg') }}" alt="">
                                        <div class="media-body d-flex gap-1 flex-column">
                                            <h6 class="mb-1">{{ $selected_delivery_man['name'] }}</h6>
                                            <div class="fs-12 text-muted">{{ translate('Active_Orders') }}: {{ $selected_delivery_man['current_orders'] }}</div>
                                            <div class="fs-12 text-muted">{{ $selected_delivery_man['distance'] }} {{ translate('away_from_restaurant') }}</div>
                                        </div>
                                    </div>
                                    <span class="badge __badge badge-success">{{ translate('Currently_Assigned') }}</span>
                                </div>
                            </li>
                            @endif
                            <p class="mb-2 text-center text-muted small {{ $order->delivery_man_id ? 'mt-2' : '' }}">
                                {{ count($deliveryMen) }} {{ translate('messages.delivery_man') }}
                            </p>
                            @foreach ($deliveryMen as $dm)
                                @if ($dm['id'] != $order->delivery_man_id)
                                <li class="list-group-item">
                                    <div class="d-flex align-items-center gap-2 justify-content-between">
                                        <div class="dm_list media gap-2" data-id="{{ $dm['id'] }}">
                                            <img class="avatar avatar-60 rounded-10 onerror-image"
                                                 data-onerror-image="{{ dynamicAsset('public/assets/admin/img/160x160/img1.jpg') }}"
                                                 src="{{ $dm['image_full_url'] ?? dynamicAsset('public/assets/admin/img/160x160/img1.jpg') }}" alt="">
                                            <div class="media-body d-flex gap-1 flex-column">
                                                <h6 class="mb-1">{{ $dm['name'] }}</h6>
                                                <div class="fs-12 text-muted">{{ translate('Active_Orders') }}: {{ $dm['current_orders'] }}</div>
                                                <div class="fs-12 text-muted">{{ $dm['distance'] }} {{ translate('away_from_restaurant') }}</div>
                                            </div>
                                        </div>
                                        <a class="btn btn-primary btn-xs add-delivery-man" data-id="{{ $dm['id'] }}">
                                            {{ $order->delivery_man_id ? translate('messages.Reassign') : translate('messages.assign') }}
                                        </a>
                                    </div>
                                </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-md-7 modal_body_map">
                        <div class="location-map" id="dmassign-map"><div id="map_canvas"></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Location Map --}}
<div class="modal fade" id="locationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header ov-modal-rtl">
                <h4 class="modal-title">{{ translate('messages.location_data') }}</h4>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="location-map" id="location-map"><div id="location_map_canvas"></div></div>
            </div>
        </div>
    </div>
</div>

{{-- Add Delivery Proof --}}
<div class="modal fade order-proof-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header ov-modal-rtl">
                <h5 class="modal-title">{{ translate('messages.add_delivery_proof') }}</h5>
                <button type="button" class="btn btn-xs btn-icon btn-ghost-secondary" data-dismiss="modal"><i class="tio-clear tio-lg"></i></button>
            </div>
            <form action="{{ route('vendor.order.add-order-proof', [$order['id']]) }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="flex-grow-1 mx-auto">
                        <div class="d-flex flex-wrap __gap-12px __new-coba" id="coba">
                            @php($proof = isset($order->order_proof) ? json_decode($order->order_proof, true) : 0)
                            @if ($proof)
                                @foreach ($proof as $key => $photo)
                                    @php($photo = is_string($photo) ? ['img'=>$photo,'storage'=>'public'] : $photo)
                                    <div class="spartan_item_wrapper min-w-100px max-w-100px">
                                        <img class="img--square" src="{{ $order->order_proof_full_url[$key] }}" alt="">
                                        <a href="{{ route('vendor.order.remove-proof-image', ['id'=>$order['id'],'name'=>$photo['img']]) }}" class="spartan_remove_row"><i class="tio-add-to-trash"></i></a>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="text-right mt-2">
                        <button class="btn btn--primary">{{ translate('messages.submit') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('script_2')
<script src="https://maps.googleapis.com/maps/api/js?key={{ \App\Models\BusinessSetting::where('key', 'map_api_key')->first()->value }}&libraries=places&callback=initMap&v=3.45.8"></script>
<script src="{{ dynamicAsset('public/assets/admin/js/spartan-multi-image-picker.js') }}"></script>
<script>
"use strict";

/* ── Cancel ─────────────────────────────────────── */
$('.cancelled-status').on('click', function() {
    Swal.fire({
        title: '{{ translate('messages.are_you_sure_?') }}',
        type: 'warning',
        html: `<select class="form-control js-select2-custom mx-1" name="reason" id="reason">
            <option value=" ">{{ translate('select_cancellation_reason') }}</option>
            @foreach ($reasons as $r)
            <option value="{{ $r->reason }}">{{ $r->reason }}</option>
            @endforeach
        </select>`,
        showCancelButton: true,
        cancelButtonColor: 'default',
        confirmButtonColor: '#d63a3a',
        cancelButtonText: '{{ translate('messages.no') }}',
        confirmButtonText: '{{ translate('messages.yes') }}',
        reverseButtons: true,
        onOpen: function() {
            $('.js-select2-custom').select2({ minimumResultsForSearch: 5, width: '100%' });
        }
    }).then((result) => {
        if (result.value) {
            let reason = document.getElementById('reason').value;
            location.href = '{!! route('vendor.order.status', ['id'=>$order['id'],'order_status'=>'canceled']) !!}&reason=' + reason;
        }
    });
});

/* ── Status change ───────────────────────────────── */
$('.order-status-change-alert').on('click', function() {
    let route       = $(this).data('url');
    let message     = $(this).data('message');
    let verification = $(this).data('verification');
    let processing  = $(this).data('processing-time') ?? false;

    if (verification) {
        Swal.fire({
            title: '{{ translate('Enter order verification code') }}',
            input: 'text',
            inputAttributes: { autocapitalize: 'off' },
            showCancelButton: true,
            confirmButtonColor: '#1b2e5e',
            confirmButtonText: '{{ translate('messages.submit') }}',
            showLoaderOnConfirm: true,
            preConfirm: (otp) => { location.href = route + '&otp=' + otp; },
            allowOutsideClick: () => !Swal.isLoading()
        });
    } else if (processing) {
        Swal.fire({
            title: '{{ translate('messages.Are you sure ?') }}',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1b2e5e',
            cancelButtonText: '{{ translate('messages.Cancel') }}',
            confirmButtonText: '{{ translate('messages.submit') }}',
            input: 'text',
            html: message + '<br/><label>{{ translate('Enter Processing time in minutes') }}</label>',
            inputValue: processing,
            preConfirm: (processing_time) => { location.href = route + '&processing_time=' + processing_time; },
            allowOutsideClick: () => !Swal.isLoading()
        });
    } else {
        Swal.fire({
            title: '{{ translate('messages.Are you sure ?') }}',
            text: message,
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1b2e5e',
            cancelButtonText: '{{ translate('messages.No') }}',
            confirmButtonText: '{{ translate('messages.Yes') }}',
            reverseButtons: true
        }).then((result) => {
            if (result.value) location.href = route;
        });
    }
});

function last_location_view() {
    toastr.warning('{{ translate('Only available when order is out for delivery!') }}', { CloseButton: true, ProgressBar: true });
}

/* ── Assign delivery man ─────────────────────────── */
$('.add-delivery-man').on('click', function() {
    let id = $(this).data('id');
    $.ajax({
        type: 'GET',
        url: '{{ url('/') }}/restaurant-panel/order/add-delivery-man/{{ $order['id'] }}/' + id,
        success: function() {
            toastr.success('{{ translate('Successfully_added') }}', { CloseButton: true, ProgressBar: true });
            location.reload();
        },
        error: function(response) {
            toastr.error(response.responseJSON.message, { CloseButton: true, ProgressBar: true });
        }
    });
});

/* ── Google Maps ─────────────────────────────────── */
let deliveryMan = <?php echo json_encode($deliveryMen); ?>;
let map = null;
let myLatlng = new google.maps.LatLng({{ isset($order->restaurant) ? $order->restaurant->latitude : 0 }}, {{ isset($order->restaurant) ? $order->restaurant->longitude : 0 }});
let dmbounds = new google.maps.LatLngBounds(null);
let locationbounds = new google.maps.LatLngBounds(null);
let dmMarkers = [];
dmbounds.extend(myLatlng);
locationbounds.extend(myLatlng);
let myOptions = {
    center: myLatlng, zoom: 13, mapTypeId: google.maps.MapTypeId.ROADMAP,
    panControl: true, mapTypeControl: false, zoomControl: true, scaleControl: false, streetViewControl: false,
    panControlOptions: { position: google.maps.ControlPosition.RIGHT_CENTER },
    zoomControlOptions: { style: google.maps.ZoomControlStyle.LARGE, position: google.maps.ControlPosition.RIGHT_CENTER }
};

function initializeGMap() {
    map = new google.maps.Map(document.getElementById('map_canvas'), myOptions);
    let infowindow = new google.maps.InfoWindow();
    @if ($order->restaurant)
    let Restaurantmarker = new google.maps.Marker({
        position: new google.maps.LatLng({{ $order->restaurant->latitude }}, {{ $order->restaurant->longitude }}),
        map: map, title: "{{ Str::limit($order->restaurant->name, 15, '...') }}",
        icon: "{{ dynamicAsset('public/assets/admin/img/restaurant_map_1.png') }}"
    });
    google.maps.event.addListener(Restaurantmarker, 'click', (function(m) {
        return function() {
            infowindow.setContent(`<div class='float--left'><img class='js--design-1' onerror="this.src='{{ dynamicAsset('public/assets/admin/img/160x160/img1.jpg') }}'" src='{{ dynamicStorage('storage/app/public/restaurant/' . $order->restaurant->logo) }}'></div><div class='text-break float--right p--10px'><b>{{ Str::limit($order->restaurant->name, 15, '...') }}</b><br/>{{ $order->restaurant->address }}</div>`);
            infowindow.open(map, m);
        };
    })(Restaurantmarker));
    @endif
    map.fitBounds(dmbounds);
    for (let i = 0; i < deliveryMan.length; i++) {
        if (deliveryMan[i].lat) {
            let point = new google.maps.LatLng(deliveryMan[i].lat, deliveryMan[i].lng);
            dmbounds.extend(point); map.fitBounds(dmbounds);
            let d_man = "{{ $order?->delivery_man_id ?? 0 }}";
            let icon = deliveryMan[i].id == d_man ? "{{ dynamicAsset('public/assets/admin/img/delivery_boy_map_2.png') }}" : "{{ dynamicAsset('public/assets/admin/img/delivery_boy_map_1.png') }}";
            let marker = new google.maps.Marker({ position: point, map: map, title: deliveryMan[i].location, icon: icon });
            dmMarkers[deliveryMan[i].id] = marker;
            google.maps.event.addListener(marker, 'click', (function(mk, i) {
                return function() {
                    infowindow.setContent(`<div class='float--left'><img class='js--design-1 mt-2' onerror="this.src='{{ dynamicAsset('public/assets/admin/img/160x160/img1.jpg') }}'" src='{{ dynamicStorage('storage/app/public/delivery-man') }}/` + deliveryMan[i].image + `'></div><div class='float--right p--10px'><b>` + deliveryMan[i].name + `</b><br/><div>{{ translate('messages.Active_Orders') }}:` + deliveryMan[i].current_orders + `</div>` + deliveryMan[i].location + `</div>`);
                    infowindow.open(map, mk);
                };
            })(marker, i));
        }
    }
}

$(document).ready(function() {
    $('#myModal').on('shown.bs.modal', function() {
        initializeGMap();
        google.maps.event.trigger(map, 'resize');
        map.setCenter(myLatlng);
    });
    $('#shipping-address-modal').on('shown.bs.modal', function() { initMap(); });

    function initializegLocationMap() {
        map = new google.maps.Map(document.getElementById('location_map_canvas'), myOptions);
        let infowindow = new google.maps.InfoWindow();
        @if (isset($c_address) && isset($c_address['latitude']) && isset($c_address['longitude']))
        let marker = new google.maps.Marker({
            position: new google.maps.LatLng({{ $c_address['latitude'] }}, {{ $c_address['longitude'] }}),
            map: map, title: "{{ $order->customer ? $order->customer->f_name.' '.$order->customer->l_name : $c_address['contact_person_name'] }}",
            icon: "{{ dynamicAsset('public/assets/admin/img/customer_location.png') }}"
        });
        google.maps.event.addListener(marker, 'click', (function(m) {
            return function() {
                infowindow.setContent(`<div class='float--left'><img class='js--design-1' src='{{ $order->customer ? dynamicStorage('storage/app/public/profile/'.$order?->customer?->image) : dynamicAsset('public/assets/admin/img/160x160/img3.png') }}'></div><div class='float--right p--10px'><b>{{ $order->customer ? $order->customer->f_name.' '.$order->customer->l_name : $c_address['contact_person_name'] }}</b><br/>{{ $c_address['address'] }}</div>`);
                infowindow.open(map, m);
            };
        })(marker));
        locationbounds.extend(marker.getPosition());
        @endif
        @if ($order->delivery_man && $order->dm_last_location)
        let dmmarker = new google.maps.Marker({
            position: new google.maps.LatLng({{ $order->dm_last_location['latitude'] }}, {{ $order->dm_last_location['longitude'] }}),
            map: map, title: "{{ $order->delivery_man->f_name }} {{ $order->delivery_man->l_name }}",
            icon: "{{ dynamicAsset('public/assets/admin/img/delivery_boy_map_2.png') }}"
        });
        locationbounds.extend(dmmarker.getPosition());
        @endif
        @if ($order->restaurant)
        let Retaurantmarker = new google.maps.Marker({
            position: new google.maps.LatLng({{ $order->restaurant->latitude }}, {{ $order->restaurant->longitude }}),
            map: map, title: "{{ Str::limit($order->restaurant->name, 15, '...') }}",
            icon: "{{ dynamicAsset('public/assets/admin/img/restaurant_map_1.png') }}"
        });
        locationbounds.extend(Retaurantmarker.getPosition());
        @endif
        google.maps.event.addListenerOnce(map, 'idle', function() { map.fitBounds(locationbounds); });
    }

    $('#locationModal').on('shown.bs.modal', function() { initializegLocationMap(); });

    $('.dm_list').on('click', function() {
        let id = $(this).data('id');
        map.panTo(dmMarkers[id].getPosition()); map.setZoom(13);
        dmMarkers[id].setAnimation(google.maps.Animation.BOUNCE);
        window.setTimeout(() => dmMarkers[id].setAnimation(null), 3);
    });
    $('.dm_list_selected').on('click', function() {
        let id = $(this).data('id');
        map.panTo(dmMarkers[id].getPosition()); map.setZoom(13);
        dmMarkers[id].setAnimation(google.maps.Animation.BOUNCE);
        window.setTimeout(() => dmMarkers[id].setAnimation(null), 3);
    });

    $(function() {
        $('#coba').spartanMultiImagePicker({
            fieldName: 'order_proof[]',
            maxCount: 6 - {{ ($order->order_proof && is_array($order->order_proof)) ? count(json_decode($order->order_proof)) : 0 }},
            rowHeight: '100px !important',
            groupClassName: 'spartan_item_wrapper min-w-100px max-w-100px',
            maxFileSize: '',
            placeholderImage: { image: "{{ dynamicAsset('public/assets/admin/img/upload.png') }}", width: '100px' },
            dropFileLabel: 'Drop Here',
            onExtensionErr: function() {
                toastr.error('{{ translate('messages.please_only_input_png_or_jpg_type_file') }}', { CloseButton: true, ProgressBar: true });
            },
            onSizeErr: function() {
                toastr.error('{{ translate('messages.file_size_too_big') }}', { CloseButton: true, ProgressBar: true });
            }
        });
    });
});

/* ─── Keyboard shortcuts (operational speed for restaurant staff) ─── */
(function () {
    var FORM_TAGS = { INPUT: 1, TEXTAREA: 1, SELECT: 1 };

    function isTypingTarget(el) {
        if (!el) return false;
        if (el.isContentEditable) return true;
        if (FORM_TAGS[el.tagName]) return true;
        // Bootstrap modal open: ignore most keys except Esc (which Bootstrap already handles)
        return false;
    }

    function modalIsOpen() {
        return document.querySelector('.modal.show, .modal.in') !== null;
    }

    function clickIfPresent(selector) {
        var el = document.querySelector(selector);
        if (el && !el.disabled && el.offsetParent !== null) {
            el.click();
            return true;
        }
        return false;
    }

    function navigate(selector) {
        var a = document.querySelector(selector);
        if (a && a.href) {
            window.location.href = a.href;
            return true;
        }
        return false;
    }

    var panel = document.getElementById('ov-kbd-panel');
    var toggle = document.getElementById('ov-kbd-toggle');

    function setPanel(open) {
        if (!panel) return;
        if (open) {
            panel.hidden = false;
            // double rAF so the transition catches the class change after `hidden` removal
            requestAnimationFrame(function () {
                requestAnimationFrame(function () { panel.classList.add('is-open'); });
            });
            if (toggle) toggle.setAttribute('aria-expanded', 'true');
        } else {
            panel.classList.remove('is-open');
            if (toggle) toggle.setAttribute('aria-expanded', 'false');
            // hide after transition to remove from a11y tree
            setTimeout(function () {
                if (!panel.classList.contains('is-open')) panel.hidden = true;
            }, 220);
        }
    }

    if (toggle && panel) {
        toggle.addEventListener('click', function () {
            setPanel(panel.hidden || !panel.classList.contains('is-open'));
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.ctrlKey || e.metaKey || e.altKey) return;
        if (isTypingTarget(e.target)) return;
        if (modalIsOpen() && e.key !== 'Escape') return;

        switch (e.key) {
            case 'Enter':
            case ' ':
                if (clickIfPresent('.ov-status-hero-action .ov-action-primary')) {
                    e.preventDefault();
                }
                break;
            case 'Escape':
                if (panel && panel.classList.contains('is-open')) {
                    setPanel(false);
                    e.preventDefault();
                    break;
                }
                if (clickIfPresent('.ov-status-hero-action .ov-action-cancel')) {
                    e.preventDefault();
                }
                break;
            case 'd':
            case 'D':
            case 'ي': // Arabic-keyboard equivalent of "d"
                if (clickIfPresent('.ov-status-hero-action .ov-assign-btn')) {
                    e.preventDefault();
                }
                break;
            case 'p':
            case 'P':
            case 'ح': // Arabic-keyboard equivalent of "p"
                if (navigate('.ov-print-btn')) {
                    e.preventDefault();
                }
                break;
            case 'ArrowLeft':
                // RTL: left arrow = next order (matches the on-screen chevron direction)
                if (navigate('.ov-topbar-nav .ov-nav-btn[title="الطلب التالي"]')) {
                    e.preventDefault();
                }
                break;
            case 'ArrowRight':
                if (navigate('.ov-topbar-nav .ov-nav-btn[title="الطلب السابق"]')) {
                    e.preventDefault();
                }
                break;
            case '?':
                setPanel(!(panel && panel.classList.contains('is-open')));
                e.preventDefault();
                break;
        }
    });
})();
</script>
@endpush
