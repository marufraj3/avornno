@extends('backEnd.layouts.master')
@section('title','General Settings Configuration')

@section('css')
<link href="{{asset('public/backEnd')}}/assets/libs/summernote/summernote-lite.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="settings-shell">
        <div class="page-title-box d-flex justify-content-between align-items-center py-3">
            <div>
                <h4 class="page-title mb-1 text-dark fw-bold">General Settings</h4>
                <p class="text-muted small mb-0">Update site identity, appearance, policies and business rules from one place.</p>
            </div>
        </div>

        <form action="{{route('settings.update')}}" method="POST" data-parsley-validate="" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{$edit_data->id}}">

            <div class="row">
                <div class="col-lg-8">
                    <div class="settings-shell">
                        <div class="settings-card">
                            <div class="section-title-pro">
                                <i class="mdi mdi-web text-primary"></i> Basic Information
                            </div>
                            <div class="p-4 row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label-pro">Site Name *</label>
                                    <input type="text" name="name" class="form-control custom-input" value="{{ $edit_data->name }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label-pro">FB Page Username</label>
                                    <input type="text" name="facebook_page_username" class="form-control custom-input" value="{{ $edit_data->facebook_page_username }}" required>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                        <label class="form-label-pro mb-0">Breaking News Ticker</label>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" name="news_ticker_enabled" value="1" id="newsTickerEnabled" {{ ($edit_data->news_ticker_enabled ?? 1) == 1 ? 'checked' : '' }}>
                                            <label class="form-check-label" for="newsTickerEnabled"><strong>টিকার চালু</strong></label>
                                        </div>
                                    </div>
                                    <textarea name="top_headline" class="form-control custom-input" rows="2" placeholder="World Cup-এর সকল জনপ্রিয় প্রোডাক্ট এখন একসাথে পাওয়া যাচ্ছে...">{{ $edit_data->top_headline }}</textarea>
                                    <small class="text-muted">টিকার চালু থাকলে এবং টেক্সট লিখলে সাইটের উপরে Breaking News বার দেখাবে। বন্ধ করলে টেক্সট সংরক্ষিত থাকবে কিন্তু দেখাবে না।</small>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label-pro">Footer About Text</label>
                                    <textarea name="footer_about_text" class="form-control custom-input" rows="3" placeholder="আপনার ব্যবসার ডিজিটাল পার্টনার। আমরা বিশ্বাস করি গুণগত মান এবং গ্রাহক সন্তুষ্টিতে। প্রযুক্তির সাথে এগিয়ে চলুন আমাদের সাথে।">{{ $edit_data->footer_about_text ?? '' }}</textarea>
                                    <small class="text-muted">This text appears in the footer about section on the frontend.</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label-pro">Google Play App Link</label>
                                    <input type="url" name="google_play_link" class="form-control custom-input" value="{{ $edit_data->google_play_link ?? '' }}" placeholder="https://play.google.com/store/apps/...">
                                    <small class="text-muted">Footer - Google Play download button</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label-pro">App Store Link</label>
                                    <input type="url" name="app_store_link" class="form-control custom-input" value="{{ $edit_data->app_store_link ?? '' }}" placeholder="https://apps.apple.com/...">
                                    <small class="text-muted">Footer - App Store download button</small>
                                </div>
                            </div>
                        </div>

                        <div class="settings-card">
                            <div class="section-title-pro">
                                <i class="mdi mdi-palette text-success"></i> Theme Appearance
                            </div>
                            <div class="p-4">
                                <div class="row g-3">
                                    @php
                                        $colors = [
                                            'primary_color' => ['label' => 'Primary Color', 'default' => '#0d6efd'],
                                            'secodery_color' => ['label' => 'Secondary Color', 'default' => '#198754'],
                                            'footer_color' => ['label' => 'Footer Color', 'default' => '#222222'],
                                            'copyright_color' => ['label' => 'Copyright Color', 'default' => '#111111']
                                        ];
                                    @endphp
                                    @foreach($colors as $key => $color)
                                    <div class="col-md-6 col-xl-3">
                                        <label class="form-label-pro">{{ $color['label'] }}</label>
                                        <div class="color-box-pro">
                                            <input type="color" name="{{ $key }}" id="{{ $key }}_cp" value="{{ old($key, $edit_data->$key ?? $color['default']) }}" class="form-control-color border-0 bg-transparent" oninput="document.getElementById('{{ $key }}_txt').value=this.value;">
                                            <input type="text" id="{{ $key }}_txt" value="{{ old($key, $edit_data->$key ?? $color['default']) }}" class="form-control border-0 p-0 small text-uppercase fw-bold" style="font-size: 11px;" oninput="document.getElementById('{{ $key }}_cp').value=this.value;">
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <div class="row mt-4 g-3">
                                    @php
                                        $logos = [
                                            'white_logo' => 'White Logo (For Dark Bg)',
                                            'dark_logo' => 'Dark Logo (For Light Bg)',
                                            'favicon' => 'Favicon Icon',
                                            'og_baner' => 'Social Banner (OG)'
                                        ];
                                    @endphp
                                    @foreach($logos as $slug => $label)
                                    <div class="col-md-6">
                                        <label class="form-label-pro">{{ $label }}</label>
                                        <input type="file" name="{{ $slug }}" class="form-control custom-input mb-2">
                                        <div class="logo-preview-box">
                                            <img src="{{asset($edit_data->$slug)}}" class="edit-image-pro" alt="Preview">
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="settings-card">
                            <div class="section-title-pro">
                                <i class="mdi mdi-text-box-outline text-info"></i> Policies & Notes
                            </div>
                            <div class="p-4">
                                <div class="mb-4">
                                    <label class="form-label-pro">Checkout Note</label>
                                    <textarea class="summernote" name="checkout_note">{{ $edit_data->checkout_note }}</textarea>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label-pro">Order Policy</label>
                                    <textarea class="summernote" name="order_policy">{{ $edit_data->order_policy }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="settings-shell">
                        <div class="settings-card">
                            <div class="section-title-pro">
                                <i class="mdi mdi-shield-check text-danger"></i> Business Logic
                            </div>
                            <div class="p-4">
                                <div class="mb-4">
                                    <label class="form-label-pro">Hot Deal End Date</label>
                                    <input type="date" name="hot_deal_end_date" class="form-control custom-input" value="{{ $edit_data->hot_deal_end_date }}">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label-pro">Flash Sale End Date</label>
                                    <input type="date" name="flash_sale_end_date" class="form-control custom-input" value="{{ $edit_data->flash_sale_end_date }}">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label-pro">Visibility Controls</label>
                                    <div class="d-grid gap-2">
                                        <select class="form-select custom-input" name="show_all_products">
                                            <option value="1" @if($edit_data->show_all_products==1) selected @endif>Home: Show All Products</option>
                                            <option value="0" @if($edit_data->show_all_products==0) selected @endif>Home: Hide All Products</option>
                                        </select>
                                        <select class="form-select custom-input" name="show_category_wise_products">
                                            <option value="1" @if($edit_data->show_category_wise_products==1) selected @endif>Home: Category Wise On</option>
                                            <option value="0" @if($edit_data->show_category_wise_products==0) selected @endif>Home: Category Wise Off</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="settings-card">
                            <div class="section-title-pro">
                                <i class="mdi mdi-shopping text-warning"></i> Quick Order Popup (দ্রুত অর্ডার পপআপ)
                            </div>
                            <div class="p-4">
                                <p class="text-muted small mb-3">
                                    ফ্রন্টএন্ডে <strong>"Order Now"</strong> বা <strong>কার্ট</strong> বাটনে ক্লিক করলে এই পপআপ দেখাবে — গ্রাহক সাইট ছেড়ে না গিয়েই ২ ধাপে অর্ডার কমপ্লিট করতে পারবে।
                                </p>
                                <div class="row g-3">
                                    <div class="col-12 mb-2">
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" name="quick_order_popup_enabled" value="1" id="qoEnabled" {{ ($edit_data->quick_order_popup_enabled ?? 1) == 1 ? 'checked' : '' }}>
                                            <label class="form-check-label" for="qoEnabled"><strong>Quick Order Popup চালু</strong></label>
                                        </div>
                                        <small class="text-muted">বন্ধ করলে আগের মতো সরাসরি কার্ট/চেকআউটে যাবে।</small>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label-pro">পপআপ টাইটেল</label>
                                        <input type="text" name="quick_order_popup_title" class="form-control custom-input" value="{{ $edit_data->quick_order_popup_title ?? '🛒 দ্রুত অর্ডার করুন' }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label-pro">কনফার্ম বাটন টেক্সট</label>
                                        <input type="text" name="quick_order_confirm_text" class="form-control custom-input" value="{{ $edit_data->quick_order_confirm_text ?? 'অর্ডার কনফার্ম করুন →' }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label-pro">কার্ট বাটন টেক্সট</label>
                                        <input type="text" name="quick_order_cart_text" class="form-control custom-input" value="{{ $edit_data->quick_order_cart_text ?? 'কার্টে রাখুন' }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label-pro">কার্টে যোগের সাফল্য বার্তা</label>
                                        <input type="text" name="quick_order_cart_toast" class="form-control custom-input" value="{{ $edit_data->quick_order_cart_toast ?? 'কার্টে যোগ হয়েছে ✔' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 text-center mb-5 mt-4">
                    <button type="submit" class="btn-save-pro">
                        <i class="mdi mdi-content-save-all me-2"></i> Update Global Settings
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script src="{{asset('public/backEnd/')}}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-validation.init.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/libs/summernote/summernote-lite.min.js"></script>
<script>
$(document).ready(function() {
    $('.summernote').summernote({
        placeholder: 'Type your policy or notes here...',
        height: 200,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture']],
            ['view', ['fullscreen', 'codeview']]
        ]
    });
});
</script>
@endsection
