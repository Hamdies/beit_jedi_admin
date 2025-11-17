@extends('layouts.vendor.app')

@section('title', 'محادثات العملاء')

@section('content')

    <div class="content container-fluid" style="background:#f5f7fa; padding:1.75rem 0 1.5rem;">
        <!-- Page Header -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3" style="border-radius:24px; background:#ffffff; padding:1.5rem 1.75rem; box-shadow:0 1px 3px rgba(15,23,42,0.04); border:1px solid rgba(44,62,111,0.06);">
            <div class="d-flex align-items-center">
                <div class="card-header-icon d-inline-flex mr-2 img">
                    <i class="tio-chat"></i>
                </div>
                <div>
                    <h1 class="mb-1" style="font-size:1.5rem; font-weight:700; color:#1b3056;">محادثات العملاء</h1>
                    <p class="mb-0" style="font-size:1rem; color:#6b7280;">تواصل مع عملائك مباشرةً من لوحة التحكم، وتابع آخر الرسائل بسهولة.</p>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <div class="row g-3">
            <div class="col-lg-4 col-md-6">
                <!-- Card -->
                <div class="card" style="border-radius:24px; border:1px solid rgba(44,62,111,0.06); box-shadow:0 10px 25px rgba(15,23,42,0.03);">
                    <div class="card-header border-0">
                        <div class="input-group input---group">
                            <div class="input-group-prepend border-inline-end-0">
                                <span class="input-group-text border-inline-end-0" id="basic-addon1"><i class="tio-search"></i></span>
                            </div>
                            <input type="text" class="form-control border-inline-start-0 pl-1" id="serach" placeholder="ابحث باسم العميل أو رقم الهاتف" aria-label="Username"
                                aria-describedby="basic-addon1" autocomplete="off">
                        </div>
                    </div>
                    <div class="card-body p-0" id="admin-conversation-list">
                        <div class="border-bottom"></div>
                        @include('vendor-views.messages.admin_data')
                    </div>
                    <div class="px-4">
                        <ul class="nav nav-tabs mb-3 border-0">
                            <li class="nav-item">
                                <a href="{{route('vendor.message.list', ['tab'=> 'customer'])}}" class="nav-link {{$tab=='customer'?'active':''}}">العملاء</a>
                            </li>
                        </ul>
                    </div>
                    <!-- Body -->
                    <div class="card-body p-0 initial-19" id="conversation-list">
                        <div class="border-bottom"></div>
                        @include('vendor-views.messages.data')
                    </div>
                    <!-- End Body -->
                </div>
                <!-- End Card -->
            </div>
            <div class="col-lg-8 col-nd-6" id="view-conversation">
                <div class="text-center view_conversation-style" style="border-radius:24px; background:#ffffff; padding:2.5rem 1.5rem; box-shadow:0 1px 3px rgba(15,23,42,0.04); border:1px dashed rgba(148,163,184,0.7); color:#6b7280;">
                    <h4 class="view_conversation-h4-style mb-1">اختر محادثة من القائمة لعرض الرسائل</h4>
                    <p class="mb-0" style="font-size:.9rem;">سيظهر هنا محتوى المحادثة مع العميل عند الضغط عليها من القائمة اليسرى.</p>
                </div>
                {{-- view here --}}
            </div>
        </div>
        <!-- End Row -->
    </div>

@endsection

@push('script_2')
    <script src="{{ dynamicAsset('public/assets/admin/js/spartan-multi-image-picker.js') }}"></script>
    <script>
        "use strict";
        function viewConvs(url, id_to_active, conv_id, sender_id) {
            var tab = getUrlParameter('tab');
            $('.customer-list').removeClass('conv-active');
            $('#' + id_to_active).addClass('conv-active');
            let new_url= "{{ route('vendor.message.list') }}" + '?tab=' + tab+ '&conversation=' + conv_id+ '&user=' + sender_id;
            $.get({
                url: url,
                success: function(data) {
                    window.history.pushState('', 'New Page Title', new_url);
                    $('#view-conversation').html(data.view);
                    converationList();
                }
            });

        }

        let page = 1;
        $('#conversation-list').scroll(function() {
            if ($('#conversation-list').scrollTop() + $('#conversation-list').height() >= $('#conversation-list')
                .height()) {
                page++;
                loadMoreData(page);
            }
        });

        function loadMoreData(page) {
            $.ajax({
                    url: "{{ route('vendor.message.list') }}" + '?tab=' + tab+ '&page=' + page,
                    type: "get",
                    beforeSend: function() {

                    }
                })
                .done(function(data) {
                    if (data.html == " ") {
                        return;
                    }
                    $("#conversation-list").append(data.html);
                })
                .fail(function(jqXHR, ajaxOptions, thrownError) {
                    alert('server not responding...');
                });
        }

        function fetch_data(page, query) {
            var tab = getUrlParameter('tab');
            $.ajax({
                url: "{{ route('vendor.message.list') }}" + '?tab=' + tab + '&page=' + page + "&key=" + query,
                success: function(data) {
                    $('#admin-conversation-list').empty();
                    $('#conversation-list').empty();
                    $("#admin-conversation-list").append(data.admin_html);
                    $("#conversation-list").append(data.html);
                }
            })
        }

        $(document).on('keyup', '#serach', function() {
            let query = $('#serach').val();
            fetch_data(page, query);
        });
    </script>
@endpush
