@extends('layouts.vendor.app')

@section('title','تعديل الطبق')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{dynamicAsset('public/assets/admin/css/tags-input.min.css')}}" rel="stylesheet">
    <style>
        .food-edit-page-modern {
            background: #f5f7fa;
            padding: 1.75rem 0 1.5rem;
        }
        .food-edit-header-card {
            border-radius: 24px;
            background: #ffffff;
            padding: 1.5rem 1.75rem;
            box-shadow: 0 1px 3px rgba(15,23,42,0.04);
            border: 1px solid rgba(44,62,111,0.06);
            margin-bottom: 1.25rem;
        }
        .food-edit-header-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: .25rem;
            color: #1b3056;
        }
        .food-edit-header-subtitle {
            font-size: .9rem;
            color: #6b7280;
            margin-bottom: 0;
        }
    </style>
@endpush

@section('content')
@php
    // category_ids can be stored as [{id:5, position:0}, ...] OR as a flat array of ints [5,12,...]
    $catId = function ($i) use ($product_category) {
        if (!isset($product_category[$i])) return null;
        $v = $product_category[$i];
        return is_object($v) ? ($v->id ?? null) : $v;
    };
@endphp

    <div class="content container-fluid food-edit-page-modern">
        <!-- Page Header -->
        <div class="food-edit-header-card d-flex align-items-center justify-content-between mb-3">
            <div class="d-flex align-items-center">
                <span class="card-header-icon d-inline-flex mr-2 img">
                    <i class="tio-edit"></i>
                </span>
                <div>
                    <h1 class="food-edit-header-title mb-1">تعديل الطبق</h1>
                    <p class="food-edit-header-subtitle mb-0">قم بتحديث بيانات الطبق، السعر، التصنيف والمخزون بسهولة.</p>
                </div>
            </div>
        </div>


        <form action="javascript:" method="post" id="product_form" enctype="multipart/form-data">
            @csrf
            <div class="row g-2">
                <div class="col-lg-6">
                    <div class="card shadow--card-2 border-0">
                        <div class="card-body pb-0">
                            @php($language = \App\Models\BusinessSetting::where('key', 'language')->first())
                            @php($language = $language->value ?? null)
                            @php($default_lang = str_replace('_', '-', app()->getLocale()))
                                @if ($language)
                                <div class="js-nav-scroller hs-nav-scroller-horizontal">
                                    <ul class="nav nav-tabs mb-4">
                                        <li class="nav-item">
                                            <a class="nav-link lang_link active"
                                                href="#"
                                                id="default-link">{{ translate('Default') }}</a>
                                        </li>
                                        @foreach (json_decode($language) as $lang)
                                            <li class="nav-item">
                                                <a class="nav-link lang_link "
                                                    href="#"
                                                    id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                        </div>

                            <div class="card-body">
                                <div class="lang_form"  id="default-form">
                                    <div class="form-group">
                                        <label class="input-label"
                                            for="default_name">اسم الطبق (اللغة الافتراضية)</label>
                                        <input type="text" name="name[]" id="default_name"
                                            class="form-control" value="{{$product?->getRawOriginal('name')}}" required
                                            placeholder="اكتب اسم الطبق هنا"
                                            {{ $lang == $default_lang ? 'required' : '' }}
                                             >
                                    </div>
                                    <input type="hidden" name="lang[]" value="default">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">وصف قصير للطبق (افتراضي)</label>
                                        <textarea type="text" name="description[]" class="form-control ckeditor min-height-154px">{!! $product?->getRawOriginal('description') ?? '' !!}</textarea>
                                    </div>
                            </div>
                                @if ($language)
                                    @foreach (json_decode($language) as $lang)

                                        <?php
                                        if (count($product['translations'])) {
                                            $translate = [];
                                            foreach ($product['translations'] as $t) {
                                                if ($t->locale == $lang && $t->key == 'name') {
                                                    $translate[$lang]['name'] = $t->value;
                                                }
                                                if ($t->locale == $lang && $t->key == 'description') {
                                                    $translate[$lang]['description'] = $t->value;
                                                }
                                            }
                                        }
                                        ?>
                                        <div class="d-none lang_form" id="{{ $lang }}-form">
                                                <div class="form-group">
                                                    <label class="input-label"
                                                        for="{{ $lang }}_name">اسم الطبق ({{ strtoupper($lang) }})</label>
                                                    <input type="text" name="name[]" id="{{ $lang }}_name"
                                                        class="form-control"  value="{{ $translate[$lang]['name'] ?? '' }}"
                                                        placeholder="اكتب اسم الطبق هنا"
                                                         >
                                                </div>
                                                <input type="hidden" name="lang[]" value="{{ $lang }}">
                                                <div class="form-group mb-0">
                                                    <label class="input-label"
                                                        for="exampleFormControlInput1">وصف قصير للطبق ({{ strtoupper($lang) }})</label>
                                                    <textarea type="text" name="description[]" class="form-control ckeditor min-height-154px">{!! $translate[$lang]['description'] ?? '' !!}</textarea>
                                                </div>
                                            </div>
                                    @endforeach
                                @endif
                            </div>

                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow--card-2 border-0 h-100">
                        <div class="card-body">
                            <h5 class="card-title">
                                <span>صورة الطبق <small
                                        class="text-danger">(نسبة 200×200)</small></span>
                            </h5>
                            <div class="form-group mb-0 h-100 d-flex flex-column align-items-center justify-content-center">
                                <label>
                                    <div id="image-viewer-section" class="my-auto text-center">
                                            <img class="initial-52 object--cover border--dashed" id="viewer"
                                            src="{{ $product?->image_full_url ?? dynamicAsset('/public/assets/admin/img/100x100/food-default-image.png') }}"
                                            alt="image">
                                        <input type="file" name="image" id="customFileEg1" class="d-none" accept="image/*">
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card shadow--card-2 border-0">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon mr-2">
                                    <i class="tio-dashboard-outlined"></i>
                                </span>
                                <span>معلومات المطعم والتصنيف</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">

                                <div class="col-sm-6 col-lg-4">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                            for="exampleFormControlSelect1">التصنيف الرئيسي<span
                                                class="input-label-secondary">*</span></label>
                                                <select name="category_id" id="category-id" class="form-control h--45px js-select2-custom get-request"
                                                data-url="{{url('/')}}/restaurant-panel/food/get-categories?parent_id=" data-id="sub-categories">
                                            @foreach($categories as $category)
                                                <option
                                                    value="{{$category['id']}}" {{ $category->id==$catId(0) ? 'selected' : ''}} >{{$category['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-4">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                            for="exampleFormControlSelect1">التصنيف الفرعي<span
                                                class="input-label-secondary" data-toggle="tooltip"
                                                data-placement="right"
                                                data-original-title="{{ translate('messages.category_required_warning') }}"><img
                                                    src="{{ dynamicAsset('/public/assets/admin/img/info-circle.svg') }}"
                                                    alt="{{ translate('messages.category_required_warning') }}"></span></label>
                                                    <select name="sub_category_id" id="sub-categories"
                                                    data-id="{{ $catId(1) ?? '' }}"
                                                    class="form-control h--45px js-select2-custom">

                                            </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-4">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">نوع الطبق</label>
                                            <select required name="veg" class="form-control js-select2-custom">
                                                <option value="0" {{ $product['veg'] == 0 ? 'selected' : '' }}>
                                                    غير نباتي
                                                </option>
                                                <option value="1" {{ $product['veg'] == 1 ? 'selected' : '' }}>
                                                    نباتي
                                                </option>
                                            </select>
                                    </div>
                                </div>
                                @php($product_nutritions = $product->nutritions->pluck('id'))
                                @php($product_allergies = $product->allergies->pluck('id'))

                                <div class="col-sm-6" id="nutrition">
                                    <label class="input-label" for="sub-categories">
                                        معلومات غذائية
                                        <span class="input-label-secondary" title="{{ translate('Specify the necessary keywords relating to energy values for the item and type this content & press enter.') }}" data-toggle="tooltip">
                                                <i class="tio-info-outined"></i>
                                            </span>
                                    </label>
                                    <select name="nutritions[]" data-placeholder="اكتب المحتوى واضغط إدخال" class="form-control multiple-select2" multiple>
                                        @php($nutritions=   \App\Models\Nutrition::select(['id','nutrition'])->get() ?? [])

                                        @foreach ($nutritions as $nutrition)
                                            <option value="{{ $nutrition->nutrition }}" {{ $product_nutritions->contains($nutrition->id) ? 'selected' : '' }}>{{ $nutrition->nutrition }}</option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="col-sm-6" id="allergy">
                                    <label class="input-label" for="sub-categories">
                                        مكونات مُسبِّبة للحساسية
                                        <span class="input-label-secondary" title="{{ translate('Specify the ingredients of the item which can make a reaction as an allergen and type this content & press enter.') }}" data-toggle="tooltip">
                                                <i class="tio-info-outined"></i>
                                            </span>
                                    </label>
                                    <select name="allergies[]" data-placeholder="اكتب المحتوى واضغط إدخال" class="form-control multiple-select2" multiple>
                                        @php($allergies=  \App\Models\Allergy::select(['id','allergy'])->get() ?? [])

                                        @foreach ( $allergies as $allergy)
                                            <option value="{{ $allergy->allergy }}" {{ $product_allergies->contains($allergy->id) ? 'selected' : '' }}>{{ $allergy->allergy }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-6 col-lg-3" id="halal">
                                    <div class="form-check mb-0 p-4">
                                        <input class="form-check-input" name="is_halal" type="checkbox" value="1" id="flexCheckDefault" {{ $product->is_halal == 1?'checked':'' }}>
                                        <label class="form-check-label" for="flexCheckDefault">
                                            هل المنتج حلال؟
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow--card-2 border-0">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon mr-2">
                                    <i class="tio-dashboard-outlined"></i>
                                </span>
                                <span>الإضافات</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <label class="input-label"
                                for="exampleFormControlSelect1">اختر الإضافات<span
                                    class="input-label-secondary" data-toggle="tooltip"
                                    data-placement="right"
                                    data-original-title="{{ translate('messages.restaurant_required_warning') }}"><img
                                        src="{{ dynamicAsset('/public/assets/admin/img/info-circle.svg') }}"
                                        alt="{{ translate('messages.restaurant_required_warning') }}"></span></label>
                                    <select name="addon_ids[]" class="form-control h--45px js-select2-custom" multiple="multiple">
                                        @foreach(\App\Models\AddOn::where('restaurant_id', \App\CentralLogics\Helpers::get_restaurant_id())->orderBy('name')->get() as $addon)
                                            <option
                                                value="{{$addon['id']}}" {{in_array($addon->id,json_decode($product['add_ons'],true))?'selected':''}}>{{$addon['name']}}</option>
                                        @endforeach
                                    </select>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow--card-2 border-0">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon mr-2"><i class="tio-date-range"></i></span>
                                <span>التوفر الزمني</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">وقت البدء المتاح</label>
                                        <input type="time" name="available_time_starts" class="form-control"
                                            id="available_time_starts"  value="{{ $product['available_time_starts'] }}"
                                            placeholder="مثال: 10:30 ص" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">وقت الانتهاء المتاح</label>
                                        <input type="time" name="available_time_ends" class="form-control" value="{{ $product['available_time_ends'] }}"
                                            id="available_time_ends" placeholder="5:45 م" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card shadow--card-2 border-0">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon mr-2"><i class="tio-dollar-outlined"></i></span>
                                <span>معلومات السعر والخصم</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">السعر</label>
                                        <input type="number" min="0" max="999999999999.99"
                                            step="0.01" value="{{ $product['price'] }}" name="price" class="form-control"
                                            placeholder="مثال: 100" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">نوع الخصم</label>
                                        <select name="discount_type" class="form-control js-select2-custom">
                                            <option value="percent"
                                                {{ $product['discount_type'] == 'percent' ? 'selected' : '' }}>
                                                نسبة مئوية (%)
                                            </option>
                                            <option value="amount" {{ $product['discount_type'] == 'amount' ? 'selected' : '' }}>
                                                مبلغ ثابت ({{ \App\CentralLogics\Helpers::currency_symbol() }} أو العملة)
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">قيمة الخصم
                                            <span class="input-label-secondary text--title" data-toggle="tooltip"
                                            data-placement="right"
                                            data-original-title="{{ translate('Currently you need to manage discount with the Restaurant.') }}">
                                            <i class="tio-info-outined"></i>
                                        </span>
                                        </label>
                                        <input type="number" min="0" max="9999999999999999999999"
                                            value="{{ $product['discount'] }}"  name="discount" class="form-control"
                                            placeholder="مثال: 10">
                                    </div>
                                </div>
                                <div class="col-md-3" id="maximum_cart_quantity">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                            for="maximum_cart_quantity">الحد الأقصى لكمية الشراء في الطلب الواحد
                                            <span
                                            class="input-label-secondary text--title" data-toggle="tooltip"
                                            data-placement="right"
                                            data-original-title="{{ translate('If_this_limit_is_exceeded,_customers_can_not_buy_the_food_in_a_single_purchase.') }}">
                                            <i class="tio-info-outined"></i>
                                        </span>
                                        </label>
                                        <input type="number"  placeholder="مثال: 10" class="form-control" name="maximum_cart_quantity" min="0" value="{{ $product->maximum_cart_quantity }}" id="cart_quantity">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">نوع المخزون</label>
                                        <select name="stock_type" id="stock_type" class="form-control js-select2-custom">
                                            <option  {{ $product->stock_type == 'unlimited' ? 'selected' : '' }}  value="unlimited">مخزون غير محدود</option>
                                            <option  {{ $product->stock_type == 'limited' ? 'selected' : '' }} value="limited">مخزون محدد</option>
                                            <option  {{ $product->stock_type == 'daily' ? 'selected' : '' }} value="daily">مخزون يومي</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3 hide_this" id="">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                            for="item_stock">المخزون الأساسي للطبق
                                            <span class="input-label-secondary text--title" data-toggle="tooltip"
                                            data-placement="right"
                                            data-original-title="{{ translate('This_Stock_amount_will_be_counted_as_the_base_stock._But_if_you_want_to_manage_variation_wise_stock,_then_need__to_manage_it_below_with_food_variation_setup.') }}">
                                            <i class="tio-info-outined"></i>
                                        </span>
                                        </label>
                                        <input type="number" value="{{ $product->item_stock }}"  placeholder="مثال: 10" class="form-control stock_disable" name="item_stock" min="0" max="99999999" id="item_stock">
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card shadow--card-2 border-0">
                        <div class="card-header flex-wrap">
                            <h5 class="card-title">
                                <span class="card-header-icon mr-2">
                                    <i class="tio-canvas-text"></i>
                                </span>
                                <span>الخيارات والاختلافات</span>
                            </h5>
                            <a class="btn text--primary-2" id="add_new_option_button">
                                إضافة خيار جديد
                                <i class="tio-add"></i>
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-md-12">
                                    <div id="add_new_option">
                                        @if (isset($product->variations))
                                        @foreach (json_decode($product->variations,true) as $key_choice_options=>$item)
                                            @if (isset($item["price"]))
                                                @break
                                            @else
                                            @include('vendor-views.product.partials._new_variations',['item'=>$item,'key'=>$key_choice_options+1])
                                            @endif
                                        @endforeach
                                    @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card shadow--card-2 border-0">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon mr-2"><i class="tio-label"></i></span>
                                <span>الوسوم (Tags)</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <input type="text" class="form-control" name="tags"  value="@foreach($product->tags as $c) {{$c->tag.','}} @endforeach" placeholder="أدخل الوسوم واضغط إنتر" data-role="tagsinput">
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="btn--container justify-content-end">
                        <button type="reset" id="reset_btn"
                            class="btn btn--reset">إعادة تعيين</button>
                        <button type="submit"
                            class="btn btn--primary">حفظ التعديلات</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection

@push('script')

@endpush

@push('script_2')
    <script src="{{dynamicAsset('public/assets/admin')}}/js/tags-input.min.js"></script>
    <script src="{{dynamicAsset('public/assets/admin')}}/js/view-pages/vendor/product-index.js"></script>
    <script>
        "use strict";
        count= {{isset($product->variations)?count(json_decode($product->variations,true)):0}};


        $('#stock_type').on('change', function () {
                if($(this).val() == 'unlimited') {
                    $('.stock_disable').prop('readonly', true).prop('required', false).attr('placeholder', '{{ translate('Unlimited') }}').val('');
                    $('.hide_this').addClass('d-none');
                } else {
                    $('.stock_disable').prop('readonly', false).prop('required', true).attr('placeholder', '{{ translate('messages.Ex:_100') }}');
                    $('.hide_this').removeClass('d-none');
                }
        });

        updatestockCount();

            function updatestockCount(){
                if($('#stock_type').val()==  'unlimited'){
                        $('.stock_disable').prop('readonly', true).prop('required', false).attr('placeholder', '{{ translate('Unlimited') }}').val('');
                        $('.hide_this').addClass('d-none');
                    } else{
                        $('.stock_disable').prop('readonly', false).prop('required', true).attr('placeholder', '{{ translate('messages.Ex:_100') }}');
                        $('.hide_this').removeClass('d-none');
                    }
            }

            $(document).ready(function(){
            $("#add_new_option_button").click(function(e) {
                $('#empty-variation').hide();
                count++;
                let add_option_view = `
                    <div class="__bg-F8F9FC-card view_new_option mb-2">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <label class="form-check form--check">
                                    <input id="options[` + count + `][required]" name="options[` + count + `][required]" class="form-check-input" type="checkbox">
                                    <span class="form-check-label">{{ translate('Required') }}</span>
                                </label>
                                <div>
                                    <button type="button" class="btn btn-danger btn-sm delete_input_button"
                                        title="حذف">
                                        <i class="tio-add-to-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-xl-4 col-lg-6">
                                    <label for="">{{ translate('name') }}  &nbsp; <span class="form-label-secondary text-danger"
                                data-toggle="tooltip" data-placement="right"
                                data-original-title="{{ translate('messages.Required.')}}"> *
                                </span></label>
                                    <input required name=options[` + count +
                    `][name] class="form-control new_option_name" type="text" data-count="`+
                    count +`">
                                </div>

                                <div class="col-xl-4 col-lg-6">
                                    <div>
                                        <label class="input-label text-capitalize d-flex align-items-center"><span class="line--limit-1">{{ translate('messages.selcetion_type') }} </span>
                                        </label>
                                        <div class="resturant-type-group px-0">
                                            <label class="form-check form--check mr-2 mr-md-4">
                                                <input class="form-check-input show_min_max" data-count="`+count+`" type="radio" value="multi"
                                                name="options[` + count + `][type]" id="type` + count +
                    `" checked
                                                >
                                                <span class="form-check-label">
                                                    {{ translate('Multiple Selection') }}
                    </span>
                </label>

                <label class="form-check form--check mr-2 mr-md-4">
                    <input class="form-check-input hide_min_max" data-count="`+count+`" type="radio" value="single"
                                                name="options[` + count + `][type]" id="type` + count +
                    `"
                                                >
                                                <span class="form-check-label">
                                                    {{ translate('Single Selection') }}
                    </span>
                </label>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-6">
        <div class="row g-2">
            <div class="col-6">
                <label for="">{{ translate('Min') }}</label>
                                            <input id="min_max1_` + count + `" required  name="options[` + count + `][min]" class="form-control" type="number" min="1">
                                        </div>
                                        <div class="col-6">
                                            <label for="">{{ translate('Max') }}</label>
                                            <input id="min_max2_` + count + `"   required name="options[` + count + `][max]" class="form-control" type="number" min="1">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="option_price_` + count + `" >
                                <div class="bg-white border rounded p-3 pb-0 mt-3">
                                    <div  id="option_price_view_` + count + `">
                                        <div class="row g-3 add_new_view_row_class mb-3">
                                            <div class="col-md-3 col-sm-6">
                                                <label for="">{{ translate('Option_name') }}  &nbsp; <span class="form-label-secondary text-danger"
                                data-toggle="tooltip" data-placement="right"
                                data-original-title="{{ translate('messages.Required.')}}"> *
                                </span></label>
                                                <input class="form-control" required type="text" name="options[` +
                    count +
                    `][values][0][label]" id="">
                                            </div>
                                            <div class="col-md-3 col-sm-6">
                                                <label for="">{{ translate('Additional_price') }}  &nbsp; <span class="form-label-secondary text-danger"
                                data-toggle="tooltip" data-placement="right"
                                data-original-title="{{ translate('messages.Required.')}}"> *
                                </span></label>
                                                <input class="form-control" required type="number" min="0" step="0.01" name="options[` +
                    count + `][values][0][optionPrice]" id="">
                                            </div>
                                            <div class="col-md-3 col-sm-6 hide_this">
                                                <label for="">{{ translate('Stock') }} </label>
                                                <input class="form-control stock_disable count_stock" required type="number" max="99999999" min="0"  name="options[` +
                    count + `][values][0][total_stock]" id="">
                                            </div>
                                        </div>
                                    </div>



                                    <input type="hidden" hidden name="options[` + count + `][values][0][option_id]" value="null" >



                                    <div class="row mt-3 p-3 mr-1 d-flex "  id="add_new_button_` + count +`">
                                        <button type="button" class="btn btn--primary btn-outline-primary add_new_row_button" data-count="`+
                    count +`" >{{ translate('Add_New_Option') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;

                $("#add_new_option").append(add_option_view);
                updatestockCount();
            });

        });

        function add_new_row_button(data) {
          var  countRow = 1 + $('#option_price_view_' + data).children('.add_new_view_row_class').length;
            let add_new_row_view = `
            <div class="row add_new_view_row_class mb-3 position-relative pt-3 pt-sm-0">
                <div class="col-md-3 col-sm-5">
                        <label for="">{{ translate('Option_name') }}  &nbsp;<span class="form-label-secondary text-danger"
                                data-toggle="tooltip" data-placement="right"
                                data-original-title="{{ translate('messages.Required.')}}"> *
                                </span></label>
                        <input class="form-control" required type="text" name="options[` + data + `][values][` +
                countRow + `][label]" id="">
                    </div>
                    <div class="col-md-3 col-sm-5">
                        <label for="">{{ translate('Additional_price') }}  &nbsp;<span class="form-label-secondary text-danger"
                                data-toggle="tooltip" data-placement="right"
                                data-original-title="{{ translate('messages.Required.')}}"> *
                                </span></label>
                        <input class="form-control"  required type="number" min="0" step="0.01" name="options[` +
                        data +
                `][values][` + countRow + `][optionPrice]" id="">
                    </div>
                    <div class="col-md-3 col-sm-5 hide_this">
                        <label for="">{{ translate('Stock') }}  </label>
                        <input class="form-control stock_disable count_stock"  required type="number" min="0" max="99999999"  name="options[` +
                        data +
                `][values][` + countRow + `][total_stock]" id="">
                    </div>

                    <input type="hidden" hidden name="options[` +
                        data +
                `][values][` + countRow + `][option_id]" value="null" >

                    <div class="col-sm-2 max-sm-absolute">
                        <label class="d-none d-sm-block">&nbsp;</label>
                        <div class="mt-1">
                            <button type="button" class="btn btn-danger btn-sm deleteRow"
                                title="حذف">
                                <i class="tio-add-to-trash"></i>
                            </button>
                        </div>
                </div>
            </div>`;
            $('#option_price_view_' + data).append(add_new_row_view);
            updatestockCount();

        }


        $(document).ready(function () {
            setTimeout(function () {
                let category = $("#category-id").val();
                let sub_category = '{{ $catId(1) ?? '' }}';
                let sub_sub_category ='{{ $catId(2) ?? '' }}';
                getRequest('{{url('/')}}/restaurant-panel/food/get-categories?parent_id=' + category + '&&sub_category=' + sub_category, 'sub-categories');
                getRequest('{{url('/')}}/restaurant-panel/food/get-categories?parent_id=' + sub_category + '&&sub_category=' + sub_sub_category, 'sub-sub-categories');
            }, 1000)
        });

        $('#product_form').on('submit', function () {
            let formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('vendor.food.update',[$product['id']])}}',
                data: $('#product_form').serialize(),
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#loading').hide();
                    if (data.errors) {
                        for (let i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else {
                        toastr.success('{{translate('messages.product_updated_successfully')}}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function () {
                            location.href = '{{route('vendor.food.list')}}';
                        }, 2000);
                    }
                }
            });
        });
        $('#reset_btn').click(function(){
            location.reload(true);
        })

        let removedVariationIDs = [];
        let removedVariationOptionIDs = [];

        $(document).on('click', '.remove_variation', function () {
            removedVariationIDs.push($(this).data('id'));
            $('#removedVariationIDs').val(removedVariationIDs.join(','));
        });
        $(document).on('click', '.remove_variation_option', function () {
            removedVariationOptionIDs.push($(this).data('id'));
            $('#removedVariationOptionIDs').val(removedVariationOptionIDs.join(','));
        });
    </script>
@endpush


