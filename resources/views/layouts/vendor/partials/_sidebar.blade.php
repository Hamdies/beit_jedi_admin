@php
    $restaurant_data = \App\CentralLogics\Helpers::get_restaurant_data();
    $bj_order_count  = \App\Models\Order::where('restaurant_id', \App\CentralLogics\Helpers::get_restaurant_id())
        ->whereNotIn('order_status', ['delivered','failed','canceled','refunded','refund_requested'])
        ->Notpos()->HasSubscriptionToday()->NotDigitalOrder()->count();
    $bj_msg_count = 0;
    try {
        $bj_vendor = auth('vendor')->user();
        if ($bj_vendor) {
            $bj_msg_count = \App\Models\Conversation::where(function($q) use ($bj_vendor) {
                $q->where('sender_id', $bj_vendor->id)->orWhere('receiver_id', $bj_vendor->id);
            })->where('unread_message_count', '>', 0)->count();
        }
    } catch(\Exception $e) {}
@endphp

<style>
/* ═══════════════════════════════════════════════
   BEIT JEDI — GLOBAL SIDEBAR
   Navy #1a2447 · Gold #c9a96b
═══════════════════════════════════════════════ */
:root {
    --bj-sb-bg:       #1a2447;
    --bj-sb-deep:     #0f1a3a;
    --bj-sb-hover:    rgba(255,255,255,.06);
    --bj-sb-active:   rgba(255,255,255,.10);
    --bj-sb-gold:     #c9a96b;
    --bj-sb-text:     rgba(255,255,255,.62);
    --bj-sb-white:    #ffffff;
    --bj-sb-mute:     rgba(255,255,255,.35);
    --bj-sb-r:        10px;
    --bj-sb-badge-pending: #e89556;
    --bj-sb-badge-msg:     #5a87d6;
}

/* ── Shell: suppress framework chrome globally ── */
.js-navbar-vertical-aside,
.navbar-vertical-aside,
#sidebarCompact,
#bj-header,
.navbar-vertical-aside-mobile-overlay { display: none !important; }

/* main#content gets no left padding since we own the layout */
main#content {
    padding-right: 0 !important;
    margin-right: 0 !important;
}

/* ── App grid shell ── */
body.bj-shell {
    background: #faf7f2 !important;
    overflow-x: hidden;
}
body.bj-shell main#content {
    padding: 0 !important;
    margin: 0 !important;
    background: #faf7f2 !important;
}
body.bj-shell .footer { display: none !important; }

.bj-app-shell {
    display: grid;
    grid-template-columns: 260px 1fr;
    min-height: 100vh;
    direction: rtl;
    font-family: 'Cairo', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* ── Sidebar ── */
.bj-sidebar {
    background: var(--bj-sb-bg);
    width: 260px;
    padding: 28px 22px;
    display: flex;
    flex-direction: column;
    gap: 28px;
    position: sticky;
    top: 0;
    height: 100vh;
    overflow-y: auto;
    scrollbar-width: none;
    flex-shrink: 0;
    z-index: 100;
}
.bj-sidebar::-webkit-scrollbar { display: none; }

/* Brand */
.bj-sb-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
}
.bj-sb-brand .mark {
    width: 36px; height: 36px;
    background: var(--bj-sb-gold);
    border-radius: var(--bj-sb-r);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.05rem; font-weight: 900;
    color: var(--bj-sb-bg);
    flex-shrink: 0;
    overflow: hidden;
}
.bj-sb-brand .mark img { width: 100%; height: 100%; object-fit: cover; border-radius: var(--bj-sb-r); }
.bj-sb-brand .titles { line-height: 1.2; }
.bj-sb-brand .titles .name {
    font-size: .95rem; font-weight: 800;
    color: var(--bj-sb-white);
}
.bj-sb-brand .titles .sub {
    font-size: .72rem; color: var(--bj-sb-mute);
    font-weight: 500;
}

/* Nav section */
.bj-sb-sec { display: flex; flex-direction: column; gap: 4px; }
.bj-sb-sec > .lbl {
    font-size: .68rem; font-weight: 700;
    color: var(--bj-sb-mute);
    text-transform: uppercase;
    letter-spacing: .06em;
    padding: 0 6px;
    margin-bottom: 4px;
}

/* Nav item */
.bj-sb-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 10px;
    border-radius: var(--bj-sb-r);
    color: var(--bj-sb-text);
    font-size: .875rem; font-weight: 600;
    text-decoration: none;
    transition: background .12s, color .12s;
    position: relative;
    white-space: nowrap;
}
.bj-sb-item:hover { background: var(--bj-sb-hover); color: rgba(255,255,255,.9); text-decoration: none; }
.bj-sb-item.active { background: var(--bj-sb-active); color: var(--bj-sb-white); }
.bj-sb-item .ico { width: 18px; text-align: center; font-size: 16px; flex-shrink: 0; }
.bj-sb-item .bdg {
    margin-right: auto;
    margin-left: 0;
    font-size: .6rem; font-weight: 800;
    min-width: 18px; height: 18px;
    border-radius: 999px;
    padding: 0 5px;
    display: inline-flex; align-items: center; justify-content: center;
    color: #fff;
    flex-shrink: 0;
}
.bj-sb-item .bdg.orders { background: var(--bj-sb-badge-pending); }
.bj-sb-item .bdg.msgs   { background: var(--bj-sb-badge-msg); }

/* ── Mobile drawer backdrop ── */
.bj-sb-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15, 26, 58, .55);
    backdrop-filter: blur(2px);
    z-index: 1099;
    opacity: 0;
    transition: opacity .25s ease;
}
.bj-sb-backdrop.show { opacity: 1; }

/* ── Responsive: ≤1280px narrow sidebar ── */
@media (max-width: 1280px) and (min-width: 1025px) {
    .bj-app-shell { grid-template-columns: 220px 1fr; }
    .bj-sidebar { width: 220px; padding: 24px 16px; }
}

/* ── Responsive: ≤1024px sidebar becomes off-canvas drawer ── */
@media (max-width: 1024px) {
    .bj-app-shell {
        grid-template-columns: 1fr;
        display: block;
    }
    .bj-sidebar {
        position: fixed;
        top: 0;
        right: 0;                    /* RTL: slides from right */
        left: auto;
        height: 100vh;
        width: 280px;
        max-width: 86vw;
        padding: 20px 16px;
        z-index: 1100;
        transform: translateX(100%);  /* hidden off-screen to the right */
        transition: transform .28s cubic-bezier(.22,1,.36,1);
        box-shadow: -12px 0 32px rgba(15,26,58,.25);
        display: flex !important;     /* override the earlier `display: none` for chrome suppression */
    }
    /* Suppress framework chrome ONLY — not our drawer */
    .js-navbar-vertical-aside,
    .navbar-vertical-aside,
    #sidebarCompact,
    .navbar-vertical-aside-mobile-overlay { display: none !important; }

    .bj-sidebar.open { transform: translateX(0); }
    .bj-sb-backdrop.show { display: block; }

    /* LTR fallback: slide from left */
    html[dir="ltr"] .bj-sidebar {
        right: auto;
        left: 0;
        transform: translateX(-100%);
        box-shadow: 12px 0 32px rgba(15,26,58,.25);
    }
    html[dir="ltr"] .bj-sidebar.open { transform: translateX(0); }

    body.bj-drawer-open { overflow: hidden; }
}
</style>

<aside class="bj-sidebar">
    {{-- Brand --}}
    <a href="{{ route('vendor.dashboard') }}" class="bj-sb-brand">
        <div class="mark">
            @if($restaurant_data?->logo_full_url)
                <img src="{{ $restaurant_data->logo_full_url }}" alt="{{ $restaurant_data->name ?? 'بيت جدي' }}">
            @else
                ج
            @endif
        </div>
        <div class="titles">
            <div class="name">{{ \Illuminate\Support\Str::limit($restaurant_data->name ?? 'بيت جدي', 18) }}</div>
            <div class="sub">لوحة التحكم</div>
        </div>
    </a>

    {{-- Primary --}}
    <nav class="bj-sb-sec">
        <span class="lbl">الرئيسية</span>
        <a href="{{ route('vendor.dashboard') }}"
           class="bj-sb-item {{ Request::is('restaurant-panel') ? 'active' : '' }}">
            <span class="ico"><i class="tio-home-outlined"></i></span>
            لوحة التحكم
        </a>

        @if(\App\CentralLogics\Helpers::employee_module_permission_check('order'))
        <a href="{{ route('vendor.order.list', ['all']) }}"
           class="bj-sb-item {{ Request::is('restaurant-panel/order*') && !Request::is('restaurant-panel/order/subscription*') ? 'active' : '' }}">
            <span class="ico"><i class="tio-receipt"></i></span>
            الطلبات
            @if($bj_order_count > 0)
                <span class="bdg orders">{{ $bj_order_count }}</span>
            @endif
        </a>
        @endif

        @if(\App\CentralLogics\Helpers::employee_module_permission_check('deliveryman'))
        <a href="{{ route('vendor.delivery-man.list') }}"
           class="bj-sb-item {{ Request::is('restaurant-panel/delivery-man*') ? 'active' : '' }}">
            <span class="ico"><i class="tio-delivery"></i></span>
            التوصيل
        </a>
        @endif

        @if(\App\CentralLogics\Helpers::employee_module_permission_check('chat'))
        <a href="{{ route('vendor.message.list', ['tab' => 'customer']) }}"
           class="bj-sb-item {{ Request::is('restaurant-panel/message*') ? 'active' : '' }}">
            <span class="ico"><i class="tio-chat-outlined"></i></span>
            الرسائل
            @if($bj_msg_count > 0)
                <span class="bdg msgs">{{ $bj_msg_count }}</span>
            @endif
        </a>
        @endif

        @if(\App\CentralLogics\Helpers::employee_module_permission_check('pos'))
        <a href="{{ route('vendor.pos.index') }}"
           class="bj-sb-item {{ Request::is('restaurant-panel/pos*') ? 'active' : '' }}">
            <span class="ico"><i class="tio-shopping"></i></span>
            نقطة البيع
        </a>
        @endif
    </nav>

    {{-- Menu --}}
    @if(\App\CentralLogics\Helpers::employee_module_permission_check('food'))
    <nav class="bj-sb-sec">
        <span class="lbl">القائمة</span>
        <a href="{{ route('vendor.food.list') }}"
           class="bj-sb-item {{ Request::is('restaurant-panel/food*') ? 'active' : '' }}">
            <span class="ico"><i class="tio-restaurant-menu"></i></span>
            الأطباق
        </a>
        <a href="{{ route('vendor.category.add') }}"
           class="bj-sb-item {{ Request::is('restaurant-panel/category*') ? 'active' : '' }}">
            <span class="ico"><i class="tio-grid"></i></span>
            التصنيفات
        </a>
        @if(\App\CentralLogics\Helpers::employee_module_permission_check('addon'))
        <a href="{{ route('vendor.addon.add-new') }}"
           class="bj-sb-item {{ Request::is('restaurant-panel/addon*') ? 'active' : '' }}">
            <span class="ico"><i class="tio-add-circle-outlined"></i></span>
            الإضافات
        </a>
        @endif
        @if(\App\CentralLogics\Helpers::employee_module_permission_check('coupon'))
        <a href="{{ route('vendor.coupon.add-new') }}"
           class="bj-sb-item {{ Request::is('restaurant-panel/coupon*') ? 'active' : '' }}">
            <span class="ico"><i class="tio-ticket"></i></span>
            القسائم
        </a>
        @endif
        <a href="{{ route('vendor.campaign.list') }}"
           class="bj-sb-item {{ Request::is('restaurant-panel/campaign*') ? 'active' : '' }}">
            <span class="ico"><i class="tio-label-outlined"></i></span>
            العروض
        </a>
    </nav>
    @endif

    {{-- Management --}}
    <nav class="bj-sb-sec">
        <span class="lbl">الإدارة</span>
        @if(\App\CentralLogics\Helpers::employee_module_permission_check('reviews'))
        <a href="{{ route('vendor.reviews') }}"
           class="bj-sb-item {{ Request::is('restaurant-panel/reviews*') ? 'active' : '' }}">
            <span class="ico"><i class="tio-star-outlined"></i></span>
            التقييمات
        </a>
        @endif
        @if(\App\CentralLogics\Helpers::employee_module_permission_check('my_shop'))
        <a href="{{ route('vendor.shop.view') }}"
           class="bj-sb-item {{ Request::is('restaurant-panel/restaurant/view*') ? 'active' : '' }}">
            <span class="ico"><i class="tio-home"></i></span>
            متجري
        </a>
        @endif
        @if(\App\CentralLogics\Helpers::employee_module_permission_check('report'))
        <a href="{{ route('vendor.report.order-report') }}"
           class="bj-sb-item {{ Request::is('restaurant-panel/report*') ? 'active' : '' }}">
            <span class="ico"><i class="tio-chart-bar-1"></i></span>
            التقارير
        </a>
        @endif
        @if(\App\CentralLogics\Helpers::employee_module_permission_check('restaurant_setup'))
        <a href="{{ route('vendor.business-settings.restaurant-setup') }}"
           class="bj-sb-item {{ Request::is('restaurant-panel/business-settings*') ? 'active' : '' }}">
            <span class="ico"><i class="tio-settings-outlined"></i></span>
            الإعدادات
        </a>
        @endif
    </nav>
</aside>

{{-- Mobile drawer backdrop --}}
<div class="bj-sb-backdrop" id="bjSidebarBackdrop" aria-hidden="true"></div>

<script>
(function(){
    var sidebar = document.querySelector('.bj-sidebar');
    var backdrop = document.getElementById('bjSidebarBackdrop');
    if (!sidebar || !backdrop) return;

    function open(){
        sidebar.classList.add('open');
        backdrop.classList.add('show');
        document.body.classList.add('bj-drawer-open');
    }
    function close(){
        sidebar.classList.remove('open');
        backdrop.classList.remove('show');
        document.body.classList.remove('bj-drawer-open');
    }
    function toggle(){
        sidebar.classList.contains('open') ? close() : open();
    }

    // Hamburger in #bj-header
    document.addEventListener('click', function(e){
        var btn = e.target.closest('.bj-sidebar-toggle, .js-navbar-vertical-aside-toggle-invoker, [data-bj-sidebar-toggle]');
        if (btn) {
            e.preventDefault();
            toggle();
        }
    });

    // Backdrop tap closes
    backdrop.addEventListener('click', close);

    // Close on nav link tap (mobile)
    sidebar.addEventListener('click', function(e){
        if (window.innerWidth > 1024) return;
        var link = e.target.closest('a.bj-sb-item');
        if (link) setTimeout(close, 80);  // brief delay so the visual press registers
    });

    // ESC closes
    document.addEventListener('keydown', function(e){
        if (e.key === 'Escape') close();
    });

    // Resize → reset
    window.addEventListener('resize', function(){
        if (window.innerWidth > 1024) close();
    });

    // Expose for any other caller
    window.bjSidebarToggle = toggle;
    window.bjSidebarOpen = open;
    window.bjSidebarClose = close;
})();
</script>
