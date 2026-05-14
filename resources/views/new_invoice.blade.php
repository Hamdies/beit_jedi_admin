@php
    use App\CentralLogics\Helpers;
    use App\Models\BusinessSetting;
    use Carbon\Carbon;

    $businessName = optional(BusinessSetting::where(['key' => 'business_name'])->first())->value ?? 'Beit Jedi';
    $address = $order->delivery_address ? json_decode($order->delivery_address, true) : [];

    $customerName = $address['contact_person_name'] ?? trim(($order?->customer?->f_name ?? '') . ' ' . ($order?->customer?->l_name ?? ''));
    $customerName = $customerName !== '' ? $customerName : translate('messages.walk_in_customer');
    $customerPhone = $address['contact_person_number'] ?? ($order?->customer?->phone ?? '');

    $branchName = $order->restaurant?->name ?? $businessName;
    $branchPhone = $order->restaurant?->phone ?? '';

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

    $isDeliveryOrder = !in_array($order->order_type, ['dine_in', 'take_away']);
    $streetArea = $address['address'] ?? 'غير متوفر';
    $buildingFloor = implode(' - ', array_filter([
        !empty($address['house']) ? 'عمارة ' . $address['house'] : null,
        !empty($address['floor']) ? 'الدور ' . $address['floor'] : null,
    ]));
    $buildingFloor = $buildingFloor ?: (!empty($address['road']) ? 'الشارع: ' . $address['road'] : 'لا توجد تفاصيل مبنى إضافية');
    $landmark = $address['road'] ?? ($address['address_type'] ?? 'لا توجد علامة مميزة');

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
                        border: 1px solid #171717;
                        border-radius: 999px;
                        min-height: 44px;
                        padding: 0 20px;
                        font-family: "Thmanyah Sans", sans-serif;
                        font-size: 13px;
                        font-weight: 800;
                        box-shadow: none !important;
                    }

                    .bj-invoice-toolbar__btn--primary {
                        background: #171717;
                        color: #fff !important;
                    }

                    .bj-invoice-toolbar__btn--ghost {
                        background: #fff;
                        color: #171717 !important;
                    }

                    .bj-invoice-sheet {
                        direction: rtl;
                        max-width: 560px;
                        margin: 0 auto 28px;
                        padding: 26px 24px 24px;
                        background: #fffdfa;
                        color: #121212;
                        border: 1px solid #efe8dd;
                        border-radius: 24px;
                        box-shadow: 0 18px 48px rgba(29, 25, 18, 0.08);
                        font-family: "Thmanyah Sans", sans-serif;
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

                    .bj-invoice-divider {
                        border: 0;
                        border-top: 2px dashed #1d1d1d;
                        margin: 16px 0;
                    }

                    .bj-invoice-header {
                        text-align: center;
                        display: grid;
                        gap: 5px;
                    }

                    .bj-invoice-logo {
                        width: 92px;
                        margin: 0 auto 4px;
                    }

                    .bj-invoice-branch {
                        font-size: 20px;
                        line-height: 1.2;
                        font-weight: 900;
                    }

                    .bj-invoice-header-meta {
                        color: #55504a;
                        font-size: 13px;
                        font-weight: 700;
                        line-height: 1.6;
                    }

                    .bj-invoice-grid-two {
                        display: grid;
                        grid-template-columns: repeat(2, minmax(0, 1fr));
                        gap: 14px;
                    }

                    .bj-invoice-panel {
                        min-height: 88px;
                    }

                    .bj-invoice-label {
                        display: block;
                        color: #666057;
                        font-size: 13px;
                        font-weight: 800;
                        margin-bottom: 6px;
                    }

                    .bj-invoice-value {
                        display: block;
                        font-weight: 900;
                        line-height: 1.15;
                        word-break: break-word;
                        letter-spacing: -0.02em;
                    }

                    .bj-invoice-value--order {
                        font-size: 30px;
                    }

                    .bj-invoice-value--type {
                        font-size: 25px;
                    }

                    .bj-invoice-customer-name {
                        font-size: 24px;
                        font-weight: 900;
                        line-height: 1.2;
                    }

                    .bj-invoice-phone {
                        direction: ltr;
                        unicode-bidi: bidi-override;
                        display: inline-block;
                        font-size: 22px;
                        font-weight: 900;
                        line-height: 1.2;
                        margin-top: 6px;
                    }

                    .bj-invoice-address-main {
                        font-size: 18px;
                        font-weight: 900;
                        line-height: 1.45;
                    }

                    .bj-invoice-address-sub {
                        margin-top: 6px;
                        color: #4f4b45;
                        font-size: 15px;
                        font-weight: 700;
                        line-height: 1.6;
                    }

                    .bj-invoice-note-box {
                        margin-top: 10px;
                        padding: 11px 14px;
                        border: 2px solid #151515;
                        border-radius: 14px;
                        background: #fff;
                    }

                    .bj-invoice-note-box .bj-invoice-label {
                        margin-bottom: 4px;
                    }

                    .bj-invoice-note-box-text {
                        font-size: 15px;
                        font-weight: 800;
                        line-height: 1.6;
                    }

                    .bj-invoice-section-title {
                        color: #666057;
                        font-size: 14px;
                        font-weight: 900;
                        margin-bottom: 10px;
                    }

                    .bj-invoice-items {
                        width: 100%;
                        border-collapse: collapse;
                    }

                    .bj-invoice-items thead th {
                        padding: 0 8px 10px;
                        border-bottom: 2px solid #1b1b1b;
                        color: #403c36;
                        font-size: 13px;
                        font-weight: 900;
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
                        padding: 13px 8px;
                        vertical-align: top;
                        border-bottom: 1px dashed #2a2722;
                        font-size: 13px;
                    }

                    .bj-invoice-items tbody tr:last-child td {
                        border-bottom: 0;
                    }

                    .bj-invoice-qty {
                        white-space: nowrap;
                        font-size: 16px;
                        font-weight: 900;
                    }

                    .bj-invoice-item-name {
                        font-size: 16px;
                        font-weight: 900;
                        line-height: 1.45;
                    }

                    .bj-invoice-item-meta {
                        margin-top: 4px;
                        color: #5c564f;
                        font-size: 12px;
                        font-weight: 700;
                        line-height: 1.7;
                    }

                    .bj-invoice-item-meta strong {
                        color: #1c1a18;
                    }

                    .bj-invoice-price {
                        white-space: nowrap;
                        font-size: 16px;
                        font-weight: 900;
                    }

                    .bj-invoice-summary {
                        display: grid;
                        gap: 9px;
                    }

                    .bj-invoice-summary-row {
                        display: flex;
                        align-items: flex-start;
                        justify-content: space-between;
                        gap: 16px;
                        font-size: 16px;
                        font-weight: 800;
                    }

                    .bj-invoice-summary-row span:last-child {
                        white-space: nowrap;
                        text-align: left;
                    }

                    .bj-invoice-summary-row--subdivider {
                        padding-top: 11px;
                        margin-top: 4px;
                        border-top: 2px dashed #1b1b1b;
                    }

                    .bj-invoice-summary-row--muted {
                        font-size: 15px;
                    }

                    .bj-invoice-summary-row--muted span:first-child {
                        color: #5a564f;
                    }

                    .bj-invoice-total-box {
                        margin-top: 6px;
                        border: 3px solid #111111;
                        border-radius: 16px;
                        padding: 14px 18px 16px;
                        background: #fff;
                        text-align: center;
                    }

                    .bj-invoice-total-title {
                        font-size: 14px;
                        font-weight: 900;
                        margin-bottom: 4px;
                    }

                    .bj-invoice-total-amount {
                        font-size: 44px;
                        font-weight: 900;
                        line-height: 1.05;
                        letter-spacing: -0.03em;
                    }

                    .bj-invoice-total-box .bj-invoice-divider {
                        margin: 12px 0 10px;
                    }

                    .bj-invoice-payment-note {
                        font-size: 16px;
                        font-weight: 900;
                        line-height: 1.65;
                    }

                    .bj-invoice-footer {
                        text-align: center;
                        display: grid;
                        gap: 8px;
                    }

                    .bj-invoice-thanks {
                        font-size: 28px;
                        font-weight: 900;
                        line-height: 1.1;
                    }

                    @media (max-width: 767px) {
                        .bj-invoice-sheet {
                            max-width: 100%;
                            padding: 22px 16px 18px;
                            border-radius: 18px;
                        }

                        .bj-invoice-grid-two {
                            grid-template-columns: 1fr;
                            gap: 12px;
                        }

                        .bj-invoice-value--order {
                            font-size: 28px;
                        }

                        .bj-invoice-value--type,
                        .bj-invoice-customer-name,
                        .bj-invoice-phone,
                        .bj-invoice-address-main {
                            font-size: 20px;
                        }

                        .bj-invoice-total-amount {
                            font-size: 38px;
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
                            padding: 12px 14px 10px;
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
                        <div class="bj-invoice-branch">{{ $branchName }}</div>
                        <div class="bj-invoice-header-meta">
                            @if($branchPhone)
                                <div>هاتف الفرع: {{ $branchPhone }}</div>
                            @endif
                            <div>{{ $orderDate }}</div>
                        </div>
                    </header>

                    <hr class="bj-invoice-divider">

                    <section class="bj-invoice-grid-two">
                        <div class="bj-invoice-panel">
                            <span class="bj-invoice-label">رقم الطلب</span>
                            <span class="bj-invoice-value bj-invoice-value--order">#{{ $order['id'] }}</span>
                        </div>
                        <div class="bj-invoice-panel">
                            <span class="bj-invoice-label">النوع</span>
                            <span class="bj-invoice-value bj-invoice-value--type">{{ $orderTypeLabel }}</span>
                        </div>
                    </section>

                    <hr class="bj-invoice-divider">

                    <section>
                        <span class="bj-invoice-label">العميل</span>
                        <div class="bj-invoice-customer-name">{{ $customerName }}</div>
                        @if($customerPhone)
                            <div class="bj-invoice-phone">{{ $customerPhone }}</div>
                        @endif
                    </section>

                    <hr class="bj-invoice-divider">

                    <section>
                        <span class="bj-invoice-label">عنوان التسليم</span>
                        <div class="bj-invoice-address-main">{{ $isDeliveryOrder ? $streetArea : $branchName }}</div>
                        <div class="bj-invoice-address-sub">
                            @if($isDeliveryOrder)
                                {{ $buildingFloor }}
                            @elseif($order->order_type === 'dine_in' && $order?->OrderReference?->table_number)
                                رقم الطاولة: {{ $order?->OrderReference?->table_number }}
                            @elseif($order->order_type === 'dine_in' && $order?->OrderReference?->token_number)
                                رقم التوكن: {{ $order?->OrderReference?->token_number }}
                            @else
                                {{ $branchPhone ?: 'استلام من الفرع' }}
                            @endif
                        </div>
                        <div class="bj-invoice-note-box">
                            <span class="bj-invoice-label">علامة مميزة</span>
                            <div class="bj-invoice-note-box-text">{{ $isDeliveryOrder ? $landmark : 'استلام مباشر من الفرع' }}</div>
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
                                        $addons = json_decode($detail['add_ons'], true) ?? [];
                                    @endphp
                                    <tr>
                                        <td class="bj-invoice-qty">x{{ $detail['quantity'] }}</td>
                                        <td>
                                            <div class="bj-invoice-item-name">{{ $itemName }}</div>
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

                        <div class="bj-invoice-summary-row bj-invoice-summary-row--subdivider">
                            <span>المجموع الفرعي</span>
                            <span>{{ Helpers::format_currency($summarySubTotal) }}</span>
                        </div>

                        @if ($order['restaurant_discount_amount'] > 0)
                            <div class="bj-invoice-summary-row bj-invoice-summary-row--muted">
                                <span>التخفيض</span>
                                <span>- {{ Helpers::format_currency($order['restaurant_discount_amount']) }}</span>
                            </div>
                        @endif

                        @if ($order['coupon_discount_amount'] > 0)
                            <div class="bj-invoice-summary-row bj-invoice-summary-row--muted">
                                <span>خصم القسيمة</span>
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

                        <div class="bj-invoice-summary-row bj-invoice-summary-row--muted">
                            <span>رسوم التوصيل</span>
                            <span>{{ Helpers::format_currency($order['delivery_charge']) }}</span>
                        </div>

                        @if ($order['dm_tips'] > 0)
                            <div class="bj-invoice-summary-row bj-invoice-summary-row--muted">
                                <span>إكرامية المندوب</span>
                                <span>{{ Helpers::format_currency($order['dm_tips']) }}</span>
                            </div>
                        @endif

                        @if ($order['additional_charge'] > 0)
                            <div class="bj-invoice-summary-row bj-invoice-summary-row--muted">
                                <span>{{ Helpers::get_business_data('additional_charge_name') ?? translate('messages.additional_charge') }}</span>
                                <span>{{ Helpers::format_currency($order['additional_charge']) }}</span>
                            </div>
                        @endif

                        @if ($order['extra_packaging_amount'] > 0)
                            <div class="bj-invoice-summary-row bj-invoice-summary-row--muted">
                                <span>رسوم تغليف إضافية</span>
                                <span>{{ Helpers::format_currency($order['extra_packaging_amount']) }}</span>
                            </div>
                        @endif

                        <div class="bj-invoice-total-box">
                            <div class="bj-invoice-total-title">الإجمالي المستحق</div>
                            <div class="bj-invoice-total-amount">{{ Helpers::format_currency($order['order_amount']) }}</div>
                            <hr class="bj-invoice-divider">
                            <div class="bj-invoice-payment-note">
                                {{ $order['payment_method'] === 'cash_on_delivery' ? 'دفع نقدي عند التسليم — يُستلم من العميل' : translate(str_replace('_', ' ', $order['payment_method'])) }}
                            </div>
                        </div>
                    </section>

                    <hr class="bj-invoice-divider">

                    <footer class="bj-invoice-footer">
                        <div class="bj-invoice-thanks">شكراً لك</div>
                        
                        <div>©POWERED BY HAMDIES SOLUTIONS</div>
                    </footer>
                </div>
            </div>
        </div>
    </div>
</div>
