@extends('layouts.vendor.app')

@section('title',translate('messages.deliverymen'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --bj-cream: #f6f1e7;
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
        }

        body { background: var(--bj-cream); }
        .footer { background: transparent; }

        .bj-dm { padding: 1.5rem 1.25rem 2rem; direction: rtl; }

        /* Top bar */
        .bj-topbar {
            display: flex; align-items: center; justify-content: space-between;
            gap: 1rem; flex-wrap: wrap; margin-bottom: 1.25rem;
        }
        .bj-page-head .bj-crumbs {
            font-size: .8rem; color: var(--bj-muted); margin-bottom: .35rem;
        }
        .bj-page-head .bj-crumbs i { font-size: .65rem; margin: 0 .35rem; opacity: .6; }
        .bj-page-head h1 {
            font-size: 1.85rem; font-weight: 800; color: var(--bj-ink); margin: 0 0 .25rem;
        }
        .bj-page-head p { color: var(--bj-muted); font-size: .85rem; margin: 0; }

        .bj-topactions { display: flex; gap: .5rem; align-items: center; flex-wrap: wrap; }
        .bj-pill-btn {
            background: #fff; border: 1px solid var(--bj-line);
            border-radius: 14px; padding: .55rem .9rem;
            font-size: .85rem; font-weight: 600; color: var(--bj-ink);
            display: inline-flex; align-items: center; gap: .4rem;
            transition: all .15s ease;
        }
        .bj-pill-btn:hover { background: var(--bj-soft); color: var(--bj-ink); text-decoration: none; }
        .bj-pill-btn.is-dark { background: var(--bj-ink); color: #fff; border-color: var(--bj-ink); }
        .bj-pill-btn.is-dark:hover { background: var(--bj-ink-2); color: #fff; }

        /* KPI cards */
        .bj-kpis {
            display: grid; grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: .75rem; margin-bottom: 1.25rem;
        }
        .bj-kpi {
            background: #fff; border: 1px solid var(--bj-line);
            border-radius: 18px; padding: .9rem 1rem; position: relative;
        }
        .bj-kpi-head {
            display: flex; align-items: center; gap: .35rem;
            font-size: .8rem; color: var(--bj-muted); font-weight: 600;
            margin-bottom: .45rem;
        }
        .bj-kpi-head .bj-dot { width: 7px; height: 7px; border-radius: 50%; }
        .bj-kpi-value { font-size: 1.85rem; font-weight: 800; color: var(--bj-ink); line-height: 1; margin-bottom: .35rem; }
        .bj-kpi-meta { font-size: .75rem; color: var(--bj-muted); }
        .bj-kpi-meta.is-ok { color: var(--bj-green); font-weight: 600; }
        .bj-kpi-meta.is-warn { color: var(--bj-orange); font-weight: 600; }
        @media (max-width: 1200px) { .bj-kpis { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 720px) { .bj-kpis { grid-template-columns: repeat(2, 1fr); } }

        /* Panel + toolbar */
        .bj-panel {
            background: #fff; border: 1px solid var(--bj-line);
            border-radius: 20px; padding: 1rem 1.1rem; margin-bottom: 1rem;
            box-shadow: 0 1px 2px rgba(15,35,63,0.02);
        }
        .bj-toolbar {
            display: flex; gap: .6rem; align-items: center; flex-wrap: wrap;
        }
        .bj-search {
            flex: 1 1 320px;
            display: flex; align-items: center; gap: .5rem;
            background: var(--bj-soft); border: 1px solid var(--bj-line);
            border-radius: 12px; padding: .55rem .9rem;
        }
        .bj-search input {
            flex: 1; border: 0; background: transparent; outline: none;
            font-size: .85rem; color: var(--bj-ink); direction: rtl; text-align: right;
        }
        .bj-search .bj-kbd {
            font-size: .7rem; color: var(--bj-muted);
            border: 1px solid var(--bj-line); border-radius: 6px;
            padding: .05rem .35rem; background: #fff;
        }
        .bj-tool-btn {
            background: #fff; border: 1px solid var(--bj-line);
            border-radius: 12px; padding: .55rem .85rem;
            font-size: .82rem; font-weight: 600; color: var(--bj-ink);
            display: inline-flex; align-items: center; gap: .4rem;
        }
        .bj-tool-btn i { color: var(--bj-muted); }

        /* Cards grid */
        .bj-dm-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
            gap: .9rem;
        }
        .bj-dm-card {
            background: #fff; border: 1px solid var(--bj-line);
            border-radius: 20px; padding: 1.1rem;
            display: flex; flex-direction: column; gap: .9rem;
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .bj-dm-card:hover { transform: translateY(-2px); box-shadow: 0 8px 22px rgba(15,35,63,0.06); }

        .bj-dm-top { display: flex; align-items: flex-start; gap: .8rem; }
        .bj-dm-avatar {
            position: relative; flex-shrink: 0;
            width: 56px; height: 56px; border-radius: 16px;
            background: var(--bj-soft); overflow: hidden;
            display: flex; align-items: center; justify-content: center;
        }
        .bj-dm-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .bj-dm-avatar .bj-dm-presence {
            position: absolute; bottom: -2px; left: -2px;
            width: 16px; height: 16px; border-radius: 50%;
            border: 2px solid #fff;
        }
        .bj-dm-presence.is-online { background: var(--bj-green); }
        .bj-dm-presence.is-offline { background: #9aa3b2; }
        .bj-dm-presence.is-pending { background: var(--bj-amber); }
        .bj-dm-presence.is-denied { background: var(--bj-red); }

        .bj-dm-info { flex: 1; min-width: 0; }
        .bj-dm-name {
            font-weight: 800; color: var(--bj-ink); font-size: 1rem;
            margin: 0 0 .15rem; text-decoration: none; display: block;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .bj-dm-name:hover { color: var(--bj-blue); text-decoration: none; }
        .bj-dm-rating {
            display: inline-flex; align-items: center; gap: .25rem;
            font-size: .78rem; color: #8c6406; font-weight: 700;
            background: var(--bj-amber-soft); padding: .1rem .45rem;
            border-radius: 8px;
        }
        .bj-dm-rating i { color: var(--bj-amber); font-size: .7rem; }

        .bj-dm-status-pill {
            display: inline-flex; align-items: center; gap: .3rem;
            font-size: .72rem; font-weight: 700;
            padding: .2rem .55rem; border-radius: 8px;
        }
        .bj-dm-status-pill .bj-dot { width: 6px; height: 6px; border-radius: 50%; }
        .bj-dm-status-pill.is-online { background: var(--bj-green-soft); color: #1f7a4a; }
        .bj-dm-status-pill.is-online .bj-dot { background: var(--bj-green); }
        .bj-dm-status-pill.is-offline { background: #eef0f4; color: #475467; }
        .bj-dm-status-pill.is-offline .bj-dot { background: #98a2b3; }
        .bj-dm-status-pill.is-busy { background: var(--bj-blue-soft); color: #1c4ab3; }
        .bj-dm-status-pill.is-busy .bj-dot { background: var(--bj-blue); }
        .bj-dm-status-pill.is-pending { background: var(--bj-amber-soft); color: #8c6406; }
        .bj-dm-status-pill.is-pending .bj-dot { background: var(--bj-amber); }
        .bj-dm-status-pill.is-denied { background: var(--bj-red-soft); color: #a53030; }
        .bj-dm-status-pill.is-denied .bj-dot { background: var(--bj-red); }

        .bj-dm-meta {
            display: flex; flex-direction: column; gap: .35rem;
            font-size: .82rem; color: var(--bj-ink);
        }
        .bj-dm-meta .row-it { display: flex; align-items: center; gap: .5rem; }
        .bj-dm-meta i { color: var(--bj-muted); width: 14px; text-align: center; }
        .bj-dm-meta a { color: var(--bj-ink); text-decoration: none; direction: ltr; text-align: right; }
        .bj-dm-meta a:hover { color: var(--bj-blue); }

        .bj-dm-stats {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: .5rem;
            background: var(--bj-soft);
            border-radius: 14px;
            padding: .7rem;
        }
        .bj-dm-stat { text-align: center; }
        .bj-dm-stat .bj-dm-stat-label {
            font-size: .68rem; color: var(--bj-muted); font-weight: 600;
            margin-bottom: .15rem;
        }
        .bj-dm-stat .bj-dm-stat-value {
            font-size: 1.05rem; font-weight: 800; color: var(--bj-ink); line-height: 1;
        }
        .bj-dm-stat .bj-dm-stat-value.is-active { color: var(--bj-blue); }

        .bj-dm-actions { display: flex; gap: .35rem; margin-top: auto; }
        .bj-act {
            flex: 1;
            height: 36px; border-radius: 10px;
            background: var(--bj-soft); color: var(--bj-ink);
            display: inline-flex; align-items: center; justify-content: center; gap: .3rem;
            border: 1px solid var(--bj-line);
            font-size: .8rem; font-weight: 600;
            text-decoration: none;
        }
        .bj-act:hover { background: var(--bj-ink); color: #fff; text-decoration: none; }
        .bj-act.is-danger { color: var(--bj-red); }
        .bj-act.is-danger:hover { background: var(--bj-red); color: #fff; }

        .bj-empty { padding: 3.5rem 1rem 3rem; text-align: center; background: #fff; border: 1px solid var(--bj-line); border-radius: 20px; }
        .bj-empty img { max-width: 140px; opacity: .75; }
        .bj-empty h5 { margin-top: 1rem; color: var(--bj-muted); font-weight: 600; }

        .bj-footer-bar {
            display: flex; justify-content: space-between; align-items: center;
            padding: 1rem 1.25rem; flex-wrap: wrap; gap: .5rem;
            margin-top: 1rem; background: #fff;
            border: 1px solid var(--bj-line); border-radius: 16px;
        }
        .bj-footer-bar .pagination { margin: 0; }
    </style>
@endpush

@section('content')
<?php
    use Illuminate\Support\Str;
    $bjStats = $dmStats ?? [];
?>

<div class="content container-fluid bj-dm">

    {{-- Top bar --}}
    <div class="bj-topbar">
        <div class="bj-page-head">
            <div class="bj-crumbs">
                <span>لوحة التحكم</span>
                <i class="tio-chevron-left"></i>
                <span>المناديب</span>
            </div>
            <h1>قائمة المناديب</h1>
            <p>إدارة فريق التوصيل، متابعة حالة كل مندوب وعدد الطلبات الفعّالة.</p>
        </div>

        <div class="bj-topactions">
            <a href="{{ route('vendor.delivery-man.list') }}" class="bj-pill-btn">
                <i class="tio-refresh"></i> تحديث
            </a>
            <a href="{{ route('vendor.delivery-man.reviews.list') }}" class="bj-pill-btn">
                <i class="tio-star-outlined"></i> التقييمات
            </a>
            <a href="{{ route('vendor.delivery-man.add') }}" class="bj-pill-btn is-dark">
                <i class="tio-add"></i> إضافة مندوب
            </a>
        </div>
    </div>

    {{-- KPI cards --}}
    <div class="bj-kpis">
        <div class="bj-kpi">
            <div class="bj-kpi-head">
                <span class="bj-dot" style="background: var(--bj-ink);"></span>
                إجمالي المناديب
            </div>
            <div class="bj-kpi-value">{{ $bjStats['total'] ?? 0 }}</div>
            <div class="bj-kpi-meta">في فريق المطعم</div>
        </div>

        <div class="bj-kpi">
            <div class="bj-kpi-head">
                <span class="bj-dot" style="background: var(--bj-green);"></span>
                متصلون الآن
            </div>
            <div class="bj-kpi-value">{{ $bjStats['online'] ?? 0 }}</div>
            <div class="bj-kpi-meta is-ok">جاهزون للتوصيل</div>
        </div>

        <div class="bj-kpi">
            <div class="bj-kpi-head">
                <span class="bj-dot" style="background: var(--bj-blue);"></span>
                مشغول
            </div>
            <div class="bj-kpi-value">{{ $bjStats['busy'] ?? 0 }}</div>
            <div class="bj-kpi-meta">يوصّل طلبات</div>
        </div>

        <div class="bj-kpi">
            <div class="bj-kpi-head">
                <span class="bj-dot" style="background: #9aa3b2;"></span>
                غير متصل
            </div>
            <div class="bj-kpi-value">{{ $bjStats['offline'] ?? 0 }}</div>
            <div class="bj-kpi-meta">خارج الخدمة</div>
        </div>

        <div class="bj-kpi">
            <div class="bj-kpi-head">
                <span class="bj-dot" style="background: var(--bj-amber);"></span>
                بانتظار الاعتماد
            </div>
            <div class="bj-kpi-value">{{ $bjStats['pending'] ?? 0 }}</div>
            <div class="bj-kpi-meta is-warn">يحتاج مراجعة</div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="bj-panel">
        <form class="bj-toolbar" method="GET" action="{{ route('vendor.delivery-man.list') }}">
            <div class="bj-search">
                <i class="tio-search" style="color: var(--bj-muted);"></i>
                <input type="search" name="search" value="{{ request('search') }}"
                       placeholder="ابحث باسم المندوب أو رقم الجوال أو البريد...">
                <span class="bj-kbd">⌘ K</span>
            </div>

            <button type="submit" class="bj-tool-btn">
                <i class="tio-filter-list"></i> بحث
            </button>

            <a href="{{ route('vendor.delivery-man.add') }}" class="bj-tool-btn">
                <i class="tio-add"></i> مندوب جديد
            </a>
        </form>
    </div>

    {{-- Cards grid --}}
    @if(count($delivery_men) > 0)
        <div class="bj-dm-grid">
            @foreach($delivery_men as $key => $dm)
                @php
                    $rating = count($dm->rating) > 0 ? number_format($dm->rating[0]->average, 1, '.', '') : '0.0';
                    $totalOrders = $dm->orders ? count($dm->orders) : 0;
                    $currentOrders = (int)($dm->current_orders ?? 0);

                    if ($dm->application_status === 'approved') {
                        if ($currentOrders > 0) {
                            $presenceClass = 'is-online';
                            $statusClass = 'is-busy';
                            $statusLabel = 'مشغول الآن';
                        } elseif ($dm->active) {
                            $presenceClass = 'is-online';
                            $statusClass = 'is-online';
                            $statusLabel = 'متصل';
                        } else {
                            $presenceClass = 'is-offline';
                            $statusClass = 'is-offline';
                            $statusLabel = 'غير متصل';
                        }
                    } elseif ($dm->application_status === 'denied') {
                        $presenceClass = 'is-denied';
                        $statusClass = 'is-denied';
                        $statusLabel = 'مرفوض';
                    } else {
                        $presenceClass = 'is-pending';
                        $statusClass = 'is-pending';
                        $statusLabel = 'بانتظار الاعتماد';
                    }

                    $fullName = trim($dm['f_name'].' '.$dm['l_name']);
                @endphp
                <div class="bj-dm-card">
                    <div class="bj-dm-top">
                        <div class="bj-dm-avatar">
                            <img class="onerror-image"
                                 data-onerror-image="{{ dynamicAsset('public/assets/admin/img/160x160/img1.jpg') }}"
                                 src="{{ $dm['image_full_url'] }}"
                                 alt="{{ $fullName }}">
                            <span class="bj-dm-presence {{ $presenceClass }}"></span>
                        </div>
                        <div class="bj-dm-info">
                            <a href="{{ route('vendor.delivery-man.preview', [$dm['id']]) }}" class="bj-dm-name">
                                {{ Str::limit($fullName, 22) }}
                            </a>
                            <div class="d-flex flex-wrap" style="gap: .35rem; align-items: center;">
                                <span class="bj-dm-rating"><i class="tio-star"></i> {{ $rating }}</span>
                                <span class="bj-dm-status-pill {{ $statusClass }}">
                                    <span class="bj-dot"></span>{{ $statusLabel }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="bj-dm-meta">
                        <div class="row-it">
                            <i class="tio-phone-outlined"></i>
                            <a href="tel:{{ $dm['phone'] }}">{{ $dm['phone'] }}</a>
                        </div>
                        @if(!empty($dm['email']))
                            <div class="row-it">
                                <i class="tio-mail-outlined"></i>
                                <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $dm['email'] }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="bj-dm-stats">
                        <div class="bj-dm-stat">
                            <div class="bj-dm-stat-label">طلبات نشطة</div>
                            <div class="bj-dm-stat-value is-active">{{ $currentOrders }}</div>
                        </div>
                        <div class="bj-dm-stat">
                            <div class="bj-dm-stat-label">إجمالي الطلبات</div>
                            <div class="bj-dm-stat-value">{{ $totalOrders }}</div>
                        </div>
                    </div>

                    <div class="bj-dm-actions">
                        <a class="bj-act" href="{{ route('vendor.delivery-man.preview', [$dm['id']]) }}" title="عرض">
                            <i class="tio-visible-outlined"></i> عرض
                        </a>
                        <a class="bj-act" href="{{ route('vendor.delivery-man.edit', [$dm['id']]) }}" title="تعديل">
                            <i class="tio-edit"></i> تعديل
                        </a>
                        <a class="bj-act is-danger form-alert" href="javascript:"
                           data-id="delivery-man-{{ $dm['id'] }}"
                           data-message="{{ translate('Want to remove this deliveryman') }}"
                           title="حذف">
                            <i class="tio-delete-outlined"></i>
                        </a>
                        <form action="{{ route('vendor.delivery-man.delete', [$dm['id']]) }}" method="post"
                              id="delivery-man-{{ $dm['id'] }}">
                            @csrf @method('delete')
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="bj-footer-bar">
            <div style="font-size:.8rem; color: var(--bj-muted);">
                إجمالي {{ $delivery_men->total() }} مندوب
            </div>
            <div>{!! $delivery_men->links() !!}</div>
        </div>
    @else
        <div class="bj-empty">
            <img src="{{ dynamicAsset('/public/assets/admin/img/empty.png') }}" alt="empty">
            <h5>{{ translate('no_data_found') }}</h5>
        </div>
    @endif

</div>
@endsection

@push('script_2')
    <script src="{{dynamicAsset('public/assets/admin/js/view-pages/datatable-search.js')}}"></script>
@endpush
