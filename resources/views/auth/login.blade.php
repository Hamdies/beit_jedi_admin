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
            --navy:         oklch(22% 0.06 255);
            --navy-hover:   oklch(27% 0.06 255);
            --gold:         oklch(68% 0.12 80);
            --gold-dim:     oklch(68% 0.08 80 / 0.15);
            --surface:      oklch(99% 0.004 255);
            --bg:           oklch(96.5% 0.006 255);
            --border:       oklch(90% 0.008 255);
            --border-focus: oklch(40% 0.08 255);
            --text-1:       oklch(18% 0.02 255);
            --text-2:       oklch(40% 0.02 255);
            --text-3:       oklch(58% 0.015 255);
            --red:          oklch(55% 0.18 25);
            --red-bg:       oklch(97% 0.015 25);
            --font:         'Thmanyah Sans', 'Open Sans', sans-serif;
            --r:            10px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html {
            height: 100%;
            direction: rtl;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body {
            min-height: 100vh;
            font-family: var(--font);
            background: var(--bg);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.25rem;
        }

        /* ── LAYOUT ── */
        .login-wrap {
            width: 100%;
            max-width: 440px;
            animation: appear 0.4s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes appear {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── BRAND ── */
        .brand {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            margin-bottom: 2.75rem;
        }

        .brand-mark {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: var(--navy);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
        }

        .brand-mark img {
            width: 28px;
            height: 28px;
            object-fit: contain;
            filter: brightness(0) invert(1);
        }

        .brand-name {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-1);
            letter-spacing: -0.01em;
        }

        .brand-sub {
            font-size: 0.75rem;
            color: var(--text-3);
            margin-top: 1px;
        }

        /* ── HEADING ── */
        .heading {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--text-1);
            letter-spacing: -0.025em;
            line-height: 1.15;
            margin-bottom: 0.35rem;
        }

        .sub {
            font-size: 0.875rem;
            color: var(--text-3);
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        /* ── FORM CARD ── */
        .form-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r);
            padding: 2rem;
            box-shadow: 0 1px 3px oklch(0% 0 0 / 0.04), 0 8px 24px oklch(0% 0 0 / 0.06);
        }

        /* ── FIELDS ── */
        .field { margin-bottom: 1.1rem; }

        .field-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-2);
            margin-bottom: 0.45rem;
            letter-spacing: 0.005em;
        }

        .field-wrap { position: relative; }

        .field-input {
            width: 100%;
            background: var(--bg);
            border: 1.5px solid var(--border);
            border-radius: calc(var(--r) - 2px);
            padding: 0.78rem 1rem;
            font-size: 0.9rem;
            font-family: var(--font);
            color: var(--text-1);
            transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
            outline: none;
            appearance: none;
        }

        .field-input::placeholder { color: var(--text-3); }

        .field-input:focus {
            background: var(--surface);
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px oklch(40% 0.08 255 / 0.1);
        }

        .field-input.is-error {
            border-color: var(--red);
            background: var(--red-bg);
        }

        .field-input.is-error:focus {
            box-shadow: 0 0 0 3px oklch(55% 0.18 25 / 0.12);
        }

        /* password field — toggle on right for RTL */
        .field-input.has-toggle { padding-right: 2.9rem; }

        .field-toggle {
            position: absolute;
            top: 50%;
            right: 0.85rem;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-3);
            font-size: 1rem;
            padding: 0;
            display: flex;
            align-items: center;
            transition: color 0.15s;
            line-height: 1;
        }

        .field-toggle:hover { color: var(--text-1); }

        .field-error {
            display: none;
            font-size: 0.75rem;
            color: var(--red);
            margin-top: 0.35rem;
            padding-right: 0.1rem;
        }

        .field.has-error .field-error { display: block; }
        .field.has-error .field-input  { border-color: var(--red); background: var(--red-bg); }

        /* ── META ROW ── */
        .meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 0.5rem 0 1.5rem;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.82rem;
            color: var(--text-3);
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
            color: var(--gold);
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            font-family: var(--font);
            transition: opacity 0.15s;
        }

        .forgot-btn:hover { opacity: 0.75; }

        /* ── SUBMIT ── */
        .btn-submit {
            width: 100%;
            padding: 0.85rem 1rem;
            background: var(--navy);
            color: oklch(98% 0.005 255);
            border: none;
            border-radius: calc(var(--r) - 2px);
            font-size: 0.95rem;
            font-weight: 700;
            font-family: var(--font);
            cursor: pointer;
            transition: background 0.18s, transform 0.12s, box-shadow 0.18s;
            box-shadow: 0 1px 3px oklch(0% 0 0 / 0.15), 0 4px 12px oklch(22% 0.06 255 / 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            position: relative;
        }

        .btn-submit:hover:not(:disabled) {
            background: var(--navy-hover);
            box-shadow: 0 2px 6px oklch(0% 0 0 / 0.12), 0 8px 20px oklch(22% 0.06 255 / 0.32);
            transform: translateY(-1px);
        }

        .btn-submit:active:not(:disabled) { transform: translateY(0); }

        .btn-submit:focus-visible {
            outline: 2px solid var(--gold);
            outline-offset: 3px;
        }

        .btn-submit:disabled {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none;
        }

        .btn-submit .spinner {
            display: none;
            width: 16px;
            height: 16px;
            border: 2px solid oklch(98% 0.005 255 / 0.35);
            border-top-color: oklch(98% 0.005 255);
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            flex-shrink: 0;
        }

        .btn-submit.loading .spinner { display: block; }
        .btn-submit.loading .btn-text { opacity: 0.7; }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* ── DEMO BOX ── */
        .demo-box {
            margin-top: 1rem;
            background: var(--gold-dim);
            border: 1px solid oklch(68% 0.12 80 / 0.25);
            border-radius: calc(var(--r) - 2px);
            padding: 0.7rem 0.9rem;
            font-size: 0.78rem;
            color: var(--text-2);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }

        .demo-box strong { color: var(--text-1); font-weight: 600; }

        .demo-copy-btn {
            background: var(--navy);
            color: oklch(98% 0.005 255);
            border: none;
            border-radius: 6px;
            padding: 0.3rem 0.65rem;
            font-size: 0.72rem;
            font-weight: 600;
            cursor: pointer;
            font-family: var(--font);
            flex-shrink: 0;
            transition: background 0.15s;
        }

        .demo-copy-btn:hover { background: var(--navy-hover); }

        /* ── FOOTER ── */
        .page-footer {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.72rem;
            color: var(--text-3);
        }

        /* ── MODALS ── */
        .modal-content {
            border: 1px solid var(--border);
            border-radius: var(--r);
            box-shadow: 0 20px 56px oklch(0% 0 0 / 0.12);
            font-family: var(--font);
        }

        .modal-header {
            border-bottom: 1px solid var(--border);
            padding: 0.9rem 1.25rem;
        }

        .close-modal-icon {
            cursor: pointer;
            color: var(--text-3);
            font-size: 1rem;
            transition: color 0.15s;
            line-height: 1;
        }

        .close-modal-icon:hover { color: var(--text-1); }

        .modal-form-content {
            padding: 1.75rem 1.5rem 2rem;
            text-align: center;
        }

        .modal-form-content img {
            height: 56px;
            margin-bottom: 1.1rem;
            opacity: 0.85;
        }

        .modal-form-content h4 {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-1);
            margin-bottom: 0.4rem;
        }

        .modal-form-content p {
            font-size: 0.8rem;
            color: var(--text-3);
            line-height: 1.7;
        }

        .modal-form-content .form-control {
            width: 100%;
            background: var(--bg);
            border: 1.5px solid var(--border);
            border-radius: calc(var(--r) - 2px);
            font-family: var(--font);
            font-size: 0.875rem;
            padding: 0.7rem 1rem;
            color: var(--text-1);
            margin-top: 1rem;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
            direction: ltr;
            text-align: right;
        }

        .modal-form-content .form-control:focus {
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px oklch(40% 0.08 255 / 0.1);
        }

        .btn-modal-primary {
            display: block;
            width: 100%;
            background: var(--navy);
            color: oklch(98% 0.005 255);
            border: none;
            border-radius: calc(var(--r) - 2px);
            font-family: var(--font);
            font-weight: 700;
            font-size: 0.875rem;
            padding: 0.75rem 1rem;
            margin-top: 0.9rem;
            cursor: pointer;
            transition: background 0.15s;
            text-align: center;
            text-decoration: none;
        }

        .btn-modal-primary:hover { background: var(--navy-hover); color: oklch(98% 0.005 255); }

        /* ── RESPONSIVE ── */
        @media (max-width: 480px) {
            .form-card { padding: 1.5rem; }
            .heading { font-size: 1.5rem; }
            .brand { margin-bottom: 2rem; }
        }

        @media (prefers-reduced-motion: reduce) {
            .login-wrap { animation: none; }
            * { transition-duration: 0.01ms !important; animation-duration: 0.01ms !important; }
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
    </style>
</head>

<body>
<main id="content" role="main" class="main auth-bg">

    <div class="login-wrap">

        <!-- Brand -->
        @php($systemlogo = \App\Models\BusinessSetting::where(['key'=>'logo'])->first())
        @php($role = $role ?? null)
        <div class="brand">
            <div class="brand-mark">
                <img class="onerror-image"
                    src="{{ \App\CentralLogics\Helpers::get_full_url('business', $systemlogo?->value, $systemlogo?->storage[0]?->value ?? 'public', 'authfav') }}"
                    data-onerror-image="{{ dynamicAsset('/public/assets/admin/img/auth-fav.png') }}"
                    alt="بيت جدي">
            </div>
            <div>
                <div class="brand-name">بيت جدي</div>
                <div class="brand-sub">لوحة الإدارة</div>
            </div>
        </div>

        <h1 class="heading">تسجيل الدخول</h1>
        <p class="sub">أدخل بياناتك للوصول إلى لوحة التحكم</p>

        <!-- Form card -->
        <div class="form-card">
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
        <p class="page-footer">بيت جدي &middot; نظام الإدارة &middot; {{ date('Y') }}</p>

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

<!-- JS -->
<script src="{{ dynamicAsset('public/assets/admin') }}/js/vendor.min.js"></script>
<script src="{{ dynamicAsset('public/assets/admin') }}/js/theme.min.js"></script>
<script src="{{ dynamicAsset('public/assets/admin') }}/js/toastr.js"></script>
{!! Toastr::message() !!}

@if ($errors->any())
    <script>
        @foreach($errors->all() as $error)
        toastr.error('{{ translate($error) }}', 'خطأ', { CloseButton: true, ProgressBar: true, timeOut: 6000 });
        @endforeach
        // Highlight fields on server-side error
        document.getElementById('field-password').classList.add('has-error');
    </script>
@endif

@if ($log_email_succ)
    @php(session()->forget('log_email_succ'))
    <script>$('#successMailModal').modal('show');</script>
@endif

<script>
    // Password toggle init
    $(document).on('ready', function () {
        $('.js-toggle-password').each(function () {
            new HSTogglePassword(this).init();
        });
        $('.js-validate').each(function () {
            $.HSCore.components.HSValidation.init($(this));
        });
    });

    // Role-based forgot password toggle
    $("#role-select").change(function () {
        var v = $(this).val();
        $("#forget-password").toggleClass('d-none', v !== 'admin');
        $("#forget-password1").toggleClass('d-none', v !== 'vendor');
    });

    // Loading state on submit
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

    // Clear field error on input
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
