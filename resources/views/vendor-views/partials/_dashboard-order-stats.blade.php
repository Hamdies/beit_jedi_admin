@php
    $hasConfirmed = ($data['confirmed'] ?? 0) > 0;
    $hasReady     = ($data['ready_for_delivery'] ?? 0) > 0;
@endphp

<a href="{{ route('vendor.order.list', ['confirmed']) }}"
   class="ops-tile ops-tile--confirmed {{ $hasConfirmed ? 'ops-tile--urgent' : '' }}">
    <span class="ops-tile-bar {{ $hasConfirmed ? 'ops-tile-urgent-dot' : '' }}"></span>
    <span class="ops-tile-num">{{ $data['confirmed'] ?? 0 }}</span>
    <span class="ops-tile-label">بانتظار التحضير</span>
</a>

<a href="{{ route('vendor.order.list', ['cooking']) }}"
   class="ops-tile ops-tile--cooking">
    <span class="ops-tile-bar"></span>
    <span class="ops-tile-num">{{ $data['cooking'] ?? 0 }}</span>
    <span class="ops-tile-label">جاري التحضير</span>
</a>

<a href="{{ route('vendor.order.list', ['ready_for_delivery']) }}"
   class="ops-tile ops-tile--ready {{ $hasReady ? 'ops-tile--urgent' : '' }}">
    <span class="ops-tile-bar"></span>
    <span class="ops-tile-num">{{ $data['ready_for_delivery'] ?? 0 }}</span>
    <span class="ops-tile-label">جاهز للتسليم</span>
</a>

<a href="{{ route('vendor.order.list', ['food_on_the_way']) }}"
   class="ops-tile ops-tile--onway">
    <span class="ops-tile-bar"></span>
    <span class="ops-tile-num">{{ $data['food_on_the_way'] ?? 0 }}</span>
    <span class="ops-tile-label">في الطريق</span>
</a>
