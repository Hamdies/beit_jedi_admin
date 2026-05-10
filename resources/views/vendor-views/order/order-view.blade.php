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
<style>
/* ─── Reset & Base ─────────────────────────────────────────── */
.ov-page {
    --navy: #1b2e5e;
    --navy-dark: #111d3d;
    --navy-light: #253d7a;
    --navy-ghost: rgba(27,46,94,.06);
    --navy-border: rgba(27,46,94,.12);
    --gold: #c9a227;
    --gold-light: #f0d878;
    --gold-pale: rgba(201,162,39,.10);
    --red: #d63a3a;
    --red-pale: rgba(214,58,58,.08);
    --green: #1d8a5a;
    --green-pale: rgba(29,138,90,.10);
    --amber: #d97706;
    --amber-pale: rgba(217,119,6,.10);
    --surface: #f4f5f8;
    --card: #ffffff;
    --text-primary: #111827;
    --text-secondary: #4b5563;
    --text-muted: #9ca3af;
    --radius: 14px;
    --radius-sm: 8px;
    --shadow: 0 1px 4px rgba(0,0,0,.07), 0 4px 16px rgba(0,0,0,.04);
    --shadow-md: 0 2px 8px rgba(0,0,0,.10), 0 8px 24px rgba(0,0,0,.06);

    background: var(--surface);
    padding: 1.5rem 0 3rem;
    direction: rtl;
}

/* ─── Top bar ──────────────────────────────────────────────── */
.ov-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.25rem;
    gap: 1rem;
    flex-wrap: wrap;
}
.ov-topbar-right {
    display: flex;
    align-items: center;
    gap: .75rem;
}
.ov-order-id {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--navy);
    letter-spacing: -.02em;
    line-height: 1;
}
.ov-order-date {
    font-size: .82rem;
    color: var(--text-muted);
    margin-top: .2rem;
}
.ov-nav-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 1.5px solid var(--navy-border);
    background: var(--card);
    color: var(--navy);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all .15s;
    text-decoration: none;
    font-size: .9rem;
}
.ov-nav-btn:hover {
    background: var(--navy);
    color: #fff;
    border-color: var(--navy);
}
.ov-print-btn {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    padding: .45rem 1rem;
    border-radius: var(--radius-sm);
    border: 1.5px solid var(--navy-border);
    background: var(--card);
    color: var(--navy);
    font-size: .82rem;
    font-weight: 600;
    text-decoration: none;
    transition: all .15s;

}
.ov-print-btn:hover {
    background: var(--navy);
    color: #fff;
    border-color: var(--navy);
    text-decoration: none;
}

/* ─── Status Hero ──────────────────────────────────────────── */
.ov-status-hero {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding: 1.25rem 1.5rem;
    border-radius: var(--radius);
    background: var(--card);
    box-shadow: var(--shadow);
    border: 1.5px solid var(--navy-border);
    margin-bottom: 1.25rem;
    flex-wrap: wrap;
}
.ov-status-badge {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    padding: .55rem 1.25rem;
    border-radius: 999px;
    font-size: .92rem;
    font-weight: 700;
    letter-spacing: .01em;
    white-space: nowrap;
}
.ov-status-badge .dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}
.status-pending   { background: var(--amber-pale); color: var(--amber); }
.status-pending .dot { background: var(--amber); }
.status-confirmed, .status-accepted { background: rgba(59,130,246,.1); color: #2563eb; }
.status-confirmed .dot, .status-accepted .dot { background: #2563eb; }
.status-processing { background: var(--amber-pale); color: var(--amber); }
.status-processing .dot { background: var(--amber); }
.status-handover  { background: rgba(139,92,246,.1); color: #7c3aed; }
.status-handover .dot { background: #7c3aed; }
.status-picked_up { background: rgba(139,92,246,.1); color: #7c3aed; }
.status-picked_up .dot { background: #7c3aed; }
.status-delivered { background: var(--green-pale); color: var(--green); }
.status-delivered .dot { background: var(--green); }
.status-canceled  { background: var(--red-pale); color: var(--red); }
.status-canceled .dot { background: var(--red); }
.status-default   { background: var(--navy-ghost); color: var(--navy); }
.status-default .dot { background: var(--navy); }

.ov-status-meta {
    display: flex;
    gap: 1.5rem;
    flex-wrap: wrap;
    flex: 1;
}
.ov-meta-chip {
    display: flex;
    flex-direction: column;
    gap: .1rem;
}
.ov-meta-label {
    font-size: .72rem;
    color: var(--text-muted);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .04em;
}
.ov-meta-value {
    font-size: .88rem;
    font-weight: 700;
    color: var(--text-primary);
}
.ov-meta-value.paid   { color: var(--green); }
.ov-meta-value.unpaid { color: var(--red); }

/* ─── Main Grid ────────────────────────────────────────────── */
.ov-grid {
    display: grid;
    grid-template-columns: 280px 1fr 260px;
    gap: 1.25rem;
    align-items: start;
}
@media (max-width: 1199px) {
    .ov-grid { grid-template-columns: 1fr 1fr; }
    .ov-col-actions { grid-column: 1 / -1; }
}

/* ─── Cards ────────────────────────────────────────────────── */
.ov-card {
    background: var(--card);
    border-radius: var(--radius);
    border: 1.5px solid var(--navy-border);
    box-shadow: var(--shadow);
    overflow: hidden;
}
.ov-card-header {
    padding: .9rem 1.25rem;
    border-bottom: 1.5px solid var(--navy-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .5rem;
}
.ov-card-title {
    font-size: .82rem;
    font-weight: 700;
    color: var(--navy);
    text-transform: uppercase;
    letter-spacing: .06em;
    display: flex;
    align-items: center;
    gap: .5rem;
}
.ov-card-title-icon {
    width: 22px;
    height: 22px;
    background: var(--gold-pale);
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: var(--gold);
    font-size: .8rem;
}
.ov-card-body { padding: 1.25rem; }

/* ─── Action Column ────────────────────────────────────────── */
.ov-action-primary {
    display: block;
    width: 100%;
    padding: .85rem 1rem;
    border-radius: var(--radius-sm);
    background: linear-gradient(135deg, var(--navy-light) 0%, var(--navy) 100%);
    color: #fff;
    font-size: .97rem;
    font-weight: 700;
    text-align: center;
    border: none;
    cursor: pointer;
    transition: all .18s;
    text-decoration: none;

    box-shadow: 0 3px 12px rgba(27,46,94,.3);
    letter-spacing: .01em;
    margin-bottom: .75rem;
}
.ov-action-primary:hover {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
    color: #fff;
    text-decoration: none;
    transform: translateY(-1px);
    box-shadow: 0 5px 18px rgba(27,46,94,.4);
}
.ov-action-primary .btn-icon { margin-left: .4rem; }

.ov-action-cancel {
    display: block;
    width: 100%;
    padding: .7rem 1rem;
    border-radius: var(--radius-sm);
    background: transparent;
    color: var(--red);
    font-size: .88rem;
    font-weight: 600;
    text-align: center;
    border: 1.5px solid rgba(214,58,58,.25);
    cursor: pointer;
    transition: all .18s;

    margin-top: .5rem;
}
.ov-action-cancel:hover {
    background: var(--red-pale);
    border-color: var(--red);
}

.ov-assign-btn {
    display: block;
    width: 100%;
    padding: .72rem 1rem;
    border-radius: var(--radius-sm);
    background: var(--gold-pale);
    color: #7a5800;
    font-size: .88rem;
    font-weight: 700;
    text-align: center;
    border: 1.5px solid rgba(201,162,39,.3);
    cursor: pointer;
    transition: all .18s;

    margin-top: .75rem;
}
.ov-assign-btn:hover {
    background: var(--gold);
    color: #fff;
    border-color: var(--gold);
    text-decoration: none;
}

.ov-action-divider {
    height: 1px;
    background: var(--navy-border);
    margin: 1rem 0;
}

/* ─── Order Items Table ────────────────────────────────────── */
.ov-items-table { width: 100%; border-collapse: collapse; }
.ov-items-table thead th {
    padding: .6rem .75rem;
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: var(--text-muted);
    border-bottom: 1.5px solid var(--navy-border);
    text-align: right;
    white-space: nowrap;
}
.ov-items-table thead th:last-child { text-align: left; }
.ov-items-table tbody td {
    padding: .85rem .75rem;
    border-bottom: 1px solid rgba(27,46,94,.05);
    vertical-align: middle;
    font-size: .88rem;
    color: var(--text-primary);
}
.ov-items-table tbody tr:last-child td { border-bottom: none; }
.ov-item-img {
    width: 44px;
    height: 44px;
    border-radius: 8px;
    object-fit: cover;
    border: 1px solid var(--navy-border);
    flex-shrink: 0;
}
.ov-item-info { display: flex; align-items: center; gap: .65rem; }
.ov-item-name { font-weight: 700; font-size: .88rem; color: var(--navy); }
.ov-item-sub  { font-size: .76rem; color: var(--text-muted); margin-top: .1rem; }
.ov-item-price { font-weight: 700; color: var(--text-primary); white-space: nowrap; text-align: left; }
.ov-badge-qty {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 22px;
    height: 22px;
    border-radius: 6px;
    background: var(--navy-ghost);
    color: var(--navy);
    font-size: .75rem;
    font-weight: 700;
    padding: 0 .35rem;
}
.ov-addon-tag {
    display: inline-block;
    font-size: .72rem;
    background: var(--gold-pale);
    color: #7a5800;
    padding: .15rem .45rem;
    border-radius: 4px;
    margin: .1rem .1rem 0 0;
    font-weight: 600;
}

/* ─── Totals ───────────────────────────────────────────────── */
.ov-totals { padding: .75rem 1.25rem 1rem; }
.ov-total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: .35rem 0;
    font-size: .84rem;
}
.ov-total-row .label { color: var(--text-secondary); }
.ov-total-row .val   { font-weight: 600; color: var(--text-primary); }
.ov-total-row.discount .val { color: var(--green); }
.ov-total-row.sep { border-top: 1px solid var(--navy-border); margin-top: .35rem; padding-top: .6rem; }
.ov-grand-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: .9rem 1.25rem;
    background: var(--navy);
    border-radius: 0 0 calc(var(--radius) - 2px) calc(var(--radius) - 2px);
    margin: 0 -1px -1px;
}
.ov-grand-total .label { color: rgba(255,255,255,.7); font-size: .82rem; font-weight: 600; }
.ov-grand-total .val   { color: #fff; font-size: 1.2rem; font-weight: 800; }

/* ─── Details Column ───────────────────────────────────────── */
.ov-info-row {
    display: flex;
    align-items: flex-start;
    gap: .75rem;
    padding: .65rem 0;
    border-bottom: 1px solid rgba(27,46,94,.05);
}
.ov-info-row:last-child { border-bottom: none; }
.ov-info-icon {
    width: 30px;
    height: 30px;
    border-radius: 7px;
    background: var(--navy-ghost);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--navy);
    font-size: .8rem;
    flex-shrink: 0;
    margin-top: .05rem;
}
.ov-info-label { font-size: .72rem; color: var(--text-muted); font-weight: 600; margin-bottom: .1rem; }
.ov-info-value { font-size: .86rem; color: var(--text-primary); font-weight: 600; line-height: 1.4; }
.ov-info-value a { color: var(--navy); text-decoration: none; }
.ov-info-value a:hover { color: var(--gold); }

/* ─── Avatar ───────────────────────────────────────────────── */
.ov-avatar-row {
    display: flex;
    align-items: center;
    gap: .85rem;
    padding: .75rem 0;
}
.ov-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--navy-border);
    flex-shrink: 0;
}
.ov-avatar-name { font-weight: 700; font-size: .9rem; color: var(--navy); }
.ov-avatar-sub  { font-size: .76rem; color: var(--text-muted); margin-top: .1rem; }
.ov-avatar-badge {
    margin-right: auto;
    font-size: .72rem;
    font-weight: 700;
    background: var(--navy-ghost);
    color: var(--navy);
    padding: .2rem .55rem;
    border-radius: 999px;
}

/* ─── Cancel info ──────────────────────────────────────────── */
.ov-cancel-info {
    padding: .75rem;
    background: var(--red-pale);
    border-radius: var(--radius-sm);
    border: 1px solid rgba(214,58,58,.15);
    margin-top: .5rem;
}
.ov-cancel-info-row {
    display: flex;
    justify-content: space-between;
    font-size: .82rem;
    padding: .2rem 0;
    gap: .5rem;
}
.ov-cancel-info-row .k { color: var(--text-secondary); }
.ov-cancel-info-row .v { color: var(--red); font-weight: 600; text-align: left; }

/* ─── Change hint ──────────────────────────────────────────── */
.ov-change-alert {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: .65rem .9rem;
    background: var(--gold-pale);
    border: 1.5px solid rgba(201,162,39,.3);
    border-radius: var(--radius-sm);
    font-size: .82rem;
    color: #7a5800;
    font-weight: 600;
    margin-top: .75rem;
}
.ov-change-alert i { color: var(--gold); font-size: 1rem; }

/* ─── Proof images ─────────────────────────────────────────── */
.ov-proof-grid {
    display: flex;
    flex-wrap: wrap;
    gap: .5rem;
    margin-top: .5rem;
}
.ov-proof-thumb {
    width: 58px;
    height: 58px;
    border-radius: 8px;
    object-fit: cover;
    border: 1.5px solid var(--navy-border);
    cursor: pointer;
    transition: transform .15s;
}
.ov-proof-thumb:hover { transform: scale(1.05); }

/* ─── Map btn ──────────────────────────────────────────────── */
.ov-map-btn {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    font-size: .78rem;
    font-weight: 600;
    color: var(--navy);
    background: var(--navy-ghost);
    border: 1px solid var(--navy-border);
    padding: .3rem .7rem;
    border-radius: 6px;
    text-decoration: none;
    transition: all .15s;
    cursor: pointer;
}
.ov-map-btn:hover { background: var(--navy); color: #fff; text-decoration: none; }

/* ─── Notes strip ──────────────────────────────────────────── */
.ov-note-strip {
    display: flex;
    align-items: flex-start;
    gap: .5rem;
    padding: .6rem .85rem;
    background: rgba(201,162,39,.07);
    border-right: 3px solid var(--gold);
    border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
    font-size: .82rem;
    color: #5c440a;
    margin-bottom: .75rem;
}

/* ─── Change btn ───────────────────────────────────────────── */
.ov-change-link {
    font-size: .76rem;
    color: var(--gold);
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
    border: none;
    background: none;

}
.ov-change-link:hover { color: var(--navy); text-decoration: underline; }

/* ─── Subscription badge ───────────────────────────────────── */
.ov-sub-badge {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    font-size: .76rem;
    font-weight: 700;
    padding: .3rem .75rem;
    border-radius: 999px;
    background: rgba(59,130,246,.09);
    color: #1d4ed8;
    margin-bottom: .5rem;
}

/* ─── Print ────────────────────────────────────────────────── */
@media print {
    .ov-col-actions { display: none !important; }
    .ov-col-details { display: none !important; }
    .ov-topbar { display: none !important; }
    .ov-status-hero { display: none !important; }
}
</style>
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
?>

<div class="content container-fluid ov-page" id="printableArea">

    {{-- ══════════════════════════════════════════════════════════
         TOP BAR
    ══════════════════════════════════════════════════════════ --}}
    <div class="ov-topbar">
        <div class="ov-topbar-right">
            <div>
                <div class="ov-order-id">طلب #{{ $order['id'] }}</div>
                <div class="ov-order-date">
                    {{ date('d M Y ' . config('timeformat'), strtotime($order['created_at'])) }}
                </div>
            </div>
            @if ($order->edited)
                <span class="badge badge-soft-danger text-capitalize px-2">معدّل</span>
            @endif
            @if ($subscription)
                <span class="ov-sub-badge"><i class="tio-refresh"></i> اشتراك</span>
            @endif
            @if ($campaign_order)
                <span class="ov-sub-badge" style="background:rgba(201,162,39,.1);color:#7a5800"><i class="tio-star"></i> عرض</span>
            @endif
        </div>
        <div style="display:flex;align-items:center;gap:.5rem">
            <a class="ov-nav-btn" href="{{ route('vendor.order.details', [$order['id'] - 1]) }}" title="الطلب السابق"><i class="tio-chevron-right"></i></a>
            <a class="ov-nav-btn" href="{{ route('vendor.order.details', [$order['id'] + 1]) }}" title="الطلب التالي"><i class="tio-chevron-left"></i></a>
            <a class="ov-print-btn d-none d-sm-inline-flex" href="{{ route('vendor.order.generate-invoice', [$order['id']]) }}">
                <i class="tio-print"></i> فاتورة طباعة
            </a>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         STATUS HERO
    ══════════════════════════════════════════════════════════ --}}
    <div class="ov-status-hero">
        <span class="ov-status-badge status-{{ $status }} status-default">
            <span class="dot"></span>
            {{ $statusLabel }}
        </span>

        <div class="ov-status-meta">
            <div class="ov-meta-chip">
                <span class="ov-meta-label">طريقة الدفع</span>
                <span class="ov-meta-value">{{ translate(str_replace('_',' ',$order['payment_method'])) }}</span>
            </div>
            <div class="ov-meta-chip">
                <span class="ov-meta-label">حالة الدفع</span>
                @if ($order['payment_status'] == 'paid')
                    <span class="ov-meta-value paid">مدفوع ✓</span>
                @elseif ($order['payment_status'] == 'partially_paid')
                    @if ($order->payments()->where('payment_status','unpaid')->exists())
                        <span class="ov-meta-value" style="color:var(--amber)">مدفوع جزئياً</span>
                    @else
                        <span class="ov-meta-value paid">مدفوع ✓</span>
                    @endif
                @else
                    <span class="ov-meta-value unpaid">غير مدفوع</span>
                @endif
            </div>
            <div class="ov-meta-chip">
                <span class="ov-meta-label">نوع الطلب</span>
                <span class="ov-meta-value">{{ translate(str_replace('_',' ',$order['order_type'])) }}</span>
            </div>
            <div class="ov-meta-chip">
                <span class="ov-meta-label">الإجمالي</span>
                <span class="ov-meta-value">{{ \App\CentralLogics\Helpers::format_currency($order['order_amount']) }}</span>
            </div>
            @if ($order->schedule_at && ($order->scheduled || $subscription))
            <div class="ov-meta-chip">
                <span class="ov-meta-label">مجدول</span>
                <span class="ov-meta-value">{{ date('d M Y ' . config('timeformat'), strtotime($order['schedule_at'])) }}</span>
            </div>
            @endif
        </div>

        @if ($c_address)
            <button class="ov-map-btn" data-toggle="modal" data-target="#locationModal">
                <i class="tio-poi-outlined"></i> عرض الموقع
            </button>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════
         NOTES / INSTRUCTIONS
    ══════════════════════════════════════════════════════════ --}}
    @if($order['order_note'])
    <div class="ov-note-strip mb-2"><i class="tio-comment-outlined" style="flex-shrink:0;margin-top:.05rem"></i> <strong>ملاحظة الطلب:</strong>&nbsp;{{ $order['order_note'] }}</div>
    @endif
    @if ($order['delivery_instruction'])
    <div class="ov-note-strip mb-2"><i class="tio-info-outined" style="flex-shrink:0;margin-top:.05rem"></i> <strong>تعليمات التوصيل:</strong>&nbsp;{{ translate($order->delivery_instruction) }}</div>
    @endif
    @if ($order['unavailable_item_note'])
    <div class="ov-note-strip mb-2"><i class="tio-warning-outlined" style="flex-shrink:0;margin-top:.05rem"></i> <strong>إذا لم يتوفر الصنف:</strong>&nbsp;{{ translate($order->unavailable_item_note) }}</div>
    @endif

    {{-- ══════════════════════════════════════════════════════════
         MAIN GRID
    ══════════════════════════════════════════════════════════ --}}
    <div class="ov-grid">

        {{-- ── COL 1: ACTIONS ────────────────────────────────────── --}}
        <div class="ov-col-actions">

            @if ($order['order_status'] != 'delivered' && $order['order_status'] != 'canceled' && $order['order_status'] != 'failed')
            <div class="ov-card mb-3">
                <div class="ov-card-header">
                    <span class="ov-card-title">
                        <span class="ov-card-title-icon"><i class="tio-settings"></i></span>
                        إجراءات الطلب
                    </span>
                </div>
                <div class="ov-card-body">
                    @if ($order['order_status'] == 'pending')
                        <a class="ov-action-primary order-status-change-alert"
                            data-url="{{ route('vendor.order.status', ['id' => $order['id'], 'order_status' => 'confirmed']) }}"
                            data-message="{{ translate('Change status to confirmed ?') }}"
                            href="javascript:">
                            <i class="tio-checkmark-circle btn-icon"></i> تأكيد الأمر
                        </a>
                        @if (config('canceled_by_restaurant'))
                        <a class="ov-action-cancel cancelled-status" href="javascript:">
                            <i class="tio-clear-circle"></i> إلغاء الطلب
                        </a>
                        @endif

                    @elseif (in_array($order['order_status'], ['confirmed','accepted']))
                        <a class="ov-action-primary order-status-change-alert"
                            data-url="{{ route('vendor.order.status', ['id' => $order['id'], 'order_status' => 'processing']) }}"
                            data-message="{{ translate('Change status to cooking ?') }}"
                            data-verification="false"
                            data-processing-time="{{ $max_processing_time }}"
                            href="javascript:">
                            <i class="tio-restaurant btn-icon"></i> البدء في الطهي
                        </a>

                    @elseif ($order['order_status'] == 'processing')
                        <a class="ov-action-primary order-status-change-alert"
                            data-url="{{ route('vendor.order.status', ['id' => $order['id'], 'order_status' => 'handover']) }}"
                            data-message="{{ translate('Change status to ready for handover ?') }}"
                            href="javascript:">
                            <i class="tio-checkmark btn-icon"></i> جاهز للتسليم
                        </a>

                    @elseif ($order['order_status'] == 'handover' && (in_array($order['order_type'],['dine_in','take_away']) || (($restaurant->restaurant_model == 'commission' && $restaurant->self_delivery_system) || ($restaurant->restaurant_model == 'subscription' && $restaurant?->restaurant_sub?->self_delivery == 1))))
                        <a class="ov-action-primary order-status-change-alert"
                            data-url="{{ route('vendor.order.status', ['id' => $order['id'], 'order_status' => 'delivered']) }}"
                            data-message="{{ translate('Change status to delivered (payment status will be paid if not) ?') }}"
                            data-verification="{{ $order_delivery_verification ? 'true' : 'false' }}"
                            href="javascript:">
                            <i class="tio-checkmark-circle btn-icon"></i>
                            {{ $order->order_type == 'dine_in' ? 'تأكيد الاكتمال' : 'تأكيد التوصيل' }}
                        </a>
                    @endif

                    {{-- Assign delivery man --}}
                    @if (!in_array($order['order_type'],['dine_in','take_away']))
                        @if (!$order->delivery_man && !in_array($order['order_status'], ['handover','delivered','take_away','refund_requested','canceled','refunded','refund_request_canceled']) && (isset($order->restaurant) && ($order->restaurant->restaurant_model == 'commission' && $order->restaurant->self_delivery_system) || ($order->restaurant->restaurant_model == 'subscription' && isset($order->restaurant->restaurant_sub) && $order->restaurant->restaurant_sub->self_delivery == 1)))
                        <a class="ov-assign-btn" href="javascript:" data-toggle="modal" data-target="#myModal">
                            <i class="tio-bike"></i> تعيين مندوب توصيل
                        </a>
                        @endif
                    @endif
                </div>
            </div>
            @endif

            {{-- Canceled info --}}
            @if ($order->order_status == 'canceled')
            <div class="ov-card mb-3">
                <div class="ov-card-header">
                    <span class="ov-card-title" style="color:var(--red)">
                        <span class="ov-card-title-icon" style="background:var(--red-pale);color:var(--red)"><i class="tio-clear"></i></span>
                        تفاصيل الإلغاء
                    </span>
                </div>
                <div class="ov-card-body">
                    <div class="ov-cancel-info">
                        @if($order->cancellation_reason)
                        <div class="ov-cancel-info-row"><span class="k">السبب</span><span class="v">{{ $order->cancellation_reason }}</span></div>
                        @endif
                        @if($order->cancellation_note)
                        <div class="ov-cancel-info-row"><span class="k">ملاحظة</span><span class="v">{{ $order->cancellation_note }}</span></div>
                        @endif
                        <div class="ov-cancel-info-row"><span class="k">بواسطة</span><span class="v">{{ translate($order->canceled_by) }}</span></div>
                    </div>
                    @if ($order->payment_status == 'paid' || $order->payment_status == 'partially_paid')
                        @if ($order?->payments)
                            @php($pay_infos = $order->payments()->where('payment_status','paid')->get())
                            @foreach ($pay_infos as $pay_info)
                            <div class="ov-info-row mt-1">
                                <span class="ov-info-label">المبلغ المدفوع ({{ translate($pay_info->payment_method) }})</span>
                                <span class="ov-info-value">{{ \App\CentralLogics\Helpers::format_currency($pay_info->amount) }}</span>
                            </div>
                            @endforeach
                        @endif
                        <div class="ov-info-row">
                            <div>
                                <div class="ov-info-label">المبلغ المُعاد للمحفظة</div>
                                @if ($order?->payments)
                                    @php($amount = $order->payments()->where('payment_status','paid')->sum('amount'))
                                    <div class="ov-info-value paid">{{ \App\CentralLogics\Helpers::format_currency($amount) }}</div>
                                @else
                                    <div class="ov-info-value paid">{{ \App\CentralLogics\Helpers::format_currency($order->order_amount) }}</div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Dine-in table --}}
            @if ($order->order_type == 'dine_in')
            <div class="ov-card mb-3">
                <div class="ov-card-header">
                    <span class="ov-card-title"><span class="ov-card-title-icon"><i class="tio-restaurant"></i></span> بيانات الطاولة</span>
                </div>
                <div class="ov-card-body">
                    <form action="{{ route('vendor.order.add_dine_in_table_number', [$order['id']]) }}" method="post">
                        @method('PUT') @csrf
                        <div class="form-group mb-3">
                            <label class="ov-info-label d-block mb-1">{{ translate('Table_Number') }}</label>
                            <input type="text" @readonly(in_array($order['order_status'],['failed','delivered','refund_requested','canceled','refunded','refund_request_canceled'])) maxlength="20" value="{{ $order?->OrderReference?->table_number }}" name="table_number" class="form-control form-control-sm" placeholder="مثال: 10">
                        </div>
                        <div class="form-group mb-3">
                            <label class="ov-info-label d-block mb-1">{{ translate('Token_Number') }}</label>
                            <input type="text" @readonly(in_array($order['order_status'],['failed','delivered','refund_requested','canceled','refunded','refund_request_canceled'])) maxlength="20" value="{{ $order?->OrderReference?->token_number }}" name="token_number" class="form-control form-control-sm" placeholder="مثال: 32">
                        </div>
                        @if (!in_array($order['order_status'],['failed','delivered','refund_requested','canceled','refunded','refund_request_canceled']))
                        <button type="submit" class="ov-action-primary" style="margin-bottom:0">{{ translate('messages.Save') }}</button>
                        @endif
                    </form>
                </div>
            </div>
            @endif

            {{-- Cutlery --}}
            <div class="ov-card mb-3">
                <div class="ov-card-body" style="padding:.75rem 1.25rem">
                    <div class="ov-info-row" style="padding:.3rem 0;border:none">
                        <span class="ov-info-icon"><i class="tio-cutlery"></i></span>
                        <div>
                            <div class="ov-info-label">أدوات المائدة</div>
                            <div class="ov-info-value" style="{{ $order->cutlery ? 'color:var(--green)' : 'color:var(--text-muted)' }}">
                                {{ $order->cutlery ? 'مطلوبة' : 'غير مطلوبة' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- /col-actions --}}

        {{-- ── COL 2: ORDER ITEMS ─────────────────────────────────── --}}
        <div class="ov-col-items">
            <div class="ov-card">
                <div class="ov-card-header">
                    <span class="ov-card-title"><span class="ov-card-title-icon"><i class="tio-restaurant"></i></span> تفاصيل الأصناف</span>
                    <a class="ov-print-btn d-sm-none" href="{{ route('vendor.order.generate-invoice', [$order['id']]) }}"><i class="tio-print"></i></a>
                </div>
                <div style="overflow-x:auto">
                    <table class="ov-items-table">
                        <thead>
                            <tr>
                                <th>الصنف</th>
                                <th>الإضافات</th>
                                <th>الكمية</th>
                                <th>السعر</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($order->details as $key => $detail)
                            @if (isset($detail->food_id))
                                @php($detail->food = json_decode($detail->food_details, true))
                                @php($food = \App\Models\Food::where(['id' => $detail->food['id']])->first())
                                <tr>
                                    <td>
                                        <div class="ov-item-info">
                                            <a href="{{ route('vendor.food.view', $detail->food['id']) }}">
                                                <img class="ov-item-img onerror-image"
                                                     src="{{ $food['image_full_url'] ?? dynamicAsset('public/assets/admin/img/100x100/food-default-image.png') }}"
                                                     data-onerror-image="{{ dynamicAsset('public/assets/admin/img/160x160/img2.jpg') }}"
                                                     alt="">
                                            </a>
                                            <div>
                                                <div class="ov-item-name">{{ Str::limit($detail->food['name'], 28, '…') }}</div>
                                                @if (count(json_decode($detail['variation'], true)) > 0)
                                                @foreach(json_decode($detail['variation'],true) as $variation)
                                                    @if (isset($variation['name']) && isset($variation['values']))
                                                    <div class="ov-item-sub">{{ $variation['name'] }}:
                                                        @foreach ($variation['values'] as $value){{ $value['label'] }} ({{ \App\CentralLogics\Helpers::format_currency($value['optionPrice']) }}) @endforeach
                                                    </div>
                                                    @break
                                                    @endif
                                                @endforeach
                                                @endif
                                                <div class="ov-item-sub">{{ \App\CentralLogics\Helpers::format_currency($detail['price']) }} / وحدة</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @foreach (json_decode($detail['add_ons'], true) as $addon)
                                            <span class="ov-addon-tag">{{ Str::limit($addon['name'],20,'…') }} ×{{ $addon['quantity'] }}</span>
                                        @endforeach
                                    </td>
                                    <td><span class="ov-badge-qty">{{ $detail['quantity'] }}</span></td>
                                    <td class="ov-item-price">{{ \App\CentralLogics\Helpers::format_currency($detail['price'] * $detail['quantity']) }}</td>
                                </tr>
                            @elseif(isset($detail->item_campaign_id))
                                @php($detail->campaign = json_decode($detail->food_details, true))
                                @php($campaign = \App\Models\ItemCampaign::where(['id' => $detail->campaign['id']])->first())
                                <tr>
                                    <td>
                                        <div class="ov-item-info">
                                            <img class="ov-item-img onerror-image"
                                                 src="{{ $campaign['image_full_url'] ?? dynamicAsset('public/assets/admin/img/100x100/food-default-image.png') }}"
                                                 data-onerror-image="{{ dynamicAsset('public/assets/admin/img/160x160/img2.jpg') }}"
                                                 alt="">
                                            <div>
                                                <div class="ov-item-name">{{ Str::limit($detail->campaign['name'], 28, '…') }}</div>
                                                <div class="ov-item-sub">{{ \App\CentralLogics\Helpers::format_currency($detail['price']) }} / وحدة</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @foreach (json_decode($detail['add_ons'], true) as $addon)
                                            <span class="ov-addon-tag">{{ Str::limit($addon['name'],20,'…') }} ×{{ $addon['quantity'] }}</span>
                                        @endforeach
                                    </td>
                                    <td><span class="ov-badge-qty">{{ $detail['quantity'] }}</span></td>
                                    <td class="ov-item-price">{{ \App\CentralLogics\Helpers::format_currency($detail['price'] * $detail['quantity']) }}</td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Totals breakdown --}}
                <div class="ov-totals">
                    <div class="ov-total-row">
                        <span class="label">سعر الأصناف</span>
                        <span class="val">{{ \App\CentralLogics\Helpers::format_currency($product_price) }}</span>
                    </div>
                    @if ($total_addon_price > 0)
                    <div class="ov-total-row">
                        <span class="label">الإضافات</span>
                        <span class="val">{{ \App\CentralLogics\Helpers::format_currency($total_addon_price) }}</span>
                    </div>
                    @endif
                    @if ($restaurant_discount_amount > 0)
                    <div class="ov-total-row discount">
                        <span class="label">خصم المطعم</span>
                        <span class="val">- {{ \App\CentralLogics\Helpers::format_currency($restaurant_discount_amount) }}</span>
                    </div>
                    @endif
                    @if ($coupon_discount_amount > 0)
                    <div class="ov-total-row discount">
                        <span class="label">خصم القسيمة</span>
                        <span class="val">- {{ \App\CentralLogics\Helpers::format_currency($coupon_discount_amount) }}</span>
                    </div>
                    @endif
                    @if ($order['ref_bonus_amount'] > 0)
                    <div class="ov-total-row discount">
                        <span class="label">خصم الإحالة</span>
                        <span class="val">- {{ \App\CentralLogics\Helpers::format_currency($order['ref_bonus_amount']) }}</span>
                    </div>
                    @endif
                    @if ($order->tax_status == 'excluded' || $order->tax_status == null)
                    <div class="ov-total-row">
                        <span class="label">ضريبة القيمة المضافة</span>
                        <span class="val">+ {{ \App\CentralLogics\Helpers::format_currency($total_tax_amount) }}</span>
                    </div>
                    @endif
                    @if ($order['dm_tips'] > 0)
                    <div class="ov-total-row">
                        <span class="label">نصائح المندوب</span>
                        <span class="val">+ {{ \App\CentralLogics\Helpers::format_currency($order['dm_tips']) }}</span>
                    </div>
                    @endif
                    <div class="ov-total-row">
                        <span class="label">رسوم التوصيل</span>
                        <span class="val">+ {{ \App\CentralLogics\Helpers::format_currency($order['delivery_charge']) }}</span>
                    </div>
                    @if ($additional_charge_status)
                    <div class="ov-total-row">
                        <span class="label">{{ \App\CentralLogics\Helpers::get_business_data('additional_charge_name') ?? translate('messages.additional_charge') }}</span>
                        <span class="val">+ {{ \App\CentralLogics\Helpers::format_currency($order['additional_charge']) }}</span>
                    </div>
                    @endif
                    @if ($order['extra_packaging_amount'] > 0)
                    <div class="ov-total-row">
                        <span class="label">تغليف إضافي</span>
                        <span class="val">+ {{ \App\CentralLogics\Helpers::format_currency($order['extra_packaging_amount']) }}</span>
                    </div>
                    @endif

                    {{-- Payment lines --}}
                    @if ($order?->payments)
                    <div class="ov-total-row sep" style="border-top:1.5px solid var(--navy-border)"></div>
                    @foreach ($order->payments as $payment)
                    <div class="ov-total-row">
                        <span class="label">
                            @if ($payment->payment_status == 'paid')
                                {{ $payment->payment_method == 'cash_on_delivery' ? 'دفع نقدي عند التوصيل' : 'دفع عبر ' . translate($payment->payment_method) }}
                            @else
                                مبلغ مستحق ({{ $payment->payment_method == 'cash_on_delivery' ? 'COD' : translate($payment->payment_method) }})
                            @endif
                        </span>
                        <span class="val">{{ \App\CentralLogics\Helpers::format_currency($payment->amount) }}</span>
                    </div>
                    @endforeach
                    @endif
                </div>

                @if ($order->bring_change_amount > 0)
                <div class="ov-change-alert" style="margin:0 1.25rem 1rem">
                    <i class="tio-money"></i>
                    {{ translate('Please instruct the delivery man to collect ' . \App\CentralLogics\Helpers::format_currency($order->bring_change_amount) . ' in change upon delivery') }}
                </div>
                @endif

                <div class="ov-grand-total">
                    <span class="label">الإجمالي</span>
                    <span class="val">{{ \App\CentralLogics\Helpers::format_currency($order['order_amount']) }}</span>
                </div>
            </div>
        </div>{{-- /col-items --}}

        {{-- ── COL 3: CUSTOMER & DELIVERY ────────────────────────── --}}
        <div class="ov-col-details">

            {{-- Customer --}}
            <div class="ov-card mb-3">
                <div class="ov-card-header">
                    <span class="ov-card-title"><span class="ov-card-title-icon"><i class="tio-user"></i></span> معلومات العميل</span>
                </div>
                <div class="ov-card-body">
                    @if ($order->customer && $order->is_guest == 0)
                    <div class="ov-avatar-row">
                        <img class="ov-avatar onerror-image"
                             src="{{ $order->customer?->image_full_url ?? dynamicAsset('public/assets/admin/img/160x160/img1.jpg') }}"
                             data-onerror-image="{{ dynamicAsset('public/assets/admin/img/160x160/img1.jpg') }}" alt="">
                        <div style="min-width:0">
                            <div class="ov-avatar-name">{{ $order->customer['f_name'] . ' ' . $order->customer['l_name'] }}</div>
                            <div class="ov-avatar-sub">{{ $order->customer->orders_count }} طلبات</div>
                        </div>
                        <span class="ov-avatar-badge">{{ $order->customer->orders_count }}</span>
                    </div>
                    <div class="ov-info-row">
                        <span class="ov-info-icon"><i class="tio-call"></i></span>
                        <div>
                            <div class="ov-info-label">الهاتف</div>
                            <div class="ov-info-value"><a href="tel:{{ $order->customer['phone'] }}">{{ $order->customer['phone'] }}</a></div>
                        </div>
                    </div>
                    @if ($order->customer['email'])
                    <div class="ov-info-row">
                        <span class="ov-info-icon"><i class="tio-email"></i></span>
                        <div>
                            <div class="ov-info-label">البريد</div>
                            <div class="ov-info-value">{{ $order->customer['email'] }}</div>
                        </div>
                    </div>
                    @endif
                    @elseif($order->is_guest)
                    <span class="badge badge-soft-success py-2 d-block text-center">زائر</span>
                    @else
                    <p class="text-muted text-center mb-0" style="font-size:.84rem">لم يُعثر على العميل</p>
                    @endif
                </div>
            </div>

            {{-- Delivery address --}}
            @if ($order->delivery_address)
            @php($address = json_decode($order->delivery_address, true))
            <div class="ov-card mb-3">
                <div class="ov-card-header">
                    <span class="ov-card-title">
                        <span class="ov-card-title-icon"><i class="tio-poi-outlined"></i></span>
                        {{ $order->order_type == 'dine_in' ? 'بيانات الطلب' : 'عنوان التوصيل' }}
                    </span>
                </div>
                <div class="ov-card-body">
                    @if (isset($address))
                    <div class="ov-info-row">
                        <span class="ov-info-icon"><i class="tio-user"></i></span>
                        <div>
                            <div class="ov-info-label">المستلم</div>
                            <div class="ov-info-value">{{ $address['contact_person_name'] ?? '' }}</div>
                        </div>
                    </div>
                    <div class="ov-info-row">
                        <span class="ov-info-icon"><i class="tio-call"></i></span>
                        <div>
                            <div class="ov-info-label">الاتصال</div>
                            <div class="ov-info-value"><a href="tel:{{ $address['contact_person_number'] ?? '' }}">{{ $address['contact_person_number'] ?? '' }}</a></div>
                        </div>
                    </div>
                    @if ($order->order_type != 'dine_in')
                        @if (isset($address['address']) && $address['address'])
                        <div class="ov-info-row">
                            <span class="ov-info-icon"><i class="tio-poi"></i></span>
                            <div>
                                <div class="ov-info-label">العنوان</div>
                                <div class="ov-info-value">{{ $address['address'] }}</div>
                            </div>
                        </div>
                        @endif
                        @if (isset($address['road']) && $address['road'])
                        <div class="ov-info-row">
                            <span class="ov-info-icon"><i class="tio-road"></i></span>
                            <div>
                                <div class="ov-info-label">الشارع</div>
                                <div class="ov-info-value">{{ $address['road'] }}</div>
                            </div>
                        </div>
                        @endif
                        @if (isset($address['house']) && $address['house'])
                        <div class="ov-info-row">
                            <span class="ov-info-icon"><i class="tio-home"></i></span>
                            <div>
                                <div class="ov-info-label">المبنى / الشقة</div>
                                <div class="ov-info-value">{{ $address['house'] }}</div>
                            </div>
                        </div>
                        @endif
                        @if (isset($address['floor']) && $address['floor'])
                        <div class="ov-info-row">
                            <span class="ov-info-icon"><i class="tio-layers"></i></span>
                            <div>
                                <div class="ov-info-label">الطابق</div>
                                <div class="ov-info-value">{{ $address['floor'] }}</div>
                            </div>
                        </div>
                        @endif
                    @endif
                    @endif
                </div>
            </div>
            @endif

            {{-- Delivery man --}}
            @if (!in_array($order['order_type'],['dine_in','take_away']))
            <div class="ov-card mb-3">
                <div class="ov-card-header">
                    <span class="ov-card-title">
                        <span class="ov-card-title-icon"><i class="tio-bike"></i></span>
                        مندوب التوصيل
                    </span>
                    @if ($order->delivery_man && !in_array($order['order_status'], ['handover','delivered','refund_requested','canceled','refunded','refund_request_canceled']) && (isset($order->restaurant) && (($order->restaurant->restaurant_model == 'commission' && $order->restaurant->self_delivery_system) || ($order->restaurant->restaurant_model == 'subscription' && isset($order->restaurant->restaurant_sub) && $order->restaurant->restaurant_sub->self_delivery == 1))))
                    <button class="ov-change-link" data-toggle="modal" data-target="#myModal">تغيير</button>
                    @endif
                </div>
                <div class="ov-card-body">
                    @if ($order->delivery_man)
                    <div class="ov-avatar-row">
                        <img class="ov-avatar onerror-image"
                             src="{{ $order->delivery_man?->image_full_url ?? dynamicAsset('public/assets/admin/img/160x160/img3.jpg') }}"
                             data-onerror-image="{{ dynamicAsset('public/assets/admin/img/160x160/img3.jpg') }}" alt="">
                        <div style="min-width:0">
                            <div class="ov-avatar-name">{{ $order->delivery_man['f_name'] . ' ' . $order->delivery_man['l_name'] }}</div>
                            <div class="ov-avatar-sub">{{ $order->delivery_man->orders_count }} طلبات</div>
                        </div>
                    </div>
                    <div class="ov-info-row">
                        <span class="ov-info-icon"><i class="tio-call"></i></span>
                        <div>
                            <div class="ov-info-label">الهاتف</div>
                            <div class="ov-info-value"><a href="tel:{{ $order->delivery_man['phone'] }}">{{ $order->delivery_man['phone'] }}</a></div>
                        </div>
                    </div>
                    @if (!in_array($order['order_type'],['dine_in','take_away']))
                        @php($dm_loc = $order->dm_last_location)
                        <div class="ov-info-row">
                            <span class="ov-info-icon"><i class="tio-poi-outlined"></i></span>
                            <div>
                                <div class="ov-info-label">آخر موقع</div>
                                <div class="ov-info-value">
                                    @if (isset($dm_loc))
                                        <a target="_blank" href="http://maps.google.com/maps?z=12&t=m&q=loc:{{ $dm_loc['latitude'] }}+{{ $dm_loc['longitude'] }}">{{ $dm_loc['location'] }}</a>
                                    @else
                                        <span style="color:var(--text-muted)">الموقع غير متاح</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                    @else
                    <p class="text-muted text-center mb-0" style="font-size:.84rem;padding:.5rem 0">لم يُعيَّن مندوب بعد</p>
                    @endif
                </div>
            </div>
            @endif

            {{-- Delivery proof --}}
            @if ($order->order_type != 'dine_in')
            <div class="ov-card mb-3">
                <div class="ov-card-header">
                    <span class="ov-card-title"><span class="ov-card-title-icon"><i class="tio-photo-camera"></i></span> إثبات التوصيل</span>
                    @if (($restaurant->restaurant_model == 'commission' && $restaurant->self_delivery_system) || ($restaurant->restaurant_model == 'subscription' && $restaurant?->restaurant_sub?->self_delivery == 1))
                    <button class="ov-change-link" data-toggle="modal" data-target=".order-proof-modal"><i class="tio-add"></i> إضافة</button>
                    @endif
                </div>
                <div class="ov-card-body">
                    @php($data = isset($order->order_proof) ? json_decode($order->order_proof, true) : 0)
                    @if ($data)
                    <div class="ov-proof-grid">
                        @foreach ($data as $key => $img)
                            @php($img = is_array($img) ? $img : ['img'=>$img,'storage'=>'public'])
                            <img class="ov-proof-thumb onerror-image"
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
                    <p class="text-muted text-center mb-0" style="font-size:.82rem;padding:.25rem 0">لا توجد صور بعد</p>
                    @endif
                </div>
            </div>
            @endif

            {{-- Subscription info --}}
            @if (isset($order->subscription))
            <div class="ov-card mb-3">
                <div class="ov-card-header">
                    <span class="ov-card-title"><span class="ov-card-title-icon"><i class="tio-refresh"></i></span> الاشتراك</span>
                </div>
                <div class="ov-card-body">
                    <div class="ov-info-row" style="border:none;padding:.25rem 0">
                        <span class="ov-info-label">الحالة</span>
                        <span class="badge badge-soft-{{ $order->subscription->status == 'active' ? 'success' : ($order->subscription->status == 'paused' ? 'warning' : 'danger') }}">
                            {{ translate('messages.' . $order->subscription->status) }}
                        </span>
                    </div>
                </div>
            </div>
            @endif

        </div>{{-- /col-details --}}

    </div>{{-- /ov-grid --}}

</div>{{-- /ov-page --}}


{{-- ════════════════════════════════════════════════════════════
     MODALS (unchanged logic, cleaned markup)
════════════════════════════════════════════════════════════ --}}

{{-- Assign Delivery Man --}}
<div class="modal fade" id="myModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header" style="direction:rtl">
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
                            <p class="mb-2 text-center text-muted {{ $order->delivery_man_id ? 'mt-2' : '' }}" style="font-size:.82rem">
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
            <div class="modal-header" style="direction:rtl">
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
            <div class="modal-header" style="direction:rtl">
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
</script>
@endpush
