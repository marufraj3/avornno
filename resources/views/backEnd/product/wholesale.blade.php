@extends('backEnd.layouts.master')
@section('title','Wholesale Products')

@section('content')
<div class="container-fluid">
    <div class="page-title-box d-flex align-items-center justify-content-between py-3">
        <div>
            <h4 class="page-title mb-1 text-dark fw-bold">Wholesale Products</h4>
            <p class="text-muted font-size-13 mb-0">Manage wholesale-ready products, filter tier-based items and run bulk status actions.</p>
        </div>
        <div class="page-title-right">
            <a href="{{route('products.create')}}" class="btn btn-danger rounded-pill shadow-sm">
                <i class="fe-plus me-1"></i> Add New Product
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="product-toolbar">
                <div class="product-bulk-actions">
                    <button data-url="{{ route('products.update_deals') }}" data-status="1" class="btn btn-sm btn-outline-success rounded-pill hotdeal_update">
                        <i class="fe-thumbs-up me-1"></i> Set Deal
                    </button>
                    <button data-url="{{ route('products.update_deals') }}" data-status="0" class="btn btn-sm btn-outline-danger rounded-pill hotdeal_update">
                        <i class="fe-thumbs-down me-1"></i> Remove Deal
                    </button>
                    <div class="vr mx-1 d-none d-lg-block"></div>
                    <button data-url="{{ route('products.update_status') }}" data-status="1" class="btn btn-sm btn-primary rounded-pill update_status">
                        <i class="fe-check me-1"></i> Active Selected
                    </button>
                    <button data-url="{{ route('products.update_status') }}" data-status="0" class="btn btn-sm btn-light border rounded-pill update_status">
                        <i class="fe-x me-1"></i> Inactive Selected
                    </button>
                </div>

                <form method="GET" action="{{ route('admin.products.wholesale') }}" class="product-search-form">
                    <div class="row g-2">
                        <div class="col-12">
                            <div class="input-group">
                                <input type="text" name="keyword" class="form-control form-control-sm" placeholder="Search by name..." value="{{ request('keyword') }}">
                                <button class="btn btn-sm btn-info px-3" type="submit"><i class="fe-search"></i></button>
                            </div>
                        </div>
                        @if($categories && $categories->count() > 0)
                        <div class="col-12">
                            <select name="category_id" class="form-control form-control-sm" onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col-12">
                            <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                                <option value="">All Status</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th><input class="checkall" type="checkbox" id="checkAll"></th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Wholesale Tiers</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $product)
                        <tr>
                            <td><input class="checkbox product-checkbox" type="checkbox" value="{{ $product->id }}" name="product_ids[]"></td>
                            <td>
                                <div class="product-box">
                                    <img src="{{ asset($product->image ? $product->image->image : 'public/uploads/default/no-image.png') }}" alt="{{ $product->name }}" class="product-img">
                                    <div class="product-info">
                                        <strong>{{ Str::limit($product->name, 40) }}</strong>
                                        <small>SKU: {{ $product->sku ?? 'N/A' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge badge-soft-info">{{ $product->category->name ?? 'N/A' }}</span></td>
                            <td>
                                @if($product->wholesalePrices && $product->wholesalePrices->count() > 0)
                                    <span class="badge badge-soft-success">{{ $product->wholesalePrices->count() }} Tier(s)</span>
                                @else
                                    <span class="badge badge-soft-warning">No Tiers</span>
                                @endif
                            </td>
                            <td>
                                <div class="product-price">৳{{ number_format($product->new_price, 2) }}</div>
                                @if($product->old_price && $product->old_price > $product->new_price)
                                    <small class="text-muted text-decoration-line-through">৳{{ number_format($product->old_price, 2) }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $product->stock > 0 ? 'badge-soft-success' : 'badge-soft-danger' }}">{{ $product->stock ?? 0 }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $product->status == 1 ? 'badge-soft-success' : 'badge-soft-danger' }}">{{ $product->status == 1 ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-2 flex-wrap justify-content-end">
                                    <a href="{{ route('products.show', $product->id) }}" class="action-btn btn-edit" title="View"><i class="fe-eye"></i></a>
                                    <a href="{{ route('products.edit', $product->id) }}" class="action-btn btn-edit" title="Edit"><i class="fe-edit"></i></a>
                                    <form method="post" action="{{ route('products.duplicate', $product->id) }}" class="d-inline" onsubmit="return confirm('Duplicate this product with all of its details?');">
                                        @csrf
                                        <button type="submit" class="action-btn btn-edit" title="Duplicate"><i class="fe-copy"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="product-empty">
                                    <i class="fe-package" style="font-size:48px; opacity:.3;"></i>
                                    <p class="mt-2 mb-0 text-muted">No wholesale products found</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($data->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $data->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
(function () {
    function selectedIds() {
        return Array.from(document.querySelectorAll('.product-checkbox:checked')).map(function (item) {
            return item.value;
        });
    }

    var checkAll = document.getElementById('checkAll');
    if (checkAll) {
        checkAll.addEventListener('change', function () {
            document.querySelectorAll('.product-checkbox').forEach(function (checkbox) {
                checkbox.checked = checkAll.checked;
            });
        });
    }

    function runBulk(url, status, actionText) {
        var ids = selectedIds();
        if (!ids.length) {
            if (window.toastr) toastr.error('Please select at least one product');
            else alert('Please select at least one product');
            return;
        }

        if (!confirm(actionText)) return;

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ product_ids: ids, status: status })
        })
        .then(function (response) { return response.json(); })
        .then(function (data) {
            if (data.success || data.status === 'success') {
                if (window.toastr) toastr.success(data.message || 'Updated successfully');
                setTimeout(function () { location.reload(); }, 700);
            } else {
                if (window.toastr) toastr.error(data.message || 'Something went wrong');
                else alert(data.message || 'Something went wrong');
            }
        })
        .catch(function () {
            if (window.toastr) toastr.error('Something went wrong');
            else alert('Something went wrong');
        });
    }

    document.querySelectorAll('.update_status').forEach(function (button) {
        button.addEventListener('click', function () {
            var status = this.getAttribute('data-status');
            runBulk(this.getAttribute('data-url'), status, 'Are you sure you want to ' + (status == '1' ? 'activate' : 'deactivate') + ' selected products?');
        });
    });

    document.querySelectorAll('.hotdeal_update').forEach(function (button) {
        button.addEventListener('click', function () {
            var status = this.getAttribute('data-status');
            runBulk(this.getAttribute('data-url'), status, 'Are you sure you want to ' + (status == '1' ? 'set' : 'remove') + ' hot deal for selected products?');
        });
    });
})();
</script>
@endsection
