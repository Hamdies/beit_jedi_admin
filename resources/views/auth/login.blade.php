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
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@300;400;500;600;700;800&family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin') }}/css/vendor.min.css">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin') }}/vendor/icon-set/style.css">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin') }}/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin') }}/css/theme.minc619.css?v=1.0">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin') }}/css/style.css">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin') }}/css/toastr.css">

    <style>
        /* ─────────────────────────────────────────
           TOKENS
        ───────────────────────────────────────── */
        :root {
            --navy:        #0f2554;
            --navy-dark:   #091a3e;
            --navy-mid:    #1a3572;
            --navy-light:  #2a4d9e;
            --gold:        #c9a84c;
            --gold-light:  #e2c47a;
            --gold-pale:   #f5e6b8;
            --gold-dark:   #9e7d2e;
            --white:       #ffffff;
            --off-white:   #f7f8fc;
            --gray-100:    #f0f2f8;
            --gray-200:    #dde2ef;
            --gray-300:    #bcc4d8;
            --gray-400:    #8d99b5;
            --gray-500:    #5e6b87;
            --gray-700:    #2e3751;
            --gray-900:    #111827;
            --danger:      #dc3545;
            --font:        'Noto Sans Arabic', 'Cairo', sans-serif;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            direction: rtl;
            font-family: var(--font);
        }

        body {
            background: var(--off-white);
            display: flex;
            align-items: stretch;
            min-height: 100vh;
        }

        /* ─────────────────────────────────────────
           TWO-PANEL WRAPPER
        ───────────────────────────────────────── */
        .login-wrap {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* ── LEFT / BRAND PANEL ── */
        .brand-panel {
            flex: 0 0 42%;
            background: var(--navy-dark);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 3rem 3.5rem;
            position: relative;
            overflow: hidden;
        }

        /* single clean diagonal rule at bottom-right */
        .brand-panel::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 380px;
            height: 380px;
            border-radius: 50%;
            border: 60px solid rgba(201, 168, 76, 0.07);
            pointer-events: none;
        }

        .brand-panel::before {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            border: 40px solid rgba(255, 255, 255, 0.04);
            pointer-events: none;
        }

        .brand-top {
            position: relative;
            z-index: 1;
        }

        .brand-logo-wrap {
            display: inline-flex;
            align-items: center;
            gap: 0.9rem;
            margin-bottom: 3.5rem;
        }

        .brand-logo-wrap img {
            height: 52px;
            width: auto;
        }

        .brand-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--white);
            letter-spacing: 0.02em;
            line-height: 1.2;
        }

        .brand-name span {
            display: block;
            font-size: 0.75rem;
            font-weight: 400;
            color: var(--gray-400);
            letter-spacing: 0.06em;
            margin-top: 0.15rem;
        }

        .brand-headline {
            font-size: 2rem;
            font-weight: 800;
            color: var(--white);
            line-height: 1.4;
            margin-bottom: 1rem;
        }

        .brand-headline em {
            font-style: normal;
            color: var(--gold);
        }

        .brand-desc {
            font-size: 0.9rem;
            color: var(--gray-400);
            line-height: 1.8;
            max-width: 300px;
        }

        /* feature pills */
        .brand-features {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-top: 2.5rem;
            position: relative;
            z-index: 1;
        }

        .brand-feature {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .brand-feature-icon {
            width: 32px;
            height: 32px;
            background: rgba(201, 168, 76, 0.12);
            border: 1px solid rgba(201, 168, 76, 0.25);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .brand-feature-icon svg {
            width: 15px;
            height: 15px;
            fill: var(--gold);
        }

        .brand-feature-text {
            font-size: 0.83rem;
            color: var(--gray-300);
            font-weight: 500;
        }

        .brand-bottom {
            position: relative;
            z-index: 1;
        }

        /* gold rule */
        .brand-rule {
            width: 48px;
            height: 3px;
            background: var(--gold);
            border-radius: 2px;
            margin-bottom: 1rem;
        }

        .brand-tagline {
            font-size: 0.75rem;
            color: var(--gray-500);
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        /* ── RIGHT / FORM PANEL ── */
        .form-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
            background: var(--white);
        }

        .form-inner {
            width: 100%;
            max-width: 440px;
        }

        /* version badge */
        .version-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: var(--gold-pale);
            border: 1px solid rgba(201, 168, 76, 0.35);
            color: var(--gold-dark);
            border-radius: 999px;
            padding: 0.28rem 0.85rem;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-bottom: 2rem;
        }

        .version-badge svg {
            width: 12px;
            height: 12px;
            fill: var(--gold-dark);
        }

        .form-heading {
            font-size: 1.65rem;
            font-weight: 800;
            color: var(--navy-dark);
            line-height: 1.25;
            margin-bottom: 0.4rem;
        }

        .form-subheading {
            font-size: 0.875rem;
            color: var(--gray-400);
            margin-bottom: 2.25rem;
            line-height: 1.6;
        }

        /* ── FIELD ── */
        .field-group {
            margin-bottom: 1.25rem;
        }

        .field-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
            letter-spacing: 0.01em;
        }

        .field-wrap {
            position: relative;
        }

        .field-input {
            width: 100%;
            background: var(--off-white);
            border: 1.5px solid var(--gray-200);
            border-radius: 10px;
            padding: 0.8rem 1rem;
            font-size: 0.9rem;
            font-family: var(--font);
            font-weight: 500;
            color: var(--gray-900);
            transition: border-color 0.18s, box-shadow 0.18s, background 0.18s;
            outline: none;
        }

        .field-input::placeholder {
            color: var(--gray-300);
            font-weight: 400;
        }

        .field-input:focus {
            background: var(--white);
            border-color: var(--navy-light);
            box-shadow: 0 0 0 3px rgba(42, 77, 158, 0.10);
        }

        /* password toggle — RTL: toggle sits on left */
        .field-input.has-toggle {
            padding-left: 3rem;
        }

        .field-toggle {
            position: absolute;
            top: 50%;
            left: 0.85rem;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--gray-400);
            font-size: 1.05rem;
            padding: 0;
            line-height: 1;
            transition: color 0.18s;
            display: flex;
            align-items: center;
        }

        .field-toggle:hover { color: var(--navy); }

        /* ── META ROW ── */
        .meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 0.25rem 0 1.75rem;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.82rem;
            color: var(--gray-500);
            cursor: pointer;
            user-select: none;
        }

        .remember-label input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: var(--navy);
            cursor: pointer;
            flex-shrink: 0;
        }

        .forgot-btn {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--navy-light);
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            font-family: var(--font);
            transition: color 0.18s;
        }

        .forgot-btn:hover { color: var(--gold-dark); }

        /* ── SUBMIT ── */
        .btn-submit {
            width: 100%;
            padding: 0.9rem 1rem;
            background: var(--navy-dark);
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 700;
            font-family: var(--font);
            letter-spacing: 0.03em;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 4px 18px rgba(9, 26, 62, 0.28);
            position: relative;
            overflow: hidden;
        }

        /* gold underline accent on button */
        .btn-submit::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 15%;
            left: 15%;
            height: 2px;
            background: var(--gold);
            border-radius: 0;
            opacity: 0.6;
        }

        .btn-submit:hover {
            background: var(--navy-mid);
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(9, 26, 62, 0.34);
        }

        .btn-submit:active {
            transform: translateY(0);
            box-shadow: 0 2px 10px rgba(9, 26, 62, 0.22);
        }

        .btn-submit:focus-visible {
            outline: 3px solid rgba(42, 77, 158, 0.4);
            outline-offset: 2px;
        }

        /* ── DIVIDER ── */
        .form-divider {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            margin: 2rem 0 1.5rem;
        }

        .form-divider::before,
        .form-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--gray-200);
        }

        .form-divider-text {
            font-size: 0.72rem;
            color: var(--gray-400);
            font-weight: 500;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        /* ── FOOTER ── */
        .form-footer {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--gray-100);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
        }

        .form-footer-text {
            font-size: 0.72rem;
            color: var(--gray-400);
            letter-spacing: 0.05em;
        }

        .form-footer-dot {
            width: 3px;
            height: 3px;
            background: var(--gold);
            border-radius: 50%;
        }

        /* ── DEMO BOX ── */
        .demo-box {
            background: var(--off-white);
            border: 1px dashed var(--gray-200);
            border-radius: 10px;
            padding: 0.9rem 1.1rem;
            margin-top: 1rem;
            font-size: 0.82rem;
            color: var(--gray-500);
        }

        .demo-box strong { color: var(--gray-700); }

        /* ── MODALS ── */
        .modal-content {
            border: 1px solid var(--gray-200);
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(9, 26, 62, 0.12);
        }

        .modal-header {
            border-bottom: 1px solid var(--gray-100);
            padding: 1rem 1.5rem;
        }

        .close-modal-icon {
            cursor: pointer;
            color: var(--gray-400);
            font-size: 1.1rem;
            transition: color 0.18s;
        }

        .close-modal-icon:hover { color: var(--gray-700); }

        .forget-pass-content {
            text-align: center;
            padding: 1.25rem 0.5rem 1.75rem;
        }

        .forget-pass-content img {
            max-height: 72px;
            margin-bottom: 1.25rem;
        }

        .forget-pass-content h4 {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--navy-dark);
            margin-bottom: 0.5rem;
        }

        .forget-pass-content p {
            font-size: 0.85rem;
            color: var(--gray-400);
            line-height: 1.65;
        }

        .forget-pass-content .form-control {
            background: var(--off-white);
            border: 1.5px solid var(--gray-200);
            border-radius: 8px;
            font-family: var(--font);
            font-size: 0.88rem;
            padding: 0.7rem 1rem;
            color: var(--gray-900);
            margin-top: 1rem;
            outline: none;
            transition: border-color 0.18s, box-shadow 0.18s;
        }

        .forget-pass-content .form-control:focus {
            border-color: var(--navy-light);
            box-shadow: 0 0 0 3px rgba(42, 77, 158, 0.10);
        }

        .btn--primary {
            display: block;
            width: 100%;
            background: var(--navy-dark);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-family: var(--font);
            font-weight: 700;
            font-size: 0.88rem;
            padding: 0.75rem 1rem;
            margin-top: 1rem;
            cursor: pointer;
            transition: background 0.2s;
            text-align: center;
            text-decoration: none;
        }

        .btn--primary:hover {
            background: var(--navy-mid);
            color: var(--white);
        }

        /* ── ANIMATION ── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .form-inner {
            animation: fadeUp 0.45s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .brand-panel { display: none; }
            .form-panel { padding: 2.5rem 1.5rem; }
        }

        @media (max-width: 480px) {
            .form-inner { max-width: 100%; }
            .form-panel { padding: 2rem 1.25rem; }
            .form-heading { font-size: 1.35rem; }
        }

        /* ── REDUCED MOTION ── */
        @media (prefers-reduced-motion: reduce) {
            .form-inner { animation: none; }
            * { transition-duration: 0.01ms !important; }
        }

        /* keep bootstrap from overriding our text-align in RTL */
        .text-center { text-align: center !important; }

        /* main wrapper reset */
        .main.auth-bg {
            display: flex;
            align-items: stretch;
            padding: 0 !important;
            background: transparent !important;
            flex: 1;
        }
    </style>
</head>

<body>
<main id="content" role="main" class="main auth-bg">
<div class="login-wrap">

    <!-- ── BRAND PANEL ── -->
    <div class="brand-panel">
        <div class="brand-top">
            @php($systemlogo = \App\Models\BusinessSetting::where(['key'=>'logo'])->first())
            @php($role = $role ?? null)

            <div class="brand-logo-wrap">
                <img class="onerror-image"
                    src="{{ \App\CentralLogics\Helpers::get_full_url('business', $systemlogo?->value, $systemlogo?->storage[0]?->value ?? 'public', 'authfav') }}"
                    data-onerror-image="{{ dynamicAsset('/public/assets/admin/img/auth-fav.png') }}"
                    alt="بيت جدي">
                <div class="brand-name">
                    بيت جدي
                    <span>لوحة الإدارة</span>
                </div>
            </div>

            <h2 class="brand-headline">
                إدارة مطعمك<br>
                من <em>مكان واحد</em>
            </h2>
            <p class="brand-desc">
                منصة متكاملة لإدارة الطلبات، المنيو، الفروع، والموظفين بكفاءة عالية.
            </p>

            <div class="brand-features">
                <div class="brand-feature">
                    <div class="brand-feature-icon">
                        <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                    </div>
                    <span class="brand-feature-text">متابعة الطلبات في الوقت الفعلي</span>
                </div>
                <div class="brand-feature">
                    <div class="brand-feature-icon">
                        <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm7 13H5v-.23c0-.62.28-1.2.76-1.58C7.47 15.82 9.64 15 12 15s4.53.82 6.24 2.19c.48.38.76.97.76 1.58V19z"/></svg>
                    </div>
                    <span class="brand-feature-text">إدارة الموظفين والصلاحيات</span>
                </div>
                <div class="brand-feature">
                    <div class="brand-feature-icon">
                        <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
                    </div>
                    <span class="brand-feature-text">تقارير وتحليلات مفصّلة</span>
                </div>
                <div class="brand-feature">
                    <div class="brand-feature-icon">
                        <svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 4l5 2.18V11c0 3.5-2.33 6.79-5 7.93-2.67-1.14-5-4.43-5-7.93V7.18L12 5z"/></svg>
                    </div>
                    <span class="brand-feature-text">حماية وأمان البيانات</span>
                </div>
            </div>
        </div>

        <div class="brand-bottom">
            <div class="brand-rule"></div>
            <p class="brand-tagline">بيت جدي &mdash; {{ date('Y') }} &mdash; جميع الحقوق محفوظة</p>
        </div>
    </div>

    <!-- ── FORM PANEL ── -->
    <div class="form-panel">
        <div class="form-inner">

            <!-- Version badge -->
            <div>
                <span class="version-badge">
                    <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                    إصدار النظام : {{ env('SOFTWARE_VERSION') }}
                </span>
            </div>

            <h1 class="form-heading">تسجيل الدخول</h1>
            <p class="form-subheading">أدخل بياناتك للوصول إلى لوحة التحكم</p>

            <!-- Login Form -->
            <form class="login_form" action="{{ route('login_post') }}" method="post" id="form-id">
                @csrf
                <input type="hidden" name="role" value="{{ $role ?? null }}">

                <!-- Email -->
                <div class="field-group">
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
                            data-msg="Please enter a valid email address.">
                    </div>
                </div>

                <!-- Password -->
                <div class="field-group">
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
                        <button type="button" id="changePassTarget" class="field-toggle" aria-label="إظهار/إخفاء كلمة المرور">
                            <i id="changePassIcon" class="tio-visible-outlined"></i>
                        </button>
                    </div>
                </div>

                <!-- Meta row -->
                <div class="meta-row">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" {{ $password ? 'checked' : '' }}>
                        تذكرني
                    </label>

                    <div class="{{ $role == 'admin'  ? '' : 'd-none' }}" id="forget-password">
                        <button type="button" class="forgot-btn" data-toggle="modal" data-target="#forgetPassModal">
                            هل نسيت كلمة المرور؟
                        </button>
                    </div>
                    <div class="{{ $role == 'vendor' ? '' : 'd-none' }}" id="forget-password1">
                        <button type="button" class="forgot-btn" data-toggle="modal" data-target="#forgetPassModal1">
                            هل نسيت كلمة المرور؟
                        </button>
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-submit" id="signInBtn" tabindex="3">
                    تسجيل الدخول
                </button>
            </form>

            <!-- Footer -->
            <div class="form-footer">
                <span class="form-footer-text">بيت جدي</span>
                <div class="form-footer-dot"></div>
                <span class="form-footer-text">نظام الإدارة</span>
                <div class="form-footer-dot"></div>
                <span class="form-footer-text">{{ date('Y') }}</span>
            </div>

            <!-- Demo credentials -->
            @if(env('APP_MODE') == 'demo')
                @if(isset($role) && $role == 'admin')
                    <div class="demo-box d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <span class="d-block"><strong>Email</strong>: admin@admin.com</span>
                            <span class="d-block"><strong>Password</strong>: 12345678</span>
                        </div>
                        <button class="btn btn-sm btn-primary" id="copy_cred">
                            <i class="tio-copy"></i>
                        </button>
                    </div>
                @endif
                @if(isset($role) && $role == 'vendor')
                    <div class="demo-box d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <span class="d-block"><strong>Email</strong>: test.restaurant@gmail.com</span>
                            <span class="d-block"><strong>Password</strong>: 12345678</span>
                        </div>
                        <button class="btn btn-sm btn-primary" id="copy_cred2">
                            <i class="tio-copy"></i>
                        </button>
                    </div>
                @endif
            @endif

        </div>
    </div>

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
            <div class="modal-body">
                <div class="forget-pass-content">
                    <img src="{{ dynamicAsset('/public/assets/admin/img/send-mail.svg') }}" alt="">
                    <h4>{{ translate('Send_Mail_to_Your_Email_?') }}</h4>
                    <p>{{ translate('A_mail_will_be_send_to_your_registered_email_with_a_link_to_change_passowrd') }}</p>
                    <a class="btn--primary" href="{{ route('reset-password') }}">
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
            <div class="modal-body">
                <div class="forget-pass-content">
                    <img src="{{ dynamicAsset('/public/assets/admin/img/send-mail.svg') }}" alt="">
                    <h4>{{ translate('messages.Send_Mail_to_Your_Email_?') }}</h4>
                    <form action="{{ route('vendor-reset-password') }}" method="post">
                        @csrf
                        <input type="email" name="email" class="form-control" required placeholder="البريد الإلكتروني">
                        <button type="submit" class="btn--primary">{{ translate('messages.Send_Mail') }}</button>
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
            <div class="modal-body">
                <div class="forget-pass-content">
                    <img src="{{ dynamicAsset('/public/assets/admin/img/sent-mail.svg') }}" alt="">
                    <h4>{{ translate('A_mail_has_been_sent_to_your_registered_email') }}!</h4>
                    <p>{{ translate('Click_the_link_in_the_mail_description_to_change_password') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS -->
<script src="{{ dynamicAsset('public/assets/admin') }}/js/vendor.min.js"></script>
<script src="{{ dynamicAsset('public/assets/admin') }}/js/theme.min.js"></script>
<script src="{{ dynamicAsset('public/assets/admin') }}/js/toastr.js"></script>
{!! Toastr::message() !!}

@if ($errors->any())
    <script>
        @foreach($errors->all() as $error)
        toastr.error('{{ translate($error) }}', 'خطأ', { CloseButton: true, ProgressBar: true });
        @endforeach
    </script>
@endif

@if ($log_email_succ)
    @php(session()->forget('log_email_succ'))
    <script>$('#successMailModal').modal('show');</script>
@endif

<script>
    $("#role-select").change(function () {
        var v = $(this).val();
        $("#forget-password").toggleClass('d-none', v !== 'admin');
        $("#forget-password1").toggleClass('d-none', v !== 'vendor');
    });
</script>

<script>
    $(document).on('ready', function () {
        $('.js-toggle-password').each(function () {
            new HSTogglePassword(this).init();
        });
        $('.js-validate').each(function () {
            $.HSCore.components.HSValidation.init($(this));
        });
    });
</script>

@if(env('APP_MODE') == 'demo')
    <script>
        $("#copy_cred").click(function () {
            $('#signinSrEmail').val('admin@admin.com');
            $('#signupSrPassword').val('12345678');
            toastr.success('تم النسخ!', 'نجاح', { CloseButton: true, ProgressBar: true });
        });
        $("#copy_cred2").click(function () {
            $('#signinSrEmail').val('test.restaurant@gmail.com');
            $('#signupSrPassword').val('12345678');
            toastr.success('تم النسخ!', 'نجاح', { CloseButton: true, ProgressBar: true });
        });
    </script>
@endif

<script>
    if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent))
        document.write('<script src="{{ dynamicAsset('public//assets/admin') }}/vendor/babel-polyfill/polyfill.min.js"><\/script>');
</script>
</body>
</html>
