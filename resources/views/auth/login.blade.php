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
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin') }}/css/vendor.min.css">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin') }}/vendor/icon-set/style.css">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin') }}/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin') }}/css/theme.minc619.css?v=1.0">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin') }}/css/style.css">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin') }}/css/toastr.css">

    <style>
        :root {
            --navy:        #0F2244;
            --navy-mid:    #1A3360;
            --navy-light:  #253F78;
            --gold:        #B8922A;
            --gold-bright: #D4A843;
            --gold-pale:   #F5EDD6;
            --gold-line:   #E8C96A;
            --white:       #FFFFFF;
            --off-white:   #F7F8FC;
            --surface:     #FFFFFF;
            --border:      #E2E6EF;
            --border-soft: #EEF0F6;
            --text-primary:#0D1B2A;
            --text-mid:    #2C3E5A;
            --text-muted:  #6B7A99;
            --error:       #C0392B;
            --font:        'Noto Sans Arabic', 'Cairo', sans-serif;
            --radius-sm:   8px;
            --radius-md:   12px;
            --radius-lg:   20px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html {
            height: 100%;
            direction: rtl;
            font-family: var(--font);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body {
            min-height: 100vh;
            background-color: var(--off-white);
            background-image:
                radial-gradient(ellipse 70% 50% at 20% 0%, rgba(15, 34, 68, 0.06) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 90% 100%, rgba(184, 146, 42, 0.07) 0%, transparent 55%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
        }

        /* ── SPLIT LAYOUT ── */
        .login-wrap {
            width: 100%;
            max-width: 900px;
            display: grid;
            grid-template-columns: 1fr 420px;
            min-height: 560px;
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow:
                0 0 0 1px rgba(15, 34, 68, 0.08),
                0 4px 8px rgba(15, 34, 68, 0.06),
                0 24px 64px rgba(15, 34, 68, 0.12);
            animation: rise 0.55s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes rise {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── LEFT PANEL ── */
        .panel-left {
            background: var(--navy);
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        /* geometric accent shapes */
        .panel-left::before {
            content: '';
            position: absolute;
            top: -80px;
            left: -80px;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            border: 1px solid rgba(232, 201, 106, 0.12);
        }

        .panel-left::after {
            content: '';
            position: absolute;
            bottom: -60px;
            right: -60px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            border: 1px solid rgba(232, 201, 106, 0.08);
        }

        .panel-brand {
            position: relative;
            z-index: 1;
        }

        .panel-logo-wrap {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(232, 201, 106, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .panel-logo-wrap img {
            width: 38px;
            height: 38px;
            object-fit: contain;
        }

        .panel-name {
            font-size: 1.5rem;
            font-weight: 800;
            color: #fff;
            line-height: 1.2;
            letter-spacing: -0.01em;
            margin-bottom: 0.5rem;
        }

        .panel-tagline {
            font-size: 0.82rem;
            font-weight: 400;
            color: rgba(255,255,255,0.5);
            line-height: 1.6;
            max-width: 200px;
        }

        /* gold divider */
        .panel-divider {
            width: 32px;
            height: 2px;
            background: var(--gold-line);
            margin: 1.5rem 0;
            border-radius: 1px;
        }

        .panel-feature-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
            position: relative;
            z-index: 1;
        }

        .panel-feature-list li {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            font-size: 0.82rem;
            color: rgba(255,255,255,0.6);
            font-weight: 400;
        }

        .feature-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--gold-line);
            flex-shrink: 0;
        }

        .panel-footer-note {
            position: relative;
            z-index: 1;
            font-size: 0.7rem;
            color: rgba(255,255,255,0.25);
            letter-spacing: 0.05em;
        }

        /* ── RIGHT PANEL (FORM) ── */
        .panel-right {
            background: var(--surface);
            padding: 2.75rem 2.5rem 2.25rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-right: 1px solid var(--border-soft);
        }

        /* gold top accent line */
        .right-accent {
            height: 3px;
            background: linear-gradient(90deg, var(--gold) 0%, var(--gold-bright) 60%, transparent 100%);
            margin: -2.75rem -2.5rem 2.5rem;
        }

        .form-eyebrow {
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 0.6rem;
        }

        .form-heading {
            font-size: 1.6rem;
            font-weight: 900;
            color: var(--text-primary);
            line-height: 1.15;
            letter-spacing: -0.02em;
            margin-bottom: 0.4rem;
        }

        .form-sub {
            font-size: 0.83rem;
            color: var(--text-muted);
            line-height: 1.65;
            margin-bottom: 2.25rem;
        }

        /* ── FIELDS ── */
        .field {
            margin-bottom: 1.1rem;
        }

        .field-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-mid);
            margin-bottom: 0.5rem;
            letter-spacing: 0.02em;
        }

        .field-wrap {
            position: relative;
        }

        .field-input {
            width: 100%;
            background: var(--off-white);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            font-family: var(--font);
            font-weight: 500;
            color: var(--text-primary);
            transition:
                border-color 0.2s ease,
                background 0.2s ease,
                box-shadow 0.2s ease;
            outline: none;
            appearance: none;
        }

        .field-input::placeholder {
            color: var(--text-muted);
            font-weight: 400;
        }

        .field-input:focus {
            background: #fff;
            border-color: var(--navy-light);
            box-shadow: 0 0 0 3px rgba(37, 63, 120, 0.10);
        }

        .field-input.has-toggle {
            padding-left: 2.75rem;
        }

        .field-toggle {
            position: absolute;
            top: 50%;
            left: 0.85rem;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            font-size: 1rem;
            padding: 0;
            line-height: 1;
            display: flex;
            align-items: center;
            transition: color 0.18s ease;
        }

        .field-toggle:hover { color: var(--navy); }

        /* ── META ROW ── */
        .meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 0.4rem 0 1.75rem;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.79rem;
            color: var(--text-muted);
            cursor: pointer;
            user-select: none;
        }

        .remember-label input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: var(--navy);
            cursor: pointer;
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
            transition: color 0.18s ease;
            text-decoration: none;
        }

        .forgot-btn:hover { color: var(--gold-bright); }

        /* ── SUBMIT ── */
        .btn-submit {
            width: 100%;
            padding: 0.875rem 1rem;
            background: var(--navy);
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            font-size: 0.9rem;
            font-weight: 800;
            font-family: var(--font);
            letter-spacing: 0.04em;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition:
                background 0.22s ease,
                transform 0.15s ease,
                box-shadow 0.22s ease;
            box-shadow: 0 2px 10px rgba(15, 34, 68, 0.22);
        }

        /* gold shimmer on hover */
        .btn-submit::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                110deg,
                transparent 20%,
                rgba(212, 168, 67, 0.15) 50%,
                transparent 80%
            );
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .btn-submit:hover {
            background: var(--navy-mid);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(15, 34, 68, 0.30);
        }

        .btn-submit:hover::after { opacity: 1; }

        .btn-submit:active {
            transform: translateY(0);
            box-shadow: 0 1px 6px rgba(15, 34, 68, 0.18);
        }

        .btn-submit:focus-visible {
            outline: 2px solid var(--gold);
            outline-offset: 3px;
        }

        /* ── BOTTOM VERSION ROW ── */
        .version-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 1.25rem;
            border-top: 1px solid var(--border-soft);
        }

        .version-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background: var(--gold-pale);
            color: var(--gold);
            border-radius: 999px;
            padding: 0.2rem 0.65rem;
            font-size: 0.63rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        .version-badge-dot {
            width: 4px;
            height: 4px;
            background: var(--gold);
            border-radius: 50%;
        }

        .version-copy {
            font-size: 0.68rem;
            color: var(--text-muted);
            letter-spacing: 0.03em;
        }

        /* ── DEMO BOX ── */
        .demo-box {
            margin-top: 1.1rem;
            background: var(--off-white);
            border: 1px dashed var(--border);
            border-radius: var(--radius-sm);
            padding: 0.8rem 1rem;
            font-size: 0.78rem;
            color: var(--text-muted);
        }

        .demo-box strong { color: var(--text-primary); }

        /* ── MODALS ── */
        .modal-content {
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            box-shadow: 0 20px 60px rgba(15, 34, 68, 0.12);
            font-family: var(--font);
        }

        .modal-header {
            border-bottom: 1px solid var(--border);
            padding: 1rem 1.5rem;
        }

        .close-modal-icon {
            cursor: pointer;
            color: var(--text-muted);
            font-size: 1.1rem;
            transition: color 0.18s ease;
        }

        .close-modal-icon:hover { color: var(--text-primary); }

        .forget-pass-content {
            text-align: center;
            padding: 1.5rem 1rem 2rem;
        }

        .forget-pass-content img {
            max-height: 72px;
            margin-bottom: 1.25rem;
        }

        .forget-pass-content h4 {
            font-size: 1rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .forget-pass-content p {
            font-size: 0.84rem;
            color: var(--text-muted);
            line-height: 1.7;
        }

        .forget-pass-content .form-control {
            background: var(--off-white);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: var(--font);
            font-size: 0.88rem;
            padding: 0.75rem 1rem;
            color: var(--text-primary);
            margin-top: 1rem;
            outline: none;
            width: 100%;
            transition: border-color 0.18s ease, box-shadow 0.18s ease;
        }

        .forget-pass-content .form-control:focus {
            border-color: var(--navy-light);
            box-shadow: 0 0 0 3px rgba(37, 63, 120, 0.10);
        }

        .btn--primary {
            display: block;
            width: 100%;
            background: var(--navy);
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            font-family: var(--font);
            font-weight: 800;
            font-size: 0.88rem;
            padding: 0.78rem 1rem;
            margin-top: 1rem;
            cursor: pointer;
            transition: background 0.2s ease;
            text-align: center;
            text-decoration: none;
        }

        .btn--primary:hover {
            background: var(--navy-mid);
            color: #fff;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 700px) {
            .login-wrap {
                grid-template-columns: 1fr;
                max-width: 440px;
            }
            .panel-left { display: none; }
            .panel-right {
                padding: 2.25rem 1.75rem 2rem;
                border-right: none;
            }
            .right-accent {
                margin: -2.25rem -1.75rem 2.25rem;
            }
        }

        @media (max-width: 420px) {
            body { padding: 1rem; }
            .panel-right { padding: 2rem 1.4rem 1.75rem; }
            .right-accent { margin: -2rem -1.4rem 2rem; }
            .form-heading { font-size: 1.35rem; }
        }

        @media (prefers-reduced-motion: reduce) {
            .login-wrap { animation: none; }
            * { transition-duration: 0.01ms !important; }
        }

        /* reset bootstrap noise */
        .main.auth-bg {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0 !important;
            background: transparent !important;
            min-height: 100vh;
        }

        .text-center { text-align: center !important; }
    </style>
</head>

<body>
<main id="content" role="main" class="main auth-bg">

    <div class="login-wrap">

        <!-- ── LEFT BRAND PANEL ── -->
        <div class="panel-left">
            <div class="panel-brand">
                @php($systemlogo = \App\Models\BusinessSetting::where(['key'=>'logo'])->first())
                <div class="panel-logo-wrap">
                    <img class="onerror-image"
                        src="{{ \App\CentralLogics\Helpers::get_full_url('business', $systemlogo?->value, $systemlogo?->storage[0]?->value ?? 'public', 'authfav') }}"
                        data-onerror-image="{{ dynamicAsset('/public/assets/admin/img/auth-fav.png') }}"
                        alt="بيت جدي">
                </div>
                <div class="panel-name">بيت جدي</div>
                <div class="panel-divider"></div>
                <div class="panel-tagline">منصة إدارة المطاعم والمتاجر بكفاءة عالية</div>
            </div>

            <ul class="panel-feature-list">
                <li><span class="feature-dot"></span>إدارة الطلبات والمنيو بسهولة</li>
                <li><span class="feature-dot"></span>تقارير ومبيعات في الوقت الفعلي</li>
                <li><span class="feature-dot"></span>إشعارات فورية وتتبع التوصيل</li>
                <li><span class="feature-dot"></span>دعم فني على مدار الساعة</li>
            </ul>

            <div class="panel-footer-note">بيت جدي · {{ date('Y') }}</div>
        </div>

        <!-- ── RIGHT FORM PANEL ── -->
        <div class="panel-right">
            @php($role = $role ?? null)

            <div class="right-accent"></div>

            <div class="form-eyebrow">لوحة التحكم</div>
            <h1 class="form-heading">تسجيل الدخول</h1>
            <p class="form-sub">أدخل بياناتك للوصول إلى حسابك</p>

            <!-- Form -->
            <form class="login_form" action="{{ route('login_post') }}" method="post" id="form-id">
                @csrf
                <input type="hidden" name="role" value="{{ $role ?? null }}">

                <!-- Email -->
                <div class="field">
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
                            autocomplete="email">
                    </div>
                </div>

                <!-- Password -->
                <div class="field">
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
                            نسيت كلمة المرور؟
                        </button>
                    </div>
                    <div class="{{ $role == 'vendor' ? '' : 'd-none' }}" id="forget-password1">
                        <button type="button" class="forgot-btn" data-toggle="modal" data-target="#forgetPassModal1">
                            نسيت كلمة المرور؟
                        </button>
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-submit" id="signInBtn" tabindex="3">
                    دخول إلى لوحة التحكم
                </button>
            </form>

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

            <!-- Version row -->
            <div class="version-row">
                <span class="version-badge">
                    <span class="version-badge-dot"></span>
                    v{{ env('SOFTWARE_VERSION') }}
                </span>
                <span class="version-copy">نظام الإدارة · بيت جدي</span>
            </div>
        </div>

    </div>

</main>

<!-- Modals -->
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
