@extends('backEnd.layouts.master')
@section('title','Edit Brand')

@section('content')
<div class="container-fluid">
    
    <div class="page-title-box d-flex align-items-center justify-content-between py-3">
        <div>
            <h4 class="page-title mb-1 text-dark fw-bold">Edit Brand: {{ $edit_data->name }}</h4>
            <p class="text-muted font-size-13 mb-0">Update brand name, logo and current publish status.</p>
        </div>
        <div class="page-title-right">
            <a href="{{route('brands.index')}}" class="btn btn-light border btn-sm rounded-pill px-3 shadow-sm">
                <i class="fe-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <form action="{{route('brands.update')}}" method="POST" enctype="multipart/form-data" data-parsley-validate>
        @csrf
        <input type="hidden" value="{{$edit_data->id}}" name="id">

        <div class="row">
            
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body p-4">
                        
                        <div class="form-group">
                            <label for="name" class="form-label">Brand Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   name="name" value="{{ $edit_data->name }}" id="name" 
                                   placeholder="Enter brand name" required>
                            @error('name')
                                <div class="invalid-feedback mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="action-toolbar">
                            <div class="d-flex align-items-center gap-3">
                                <label class="mb-0 fw-bold text-dark font-size-14" for="status" style="cursor: pointer;">Active Status</label>
                                <div>
                                    <input type="checkbox" id="status" name="status" value="1" class="toggle-checkbox" {{ $edit_data->status == 1 ? 'checked' : '' }}>
                                    <label for="status" class="toggle-label" title="Toggle Status"></label>
                                </div>
                            </div>

                            <button type="submit" class="btn-update">
                                <i class="fe-check-circle"></i> Update Brand
                            </button>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body p-4">
                        <label class="form-label mb-2">Brand Logo</label>
                        
                        <div class="logo-upload-container p-0">
                            <div class="logo-preview-box" onclick="document.getElementById('image').click()">
                                <img id="preview_image" src="{{ asset($edit_data->image) }}" alt="Logo">
                                <div class="upload-hint">Click to change</div>
                            </div>
                            
                            <input type="file" name="image" id="image" class="d-none" accept="image/*" onchange="readURL(this)">
                            
                            @error('image')
                                <div class="text-danger small mt-2 text-center">{{ $message }}</div>
                            @enderror

                            <div class="mt-3 text-center">
                                <small class="text-muted d-block" style="font-size: 11px;">
                                    Format: PNG, JPG, WEBP <br> Size: 120x120 px
                                </small>
                            </div>
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

<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#preview_image').attr('src', e.target.result);
                $('.upload-hint').text('Image Selected'); // Update hint text
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection