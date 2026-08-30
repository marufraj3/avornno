@extends('backEnd.layouts.master')

@section('title','Fraud API Settings')

@section('content')

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --glass-white: rgba(255, 255, 255, 0.95);
        --text-dark: #2d3748;
        --text-muted: #718096;
        --border-color: #e2e8f0;
    }

    .fraud-page-wrapper {
        padding-top: 30px;
        background-color: #f8f9fc;
        min-height: 100vh;
        font-family: 'Inter', sans-serif;
    }

    /* Header Styling */
    .fraud-header-card {
        background: var(--primary-gradient);
        border-radius: 16px;
        padding: 30px;
        color: white;
        box-shadow: 0 10px 25px rgba(118, 75, 162, 0.2);
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }

    /* Form Card Styling */
    .settings-card {
        background: var(--glass-white);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
    }

    .settings-card-header {
        background: transparent;
        border-bottom: 1px solid var(--border-color);
        padding: 20px 25px;
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-dark);
        display: flex;
        align-items: center;
    }

    .form-control-lg-custom {
        padding: 12px 15px;
        font-size: 0.95rem;
        border-radius: 8px;
        border: 1px solid #cbd5e0;
    }

    .form-control-lg-custom:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    /* Button Styling */
    .btn-save {
        background: var(--primary-gradient);
        border: 0;
        padding: 12px;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(118, 75, 162, 0.3);
    }

    /* Timeline Styling */
    .timeline {
        position: relative;
        padding-left: 10px;
    }
    .timeline-item {
        position: relative;
        padding-left: 40px;
        padding-bottom: 30px;
    }
    .timeline-item:last-child {
        padding-bottom: 0;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        width: 2px;
        height: 100%;
        background: #e2e8f0;
    }
    .timeline-item:last-child::before {
        display: none;
    }
    .timeline-badge {
        position: absolute;
        left: -9px;
        top: 0;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #667eea;
        border: 4px solid #fff;
        box-shadow: 0 0 0 1px #667eea;
    }
    .timeline-content h6 {
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 5px;
    }
    .timeline-content p {
        color: var(--text-muted);
        font-size: 0.9rem;
        line-height: 1.5;
    }

    /* Alert Styling */
    .alert-custom {
        border-radius: 10px;
        border: none;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
</style>

<div class="container-fluid fraud-page-wrapper">

    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="fraud-header-card d-flex flex-wrap justify-content-between align-items-center">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <div class="bg-white p-2 rounded-circle me-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fe-shield text-primary" style="font-size: 28px;"></i>
                    </div>
                    <div>
                        <h2 class="mb-1 text-white fw-bold">Hoorin Courier API</h2>
                        <p class="mb-0 text-white-50 small">Secure API Configuration Panel</p>
                    </div>
                </div>
                
                <a href="https://dash.hoorin.com" target="_blank" class="btn btn-light text-primary fw-bold px-4 py-2 rounded-pill shadow-sm">
                    <i class="fe-globe me-2"></i> Visit Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        
        <div class="col-lg-5 mb-4">
            
            @if(session()->has('success') || session()->has('message'))
                <div class="alert alert-success alert-custom alert-dismissible fade show mb-4 d-flex align-items-center" role="alert">
                    <i class="fe-check-circle fs-4 me-2"></i>
                    <div>
                        <strong>সফল হয়েছে!</strong> {{ session('success') ?? session('message') }}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-custom alert-dismissible fade show mb-4" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card settings-card h-100">
                <div class="settings-card-header">
                    <i class="fe-sliders me-2 text-primary"></i> API Configuration
                </div>
                
                <div class="card-body p-4 d-flex flex-column justify-content-center">
                    
                    <form action="{{ route('admin.fraud.update') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark mb-2">Hoorin API Key <span class="text-danger">*</span></label>
                            
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fe-key text-muted"></i></span>
                                <input type="text" name="fraud_api_key" 
                                       class="form-control form-control-lg-custom border-start-0" 
                                       placeholder="Enter your Hoorin API Key here"
                                       value="{{ old('fraud_api_key', $data->fraud_api_key ?? '') }}"
                                       required>
                            </div>
                            <small class="text-muted mt-2 d-block">
                                <i class="fe-shield me-1"></i> এই কি (Key) কুরিয়ার ফ্রড/সাকসেস রেশিও চেকিং API-এর জন্য ব্যবহৃত হবে (dash.hoorin.com থেকে পাওয়া যায়)।
                            </small>
                        </div>


                        <button type="submit" class="btn btn-primary btn-save w-100 text-white rounded-pill">
                            <i class="fe-save me-2"></i> সেটিংস আপডেট করুন
                        </button>
                    </form>

                    <div class="mt-4 p-3 bg-light rounded border border-light">
                        <div class="d-flex">
                            <i class="fe-info text-primary mt-1 me-2"></i>
                            <p class="small text-muted mb-0">
                                <strong>বিঃদ্রঃ</strong> ভুল API Key দিলে অর্ডার ভেরিফিকেশন কাজ করবে না। নিশ্চিত হয়ে Key টি ইনপুট দিন।
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="ms-lg-3">
                <h5 class="mb-4 fw-bold text-dark px-2 border-start border-4 border-primary">
                    &nbsp;কিভাবে API Key পাবেন?
                </h5>

                <div class="timeline mt-2">
                    <div class="timeline-item">
                        <div class="timeline-badge"></div>
                        <div class="timeline-content ms-3">
                            <h6>১. ড্যাশবোর্ডে লগইন করুন</h6>
                            <p>প্রথমে <a href="https://dash.hoorin.com" target="_blank" class="fw-bold text-primary text-decoration-none">dash.hoorin.com</a> -এ লগইন করুন।</p>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-badge"></div>
                        <div class="timeline-content ms-3">
                            <h6>২. Courier Connect সেটিংস</h6>
                            <p><strong>Settings</strong> পেজে যান এবং বাম পাশের ন্যাভিগেশন থেকে <strong>Courier Connect</strong> মেনু সিলেক্ট করুন।</p>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-badge"></div>
                        <div class="timeline-content ms-3">
                            <h6>৩. Steadfast ক্রেডেনশিয়াল কানেক্ট</h6>
                            <p><strong>Steadfast</strong> অপশনটি চুজ করে আপনার কুরিয়ার প্যানেলের <strong>Client ID</strong> এবং <strong>Secret Key</strong> ইনপুট দিয়ে সেভ করুন।</p>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-badge"></div>
                        <div class="timeline-content ms-3">
                            <h6>৪. API Key কপি করুন</h6>
                            <p>কানেক্ট সম্পন্ন হলে ড্যাশবোর্ড থেকে আপনার <strong>API Key</strong> কপি করে উপরের ফর্মে বসান এবং সেভ করুন।</p>
                        </div>
                    </div>
                </div>

                <div class="mt-2 ms-4 ps-2">
                    <a href="https://dash.hoorin.com/doc" target="_blank" 
                       class="btn btn-outline-primary btn-sm rounded-pill px-4">
                        <i class="fe-book-open me-1"></i> ডকুমেন্টেশন দেখুন
                    </a>
                </div>
            </div>
        </div>

    </div>
</div> 

@endsection