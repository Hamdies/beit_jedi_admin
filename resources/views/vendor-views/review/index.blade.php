@extends('layouts.vendor.app')

@section('title','تقييمات العملاء')

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid" style="background:#f5f7fa; padding:1.75rem 0 1.5rem;">
    <!-- Page Header -->
     <div class="d-flex flex-wrap justify-content-between align-items-center mb-3" style="border-radius:24px; background:#ffffff; padding:1.5rem 1.75rem; box-shadow:0 1px 3px rgba(15,23,42,0.04); border:1px solid rgba(44,62,111,0.06);">
            <div class="d-flex align-items-center">
                <div class="card-header-icon d-inline-flex mr-2 img">
                    <img src="{{dynamicAsset('/public/assets/admin/img/resturant-panel/page-title/review.png')}}" alt="public">
                </div>
                <div>
                    <h2 class="mb-1" style="font-size:1.3rem; font-weight:700; color:#1b3056;">تقييمات العملاء</h2>
                    <p class="mb-0" style="font-size:.9rem; color:#6b7280;">استعرض تقييمات العملاء لأطباقك وردّ عليهم من لوحة التحكم.</p>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <!-- Card -->
        <div class="card" style="border-radius:24px; border:1px solid rgba(44,62,111,0.06); box-shadow:0 10px 25px rgba(15,23,42,0.03);">
            <!-- Header -->
            <div class="card-header py-2 border-0">
                <div class="search--button-wrapper justify-content-end">
                    <form action="javascript:" id="search-form" class="my-2 vendor--search">
                        <div class="input--group input-group">
                            <input type="search" name="search" id="column1_search" class="form-control" placeholder="مثال: بحث باسم الطبق أو رقم الهاتف" required>
                            <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- End Header -->
@php($restaurant_review_reply = App\Models\BusinessSetting::where('key' , 'restaurant_review_reply')->first()->value ?? 0)
            <!-- Table -->
            <div class="table-responsive datatable-custom">
                <table id="columnSearchDatatable"
                        class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                        data-hs-datatables-options='{
                            "order": [],
                            "orderCellsTop": true,
                            "paging": false
                        }'>
                    <thead class="thead-light">
                    <tr>
                        <th>م</th>
                        <th>الطبق</th>
                        <th>العميل</th>
                        <th>التقييم</th>
                        <th>التاريخ</th>
                        @if($restaurant_review_reply == '1')
                        <th class="text-center">إجراء</th>
                        @endif
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($reviews as $key=>$review)
                        <tr>
                            <td>{{$key+$reviews->firstItem()}}</td>
                            <td>
                                @if ($review->food)
                                <div class="position-relative media align-items-center">
                                    <a class=" text-hover-primary absolute--link" href="{{route('vendor.food.view',[$review->food['id']])}}">
                                    </a>
                                    <img class="avatar avatar-lg mr-3  onerror-image"  data-onerror-image="{{dynamicAsset('public/assets/admin/img/100x100/food-default-image.png')}}"
                                         src="{{ $review->food['image_full_url'] ?? dynamicAsset('public/assets/admin/img/100x100/food-default-image.png') }}" alt="{{$review->food->name}} image">
                                    <div class="media-body">
                                        <h5 class="text-hover-primary important--link mb-0">{{Str::limit($review->food['name'],10)}}</h5>
                                        <a href="{{route('vendor.order.details',['id'=>$review->order_id])}}"  class="fz--12 text-body important--link">رقم الطلب #{{$review->order_id}}</a>
                                    </div>
                                </div>
                                @else
                                    {{translate('messages.Food_deleted!')}}
                                @endif
                            </td>
                            <td>
                                @if($review->customer)
                                <div>
                                    <h5 class="d-block text-hover-primary mb-1">{{Str::limit($review->customer['f_name']." ".$review->customer['l_name'])}} <i
                                            class="tio-verified text-primary" data-toggle="tooltip" data-placement="top"
                                            title="عميل موثّق"></i></h5>
                                    <span class="d-block font-size-sm text-body">{{Str::limit($review->customer->phone)}}</span>
                                </div>
                                @else
                                {{translate('messages.customer_not_found')}}
                                @endif
                            </td>
                            <td>
                                <div class="text-wrap w-18rem">
                                    <label class="rating">
                                        <i class="tio-star"></i>
                                        <span>{{$review->rating}}</span>
                                    </label>
                                    <p data-toggle="tooltip" data-placement="bottom"
                                    data-original-title="{{ $review?->comment }}" >
                                        {{Str::limit($review['comment'], 80)}}
                                    </p>
                                </div>
                            </td>
                            <td>
                                <span class="d-block">
                                    {{ \App\CentralLogics\Helpers::date_format($review->created_at)  }}
                                </span>
                                <span class="d-block"> {{ \App\CentralLogics\Helpers::time_format($review->created_at)  }}</span>
                            </td>
                            @if($restaurant_review_reply == '1')
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a  class="btn btn-sm btn--primary {{ $review->reply ? 'btn-outline-primary' : ''}}" data-toggle="modal" data-target="#reply-{{$review->id}}" title="عرض التفاصيل">
                                        {{ $review->reply ? 'عرض الرد' : 'إضافة رد'}}
                                    </a>
                                </div>
                            </td>
                            @endif
                            <div class="modal fade" id="reply-{{$review->id}}">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header pb-4">
                                            <button type="button" class="payment-modal-close btn-close border-0 outline-0 bg-transparent" data-dismiss="modal">
                                                <i class="tio-clear"></i>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="position-relative media align-items-center">
                                                <a class="absolute--link" href="{{route('vendor.food.view',[$review->food['id']])}}">
                                                </a>
                                                <img class="avatar avatar-lg mr-3  onerror-image"  data-onerror-image="{{dynamicAsset('public/assets/admin/img/100x100/food-default-image.png')}}"
                                                     src="{{ $review->food['image_full_url'] ?? dynamicAsset('public/assets/admin/img/100x100/food-default-image.png') }}" alt="{{$review->food->name}} image">
                                                <div>
                                                    <h5 class="text-hover-primary mb-0">{{ $review->food['name'] }}</h5>
                                                    @if ($review->food['avg_rating'] == 5)
                                                        <div class="rating">
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star"></i></span>
                                                        </div>
                                                    @elseif ($review->food['avg_rating'] < 5 && $review->food['avg_rating'] >= 4.5)
                                                        <div class="rating">
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star-half"></i></span>
                                                        </div>
                                                    @elseif ($review->food['avg_rating'] < 4.5 && $review->food['avg_rating'] >= 4)
                                                        <div class="rating">
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                        </div>
                                                    @elseif ($review->food['avg_rating'] < 4 && $review->food['avg_rating'] >= 3.5)
                                                        <div class="rating">
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star-half"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                        </div>
                                                    @elseif ($review->food['avg_rating'] < 3.5 && $review->food['avg_rating'] >= 3)
                                                        <div class="rating">
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                        </div>
                                                    @elseif ($review->food['avg_rating'] < 3 && $review->food['avg_rating'] >= 2.5)
                                                        <div class="rating">
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star-half"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                        </div>
                                                    @elseif ($review->food['avg_rating'] < 2.5 && $review->food['avg_rating'] > 2)
                                                        <div class="rating">
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                        </div>
                                                    @elseif ($review->food['avg_rating'] < 2 && $review->food['avg_rating'] >= 1.5)
                                                        <div class="rating">
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star-half"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                        </div>
                                                    @elseif ($review->food['avg_rating'] < 1.5 && $review->food['avg_rating'] > 1)
                                                        <div class="rating">
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                        </div>
                                                    @elseif ($review->food['avg_rating'] < 1 && $review->food['avg_rating'] > 0)
                                                        <div class="rating">
                                                            <span><i class="tio-star-half"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                        </div>
                                                    @elseif ($review->food['avg_rating'] == 1)
                                                        <div class="rating">
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                        </div>
                                                    @elseif ($review->food['avg_rating'] == 0)
                                                        <div class="rating">
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="mt-2">
                                                @if($review->customer)
                                                    <div>
                                                        <h5 class="d-block text-hover-primary mb-1">{{Str::limit($review->customer['f_name']." ".$review->customer['l_name'])}} <i
                                                                class="tio-verified text-primary" data-toggle="tooltip" data-placement="top"
                                                                title="Verified Customer"></i></h5>
                                                        <span class="d-block font-size-sm text-body">{{Str::limit($review->comment)}}</span>
                                                    </div>
                                                @else
                                                    {{translate('messages.customer_not_found')}}
                                                @endif
                                            </div>
                                            <div class="mt-2">
                                                <form action="{{route('vendor.review-reply',[$review['id']])}}" method="POST">
                                                    @csrf
                                                    <textarea id="reply" name="reply" required class="form-control" cols="30" rows="3" placeholder="اكتب ردّك هنا">{{ $review->reply ?? '' }}</textarea>
                                                    <div class="mt-3 btn--container justify-content-end">
                                                        <button class="btn btn-primary">{{ $review->reply ? 'تحديث الرد' : 'إرسال الرد'}}</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @if(count($reviews) === 0)
                <div class="empty--data">
                    <img src="{{dynamicAsset('/public/assets/admin/img/empty.png')}}" alt="public">
                    <h5>
                        {{translate('no_data_found')}}
                    </h5>
                </div>
                @endif
                <table>
                    <tfoot>
                    {!! $reviews->links() !!}
                    </tfoot>
                </table>
            </div>
            <!-- End Table -->
        </div>
        <!-- End Card -->
    </div>

@endsection

@push('script_2')
    <script>
        $(document).on('ready', function () {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            let datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

            $('#column1_search').on('keyup', function () {
                    datatable
                        .columns(1)
                        .search(this.value)
                        .draw();
                });
        });
    </script>
@endpush
