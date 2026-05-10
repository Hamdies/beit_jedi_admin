@extends('layouts.vendor.app')

@section('title', translate('messages.dashboard'))

@push('css_or_js')
<style>
/* ═══════════════════════════════════════════════
   BEIT JEDI — ORDER OPERATIONS CENTER
   Palette: Navy #1C2E5E · Gold #D4A017 · Warm white
═══════════════════════════════════════════════ */


.ops-panel,
.ops-panel *,
.ops-panel input,
.ops-panel button,
.ops-panel select {
}

:root {
    --navy:        #1C2E5E;
    --navy-hover:  #243777;
    --navy-soft:   #EEF1F8;
    --navy-border: #C8D0E7;
    --gold:        #D4A017;
    --gold-light:  #FBF3DC;
    --amber:       #E07B00;
    --amber-soft:  #FFF3E0;
    --green:       #1A7F4B;
    --green-soft:  #E8F5EE;
    --red:         #C0392B;
    --red-soft:    #FDECEA;
    --bg:          #F4F5F8;
    --surface:     #FFFFFF;
    --border:      #E2E5ED;
    --text:        #1A1F36;
    --text-mid:    #4A5068;
    --muted:       #8B91A8;
    --radius-lg:   14px;
    --radius-md:   10px;
    --radius-sm:   7px;
    --shadow-card: 0 1px 3px rgba(28,46,94,0.06), 0 2px 8px rgba(28,46,94,0.04);
    --shadow-hover: 0 4px 16px rgba(28,46,94,0.10);
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

/* ── PANEL SHELL ─────────────────────────────── */
.ops-panel {
    background: var(--bg);
    min-height: 100vh;
    padding: 1.5rem 0 4rem;
    direction: rtl;
}

.ops-wrap {
    max-width: 1480px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

/* ── TOPBAR ─────────────────────────────────── */
.ops-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: .75rem;
}

.ops-brand {
    display: flex;
    align-items: center;
    gap: .875rem;
}

.ops-brand-icon {
    width: 46px;
    height: 46px;
    background: var(--navy);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gold);
    font-size: 22px;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(28,46,94,0.20);
}

.ops-brand-name {
    font-size: 1.45rem;
    font-weight: 900;
    color: var(--text);
    line-height: 1.1;
    letter-spacing: -.4px;
}

.ops-brand-sub {
    font-size: .78rem;
    color: var(--muted);
    font-weight: 500;
    margin-top: 1px;
}

.ops-topbar-right {
    display: flex;
    align-items: center;
    gap: .625rem;
}

.ops-badge-live {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    background: var(--green-soft);
    color: var(--green);
    font-size: .75rem;
    font-weight: 700;
    padding: .35rem .85rem;
    border-radius: 999px;
    border: 1.5px solid #A8DFC0;
}

.ops-live-dot {
    width: 7px; height: 7px;
    background: var(--green);
    border-radius: 50%;
    animation: blink 2s ease-in-out infinite;
}

@keyframes blink {
    0%,100% { opacity:1; transform:scale(1); }
    50%      { opacity:.4; transform:scale(.75); }
}

.ops-btn {
    display: inline-flex;
    align-items: center;
    gap: .375rem;
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--navy);
    font-size: .8rem;
    font-weight: 700;
    padding: .45rem .9rem;
    cursor: pointer;
    transition: background .15s, border-color .15s, box-shadow .15s;
    font-family: inherit;
    text-decoration: none;
    white-space: nowrap;
}

.ops-btn:hover {
    background: var(--navy-soft);
    border-color: var(--navy-border);
    box-shadow: var(--shadow-hover);
    text-decoration: none;
    color: var(--navy);
}

/* ── STOCK ALERT ────────────────────────────── */
.ops-stock-alert {
    background: var(--red-soft);
    border: 1.5px solid #F5C6C2;
    border-radius: var(--radius-md);
    padding: .85rem 1.25rem;
    display: flex;
    align-items: center;
    gap: .75rem;
    margin-bottom: 1.25rem;
}

.ops-stock-alert i { color: var(--red); font-size: 20px; flex-shrink: 0; }

.ops-stock-text {
    flex: 1;
    font-size: .85rem;
    font-weight: 600;
    color: #8B2020;
}

.ops-stock-text a { color: #8B2020; font-weight: 800; text-decoration: underline; }

.ops-stock-close {
    background: none; border: none; cursor: pointer;
    color: var(--muted); font-size: 16px; line-height: 1;
    padding: 0; flex-shrink: 0;
}

/* ── STATUS TILES ───────────────────────────── */
.ops-tiles {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.ops-tile {
    background: var(--surface);
    border: 2px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 1.375rem 1.25rem 1.125rem;
    position: relative;
    overflow: hidden;
    text-decoration: none;
    display: block;
    transition: transform .18s cubic-bezier(.22,1,.36,1),
                box-shadow .18s cubic-bezier(.22,1,.36,1),
                border-color .18s;
    box-shadow: var(--shadow-card);
}

.ops-tile::before {
    content: '';
    position: absolute;
    top: 0; right: 0; left: 0;
    height: 4px;
    border-radius: 0;
}

.ops-tile:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-hover);
    text-decoration: none;
}

.ops-tile--confirmed { border-color: #F5C97E; }
.ops-tile--confirmed::before { background: var(--amber); }

.ops-tile--cooking { border-color: #B9D9C3; }
.ops-tile--cooking::before { background: var(--green); }

.ops-tile--ready { border-color: var(--navy-border); }
.ops-tile--ready::before { background: var(--navy); }

.ops-tile--onway { border-color: #D0C0F5; }
.ops-tile--onway::before { background: #6C4FBF; }

/* Urgent pulsing border */
.ops-tile--urgent {
    animation: urgentBorder 1.8s ease-in-out infinite;
}

@keyframes urgentBorder {
    0%,100% { border-color: #F5C97E; }
    50%      { border-color: var(--amber); box-shadow: 0 0 0 4px rgba(224,123,0,.12); }
}

.ops-tile-bar {
    position: absolute;
    top: 1.1rem; left: 1.1rem;
    width: 8px; height: 8px;
    border-radius: 50%;
}

.ops-tile--confirmed .ops-tile-bar { background: var(--amber); }
.ops-tile--cooking   .ops-tile-bar { background: var(--green); }
.ops-tile--ready     .ops-tile-bar { background: var(--navy); }
.ops-tile--onway     .ops-tile-bar { background: #6C4FBF; }

.ops-tile-urgent-dot {
    animation: blink .9s ease-in-out infinite;
}

.ops-tile-num {
    font-size: 2.75rem;
    font-weight: 900;
    color: var(--text);
    line-height: 1;
    letter-spacing: -1.5px;
    direction: ltr;
    display: block;
    margin-bottom: .35rem;
}

.ops-tile-label {
    font-size: .8rem;
    font-weight: 600;
    color: var(--muted);
}

/* ── MAIN GRID ──────────────────────────────── */
.ops-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 300px;
    gap: 1.125rem;
    align-items: start;
}


/* ── SECTION TITLE ──────────────────────────── */
.ops-section-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: .875rem;
}

.ops-section-title {
    font-size: .95rem;
    font-weight: 800;
    color: var(--text);
}

.ops-section-link {
    font-size: .75rem;
    font-weight: 700;
    color: var(--navy);
    text-decoration: none;
}

.ops-section-link:hover { text-decoration: underline; }

/* ── KANBAN LANES ───────────────────────────── */
.ops-lanes {
    display: flex;
    flex-direction: column;
    gap: .875rem;
}

.ops-lane {
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-card);
}

.ops-lane-head {
    display: flex;
    align-items: center;
    gap: .5rem;
    padding: .75rem 1rem;
    border-bottom: 1px solid var(--border);
}

.ops-lane-dot {
    width: 9px; height: 9px;
    border-radius: 50%;
    flex-shrink: 0;
}

.ops-lane--confirmed .ops-lane-dot { background: var(--amber); }
.ops-lane--cooking   .ops-lane-dot { background: var(--green); }
.ops-lane--ready     .ops-lane-dot { background: var(--navy); }

.ops-lane-name {
    font-size: .82rem;
    font-weight: 800;
    color: var(--text);
    flex: 1;
}

.ops-lane-count {
    background: var(--navy-soft);
    color: var(--navy);
    font-size: .7rem;
    font-weight: 800;
    padding: .12rem .55rem;
    border-radius: 999px;
    min-width: 22px;
    text-align: center;
}

.ops-lane-body {
    padding: .625rem;
    display: flex;
    flex-direction: column;
    gap: .5rem;
    min-height: 80px;
}

/* ── ORDER CARD ─────────────────────────────── */
.ops-card {
    background: var(--bg);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-md);
    padding: .75rem .875rem;
    display: flex;
    flex-direction: column;
    gap: .4rem;
    text-decoration: none;
    color: inherit;
    transition: box-shadow .15s, border-color .15s;
    animation: cardSlide .2s ease-out;
}

@keyframes cardSlide {
    from { opacity:0; transform:translateY(5px); }
    to   { opacity:1; transform:translateY(0); }
}

.ops-card:hover {
    border-color: var(--navy-border);
    box-shadow: var(--shadow-hover);
    text-decoration: none;
    color: inherit;
}

.ops-card--new {
    background: var(--amber-soft);
    border-color: var(--amber);
    animation: cardSlide .25s ease-out, newPulse 2s .25s ease-in-out 3;
}

@keyframes newPulse {
    0%,100% { box-shadow: 0 0 0 0 rgba(224,123,0,0); }
    50%      { box-shadow: 0 0 0 4px rgba(224,123,0,.18); }
}

.ops-card-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .5rem;
}

.ops-card-id {
    font-size: .78rem;
    font-weight: 800;
    color: var(--navy);
    direction: ltr;
}

.ops-card-time {
    font-size: .7rem;
    color: var(--muted);
    font-weight: 500;
    white-space: nowrap;
}

.ops-badge-new {
    background: var(--amber);
    color: #fff;
    font-size: .62rem;
    font-weight: 800;
    padding: .1rem .45rem;
    border-radius: 999px;
    letter-spacing: .3px;
    text-transform: uppercase;
}

.ops-card-name {
    font-size: .85rem;
    font-weight: 700;
    color: var(--text);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.ops-card-items {
    font-size: .75rem;
    color: var(--muted);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.ops-card-foot {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .5rem;
    margin-top: .1rem;
}

.ops-card-amount {
    font-size: .85rem;
    font-weight: 800;
    color: var(--text);
    direction: ltr;
}

.ops-action {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    padding: .32rem .75rem;
    border-radius: var(--radius-sm);
    font-size: .74rem;
    font-weight: 800;
    border: none;
    cursor: pointer;
    font-family: inherit;
    text-decoration: none;
    white-space: nowrap;
    transition: opacity .15s, transform .1s;
    letter-spacing: .1px;
}

.ops-action:hover { opacity:.85; transform:scale(.98); text-decoration:none; }
.ops-action:active { transform:scale(.95); }

.ops-action--go   { background: var(--amber);  color: #fff; }
.ops-action--cook { background: var(--navy);   color: #fff; }
.ops-action--done { background: var(--green);  color: #fff; }

.ops-lane-empty {
    text-align: center;
    padding: 1.5rem 1rem;
    color: var(--muted);
    font-size: .78rem;
    font-weight: 600;
}

.ops-lane-empty i {
    display: block;
    font-size: 1.75rem;
    margin-bottom: .4rem;
    opacity: .25;
}

/* ── ACTIVITY COLUMN ────────────────────────── */
.ops-activity {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.ops-panel-card {
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-card);
}

.ops-panel-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .875rem 1.125rem;
    border-bottom: 1px solid var(--border);
}

.ops-panel-title {
    font-size: .875rem;
    font-weight: 800;
    color: var(--text);
    display: flex;
    align-items: center;
    gap: .5rem;
}

.ops-panel-title i {
    font-size: 16px;
    color: var(--navy);
    opacity: .7;
}

.ops-panel-more {
    font-size: .73rem;
    font-weight: 700;
    color: var(--navy);
    text-decoration: none;
}

.ops-panel-more:hover { text-decoration: underline; }

/* Activity list items */
.ops-activity-item {
    display: flex;
    align-items: flex-start;
    gap: .75rem;
    padding: .75rem 1.125rem;
    border-bottom: 1px solid var(--border);
    text-decoration: none;
    color: inherit;
    transition: background .12s;
}

.ops-activity-item:last-child { border-bottom: none; }

.ops-activity-item:hover {
    background: var(--navy-soft);
    text-decoration: none;
    color: inherit;
}

.ops-activity-icon {
    width: 32px; height: 32px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
    margin-top: 1px;
}

.ops-icon--order   { background: var(--navy-soft); color: var(--navy); }
.ops-icon--msg     { background: var(--gold-light); color: var(--gold); }
.ops-icon--alert   { background: var(--amber-soft); color: var(--amber); }
.ops-icon--done    { background: var(--green-soft); color: var(--green); }

.ops-activity-content { flex: 1; min-width: 0; }

.ops-activity-main {
    font-size: .82rem;
    font-weight: 700;
    color: var(--text);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.ops-activity-sub {
    font-size: .73rem;
    color: var(--muted);
    margin-top: 1px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.ops-activity-time {
    font-size: .68rem;
    color: var(--muted);
    white-space: nowrap;
    flex-shrink: 0;
    padding-top: 2px;
}

.ops-unread-dot {
    width: 7px; height: 7px;
    background: var(--amber);
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 6px;
}

/* ── SIDEBAR ────────────────────────────────── */
.ops-sidebar {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

/* Quick nav */
.ops-quick-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: .5rem;
    padding: .75rem;
}

.ops-quick-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: .3rem;
    padding: .875rem .5rem;
    background: var(--bg);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-md);
    text-decoration: none;
    color: var(--text);
    font-size: .73rem;
    font-weight: 700;
    text-align: center;
    transition: background .15s, border-color .15s;
}

.ops-quick-btn i { font-size: 20px; color: var(--navy); margin-bottom: 1px; }

.ops-quick-btn:hover {
    background: var(--navy-soft);
    border-color: var(--navy-border);
    text-decoration: none;
    color: var(--text);
}

/* Today summary */
.ops-summary-body {
    padding: .875rem 1.125rem;
    display: flex;
    flex-direction: column;
    gap: .75rem;
}

.ops-summary-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.ops-summary-label {
    font-size: .78rem;
    color: var(--muted);
    font-weight: 600;
}

.ops-summary-val {
    font-size: 1.05rem;
    font-weight: 900;
    color: var(--text);
    direction: ltr;
}

.ops-summary-val--amber  { color: var(--amber); }
.ops-summary-val--green  { color: var(--green); }

/* Secondary stats strip */
.ops-sec-strip {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: .75rem;
    margin-top: 1.125rem;
}

.ops-sec-tile {
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-md);
    padding: .875rem 1rem;
    display: flex;
    align-items: center;
    gap: .75rem;
    text-decoration: none;
    transition: border-color .15s, box-shadow .15s;
    box-shadow: var(--shadow-card);
}

.ops-sec-tile:hover {
    border-color: var(--navy-border);
    box-shadow: var(--shadow-hover);
    text-decoration: none;
}

.ops-sec-icon {
    width: 36px; height: 36px;
    background: var(--navy-soft);
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--navy);
    font-size: 17px;
    flex-shrink: 0;
}

.ops-sec-val {
    font-size: 1.25rem;
    font-weight: 900;
    color: var(--text);
    line-height: 1;
    direction: ltr;
}

.ops-sec-lbl {
    font-size: .7rem;
    color: var(--muted);
    font-weight: 600;
    margin-top: 2px;
}

/* ── TOAST ──────────────────────────────────── */
.ops-toast {
    position: fixed;
    top: 1.5rem;
    right: 1.5rem;
    z-index: 9999;
    background: var(--surface);
    border: 2px solid var(--amber);
    border-radius: var(--radius-lg);
    padding: 1rem 1.25rem;
    box-shadow: var(--shadow-hover), 0 0 0 4px rgba(224,123,0,.1);
    min-width: 280px;
    display: none;
    animation: toastIn .3s cubic-bezier(.22,1,.36,1);
    direction: rtl;
}

@keyframes toastIn {
    from { opacity:0; transform:translateX(16px); }
    to   { opacity:1; transform:translateX(0); }
}

.ops-toast.show { display: block; }

.ops-toast-title {
    font-size: .92rem;
    font-weight: 800;
    color: var(--text);
    margin-bottom: .3rem;
}

.ops-toast-body {
    font-size: .8rem;
    color: var(--muted);
    margin-bottom: .75rem;
}

.ops-toast-actions { display: flex; gap: .5rem; }

.ops-toast-print {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    background: var(--navy);
    color: #fff;
    font-size: .78rem;
    font-weight: 800;
    padding: .4rem .85rem;
    border-radius: var(--radius-sm);
    border: none;
    cursor: pointer;
    font-family: inherit;
}

.ops-toast-dismiss {
    background: none;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: .4rem .75rem;
    font-size: .78rem;
    color: var(--muted);
    cursor: pointer;
    font-family: inherit;
}

/* ── RESPONSIVE ─────────────────────────────── */
@media (max-width: 1200px) {
    .ops-grid { grid-template-columns: 1fr 1fr; }
    .ops-sidebar { display: none; }
}

@media (max-width: 900px) {
    .ops-tiles { grid-template-columns: repeat(2, 1fr); }
    .ops-grid { grid-template-columns: 1fr; }
    .ops-sec-strip { grid-template-columns: repeat(2, 1fr); }
    .ops-activity { flex-direction: column; }
}

@media (max-width: 600px) {
    .ops-wrap { padding: 0 .875rem; }
    .ops-tiles { grid-template-columns: repeat(2, 1fr); gap: .625rem; }
    .ops-tile-num { font-size: 2.2rem; }
    .ops-grid { grid-template-columns: 1fr; }
    .ops-topbar { gap: .5rem; }
    .ops-brand-name { font-size: 1.25rem; }
}

/* ── FOCUS ──────────────────────────────────── */
.ops-tile:focus-visible,
.ops-btn:focus-visible,
.ops-card:focus-visible,
.ops-action:focus-visible,
.ops-quick-btn:focus-visible,
.ops-activity-item:focus-visible,
.ops-sec-tile:focus-visible {
    outline: 2.5px solid var(--navy);
    outline-offset: 2px;
}

/* ── PRINT ──────────────────────────────────── */
@media print {
    .ops-topbar, .ops-tiles, .ops-grid, .ops-sec-strip,
    .ops-toast, .ops-sidebar { display: none !important; }
}
</style>
@endpush

@section('content')
<div class="ops-panel">
<div class="ops-wrap">

@if(auth('vendor')->check())
@php
    $restaurant_id = auth('vendor')->user()->restaurants[0]->id ?? null;
@endphp

{{-- ── NEW ORDER TOAST ───────────────────── --}}
<div class="ops-toast" id="ops-toast">
    <div class="ops-toast-title">🛎 طلب جديد وصل!</div>
    <div class="ops-toast-body" id="ops-toast-body">جاري التحديث...</div>
    <div class="ops-toast-actions">
        <button class="ops-toast-print" onclick="printLatest()"><i class="tio-print"></i> طباعة</button>
        <button class="ops-toast-dismiss" onclick="dismissToast()">تجاهل</button>
    </div>
</div>
<iframe id="ops-print-frame" style="display:none"></iframe>

{{-- ── TOPBAR ────────────────────────────── --}}
<div class="ops-topbar">
    <div class="ops-brand">
        <div class="ops-brand-icon"><i class="tio-restaurant"></i></div>
        <div>
            <div class="ops-brand-name">{{ \App\CentralLogics\Helpers::get_restaurant_data()->name ?? 'Beit Jedi' }}</div>
            <div class="ops-brand-sub">مركز إدارة الطلبات</div>
        </div>
    </div>
    <div class="ops-topbar-right">
        <span class="ops-badge-live"><span class="ops-live-dot"></span> مباشر</span>
        <button class="ops-btn" onclick="refreshStats()"><i class="tio-refresh"></i> تحديث</button>
        <a href="{{ route('vendor.order.list', ['all']) }}" class="ops-btn"><i class="tio-receipt"></i> كل الطلبات</a>
    </div>
</div>

{{-- ── STOCK ALERT ───────────────────────── --}}
@if(Session::get('stock_out_reminder_close_btn') !== true && isset($out_out_count) && $out_out_count > 0)
<div class="ops-stock-alert" id="ops-stock-alert">
    <i class="tio-warning-outlined"></i>
    <span class="ops-stock-text">
        @if($out_out_count == 1 && isset($food))
            {{ $food?->name }} — نفدت الكمية
        @else
            {{ $out_out_count }} منتجات نفدت من المخزن
        @endif
        &nbsp;<a href="{{ route('vendor.food.stockOutList') }}">عرض القائمة</a>
    </span>
    <button class="ops-stock-close add-to-session" data-id="stock_out_reminder_close_btn"
        onclick="this.closest('.ops-stock-alert').remove()"><i class="tio-clear"></i></button>
</div>
@endif

{{-- ── STATUS TILES ──────────────────────── --}}
<div class="ops-tiles" id="order_stats">
    @include('vendor-views.partials._dashboard-order-stats', ['data' => $data])
</div>

{{-- ── MAIN GRID ─────────────────────────── --}}
@php
    // Recent order activity (last 12 orders of any status)
    $recent_orders = \App\Models\Order::where('restaurant_id', $restaurant_id)
        ->with('customer')
        ->latest()->take(12)->get();

    // Recent messages/conversations
    $sender = auth('vendor')->user();
    $recent_convs = \App\Models\Conversation::with(['sender','last_message'])
        ->where(function($q) use ($sender) {
            $q->where('sender_id', $sender?->id)
              ->orWhere('receiver_id', $sender?->id);
        })
        ->latest('updated_at')
        ->take(8)
        ->get();

    // Today summary
    $today_orders = \App\Models\Order::where('restaurant_id', $restaurant_id)
        ->whereDate('created_at', today())->get();
    $today_revenue  = $today_orders->whereIn('order_status',['delivered'])->sum('order_amount');
    $today_count    = $today_orders->count();
    $today_pending  = $today_orders->whereIn('order_status',['confirmed','accepted','cooking','ready_for_delivery'])->count();
@endphp

<div class="ops-grid">

    {{-- ── COL 1: RECENT ORDERS ────────────── --}}
    <div class="ops-panel-card">
        <div class="ops-panel-head">
            <span class="ops-panel-title">
                <i class="tio-receipt"></i> آخر الطلبات
            </span>
            <a href="{{ route('vendor.order.list', ['all']) }}" class="ops-panel-more">عرض الكل</a>
        </div>
        @forelse($recent_orders as $order)
        @php
            $statusMap = [
                'pending'            => ['label' => 'معلّق',           'icon' => 'tio-time',                    'cls' => 'ops-icon--alert'],
                'confirmed'          => ['label' => 'مؤكد',            'icon' => 'tio-checkmark-circle',        'cls' => 'ops-icon--order'],
                'accepted'           => ['label' => 'مؤكد',            'icon' => 'tio-checkmark-circle',        'cls' => 'ops-icon--order'],
                'cooking'            => ['label' => 'قيد التحضير',     'icon' => 'tio-restaurant',              'cls' => 'ops-icon--alert'],
                'ready_for_delivery' => ['label' => 'جاهز للتسليم',   'icon' => 'tio-done',                    'cls' => 'ops-icon--done'],
                'food_on_the_way'    => ['label' => 'في الطريق',      'icon' => 'tio-delivery',                'cls' => 'ops-icon--order'],
                'delivered'          => ['label' => 'تم التسليم',     'icon' => 'tio-checkmark-circle-outlined','cls' => 'ops-icon--done'],
                'canceled'           => ['label' => 'ملغي',            'icon' => 'tio-clear',                   'cls' => 'ops-icon--alert'],
                'refunded'           => ['label' => 'مُسترد',          'icon' => 'tio-money',                   'cls' => 'ops-icon--alert'],
            ];
            $s = $statusMap[$order->order_status] ?? ['label' => $order->order_status, 'icon' => 'tio-receipt', 'cls' => 'ops-icon--order'];
        @endphp
        <a href="{{ route('vendor.order.details', ['id' => $order->id]) }}" class="ops-activity-item">
            <div class="ops-activity-icon {{ $s['cls'] }}">
                <i class="{{ $s['icon'] }}"></i>
            </div>
            <div class="ops-activity-content">
                <div class="ops-activity-main">
                    طلب #{{ $order->id }}
                    — {{ $order->customer->f_name ?? 'عميل' }} {{ $order->customer->l_name ?? '' }}
                </div>
                <div class="ops-activity-sub">{{ $s['label'] }} · {{ \App\CentralLogics\Helpers::format_currency($order->order_amount) }}</div>
            </div>
            <div class="ops-activity-time">{{ \Carbon\Carbon::parse($order->created_at)->diffForHumans(null, true) }}</div>
        </a>
        @empty
        <div class="ops-lane-empty"><i class="tio-receipt"></i>لا طلبات حتى الآن</div>
        @endforelse
    </div>{{-- /col 1 --}}

    {{-- ── COL 2: RECENT MESSAGES ──────────── --}}
    <div class="ops-panel-card">
        <div class="ops-panel-head">
            <span class="ops-panel-title">
                <i class="tio-messages"></i> آخر الرسائل
            </span>
            <a href="{{ route('vendor.message.list') }}" class="ops-panel-more">عرض الكل</a>
        </div>
        @forelse($recent_convs as $conv)
        @php
            $otherUser = $conv->sender_id === $sender?->id ? $conv->receiver : $conv->sender;
            $lastMsg   = $conv->last_message?->message ?? '...';
            $unread    = ($conv->unread_message_count ?? 0) > 0;
        @endphp
        <a href="{{ route('vendor.message.view', ['conversation_id' => $conv->id, 'user_id' => $otherUser?->id ?? 0]) }}"
           class="ops-activity-item">
            <div class="ops-activity-icon ops-icon--msg">
                <i class="tio-chat-outlined"></i>
            </div>
            <div class="ops-activity-content">
                <div class="ops-activity-main">
                    {{ $otherUser?->f_name ?? 'مستخدم' }} {{ $otherUser?->l_name ?? '' }}
                </div>
                <div class="ops-activity-sub">{{ Str::limit($lastMsg, 45) }}</div>
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.25rem;">
                <div class="ops-activity-time">{{ \Carbon\Carbon::parse($conv->updated_at)->diffForHumans(null, true) }}</div>
                @if($unread)<div class="ops-unread-dot"></div>@endif
            </div>
        </a>
        @empty
        <div class="ops-lane-empty"><i class="tio-messages"></i>لا رسائل حتى الآن</div>
        @endforelse
    </div>{{-- /col 2 --}}

    {{-- ── COL 3: SIDEBAR ──────────────────── --}}
    <div class="ops-sidebar">

        {{-- Quick Nav --}}
        <div class="ops-panel-card">
            <div class="ops-panel-head">
                <span class="ops-panel-title"><i class="tio-apps"></i> تنقل سريع</span>
            </div>
            <div class="ops-quick-grid">
                <a href="{{ route('vendor.food.list') }}" class="ops-quick-btn">
                    <i class="tio-restaurant"></i> القائمة
                </a>
                <a href="{{ route('vendor.order.list', ['all']) }}" class="ops-quick-btn">
                    <i class="tio-receipt"></i> الطلبات
                </a>
                <a href="{{ route('vendor.delivery-man.list') }}" class="ops-quick-btn">
                    <i class="tio-delivery"></i> المناديب
                </a>
                <a href="{{ route('vendor.shop.edit') }}" class="ops-quick-btn">
                    <i class="tio-settings-outlined"></i> الإعدادات
                </a>
            </div>
        </div>

        {{-- Today Summary --}}
        <div class="ops-panel-card">
            <div class="ops-panel-head">
                <span class="ops-panel-title"><i class="tio-chart-bar-4"></i> ملخص اليوم</span>
            </div>
            <div class="ops-summary-body">
                <div class="ops-summary-row">
                    <span class="ops-summary-label">إجمالي الطلبات</span>
                    <span class="ops-summary-val">{{ $today_count }}</span>
                </div>
                <div class="ops-summary-row">
                    <span class="ops-summary-label">قيد التنفيذ</span>
                    <span class="ops-summary-val ops-summary-val--amber">{{ $today_pending }}</span>
                </div>
                <div class="ops-summary-row">
                    <span class="ops-summary-label">إيرادات اليوم</span>
                    <span class="ops-summary-val ops-summary-val--green">{{ \App\CentralLogics\Helpers::format_currency($today_revenue) }}</span>
                </div>
            </div>
        </div>

        {{-- Period filter --}}
        <div class="ops-panel-card">
            <div class="ops-panel-head">
                <span class="ops-panel-title"><i class="tio-filter-list"></i> فترة الإحصاء</span>
            </div>
            <div style="padding:.75rem;">
                <select class="ops-btn" style="width:100%;justify-content:center;"
                    onchange="filterStats(this.value)">
                    <option value="today" {{ ($params['statistics_type'] ?? 'today') == 'today' ? 'selected' : '' }}>اليوم</option>
                    <option value="this_month" {{ ($params['statistics_type'] ?? '') == 'this_month' ? 'selected' : '' }}>هذا الشهر</option>
                    <option value="overall" {{ ($params['statistics_type'] ?? '') == 'overall' ? 'selected' : '' }}>الإجمالي</option>
                </select>
            </div>
        </div>

    </div>{{-- /ops-sidebar --}}

</div>{{-- /ops-grid --}}

{{-- ── SECONDARY STATS STRIP ──────────────── --}}
<div class="ops-sec-strip">
    <a href="{{ route('vendor.order.list', ['delivered']) }}" class="ops-sec-tile">
        <div class="ops-sec-icon"><i class="tio-checkmark-circle"></i></div>
        <div><div class="ops-sec-val">{{ $data['delivered'] }}</div><div class="ops-sec-lbl">تم التسليم</div></div>
    </a>
    <a href="{{ route('vendor.order.list', ['food_on_the_way']) }}" class="ops-sec-tile">
        <div class="ops-sec-icon"><i class="tio-delivery"></i></div>
        <div><div class="ops-sec-val">{{ $data['food_on_the_way'] }}</div><div class="ops-sec-lbl">في الطريق</div></div>
    </a>
    <a href="{{ route('vendor.order.list', ['scheduled']) }}" class="ops-sec-tile">
        <div class="ops-sec-icon"><i class="tio-time"></i></div>
        <div><div class="ops-sec-val">{{ $data['scheduled'] }}</div><div class="ops-sec-lbl">مجدولة</div></div>
    </a>
    <a href="{{ route('vendor.order.list', ['all']) }}" class="ops-sec-tile">
        <div class="ops-sec-icon"><i class="tio-receipt"></i></div>
        <div><div class="ops-sec-val">{{ $data['all'] }}</div><div class="ops-sec-lbl">كل الطلبات</div></div>
    </a>
</div>

@else
{{-- Employee fallback --}}
<div style="padding:4rem 0;text-align:center;">
    <h2 style="font-weight:800;color:var(--text);">
        {{ translate('messages.welcome') }}, {{ auth('vendor_employee')->user()->f_name }}
    </h2>
    <p style="color:var(--muted);">{{ translate('messages.employee_welcome_message') }}</p>
</div>
@endif

</div>{{-- /ops-wrap --}}
</div>{{-- /ops-panel --}}

<iframe id="ops-print-frame" style="display:none"></iframe>
@endsection

@push('script')
<script>
(function(){
    'use strict';

    var lastConfirmed = {{ $data['confirmed'] ?? 0 }};
    var latestOrderId = null;
    var audioCtx      = null;

    // ── Audio unlock on first click ──────────
    document.addEventListener('click', function unlock(){
        try { audioCtx = new (window.AudioContext||window.webkitAudioContext)(); } catch(e){}
        document.removeEventListener('click', unlock);
    }, {once:true});

    function beep(freq, start, dur){
        if(!audioCtx) return;
        var o = audioCtx.createOscillator();
        var g = audioCtx.createGain();
        o.connect(g); g.connect(audioCtx.destination);
        o.type = 'sine';
        o.frequency.setValueAtTime(freq, audioCtx.currentTime + start);
        g.gain.setValueAtTime(0.35, audioCtx.currentTime + start);
        g.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + start + dur);
        o.start(audioCtx.currentTime + start);
        o.stop(audioCtx.currentTime + start + dur);
    }

    function playAlert(){
        beep(880, 0,    0.35);
        beep(660, 0.45, 0.35);
        beep(880, 0.9,  0.35);
    }

    function autoPrint(id){
        if(!id) return;
        var frame = document.getElementById('ops-print-frame');
        if(!frame) return;
        var url = '{{ route("vendor.order.generate-invoice", ["id" => "__X__"]) }}'.replace('__X__', id);
        frame.onload = function(){
            try { frame.contentWindow.focus(); frame.contentWindow.print(); } catch(e){}
        };
        frame.src = url;
    }

    window.printLatest = function(){
        if(latestOrderId) autoPrint(latestOrderId);
        dismissToast();
    };

    window.dismissToast = function(){
        var t = document.getElementById('ops-toast');
        if(t) t.classList.remove('show');
    };

    function showToast(id, customer){
        latestOrderId = id;
        var t    = document.getElementById('ops-toast');
        var body = document.getElementById('ops-toast-body');
        if(body) body.textContent = 'طلب #' + id + ' — ' + (customer || 'عميل جديد');
        if(t){ t.classList.add('show'); playAlert(); autoPrint(id); }
        setTimeout(dismissToast, 14000);
    }

    // ── Refresh stats via AJAX ───────────────
    function refreshStats(){
        var type = document.querySelector('[onchange^="filterStats"]')?.value || 'today';
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        $.post('{{ route("vendor.dashboard.order-stats") }}', { statistics_type: type }, function(data){
            var el = document.getElementById('order_stats');
            if(el && data.view) el.innerHTML = data.view;
            var newConfirmed = parseInt(data.data?.confirmed ?? 0);
            if(newConfirmed > lastConfirmed){
                var id  = data.data?.latest_order_id  || null;
                var cus = data.data?.latest_customer   || '';
                showToast(id, cus);
                lastConfirmed = newConfirmed;
            }
        });
    }

    window.refreshStats = refreshStats;

    window.filterStats = function(type){
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        $.post('{{ route("vendor.dashboard.order-stats") }}', { statistics_type: type }, function(data){
            var el = document.getElementById('order_stats');
            if(el && data.view) el.innerHTML = data.view;
        });
    };

    // ── Session close ────────────────────────
    $(document).on('click', '.add-to-session', function(){
        $.ajax({
            url: '{{ route("vendor.food.addToSession") }}',
            method: 'POST',
            data: { value: $(this).data('id'), _token: '{{ csrf_token() }}' }
        });
    });

    // ── Auto-refresh every 40s ───────────────
    var timer = setInterval(refreshStats, 40000);
    window.addEventListener('beforeunload', function(){ clearInterval(timer); });

})();
</script>
@endpush
