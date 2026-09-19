@php
    // Brand wordmark is part of the landing design (Arabic lockup), so it is not
    // driven by the business_name setting — that still holds the StackFood default.
    $businessName = 'بيت جدي';
    $landing_page_links = \App\Models\BusinessSetting::where(['key' => 'landing_page_links'])->first();
    $landing_page_links = isset($landing_page_links->value) ? json_decode($landing_page_links->value, true) : null;
    // Real Google Play listing. The admin setting only wins when it has been
    // pointed somewhere other than the seeded https://play.google.com placeholder.
    $beitJediPlayStore = 'https://play.google.com/store/apps/details?id=com.hamdies.beit.jedi';
    $configuredAndroid = $landing_page_links['app_url_android'] ?? \App\CentralLogics\Helpers::get_settings('app_url_android');
    $configuredAndroid = is_string($configuredAndroid) ? trim($configuredAndroid) : '';
    $isPlaceholder = $configuredAndroid === '' || rtrim($configuredAndroid, '/') === 'https://play.google.com';
    $playStoreLink = $isPlaceholder ? $beitJediPlayStore : $configuredAndroid;
    // Cache-buster: filemtime changes whenever the stylesheet is edited, so browsers
    // never pair new markup with a stale cached stylesheet.
    $cssPath = public_path('assets/landing/beitjedi/landing.css');
    $assetVersion = file_exists($cssPath) ? filemtime($cssPath) : '1';
    $icon = \App\CentralLogics\Helpers::get_settings('icon');
@endphp
<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $businessName }} — التطبيق</title>
<meta name="description" content="شامي، يمني، إيطالي — كله من مطبخ واحد. حمّل تطبيق {{ $businessName }} واطلب بضغطة.">
<link rel="shortcut icon" type="image/x-icon" href="{{ dynamicStorage('storage/app/public/business/'. ($icon ?? '')) }}">
<link rel="canonical" href="{{ url('/') }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $businessName }}">
<meta property="og:title" content="{{ $businessName }} — شامي، يمني، إيطالي، كله من مطبخ واحد">
<meta property="og:description" content="شاورما ومندي وباستا وبرجر — طلب واحد، فاتورة واحدة، وتوصيل واحد يوصلك سخن. حمّل التطبيق.">
<meta property="og:url" content="{{ url('/') }}">
<meta property="og:locale" content="ar_EG">
<meta property="og:image" content="{{ dynamicAsset('/public/assets/landing/beitjedi/share-card.jpg') }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="أطباق من مطبخ {{ $businessName }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $businessName }} — شامي، يمني، إيطالي، كله من مطبخ واحد">
<meta name="twitter:description" content="شاورما ومندي وباستا وبرجر — طلب واحد، فاتورة واحدة، وتوصيل واحد يوصلك سخن.">
<meta name="twitter:image" content="{{ dynamicAsset('/public/assets/landing/beitjedi/share-card.jpg') }}">
<link rel="stylesheet" href="{{ dynamicAsset('/public/assets/landing/beitjedi/landing.css') }}?v={{ $assetVersion }}">
</head>
<body>
<main>

<div class="top">
<div class="mast"><div class="w">
<div class="n">{{ $businessName }}</div>
<div class="est">الطعم الدمشقي</div>
<div class="sp"></div>
<a class="dl" href="{{ $playStoreLink }}" target="_blank" rel="noopener"><svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3.6 2.3c-.3.3-.5.8-.5 1.4v16.6c0 .6.2 1.1.5 1.4l.1.1 9.3-9.3v-.2L3.6 2.3zM16.3 15.6l-3.1-3.1v-.2l3.1-3.1.1.1 3.7 2.1c1 .6 1 1.6 0 2.2l-3.8 2zM15.9 16l-3.2-3.2-9.1 9.1c.3.4.9.4 1.6.1L15.9 16M15.9 8L5.2 2c-.7-.4-1.3-.3-1.6.1l9.1 9.1L15.9 8z"/></svg> حمّل التطبيق</a>
</div></div>

<header class="hero"><div class="w">
<div>
<div class="kicker">التطبيق متاح على أندرويد</div>
<h1>شامي، يمني، إيطالي<span class="l2">كله من مطبخ واحد</span></h1>
<p class="sub">أصلنا دمشقي، بس المنيو مش واقف عنده. شاورما ومندي وباستا وبرجر — طلب واحد، فاتورة واحدة، وتوصيل واحد يوصلك سخن.</p>
<div class="get" id="get">
<a class="play" href="{{ $playStoreLink }}" target="_blank" rel="noopener"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3.6 2.3c-.3.3-.5.8-.5 1.4v16.6c0 .6.2 1.1.5 1.4l.1.1 9.3-9.3v-.2L3.6 2.3zM16.3 15.6l-3.1-3.1v-.2l3.1-3.1.1.1 3.7 2.1c1 .6 1 1.6 0 2.2l-3.8 2zM15.9 16l-3.2-3.2-9.1 9.1c.3.4.9.4 1.6.1L15.9 16M15.9 8L5.2 2c-.7-.4-1.3-.3-1.6.1l9.1 9.1L15.9 8z"/></svg><span class="t"><i>حمّل من</i><b>Google Play</b></span></a>
</div>
</div>
<div class="dev">
<div class="phone"><div class="notch"></div><div class="screen"><img src="{{ dynamicAsset('/public/assets/landing/beitjedi/app-hero.webp') }}" alt="لقطة شاشة من تطبيق {{ $businessName }}" width="640" height="1081" loading="eager" fetchpriority="high"></div></div>
<div class="shadow"></div>
<img class="dish d1" src="{{ dynamicAsset('/public/assets/landing/beitjedi/dish-mandi.webp') }}" alt="" aria-hidden="true" width="300" height="300" loading="lazy" decoding="async">
<img class="dish d2" src="{{ dynamicAsset('/public/assets/landing/beitjedi/dish-pasta.webp') }}" alt="" aria-hidden="true" width="300" height="300" loading="lazy" decoding="async">
<img class="dish d3" src="{{ dynamicAsset('/public/assets/landing/beitjedi/dish-grill.webp') }}" alt="" aria-hidden="true" width="300" height="300" loading="lazy" decoding="async">
<img class="dish d4" src="{{ dynamicAsset('/public/assets/landing/beitjedi/dish-shawarma.webp') }}" alt="" aria-hidden="true" width="300" height="300" loading="lazy" decoding="async">
</div>
</div>
<div class="ticker"><div class="rail">
<span>شاورما دمشقي</span><i></i><span>مندي يمني</span><i></i><span>باستا إيطالي</span><i></i><span>برجر وستيك</span><i></i><span>مشاوي وكبة</span><i></i>
<span>شاورما دمشقي</span><i></i><span>مندي يمني</span><i></i><span>باستا إيطالي</span><i></i><span>برجر وستيك</span><i></i><span>مشاوي وكبة</span><i></i>
</div></div>
</header>
</div>

<section class="cuisine"><div class="w">
<div class="lead">أربع مطابخ · مكان واحد</div>
<ul>
<li><h2>شامي دمشقي</h2><p>شاورما وكبة ومشاوي، بالوصفة اللي جِينا بيها من الشام.</p></li>
<li><h2>مندي يمني</h2><p>لحم وفراخ تستوي بالساعات في الطابون على الفحم.</p></li>
<li><h2>إيطالي</h2><p>باستا وبيتزا بعجينة تتخمّر يوم كامل قبل الفرن.</p></li>
<li><h2>غربي</h2><p>برجر وستيك وفراخ مقرمشة — لما العيلة تختلف في الطلب.</p></li>
</ul>
</div></section>

<section class="facts"><div class="w">
<div class="f"><b>٣٠ د</b><span>متوسط وقت التوصيل</span></div>
<div class="f"><b>منذ ٢٠١٩</b><span>نطبخ بنفس الوصفة</span></div>
<div class="f"><b>١٢٤٠٠</b><span>طلب اتوصّل السنة دي</span></div>
</div></section>

<section class="steps"><div class="w">
<ol>
<li><span class="num">٠١</span><h2>منيو واحد لكل حاجة</h2><p>شاورما ومندي وباستا في نفس السلة. اللي خلص من المطبخ بيختفي فورًا — مش هتطلب حاجة مش موجودة.</p></li>
<li><span class="num">٠٢</span><h2>الطلب في ضغطة</h2><p>عنوانك وطريقة دفعك محفوظين، والكوبون بيتطبّق لوحده. من غير مكالمات ولا انتظار على الخط.</p></li>
<li><span class="num">٠٣</span><h2>تابعه لحد الباب</h2><p>تعرف طلبك على الفحم ولا مع الكابتن، ووقت الوصول بالدقيقة — ورقم الكابتن قدامك لو احتجت.</p></li>
</ol>
</div></section>

<section class="end"><div class="w"><div class="grid">
<div>
<h2>أول طلب <em>بخصم ١٠٪</em></h2>
<p>حمّل التطبيق واكتب الكود عند الدفع. صالح لحد آخر الشهر. متاح على أندرويد ٨ أو أحدث.</p>
<div class="code">أول_طلب</div>
</div>
<div class="get">
<a class="play" href="{{ $playStoreLink }}" target="_blank" rel="noopener"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3.6 2.3c-.3.3-.5.8-.5 1.4v16.6c0 .6.2 1.1.5 1.4l.1.1 9.3-9.3v-.2L3.6 2.3zM16.3 15.6l-3.1-3.1v-.2l3.1-3.1.1.1 3.7 2.1c1 .6 1 1.6 0 2.2l-3.8 2zM15.9 16l-3.2-3.2-9.1 9.1c.3.4.9.4 1.6.1L15.9 16M15.9 8L5.2 2c-.7-.4-1.3-.3-1.6.1l9.1 9.1L15.9 8z"/></svg><span class="t"><i>حمّل من</i><b>Google Play</b></span></a>
</div>
</div></div></section>

</main>

<footer class="site-footer"><div class="w">
<div>{{ $businessName }} © ٢٠٢٦</div>
<div class="ln"><a href="{{ route('contact-us') }}">تواصل معنا</a><a href="{{ route('terms-and-conditions') }}">الشروط</a><a href="{{ route('privacy-policy') }}">الخصوصية</a></div>
</div></footer>

<div class="dock" id="dock">
<div class="tx"><b>تطبيق {{ $businessName }}</b><span>أندرويد · مجانًا · أول طلب ١٠٪</span></div>
<a class="play" href="{{ $playStoreLink }}" target="_blank" rel="noopener"><svg width="19" height="19" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3.6 2.3c-.3.3-.5.8-.5 1.4v16.6c0 .6.2 1.1.5 1.4l.1.1 9.3-9.3v-.2L3.6 2.3zM16.3 15.6l-3.1-3.1v-.2l3.1-3.1.1.1 3.7 2.1c1 .6 1 1.6 0 2.2l-3.8 2zM15.9 16l-3.2-3.2-9.1 9.1c.3.4.9.4 1.6.1L15.9 16M15.9 8L5.2 2c-.7-.4-1.3-.3-1.6.1l9.1 9.1L15.9 8z"/></svg><span class="t"><b>حمّل</b></span></a>
</div>
<script>
(function(){var d=document.getElementById('dock'),g=document.getElementById('get'),e=document.querySelector('.end .get');
// Hide the dock while either CTA is on screen: it is redundant there, and at the
// foot of the page it would otherwise sit on top of the closing section and footer.
var vis={};
var io=new IntersectionObserver(function(es){
  es.forEach(function(x){vis[x.target===g?'g':'e']=x.isIntersecting});
  d.classList.toggle('on',!vis.g&&!vis.e&&window.scrollY>0);
},{threshold:0});
io.observe(g); if(e){io.observe(e)}})();
</script>
</body>
</html>
