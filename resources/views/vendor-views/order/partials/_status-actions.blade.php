{{-- Primary status-change CTA.
     Rendered inside .ov-actions-bar / .ov-action-chip--cta-wrap --}}

@php
    $orderTotalFormatted = \App\CentralLogics\Helpers::format_currency($order['order_amount']);
    $customerName = ($order->customer && $order->is_guest == 0)
        ? trim(($order->customer['f_name'] ?? '') . ' ' . ($order->customer['l_name'] ?? ''))
        : 'العميل';
    $deliverLabel = $order->order_type == 'dine_in' ? 'تأكيد الاكتمال' : 'تأكيد التوصيل';
    $deliverConfirm = $order->order_type == 'dine_in'
        ? "تأكيد اكتمال الطلب للعميل {$customerName} بإجمالي {$orderTotalFormatted}؟"
        : "تأكيد التوصيل للعميل {$customerName} بإجمالي {$orderTotalFormatted}؟";
@endphp

@if ($order['order_status'] == 'pending')
    <a class="ov-action-chip ov-action-chip--primary ov-action-primary order-status-change-alert"
        data-url="{{ route('vendor.order.status', ['id' => $order['id'], 'order_status' => 'confirmed']) }}"
        data-message="تأكيد الطلب #{{ $order['id'] }} للعميل {{ $customerName }}؟"
        href="javascript:">
        <i class="tio-checkmark-circle"></i> تأكيد الأمر
    </a>
    @if (config('canceled_by_restaurant'))
    <a class="ov-action-chip ov-action-cancel cancelled-status"
        href="javascript:"
        data-amount="{{ $order['order_amount'] }}"
        data-amount-formatted="{{ $orderTotalFormatted }}"
        data-customer="{{ $customerName }}"
        style="color:var(--red,#c8312b); border-color:rgba(200,49,43,.25);">
        <i class="tio-clear-circle"></i> إلغاء
    </a>
    @endif

@elseif (in_array($order['order_status'], ['confirmed','accepted']))
    <a class="ov-action-chip ov-action-chip--primary ov-action-primary order-status-change-alert"
        data-url="{{ route('vendor.order.status', ['id' => $order['id'], 'order_status' => 'processing']) }}"
        data-message="بدء طهي الطلب #{{ $order['id'] }}؟"
        data-verification="false"
        data-processing-time="{{ $max_processing_time }}"
        href="javascript:">
        <i class="tio-restaurant"></i> البدء في الطهي
    </a>

@elseif ($order['order_status'] == 'processing')
    <a class="ov-action-chip ov-action-chip--primary ov-action-primary order-status-change-alert"
        data-url="{{ route('vendor.order.status', ['id' => $order['id'], 'order_status' => 'handover']) }}"
        data-message="تجهيز الطلب #{{ $order['id'] }} للتسليم؟"
        href="javascript:">
        <i class="tio-checkmark"></i> جاهز للتسليم
    </a>

@elseif ($order['order_status'] == 'handover' && (in_array($order['order_type'],['dine_in','take_away']) || (($restaurant->restaurant_model == 'commission' && $restaurant->self_delivery_system) || ($restaurant->restaurant_model == 'subscription' && $restaurant?->restaurant_sub?->self_delivery == 1))))
    <a class="ov-action-chip ov-action-chip--primary ov-action-primary order-status-change-alert"
        data-url="{{ route('vendor.order.status', ['id' => $order['id'], 'order_status' => 'delivered']) }}"
        data-message="{{ $deliverConfirm }}"
        data-amount="{{ $order['order_amount'] }}"
        data-amount-formatted="{{ $orderTotalFormatted }}"
        data-customer="{{ $customerName }}"
        data-verification="{{ $order_delivery_verification ? 'true' : 'false' }}"
        href="javascript:">
        <i class="tio-checkmark-circle"></i> {{ $deliverLabel }}
    </a>
@endif
