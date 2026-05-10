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
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin') }}/css/vendor.min.css">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin') }}/vendor/icon-set/style.css">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin') }}/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin') }}/css/theme.minc619.css?v=1.0">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin') }}/css/style.css">
    <link rel="stylesheet" href="{{ dynamicAsset('public/assets/admin') }}/css/toastr.css">

    <style>
        :root {
            --ink:          oklch(16% 0.025 258);
            --ink-mid:      oklch(22% 0.030 258);
            --ink-surface:  oklch(19% 0.028 258);
            --gold:         oklch(72% 0.12 80);
            --gold-dim:     oklch(55% 0.09 80);
            --gold-pale:    oklch(90% 0.05 80);
            --surface:      oklch(99% 0.005 258);
            --muted:        oklch(55% 0.015 258);
            --border:       oklch(88% 0.010 258);
            --border-focus: oklch(40% 0.12 258);
            --text:         oklch(18% 0.020 258);
            --error:        oklch(52% 0.20 25);
            --font:         'Cairo', 'Noto Sans Arabic', sans-serif;
            --radius-sm:    8px;
            --radius-md:    12px;
            --radius-lg:    16px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html {
            height: 100%;
            direction: rtl;
            font-family: var(--font);
            -webkit-font-smoothing: antialiased;
        }

        body {
            min-height: 100vh;
            background-color: var(--ink);
            background-image:
                radial-gradient(ellipse 80% 60% at 50% 0%, oklch(22% 0.032 258) 0%, transparent 70%),
                radial-gradient(ellipse 60% 40% at 80% 100%, oklch(20% 0.025 80 / 0.18) 0%, transparent 60%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
        }

        /* ── CARD ── */
        .login-card {
            width: 100%;
            max-width: 440px;
            background: var(--surface);
            border-radius: var(--radius-lg);
            box-shadow:
                0 0 0 1px oklch(100% 0 0 / 0.06),
                0 4px 6px -1px oklch(0% 0 0 / 0.25),
                0 20px 60px -12px oklch(0% 0 0 / 0.45);
            overflow: hidden;
            animation: lift 0.5s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes lift {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* gold header bar — structural, not decorative */
        .card-header-bar {
            height: 3px;
            background: var(--gold);
        }

        .card-body {
            padding: 2.5rem 2.25rem 2rem;
        }

        /* ── BRAND MARK ── */
        .brand-mark {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 2.25rem;
        }

        .brand-logo {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            object-fit: contain;
            background: var(--ink-surface);
            padding: 4px;
        }

        .brand-text {
            display: flex;
            flex-direction: column;
        }

        .brand-name {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--text);
            line-height: 1.2;
            letter-spacing: 0.01em;
        }

        .brand-sub {
            font-size: 0.72rem;
            font-weight: 500;
            color: var(--muted);
            letter-spacing: 0.06em;
            margin-top: 1px;
        }

        /* ── HEADING ── */
        .form-heading {
            font-size: 1.55rem;
            font-weight: 900;
            color: var(--text);
            line-height: 1.2;
            margin-bottom: 0.35rem;
            letter-spacing: -0.01em;
        }

        .form-sub {
            font-size: 0.85rem;
            color: var(--muted);
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        /* ── VERSION PILL ── */
        .version-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background: var(--gold-pale);
            color: var(--gold-dim);
            border-radius: 999px;
            padding: 0.22rem 0.7rem;
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
        }

        .version-pill-dot {
            width: 5px;
            height: 5px;
            background: var(--gold);
            border-radius: 50%;
        }

        /* ── FIELD ── */
        .field {
            margin-bottom: 1.1rem;
        }

        .field-label {
            display: block;
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 0.45rem;
            letter-spacing: 0.01em;
        }

        .field-wrap {
            position: relative;
        }

        .field-input {
            width: 100%;
            background: oklch(96% 0.006 258);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 0.78rem 0.9rem;
            font-size: 0.88rem;
            font-family: var(--font);
            font-weight: 500;
            color: var(--text);
            transition:
                border-color 0.18s ease,
                background 0.18s ease,
                box-shadow 0.18s ease;
            outline: none;
            appearance: none;
        }

        .field-input::placeholder {
            color: oklch(72% 0.010 258);
            font-weight: 400;
        }

        .field-input:focus {
            background: #fff;
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px oklch(40% 0.12 258 / 0.10);
        }

        .field-input.has-toggle {
            padding-left: 2.75rem;
        }

        .field-toggle {
            position: absolute;
            top: 50%;
            left: 0.8rem;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--muted);
            font-size: 1rem;
            padding: 0;
            line-height: 1;
            display: flex;
            align-items: center;
            transition: color 0.18s ease;
        }

        .field-toggle:hover { color: var(--text); }

        /* ── META ROW ── */
        .meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 0.5rem 0 1.6rem;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.8rem;
            color: var(--muted);
            cursor: pointer;
            user-select: none;
        }

        .remember-label input[type="checkbox"] {
            width: 14px;
            height: 14px;
            accent-color: var(--ink-mid);
            cursor: pointer;
        }

        .forgot-btn {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--gold-dim);
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            font-family: var(--font);
            transition: color 0.18s ease;
        }

        .forgot-btn:hover { color: var(--gold); }

        /* ── SUBMIT ── */
        .btn-submit {
            width: 100%;
            padding: 0.88rem 1rem;
            background: var(--ink-mid);
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            font-size: 0.92rem;
            font-weight: 800;
            font-family: var(--font);
            letter-spacing: 0.03em;
            cursor: pointer;
            transition:
                background 0.2s ease,
                transform 0.15s ease,
                box-shadow 0.2s ease;
            box-shadow: 0 2px 12px oklch(0% 0 0 / 0.20);
            position: relative;
        }

        .btn-submit:hover {
            background: var(--ink);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px oklch(0% 0 0 / 0.28);
        }

        .btn-submit:active {
            transform: translateY(0);
            box-shadow: 0 1px 6px oklch(0% 0 0 / 0.16);
        }

        .btn-submit:focus-visible {
            outline: 2px solid var(--border-focus);
            outline-offset: 3px;
        }

        /* gold shimmer on hover */
        .btn-submit::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: linear-gradient(
                90deg,
                transparent 0%,
                oklch(72% 0.12 80 / 0.08) 50%,
                transparent 100%
            );
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .btn-submit:hover::before { opacity: 1; }

        /* ── CARD FOOTER ── */
        .card-footer-strip {
            border-top: 1px solid var(--border);
            padding: 1rem 2.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .footer-text {
            font-size: 0.7rem;
            color: var(--muted);
            letter-spacing: 0.04em;
        }

        .footer-sep {
            width: 2px;
            height: 2px;
            border-radius: 50%;
            background: var(--gold);
            opacity: 0.6;
        }

        /* ── DEMO BOX ── */
        .demo-box {
            margin-top: 1.25rem;
            background: oklch(96% 0.006 258);
            border: 1px dashed var(--border);
            border-radius: var(--radius-sm);
            padding: 0.85rem 1rem;
            font-size: 0.8rem;
            color: var(--muted);
        }

        .demo-box strong { color: var(--text); }

        /* ── MODALS ── */
        .modal-content {
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: 0 20px 60px oklch(0% 0 0 / 0.14);
            font-family: var(--font);
        }

        .modal-header {
            border-bottom: 1px solid var(--border);
            padding: 1rem 1.5rem;
        }

        .close-modal-icon {
            cursor: pointer;
            color: var(--muted);
            font-size: 1.1rem;
            transition: color 0.18s ease;
        }

        .close-modal-icon:hover { color: var(--text); }

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
            color: var(--text);
            margin-bottom: 0.5rem;
        }

        .forget-pass-content p {
            font-size: 0.84rem;
            color: var(--muted);
            line-height: 1.7;
        }

        .forget-pass-content .form-control {
            background: oklch(96% 0.006 258);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: var(--font);
            font-size: 0.88rem;
            padding: 0.75rem 1rem;
            color: var(--text);
            margin-top: 1rem;
            outline: none;
            width: 100%;
            transition: border-color 0.18s ease, box-shadow 0.18s ease;
        }

        .forget-pass-content .form-control:focus {
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px oklch(40% 0.12 258 / 0.10);
        }

        .btn--primary {
            display: block;
            width: 100%;
            background: var(--ink-mid);
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
            background: var(--ink);
            color: #fff;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 520px) {
            body { padding: 1.25rem 1rem; }
            .card-body { padding: 2rem 1.5rem 1.5rem; }
            .card-footer-strip { padding: 0.9rem 1.5rem; }
            .form-heading { font-size: 1.3rem; }
        }

        @media (prefers-reduced-motion: reduce) {
            .login-card { animation: none; }
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

    <div class="login-card">
        <div class="card-header-bar"></div>

        <div class="card-body">
            @php($systemlogo = \App\Models\BusinessSetting::where(['key'=>'logo'])->first())
            @php($role = $role ?? null)

            <!-- Brand mark -->
            <div class="brand-mark">
                <img class="brand-logo onerror-image"
                    src="{{ \App\CentralLogics\Helpers::get_full_url('business', $systemlogo?->value, $systemlogo?->storage[0]?->value ?? 'public', 'authfav') }}"
                    data-onerror-image="{{ dynamicAsset('/public/assets/admin/img/auth-fav.png') }}"
                    alt="بيت جدي">
                <div class="brand-text">
                    <span class="brand-name">بيت جدي</span>
                    <span class="brand-sub">لوحة الإدارة</span>
                </div>
            </div>

            <!-- Version -->
            <div>
                <span class="version-pill">
                    <span class="version-pill-dot"></span>
                    إصدار {{ env('SOFTWARE_VERSION') }}
                </span>
            </div>

            <h1 class="form-heading">تسجيل الدخول</h1>
            <p class="form-sub">أدخل بياناتك للوصول إلى لوحة التحكم</p>

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
                    تسجيل الدخول
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
        </div>

        <!-- Footer strip -->
        <div class="card-footer-strip">
            <span class="footer-text">بيت جدي</span>
            <span class="footer-sep"></span>
            <span class="footer-text">نظام الإدارة</span>
            <span class="footer-sep"></span>
            <span class="footer-text">{{ date('Y') }}</span>
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
