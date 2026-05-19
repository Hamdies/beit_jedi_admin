@extends('layouts.vendor.app')

@section('title','قائمة الطلبات')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --bj-cream: #f6f1e7;
            --bj-cream-2: #efe8d8;
            --bj-ink: #15233f;
            --bj-ink-2: #1f2d4a;
            --bj-muted: #6b7280;
            --bj-line: rgba(15,35,63,0.08);
            --bj-soft: #fbf8f1;
            --bj-amber: #f0b829;
            --bj-amber-soft: #fdf1d2;
            --bj-orange: #ef8a3c;
            --bj-orange-soft: #fde6d2;
            --bj-green: #2fb574;
            --bj-green-soft: #d8f1e2;
            --bj-blue: #2f6fed;
            --bj-blue-soft: #dde7fb;
            --bj-red: #e25c5c;
            --bj-red-soft: #fadcdc;
            --bj-purple: #7a5af8;
        }

        body { background: var(--bj-cream); }
        .footer { background: transparent; }

        .bj-orders { padding: 1.5rem 1.25rem 2rem; direction: rtl; }

        /* ===== Top welcome bar ===== */
        .bj-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1.25rem;
        }
        .bj-page-head h1 {
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--bj-ink);
            margin: 0 0 .25rem;
        }
        .bj-page-head .bj-crumbs {
            font-size: .8rem;
            color: var(--bj-muted);
            margin-bottom: .35rem;
        }
        .bj-page-head .bj-crumbs i { font-size: .65rem; margin: 0 .35rem; opacity: .6; }
        .bj-page-head p { color: var(--bj-muted); font-size: .85rem; margin: 0; }

        .bj-welcome {
            display: flex;
            align-items: center;
            gap: .85rem;
            background: #fff;
            border: 1px solid var(--bj-line);
            border-radius: 16px;
            padding: .55rem .9rem;
            box-shadow: 0 1px 2px rgba(15,35,63,0.03);
        }
        .bj-welcome .bj-avatar {
            width: 38px; height: 38px; border-radius: 12px;
            background: var(--bj-ink); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 1rem;
        }
        .bj-welcome .bj-role { font-size: .72rem; color: var(--bj-muted); }
        .bj-welcome .bj-name { font-weight: 700; color: var(--bj-ink); font-size: .9rem; }

        .bj-attention {
            display: flex; flex-direction: column; align-items: flex-end;
            font-size: .85rem; line-height: 1.3;
        }
        .bj-attention .bj-greet { color: var(--bj-muted); font-size: .8rem; }
        .bj-attention .bj-greet .bj-wave { display: inline-block; transform: scaleX(-1); }
        .bj-attention strong { color: var(--bj-ink); font-weight: 700; }

        .bj-topactions { display: flex; gap: .5rem; align-items: center; flex-wrap: wrap; }
        .bj-pill-btn {
            background: #fff;
            border: 1px solid var(--bj-line);
            border-radius: 14px;
            padding: .55rem .9rem;
            font-size: .85rem;
            font-weight: 600;
            color: var(--bj-ink);
            display: inline-flex; align-items: center; gap: .4rem;
            transition: all .15s ease;
        }
        .bj-pill-btn:hover { background: var(--bj-soft); color: var(--bj-ink); text-decoration: none; }
        .bj-pill-btn.is-dark { background: var(--bj-ink); color: #fff; border-color: var(--bj-ink); }
        .bj-pill-btn.is-dark:hover { background: var(--bj-ink-2); color: #fff; }
        .bj-pill-btn .bj-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--bj-green); display: inline-block; }
        .bj-icon-btn {
            width: 40px; height: 40px; border-radius: 12px;
            background: #fff; border: 1px solid var(--bj-line);
            display: inline-flex; align-items: center; justify-content: center;
            color: var(--bj-ink); position: relative;
        }
        .bj-icon-btn .bj-badge-dot {
            position: absolute; top: 8px; left: 9px;
            width: 8px; height: 8px; border-radius: 50%; background: var(--bj-red);
            border: 2px solid #fff;
        }

        /* ===== KPI cards ===== */
        .bj-kpis {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: .75rem;
            margin-bottom: 1.25rem;
        }
        .bj-kpi {
            background: #fff;
            border: 1px solid var(--bj-line);
            border-radius: 18px;
            padding: .9rem 1rem;
            position: relative;
        }
        .bj-kpi-head {
            display: flex; align-items: center; gap: .35rem;
            font-size: .8rem; color: var(--bj-muted); font-weight: 600;
            margin-bottom: .45rem;
        }
        .bj-kpi-head .bj-dot { width: 7px; height: 7px; border-radius: 50%; }
        .bj-kpi-value { font-size: 1.85rem; font-weight: 800; color: var(--bj-ink); line-height: 1; margin-bottom: .35rem; }
        .bj-kpi-meta { font-size: .75rem; color: var(--bj-muted); }
        .bj-kpi-meta .up { color: var(--bj-green); font-weight: 700; }
        .bj-kpi-meta .down { color: var(--bj-red); font-weight: 700; }
        .bj-kpi-meta.is-warn { color: var(--bj-orange); font-weight: 600; }
        .bj-kpi-meta.is-ok { color: var(--bj-green); font-weight: 600; }

        @media (max-width: 1200px) { .bj-kpis { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 720px) { .bj-kpis { grid-template-columns: repeat(2, 1fr); } }

        /* ===== Filter pills ===== */
        .bj-panel {
            background: #fff;
            border: 1px solid var(--bj-line);
            border-radius: 20px;
            padding: 1rem 1.1rem;
            margin-bottom: 1rem;
            box-shadow: 0 1px 2px rgba(15,35,63,0.02);
        }
        .bj-status-tabs {
            display: flex; gap: .4rem; flex-wrap: wrap;
            justify-content: flex-end;
            background: var(--bj-soft);
            border-radius: 14px;
            padding: .35rem;
        }
        .bj-tab {
            display: inline-flex; align-items: center; gap: .35rem;
            background: transparent; border: 0;
            padding: .45rem .85rem;
            font-size: .82rem; font-weight: 600;
            color: var(--bj-ink); border-radius: 10px;
            text-decoration: none;
            transition: all .15s;
        }
        .bj-tab:hover { background: rgba(15,35,63,0.04); color: var(--bj-ink); text-decoration: none; }
        .bj-tab.is-active { background: var(--bj-ink); color: #fff; box-shadow: 0 4px 10px rgba(15,35,63,0.18); }
        .bj-tab .bj-tab-count {
            background: rgba(255,255,255,0.15); color: inherit;
            font-size: .72rem; padding: .1rem .45rem;
            border-radius: 8px; font-weight: 700;
        }
        .bj-tab:not(.is-active) .bj-tab-count { background: rgba(15,35,63,0.06); color: var(--bj-ink); }
        .bj-tab .bj-dot { width: 7px; height: 7px; border-radius: 50%; }

        /* ===== Toolbar ===== */
        .bj-toolbar {
            display: flex; gap: .6rem; align-items: center;
            flex-wrap: wrap;
            margin-top: .85rem;
        }
        .bj-search {
            flex: 1 1 320px;
            display: flex; align-items: center; gap: .5rem;
            background: var(--bj-soft);
            border: 1px solid var(--bj-line);
            border-radius: 12px;
            padding: .55rem .9rem;
        }
        .bj-search input {
            flex: 1; border: 0; background: transparent; outline: none;
            font-size: .85rem; color: var(--bj-ink);
            direction: rtl; text-align: right;
        }
        .bj-search .bj-kbd {
            font-size: .7rem; color: var(--bj-muted);
            border: 1px solid var(--bj-line); border-radius: 6px;
            padding: .05rem .35rem; background: #fff;
        }
        .bj-tool-btn {
            background: #fff;
            border: 1px solid var(--bj-line);
            border-radius: 12px;
            padding: .55rem .85rem;
            font-size: .82rem; font-weight: 600;
            color: var(--bj-ink);
            display: inline-flex; align-items: center; gap: .4rem;
        }
        .bj-tool-btn.is-active { border-color: var(--bj-ink); box-shadow: 0 0 0 2px rgba(15,35,63,0.08); }
        .bj-tool-btn i { color: var(--bj-muted); }

        /* ===== Orders table ===== */
        .bj-table-wrap {
            background: #fff;
            border: 1px solid var(--bj-line);
            border-radius: 20px;
            overflow: hidden;
        }
        .bj-table { width: 100%; border-collapse: collapse; }
        .bj-table thead th {
            background: var(--bj-soft);
            font-size: .78rem; font-weight: 700;
            color: var(--bj-muted);
            padding: .85rem 1rem;
            text-align: right;
            border-bottom: 1px solid var(--bj-line);
            white-space: nowrap;
        }
        .bj-table thead th .bj-sort { font-size: .65rem; margin-right: .2rem; opacity: .55; }
        .bj-table tbody td {
            padding: 1rem;
            border-bottom: 1px solid var(--bj-line);
            font-size: .85rem;
            color: var(--bj-ink);
            vertical-align: middle;
        }
        .bj-table tbody tr:last-child td { border-bottom: 0; }
        .bj-table tbody tr.is-selected td { background: #f4f6fb; }
        .bj-table tbody tr:hover td { background: #fafafa; }
        .bj-table tbody tr.is-selected:hover td { background: #eef1f7; }

        .bj-check {
            appearance: none; width: 18px; height: 18px;
            border: 1.5px solid #c5cad6; border-radius: 5px;
            background: #fff; cursor: pointer;
            display: inline-flex; align-items: center; justify-content: center;
        }
        .bj-check:checked { background: var(--bj-ink); border-color: var(--bj-ink); }
        .bj-check:checked::after {
            content: ""; width: 5px; height: 9px;
            border: solid #fff; border-width: 0 2px 2px 0;
            transform: rotate(45deg); margin-top: -2px;
        }

        .bj-order-id {
            color: var(--bj-ink); font-weight: 800; font-size: .95rem;
            display: block; text-decoration: none;
        }
        .bj-order-id:hover { color: var(--bj-blue); text-decoration: none; }
        .bj-order-id.is-warn { color: var(--bj-orange); }
        .bj-order-tag {
            display: inline-flex; align-items: center; gap: .25rem;
            font-size: .7rem; color: var(--bj-muted); margin-top: .25rem;
        }
        .bj-vip {
            display: inline-flex; align-items: center; gap: .2rem;
            background: var(--bj-amber-soft); color: #a4760b;
            font-size: .68rem; font-weight: 800;
            padding: .1rem .45rem; border-radius: 6px;
            margin-top: .25rem;
        }
        .bj-recurring {
            display: inline-flex; align-items: center; gap: .2rem;
            background: var(--bj-amber-soft); color: #a4760b;
            font-size: .7rem; font-weight: 700;
            padding: .1rem .45rem; border-radius: 6px;
            margin-top: .25rem;
        }

        .bj-customer { display: flex; align-items: center; gap: .55rem; }
        .bj-customer-avatar {
            width: 32px; height: 32px; border-radius: 10px;
            background: var(--bj-ink); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: .85rem; flex-shrink: 0;
        }
        .bj-customer-avatar.c-2 { background: #5a6f9f; }
        .bj-customer-avatar.c-3 { background: #6d4ea3; }
        .bj-customer-name { font-weight: 700; color: var(--bj-ink); font-size: .87rem; }
        .bj-customer-phone { font-size: .73rem; color: var(--bj-muted); direction: ltr; text-align: right; }

        .bj-items { font-size: .82rem; color: var(--bj-ink); }
        .bj-items .bj-items-more { color: var(--bj-muted); font-size: .75rem; margin-top: .15rem; }

        .bj-date { font-size: .82rem; color: var(--bj-ink); }
        .bj-date .bj-time { font-size: .73rem; color: var(--bj-muted); display: block; margin-top: .1rem; }
        .bj-relative {
            display: inline-flex; align-items: center; gap: .25rem;
            background: var(--bj-amber-soft); color: #8c6406;
            font-size: .7rem; font-weight: 600;
            padding: .15rem .5rem; border-radius: 8px;
            margin-top: .3rem;
        }
        .bj-relative.is-now { background: var(--bj-blue-soft); color: #1c4ab3; }

        .bj-amount { font-weight: 800; color: var(--bj-ink); font-size: .95rem; }
        .bj-pay-pill {
            display: inline-block; margin-top: .3rem;
            font-size: .7rem; font-weight: 700;
            padding: .15rem .5rem; border-radius: 8px;
        }
        .bj-pay-pill.is-paid { background: var(--bj-green-soft); color: #1f7a4a; }
        .bj-pay-pill.is-unpaid { background: var(--bj-orange-soft); color: #a55411; }

        .bj-status-pill {
            display: inline-flex; align-items: center; gap: .35rem;
            font-size: .78rem; font-weight: 700;
            padding: .3rem .65rem; border-radius: 10px;
        }
        .bj-status-pill .bj-dot { width: 7px; height: 7px; border-radius: 50%; }
        .bj-status-pill.s-delivered { background: var(--bj-green-soft); color: #1f7a4a; }
        .bj-status-pill.s-delivered .bj-dot { background: var(--bj-green); }
        .bj-status-pill.s-onway { background: var(--bj-blue-soft); color: #1c4ab3; }
        .bj-status-pill.s-onway .bj-dot { background: var(--bj-blue); }
        .bj-status-pill.s-ready { background: var(--bj-green-soft); color: #1f7a4a; }
        .bj-status-pill.s-ready .bj-dot { background: var(--bj-green); }
        .bj-status-pill.s-cooking { background: var(--bj-amber-soft); color: #8c6406; }
        .bj-status-pill.s-cooking .bj-dot { background: var(--bj-amber); }
        .bj-status-pill.s-pending { background: var(--bj-orange-soft); color: #a55411; }
        .bj-status-pill.s-pending .bj-dot { background: var(--bj-orange); }
        .bj-status-pill.s-canceled { background: var(--bj-red-soft); color: #a53030; }
        .bj-status-pill.s-canceled .bj-dot { background: var(--bj-red); }
        .bj-status-pill.s-default { background: #eef0f4; color: #475467; }
        .bj-status-pill.s-default .bj-dot { background: #98a2b3; }

        .bj-status-sub { font-size: .73rem; color: var(--bj-muted); margin-top: .3rem; }

        .bj-duration { font-size: .73rem; color: var(--bj-muted); display: flex; align-items: center; gap: .35rem; }
        .bj-duration i { color: var(--bj-muted); }
        .bj-progress {
            width: 60px; height: 4px; background: rgba(15,35,63,0.08);
            border-radius: 99px; overflow: hidden; margin-top: .35rem;
        }
        .bj-progress > span {
            display: block; height: 100%; background: var(--bj-amber);
            border-radius: 99px;
        }

        .bj-actions { display: flex; gap: .35rem; }
        .bj-act {
            width: 32px; height: 32px; border-radius: 9px;
            background: var(--bj-soft); color: var(--bj-ink);
            display: inline-flex; align-items: center; justify-content: center;
            border: 1px solid var(--bj-line);
        }
        .bj-act:hover { background: var(--bj-ink); color: #fff; }

        .bj-empty { padding: 3.5rem 1rem 3rem; text-align: center; }
        .bj-empty img { max-width: 140px; opacity: .75; }
        .bj-empty h5 { margin-top: 1rem; color: var(--bj-muted); font-weight: 600; }

        .bj-footer-bar {
            display: flex; justify-content: space-between; align-items: center;
            padding: 1rem 1.25rem; flex-wrap: wrap; gap: .5rem;
            border-top: 1px solid var(--bj-line);
            background: var(--bj-soft);
        }
        .bj-footer-bar .pagination { margin: 0; }

        @media (max-width: 992px) {
            .bj-table thead { display: none; }
            .bj-table, .bj-table tbody, .bj-table tr, .bj-table td { display: block; width: 100%; }
            .bj-table tr { padding: .5rem; border-bottom: 1px solid var(--bj-line); }
            .bj-table tbody td { padding: .4rem .8rem; border: 0; }
        }
    </style>
@endpush

@section('content')
<?php
    use Illuminate\Support\Str;
    $bjVendor       = auth('vendor')->check() ? auth('vendor')->user() : auth('vendor_employee')->user();
    $bjFirstName    = $bjVendor?->f_name;
    $bjLastName     = $bjVendor?->l_name;
    $bjDisplayName  = trim(($bjFirstName ?? '').' '.($bjLastName ?? '')) ?: 'مرحبا';
    $bjInitial      = mb_substr($bjFirstName ?: $bjDisplayName, 0, 1);
    $bjRestaurant   = \App\CentralLogics\Helpers::get_restaurant_data();
    $bjIsOpen       = (int)($bjRestaurant->status ?? 1) === 1;
    $bjHour         = (int) now()->format('H');
    $bjGreet        = $bjHour < 12 ? 'صباح الخير' : ($bjHour < 18 ? 'مساء الخير' : 'مساء الخير');

    $bjStatusCounts = $statusCounts ?? [];
    $bjWeeklyTotal  = $weeklyTotal ?? 0;
    $bjWeeklyGrowth = $weeklyGrowth ?? 0;
    $bjAttention    = $needsAttention ?? 0;

    $bjTabs = [
        ['key' => 'all',           'label' => 'الكل',         'count' => $bjStatusCounts['all'] ?? 0,        'dot' => null],
        ['key' => 'pending',       'label' => 'معلّقة',       'count' => $bjStatusCounts['pending'] ?? 0,    'dot' => 'var(--bj-orange)'],
        ['key' => 'cooking',       'label' => 'تحضير',        'count' => $bjStatusCounts['cooking'] ?? 0,    'dot' => 'var(--bj-amber)'],
        ['key' => 'ready_for_delivery', 'label' => 'جاهز',    'count' => $bjStatusCounts['ready'] ?? 0,      'dot' => 'var(--bj-green)'],
        ['key' => 'food_on_the_way','label' => 'في الطريق',   'count' => $bjStatusCounts['on_the_way'] ?? 0, 'dot' => 'var(--bj-blue)'],
        ['key' => 'delivered',     'label' => 'تم التوصيل',   'count' => $bjStatusCounts['delivered'] ?? 0,  'dot' => 'var(--bj-green)'],
    ];

    $bjCurrentStatus = $st ?? 'all';

    $bjAvatarColors = ['', 'c-2', 'c-3'];
?>

<div class="content container-fluid bj-orders">

    {{-- Top bar: title + welcome + top actions --}}
    <div class="bj-topbar">
        <div class="bj-page-head">
            <div class="bj-crumbs">
                <span>لوحة التحكم</span>
                <i class="tio-chevron-left"></i>
                <span>الطلبات</span>
            </div>
            <h1>قائمة الطلبات</h1>
            <p>استعرض جميع الطلبات الواردة لمطعمك وتابع حالتها بسهولة.</p>
        </div>

        <div class="bj-topactions">
            <div class="bj-welcome">
                <div class="bj-attention">
                    <span class="bj-greet">{{ $bjGreet }} <span class="bj-wave">👋</span></span>
                    @if($bjAttention > 0)
                        <span>لديك <strong>{{ $bjAttention }}</strong> طلب يحتاج اهتمامك</span>
                    @else
                        <span>لا توجد طلبات معلّقة الآن</span>
                    @endif
                </div>
                <div>
                    <div class="bj-role">مدير المطعم</div>
                    <div class="bj-name">{{ $bjDisplayName }}</div>
                </div>
                <div class="bj-avatar">{{ Str::upper($bjInitial) ?: 'م' }}</div>
            </div>

            <span class="bj-pill-btn">
                <span class="bj-dot" style="background: {{ $bjIsOpen ? 'var(--bj-green)' : 'var(--bj-red)' }};"></span>
                {{ $bjIsOpen ? 'المطعم مفتوح' : 'المطعم مغلق' }}
            </span>

            <a href="{{ route('vendor.order.list', ['status' => $st]) }}" class="bj-pill-btn">
                <i class="tio-refresh"></i> تحديث
            </a>

            <a href="{{ route('vendor.order.list', ['status' => 'all']) }}" class="bj-pill-btn is-dark">
                <i class="tio-content"></i> كل الطلبات
            </a>

            <a href="javascript:;" class="bj-icon-btn">
                <i class="tio-notifications-on-outlined"></i>
                <span class="bj-badge-dot"></span>
            </a>
        </div>
    </div>

    {{-- KPI cards --}}
    <div class="bj-kpis">
        <div class="bj-kpi">
            <div class="bj-kpi-head">
                <span class="bj-dot" style="background: var(--bj-ink);"></span>
                إجمالي
            </div>
            <div class="bj-kpi-value">{{ $bjWeeklyTotal }}</div>
            <div class="bj-kpi-meta">
                @if($bjWeeklyGrowth >= 0)
                    <span class="up">▲ {{ $bjWeeklyGrowth }}%</span>
                @else
                    <span class="down">▼ {{ abs($bjWeeklyGrowth) }}%</span>
                @endif
                من الأسبوع الماضي
            </div>
        </div>

        <div class="bj-kpi">
            <div class="bj-kpi-head">
                <span class="bj-dot" style="background: var(--bj-orange);"></span>
                معلّقة
            </div>
            <div class="bj-kpi-value">{{ $bjStatusCounts['pending'] ?? 0 }}</div>
            <div class="bj-kpi-meta is-warn">يحتاج اهتمامك</div>
        </div>

        <div class="bj-kpi">
            <div class="bj-kpi-head">
                <span class="bj-dot" style="background: var(--bj-amber);"></span>
                تحضير
            </div>
            <div class="bj-kpi-value">{{ $bjStatusCounts['cooking'] ?? 0 }}</div>
            <div class="bj-kpi-meta">متوسط 14 د</div>
        </div>

        <div class="bj-kpi">
            <div class="bj-kpi-head">
                <span class="bj-dot" style="background: var(--bj-green);"></span>
                جاهز
            </div>
            <div class="bj-kpi-value">{{ $bjStatusCounts['ready'] ?? 0 }}</div>
            <div class="bj-kpi-meta is-ok">بانتظار المندوب</div>
        </div>

        <div class="bj-kpi">
            <div class="bj-kpi-head">
                <span class="bj-dot" style="background: var(--bj-blue);"></span>
                في الطريق
            </div>
            <div class="bj-kpi-value">{{ $bjStatusCounts['on_the_way'] ?? 0 }}</div>
            <div class="bj-kpi-meta">أقرب وصول 7 د</div>
        </div>
    </div>

    {{-- Filter + toolbar panel --}}
    <div class="bj-panel">
        <div class="bj-status-tabs">
            @foreach($bjTabs as $tab)
                <a href="{{ route('vendor.order.list', ['status' => $tab['key']]) }}"
                   class="bj-tab {{ $bjCurrentStatus === $tab['key'] ? 'is-active' : '' }}">
                    @if($tab['dot'])
                        <span class="bj-dot" style="background: {{ $tab['dot'] }};"></span>
                    @endif
                    <span>{{ $tab['label'] }}</span>
                    <span class="bj-tab-count">{{ $tab['count'] }}</span>
                </a>
            @endforeach
        </div>

        <form class="bj-toolbar" method="GET" action="{{ route('vendor.order.list', ['status' => $st]) }}">
            <div class="bj-search">
                <i class="tio-search" style="color: var(--bj-muted);"></i>
                <input type="search" name="search" value="{{ request('search') }}"
                       placeholder="ابحث برقم الطلب أو اسم العميل أو ...">
                <span class="bj-kbd">⌘ K</span>
            </div>

            <button type="button" class="bj-tool-btn is-active">
                <i class="tio-time"></i> آخر 7 أيام
                <i class="tio-chevron-down" style="font-size: .65rem;"></i>
            </button>

            <button type="button" class="bj-tool-btn">
                <i class="tio-credit-card-outlined"></i> الدفع: الكل
                <i class="tio-chevron-down" style="font-size: .65rem;"></i>
            </button>

            <div class="hs-unfold">
                <a class="bj-tool-btn js-hs-unfold-invoker" href="javascript:;"
                   data-hs-unfold-options='{"target": "#bjExportDropdown", "type": "css-animation"}'>
                    <i class="tio-download-to"></i> تصدير
                </a>
                <div id="bjExportDropdown" class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                    <span class="dropdown-header">خيارات التنزيل</span>
                    <a class="dropdown-item" href="{{ route('vendor.order.export', ['status'=>$st,'type'=>'excel', request()->getQueryString()]) }}">
                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                             src="{{ dynamicAsset('public/assets/admin') }}/svg/components/excel.svg" alt="excel">
                        ملف Excel
                    </a>
                    <a class="dropdown-item" href="{{ route('vendor.order.export', ['status'=>$st,'type'=>'csv', request()->getQueryString()]) }}">
                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                             src="{{ dynamicAsset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg" alt="csv">
                        ملف CSV
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bj-table-wrap">
        <table class="bj-table">
            <thead>
                <tr>
                    <th style="width:42px;"><input type="checkbox" class="bj-check" id="bj-check-all"></th>
                    <th style="width:60px;">م</th>
                    <th>رقم الطلب <span class="bj-sort">⌄</span></th>
                    <th>العميل</th>
                    <th>الأصناف</th>
                    <th>التاريخ <span class="bj-sort">⌄</span></th>
                    <th>المبلغ</th>
                    <th>الحالة</th>
                    <th>المدة / وقت</th>
                    <th style="width:90px;"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $key => $order)
                    @php
                        $isGuest = $order->is_guest;
                        if ($isGuest) {
                            $addr = json_decode($order['delivery_address'], true);
                            $custName  = $addr['contact_person_name']   ?? '—';
                            $custPhone = $addr['contact_person_number'] ?? '';
                        } else {
                            $custName  = trim(($order->customer['f_name'] ?? '').' '.($order->customer['l_name'] ?? '')) ?: '—';
                            $custPhone = $order->customer['phone'] ?? '';
                        }
                        $custInitial = mb_substr($custName, 0, 1);
                        $avatarClass = $bjAvatarColors[$key % 3];

                        $detailsCount = $order->details()->count();
                        $firstDetail  = $order->details()->first();
                        $firstItem    = null;
                        $firstQty     = 1;
                        if ($firstDetail) {
                            $food = json_decode($firstDetail->food_details ?? '{}', true);
                            $firstItem = $food['name'] ?? '—';
                            $firstQty  = $firstDetail->quantity ?? 1;
                        }
                        $extraItems = max(0, $detailsCount - 1);

                        $createdAt = \Carbon\Carbon::parse($order['created_at']);
                        $diffMin   = $createdAt->diffInMinutes(now());
                        $relative  = $createdAt->locale('ar')->diffForHumans(null, true);

                        $statusMap = [
                            'pending'   => ['class' => 's-pending',  'label' => 'بانتظار التأكيد', 'sub' => 'يحتاج اهتمامك'],
                            'confirmed' => ['class' => 's-pending',  'label' => 'مؤكد',           'sub' => 'بانتظار التحضير'],
                            'processing'=> ['class' => 's-cooking',  'label' => 'جاري التحضير',   'sub' => 'في المطبخ'],
                            'handover'  => ['class' => 's-ready',    'label' => 'جاهز للتسليم',   'sub' => 'بانتظار المندوب'],
                            'picked_up' => ['class' => 's-onway',    'label' => 'في الطريق',      'sub' => 'مع المندوب'],
                            'delivered' => ['class' => 's-delivered','label' => 'تم التوصيل',     'sub' => 'مدفوع ومسلَّم'],
                            'canceled'  => ['class' => 's-canceled', 'label' => 'ملغي',           'sub' => 'تم الإلغاء'],
                            'failed'    => ['class' => 's-canceled', 'label' => 'فشل',            'sub' => 'الدفع فشل'],
                        ];
                        $s = $statusMap[$order['order_status']] ?? ['class' => 's-default', 'label' => str_replace('_',' ', $order['order_status']), 'sub' => ''];

                        $orderTypeIcon = match($order['order_type']) {
                            'take_away' => ['tio-shop-outlined', 'استلام'],
                            'dine_in'   => ['tio-restaurant-menu', 'تناول'],
                            default     => ['tio-home-outlined', 'توصيل'],
                        };

                        $progress = match($order['order_status']) {
                            'pending'    => 15,
                            'confirmed'  => 30,
                            'processing' => 55,
                            'handover'   => 75,
                            'picked_up'  => 90,
                            'delivered'  => 100,
                            default      => 10,
                        };
                    @endphp
                    <tr class="status-{{ $order['order_status'] }}">
                        <td><input type="checkbox" class="bj-check bj-row-check"></td>
                        <td style="color: var(--bj-muted);">{{ $key + $orders->firstItem() }}</td>
                        <td>
                            <a href="{{ route('vendor.order.details', ['id' => $order['id']]) }}"
                               class="bj-order-id {{ $order['order_status'] === 'pending' ? 'is-warn' : '' }}">
                                #{{ 100000 + $order['id'] }}
                            </a>
                            <div class="bj-order-tag">
                                <i class="{{ $orderTypeIcon[0] }}"></i> {{ $orderTypeIcon[1] }}
                            </div>
                            @if(($order->customer?->orders_count ?? 0) >= 5)
                                <span class="bj-vip">★ VIP</span>
                            @elseif(isset($order->subscription_id))
                                <span class="bj-recurring">★ دائم</span>
                            @endif
                        </td>
                        <td>
                            <div class="bj-customer">
                                <div class="bj-customer-avatar {{ $avatarClass }}">{{ Str::upper($custInitial) }}</div>
                                <div>
                                    <div class="bj-customer-name">{{ Str::limit($custName, 14) }}</div>
                                    <div class="bj-customer-phone">{{ $custPhone }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="bj-items">
                                @if($firstItem)
                                    <div>{{ Str::limit($firstItem, 18) }} × {{ $firstQty }}</div>
                                @endif
                                @if($extraItems > 0)
                                    <div class="bj-items-more">+ {{ $extraItems }} {{ $extraItems === 1 ? 'صنف آخر' : 'أصناف أخرى' }}</div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="bj-date">
                                <div>{{ $createdAt->locale(app()->getLocale())->translatedFormat('d M Y') }}</div>
                                <span class="bj-time">{{ $createdAt->translatedFormat(config('timeformat') ?? 'h:i A') }}</span>
                                <span class="bj-relative {{ $diffMin < 5 ? 'is-now' : '' }}">
                                    <i class="tio-time" style="font-size:.65rem;"></i>
                                    {{ $diffMin < 1 ? 'الآن' : 'منذ '.$relative }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="bj-amount">
                                {!! \App\CentralLogics\Helpers::format_currency($order['order_amount']) !!}
                            </div>
                            @if($order->payment_status === 'paid')
                                <span class="bj-pay-pill is-paid">✓ مدفوع</span>
                            @elseif($order->payment_status === 'partially_paid')
                                <span class="bj-pay-pill is-paid">مدفوع جزئيًا</span>
                            @else
                                <span class="bj-pay-pill is-unpaid">غير مدفوع</span>
                            @endif
                        </td>
                        <td>
                            <span class="bj-status-pill {{ $s['class'] }}">
                                <span class="bj-dot"></span>{{ $s['label'] }}
                            </span>
                            @if(!empty($s['sub']))
                                <div class="bj-status-sub">{{ $s['sub'] }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="bj-duration">
                                <i class="tio-time"></i>
                                <span>{{ $diffMin < 60 ? $diffMin.' د' : round($diffMin/60).' س' }}</span>
                            </div>
                            <div class="bj-progress"><span style="width: {{ $progress }}%;"></span></div>
                        </td>
                        <td>
                            <div class="bj-actions">
                                <a class="bj-act" href="{{ route('vendor.order.details', ['id' => $order['id']]) }}" title="عرض">
                                    <i class="tio-visible-outlined"></i>
                                </a>
                                <a class="bj-act" target="_blank"
                                   href="{{ route('vendor.order.generate-invoice', [$order['id']]) }}" title="طباعة">
                                    <i class="tio-print"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if(count($orders) === 0)
            <div class="bj-empty">
                <img src="{{ dynamicAsset('/public/assets/admin/img/empty.png') }}" alt="empty">
                <h5>لا توجد طلبات حتى الآن</h5>
            </div>
        @endif

        <div class="bj-footer-bar">
            <div style="font-size:.8rem; color: var(--bj-muted);">
                إجمالي {{ $orders->total() }} طلب
            </div>
            <div>{!! $orders->links() !!}</div>
        </div>
    </div>
</div>
@endsection

@push('script_2')
<script>
    "use strict";
    document.addEventListener('DOMContentLoaded', function () {
        var checkAll = document.getElementById('bj-check-all');
        var rows = document.querySelectorAll('.bj-row-check');

        if (checkAll) {
            checkAll.addEventListener('change', function () {
                rows.forEach(function (cb) {
                    cb.checked = checkAll.checked;
                    cb.closest('tr').classList.toggle('is-selected', checkAll.checked);
                });
            });
        }

        rows.forEach(function (cb) {
            cb.addEventListener('change', function () {
                cb.closest('tr').classList.toggle('is-selected', cb.checked);
                if (!cb.checked && checkAll) checkAll.checked = false;
            });
        });
    });
</script>
@endpush
