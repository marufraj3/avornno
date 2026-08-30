@extends('backEnd.layouts.master')
@section('title','Facebook Conversion API Settings')

@section('content')
<div class="container-fluid">
    <div class="page-title-box d-flex justify-content-between align-items-center py-3">
        <div>
            <h4 class="page-title mb-1 text-dark fw-bold">Facebook Conversion API Settings</h4>
            <p class="text-muted font-size-13 mb-0">এখানে Facebook CAPI এর Pixel ID, token এবং test event configuration সংরক্ষণ করবেন।</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7 col-xl-6">
            <div class="card">
                <div class="card-header">
                    <div class="header-icon">
                        <i class="fe-share-2"></i>
                    </div>
                    <h5 class="card-title mb-0">Credentials Configuration</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.facebook_capi.update') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label" for="pixel_id">Facebook Pixel ID <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('pixel_id') is-invalid @enderror" id="pixel_id" name="pixel_id" value="{{ old('pixel_id', $setting->pixel_id ?? '') }}" placeholder="e.g. 123456789012345" required>
                            <small class="text-muted">Facebook Events Manager থেকে Pixel ID কপি করে এখানে পেস্ট করুন।</small>
                            @error('pixel_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="access_token">Long-lived Access Token <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('access_token') is-invalid @enderror" id="access_token" name="access_token" rows="3" placeholder="Paste your long-lived access token here" required>{{ old('access_token', $setting->access_token ?? '') }}</textarea>
                            <small class="text-muted">Facebook Developer Tools থেকে generated CAPI এর long-lived access token এখানে রাখবেন।</small>
                            @error('access_token')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="test_event_code">Test Event Code (optional)</label>
                            <input type="text" class="form-control @error('test_event_code') is-invalid @enderror" id="test_event_code" name="test_event_code" value="{{ old('test_event_code', $setting->test_event_code ?? '') }}" placeholder="e.g. TEST1234">
                            <small class="text-muted">Events Manager &gt; Test Events থেকে পাওয়া Test Event Code (যদি ব্যবহার করেন)।</small>
                            @error('test_event_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="status-box mb-4">
                            <div>
                                <h6 class="mb-1 text-dark fw-bold">CAPI Status</h6>
                                <small class="text-muted">Facebook CAPI Active রাখলে server-side event fire হবে।</small>
                            </div>
                            <label class="switch">
                                <input type="checkbox" id="status" name="status" value="1" {{ old('status', $setting->status ?? 1) ? 'checked' : '' }}>
                                <span class="slider round"></span>
                            </label>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-submit px-4 rounded-pill">
                                <i class="fe-save me-1"></i> Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
