<!-- Header -->
<div class="card-header bg-white border-0 px-4 pt-4 pb-0">
    <div class="d-flex justify-content-between align-items-center w-100">
        <div class="d-flex align-items-center">
            <div class="top-rated-icon-wrapper mr-3">
                <i class="tio-star-outlined" style="color: #F4B400; font-size: 20px;"></i>
            </div>
            <div>
                <h5 class="mb-1 top-rated-title">الأعلى تقييماً</h5>
                <p class="mb-0 top-rated-subtitle">اعتماداً على تقييمات وتجارب العملاء</p>
            </div>
        </div>
    </div>
</div>
<!-- End Header -->

<!-- Body -->
<div class="card-body px-4 pb-4 pt-3">
    <style>
        .top-rated-icon-wrapper {
            width: 40px;
            height: 40px;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(244, 180, 0, 0.16) 0%, rgba(44, 62, 111, 0.08) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .top-rated-title {
            font-weight: 700;
            font-size: 1.05rem;
            letter-spacing: -0.2px;
        }
        .top-rated-subtitle {
            font-size: 0.8rem;
            color: #9ca3af;
        }
        .top-rated-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 1rem;
        }
        .top-rated-card-modern {
            border-radius: 18px;
            background: #ffffff;
            border: 1px solid rgba(44, 62, 111, 0.06);
            padding: 0.9rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
            text-decoration: none;
        }
        .top-rated-card-modern:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(44, 62, 111, 0.14);
            border-color: rgba(44, 62, 111, 0.18);
            text-decoration: none;
        }
        .top-rated-image-wrapper {
            border-radius: 14px;
            overflow: hidden;
            position: relative;
        }
        .top-rated-image-wrapper img {
            width: 100%;
            height: 90px;
            object-fit: cover;
            display: block;
            transition: transform 0.4s ease;
        }
        .top-rated-card-modern:hover .top-rated-image-wrapper img {
            transform: scale(1.05);
        }
        .top-rated-rating-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 0.2rem 0.6rem;
            border-radius: 999px;
            background: rgba(17, 24, 39, 0.9);
            font-size: 0.7rem;
            font-weight: 600;
            color: #FBBF24;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
        }
        .top-rated-rating-badge i {
            font-size: 0.8rem;
        }
        .top-rated-name {
            margin-top: 0.75rem;
            font-size: 0.9rem;
            font-weight: 600;
            color: #111827;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .top-rated-meta {
            font-size: 0.75rem;
            color: #9ca3af;
            margin-top: 0.1rem;
        }
    </style>

    <div class="top-rated-grid">
        @foreach($most_rated_foods as $key=>$item)
            <a href="{{route('vendor.food.view',[$item['id']])}}" class="top-rated-card-modern">
                <div class="top-rated-image-wrapper">
                    <span class="top-rated-rating-badge">
                        <i class="tio-star"></i>
                        {{round($item['avg_rating'],1)}}
                    </span>
                    <img class="onerror-image" src="{{ $item['image_full_url'] }}"
                         data-onerror-image="{{dynamicAsset('public/assets/admin/img/100x100/2.png')}}" alt="{{$item->name}} image">
                </div>
                <div>
                    <div class="top-rated-name" title="{{$item->name ?? 'طبق محذوف'}}">
                        {{Str::limit($item->name ?? 'طبق محذوف',28,'...')}}
                    </div>
                    <div class="top-rated-meta">
                        {{$item['rating_count']}} مراجعة
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
<!-- End Body -->
