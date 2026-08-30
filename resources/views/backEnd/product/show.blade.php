@extends('backEnd.layouts.master')
@section('title','Product Details')

@section('content')
<div class="container-fluid">
    <div class="page-title-box d-flex align-items-center justify-content-between py-3">
        <div>
            <h4 class="page-title mb-1 text-dark fw-bold">Product Details</h4>
            <p class="text-muted font-size-13 mb-0">Review product information, pricing, media, stock, variants and publishing state.</p>
        </div>
        <div class="page-title-right gap-2 d-flex flex-wrap">
            <form action="{{ route('admin.facebook_page.post_product', $product->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary rounded-pill shadow-sm" title="Post to Facebook Page">
                    <i class="fe-facebook me-1"></i> Post to Facebook
                </button>
            </form>
            <a href="{{route('products.edit', $product->id)}}" class="btn btn-info rounded-pill shadow-sm">
                <i class="fe-edit me-1"></i> Edit Product
            </a>
            <a href="{{route('products.index')}}" class="btn btn-light rounded-pill shadow-sm">
                <i class="fe-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="product-detail-side">
                <div class="card">
                    <div class="card-body">
                        <div class="pro-img-details shadow-sm">
                            <img src="{{ asset($product->image ? $product->image->image : 'storage/uploads/placeholder.png') }}" alt="{{ $product->name }}" id="main_image">
                        </div>

                        @if($product->images->count() > 0)
                            <div class="pro-thumb-list mt-3">
                                <img src="{{ asset($product->image ? $product->image->image : 'storage/uploads/placeholder.png') }}" class="pro-thumb-img" onclick="changeImage(this.src)">
                                @foreach($product->images as $img)
                                    <img src="{{ asset($img->image) }}" class="pro-thumb-img" onclick="changeImage(this.src)">
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Catalog Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="dash-cust">
                            <div>
                                <strong>Brand</strong>
                                <span class="text-muted d-block mt-1">{{ $product->brand ? $product->brand->name : 'No Brand' }}</span>
                            </div>
                            <i class="fe-box text-primary"></i>
                        </div>
                        <div class="dash-cust">
                            <div>
                                <strong>Product Type</strong>
                                <span class="text-muted d-block mt-1">{{ $product->is_digital ? 'Digital Product' : 'Physical Product' }}</span>
                            </div>
                            <i class="fe-layers text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card">
                <div class="card-body">
                    <h3 class="pro-title">{{ $product->name }}</h3>

                    <div class="mb-3 mt-3 d-flex flex-wrap gap-2">
                        @if($product->status == 1)
                            <span class="badge badge-soft-success px-2 py-1">Active</span>
                        @else
                            <span class="badge badge-soft-danger px-2 py-1">Inactive</span>
                        @endif

                        @if($product->topsale == 1)
                            <span class="badge badge-soft-warning px-2 py-1"><i class="fe-zap"></i> Hot Deal</span>
                        @endif

                        @if($product->feature_product == 1)
                            <span class="badge badge-soft-primary px-2 py-1"><i class="fe-star"></i> Featured</span>
                        @endif

                    </div>

                    <div class="mt-3">
                        <span class="price-tag">৳{{ number_format($product->new_price, 2) }}</span>
                        @if($product->old_price)
                            <span class="old-price">৳{{ number_format($product->old_price, 2) }}</span>
                            <small class="text-danger ms-1">({{ round((($product->old_price - $product->new_price) / $product->old_price) * 100) }}% OFF)</small>
                        @endif
                    </div>

                    <div class="stock-box mt-3 mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted fw-bold">Current Stock</p>
                                <h4 class="mb-0 {{ $product->stock <= 5 ? 'text-danger' : 'text-success' }}">
                                    {{ $product->stock }} <small class="font-size-14 text-muted">{{ $product->pro_unit ?? 'pcs' }}</small>
                                </h4>
                            </div>
                            <div class="col-md-6 border-start">
                                <p class="mb-1 text-muted fw-bold">Purchase Price</p>
                                <h5 class="mb-0 text-dark">৳{{ number_format($product->purchase_price, 2) }}</h5>
                            </div>
                        </div>
                    </div>

                    <h5 class="font-size-15 mb-3 text-uppercase text-muted">Specifications</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0 product-info-table">
                            <tbody>
                                <tr>
                                    <th>Product Code</th>
                                    <td>#{{ $product->product_code ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Category</th>
                                    <td>
                                        {{ $product->category ? $product->category->name : 'N/A' }}
                                        @if($product->subcategory)
                                            <i class="fe-chevron-right mx-1 font-size-10"></i> {{ $product->subcategory->subcategoryName ?? $product->subcategory->name }}
                                        @endif
                                        @if($product->childcategory)
                                            <i class="fe-chevron-right mx-1 font-size-10"></i> {{ $product->childcategory->childcategoryName ?? $product->childcategory->name }}
                                        @endif
                                    </td>
                                </tr>
                                @if($product->is_digital)
                                <tr>
                                    <th>Digital File</th>
                                    <td>
                                        @if($product->digital_file)
                                            <a href="#" class="text-primary"><i class="fe-download me-1"></i> Download File</a>
                                        @else
                                            <span class="text-muted">No file uploaded</span>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                @if($product->variantPrices && $product->variantPrices->count() > 0)
                                <tr>
                                    <th>Product Variants</th>
                                    <td>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Color</th>
                                                        <th>Size</th>
                                                        <th>Price</th>
                                                        <th>Stock</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($product->variantPrices as $variant)
                                                    <tr>
                                                        <td>{{ $variant->color ? ($variant->color->colorName ?? $variant->color->name) : 'N/A' }}</td>
                                                        <td>{{ $variant->size ? ($variant->size->sizeName ?? $variant->size->name) : 'N/A' }}</td>
                                                        <td>৳{{ number_format($variant->price, 2) }}</td>
                                                        <td>{{ $variant->stock ?? 0 }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Short Note</th>
                                    <td>{{ $product->note ?? 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <h5 class="font-size-15 mb-3 text-uppercase text-muted">Description</h5>
                        <div class="product-description-box">
                            @if($product->description)
                                {!! $product->description !!}
                            @else
                                <span class="text-muted font-italic">No description available.</span>
                            @endif
                        </div>
                    </div>

                    @php $showVideoType = $product->pro_video_type ?? ($product->pro_video ? 'youtube' : null); @endphp
                    @if($showVideoType === 'youtube' && $product->pro_video)
                    <div class="mt-4">
                        <h5 class="font-size-15 mb-2">Product Video</h5>
                        <a href="https://www.youtube.com/watch?v={{ $product->pro_video }}" target="_blank" class="btn btn-outline-danger btn-sm mb-2">
                            <i class="fa fa-youtube-play me-1"></i> YouTube-এ দেখুন
                        </a>
                        <div class="product-video-frame">
                            <iframe height="250" src="https://www.youtube.com/embed/{{ $product->pro_video }}" frameborder="0" allowfullscreen></iframe>
                        </div>
                    </div>
                    @elseif($showVideoType === 'upload' && $product->pro_video_path)
                    <div class="mt-4">
                        <h5 class="font-size-15 mb-2">Product Video <span class="badge bg-primary" style="font-size:11px;">Hosted</span></h5>
                        <div class="product-video-frame">
                            <video height="250" controls>
                                <source src="{{ asset($product->pro_video_path) }}" type="video/mp4">
                                <source src="{{ asset($product->pro_video_path) }}" type="video/webm">
                            </video>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function changeImage(src) {
    document.getElementById('main_image').src = src;
}
</script>
@endsection
