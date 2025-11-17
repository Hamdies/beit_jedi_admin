<style>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

.stat-card-wrapper {
    animation: fadeInUp 0.6s ease-out forwards;
    opacity: 0;
}

.stat-card-wrapper:nth-child(1) { animation-delay: 0.1s; }
.stat-card-wrapper:nth-child(2) { animation-delay: 0.2s; }
.stat-card-wrapper:nth-child(3) { animation-delay: 0.3s; }
.stat-card-wrapper:nth-child(4) { animation-delay: 0.4s; }

.ultra-modern-stat-card {
    display: block;
    padding: 2rem;
    background: white;
    border-radius: 20px;
    border: 1px solid rgba(44, 62, 111, 0.08);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    position: relative;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

.ultra-modern-stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(44, 62, 111, 0.03) 0%, rgba(244, 208, 63, 0.03) 100%);
    opacity: 0;
    transition: opacity 0.4s ease;
}

.ultra-modern-stat-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(44, 62, 111, 0.12);
    text-decoration: none;
    border-color: rgba(44, 62, 111, 0.15);
}

.ultra-modern-stat-card:hover::before {
    opacity: 1;
}

.ultra-modern-stat-card:hover .stat-icon-modern {
    transform: scale(1.1) rotate(5deg);
}

.stat-icon-modern {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
    font-size: 32px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    z-index: 1;
}

.stat-icon-modern i {
    color: white;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
}

.stat-number-modern {
    font-size: 2.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, #2C3E6F 0%, #3d5278 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin: 0.5rem 0;
    line-height: 1.2;
    position: relative;
    z-index: 1;
}

.stat-label-modern {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 600;
    letter-spacing: 0.3px;
    position: relative;
    z-index: 1;
}

.bg-navy { background: linear-gradient(135deg, #2C3E6F 0%, #3d5278 100%); }
.bg-gold { background: linear-gradient(135deg, #F4D03F 0%, #f5e17d 100%); }
.bg-navy-light { background: linear-gradient(135deg, #5a6c8f 0%, #7a8ba8 100%); }
.bg-navy-dark { background: linear-gradient(135deg, #1f2d4d 0%, #2C3E6F 100%); }
</style>

<div class="col-xl-3 col-sm-6 stat-card-wrapper">
    <a class="ultra-modern-stat-card" href="{{route('vendor.order.list',['confirmed'])}}">
        <div class="stat-icon-modern bg-navy">
            <i class="tio-checkmark-circle-outlined"></i>
        </div>
        <h4 class="stat-number-modern">{{$data['confirmed']}}</h4>
        <span class="stat-label-modern">طلبات مؤكدة</span>
    </a>
</div>

<div class="col-xl-3 col-sm-6 stat-card-wrapper">
    <a class="ultra-modern-stat-card" href="{{route('vendor.order.list',['cooking'])}}">
        <div class="stat-icon-modern bg-gold">
            <i class="tio-restaurant"></i>
        </div>
        <h4 class="stat-number-modern">{{$data['cooking']}}</h4>
        <span class="stat-label-modern">جاري التجهيز</span>
    </a>
</div>

<div class="col-xl-3 col-sm-6 stat-card-wrapper">
    <a class="ultra-modern-stat-card" href="{{route('vendor.order.list',['ready_for_delivery'])}}">
        <div class="stat-icon-modern bg-navy-light">
            <i class="tio-done"></i>
        </div>
        <h4 class="stat-number-modern">{{$data['ready_for_delivery']}}</h4>
        <span class="stat-label-modern">جاهز للتسليم</span>
    </a>
</div>

<div class="col-xl-3 col-sm-6 stat-card-wrapper">
    <a class="ultra-modern-stat-card" href="{{route('vendor.order.list',['food_on_the_way'])}}">
        <div class="stat-icon-modern bg-navy-dark">
            <i class="tio-delivery"></i>
        </div>
        <h4 class="stat-number-modern">{{$data['food_on_the_way']}}</h4>
        <span class="stat-label-modern">الطلب في الطريق</span>
    </a>
</div>

<style>
.mini-card-wrapper {
    animation: fadeInUp 0.6s ease-out forwards;
    opacity: 0;
}

.mini-card-wrapper:nth-child(1) { animation-delay: 0.5s; }
.mini-card-wrapper:nth-child(2) { animation-delay: 0.6s; }
.mini-card-wrapper:nth-child(3) { animation-delay: 0.7s; }
.mini-card-wrapper:nth-child(4) { animation-delay: 0.8s; }

.ultra-modern-mini-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.5rem;
    background: white;
    border-radius: 16px;
    border: 1px solid rgba(44, 62, 111, 0.06);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    height: 100%;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
}

.ultra-modern-mini-card:hover {
    transform: translateX(4px);
    box-shadow: 0 8px 16px rgba(44, 62, 111, 0.08);
    border-color: rgba(44, 62, 111, 0.12);
    text-decoration: none;
}

.mini-card-content {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.mini-icon-wrapper {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: linear-gradient(135deg, rgba(44, 62, 111, 0.08) 0%, rgba(244, 208, 63, 0.08) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: #2C3E6F;
    transition: all 0.3s ease;
}

.ultra-modern-mini-card:hover .mini-icon-wrapper {
    background: linear-gradient(135deg, #2C3E6F 0%, #3d5278 100%);
    color: white;
    transform: rotate(10deg);
}

.mini-card-info {
    display: flex;
    flex-direction: column;
}

.mini-card-label-new {
    font-size: 0.75rem;
    color: #9ca3af;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.mini-card-number-new {
    font-size: 1.5rem;
    font-weight: 800;
    color: #2C3E6F;
    margin: 0;
    line-height: 1;
}

.mini-arrow {
    font-size: 18px;
    color: #d1d5db;
    transition: all 0.3s ease;
}

.ultra-modern-mini-card:hover .mini-arrow {
    color: #2C3E6F;
    transform: translateX(4px);
}
</style>

<div class="col-12 mt-4">
    <div class="row g-3">
        <div class="col-sm-6 col-lg-3 mini-card-wrapper">
            <a href="{{route('vendor.order.list',['delivered'])}}" class="ultra-modern-mini-card">
                <div class="mini-card-content">
                    <div class="mini-icon-wrapper">
                        <i class="tio-checkmark-circle"></i>
                    </div>
                    <div class="mini-card-info">
                        <span class="mini-card-label-new">تم التسليم</span>
                        <h3 class="mini-card-number-new">{{$data['delivered']}}</h3>
                    </div>
                </div>
                <i class="tio-chevron-right mini-arrow"></i>
            </a>
        </div>

        <div class="col-sm-6 col-lg-3 mini-card-wrapper">
            <a href="{{route('vendor.order.list',['refunded'])}}" class="ultra-modern-mini-card">
                <div class="mini-card-content">
                    <div class="mini-icon-wrapper">
                        <i class="tio-money"></i>
                    </div>
                    <div class="mini-card-info">
                        <span class="mini-card-label-new">مبالغ مستردة</span>
                        <h3 class="mini-card-number-new">{{$data['refunded']}}</h3>
                    </div>
                </div>
                <i class="tio-chevron-right mini-arrow"></i>
            </a>
        </div>

        <div class="col-sm-6 col-lg-3 mini-card-wrapper">
            <a href="{{route('vendor.order.list',['scheduled'])}}" class="ultra-modern-mini-card">
                <div class="mini-card-content">
                    <div class="mini-icon-wrapper">
                        <i class="tio-time"></i>
                    </div>
                    <div class="mini-card-info">
                        <span class="mini-card-label-new">طلبات مجدولة</span>
                        <h3 class="mini-card-number-new">{{$data['scheduled']}}</h3>
                    </div>
                </div>
                <i class="tio-chevron-right mini-arrow"></i>
            </a>
        </div>

        <div class="col-sm-6 col-lg-3 mini-card-wrapper">
            <a href="{{route('vendor.order.list',['all'])}}" class="ultra-modern-mini-card">
                <div class="mini-card-content">
                    <div class="mini-icon-wrapper">
                        <i class="tio-receipt"></i>
                    </div>
                    <div class="mini-card-info">
                        <span class="mini-card-label-new">كل الطلبات</span>
                        <h3 class="mini-card-number-new">{{$data['all']}}</h3>
                    </div>
                </div>
                <i class="tio-chevron-right mini-arrow"></i>
            </a>
        </div>
    </div>
</div>
