<!DOCTYPE html>
    <?php
    $log_email_succ = session()->get('log_email_succ');
    ?>
<html dir="{{ $site_direction }}" lang="{{ $locale }}" class="{{ $site_direction === 'rtl'?'active':'' }}">
<head>
    <!-- Required Meta Tags Always Come First -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @php
        $app_name = \App\CentralLogics\Helpers::get_business_settings('business_name', false);
        $icon = \App\CentralLogics\Helpers::get_business_settings('icon', false);
    @endphp
    <!-- Title -->
    <title>{{ translate('messages.login') }} | {{$app_name??translate('STACKFOOD')}}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{asset($icon ? 'storage/app/public/business/'.$icon : 'public/favicon.ico')}}">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&amp;display=swap" rel="stylesheet">
    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{dynamicAsset('public/assets/admin')}}/css/vendor.min.css">
    <link rel="stylesheet" href="{{dynamicAsset('public/assets/admin')}}/vendor/icon-set/style.css">
    <!-- CSS Front Template -->
    <link rel="stylesheet" href="{{dynamicAsset('public/assets/admin')}}/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{dynamicAsset('public/assets/admin')}}/css/theme.minc619.css?v=1.0">
    <link rel="stylesheet" href="{{dynamicAsset('public/assets/admin')}}/css/style.css">
    <link rel="stylesheet" href="{{dynamicAsset('public/assets/admin')}}/css/toastr.css">
    <style>
        body {
            min-height: 100vh;
            background: radial-gradient(circle at top left, #101727 0, #050814 40%, #02030a 100%);
            display: flex;
            align-items: stretch;
            justify-content: center;
            font-family: 'Open Sans', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        .main.auth-bg {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
        }

        .auth-shell {
            width: 100%;
            max-width: 980px;
            background: radial-gradient(circle at top left, rgba(255, 255, 255, 0.12), transparent 55%),
                        linear-gradient(135deg, rgba(13, 27, 56, 0.96), rgba(4, 10, 24, 0.96));
            border-radius: 1.75rem;
            box-shadow: 0 28px 80px rgba(0, 0, 0, 0.55);
            border: 1px solid rgba(255, 255, 255, 0.06);
            overflow: hidden;
            backdrop-filter: blur(18px);
            color: #f5f7fb;
        }

        .auth-shell-inner {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(0, 1fr);
            gap: 0;
        }

        @media (max-width: 992px) {
            .auth-shell-inner {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        .auth-hero {
            position: relative;
            padding: 2.75rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: radial-gradient(circle at top left, rgba(255, 255, 255, 0.12), transparent 65%);
        }

        .auth-hero .title {
            font-size: 1.65rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #e5ecff;
            margin-bottom: 0.75rem;
        }

        .auth-hero p {
            margin: 0;
            color: #9aa4c9;
            max-width: 320px;
        }

        .auth-hero-footer {
            margin-top: 2.5rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.16em;
            color: #7d8ab5;
        }

        .auth-right {
            padding: 2.75rem 2.5rem;
            background: radial-gradient(circle at top right, rgba(15, 35, 85, 0.65), transparent 60%);
            border-left: 1px solid rgba(255, 255, 255, 0.04);
        }

        .auth-wrapper-body {
            background: transparent;
            box-shadow: none;
            border-radius: 0;
        }

        .auth-logo img {
            max-height: 52px;
            filter: drop-shadow(0 12px 22px rgba(0, 0, 0, 0.55));
        }

        .auth-header .signin-txt {
            font-size: 1.4rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #f5f7ff;
        }

        .auth-header .signin-sub {
            font-size: 0.86rem;
            color: #8f9ac3;
            margin-top: 0.25rem;
        }

        .auth-version-badge {
            border-radius: 999px;
            padding: 0.35rem 0.9rem;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            background: rgba(5, 25, 68, 0.8);
            border: 1px solid rgba(93, 118, 191, 0.6);
            color: #b8c6f0;
        }

        .form-label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #ccd3ff;
        }

        .form-control.form-control-lg {
            background: rgba(3, 10, 32, 0.9);
            border-radius: 0.9rem;
            border: 1px solid rgba(61, 82, 138, 0.6);
            color: #f5f7ff;
            padding: 0.8rem 1rem;
            font-size: 0.9rem;
        }

        .form-control.form-control-lg::placeholder {
            color: #707da7;
        }

        .form-control.form-control-lg:focus {
            background-color: rgba(6, 16, 46, 0.95);
            border-color: #2f5ecb;
            box-shadow: 0 0 0 1px rgba(47, 94, 203, 0.6), 0 0 0 4px rgba(12, 46, 132, 0.45);
            outline: none;
        }

        .custom-control-label.text-muted {
            color: #909bbe !important;
            font-size: 0.82rem;
        }

        .auth-meta-row {
            margin-top: 1.1rem;
        }

        .auth-meta-row span[data-toggle="modal"] {
            font-size: 0.82rem;
            color: #a6b3ff;
            cursor: pointer;
            transition: color 0.18s ease, transform 0.18s ease;
        }

        .auth-meta-row span[data-toggle="modal"]:hover {
            color: #e4ebff;
            transform: translateY(-1px);
        }

        .auth-captcha-row {
            margin-top: 1.25rem;
            background: rgba(4, 12, 40, 0.9);
            border-radius: 0.9rem;
            border: 1px solid rgba(61, 82, 138, 0.55);
        }

        .auth-captcha-row .form-control-lg {
            border-radius: 0.9rem 0 0 0.9rem;
        }

        .auth-captcha-row .bg-white {
            background: radial-gradient(circle at top, #ffffff, #dfe6ff) !important;
        }

        .auth-captcha-row .capcha-spin {
            color: #233571;
        }

        .btn-navy-primary {
            background: linear-gradient(135deg, #102a6b, #04153c);
            border-color: #102a6b;
            color: #f5f7ff;
            border-radius: 999px;
            padding: 0.85rem 1rem;
            font-size: 0.95rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            box-shadow: 0 14px 30px rgba(3, 16, 66, 0.75);
            transition: background 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease, border-color 0.18s ease;
        }

        .btn-navy-primary:hover {
            background: linear-gradient(135deg, #18398d, #061b4f);
            border-color: #18398d;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 18px 40px rgba(3, 20, 80, 0.9);
        }

        .btn-navy-primary:focus,
        .btn-navy-primary:active {
            background: linear-gradient(135deg, #0b2467, #021131);
            border-color: #2a4aa4;
            box-shadow: 0 0 0 1px rgba(144, 169, 255, 0.6), 0 0 0 5px rgba(33, 61, 150, 0.75);
            outline: none;
        }

        .btn-navy-primary:disabled {
            background: linear-gradient(135deg, #101727, #111321);
            border-color: #262d46;
            box-shadow: none;
            opacity: 0.7;
        }

        .auto-fill-data-copy {
            background: rgba(3, 12, 38, 0.95);
            border-radius: 1rem;
            border: 1px dashed rgba(88, 116, 190, 0.75);
            color: #d7dfff;
        }

        .auto-fill-data-copy strong {
            color: #f5f7ff;
        }

        .auto-fill-data-copy .btn-primary {
            border-radius: 999px;
        }

        .forget-pass-content h4 {
            color: #111827;
        }

        .forget-pass-content p {
            color: #4b5563;
        }
    </style>
</head>

<body>
<!-- ========== MAIN CONTENT ========== -->
<main id="content" role="main" class="main auth-bg">
    <!-- Content -->
    <div class="auth-shell">
        <div class="auth-shell-inner">
            <div class="auth-content auth-hero">
                <div class="content">
                    <h2 class="title text-uppercase">{{translate('messages.welcome_to')}} {{ $app_name??'STACKFOOD' }}</h2>
                    <p>
                        {{translate('Manage_your_app_&_website_easily')}}
                    </p>
                </div>
                <div class="auth-hero-footer">
                    {{ translate('Secure_restaurant_and_admin_management_panel') }}
                </div>
            </div>
            <div class="auth-wrapper auth-right">
            <div class="auth-wrapper-body auth-form-appear">
                @php($systemlogo=\App\Models\BusinessSetting::where(['key'=>'logo'])->first())
                @php($role = $role ?? null )
                <a class="auth-logo mb-5" href="javascript:">
                    <img class="z-index-2 onerror-image"
                    src="{{ \App\CentralLogics\Helpers::get_full_url('business',$systemlogo?->value,$systemlogo?->storage[0]?->value ?? 'public', 'authfav') }}"
                    data-onerror-image="{{ dynamicAsset('/public/assets/admin/img/auth-fav.png') }}" alt="image">
                </a>
                <div class="text-center">
                    <div class="auth-header mb-2">
                        <h2 class="signin-txt">{{ translate('messages.Signin_To_Your_Panel')}}</h2>
                        <p class="signin-sub">{{ translate('Access_all_your_tools_from_a_single_modern_dashboard') }}</p>
                    </div>
                </div>
                <!-- Content -->
                <label class="badge badge-soft-success float-right initial-1 auth-version-badge">
                    {{translate('messages.software_version')}} : {{env('SOFTWARE_VERSION')}}
                </label>
                <!-- Form -->
                <form class="login_form" action="{{route('login_post')}}" method="post" id="form-id">
                    @csrf
                    <input type="hidden" name="role" value="{{  $role ?? null }}">

                    <div class="js-form-message form-group mb-2">
                        <label class="form-label text-capitalize" for="signinSrEmail">{{translate('messages.your_email')}}</label>
                        <input type="email" class="form-control form-control-lg" value="{{ $email ?? '' }}" name="email" id="signinSrEmail"
                            tabindex="1" aria-label="email@address.com"
                            required data-msg="Please enter a valid email address.">
                        <div class="focus-effects"></div>
                    </div>
                    <!-- End Form Group -->

                    <!-- Form Group -->
                    <div class="js-form-message form-group">
                        <label class="form-label text-capitalize" for="signupSrPassword" tabindex="0">
                            <span class="d-flex justify-content-between align-items-center">
                            {{translate('messages.password')}}
                            </span>
                        </label>
                        <div class="input-group input-group-merge">
                            <input type="password" class="js-toggle-password form-control form-control-lg __rounded"
                                name="password" id="signupSrPassword" value="{{ $password ?? '' }}"
                                aria-label="{{translate('messages.password_length_placeholder',['length'=>'6+'])}}" required
                                data-msg="{{translate('messages.invalid_password_warning')}}"
                                data-hs-toggle-password-options='{
                                            "target": "#changePassTarget",
                                    "defaultClass": "tio-hidden-outlined",
                                    "showClass": "tio-visible-outlined",
                                    "classChangeTarget": "#changePassIcon"
                                    }'>

                            <div class="focus-effects"></div>
                            <div id="changePassTarget" class="input-group-append">
                                <a class="input-group-text" href="javascript:">
                                    <i id="changePassIcon" class="tio-visible-outlined"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- End Form Group -->
                        <div class="mb-2"></div>
                        <div class="d-flex justify-content-between mt-3 auth-meta-row">
                    <!-- Checkbox -->
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="termsCheckbox" {{ $password ? 'checked' : '' }}
                                    name="remember">
                                <label class="custom-control-label text-muted" for="termsCheckbox">
                                    {{translate('messages.remember_me')}}
                                </label>
                            </div>
                        </div>
                    <!-- End Checkbox -->
                    <!-- forget password -->
                        <div class="form-group {{ $role == 'admin' ? '' : 'd-none' }}"  id="forget-password">
                            <div class="custom-control">
                                <span type="button" data-toggle="modal" data-target="#forgetPassModal">{{ translate('Forget_Password?') }}</span>
                            </div>
                        </div>
                        <div class="form-group {{ $role == 'vendor' ? '' : 'd-none' }}"  id="forget-password1">
                            <div class="custom-control">
                                <span type="button" data-toggle="modal" data-target="#forgetPassModal1">{{ translate('Forget_Password?') }}</span>
                            </div>
                        </div>
                    </div>
                    <!-- End forget password -->


                    @php($recaptcha = \App\CentralLogics\Helpers::get_business_settings('recaptcha'))
                    @if(isset($recaptcha) && $recaptcha['status'] == 1)
                        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

                        <input type="hidden" name="set_default_captcha" id="set_default_captcha_value" value="0" >
                        <div class="row p-2 d-none" id="reload-captcha">
                            <div class="col-6 pr-0">
                                <input type="text" class="form-control form-control-lg border-0" name="custome_recaptcha"
                                       id="custome_recaptcha" required placeholder="{{translate('Enter recaptcha value')}}" autocomplete="off" value="{{env('APP_MODE')=='dev'? session('six_captcha'):''}}">
                            </div>
                            <div class="col-6 bg-white rounded d-flex">
                                <img src="<?php echo $custome_recaptcha->inline(); ?>" class="rounded w-100" />
                                <div class="p-3 pr-0 capcha-spin reloadCaptcha">
                                    <i class="tio-cached"></i>
                                </div>
                            </div>
                        </div>

                    @else
                        <div class="row p-2 auth-captcha-row" id="reload-captcha">
                            <div class="col-6 pr-0">
                                <input type="text" class="form-control form-control-lg border-0" name="custome_recaptcha"
                                       id="custome_recaptcha" required placeholder="{{translate('Enter recaptcha value')}}" autocomplete="off" value="{{env('APP_MODE')=='dev'? session('six_captcha'):''}}">
                            </div>
                            <div class="col-6 bg-white rounded d-flex">
                                <img src="<?php echo $custome_recaptcha->inline(); ?>" class="rounded w-100" />
                                <div class="p-3 pr-0 capcha-spin reloadCaptcha">
                                    <i class="tio-cached"></i>
                                </div>
                            </div>
                        </div>
                    @endif

                    <button type="submit" class="btn btn-lg btn-block btn-primary btn-navy-primary" id="signInBtn">{{translate('messages.sign_in')}}</button>
                </form>
                <!-- End Form -->

                <!-- End Content -->
            </div>
            @if(env('APP_MODE') =='demo' )
                @if (isset($role) &&  $role == 'admin')
                    <div class="auto-fill-data-copy">
                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                            <div>
                                <span class="d-block"><strong>Email</strong> : admin@admin.com</span>
                                <span class="d-block"><strong>Password</strong> : 12345678</span>
                            </div>
                            <div>
                                <button class="btn btn-primary m-0" id="copy_cred"><i class="tio-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
                @if (isset($role) &&  $role == 'vendor')
                    <div class="auto-fill-data-copy">
                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                            <div>
                                <span class="d-block"><strong>Email</strong> : test.restaurant@gmail.com</span>
                                <span class="d-block"><strong>Password</strong> : 12345678</span>
                            </div>
                            <div>
                                <button class="btn btn-primary m-0" id="copy_cred2"><i class="tio-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
        </div>
    </div>
</main>
<!-- ========== END MAIN CONTENT ========== -->


<div class="modal fade" id="forgetPassModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header justify-content-end">
          <span type="button" class="close-modal-icon" data-dismiss="modal">
              <i class="tio-clear"></i>
          </span>
        </div>
        <div class="modal-body">
          <div class="forget-pass-content">
              <img src="{{dynamicAsset('/public/assets/admin/img/send-mail.svg')}}" alt="">
              <!-- After Succeed -->
              <h4>
                  {{ translate('Send_Mail_to_Your_Email_?') }}
              </h4>
              <p>
                  {{ translate('A_mail_will_be_send_to_your_registered_email_with_a_link_to_change_passowrd') }}
              </p>
              <a class="btn btn-lg btn-block btn--primary mt-3" href="{{route('reset-password')}}">
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
          <span type="button" class="close-modal-icon" data-dismiss="modal">
              <i class="tio-clear"></i>
          </span>
        </div>
        <div class="modal-body">
          <div class="forget-pass-content">
              <img src="{{dynamicAsset('/public/assets/admin/img/send-mail.svg')}}" alt="">
              <!-- After Succeed -->
              <h4>
                  {{ translate('messages.Send_Mail_to_Your_Email_?') }}
              </h4>
              <form class="" action="{{ route('vendor-reset-password') }}" method="post">
                  @csrf

                  <input type="email" name="email" id="" class="form-control" required>
                  <button type="submit" class="btn btn-lg btn-block btn--primary mt-3">{{ translate('messages.Send_Mail') }}</button>
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
            <span type="button" class="close-modal-icon" data-dismiss="modal">
                <i class="tio-clear"></i>
            </span>
          </div>
          <div class="modal-body">
            <div class="forget-pass-content">
                <!-- After Succeed -->
                <img src="{{dynamicAsset('/public/assets/admin/img/sent-mail.svg')}}" alt="">
                <h4>
                  {{ translate('A_mail_has_been_sent_to_your_registered_email') }}!
                </h4>
                <p>
                  {{ translate('Click_the_link_in_the_mail_description_to_change_password') }}
                </p>
            </div>
          </div>
        </div>
      </div>
    </div>


<!-- JS Implementing Plugins -->
<script src="{{dynamicAsset('public/assets/admin')}}/js/vendor.min.js"></script>

<!-- JS Front -->
<script src="{{dynamicAsset('public/assets/admin')}}/js/theme.min.js"></script>
<script src="{{dynamicAsset('public/assets/admin')}}/js/toastr.js"></script>
{!! Toastr::message() !!}

@if ($errors->any())
    <script>
        @foreach($errors->all() as $error)
        toastr.error('{{translate($error)}}', Error, {
            CloseButton: true,
            ProgressBar: true
        });
        @endforeach
    </script>
@endif
@if ($log_email_succ)
@php(session()->forget('log_email_succ'))
    <script>
        $('#successMailModal').modal('show');
    </script>
@endif

<script>
    // $("#forget-password").hide();
      $("#role-select").change(function() {
        var selectValue = $(this).val();
        if (selectValue == "admin") {
          $("#forget-password").show();
          $("#forget-password1").hide();
        } else if(selectValue == "vendor") {
          $("#forget-password").hide();
          $("#forget-password1").show();
        }
        else {
          $("#forget-password").hide();
          $("#forget-password1").hide();
        }
      });
</script>


<script>
    $(document).on('click','.reloadCaptcha', function(){
        $.ajax({
            url: "{{ route('reload-captcha') }}",
            type: "GET",
            dataType: 'json',
            beforeSend: function () {
                $('#loading').show()
                $('.capcha-spin').addClass('active')
            },
            success: function(data) {
                $('#reload-captcha').html(data.view);
            },
            complete: function () {
                $('#loading').hide()
                $('.capcha-spin').removeClass('active')
            }
        });
    });
</script>
<!-- JS Plugins Init. -->
<script>
    $(document).on('ready', function () {
        // INITIALIZATION OF SHOW PASSWORD
        // =======================================================
        $('.js-toggle-password').each(function () {
            new HSTogglePassword(this).init()
        });

        // INITIALIZATION OF FORM VALIDATION
        // =======================================================
        $('.js-validate').each(function () {
            $.HSCore.components.HSValidation.init($(this));
        });
    });
</script>

@if(isset($recaptcha) && $recaptcha['status'] == 1)
    <script src="https://www.google.com/recaptcha/api.js?render={{$recaptcha['site_key']}}"></script>
@endif
@if(isset($recaptcha) && $recaptcha['status'] == 1)
    <script>
        $(document).ready(function() {
            $('#signInBtn').click(function (e) {
                if( $('#set_default_captcha_value').val() == 1){
                    $('#form-id').submit();
                    return true;
                }
                e.preventDefault();
                if (typeof grecaptcha === 'undefined') {
                    toastr.error('Invalid recaptcha key provided. Please check the recaptcha configuration.');
                    $('#reload-captcha').removeClass('d-none');
                    $('#set_default_captcha_value').val('1');

                    return;
                }
                grecaptcha.ready(function () {
                    grecaptcha.execute('{{$recaptcha['site_key']}}', {action: 'submit'}).then(function (token) {
                        $('#g-recaptcha-response').value = token;
                        $('#form-id').submit();
                    });
                });
                window.onerror = function (message) {
                    var errorMessage = 'An unexpected error occurred. Please check the recaptcha configuration';
                    if (message.includes('Invalid site key')) {
                        errorMessage = 'Invalid site key provided. Please check the recaptcha configuration.';
                    } else if (message.includes('not loaded in api.js')) {
                        errorMessage = 'reCAPTCHA API could not be loaded. Please check the recaptcha API configuration.';
                    }
                    $('#reload-captcha').removeClass('d-none');
                    $('#set_default_captcha_value').val('1');
                    toastr.error(errorMessage)
                    return true;
                };
            });
        });
    </script>
@endif
{{-- recaptcha scripts end --}}



@if(env('APP_MODE') =='demo')
    <script>
        $("#copy_cred").click(function() {
            $('#signinSrEmail').val('admin@admin.com');
            $('#signupSrPassword').val('12345678');
            toastr.success('Copied successfully!', 'Success!', {
                CloseButton: true,
                ProgressBar: true
            });
        })
        $("#copy_cred2").click(function() {
            $('#signinSrEmail').val('test.restaurant@gmail.com');
            $('#signupSrPassword').val('12345678');
            toastr.success('Copied successfully!', 'Success!', {
                CloseButton: true,
                ProgressBar: true
            });
        })
    </script>
@endif

<!-- IE Support -->
<script>
    if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) document.write('<script src="{{dynamicAsset('public//assets/admin')}}/vendor/babel-polyfill/polyfill.min.js"><\/script>');
</script>
</body>
</html>
