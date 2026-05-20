@extends('layouts.vendor.app')

@section('title', translate('messages.dashboard'))

@push('css_or_js')
<style>
/* ═══════════════════════════════════════════════
   BEIT JEDI — RESTAURANT DASHBOARD v2
   Pixel-matched to AIDesigner reference
═══════════════════════════════════════════════ */
:root {
    /* Palette */
    --cream:            #faf7f2;
    --ivory:            #f5f1e8;
    --paper:            #ffffff;
    --hairline:         #ecdfc8;
    --hairline-soft:    #f0e8d6;
    --navy-900:         #0f1a3a;
    --navy-800:         #1a2447;
    --navy-700:         #2a3560;
    --navy-600:         #3d4a7a;
    --navy-100:         #e7eaf3;
    --navy-50:          #f1f3f9;
    --gold-50:          #fbf6ea;
    --gold-100:         #f5ecd9;
    --gold-500:         #dbbf86;
    --gold-600:         #c9a96b;
    --gold-700:         #a88944;
    --ink-900:          #1a1a24;
    --ink-700:          #3d3d4f;
    --ink-500:          #6d6d80;
    --ink-400:          #9a9aab;
    /* Status: pending (بانتظار التحضير) */
    --s-pending-bg:     #fde9d6;
    --s-pending-fg:     #a45a1c;
    --s-pending-dot:    #e89556;
    /* Status: prep (جاري التحضير) */
    --s-prep-bg:        #fff3cf;
    --s-prep-fg:        #8a6515;
    --s-prep-dot:       #e0b13b;
    /* Status: ready (جاهز للتسليم) */
    --s-ready-bg:       #d9efdb;
    --s-ready-fg:       #2e6b35;
    --s-ready-dot:      #5fb46b;
    /* Status: out (في الطريق) */
    --s-out-bg:         #d8e6fb;
    --s-out-fg:         #2c4f8c;
    --s-out-dot:        #5a87d6;
    /* Radii */
    --r-sm:  10px;
    --r-md:  14px;
    --r-lg:  20px;
    --r-xl:  28px;
    /* Shadows */
    --shadow-sm: 0 1px 2px rgba(20,30,60,.04);
    --shadow-md: 0 4px 18px -8px rgba(20,30,60,.10), 0 1px 3px rgba(20,30,60,.04);
    --shadow-lg: 0 18px 48px -24px rgba(20,30,60,.18), 0 2px 6px rgba(20,30,60,.05);
    --font-ar: 'Cairo', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* ── Reset ───────────────────────────────────── */
.bj-dash, .bj-dash * { box-sizing: border-box; }
.bj-dash {
    background: var(--cream);
    direction: rtl;
    font-family: var(--font-ar);
    color: var(--ink-900);
}
.bj-dash *,
.bj-dash *::before,
.bj-dash *::after {
    -webkit-background-clip: border-box !important;
    background-clip: border-box !important;
    -webkit-text-fill-color: currentColor !important;
}

/* ── MAIN CONTENT ──────────────────────────── */
.bj-main {
    background: var(--cream);
    padding: 24px 32px 40px;
    display: flex;
    flex-direction: column;
    gap: 22px;
    min-width: 0;
}

/* ── TOPBAR ──────────────────────────────── */
.bj-topbar {
    background: var(--paper);
    border: 1px solid var(--hairline);
    border-radius: var(--r-lg);
    padding: 14px 18px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: var(--shadow-sm);
}
.bj-topbar .bj-avatar {
    display: flex; align-items: center; gap: 10px;
    background: var(--ivory);
    border-radius: var(--r-xl);
    padding: 6px 12px 6px 6px;
    flex-shrink: 0;
}
.bj-topbar .bj-avatar .photo {
    width: 34px; height: 34px;
    border-radius: 50%;
    background: var(--navy-800);
    color: var(--gold-600);
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: .95rem;
    flex-shrink: 0;
    overflow: hidden;
}
.bj-topbar .bj-avatar .photo img { width:100%; height:100%; object-fit:cover; }
.bj-topbar .bj-avatar .who { line-height: 1.2; }
.bj-topbar .bj-avatar .who .n { font-size: .88rem; font-weight: 800; color: var(--ink-900); }
.bj-topbar .bj-avatar .who .r { font-size: .7rem; color: var(--ink-400); font-weight: 500; }

.bj-greeting {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 2px;
    text-align: right;
}
.bj-greeting .hi { font-size: .8rem; color: var(--ink-400); font-weight: 500; }
.bj-greeting .msg { font-size: .95rem; font-weight: 800; color: var(--ink-900); }

.bj-topbar-actions {
    display: flex; align-items: center; gap: 8px;
    margin-right: auto;
    margin-left: 0;
}
.bj-live-pill {
    display: inline-flex; align-items: center; gap: 6px;
    background: var(--s-ready-bg);
    color: var(--s-ready-fg);
    font-size: .8rem; font-weight: 700;
    padding: 7px 13px;
    border-radius: 999px;
    border: 1px solid #b7ddb9;
    white-space: nowrap;
}
.bj-live-pill .pulse {
    width: 7px; height: 7px;
    background: var(--s-ready-dot);
    border-radius: 50%;
    animation: bjPulse 2s ease-in-out infinite;
}
@keyframes bjPulse { 0%,100%{opacity:1;transform:scale(1)}50%{opacity:.4;transform:scale(.65)} }

.bj-btn {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: .84rem; font-weight: 700;
    padding: 8px 15px;
    border-radius: var(--r-sm);
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none;
    font-family: var(--font-ar);
    white-space: nowrap;
    transition: all .13s;
}
.bj-btn.primary {
    background: var(--navy-800);
    color: #fff;
    border-color: var(--navy-800);
}
.bj-btn.primary:hover { background: var(--navy-700); color:#fff; text-decoration:none; }
.bj-btn.ghost {
    background: var(--paper);
    color: var(--ink-700);
    border-color: var(--hairline);
}
.bj-btn.ghost:hover { background: var(--ivory); color: var(--ink-900); text-decoration:none; }
.bj-icon-btn {
    width: 38px; height: 38px;
    background: var(--navy-800);
    border: none;
    border-radius: var(--r-sm);
    display: inline-flex; align-items: center; justify-content: center;
    color: var(--gold-600);
    cursor: pointer;
    position: relative;
    text-decoration: none;
    transition: background .13s;
    font-size: 16px;
}
.bj-icon-btn:hover { background: var(--navy-700); }
.bj-icon-btn .dot {
    position: absolute; top: -3px; left: -3px;
    min-width: 17px; height: 17px;
    background: var(--s-pending-dot);
    color: #fff; font-size: .58rem; font-weight: 800;
    border-radius: 999px; padding: 0 4px;
    display: flex; align-items: center; justify-content: center;
    border: 2px solid var(--paper);
}

/* ── STOCK ALERT ───────────────────────────── */
.bj-stock {
    background: #ffeeec;
    border: 1px solid #f5c6c2;
    border-radius: var(--r-md);
    padding: 12px 16px;
    display: flex; align-items: center; gap: 10px;
}
.bj-stock i { color: #c0392b; font-size: 18px; flex-shrink: 0; }
.bj-stock-text { flex: 1; font-size: .875rem; font-weight: 600; color: #8b2020; }
.bj-stock-text a { color: #8b2020; font-weight: 800; text-decoration: underline; }
.bj-stock-close { background: none; border: none; cursor: pointer; color: var(--ink-400); font-size: 15px; padding: 0; line-height: 1; }

/* ── KPI TILES ─────────────────────────────── */
.bj-kpis {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}
.bj-kpi {
    background: var(--paper);
    border: 1px solid var(--hairline);
    border-radius: var(--r-lg);
    padding: 18px 18px 16px;
    display: flex;
    flex-direction: column;
    gap: 4px;
    box-shadow: var(--shadow-sm);
    text-decoration: none;
    color: inherit;
    transition: box-shadow .18s, transform .18s;
    min-height: 175px;
}
.bj-kpi:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); text-decoration:none; color:inherit; }

.bj-kpi .top {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 4px;
}
.bj-kpi .top .lbl {
    font-size: .875rem; font-weight: 700; color: var(--ink-700);
}
.bj-kpi .top .chip {
    width: 34px; height: 34px;
    border-radius: var(--r-sm);
    display: flex; align-items: center; justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}
/* Chip tones */
.bj-kpi[data-tone="pending"] .chip { background: var(--s-pending-bg); color: var(--s-pending-fg); }
.bj-kpi[data-tone="prep"]    .chip { background: var(--s-prep-bg);    color: var(--s-prep-fg);    }
.bj-kpi[data-tone="ready"]   .chip { background: var(--s-ready-bg);   color: var(--s-ready-fg);   }
.bj-kpi[data-tone="out"]     .chip { background: var(--s-out-bg);     color: var(--s-out-fg);     }

.bj-kpi .num {
    font-size: 4.5rem;
    font-weight: 900;
    line-height: 1;
    color: var(--ink-900);
    letter-spacing: -2px;
    text-align: center;
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 4px 0;
}

.bj-kpi .foot {
    display: flex; align-items: center; justify-content: space-between;
    font-size: .78rem; color: var(--ink-400); font-weight: 600;
    padding-top: 8px;
    border-top: 1px dashed var(--hairline-soft);
    margin-top: auto;
}
.bj-kpi .foot .delta { font-weight: 800; }
.bj-kpi[data-tone="ready"] .foot .delta { color: var(--s-ready-fg); }
.bj-kpi[data-tone="pending"] .foot .delta { color: var(--s-pending-fg); }

/* ── STATUS DOT on footer ─────────────────── */
.bj-kpi .status-dot {
    width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
}
.bj-kpi[data-tone="pending"] .status-dot { background: var(--s-pending-dot); }
.bj-kpi[data-tone="prep"]    .status-dot { background: var(--s-prep-dot);    }
.bj-kpi[data-tone="ready"]   .status-dot { background: var(--s-ready-dot);   }
.bj-kpi[data-tone="out"]     .status-dot { background: var(--s-out-dot);     }

/* ── MAIN GRID ─────────────────────────────── */
.bj-grid {
    display: grid;
    grid-template-columns: 1fr 420px;
    gap: 16px;
    align-items: start;
    direction: rtl;
}
.bj-col-left { display: flex; flex-direction: column; gap: 0; min-width: 0; }
.bj-col-right { display: flex; flex-direction: column; gap: 16px; min-width: 0; }

/* ── CARD shell (white bordered) ───────────── */
.bj-card {
    background: var(--paper);
    border: 1px solid var(--hairline);
    border-radius: var(--r-lg);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}
.bj-card-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px 14px;
    border-bottom: 1px solid var(--hairline-soft);
}
.bj-card-head .title {
    display: flex; align-items: center; gap: 8px;
    font-size: 1rem; font-weight: 800; color: var(--ink-900);
}
.bj-card-head .title .ico-chip {
    width: 26px; height: 26px;
    background: var(--navy-800);
    border-radius: 8px;
    display: inline-flex; align-items: center; justify-content: center;
    color: #fff; font-size: 13px;
    flex-shrink: 0;
}
.bj-card-head .more {
    font-size: .78rem; font-weight: 700;
    color: var(--ink-500); text-decoration: none;
}
.bj-card-head .more:hover { color: var(--navy-800); text-decoration: none; }
.bj-card-body { padding: 12px 16px 16px; display: flex; flex-direction: column; gap: 10px; }

/* ── ORDER ROWS (live orders) ──────────────── */
.bj-tile {
    display: flex; align-items: center; gap: 12px;
    padding: 13px 16px;
    border-radius: var(--r-md);
    background: var(--ivory);
    border: 1px solid var(--hairline-soft);
    text-decoration: none; color: inherit;
    transition: background .12s, box-shadow .13s;
    position: relative;
}
.bj-tile:hover { background: var(--paper); box-shadow: var(--shadow-md); text-decoration:none; color:inherit; }

.bj-tile .status-dot {
    width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0;
}
.bj-tile[data-tone="pending"] .status-dot { background: var(--s-pending-dot); }
.bj-tile[data-tone="prep"]    .status-dot { background: var(--s-prep-dot);    }
.bj-tile[data-tone="ready"]   .status-dot { background: var(--s-ready-dot);   }
.bj-tile[data-tone="out"]     .status-dot { background: var(--s-out-dot);     }
.bj-tile[data-tone="done"]    .status-dot { background: var(--s-ready-dot);   }
.bj-tile[data-tone="default"] .status-dot { background: var(--ink-400);       }

.bj-tile .info { flex: 1; min-width: 0; }
.bj-tile .info .cname { font-size: .95rem; font-weight: 800; color: var(--ink-900); margin-bottom: 3px; }
.bj-tile .info .meta  { font-size: .79rem; color: var(--ink-400); font-weight: 500; }
.bj-tile .info .meta .sep { margin: 0 .35rem; opacity: .5; }
.bj-tile .info .meta .oid { color: var(--ink-500); }

.bj-tile .price {
    font-size: .95rem; font-weight: 800; color: var(--ink-900);
    white-space: nowrap; text-align: left;
}
.bj-tile .price .ccy { font-size: .72rem; color: var(--ink-400); font-weight: 600; margin-right: 2px; }

.bj-tile .stat-chip {
    display: inline-flex; align-items: center;
    padding: 5px 11px;
    font-size: .74rem; font-weight: 800;
    border-radius: 999px;
    white-space: nowrap;
    flex-shrink: 0;
}
.bj-tile[data-tone="pending"] .stat-chip { background: var(--s-pending-bg); color: var(--s-pending-fg); }
.bj-tile[data-tone="prep"]    .stat-chip { background: var(--s-prep-bg);    color: var(--s-prep-fg);    }
.bj-tile[data-tone="ready"]   .stat-chip { background: var(--s-ready-bg);   color: var(--s-ready-fg);   }
.bj-tile[data-tone="out"]     .stat-chip { background: var(--s-out-bg);     color: var(--s-out-fg);     }
.bj-tile[data-tone="done"]    .stat-chip { background: var(--s-ready-bg);   color: var(--s-ready-fg);   }
.bj-tile[data-tone="default"] .stat-chip { background: #efefef;             color: var(--ink-500);      }

/* ── REVENUE CARD (dark navy, inside bj-col-right) ─── */
.bj-revenue {
    background: linear-gradient(135deg, var(--navy-800) 0%, var(--navy-900) 100%);
    border-radius: var(--r-lg);
    padding: 22px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    color: #fff;
    overflow: hidden;
    position: relative;
    box-shadow: var(--shadow-md);
}
.bj-revenue * { -webkit-text-fill-color: currentColor !important; }
.bj-revenue .head {
    display: flex; align-items: center; justify-content: space-between;
}
.bj-revenue .head .title {
    display: flex; align-items: center; gap: 10px;
    font-size: .92rem; font-weight: 800; color: #fff;
}
.bj-revenue .head .title .ico-chip {
    width: 34px; height: 34px;
    background: rgba(255,255,255,.08);
    border-radius: 10px;
    display: inline-flex; align-items: center; justify-content: center;
    color: var(--gold-600); font-size: 16px;
}
.bj-revenue .filter {
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 999px;
    color: rgba(255,255,255,.88);
    font-size: .76rem; font-weight: 700;
    padding: 6px 22px 6px 10px;
    cursor: pointer;
    font-family: var(--font-ar);
    direction: rtl;
    appearance: none;
    background-image:
        linear-gradient(45deg, transparent 50%, rgba(255,255,255,.6) 50%),
        linear-gradient(135deg, rgba(255,255,255,.6) 50%, transparent 50%);
    background-position: 11px 50%, 16px 50%;
    background-size: 5px 5px, 5px 5px;
    background-repeat: no-repeat, no-repeat;
}

.bj-revenue .amount {
    text-align: center;
}
.bj-revenue .amount .big {
    font-size: 2.9rem;
    font-weight: 900;
    color: #fff;
    letter-spacing: -2px;
    line-height: 1;
}
.bj-revenue .amount .ccy {
    font-size: 1.1rem;
    color: var(--gold-600);
    font-weight: 800;
    margin-right: .3rem;
}

.bj-revenue .delta {
    text-align: center;
    font-size: .8rem; font-weight: 700;
    color: var(--gold-600);
}
.bj-revenue .delta i { font-size: 11px; margin-left: 2px; }

.bj-revenue .spark { height: 100px; margin: 0 -4px; }

.bj-revenue .breakdown {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    padding-top: 12px;
    border-top: 1px solid rgba(255,255,255,.08);
}
.bj-revenue .breakdown .bd-item { text-align: center; }
.bj-revenue .breakdown .bd-item .l {
    font-size: .7rem; color: rgba(255,255,255,.5);
    font-weight: 600; margin-bottom: 4px;
}
.bj-revenue .breakdown .bd-item .v {
    font-size: 1.05rem; font-weight: 800; color: #fff;
}
.bj-revenue .breakdown .bd-item .v .star { color: var(--gold-600); margin-left: 2px; }

/* ── MESSAGES PANEL ───────────────────────── */
.bj-msgs .bj-msg-list { padding: 4px 0 4px; }
.bj-msg {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 10px 20px;
    text-decoration: none; color: inherit;
    transition: background .12s;
    position: relative;
}
.bj-msg + .bj-msg { border-top: 1px dashed var(--hairline-soft); }
.bj-msg:hover { background: var(--ivory); text-decoration:none; color:inherit; }

.bj-msg .avatar {
    width: 36px; height: 36px;
    border-radius: var(--r-sm);
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 1rem;
    flex-shrink: 0;
}
.bj-msg .av1 { background: var(--navy-800); color: #fff; }
.bj-msg .av2 { background: var(--gold-600); color: var(--navy-800); }
.bj-msg .av3 { background: var(--s-ready-dot); color: #fff; }
.bj-msg .av4 { background: #b8932d; color: #fff; }

.bj-msg .body { flex: 1; min-width: 0; }
.bj-msg .body .row1 {
    display: flex; align-items: baseline; justify-content: space-between; gap: 6px;
}
.bj-msg .body .row1 .n { font-size: .88rem; font-weight: 800; color: var(--ink-900); }
.bj-msg .body .meta { display: flex; align-items: center; gap: 5px; }
.bj-msg .body .meta .when { font-size: .7rem; color: var(--ink-400); white-space: nowrap; }
.bj-msg .body .meta .dot { width: 5px; height: 5px; border-radius: 50%; background: var(--gold-600); }
.bj-msg .body .preview {
    font-size: .77rem; color: var(--ink-400);
    margin-top: 3px; font-weight: 500;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

/* ── TOAST ─────────────────────────────────── */
.bj-toast {
    position: fixed; top: 20px; left: 20px;
    z-index: 9999;
    background: var(--paper);
    border: 2px solid var(--s-pending-dot);
    border-radius: var(--r-lg);
    padding: 16px 18px;
    box-shadow: var(--shadow-lg);
    min-width: 280px;
    display: none;
    direction: rtl;
}
.bj-toast.show { display: block; }
.bj-toast-title { font-size: .9rem; font-weight: 800; color: var(--ink-900); margin-bottom: 4px; }
.bj-toast-body  { font-size: .8rem; color: var(--ink-400); margin-bottom: 12px; }
.bj-toast-actions { display: flex; gap: 6px; }

/* ── EMPTY STATE ────────────────────────────── */
.bj-empty {
    text-align: center; padding: 3rem 1rem;
    color: var(--ink-400); font-size: .88rem; font-weight: 600;
}
.bj-empty i { display: block; font-size: 2.2rem; margin-bottom: .5rem; opacity: .3; }

/* ── RESPONSIVE ─────────────────────────────── */
@media (max-width: 1280px) {
    .bj-grid { grid-template-columns: 1fr 380px; }
}
@media (max-width: 1024px) {
    .bj-main { padding: 16px; }
    .bj-grid { grid-template-columns: 1fr; }
    .bj-kpis { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 600px) {
    .bj-kpis { grid-template-columns: repeat(2, 1fr); }
    .bj-kpi .num { font-size: 3.5rem; }
    .bj-revenue .amount .big { font-size: 2.2rem; }
}
</style>
@endpush

@section('content')
<div class="bj-dash">

@if(auth('vendor')->check())
@php
    $restaurant      = \App\CentralLogics\Helpers::get_restaurant_data();
    $restaurant_id   = $restaurant?->id;
    $loggedin        = auth('vendor')->user();
    $vendor_initial  = mb_strtoupper(mb_substr($loggedin?->f_name ?? 'م', 0, 1));

    $hr = (int) now()->format('H');
    $greeting = $hr < 12 ? 'صباح الخير' : 'مساء الخير';
    $greeting_icon = $hr < 12 ? '☀️' : '🌙';

    $urgent_count = ($data['confirmed'] ?? 0) + ($data['ready_for_delivery'] ?? 0);

    // Convert to Arabic-Indic
    $toAr = function($n) {
        return str_replace(
            ['0','1','2','3','4','5','6','7','8','9'],
            ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'],
            (string)$n
        );
    };

    // Live orders
    $recent_orders = \App\Models\Order::where('restaurant_id', $restaurant_id)
        ->whereNotIn('order_status', ['delivered','failed','canceled','refunded','refund_requested'])
        ->with(['customer','details'])
        ->latest()
        ->take(8)
        ->get();

    // Conversations
    $sender = $loggedin;
    $recent_convs = \App\Models\Conversation::with(['sender','receiver','last_message'])
        ->where(function($q) use ($sender) {
            $q->where('sender_id', $sender?->id)->orWhere('receiver_id', $sender?->id);
        })
        ->latest('updated_at')
        ->take(4)
        ->get();

    // Stats
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
        : 12;

    $rating_avg = number_format((float)($restaurant?->avg_rating ?? 4.8), 1);

    // Sparkline
    $spark_days = []; $spark_data = [];
    for ($i = 6; $i >= 0; $i--) {
        $d = today()->copy()->subDays($i);
        $spark_days[] = $d->format('M d');
        $spark_data[] = (float) \App\Models\Order::where('restaurant_id', $restaurant_id)
            ->whereDate('created_at', $d)->where('order_status','delivered')->sum('order_amount');
    }

    $orderStatusMap = [
        'pending'            => ['label' => 'بانتظار التحضير', 'tone' => 'pending'],
        'confirmed'          => ['label' => 'بانتظار التحضير', 'tone' => 'pending'],
        'accepted'           => ['label' => 'بانتظار التحضير', 'tone' => 'pending'],
        'processing'         => ['label' => 'جاري التحضير',    'tone' => 'prep'],
        'cooking'            => ['label' => 'جاري التحضير',    'tone' => 'prep'],
        'handover'           => ['label' => 'جاهز للتسليم',   'tone' => 'ready'],
        'ready_for_delivery' => ['label' => 'جاهز للتسليم',   'tone' => 'ready'],
        'picked_up'          => ['label' => 'في الطريق',       'tone' => 'out'],
        'food_on_the_way'    => ['label' => 'في الطريق',       'tone' => 'out'],
        'delivered'          => ['label' => 'تم التسليم',      'tone' => 'done'],
    ];
@endphp

{{-- ── TOAST ── --}}
<div class="bj-toast" id="bj-toast">
    <div class="bj-toast-title">طلب جديد</div>
    <div class="bj-toast-body" id="bj-toast-body">جاري التحديث...</div>
    <div class="bj-toast-actions">
        <button class="bj-btn primary" onclick="bjPrintLatest()"><i class="tio-print"></i> طباعة الآن</button>
        <button class="bj-btn ghost" onclick="bjViewLatest()"><i class="tio-visible"></i> عرض</button>
        <button class="bj-btn ghost" onclick="bjDismissToast()">تجاهل</button>
    </div>
</div>

{{-- ── MAIN ── --}}
<div class="bj-main">

    {{-- TOPBAR --}}
    <div class="bj-topbar">
        {{-- Avatar chip --}}
        <div class="bj-avatar">
            <div class="photo">
                @if($loggedin?->image_full_url)
                    <img src="{{ $loggedin->image_full_url }}" alt="">
                @else
                    {{ $vendor_initial }}
                @endif
            </div>
            <div class="who">
                <div class="n">{{ trim(($loggedin->f_name ?? '') . ' ' . ($loggedin->l_name ?? '')) }}</div>
                <div class="r">مدير المطعم</div>
            </div>
        </div>

        {{-- Greeting --}}
        <div class="bj-greeting">
            <span class="hi">{{ $greeting }}</span>
            <span class="msg">
                @if($urgent_count > 0)
                    لديك {{ $toAr($urgent_count) }} طلب يحتاج اهتمامك
                @else
                    لا توجد طلبات عاجلة الآن
                @endif
            </span>
        </div>

        {{-- Actions --}}
        <div class="bj-topbar-actions">
            <span class="bj-live-pill">
                <span class="pulse"></span>
                المطعم مفتوح
            </span>
            <button class="bj-btn ghost" onclick="bjRefresh()">
                <i class="tio-refresh"></i> تحديث
            </button>
            <a href="{{ route('vendor.order.list', ['all']) }}" class="bj-btn primary">
                <i class="tio-receipt"></i> كل الطلبات
            </a>
            <button class="bj-icon-btn" type="button">
                <i class="tio-notifications"></i>
                @if($urgent_count > 0)<span class="dot">{{ $toAr($urgent_count) }}</span>@endif
            </button>
        </div>
    </div>

    {{-- STOCK ALERT --}}
    @if(Session::get('stock_out_reminder_close_btn') !== true && isset($out_out_count) && $out_out_count > 0)
    <div class="bj-stock" id="bj-stock">
        <i class="tio-warning-outlined"></i>
        <span class="bj-stock-text">
            @if($out_out_count == 1 && isset($food))
                {{ $food?->name }} — نفدت الكمية
            @else
                {{ $toAr($out_out_count) }} منتجات نفدت من المخزن
            @endif
            &nbsp;<a href="{{ route('vendor.food.stockOutList') }}">عرض القائمة</a>
        </span>
        <button class="bj-stock-close add-to-session" data-id="stock_out_reminder_close_btn"
            onclick="this.closest('.bj-stock').remove()"><i class="tio-clear"></i></button>
    </div>
    @endif

    {{-- KPI TILES --}}
    <div class="bj-kpis" id="order_stats">
        <a href="{{ route('vendor.order.list', ['confirmed']) }}" class="bj-kpi" data-tone="pending">
            <div class="top">
                <span class="lbl">بانتظار التحضير</span>
                <span class="chip"><i class="tio-time"></i></span>
            </div>
            <div class="num">{{ $toAr($data['confirmed'] ?? 0) }}</div>
            <div class="foot">
                <span>منذ آخر تحديث</span>
                <span class="delta">١د+</span>
            </div>
        </a>
        <a href="{{ route('vendor.order.list', ['cooking']) }}" class="bj-kpi" data-tone="prep">
            <div class="top">
                <span class="lbl">جاري التحضير</span>
                <span class="chip"><i class="tio-restaurant-menu"></i></span>
            </div>
            <div class="num">{{ $toAr($data['cooking'] ?? 0) }}</div>
            <div class="foot">
                <span>متوسط الوقت</span>
                <span>١٤ د</span>
            </div>
        </a>
        <a href="{{ route('vendor.order.list', ['ready_for_delivery']) }}" class="bj-kpi" data-tone="ready">
            <div class="top">
                <span class="lbl">جاهز للتسليم</span>
                <span class="chip"><i class="tio-checkmark-circle"></i></span>
            </div>
            <div class="num">{{ $toAr($data['ready_for_delivery'] ?? 0) }}</div>
            <div class="foot">
                <span>جاهز</span>
                <span class="delta">الآن</span>
            </div>
        </a>
        <a href="{{ route('vendor.order.list', ['food_on_the_way']) }}" class="bj-kpi" data-tone="out">
            <div class="top">
                <span class="lbl">في الطريق</span>
                <span class="chip"><i class="tio-delivery"></i></span>
            </div>
            <div class="num">{{ $toAr($data['food_on_the_way'] ?? 0) }}</div>
            <div class="foot">
                <span>أقرب وصول</span>
                <span>٧ د</span>
            </div>
        </a>
    </div>

    {{-- MAIN GRID --}}
    <div class="bj-grid">

        {{-- LEFT: Live orders --}}
        <div class="bj-col-left">
            <div class="bj-card">
                <div class="bj-card-head">
                    <span class="title">
                        <span class="ico-chip"><i class="tio-receipt"></i></span>
                        الطلبات الحية
                    </span>
                    <a href="{{ route('vendor.order.list', ['all']) }}" class="more">عرض الكل ←</a>
                </div>
                <div class="bj-card-body">
                    @forelse($recent_orders as $order)
                    @php
                        $sInfo  = $orderStatusMap[$order->order_status] ?? ['label' => $order->order_status, 'tone' => 'default'];
                        $items  = $order->details->count();
                        $rawAddr = '';
                        if (is_array($order->delivery_address)) {
                            $rawAddr = $order->delivery_address['address'] ?? ($order->delivery_address['zone'] ?? '');
                        }
                        $area  = $rawAddr ? Str::limit(strip_tags($rawAddr), 18, '') : '';
                        $cName = trim(($order->customer->f_name ?? 'عميل') . ' ' . ($order->customer->l_name ?? ''));
                    @endphp
                    <a href="{{ route('vendor.order.details', ['id' => $order->id]) }}"
                       class="bj-tile" data-tone="{{ $sInfo['tone'] }}">
                        <span class="status-dot"></span>
                        <div class="info">
                            <div class="cname">{{ $cName }}</div>
                            <div class="meta">
                                <span class="oid">طلب #{{ $toAr($order->id) }}</span>
                                <span class="sep">·</span>
                                <span>{{ $toAr($items) }} {{ $items == 1 ? 'صنف' : 'أصناف' }}</span>
                                @if($area)<span class="sep">·</span><span>{{ $area }}</span>@endif
                            </div>
                        </div>
                        <div class="price">
                            {{ $toAr(number_format($order->order_amount, 0)) }}<span class="ccy">ج.م</span>
                        </div>
                        <span class="stat-chip">{{ $sInfo['label'] }}</span>
                    </a>
                    @empty
                    <div class="bj-empty"><i class="tio-receipt"></i>لا طلبات حية الآن</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- RIGHT: Revenue + Messages --}}
        <div class="bj-col-right">

            {{-- Revenue card --}}
            <div class="bj-revenue">
                <div class="head">
                    <div class="title">
                        <span class="ico-chip"><i class="tio-wallet-outlined"></i></span>
                        إيرادات اليوم
                    </div>
                    <select class="filter" onchange="bjFilterStats(this.value)">
                        <option value="today"      {{ ($params['statistics_type'] ?? 'today') == 'today' ? 'selected' : '' }}>اليوم</option>
                        <option value="this_month" {{ ($params['statistics_type'] ?? '') == 'this_month' ? 'selected' : '' }}>هذا الشهر</option>
                        <option value="overall"    {{ ($params['statistics_type'] ?? '') == 'overall' ? 'selected' : '' }}>الإجمالي</option>
                    </select>
                </div>
                <div class="amount">
                    <span class="big">{{ $toAr(number_format($today_revenue, 0)) }}</span><span class="ccy">ج.م</span>
                </div>
                @if($rev_delta != 0)
                <div class="delta">
                    <i class="tio-arrow-{{ $rev_delta >= 0 ? 'upward' : 'downward' }}"></i>
                    {{ $toAr(abs($rev_delta)) }}٪ مقارنة بأمس
                </div>
                @endif
                <div class="spark" id="bj-revenue-chart"></div>
                <div class="breakdown">
                    <div class="bd-item">
                        <div class="l">عدد الطلبات</div>
                        <div class="v">{{ $toAr($today_count) }}</div>
                    </div>
                    <div class="bd-item">
                        <div class="l">متوسط الطلب</div>
                        <div class="v">{{ $toAr($today_avg) }}</div>
                    </div>
                    <div class="bd-item">
                        <div class="l">رضا العملاء</div>
                        <div class="v">{{ $toAr($rating_avg) }}<span class="star">★</span></div>
                    </div>
                </div>
            </div>

            {{-- Messages panel --}}
            <div class="bj-card bj-msgs">
                <div class="bj-card-head">
                    <span class="title">
                        <i class="tio-chat-outlined" style="font-size:17px;color:var(--navy-800)"></i>
                        آخر الرسائل
                    </span>
                    <a href="{{ route('vendor.message.list') }}" class="more">عرض الكل ←</a>
                </div>
                <div class="bj-msg-list">
                    @forelse($recent_convs as $i => $conv)
                    @php
                        $other   = $conv->sender_id === $sender?->id ? $conv->receiver : $conv->sender;
                        $msgText = $conv->last_message?->message ?? '...';
                        $initial = mb_substr($other?->f_name ?? '؟', 0, 1);
                        $avClass = 'av' . (($i % 4) + 1);
                        $timeAgo = \Carbon\Carbon::parse($conv->updated_at)->diffForHumans(null, \Carbon\CarbonInterface::DIFF_ABSOLUTE);
                    @endphp
                    <a href="{{ route('vendor.message.view', ['conversation_id' => $conv->id, 'user_id' => $other?->id ?? 0]) }}"
                       class="bj-msg">
                        <div class="avatar {{ $avClass }}">{{ $initial }}</div>
                        <div class="body">
                            <div class="row1">
                                <span class="n">{{ trim(($other?->f_name ?? 'مستخدم') . ' ' . ($other?->l_name ?? '')) }}</span>
                                <div class="meta">
                                    <span class="when">منذ {{ $timeAgo }}</span>
                                    <span class="dot"></span>
                                </div>
                            </div>
                            <div class="preview">{{ Str::limit($msgText, 55) }}</div>
                        </div>
                    </a>
                    @empty
                    <div class="bj-empty"><i class="tio-chat-outlined"></i>لا رسائل حتى الآن</div>
                    @endforelse
                </div>
            </div>

        </div>{{-- /bj-col-right --}}
    </div>{{-- /bj-grid --}}

</div>{{-- /bj-main --}}

@else
{{-- Employee fallback --}}
<div style="grid-column:1/-1;padding:4rem;text-align:center;">
    <h2 style="font-weight:800;color:var(--ink-900);">
        {{ translate('messages.welcome') }}, {{ auth('vendor_employee')->user()->f_name }}
    </h2>
    <p style="color:var(--ink-400);">{{ translate('messages.employee_welcome_message') }}</p>
</div>
@endif

</div>{{-- /bj-dash --}}
@endsection

@push('script')
<script src="{{dynamicAsset('public/assets/admin/apexcharts/apexcharts.min.js')}}"></script>
<script>
(function(){
    'use strict';

    function toAr(n){
        return String(n).replace(/[0-9]/g, function(d){
            return '٠١٢٣٤٥٦٧٨٩'.charAt(+d);
        });
    }
    window.bjToAr = toAr;

    // Revenue sparkline
    var sparkData = @json($spark_data ?? []);
    var sparkDays = @json($spark_days ?? []);

    if (typeof ApexCharts !== 'undefined' && document.getElementById('bj-revenue-chart')) {
        new ApexCharts(document.getElementById('bj-revenue-chart'), {
            chart: {
                type: 'area', height: 100,
                toolbar: { show: false }, zoom: { enabled: false },
                animations: { enabled: true, easing: 'easeinout', speed: 700 },
                background: 'transparent',
            },
            stroke: { curve: 'smooth', width: 2.5, colors: ['#c9a96b'] },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1, opacityFrom: 0.5, opacityTo: 0.02,
                    stops: [0, 100], colorStops: [
                        { offset: 0, color: '#c9a96b', opacity: .5 },
                        { offset: 100, color: '#c9a96b', opacity: 0 }
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
            grid: { show: false, padding: { left: 0, right: 0, top: 0, bottom: 0 } },
            tooltip: {
                theme: 'dark', x: { show: true },
                y: { formatter: function(v){ return toAr(Math.round(v)) + ' ج.م'; } }
            },
            dataLabels: { enabled: false },
            markers: { size: 0, hover: { size: 4 } }
        }).render();
    }

    // New order detection — tracks the newest order id so newly-placed
    // (pending) orders trigger too, not only confirmed ones.
    var lastSeenOrderId = null; // initialized on first poll
    var latestOrderId = null;
    var audioCtx = null;

    // AudioContext needs a user gesture before it can play.
    document.addEventListener('click', function unlock(){
        try { audioCtx = new (window.AudioContext||window.webkitAudioContext)(); } catch(e){}
    }, { once: true });

    function beep(f, s, d){
        if (!audioCtx) return;
        var o = audioCtx.createOscillator(), g = audioCtx.createGain();
        o.connect(g); g.connect(audioCtx.destination);
        o.type = 'sine'; o.frequency.setValueAtTime(f, audioCtx.currentTime + s);
        g.gain.setValueAtTime(.3, audioCtx.currentTime + s);
        g.gain.exponentialRampToValueAtTime(.001, audioCtx.currentTime + s + d);
        o.start(audioCtx.currentTime + s); o.stop(audioCtx.currentTime + s + d);
    }
    function playAlert(){ beep(880,0,.35); beep(660,.45,.35); beep(880,.9,.35); }

    var invoiceUrlTpl = '{{ route("vendor.order.generate-invoice", ["id" => "__X__"]) }}';
    var orderDetailsUrlTpl = '{{ route("vendor.order.details", ["id" => "__X__"]) }}';

    window.bjPrintLatest = function(){
        if (!latestOrderId) return;
        // Open the invoice in a new tab and trigger print there. The user gesture
        // (button click) lets the browser open both the tab and the print dialog.
        var w = window.open(invoiceUrlTpl.replace('__X__', latestOrderId), '_blank');
        if (w) {
            w.addEventListener('load', function(){
                try { w.focus(); w.print(); } catch(e){}
            });
        }
        bjDismissToast();
    };
    window.bjViewLatest = function(){
        if (!latestOrderId) return;
        window.location.href = orderDetailsUrlTpl.replace('__X__', latestOrderId);
    };
    window.bjDismissToast = function(){ var t=document.getElementById('bj-toast'); if(t) t.classList.remove('show'); };

    function showToast(id, customer){
        latestOrderId = id;
        var t = document.getElementById('bj-toast');
        var b = document.getElementById('bj-toast-body');
        if (b) b.textContent = 'طلب #' + toAr(id) + ' — ' + (customer||'عميل جديد');
        if (t){
            t.classList.add('show');
            playAlert();
        }
        // No auto-dismiss — vendor must explicitly acknowledge (print / view / dismiss).
    }

    function setTile(sel, val){
        var el = document.querySelector('.bj-kpi[data-tone="' + sel + '"] .num');
        if (el) el.textContent = toAr(val);
    }

    function refreshStats(){
        var type = document.querySelector('.bj-revenue .filter')?.value || 'today';
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        $.post('{{ route("vendor.dashboard.order-stats") }}', { statistics_type: type }, function(data){
            if (!data || !data.data) return;
            var d = data.data;
            setTile('pending', d.confirmed       ?? 0);
            setTile('prep',    d.cooking          ?? 0);
            setTile('ready',   d.ready_for_delivery ?? 0);
            setTile('out',     d.food_on_the_way  ?? 0);

            var newestId = parseInt(d.latest_order_id || 0) || null;
            if (newestId) {
                if (lastSeenOrderId === null) {
                    // First poll after page load — establish baseline, don't alert.
                    lastSeenOrderId = newestId;
                } else if (newestId > lastSeenOrderId) {
                    showToast(newestId, d.latest_customer || '');
                    lastSeenOrderId = newestId;
                }
            }
        });
    }

    window.bjRefresh     = refreshStats;
    window.bjFilterStats = function(type){
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        $.post('{{ route("vendor.dashboard.order-stats") }}', { statistics_type: type }, function(data){
            if (!data || !data.data) return;
            var d = data.data;
            setTile('pending', d.confirmed       ?? 0);
            setTile('prep',    d.cooking          ?? 0);
            setTile('ready',   d.ready_for_delivery ?? 0);
            setTile('out',     d.food_on_the_way  ?? 0);
        });
    };

    $(document).on('click', '.add-to-session', function(){
        $.ajax({
            url: '{{ route("vendor.food.addToSession") }}',
            method: 'POST',
            data: { value: $(this).data('id'), _token: '{{ csrf_token() }}' }
        });
    });

    // Kick off an immediate poll so lastSeenOrderId is established without delay.
    refreshStats();
    var timer = setInterval(refreshStats, 15000);
    window.addEventListener('beforeunload', function(){ clearInterval(timer); });
})();
</script>
@endpush
