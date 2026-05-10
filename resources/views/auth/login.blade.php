<!DOCTYPE html>
<?php $log_email_succ = session()->get('log_email_succ'); ?>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @php
        $app_name = \App\CentralLogics\Helpers::get_business_settings('business_name', false);
        $icon     = \App\CentralLogics\Helpers::get_business_settings('icon', false);
    @endphp
    <title>{{ translate('messages.login') }} | {{ $app_name ?? translate('STACKFOOD') }}</title>
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
        :root {
            /* Navy family */
            --navy-900: oklch(19% 0.055 258);
            --navy-800: oklch(24% 0.062 258);
            --navy-700: oklch(30% 0.065 258);
            --navy-200: oklch(88% 0.018 258);
            --navy-100: oklch(93% 0.010 258);

            /* Gold — single accent, used sparingly */
            --gold:     oklch(66% 0.115 75);
            --gold-dim: oklch(66% 0.06  75 / 0.14);

            /* Surface scale — warm paper, not clinical white */
            --bg:       oklch(96.8% 0.006 80);
            --surface:  oklch(99%   0.004 80);
            --surface-2:oklch(97%   0.005 80);

            /* Borders */
            --border:       oklch(88% 0.010 258);
            --border-strong:oklch(78% 0.018 258);

            /* Text scale */
            --text-1: oklch(16% 0.025 258);
            --text-2: oklch(36% 0.022 258);
            --text-3: oklch(56% 0.016 258);

            /* Semantic */
            --red:    oklch(53% 0.19 25);
            --red-bg: oklch(97.5% 0.012 25);

            /* Geometry */
            --r-sm: 7px;
            --r:    11px;
            --r-lg: 14px;

            --font: 'Thmanyah Sans', 'Open Sans', sans-serif;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html {
            height: 100%;
            direction: rtl;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* ── PAGE SHELL ── */
        body {
            min-height: 100vh;
            font-family: var(--font);
            background: var(--bg);
            display: grid;
            place-items: center;
            padding: 2rem 1.25rem;
        }

        /* Subtle ruled texture — just enough to break flatness */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: repeating-linear-gradient(
                0deg,
                oklch(90% 0.007 258 / 0.45) 0px,
                transparent 1px,
                transparent 28px
            );
            pointer-events: none;
            z-index: 0;
        }

        /* ── CARD SHELL ── */
        .login-shell {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            animation: rise 0.45s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes rise {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── WORDMARK ── */
        .wordmark {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            margin-bottom: 2.5rem;
            padding-right: 0.25rem;
        }

        .wordmark-badge {
            width: 42px;
            height: 42px;
            border-radius: var(--r-sm);
            background: var(--navy-900);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
            box-shadow:
                0 1px 2px oklch(0% 0 0 / 0.18),
                0 3px 8px oklch(19% 0.055 258 / 0.28);
        }

        .wordmark-badge img {
            width: 26px;
            height: 26px;
            object-fit: contain;
            filter: brightness(0) invert(1);
        }

        .wordmark-text {
            line-height: 1;
        }

        .wordmark-name {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-1);
            letter-spacing: -0.015em;
        }

        .wordmark-sub {
            font-size: 0.725rem;
            color: var(--text-3);
            margin-top: 3px;
            letter-spacing: 0.01em;
        }

        /* ── EYEBROW + HEADING ── */
        .eyebrow {
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 0.5rem;
        }

        .heading {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-1);
            letter-spacing: -0.03em;
            line-height: 1.1;
            margin-bottom: 0.4rem;
        }

        .sub {
            font-size: 0.85rem;
            color: var(--text-3);
            line-height: 1.65;
            margin-bottom: 1.75rem;
        }

        /* ── FORM CARD ── */
        .form-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r-lg);
            padding: 2rem 2rem 2.25rem;
            box-shadow:
                0 0 0 1px oklch(85% 0.015 258 / 0.5) inset,
                0 1px 2px oklch(0% 0 0 / 0.04),
                0 6px 18px oklch(0% 0 0 / 0.065),
                0 18px 48px oklch(0% 0 0 / 0.05);
        }

        /* ── DIVIDER LABEL ── */
        .form-section-label {
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: var(--text-3);
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .form-section-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        /* ── FIELDS ── */
        .field { margin-bottom: 1.1rem; }

        .field-label {
            display: flex;
            align-items: baseline;
            gap: 0.3rem;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--text-2);
            margin-bottom: 0.42rem;
            letter-spacing: 0.01em;
        }

        .field-wrap { position: relative; }

        .field-input {
            width: 100%;
            background: var(--surface-2);
            border: 1.5px solid var(--border);
            border-radius: var(--r-sm);
            padding: 0.76rem 1rem;
            font-size: 0.88rem;
            font-family: var(--font);
            color: var(--text-1);
            transition: border-color 0.14s, box-shadow 0.14s, background 0.14s;
            outline: none;
            appearance: none;
        }

        .field-input::placeholder { color: var(--text-3); }

        .field-input:hover:not(:focus) {
            border-color: var(--border-strong);
        }

        .field-input:focus {
            background: var(--surface);
            border-color: var(--navy-700);
            box-shadow: 0 0 0 3px oklch(30% 0.065 258 / 0.12);
        }

        /* password toggle */
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
            font-size: 0.95rem;
            padding: 0;
            display: flex;
            align-items: center;
            transition: color 0.14s;
            line-height: 1;
        }

        .field-toggle:hover { color: var(--text-2); }

        .field-error {
            display: none;
            font-size: 0.73rem;
            color: var(--red);
            margin-top: 0.32rem;
            padding-right: 0.05rem;
        }

        .field.has-error .field-error  { display: block; }
        .field.has-error .field-input  {
            border-color: var(--red);
            background: var(--red-bg);
        }

        .field.has-error .field-input:focus {
            box-shadow: 0 0 0 3px oklch(53% 0.19 25 / 0.12);
        }

        /* ── META ROW ── */
        .meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 0.25rem 0 1.5rem;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.8rem;
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

        /* Gold accent: the single use of it on the page */
        .forgot-btn {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--gold);
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            font-family: var(--font);
            transition: opacity 0.14s;
            text-underline-offset: 3px;
        }

        .forgot-btn:hover { opacity: 0.72; text-decoration: underline; }

        /* ── SUBMIT ── */
        .btn-submit {
            width: 100%;
            padding: 0.88rem 1rem;
            background: var(--navy-900);
            color: oklch(97% 0.005 258);
            border: none;
            border-radius: var(--r-sm);
            font-size: 0.93rem;
            font-weight: 700;
            font-family: var(--font);
            letter-spacing: 0.005em;
            cursor: pointer;
            transition: background 0.16s, transform 0.1s, box-shadow 0.16s;
            box-shadow:
                0 1px 0 oklch(100% 0 0 / 0.06) inset,
                0 2px 4px oklch(0% 0 0 / 0.12),
                0 6px 16px oklch(19% 0.055 258 / 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-submit:hover:not(:disabled) {
            background: var(--navy-800);
            transform: translateY(-1px);
            box-shadow:
                0 1px 0 oklch(100% 0 0 / 0.06) inset,
                0 3px 8px oklch(0% 0 0 / 0.12),
                0 10px 24px oklch(19% 0.055 258 / 0.34);
        }

        .btn-submit:active:not(:disabled) {
            transform: translateY(0);
            box-shadow:
                0 1px 0 oklch(100% 0 0 / 0.06) inset,
                0 1px 3px oklch(0% 0 0 / 0.1),
                0 4px 10px oklch(19% 0.055 258 / 0.22);
        }

        .btn-submit:focus-visible {
            outline: 2px solid var(--gold);
            outline-offset: 3px;
        }

        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-submit .spinner {
            display: none;
            width: 15px;
            height: 15px;
            border: 2px solid oklch(97% 0.005 258 / 0.3);
            border-top-color: oklch(97% 0.005 258);
            border-radius: 50%;
            animation: spin 0.65s linear infinite;
            flex-shrink: 0;
        }

        .btn-submit.loading .spinner { display: block; }
        .btn-submit.loading .btn-text { opacity: 0.72; }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── DEMO BOX ── */
        .demo-box {
            margin-top: 1rem;
            background: var(--gold-dim);
            border: 1px solid oklch(66% 0.115 75 / 0.22);
            border-radius: var(--r-sm);
            padding: 0.68rem 0.9rem;
            font-size: 0.77rem;
            color: var(--text-2);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }

        .demo-box strong { color: var(--text-1); font-weight: 600; }

        .demo-copy-btn {
            background: var(--navy-900);
            color: oklch(97% 0.005 258);
            border: none;
            border-radius: 5px;
            padding: 0.28rem 0.6rem;
            font-size: 0.71rem;
            font-weight: 600;
            cursor: pointer;
            font-family: var(--font);
            flex-shrink: 0;
            transition: background 0.14s;
        }

        .demo-copy-btn:hover { background: var(--navy-800); }

        /* ── FOOTER ── */
        .page-footer {
            margin-top: 1.5rem;
            text-align: center;
        }

        .page-footer-inner {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.7rem;
            color: var(--text-3);
        }

        .page-footer-dot {
            width: 3px;
            height: 3px;
            border-radius: 50%;
            background: var(--border-strong);
            flex-shrink: 0;
        }

        /* ── MODALS ── */
        .modal-content {
            border: 1px solid var(--border);
            border-radius: var(--r);
            box-shadow:
                0 0 0 1px oklch(85% 0.015 258 / 0.5) inset,
                0 20px 60px oklch(0% 0 0 / 0.14);
            font-family: var(--font);
        }

        .modal-header {
            border-bottom: 1px solid var(--border);
            padding: 0.85rem 1.2rem;
        }

        .close-modal-icon {
            cursor: pointer;
            color: var(--text-3);
            font-size: 0.95rem;
            transition: color 0.14s;
            line-height: 1;
        }

        .close-modal-icon:hover { color: var(--text-1); }

        .modal-form-content {
            padding: 1.75rem 1.5rem 2rem;
            text-align: center;
        }

        .modal-form-content img {
            height: 52px;
            margin-bottom: 1rem;
            opacity: 0.8;
        }

        .modal-form-content h4 {
            font-size: 0.92rem;
            font-weight: 700;
            color: var(--text-1);
            margin-bottom: 0.38rem;
        }

        .modal-form-content p {
            font-size: 0.78rem;
            color: var(--text-3);
            line-height: 1.7;
        }

        .modal-form-content .form-control {
            width: 100%;
            background: var(--surface-2);
            border: 1.5px solid var(--border);
            border-radius: var(--r-sm);
            font-family: var(--font);
            font-size: 0.85rem;
            padding: 0.7rem 1rem;
            color: var(--text-1);
            margin-top: 1rem;
            outline: none;
            transition: border-color 0.14s, box-shadow 0.14s;
            direction: ltr;
            text-align: right;
        }

        .modal-form-content .form-control:focus {
            border-color: var(--navy-700);
            box-shadow: 0 0 0 3px oklch(30% 0.065 258 / 0.12);
        }

        .btn-modal-primary {
            display: block;
            width: 100%;
            background: var(--navy-900);
            color: oklch(97% 0.005 258);
            border: none;
            border-radius: var(--r-sm);
            font-family: var(--font);
            font-weight: 700;
            font-size: 0.85rem;
            padding: 0.72rem 1rem;
            margin-top: 0.85rem;
            cursor: pointer;
            transition: background 0.14s;
            text-align: center;
            text-decoration: none;
            box-shadow: 0 2px 6px oklch(19% 0.055 258 / 0.25);
        }

        .btn-modal-primary:hover {
            background: var(--navy-800);
            color: oklch(97% 0.005 258);
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 480px) {
            .form-card { padding: 1.5rem 1.35rem 1.75rem; }
            .heading   { font-size: 1.55rem; }
            .wordmark  { margin-bottom: 2rem; }
        }

        @media (prefers-reduced-motion: reduce) {
            .login-shell { animation: none; }
            * { transition-duration: 0.01ms !important; animation-duration: 0.01ms !important; }
        }

        /* Neutralize bootstrap / theme noise on body layout */
        .main.auth-bg {
            display: grid !important;
            place-items: center !important;
            padding: 0 !important;
            background: transparent !important;
            min-height: 100vh !important;
        }
    </style>
</head>

<body>
<main id="content" role="main" class="main auth-bg">

    <div class="login-shell">

        <!-- Wordmark -->
        @php($systemlogo = \App\Models\BusinessSetting::where(['key'=>'logo'])->first())
        @php($role = $role ?? null)
        <div class="wordmark">
            <div class="wordmark-badge">
                <img class="onerror-image"
                    src="{{ \App\CentralLogics\Helpers::get_full_url('business', $systemlogo?->value, $systemlogo?->storage[0]?->value ?? 'public', 'authfav') }}"
                    data-onerror-image="{{ dynamicAsset('/public/assets/admin/img/auth-fav.png') }}"
                    alt="بيت جدي">
            </div>
            <div class="wordmark-text">
                <div class="wordmark-name">بيت جدي</div>
                <div class="wordmark-sub">لوحة الإدارة</div>
            </div>
        </div>

        <!-- Heading -->
        <p class="eyebrow">تسجيل الدخول</p>
        <h1 class="heading">مرحباً بعودتك</h1>
        <p class="sub">أدخل بياناتك للوصول إلى لوحة التحكم</p>

        <!-- Form card -->
        <div class="form-card">

            <p class="form-section-label">بيانات الحساب</p>

            <form class="login_form" action="{{ route('login_post') }}" method="post" id="form-id" novalidate>
                @csrf
                <input type="hidden" name="role" value="{{ $role ?? null }}">

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

            <!-- Demo credentials -->
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

        <!-- Footer -->
        <p class="page-footer">
            <span class="page-footer-inner">
                <span>بيت جدي</span>
                <span class="page-footer-dot" aria-hidden="true"></span>
                <span>نظام الإدارة</span>
                <span class="page-footer-dot" aria-hidden="true"></span>
                <span>{{ date('Y') }}</span>
            </span>
        </p>

    </div>

</main>

<!-- ── MODALS ── -->
<div class="modal fade" id="forgetPassModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header justify-content-end">
                <span class="close-modal-icon" data-dismiss="modal" role="button" tabindex="0" aria-label="إغلاق">
                    <i class="tio-clear"></i>
                </span>
            </div>
            <div class="modal-body p-0">
                <div class="modal-form-content">
                    <img src="{{ dynamicAsset('/public/assets/admin/img/send-mail.svg') }}" alt="">
                    <h4>{{ translate('Send_Mail_to_Your_Email_?') }}</h4>
                    <p>{{ translate('A_mail_will_be_send_to_your_registered_email_with_a_link_to_change_passowrd') }}</p>
                    <a class="btn-modal-primary" href="{{ route('reset-password') }}">
                        {{ translate('Send_Mail') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="forgetPassModal1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header justify-content-end">
                <span class="close-modal-icon" data-dismiss="modal" role="button" tabindex="0" aria-label="إغلاق">
                    <i class="tio-clear"></i>
                </span>
            </div>
            <div class="modal-body p-0">
                <div class="modal-form-content">
                    <img src="{{ dynamicAsset('/public/assets/admin/img/send-mail.svg') }}" alt="">
                    <h4>{{ translate('messages.Send_Mail_to_Your_Email_?') }}</h4>
                    <form action="{{ route('vendor-reset-password') }}" method="post">
                        @csrf
                        <input type="email" name="email" class="form-control" required placeholder="البريد الإلكتروني">
                        <button type="submit" class="btn-modal-primary">{{ translate('messages.Send_Mail') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="successMailModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header justify-content-end">
                <span class="close-modal-icon" data-dismiss="modal" role="button" tabindex="0" aria-label="إغلاق">
                    <i class="tio-clear"></i>
                </span>
            </div>
            <div class="modal-body p-0">
                <div class="modal-form-content">
                    <img src="{{ dynamicAsset('/public/assets/admin/img/sent-mail.svg') }}" alt="">
                    <h4>{{ translate('A_mail_has_been_sent_to_your_registered_email') }}!</h4>
                    <p>{{ translate('Click_the_link_in_the_mail_description_to_change_password') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── JS ── -->
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
