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

    // Address fields — parse road like "مدينتي/ مجموعة 14/ عمارة 40/شقة 43"
    $addrRaw     = $address['road'] ?? '';
    $addrHouse   = $address['house'] ?? '';
    $addrFloor   = $address['floor'] ?? '';

    // Extract named parts from road string
    $addrMadina  = '';
    $addrGroup   = '';
    $addrBuild   = '';
    $addrApt     = '';

    if ($addrRaw) {
        // Split on / or ،
        $parts = preg_split('/[\/،]+/', $addrRaw);
        foreach ($parts as $part) {
            $part = trim($part);
            if (preg_match('/مجموعة\s*(.*)/u', $part, $m))      $addrGroup  = trim($m[1]) ?: $part;
            elseif (preg_match('/عمارة\s*(.*)/u', $part, $m))   $addrBuild  = trim($m[1]) ?: $part;
            elseif (preg_match('/شقة\s*(.*)/u', $part, $m))     $addrApt    = trim($m[1]) ?: $part;
            elseif ($addrMadina === '')                          $addrMadina = $part;
        }
        // fallback: if nothing parsed, show raw
        if (!$addrGroup && !$addrBuild && !$addrApt) $addrMadina = $addrRaw;
    }

    // Use house/floor fields as fallback for build/floor
    if (!$addrBuild && $addrHouse) $addrBuild = $addrHouse;

    // Restaurant logo
    $restaurantLogo = $order->restaurant?->logo_full_url ?? null;

    // Currency helper — always display as ج.م
    $fmt = fn($val) => 'ج.م ' . number_format((float)$val, 2);

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
                <input type="button" id="bj-print-btn" class="btn bj-invoice-toolbar__btn bj-invoice-toolbar__btn--primary"
                    value="{{ translate('messages.Proceed_If_thermal_printer_is_ready.') }}" />
                <a href="{{ url()->previous() }}" class="btn bj-invoice-toolbar__btn bj-invoice-toolbar__btn--ghost">
                    {{ translate('messages.back') }}
                </a>
            </div>

            <script>
                document.getElementById('bj-print-btn').addEventListener('click', function () {
                    window.print();
                });

                // Auto-print when opened with ?print=1 (e.g. from the new-order modal).
                (function(){
                    var params = new URLSearchParams(window.location.search);
                    if (params.get('print') !== '1') return;
                    function doPrint(){
                        try { window.focus(); } catch(e){}
                        // Small delay so fonts/images settle before the print dialog.
                        setTimeout(function(){ window.print(); }, 400);
                    }
                    if (document.readyState === 'complete') {
                        doPrint();
                    } else {
                        window.addEventListener('load', doPrint);
                    }
                })();
            </script>

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
                        white-space: nowrap;
                        vertical-align: top;
                    }

                    .bj-invoice-items tbody td {
                        padding: 13px 8px;
                        vertical-align: top;
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
                        display: block;
                        margin-top: 3px;
                        font-size: 13px;
                        font-weight: 900;
                        color: #1a1a1a;
                        direction: ltr;
                        unicode-bidi: isolate;
                        text-align: left;
                    }

                    .bj-invoice-summary {
                        display: grid;
                        gap: 9px;
                    }

                    .bj-invoice-summary-row {
                        display: grid;
                        grid-template-columns: 1fr auto;
                        align-items: baseline;
                        gap: 6px;
                        font-size: 16px;
                        font-weight: 800;
                    }

                    .bj-invoice-summary-row span:last-child {
                        white-space: nowrap;
                        direction: ltr;
                        text-align: left;
                    }

                    .bj-invoice-summary-row--subdivider {
                        padding-top: 11px;
                        margin-top: 4px;
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

                    .bj-invoice-powered {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        gap: 5px;
                        color: #999;
                        font-size: 10px;
                        font-weight: 500;
                        letter-spacing: 0.03em;
                        margin-top: 2px;
                    }

                    .bj-invoice-powered strong {
                        color: #555;
                        font-weight: 700;
                        letter-spacing: 0.01em;
                    }

                    .bj-invoice-address-grid {
                        display: grid;
                        gap: 0;
                        border: 2px solid #1b1b1b;
                        border-radius: 14px;
                        overflow: hidden;
                        margin-top: 8px;
                    }

                    .bj-addr-row {
                        display: flex;
                        align-items: stretch;
                        border-bottom: 1px solid #d4cec7;
                    }

                    .bj-addr-row:last-child {
                        border-bottom: 0;
                    }

                    .bj-addr-key {
                        flex: 0 0 68px;
                        padding: 8px 8px;
                        background: #f5f0ea;
                        color: #5a564f;
                        font-size: 11px;
                        font-weight: 900;
                        border-left: 1px solid #d4cec7;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        text-align: center;
                    }

                    .bj-addr-val {
                        flex: 1;
                        padding: 8px 10px;
                        font-size: 14px;
                        font-weight: 900;
                        line-height: 1.4;
                        display: flex;
                        align-items: center;
                        word-break: break-word;
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
                        @page {
                            size: 80mm auto;
                            margin: 0;
                        }

                        * {
                            -webkit-print-color-adjust: exact !important;
                            print-color-adjust: exact !important;
                        }

                        /* ── Hide all admin chrome when printing ── */
                        body > *:not(script):not(#printableArea):not(.bj-print-host) {
                            display: none !important;
                        }
                        .bj-app-shell, .main, #content, aside, nav, header, footer,
                        .js-navbar-vertical-aside, .navbar, .footer, .modal, .toast,
                        #pre--loader, #loading {
                            display: none !important;
                            visibility: hidden !important;
                        }

                        html, body {
                            background: #fff !important;
                            margin: 0 !important;
                            padding: 0 !important;
                            width: 80mm !important;
                            min-height: 0 !important;
                            height: auto !important;
                            overflow: visible !important;
                        }

                        /* The invoice's outer wrappers from the layout need to collapse */
                        .content, .container-fluid, .row, .col-12,
                        .bj-invoice-page, .new-invoice {
                            display: block !important;
                            width: 80mm !important;
                            max-width: 80mm !important;
                            margin: 0 !important;
                            padding: 0 !important;
                            background: #fff !important;
                            overflow: visible !important;
                            float: none !important;
                            position: static !important;
                        }

                        /* Force the invoice to be the only thing on the page */
                        #printableArea {
                            display: block !important;
                            width: 80mm !important;
                            margin: 0 !important;
                            padding: 3mm 4mm !important;
                            background: #fff !important;
                            position: absolute !important;
                            top: 0 !important;
                            left: 0 !important;
                            right: auto !important;
                        }

                        .non-printable {
                            display: none !important;
                        }

                        .bj-invoice-sheet {
                            max-width: 100% !important;
                            width: 100% !important;
                            margin: 0 !important;
                            border: 0 !important;
                            border-radius: 0 !important;
                            box-shadow: none !important;
                            padding: 0 !important;
                            background: #fff !important;
                            overflow: visible !important;
                        }

                        .non-printable {
                            display: none !important;
                        }

                        /* ── Logo ── */
                        .bj-invoice-logo {
                            width: 48px !important;
                            height: auto !important;
                            display: block !important;
                            margin: 0 auto 3px !important;
                        }

                        /* ── Header ── */
                        .bj-invoice-header {
                            display: grid !important;
                            text-align: center !important;
                            gap: 2px !important;
                        }

                        .bj-invoice-branch {
                            font-size: 11pt !important;
                            display: block !important;
                        }

                        .bj-invoice-header-meta {
                            font-size: 7pt !important;
                            display: block !important;
                        }

                        /* ── Order / type ── */
                        .bj-invoice-value--order {
                            font-size: 16pt !important;
                        }

                        .bj-invoice-value--type {
                            font-size: 11pt !important;
                        }

                        .bj-invoice-grid-two {
                            display: grid !important;
                            grid-template-columns: repeat(2, 1fr) !important;
                            gap: 6px !important;
                        }

                        .bj-invoice-panel {
                            min-height: unset !important;
                        }

                        .bj-invoice-label {
                            font-size: 6.5pt !important;
                            margin-bottom: 1px !important;
                        }

                        /* ── Customer ── */
                        .bj-invoice-customer-name {
                            font-size: 11pt !important;
                        }

                        .bj-invoice-phone {
                            font-size: 10pt !important;
                            margin-top: 1px !important;
                        }

                        /* ── Address grid ── */
                        .bj-invoice-address-grid {
                            border: 1.5px solid #1b1b1b !important;
                            border-radius: 0 !important;
                            margin-top: 3px !important;
                        }

                        .bj-addr-key {
                            flex: 0 0 52px !important;
                            font-size: 7pt !important;
                            padding: 5px 4px !important;
                            background: #f0ece6 !important;
                            text-align: center !important;
                            justify-content: center !important;
                        }

                        .bj-addr-val {
                            font-size: 9pt !important;
                            font-weight: 900 !important;
                            padding: 5px 6px !important;
                        }

                        /* ── Items table ── */
                        .bj-invoice-section-title {
                            font-size: 7pt !important;
                            margin-bottom: 3px !important;
                        }

                        .bj-invoice-items {
                            width: 100% !important;
                            table-layout: fixed !important;
                        }

                        .bj-invoice-items thead th {
                            font-size: 7pt !important;
                            padding: 0 2px 5px !important;
                        }

                        .bj-invoice-items tbody td {
                            font-size: 8pt !important;
                            padding: 5px 2px !important;
                            word-wrap: break-word !important;
                            overflow-wrap: break-word !important;
                        }

                        .bj-invoice-item-name {
                            font-size: 8.5pt !important;
                        }

                        .bj-invoice-item-meta {
                            font-size: 7pt !important;
                        }

                        .bj-invoice-qty {
                            font-size: 8.5pt !important;
                        }

                        .bj-invoice-price {
                            font-size: 8pt !important;
                            margin-top: 2px !important;
                        }

                        /* ── Summary ── */
                        .bj-invoice-summary {
                            gap: 4px !important;
                            width: 100% !important;
                        }

                        .bj-invoice-summary-row {
                            display: flex !important;
                            flex-direction: row !important;
                            justify-content: space-between !important;
                            align-items: baseline !important;
                            gap: 6px !important;
                            font-size: 9pt !important;
                            width: 100% !important;
                        }

                        .bj-invoice-summary-row span:first-child {
                            flex: 1 1 auto !important;
                            text-align: right !important;
                            min-width: 0 !important;
                            overflow: hidden !important;
                            text-overflow: ellipsis !important;
                        }

                        .bj-invoice-summary-row span:last-child {
                            flex: 0 0 auto !important;
                            white-space: nowrap !important;
                            direction: ltr !important;
                            unicode-bidi: isolate !important;
                            text-align: left !important;
                            font-weight: 900 !important;
                        }

                        .bj-invoice-summary-row--muted {
                            font-size: 8.5pt !important;
                        }

                        .bj-invoice-summary-row--subdivider {
                            padding-top: 5px !important;
                            margin-top: 2px !important;
                            border-top: 0 !important;
                        }

                        /* ── Total box ── */
                        .bj-invoice-total-box {
                            border-radius: 0 !important;
                            border: 2px solid #111 !important;
                            padding: 6px 8px 8px !important;
                        }

                        .bj-invoice-total-title {
                            font-size: 8pt !important;
                        }

                        .bj-invoice-total-amount {
                            font-size: 20pt !important;
                        }

                        .bj-invoice-payment-note {
                            font-size: 8pt !important;
                        }

                        /* ── Footer ── */
                        .bj-invoice-footer {
                            gap: 3px !important;
                        }

                        .bj-invoice-thanks {
                            font-size: 11pt !important;
                        }

                        /* ── Dividers ── */
                        .bj-invoice-divider {
                            margin: 6px 0 !important;
                        }

                        /* ── Force all text to full black for thermal ── */
                        .bj-invoice-sheet,
                        .bj-invoice-sheet * {
                            color: #000 !important;
                        }

                        /* ── Powered by — fix direction for thermal LTR rendering ── */
                        .bj-invoice-powered {
                            direction: ltr !important;
                            unicode-bidi: embed !important;
                            font-size: 7pt !important;
                            letter-spacing: 0.02em !important;
                            color: #000 !important;
                            margin-top: 4px !important;
                        }

                        .bj-invoice-powered span,
                        .bj-invoice-powered strong {
                            color: #000 !important;
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
                        @if($isDeliveryOrder)
                            <div class="bj-invoice-address-grid">
                                @if($addrMadina)
                                    <div class="bj-addr-row">
                                        <span class="bj-addr-key">المدينة</span>
                                        <span class="bj-addr-val">{{ $addrMadina }}</span>
                                    </div>
                                @endif
                                @if($addrGroup)
                                    <div class="bj-addr-row">
                                        <span class="bj-addr-key">المجموعة</span>
                                        <span class="bj-addr-val">{{ $addrGroup }}</span>
                                    </div>
                                @endif
                                @if($addrBuild)
                                    <div class="bj-addr-row">
                                        <span class="bj-addr-key">العمارة</span>
                                        <span class="bj-addr-val">{{ $addrBuild }}</span>
                                    </div>
                                @endif
                                @if($addrApt)
                                    <div class="bj-addr-row">
                                        <span class="bj-addr-key">الشقة</span>
                                        <span class="bj-addr-val">{{ $addrApt }}</span>
                                    </div>
                                @endif
                                @if($addrFloor)
                                    <div class="bj-addr-row">
                                        <span class="bj-addr-key">الطابق</span>
                                        <span class="bj-addr-val">{{ $addrFloor }}</span>
                                    </div>
                                @endif
                                @if(!$addrMadina && !$addrGroup && !$addrBuild && !$addrApt && !$addrFloor)
                                    <div class="bj-addr-row">
                                        <span class="bj-addr-val">غير متوفر</span>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="bj-invoice-address-main">{{ $branchName }}</div>
                            <div class="bj-invoice-address-sub">
                                @if($order->order_type === 'dine_in' && $order?->OrderReference?->table_number)
                                    رقم الطاولة: {{ $order?->OrderReference?->table_number }}
                                @elseif($order->order_type === 'dine_in' && $order?->OrderReference?->token_number)
                                    رقم التوكن: {{ $order?->OrderReference?->token_number }}
                                @else
                                    {{ $branchPhone ?: 'استلام من الفرع' }}
                                @endif
                            </div>
                        @endif
                    </section>

                    <hr class="bj-invoice-divider">

                    <section>
                        <div class="bj-invoice-section-title">الأصناف</div>
                        <table class="bj-invoice-items">
                            <thead>
                                <tr>
                                    <th style="width:30px">الكمية</th>
                                    <th>الصنف / السعر</th>
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
                                                        <div>{{ $addon['name'] }} {{ $addon['quantity'] }}x — {{ $fmt($addon['price']) }}</div>
                                                    @endforeach
                                                </div>
                                            @endif
                                            <div class="bj-invoice-price">{{ $fmt($amount) }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </section>

                    <hr class="bj-invoice-divider">

                    <section class="bj-invoice-summary">
                        <div class="bj-invoice-summary-row">
                            <span>سعر الأصناف</span>
                            <span>{{ $fmt($subTotal) }}</span>
                        </div>

                        <div class="bj-invoice-summary-row">
                            <span>الإضافات</span>
                            <span>{{ $fmt($addOnsCost) }}</span>
                        </div>

                        <div class="bj-invoice-summary-row bj-invoice-summary-row--subdivider">
                            <span>المجموع الفرعي</span>
                            <span>{{ $fmt($summarySubTotal) }}</span>
                        </div>

                        @if ($order['restaurant_discount_amount'] > 0)
                            <div class="bj-invoice-summary-row bj-invoice-summary-row--muted">
                                <span>التخفيض</span>
                                <span>- {{ $fmt($order['restaurant_discount_amount']) }}</span>
                            </div>
                        @endif

                        @if ($order['coupon_discount_amount'] > 0)
                            <div class="bj-invoice-summary-row bj-invoice-summary-row--muted">
                                <span>خصم القسيمة</span>
                                <span>- {{ $fmt($order['coupon_discount_amount']) }}</span>
                            </div>
                        @endif

                        @if ($order['ref_bonus_amount'] > 0)
                            <div class="bj-invoice-summary-row bj-invoice-summary-row--muted">
                                <span>خصم الإحالة</span>
                                <span>- {{ $fmt($order['ref_bonus_amount']) }}</span>
                            </div>
                        @endif

                        @if ($order->tax_status == 'excluded' || $order->tax_status == null)
                            <div class="bj-invoice-summary-row bj-invoice-summary-row--muted">
                                <span>ضريبة القيمة المضافة</span>
                                <span>{{ $fmt($order['total_tax_amount']) }}</span>
                            </div>
                        @endif

                        <div class="bj-invoice-summary-row bj-invoice-summary-row--muted">
                            <span>رسوم التوصيل</span>
                            <span>{{ $fmt($order['delivery_charge']) }}</span>
                        </div>

                        @if ($order['dm_tips'] > 0)
                            <div class="bj-invoice-summary-row bj-invoice-summary-row--muted">
                                <span>إكرامية المندوب</span>
                                <span>{{ $fmt($order['dm_tips']) }}</span>
                            </div>
                        @endif

                        @if ($order['additional_charge'] > 0)
                            <div class="bj-invoice-summary-row bj-invoice-summary-row--muted">
                                <span>{{ Helpers::get_business_data('additional_charge_name') ?? translate('messages.additional_charge') }}</span>
                                <span>{{ $fmt($order['additional_charge']) }}</span>
                            </div>
                        @endif

                        @if ($order['extra_packaging_amount'] > 0)
                            <div class="bj-invoice-summary-row bj-invoice-summary-row--muted">
                                <span>رسوم تغليف إضافية</span>
                                <span>{{ $fmt($order['extra_packaging_amount']) }}</span>
                            </div>
                        @endif

                        <div class="bj-invoice-total-box">
                            <div class="bj-invoice-total-title">الإجمالي المستحق</div>
                            <div class="bj-invoice-total-amount">{{ $fmt($order['order_amount']) }}</div>
                            <hr class="bj-invoice-divider">
                            <div class="bj-invoice-payment-note">
                                {{ $order['payment_method'] === 'cash_on_delivery' ? 'دفع نقدي عند التسليم — يُستلم من العميل' : translate(str_replace('_', ' ', $order['payment_method'])) }}
                            </div>
                        </div>
                    </section>

                    <hr class="bj-invoice-divider">

                    <footer class="bj-invoice-footer">
                        <div class="bj-invoice-thanks">حمّل التطبيق · خصومات حصرية</div>
                        <div class="bj-invoice-powered" dir="ltr">
                            <span>Powered by</span> <strong>Hamdies Solutions</strong>
                        </div>
                    </footer>
                </div>
            </div>
        </div>
    </div>
</div>
