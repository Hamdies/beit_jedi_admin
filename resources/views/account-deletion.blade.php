@extends('layouts.landing.app')
@section('title', 'Delete your Beit Jedi account | حذف حساب بيت جدي')

@php($support_email = \App\CentralLogics\Helpers::get_settings('email_address'))
@php($support_phone = \App\CentralLogics\Helpers::get_settings('phone'))

@section('content')

        <!-- Page Header Gap -->
        <div class="h-148px"></div>
        <!-- Page Header Gap -->

        <!-- ======= Account Deletion Section ======= -->
        <section class="privacy-section">
            <div class="container">
                <div class="section-wrapper">
                    <div class="section-wrapper-inner">

                        <div class="section-header mw-100" dir="rtl">
                            <h2 class="title"><span class="text-base">حذف الحساب في تطبيق بيت جدي</span></h2>
                        </div>
                        <div class="about--content" dir="rtl" style="text-align: right">
                            <p>تشرح هذه الصفحة كيف يمكن لمستخدمي تطبيق <strong>بيت جدي (Beit Jedi)</strong>، المنشور على Google Play بواسطة <strong>بيت جدي</strong>، طلب حذف حساباتهم والبيانات المرتبطة بها.</p>

                            <h4>الطريقة الأولى: من داخل التطبيق</h4>
                            <ol>
                                <li>افتح تطبيق بيت جدي وسجّل الدخول إلى حسابك.</li>
                                <li>اذهب إلى <strong>الملف الشخصي</strong>.</li>
                                <li>اضغط على <strong>حذف الحساب</strong> ثم أكّد الحذف.</li>
                            </ol>
                            <p>ملاحظة: لا يمكن حذف الحساب أثناء وجود طلب قيد التنفيذ. يُرجى الانتظار حتى يكتمل الطلب أو يُلغى.</p>

                            <h4>الطريقة الثانية: بالبريد الإلكتروني أو الهاتف</h4>
                            <p>إذا لم تتمكن من الوصول إلى التطبيق، أرسل طلب حذف إلى
                                <a href="mailto:{{ $support_email }}?subject=Beit%20Jedi%20account%20deletion" class="text-base">{{ $support_email }}</a>
                                أو اتصل على <a href="tel:{{ $support_phone }}" class="text-base" dir="ltr">{{ $support_phone }}</a>
                                من رقم الهاتف المسجّل في حسابك، مع ذكر عبارة "حذف حساب بيت جدي". سنتحقق من هويتك ونحذف الحساب خلال 7 أيام عمل.</p>

                            <h4>البيانات التي يتم حذفها</h4>
                            <ul>
                                <li>بيانات الحساب: الاسم، رقم الهاتف، البريد الإلكتروني، صورة الملف الشخصي.</li>
                                <li>العناوين المحفوظة وبيانات تسجيل الدخول ورموز الإشعارات.</li>
                                <li>المفضلة وسلة المشتريات وإعدادات الحساب.</li>
                            </ul>

                            <h4>البيانات التي قد يتم الاحتفاظ بها</h4>
                            <ul>
                                <li>سجلات الطلبات والفواتير والمعاملات المالية، ويُحتفظ بها لمدة تصل إلى 5 سنوات للامتثال للمتطلبات القانونية والضريبية والمحاسبية ولمنع الاحتيال، ثم تُحذف.</li>
                            </ul>
                        </div>

                        <hr class="my-5">

                        <div class="section-header mw-100" dir="ltr">
                            <h2 class="title"><span class="text-base">Delete your Beit Jedi account</span></h2>
                        </div>
                        <div class="about--content" dir="ltr" style="text-align: left">
                            <p>This page explains how users of the <strong>Beit Jedi (بيت جدي)</strong> app, published on Google Play by <strong>Beit Jedi</strong>, can request deletion of their account and associated data.</p>

                            <h4>Option 1: In the app</h4>
                            <ol>
                                <li>Open the Beit Jedi app and sign in.</li>
                                <li>Go to <strong>Profile</strong>.</li>
                                <li>Tap <strong>Delete Account</strong> and confirm.</li>
                            </ol>
                            <p>Note: an account cannot be deleted while it has an order in progress. Please wait until the order is completed or cancelled.</p>

                            <h4>Option 2: By email or phone</h4>
                            <p>If you cannot access the app, send a deletion request to
                                <a href="mailto:{{ $support_email }}?subject=Beit%20Jedi%20account%20deletion" class="text-base">{{ $support_email }}</a>
                                or call <a href="tel:{{ $support_phone }}" class="text-base">{{ $support_phone }}</a>
                                from the phone number registered on your account, mentioning "Beit Jedi account deletion". We will verify your identity and delete the account within 7 business days.</p>

                            <h4>Data that is deleted</h4>
                            <ul>
                                <li>Account details: name, phone number, email address, profile picture.</li>
                                <li>Saved addresses, login credentials and notification tokens.</li>
                                <li>Wishlist, cart and account preferences.</li>
                            </ul>

                            <h4>Data that may be retained</h4>
                            <ul>
                                <li>Order history, invoices and payment transaction records are kept for up to 5 years to comply with legal, tax and accounting obligations and to prevent fraud, and are then deleted.</li>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
        </section>
        <!-- ======= Account Deletion Section ======= -->
@endsection
