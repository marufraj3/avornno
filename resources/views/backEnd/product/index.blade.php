@extends('backEnd.layouts.master')
@section('title','Product Management')

@section('content')
<div class="container-fluid">
    <div class="page-title-box d-flex align-items-center justify-content-between py-3">
        <div>
            <h4 class="page-title mb-1 text-dark fw-bold">Product Management</h4>
            <p class="text-muted font-size-13 mb-0">Manage catalog items, bulk actions, stock visibility and approval workflow.</p>
        </div>
        <div class="page-title-right">
            <a href="{{route('products.pending')}}" class="btn btn-warning rounded-pill shadow-sm me-2">
                <i class="fe-clock me-1"></i> Pending Products
            </a>
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

                <form method="GET" action="{{ route('products.index') }}" data-ajax-form class="product-search-form">
                    <div class="input-group">
                        <input type="text" name="keyword" class="form-control form-control-sm" placeholder="Search by product name..." value="{{ request('keyword') }}">
                        <button class="btn btn-sm btn-info" type="submit">Search</button>
                    </div>
                </form>
            </div>

            <div data-ajax-list>
                @include('backEnd.product._rows')
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('public/backEnd/assets/js/admin-ajax-list.js') }}"></script>
<script>
$(function(){
    $(document).on('change', '.checkall', function(){
        $('.checkbox').prop('checked', $(this).is(':checked'));
    });

    function getCheckedIds() {
        return $('input.checkbox:checked').map(function(){ return $(this).val(); }).get();
    }

    function sendBulkRequest(url, status) {
        var ids = getCheckedIds();
        if(ids.length === 0){
            if (typeof toastr !== 'undefined') toastr.error('Please select at least one product!');
            else alert('Please select at least one product!');
            return;
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: JSON.stringify({ product_ids: ids, status: status }),
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(res){
                if(res.status === 'success'){
                    if (typeof toastr !== 'undefined') toastr.success(res.message);
                    setTimeout(function(){ location.reload(); }, 800);
                } else if (typeof toastr !== 'undefined') {
                    toastr.error(res.message || 'Action failed');
                }
            },
            error: function(xhr){
                let msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Server Error';
                if (typeof toastr !== 'undefined') toastr.error(msg); else alert(msg);
            }
        });
    }

    $(document).on('click', '.hotdeal_update, .update_status', function(e){
        e.preventDefault();
        var url = $(this).data('url');
        var status = $(this).data('status');
        if(url) sendBulkRequest(url, status);
    });
});
</script>
@endsection
