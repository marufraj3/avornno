<div class="table-responsive">
  <table class="table table-hover mb-0">
    <thead>
      <tr>
        <th><input type="checkbox" class="checkall"></th>
        <th>SL</th>
        <th>Product</th>
        <th>Category</th>
        <th>Price / Stock</th>
        <th>Status</th>
        <th class="text-end">Action</th>
      </tr>
    </thead>
    <tbody>
      @forelse($data as $key => $value)
        @php
          $img = $value->image->image ?? 'storage/uploads/placeholder.png';
          $variantRows = $value->variantPrices ?? collect();
          $hasVariantStock = $variantRows->contains(fn($v) => $v->stock !== null);
          $totalStock = $hasVariantStock
              ? $variantRows->sum(fn($v) => max(0, (int) $v->stock))
              : (int) ($value->stock ?? 0);
          $categoryTrail = $value->category->name ?? '—';
          if (!empty($value->subcategory)) {
              $categoryTrail .= ' / ' . ($value->subcategory->subcategoryName ?? $value->subcategory->name);
          }
        @endphp
        <tr>
          <td><input type="checkbox" class="checkbox" value="{{ $value->id }}"></td>
          <td>{{ $data->firstItem() + $key }}</td>
          <td>
            <div class="product-box">
              <img src="{{ asset($img) }}" alt="{{ $value->name }}" loading="lazy" decoding="async" class="product-thumb">
              <div class="product-info">
                <strong title="{{ $value->name }}">{{ Str::limit($value->name, 52) }}</strong>
                <small>
                  {{ !empty($value->is_digital) ? 'Digital product' : 'Physical product' }}

                  @if(!empty($value->topsale)) · Hot deal @endif
                </small>
              </div>
            </div>
          </td>
          <td><span class="text-muted">{{ $categoryTrail }}</span></td>
          <td>
            <div class="product-price">৳{{ number_format((float)$value->new_price, 2) }}</div>
            <small class="text-muted">Stock {{ $totalStock }}</small>
          </td>
          <td>
            @if($value->status==1)
              <span class="badge bg-success">Active</span>
            @else
              <span class="badge bg-danger">Inactive</span>
            @endif
          </td>
          <td class="text-end">
            <div class="d-inline-flex align-items-center gap-2 flex-wrap justify-content-end">
              @if($value->status == 1)
                <form method="post" action="{{ route('products.inactive') }}" class="d-inline">
                  @csrf
                  <input type="hidden" name="hidden_id" value="{{ $value->id }}">
                  <button type="submit" class="action-btn btn-inactive change-confirm" title="Deactivate">
                    <i class="fe-eye-off"></i>
                  </button>
                </form>
              @else
                <form method="post" action="{{ route('products.active') }}" class="d-inline">
                  @csrf
                  <input type="hidden" name="hidden_id" value="{{ $value->id }}">
                  <button type="submit" class="action-btn btn-active change-confirm" title="Activate">
                    <i class="fe-eye"></i>
                  </button>
                </form>
              @endif

              <a href="{{ route('products.show', $value->id) }}" class="action-btn btn-edit" title="View Product">
                <i class="fe-eye"></i>
              </a>

              <a href="{{ route('products.edit', $value->id) }}" class="action-btn btn-edit" title="Edit Product">
                <i class="fe-edit"></i>
              </a>

              <form method="post" action="{{ route('products.destroy') }}" class="d-inline">
                @csrf
                <input type="hidden" name="hidden_id" value="{{ $value->id }}">
                <button type="submit" class="action-btn btn-delete delete-confirm" title="Delete Product">
                  <i class="fe-trash-2"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="7" class="text-center text-muted py-4">No products found</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="p-3 d-flex justify-content-between flex-wrap gap-2">
  <small class="text-muted">Showing {{ $data->count() ? $data->firstItem() : 0 }}–{{ $data->count() ? $data->lastItem() : 0 }} of {{ $data->total() }}</small>
  <div>{{ $data->links('pagination::bootstrap-4') }}</div>
</div>
