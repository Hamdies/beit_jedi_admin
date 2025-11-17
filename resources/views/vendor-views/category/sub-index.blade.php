@extends('layouts.vendor.app')

@section('title','قائمة التصنيفات الفرعية')

@push('css_or_js')
    <style>
        .subcategories-page-modern {
            background: #f5f7fa;
            padding: 1.75rem 0 1.5rem;
        }
        .subcategories-header-card {
            border-radius: 24px;
            background: #ffffff;
            padding: 1.5rem 1.75rem;
            box-shadow: 0 1px 3px rgba(15,23,42,0.04);
            border: 1px solid rgba(44,62,111,0.06);
            margin-bottom: 1.25rem;
        }
        .subcategories-header-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: .25rem;
            color: #1b3056;
        }
        .subcategories-header-subtitle {
            font-size: .9rem;
            color: #6b7280;
            margin-bottom: 0;
        }
        .subcategories-header-badge {
            border-radius: 999px;
            background: rgba(44,62,111,0.04);
            color: #1b3056;
            padding: .25rem .75rem;
            font-size: .8rem;
            display: inline-flex;
            align-items: center;
            gap: .25rem;
        }
        .subcategories-list-card {
            border-radius: 24px;
            border: 1px solid rgba(44,62,111,0.06);
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.03);
        }
        .subcategories-search-wrapper .input-group .form-control {
            border-radius: 999px 0 0 999px;
        }
        .subcategories-search-wrapper .btn.btn--secondary {
            border-radius: 0 999px 999px 0;
        }
        .subcategories-search-input {
            direction: rtl;
            text-align: right;
        }
        .subcategories-table thead th {
            font-size: .8rem;
            font-weight: 600;
            color: #6b7280;
            border-bottom: 1px solid rgba(15,23,42,0.06);
        }
        .subcategories-table tbody tr:hover {
            background-color: #f9fafb;
        }
        .subcategories-empty {
            padding: 3rem 1rem 2.5rem;
        }
        .subcategories-empty h5 {
            margin-top: 1rem;
            font-weight: 500;
            color: #6b7280;
        }
        @media (max-width: 768px) {
            .subcategories-header-card {
                padding: 1.25rem 1.5rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid subcategories-page-modern">
        <div class="subcategories-header-card d-flex flex-wrap justify-content-between align-items-center">
            <div class="d-flex align-items-center mb-2 mb-md-0">
                <div class="card-header-icon d-inline-flex mr-2 img">
                    <img src="{{dynamicAsset('/public/assets/admin/img/resturant-panel/page-title/category.png')}}" alt="public">
                </div>
                <div>
                    <h2 class="subcategories-header-title mb-1">قائمة التصنيفات الفرعية</h2>
                    <p class="subcategories-header-subtitle mb-0">إدارة التصنيفات الفرعية وربطها بالتصنيفات الرئيسية بسهولة.</p>
                </div>
            </div>
            <div class="subcategories-header-badge mt-2 mt-md-0">
                <i class="tio-category ml-1"></i>
                <span>إجمالي التصنيفات الفرعية:</span>
                <span id="itemCount">{{$categories->total()}}</span>
            </div>
        </div>
        <div class="card border-0 subcategories-list-card">
            <div class="card-header border-0 py-2">
                <div class="search--button-wrapper subcategories-search-wrapper justify-content-end">
                    <form  class="search-form ml-auto">

                        <!-- Search -->
                        <div class="input-group input--group">
                            <input id="datatableSearch" value="{{ request()?->search ?? null }}"  name="search" type="search" class="form-control subcategories-search-input" placeholder="مثال: بحث باسم التصنيف الفرعي" aria-label="بحث في التصنيفات الفرعية">
                            <button class="btn btn--secondary" type="submit">
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
                        class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table subcategories-table"
                        data-hs-datatables-options='{
                            "search": "#datatableSearch",
                            "entries": "#datatableEntries",
                            "isResponsive": false,
                            "isShowPaging": false,
                            "paging":false,
                        }'>
                        <thead class="thead-light">
                            <tr>
                                <th class="w-100px text-center">م</th>
                                <th class="w-30p text-center">رقم التصنيف الفرعي</th>
                                <th class="w-30p">التصنيف الرئيسي</th>
                                <th class="w-30p">اسم التصنيف الفرعي</th>
                            </tr>
                        </thead>

                        <tbody id="set-rows">
                        @foreach($categories as $key=>$category)
                            <tr>
                                <td class="text-center">{{$key+$categories->firstItem()}}</td>
                                <td class="text-center">{{$category->id}}</td>
                                <td>
                                    <span class="d-block font-size-sm text-body">
                                        {{Str::limit($category->parent['name'],20,'...')}}
                                    </span>
                                </td>
                                <td>
                                    <span class="d-block font-size-sm text-body">
                                        {{Str::limit($category->name,20,'...')}}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @if(count($categories) === 0)
                    <div class="empty--data subcategories-empty text-center">
                        <img src="{{dynamicAsset('/public/assets/admin/img/empty.png')}}" alt="public">
                        <h5>
                            لا توجد تصنيفات فرعية حتى الآن
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
            let datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'), {
                select: {
                    style: 'multi',
                    classMap: {
                        checkAll: '#datatableCheckAll',
                        counter: '#datatableCounter',
                        counterInfo: '#datatableCounterInfo'
                    }
                },
                language: {
                    zeroRecords: '<div class="text-center p-4">' +
                    '<img class="mb-3 w-7rem" src="{{dynamicAsset('public/assets/admin/svg/illustrations/sorry.svg')}}" alt="Image Description">' +
                    '<p class="mb-0">{{ translate('No_data_to_show') }}</p>' +
                    '</div>'
                }
            });
            $('#datatableSearch').on('keyup', function () {
                datatable
                    .columns(1)
                    .search(this.value)
                    .draw();
            });

            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                let select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });

    </script>
@endpush
