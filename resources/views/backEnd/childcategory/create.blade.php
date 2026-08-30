@extends('backEnd.layouts.master')
@section('title','Create Childcategory')

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
                    <h4 class="page-title mb-1 text-dark fw-bold">Create Childcategory</h4>
                    <p class="text-muted font-size-13 mb-0">Add a new level to your product hierarchy.</p>
                </div>
                <div class="page-title-right">
                    <a href="{{route('childcategories.index')}}" class="btn btn-light rounded-pill border shadow-sm px-4">
                        <i class="fe-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form action="{{route('childcategories.store')}}" method="POST" enctype="multipart/form-data" data-parsley-validate>
        @csrf
        <div class="row">
            
            <div class="col-lg-8">
                
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="header-icon"><i class="fe-git-merge"></i></div>
                        <h5 class="card-title">General Information</h5>
                    </div>
                    <div class="card-body">
                        
                        <div class="form-group mb-4">
                            <label for="subcategory_id" class="form-label">Parent Subcategory <span class="text-danger">*</span></label>
                            <select class="form-control select2 @error('subcategory_id') is-invalid @enderror" 
                                    id="subcategory_id" name="subcategory_id" required>
                                <option value="">Select Subcategory</option>
                                @foreach ($menucategories as $category)
                                    <optgroup label="{{ $category->name }}">
                                        @foreach ($category->subcategories as $subcat)
                                            <option value="{{ $subcat->id }}">{{ $subcat->subcategoryName }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('subcategory_id')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="childcategoryName" class="form-label">Childcategory Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('childcategoryName') is-invalid @enderror" 
                                   name="childcategoryName" value="{{ old('childcategoryName') }}" 
                                   id="childcategoryName" placeholder="e.g. T-Shirts, Wireless Mouse" required>
                            @error('childcategoryName')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                                   name="meta_title" value="{{ old('meta_title') }}" placeholder="SEO Title">
                            @error('meta_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label for="meta_description" class="form-label">Meta Description</label>
                            <textarea class="summernote form-control @error('meta_description') is-invalid @enderror" 
                                      name="meta_description">{{ old('meta_description') }}</textarea>
                            @error('meta_description')
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
                        <h5 class="card-title">Visibility</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded border border-light">
                            <div>
                                <h6 class="mb-1 text-dark fw-bold">Status</h6>
                                <p class="text-muted font-size-12 mb-0">Enable or disable category</p>
                            </div>
                            <label class="switch">
                                <input type="checkbox" name="status" value="1" checked>
                                <span class="slider round"></span>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-submit w-100 rounded-pill">
                            <i class="fe-save me-1"></i> Save Childcategory
                        </button>
                    </div>
                </div>

                <div class="card bg-soft-secondary border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <i class="fe-help-circle font-size-18 me-2 text-secondary"></i>
                            <p class="mb-0 font-size-13 text-secondary">
                                Childcategories are the 3rd level of categorization (Category -> Subcategory -> Childcategory). Use them for specific item grouping.
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
        // Cleaner Summernote Toolbar
        $(".summernote").summernote({
            placeholder: "Enter SEO description...",
            height: 120,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol']],
                ['misc', ['fullscreen', 'codeview']]
            ]
        });
        
        // Select2 with custom placeholder styling
        $(".select2").select2({
            width: '100%'
        });
    });
</script>
@endsection