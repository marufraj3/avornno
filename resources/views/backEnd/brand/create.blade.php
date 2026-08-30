@extends('backEnd.layouts.master')
@section('title','Create Brand')

@section('content')
<div class="container-fluid">
    
    <div class="page-title-box d-flex align-items-center justify-content-between py-3">
        <div>
            <h4 class="page-title mb-1 text-dark fw-bold">Add New Brand</h4>
            <p class="text-muted font-size-13 mb-0">Create a new brand entry with name, status and logo.</p>
        </div>
        <div class="page-title-right">
            <a href="{{route('brands.index')}}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fe-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <form action="{{route('brands.store')}}" method="POST" enctype="multipart/form-data" data-parsley-validate>
        @csrf
        <div class="row">
            
            <div class="col-lg-8">
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-0">
                            <label for="name" class="form-label">Brand Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name') }}" id="name" 
                                   placeholder="e.g. Nike" required autofocus>
                            @error('name')
                                <div class="invalid-feedback mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Publish</h5>
                    </div>
                    <div class="card-body">
                        <div class="status-box">
                            <div>
                                <h6 class="mb-0 fw-bold text-dark font-size-14">Active Status</h6>
                                <small class="text-muted" style="font-size: 11px;">Enable this brand on website</small>
                            </div>
                            <label class="switch">
                                <input type="checkbox" name="status" value="1" checked>
                                <span class="slider round"></span>
                            </label>
                        </div>
                        @error('status')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror

                        <button type="submit" class="btn-save mt-3">
                            <i class="fe-save me-1"></i> Save Brand
                        </button>
                    </div>
                </div>

            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Brand Logo</h5>
                    </div>
                    <div class="card-body">
                        <div class="logo-upload-box" onclick="document.getElementById('image').click()">
                            <input type="file" name="image" id="image" class="d-none" accept="image/*" onchange="readURL(this)">
                            
                            <img id="preview_image" class="preview-img" src="#" alt="Preview">
                            
                            <div id="upload_placeholder" class="upload-placeholder">
                                <i class="fe-image"></i>
                                <p>Click to upload logo</p>
                                <small class="text-muted d-block mt-1">(PNG, JPG, WEBP)</small>
                            </div>
                        </div>
                        @error('image')
                            <div class="text-danger small mt-2 text-center">{{ $message }}</div>
                        @enderror
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
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#preview_image').attr('src', e.target.result).show();
                $('#upload_placeholder').hide();
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection