@extends('backEnd.layouts.master')
@section('title','Create Color')

@section('content')
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between py-4">
                <div>
                    <h4 class="page-title mb-1 text-dark fw-bold">Create New Color</h4>
                    <p class="text-muted font-size-13 mb-0">Add a new color variant for your products.</p>
                </div>
                <div class="page-title-right">
                    <a href="{{route('colors.index')}}" class="btn btn-light rounded-pill border shadow-sm px-4">
                        <i class="fe-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form action="{{route('colors.store')}}" method="POST" enctype="multipart/form-data" data-parsley-validate>
        @csrf
        <div class="row">
            
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="header-icon"><i class="fe-droplet"></i></div>
                        <h5 class="card-title">Color Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="colorName" class="form-label">Color Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('colorName') is-invalid @enderror" 
                                           name="colorName" value="{{ old('colorName') }}" id="colorName" 
                                           placeholder="e.g. Midnight Blue" required>
                                    @error('colorName')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="color" class="form-label">Color Picker <span class="text-danger">*</span></label>
                                    <div class="color-preview-box">
                                        <input type="color" class="form-control @error('color') is-invalid @enderror" 
                                               name="color" value="{{ old('color') }}" id="color" required 
                                               onchange="updateColorCode(this.value)">
                                        <span id="colorCode" class="color-code">{{ old('color') ?? '#000000' }}</span>
                                    </div>
                                    @error('color')
                                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="header-icon"><i class="fe-settings"></i></div>
                        <h5 class="card-title">Visibility</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded border border-light">
                            <div>
                                <h6 class="mb-1 text-dark fw-bold">Active Status</h6>
                                <p class="text-muted font-size-12 mb-0">Enable or disable color</p>
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
                            <i class="fe-check-circle me-1"></i> Save Color
                        </button>
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

<script>

    // Update Hex Code Display
    function updateColorCode(color) {
        document.getElementById('colorCode').innerText = color;
    }
</script>
@endsection