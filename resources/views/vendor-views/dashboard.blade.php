@extends('layouts.vendor.app')

@section('title', translate('messages.dashboard'))

@push('css_or_js')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap');

    :root {
        --navy:       oklch(32% 0.08 255);
        --navy-mid:   oklch(40% 0.07 255);
        --navy-soft:  oklch(92% 0.015 255);
        --gold:       oklch(80% 0.14 80);
        --gold-deep:  oklch(68% 0.16 75);
        --amber:      oklch(72% 0.18 55);
        --green:      oklch(60% 0.17 145);
        --green-soft: oklch(94% 0.06 145);
        --red:        oklch(56% 0.20 25);
        --red-soft:   oklch(95% 0.04 25);
        --bg:         oklch(97% 0.008 255);
        --surface:    oklch(99.5% 0.004 255);
        --border:     oklch(90% 0.01 255);
        --text:       oklch(22% 0.04 255);
        --muted:      oklch(58% 0.025 255);
        --radius:     16px;
        --radius-sm:  10px;
        --shadow-sm:  0 1px 3px oklch(0% 0 0 / 0.06), 0 1px 2px oklch(0% 0 0 / 0.04);
        --shadow-md:  0 4px 12px oklch(0% 0 0 / 0.08);
    }

    *, *::before, *::after { box-sizing: border-box; }

    body, .ops-panel {
        font-family: 'Cairo', system-ui, sans-serif;
        direction: rtl;
    }

    /* ── LAYOUT ─────────────────────────────── */
    .ops-panel {
        background: var(--bg);
        min-height: 100vh;
        padding: 1.5rem 0 3rem;
    }

    .ops-container {
        max-width: 1440px;
        margin: 0 auto;
        padding: 0 1.5rem;
    }

    /* ── TOPBAR ─────────────────────────────── */
    .ops-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.75rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .ops-brand {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .ops-brand-logo {
        width: 44px;
        height: 44px;
        background: var(--navy);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--gold);
        font-size: 22px;
        flex-shrink: 0;
    }

    .ops-brand-name {
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--text);
        line-height: 1.1;
        letter-spacing: -0.3px;
    }

    .ops-brand-sub {
        font-size: 0.8rem;
        color: var(--muted);
        font-weight: 500;
        margin-top: 1px;
    }

    .ops-topbar-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .ops-live-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: var(--green-soft);
        color: var(--green);
        font-size: 0.78rem;
        font-weight: 700;
        padding: 0.35rem 0.85rem;
        border-radius: 999px;
        border: 1px solid color-mix(in oklch, var(--green) 25%, transparent);
        letter-spacing: 0.3px;
    }

    .ops-live-dot {
        width: 7px;
        height: 7px;
        background: var(--green);
        border-radius: 50%;
        animation: livePulse 2s ease-in-out infinite;
    }

    @keyframes livePulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: 0.5; transform: scale(0.8); }
    }

    .ops-refresh-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        color: var(--navy);
        font-size: 0.82rem;
        font-weight: 600;
        padding: 0.45rem 0.9rem;
        cursor: pointer;
        transition: background 0.15s, border-color 0.15s;
        font-family: inherit;
        text-decoration: none;
    }

    .ops-refresh-btn:hover {
        background: var(--navy-soft);
        border-color: var(--navy);
        color: var(--navy);
        text-decoration: none;
    }

    /* ── URGENT ALERTS ──────────────────────── */
    .ops-alert-bar {
        background: var(--red-soft);
        border: 1px solid color-mix(in oklch, var(--red) 20%, transparent);
        border-radius: var(--radius);
        padding: 0.9rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        animation: slideIn 0.3s ease-out;
    }

    .ops-alert-bar .alert-icon {
        font-size: 20px;
        color: var(--red);
        flex-shrink: 0;
    }

    .ops-alert-bar .alert-text {
        font-size: 0.875rem;
        font-weight: 600;
        color: color-mix(in oklch, var(--red) 80%, var(--text));
        flex: 1;
    }

    .ops-alert-close {
        background: none;
        border: none;
        cursor: pointer;
        color: var(--muted);
        font-size: 16px;
        padding: 0;
        line-height: 1;
    }

    /* ── STATUS SUMMARY STRIP ───────────────── */
    .ops-status-strip {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.875rem;
        margin-bottom: 1.75rem;
    }

    .ops-status-tile {
        background: var(--surface);
        border: 1.5px solid var(--border);
        border-radius: var(--radius);
        padding: 1.25rem 1rem 1rem;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        position: relative;
        overflow: hidden;
        transition: transform 0.15s ease-out, box-shadow 0.15s ease-out, border-color 0.15s;
        cursor: pointer;
    }

    .ops-status-tile::after {
        content: '';
        position: absolute;
        top: 0; right: 0; left: 0;
        height: 3px;
        border-radius: 999px 999px 0 0;
    }

    .ops-status-tile:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        text-decoration: none;
    }

    .ops-status-tile--confirmed { border-color: color-mix(in oklch, var(--amber) 35%, transparent); }
    .ops-status-tile--confirmed::after { background: var(--amber); }

    .ops-status-tile--cooking { border-color: color-mix(in oklch, var(--gold-deep) 35%, transparent); }
    .ops-status-tile--cooking::after { background: var(--gold-deep); }

    .ops-status-tile--ready { border-color: color-mix(in oklch, var(--green) 35%, transparent); }
    .ops-status-tile--ready::after { background: var(--green); }

    .ops-status-tile--onway { border-color: color-mix(in oklch, var(--navy-mid) 35%, transparent); }
    .ops-status-tile--onway::after { background: var(--navy-mid); }

    .ops-status-tile--urgent {
        animation: tileUrgent 1.8s ease-in-out infinite;
    }

    @keyframes tileUrgent {
        0%, 100% { border-color: color-mix(in oklch, var(--amber) 35%, transparent); }
        50%       { border-color: var(--amber); box-shadow: 0 0 0 3px color-mix(in oklch, var(--amber) 15%, transparent); }
    }

    .ops-tile-count {
        font-size: 2.4rem;
        font-weight: 900;
        line-height: 1;
        letter-spacing: -1px;
        color: var(--text);
    }

    .ops-tile-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--muted);
    }

    .ops-tile-indicator {
        position: absolute;
        top: 1rem;
        left: 1rem;
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .ops-status-tile--confirmed .ops-tile-indicator { background: var(--amber); }
    .ops-status-tile--cooking  .ops-tile-indicator { background: var(--gold-deep); }
    .ops-status-tile--ready    .ops-tile-indicator { background: var(--green); }
    .ops-status-tile--onway    .ops-tile-indicator { background: var(--navy-mid); }

    .ops-tile-arrow {
        position: absolute;
        bottom: 1rem;
        left: 1rem;
        font-size: 14px;
        color: var(--border);
        transition: color 0.15s, transform 0.15s;
    }

    .ops-status-tile:hover .ops-tile-arrow {
        color: var(--muted);
        transform: translateX(-3px);
    }

    /* ── MAIN CONTENT GRID ──────────────────── */
    .ops-main-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 1.25rem;
        align-items: start;
    }

    /* ── KANBAN LANES ───────────────────────── */
    .ops-lanes-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .ops-lanes-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--text);
    }

    .ops-filter-tabs {
        display: flex;
        gap: 0.4rem;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 0.25rem;
    }

    .ops-filter-tab {
        padding: 0.3rem 0.75rem;
        border-radius: 7px;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--muted);
        cursor: pointer;
        border: none;
        background: transparent;
        font-family: inherit;
        transition: background 0.15s, color 0.15s;
    }

    .ops-filter-tab.active {
        background: var(--navy);
        color: #fff;
    }

    .ops-lanes {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }

    .ops-lane {
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
    }

    .ops-lane-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.875rem 1rem;
        border-bottom: 1px solid var(--border);
        background: var(--surface);
    }

    .ops-lane-dot {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .ops-lane--confirmed .ops-lane-dot { background: var(--amber); }
    .ops-lane--cooking   .ops-lane-dot { background: var(--gold-deep); }
    .ops-lane--ready     .ops-lane-dot { background: var(--green); }

    .ops-lane-name {
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--text);
        flex: 1;
    }

    .ops-lane-count {
        background: var(--navy-soft);
        color: var(--navy);
        font-size: 0.72rem;
        font-weight: 800;
        padding: 0.15rem 0.5rem;
        border-radius: 999px;
        min-width: 22px;
        text-align: center;
    }

    .ops-lane-body {
        padding: 0.75rem;
        display: flex;
        flex-direction: column;
        gap: 0.625rem;
        min-height: 120px;
    }

    /* ── ORDER CARD ─────────────────────────── */
    .ops-order-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 0.9rem 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        cursor: pointer;
        transition: box-shadow 0.15s, border-color 0.15s;
        animation: cardIn 0.25s ease-out;
        text-decoration: none;
        color: inherit;
    }

    @keyframes cardIn {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .ops-order-card:hover {
        box-shadow: var(--shadow-md);
        border-color: color-mix(in oklch, var(--navy) 20%, transparent);
        text-decoration: none;
        color: inherit;
    }

    .ops-order-card--new {
        border-color: var(--amber);
        box-shadow: 0 0 0 2px color-mix(in oklch, var(--amber) 20%, transparent);
        animation: newOrder 0.4s ease-out, cardPulse 2s 0.4s ease-in-out 3;
    }

    @keyframes newOrder {
        from { opacity: 0; transform: scale(0.96); }
        to   { opacity: 1; transform: scale(1); }
    }

    @keyframes cardPulse {
        0%, 100% { box-shadow: 0 0 0 2px color-mix(in oklch, var(--amber) 20%, transparent); }
        50%       { box-shadow: 0 0 0 4px color-mix(in oklch, var(--amber) 30%, transparent); }
    }

    .ops-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .ops-card-id {
        font-size: 0.8rem;
        font-weight: 800;
        color: var(--navy);
        letter-spacing: 0.3px;
    }

    .ops-card-time {
        font-size: 0.72rem;
        color: var(--muted);
        font-weight: 500;
    }

    .ops-card-customer {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .ops-card-items {
        font-size: 0.76rem;
        color: var(--muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .ops-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 0.1rem;
    }

    .ops-card-amount {
        font-size: 0.85rem;
        font-weight: 800;
        color: var(--text);
    }

    .ops-advance-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.35rem 0.75rem;
        border-radius: 7px;
        font-size: 0.76rem;
        font-weight: 700;
        border: none;
        cursor: pointer;
        font-family: inherit;
        transition: opacity 0.15s, transform 0.1s;
        white-space: nowrap;
        text-decoration: none;
    }

    .ops-advance-btn:hover {
        opacity: 0.88;
        transform: scale(0.98);
        text-decoration: none;
    }

    .ops-advance-btn:active { transform: scale(0.95); }

    .ops-advance-btn--confirm {
        background: var(--amber);
        color: oklch(20% 0.05 80);
    }

    .ops-advance-btn--cook {
        background: var(--navy);
        color: #fff;
    }

    .ops-advance-btn--ready {
        background: var(--green);
        color: #fff;
    }

    .ops-card-new-badge {
        background: var(--amber);
        color: oklch(20% 0.05 80);
        font-size: 0.65rem;
        font-weight: 800;
        padding: 0.12rem 0.45rem;
        border-radius: 999px;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }

    .ops-empty-lane {
        text-align: center;
        padding: 2rem 1rem;
        color: var(--muted);
        font-size: 0.8rem;
    }

    .ops-empty-lane i {
        display: block;
        font-size: 2rem;
        margin-bottom: 0.5rem;
        opacity: 0.3;
    }

    /* ── SECONDARY STATS ─────────────────────── */
    .ops-secondary-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.75rem;
        margin-top: 1.25rem;
    }

    .ops-sec-stat {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 0.9rem 1rem;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transition: border-color 0.15s;
    }

    .ops-sec-stat:hover {
        border-color: color-mix(in oklch, var(--navy) 25%, transparent);
        text-decoration: none;
    }

    .ops-sec-stat-icon {
        width: 36px;
        height: 36px;
        background: var(--navy-soft);
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--navy);
        font-size: 17px;
        flex-shrink: 0;
    }

    .ops-sec-stat-val {
        font-size: 1.3rem;
        font-weight: 800;
        color: var(--text);
        line-height: 1;
    }

    .ops-sec-stat-lbl {
        font-size: 0.72rem;
        color: var(--muted);
        font-weight: 500;
        margin-top: 2px;
    }

    /* ── SIDEBAR ─────────────────────────────── */
    .ops-sidebar {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .ops-sidebar-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
    }

    .ops-sidebar-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .ops-sidebar-title {
        font-size: 0.88rem;
        font-weight: 700;
        color: var(--text);
    }

    .ops-sidebar-link {
        font-size: 0.75rem;
        color: var(--navy);
        font-weight: 600;
        text-decoration: none;
    }

    .ops-sidebar-link:hover { text-decoration: underline; }

    /* Scheduled orders list */
    .ops-scheduled-list {
        padding: 0.5rem 0;
    }

    .ops-scheduled-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.7rem 1.25rem;
        border-bottom: 1px solid var(--border);
        text-decoration: none;
        transition: background 0.12s;
    }

    .ops-scheduled-item:last-child { border-bottom: none; }

    .ops-scheduled-item:hover {
        background: var(--navy-soft);
        text-decoration: none;
    }

    .ops-sched-time {
        background: var(--navy-soft);
        color: var(--navy);
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .ops-sched-name {
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--text);
        flex: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .ops-sched-id {
        font-size: 0.72rem;
        color: var(--muted);
        flex-shrink: 0;
    }

    /* Quick nav */
    .ops-quick-nav {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
        padding: 0.875rem;
    }

    .ops-quick-nav-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.3rem;
        padding: 0.85rem 0.5rem;
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        text-decoration: none;
        color: var(--text);
        font-size: 0.72rem;
        font-weight: 600;
        transition: background 0.15s, border-color 0.15s;
        text-align: center;
    }

    .ops-quick-nav-btn i {
        font-size: 20px;
        color: var(--navy);
    }

    .ops-quick-nav-btn:hover {
        background: var(--navy-soft);
        border-color: color-mix(in oklch, var(--navy) 25%, transparent);
        text-decoration: none;
        color: var(--text);
    }

    /* Filter period select */
    .ops-period-select {
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 0.35rem 0.7rem;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--navy);
        font-family: inherit;
        cursor: pointer;
        direction: rtl;
    }

    /* ── NEW ORDER ALERT OVERLAY ─────────────── */
    .ops-new-order-toast {
        position: fixed;
        top: 1.5rem;
        right: 1.5rem;
        z-index: 9999;
        background: var(--surface);
        border: 2px solid var(--amber);
        border-radius: var(--radius);
        padding: 1rem 1.25rem;
        box-shadow: var(--shadow-md), 0 0 0 4px color-mix(in oklch, var(--amber) 15%, transparent);
        min-width: 280px;
        display: none;
        animation: toastIn 0.3s ease-out;
    }

    @keyframes toastIn {
        from { opacity: 0; transform: translateX(20px); }
        to   { opacity: 1; transform: translateX(0); }
    }

    .ops-new-order-toast.show { display: block; }

    .ops-toast-title {
        font-size: 0.9rem;
        font-weight: 800;
        color: var(--text);
        margin-bottom: 0.3rem;
    }

    .ops-toast-body {
        font-size: 0.8rem;
        color: var(--muted);
    }

    .ops-toast-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.75rem;
    }

    .ops-toast-print {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        background: var(--navy);
        color: #fff;
        font-size: 0.78rem;
        font-weight: 700;
        padding: 0.4rem 0.85rem;
        border-radius: 7px;
        border: none;
        cursor: pointer;
        font-family: inherit;
    }

    .ops-toast-dismiss {
        background: none;
        border: 1px solid var(--border);
        border-radius: 7px;
        padding: 0.4rem 0.75rem;
        font-size: 0.78rem;
        color: var(--muted);
        cursor: pointer;
        font-family: inherit;
    }

    /* ── ANIMATIONS ─────────────────────────── */
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-8px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── RESPONSIVE ─────────────────────────── */
    @media (max-width: 1200px) {
        .ops-main-grid { grid-template-columns: 1fr; }
        .ops-sidebar { flex-direction: row; flex-wrap: wrap; }
        .ops-sidebar-card { flex: 1 1 300px; }
    }

    @media (max-width: 900px) {
        .ops-status-strip { grid-template-columns: repeat(2, 1fr); }
        .ops-lanes { grid-template-columns: 1fr; }
        .ops-secondary-stats { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 600px) {
        .ops-container { padding: 0 1rem; }
        .ops-status-strip { grid-template-columns: repeat(2, 1fr); gap: 0.625rem; }
        .ops-secondary-stats { grid-template-columns: repeat(2, 1fr); }
        .ops-tile-count { font-size: 2rem; }
        .ops-lanes { grid-template-columns: 1fr; }
        .ops-filter-tabs { display: none; }
    }

    /* ── TOUCH TARGETS ──────────────────────── */
    @media (pointer: coarse) {
        .ops-advance-btn { padding: 0.6rem 1rem; font-size: 0.82rem; }
        .ops-order-card { padding: 1rem 1.1rem; }
        .ops-status-tile { padding: 1.4rem 1.1rem 1.1rem; }
    }

    /* ── FOCUS STATES (keyboard accessibility) ─ */
    .ops-advance-btn:focus-visible,
    .ops-refresh-btn:focus-visible,
    .ops-filter-tab:focus-visible,
    .ops-order-card:focus-visible,
    .ops-status-tile:focus-visible,
    .ops-sec-stat:focus-visible,
    .ops-quick-nav-btn:focus-visible,
    .ops-scheduled-item:focus-visible {
        outline: 2px solid var(--navy);
        outline-offset: 2px;
    }

    /* ── SIDEBAR NAV POLISH (scoped to dashboard) ─ */
    .navbar-vertical-content .nav-link {
        border-radius: 8px !important;
        margin: 1px 8px !important;
        transition: background 0.15s !important;
    }

    .navbar-vertical-content .nav-link:hover {
        background: rgba(255,255,255,0.08) !important;
    }

    .navbar-vertical-content .navbar-vertical-aside-has-menu.active > .nav-link {
        background: rgba(255,255,255,0.12) !important;
    }

    .nav-subtitle {
        font-size: 0.68rem !important;
        letter-spacing: 0.6px !important;
        font-weight: 700 !important;
        opacity: 0.55 !important;
        padding-right: 1rem !important;
        padding-left: 1rem !important;
        margin-top: 0.75rem !important;
    }

    /* ── STATUS TILE TRANSITION UPGRADE ─────── */
    .ops-status-tile {
        transition: transform 0.2s cubic-bezier(0.22, 1, 0.36, 1),
                    box-shadow 0.2s cubic-bezier(0.22, 1, 0.36, 1),
                    border-color 0.2s ease;
    }

    .ops-order-card {
        transition: box-shadow 0.2s cubic-bezier(0.22, 1, 0.36, 1),
                    border-color 0.2s ease;
    }

    /* ── SCROLLABLE LANES on small screens ───── */
    @media (max-width: 900px) {
        .ops-lanes {
            display: flex;
            overflow-x: auto;
            gap: 0.875rem;
            padding-bottom: 0.5rem;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
        }

        .ops-lane {
            min-width: 280px;
            flex-shrink: 0;
            scroll-snap-align: start;
        }
    }

    /* ── RTL TYPOGRAPHY REFINEMENTS ─────────── */
    .ops-panel * {
        font-feature-settings: "kern" 1, "liga" 1;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    /* Numbers stay LTR (digits render better) */
    .ops-tile-count,
    .ops-card-amount,
    .ops-sec-stat-val,
    .ops-card-id {
        direction: ltr;
        display: inline-block;
        unicode-bidi: embed;
    }

    /* ── PRINT: hide ops panel chrome ────────── */
    @media print {
        .ops-topbar,
        .ops-status-strip,
        .ops-lanes-header,
        .ops-filter-tabs,
        .ops-sidebar,
        .ops-secondary-stats,
        .ops-new-order-toast {
            display: none !important;
        }
    }
</style>
@endpush

@section('content')
<div class="ops-panel">
    <div class="ops-container">

        @if(auth('vendor')->check())

        {{-- New Order Toast --}}
        <div class="ops-new-order-toast" id="newOrderToast">
            <div class="ops-toast-title">🛎 طلب جديد وصل!</div>
            <div class="ops-toast-body" id="toastBody">جاري التحديث...</div>
            <div class="ops-toast-actions">
                <button class="ops-toast-print" onclick="printLatestOrder()">
                    <i class="tio-print"></i> طباعة
                </button>
                <button class="ops-toast-dismiss" onclick="dismissToast()">تجاهل</button>
            </div>
        </div>

        {{-- Topbar --}}
        <div class="ops-topbar">
            <div class="ops-brand">
                <div class="ops-brand-logo">
                    <i class="tio-restaurant"></i>
                </div>
                <div>
                    <div class="ops-brand-name">{{ \App\CentralLogics\Helpers::get_restaurant_data()->name ?? 'Beit Jedi' }}</div>
                    <div class="ops-brand-sub">مركز إدارة الطلبات</div>
                </div>
            </div>
            <div class="ops-topbar-actions">
                <span class="ops-live-badge">
                    <span class="ops-live-dot"></span>
                    مباشر
                </span>
                <button class="ops-refresh-btn" onclick="refreshOrders()">
                    <i class="tio-refresh"></i>
                    تحديث
                </button>
                <a href="{{ route('vendor.order.list', ['all']) }}" class="ops-refresh-btn">
                    <i class="tio-receipt"></i>
                    كل الطلبات
                </a>
            </div>
        </div>

        {{-- Stock Warning --}}
        @if(Session::get('stock_out_reminder_close_btn') !== true && isset($out_out_count) && $out_out_count > 0)
        <div class="ops-alert-bar" id="stockAlert">
            <i class="tio-warning-outlined alert-icon"></i>
            <span class="alert-text">
                @if($out_out_count == 1 && isset($food))
                    {{ $food?->name }} — نفدت الكمية من المخزن
                @else
                    {{ $out_out_count }} منتجات نفدت من المخزن
                @endif
                &nbsp;
                <a href="{{ route('vendor.food.stockOutList') }}" style="color:inherit;font-weight:800;text-decoration:underline;">عرض القائمة</a>
            </span>
            <button class="ops-alert-close add-to-session" data-id="stock_out_reminder_close_btn" onclick="this.closest('.ops-alert-bar').remove()">
                <i class="tio-clear"></i>
            </button>
        </div>
        @endif

        {{-- Status Summary Strip --}}
        <div class="ops-status-strip" id="order_stats">
            @include('vendor-views.partials._dashboard-order-stats', ['data' => $data])
        </div>

        {{-- Main Grid --}}
        <div class="ops-main-grid">

            {{-- Left: Kanban + Secondary --}}
            <div>
                <div class="ops-lanes-header">
                    <div class="ops-lanes-title">الطلبات النشطة</div>
                    <div class="ops-filter-tabs">
                        <button class="ops-filter-tab active" onclick="filterOrders('today', this)">اليوم</button>
                        <button class="ops-filter-tab" onclick="filterOrders('this_month', this)">الشهر</button>
                        <button class="ops-filter-tab" onclick="filterOrders('overall', this)">الكل</button>
                    </div>
                </div>

                {{-- Three-Lane Kanban: Confirmed / Cooking / Ready --}}
                <div class="ops-lanes" id="kanbanlanes">
                    @php
                        $confirmed_orders = \App\Models\Order::where('restaurant_id', auth('vendor')->user()->restaurants[0]->id ?? null)
                            ->whereIn('order_status', ['confirmed', 'accepted'])
                            ->latest()->take(10)->get();
                        $cooking_orders = \App\Models\Order::where('restaurant_id', auth('vendor')->user()->restaurants[0]->id ?? null)
                            ->where('order_status', 'cooking')
                            ->latest()->take(10)->get();
                        $ready_orders = \App\Models\Order::where('restaurant_id', auth('vendor')->user()->restaurants[0]->id ?? null)
                            ->where('order_status', 'ready_for_delivery')
                            ->latest()->take(10)->get();
                    @endphp

                    {{-- Lane: Confirmed --}}
                    <div class="ops-lane ops-lane--confirmed">
                        <div class="ops-lane-header">
                            <span class="ops-lane-dot"></span>
                            <span class="ops-lane-name">بانتظار التحضير</span>
                            <span class="ops-lane-count">{{ $confirmed_orders->count() }}</span>
                        </div>
                        <div class="ops-lane-body">
                            @forelse($confirmed_orders as $order)
                            @php
                                $isNew = $order->created_at->diffInMinutes(now()) < 10;
                                $items = $order->details->pluck('food.name')->filter()->implode('، ');
                            @endphp
                            <a href="{{ route('vendor.order.details', ['id' => $order->id]) }}" class="ops-order-card {{ $isNew ? 'ops-order-card--new' : '' }}">
                                <div class="ops-card-top">
                                    <span class="ops-card-id">#{{ $order->id }}</span>
                                    @if($isNew)
                                        <span class="ops-card-new-badge">جديد</span>
                                    @else
                                        <span class="ops-card-time">{{ $order->created_at->diffForHumans() }}</span>
                                    @endif
                                </div>
                                <div class="ops-card-customer">{{ $order->customer->f_name ?? '' }} {{ $order->customer->l_name ?? 'عميل' }}</div>
                                @if($items)
                                <div class="ops-card-items">{{ Str::limit($items, 45) }}</div>
                                @endif
                                <div class="ops-card-footer">
                                    <span class="ops-card-amount">{{ \App\CentralLogics\Helpers::format_currency($order->order_amount) }}</span>
                                    <a href="{{ route('vendor.order.details', ['id' => $order->id]) }}" class="ops-advance-btn ops-advance-btn--confirm" onclick="event.stopPropagation()">
                                        <i class="tio-restaurant"></i> ابدأ التحضير
                                    </a>
                                </div>
                            </a>
                            @empty
                            <div class="ops-empty-lane">
                                <i class="tio-checkmark-circle-outlined"></i>
                                لا طلبات بانتظار التحضير
                            </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Lane: Cooking --}}
                    <div class="ops-lane ops-lane--cooking">
                        <div class="ops-lane-header">
                            <span class="ops-lane-dot"></span>
                            <span class="ops-lane-name">جاري التحضير</span>
                            <span class="ops-lane-count">{{ $cooking_orders->count() }}</span>
                        </div>
                        <div class="ops-lane-body">
                            @forelse($cooking_orders as $order)
                            @php
                                $items = $order->details->pluck('food.name')->filter()->implode('، ');
                            @endphp
                            <a href="{{ route('vendor.order.details', ['id' => $order->id]) }}" class="ops-order-card">
                                <div class="ops-card-top">
                                    <span class="ops-card-id">#{{ $order->id }}</span>
                                    <span class="ops-card-time">{{ $order->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="ops-card-customer">{{ $order->customer->f_name ?? '' }} {{ $order->customer->l_name ?? 'عميل' }}</div>
                                @if($items)
                                <div class="ops-card-items">{{ Str::limit($items, 45) }}</div>
                                @endif
                                <div class="ops-card-footer">
                                    <span class="ops-card-amount">{{ \App\CentralLogics\Helpers::format_currency($order->order_amount) }}</span>
                                    <a href="{{ route('vendor.order.details', ['id' => $order->id]) }}" class="ops-advance-btn ops-advance-btn--cook" onclick="event.stopPropagation()">
                                        <i class="tio-done"></i> جاهز
                                    </a>
                                </div>
                            </a>
                            @empty
                            <div class="ops-empty-lane">
                                <i class="tio-restaurant"></i>
                                لا طلبات قيد التحضير
                            </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Lane: Ready for Delivery --}}
                    <div class="ops-lane ops-lane--ready">
                        <div class="ops-lane-header">
                            <span class="ops-lane-dot"></span>
                            <span class="ops-lane-name">جاهز للتسليم</span>
                            <span class="ops-lane-count">{{ $ready_orders->count() }}</span>
                        </div>
                        <div class="ops-lane-body">
                            @forelse($ready_orders as $order)
                            @php
                                $items = $order->details->pluck('food.name')->filter()->implode('، ');
                            @endphp
                            <a href="{{ route('vendor.order.details', ['id' => $order->id]) }}" class="ops-order-card">
                                <div class="ops-card-top">
                                    <span class="ops-card-id">#{{ $order->id }}</span>
                                    <span class="ops-card-time">{{ $order->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="ops-card-customer">{{ $order->customer->f_name ?? '' }} {{ $order->customer->l_name ?? 'عميل' }}</div>
                                @if($items)
                                <div class="ops-card-items">{{ Str::limit($items, 45) }}</div>
                                @endif
                                <div class="ops-card-footer">
                                    <span class="ops-card-amount">{{ \App\CentralLogics\Helpers::format_currency($order->order_amount) }}</span>
                                    <a href="{{ route('vendor.order.details', ['id' => $order->id]) }}" class="ops-advance-btn ops-advance-btn--ready" onclick="event.stopPropagation()">
                                        <i class="tio-delivery"></i> تسليم
                                    </a>
                                </div>
                            </a>
                            @empty
                            <div class="ops-empty-lane">
                                <i class="tio-done-all"></i>
                                لا طلبات جاهزة
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Secondary Stats --}}
                <div class="ops-secondary-stats">
                    <a href="{{ route('vendor.order.list', ['delivered']) }}" class="ops-sec-stat">
                        <div class="ops-sec-stat-icon"><i class="tio-checkmark-circle"></i></div>
                        <div>
                            <div class="ops-sec-stat-val">{{ $data['delivered'] }}</div>
                            <div class="ops-sec-stat-lbl">تم التسليم</div>
                        </div>
                    </a>
                    <a href="{{ route('vendor.order.list', ['food_on_the_way']) }}" class="ops-sec-stat">
                        <div class="ops-sec-stat-icon"><i class="tio-delivery"></i></div>
                        <div>
                            <div class="ops-sec-stat-val">{{ $data['food_on_the_way'] }}</div>
                            <div class="ops-sec-stat-lbl">في الطريق</div>
                        </div>
                    </a>
                    <a href="{{ route('vendor.order.list', ['scheduled']) }}" class="ops-sec-stat">
                        <div class="ops-sec-stat-icon"><i class="tio-time"></i></div>
                        <div>
                            <div class="ops-sec-stat-val">{{ $data['scheduled'] }}</div>
                            <div class="ops-sec-stat-lbl">مجدولة</div>
                        </div>
                    </a>
                    <a href="{{ route('vendor.order.list', ['all']) }}" class="ops-sec-stat">
                        <div class="ops-sec-stat-icon"><i class="tio-receipt"></i></div>
                        <div>
                            <div class="ops-sec-stat-val">{{ $data['all'] }}</div>
                            <div class="ops-sec-stat-lbl">كل الطلبات</div>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Right Sidebar --}}
            <div class="ops-sidebar">

                {{-- Quick Navigation --}}
                <div class="ops-sidebar-card">
                    <div class="ops-sidebar-header">
                        <span class="ops-sidebar-title">تنقل سريع</span>
                    </div>
                    <div class="ops-quick-nav">
                        <a href="{{ route('vendor.food.list') }}" class="ops-quick-nav-btn">
                            <i class="tio-restaurant"></i>
                            القائمة
                        </a>
                        <a href="{{ route('vendor.order.list', ['all']) }}" class="ops-quick-nav-btn">
                            <i class="tio-receipt"></i>
                            الطلبات
                        </a>
                        <a href="{{ route('vendor.delivery-man.list') }}" class="ops-quick-nav-btn">
                            <i class="tio-delivery"></i>
                            المناديب
                        </a>
                        <a href="{{ route('vendor.shop.edit') }}" class="ops-quick-nav-btn">
                            <i class="tio-settings-outlined"></i>
                            الإعدادات
                        </a>
                    </div>
                </div>

                {{-- Scheduled Orders --}}
                @php
                    $scheduled_orders = \App\Models\Order::where('restaurant_id', auth('vendor')->user()->restaurants[0]->id ?? null)
                        ->where('scheduled', 1)
                        ->whereIn('order_status', ['confirmed', 'accepted', 'pending'])
                        ->orderBy('schedule_at')
                        ->take(6)
                        ->get();
                @endphp
                @if($scheduled_orders->count() > 0)
                <div class="ops-sidebar-card">
                    <div class="ops-sidebar-header">
                        <span class="ops-sidebar-title">طلبات مجدولة</span>
                        <a href="{{ route('vendor.order.list', ['scheduled']) }}" class="ops-sidebar-link">عرض الكل</a>
                    </div>
                    <div class="ops-scheduled-list">
                        @foreach($scheduled_orders as $sched)
                        <a href="{{ route('vendor.order.details', ['id' => $sched->id]) }}" class="ops-scheduled-item">
                            <span class="ops-sched-time">
                                {{ $sched->schedule_at ? \Carbon\Carbon::parse($sched->schedule_at)->format('h:i A') : '--' }}
                            </span>
                            <span class="ops-sched-name">{{ $sched->customer->f_name ?? 'عميل' }}</span>
                            <span class="ops-sched-id">#{{ $sched->id }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Today's Summary --}}
                <div class="ops-sidebar-card">
                    <div class="ops-sidebar-header">
                        <span class="ops-sidebar-title">ملخص اليوم</span>
                    </div>
                    <div style="padding: 1rem 1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
                        @php
                            $today_orders = \App\Models\Order::where('restaurant_id', auth('vendor')->user()->restaurants[0]->id ?? null)
                                ->whereDate('created_at', today())->get();
                            $today_revenue = $today_orders->where('order_status', 'delivered')->sum('order_amount');
                            $today_count = $today_orders->count();
                            $today_pending = $today_orders->whereIn('order_status', ['confirmed', 'accepted', 'cooking', 'ready_for_delivery'])->count();
                        @endphp
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <span style="font-size:0.8rem; color:var(--muted); font-weight:600;">إجمالي الطلبات</span>
                            <span style="font-size:1.1rem; font-weight:800; color:var(--text);">{{ $today_count }}</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <span style="font-size:0.8rem; color:var(--muted); font-weight:600;">قيد التنفيذ</span>
                            <span style="font-size:1.1rem; font-weight:800; color:var(--amber);">{{ $today_pending }}</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <span style="font-size:0.8rem; color:var(--muted); font-weight:600;">إيرادات اليوم</span>
                            <span style="font-size:1.1rem; font-weight:800; color:var(--green);">{{ \App\CentralLogics\Helpers::format_currency($today_revenue) }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        @else
        <div style="padding: 4rem 0; text-align:center;">
            <h2 style="color:var(--text); font-weight:700;">{{ translate('messages.welcome') }}, {{ auth('vendor_employee')->user()->f_name }}</h2>
            <p style="color:var(--muted);">{{ translate('messages.employee_welcome_message') }}</p>
        </div>
        @endif

    </div>
</div>

{{-- Hidden print frame --}}
<iframe id="printFrame" style="display:none;"></iframe>
@endsection

@push('script')
<script>
(function () {
    'use strict';

    // ── Auto-refresh & new order detection ──────────────────────────
    let lastKnownOrderId = {{ $data['confirmed'] ?? 0 }};
    let refreshInterval = null;
    let audioCtx = null;
    let latestNewOrderId = null;

    function playAlertSound() {
        try {
            if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.type = 'sine';
            osc.frequency.setValueAtTime(880, audioCtx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(440, audioCtx.currentTime + 0.3);
            gain.gain.setValueAtTime(0.4, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.6);
            osc.start(audioCtx.currentTime);
            osc.stop(audioCtx.currentTime + 0.6);
            // second beep
            const osc2 = audioCtx.createOscillator();
            const gain2 = audioCtx.createGain();
            osc2.connect(gain2);
            gain2.connect(audioCtx.destination);
            osc2.type = 'sine';
            osc2.frequency.setValueAtTime(880, audioCtx.currentTime + 0.7);
            gain2.gain.setValueAtTime(0.4, audioCtx.currentTime + 0.7);
            gain2.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 1.1);
            osc2.start(audioCtx.currentTime + 0.7);
            osc2.stop(audioCtx.currentTime + 1.1);
        } catch(e) {}
    }

    function showNewOrderToast(orderId, customerName) {
        const toast = document.getElementById('newOrderToast');
        const body  = document.getElementById('toastBody');
        latestNewOrderId = orderId;
        if (body) body.textContent = 'طلب #' + orderId + ' — ' + (customerName || 'عميل جديد');
        if (toast) {
            toast.classList.add('show');
            playAlertSound();
            autoPrint(orderId);
            setTimeout(() => toast.classList.remove('show'), 12000);
        }
    }

    window.dismissToast = function () {
        const toast = document.getElementById('newOrderToast');
        if (toast) toast.classList.remove('show');
    };

    // Auto-print via hidden iframe
    function autoPrint(orderId) {
        const frame = document.getElementById('printFrame');
        if (!frame || !orderId) return;
        const printUrl = '{{ route("vendor.order.generate-invoice", ["id" => "__ID__"]) }}'.replace('__ID__', orderId);
        frame.onload = function () {
            try {
                frame.contentWindow.focus();
                frame.contentWindow.print();
            } catch(e) {}
        };
        frame.src = printUrl;
    }

    window.printLatestOrder = function () {
        if (latestNewOrderId) autoPrint(latestNewOrderId);
        dismissToast();
    };

    function refreshOrders() {
        const btn = document.querySelector('.ops-refresh-btn');
        const statsType = '{{ $params["statistics_type"] ?? "today" }}';

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        $.post('{{ route("vendor.dashboard.order-stats") }}', { statistics_type: statsType }, function (data) {
            const statsEl = document.getElementById('order_stats');
            if (statsEl && data.view) statsEl.innerHTML = data.view;

            // Detect new confirmed orders
            const newConfirmed = parseInt(data.data?.confirmed ?? 0);
            if (newConfirmed > lastKnownOrderId) {
                const diff = newConfirmed - lastKnownOrderId;
                showNewOrderToast(data.data?.latest_order_id ?? '', data.data?.latest_customer ?? '');
                lastKnownOrderId = newConfirmed;
            }
        });
    }

    window.refreshOrders = refreshOrders;

    // ── Period filter ────────────────────────────────────────────────
    window.filterOrders = function (type, btn) {
        document.querySelectorAll('.ops-filter-tab').forEach(t => t.classList.remove('active'));
        if (btn) btn.classList.add('active');

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        $.post('{{ route("vendor.dashboard.order-stats") }}', { statistics_type: type }, function (data) {
            const statsEl = document.getElementById('order_stats');
            if (statsEl && data.view) statsEl.innerHTML = data.view;
        });
    };

    // ── Session close btn ────────────────────────────────────────────
    $(document).on('click', '.add-to-session', function () {
        var session_data = $(this).data('id');
        $.ajax({
            url: '{{ route("vendor.food.addToSession") }}',
            method: 'POST',
            data: { value: session_data, _token: '{{ csrf_token() }}' }
        });
    });

    // ── Auto-refresh every 45 seconds ────────────────────────────────
    // Unlock AudioContext on first user interaction
    document.addEventListener('click', function unlockAudio() {
        if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        if (audioCtx.state === 'suspended') audioCtx.resume();
        document.removeEventListener('click', unlockAudio);
    }, { once: true });

    refreshInterval = setInterval(refreshOrders, 45000);

    // Clear interval on page unload
    window.addEventListener('beforeunload', () => clearInterval(refreshInterval));

})();
</script>
@endpush
