@extends('layouts.vendor.app')

@section('title','قائمة التصنيفات')

@push('css_or_js')
    <style>
        .categories-page-modern {
            background: #f5f7fa;
            padding: 1.75rem 0 1.5rem;
        }
        .categories-header-card {
            border-radius: 24px;
            background: #ffffff;
            padding: 1.5rem 1.75rem;
            box-shadow: 0 1px 3px rgba(15,23,42,0.04);
            border: 1px solid rgba(44,62,111,0.06);
            margin-bottom: 1.25rem;
        }
        .categories-header-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: .25rem;
            color: #1b3056;
        }
        .categories-header-subtitle {
            font-size: .9rem;
            color: #6b7280;
            margin-bottom: 0;
        }
        .categories-header-badge {
            border-radius: 999px;
            background: rgba(44,62,111,0.04);
            color: #1b3056;
            padding: .25rem .75rem;
            font-size: .8rem;
            display: inline-flex;
            align-items: center;
            gap: .25rem;
        }
        .categories-list-card {
            border-radius: 24px;
            border: 1px solid rgba(44,62,111,0.06);
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.03);
        }
        .categories-search-wrapper .input-group .form-control {
            border-radius: 999px 0 0 999px;
        }
        .categories-search-wrapper .btn.btn--secondary {
            border-radius: 0 999px 999px 0;
        }
        .categories-search-input {
            direction: rtl;
            text-align: right;
        }
        .categories-table thead th {
            font-size: .8rem;
            font-weight: 600;
            color: #6b7280;
            border-bottom: 1px solid rgba(15,23,42,0.06);
        }
        .categories-table tbody tr:hover {
            background-color: #f9fafb;
        }
        .categories-empty {
            padding: 3rem 1rem 2.5rem;
        }
        .categories-empty h5 {
            margin-top: 1rem;
            font-weight: 500;
            color: #6b7280;
        }
        @media (max-width: 768px) {
            .categories-header-card {
                padding: 1.25rem 1.5rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid categories-page-modern">
        <div class="categories-header-card d-flex flex-wrap justify-content-between align-items-center">
            <div class="d-flex align-items-center mb-2 mb-md-0">
                <div class="card-header-icon d-inline-flex mr-2 img">
                    <img src="{{dynamicAsset('/public/assets/admin/img/resturant-panel/page-title/category.png')}}" alt="public">
                </div>
                <div>
                    <h2 class="categories-header-title mb-1">قائمة التصنيفات</h2>
                    <p class="categories-header-subtitle mb-0">إدارة التصنيفات الرئيسية لأطباق مطعمك وتنظيم القائمة بسهولة.</p>
                </div>
            </div>
            <div class="categories-header-badge mt-2 mt-md-0">
                <i class="tio-category ml-1"></i>
                <span>إجمالي التصنيفات:</span>
                <span id="itemCount">{{$categories->total()}}</span>
            </div>
        </div>
        <div class="card border-0 categories-list-card">
            <div class="card-header border-0 py-2">
                <div class="search--button-wrapper categories-search-wrapper justify-content-end">
                    <form  class="search-form ml-auto">

                        <!-- Search -->
                        <div class="input-group input--group">
                            <input type="search" name="search" value="{{ request()?->search ?? null }}"  class="form-control categories-search-input" placeholder="مثال: بحث باسم التصنيف" aria-label="بحث في التصنيفات">
                            <button type="submit" class="btn btn--secondary">
                                <i class="tio-search"></i>
                            </button>
                        </div>
                        <!-- End Search -->
                    </form>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive datatable-custom">
                    <table id="columnSearchDatatable"
                        class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table categories-table"
                        data-hs-datatables-options='{
                            "search": "#datatableSearch",
                            "entries": "#datatableEntries",
                            "isResponsive": false,
                            "isShowPaging": false,
                            "paging":false,
                        }'>
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center w-5p">م</th>
                                <th class="text-center w-10p">الصورة</th>
                                <th class="text-center w-08p">رقم التصنيف</th>
                                <th class="text-center w-15p">اسم التصنيف</th>
                            </tr>
                        </thead>

                        <tbody id="table-div">

                        @foreach($categories as $key=>$category)

                        <tr>
                                <td class="text-center">{{$key+$categories->firstItem()}}</td>
                                <td class="text-center">
                                    <img class="avatar avatar-lg mr-3 onerror-image" src="{{ $category->image_full_url}}"
                                    data-onerror-image="{{dynamicAsset('public/assets/admin/img/100x100/food-default-image.png')}}" alt="image">
                                </td>
                                <td class="text-center">{{$category->id}}</td>
                                <td class="text-center">
                                <span class="d-block font-size-sm text-body">
                                    {{Str::limit($category['name'],20,'...')}}
                                </span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @if(count($categories) === 0)
                    <div class="empty--data categories-empty text-center">
                        <img src="{{dynamicAsset('/public/assets/admin/img/empty.png')}}" alt="public">
                        <h5>
                            لا توجد تصنيفات حتى الآن
                        </h5>
                    </div>
                    @endif
                </div>
            </div>
            <div class="card-footer page-area">
                <!-- Pagination -->
                <div class="row justify-content-center justify-content-sm-between align-items-sm-center">
                    <div class="col-sm-auto">
                        <div class="d-flex justify-content-center justify-content-sm-end">
                            <!-- Pagination -->
                            {!! $categories->links() !!}
                        </div>
                    </div>
                </div>
                <!-- End Pagination -->
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        "use strict";
        $(document).on('ready', function () {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            let datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));



            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                let select2 = $.HSCore.components.HSSelect2.init($(this));
            });



        });

        $("#customFileEg1").change(function () {
            readURL(this);
        });

    </script>
@endpush
