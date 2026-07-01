@extends('layouts.vendor.app')

@section('title','قائمة التصنيفات')

@push('css_or_js')
<style>
/* ═══════════════════════════════════════════════
   BEIT JEDI — CATEGORIES PAGE (matches dashboard)
═══════════════════════════════════════════════ */
:root {
    --cream:            #faf7f2;
    --ivory:            #f5f1e8;
    --paper:            #ffffff;
    --hairline:         #ecdfc8;
    --hairline-soft:    #f0e8d6;
    --navy-900:         #0f1a3a;
    --navy-800:         #1a2447;
    --navy-700:         #2a3560;
    --navy-100:         #e7eaf3;
    --gold-500:         #dbbf86;
    --gold-600:         #c9a96b;
    --gold-700:         #a88944;
    --ink-900:          #1a1a24;
    --ink-700:          #3d3d4f;
    --ink-500:          #6d6d80;
    --ink-400:          #9a9aab;
    --s-ready-bg:       #d9efdb;
    --s-ready-fg:       #2e6b35;
    --s-ready-dot:      #5fb46b;
    --danger-bg:        #fde2e0;
    --danger-fg:        #a02921;
    --danger-dot:       #d9534f;
    --r-sm:  10px;
    --r-md:  14px;
    --r-lg:  20px;
    --r-xl:  28px;
    --shadow-sm: 0 1px 2px rgba(20,30,60,.04);
    --shadow-md: 0 4px 18px -8px rgba(20,30,60,.10), 0 1px 3px rgba(20,30,60,.04);
    --shadow-lg: 0 18px 48px -24px rgba(20,30,60,.18), 0 2px 6px rgba(20,30,60,.05);
    --font-ar: 'Cairo', -apple-system, BlinkMacSystemFont, sans-serif;
}

.bj-cats, .bj-cats * { box-sizing: border-box; }
.bj-cats {
    background: var(--cream);
    direction: rtl;
    font-family: var(--font-ar);
    color: var(--ink-900);
    padding: 24px 32px 40px;
    display: flex;
    flex-direction: column;
    gap: 18px;
    min-height: 100vh;
}

/* ── HEADER ─────────────────────────────────── */
.bj-cats-header {
    background: var(--paper);
    border: 1px solid var(--hairline);
    border-radius: var(--r-lg);
    padding: 18px 22px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: var(--shadow-sm);
    flex-wrap: wrap;
}
.bj-cats-header .ico {
    width: 52px; height: 52px;
    background: var(--navy-800);
    color: var(--gold-600);
    border-radius: var(--r-md);
    display: flex; align-items: center; justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
}
.bj-cats-header .text { flex: 1; min-width: 200px; }
.bj-cats-header .text h2 {
    font-size: 1.25rem; font-weight: 800; color: var(--ink-900);
    margin: 0 0 4px;
}
.bj-cats-header .text p {
    font-size: .85rem; color: var(--ink-400);
    margin: 0; font-weight: 500;
}
.bj-cats-header .pill {
    display: inline-flex; align-items: center; gap: 6px;
    background: var(--ivory);
    border: 1px solid var(--hairline);
    color: var(--ink-700);
    border-radius: 999px;
    padding: 7px 14px;
    font-size: .82rem; font-weight: 700;
    white-space: nowrap;
}
.bj-cats-header .pill .count {
    background: var(--navy-800);
    color: var(--gold-600);
    border-radius: 999px;
    padding: 1px 9px;
    font-size: .78rem; font-weight: 800;
    min-width: 24px; text-align: center;
}

/* ── BUTTONS ────────────────────────────────── */
.bj-btn {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: .85rem; font-weight: 700;
    padding: 9px 16px;
    border-radius: var(--r-sm);
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none;
    font-family: var(--font-ar);
    white-space: nowrap;
    transition: all .13s;
    line-height: 1;
}
.bj-btn.primary {
    background: var(--navy-800); color: #fff; border-color: var(--navy-800);
}
.bj-btn.primary:hover { background: var(--navy-700); color:#fff; text-decoration:none; }
.bj-btn.ghost {
    background: var(--paper); color: var(--ink-700); border-color: var(--hairline);
}
.bj-btn.ghost:hover { background: var(--ivory); color: var(--ink-900); text-decoration:none; }
.bj-btn.gold {
    background: linear-gradient(135deg, var(--gold-600), var(--gold-700));
    color: #fff; border-color: var(--gold-700);
}
.bj-btn.gold:hover { filter: brightness(1.05); color:#fff; text-decoration:none; }

/* ── TOOLBAR (search + add) ─────────────────── */
.bj-toolbar {
    background: var(--paper);
    border: 1px solid var(--hairline);
    border-radius: var(--r-lg);
    padding: 14px 18px;
    display: flex; align-items: center; gap: 12px;
    box-shadow: var(--shadow-sm);
    flex-wrap: wrap;
}
.bj-search {
    flex: 1; min-width: 260px;
    display: flex; align-items: center;
    background: var(--ivory);
    border: 1px solid var(--hairline-soft);
    border-radius: 999px;
    padding: 0 6px 0 16px;
}
.bj-search:focus-within { border-color: var(--gold-600); background: var(--paper); }
.bj-search i { color: var(--ink-400); font-size: 16px; flex-shrink: 0; }
.bj-search input {
    flex: 1;
    background: transparent;
    border: none;
    padding: 10px 10px;
    font-family: var(--font-ar);
    font-size: .9rem;
    color: var(--ink-900);
    outline: none;
    direction: rtl;
}
.bj-search button {
    background: var(--navy-800); color: #fff;
    border: none; border-radius: 999px;
    padding: 7px 16px; font-size: .82rem; font-weight: 700;
    cursor: pointer;
    font-family: var(--font-ar);
}
.bj-search button:hover { background: var(--navy-700); }

/* ── GRID OF CATEGORY CARDS ─────────────────── */
.bj-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 16px;
}
.bj-cat-card {
    background: var(--paper);
    border: 1px solid var(--hairline);
    border-radius: var(--r-lg);
    padding: 18px 14px 14px;
    display: flex; flex-direction: column; align-items: center;
    text-align: center;
    box-shadow: var(--shadow-sm);
    transition: box-shadow .18s, transform .18s, border-color .18s;
    position: relative;
    overflow: hidden;
}
.bj-cat-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-3px);
    border-color: var(--gold-500);
}
.bj-cat-card .badge-id {
    position: absolute;
    top: 10px; right: 10px;
    background: var(--ivory);
    color: var(--ink-500);
    font-size: .68rem; font-weight: 800;
    padding: 3px 8px;
    border-radius: 999px;
    border: 1px solid var(--hairline-soft);
}
.bj-cat-card .thumb {
    width: 84px; height: 84px;
    border-radius: 50%;
    overflow: hidden;
    background: var(--ivory);
    border: 2px solid var(--hairline);
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 12px;
    margin-top: 6px;
}
.bj-cat-card .thumb img {
    width: 100%; height: 100%;
    object-fit: cover;
}
.bj-cat-card .name {
    font-size: .98rem; font-weight: 800;
    color: var(--ink-900);
    margin-bottom: 4px;
    max-width: 100%;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}
.bj-cat-card .sub-count {
    font-size: .72rem; color: var(--ink-400); font-weight: 600;
    margin-bottom: 14px;
}
.bj-cat-card .actions {
    display: flex; gap: 6px;
    width: 100%;
    padding-top: 12px;
    border-top: 1px dashed var(--hairline-soft);
}
.bj-cat-card .act {
    flex: 1;
    display: inline-flex; align-items: center; justify-content: center; gap: 5px;
    padding: 7px 4px;
    font-size: .76rem; font-weight: 700;
    border-radius: var(--r-sm);
    border: 1px solid var(--hairline);
    background: var(--paper);
    color: var(--ink-700);
    cursor: pointer;
    transition: all .13s;
    font-family: var(--font-ar);
    text-decoration: none;
}
.bj-cat-card .act:hover { background: var(--ivory); color: var(--ink-900); text-decoration: none; }
.bj-cat-card .act.edit:hover  { color: var(--navy-800); border-color: var(--navy-800); }
.bj-cat-card .act.del:hover   { color: var(--danger-fg); border-color: var(--danger-dot); background: var(--danger-bg); }
.bj-cat-card .act i { font-size: 13px; }

/* ── EMPTY ──────────────────────────────────── */
.bj-empty {
    background: var(--paper);
    border: 1px dashed var(--hairline);
    border-radius: var(--r-lg);
    padding: 4rem 1.5rem;
    text-align: center;
    color: var(--ink-400);
}
.bj-empty img { max-width: 140px; opacity: .7; margin-bottom: 1rem; }
.bj-empty h5 { font-weight: 700; color: var(--ink-500); margin: 0 0 .5rem; }
.bj-empty p { font-size: .85rem; margin: 0; }

/* ── PAGINATION ─────────────────────────────── */
.bj-pagi {
    background: var(--paper);
    border: 1px solid var(--hairline);
    border-radius: var(--r-lg);
    padding: 12px 18px;
    display: flex; justify-content: center;
    box-shadow: var(--shadow-sm);
}
.bj-pagi .pagination { margin: 0; }
.bj-pagi .page-link {
    color: var(--ink-700);
    border-radius: var(--r-sm) !important;
    margin: 0 2px;
    border: 1px solid var(--hairline);
    font-family: var(--font-ar);
}
.bj-pagi .page-item.active .page-link {
    background: var(--navy-800); border-color: var(--navy-800); color: #fff;
}

/* ── MODAL ──────────────────────────────────── */
.bj-modal-overlay {
    position: fixed; inset: 0;
    background: rgba(15,26,58,.55);
    backdrop-filter: blur(3px);
    z-index: 9998;
    display: none;
    align-items: center; justify-content: center;
    padding: 1rem;
    animation: bjFade .18s ease;
}
.bj-modal-overlay.show { display: flex; }
@keyframes bjFade { from{opacity:0} to{opacity:1} }
.bj-modal {
    background: var(--paper);
    border-radius: var(--r-xl);
    padding: 0;
    width: 100%;
    max-width: 480px;
    box-shadow: var(--shadow-lg);
    direction: rtl;
    font-family: var(--font-ar);
    overflow: hidden;
    animation: bjSlide .25s ease;
    border: 1px solid var(--hairline);
}
@keyframes bjSlide { from{transform:translateY(-12px);opacity:0} to{transform:translateY(0);opacity:1} }
.bj-modal-head {
    background: linear-gradient(135deg, var(--navy-800), var(--navy-900));
    color: #fff;
    padding: 18px 22px;
    display: flex; align-items: center; justify-content: space-between;
}
.bj-modal-head .ttl {
    display: flex; align-items: center; gap: 10px;
    font-size: 1.05rem; font-weight: 800;
}
.bj-modal-head .ttl i { color: var(--gold-600); font-size: 18px; }
.bj-modal-head .close {
    background: rgba(255,255,255,.08);
    border: none;
    color: #fff;
    width: 30px; height: 30px;
    border-radius: 50%;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background .13s;
}
.bj-modal-head .close:hover { background: rgba(255,255,255,.18); }
.bj-modal-body { padding: 22px; }
.bj-field { margin-bottom: 16px; }
.bj-field label {
    display: block;
    font-size: .82rem; font-weight: 700;
    color: var(--ink-700);
    margin-bottom: 6px;
}
.bj-field input[type="text"] {
    width: 100%;
    padding: 11px 14px;
    border: 1px solid var(--hairline);
    border-radius: var(--r-sm);
    background: var(--ivory);
    font-family: var(--font-ar);
    font-size: .9rem;
    color: var(--ink-900);
    outline: none;
    transition: border-color .13s, background .13s;
    direction: rtl;
}
.bj-field input[type="text"]:focus {
    border-color: var(--gold-600);
    background: var(--paper);
}
.bj-upload {
    border: 2px dashed var(--hairline);
    border-radius: var(--r-md);
    padding: 18px;
    text-align: center;
    background: var(--ivory);
    cursor: pointer;
    transition: border-color .13s, background .13s;
    position: relative;
}
.bj-upload:hover { border-color: var(--gold-600); background: var(--paper); }
.bj-upload input[type="file"] {
    position: absolute; inset: 0; opacity: 0; cursor: pointer;
}
.bj-upload .preview {
    width: 80px; height: 80px;
    border-radius: 50%;
    margin: 0 auto 8px;
    background: var(--paper);
    border: 1px solid var(--hairline);
    overflow: hidden;
    display: flex; align-items: center; justify-content: center;
}
.bj-upload .preview img { width: 100%; height: 100%; object-fit: cover; }
.bj-upload .preview i { color: var(--ink-400); font-size: 30px; }
.bj-upload .hint { font-size: .78rem; color: var(--ink-500); font-weight: 600; }
.bj-upload .hint b { color: var(--navy-800); }

.bj-modal-foot {
    padding: 14px 22px;
    background: var(--ivory);
    border-top: 1px solid var(--hairline-soft);
    display: flex; gap: 8px; justify-content: flex-start;
}

/* Lang tabs */
.bj-lang-tabs {
    display: flex; gap: 4px;
    margin-bottom: 10px;
    border-bottom: 1px solid var(--hairline-soft);
    overflow-x: auto;
}
.bj-lang-tab {
    background: transparent; border: none;
    padding: 7px 14px;
    font-size: .78rem; font-weight: 700;
    color: var(--ink-500);
    cursor: pointer;
    border-bottom: 2px solid transparent;
    font-family: var(--font-ar);
    white-space: nowrap;
    transition: color .13s, border-color .13s;
}
.bj-lang-tab.active { color: var(--navy-800); border-bottom-color: var(--gold-600); }
.bj-lang-form { display: none; }
.bj-lang-form.active { display: block; }

@media (max-width: 768px) {
    .bj-cats { padding: 14px; }
    .bj-cats-header { padding: 14px; }
    .bj-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .bj-cat-card { padding: 14px 8px 10px; }
    .bj-cat-card .thumb { width: 64px; height: 64px; }
    .bj-cat-card .name { font-size: .85rem; }
    .bj-cat-card .act { font-size: .7rem; padding: 6px 2px; }
    .bj-modal-head { padding: 14px 18px; }
    .bj-modal-body { padding: 16px; }
}
</style>
@endpush

@section('content')
<div class="bj-cats">

    @php($language=\App\Models\BusinessSetting::where('key','language')->first())
    @php($language = $language->value ?? null)
    @php($default_lang = str_replace('_', '-', app()->getLocale()))
    @php($languages = $language ? json_decode($language) : [])

    {{-- HEADER --}}
    <div class="bj-cats-header">
        <div class="ico"><i class="tio-category"></i></div>
        <div class="text">
            <h2>قائمة التصنيفات</h2>
            <p>إدارة التصنيفات الرئيسية لأطباق مطعمك وتنظيم القائمة بسهولة.</p>
        </div>
        <span class="pill">
            <i class="tio-folder"></i>
            إجمالي التصنيفات
            <span class="count" id="itemCount">{{$categories->total()}}</span>
        </span>
        <button class="bj-btn gold" type="button" onclick="bjOpenAdd()">
            <i class="tio-add"></i> إضافة تصنيف
        </button>
    </div>

    {{-- TOOLBAR --}}
    <form class="bj-toolbar" method="get">
        <div class="bj-search">
            <i class="tio-search"></i>
            <input type="search" name="search" value="{{ request()?->search ?? '' }}" placeholder="ابحث باسم التصنيف...">
            <button type="submit">بحث</button>
        </div>
    </form>

    {{-- GRID --}}
    @if(count($categories) > 0)
    <div class="bj-grid">
        @foreach($categories as $category)
        <div class="bj-cat-card">
            <span class="badge-id">#{{$category->id}}</span>
            <div class="thumb">
                <img class="onerror-image" src="{{ $category->image_full_url }}"
                     data-onerror-image="{{dynamicAsset('public/assets/admin/img/100x100/food-default-image.png')}}"
                     alt="{{ $category->name }}">
            </div>
            <div class="name" title="{{ $category->name }}">{{ Str::limit($category->name, 22, '...') }}</div>
            <div class="sub-count">
                <i class="tio-folder-opened"></i>
                {{ $category->childes?->count() ?? 0 }} تصنيف فرعي
            </div>
            <div class="actions">
                <button type="button" class="act edit" onclick="bjOpenEdit({{$category->id}})">
                    <i class="tio-edit"></i> تعديل
                </button>
                <form action="{{route('vendor.category.delete',[$category->id])}}" method="post" class="m-0" style="flex:1;display:flex;" onsubmit="return bjConfirmDelete(event, this)">
                    @csrf @method('DELETE')
                    <button type="submit" class="act del" style="width:100%;">
                        <i class="tio-delete"></i> حذف
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bj-empty">
        <img src="{{dynamicAsset('/public/assets/admin/img/empty.png')}}" alt="empty">
        <h5>لا توجد تصنيفات حتى الآن</h5>
        <p>ابدأ بإضافة أول تصنيف لقائمتك بالضغط على زر "إضافة تصنيف".</p>
    </div>
    @endif

    {{-- PAGINATION --}}
    @if($categories->hasPages())
    <div class="bj-pagi">
        {!! $categories->links() !!}
    </div>
    @endif

</div>

{{-- ADD / EDIT MODAL --}}
<div class="bj-modal-overlay" id="bj-modal" onclick="if(event.target===this)bjCloseModal()">
    <div class="bj-modal">
        <div class="bj-modal-head">
            <div class="ttl">
                <i class="tio-category" id="bj-modal-ico"></i>
                <span id="bj-modal-title">إضافة تصنيف جديد</span>
            </div>
            <button type="button" class="close" onclick="bjCloseModal()"><i class="tio-clear"></i></button>
        </div>
        <form id="bj-cat-form" method="post" enctype="multipart/form-data">
            @csrf
            <div class="bj-modal-body">

                {{-- Language tabs --}}
                @if($language && count($languages) > 0)
                <div class="bj-lang-tabs" id="bj-lang-tabs">
                    <button type="button" class="bj-lang-tab active" data-lang="default" onclick="bjSwitchLang('default')">الافتراضي</button>
                    @foreach($languages as $lng)
                        <button type="button" class="bj-lang-tab" data-lang="{{$lng}}" onclick="bjSwitchLang('{{$lng}}')">
                            {{ strtoupper($lng) }}
                        </button>
                    @endforeach
                </div>

                <div class="bj-field bj-lang-form active" data-lang-form="default">
                    <label>اسم التصنيف (الافتراضي)</label>
                    <input type="text" name="name[]" class="bj-name-input" data-lang="default" placeholder="مثال: المشاوي" maxlength="191" required>
                    <input type="hidden" name="lang[]" value="default">
                </div>
                @foreach($languages as $lng)
                <div class="bj-field bj-lang-form" data-lang-form="{{$lng}}">
                    <label>اسم التصنيف ({{ strtoupper($lng) }})</label>
                    <input type="text" name="name[]" class="bj-name-input" data-lang="{{$lng}}" placeholder="Category name" maxlength="191">
                    <input type="hidden" name="lang[]" value="{{$lng}}">
                </div>
                @endforeach
                @else
                <div class="bj-field">
                    <label>اسم التصنيف</label>
                    <input type="text" name="name[]" class="bj-name-input" data-lang="default" placeholder="مثال: المشاوي" maxlength="191" required>
                    <input type="hidden" name="lang[]" value="default">
                </div>
                @endif

                {{-- Image upload --}}
                <div class="bj-field">
                    <label>صورة التصنيف <span style="color:var(--ink-400);font-weight:500">(اختياري)</span></label>
                    <div class="bj-upload" id="bj-upload">
                        <div class="preview" id="bj-preview"><i class="tio-image"></i></div>
                        <div class="hint">اضغط لاختيار صورة <b>(PNG/JPG، أقل من 2MB)</b></div>
                        <input type="file" name="image" id="bj-image-input" accept="image/*" onchange="bjPreviewImage(this)">
                    </div>
                </div>

                {{-- Cart upsell toggle --}}
                <div class="bj-field" style="margin-bottom:0;">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input type="checkbox" name="is_cart_upsell" id="bj-cart-upsell" value="1" style="width:16px;height:16px;">
                        إظهار منتجات هذا التصنيف كاقتراحات إتمام الطلب في السلة
                    </label>
                </div>
            </div>
            <div class="bj-modal-foot">
                <button type="submit" class="bj-btn primary"><i class="tio-checkmark"></i> <span id="bj-submit-text">حفظ</span></button>
                <button type="button" class="bj-btn ghost" onclick="bjCloseModal()">إلغاء</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('script_2')
<script>
"use strict";
(function(){
    const STORE_URL  = '{{ route("vendor.category.store") }}';
    const UPDATE_TPL = '{{ route("vendor.category.update", ["id" => "__X__"]) }}';
    const EDIT_TPL   = '{{ route("vendor.category.edit", ["id" => "__X__"]) }}';
    const DEFAULT_IMG = '{{ dynamicAsset("public/assets/admin/img/100x100/food-default-image.png") }}';

    const $modal       = document.getElementById('bj-modal');
    const $form        = document.getElementById('bj-cat-form');
    const $title       = document.getElementById('bj-modal-title');
    const $submitText  = document.getElementById('bj-submit-text');
    const $preview     = document.getElementById('bj-preview');
    const $imgInput    = document.getElementById('bj-image-input');
    const $cartUpsell  = document.getElementById('bj-cart-upsell');

    function resetForm(){
        $form.reset();
        $form.querySelectorAll('.bj-name-input').forEach(i => i.value = '');
        $preview.innerHTML = '<i class="tio-image"></i>';
        $cartUpsell.checked = false;
        bjSwitchLang('default');
    }

    window.bjOpenAdd = function(){
        resetForm();
        $form.action = STORE_URL;
        $title.textContent = 'إضافة تصنيف جديد';
        $submitText.textContent = 'إضافة التصنيف';
        $modal.classList.add('show');
    };

    window.bjOpenEdit = function(id){
        resetForm();
        $form.action = UPDATE_TPL.replace('__X__', id);
        $title.textContent = 'تعديل التصنيف';
        $submitText.textContent = 'حفظ التغييرات';
        $modal.classList.add('show');

        fetch(EDIT_TPL.replace('__X__', id), { headers: { 'Accept': 'application/json' }})
            .then(r => r.json())
            .then(data => {
                const def = $form.querySelector('.bj-name-input[data-lang="default"]');
                if (def) def.value = data.name || '';
                if (data.image) {
                    $preview.innerHTML = '<img src="' + data.image + '" alt="" onerror="this.parentElement.innerHTML=\'<i class=tio-image></i>\'">';
                }
                $cartUpsell.checked = !!data.is_cart_upsell;
            })
            .catch(() => {});
    };

    window.bjCloseModal = function(){
        $modal.classList.remove('show');
    };

    window.bjSwitchLang = function(lang){
        document.querySelectorAll('.bj-lang-tab').forEach(t => {
            t.classList.toggle('active', t.dataset.lang === lang);
        });
        document.querySelectorAll('.bj-lang-form').forEach(f => {
            f.classList.toggle('active', f.dataset.langForm === lang);
        });
    };

    window.bjPreviewImage = function(input){
        const file = input.files && input.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => { $preview.innerHTML = '<img src="' + e.target.result + '" alt="">'; };
        reader.readAsDataURL(file);
    };

    window.bjConfirmDelete = function(e, form){
        e.preventDefault();
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'حذف التصنيف؟',
                text: 'سيتم حذف هذا التصنيف نهائياً.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#d9534f',
                cancelButtonColor: '#6d6d80',
            }).then(result => { if (result.isConfirmed) form.submit(); });
        } else if (confirm('هل أنت متأكد من حذف هذا التصنيف؟')) {
            form.submit();
        }
        return false;
    };

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && $modal.classList.contains('show')) bjCloseModal();
    });
})();
</script>
@endpush
