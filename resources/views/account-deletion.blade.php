@php
    // Linked from the Google Play Data safety form, so the page must name the app
    // exactly as the store listing does.
    $businessName = 'بيت جدي';
    $playStoreLink = 'https://play.google.com/store/apps/details?id=com.hamdies.beit.jedi';
    $supportEmail = \App\CentralLogics\Helpers::get_settings('email_address');
    $supportPhone = \App\CentralLogics\Helpers::get_settings('phone');
    $icon = \App\CentralLogics\Helpers::get_settings('icon');
    $mailto = 'mailto:' . $supportEmail . '?subject=' . rawurlencode('Beit Jedi account deletion - حذف حساب بيت جدي');
    $v = fn ($file) => file_exists(public_path('assets/landing/beitjedi/' . $file)) ? filemtime(public_path('assets/landing/beitjedi/' . $file)) : '1';
@endphp
<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>حذف الحساب — {{ $businessName }} | Delete your Beit Jedi account</title>
<meta name="description" content="طريقة حذف حسابك في تطبيق بيت جدي والبيانات المرتبطة به. How to delete your Beit Jedi app account and associated data.">
<link rel="shortcut icon" type="image/x-icon" href="{{ dynamicStorage('storage/app/public/business/'. ($icon ?? '')) }}">
<link rel="canonical" href="{{ route('account-deletion') }}">
<link rel="stylesheet" href="{{ dynamicAsset('/public/assets/landing/beitjedi/landing.css') }}?v={{ $v('landing.css') }}">
<link rel="stylesheet" href="{{ dynamicAsset('/public/assets/landing/beitjedi/account-deletion.css') }}?v={{ $v('account-deletion.css') }}">
</head>
<body>
<main>

<div class="top">
<div class="mast"><div class="w">
<a class="n" href="{{ url('/') }}">{{ $businessName }}</a>
<div class="est">الطعم الدمشقي</div>
<div class="sp"></div>
<a class="dl" href="{{ $playStoreLink }}" target="_blank" rel="noopener"><svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3.6 2.3c-.3.3-.5.8-.5 1.4v16.6c0 .6.2 1.1.5 1.4l.1.1 9.3-9.3v-.2L3.6 2.3zM16.3 15.6l-3.1-3.1v-.2l3.1-3.1.1.1 3.7 2.1c1 .6 1 1.6 0 2.2l-3.8 2zM15.9 16l-3.2-3.2-9.1 9.1c.3.4.9.4 1.6.1L15.9 16M15.9 8L5.2 2c-.7-.4-1.3-.3-1.6.1l9.1 9.1L15.9 8z"/></svg> حمّل التطبيق</a>
</div></div>

<header class="ad-hero"><div class="w">
<div class="kicker">الحساب والخصوصية</div>
<h1>حذف حسابك في <span class="l2">تطبيق بيت جدي</span></h1>
<p class="sub">تقدر تحذف حسابك في تطبيق بيت جدي (Beit Jedi) على Google Play وكل البيانات المرتبطة بيه في أي وقت — من التطبيق مباشرة، أو بطلب منّا لو مش قادر تدخل على حسابك.</p>
<a class="lang" href="#en" lang="en">English version ↓</a>
</div></header>
</div>

<section class="ad-body"><div class="w">

<div>
<div class="ad-sec">
<div class="tag">الطريقة ١ · من التطبيق</div>
<h2>احذف حسابك في أقل من دقيقة</h2>
<ol class="ad-steps">
<li><span class="num">٠١</span><div><b>افتح تطبيق بيت جدي</b><span>وسجّل دخول بالحساب اللي عايز تحذفه.</span></div></li>
<li><span class="num">٠٢</span><div><b>ادخل على «الملف الشخصي»</b><span>من القائمة السفلية في التطبيق.</span></div></li>
<li><span class="num">٠٣</span><div><b>اضغط «حذف الحساب» وأكّد</b><span>الحساب بيتحذف فورًا وبيتم تسجيل خروجك من كل الأجهزة.</span></div></li>
</ol>
<div class="ad-note"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg><div>مش هينفع تحذف الحساب وعندك طلب لسه شغّال. استنى لحد ما الطلب يوصل أو يتلغي، وبعدها احذف الحساب.</div></div>
</div>

<div class="ad-sec">
<div class="tag">الطريقة ٢ · من غير التطبيق</div>
<h2>اطلب الحذف بالإيميل أو التليفون</h2>
<p>لو مش قادر تدخل على التطبيق، ابعتلنا من الإيميل أو رقم الموبايل المسجّل في حسابك واكتب «حذف حساب بيت جدي». هنتأكد إن الحساب بتاعك ونحذفه خلال ٧ أيام عمل، ونبعتلك تأكيد لما الحذف يتم.</p>
</div>

<div class="ad-sec">
<div class="tag">بياناتك</div>
<h2>إيه اللي بيتحذف وإيه اللي بنحتفظ بيه</h2>
<div class="ad-data">
<div class="ad-card del">
<h3><i><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/></svg></i>بيتحذف نهائيًا</h3>
<ul>
<li>الاسم ورقم الموبايل والإيميل وصورة الحساب</li>
<li>العناوين المحفوظة</li>
<li>بيانات الدخول وإشعارات الجهاز</li>
<li>المفضلة والسلة وإعدادات الحساب</li>
</ul>
</div>
<div class="ad-card keep">
<h3><i><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18M8 14h8"/></svg></i>بنحتفظ بيه مؤقتًا</h3>
<ul>
<li>سجل الطلبات والفواتير</li>
<li>بيانات عمليات الدفع</li>
</ul>
<div class="period">لمدة تصل إلى ٥ سنوات، للالتزام بالمتطلبات القانونية والضريبية ومنع الاحتيال، وبعدها بتتحذف.</div>
</div>
</div>
</div>
</div>

<aside class="ad-aside">
<div class="tag">محتاج مساعدة؟</div>
<h2>فريق بيت جدي معاك</h2>
<p>ابعت طلب الحذف من البيانات المسجّلة في حسابك.</p>
<a class="ch" href="{{ $mailto }}"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/></svg><span><i>الإيميل</i><b dir="ltr">{{ $supportEmail }}</b></span></a>
<a class="ch" href="tel:{{ $supportPhone }}"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/></svg><span><i>التليفون</i><b dir="ltr">{{ $supportPhone }}</b></span></a>
<div class="sla">الحذف بيتم خلال ٧ أيام عمل</div>
</aside>

</div></section>

<section class="ad-body ad-en" id="en" dir="ltr" lang="en"><div class="w">
<div class="head">
<div class="tag">ACCOUNT &amp; PRIVACY</div>
<h2>Delete your Beit Jedi account</h2>
<p>Users of the <strong>Beit Jedi (بيت جدي)</strong> app on Google Play can delete their account and its associated data at any time, either in the app or by contacting us.</p>
</div>

<div class="ad-sec">
<div class="tag">OPTION 1 · IN THE APP</div>
<h2>Delete it yourself</h2>
<ol class="ad-steps">
<li><span class="num">01</span><div><b>Open the Beit Jedi app</b><span>Sign in to the account you want to delete.</span></div></li>
<li><span class="num">02</span><div><b>Go to Profile</b><span>From the bottom navigation bar.</span></div></li>
<li><span class="num">03</span><div><b>Tap “Delete Account” and confirm</b><span>The account is deleted immediately and you are signed out on all devices.</span></div></li>
</ol>
<div class="ad-note"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg><div>An account can’t be deleted while it has an order in progress. Wait until the order is delivered or cancelled, then delete the account.</div></div>
</div>

<div class="ad-sec">
<div class="tag">OPTION 2 · WITHOUT THE APP</div>
<h2>Request deletion by email or phone</h2>
<p>Email <a class="inl" href="{{ $mailto }}">{{ $supportEmail }}</a> or call <a class="inl" href="tel:{{ $supportPhone }}">{{ $supportPhone }}</a> from the email or phone number registered on your account, and say “Beit Jedi account deletion”. We verify the account is yours, delete it within 7 business days, and confirm once it’s done.</p>
</div>

<div class="ad-sec">
<div class="tag">YOUR DATA</div>
<h2>What’s deleted and what’s kept</h2>
<div class="ad-data">
<div class="ad-card del">
<h3><i><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/></svg></i>Permanently deleted</h3>
<ul>
<li>Name, phone number, email and profile photo</li>
<li>Saved addresses</li>
<li>Login credentials and device notification tokens</li>
<li>Wishlist, cart and account preferences</li>
</ul>
</div>
<div class="ad-card keep">
<h3><i><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18M8 14h8"/></svg></i>Retained temporarily</h3>
<ul>
<li>Order history and invoices</li>
<li>Payment transaction records</li>
</ul>
<div class="period">Kept for up to 5 years to meet legal, tax and fraud-prevention obligations, then deleted.</div>
</div>
</div>
</div>
</div></section>

</main>

<footer class="site-footer"><div class="w">
<div>{{ $businessName }} © {{ strtr(date('Y'), ['0'=>'٠','1'=>'١','2'=>'٢','3'=>'٣','4'=>'٤','5'=>'٥','6'=>'٦','7'=>'٧','8'=>'٨','9'=>'٩']) }}</div>
<div class="ln"><a href="{{ route('contact-us') }}">تواصل معنا</a><a href="{{ route('terms-and-conditions') }}">الشروط</a><a href="{{ route('privacy-policy') }}">الخصوصية</a></div>
</div></footer>
</body>
</html>
