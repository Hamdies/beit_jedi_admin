@extends('layouts.vendor.app')

@section('title',translate('Update delivery-man'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --bj-cream: #f6f1e7;
            --bj-ink: #15233f;
            --bj-ink-2: #1f2d4a;
            --bj-muted: #6b7280;
            --bj-line: rgba(15,35,63,0.08);
            --bj-soft: #fbf8f1;
            --bj-amber: #f0b829;
            --bj-amber-soft: #fdf1d2;
            --bj-green: #2fb574;
            --bj-green-soft: #d8f1e2;
            --bj-blue: #2f6fed;
            --bj-blue-soft: #dde7fb;
            --bj-red: #e25c5c;
        }

        body { background: var(--bj-cream); }
        .footer { background: transparent; }

        .bj-dm-edit { padding: 1.5rem 1.25rem 2rem; direction: rtl; }

        /* Top bar */
        .bj-topbar {
            display: flex; align-items: center; justify-content: space-between;
            gap: 1rem; flex-wrap: wrap; margin-bottom: 1.25rem;
        }
        .bj-page-head .bj-crumbs {
            font-size: .8rem; color: var(--bj-muted); margin-bottom: .35rem;
            display: flex; align-items: center; gap: .25rem; flex-wrap: wrap;
        }
        .bj-page-head .bj-crumbs i { font-size: .65rem; opacity: .6; }
        .bj-page-head .bj-crumbs a { color: var(--bj-muted); text-decoration: none; }
        .bj-page-head .bj-crumbs a:hover { color: var(--bj-ink); }
        .bj-page-head h1 {
            font-size: 1.85rem; font-weight: 800; color: var(--bj-ink); margin: 0 0 .25rem;
        }
        .bj-page-head p { color: var(--bj-muted); font-size: .85rem; margin: 0; }

        .bj-topactions { display: flex; gap: .5rem; align-items: center; flex-wrap: wrap; }
        .bj-pill-btn {
            background: #fff; border: 1px solid var(--bj-line);
            border-radius: 14px; padding: .55rem .9rem;
            font-size: .85rem; font-weight: 600; color: var(--bj-ink);
            display: inline-flex; align-items: center; gap: .4rem;
            transition: all .15s ease; cursor: pointer;
        }
        .bj-pill-btn:hover { background: var(--bj-soft); color: var(--bj-ink); text-decoration: none; }
        .bj-pill-btn.is-dark { background: var(--bj-ink); color: #fff; border-color: var(--bj-ink); }
        .bj-pill-btn.is-dark:hover { background: var(--bj-ink-2); color: #fff; }

        /* Layout */
        .bj-grid {
            display: grid;
            grid-template-columns: 360px 1fr;
            gap: 1rem;
            align-items: start;
        }
        @media (max-width: 991px) { .bj-grid { grid-template-columns: 1fr; } }

        .bj-card {
            background: #fff;
            border: 1px solid var(--bj-line);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(15,35,63,0.02);
        }
        .bj-card-head {
            display: flex; align-items: center; gap: .55rem;
            padding: 1rem 1.2rem .75rem;
            border-bottom: 1px solid var(--bj-line);
            background: var(--bj-soft);
        }
        .bj-card-head .bj-icon {
            width: 32px; height: 32px; border-radius: 10px;
            background: #fff; border: 1px solid var(--bj-line);
            display: inline-flex; align-items: center; justify-content: center;
            color: var(--bj-ink);
        }
        .bj-card-head h5 { margin: 0; font-weight: 800; color: var(--bj-ink); font-size: .95rem; }
        .bj-card-head .bj-sub { font-size: .75rem; color: var(--bj-muted); }
        .bj-card-body { padding: 1.2rem; }

        /* Identity card (left side) */
        .bj-identity-card { position: sticky; top: 1rem; }
        .bj-identity-card .bj-card-body {
            display: flex; flex-direction: column; align-items: center;
            padding: 1.5rem 1.2rem 1.2rem;
        }
        .bj-avatar-frame {
            position: relative; width: 140px; height: 140px;
            border-radius: 28px;
            background: var(--bj-soft);
            border: 1px solid var(--bj-line);
            overflow: hidden;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 14px rgba(15,35,63,0.06);
        }
        .bj-avatar-frame img {
            width: 100%; height: 100%; object-fit: cover;
        }
        .bj-avatar-frame .bj-presence {
            position: absolute; bottom: -2px; left: -2px;
            width: 22px; height: 22px; border-radius: 50%;
            border: 3px solid #fff; background: var(--bj-green);
        }
        .bj-identity-name {
            margin-top: 1rem; font-size: 1.1rem; font-weight: 800; color: var(--bj-ink);
            text-align: center;
        }
        .bj-identity-meta {
            font-size: .8rem; color: var(--bj-muted); text-align: center; margin-top: .15rem;
            direction: ltr;
        }
        .bj-identity-tags {
            display: flex; gap: .35rem; flex-wrap: wrap; justify-content: center; margin-top: .75rem;
        }
        .bj-tag {
            display: inline-flex; align-items: center; gap: .25rem;
            font-size: .72rem; font-weight: 700;
            padding: .2rem .55rem; border-radius: 8px;
        }
        .bj-tag.is-rating { background: var(--bj-amber-soft); color: #8c6406; }
        .bj-tag.is-status { background: var(--bj-green-soft); color: #1f7a4a; }
        .bj-tag.is-status .bj-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--bj-green); }

        .bj-upload {
            margin-top: 1rem; width: 100%;
            border: 1.5px dashed rgba(15,35,63,0.18);
            background: var(--bj-soft);
            border-radius: 14px;
            padding: .85rem 1rem;
            display: flex; align-items: center; gap: .55rem;
            cursor: pointer; transition: border-color .15s, background .15s;
        }
        .bj-upload:hover { border-color: var(--bj-ink); background: #fff; }
        .bj-upload i { color: var(--bj-ink); font-size: 1.05rem; }
        .bj-upload .bj-upload-text { flex: 1; }
        .bj-upload .bj-upload-text strong { display: block; color: var(--bj-ink); font-size: .85rem; }
        .bj-upload .bj-upload-text span { font-size: .72rem; color: var(--bj-muted); }
        .bj-upload input[type="file"] { display: none; }

        /* Form fields */
        .bj-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        @media (max-width: 600px) { .bj-row { grid-template-columns: 1fr; } }

        .bj-field { display: flex; flex-direction: column; gap: .4rem; }
        .bj-field label {
            font-size: .82rem; font-weight: 700; color: var(--bj-ink); margin: 0;
            display: flex; align-items: center; gap: .35rem;
        }
        .bj-field label .req { color: var(--bj-red); font-weight: 700; }
        .bj-input-wrap {
            position: relative;
            display: flex; align-items: center;
            background: var(--bj-soft);
            border: 1px solid var(--bj-line);
            border-radius: 12px;
            padding: 0 .9rem;
            transition: border-color .15s, background .15s, box-shadow .15s;
        }
        .bj-input-wrap:focus-within {
            border-color: var(--bj-ink); background: #fff;
            box-shadow: 0 0 0 3px rgba(15,35,63,0.07);
        }
        .bj-input-wrap > i { color: var(--bj-muted); font-size: .9rem; }
        .bj-input-wrap input {
            flex: 1; border: 0; background: transparent; outline: none;
            padding: .7rem .6rem; font-size: .9rem; color: var(--bj-ink);
            direction: rtl; text-align: right; width: 100%;
        }
        .bj-input-wrap input[type="tel"],
        .bj-input-wrap input[type="email"] { direction: ltr; text-align: left; }
        .bj-help {
            font-size: .72rem; color: var(--bj-muted);
            display: flex; align-items: center; gap: .3rem;
        }

        /* Form actions */
        .bj-form-actions {
            display: flex; justify-content: flex-end; gap: .5rem;
            margin-top: 1.25rem; padding-top: 1.1rem;
            border-top: 1px solid var(--bj-line);
        }
        .bj-btn {
            border: 1px solid var(--bj-line);
            background: #fff; color: var(--bj-ink);
            padding: .65rem 1.2rem; border-radius: 12px;
            font-size: .87rem; font-weight: 700; cursor: pointer;
            display: inline-flex; align-items: center; gap: .4rem;
            transition: all .15s;
        }
        .bj-btn:hover { background: var(--bj-soft); }
        .bj-btn.is-primary {
            background: var(--bj-ink); color: #fff; border-color: var(--bj-ink);
        }
        .bj-btn.is-primary:hover { background: var(--bj-ink-2); }

        /* card stack spacing */
        .bj-stack { display: flex; flex-direction: column; gap: 1rem; }
    </style>
@endpush

@section('content')
<?php
    $dm = $delivery_man;
    $fullName = trim(($dm['f_name'] ?? '').' '.($dm['l_name'] ?? ''));
    $rating = (isset($dm->rating) && count($dm->rating) > 0)
        ? number_format($dm->rating[0]->average, 1, '.', '')
        : '0.0';
    $isApproved = ($dm->application_status ?? '') === 'approved';
    $isActive = $isApproved && (int)($dm->active ?? 0) === 1;
    if ($isActive) {
        $statusLabel = 'متصل';
        $statusBg = 'var(--bj-green-soft)';
        $statusFg = '#1f7a4a';
        $statusDot = 'var(--bj-green)';
    } elseif ($isApproved) {
        $statusLabel = 'غير متصل';
        $statusBg = '#eef0f4';
        $statusFg = '#475467';
        $statusDot = '#98a2b3';
    } elseif (($dm->application_status ?? '') === 'denied') {
        $statusLabel = 'مرفوض';
        $statusBg = 'var(--bj-amber-soft)';
        $statusFg = '#a53030';
        $statusDot = 'var(--bj-red)';
    } else {
        $statusLabel = 'بانتظار الاعتماد';
        $statusBg = 'var(--bj-amber-soft)';
        $statusFg = '#8c6406';
        $statusDot = 'var(--bj-amber)';
    }
?>

<div class="content container-fluid bj-dm-edit">

    {{-- Top bar --}}
    <div class="bj-topbar">
        <div class="bj-page-head">
            <div class="bj-crumbs">
                <a href="{{ route('vendor.dashboard') }}">لوحة التحكم</a>
                <i class="tio-chevron-left"></i>
                <a href="{{ route('vendor.delivery-man.list') }}">المناديب</a>
                <i class="tio-chevron-left"></i>
                <span>تعديل</span>
            </div>
            <h1>تعديل بيانات المندوب</h1>
            <p>حدّث المعلومات الشخصية وبيانات التواصل والصورة الخاصة بالمندوب.</p>
        </div>

        <div class="bj-topactions">
            <a href="{{ route('vendor.delivery-man.list') }}" class="bj-pill-btn">
                <i class="tio-chevron-right"></i> رجوع للقائمة
            </a>
            <a href="{{ route('vendor.delivery-man.preview', [$dm['id']]) }}" class="bj-pill-btn">
                <i class="tio-visible-outlined"></i> عرض الملف
            </a>
        </div>
    </div>

    <form action="javascript:" method="post" enctype="multipart/form-data" id="deliaveryman_form">
        @csrf

        <div class="bj-grid">

            {{-- Identity card --}}
            <div>
                <div class="bj-card bj-identity-card">
                    <div class="bj-card-head">
                        <span class="bj-icon"><i class="tio-user"></i></span>
                        <div>
                            <h5>{{ translate('messages.Delivery_Man_Image') }}</h5>
                            <div class="bj-sub">نسبة الأبعاد الموصى بها 1:1</div>
                        </div>
                    </div>
                    <div class="bj-card-body">
                        <div class="bj-avatar-frame">
                            <img id="viewer"
                                 src="{{ $dm['image_full_url'] }}"
                                 alt="{{ $fullName }}"/>
                            <span class="bj-presence" style="background: {{ $statusDot }};"></span>
                        </div>

                        <div class="bj-identity-name">{{ $fullName ?: '—' }}</div>
                        @if(!empty($dm['phone']))
                            <div class="bj-identity-meta">{{ $dm['phone'] }}</div>
                        @endif

                        <div class="bj-identity-tags">
                            <span class="bj-tag is-rating"><i class="tio-star"></i> {{ $rating }}</span>
                            <span class="bj-tag is-status" style="background: {{ $statusBg }}; color: {{ $statusFg }};">
                                <span class="bj-dot" style="background: {{ $statusDot }};"></span>
                                {{ $statusLabel }}
                            </span>
                        </div>

                        <label class="bj-upload" for="customFileEg1">
                            <i class="tio-upload"></i>
                            <div class="bj-upload-text">
                                <strong>تغيير الصورة</strong>
                                <span>JPG, PNG, WEBP — حتى 2MB</span>
                            </div>
                            <input type="file" name="image" id="customFileEg1"
                                   accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                        </label>
                    </div>
                </div>
            </div>

            {{-- Form columns --}}
            <div class="bj-stack">

                {{-- General Info --}}
                <div class="bj-card">
                    <div class="bj-card-head">
                        <span class="bj-icon"><i class="tio-user-outlined"></i></span>
                        <div>
                            <h5>{{ translate('messages.General_Information') }}</h5>
                            <div class="bj-sub">الاسم الذي يظهر للعملاء وفريق العمليات</div>
                        </div>
                    </div>
                    <div class="bj-card-body">
                        <div class="bj-row">
                            <div class="bj-field">
                                <label>{{ translate('messages.first_name') }} <span class="req">*</span></label>
                                <div class="bj-input-wrap">
                                    <i class="tio-user"></i>
                                    <input type="text" name="f_name"
                                           value="{{ $dm['f_name'] }}"
                                           placeholder="{{ translate('messages.first_name') }}" required>
                                </div>
                            </div>
                            <div class="bj-field">
                                <label>{{ translate('messages.last_name') }} <span class="req">*</span></label>
                                <div class="bj-input-wrap">
                                    <i class="tio-user"></i>
                                    <input type="text" name="l_name"
                                           value="{{ $dm['l_name'] }}"
                                           placeholder="{{ translate('messages.last_name') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Account Info --}}
                <div class="bj-card">
                    <div class="bj-card-head">
                        <span class="bj-icon"><i class="tio-phone-outlined"></i></span>
                        <div>
                            <h5>{{ translate('messages.account_Information') }}</h5>
                            <div class="bj-sub">بيانات التواصل المستخدمة في تسجيل الدخول والتنبيهات</div>
                        </div>
                    </div>
                    <div class="bj-card-body">
                        <div class="bj-row">
                            <div class="bj-field">
                                <label>{{ translate('messages.phone') }} <span class="req">*</span></label>
                                <div class="bj-input-wrap">
                                    <i class="tio-phone"></i>
                                    <input type="tel" id="phone" name="phone"
                                           value="{{ $dm['phone'] }}"
                                           placeholder="{{ translate('messages.Ex :') }} 017********"
                                           required>
                                </div>
                                <span class="bj-help">
                                    <i class="tio-info-outlined"></i> رقم الجوال يُستخدم لتسجيل الدخول.
                                </span>
                            </div>

                            @if(!empty($dm['email']))
                                <div class="bj-field">
                                    <label>البريد الإلكتروني</label>
                                    <div class="bj-input-wrap">
                                        <i class="tio-mail-outlined"></i>
                                        <input type="email" value="{{ $dm['email'] }}"
                                               placeholder="example@mail.com" disabled
                                               style="opacity:.75; cursor:not-allowed;">
                                    </div>
                                    <span class="bj-help">
                                        <i class="tio-locked-outlined"></i> لا يمكن تعديل البريد من هنا.
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="bj-form-actions">
                            <button type="reset" class="bj-btn">
                                <i class="tio-refresh"></i> {{ translate('messages.reset') }}
                            </button>
                            <button type="submit" class="bj-btn is-primary">
                                <i class="tio-checkmark-circle-outlined"></i> {{ translate('messages.submit') }}
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </form>
</div>
@endsection

@push('script_2')
    <script>
        "use strict";
        $("#customFileEg1").change(function () {
            readURL(this);
        });

        $('#deliaveryman_form').on('submit', function () {
            let formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{ route('vendor.delivery-man.update',[$delivery_man['id']]) }}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    if (data.errors) {
                        for (let i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else if (data.message) {
                        toastr.success(data.message, {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function () {
                            location.href = '{{ route('vendor.delivery-man.list') }}';
                        }, 2000);
                    }
                }
            });
        });
    </script>
@endpush
