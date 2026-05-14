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
    'processing' => 'جاري التحضير',
    'handover'   => 'جاهز للتسليم',
    'picked_up'  => 'في الطريق',
    'delivered'  => $order->order_type == 'dine_in' ? 'مكتمل' : 'تم التوصيل',
    'canceled'   => 'ملغي',
    'failed'     => 'فشل',
];
$statusLabel = $statusLabels[$status] ?? translate(str_replace('_',' ',$status));

$isTerminal = in_array($status, ['canceled','failed','refunded','refund_requested','refund_request_canceled']);
$isDelivery = !in_array($order['order_type'], ['dine_in','take_away']);

/* 5-step stepper matching the image */
$stepDefs = [
    ['n'=>1, 'key'=>'accepted',    'label'=>'تم القبول'],
    ['n'=>2, 'key'=>'confirmed',   'label'=>'تم التأكيد'],
    ['n'=>3, 'key'=>'processing',  'label'=>'جاري التحضير'],
    ['n'=>4, 'key'=>'handover',    'label'=>'جاهز للتسليم'],
    ['n'=>5, 'key'=>'delivered',   'label'=>'تم التوصيل'],
];
$stepPhaseMap = [
    'pending'    => 0,
    'confirmed'  => 2,
    'accepted'   => 1,
    'processing' => 3,
    'handover'   => 4,
    'picked_up'  => 4,
    'delivered'  => 5,
];
$currentStep = $stepPhaseMap[$status] ?? 0;

/* semantic class */
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

$customerOrdersCount = $order->customer ? \App\Models\Order::where('user_id', $order->customer->id)->count() : 0;
$isNewCustomer = $order->customer && $customerOrdersCount <= 1;

/* status timestamps from logs */
$statusLogs = \App\Models\Log::where('model','Order')->where('model_id', $order->id)
    ->whereIn('action_type', ['status_change','order_status'])
    ->orderBy('created_at')->get();
$statusTimes = [];
foreach ($statusLogs as $lg) {
    $af = is_string($lg->after_state) ? json_decode($lg->after_state, true) : (array)$lg->after_state;
    $st = $af['order_status'] ?? null;
    if ($st && !isset($statusTimes[$st])) {
        $statusTimes[$st] = $lg->created_at;
    }
}
/* map step keys to status */
$stepStatusMap = [
    'accepted'   => ['accepted','confirmed'],
    'confirmed'  => ['confirmed'],
    'processing' => ['processing'],
    'handover'   => ['handover'],
    'delivered'  => ['delivered'],
];

/* pending orders count for top bar */
$pendingOrdersCount = \App\Models\Order::where('restaurant_id', $restaurant->id)
    ->whereIn('order_status', ['pending','accepted','confirmed','processing','handover'])
    ->count();
$hr = (int) date('H');
$greeting = $hr < 12 ? 'صباح الخير ☀️' : ($hr < 17 ? 'مساء الخير 🌤️' : 'مساء الخير 🌙');
$restaurantOpen = $restaurant->active ?? 1;
?>

<div class="content container-fluid ov-page" id="printableArea">

    {{-- ── Contextual top bar ───────────────────────────────── --}}
    <div class="ov-ctx-bar">
        <div class="ov-ctx-bar-left">
            <div class="ov-ctx-greeting">
                <span class="ov-ctx-hi">{{ $greeting }}</span>
                <span class="ov-ctx-msg">
                    @if($pendingOrdersCount > 0)
                        لديك {{ $pendingOrdersCount }} طلب يحتاج اهتمامك
                    @else
                        لا توجد طلبات عاجلة الآن
                    @endif
                </span>
            </div>
        </div>
        <div class="ov-ctx-bar-right">
            <span class="ov-ctx-status-pill {{ $restaurantOpen ? 'open' : 'closed' }}">
                <span class="ov-ctx-pulse"></span>
                {{ $restaurantOpen ? 'المطعم مفتوح' : 'المطعم مغلق' }}
            </span>
            <button class="ov-ctx-btn ghost" onclick="location.reload()">
                <i class="tio-refresh"></i> تحديث
            </button>
            <a href="{{ route('vendor.order.list', ['status' => 'all']) }}" class="ov-ctx-btn primary">
                <i class="tio-receipt"></i> كل الطلبات
            </a>
        </div>
    </div>

    {{-- ── Breadcrumb + topbar ──────────────────────────────── --}}
    <div class="ov-topbar">
        <div class="ov-topbar-left">
            <div class="ov-breadcrumb">
                <a href="{{ route('vendor.order.list', ['status' => 'all']) }}" class="ov-breadcrumb-link">الطلبات</a>
                <span class="ov-breadcrumb-sep"><i class="tio-chevron-left"></i></span>
                <span class="ov-breadcrumb-cur">تفاصيل الطلب</span>
            </div>
            <div class="ov-topbar-id-row">
                <h1 class="ov-order-id">طلب #{{ $order['id'] }}</h1>
                <span class="ov-order-date">{{ date('d M Y · ' . config('timeformat'), strtotime($order['created_at'])) }}</span>
                @if ($order->edited)<span class="ov-tag warn">معدّل</span>@endif
                @if ($subscription)<span class="ov-sub-badge"><i class="tio-refresh"></i> اشتراك</span>@endif
                @if ($campaign_order)<span class="ov-sub-badge gold"><i class="tio-star"></i> عرض</span>@endif
            </div>
        </div>
        <div class="ov-topbar-right">
            <a class="ov-nav-btn" id="ov-prev-btn" href="{{ route('vendor.order.details', [$order['id'] - 1]) }}" title="الطلب السابق">
                <i class="tio-chevron-right"></i>
                <span class="ov-nav-label">السابق</span>
            </a>
            <a class="ov-nav-btn" id="ov-next-btn" href="{{ route('vendor.order.details', [$order['id'] + 1]) }}" title="الطلب التالي">
                <span class="ov-nav-label">التالي</span>
                <i class="tio-chevron-left"></i>
            </a>
            <a class="ov-print-btn" href="{{ route('vendor.order.generate-invoice', [$order['id']]) }}" title="طباعة الإيصال (P)">
                <i class="tio-print"></i>
                <span class="ov-nav-label">طباعة</span>
                <span class="ov-kbd-inline">P</span>
            </a>
        </div>
    </div>

    {{-- ── Quick-action buttons row (matches image) ─────────── --}}
    <div class="ov-actions-bar">
        <div class="ov-actions-left">
            @if ($isNewCustomer)
            <div class="ov-new-customer-alert">
                <div class="ov-new-customer-avatar">{{ mb_substr($order->customer['f_name'] ?? 'ع', 0, 1) }}</div>
                <div class="ov-new-customer-text">
                    <span class="ov-new-customer-label">عميل جديد · أول طلب</span>
                    <span class="ov-new-customer-sub">اتصل به قبل التسليم للتأكد من العنوان</span>
                </div>
            </div>
            @endif
        </div>
        <div class="ov-actions-right">
            <a class="ov-action-chip" href="{{ route('vendor.order.generate-invoice', [$order['id']]) }}">
                <i class="tio-print"></i> اطبع الإيصال الآن قبل التسليم
            </a>
            <button class="ov-action-chip" onclick="void(0)">
                <i class="tio-chat-outlined"></i> أرسل رسالة &ldquo;طلبك في الطريق&rdquo;
            </button>
            @if (!in_array($order['order_type'],['dine_in','take_away']) && !$order->delivery_man && !in_array($order['order_status'], ['handover','delivered','canceled','refunded']))
            <button class="ov-action-chip ov-action-chip--primary" data-toggle="modal" data-target="#myModal">
                <i class="tio-bike"></i> اختر مندوب التوصيل
            </button>
            @endif
            @if ($order['order_status'] != 'delivered' && $order['order_status'] != 'canceled' && $order['order_status'] != 'failed')
            <div class="ov-action-chip--cta-wrap">
                @include('vendor-views.order.partials._status-actions')
            </div>
            @endif
        </div>
    </div>

    {{-- ── Hero card ───────────────────────────────────────── --}}
    <section class="ov-hero">
        <div class="ov-hero-top">
            <div class="ov-hero-status-row">
                <span class="ov-hero-status {{ $statusClass }}">
                    <span class="ov-dot"></span>
                    {{ $statusLabel }}
                    @if(!in_array($status,['delivered','canceled','failed']) && $isDelivery)
                        <span class="ov-status-sub">· بانتظار المندوب</span>
                    @endif
                </span>
                <span class="ov-hero-timer" id="ov-timer" data-created="{{ $order['created_at'] }}">
                    <i class="tio-time"></i> <span id="ov-timer-val">—</span> منذ
                </span>
            </div>
            <div class="ov-hero-amount-row">
                <h2 class="ov-hero-amount">{{ \App\CentralLogics\Helpers::format_currency($order['order_amount']) }}</h2>
            </div>
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
                        <span class="v unpaid">نقد عند التسليم</span>
                    @endif
                </span>
                <span class="ov-hero-meta-sep">·</span>
                <span class="ov-hero-meta-item">
                    <span class="k">النوع</span>
                    <span class="v">{{ translate(str_replace('_',' ',$order['order_type'])) }}</span>
                </span>
                @if ($order->schedule_at && ($order->scheduled || $subscription))
                <span class="ov-hero-meta-sep">·</span>
                <span class="ov-hero-meta-item">
                    <i class="tio-time"></i>
                    <span class="k">منتقى للوعد</span>
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
    </section>

    {{-- ── Stepper card (separate from hero) ─────────────────── --}}
    <section class="ov-stepper-card">
        @if ($isTerminal)
            <div class="ov-banner {{ $status }}">
                <i class="tio-{{ $status === 'canceled' ? 'clear-circle' : 'warning-outlined' }}"></i>
                <span>{{ $statusLabel }}</span>
            </div>
        @else
            <div class="ov-stepper" role="group" aria-label="مراحل الطلب">
                @foreach ($stepDefs as $i => $s)
                    @php
                        $isDone   = $currentStep > $s['n'];
                        $isActive = $currentStep === $s['n'];
                        $stepCls  = $isDone ? 'done' : ($isActive ? 'active' : '');
                        $stepTime = null;
                        foreach (($stepStatusMap[$s['key']] ?? [$s['key']]) as $sk) {
                            if (isset($statusTimes[$sk])) { $stepTime = $statusTimes[$sk]; break; }
                        }
                        if (!$stepTime && $s['key'] === 'accepted' && $isDone) {
                            $stepTime = $order->created_at;
                        }
                    @endphp
                    <div class="ov-step {{ $stepCls }}" aria-current="{{ $isActive ? 'step' : 'false' }}">
                        <div class="ov-step-node">
                            @if($isDone)
                                <i class="tio-checkmark"></i>
                            @else
                                <span class="ov-step-num">{{ $s['n'] }}</span>
                            @endif
                        </div>
                        <span class="ov-step-label">{{ $s['label'] }}</span>
                        @if($isActive)
                            <span class="ov-step-time active-now">الآن</span>
                        @elseif($isDone && $stepTime)
                            <span class="ov-step-time">{{ date(config('timeformat','h:i A'), strtotime($stepTime)) }}</span>
                        @endif
                        @if (!$loop->last)
                            <div class="ov-step-line {{ ($isDone || $isActive) ? 'filled' : '' }}"></div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Processing time progress bar --}}
        @if(in_array($status,['processing','handover']))
        @php
            $elapsedSec  = time() - strtotime($order['created_at']);
            $goalSec     = $max_processing_time * 60;
            $fillPct     = $goalSec > 0 ? min(100, round($elapsedSec / $goalSec * 100)) : 0;
            $goalTimeStr = $goalSec > 0 ? date(config('timeformat','h:i A'), strtotime($order['created_at']) + $goalSec) : '—';
            $nowTimeStr  = date(config('timeformat','h:i A'));
        @endphp
        <div class="ov-goal-bar-wrap">
            <div class="ov-goal-bar-row">
                <span class="ov-goal-label">
                    <i class="tio-time"></i>
                    وقت التحضير المستهدف: {{ $max_processing_time }} دقيقة
                    @if($fillPct >= 100)<span class="ov-goal-over">تجاوز الهدف!</span>@endif
                </span>
                <span class="ov-goal-times">هدف {{ $goalTimeStr }} · الآن {{ $nowTimeStr }}</span>
            </div>
            <div class="ov-goal-bar">
                <div class="ov-goal-bar-fill {{ $fillPct >= 100 ? 'over' : '' }}" style="width: {{ $fillPct }}%"></div>
            </div>
        </div>
        @endif
    </section>

    {{-- ── Notes ─────────────────────────────────────────────── --}}
    @if($order['order_note'])
    <div class="ov-note"><i class="tio-comment-outlined"></i><strong>ملاحظة الطلب:</strong>&nbsp;{{ $order['order_note'] }}</div>
    @endif
    @if ($order['delivery_instruction'])
    <div class="ov-note info"><i class="tio-info-outlined"></i><strong>تعليمات التوصيل:</strong>&nbsp;{{ translate($order->delivery_instruction) }}</div>
    @endif
    @if ($order['unavailable_item_note'])
    <div class="ov-note warn"><i class="tio-warning-outlined"></i><strong>إذا لم يتوفر:</strong>&nbsp;{{ translate($order->unavailable_item_note) }}</div>
    @endif

    {{-- ── Cancellation block ──────────────────────────────── --}}
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

    {{-- ── Main 2-col grid ────────────────────────────────── --}}
    <div class="ov-grid">

        {{-- COL 1: Items + totals --}}
        <div class="ov-col-items">
            <section class="ov-card">
                <div class="ov-card-head">
                    <h2 class="ov-card-title">الأصناف المطلوبة</h2>
                    <div style="display:flex;align-items:center;gap:.5rem;">
                        <span class="ov-items-count">{{ $order->details->count() }} أصناف · {{ $order->details->sum('quantity') }} قطع</span>
                        <a class="ov-card-action" href="{{ route('vendor.order.generate-invoice', [$order['id']]) }}" title="طباعة"><i class="tio-print"></i></a>
                    </div>
                </div>
                <ul class="ov-items">
                @foreach ($order->details as $key => $detail)
                    @if (isset($detail->food_id))
                        @php($detail->food = json_decode($detail->food_details, true))
                        @php($food = \App\Models\Food::where(['id' => $detail->food['id']])->first())
                        <li class="ov-item">
                            <div class="ov-item-img-wrap">
                                <img class="ov-item-img onerror-image"
                                     src="{{ $food['image_full_url'] ?? dynamicAsset('public/assets/admin/img/100x100/food-default-image.png') }}"
                                     data-onerror-image="{{ dynamicAsset('public/assets/admin/img/160x160/img2.jpg') }}"
                                     alt="">
                            </div>
                            <div class="ov-item-body">
                                <div class="ov-item-name">{{ Str::limit($detail->food['name'], 36, '…') }}</div>
                                <div class="ov-item-sub">
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
                                        <span class="ov-addon">+ {{ Str::limit($addon['name'],20,'…') }} ×{{ $addon['quantity'] }}</span>
                                    @endforeach
                                </div>
                                @endif
                                <div class="ov-item-price-mobile">{{ \App\CentralLogics\Helpers::format_currency($detail['price'] * $detail['quantity']) }}</div>
                            </div>
                            <div class="ov-item-qty-col">
                                <div class="ov-qty-stepper">
                                    <button class="ov-qty-btn" disabled>−</button>
                                    <span class="ov-qty-val">{{ $detail['quantity'] }}</span>
                                    <button class="ov-qty-btn" disabled>+</button>
                                </div>
                                <span class="ov-item-unit-price">{{ \App\CentralLogics\Helpers::format_currency($detail['price']) }} للوحدة</span>
                            </div>
                            <div class="ov-item-price">{{ \App\CentralLogics\Helpers::format_currency($detail['price'] * $detail['quantity']) }}</div>
                        </li>
                    @elseif(isset($detail->item_campaign_id))
                        @php($detail->campaign = json_decode($detail->food_details, true))
                        <li class="ov-item">
                            <div class="ov-item-img-wrap">
                                <img class="ov-item-img onerror-image"
                                     src="{{ $detail->campaign['image_full_url'] ?? dynamicAsset('public/assets/admin/img/100x100/food-default-image.png') }}"
                                     data-onerror-image="{{ dynamicAsset('public/assets/admin/img/160x160/img2.jpg') }}"
                                     alt="">
                            </div>
                            <div class="ov-item-body">
                                <div class="ov-item-name">{{ Str::limit($detail->campaign['name'], 36, '…') }}</div>
                                @php($addons = json_decode($detail['add_ons'], true))
                                @if (count($addons))
                                <div class="ov-item-addons">
                                    @foreach ($addons as $addon)
                                        <span class="ov-addon">+ {{ Str::limit($addon['name'],20,'…') }} ×{{ $addon['quantity'] }}</span>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            <div class="ov-item-qty-col">
                                <div class="ov-qty-stepper">
                                    <button class="ov-qty-btn" disabled>−</button>
                                    <span class="ov-qty-val">{{ $detail['quantity'] }}</span>
                                    <button class="ov-qty-btn" disabled>+</button>
                                </div>
                                <span class="ov-item-unit-price">{{ \App\CentralLogics\Helpers::format_currency($detail['price']) }} للوحدة</span>
                            </div>
                            <div class="ov-item-price">{{ \App\CentralLogics\Helpers::format_currency($detail['price'] * $detail['quantity']) }}</div>
                        </li>
                    @endif
                @endforeach
                </ul>

                {{-- Totals --}}
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
                        <span>{{ $payment->payment_method == 'cash_on_delivery' ? 'دفع نقدي عند التوصيل' : 'دفع عبر ' . translate($payment->payment_method) }}</span>
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

        {{-- COL 2: Delivery man list + customer --}}
        <div class="ov-col-sidebar">

            {{-- Delivery man picker (visible for delivery orders when man not yet assigned) --}}
            @if (!in_array($order['order_type'],['dine_in','take_away']))
            <section class="ov-card">
                <div class="ov-card-head">
                    <h2 class="ov-card-title">
                        <i class="tio-bike"></i>
                        اختر مندوب التوصيل
                    </h2>
                    <span class="ov-dm-count">{{ count($deliveryMen) }} مناديب قريبون</span>
                </div>
                <div class="ov-card-body ov-dm-list-wrap">
                    @if ($order->delivery_man)
                    <div class="ov-dm-card assigned">
                        <div class="ov-dm-avatar assigned-avatar">{{ mb_substr($order->delivery_man['f_name'] ?? 'م', 0, 1) }}</div>
                        <div class="ov-dm-info">
                            <span class="ov-dm-name">{{ $order->delivery_man['f_name'] . ' ' . $order->delivery_man['l_name'] }}</span>
                            <span class="ov-dm-meta">معيَّن حالياً</span>
                        </div>
                        @if(!in_array($order['order_status'], ['handover','delivered','canceled','refunded']))
                        <button class="ov-dm-assign assigned-btn" data-toggle="modal" data-target="#myModal">تغيير</button>
                        @endif
                    </div>
                    @else
                    <ul class="ov-dm-list">
                        <?php if(count($deliveryMen) == 0): ?>
                        <li class="ov-dm-empty">لا يوجد مناديب متاحون الآن</li>
                        <?php else: foreach($deliveryMen as $dm):
                            $eta = isset($dm['distance']) ? $dm['distance'] : '—';
                            $mins = isset($dm['distance']) && is_numeric($dm['distance']) ? round((float)$dm['distance'] * 3) : '—';
                            $dmColors = ['#e8d5c4','#c4d5e8','#c4e8d5','#e8c4d5'];
                            $dmColor = $dmColors[array_search($dm, $deliveryMen) % 4];
                            $dmBusy = isset($dm['current_orders']) && $dm['current_orders'] > 0;
                            $dmCanAssign = !in_array($order['order_status'],['handover','delivered','canceled','refunded']);
                        ?>
                        <li class="ov-dm-card">
                            <div class="ov-dm-avatar" style="background:<?php echo $dmColor ?>">{{ mb_substr($dm['name'] ?? 'م', 0, 1) }}</div>
                            <div class="ov-dm-info">
                                <span class="ov-dm-name">{{ $dm['name'] }}</span>
                                <span class="ov-dm-meta">
                                    <?php if($dmBusy): ?>
                                        <span class="ov-dm-badge warn">{{ $dm['current_orders'] }} طلبات</span>
                                    <?php else: ?>
                                        <span class="ov-dm-badge ok">متاح</span>
                                    <?php endif ?>
                                    · {{ $eta }} كم
                                </span>
                            </div>
                            <div class="ov-dm-eta">
                                <span class="ov-dm-eta-val"><?php echo $mins ?> د</span>
                                <span class="ov-dm-eta-sub">وصول متوقع</span>
                            </div>
                            <?php if($dmCanAssign): ?>
                            <a class="ov-dm-assign add-delivery-man" data-id="{{ $dm['id'] }}" href="javascript:">تعيين</a>
                            <?php endif ?>
                        </li>
                        <?php endforeach; endif ?>
                    </ul>
                    @endif
                </div>
            </section>
            @endif

            {{-- Customer --}}
            <section class="ov-card">
                <div class="ov-card-head">
                    <h2 class="ov-card-title">العميل</h2>
                    @if($order->customer)
                    <a class="ov-card-action ov-card-action--profile" href="javascript:">
                        <i class="tio-user-outlined"></i> عرض الملف
                    </a>
                    @endif
                </div>
                <div class="ov-card-body">
                    @if ($order->customer && $order->is_guest == 0)
                    <div class="ov-person">
                        <div class="ov-person-avatar">{{ mb_substr($order->customer['f_name'] ?? 'ع', 0, 1) }}</div>
                        <div class="ov-person-text">
                            <div class="ov-person-name">{{ $order->customer['f_name'] . ' ' . $order->customer['l_name'] }}</div>
                            <div class="ov-person-sub">
                                @if($isNewCustomer)
                                <span class="ov-tag success">عميل جديد</span>
                                @endif
                                {{ $customerOrdersCount == 1 ? 'أول طلب اليوم' : $customerOrdersCount . ' طلب سابق' }}
                            </div>
                        </div>
                        <div class="ov-person-actions">
                            @if($order->customer['phone'])
                            <a href="tel:{{ $order->customer['phone'] }}" class="ov-icon-btn" title="اتصال"><i class="tio-call"></i></a>
                            @endif
                            <button class="ov-icon-btn" title="إشعار"><i class="tio-notifications-outlined"></i></button>
                            <button class="ov-icon-btn" title="تحديث"><i class="tio-refresh"></i></button>
                        </div>
                    </div>
                    <div class="ov-customer-stats">
                        <div class="ov-stat-box">
                            <span class="ov-stat-val">{{ \App\CentralLogics\Helpers::format_currency($order['order_amount']) }}</span>
                            <span class="ov-stat-label">إجمالي الإنفاق</span>
                        </div>
                        <div class="ov-stat-box">
                            <span class="ov-stat-val">{{ $customerOrdersCount }}</span>
                            <span class="ov-stat-label">إجمالي الطلبات</span>
                        </div>
                        <div class="ov-stat-box">
                            <span class="ov-stat-val">0</span>
                            <span class="ov-stat-label">شكاوى سابقة</span>
                        </div>
                    </div>
                    <div class="ov-detail"><span class="k">الهاتف</span><span class="v"><a href="tel:{{ $order->customer['phone'] }}">{{ $order->customer['phone'] }}</a></span></div>
                    @if ($order->customer['email'])
                    <div class="ov-detail"><span class="k">البريد</span><span class="v">{{ $order->customer['email'] }}</span></div>
                    @endif
                    @elseif($order->is_guest)
                    <span class="ov-tag success ov-tag--block">زائر</span>
                    @else
                    <p class="ov-empty">لم يُعثر على العميل</p>
                    @endif

                    {{-- Cutlery note: neutral when not needed, amber when required --}}
                    <div class="ov-cutlery-note {{ $order->cutlery ? 'need' : '' }}">
                        <i class="tio-cutlery"></i>
                        <span>أدوات المائدة</span>
                        <strong>{{ $order->cutlery ? 'مطلوبة ⚠️' : 'غير مطلوبة' }}</strong>
                    </div>
                </div>
            </section>

            {{-- Delivery address --}}
            @if ($order->delivery_address)
            @php($address = json_decode($order->delivery_address, true))
            <section class="ov-card">
                <div class="ov-card-head">
                    <h2 class="ov-card-title">{{ $order->order_type == 'dine_in' ? 'بيانات الطلب' : 'عنوان التوصيل' }}</h2>
                    @if($c_address)
                    <button class="ov-card-action" data-toggle="modal" data-target="#locationModal"><i class="tio-poi-outlined"></i> الخريطة</button>
                    @endif
                </div>
                <div class="ov-card-body">
                    @if (isset($address))
                    <div class="ov-detail"><span class="k">المستلم</span><span class="v">{{ $address['contact_person_name'] ?? '' }}</span></div>
                    <div class="ov-detail"><span class="k">الاتصال</span><span class="v"><a href="tel:{{ $address['contact_person_number'] ?? '' }}">{{ $address['contact_person_number'] ?? '' }}</a></span></div>
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

            {{-- Dine-in table data --}}
            @if ($order->order_type == 'dine_in')
            <section class="ov-card">
                <div class="ov-card-head"><h2 class="ov-card-title">بيانات الطاولة</h2></div>
                <div class="ov-card-body">
                    <form action="{{ route('vendor.order.add_dine_in_table_number', [$order['id']]) }}" method="post">
                        @method('PUT') @csrf
                        <div class="ov-field"><label class="ov-field-label">{{ translate('Table_Number') }}</label>
                            <input type="text" class="ov-input" @readonly(in_array($order['order_status'],['failed','delivered','refund_requested','canceled','refunded','refund_request_canceled'])) maxlength="20" value="{{ $order?->OrderReference?->table_number }}" name="table_number" placeholder="مثال: 10">
                        </div>
                        <div class="ov-field"><label class="ov-field-label">{{ translate('Token_Number') }}</label>
                            <input type="text" class="ov-input" @readonly(in_array($order['order_status'],['failed','delivered','refund_requested','canceled','refunded','refund_request_canceled'])) maxlength="20" value="{{ $order?->OrderReference?->token_number }}" name="token_number" placeholder="مثال: 32">
                        </div>
                        @if (!in_array($order['order_status'],['failed','delivered','refund_requested','canceled','refunded','refund_request_canceled']))
                        <button type="submit" class="ov-btn-primary ov-btn-primary--block">{{ translate('messages.Save') }}</button>
                        @endif
                    </form>
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
                                    <div class="modal-header ov-modal-rtl"><h4 class="modal-title">إثبات التوصيل</h4><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                                    <div class="modal-body"><img src="{{ \App\CentralLogics\Helpers::get_full_url('order',$img['img'],$img['storage']) }}" class="w-100"></div>
                                    @php($storage = $img['storage'] ?? 'public')
                                    @php($file = $storage == 's3' ? base64_encode('order/'.$img['img']) : base64_encode('public/order/'.$img['img']))
                                    <div class="modal-footer"><a class="btn btn-primary btn-sm" href="{{ route('vendor.file-manager.download', [$file,$storage]) }}"><i class="tio-download"></i> تحميل</a></div>
                                </div></div>
                            </div>
                        @endforeach
                    </div>
                    @else
                    <div class="ov-proof-empty">
                        <i class="tio-camera-outlined"></i>
                        <p>لا توجد صور إثبات بعد</p>
                        @if (($restaurant->restaurant_model == 'commission' && $restaurant->self_delivery_system) || ($restaurant->restaurant_model == 'subscription' && $restaurant?->restaurant_sub?->self_delivery == 1))
                        <button class="ov-proof-empty-btn" data-toggle="modal" data-target=".order-proof-modal">+ أضف صورة الآن</button>
                        @elseif(!in_array($order['order_status'], ['delivered','canceled','failed']))
                        <span class="ov-proof-empty-hint">سيضيفها المندوب عند التسليم</span>
                        @endif
                    </div>
                    @endif
                </div>
            </section>
            @endif

        </div>

    </div>{{-- /ov-grid --}}

</div>{{-- /ov-page --}}


{{-- ════ MODALS ════ --}}

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

{{-- Delivery Proof --}}
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

/* ── Live timer with urgency coloring ────────────── */
(function() {
    var el   = document.getElementById('ov-timer-val');
    var wrap = document.getElementById('ov-timer');
    if (!el || !wrap) return;
    var created = new Date(wrap.dataset.created.replace(' ', 'T'));
    function tick() {
        var diff = Math.floor((Date.now() - created) / 1000);
        var h = Math.floor(diff / 3600);
        var m = Math.floor((diff % 3600) / 60);
        var s = diff % 60;
        el.textContent = (h > 0 ? h + ':' : '') + (h > 0 ? String(m).padStart(2,'0') : m) + ':' + String(s).padStart(2,'0');
        /* <30 min = ok (green), 30-60 min = warn (amber), >60 min = urgent (red) */
        wrap.classList.remove('timer-ok', 'timer-warn', 'timer-urgent');
        if (diff < 1800)      wrap.classList.add('timer-ok');
        else if (diff < 3600) wrap.classList.add('timer-warn');
        else                  wrap.classList.add('timer-urgent');
    }
    tick();
    setInterval(tick, 1000);
})();

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
        }
    }
}

$(document).ready(function() {
    $('#myModal').on('shown.bs.modal', function() { initializeGMap(); google.maps.event.trigger(map, 'resize'); map.setCenter(myLatlng); });
    $('#shipping-address-modal').on('shown.bs.modal', function() { initMap(); });

    function initializegLocationMap() {
        map = new google.maps.Map(document.getElementById('location_map_canvas'), myOptions);
        let infowindow = new google.maps.InfoWindow();
        @if (isset($c_address) && isset($c_address['latitude']) && isset($c_address['longitude']))
        let marker = new google.maps.Marker({
            position: new google.maps.LatLng({{ $c_address['latitude'] }}, {{ $c_address['longitude'] }}),
            map: map,
            icon: "{{ dynamicAsset('public/assets/admin/img/customer_location.png') }}"
        });
        locationbounds.extend(marker.getPosition());
        @endif
        @if ($order->delivery_man && $order->dm_last_location)
        let dmmarker = new google.maps.Marker({
            position: new google.maps.LatLng({{ $order->dm_last_location['latitude'] }}, {{ $order->dm_last_location['longitude'] }}),
            map: map,
            icon: "{{ dynamicAsset('public/assets/admin/img/delivery_boy_map_2.png') }}"
        });
        locationbounds.extend(dmmarker.getPosition());
        @endif
        @if ($order->restaurant)
        let Retaurantmarker = new google.maps.Marker({
            position: new google.maps.LatLng({{ $order->restaurant->latitude }}, {{ $order->restaurant->longitude }}),
            map: map,
            icon: "{{ dynamicAsset('public/assets/admin/img/restaurant_map_1.png') }}"
        });
        locationbounds.extend(Retaurantmarker.getPosition());
        @endif
        google.maps.event.addListenerOnce(map, 'idle', function() { map.fitBounds(locationbounds); });
    }

    $('#locationModal').on('shown.bs.modal', function() { initializegLocationMap(); });

    $('.dm_list').on('click', function() {
        let id = $(this).data('id');
        if (dmMarkers[id]) { map.panTo(dmMarkers[id].getPosition()); map.setZoom(13); }
    });
    $('.dm_list_selected').on('click', function() {
        let id = $(this).data('id');
        if (dmMarkers[id]) { map.panTo(dmMarkers[id].getPosition()); map.setZoom(13); }
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
            onExtensionErr: function() { toastr.error('{{ translate('messages.please_only_input_png_or_jpg_type_file') }}'); },
            onSizeErr: function() { toastr.error('{{ translate('messages.file_size_too_big') }}'); }
        });
    });
});

/* ── Keyboard shortcuts ──────────────────────── */
document.addEventListener('keydown', function(e) {
    /* Skip when typing in inputs or modals are open */
    if (e.target.matches('input,textarea,select') || document.querySelector('.modal.show')) return;
    if (e.key === 'ArrowLeft'  || e.key === 'ArrowRight') {
        var btn = e.key === 'ArrowRight'
            ? document.getElementById('ov-prev-btn')
            : document.getElementById('ov-next-btn');
        if (btn) btn.click();
    }
    if (e.key === 'p' || e.key === 'P') {
        var printBtn = document.querySelector('.ov-print-btn');
        if (printBtn) { e.preventDefault(); printBtn.click(); }
    }
});
</script>
@endpush
