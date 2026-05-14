@php
    use App\CentralLogics\Helpers;
    use Carbon\Carbon;

    $businessName = optional(\App\Models\BusinessSetting::where(['key' => 'business_name'])->first())->value ?? 'Beit Jedi';
    $address = $order->delivery_address ? json_decode($order->delivery_address, true) : [];

    $customerName = $address['contact_person_name'] ?? trim(($order?->customer?->f_name ?? '') . ' ' . ($order?->customer?->l_name ?? ''));
    $customerName = $customerName !== '' ? $customerName : translate('messages.walk_in_customer');
    $customerPhone = $address['contact_person_number'] ?? ($order?->customer?->phone ?? '');

    $branchName = $order->restaurant?->name ?? $businessName;
    $branchPhone = $order->restaurant?->phone ?? '';
    $branchAddress = $order->restaurant?->address ?? '';

    $orderDate = Carbon::parse($order['created_at'])
        ->locale(app()->getLocale())
        ->translatedFormat('d F Y - ' . config('timeformat'));

    $orderTypeLabels = [
        'delivery' => 'توصيل منازل',
        'take_away' => 'استلام من الفرع',
        'dine_in' => 'داخل الفرع',
        'parcel' => 'طرد',
    ];
    $orderTypeLabel = $orderTypeLabels[$order->order_type] ?? ucfirst(str_replace('_', ' ', $order->order_type));

    $paymentMethodLabel = $order['payment_method'] === 'cash_on_delivery'
        ? 'دفع نقدي عند التسليم'
        : translate(str_replace('_', ' ', $order['payment_method']));

    $isDeliveryOrder = !in_array($order->order_type, ['dine_in', 'take_away']);

    $addressBits = array_filter([
        !empty($address['house']) ? 'عمارة: ' . $address['house'] : null,
        !empty($address['floor']) ? 'دور: ' . $address['floor'] : null,
        !empty($address['road']) ? 'شارع: ' . $address['road'] : null,
    ]);
    $addressNote = !empty($addressBits) ? implode(' - ', $addressBits) : ($address['address_type'] ?? 'لا توجد ملاحظات إضافية');

    $subTotal = 0;
    $addOnsCost = 0;

    foreach ($order->details as $detail) {
        if ($detail->food_id || $detail->campaign == null) {
            $subTotal += $detail['price'] * $detail['quantity'];
        } elseif ($detail->campaign) {
            $subTotal += $detail['price'] * $detail['quantity'];
        }

        foreach ((json_decode($detail['add_ons'], true) ?? []) as $addon) {
            $addOnsCost += (($addon['price'] ?? 0) * ($addon['quantity'] ?? 0));
        }
    }

    $summarySubTotal = $subTotal + $addOnsCost;
    $showAdditionalCharge = Helpers::get_business_data('additional_charge_status') == 1 || $order['additional_charge'] > 0;
    $additionalChargeName = Helpers::get_business_data('additional_charge_name') ?? translate('messages.additional_charge');
    $couponLabel = $order->coupon_code ? 'خصم القسيمة (' . $order->coupon_code . ')' : 'خصم القسيمة';
@endphp

<div class="content container-fluid bj-invoice-page new-invoice">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="bj-invoice-toolbar non-printable">
                <input type="button" class="btn bj-invoice-toolbar__btn bj-invoice-toolbar__btn--primary print-Div"
                    value="{{ translate('messages.Proceed_If_thermal_printer_is_ready.') }}" />
                <a href="{{ url()->previous() }}" class="btn bj-invoice-toolbar__btn bj-invoice-toolbar__btn--ghost">
                    {{ translate('messages.back') }}
                </a>
            </div>

            <div id="printableArea">
                <style>
                    @font-face {
                        font-family: "Thmanyah Sans";
                        src: url('{{ dynamicAsset("public/assets/admin/fonts/thmanyahsans-Regular.otf") }}') format("opentype");
                        font-weight: 400;
                        font-style: normal;
                    }

                    @font-face {
                        font-family: "Thmanyah Sans";
                        src: url('{{ dynamicAsset("public/assets/admin/fonts/thmanyahsans-Medium.otf") }}') format("opentype");
                        font-weight: 500;
                        font-style: normal;
                    }

                    @font-face {
                        font-family: "Thmanyah Sans";
                        src: url('{{ dynamicAsset("public/assets/admin/fonts/thmanyahsans-Bold.otf") }}') format("opentype");
                        font-weight: 700;
                        font-style: normal;
                    }

                    @font-face {
                        font-family: "Thmanyah Sans";
                        src: url('{{ dynamicAsset("public/assets/admin/fonts/thmanyahsans-Black.otf") }}') format("opentype");
                        font-weight: 900;
                        font-style: normal;
                    }

                    .bj-invoice-toolbar {
                        display: flex;
                        justify-content: center;
                        gap: 12px;
                        flex-wrap: wrap;
                        margin: 18px 0 22px;
                    }

                    .bj-invoice-toolbar__btn {
                        border: 1px solid #d9c8a2;
                        border-radius: 999px;
                        min-height: 48px;
                        padding: 0 22px;
                        font-family: "Tajawal", "Thmanyah Sans", sans-serif;
                        font-size: 14px;
                        font-weight: 700;
                        box-shadow: none !important;
                    }

                    .bj-invoice-toolbar__btn--primary {
                        background: #1b2e5e;
                        border-color: #1b2e5e;
                        color: #fff !important;
                    }

                    .bj-invoice-toolbar__btn--ghost {
                        background: #fff;
                        color: #2d2418 !important;
                    }

                    .bj-invoice-sheet {
                        direction: rtl;
                        --invoice-ink: #161615;
                        --invoice-ink-soft: #686156;
                        --invoice-accent: #c89b52;
                        --invoice-accent-soft: #fbf1df;
                        --invoice-border: #eadfc9;
                        --invoice-bg: #fffdfa;
                        max-width: 730px;
                        margin: 0 auto 28px;
                        padding: 42px 44px 34px;
                        background: var(--invoice-bg);
                        color: var(--invoice-ink);
                        border: 1px solid var(--invoice-border);
                        border-radius: 34px;
                        box-shadow: 0 26px 70px rgba(45, 36, 24, 0.08);
                        font-family: "Tajawal", "Thmanyah Sans", sans-serif;
                        font-variant-numeric: tabular-nums lining-nums;
                        -webkit-print-color-adjust: exact;
                        print-color-adjust: exact;
                    }

                    .bj-invoice-sheet *,
                    .bj-invoice-sheet *::before,
                    .bj-invoice-sheet *::after {
                        box-sizing: border-box;
                    }

                    .bj-invoice-sheet h1,
                    .bj-invoice-sheet h2,
                    .bj-invoice-sheet h3,
                    .bj-invoice-sheet h4,
                    .bj-invoice-sheet h5,
                    .bj-invoice-sheet p {
                        margin: 0;
                    }

                    .bj-invoice-sheet img {
                        max-width: 100%;
                        display: block;
                    }

                    .bj-invoice-sheet a {
                        color: inherit;
                        text-decoration: none;
                    }

                    .bj-invoice-header {
                        text-align: center;
                        display: grid;
                        gap: 10px;
                        padding-bottom: 8px;
                    }

                    .bj-invoice-logo {
                        width: 150px;
                        margin: 0 auto 6px;
                    }

                    .bj-invoice-brand {
                        display: none;
                    }

                    .bj-invoice-branch {
                        font-size: 30px;
                        line-height: 1.2;
                        font-weight: 800;
                    }

                    .bj-invoice-branch-meta {
                        display: grid;
                        gap: 5px;
                        color: var(--invoice-ink-soft);
                        font-size: 16px;
                        font-weight: 600;
                        line-height: 1.65;
                    }

                    .bj-invoice-divider {
                        border: 0;
                        border-top: 2px dashed #1b1a18;
                        margin: 22px 0;
                    }

                    .bj-invoice-grid-two {
                        display: grid;
                        grid-template-columns: repeat(2, minmax(0, 1fr));
                        gap: 24px;
                    }

                    .bj-invoice-label {
                        display: block;
                        color: var(--invoice-ink-soft);
                        font-size: 15px;
                        font-weight: 700;
                        margin-bottom: 10px;
                    }

                    .bj-invoice-value {
                        display: block;
                        font-size: 29px;
                        line-height: 1.18;
                        font-weight: 900;
                        word-break: break-word;
                        letter-spacing: -0.03em;
                    }

                    .bj-invoice-value--order {
                        font-size: 54px;
                    }

                    .bj-invoice-value--type {
                        font-size: 30px;
                    }

                    .bj-invoice-block {
                        display: grid;
                        gap: 8px;
                    }

                    .bj-invoice-block--compact {
                        gap: 7px;
                    }

                    .bj-invoice-text {
                        font-size: 20px;
                        font-weight: 700;
                        line-height: 1.7;
                        word-break: break-word;
                    }

                    .bj-invoice-text--muted {
                        color: var(--invoice-ink-soft);
                    }

                    .bj-invoice-note-box {
                        margin-top: 12px;
                        padding: 14px 18px;
                        border: 2px solid #1d1c18;
                        border-radius: 18px;
                        background: #fff;
                        font-size: 18px;
                        font-weight: 700;
                        line-height: 1.7;
                    }

                    .bj-invoice-section-title {
                        color: var(--invoice-ink-soft);
                        font-size: 17px;
                        font-weight: 800;
                        margin-bottom: 14px;
                    }

                    .bj-invoice-items {
                        width: 100%;
                        border-collapse: collapse;
                    }

                    .bj-invoice-items thead th {
                        padding: 0 10px 14px;
                        border-bottom: 2px solid #1d1b18;
                        color: var(--invoice-ink-soft);
                        font-size: 15px;
                        font-weight: 800;
                        text-align: right;
                    }

                    .bj-invoice-items thead th:first-child,
                    .bj-invoice-items tbody td:first-child {
                        padding-right: 0;
                    }

                    .bj-invoice-items thead th:last-child,
                    .bj-invoice-items tbody td:last-child {
                        padding-left: 0;
                        text-align: left;
                    }

                    .bj-invoice-items tbody td {
                        padding: 18px 10px;
                        vertical-align: top;
                        border-bottom: 2px dashed #24211b;
                        font-size: 15px;
                    }

                    .bj-invoice-items tbody tr:last-child td {
                        border-bottom: 0;
                    }

                    .bj-invoice-qty {
                        white-space: nowrap;
                        font-size: 18px;
                        font-weight: 900;
                    }

                    .bj-invoice-item-name {
                        font-size: 20px;
                        font-weight: 900;
                        line-height: 1.45;
                    }

                    .bj-invoice-item-meta {
                        margin-top: 7px;
                        color: var(--invoice-ink-soft);
                        font-size: 15px;
                        font-weight: 600;
                        line-height: 1.75;
                    }

                    .bj-invoice-item-meta strong {
                        color: var(--invoice-ink);
                        font-weight: 800;
                    }

                    .bj-invoice-price {
                        white-space: nowrap;
                        font-size: 18px;
                        font-weight: 900;
                    }

                    .bj-invoice-summary {
                        display: grid;
                        gap: 16px;
                    }

                    .bj-invoice-summary-row {
                        display: flex;
                        align-items: flex-start;
                        justify-content: space-between;
                        gap: 16px;
                        font-size: 19px;
                        font-weight: 800;
                    }

                    .bj-invoice-summary-row span:first-child {
                        color: var(--invoice-ink);
                    }

                    .bj-invoice-summary-row span:last-child {
                        white-space: nowrap;
                        text-align: left;
                    }

                    .bj-invoice-summary-row--muted {
                        font-size: 18px;
                    }

                    .bj-invoice-summary-row--muted span:first-child {
                        color: #494338;
                    }

                    .bj-invoice-total-box {
                        margin-top: 6px;
                        border: 3px solid #171612;
                        border-radius: 22px;
                        padding: 22px 24px 20px;
                        background: #fff;
                        text-align: center;
                    }

                    .bj-invoice-total-box .bj-invoice-label {
                        margin-bottom: 8px;
                        color: var(--invoice-ink);
                    }

                    .bj-invoice-total-amount {
                        font-size: 58px;
                        font-weight: 900;
                        line-height: 1.06;
                        letter-spacing: -0.02em;
                    }

                    .bj-invoice-total-box .bj-invoice-divider {
                        margin: 16px 0 12px;
                    }

                    .bj-invoice-payment-note {
                        font-size: 22px;
                        font-weight: 800;
                        line-height: 1.7;
                    }

                    .bj-invoice-adjustment {
                        margin-top: 12px;
                        display: grid;
                        gap: 8px;
                    }

                    .bj-invoice-footer {
                        text-align: center;
                        display: grid;
                        gap: 12px;
                    }

                    .bj-invoice-thanks {
                        font-size: 52px;
                        font-weight: 900;
                        line-height: 1.05;
                        letter-spacing: -0.03em;
                    }

                    .bj-invoice-footer-copy {
                        font-size: 18px;
                        font-weight: 700;
                        color: #4f493f;
                        line-height: 1.8;
                    }

                    .bj-invoice-footer-copy--strong {
                        color: var(--invoice-ink);
                        font-weight: 900;
                    }

                    @media (max-width: 767px) {
                        .bj-invoice-sheet {
                            padding: 28px 18px 24px;
                            border-radius: 22px;
                        }

                        .bj-invoice-grid-two {
                            grid-template-columns: 1fr;
                            gap: 14px;
                        }

                        .bj-invoice-logo {
                            width: 126px;
                        }

                        .bj-invoice-branch {
                            font-size: 24px;
                        }

                        .bj-invoice-value {
                            font-size: 24px;
                        }

                        .bj-invoice-value--order {
                            font-size: 38px;
                        }

                        .bj-invoice-text,
                        .bj-invoice-summary-row,
                        .bj-invoice-payment-note {
                            font-size: 17px;
                        }

                        .bj-invoice-total-amount,
                        .bj-invoice-thanks {
                            font-size: 36px;
                        }

                        .bj-invoice-items thead th,
                        .bj-invoice-items tbody td {
                            padding-right: 6px;
                            padding-left: 6px;
                        }
                    }

                    @media print {
                        body {
                            background: #fff !important;
                            margin: 0 !important;
                            padding: 0 !important;
                        }

                        .bj-invoice-sheet {
                            max-width: none;
                            margin: 0;
                            border: 0;
                            border-radius: 0;
                            box-shadow: none;
                            padding: 18px 20px 14px;
                            background: #fff;
                        }

                        .non-printable {
                            display: none !important;
                        }
                    }
                </style>

                <div class="initial-38-1 bj-invoice-sheet" dir="rtl">
                    <header class="bj-invoice-header">
                        <img src="{{ dynamicAsset('public/assets/admin/css/images/beit_no_bg.png') }}" alt="Beit Jedi logo" class="bj-invoice-logo">
                        <div class="bj-invoice-brand">{{ $businessName }}</div>
                        <div class="bj-invoice-branch">{{ $branchName }}</div>
                        <div class="bj-invoice-branch-meta">
                            @if($branchPhone)
                                <span>هاتف الفرع: {{ $branchPhone }}</span>
                            @endif
                            @if($branchAddress)
                                <span>{{ $branchAddress }}</span>
                            @endif
                            <span>{{ $orderDate }}</span>
                        </div>
                    </header>

                    <hr class="bj-invoice-divider">

                    <section class="bj-invoice-grid-two">
                        <div class="bj-invoice-block">
                            <span class="bj-invoice-label">رقم الطلب</span>
                            <span class="bj-invoice-value bj-invoice-value--order">#{{ $order['id'] }}</span>
                        </div>
                        <div class="bj-invoice-block">
                            <span class="bj-invoice-label">النوع</span>
                            <span class="bj-invoice-value bj-invoice-value--type">{{ $orderTypeLabel }}</span>
                        </div>
                    </section>

                    <hr class="bj-invoice-divider">

                    <section class="bj-invoice-grid-two">
                        <div class="bj-invoice-block">
                            <span class="bj-invoice-label">العميل</span>
                            <span class="bj-invoice-value">{{ $customerName }}</span>
                            @if($customerPhone)
                                <span class="bj-invoice-text">{{ $customerPhone }}</span>
                            @endif
                        </div>
                        <div class="bj-invoice-block bj-invoice-block--compact">
                            @if($isDeliveryOrder)
                                <span class="bj-invoice-label">عنوان التسليم</span>
                                <span class="bj-invoice-value">{{ $address['address'] ?? 'غير متوفر' }}</span>
                                <span class="bj-invoice-text bj-invoice-text--muted">{{ $address['address_type'] ?? 'Delivery' }}</span>
                                <div class="bj-invoice-note-box">{{ $addressNote }}</div>
                            @else
                                <span class="bj-invoice-label">تفاصيل الطلب</span>
                                @if($order->order_type === 'dine_in' && $order?->OrderReference?->table_number)
                                    <span class="bj-invoice-text">رقم الطاولة: {{ $order?->OrderReference?->table_number }}</span>
                                @endif
                                @if($order->order_type === 'dine_in' && $order?->OrderReference?->token_number)
                                    <span class="bj-invoice-text">رقم التوكن: {{ $order?->OrderReference?->token_number }}</span>
                                @endif
                                @if(!$order?->OrderReference?->table_number && !$order?->OrderReference?->token_number)
                                    <span class="bj-invoice-text bj-invoice-text--muted">لا توجد تفاصيل إضافية</span>
                                @endif
                            @endif
                        </div>
                    </section>

                    <hr class="bj-invoice-divider">

                    <section>
                        <div class="bj-invoice-section-title">الأصناف</div>
                        <table class="bj-invoice-items">
                            <thead>
                                <tr>
                                    <th>الكمية</th>
                                    <th>الصنف</th>
                                    <th>السعر</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->details as $detail)
                                    @php
                                        $itemName = $detail->campaign ? $detail->campaign['title'] : (json_decode($detail->food_details, true)['name'] ?? '');
                                        $amount = $detail['price'] * $detail['quantity'];
                                        $variations = json_decode($detail['variation'], true) ?? [];
                                        $addons = json_decode($detail['add_ons'], true) ?? [];
                                    @endphp
                                    <tr>
                                        <td class="bj-invoice-qty">x{{ $detail['quantity'] }}</td>
                                        <td>
                                            <div class="bj-invoice-item-name">{{ $itemName }}</div>

                                            @if (count($variations) > 0)
                                                <div class="bj-invoice-item-meta">
                                                    <strong>الاختيارات:</strong>
                                                    @foreach($variations as $variation)
                                                        @if(isset($variation['name']) && isset($variation['values']))
                                                            <div>
                                                                {{ $variation['name'] }}:
                                                                @foreach ($variation['values'] as $value)
                                                                    <span>{{ $value['label'] }} ({{ Helpers::format_currency($value['optionPrice']) }})@if(!$loop->last)، @endif</span>
                                                                @endforeach
                                                            </div>
                                                        @elseif(isset($variations[0]))
                                                            @foreach($variations[0] as $key1 => $value1)
                                                                <div>{{ $key1 }}: <span>{{ $value1 }}</span></div>
                                                            @endforeach
                                                            @break
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="bj-invoice-item-meta">السعر للوحدة: {{ Helpers::format_currency($detail->price) }}</div>
                                            @endif

                                            @if(count($addons) > 0)
                                                <div class="bj-invoice-item-meta">
                                                    <strong>الإضافات:</strong>
                                                    @foreach($addons as $addon)
                                                        <div>{{ $addon['name'] }} - {{ $addon['quantity'] }} x {{ Helpers::format_currency($addon['price']) }}</div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                        <td class="bj-invoice-price">{{ Helpers::format_currency($amount) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </section>

                    <hr class="bj-invoice-divider">

                    <section class="bj-invoice-summary">
                        <div class="bj-invoice-summary-row">
                            <span>سعر الأصناف</span>
                            <span>{{ Helpers::format_currency($subTotal) }}</span>
                        </div>

                        <div class="bj-invoice-summary-row">
                            <span>الإضافات</span>
                            <span>{{ Helpers::format_currency($addOnsCost) }}</span>
                        </div>

                        <hr class="bj-invoice-divider">

                        <div class="bj-invoice-summary-row">
                            <span>المجموع الفرعي</span>
                            <span>{{ Helpers::format_currency($summarySubTotal) }}</span>
                        </div>

                        @if ($order['restaurant_discount_amount'] > 0)
                            <div class="bj-invoice-summary-row bj-invoice-summary-row--muted">
                                <span>خصم المطعم</span>
                                <span>- {{ Helpers::format_currency($order['restaurant_discount_amount']) }}</span>
                            </div>
                        @endif

                        @if ($order['coupon_discount_amount'] > 0)
                            <div class="bj-invoice-summary-row bj-invoice-summary-row--muted">
                                <span>{{ $couponLabel }}</span>
                                <span>- {{ Helpers::format_currency($order['coupon_discount_amount']) }}</span>
                            </div>
                        @endif

                        @if ($order['ref_bonus_amount'] > 0)
                            <div class="bj-invoice-summary-row bj-invoice-summary-row--muted">
                                <span>خصم الإحالة</span>
                                <span>- {{ Helpers::format_currency($order['ref_bonus_amount']) }}</span>
                            </div>
                        @endif

                        @if ($order->tax_status == 'excluded' || $order->tax_status == null)
                            <div class="bj-invoice-summary-row bj-invoice-summary-row--muted">
                                <span>ضريبة القيمة المضافة</span>
                                <span>{{ Helpers::format_currency($order['total_tax_amount']) }}</span>
                            </div>
                        @endif

                        @if ($order['dm_tips'] > 0)
                            <div class="bj-invoice-summary-row bj-invoice-summary-row--muted">
                                <span>إكرامية مندوب التوصيل</span>
                                <span>{{ Helpers::format_currency($order['dm_tips']) }}</span>
                            </div>
                        @endif

                        <div class="bj-invoice-summary-row bj-invoice-summary-row--muted">
                            <span>رسوم التوصيل</span>
                            <span>{{ Helpers::format_currency($order['delivery_charge']) }}</span>
                        </div>

                        @if ($showAdditionalCharge)
                            <div class="bj-invoice-summary-row bj-invoice-summary-row--muted">
                                <span>{{ $additionalChargeName }}</span>
                                <span>{{ Helpers::format_currency($order['additional_charge']) }}</span>
                            </div>
                        @endif

                        @if ($order['extra_packaging_amount'] > 0)
                            <div class="bj-invoice-summary-row bj-invoice-summary-row--muted">
                                <span>رسوم التغليف الإضافية</span>
                                <span>{{ Helpers::format_currency($order['extra_packaging_amount']) }}</span>
                            </div>
                        @endif

                        @if ($order?->payments)
                            @foreach ($order->payments as $payment)
                                <div class="bj-invoice-summary-row bj-invoice-summary-row--muted">
                                    <span>
                                        @if ($payment->payment_status == 'paid')
                                            {{ $payment->payment_method == 'cash_on_delivery' ? 'مدفوع نقداً' : 'مدفوع بواسطة ' . translate($payment->payment_method) }}
                                        @else
                                            {{ 'المبلغ المستحق - ' . ($payment->payment_method == 'cash_on_delivery' ? translate('messages.COD') : translate($payment->payment_method)) }}
                                        @endif
                                    </span>
                                    <span>{{ Helpers::format_currency($payment->amount) }}</span>
                                </div>
                            @endforeach
                        @endif

                        <div class="bj-invoice-total-box">
                            <span class="bj-invoice-label">الإجمالي المستحق</span>
                            <div class="bj-invoice-total-amount">{{ Helpers::format_currency($order['order_amount']) }}</div>
                            <hr class="bj-invoice-divider">
                            <div class="bj-invoice-payment-note">
                                {{ $paymentMethodLabel }}
                            </div>

                            @if ($order->adjusment > $order->order_amount)
                                <div class="bj-invoice-adjustment">
                                    <div class="bj-invoice-summary-row bj-invoice-summary-row--muted">
                                        <span>المبلغ المدفوع</span>
                                        <span>{{ Helpers::format_currency($order->adjusment) }}</span>
                                    </div>
                                    <div class="bj-invoice-summary-row bj-invoice-summary-row--muted">
                                        <span>الباقي</span>
                                        <span>{{ Helpers::format_currency($order->adjusment - $order->order_amount) }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </section>

                    <hr class="bj-invoice-divider">

                    <footer class="bj-invoice-footer">
                        <div class="bj-invoice-thanks">شكراً لك</div>
                        <div class="bj-invoice-footer-copy">لطلب الطعام من {{ $businessName }}</div>
                        <hr class="bj-invoice-divider">
                        <div class="bj-invoice-footer-copy">© 2026 Beit Jedi. كل الحق محجوز</div>
                        <div class="bj-invoice-footer-copy bj-invoice-footer-copy--strong">POWERED BY HAMDIES SOLUTIONS©</div>
                    </footer>
                </div>
            </div>
        </div>
    </div>
</div>
