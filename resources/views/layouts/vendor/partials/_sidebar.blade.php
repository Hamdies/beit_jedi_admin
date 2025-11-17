<div id="sidebarMain" class="d-none">
    <style>
        .navbar-vertical-content .nav-sub {
            background-color: #111a4d !important;
        }
    </style>
    <aside
        class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered">
        <div class="navbar-vertical-container">
            <div class="navbar-brand-wrapper justify-content-between">
                <!-- Logo -->
                <div class="sidebar-logo-container">
                    @php($restaurant_data=\App\CentralLogics\Helpers::get_restaurant_data())
                    <a class="navbar-brand pt-0 pb-0" href="{{route('vendor.dashboard')}}" aria-label="Front">
                            <img class="navbar-brand-logo"
                            src="{{ $restaurant_data?->logo_full_url }}"
                            alt="image">
                            <img class="navbar-brand-logo-mini"
                            src="{{ $restaurant_data?->logo_full_url }}"
                            alt="image">

                        <div class="ps-2">
                            <h6>
                                {{\Illuminate\Support\Str::limit($restaurant_data->name,15)}}
                            </h6>
                        </div>
                    </a>
                    <!-- End Logo -->

                    <!-- Navbar Vertical Toggle -->
                    <button type="button"
                            class="js-navbar-vertical-aside-toggle-invoker navbar-vertical-aside-toggle btn btn-icon btn-xs btn-ghost-dark">
                        <i class="tio-clear tio-lg"></i>
                    </button>
                    <!-- End Navbar Vertical Toggle -->
                </div>
                <div class="navbar-nav-wrap-content-left ml-auto d-none d-xl-block">
                    <!-- Navbar Vertical Toggle -->
                    <button type="button" class="js-navbar-vertical-aside-toggle-invoker close">
                        <i class="tio-first-page navbar-vertical-aside-toggle-short-align" data-toggle="tooltip"
                        data-placement="right" title="Collapse"></i>
                        <i class="tio-last-page navbar-vertical-aside-toggle-full-align"
                        data-template='<div class="tooltip d-none d-sm-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'></i>
                    </button>
                    <!-- End Navbar Vertical Toggle -->
                </div>

            </div>

            <!-- Content -->
            <div class="navbar-vertical-content text-capitalize bg-334257" style="background-color:#111a4d;">
                <ul class="navbar-nav navbar-nav-lg nav-tabs mt-3">
                    <li class="navbar-vertical-aside-has-menu {{Request::is('restaurant-panel')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{route('vendor.dashboard')}}" title="لوحة التحكم">
                            <i class="tio-home-vs-1-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                لوحة التحكم
                            </span>
                        </a>
                    </li>
                    <!-- End Dashboards -->

                    @if(\App\CentralLogics\Helpers::employee_module_permission_check('pos'))
                    <!-- POS -->
                    <li class="navbar-vertical-aside-has-menu {{Request::is('restaurant-panel/pos')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('vendor.pos.index')}}" title="{{translate('Point Of Sale')}}"
                        >
                            <i class="tio-shopping nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('Point Of Sale')}}</span>
                        </a>
                    </li>
                    <!-- End POS -->
                    @endif

                    <li class="nav-item">
                        <small
                            class="nav-subtitle">العروض والترويج</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>
                    <!-- Campaign removed from vendor sidebar -->


                <!-- Coupon -->
                @if (\App\CentralLogics\Helpers::employee_module_permission_check('coupon'))
                <li class="navbar-vertical-aside-has-menu {{ Request::is('restaurant-panel/coupon*') ? 'active' : '' }}">
                <a class="js-navbar-vertical-aside-menu-link nav-link"
                    href="{{ route('vendor.coupon.add-new') }}"
                    title="القسائم والعروض">
                    <i class="tio-ticket nav-icon"></i>
                    <span
                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">القسائم</span>
                </a>
                </li>
                @endif
                <!-- End Coupon   --}}

                    @if(\App\CentralLogics\Helpers::employee_module_permission_check('order'))
                    <li class="nav-item">
                        <small class="nav-subtitle" title="إدارة الطلبات">إدارة الطلبات</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>

                    <!-- Order -->
                    <li class="navbar-vertical-aside-has-menu {{Request::is('restaurant-panel/order*') && (Request::is('restaurant-panel/order/subscription*') == false ) ?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                            title="الطلبات">
                            <i class="tio-shopping-cart nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                الطلبات
                            </span>
                        </a>


                        @php($data =0)
                        @php($restaurant =\App\CentralLogics\Helpers::get_restaurant_data())
                        @if (($restaurant->restaurant_model == 'subscription' && isset($restaurant->restaurant_sub) && $restaurant->restaurant_sub->self_delivery == 1)  || ($restaurant->restaurant_model == 'commission' && $restaurant->self_delivery_system == 1) )
                        @php($data =1)
                        @endif

                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                        style="display:  {{Request::is('restaurant-panel/order*') && (Request::is('restaurant-panel/order/subscription*') == false )?'block':'none'}}">
                            <li class="nav-item {{Request::is('restaurant-panel/order/list/all')?'active':''}} @yield('all_order') ">
                                <a class="nav-link" href="{{route('vendor.order.list',['all'])}}" title="{{translate('messages.all_order')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate sidebar--badge-container">
                                        الكل
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{\App\Models\Order::where('restaurant_id', \App\CentralLogics\Helpers::get_restaurant_id())
                                                ->where(function($query) use($data){
                                                    return $query->whereNotIn('order_status',(config('order_confirmation_model') == 'restaurant'|| $data)?['failed','canceled', 'refund_requested', 'refunded']:['pending','failed','canceled', 'refund_requested', 'refunded'])
                                                    ->orWhere(function($query){
                                                        return $query->where('order_status','pending')->whereIn('order_type', ['take_away','dine_in']);
                                                    });
                                            })->Notpos()->HasSubscriptionToday()->NotDigitalOrder()
                                            ->count()}}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('restaurant-panel/order/list/pending')?'active':'' }} @yield('pending')">
                                <a class="nav-link " href="{{route('vendor.order.list',['pending'])}}" title="{{translate('messages.pending')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate sidebar--badge-container">
                                        قيد الانتظار
                                            <span class="badge badge-soft-success badge-pill ml-1">
                                            @if(config('order_confirmation_model') == 'restaurant' || $data)
                                            {{\App\Models\Order::where(['order_status'=>'pending','restaurant_id'=>\App\CentralLogics\Helpers::get_restaurant_id()])->Notpos()->NotDigitalOrder()->HasSubscriptionToday()->OrderScheduledIn(30)->count()}}
                                            @else
                                            {{\App\Models\Order::where(['order_status'=>'pending','restaurant_id'=>\App\CentralLogics\Helpers::get_restaurant_id()])->whereIn('order_type',['take_away','dine_in'])->NotDigitalOrder()->Notpos()->HasSubscriptionToday()->OrderScheduledIn(30)->count()}}
                                            @endif
                                        </span>
                                    </span>
                                </a>
                            </li>

                            <li class="nav-item {{Request::is('restaurant-panel/order/list/confirmed')?'active':''}} @yield('confirmed') ">
                                <a class="nav-link " href="{{route('vendor.order.list',['confirmed'])}}" title="{{translate('messages.confirmed')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate sidebar--badge-container">
                                        تم التأكيد
                                            <span class="badge badge-soft-success badge-pill ml-1">
                                            {{\App\Models\Order::whereIn('order_status',['confirmed',])->NotDigitalOrder()->Notpos()->whereNotNull('confirmed')->where('restaurant_id', \App\CentralLogics\Helpers::get_restaurant_id())->HasSubscriptionToday()->OrderScheduledIn(30)->count()}}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('restaurant-panel/order/list/accepted')?'active':''}} @yield('accepted')">
                                <a class="nav-link " href="{{route('vendor.order.list',['accepted'])}}"  title="{{translate('accepted')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate sidebar--badge-container">
                                        تم القبول
                                            <span class="badge badge-soft-success badge-pill ml-1">
                                            {{\App\Models\Order::whereIn('order_status',['accepted'])->NotDigitalOrder()->hasSubscriptionToday()->where(['restaurant_id'=>\App\CentralLogics\Helpers::get_restaurant_id()])->Notpos()->count()}}
                                        </span>
                                    </span>
                                </a>
                            </li>

                            <li class="nav-item {{Request::is('restaurant-panel/order/list/cooking')?'active':''}} @yield('processing')">
                                <a class="nav-link" href="{{route('vendor.order.list',['cooking'])}}" title="{{translate('messages.cooking')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate sidebar--badge-container">
                                        قيد التحضير
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{\App\Models\Order::where(['order_status'=>'processing', 'restaurant_id'=>\App\CentralLogics\Helpers::get_restaurant_id()])->NotDigitalOrder()->HasSubscriptionToday()->Notpos()->count()}}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('restaurant-panel/order/list/ready_for_delivery')?'active':''}} @yield('handover')">
                                <a class="nav-link" href="{{route('vendor.order.list',['ready_for_delivery'])}}" title="{{translate('Ready For Delivery')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate sidebar--badge-container">
                                        جاهز للتسليم
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{\App\Models\Order::where(['order_status'=>'handover', 'restaurant_id'=>\App\CentralLogics\Helpers::get_restaurant_id()])->NotDigitalOrder()->HasSubscriptionToday()->Notpos()->count()}}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('restaurant-panel/order/list/food_on_the_way')?'active':''}} @yield('picked_up')">
                                <a class="nav-link" href="{{route('vendor.order.list',['food_on_the_way'])}}" title="{{translate('Food On The Way')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate sidebar--badge-container">
                                        في الطريق
                                        <span class="badge badge-soft-info badge-pill ml-1">
                                            {{\App\Models\Order::where(['order_status'=>'picked_up', 'restaurant_id'=>\App\CentralLogics\Helpers::get_restaurant_id()])->NotDigitalOrder()->HasSubscriptionToday()->Notpos()->count()}}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('restaurant-panel/order/list/delivered')?'active':''}} @yield('delivered')">
                                <a class="nav-link " href="{{route('vendor.order.list',['delivered'])}}"  title="{{translate('Delivered')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate sidebar--badge-container">
                                        مكتمل
                                            <span class="badge badge-soft-success badge-pill ml-1">
                                            {{\App\Models\Order::where(['order_status'=>'delivered','restaurant_id'=>\App\CentralLogics\Helpers::get_restaurant_id()])->NotDigitalOrder()->HasSubscriptionToday()->Notpos()->count()}}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('restaurant-panel/order/list/refunded')?'active':''}} @yield('refunded')">
                                <a class="nav-link " href="{{route('vendor.order.list',['refunded'])}}"  title="{{translate('Refunded')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate sidebar--badge-container">
                                        مسترجع
                                            <span class="badge badge-soft-danger bg-light badge-pill ml-1">
                                            {{\App\Models\Order::Refunded()->where(['restaurant_id'=>\App\CentralLogics\Helpers::get_restaurant_id()])->NotDigitalOrder()->HasSubscriptionToday()->Notpos()->count()}}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('restaurant-panel/order/list/refund_requested')?'active':''}} @yield('refund_requested')">
                                <a class="nav-link " href="{{route('vendor.order.list',['refund_requested'])}}"  title="{{translate('refund_requested')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate sidebar--badge-container">
                                        طلب استرجاع
                                            <span class="badge badge-soft-danger bg-light badge-pill ml-1">
                                            {{\App\Models\Order::Refund_requested()->NotDigitalOrder()->where(['restaurant_id'=>\App\CentralLogics\Helpers::get_restaurant_id()])->Notpos()->count()}}
                                        </span>
                                    </span>
                                </a>
                            </li>


                            <li class="nav-item {{Request::is('restaurant-panel/order/list/payment_failed')?'active':''}} @yield('failed')">
                                <a class="nav-link " href="{{route('vendor.order.list',['payment_failed'])}}"  title="{{translate('payment_failed')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate sidebar--badge-container">
                                        فشل الدفع
                                            <span class="badge badge-soft-success badge-pill ml-1">
                                            {{\App\Models\Order::where('order_status','failed')->NotDigitalOrder()->where(['restaurant_id'=>\App\CentralLogics\Helpers::get_restaurant_id()])->Notpos()->count()}}
                                        </span>
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('restaurant-panel/order/list/canceled')?'active':''}} @yield('canceled')">
                                <a class="nav-link " href="{{route('vendor.order.list',['canceled'])}}"  title="{{translate('canceled')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate sidebar--badge-container">
                                        ملغى
                                            <span class="badge badge-soft-success badge-pill ml-1">
                                            {{\App\Models\Order::where('order_status','canceled')->NotDigitalOrder()->where(['restaurant_id'=>\App\CentralLogics\Helpers::get_restaurant_id()])->Notpos()->count()}}
                                        </span>
                                    </span>
                                </a>
                            </li>



                        </ul>
                    </li>

                    {{-- Order subscription menu removed from vendor sidebar --}}

                    <!-- End Order -->
                    @endif
                    <li class="nav-item">
                        <small
                            class="nav-subtitle">إدارة القائمة والأطباق</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>
                    <!-- End AddOn -->
                    @if(\App\CentralLogics\Helpers::employee_module_permission_check('food'))
                    <li class="navbar-vertical-aside-has-menu {{Request::is('restaurant-panel/category*')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                            href="javascript:" title="التصنيفات"
                        >
                            <i class="tio-category nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">التصنيفات</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                        style="display: {{Request::is('restaurant-panel/category*')?'block':'none'}}">
                            <li class="nav-item {{Request::is('restaurant-panel/category/list')?'active':''}}">
                                <a class="nav-link " href="{{route('vendor.category.add')}}"
                                    title="{{translate('messages.category')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">تصنيفات رئيسية</span>
                                </a>
                            </li>

                            <li class="nav-item {{Request::is('restaurant-panel/category/sub-category-list')?'active':''}}">
                                <a class="nav-link " href="{{route('vendor.category.add-sub-category')}}"
                                    title="{{translate('messages.sub_category')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">تصنيفات فرعية</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <!-- Food -->
                    <li class="navbar-vertical-aside-has-menu {{Request::is('restaurant-panel/food*')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:" title="الأطباق"
                        >
                            <i class="tio-premium-outlined nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">الأطباق</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                        style="display:{{Request::is('restaurant-panel/food*')?'block':'none'}}">
                            <li class="nav-item {{Request::is('restaurant-panel/food/add-new')?'active':''}}">
                                <a class="nav-link " href="{{route('vendor.food.add-new')}}"
                                     title="{{translate('Add New Food')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span
                                        class="text-truncate">إضافة طبق جديد</span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('restaurant-panel/food/list')?'active':''}}">
                                <a class="nav-link " href="{{route('vendor.food.list')}}"  title="{{translate('Food List')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">قائمة الأطباق</span>
                                </a>
                            </li>
                            @if(\App\CentralLogics\Helpers::get_restaurant_data()->food_section)
                            {{-- Bulk Import/Export hidden from vendor Foods sidebar --}}
                            @endif
                        </ul>
                    </li>
                    <!-- End Food -->
                    @endif
                    <!-- AddOn -->
                    @if(\App\CentralLogics\Helpers::employee_module_permission_check('addon'))
                    <li class="navbar-vertical-aside-has-menu {{Request::is('restaurant-panel/addon*')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{route('vendor.addon.add-new')}}" title="الإضافات"
                        >
                            <i class="tio-add-circle-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                الإضافات
                            </span>
                        </a>
                    </li>
                    @endif

                    <!-- DeliveryMan -->
                    @if(\App\CentralLogics\Helpers::employee_module_permission_check('deliveryman'))
                        <li class="nav-item">
                            <small class="nav-subtitle"
                                   title="{{translate('messages.deliveryman_section')}}">{{translate('messages.deliveryman_management')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{Request::is('restaurant-panel/delivery-man/add')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('vendor.delivery-man.add')}}"
                               title="{{translate('messages.add_delivery_man')}}"
                            >
                                <i class="tio-running nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('messages.add_delivery_man')}}
                                </span>
                            </a>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('restaurant-panel/delivery-man/list')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('vendor.delivery-man.list')}}"
                               title="{{translate('messages.deliveryman_list')}}"
                            >
                                <i class="tio-filter-list nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('messages.deliverymen_list')}}
                                </span>
                            </a>
                        </li>

                        {{--<li class="navbar-vertical-aside-has-menu {{Request::is('restaurant-panel/delivery-man/reviews/list')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('vendor.delivery-man.reviews.list')}}" title="{{translate('messages.reviews')}}"
                            >
                                <i class="tio-star-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('messages.reviews')}}
                                </span>
                            </a>
                        </li>--}}
                    @endif
                <!-- End DeliveryMan -->


                    <!-- Business Section-->
                    <li class="nav-item">
                        <small class="nav-subtitle"
                                title="{{translate('messages.business_section')}}">{{translate('messages.business_management')}}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>

                    @if(\App\CentralLogics\Helpers::employee_module_permission_check('restaurant_setup'))
                    <li class="navbar-vertical-aside-has-menu {{Request::is('restaurant-panel/business-settings/restaurant-setup')?'active':''}}">
                        <a class="nav-link " href="{{route('vendor.business-settings.restaurant-setup')}}" title="إعدادات المطعم"
                        >
                            <span class="tio-settings nav-icon"></span>
                            <span
                                class="text-truncate">إعدادات المطعم</span>
                        </a>
                    </li>
                    @endif
                    {{-- Notification Setup hidden from Business Management sidebar --}}

                    @if(\App\CentralLogics\Helpers::employee_module_permission_check('my_shop'))
                    <li class="navbar-vertical-aside-has-menu {{Request::is('restaurant-panel/restaurant/view')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{route('vendor.shop.view')}}"
                            title="{{translate('My Resturant')}}">
                            <i class="tio-home nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                متجري
                            </span>
                        </a>
                    </li>
                    {{-- My Qr Code hidden from Business Management sidebar --}}
                    @endif
                    {{-- My Business Plan hidden from Business Management sidebar --}}




                    @if(\App\CentralLogics\Helpers::employee_module_permission_check('wallet'))
                    {{-- My Wallet & Wallet Method hidden from Business Management sidebar --}}
                    @endif
                    <!-- End RestaurantWallet -->
                    @if(\App\CentralLogics\Helpers::employee_module_permission_check('reviews'))
                    <li class="navbar-vertical-aside-has-menu {{Request::is('restaurant-panel/reviews')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{route('vendor.reviews')}}" title="التقييمات"
                        >
                            <i class="tio-star-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                التقييمات
                            </span>
                        </a>
                    </li>
                    @endif
                    <!-- End RestaurantWallet -->
                    @if(\App\CentralLogics\Helpers::employee_module_permission_check('chat'))
                    <li class="navbar-vertical-aside-has-menu {{Request::is('restaurant-panel/message*')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link"
                            href="{{route('vendor.message.list', ['tab' => 'customer'])}}" title="{{translate('messages.chat')}}"
                        >
                            <i class="tio-chat nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{translate('messages.chat')}}
                            </span>
                        </a>
                    </li>
                    @endif
                    <!-- End Business Settings -->
                    <!-- Employee-->
                    <li class="nav-item">
                        <small class="nav-subtitle" title="قسم التقارير">قسم التقارير</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>

                    @if(\App\CentralLogics\Helpers::employee_module_permission_check('report'))
                    {{-- Expense Report hidden from Report Section --}}

                    {{-- Transaction Report hidden from Report Section --}}

                    {{-- Disbursement Report hidden from Report Section --}}


                    <li class="navbar-vertical-aside-has-menu  {{Request::is('restaurant-panel/report/order-report') || Request::is('restaurant-panel/report/campaign-order-report') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                            title="تقرير الطلبات">
                            <i class="tio-user nav-icon"></i>
                            <span
                                class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">تقرير الطلبات</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                            style="display: {{Request::is('restaurant-panel/report/order-report') || Request::is('restaurant-panel/report/campaign-order-report') ? 'block' : 'none' }}">
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('restaurant-panel/report/order-report') ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('vendor.report.order-report') }}" title="تقرير الطلبات العادية">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate text-capitalize">تقرير الطلبات العادية</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif
                    <!-- End Employee -->

                    {{-- Highlights / promo card hidden from sidebar --}}
            </div>
            <!-- End Content -->
        </div>
    </aside>
</div>

<div id="sidebarCompact" class="d-none">

</div>



@push('script_2')
<script>
    "use strict";
    $(window).on('load' , function() {
        if($(".navbar-vertical-content li.active").length) {
            $('.navbar-vertical-content').animate({
                scrollTop: $(".navbar-vertical-content li.active").offset().top - 150
            }, 100);
        }
        });
</script>
@endpush
