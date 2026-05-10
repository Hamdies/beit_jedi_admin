<!DOCTYPE html>
<?php $log_email_succ = session()->get('log_email_succ'); ?>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @php
        $app_name    = \App\CentralLogics\Helpers::get_business_settings('business_name', false);
        $icon        = \App\CentralLogics\Helpers::get_business_settings('icon', false);
        $systemlogo  = \App\Models\BusinessSetting::where(['key'=>'logo'])->first();
        $role        = $role ?? null;
        $logoUrl     = \App\CentralLogics\Helpers::get_full_url('business', $systemlogo?->value, $systemlogo?->storage[0]?->value ?? 'public', 'logo');
        $fallbackUrl = dynamicAsset('/public/assets/admin/img/logo.png');
    @endphp
    <title>{{ translate('messages.login') }} | {{ $app_name ?? 'بيت جدي' }}</title>
    <link rel="shortcut icon" href="{{ asset($icon ? 'storage/app/public/business/'.$icon : 'public/favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin') }}/css/fonts.css">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin') }}/css/vendor.min.css">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin') }}/vendor/icon-set/style.css">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin') }}/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin') }}/css/theme.minc619.css?v=1.0">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin') }}/css/style.css">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin') }}/css/toastr.css">

    <style>
        /* ─── TOKENS ─────────────────────────────────────────────────── */
        :root {
            --navy-950: oklch(14% 0.048 258);
            --navy-900: oklch(19% 0.055 258);
            --navy-800: oklch(24% 0.062 258);
            --navy-700: oklch(30% 0.065 258);
            --navy-400: oklch(52% 0.05  258);
            --navy-200: oklch(86% 0.018 258);
            --navy-100: oklch(93% 0.010 258);

            --gold:      oklch(68% 0.12  76);
            --gold-soft: oklch(68% 0.07  76 / 0.18);
            --gold-line: oklch(68% 0.10  76 / 0.35);

            --bg:        oklch(96.5% 0.005 80);
            --surface:   oklch(99%   0.003 80);
            --surface-2: oklch(97%   0.004 80);

            --border:        oklch(88% 0.010 258);
            --border-strong: oklch(78% 0.018 258);

            --text-1: oklch(15% 0.022 258);
            --text-2: oklch(35% 0.020 258);
            --text-3: oklch(55% 0.015 258);
            --text-inv: oklch(96% 0.005 258);

            --red:    oklch(53% 0.19 25);
            --red-bg: oklch(97.5% 0.012 25);

            --r:    10px;
            --r-sm:  7px;
            --r-lg: 14px;

            --font: 'Thmanyah Sans', 'Open Sans', sans-serif;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            direction: rtl;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            font-family: var(--font);
        }

        /* Bootstrap / theme overrides */
        body, .main.auth-bg {
            background: var(--bg) !important;
            padding: 0 !important;
            margin: 0 !important;
            min-height: 100vh !important;
        }

        /* ─── SPLIT LAYOUT ───────────────────────────────────────────── */
        .auth-layout {
            display: grid;
            grid-template-columns: 5fr 7fr;
            min-height: 100vh;
        }

        /* ─── LEFT PANEL (brand) ──────────────────────────────────────── */
        .brand-panel {
            background: var(--navy-900);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 2.5rem 2.75rem;
            position: relative;
            overflow: hidden;
        }

        /* Geometric grid pattern overlay */
        .brand-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(oklch(100% 0 0 / 0.03) 1px, transparent 1px),
                linear-gradient(90deg, oklch(100% 0 0 / 0.03) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
        }

        /* Radial glow from bottom-left */
        .brand-panel::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, oklch(68% 0.12 76 / 0.12) 0%, transparent 65%);
            pointer-events: none;
        }

        .brand-panel-top {
            position: relative;
            z-index: 1;
        }

        /* Logo area */
        .brand-logo-wrap {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .brand-logo-img {
            height: 48px;
            width: auto;
            object-fit: contain;
            filter: brightness(0) invert(1);
            opacity: 0.95;
        }

        .brand-logo-fallback {
            width: 48px;
            height: 48px;
            background: oklch(100% 0 0 / 0.1);
            border: 1px solid oklch(100% 0 0 / 0.15);
            border-radius: var(--r-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gold);
            font-size: 1.4rem;
            font-weight: 800;
            flex-shrink: 0;
        }

        .brand-logo-name {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-inv);
            letter-spacing: -0.01em;
        }

        /* Center content */
        .brand-panel-center {
            position: relative;
            z-index: 1;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem 0;
        }

        .brand-tagline-eyebrow {
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 1rem;
        }

        .brand-tagline {
            font-size: 2.1rem;
            font-weight: 800;
            color: var(--text-inv);
            line-height: 1.18;
            letter-spacing: -0.03em;
            max-width: 320px;
        }

        .brand-tagline em {
            font-style: normal;
            color: var(--gold);
        }

        .brand-desc {
            margin-top: 1.25rem;
            font-size: 0.85rem;
            color: oklch(75% 0.010 258);
            line-height: 1.7;
            max-width: 290px;
        }

        /* Feature pills */
        .brand-pills {
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
            margin-top: 2.5rem;
        }

        .brand-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            background: oklch(100% 0 0 / 0.07);
            border: 1px solid oklch(100% 0 0 / 0.1);
            border-radius: 100px;
            padding: 0.45rem 0.9rem 0.45rem 0.65rem;
            font-size: 0.8rem;
            color: oklch(88% 0.010 258);
            width: fit-content;
        }

        .brand-pill-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--gold);
            flex-shrink: 0;
        }

        /* Bottom strip */
        .brand-panel-bottom {
            position: relative;
            z-index: 1;
            border-top: 1px solid oklch(100% 0 0 / 0.1);
            padding-top: 1.25rem;
            font-size: 0.72rem;
            color: oklch(55% 0.015 258);
        }

        /* Gold rule accent below tagline */
        .brand-rule {
            width: 40px;
            height: 3px;
            background: var(--gold);
            border-radius: 100px;
            margin-top: 1.5rem;
            margin-bottom: 0;
        }

        /* ─── RIGHT PANEL (form) ──────────────────────────────────────── */
        .form-panel {
            background: var(--bg);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2.5rem;
        }

        .form-inner {
            width: 100%;
            max-width: 400px;
            animation: rise 0.4s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes rise {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Mobile wordmark (hidden on desktop) */
        .mobile-wordmark {
            display: none;
            align-items: center;
            gap: 0.8rem;
            margin-bottom: 2rem;
        }

        .mobile-wordmark-badge {
            width: 40px;
            height: 40px;
            background: var(--navy-900);
            border-radius: var(--r-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
        }

        .mobile-wordmark-badge img {
            width: 26px;
            height: 26px;
            object-fit: contain;
            filter: brightness(0) invert(1);
        }

        .mobile-wordmark-name {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-1);
        }

        /* Form heading */
        .form-eyebrow {
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 0.5rem;
        }

        .form-heading {
            font-size: 1.9rem;
            font-weight: 800;
            color: var(--text-1);
            letter-spacing: -0.03em;
            line-height: 1.1;
            margin-bottom: 0.4rem;
        }

        .form-sub {
            font-size: 0.85rem;
            color: var(--text-3);
            line-height: 1.65;
            margin-bottom: 2rem;
        }

        /* ─── FORM CARD ───────────────────────────────────────────────── */
        .form-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r-lg);
            padding: 1.75rem 1.75rem 2rem;
            box-shadow:
                0 0 0 1px oklch(84% 0.014 258 / 0.45) inset,
                0 1px 3px oklch(0% 0 0 / 0.04),
                0 8px 24px oklch(0% 0 0 / 0.07),
                0 24px 56px oklch(0% 0 0 / 0.045);
        }

        /* ─── FIELDS ──────────────────────────────────────────────────── */
        .field { margin-bottom: 1.1rem; }

        .field-label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--text-2);
            margin-bottom: 0.4rem;
            letter-spacing: 0.01em;
        }

        .field-wrap { position: relative; }

        .field-input {
            width: 100%;
            background: var(--surface-2);
            border: 1.5px solid var(--border);
            border-radius: var(--r-sm);
            padding: 0.76rem 1rem;
            font-size: 0.875rem;
            font-family: var(--font);
            color: var(--text-1);
            transition: border-color 0.13s, box-shadow 0.13s, background 0.13s;
            outline: none;
            appearance: none;
        }

        .field-input::placeholder { color: var(--text-3); }

        .field-input:hover:not(:focus) { border-color: var(--border-strong); }

        .field-input:focus {
            background: var(--surface);
            border-color: var(--navy-700);
            box-shadow: 0 0 0 3px oklch(30% 0.065 258 / 0.11);
        }

        .field-input.has-toggle { padding-right: 2.75rem; }

        .field-toggle {
            position: absolute;
            top: 50%;
            right: 0.82rem;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-3);
            font-size: 0.92rem;
            padding: 0;
            display: flex;
            align-items: center;
            transition: color 0.13s;
            line-height: 1;
        }

        .field-toggle:hover { color: var(--text-2); }

        .field-error {
            display: none;
            font-size: 0.72rem;
            color: var(--red);
            margin-top: 0.3rem;
        }

        .field.has-error .field-error { display: block; }
        .field.has-error .field-input {
            border-color: var(--red);
            background: var(--red-bg);
        }

        .field.has-error .field-input:focus {
            box-shadow: 0 0 0 3px oklch(53% 0.19 25 / 0.11);
        }

        /* ─── META ROW ────────────────────────────────────────────────── */
        .meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 0.2rem 0 1.4rem;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.79rem;
            color: var(--text-3);
            cursor: pointer;
            user-select: none;
        }

        .remember-label input[type="checkbox"] {
            width: 14px;
            height: 14px;
            accent-color: var(--navy-900);
            cursor: pointer;
            flex-shrink: 0;
        }

        .forgot-btn {
            font-size: 0.79rem;
            font-weight: 600;
            color: var(--gold);
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            font-family: var(--font);
            transition: opacity 0.13s;
        }

        .forgot-btn:hover { opacity: 0.7; text-decoration: underline; text-underline-offset: 3px; }

        /* ─── SUBMIT ──────────────────────────────────────────────────── */
        .btn-submit {
            width: 100%;
            padding: 0.88rem 1rem;
            background: var(--navy-900);
            color: var(--text-inv);
            border: none;
            border-radius: var(--r-sm);
            font-size: 0.92rem;
            font-weight: 700;
            font-family: var(--font);
            letter-spacing: 0.01em;
            cursor: pointer;
            transition: background 0.15s, transform 0.1s, box-shadow 0.15s;
            box-shadow:
                0 1px 0 oklch(100% 0 0 / 0.07) inset,
                0 2px 4px oklch(0% 0 0 / 0.12),
                0 6px 18px oklch(19% 0.055 258 / 0.28);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-submit:hover:not(:disabled) {
            background: var(--navy-800);
            transform: translateY(-1px);
            box-shadow:
                0 1px 0 oklch(100% 0 0 / 0.07) inset,
                0 4px 8px oklch(0% 0 0 / 0.12),
                0 10px 28px oklch(19% 0.055 258 / 0.32);
        }

        .btn-submit:active:not(:disabled) { transform: translateY(0); }

        .btn-submit:focus-visible {
            outline: 2px solid var(--gold);
            outline-offset: 3px;
        }

        .btn-submit:disabled { opacity: 0.58; cursor: not-allowed; transform: none; }

        .btn-submit .spinner {
            display: none;
            width: 15px;
            height: 15px;
            border: 2px solid oklch(96% 0.005 258 / 0.3);
            border-top-color: oklch(96% 0.005 258);
            border-radius: 50%;
            animation: spin 0.65s linear infinite;
            flex-shrink: 0;
        }

        .btn-submit.loading .spinner { display: block; }
        .btn-submit.loading .btn-text { opacity: 0.7; }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* ─── DEMO BOX ────────────────────────────────────────────────── */
        .demo-box {
            margin-top: 0.85rem;
            background: var(--gold-soft);
            border: 1px solid var(--gold-line);
            border-radius: var(--r-sm);
            padding: 0.65rem 0.85rem;
            font-size: 0.76rem;
            color: var(--text-2);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.65rem;
        }

        .demo-box strong { color: var(--text-1); font-weight: 600; }

        .demo-copy-btn {
            background: var(--navy-900);
            color: var(--text-inv);
            border: none;
            border-radius: 5px;
            padding: 0.26rem 0.58rem;
            font-size: 0.7rem;
            font-weight: 600;
            cursor: pointer;
            font-family: var(--font);
            flex-shrink: 0;
            transition: background 0.13s;
        }

        .demo-copy-btn:hover { background: var(--navy-800); }

        /* ─── RESPONSIVE ──────────────────────────────────────────────── */
        @media (max-width: 860px) {
            .auth-layout {
                grid-template-columns: 1fr;
                grid-template-rows: auto 1fr;
            }

            .brand-panel {
                padding: 2rem 1.75rem;
                min-height: auto;
            }

            .brand-panel-center { padding: 1.5rem 0; }

            .brand-tagline { font-size: 1.8rem; }

            .brand-pills { display: none; }

            .form-panel { padding: 2.5rem 1.5rem; }

            .mobile-wordmark { display: none; }
        }

        @media (max-width: 480px) {
            .brand-panel {
                padding: 1.5rem 1.25rem;
            }

            .brand-tagline { font-size: 1.5rem; }

            .brand-panel-center { padding: 1rem 0; }

            .brand-desc { display: none; }

            .form-panel { padding: 2rem 1.25rem; }

            .form-card { padding: 1.4rem 1.25rem 1.6rem; }

            .form-heading { font-size: 1.6rem; }

            .mobile-wordmark { display: flex; }
        }

        @media (prefers-reduced-motion: reduce) {
            .form-inner { animation: none; }
            * { transition-duration: 0.01ms !important; animation-duration: 0.01ms !important; }
        }

        /* Hard reset of bootstrap noise */
        .main.auth-bg {
            display: block !important;
            padding: 0 !important;
            background: transparent !important;
            min-height: 100vh !important;
        }
    </style>
</head>

<body>
<main id="content" role="main" class="main auth-bg">
<div class="auth-layout">

    <!-- ── LEFT: BRAND PANEL ─────────────────────────────────────── -->
    <div class="brand-panel">

        <div class="brand-panel-top">
            <div class="brand-logo-wrap">
                <img
                    class="brand-logo-img onerror-image"
                    src="{{ $logoUrl }}"
                    data-onerror-image="{{ $fallbackUrl }}"
                    alt="{{ $app_name ?? 'بيت جدي' }}"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="brand-logo-fallback" style="display:none;">ب</div>
                <span class="brand-logo-name">{{ $app_name ?? 'بيت جدي' }}</span>
            </div>
        </div>

        <div class="brand-panel-center">
            <p class="brand-tagline-eyebrow">نظام الإدارة المتكامل</p>
            <h2 class="brand-tagline">
                إدارة مطعمك<br>بكل <em>سهولة</em><br>واحترافية
            </h2>
            <div class="brand-rule"></div>
            <p class="brand-desc">
                منصة متكاملة لإدارة الطلبات، القوائم، الفروع، والتقارير — كل ما تحتاجه في مكان واحد.
            </p>
            <div class="brand-pills">
                <div class="brand-pill">
                    <span class="brand-pill-dot"></span>
                    إدارة الطلبات والتوصيل
                </div>
                <div class="brand-pill">
                    <span class="brand-pill-dot"></span>
                    تقارير وإحصاءات فورية
                </div>
                <div class="brand-pill">
                    <span class="brand-pill-dot"></span>
                    إدارة متعددة الفروع
                </div>
            </div>
        </div>

        <div class="brand-panel-bottom">
            بيت جدي &middot; {{ date('Y') }} &middot; جميع الحقوق محفوظة
        </div>

    </div>

    <!-- ── RIGHT: FORM PANEL ──────────────────────────────────────── -->
    <div class="form-panel">
        <div class="form-inner">

            <!-- Mobile only wordmark -->
            <div class="mobile-wordmark">
                <div class="mobile-wordmark-badge">
                    <img src="{{ $logoUrl }}" alt="" onerror="this.src='{{ $fallbackUrl }}'">
                </div>
                <span class="mobile-wordmark-name">{{ $app_name ?? 'بيت جدي' }}</span>
            </div>

            <p class="form-eyebrow">تسجيل الدخول</p>
            <h1 class="form-heading">مرحباً بعودتك</h1>
            <p class="form-sub">أدخل بياناتك للوصول إلى لوحة التحكم</p>

            <div class="form-card">
                <form class="login_form" action="{{ route('login_post') }}" method="post" id="form-id" novalidate>
                    @csrf
                    <input type="hidden" name="role" value="{{ $role }}">

                    <!-- Email -->
                    <div class="field" id="field-email">
                        <label class="field-label" for="signinSrEmail">البريد الإلكتروني</label>
                        <div class="field-wrap">
                            <input
                                type="email"
                                class="field-input"
                                id="signinSrEmail"
                                name="email"
                                value="{{ $email ?? '' }}"
                                placeholder="example@domain.com"
                                tabindex="1"
                                required
                                autocomplete="email"
                                autofocus>
                        </div>
                        <span class="field-error" role="alert">يرجى إدخال بريد إلكتروني صحيح</span>
                    </div>

                    <!-- Password -->
                    <div class="field" id="field-password">
                        <label class="field-label" for="signupSrPassword">كلمة المرور</label>
                        <div class="field-wrap">
                            <input
                                type="password"
                                class="js-toggle-password field-input has-toggle"
                                id="signupSrPassword"
                                name="password"
                                value="{{ $password ?? '' }}"
                                placeholder="••••••••"
                                tabindex="2"
                                required
                                autocomplete="current-password"
                                data-msg="{{ translate('messages.invalid_password_warning') }}"
                                data-hs-toggle-password-options='{
                                    "target": "#changePassTarget",
                                    "defaultClass": "tio-hidden-outlined",
                                    "showClass": "tio-visible-outlined",
                                    "classChangeTarget": "#changePassIcon"
                                }'>
                            <button type="button" id="changePassTarget" class="field-toggle" aria-label="إظهار/إخفاء كلمة المرور" tabindex="-1">
                                <i id="changePassIcon" class="tio-visible-outlined"></i>
                            </button>
                        </div>
                        <span class="field-error" role="alert">يرجى إدخال كلمة المرور</span>
                    </div>

                    <!-- Meta row -->
                    <div class="meta-row">
                        <label class="remember-label">
                            <input type="checkbox" name="remember" tabindex="3">
                            تذكرني
                        </label>

                        <div class="{{ $role == 'admin'  ? '' : 'd-none' }}" id="forget-password">
                            <button type="button" class="forgot-btn" data-toggle="modal" data-target="#forgetPassModal" tabindex="4">
                                نسيت كلمة المرور؟
                            </button>
                        </div>
                        <div class="{{ $role == 'vendor' ? '' : 'd-none' }}" id="forget-password1">
                            <button type="button" class="forgot-btn" data-toggle="modal" data-target="#forgetPassModal1" tabindex="4">
                                نسيت كلمة المرور؟
                            </button>
                        </div>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn-submit" id="signInBtn" tabindex="5">
                        <span class="spinner" aria-hidden="true"></span>
                        <span class="btn-text">تسجيل الدخول</span>
                    </button>
                </form>

                @if(env('APP_MODE') == 'demo')
                    @if(isset($role) && $role == 'admin')
                        <div class="demo-box">
                            <div>
                                <span class="d-block"><strong>Email</strong>: admin@admin.com</span>
                                <span class="d-block"><strong>Password</strong>: 12345678</span>
                            </div>
                            <button class="demo-copy-btn" id="copy_cred">نسخ</button>
                        </div>
                    @endif
                    @if(isset($role) && $role == 'vendor')
                        <div class="demo-box">
                            <div>
                                <span class="d-block"><strong>Email</strong>: test.restaurant@gmail.com</span>
                                <span class="d-block"><strong>Password</strong>: 12345678</span>
                            </div>
                            <button class="demo-copy-btn" id="copy_cred2">نسخ</button>
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>

</div>
</main>

<!-- ── MODALS ──────────────────────────────────────────────────────── -->
<div class="modal fade" id="forgetPassModal">
    <div class="modal-dialog">
        <div class="modal-content" style="border:1px solid var(--border); border-radius:var(--r); font-family:var(--font); box-shadow:0 20px 60px oklch(0% 0 0 / 0.14);">
            <div class="modal-header justify-content-end" style="border-bottom:1px solid var(--border); padding:0.85rem 1.2rem;">
                <span style="cursor:pointer;color:var(--text-3);font-size:0.95rem;transition:color 0.13s;line-height:1;" data-dismiss="modal" role="button" tabindex="0" aria-label="إغلاق">
                    <i class="tio-clear"></i>
                </span>
            </div>
            <div class="modal-body p-0">
                <div style="padding:1.75rem 1.5rem 2rem; text-align:center;">
                    <img src="{{ dynamicAsset('/public/assets/admin/img/send-mail.svg') }}" alt="" style="height:52px;margin-bottom:1rem;opacity:0.8;">
                    <h4 style="font-size:0.92rem;font-weight:700;color:var(--text-1);margin-bottom:0.4rem;">{{ translate('Send_Mail_to_Your_Email_?') }}</h4>
                    <p style="font-size:0.78rem;color:var(--text-3);line-height:1.7;">{{ translate('A_mail_will_be_send_to_your_registered_email_with_a_link_to_change_passowrd') }}</p>
                    <a style="display:block;width:100%;background:var(--navy-900);color:var(--text-inv);border:none;border-radius:var(--r-sm);font-family:var(--font);font-weight:700;font-size:0.85rem;padding:0.72rem 1rem;margin-top:0.9rem;cursor:pointer;transition:background 0.13s;text-align:center;text-decoration:none;box-shadow:0 2px 6px oklch(19% 0.055 258 / 0.25);"
                       href="{{ route('reset-password') }}">
                        {{ translate('Send_Mail') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="forgetPassModal1">
    <div class="modal-dialog">
        <div class="modal-content" style="border:1px solid var(--border); border-radius:var(--r); font-family:var(--font); box-shadow:0 20px 60px oklch(0% 0 0 / 0.14);">
            <div class="modal-header justify-content-end" style="border-bottom:1px solid var(--border); padding:0.85rem 1.2rem;">
                <span style="cursor:pointer;color:var(--text-3);font-size:0.95rem;" data-dismiss="modal" role="button" tabindex="0" aria-label="إغلاق">
                    <i class="tio-clear"></i>
                </span>
            </div>
            <div class="modal-body p-0">
                <div style="padding:1.75rem 1.5rem 2rem; text-align:center;">
                    <img src="{{ dynamicAsset('/public/assets/admin/img/send-mail.svg') }}" alt="" style="height:52px;margin-bottom:1rem;opacity:0.8;">
                    <h4 style="font-size:0.92rem;font-weight:700;color:var(--text-1);margin-bottom:0.4rem;">{{ translate('messages.Send_Mail_to_Your_Email_?') }}</h4>
                    <form action="{{ route('vendor-reset-password') }}" method="post">
                        @csrf
                        <input type="email" name="email"
                            style="width:100%;background:var(--surface-2);border:1.5px solid var(--border);border-radius:var(--r-sm);font-family:var(--font);font-size:0.85rem;padding:0.7rem 1rem;color:var(--text-1);margin-top:1rem;outline:none;direction:ltr;text-align:right;"
                            required placeholder="البريد الإلكتروني">
                        <button type="submit"
                            style="display:block;width:100%;background:var(--navy-900);color:var(--text-inv);border:none;border-radius:var(--r-sm);font-family:var(--font);font-weight:700;font-size:0.85rem;padding:0.72rem 1rem;margin-top:0.85rem;cursor:pointer;transition:background 0.13s;text-align:center;box-shadow:0 2px 6px oklch(19% 0.055 258 / 0.25);">
                            {{ translate('messages.Send_Mail') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="successMailModal">
    <div class="modal-dialog">
        <div class="modal-content" style="border:1px solid var(--border); border-radius:var(--r); font-family:var(--font); box-shadow:0 20px 60px oklch(0% 0 0 / 0.14);">
            <div class="modal-header justify-content-end" style="border-bottom:1px solid var(--border); padding:0.85rem 1.2rem;">
                <span style="cursor:pointer;color:var(--text-3);font-size:0.95rem;" data-dismiss="modal" role="button" tabindex="0" aria-label="إغلاق">
                    <i class="tio-clear"></i>
                </span>
            </div>
            <div class="modal-body p-0">
                <div style="padding:1.75rem 1.5rem 2rem; text-align:center;">
                    <img src="{{ dynamicAsset('/public/assets/admin/img/sent-mail.svg') }}" alt="" style="height:52px;margin-bottom:1rem;opacity:0.8;">
                    <h4 style="font-size:0.92rem;font-weight:700;color:var(--text-1);margin-bottom:0.4rem;">{{ translate('A_mail_has_been_sent_to_your_registered_email') }}!</h4>
                    <p style="font-size:0.78rem;color:var(--text-3);line-height:1.7;">{{ translate('Click_the_link_in_the_mail_description_to_change_password') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── JS ─────────────────────────────────────────────────────────── -->
<script src="{{ dynamicAsset('public/assets/admin') }}/js/vendor.min.js"></script>
<script src="{{ dynamicAsset('public/assets/admin') }}/js/theme.min.js"></script>
<script src="{{ dynamicAsset('public/assets/admin') }}/js/toastr.js"></script>
{!! Toastr::message() !!}

@if ($errors->any())
    <script>
        @foreach($errors->all() as $error)
        toastr.error('{{ translate($error) }}', 'خطأ', { CloseButton: true, ProgressBar: true, timeOut: 6000 });
        @endforeach
        document.getElementById('field-password').classList.add('has-error');
    </script>
@endif

@if ($log_email_succ)
    @php(session()->forget('log_email_succ'))
    <script>$('#successMailModal').modal('show');</script>
@endif

<script>
    $(document).on('ready', function () {
        $('.js-toggle-password').each(function () {
            new HSTogglePassword(this).init();
        });
        $('.js-validate').each(function () {
            $.HSCore.components.HSValidation.init($(this));
        });
    });

    $("#role-select").change(function () {
        var v = $(this).val();
        $("#forget-password").toggleClass('d-none',  v !== 'admin');
        $("#forget-password1").toggleClass('d-none', v !== 'vendor');
    });

    document.getElementById('form-id').addEventListener('submit', function (e) {
        var email    = document.getElementById('signinSrEmail');
        var password = document.getElementById('signupSrPassword');
        var eField   = document.getElementById('field-email');
        var pField   = document.getElementById('field-password');
        var valid    = true;

        eField.classList.remove('has-error');
        pField.classList.remove('has-error');

        if (!email.value.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
            eField.classList.add('has-error');
            valid = false;
        }
        if (!password.value.trim()) {
            pField.classList.add('has-error');
            valid = false;
        }

        if (!valid) { e.preventDefault(); return; }

        var btn = document.getElementById('signInBtn');
        btn.classList.add('loading');
        btn.disabled = true;
    });

    ['signinSrEmail', 'signupSrPassword'].forEach(function (id) {
        document.getElementById(id).addEventListener('input', function () {
            this.closest('.field').classList.remove('has-error');
        });
    });
</script>

@if(env('APP_MODE') == 'demo')
    <script>
        $("#copy_cred").click(function () {
            $('#signinSrEmail').val('admin@admin.com');
            $('#signupSrPassword').val('12345678');
            toastr.success('تم نسخ بيانات الدخول', 'نجاح', { CloseButton: true, ProgressBar: true });
        });
        $("#copy_cred2").click(function () {
            $('#signinSrEmail').val('test.restaurant@gmail.com');
            $('#signupSrPassword').val('12345678');
            toastr.success('تم نسخ بيانات الدخول', 'نجاح', { CloseButton: true, ProgressBar: true });
        });
    </script>
@endif

<script>
    if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent))
        document.write('<script src="{{ dynamicAsset('public//assets/admin') }}/vendor/babel-polyfill/polyfill.min.js"><\/script>');
</script>
</body>
</html>
