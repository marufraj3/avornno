@extends('backEnd.layouts.master')
@section('title','Pending Products')

@section('content')
<div class="container-fluid">
    <div class="page-title-box d-flex align-items-center justify-content-between py-3">
        <div>
            <h4 class="page-title mb-1 text-dark fw-bold">Pending Approvals</h4>
            <p class="text-muted font-size-13 mb-0">Review submitted products, approve ready items and reject incomplete ones.</p>
        </div>
        <div class="page-title-right">
            <a href="{{route('products.index')}}" class="btn btn-light rounded-pill shadow-sm px-4">
                <i class="fe-arrow-left me-1"></i> Back to Products
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="card-title mb-1">Waiting for Approval</h5>
                <small class="text-muted">Manually verify product quality, category and pricing before publishing.</small>
            </div>

            <form class="product-search-form" method="GET" action="{{ route('products.pending') }}">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fe-search"></i></span>
                    <input type="text" name="keyword" class="form-control" placeholder="Search pending product..." value="{{ request('keyword') }}">
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Product Details</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $key=>$value)
                    <tr>
                        <td>{{ $data->firstItem() + $key }}</td>
                        <td>
                            <div class="product-box">
                                <img src="{{ asset($value->image ? $value->image->image : 'storage/uploads/placeholder.png') }}" class="product-img" alt="Product">
                                <div class="product-info">
                                    <h6 class="text-truncate" style="max-width: 250px;" title="{{$value->name}}">{{$value->name}}</h6>
                                    @php $isDigital = isset($value->is_digital) ? (bool) $value->is_digital : ($value->product_type === 'digital'); @endphp
                                    <small>
                                        {{ $isDigital ? 'Digital Product' : 'Physical Product' }}
                                        @if($value->brand) · {{ $value->brand->name }} @endif
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $value->category ? $value->category->name : 'N/A' }}</td>
                        <td><span class="product-price">৳{{ number_format($value->new_price, 2) }}</span></td>
                        <td>
                            @if($value->stock > 0)
                                <span class="badge badge-soft-success">{{ $value->stock }}</span>
                            @else
                                <span class="badge badge-soft-danger">Out of Stock</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-pill badge-soft-warning">
                                <i class="fe-clock me-1"></i> Pending
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex align-items-center gap-2 flex-wrap justify-content-end">
                                <a href="{{route('products.show',$value->id)}}" class="action-btn btn-edit" title="View Details">
                                    <i class="fe-eye"></i>
                                </a>
                                <a href="{{route('products.edit',$value->id)}}" class="action-btn btn-edit" title="Edit">
                                    <i class="fe-edit"></i>
                                </a>

                                <form method="POST" action="{{ route('products.approve') }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $value->id }}">
                                    <button type="submit" class="btn-approve change-confirm">
                                        <i class="fe-check me-1"></i> Approve
                                    </button>
                                </form>

                                <button type="button" class="btn-reject" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $value->id }}">
                                    <i class="fe-x me-1"></i> Reject
                                </button>
                            </div>

                            <div class="modal fade" id="rejectModal{{ $value->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title text-danger"><i class="fe-alert-triangle me-2"></i>Reject Product</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form method="POST" action="{{ route('products.reject') }}">
                                            @csrf
                                            <div class="modal-body text-start">
                                                <input type="hidden" name="id" value="{{ $value->id }}">
                                                <p class="mb-2">Are you sure you want to reject <strong>{{ $value->name }}</strong>?</p>
                                                <div class="form-group mt-3">
                                                    <label class="form-label small fw-bold">Rejection Reason (Optional)</label>
                                                    <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Explain why the product is rejected..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-sm btn-danger">Confirm Rejection</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="product-empty">
                                <img src="{{ asset('public/backEnd/assets/images/small/img-1.jpg') }}" alt="No pending data">
                                <h5 class="text-muted">No pending approvals!</h5>
                                <p class="text-muted mb-0">All products have been processed.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-body border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="text-muted small">
                Showing {{ $data->count() ? $data->firstItem() : 0 }} to {{ $data->count() ? $data->lastItem() : 0 }} of {{ $data->total() }} results
            </div>
            <div>{{ $data->links('pagination::bootstrap-4') }}</div>
        </div>
    </div>
</div>
@endsection
