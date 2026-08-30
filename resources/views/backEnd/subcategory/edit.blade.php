@extends('backEnd.layouts.master')
@section('title','Edit Subcategory')

@section('css')
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('public/backEnd')}}/assets/libs/summernote/summernote-lite.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between py-4">
                <div>
                    <h4 class="page-title mb-1 text-dark fw-bold">Edit Subcategory: {{ $edit_data->subcategoryName }}</h4>
                    <p class="text-muted font-size-13 mb-0">Update subcategory details and configuration.</p>
                </div>
                <div class="page-title-right">
                    <a href="{{route('subcategories.index')}}" class="btn btn-light rounded-pill border shadow-sm px-4">
                        <i class="fe-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form action="{{route('subcategories.update')}}" method="POST" name="editForm" enctype="multipart/form-data" data-parsley-validate>
        @csrf
        <input type="hidden" value="{{$edit_data->id}}" name="id">

        <div class="row">
            
            <div class="col-lg-8">
                
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="header-icon"><i class="fe-edit"></i></div>
                        <h5 class="card-title">Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="category_id" class="form-label">Parent Category <span class="text-danger">*</span></label>
                                    <select class="form-control select2 @error('category_id') is-invalid @enderror" 
                                            id="category_id" name="category_id" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ $edit_data->category_id == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="subcategoryName" class="form-label">Subcategory Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('subcategoryName') is-invalid @enderror" 
                                           name="subcategoryName" value="{{ $edit_data->subcategoryName }}" 
                                           id="subcategoryName" required>
                                    @error('subcategoryName')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <div class="header-icon"><i class="fe-search"></i></div>
                        <h5 class="card-title">SEO Configuration</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-4">
                            <label for="meta_title" class="form-label">Meta Title</label>
                            <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                                   name="meta_title" value="{{ $edit_data->meta_title }}" id="meta_title">
                            @error('meta_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label for="meta_description" class="form-label">Meta Description</label>
                            <textarea class="summernote form-control @error('meta_description') is-invalid @enderror" 
                                      name="meta_description" id="meta_description">{!! $edit_data->meta_description !!}</textarea>
                            @error('meta_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                
                <div class="card">
                    <div class="card-header">
                        <div class="header-icon"><i class="fe-settings"></i></div>
                        <h5 class="card-title">Visibility</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded border border-light">
                            <div>
                                <h6 class="mb-1 text-dark fw-bold">Status</h6>
                                <p class="text-muted font-size-12 mb-0">Enable or disable subcategory</p>
                            </div>
                            <label class="switch">
                                <input type="checkbox" name="status" value="1" {{ $edit_data->status == 1 ? 'checked' : '' }}>
                                <span class="slider round"></span>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-submit w-100 rounded-pill">
                            <i class="fe-save me-1"></i> Update Changes
                        </button>
                    </div>
                </div>

                <div class="card bg-soft-info border-0 mt-4">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <i class="fe-info font-size-18 me-2 text-info"></i>
                            <p class="mb-0 font-size-13 text-info">
                                changing parent category will affect the product hierarchy and search results.
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
<script src="{{asset('public/backEnd/')}}/assets/libs/select2/js/select2.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/libs/summernote/summernote-lite.min.js"></script>

<script>
    $(document).ready(function(){
        // Summernote Setup
        $(".summernote").summernote({
            placeholder: "Enter SEO description...",
            height: 120,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol']],
                ['misc', ['fullscreen', 'codeview']]
            ]
        });

        // Select2 Setup
        $(".select2").select2({
            width: '100%'
        });
    });
</script>
@endsection