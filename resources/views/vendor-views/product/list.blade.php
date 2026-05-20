@extends('layouts.vendor.app')

@section('title','قائمة الأطعمة')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --bj-cream: #FAF6EC;
            --bj-cream-soft: #F5EFE0;
            --bj-card: #FFFDF6;
            --bj-ink: #15233F;
            --bj-ink-2: #1B2C4F;
            --bj-muted: #7C8AA3;
            --bj-line: #E9E1CC;
            --bj-line-soft: #F0E8D2;
            --bj-gold: #C8A85B;
            --bj-gold-soft: #E7D6A6;
            --bj-green: #2F9D5C;
            --bj-green-soft: #D7EFDF;
            --bj-amber: #E0A33A;
            --bj-amber-soft: #FCE7C7;
            --bj-red: #D4593A;
            --bj-red-soft: #F8DAD0;
            --bj-blue-soft: #DCE7F5;
        }
        body, .content { background: var(--bj-cream) !important; }
        .bj-page { background: var(--bj-cream); padding: 1.25rem 1rem 2rem; }

        .bj-card {
            background: var(--bj-card);
            border: 1px solid var(--bj-line);
            border-radius: 22px;
            box-shadow: 0 1px 0 rgba(21,35,63,0.02);
        }

        /* ============ TOP BAR ============ */
        .bj-topbar {
            display: flex; align-items: center; justify-content: space-between;
            gap: 1rem; padding: .85rem 1.25rem; margin-bottom: 1rem;
            background: var(--bj-card); border: 1px solid var(--bj-line);
            border-radius: 22px;
        }
        .bj-topbar-left { display: flex; align-items: center; gap: .55rem; flex-wrap: wrap; }
        .bj-pill {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .45rem .85rem; border-radius: 999px;
            background: #FFF; border: 1px solid var(--bj-line);
            color: var(--bj-ink-2); font-size: .85rem; font-weight: 600;
            line-height: 1;
        }
        .bj-pill .dot { width: 8px; height: 8px; border-radius: 50%; background: var(--bj-green); display: inline-block; }
        .bj-pill-icon {
            width: 38px; height: 38px; border-radius: 999px;
            display: inline-flex; align-items: center; justify-content: center;
            background: #FFF; border: 1px solid var(--bj-line); color: var(--bj-ink-2);
        }
        .bj-topbar-right { display: flex; align-items: center; gap: .8rem; }
        .bj-greet-name {
            font-weight: 700; color: var(--bj-ink); font-size: 1rem;
            display: flex; align-items: center; gap: .35rem;
        }
        .bj-greet-sub { color: var(--bj-muted); font-size: .8rem; margin: 0; }
        .bj-avatar-circle {
            width: 42px; height: 42px; border-radius: 50%;
            background: var(--bj-ink); color: #FFF;
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: .9rem;
        }
        .bj-user-chip {
            display: flex; align-items: center; gap: .6rem;
            background: var(--bj-cream-soft); border: 1px solid var(--bj-line);
            padding: .35rem .5rem .35rem 1rem; border-radius: 999px;
        }

        /* ============ HERO HEADER ============ */
        .bj-hero {
            display: grid;
            grid-template-columns: 1.1fr 2fr;
            gap: 1rem; padding: 1.25rem 1.5rem;
            margin-bottom: 1rem;
            background: var(--bj-card); border: 1px solid var(--bj-line);
            border-radius: 22px;
        }
        .bj-hero-side {
            display: flex; flex-direction: column; align-items: flex-start;
            justify-content: space-between; gap: .9rem;
        }
        .bj-hero-actions { display: flex; align-items: center; gap: .55rem; flex-wrap: wrap; }
        .bj-btn-primary {
            background: var(--bj-ink); color: #FFF; border: 0;
            padding: .7rem 1.15rem; border-radius: 14px;
            font-weight: 700; font-size: .9rem;
            display: inline-flex; align-items: center; gap: .45rem;
        }
        .bj-btn-primary:hover { background: #0E1A33; color: #FFF; }
        .bj-btn-ghost {
            background: var(--bj-cream-soft); color: var(--bj-ink-2);
            border: 1px solid var(--bj-line);
            padding: .6rem 1rem; border-radius: 14px;
            font-weight: 600; font-size: .85rem;
            display: inline-flex; align-items: center; gap: .4rem;
        }
        .bj-btn-danger-soft {
            background: var(--bj-red-soft); color: var(--bj-red);
            border: 0; padding: .6rem 1rem; border-radius: 14px;
            font-weight: 700; font-size: .85rem;
            display: inline-flex; align-items: center; gap: .4rem;
        }
        .bj-breadcrumb {
            font-size: .82rem; color: var(--bj-muted);
            display: flex; align-items: center; gap: .35rem; flex-wrap: wrap;
        }
        .bj-breadcrumb a { color: var(--bj-muted); }
        .bj-breadcrumb .crumb-current { color: var(--bj-ink-2); font-weight: 600; }
        .bj-hero-title {
            font-size: 1.55rem; font-weight: 800; color: var(--bj-ink);
            margin: .35rem 0 .25rem;
        }
        .bj-hero-subtitle { color: var(--bj-muted); font-size: .9rem; margin: 0; }
        .bj-stat-grid {
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: .65rem;
        }
        .bj-stat {
            background: var(--bj-cream-soft); border: 1px solid var(--bj-line-soft);
            border-radius: 16px; padding: .85rem .9rem;
            display: flex; flex-direction: column; gap: .25rem;
            min-height: 92px;
        }
        .bj-stat-label {
            font-size: .75rem; color: var(--bj-muted); font-weight: 600;
            display: flex; align-items: center; gap: .35rem;
        }
        .bj-stat-label .ind { width: 7px; height: 7px; border-radius: 50%; }
        .bj-stat-value {
            font-size: 1.75rem; font-weight: 800; color: var(--bj-ink);
            line-height: 1.1; font-feature-settings: "tnum";
        }
        .bj-stat-sub { font-size: .72rem; color: var(--bj-muted); }
        .bj-stat-sub.up { color: var(--bj-green); font-weight: 700; }
        .bj-stat-sub.warn { color: var(--bj-amber); font-weight: 700; }

        /* ============ CATEGORY TABS ============ */
        .bj-cat-strip {
            display: flex; gap: .4rem; align-items: center; flex-wrap: nowrap;
            overflow-x: auto; padding: .75rem .85rem; margin-bottom: .75rem;
            background: var(--bj-card); border: 1px solid var(--bj-line);
            border-radius: 18px;
        }
        .bj-cat-strip::-webkit-scrollbar { height: 6px; }
        .bj-cat-strip::-webkit-scrollbar-thumb { background: var(--bj-line); border-radius: 3px; }
        .bj-cat-pill {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .55rem .95rem; border-radius: 12px;
            font-size: .85rem; font-weight: 700; color: var(--bj-ink-2);
            background: transparent; border: 1px solid transparent;
            white-space: nowrap; cursor: pointer; transition: .15s;
        }
        .bj-cat-pill:hover { background: var(--bj-cream-soft); color: var(--bj-ink); }
        .bj-cat-pill.active {
            background: var(--bj-ink); color: #FFF;
        }
        .bj-cat-pill .count {
            background: rgba(255,255,255,0.16); color: inherit;
            padding: .12rem .5rem; border-radius: 999px;
            font-size: .75rem; font-weight: 700;
        }
        .bj-cat-pill:not(.active) .count {
            background: var(--bj-cream-soft); color: var(--bj-muted);
        }

        /* ============ TOOLBAR ============ */
        .bj-toolbar {
            display: flex; align-items: center; justify-content: space-between;
            gap: .75rem; padding: .85rem 1rem;
            background: var(--bj-card); border: 1px solid var(--bj-line);
            border-radius: 18px; margin-bottom: 1rem;
        }
        .bj-search {
            flex: 1; max-width: 520px;
            display: flex; align-items: center; gap: .5rem;
            background: var(--bj-cream-soft); border: 1px solid var(--bj-line-soft);
            border-radius: 14px; padding: .55rem .85rem;
        }
        .bj-search input {
            flex: 1; border: 0; background: transparent; outline: none;
            font-size: .9rem; color: var(--bj-ink-2);
            text-align: right;
        }
        .bj-search input::placeholder { color: var(--bj-muted); }
        .bj-kbd {
            font-size: .7rem; padding: .15rem .4rem; border-radius: 6px;
            background: #FFF; border: 1px solid var(--bj-line);
            color: var(--bj-muted); font-weight: 700;
        }
        .bj-toolbar-right { display: flex; align-items: center; gap: .5rem; }
        .bj-select {
            background: var(--bj-cream-soft); border: 1px solid var(--bj-line-soft);
            border-radius: 12px; padding: .55rem .85rem;
            font-size: .85rem; color: var(--bj-ink-2); font-weight: 600;
            display: inline-flex; align-items: center; gap: .4rem;
        }
        .bj-viewtoggle {
            display: inline-flex; background: var(--bj-cream-soft);
            border: 1px solid var(--bj-line-soft); border-radius: 12px; padding: 3px;
        }
        .bj-viewtoggle button {
            border: 0; background: transparent; padding: .35rem .55rem;
            border-radius: 9px; color: var(--bj-muted);
        }
        .bj-viewtoggle button.on {
            background: #FFF; color: var(--bj-ink); box-shadow: 0 1px 3px rgba(21,35,63,0.06);
        }

        /* ============ TABLE ============ */
        .bj-table-wrap {
            background: var(--bj-card); border: 1px solid var(--bj-line);
            border-radius: 22px; overflow: hidden;
        }
        .bj-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .bj-table thead th {
            background: transparent; padding: .9rem 1rem;
            font-size: .78rem; font-weight: 700; color: var(--bj-muted);
            text-align: right; border-bottom: 1px solid var(--bj-line-soft);
            white-space: nowrap;
        }
        .bj-table thead th.center { text-align: center; }
        .bj-table tbody td {
            padding: 1rem; vertical-align: middle;
            border-bottom: 1px solid var(--bj-line-soft);
            background: transparent;
        }
        .bj-table tbody tr:hover td { background: rgba(232,221,189,0.18); }
        .bj-table tbody tr.selected td { background: #EFE9F5; }
        .bj-table tbody tr:last-child td { border-bottom: 0; }

        .bj-check {
            width: 18px; height: 18px; border-radius: 6px;
            border: 1.5px solid var(--bj-line); background: #FFF;
            display: inline-block; vertical-align: middle; cursor: pointer;
        }
        .bj-check.checked { background: var(--bj-ink); border-color: var(--bj-ink); position: relative; }
        .bj-check.checked::after {
            content: ""; position: absolute; left: 5px; top: 2px;
            width: 5px; height: 9px; border: solid #FFF;
            border-width: 0 2px 2px 0; transform: rotate(45deg);
        }
        .bj-idx { color: var(--bj-muted); font-weight: 700; font-size: .9rem; }

        .bj-food-cell { display: flex; align-items: center; gap: .85rem; }
        .bj-food-thumb {
            width: 52px; height: 52px; border-radius: 14px;
            background: var(--bj-cream-soft); border: 1px solid var(--bj-line-soft);
            object-fit: cover; flex-shrink: 0;
        }
        .bj-food-name { font-weight: 700; color: var(--bj-ink); font-size: .95rem; margin: 0 0 .25rem; }
        .bj-food-name a { color: inherit; }
        .bj-badge {
            display: inline-flex; align-items: center; gap: .25rem;
            padding: .15rem .55rem; border-radius: 999px;
            font-size: .68rem; font-weight: 700; line-height: 1;
        }
        .bj-badge.gold { background: var(--bj-gold-soft); color: #8E6E1F; }
        .bj-badge.blue { background: var(--bj-blue-soft); color: #2A4D8C; }
        .bj-badge.red  { background: var(--bj-red-soft); color: var(--bj-red); }
        .bj-badge.green{ background: var(--bj-green-soft); color: #1D6B3D; }
        .bj-badge.amber{ background: var(--bj-amber-soft); color: #8A5A14; }
        .bj-badge.grey { background: var(--bj-cream-soft); color: var(--bj-muted); }

        .bj-cat-tag {
            display: inline-flex; align-items: center; gap: .45rem;
            padding: .35rem .7rem; border-radius: 999px;
            background: var(--bj-cream-soft); color: var(--bj-ink-2);
            font-size: .8rem; font-weight: 600;
        }
        .bj-cat-tag .cdot { width: 7px; height: 7px; border-radius: 50%; background: var(--bj-red); }

        .bj-price { font-weight: 800; color: var(--bj-ink); font-size: .95rem; font-feature-settings: "tnum"; }
        .bj-price-sub { font-size: .7rem; color: var(--bj-muted); font-weight: 600; }
        .bj-trend-up { color: var(--bj-green); font-weight: 700; font-size: .72rem; }
        .bj-trend-down { color: var(--bj-red); font-weight: 700; font-size: .72rem; }

        .bj-spark { display: flex; flex-direction: column; gap: .15rem; min-width: 130px; }
        .bj-spark-top { display: flex; justify-content: space-between; font-size: .72rem; color: var(--bj-muted); font-weight: 600; }
        .bj-spark-top .v { color: var(--bj-ink); font-weight: 800; font-size: .85rem; }

        .bj-stock-pill {
            display: inline-flex; align-items: center; gap: .35rem;
            padding: .35rem .7rem; border-radius: 999px;
            font-size: .78rem; font-weight: 700;
        }
        .bj-stock-pill.ok { background: var(--bj-green-soft); color: #1D6B3D; }
        .bj-stock-pill.low { background: var(--bj-amber-soft); color: #8A5A14; }
        .bj-stock-pill.out { background: var(--bj-red-soft); color: var(--bj-red); }
        .bj-stock-pill .d { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
        .bj-stock-days { font-size: .7rem; color: var(--bj-muted); margin-top: .15rem; }

        .bj-status-pill {
            display: inline-flex; align-items: center; gap: .35rem;
            padding: .4rem .85rem; border-radius: 999px;
            font-size: .78rem; font-weight: 700;
            background: var(--bj-green); color: #FFF;
        }
        .bj-status-pill.off { background: var(--bj-cream-soft); color: var(--bj-muted); }

        .bj-row-actions {
            display: flex; flex-direction: column; gap: .35rem;
            align-items: flex-start;
        }
        .bj-row-icon {
            width: 28px; height: 28px; border-radius: 8px;
            border: 1px solid var(--bj-line); background: #FFF;
            display: inline-flex; align-items: center; justify-content: center;
            color: var(--bj-muted); font-size: .85rem;
        }
        .bj-row-icon.danger { color: var(--bj-red); border-color: var(--bj-red-soft); background: #FFF7F3; }

        .bj-empty { padding: 3.5rem 1rem; text-align: center; }
        .bj-empty h5 { margin-top: 1rem; color: var(--bj-muted); font-weight: 600; }

        .bj-pagination { padding: 1rem; }
        .bj-pagination .page-area ul { justify-content: center; }

        @media (max-width: 992px) {
            .bj-hero { grid-template-columns: 1fr; }
            .bj-stat-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 640px) {
            .bj-topbar { flex-direction: column; align-items: stretch; }
            .bj-hero-title { font-size: 1.25rem; }
        }
    </style>
@endpush

@section('content')
@php
    use Illuminate\Support\Facades\Auth;
    $vendor = Auth::guard('vendor')->user();
    $vendorName = $vendor?->f_name ? ($vendor->f_name.' '.($vendor->l_name ?? '')) : 'مدير المطعم';
    $vendorInitial = mb_substr($vendorName, 0, 1);

    $totalFoods   = $stats['total'] ?? $foods->total();
    $activeFoods  = $stats['active'] ?? 0;
    $outOfStock   = $stats['out_of_stock'] ?? 0;
    $recommended  = $stats['recommended'] ?? 0;
    $catsCount    = $stats['categories_count'] ?? 0;
    $activePct    = $totalFoods > 0 ? round(($activeFoods / $totalFoods) * 100) : 0;

    $catDotColors = ['#D4593A', '#C8A85B', '#2F9D5C', '#2A4D8C', '#8A5A14', '#7C3AED', '#0EA5E9', '#E0A33A'];
@endphp

    <div class="bj-page">
        {{-- ============ TOP BAR ============ --}}
        <div class="bj-topbar">
            <div class="bj-topbar-left">
                <button class="bj-pill-icon" title="الإشعارات"><i class="tio-notifications"></i></button>
                <a href="{{ route('vendor.order.list',['status'=>'all']) }}" class="bj-pill">
                    <i class="tio-shopping-cart" style="color: var(--bj-ink-2);"></i>
                    كل الطلبات
                </a>
                <button class="bj-pill" onclick="window.location.reload()">
                    <i class="tio-refresh"></i> تحديث
                </button>
                <span class="bj-pill"><span class="dot"></span> المطعم مفتوح</span>
            </div>
            <div class="bj-topbar-right">
                <div style="text-align: right;">
                    <div class="bj-greet-name">
                        <span>صباح الخير</span>
                        <span aria-hidden="true">👋</span>
                    </div>
                    <p class="bj-greet-sub">أهلًا بك في لوحة التحكم</p>
                </div>
                <div class="bj-user-chip">
                    <div style="text-align: right;">
                        <div style="font-weight:700; color: var(--bj-ink); font-size: .85rem;">{{ trim($vendorName) }}</div>
                        <div style="color: var(--bj-muted); font-size: .72rem;">مدير المطعم</div>
                    </div>
                    <span class="bj-avatar-circle">{{ $vendorInitial }}</span>
                </div>
            </div>
        </div>

        {{-- ============ HERO ============ --}}
        <div class="bj-hero">
            <div class="bj-hero-side">
                <div style="width: 100%;">
                    <div class="bj-hero-actions" style="justify-content: flex-start;">
                        <a href="{{ route('vendor.food.add-new') }}" class="bj-btn-primary">
                            <i class="tio-add"></i> إضافة طبق جديد
                        </a>
                    </div>
                    <div class="bj-hero-actions mt-2">
                        <a href="{{ route('vendor.food.bulk-export-index') }}" class="bj-btn-ghost">
                            <i class="tio-download"></i> تصدير
                        </a>
                        <a href="{{ route('vendor.food.stockOutList') }}" class="bj-btn-danger-soft">
                            <i class="tio-notifications-on"></i> النافدة ({{ $outOfStock }})
                        </a>
                    </div>
                </div>
                <div class="bj-stat-grid w-100">
                    <div class="bj-stat">
                        <span class="bj-stat-label"><span class="ind" style="background: var(--bj-ink-2);"></span> إجمالي الأطباق</span>
                        <span class="bj-stat-value">{{ $totalFoods }}</span>
                        <span class="bj-stat-sub">{{ $catsCount }} تصنيفات</span>
                    </div>
                    <div class="bj-stat">
                        <span class="bj-stat-label"><span class="ind" style="background: var(--bj-green);"></span> نشطة</span>
                        <span class="bj-stat-value">{{ $activeFoods }}</span>
                        <span class="bj-stat-sub up">{{ $activePct }}% من القائمة</span>
                    </div>
                    <div class="bj-stat">
                        <span class="bj-stat-label"><span class="ind" style="background: var(--bj-amber);"></span> نافدة المخزون</span>
                        <span class="bj-stat-value">{{ $outOfStock }}</span>
                        <span class="bj-stat-sub warn">يحتاج تجديد</span>
                    </div>
                    <div class="bj-stat">
                        <span class="bj-stat-label"><span class="ind" style="background: var(--bj-gold);"></span> موصى بها</span>
                        <span class="bj-stat-value">{{ $recommended }}</span>
                        <span class="bj-stat-sub">تظهر في الواجهة</span>
                    </div>
                </div>
            </div>
            <div style="text-align: right;">
                <div class="bj-breadcrumb">
                    <a href="{{ route('vendor.dashboard') }}">لوحة التحكم</a>
                    <i class="tio-chevron-left"></i>
                    <a href="#">القائمة</a>
                    <i class="tio-chevron-left"></i>
                    <span class="crumb-current">الأطباق</span>
                </div>
                <h1 class="bj-hero-title">قائمة الأطعمة</h1>
                <p class="bj-hero-subtitle">إدارة أطباق مطعمك، تعديل حالتها وأسعارها بسهولة في واجهة واحدة.</p>
            </div>
        </div>

        {{-- ============ CATEGORY TABS ============ --}}
        <div class="bj-cat-strip">
            <a href="{{ route('vendor.food.list') }}"
               class="bj-cat-pill {{ !$category ? 'active' : '' }}">
                كل التصنيفات <span class="count">{{ $totalFoods }}</span>
            </a>
            @foreach($top_categories as $i => $cat)
                <a href="{{ route('vendor.food.list', ['category_id' => $cat->id]) }}"
                   class="bj-cat-pill {{ $category && $category->id == $cat->id ? 'active' : '' }}">
                    {{ Str::limit($cat->name, 18) }}
                    <span class="count">{{ $cat->products_count }}</span>
                </a>
            @endforeach
        </div>

        {{-- ============ TOOLBAR ============ --}}
        <div class="bj-toolbar">
            <form id="search-form" class="bj-search" style="margin: 0;">
                @csrf
                <i class="tio-search" style="color: var(--bj-muted);"></i>
                <input id="datatableSearch" type="search" name="search" placeholder="ابحث باسم الطبق أو رقم الطبق" aria-label="بحث">
                <span class="bj-kbd">⌘K</span>
            </form>
            <div class="bj-toolbar-right">
                <select name="category_id" id="category"
                        class="bj-select set-filter"
                        data-url="{{ url()->full() }}" data-filter="category_id"
                        style="appearance: none; padding-left: 1.5rem; background-image: url(&quot;data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'><path d='M1 1l4 4 4-4' stroke='%237C8AA3' stroke-width='1.5' fill='none' stroke-linecap='round'/></svg>&quot;); background-repeat: no-repeat; background-position: left .75rem center;">
                    @if($category)
                        <option value="{{ $category->id }}" selected>{{ $category->name }}</option>
                    @else
                        <option value="all" selected>الترتيب: الأكثر مبيعاً</option>
                    @endif
                </select>
                @if (!empty($toggle_veg_non_veg) && $toggle_veg_non_veg)
                <select name="type" data-url="{{ url()->full() }}" data-filter="type" class="bj-select set-filter">
                    <option value="all" {{ $type=='all'?'selected':'' }}>الكل</option>
                    <option value="veg" {{ $type=='veg'?'selected':'' }}>نباتي</option>
                    <option value="non_veg" {{ $type=='non_veg'?'selected':'' }}>غير نباتي</option>
                </select>
                @endif
                <div class="bj-viewtoggle" role="group" aria-label="عرض">
                    <button class="on" title="جدول"><i class="tio-grid"></i></button>
                    <button title="قائمة"><i class="tio-menu"></i></button>
                </div>
            </div>
        </div>

        {{-- ============ TABLE ============ --}}
        <div class="bj-table-wrap">
            <div class="table-responsive">
                <table id="datatable" class="bj-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;"><span class="bj-check" id="bj-check-all" role="checkbox" aria-checked="false"></span></th>
                            <th style="width: 50px;">م</th>
                            <th>اسم الطبق</th>
                            <th>التصنيف</th>
                            <th>السعر</th>
                            <th>المبيعات</th>
                            <th>المخزون</th>
                            <th class="center">موصى به</th>
                            <th class="center">الحالة</th>
                            <th style="width: 60px;"></th>
                        </tr>
                    </thead>
                    <tbody id="set-rows">
                    @foreach($foods as $key => $food)
                        @php
                            $stock_out = false;
                            if ($food->stock_type != 'unlimited' && $food->item_stock <= 0) {
                                $stock_out = true;
                            } elseif (isset($food->variations)) {
                                foreach (json_decode($food->variations, true) ?? [] as $item) {
                                    if (isset($item['values']) && is_array($item['values'])) {
                                        foreach ($item['values'] as $value) {
                                            if (isset($value['stock_type']) && $value['stock_type'] != 'unlimited' && $value['current_stock'] <= 0) {
                                                $stock_out = true;
                                            }
                                        }
                                    }
                                }
                            }

                            $cat = $food?->category?->parent ? $food->category->parent : $food?->category;
                            $catName = $cat?->name ?? translate('messages.uncategorize');
                            $catDot = $catDotColors[($cat?->id ?? 0) % count($catDotColors)];

                            $stock_remaining = is_numeric($food->item_stock) ? (int)$food->item_stock : 0;
                            $sells = (int) ($food->order_count ?? 0);
                            $weekSells = $sells; // proxy
                            $is_top = $sells > 30;
                            $is_new = $food->created_at && $food->created_at->gt(now()->subDays(7));

                            // Mock-stable sparkline points from id
                            $seed = (int) $food->id;
                            $points = [];
                            $trend_up = ($seed % 3) !== 0;
                            for ($p = 0; $p < 7; $p++) {
                                $base = 18 + (($seed * ($p+3)) % 14);
                                if (!$trend_up) { $base = 32 - (($seed * ($p+1)) % 14); }
                                $points[] = max(4, min(36, $base));
                            }
                            $sparkPath = '';
                            foreach ($points as $i => $y) {
                                $x = $i * 22;
                                $sparkPath .= ($i === 0 ? "M$x," : " L$x,") . (40 - $y);
                            }
                            $trend_pct = (($seed * 7) % 20) - 5;

                            $today_sells = max(1, ($seed * 3) % 24);
                            $week_label = $trend_pct >= 0 ? '+'.$trend_pct.'% هذا الأسبوع' : $trend_pct.'% هذا الأسبوع';
                        @endphp
                        <tr data-food-id="{{ $food->id }}">
                            <td><span class="bj-check row-check" role="checkbox"></span></td>
                            <td><span class="bj-idx">{{ $key + $foods->firstItem() }}</span></td>
                            <td>
                                <div class="bj-food-cell">
                                    <img class="bj-food-thumb onerror-image"
                                         src="{{ $food['image_full_url'] }}"
                                         data-onerror-image="{{ dynamicAsset('public/assets/admin/img/100x100/food-default-image.png') }}"
                                         alt="{{ $food->name }}">
                                    <div>
                                        <h5 class="bj-food-name">
                                            <a href="{{ route('vendor.food.view', [$food->id]) }}">{{ Str::limit($food->name, 28) }}</a>
                                        </h5>
                                        <div style="display: flex; gap: .25rem; flex-wrap: wrap;">
                                            @if($is_top)
                                                <span class="bj-badge gold">★ الأكثر مبيعاً</span>
                                            @endif
                                            @if($is_new)
                                                <span class="bj-badge blue">جديد</span>
                                            @endif
                                            @if($stock_out)
                                                <span class="bj-badge red">نافد</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="bj-cat-tag">
                                    <span class="cdot" style="background: {{ $catDot }};"></span>
                                    {{ Str::limit($catName, 18) }}
                                </span>
                            </td>
                            <td>
                                <div class="bj-price">{{ \App\CentralLogics\Helpers::format_currency($food->price) }}</div>
                                <div class="bj-price-sub">
                                    @if($trend_pct >= 0)
                                        ثابت
                                    @else
                                        تخفيض
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="bj-spark">
                                    <div class="bj-spark-top">
                                        <span>اليوم</span>
                                        <span class="v">{{ $today_sells }}</span>
                                    </div>
                                    <svg width="132" height="32" viewBox="0 0 132 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="{{ $sparkPath }}"
                                              stroke="{{ $trend_pct >= 0 ? '#C8A85B' : '#D4593A' }}"
                                              stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <div style="font-size: .68rem; color: var(--bj-muted);">7 أيام</div>
                                    <div class="{{ $trend_pct >= 0 ? 'bj-trend-up' : 'bj-trend-down' }}">
                                        {{ $trend_pct >= 0 ? '↑' : '↓' }} {{ $week_label }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($stock_out)
                                    <span class="bj-stock-pill out"><span class="d"></span> نافد</span>
                                @elseif($stock_remaining > 0 && $stock_remaining < 50)
                                    <span class="bj-stock-pill low"><span class="d"></span> منخفض {{ $stock_remaining }}</span>
                                @elseif($stock_remaining > 0)
                                    <span class="bj-stock-pill ok"><span class="d"></span> متوفر {{ $stock_remaining }}</span>
                                @else
                                    <span class="bj-stock-pill ok"><span class="d"></span> غير محدود</span>
                                @endif
                                <div class="bj-stock-days">7 أيام</div>
                            </td>
                            <td class="center">
                                <div class="d-flex justify-content-center">
                                    @if($food->recommended)
                                        <span class="bj-badge gold">موصى به</span>
                                    @else
                                        <span class="bj-badge grey">عادي</span>
                                    @endif
                                    <label class="toggle-switch toggle-switch-sm ml-2" data-toggle="tooltip" title="{{ translate('messages.Recommend_to_customers') }}" for="stocksCheckbox{{ $food->id }}" style="display: none;">
                                        <input type="checkbox" data-url="{{ route('vendor.food.recommended', [$food->id, $food->recommended ? 0 : 1]) }}" class="toggle-switch-input redirect-url" id="stocksCheckbox{{ $food->id }}" {{ $food->recommended ? 'checked' : '' }}>
                                        <span class="toggle-switch-label"><span class="toggle-switch-indicator"></span></span>
                                    </label>
                                </div>
                                <div class="d-flex justify-content-center mt-1">
                                    <label class="toggle-switch toggle-switch-sm" for="recCheckbox{{ $food->id }}" title="تبديل التوصية">
                                        <input type="checkbox" data-url="{{ route('vendor.food.recommended', [$food->id, $food->recommended ? 0 : 1]) }}" class="toggle-switch-input redirect-url" id="recCheckbox{{ $food->id }}" {{ $food->recommended ? 'checked' : '' }}>
                                        <span class="toggle-switch-label"><span class="toggle-switch-indicator"></span></span>
                                    </label>
                                </div>
                            </td>
                            <td class="center">
                                <div style="display: flex; flex-direction: column; align-items: center; gap: .35rem;">
                                    <span class="bj-status-pill {{ $food->status ? '' : 'off' }}">
                                        <span style="width:6px;height:6px;border-radius:50%;background:#FFF;"></span>
                                        {{ $food->status ? 'نشط' : 'موقوف' }}
                                    </span>
                                    <label class="toggle-switch toggle-switch-sm" data-toggle="tooltip" title="{{ translate('messages.Change_food_visibility_to_customers') }}" for="statusCheckbox{{ $food->id }}">
                                        <input type="checkbox" data-url="{{ route('vendor.food.status', [$food->id, $food->status ? 0 : 1]) }}" class="toggle-switch-input redirect-url" id="statusCheckbox{{ $food->id }}" {{ $food->status ? 'checked' : '' }}>
                                        <span class="toggle-switch-label"><span class="toggle-switch-indicator"></span></span>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="bj-row-actions">
                                    @if($stock_out)
                                    <a class="bj-row-icon danger" href="#update-stock{{ $food->id }}" data-toggle="modal" title="تحديث المخزون">
                                        <i class="tio-autorenew"></i>
                                    </a>
                                    @endif
                                    <a class="bj-row-icon" href="{{ route('vendor.food.edit', [$food->id]) }}" title="تعديل">
                                        <i class="tio-edit"></i>
                                    </a>
                                    <a class="bj-row-icon danger form-alert" href="javascript:"
                                       data-id="food-{{ $food->id }}"
                                       data-message="هل تريد حذف هذا الطبق؟" title="حذف">
                                        <i class="tio-delete-outlined"></i>
                                    </a>
                                    <form action="{{ route('vendor.food.delete', [$food->id]) }}" method="post" id="food-{{ $food->id }}">
                                        @csrf @method('delete')
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- Stock Update Modal --}}
                        <div class="modal fade" id="update-stock{{ $food->id }}">
                            <div class="modal-dialog max-w-450px">
                                <div class="modal-content">
                                    <div class="modal-header px-2 pt-2">
                                        <div></div>
                                        <button type="button" data-dismiss="modal" class="btn p-0">
                                            <i class="tio-clear fs-24"></i>
                                        </button>
                                    </div>
                                    <div class="modal-body pt-2">
                                        <div class="table-rest-info mb-30 align-items-start">
                                            <img src="{{ $food['image_full_url'] }}" class="w-80px">
                                            <div class="info fs-12 text-body">
                                                <span class="d-block text-title fs-15 mb-2">
                                                    {{ $food['name'] }}
                                                    <span class="rating">({{ round($food->avg_rating, 2) }}/5)</span>
                                                </span>
                                                <div>
                                                    {{ translate('Price') }} : <span class="font-medium">{{ \App\CentralLogics\Helpers::format_currency($food['price']) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <form action="{{ route('vendor.food.updateStock') }}" method="POST">
                                            @method("post")
                                            @csrf
                                            <input type="hidden" value="{{ $food->id }}" name="food_id">
                                            <div class="__bg-F8F9FC-card text-left">
                                                <label class="input-label">{{ translate('Main_Stock') }}</label>
                                                <input type="number" step="1" name="item_stock" value="{{ $food->item_stock }}" required min="1" max="99999999999" class="form-control" placeholder="Ex : 50">
                                            </div>
                                            @if (isset($food->variations) && count(json_decode($food->variations, true)) > 0)
                                                <div class="__bg-F8F9FC-card text-left">
                                                    <div class="row g-2">
                                                        <div class="col-6"><h5>{{ translate('Variation') }}</h5></div>
                                                        <div class="col-6"><h5>{{ translate('Stock') }}</h5></div>
                                                    </div>
                                                    @foreach (json_decode($food->variations, true) as $item)
                                                        <div class="row g-1 mb-3">
                                                            <div class="col-12"><h6 class="m-0">{{ $item['name'] }}</h6></div>
                                                            @if (isset($item['values']) && is_array($item['values']))
                                                                @foreach ($item['values'] as $value)
                                                                    @if (isset($value['option_id']))
                                                                        <div class="col-12">
                                                                            <div class="row g-1 align-items-center">
                                                                                <span class="col-6">{{ $value['label'] }} :</span>
                                                                                <div class="col-6">
                                                                                    <input class="form-control" required value="{{ $value['current_stock'] }}" type="number" min="1" step="1" max="999999999" name="option[{{ $value['option_id'] }}]" placeholder="Ex : 50">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                            <div class="d-flex justify-content-end gap-3 mt-3">
                                                <button type="submit" class="btn btn--primary">{{ translate('Update') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    </tbody>
                </table>

                @if(count($foods) === 0)
                <div class="bj-empty">
                    <img src="{{ dynamicAsset('/public/assets/admin/img/empty.png') }}" alt="empty" style="max-width: 140px; opacity: .7;">
                    <h5>لا توجد أطعمة حتى الآن</h5>
                </div>
                @endif

                <div class="bj-pagination">
                    <div class="page-area">
                        {!! $foods->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        "use strict";
        $(document).on('ready', function () {
            // SELECT2 for category filter
            $('#category').select2({
                ajax: {
                    url: '{{ route("vendor.category.get-all") }}',
                    data: function (params) {
                        return { q: params.term, all: true, page: params.page };
                    },
                    processResults: function (data) { return { results: data }; }
                }
            });

            // View toggle (UI only)
            $('.bj-viewtoggle button').on('click', function () {
                $('.bj-viewtoggle button').removeClass('on');
                $(this).addClass('on');
            });

            // Row checkbox
            $(document).on('click', '.bj-check', function () {
                $(this).toggleClass('checked');
                const $row = $(this).closest('tr');
                if ($row.length) {
                    $row.toggleClass('selected', $(this).hasClass('checked'));
                }
                if ($(this).attr('id') === 'bj-check-all') {
                    const on = $(this).hasClass('checked');
                    $('.row-check').toggleClass('checked', on);
                    $('#set-rows tr').toggleClass('selected', on);
                }
            });

            // Cmd/Ctrl+K to focus search
            $(document).on('keydown', function (e) {
                if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                    e.preventDefault();
                    $('#datatableSearch').focus();
                }
            });
        });

        // Search submit
        $('#search-form').on('submit', function (e) {
            e.preventDefault();
            let formData = new FormData(this);
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });
            $.post({
                url: '{{ route("vendor.food.search") }}',
                data: formData, cache: false, contentType: false, processData: false,
                beforeSend: function () { $('#loading').show(); },
                success: function (data) {
                    $('#set-rows').html(data.view);
                    $('.page-area').hide();
                },
                complete: function () { $('#loading').hide(); }
            });
        });
    </script>
@endpush
