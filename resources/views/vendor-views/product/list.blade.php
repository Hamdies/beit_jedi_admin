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
        }
        body, .content { background: var(--bj-cream) !important; }
        .bj-page { background: var(--bj-cream); padding: 1.25rem 1rem 2rem; }

        /* ============ HEADER ============ */
        .bj-header {
            display: flex; align-items: flex-end; justify-content: space-between;
            gap: 1rem; margin-bottom: 1rem; flex-wrap: wrap;
        }
        .bj-title { font-size: 1.5rem; font-weight: 800; color: var(--bj-ink); margin: 0 0 .25rem; }
        .bj-subtitle { color: var(--bj-muted); font-size: .88rem; margin: 0; }
        .bj-header-actions { display: flex; align-items: center; gap: .5rem; flex-wrap: wrap; }
        .bj-btn-primary {
            background: var(--bj-ink); color: #FFF; border: 0;
            padding: .65rem 1.1rem; border-radius: 12px;
            font-weight: 700; font-size: .88rem;
            display: inline-flex; align-items: center; gap: .4rem;
        }
        .bj-btn-primary:hover { background: #0E1A33; color: #FFF; }
        .bj-btn-ghost {
            background: #FFF; color: var(--bj-ink-2);
            border: 1px solid var(--bj-line);
            padding: .6rem 1rem; border-radius: 12px;
            font-weight: 600; font-size: .85rem;
            display: inline-flex; align-items: center; gap: .35rem;
        }
        .bj-btn-danger-soft {
            background: var(--bj-red-soft); color: var(--bj-red);
            border: 0; padding: .6rem 1rem; border-radius: 12px;
            font-weight: 700; font-size: .85rem;
            display: inline-flex; align-items: center; gap: .35rem;
        }

        /* ============ STATS STRIP ============ */
        .bj-stats {
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: .6rem; margin-bottom: 1rem;
        }
        .bj-stat {
            background: var(--bj-card); border: 1px solid var(--bj-line);
            border-radius: 14px; padding: .8rem 1rem;
            display: flex; align-items: center; justify-content: space-between;
        }
        .bj-stat-label { font-size: .8rem; color: var(--bj-muted); font-weight: 600; }
        .bj-stat-value { font-size: 1.5rem; font-weight: 800; color: var(--bj-ink); font-feature-settings: "tnum"; }
        .bj-stat .ind { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-left: .35rem; }

        /* ============ CATEGORY GRID (all categories, wraps) ============ */
        .bj-cat-wrap {
            background: var(--bj-card); border: 1px solid var(--bj-line);
            border-radius: 16px; padding: .85rem 1rem; margin-bottom: 1rem;
        }
        .bj-cat-head {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: .6rem;
        }
        .bj-cat-head h6 { margin: 0; font-size: .85rem; font-weight: 700; color: var(--bj-ink-2); }
        .bj-cat-search {
            display: flex; align-items: center; gap: .4rem;
            background: var(--bj-cream-soft); border: 1px solid var(--bj-line-soft);
            border-radius: 10px; padding: .35rem .7rem; max-width: 220px;
        }
        .bj-cat-search input {
            border: 0; background: transparent; outline: none;
            font-size: .82rem; color: var(--bj-ink-2); text-align: right; width: 100%;
        }
        .bj-cat-list {
            display: flex; flex-wrap: wrap; gap: .4rem;
        }
        .bj-cat-pill {
            display: inline-flex; align-items: center; gap: .45rem;
            padding: .45rem .85rem; border-radius: 10px;
            font-size: .83rem; font-weight: 600; color: var(--bj-ink-2);
            background: var(--bj-cream-soft); border: 1px solid transparent;
            white-space: nowrap; cursor: pointer; transition: .15s;
        }
        .bj-cat-pill:hover { background: #FFF; border-color: var(--bj-line); color: var(--bj-ink); }
        .bj-cat-pill.active { background: var(--bj-ink); color: #FFF; }
        .bj-cat-pill .count {
            background: rgba(0,0,0,0.06); color: inherit;
            padding: .1rem .45rem; border-radius: 999px;
            font-size: .72rem; font-weight: 700;
        }
        .bj-cat-pill.active .count { background: rgba(255,255,255,0.18); }
        .bj-cat-pill.hidden { display: none; }

        /* ============ TOOLBAR ============ */
        .bj-toolbar {
            display: flex; align-items: center; justify-content: space-between;
            gap: .75rem; padding: .75rem 1rem;
            background: var(--bj-card); border: 1px solid var(--bj-line);
            border-radius: 14px; margin-bottom: 1rem; flex-wrap: wrap;
        }
        .bj-search {
            flex: 1; min-width: 220px; max-width: 520px;
            display: flex; align-items: center; gap: .5rem;
            background: var(--bj-cream-soft); border: 1px solid var(--bj-line-soft);
            border-radius: 12px; padding: .5rem .8rem;
        }
        .bj-search input {
            flex: 1; border: 0; background: transparent; outline: none;
            font-size: .9rem; color: var(--bj-ink-2); text-align: right;
        }
        .bj-search input::placeholder { color: var(--bj-muted); }
        .bj-select {
            background: var(--bj-cream-soft); border: 1px solid var(--bj-line-soft);
            border-radius: 12px; padding: .5rem .8rem;
            font-size: .85rem; color: var(--bj-ink-2); font-weight: 600;
        }

        /* ============ TABLE ============ */
        .bj-table-wrap {
            background: var(--bj-card); border: 1px solid var(--bj-line);
            border-radius: 16px; overflow: hidden;
        }
        .bj-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .bj-table thead th {
            background: transparent; padding: .85rem 1rem;
            font-size: .78rem; font-weight: 700; color: var(--bj-muted);
            text-align: right; border-bottom: 1px solid var(--bj-line-soft);
            white-space: nowrap;
        }
        .bj-table thead th.center { text-align: center; }
        .bj-table tbody td {
            padding: .85rem 1rem; vertical-align: middle;
            border-bottom: 1px solid var(--bj-line-soft);
        }
        .bj-table tbody tr:hover td { background: rgba(232,221,189,0.18); }
        .bj-table tbody tr:last-child td { border-bottom: 0; }
        .bj-idx { color: var(--bj-muted); font-weight: 700; font-size: .88rem; }

        .bj-food-cell { display: flex; align-items: center; gap: .75rem; }
        .bj-food-thumb {
            width: 44px; height: 44px; border-radius: 10px;
            background: var(--bj-cream-soft); border: 1px solid var(--bj-line-soft);
            object-fit: cover; flex-shrink: 0;
        }
        .bj-food-name { font-weight: 700; color: var(--bj-ink); font-size: .92rem; margin: 0 0 .15rem; }
        .bj-food-name a { color: inherit; }
        .bj-badge {
            display: inline-flex; align-items: center;
            padding: .12rem .5rem; border-radius: 999px;
            font-size: .68rem; font-weight: 700; line-height: 1.4;
        }
        .bj-badge.gold { background: var(--bj-gold-soft); color: #8E6E1F; }
        .bj-badge.red  { background: var(--bj-red-soft); color: var(--bj-red); }

        .bj-cat-tag {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .3rem .65rem; border-radius: 999px;
            background: var(--bj-cream-soft); color: var(--bj-ink-2);
            font-size: .8rem; font-weight: 600;
        }
        .bj-cat-tag .cdot { width: 7px; height: 7px; border-radius: 50%; }

        .bj-price { font-weight: 800; color: var(--bj-ink); font-size: .92rem; font-feature-settings: "tnum"; }

        .bj-stock-pill {
            display: inline-flex; align-items: center; gap: .35rem;
            padding: .3rem .65rem; border-radius: 999px;
            font-size: .76rem; font-weight: 700;
        }
        .bj-stock-pill.ok { background: var(--bj-green-soft); color: #1D6B3D; }
        .bj-stock-pill.low { background: var(--bj-amber-soft); color: #8A5A14; }
        .bj-stock-pill.out { background: var(--bj-red-soft); color: var(--bj-red); }

        .bj-row-actions { display: flex; align-items: center; gap: .35rem; justify-content: flex-end; }
        .bj-row-icon {
            width: 30px; height: 30px; border-radius: 8px;
            border: 1px solid var(--bj-line); background: #FFF;
            display: inline-flex; align-items: center; justify-content: center;
            color: var(--bj-muted); font-size: .9rem;
        }
        .bj-row-icon:hover { color: var(--bj-ink); }
        .bj-row-icon.danger { color: var(--bj-red); border-color: var(--bj-red-soft); background: #FFF7F3; }

        .bj-empty { padding: 3.5rem 1rem; text-align: center; }
        .bj-empty h5 { margin-top: 1rem; color: var(--bj-muted); font-weight: 600; }

        /* Inline price editor */
        .bj-price-cell { min-width: 110px; }
        .bj-price-edit { display: flex; align-items: center; gap: .3rem; }
        .bj-price-input {
            width: 90px; padding: .35rem .5rem;
            border: 1px solid var(--bj-line); border-radius: 8px;
            background: #FFF; color: var(--bj-ink);
            font-size: .88rem; font-weight: 700; font-feature-settings: "tnum";
            text-align: right; outline: none;
        }
        .bj-price-input:focus { border-color: var(--bj-ink); box-shadow: 0 0 0 3px rgba(21,35,63,0.08); }
        .bj-price-btn {
            width: 28px; height: 28px; border-radius: 8px;
            border: 1px solid var(--bj-line); background: #FFF;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: .85rem; cursor: pointer; padding: 0;
        }
        .bj-price-btn.save { color: var(--bj-green); border-color: var(--bj-green-soft); background: #F1FAF4; }
        .bj-price-btn.save:hover { background: var(--bj-green); color: #FFF; }
        .bj-price-btn.cancel { color: var(--bj-muted); }
        .bj-price-btn:disabled { opacity: .5; cursor: wait; }
        .js-edit-price { cursor: pointer; }
        .js-edit-price.editing { background: var(--bj-ink); color: #FFF; border-color: var(--bj-ink); }

        .bj-pagination { padding: 1rem; }
        .bj-pagination .page-area ul { justify-content: center; margin: 0; }

        @media (max-width: 992px) {
            .bj-stats { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 640px) {
            .bj-header { flex-direction: column; align-items: stretch; }
            .bj-title { font-size: 1.2rem; }
        }
    </style>
@endpush

@section('content')
@php
    $totalFoods   = $stats['total'] ?? $foods->total();
    $activeFoods  = $stats['active'] ?? 0;
    $outOfStock   = $stats['out_of_stock'] ?? 0;
    $recommended  = $stats['recommended'] ?? 0;

    $catDotColors = ['#D4593A', '#C8A85B', '#2F9D5C', '#2A4D8C', '#8A5A14', '#7C3AED', '#0EA5E9', '#E0A33A'];
@endphp

    <div class="bj-page">
        {{-- ============ HEADER ============ --}}
        <div class="bj-header">
            <div>
                <h1 class="bj-title">قائمة الأطعمة</h1>
                <p class="bj-subtitle">إدارة أطباق مطعمك بسهولة في واجهة واحدة.</p>
            </div>
            <div class="bj-header-actions">
                @if($outOfStock > 0)
                    <a href="{{ route('vendor.food.stockOutList') }}" class="bj-btn-danger-soft">
                        <i class="tio-notifications-on"></i> النافدة ({{ $outOfStock }})
                    </a>
                @endif
                <a href="{{ route('vendor.food.bulk-export-index') }}" class="bj-btn-ghost">
                    <i class="tio-download"></i> تصدير
                </a>
                <a href="{{ route('vendor.food.add-new') }}" class="bj-btn-primary">
                    <i class="tio-add"></i> إضافة طبق جديد
                </a>
            </div>
        </div>

        {{-- ============ STATS ============ --}}
        <div class="bj-stats">
            <div class="bj-stat">
                <span class="bj-stat-label"><span class="ind" style="background: var(--bj-ink-2);"></span>إجمالي الأطباق</span>
                <span class="bj-stat-value">{{ $totalFoods }}</span>
            </div>
            <div class="bj-stat">
                <span class="bj-stat-label"><span class="ind" style="background: var(--bj-green);"></span>نشطة</span>
                <span class="bj-stat-value">{{ $activeFoods }}</span>
            </div>
            <div class="bj-stat">
                <span class="bj-stat-label"><span class="ind" style="background: var(--bj-amber);"></span>نافدة المخزون</span>
                <span class="bj-stat-value">{{ $outOfStock }}</span>
            </div>
            <div class="bj-stat">
                <span class="bj-stat-label"><span class="ind" style="background: var(--bj-gold);"></span>موصى بها</span>
                <span class="bj-stat-value">{{ $recommended }}</span>
            </div>
        </div>

        {{-- ============ ALL CATEGORIES (wraps, searchable) ============ --}}
        <div class="bj-cat-wrap">
            <div class="bj-cat-head">
                <h6>التصنيفات ({{ count($top_categories) }})</h6>
                <div class="bj-cat-search">
                    <i class="tio-search" style="color: var(--bj-muted); font-size: .85rem;"></i>
                    <input id="bj-cat-filter" type="search" placeholder="ابحث في التصنيفات">
                </div>
            </div>
            <div class="bj-cat-list" id="bj-cat-list">
                <a href="{{ route('vendor.food.list') }}"
                   class="bj-cat-pill {{ !$category ? 'active' : '' }}"
                   data-name="كل التصنيفات">
                    الكل <span class="count">{{ $totalFoods }}</span>
                </a>
                @foreach($top_categories as $cat)
                    <a href="{{ route('vendor.food.list', ['category_id' => $cat->id]) }}"
                       class="bj-cat-pill {{ $category && $category->id == $cat->id ? 'active' : '' }}"
                       data-name="{{ $cat->name }}">
                        {{ $cat->name }}
                        <span class="count">{{ $cat->products_count }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- ============ TOOLBAR ============ --}}
        <div class="bj-toolbar">
            <form id="search-form" class="bj-search" style="margin: 0;">
                @csrf
                <i class="tio-search" style="color: var(--bj-muted);"></i>
                <input id="datatableSearch" type="search" name="search" placeholder="ابحث باسم الطبق أو رقم الطبق" aria-label="بحث">
            </form>
            @if (!empty($toggle_veg_non_veg) && $toggle_veg_non_veg)
            <select name="type" data-url="{{ url()->full() }}" data-filter="type" class="bj-select set-filter">
                <option value="all" {{ $type=='all'?'selected':'' }}>الكل</option>
                <option value="veg" {{ $type=='veg'?'selected':'' }}>نباتي</option>
                <option value="non_veg" {{ $type=='non_veg'?'selected':'' }}>غير نباتي</option>
            </select>
            @endif
        </div>

        {{-- ============ TABLE ============ --}}
        <div class="bj-table-wrap">
            <div class="table-responsive">
                <table id="datatable" class="bj-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">م</th>
                            <th>اسم الطبق</th>
                            <th>التصنيف</th>
                            <th>السعر</th>
                            <th>المخزون</th>
                            <th class="center">موصى به</th>
                            <th class="center">الحالة</th>
                            <th style="width: 120px;"></th>
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
                            $is_new = $food->created_at && $food->created_at->gt(now()->subDays(7));
                        @endphp
                        <tr data-food-id="{{ $food->id }}">
                            <td><span class="bj-idx">{{ $key + $foods->firstItem() }}</span></td>
                            <td>
                                <div class="bj-food-cell">
                                    <img class="bj-food-thumb onerror-image"
                                         src="{{ $food['image_full_url'] }}"
                                         data-onerror-image="{{ dynamicAsset('public/assets/admin/img/100x100/food-default-image.png') }}"
                                         alt="{{ $food->name }}">
                                    <div>
                                        <h5 class="bj-food-name">
                                            <a href="{{ route('vendor.food.view', [$food->id]) }}">{{ Str::limit($food->name, 32) }}</a>
                                        </h5>
                                        <div style="display: flex; gap: .25rem; flex-wrap: wrap;">
                                            @if($is_new)
                                                <span class="bj-badge gold">جديد</span>
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
                                <div class="bj-price-cell" data-food-id="{{ $food->id }}">
                                    <div class="bj-price js-price-view">{{ \App\CentralLogics\Helpers::format_currency($food->price) }}</div>
                                    <div class="bj-price-edit js-price-edit" style="display:none;">
                                        <input type="number" step="0.01" min="0"
                                               class="bj-price-input js-price-input"
                                               value="{{ $food->price }}">
                                        <button type="button" class="bj-price-btn save js-price-save" title="حفظ">
                                            <i class="tio-checkmark"></i>
                                        </button>
                                        <button type="button" class="bj-price-btn cancel js-price-cancel" title="إلغاء">
                                            <i class="tio-clear"></i>
                                        </button>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($stock_out)
                                    <span class="bj-stock-pill out">نافد</span>
                                @elseif($food->stock_type == 'unlimited')
                                    <span class="bj-stock-pill ok">غير محدود</span>
                                @elseif($stock_remaining > 0 && $stock_remaining < 50)
                                    <span class="bj-stock-pill low">منخفض · {{ $stock_remaining }}</span>
                                @else
                                    <span class="bj-stock-pill ok">{{ $stock_remaining }}</span>
                                @endif
                            </td>
                            <td class="center">
                                <label class="toggle-switch toggle-switch-sm" for="recCheckbox{{ $food->id }}" title="{{ translate('messages.Recommend_to_customers') }}">
                                    <input type="checkbox" data-url="{{ route('vendor.food.recommended', [$food->id, $food->recommended ? 0 : 1]) }}" class="toggle-switch-input redirect-url" id="recCheckbox{{ $food->id }}" {{ $food->recommended ? 'checked' : '' }}>
                                    <span class="toggle-switch-label"><span class="toggle-switch-indicator"></span></span>
                                </label>
                            </td>
                            <td class="center">
                                <label class="toggle-switch toggle-switch-sm" for="statusCheckbox{{ $food->id }}" title="{{ translate('messages.Change_food_visibility_to_customers') }}">
                                    <input type="checkbox" data-url="{{ route('vendor.food.status', [$food->id, $food->status ? 0 : 1]) }}" class="toggle-switch-input redirect-url" id="statusCheckbox{{ $food->id }}" {{ $food->status ? 'checked' : '' }}>
                                    <span class="toggle-switch-label"><span class="toggle-switch-indicator"></span></span>
                                </label>
                            </td>
                            <td>
                                <div class="bj-row-actions">
                                    @if($stock_out)
                                    <a class="bj-row-icon danger" href="#update-stock{{ $food->id }}" data-toggle="modal" title="تحديث المخزون">
                                        <i class="tio-autorenew"></i>
                                    </a>
                                    @endif
                                    <button type="button" class="bj-row-icon js-edit-price" data-food-id="{{ $food->id }}" title="تعديل السعر">
                                        <i class="tio-dollar"></i>
                                    </button>
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

        // ===== Inline price edit =====
        const updatePriceUrl = '{{ route("vendor.food.updatePrice") }}';
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        function togglePriceEditor($row, on) {
            const $cell = $row.find('.bj-price-cell');
            const $view = $cell.find('.js-price-view');
            const $edit = $cell.find('.js-price-edit');
            const $btn  = $row.find('.js-edit-price');
            if (on) {
                $view.hide(); $edit.css('display', 'flex');
                $btn.addClass('editing');
                $cell.find('.js-price-input').focus().select();
            } else {
                $edit.hide(); $view.show();
                $btn.removeClass('editing');
            }
        }

        $(document).on('click', '.js-edit-price', function () {
            const $row = $(this).closest('tr');
            const isOpen = $(this).hasClass('editing');
            togglePriceEditor($row, !isOpen);
        });

        $(document).on('click', '.js-price-cancel', function () {
            const $row = $(this).closest('tr');
            const $cell = $row.find('.bj-price-cell');
            // Reset input to displayed value if it was previously saved
            const original = $cell.data('original-price');
            if (typeof original !== 'undefined') {
                $cell.find('.js-price-input').val(original);
            }
            togglePriceEditor($row, false);
        });

        $(document).on('keydown', '.js-price-input', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); $(this).closest('.bj-price-edit').find('.js-price-save').click(); }
            if (e.key === 'Escape') { $(this).closest('tr').find('.js-price-cancel').click(); }
        });

        $(document).on('click', '.js-price-save', function () {
            const $btn = $(this);
            const $row = $btn.closest('tr');
            const $cell = $row.find('.bj-price-cell');
            const foodId = $cell.data('food-id');
            const price = parseFloat($cell.find('.js-price-input').val());
            if (!(price >= 0) || isNaN(price)) {
                toastr ? toastr.error('سعر غير صحيح') : alert('سعر غير صحيح');
                return;
            }
            $btn.prop('disabled', true);
            $.ajax({
                url: updatePriceUrl,
                method: 'POST',
                data: { _token: csrfToken, food_id: foodId, price: price },
                success: function (res) {
                    if (res && res.success) {
                        $cell.find('.js-price-view').text(res.price_formatted);
                        $cell.data('original-price', res.price);
                        $cell.find('.js-price-input').val(res.price);
                        togglePriceEditor($row, false);
                        if (window.toastr) toastr.success('تم تحديث السعر');
                    }
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.message || 'تعذر تحديث السعر';
                    if (window.toastr) toastr.error(msg); else alert(msg);
                },
                complete: function () { $btn.prop('disabled', false); }
            });
        });

        // Live-filter the category pills as the user types
        $(document).on('input', '#bj-cat-filter', function () {
            const q = $(this).val().trim().toLowerCase();
            $('#bj-cat-list .bj-cat-pill').each(function () {
                const name = ($(this).data('name') || '').toString().toLowerCase();
                $(this).toggleClass('hidden', q !== '' && name.indexOf(q) === -1);
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
