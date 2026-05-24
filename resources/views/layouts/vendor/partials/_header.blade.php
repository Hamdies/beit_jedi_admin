<style>
/* ── BEIT JEDI HEADER ─────────────────────────────────
   Clean, RTL-first, no search, no lang selector
──────────────────────────────────────────────────── */
#bj-header {
    background: #1C2E5E;
    border-bottom: none;
    height: 60px;
    display: flex;
    align-items: center;
    padding: 0 1.5rem;
    position: sticky;
    top: 0;
    z-index: 1030;
    box-shadow: 0 2px 12px rgba(28,46,94,0.18);
    direction: rtl;
}

.bj-header-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    gap: 1rem;
}

.bj-sidebar-toggle {
    background: none;
    border: none;
    color: rgba(255,255,255,.65);
    font-size: 20px;
    cursor: pointer;
    padding: .25rem .5rem;
    border-radius: 7px;
    transition: color .15s, background .15s;
    line-height: 1;
    flex-shrink: 0;
}
.bj-sidebar-toggle:hover { color: #fff; background: rgba(255,255,255,.08); }

.bj-logo-area {
    display: flex;
    align-items: center;
    gap: .75rem;
    text-decoration: none;
    flex-shrink: 0;
}
.bj-logo-img {
    width: 36px; height: 36px;
    object-fit: contain;
    border-radius: 9px;
    background: rgba(255,255,255,.1);
}
.bj-logo-text {
    font-size: 1.05rem; font-weight: 800;
    color: #fff; line-height: 1.1; letter-spacing: -.3px;
}
.bj-logo-sub {
    font-size: .68rem; color: #D4A017;
    font-weight: 600; margin-top: 1px;
}

.bj-spacer { flex: 1; }

.bj-actions {
    display: flex; align-items: center; gap: .375rem;
}

.bj-icon-btn {
    position: relative;
    display: inline-flex; align-items: center; justify-content: center;
    width: 38px; height: 38px;
    border-radius: 10px;
    background: rgba(255,255,255,.08);
    border: 1.5px solid rgba(255,255,255,.1);
    color: rgba(255,255,255,.8);
    font-size: 18px;
    text-decoration: none;
    transition: background .15s, color .15s, border-color .15s;
    cursor: pointer; flex-shrink: 0;
}
.bj-icon-btn:hover {
    background: rgba(255,255,255,.15);
    border-color: rgba(255,255,255,.2);
    color: #fff; text-decoration: none;
}

.bj-msg-badge {
    position: absolute;
    top: -5px; left: -5px;
    min-width: 18px; height: 18px;
    background: #E74C3C; color: #fff;
    font-size: .62rem; font-weight: 800;
    border-radius: 999px;
    display: flex; align-items: center; justify-content: center;
    padding: 0 4px;
    border: 2px solid #1C2E5E;
    line-height: 1;
    animation: badgePop .25s cubic-bezier(.22,1,.36,1);
}
@keyframes badgePop {
    from { transform: scale(0); } to { transform: scale(1); }
}

.bj-divider {
    width: 1px; height: 24px;
    background: rgba(255,255,255,.12);
    margin: 0 .25rem; flex-shrink: 0;
}

.bj-user-pill {
    display: flex; align-items: center; gap: .625rem;
    background: rgba(255,255,255,.08);
    border: 1.5px solid rgba(255,255,255,.1);
    border-radius: 10px;
    padding: .35rem .75rem .35rem .5rem;
    cursor: pointer;
    transition: background .15s, border-color .15s;
    text-decoration: none; position: relative;
}
.bj-user-pill:hover {
    background: rgba(255,255,255,.14);
    border-color: rgba(255,255,255,.2); text-decoration: none;
}
.bj-user-avatar {
    width: 30px; height: 30px;
    border-radius: 8px; object-fit: cover; flex-shrink: 0;
}
.bj-user-avatar-placeholder {
    width: 30px; height: 30px; border-radius: 8px;
    background: #D4A017;
    display: flex; align-items: center; justify-content: center;
    color: #1C2E5E; font-size: 13px; font-weight: 800; flex-shrink: 0;
}
.bj-user-name {
    font-size: .82rem; font-weight: 700; color: #fff;
    max-width: 120px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.bj-user-role {
    font-size: .65rem; color: rgba(255,255,255,.5);
    font-weight: 500; line-height: 1; margin-top: 1px;
}
.bj-user-caret { font-size: 12px; color: rgba(255,255,255,.4); margin-right: .125rem; }

.bj-dropdown {
    position: absolute; top: calc(100% + 8px); left: 0;
    min-width: 200px;
    background: #fff;
    border: 1.5px solid #E2E5ED;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(28,46,94,0.14);
    overflow: hidden;
    opacity: 0; visibility: hidden; transform: translateY(-6px);
    transition: opacity .18s, transform .18s, visibility .18s;
    z-index: 9999; direction: rtl;
}
.bj-dropdown.open { opacity: 1; visibility: visible; transform: translateY(0); }

.bj-dropdown-header {
    padding: .875rem 1rem;
    border-bottom: 1px solid #E2E5ED;
    background: #F4F5F8;
}
.bj-dropdown-name { font-size: .9rem; font-weight: 800; color: #1A1F36; }
.bj-dropdown-email {
    font-size: .73rem; color: #8B91A8; margin-top: 1px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

.bj-dropdown-item {
    display: flex; align-items: center; gap: .625rem;
    padding: .75rem 1rem;
    font-size: .82rem; font-weight: 600; color: #1A1F36;
    text-decoration: none;
    transition: background .12s;
    border: none; background: none; width: 100%;
    cursor: pointer; font-family: inherit;
}
.bj-dropdown-item i { font-size: 16px; color: #8B91A8; }
.bj-dropdown-item:hover { background: #EEF1F8; text-decoration: none; color: #1C2E5E; }
.bj-dropdown-item:hover i { color: #1C2E5E; }
.bj-dropdown-item--danger { color: #C0392B; }
.bj-dropdown-item--danger i { color: #C0392B; }
.bj-dropdown-item--danger:hover { background: #FDECEA; color: #C0392B; }
.bj-dropdown-sep { height: 1px; background: #E2E5ED; margin: .25rem 0; }

/* Override the framework's navbar height/positioning */
.main { padding-top: 0 !important; }
#header { display: none !important; }

@media (max-width: 600px) {
    #bj-header { padding: 0 .875rem; }
    .bj-user-name, .bj-user-role { display: none; }
    .bj-user-pill { padding: .35rem; }
    .bj-logo-text, .bj-logo-sub { display: none; }
}

/* ── Mobile: reveal header (hidden on desktop because sidebar is always visible) ── */
@media (max-width: 1024px) {
    #headerMain.d-none { display: block !important; }
    #bj-header { height: 56px; }
    /* Push content below sticky header so first card isn't covered */
    body.bj-shell main#content > * { scroll-margin-top: 64px; }
}
@media (min-width: 1025px) {
    .bj-sidebar-toggle { display: none !important; }
}
</style>

<div id="headerMain" class="d-none">
<header id="bj-header">
    <div class="bj-header-inner">

        <button class="bj-sidebar-toggle js-navbar-vertical-aside-toggle-invoker" type="button">
            <i class="tio-menu-hamburger"></i>
        </button>

        @php($restaurant_logo = \App\CentralLogics\Helpers::get_restaurant_data()?->logo_full_url)
        <a class="bj-logo-area" href="{{ route('vendor.dashboard') }}">
            @if($restaurant_logo)
                <img class="bj-logo-img" src="{{ $restaurant_logo }}" alt="logo">
            @endif
            <div>
                <div class="bj-logo-text">{{ \App\CentralLogics\Helpers::get_restaurant_data()->name ?? 'Beit Jedi' }}</div>
                <div class="bj-logo-sub">لوحة التحكم</div>
            </div>
        </a>

        <div class="bj-spacer"></div>

        <div class="bj-actions">

            <a class="bj-icon-btn" href="{{ route('vendor.order.list', ['status' => 'pending']) }}" title="الطلبات المعلقة">
                <i class="tio-shopping-basket-outlined"></i>
            </a>

            @php($msg_count = \App\Models\Conversation::whereUser(\App\CentralLogics\Helpers::get_loggedin_user()->id)->where('unread_message_count', '>', 0)->count())
            <a class="bj-icon-btn" href="{{ route('vendor.message.list', ['tab' => 'customer']) }}" title="الرسائل">
                <i class="tio-messages-outlined"></i>
                @if($msg_count > 0)
                    <span class="bj-msg-badge">{{ $msg_count > 99 ? '99+' : $msg_count }}</span>
                @endif
            </a>

            <div class="bj-divider"></div>

            @php($loggedin = \App\CentralLogics\Helpers::get_loggedin_user())
            <div style="position:relative;">
                <div class="bj-user-pill" id="bj-user-pill" onclick="bjToggleDropdown()">
                    @if($loggedin?->image_full_url)
                        <img class="bj-user-avatar" src="{{ $loggedin->image_full_url }}" alt="avatar">
                    @else
                        <div class="bj-user-avatar-placeholder">
                            {{ strtoupper(substr($loggedin->f_name ?? 'U', 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <div class="bj-user-name">{{ $loggedin->f_name ?? '' }}</div>
                        <div class="bj-user-role">{{ $loggedin->email ?? '' }}</div>
                    </div>
                    <i class="tio-chevron-down bj-user-caret"></i>
                </div>

                <div class="bj-dropdown" id="bj-dropdown">
                    <div class="bj-dropdown-header">
                        <div class="bj-dropdown-name">{{ $loggedin->f_name ?? '' }}</div>
                        <div class="bj-dropdown-email">{{ $loggedin->email ?? '' }}</div>
                    </div>
                    <a class="bj-dropdown-item" href="{{ route('vendor.profile.view') }}">
                        <i class="tio-settings-outlined"></i>
                        {{ translate('messages.settings') }}
                    </a>
                    <div class="bj-dropdown-sep"></div>
                    <button class="bj-dropdown-item bj-dropdown-item--danger" type="button"
                        onclick="Swal.fire({
                            title: '{{ translate('Do_You_Want_To_Sign_Out_?') }}',
                            showCancelButton: true,
                            confirmButtonColor: '#C0392B',
                            cancelButtonColor: '#4A5068',
                            confirmButtonText: '{{ translate('messages.Yes') }}',
                            cancelButtonText: '{{ translate('messages.cancel') }}',
                        }).then(function(r){ if(r.value) location.href='{{ route('logout') }}'; })">
                        <i class="tio-exit"></i>
                        {{ translate('messages.sign_out') }}
                    </button>
                </div>
            </div>

        </div>

    </div>
</header>
</div>
<div id="headerFluid" class="d-none"></div>
<div id="headerDouble" class="d-none"></div>

<?php
$wallet = \App\Models\RestaurantWallet::where('vendor_id', \App\CentralLogics\Helpers::get_vendor_id())->first();
$Payable_Balance = $wallet?->collected_cash > 0 ? 1 : 0;

$cash_in_hand_overflow = \App\Models\BusinessSetting::where('key', 'cash_in_hand_overflow_restaurant')->first()?->value;
$cash_in_hand_overflow_restaurant_amount = \App\Models\BusinessSetting::where('key', 'cash_in_hand_overflow_restaurant_amount')->first()?->value;
$val = round($cash_in_hand_overflow_restaurant_amount - ($cash_in_hand_overflow_restaurant_amount * 10) / 100, 8);
?>

@if ($Payable_Balance == 1 && $cash_in_hand_overflow && $wallet?->balance < 0 && $val <= abs($wallet?->collected_cash))
    <div class="alert __alert-2 alert-warning m-0 py-1 px-2" role="alert">
        <img class="rounded mr-1" width="25"
            src="{{ dynamicAsset('/public/assets/admin/img/header_warning.png') }}" alt="">
        <div class="cont">
            <h4 class="m-0">{{ translate('Attention_Please') }} </h4>
            {{ translate('The_Cash_in_Hand_amount_is_about_to_exceed_the_limit._Please_pay_the_due_amount._If_the_limit_exceeds,_your_account_will_be_suspended.') }}
        </div>
    </div>
@endif

@if (
    $Payable_Balance == 1 &&
        $cash_in_hand_overflow &&
        $wallet?->balance < 0 &&
        $cash_in_hand_overflow_restaurant_amount < $wallet?->collected_cash)
    <div class="alert __alert-2 alert-warning m-0 py-1 px-2" role="alert">
        <img class="mr-1" width="25" src="{{ dynamicAsset('/public/assets/admin/img/header_warning.png') }}"
            alt="">
        <div class="cont">
            <h4 class="m-0">{{ translate('Attention_Please') }} </h4>
            {{ translate('The_Cash_in_Hand_amount_limit_is_exceeded._Your_account_is_now_suspended._Please_pay_the_due_amount_to_receive_new_order_requests_again.') }}<a
                href="{{ route('vendor.wallet.index') }}" class="alert-link"> &nbsp;
                {{ translate('Pay_the_due') }}</a>
        </div>
    </div>
@endif

<?php
$restaurant_data = \App\CentralLogics\Helpers::get_restaurant_data();
$subscription_deadline_warning_days = \App\Models\BusinessSetting::where('key', 'subscription_deadline_warning_days')->first()?->value ?? 7;
$subscription_deadline_warning_message = \App\Models\BusinessSetting::where('key', 'subscription_deadline_warning_message')->first()?->value ?? null;
?>


<div id="hide-subscription-warnings">



    @if (
        !in_array($restaurant_data->restaurant_model, ['none', 'commission']) &&
            !Request::is('restaurant-panel/subscription/*'))

        <?php
        $pers = 10;
        if ($restaurant_data?->restaurant_sub) {
            $validity = $restaurant_data?->restaurant_sub?->validity;
            $remaining_days = Carbon\Carbon::now()->diffInDays($restaurant_data?->restaurant_sub?->expiry_date_parsed->format('Y-m-d'), false);
            $pers = $validity - $remaining_days > 0 ? (($validity - $remaining_days) / $validity) * 100 : 1;
            $pers = (439.6 * $pers) / 100;
        }
        ?>
@if (
    $restaurant_data?->restaurant_sub?->is_trial == 0 &&
        $restaurant_data?->restaurant_sub?->expiry_date_parsed &&
        $restaurant_data?->restaurant_sub->expiry_date_parsed->subDays($subscription_deadline_warning_days)->isBefore(now()) &&
        Request::is('restaurant-panel'))

    <!--Always in header Renew -->
    <div class="renew-badge mx-3 mt-3" id="renew-badge">
        <div class="renew-content d-flex align-items-center">

            <img src="{{ dynamicAsset('/public/assets/admin/img/timer.svg') }}" alt="">
            <div class="txt">
                {{ $subscription_deadline_warning_message != null ? $subscription_deadline_warning_message : translate('Your subscription ending soon. Please renew to continue access') }}
            </div>
        </div>
        <div>
            <a href="{{ route('vendor.subscriptionackage.subscriberDetail', ['renew_now' => true]) }}"
                class="btn btn--danger">{{ translate('Renew') }}</a>
        </div>
    </div>
@elseif (Session::get('subscription_renew_close_btn') !== true &&
        $restaurant_data?->restaurant_sub?->is_trial == 0 &&
        $restaurant_data?->restaurant_sub?->expiry_date_parsed &&
        $restaurant_data?->restaurant_sub->expiry_date_parsed->subDays($subscription_deadline_warning_days)->isBefore(now()) &&
        !Request::is('restaurant-panel'))
    <div class="renew-badge mx-3 mt-3 hide-warning" id="renew-badge">
        <div class="renew-content d-flex align-items-center">

            <img src="{{ dynamicAsset('/public/assets/admin/img/timer.svg') }}" alt="">
            <div class="txt">
                {{ $subscription_deadline_warning_message != null ? $subscription_deadline_warning_message : translate('Your subscription ending soon. Please renew to continue access') }}
            </div>
        </div>
        <div>
            @if ($restaurant_data?->restaurant_sub?->is_canceled == 1)
                <a href="{{ route('vendor.subscriptionackage.subscriberDetail', ['open_plans' => true]) }}"
                    class="btn btn--danger">{{ translate('Change_Subscription') }}</a>
            @else
                <a href="{{ route('vendor.subscriptionackage.subscriberDetail', ['renew_now' => true]) }}"
                    class="btn btn--danger">{{ translate('Renew') }}</a>
            @endif
            <button data-id="subscription_renew_close_btn" id="subs-hide-warning"
                class="btn btn-sm btn-primary add-to-session">{{ translate('remind_me_later') }}</button>
        </div>
    </div>
    <!-- Renew -->


@endif




        @if (Session::get('subscription_free_trial_close_btn') !== true &&
                $restaurant_data?->restaurant_sub?->status == 1 &&
                $restaurant_data?->restaurant_sub?->is_trial == 1 &&
                $restaurant_data?->restaurant_sub?->is_canceled == 0)
            <div class="free-trial trial success-bg">
                <div class="inner-div">
                    <div class="left">
                        <img src="{{ dynamicAsset('/public/assets/admin/img/icon-puck.svg') }}" alt="">
                        <div class="left-content">
                            <h6>{{ translate('Get the best experience of your business') }}</h6>
                            <div>{{ translate('Run your business with the most popular platform') }}</div>
                        </div>
                    </div>
                    <div class="right">
                        <a href="#" class="btn btn-2">
                            <span class="circle-progress-container">
                                <svg width="40" viewBox="0 0 160 160">
                                    <circle r="70" cx="80" cy="80" fill="transparent"
                                        stroke="#ffffff20" stroke-width="12px"></circle>
                                    <circle r="70" cx="80" cy="80" fill="transparent" stroke="#ffffff"
                                        stroke-width="12px" stroke-dasharray="439.6px"
                                        stroke-dashoffset="{{ $pers }}px"></circle>
                                </svg>
                                {{1+ Carbon\Carbon::now()->diffInDays($restaurant_data?->restaurant_sub?->expiry_date_parsed->format('Y-m-d'), false) }}
                            </span>
                            {{ translate('Days_left_in_free_trial') }}
                        </a>
                        <a href="{{ route('vendor.subscriptionackage.subscriberDetail', ['open_plans' => true]) }}"
                            class="btn btn-light">{{ translate('Choose_Subscription_Plan') }} <i
                                class="tio-arrow-forward"></i></a>
                    </div>

                    <button type="button" data-id="subscription_free_trial_close_btn"
                        class="trial-close add-to-session ">
                        <i class="tio-clear-circle"></i>
                    </button>
                </div>
            </div>
        @elseif ($restaurant_data?->restaurant_sub == null && $restaurant_data?->restaurant_sub_update_application?->is_trial == 1)
            <div class="modal fade show trial-ended-modal" id="free-trial-modal">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body p-0">
                            <div class="trial-ended-modal-wrapper">
                                {{-- <button type="button" class="trial-ended-close-btn text-md-white" data-dismiss="modal">
                                <i class="tio-clear-circle"></i>
                            </button> --}}
                                <div class="trial-ended-modal-content align-self-center">
                                    <h3 class="title">{{ translate('Your_Free_Trial_Has_Been_Ended') }}</h3>
                                    <p class="mb-4">
                                        {{ translate('Purchase a subscription plan or contact with the admin to settle the payment and unblock the access to service.') }}
                                    </p>
                                    <a href="{{ route('vendor.subscriptionackage.subscriberDetail', ['open_plans' => true]) }}"
                                        class="btn btn--primary">{{ translate('Choose Subscription Plan') }} <i
                                            class="tio-arrow-forward"></i></a>
                                    <div class="blocked-subscription mt-5">
                                        <img src="{{ dynamicAsset('/public/assets/admin/img/WarningOctagon.svg') }}"
                                            alt="">
                                        <span>{{ translate('All Access to service has been blocked due to no active subscription') }}</span>
                                    </div>
                                </div>
                                <div class="trial-ended-modal-img d-none d-md-block">
                                    <img src="{{ dynamicAsset('/public/assets/admin/img/trial-ended-bg.png') }}"
                                        alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <div class="free-trial trial danger-bg">
                <div class="inner-div">
                    <div class="left">
                        <img src="{{ dynamicAsset('/public/assets/admin/img/timer-2.svg') }}" alt="">
                        <div class="left-content">
                            <h6>{{ translate('Free_Trial_Has_Been_Ended') }}</h6>
                            <div>{{ translate('Get_a_subscription_plan_to_continue_with_your_business') }}</div>
                        </div>
                    </div>
                    <div class="right">
                        <a href="{{ route('vendor.subscriptionackage.subscriberDetail', ['open_plans' => true]) }}"
                            class="btn btn-light">{{ translate('Choose_Subscription_Plan') }} <i
                                class="tio-arrow-forward"></i></a>
                    </div>
                    {{-- <button type="button" class="trial-close">
                    <i class="tio-clear-circle"></i>
                </button> --}}
                </div>
            </div>
        @elseif (Session::get('subscription_cancel_close_btn') !== true &&
                $restaurant_data?->restaurant_sub &&
                $restaurant_data?->restaurant_sub?->is_canceled == 1)
            <div class="free-trial trial danger-bg">
                <div class="inner-div">
                    <div class="left">
                        <img src="{{ dynamicAsset('/public/assets/admin/img/timer-2.svg') }}" alt="">
                        <div class="left-content">
                            <h6>{{ translate('Your_Subscription_Has_Been_Cnaceled_by') }}
                                {{ $restaurant_data?->restaurant_sub?->canceled_by == 'admin' ? translate($restaurant_data?->restaurant_sub?->canceled_by) : translate('Yourself') }}
                            </h6>
                            <div>{{ translate('You_can_not_consume_your_subscription_after') }}
                                {{ \App\CentralLogics\Helpers::date_format($restaurant_data?->restaurant_sub?->expiry_date_parsed) }}
                            </div>
                        </div>
                    </div>
                    <div class="right">
                        <a href="#" class="btn btn-2">
                            <span class="circle-progress-container">
                                <svg width="40" viewBox="0 0 160 160">
                                    <circle r="70" cx="80" cy="80" fill="transparent"
                                        stroke="#ffffff20" stroke-width="12px"></circle>
                                    <circle r="70" cx="80" cy="80" fill="transparent" stroke="#ffffff"
                                        stroke-width="12px" stroke-dasharray="439.6px"
                                        stroke-dashoffset="{{ $pers }}px"></circle>
                                </svg>
                                {{1+ Carbon\Carbon::now()->diffInDays($restaurant_data?->restaurant_sub?->expiry_date_parsed->format('Y-m-d'), false) }}
                            </span>
                            {{ translate('Days_left_in_this_subscription') }}
                        </a>
                        <a href="{{ route('vendor.subscriptionackage.subscriberDetail', ['open_plans' => true]) }}"
                            class="btn btn-light">{{ translate('Change_Subscription_Plan') }} <i
                                class="tio-arrow-forward"></i></a>
                    </div>

                    <button type="button" data-id="subscription_cancel_close_btn"
                        class="trial-close add-to-session ">
                        <i class="tio-clear-circle"></i>
                    </button>
                </div>
            </div>
        @elseif (Session::get('subscription_plan_update_close_btn') !== true &&
                $restaurant_data?->restaurant_sub &&
                $restaurant_data?->restaurant_sub?->package?->status != 1)
            <div class="free-trial trial danger-bg">
                <div class="inner-div">
                    <div class="left">
                        <img src="{{ dynamicAsset('/public/assets/admin/img/timer-2.svg') }}" alt="">
                        <div class="left-content">
                            <h6>{{ translate('Your_Current_Subscription_Package_has_been_Disable_By_Admin.') }} </h6>
                            <div>{{ translate('You_can_not_renew_this_Package_after') }}
                                {{ \App\CentralLogics\Helpers::date_format($restaurant_data?->restaurant_sub?->expiry_date_parsed) }}.
                                {{ translate('to_continue_your_subscription_please_chose_another_package.') }}</div>
                        </div>
                    </div>
                    <div class="right">
                        <a href="#" class="btn btn-2">
                            <span class="circle-progress-container">
                                <svg width="40" viewBox="0 0 160 160">
                                    <circle r="70" cx="80" cy="80" fill="transparent"
                                        stroke="#ffffff20" stroke-width="12px"></circle>
                                    <circle r="70" cx="80" cy="80" fill="transparent" stroke="#ffffff"
                                        stroke-width="12px" stroke-dasharray="439.6px"
                                        stroke-dashoffset="{{ $pers }}px"></circle>
                                </svg>
                                {{1+ Carbon\Carbon::now()->diffInDays($restaurant_data?->restaurant_sub?->expiry_date_parsed->format('Y-m-d'), false) }}
                            </span>
                            {{ translate('Days_left_in_this_subscription') }}
                        </a>
                        <a href="{{ route('vendor.subscriptionackage.subscriberDetail', ['open_plans' => true]) }}"
                            class="btn btn-light">{{ translate('Change_Subscription_Plan') }} <i
                                class="tio-arrow-forward"></i></a>
                    </div>

                    <button type="button" data-id="subscription_plan_update_close_btn"
                        class="trial-close add-to-session ">
                        <i class="tio-clear-circle"></i>
                    </button>
                </div>
            </div>
        @elseif ($restaurant_data?->restaurant_model == 'unsubscribed' && !$restaurant_data?->restaurant_sub_update_application )
            <div class="free-trial trial danger-bg">
                <div class="inner-div">
                    <div class="left">
                        <img src="{{ dynamicAsset('/public/assets/admin/img/timer-2.svg') }}" alt="">
                        <div class="left-content">
                            <h6>{{ translate('Your_are_not_subscribed') }}
                                {{-- {{ \App\CentralLogics\Helpers::date_format($restaurant_data?->restaurant_sub_update_application?->expiry_date_parsed) }} --}}
                            </h6>
                            <div>
                                {{ translate('Purchase a subscription plan or contact with the admin to settle the payment and unblock the access to service') }}
                            </div>
                        </div>
                    </div>
                    <div class="right">

                        <a href="{{ route('vendor.subscriptionackage.subscriberDetail', ['open_plans' => true]) }}"
                            class="btn btn-light">{{ translate('Choose Subscription_Plan') }} <i
                                class="tio-arrow-forward"></i></a>
                    </div>

                </div>
            </div>

        @elseif ($restaurant_data?->restaurant_sub == null)
            <div class="free-trial trial danger-bg">
                <div class="inner-div">
                    <div class="left">
                        <img src="{{ dynamicAsset('/public/assets/admin/img/timer-2.svg') }}" alt="">
                        <div class="left-content">
                            <h6>{{ translate('Your_Subscription_Has_Been_Expired_on') }}
                                {{ \App\CentralLogics\Helpers::date_format($restaurant_data?->restaurant_sub_update_application?->expiry_date_parsed) }}
                            </h6>
                            <div>
                                {{ translate('Purchase a subscription plan or contact with the admin to settle the payment and unblock the access to service') }}
                            </div>
                        </div>
                    </div>
                    <div class="right">

                        <a href="{{ route('vendor.subscriptionackage.subscriberDetail', ['open_plans' => true]) }}"
                            class="btn btn-light">{{ translate('Change/Renew Subscription_Plan') }} <i
                                class="tio-arrow-forward"></i></a>
                    </div>

                </div>
            </div>
        @endif

    @endif
</div>


<div class="modal fade removeSlideDown" id="staticBackdrop" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered max-w-520">
        <div class="modal-content modal-content__search border-0">
            <div class="d-flex flex-column gap-3 rounded-20 bg-card py-2 px-3">
                <div class="d-flex gap-2 align-items-center position-relative">
                    <form class="flex-grow-1" id="searchForm" action="{{ route('vendor.search.routing') }}">
                        @csrf
                        <div class="d-flex align-items-center global-search-container">
                            <input class="form-control flex-grow-1 rounded-10 search-input" id="searchInput" name="search" type="search" placeholder="Search" aria-label="Search" autofocus>
                        </div>
                    </form>
                    <div class="position-absolute right-0 pr-2">
                        <button class="border-0 rounded px-2 py-1" type="button" data-dismiss="modal">{{ translate('Esc') }}</button>
                    </div>
                </div>

                <div class="min-h-350">
                    <div class="search-result" id="searchResults">
                        <div class="text-center text-muted py-5">{{translate('It appears that you have not yet searched.')}}.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    // Beit Jedi header dropdown
    function bjToggleDropdown() {
        var d = document.getElementById('bj-dropdown');
        if (d) d.classList.toggle('open');
    }
    document.addEventListener('click', function(e) {
        var pill = document.getElementById('bj-user-pill');
        var drop = document.getElementById('bj-dropdown');
        if (drop && pill && !pill.contains(e.target) && !drop.contains(e.target)) {
            drop.classList.remove('open');
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        $(document).on('click', '.add-to-session', function() {
            var session_data = $(this).data("id");
            $.ajax({
                url: '{{ route('vendor.subscriptionackage.addToSession') }}',
                method: 'POST',
                data: {
                    value: session_data,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#hide-subscription-warnings').addClass('d-none')
                }
            });
        });
    });
</script>
