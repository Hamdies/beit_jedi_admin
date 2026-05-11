@extends('layouts.vendor.app')

@section('title', translate('messages.dashboard'))

@push('css_or_js')
<style>
/* ═══════════════════════════════════════════════
   BEIT JEDI — RESTAURANT DASHBOARD (reference-matched)
   Cream surface · Navy revenue card · Gold accents
═══════════════════════════════════════════════ */
:root {
    --bj-bg:          #FAF6EE;
    --bj-surface:     #FFFFFF;
    --bj-navy:        #0E1A3D;
    --bj-navy-deep:   #0A1530;
    --bj-navy-soft:   #EEF1F8;
    --bj-gold:        #D4A017;
    --bj-gold-soft:   #FBF3DC;
    --bj-amber:       #E07B00;
    --bj-amber-soft:  #FFE8CF;
    --bj-amber-text:  #B26100;
    --bj-green:       #1A7F4B;
    --bj-green-soft:  #D9F0E0;
    --bj-green-text:  #186F40;
    --bj-blue-soft:   #DDE6F5;
    --bj-blue-text:   #2A4998;
    --bj-border:      #ECE4D2;
    --bj-border-2:    #E2E5ED;
    --bj-text:        #1A1F36;
    --bj-text-mid:    #4A5068;
    --bj-muted:       #8B91A8;
    --bj-radius-xl:   18px;
    --bj-radius-lg:   14px;
    --bj-radius-md:   10px;
    --bj-radius-sm:   8px;
    --bj-shadow:      0 1px 3px rgba(14,26,61,0.04), 0 4px 16px rgba(14,26,61,0.05);
    --bj-shadow-hover:0 6px 22px rgba(14,26,61,0.10);
}

.bj-dash, .bj-dash * { box-sizing: border-box; }
.bj-dash {
    background: var(--bj-bg);
    min-height: 100vh;
    padding: 1.5rem 0 4rem;
    direction: rtl;
    font-family: 'Cairo', -apple-system, BlinkMacSystemFont, sans-serif;
}
.bj-wrap {
    max-width: 1480px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

/* ── GREETING BAR ─────────────────────────────── */
.bj-greet {
    background: var(--bj-surface);
    border: 1px solid var(--bj-border);
    border-radius: var(--bj-radius-xl);
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1.25rem;
    box-shadow: var(--bj-shadow);
    flex-wrap: wrap;
}
.bj-greet-left { display: flex; align-items: center; gap: .75rem; flex-wrap: wrap; }

.bj-user-chip {
    display: flex; align-items: center; gap: .625rem;
    background: var(--bj-gold-soft);
    border: 1px solid #F2E0A8;
    border-radius: 999px;
    padding: .4rem .75rem .4rem .55rem;
}
.bj-user-chip-avatar {
    width: 30px; height: 30px;
    border-radius: 50%;
    background: var(--bj-navy);
    color: var(--bj-gold);
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: .82rem;
    overflow: hidden;
    flex-shrink: 0;
}
.bj-user-chip-avatar img { width:100%; height:100%; object-fit:cover; }
.bj-user-chip-text { line-height: 1.1; }
.bj-user-chip-name { font-size: .82rem; font-weight: 800; color: var(--bj-text); }
.bj-user-chip-role { font-size: .66rem; color: var(--bj-muted); font-weight: 600; margin-top: 1px; }

.bj-greet-msg {
    display: flex; align-items: center; gap: .5rem;
    font-size: .85rem;
}
.bj-greet-hello { color: var(--bj-muted); font-weight: 600; }
.bj-greet-headline { color: var(--bj-text); font-weight: 800; font-size: .95rem; }

.bj-greet-right { display: flex; align-items: center; gap: .5rem; flex-wrap: wrap; }

.bj-open-pill {
    display: inline-flex; align-items: center; gap: .4rem;
    background: var(--bj-green-soft);
    color: var(--bj-green-text);
    font-size: .76rem; font-weight: 800;
    padding: .42rem .85rem;
    border-radius: 999px;
    border: 1px solid #B9DDC2;
}
.bj-open-dot {
    width: 7px; height: 7px;
    background: var(--bj-green);
    border-radius: 50%;
    animation: bjBlink 2s ease-in-out infinite;
}
@keyframes bjBlink { 0%,100% { opacity:1; transform:scale(1); } 50% { opacity:.4; transform:scale(.7); } }

.bj-btn {
    display: inline-flex; align-items: center; gap: .375rem;
    background: var(--bj-navy);
    color: #fff;
    font-size: .8rem; font-weight: 800;
    padding: .55rem 1.1rem;
    border-radius: 999px;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: background .15s, transform .1s, box-shadow .15s;
    font-family: inherit;
    white-space: nowrap;
}
.bj-btn:hover { background: #1A2750; color: #fff; text-decoration: none; box-shadow: 0 4px 14px rgba(14,26,61,.2); }
.bj-btn:active { transform: scale(.97); }
.bj-btn-ghost {
    background: var(--bj-surface);
    color: var(--bj-navy);
    border: 1px solid var(--bj-border);
}
.bj-btn-ghost:hover { background: var(--bj-bg); color: var(--bj-navy); }
.bj-btn-icon {
    width: 38px; height: 38px;
    background: var(--bj-surface);
    border: 1px solid var(--bj-border);
    border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    color: var(--bj-navy);
    cursor: pointer;
    position: relative;
    text-decoration: none;
    transition: background .15s, box-shadow .15s;
}
.bj-btn-icon:hover { background: var(--bj-bg); box-shadow: var(--bj-shadow); }
.bj-btn-icon-badge {
    position: absolute; top: -2px; left: -2px;
    min-width: 16px; height: 16px;
    background: var(--bj-amber);
    color: #fff;
    font-size: .58rem; font-weight: 800;
    border-radius: 999px;
    padding: 0 4px;
    display: flex; align-items: center; justify-content: center;
    border: 2px solid var(--bj-surface);
}

/* ── STATUS TILES ─────────────────────────────── */
.bj-tiles {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 1.25rem;
}
.bj-tile {
    background: var(--bj-surface);
    border: 1px solid var(--bj-border);
    border-radius: var(--bj-radius-xl);
    padding: 1.25rem 1.3rem 1.1rem;
    text-decoration: none;
    display: block;
    box-shadow: var(--bj-shadow);
    transition: transform .18s, box-shadow .18s;
    position: relative;
}
.bj-tile:hover { transform: translateY(-2px); box-shadow: var(--bj-shadow-hover); text-decoration: none; }
.bj-tile-head {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: .85rem;
}
.bj-tile-label {
    font-size: .82rem; font-weight: 700; color: var(--bj-text-mid);
}
.bj-tile-ico {
    width: 36px; height: 36px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 17px;
    flex-shrink: 0;
}
.bj-tile--confirmed .bj-tile-ico { background: var(--bj-amber-soft); color: var(--bj-amber); }
.bj-tile--cooking   .bj-tile-ico { background: var(--bj-gold-soft);  color: var(--bj-gold); }
.bj-tile--ready     .bj-tile-ico { background: var(--bj-green-soft); color: var(--bj-green); }
.bj-tile--onway     .bj-tile-ico { background: var(--bj-blue-soft);  color: var(--bj-blue-text); }

.bj-tile-num {
    font-size: 2.9rem;
    font-weight: 900;
    color: var(--bj-navy);
    line-height: 1;
    direction: ltr;
    text-align: right;
    letter-spacing: -2px;
    margin-bottom: .5rem;
}
.bj-tile-foot {
    font-size: .72rem;
    color: var(--bj-muted);
    font-weight: 600;
    display: flex; justify-content: space-between; align-items: center;
}
.bj-tile-foot strong { color: var(--bj-green); font-weight: 800; }
.bj-tile-foot-num { font-weight: 800; color: var(--bj-text); direction: ltr; }

/* ── MAIN GRID ──────────────────────────────── */
.bj-grid {
    display: grid;
    grid-template-columns: 360px 1fr;
    gap: 1.125rem;
    align-items: start;
}

/* ── REVENUE CARD (dark navy) ─────────────────── */
.bj-revenue {
    background: linear-gradient(160deg, #131F4D 0%, var(--bj-navy-deep) 100%);
    border-radius: var(--bj-radius-xl);
    padding: 1.25rem 1.25rem 1rem;
    color: #fff;
    box-shadow: 0 8px 28px rgba(14,26,61,.18);
    position: relative;
    overflow: hidden;
}
.bj-revenue-head {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: .25rem;
}
.bj-revenue-title {
    display: flex; align-items: center; gap: .55rem;
    font-size: .9rem; font-weight: 700;
    color: rgba(255,255,255,.9);
}
.bj-revenue-title .bj-ico-chip {
    width: 30px; height: 30px;
    background: rgba(255,255,255,.08);
    border-radius: 9px;
    display: inline-flex; align-items: center; justify-content: center;
    color: var(--bj-gold); font-size: 14px;
}
.bj-revenue-period {
    background: rgba(255,255,255,.08);
    color: #fff;
    border: 1px solid rgba(255,255,255,.14);
    border-radius: 999px;
    padding: .3rem .7rem .3rem .9rem;
    font-size: .72rem; font-weight: 700;
    cursor: pointer;
    font-family: inherit;
    direction: rtl;
    appearance: none;
    background-image: linear-gradient(45deg, transparent 50%, rgba(255,255,255,.7) 50%),
                      linear-gradient(135deg, rgba(255,255,255,.7) 50%, transparent 50%);
    background-position: calc(100% - 18px) 50%, calc(100% - 13px) 50%;
    background-size: 5px 5px, 5px 5px;
    background-repeat: no-repeat;
}

.bj-revenue-amount {
    font-size: 2.4rem;
    font-weight: 900;
    color: #fff;
    line-height: 1.1;
    direction: ltr;
    text-align: right;
    margin: .25rem 0 .15rem;
    letter-spacing: -1.5px;
}
.bj-revenue-currency { font-size: 1rem; color: var(--bj-gold); font-weight: 700; margin-right: .25rem; }
.bj-revenue-delta {
    font-size: .72rem; color: var(--bj-gold);
    font-weight: 700; display: inline-flex; align-items: center; gap: .25rem;
}
.bj-revenue-chart {
    margin: .25rem -.5rem .5rem;
    height: 90px;
}
.bj-revenue-foot {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: .5rem;
    padding-top: .75rem;
    border-top: 1px solid rgba(255,255,255,.08);
}
.bj-rev-stat { text-align: center; }
.bj-rev-stat-label { font-size: .68rem; color: rgba(255,255,255,.55); font-weight: 600; margin-bottom: .2rem; }
.bj-rev-stat-val { font-size: 1rem; font-weight: 800; color: #fff; direction: ltr; }
.bj-rev-stat-val .bj-star { color: var(--bj-gold); margin-left: 2px; }

/* ── MESSAGES PANEL (under revenue) ───────────── */
.bj-messages {
    background: var(--bj-surface);
    border: 1px solid var(--bj-border);
    border-radius: var(--bj-radius-xl);
    box-shadow: var(--bj-shadow);
    margin-top: 1.125rem;
    overflow: hidden;
}
.bj-panel-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 1rem 1.25rem .75rem;
}
.bj-panel-title {
    display: flex; align-items: center; gap: .5rem;
    font-size: .95rem; font-weight: 800; color: var(--bj-text);
}
.bj-panel-title i { color: var(--bj-navy); font-size: 18px; }
.bj-panel-more {
    font-size: .76rem; font-weight: 700; color: var(--bj-navy);
    text-decoration: none;
}
.bj-panel-more:hover { color: var(--bj-gold); text-decoration: none; }

.bj-msg-list { padding: 0 .5rem .5rem; }
.bj-msg-item {
    display: flex; align-items: flex-start; gap: .75rem;
    padding: .75rem .8rem;
    border-radius: var(--bj-radius-md);
    text-decoration: none;
    color: inherit;
    transition: background .12s;
    position: relative;
}
.bj-msg-item + .bj-msg-item { margin-top: 2px; }
.bj-msg-item:hover { background: var(--bj-bg); text-decoration: none; color: inherit; }

.bj-msg-avatar {
    width: 36px; height: 36px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-weight: 800; font-size: .85rem;
    flex-shrink: 0;
}
.bj-msg-av-1 { background: var(--bj-navy); color: var(--bj-gold); }
.bj-msg-av-2 { background: var(--bj-gold); color: var(--bj-navy); }
.bj-msg-av-3 { background: var(--bj-green); }
.bj-msg-av-4 { background: var(--bj-amber); }

.bj-msg-body { flex: 1; min-width: 0; }
.bj-msg-top {
    display: flex; align-items: baseline; justify-content: space-between; gap: .5rem;
}
.bj-msg-name { font-size: .82rem; font-weight: 800; color: var(--bj-text); }
.bj-msg-time { font-size: .68rem; color: var(--bj-muted); white-space: nowrap; }
.bj-msg-preview {
    font-size: .74rem; color: var(--bj-muted);
    margin-top: 2px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    font-weight: 500;
}
.bj-msg-item--unread .bj-msg-preview { color: var(--bj-text-mid); font-weight: 600; }

/* ── LIVE ORDERS PANEL ────────────────────────── */
.bj-live {
    background: var(--bj-surface);
    border: 1px solid var(--bj-border);
    border-radius: var(--bj-radius-xl);
    box-shadow: var(--bj-shadow);
    overflow: hidden;
}
.bj-live-list { padding: .25rem .75rem .75rem; }
.bj-order-row {
    display: flex; align-items: center; gap: 1rem;
    padding: .9rem .75rem;
    border-radius: var(--bj-radius-md);
    text-decoration: none;
    color: inherit;
    position: relative;
    transition: background .12s;
}
.bj-order-row + .bj-order-row {
    border-top: 1px dashed var(--bj-border);
}
.bj-order-row:hover { background: var(--bj-bg); text-decoration: none; color: inherit; }

.bj-order-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
    margin-left: .25rem;
}
.bj-status-confirmed .bj-order-dot { background: var(--bj-amber); }
.bj-status-cooking   .bj-order-dot { background: var(--bj-gold); }
.bj-status-ready     .bj-order-dot { background: var(--bj-green); }
.bj-status-onway     .bj-order-dot { background: var(--bj-blue-text); }
.bj-status-delivered .bj-order-dot { background: var(--bj-green); }
.bj-status-default   .bj-order-dot { background: var(--bj-muted); }

.bj-order-info { flex: 1; min-width: 0; }
.bj-order-name {
    font-size: .92rem; font-weight: 800; color: var(--bj-text);
    margin-bottom: .15rem;
}
.bj-order-meta {
    font-size: .73rem; color: var(--bj-muted); font-weight: 600;
    direction: rtl;
}
.bj-order-meta .bj-sep { margin: 0 .3rem; opacity: .55; }
.bj-order-id { color: var(--bj-text-mid); direction: ltr; display: inline-block; }

.bj-order-price {
    font-size: .9rem; font-weight: 800; color: var(--bj-text);
    direction: ltr;
    white-space: nowrap;
}
.bj-order-price small { font-size: .7rem; color: var(--bj-muted); font-weight: 600; margin-right: 2px; }

.bj-pill {
    display: inline-flex; align-items: center;
    padding: .3rem .75rem;
    font-size: .7rem; font-weight: 800;
    border-radius: 999px;
    white-space: nowrap;
}
.bj-pill-confirmed { background: var(--bj-amber-soft); color: var(--bj-amber-text); }
.bj-pill-cooking   { background: var(--bj-gold-soft);  color: #8B6B0A; }
.bj-pill-ready     { background: var(--bj-green-soft); color: var(--bj-green-text); }
.bj-pill-onway     { background: var(--bj-blue-soft);  color: var(--bj-blue-text); }
.bj-pill-delivered { background: var(--bj-green-soft); color: var(--bj-green-text); }
.bj-pill-default   { background: #EFEFEF; color: var(--bj-text-mid); }

.bj-empty {
    text-align: center;
    padding: 2.5rem 1rem;
    color: var(--bj-muted);
    font-size: .85rem; font-weight: 600;
}
.bj-empty i { display: block; font-size: 2.25rem; margin-bottom: .5rem; opacity: .35; }

/* ── STOCK ALERT ──────────────────────────────── */
.bj-stock {
    background: #FFEEEC;
    border: 1px solid #F5C6C2;
    border-radius: var(--bj-radius-lg);
    padding: .8rem 1.15rem;
    display: flex; align-items: center; gap: .65rem;
    margin-bottom: 1rem;
}
.bj-stock i { color: #C0392B; font-size: 19px; flex-shrink: 0; }
.bj-stock-text { flex: 1; font-size: .85rem; font-weight: 600; color: #8B2020; }
.bj-stock-text a { color: #8B2020; font-weight: 800; text-decoration: underline; }
.bj-stock-close {
    background: none; border: none; cursor: pointer;
    color: var(--bj-muted); font-size: 16px; padding: 0; line-height: 1;
}

/* ── TOAST ────────────────────────────────────── */
.bj-toast {
    position: fixed; top: 1.5rem; left: 1.5rem;
    z-index: 9999;
    background: var(--bj-surface);
    border: 2px solid var(--bj-amber);
    border-radius: var(--bj-radius-lg);
    padding: 1rem 1.25rem;
    box-shadow: var(--bj-shadow-hover), 0 0 0 4px rgba(224,123,0,.12);
    min-width: 280px;
    display: none;
    direction: rtl;
    animation: bjToast .3s cubic-bezier(.22,1,.36,1);
}
@keyframes bjToast { from { opacity:0; transform:translateX(-16px); } to { opacity:1; transform:translateX(0); } }
.bj-toast.show { display: block; }
.bj-toast-title { font-size: .92rem; font-weight: 800; color: var(--bj-text); margin-bottom: .3rem; }
.bj-toast-body  { font-size: .8rem; color: var(--bj-muted); margin-bottom: .75rem; }
.bj-toast-actions { display: flex; gap: .5rem; }
.bj-toast-actions .bj-btn { padding: .4rem .9rem; font-size: .78rem; }

/* ── RESPONSIVE ───────────────────────────────── */
@media (max-width: 1100px) {
    .bj-grid { grid-template-columns: 1fr; }
}
@media (max-width: 900px) {
    .bj-tiles { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 600px) {
    .bj-wrap { padding: 0 .875rem; }
    .bj-tile-num { font-size: 2.3rem; }
    .bj-revenue-amount { font-size: 2rem; }
    .bj-order-row { gap: .5rem; padding: .75rem .5rem; }
    .bj-greet-msg { display: none; }
}

/* ── ARABIC NUMERALS HELPER ───────────────────── */
.bj-ar-num { font-feature-settings: "tnum"; }
</style>
@endpush

@section('content')
<div class="bj-dash">
<div class="bj-wrap">

@if(auth('vendor')->check())
@php
    $restaurant      = \App\CentralLogics\Helpers::get_restaurant_data();
    $restaurant_id   = $restaurant?->id;
    $loggedin        = auth('vendor')->user();
    $vendor_initial  = mb_strtoupper(mb_substr($loggedin?->f_name ?? 'م', 0, 1));

    // Time-based greeting
    $hr = (int) now()->format('H');
    if ($hr < 12)       $greeting = 'صباح الخير';
    elseif ($hr < 17)   $greeting = 'مساء الخير';
    else                $greeting = 'مساء الخير';

    // Urgent count (confirmed waiting + ready waiting handoff)
    $urgent_count = ($data['confirmed'] ?? 0) + ($data['ready_for_delivery'] ?? 0);

    // Recent orders (live)
    $recent_orders = \App\Models\Order::where('restaurant_id', $restaurant_id)
        ->whereNotIn('order_status', ['delivered','failed','canceled','refunded','refund_requested'])
        ->with(['customer','details'])
        ->latest()
        ->take(6)
        ->get();

    // Recent conversations
    $sender = $loggedin;
    $recent_convs = \App\Models\Conversation::with(['sender','receiver','last_message'])
        ->where(function($q) use ($sender) {
            $q->where('sender_id', $sender?->id)->orWhere('receiver_id', $sender?->id);
        })
        ->latest('updated_at')
        ->take(4)
        ->get();

    // Today summary
    $today_orders   = \App\Models\Order::where('restaurant_id', $restaurant_id)
        ->whereDate('created_at', today())->get();
    $today_revenue  = $today_orders->whereIn('order_status', ['delivered'])->sum('order_amount');
    $today_count    = $today_orders->count();
    $today_avg      = $today_count > 0 ? round($today_orders->sum('order_amount') / $today_count, 0) : 0;
    $yesterday_revenue = \App\Models\Order::where('restaurant_id', $restaurant_id)
        ->whereDate('created_at', today()->subDay())
        ->whereIn('order_status', ['delivered'])->sum('order_amount');
    $rev_delta = $yesterday_revenue > 0
        ? round((($today_revenue - $yesterday_revenue) / $yesterday_revenue) * 100, 0)
        : 0;

    // Customer rating
    $rating_avg = number_format((float)($restaurant?->avg_rating ?? 0), 1);

    // Last 7 days revenue sparkline
    $spark_days = [];
    $spark_data = [];
    for ($i = 6; $i >= 0; $i--) {
        $d = today()->copy()->subDays($i);
        $spark_days[] = $d->format('M d');
        $spark_data[] = (float) \App\Models\Order::where('restaurant_id', $restaurant_id)
            ->whereDate('created_at', $d)
            ->where('order_status', 'delivered')
            ->sum('order_amount');
    }
@endphp

{{-- ── TOAST ─────────────────────────────── --}}
<div class="bj-toast" id="bj-toast">
    <div class="bj-toast-title">🛎 طلب جديد!</div>
    <div class="bj-toast-body" id="bj-toast-body">جاري التحديث...</div>
    <div class="bj-toast-actions">
        <button class="bj-btn" onclick="bjPrintLatest()"><i class="tio-print"></i> طباعة</button>
        <button class="bj-btn bj-btn-ghost" onclick="bjDismissToast()">تجاهل</button>
    </div>
</div>
<iframe id="bj-print-frame" style="display:none"></iframe>

{{-- ── GREETING BAR ──────────────────────── --}}
<div class="bj-greet">
    <div class="bj-greet-left">
        <div class="bj-user-chip">
            <div class="bj-user-chip-avatar">
                @if($loggedin?->image_full_url)
                    <img src="{{ $loggedin->image_full_url }}" alt="">
                @else
                    {{ $vendor_initial }}
                @endif
            </div>
            <div class="bj-user-chip-text">
                <div class="bj-user-chip-name">{{ $loggedin->f_name ?? '' }} {{ $loggedin->l_name ?? '' }}</div>
                <div class="bj-user-chip-role">مدير المطعم</div>
            </div>
        </div>
        <div class="bj-greet-msg">
            <span class="bj-greet-hello">{{ $greeting }} 👋</span>
            @if($urgent_count > 0)
                <span class="bj-greet-headline">لديك {{ $urgent_count }} طلب يحتاج اهتمامك</span>
            @endif
        </div>
    </div>
    <div class="bj-greet-right">
        <span class="bj-open-pill"><span class="bj-open-dot"></span> المطعم مفتوح</span>
        <button class="bj-btn bj-btn-ghost" onclick="bjRefresh()" title="تحديث">
            <i class="tio-refresh"></i> تحديث
        </button>
        <a href="{{ route('vendor.order.list', ['all']) }}" class="bj-btn">
            <i class="tio-receipt"></i> كل الطلبات
        </a>
        <button class="bj-btn-icon" type="button" title="الإشعارات">
            <i class="tio-notifications"></i>
            @if($urgent_count > 0)<span class="bj-btn-icon-badge">{{ $urgent_count }}</span>@endif
        </button>
    </div>
</div>

{{-- ── STOCK ALERT ───────────────────────── --}}
@if(Session::get('stock_out_reminder_close_btn') !== true && isset($out_out_count) && $out_out_count > 0)
<div class="bj-stock" id="bj-stock">
    <i class="tio-warning-outlined"></i>
    <span class="bj-stock-text">
        @if($out_out_count == 1 && isset($food))
            {{ $food?->name }} — نفدت الكمية
        @else
            {{ $out_out_count }} منتجات نفدت من المخزن
        @endif
        &nbsp;<a href="{{ route('vendor.food.stockOutList') }}">عرض القائمة</a>
    </span>
    <button class="bj-stock-close add-to-session" data-id="stock_out_reminder_close_btn"
        onclick="this.closest('.bj-stock').remove()"><i class="tio-clear"></i></button>
</div>
@endif

{{-- ── STATUS TILES ──────────────────────── --}}
<div class="bj-tiles" id="order_stats">
    <a href="{{ route('vendor.order.list', ['confirmed']) }}" class="bj-tile bj-tile--confirmed">
        <div class="bj-tile-head">
            <span class="bj-tile-label">بانتظار التحضير</span>
            <span class="bj-tile-ico"><i class="tio-time"></i></span>
        </div>
        <div class="bj-tile-num bj-ar-num">{{ $data['confirmed'] ?? 0 }}</div>
        <div class="bj-tile-foot">
            <span>منذ آخر تحديث</span>
            <span class="bj-tile-foot-num">1د+</span>
        </div>
    </a>
    <a href="{{ route('vendor.order.list', ['cooking']) }}" class="bj-tile bj-tile--cooking">
        <div class="bj-tile-head">
            <span class="bj-tile-label">جاري التحضير</span>
            <span class="bj-tile-ico"><i class="tio-restaurant-menu"></i></span>
        </div>
        <div class="bj-tile-num bj-ar-num">{{ $data['cooking'] ?? 0 }}</div>
        <div class="bj-tile-foot">
            <span>متوسط الوقت</span>
            <span class="bj-tile-foot-num">14د</span>
        </div>
    </a>
    <a href="{{ route('vendor.order.list', ['ready_for_delivery']) }}" class="bj-tile bj-tile--ready">
        <div class="bj-tile-head">
            <span class="bj-tile-label">جاهز للتسليم</span>
            <span class="bj-tile-ico"><i class="tio-checkmark-circle"></i></span>
        </div>
        <div class="bj-tile-num bj-ar-num">{{ $data['ready_for_delivery'] ?? 0 }}</div>
        <div class="bj-tile-foot">
            <span>جاهز</span>
            <strong>الآن</strong>
        </div>
    </a>
    <a href="{{ route('vendor.order.list', ['food_on_the_way']) }}" class="bj-tile bj-tile--onway">
        <div class="bj-tile-head">
            <span class="bj-tile-label">في الطريق</span>
            <span class="bj-tile-ico"><i class="tio-delivery"></i></span>
        </div>
        <div class="bj-tile-num bj-ar-num">{{ $data['food_on_the_way'] ?? 0 }}</div>
        <div class="bj-tile-foot">
            <span>أقرب وصول</span>
            <span class="bj-tile-foot-num">7د</span>
        </div>
    </a>
</div>

{{-- ── MAIN GRID ─────────────────────────── --}}
<div class="bj-grid">

    {{-- LEFT: revenue + messages --}}
    <div>
        {{-- Revenue card --}}
        <div class="bj-revenue">
            <div class="bj-revenue-head">
                <div class="bj-revenue-title">
                    <span class="bj-ico-chip"><i class="tio-money"></i></span>
                    إيرادات اليوم
                </div>
                <select class="bj-revenue-period" onchange="bjFilterStats(this.value)">
                    <option value="today"      {{ ($params['statistics_type'] ?? 'today') == 'today' ? 'selected' : '' }}>اليوم</option>
                    <option value="this_month" {{ ($params['statistics_type'] ?? '') == 'this_month' ? 'selected' : '' }}>هذا الشهر</option>
                    <option value="overall"    {{ ($params['statistics_type'] ?? '') == 'overall' ? 'selected' : '' }}>الإجمالي</option>
                </select>
            </div>
            <div class="bj-revenue-amount bj-ar-num">
                {{ number_format($today_revenue, 0) }}<span class="bj-revenue-currency">ج.م</span>
            </div>
            @if($rev_delta != 0)
                <div class="bj-revenue-delta">
                    <i class="tio-arrow-{{ $rev_delta >= 0 ? 'upward' : 'downward' }}"></i>
                    {{ abs($rev_delta) }}% مقارنة بأمس
                </div>
            @endif
            <div class="bj-revenue-chart" id="bj-revenue-chart"></div>
            <div class="bj-revenue-foot">
                <div class="bj-rev-stat">
                    <div class="bj-rev-stat-label">عدد الطلبات</div>
                    <div class="bj-rev-stat-val bj-ar-num">{{ $today_count }}</div>
                </div>
                <div class="bj-rev-stat">
                    <div class="bj-rev-stat-label">متوسط الطلب</div>
                    <div class="bj-rev-stat-val bj-ar-num">{{ $today_avg }}</div>
                </div>
                <div class="bj-rev-stat">
                    <div class="bj-rev-stat-label">رضا العملاء</div>
                    <div class="bj-rev-stat-val bj-ar-num">{{ $rating_avg }}<span class="bj-star">★</span></div>
                </div>
            </div>
        </div>

        {{-- Messages --}}
        <div class="bj-messages">
            <div class="bj-panel-head">
                <span class="bj-panel-title"><i class="tio-chat-outlined"></i> آخر الرسائل</span>
                <a href="{{ route('vendor.message.list') }}" class="bj-panel-more">عرض الكل ←</a>
            </div>
            <div class="bj-msg-list">
                @forelse($recent_convs as $i => $conv)
                @php
                    $other     = $conv->sender_id === $sender?->id ? $conv->receiver : $conv->sender;
                    $msgText   = $conv->last_message?->message ?? '...';
                    $unread    = ($conv->unread_message_count ?? 0) > 0;
                    $initial   = mb_strtoupper(mb_substr($other?->f_name ?? '?', 0, 1));
                    $avClass   = 'bj-msg-av-' . (($i % 4) + 1);
                @endphp
                <a href="{{ route('vendor.message.view', ['conversation_id' => $conv->id, 'user_id' => $other?->id ?? 0]) }}"
                   class="bj-msg-item {{ $unread ? 'bj-msg-item--unread' : '' }}">
                    <div class="bj-msg-avatar {{ $avClass }}">{{ $initial }}</div>
                    <div class="bj-msg-body">
                        <div class="bj-msg-top">
                            <span class="bj-msg-name">{{ $other?->f_name ?? 'مستخدم' }} {{ $other?->l_name ?? '' }}</span>
                            <span class="bj-msg-time">منذ {{ \Carbon\Carbon::parse($conv->updated_at)->diffForHumans(null, \Carbon\CarbonInterface::DIFF_ABSOLUTE) }}</span>
                        </div>
                        <div class="bj-msg-preview">{{ Str::limit($msgText, 50) }}</div>
                    </div>
                </a>
                @empty
                <div class="bj-empty"><i class="tio-chat-outlined"></i>لا رسائل حتى الآن</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- RIGHT: live orders --}}
    <div class="bj-live">
        <div class="bj-panel-head">
            <span class="bj-panel-title"><i class="tio-receipt"></i> الطلبات الحية</span>
            <a href="{{ route('vendor.order.list', ['all']) }}" class="bj-panel-more">عرض الكل ←</a>
        </div>
        <div class="bj-live-list">
            @php
                $orderStatusMap = [
                    'pending'            => ['label' => 'قيد الانتظار',  'cls' => 'confirmed'],
                    'confirmed'          => ['label' => 'بانتظار التحضير','cls' => 'confirmed'],
                    'accepted'           => ['label' => 'بانتظار التحضير','cls' => 'confirmed'],
                    'processing'         => ['label' => 'جاري التحضير',   'cls' => 'cooking'],
                    'cooking'            => ['label' => 'جاري التحضير',   'cls' => 'cooking'],
                    'handover'           => ['label' => 'جاهز للتسليم',  'cls' => 'ready'],
                    'ready_for_delivery' => ['label' => 'جاهز للتسليم',  'cls' => 'ready'],
                    'picked_up'          => ['label' => 'في الطريق',     'cls' => 'onway'],
                    'food_on_the_way'    => ['label' => 'في الطريق',     'cls' => 'onway'],
                    'delivered'          => ['label' => 'تم التسليم',    'cls' => 'delivered'],
                ];
            @endphp
            @forelse($recent_orders as $order)
                @php
                    $sInfo  = $orderStatusMap[$order->order_status] ?? ['label' => $order->order_status, 'cls' => 'default'];
                    $items  = $order->details->count();
                    $area   = $order->delivery_address['address'] ?? $order->delivery_address['zone'] ?? '';
                    $area   = $area ? Str::limit(strip_tags($area), 22, '') : '';
                    $cName  = trim(($order->customer->f_name ?? 'عميل') . ' ' . ($order->customer->l_name ?? ''));
                @endphp
                <a href="{{ route('vendor.order.details', ['id' => $order->id]) }}"
                   class="bj-order-row bj-status-{{ $sInfo['cls'] }}">
                    <span class="bj-order-dot"></span>
                    <div class="bj-order-info">
                        <div class="bj-order-name">{{ $cName }}</div>
                        <div class="bj-order-meta">
                            <span class="bj-order-id">طلب #{{ $order->id }}</span>
                            <span class="bj-sep">·</span>
                            <span class="bj-ar-num">{{ $items }} {{ $items == 1 ? 'صنف' : 'أصناف' }}</span>
                            @if($area)
                                <span class="bj-sep">·</span>
                                <span>{{ $area }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="bj-order-price bj-ar-num">
                        {{ number_format($order->order_amount, 0) }}<small>ج.م</small>
                    </div>
                    <span class="bj-pill bj-pill-{{ $sInfo['cls'] }}">{{ $sInfo['label'] }}</span>
                </a>
            @empty
                <div class="bj-empty"><i class="tio-receipt"></i>لا طلبات حية الآن</div>
            @endforelse
        </div>
    </div>

</div>{{-- /bj-grid --}}

@else
{{-- Employee fallback --}}
<div style="padding:4rem 0;text-align:center;">
    <h2 style="font-weight:800;color:var(--bj-text);">
        {{ translate('messages.welcome') }}, {{ auth('vendor_employee')->user()->f_name }}
    </h2>
    <p style="color:var(--bj-muted);">{{ translate('messages.employee_welcome_message') }}</p>
</div>
@endif

</div>{{-- /bj-wrap --}}
</div>{{-- /bj-dash --}}
@endsection

@push('script')
<script src="{{dynamicAsset('public/assets/admin/apexcharts/apexcharts.min.js')}}"></script>
<script>
(function(){
    'use strict';

    // ── Revenue sparkline ────────────────────
    var sparkData = @json($spark_data ?? []);
    var sparkDays = @json($spark_days ?? []);

    if (typeof ApexCharts !== 'undefined' && document.getElementById('bj-revenue-chart')) {
        var chart = new ApexCharts(document.getElementById('bj-revenue-chart'), {
            chart: {
                type: 'area', height: 90, sparkline: { enabled: false },
                toolbar: { show: false }, zoom: { enabled: false },
                animations: { enabled: true, easing: 'easeinout', speed: 600 },
                background: 'transparent'
            },
            stroke: { curve: 'smooth', width: 2.5, colors: ['#D4A017'] },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.02,
                    stops: [0, 100], colorStops: [
                        { offset: 0, color: '#D4A017', opacity: .45 },
                        { offset: 100, color: '#D4A017', opacity: 0 }
                    ]
                }
            },
            series: [{ name: 'إيرادات', data: sparkData }],
            xaxis: {
                categories: sparkDays,
                labels: { show: false },
                axisBorder: { show: false }, axisTicks: { show: false }
            },
            yaxis: { labels: { show: false } },
            grid: { show: false, padding: { left: 8, right: 8, top: 0, bottom: 0 } },
            tooltip: {
                theme: 'dark', x: { show: true },
                y: { formatter: function(v){ return Math.round(v) + ' ج.م'; } }
            },
            dataLabels: { enabled: false },
            markers: { size: 0, hover: { size: 4 } }
        });
        chart.render();
    }

    // ── New order toast / audio ──────────────
    var lastConfirmed = {{ ($data['confirmed'] ?? 0) }};
    var latestOrderId = null;
    var audioCtx = null;

    document.addEventListener('click', function unlock(){
        try { audioCtx = new (window.AudioContext||window.webkitAudioContext)(); } catch(e){}
        document.removeEventListener('click', unlock);
    }, { once: true });

    function beep(freq, start, dur){
        if (!audioCtx) return;
        var o = audioCtx.createOscillator(); var g = audioCtx.createGain();
        o.connect(g); g.connect(audioCtx.destination);
        o.type = 'sine';
        o.frequency.setValueAtTime(freq, audioCtx.currentTime + start);
        g.gain.setValueAtTime(0.3, audioCtx.currentTime + start);
        g.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + start + dur);
        o.start(audioCtx.currentTime + start);
        o.stop(audioCtx.currentTime + start + dur);
    }
    function playAlert(){ beep(880,0,.35); beep(660,.45,.35); beep(880,.9,.35); }

    function autoPrint(id){
        if (!id) return;
        var frame = document.getElementById('bj-print-frame');
        if (!frame) return;
        var url = '{{ route("vendor.order.generate-invoice", ["id" => "__X__"]) }}'.replace('__X__', id);
        frame.onload = function(){ try { frame.contentWindow.focus(); frame.contentWindow.print(); } catch(e){} };
        frame.src = url;
    }

    window.bjPrintLatest   = function(){ if (latestOrderId) autoPrint(latestOrderId); bjDismissToast(); };
    window.bjDismissToast  = function(){ var t = document.getElementById('bj-toast'); if (t) t.classList.remove('show'); };

    function showToast(id, customer){
        latestOrderId = id;
        var t    = document.getElementById('bj-toast');
        var body = document.getElementById('bj-toast-body');
        if (body) body.textContent = 'طلب #' + id + ' — ' + (customer || 'عميل جديد');
        if (t) { t.classList.add('show'); playAlert(); autoPrint(id); }
        setTimeout(bjDismissToast, 14000);
    }

    function refreshStats(){
        var type = document.querySelector('.bj-revenue-period')?.value || 'today';
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        $.post('{{ route("vendor.dashboard.order-stats") }}', { statistics_type: type }, function(data){
            // We don't replace the new tiles HTML — the partial structure is different.
            // Instead, pull the numbers from data.data and update in-place.
            if (data && data.data) {
                bjSetTile('--confirmed', data.data.confirmed ?? 0);
                bjSetTile('--cooking',   data.data.cooking   ?? 0);
                bjSetTile('--ready',     data.data.ready_for_delivery ?? 0);
                bjSetTile('--onway',     data.data.food_on_the_way    ?? 0);

                var newConfirmed = parseInt(data.data.confirmed ?? 0);
                if (newConfirmed > lastConfirmed) {
                    showToast(data.data.latest_order_id || null, data.data.latest_customer || '');
                    lastConfirmed = newConfirmed;
                }
            }
        });
    }
    function bjSetTile(modifier, val){
        var el = document.querySelector('.bj-tile' + modifier + ' .bj-tile-num');
        if (el) el.textContent = val;
    }

    window.bjRefresh     = refreshStats;
    window.bjFilterStats = function(type){
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        $.post('{{ route("vendor.dashboard.order-stats") }}', { statistics_type: type }, function(data){
            if (data && data.data) {
                bjSetTile('--confirmed', data.data.confirmed ?? 0);
                bjSetTile('--cooking',   data.data.cooking   ?? 0);
                bjSetTile('--ready',     data.data.ready_for_delivery ?? 0);
                bjSetTile('--onway',     data.data.food_on_the_way    ?? 0);
            }
        });
    };

    $(document).on('click', '.add-to-session', function(){
        $.ajax({
            url: '{{ route("vendor.food.addToSession") }}',
            method: 'POST',
            data: { value: $(this).data('id'), _token: '{{ csrf_token() }}' }
        });
    });

    var timer = setInterval(refreshStats, 40000);
    window.addEventListener('beforeunload', function(){ clearInterval(timer); });
})();
</script>
@endpush
