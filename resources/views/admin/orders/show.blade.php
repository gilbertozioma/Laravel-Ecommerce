@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 900px;">
    <div class="py-4">
        <!-- Back Button -->
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm mb-4">
            <svg class="icon me-2" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Back to Orders
        </a>

        <!-- Header -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <h1 class="display-6 fw-bold">Order #{{ $order->id }}</h1>
                <p class="text-muted">Created on {{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span
                    class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : ($order->status === 'shipped' ? 'info' : 'warning')) }} fs-5">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="row g-4">
            <!-- Order Items -->
            <div class="col-lg-8">
                <div class="card border-1 shadow-sm mb-4">
                    <div class="card-header bg-light border-0 py-3">
                        <h5 class="fw-bold mb-0">Order Items</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="border-bottom">
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-end">Qty</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item->product->name }}</strong><br>
                                            <small class="text-muted">SKU: #{{ str_pad($item->product_id, 5, '0',
                                                STR_PAD_LEFT) }}</small>
                                        </td>
                                        <td class="text-end">${{ number_format($item->price, 2) }}</td>
                                        <td class="text-end">{{ $item->quantity }}</td>
                                        <td class="text-end fw-bold">${{ number_format($item->price * $item->quantity,
                                            2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Customer Info -->
                <div class="card border-1 shadow-sm">
                    <div class="card-header bg-light border-0 py-3">
                        <h5 class="fw-bold mb-0">Customer Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="text-muted small mb-1">Full Name</p>
                                <p class="fw-semibold">{{ $order->user->name }}</p>

                                <p class="text-muted small mb-1 mt-3">Email</p>
                                <p class="fw-semibold">
                                    <a href="mailto:{{ $order->user->email }}">{{ $order->user->email }}</a>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted small mb-1">Member Since</p>
                                <p class="fw-semibold">{{ $order->user->created_at->format('F d, Y') }}</p>

                                <p class="text-muted small mb-1 mt-3">Total Orders</p>
                                <p class="fw-semibold">{{ $order->user->orders()->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary & Status Update -->
            <div class="col-lg-4">
                <!-- Order Summary -->
                <div class="card border-1 shadow-sm mb-4">
                    <div class="card-header bg-light border-0 py-3">
                        <h5 class="fw-bold mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal:</span>
                            <span>${{ number_format($order->total, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span class="text-muted">Shipping:</span>
                            <span>Free</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong class="text-warning fs-5">${{ number_format($order->total, 2) }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Update Status -->
                <div class="card border-1 shadow-sm">
                    <div class="card-header bg-light border-0 py-3">
                        <h5 class="fw-bold mb-0">Update Status</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <div class="mb-3">
                                <label for="status" class="form-label small text-muted">Order Status</label>
                                <select name="status" id="status" class="form-select form-select-lg">
                                    @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <svg class="icon me-2" width="16" height="16" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Update Status
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Order Notes -->
                <div class="card border-1 shadow-sm mt-4">
                    <div class="card-header bg-light border-0 py-3">
                        <h5 class="fw-bold mb-0">Additional Info</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-1">Order ID</p>
                        <p class="fw-semibold font-monospace">#{{ $order->id }}</p>

                        <p class="text-muted small mb-1 mt-3">Order Date</p>
                        <p class="fw-semibold">{{ $order->created_at->format('F d, Y H:i:s') }}</p>

                        <p class="text-muted small mb-1 mt-3">Items Count</p>
                        <p class="fw-semibold">{{ $order->items->count() }} item(s)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection