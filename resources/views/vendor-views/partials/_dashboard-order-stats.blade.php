{{-- Order Status Tiles — urgency-tiered, not equal-weight --}}
@php
    $confirmedUrgent = ($data['confirmed'] ?? 0) > 0;
    $readyUrgent     = ($data['ready_for_delivery'] ?? 0) > 0;
@endphp

<div class="ops-status-tile ops-status-tile--confirmed {{ $confirmedUrgent ? 'ops-status-tile--urgent' : '' }}">
    @if($confirmedUrgent)
        <span class="ops-tile-indicator" style="animation: livePulse 1.2s ease-in-out infinite;"></span>
    @else
        <span class="ops-tile-indicator"></span>
    @endif
    <a href="{{ route('vendor.order.list', ['confirmed']) }}" style="text-decoration:none;color:inherit;display:contents;">
        <div class="ops-tile-count">{{ $data['confirmed'] ?? 0 }}</div>
        <div class="ops-tile-label">بانتظار التحضير</div>
    </a>
    <i class="tio-chevron-left ops-tile-arrow"></i>
</div>

<div class="ops-status-tile ops-status-tile--cooking">
    <span class="ops-tile-indicator"></span>
    <a href="{{ route('vendor.order.list', ['cooking']) }}" style="text-decoration:none;color:inherit;display:contents;">
        <div class="ops-tile-count">{{ $data['cooking'] ?? 0 }}</div>
        <div class="ops-tile-label">جاري التحضير</div>
    </a>
    <i class="tio-chevron-left ops-tile-arrow"></i>
</div>

<div class="ops-status-tile ops-status-tile--ready {{ $readyUrgent ? 'ops-status-tile--urgent' : '' }}" style="{{ $readyUrgent ? '--amber: oklch(60% 0.17 145); --amber-soft: oklch(94% 0.06 145);' : '' }}">
    <span class="ops-tile-indicator"></span>
    <a href="{{ route('vendor.order.list', ['ready_for_delivery']) }}" style="text-decoration:none;color:inherit;display:contents;">
        <div class="ops-tile-count">{{ $data['ready_for_delivery'] ?? 0 }}</div>
        <div class="ops-tile-label">جاهز للتسليم</div>
    </a>
    <i class="tio-chevron-left ops-tile-arrow"></i>
</div>

<div class="ops-status-tile ops-status-tile--onway">
    <span class="ops-tile-indicator"></span>
    <a href="{{ route('vendor.order.list', ['food_on_the_way']) }}" style="text-decoration:none;color:inherit;display:contents;">
        <div class="ops-tile-count">{{ $data['food_on_the_way'] ?? 0 }}</div>
        <div class="ops-tile-label">في الطريق</div>
    </a>
    <i class="tio-chevron-left ops-tile-arrow"></i>
</div>
