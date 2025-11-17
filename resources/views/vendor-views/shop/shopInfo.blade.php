@extends('layouts.vendor.app')
@section('title','بيانات المتجر')
@section('content')
    <div class="content container-fluid" style="background:#f5f7fa; padding:1.75rem 0 1.5rem;">
        <div class="card card-from-sm" style="border-radius:24px; border:1px solid rgba(44,62,111,0.06); box-shadow:0 10px 25px rgba(15,23,42,0.03);">
            <div class="card-body">
                <!-- Page Header -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <div class="page--header-title">
                        <h1 class="page-header-title mb-1" style="font-weight:700; color:#1b3056;">بيانات المتجر</h1>
                        <p class="page-header-text mb-0" style="color:#6b7280; font-size:.9rem;">تاريخ إنشاء المتجر: {{ \App\CentralLogics\Helpers::time_date_format($shop->created_at) }}</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{route('vendor.shop.edit')}}" class="btn btn--primary"><i class="tio-open-in-new"></i> تعديل بيانات المتجر</a>
                    </div>
                </div>
                <!-- End Page Header -->
                <!-- Banner -->
                <section class="shop-details-banner">
                    <div class="card">
                        <div class="card-body px-0 pt-0">
                            <img  class="shop-details-banner-img"
                            src="{{ $shop?->cover_photo_full_url ?? dynamicAsset('public/assets/admin/img/900x400/img1.jpg') }}"
                            alt="image">

                            <div class="shop-details-banner-content">
                                <div class="shop-details-banner-content-thumbnail">
                                    <img class="thumbnail"
                                    src="{{ $shop?->logo_full_url ?? dynamicAsset('public/assets/admin/img/160x160/img1.jpg') }}"
                                    alt="image">
                                    <h3 class="mt-4 pt-3 mb-4 d-sm-none">{{$shop->name}}</h3>
                                </div>
                                <div class="shop-details-banner-content-content">
                                    <h3 class="mt-sm-4 pt-sm-3 mb-4 d-none d-sm-block">{{$shop->name}}</h3>
                                    <div class="shop-details-model">
                                        {{-- Commission-related info hidden from shop view --}}
                                        {{--
                                        <div class="shop-details-model-item">
                                            <img src="{{dynamicAsset('/public/assets/admin/new-img/icon-1.png')}}" alt="">
                                            <div class="shop-details-model-item-content">
                                                <h6>نظام عمل المتجر</h6>
                                                @if($shop->restaurant_model == 'commission')
                                                    <div>عمولة على كل طلب</div>
                                                @elseif($shop->restaurant_model == 'none')
                                                    <div>لم يتم الاختيار بعد</div>
                                                @else
                                                    <div>اشتراك شهري / سنوي</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="shop-details-model-item">
                                            <img src="{{dynamicAsset('/public/assets/admin/new-img/icon_6.png')}}" alt="">
                                            <div class="shop-details-model-item-content">
                                                <h6>عمولة المنصة</h6>
                                                <div> {{(isset($shop->comission)?$shop->comission:\App\Models\BusinessSetting::where('key','admin_commission')->first()?->value)}} %</div>
                                            </div>
                                        </div>
                                        --}}
                                        <div class="shop-details-model-item">
                                            <img src="{{dynamicAsset('/public/assets/admin/new-img/icon-2.png')}}" alt="">
                                            <div class="shop-details-model-item-content">
                                                <h6 style="font-weight:600; color:#1b3056;">الضريبة المضافة</h6>
                                                <div style="color:#4b5563;"> {{ $shop->tax  }} %</div>
                                            </div>
                                        </div>
                                        <div class="shop-details-model-item">
                                            <img src="{{dynamicAsset('/public/assets/admin/new-img/icon-3.png')}}" alt="">
                                            <div class="shop-details-model-item-content">
                                                <h6 style="font-weight:600; color:#1b3056;">رقم الهاتف</h6>
                                                <div style="color:#4b5563;">{{$shop->phone}}</div>
                                            </div>
                                        </div>
                                        <div class="shop-details-model-item">
                                            <img src="{{dynamicAsset('/public/assets/admin/new-img/icon-4.png')}}" alt="">
                                            <div class="shop-details-model-item-content">
                                                <h6 style="font-weight:600; color:#1b3056;">العنوان</h6>
                                                <div style="color:#4b5563;">{{$shop->address}}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Announcement card hidden from shop view page --}}
                </section>
                <!-- Banner -->

            </div>
        </div>
    </div>
@endsection


@push('script_2')
    <script>
        "use strict";
        $('.update-status').on('click', function (){
            let route = $(this).data('url');
            let code = $(this).data('code');
            updateStatus(route, code);
        })

        function updateStatus(route, code) {
            $.get({
                url: route,
                data: {
                    code: code,
                },
                success: function (data) {
                    if (data.error == 403) {
                        toastr.error('{{translate('status_can_not_be_updated')}}');
                        location.reload();
                    }
                    else{
                        toastr.success('{{translate('messages.Restaurant settings updated!')}}');
                    }
                }
            });
        }
    </script>
    <!-- Page level plugins -->
@endpush
