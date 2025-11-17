<!-- Header -->
<div class="card-header bg-white border-0 px-4 pt-4 pb-0">
    <div class="d-flex justify-content-between align-items-center w-100">
        <div class="d-flex align-items-center">
            <div class="top-selling-icon-wrapper mr-3">
                <i class="tio-trending-up" style="color: #2C3E6F; font-size: 20px;"></i>
            </div>
            <div>
                <h5 class="mb-1 top-selling-title">الأكثر مبيعاً</h5>
                <p class="mb-0 top-selling-subtitle">حسب عدد الطلبات على أطباق المطعم</p>
            </div>
        </div>
    </div>
</div>
<!-- End Header -->

<!-- Body -->
<div class="card-body px-4 pb-4 pt-3">
    <style>
        .top-selling-icon-wrapper {
            width: 40px;
            height: 40px;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(44, 62, 111, 0.08) 0%, rgba(244, 208, 63, 0.16) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .top-selling-title {
            font-weight: 700;
            font-size: 1.05rem;
            letter-spacing: -0.2px;
        }
        .top-selling-subtitle {
            font-size: 0.8rem;
            color: #9ca3af;
        }
        .top-selling-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 1rem;
        }
        .top-selling-card-modern {
            border-radius: 18px;
            background: #ffffff;
            border: 1px solid rgba(44, 62, 111, 0.06);
            padding: 0.9rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
        }
        .top-selling-card-modern:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(44, 62, 111, 0.14);
            border-color: rgba(44, 62, 111, 0.18);
        }
        .top-selling-image-wrapper {
            border-radius: 14px;
            overflow: hidden;
            position: relative;
        }
        .top-selling-image-wrapper img {
            width: 100%;
            height: 90px;
            object-fit: cover;
            display: block;
            transition: transform 0.4s ease;
        }
        .top-selling-card-modern:hover .top-selling-image-wrapper img {
            transform: scale(1.05);
        }
        .top-selling-sold-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 0.2rem 0.55rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.95);
            font-size: 0.7rem;
            font-weight: 600;
            color: #111827;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
        }
        .top-selling-sold-dot {
            width: 6px;
            height: 6px;
            border-radius: 999px;
            background: #2C3E6F;
        }
        .top-selling-name {
            margin-top: 0.75rem;
            font-size: 0.9rem;
            font-weight: 600;
            color: #111827;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .top-selling-meta {
            font-size: 0.75rem;
            color: #9ca3af;
            margin-top: 0.1rem;
        }
    </style>

    <div class="top-selling-grid">
        @foreach($top_sell as $key=>$item)
            <div class="top-selling-card-modern redirect-url" data-url="{{route('vendor.food.view',[$item['id']])}}">
                <div class="top-selling-image-wrapper">
                    <span class="top-selling-sold-badge">
                        <span class="top-selling-sold-dot"></span>
                        <i class="tio-shopping-basket-outlined" style="font-size: 0.8rem; color: #4b5563;"></i>
                        تم بيع {{$item['order_count']}}
                    </span>
                    <img class="onerror-image" src="{{ $item['image_full_url'] }}"
                         data-onerror-image="{{dynamicAsset('public/assets/admin/img/100x100/food.png')}}" alt="{{$item->name}} image">
                </div>
                <div>
                    <div class="top-selling-name" title="{{$item->name ?? 'طبق محذوف'}}">
                        {{Str::limit($item->name ?? 'طبق محذوف',28,'...')}}
                    </div>
                    <div class="top-selling-meta">
                        إجمالي الطلبات: {{$item['order_count']}}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
<!-- End Body -->
