@extends('backEnd.layouts.master')
@section('title','Create Order Status')

@section('content')
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between py-4">
                <div>
                    <h4 class="page-title mb-1 text-dark fw-bold">Create Order Status</h4>
                    <p class="text-muted font-size-13 mb-0">Define new statuses for order tracking (e.g. Pending, Shipped).</p>
                </div>
                <div class="page-title-right">
                    <a href="{{route('orderstatus.index')}}" class="btn btn-light rounded-pill border shadow-sm px-4">
                        <i class="fe-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form action="{{route('orderstatus.store')}}" method="POST" enctype="multipart/form-data" data-parsley-validate>
        @csrf
        <div class="row">
            
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="header-icon"><i class="fe-flag"></i></div>
                        <h5 class="card-title">Status Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-4">
                            <label for="name" class="form-label">Status Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name') }}" id="name" 
                                   placeholder="e.g. Pending, Processing, Delivered" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="header-icon"><i class="fe-settings"></i></div>
                        <h5 class="card-title">Configuration</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded border border-light">
                            <div>
                                <h6 class="mb-1 text-dark fw-bold">Active Status</h6>
                                <p class="text-muted font-size-12 mb-0">Enable this status</p>
                            </div>
                            <label class="switch">
                                <input type="checkbox" name="status" value="1" checked>
                                <span class="slider round"></span>
                            </label>
                        </div>
                        @error('status')
                            <div class="text-danger small mb-2">{{ $message }}</div>
                        @enderror

                        <button type="submit" class="btn btn-submit w-100 rounded-pill">
                            <i class="fe-check-circle me-1"></i> Save Status
                        </button>
                    </div>
                </div>

                <div class="card bg-soft-info border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <i class="fe-info font-size-18 me-2 text-info"></i>
                            <p class="mb-0 font-size-13 text-info">
                                These statuses will be used to track the progress of customer orders. Ensure clear naming.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
<script src="{{asset('public/backEnd/')}}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-validation.init.js"></script>
@endsection