@extends('backEnd.layouts.master')
@section('title','Inhouse Products')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0">Inhouse Products</h4>
    <a href="{{ route('products.create') }}" class="btn btn-sm btn-danger">Add product</a>
  </div>
  <div class="card">
    <div class="card-body">
      <div class="d-flex justify-content-between flex-wrap gap-2 mb-3">
        <div>
          <button data-url="{{ route('products.update_deals') }}" data-status="1" class="btn btn-sm btn-outline-success hotdeal_update">Set deal</button>
          <button data-url="{{ route('products.update_status') }}" data-status="1" class="btn btn-sm btn-primary update_status">Active</button>
          <button data-url="{{ route('products.update_status') }}" data-status="0" class="btn btn-sm btn-light update_status">Inactive</button>
        </div>
        <form method="GET" action="{{ route('inhouse.products.index') }}" data-ajax-form class="d-flex gap-2">
          <input type="text" name="keyword" class="form-control" placeholder="Search..." value="{{ request('keyword') }}">
          <button class="btn btn-primary">Search</button>
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
document.addEventListener('click', function(e){
  var btn = e.target.closest('.hotdeal_update, .update_status');
  if(!btn) return;
  e.preventDefault();
  var ids = Array.prototype.map.call(document.querySelectorAll('input.checkbox:checked'), function(el){ return el.value; });
  if(!ids.length){ if(window.toastr) toastr.error('Select products'); return; }
  fetch(btn.getAttribute('data-url'), {
    method:'POST',
    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'},
    body: JSON.stringify({product_ids: ids, status: btn.getAttribute('data-status')})
  }).then(r=>r.json()).then(function(res){
    if(window.toastr) toastr[res.status==='success'?'success':'error'](res.message||'Done');
    if(res.status==='success') location.reload();
  });
});
</script>
@endsection
