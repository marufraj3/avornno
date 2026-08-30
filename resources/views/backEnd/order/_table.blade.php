<div class="table-responsive">
  <table class="table otable mb-0" id="orderTable">
    <thead>
      <tr>
        <th><input type="checkbox" class="checkall"></th>
        <th>Invoice</th>
        <th>Customer</th>
        <th>Amount</th>
        <th>Status</th>
        <th>Courier</th>
        <th>Date</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
    @forelse($show_data as $value)
      @php
        $paid = (float) ($value->paid_amount ?? optional($value->payment)->amount ?? 0);
        $name = $value->shipping->name ?? '';
        $phone = $value->shipping->phone ?? '';
        $track = $value->courier_tracking_id ?: $value->consignment_id;
      @endphp
      <tr data-filter="{{ strtolower($value->invoice_id.' '.$name.' '.$phone) }}">
        <td><input type="checkbox" class="checkbox" value="{{ $value->id }}"></td>
        <td><strong>#{{ $value->invoice_id }}</strong></td>
        <td>{{ $name }}<br><small class="text-muted">{{ $phone }}</small></td>
        <td>৳{{ number_format((float)$value->amount, 2) }}<br><small class="text-success">Paid {{ number_format($paid, 2) }}</small></td>
        <td><span class="badge bg-info">{{ $value->status->name ?? '—' }}</span></td>
        <td>{{ $track ? ucfirst($value->courier_type ?: 'courier') : '—' }}</td>
        <td>{{ optional($value->updated_at)->format('d M, h:i A') }}</td>
        <td class="oact">
          <a href="{{ route('admin.order.invoice', ['invoice_id'=>$value->invoice_id]) }}">View</a>
          <a href="{{ route('admin.order.edit', ['invoice_id'=>$value->invoice_id]) }}">Edit</a>
          <button type="button" class="quick-order-view" data-url="{{ route('admin.order.quick_view_json', ['invoice_id'=>$value->invoice_id]) }}">Quick</button>
        </td>
      </tr>
    @empty
      <tr><td colspan="8" class="text-center text-muted py-4">No orders</td></tr>
    @endforelse
    </tbody>
  </table>
</div>
<div class="p-3">{{ $show_data->links('pagination::bootstrap-4') }}</div>
