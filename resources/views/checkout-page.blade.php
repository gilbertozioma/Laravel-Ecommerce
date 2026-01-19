@extends('layouts.app')

@section('content')
<div class="py-5" style="background: linear-gradient(to right, #f9fafb, #f0fdf4);">
    <div class="container-fluid">
        <div class="mb-5">
            <div class="mb-3">
                <a href="/cart" class="btn btn-link text-success ps-0">
                    <svg class="icon me-2" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                    Back to Cart
                </a>
                <h1 class="display-5 fw-bold mt-2">Checkout</h1>
            </div>
            <p class="text-muted ps-4">Complete your purchase in just a few steps</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow mb-4">
                    <div class="card-header bg-success text-white py-3">
                        <h5 class="card-title mb-0 d-flex align-items-center">
                            <svg class="icon me-2" width="32" height="32" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Order Confirmation
                        </h5>
                    </div>
                    <div class="card-body">
                        <div>
                            @php $items = collect($items); @endphp
                            @if($items->isEmpty())
                            <div class="text-center py-5">
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-4"
                                    style="width: 96px; height: 96px;">
                                    <svg class="text-warning" width="48" height="48" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                </div>
                                <h4 class="fw-bold text-dark mb-2">No items to checkout</h4>
                                <p class="text-muted mb-4">Add items to your cart first</p>
                                <a href="/shop" class="btn btn-warning">
                                    Start Shopping
                                </a>
                            </div>
                            @else
                            <div class="space-y-3">
                                @foreach($items as $item)
                                <div class="card border-1 bg-light" style="transition: all 0.2s ease;">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <div class="bg-white rounded d-flex align-items-center justify-content-center"
                                                    style="width: 64px; height: 64px;">
                                                    <svg class="text-success" width="32" height="32" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="1"
                                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <h6 class="fw-bold text-dark mb-2">{{ $item->product->name }}</h6>
                                                <div class="d-flex gap-3">
                                                    <span class="fw-semibold text-success">${{
                                                        number_format($item->product->price, 2) }}</span>
                                                    <span class="text-muted">•</span>
                                                    <small class="text-muted">Qty: {{ $item->quantity }}</small>
                                                </div>
                                            </div>
                                            <div class="col-auto text-end">
                                                <div class="fs-6 fw-bold text-dark">${{
                                                    number_format($item->product->price * $item->quantity, 2) }}</div>
                                                <small class="text-muted">Total for this item</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div class="mt-5 pt-4 border-top">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Subtotal</span>
                                            <span class="text-muted">${{ number_format($items->sum(fn($i) =>
                                                $i->quantity * $i->product->price), 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Shipping</span>
                                            <span class="text-success">Free</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Tax (10%)</span>
                                            <span class="text-muted">${{ number_format($items->sum(fn($i) =>
                                                $i->quantity * $i->product->price) * 0.1, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-12 border-top pt-3">
                                        <div class="d-flex justify-content-between">
                                            <span class="fs-5 fw-bold">Total Amount</span>
                                            <span class="fs-5 fw-bold text-success">${{ number_format($items->sum(fn($i)
                                                => $i->quantity * $i->product->price) * 1.1, 2) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button wire:click="placeOrder"
                                        class="btn btn-success btn-lg w-100 fw-bold d-flex align-items-center justify-content-center gap-2">
                                        <svg class="icon" width="24" height="24" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                            </path>
                                        </svg>
                                        <span>Confirm & Pay ${{ number_format($items->sum(fn($i) => $i->quantity *
                                            $i->product->price) * 1.1, 2) }}</span>
                                    </button>

                                    <p class="text-center text-muted small mt-3">
                                        By completing your purchase, you agree to our Terms of Service
                                    </p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow">
                    <div class="card-header py-3 border-bottom">
                        <h5 class="card-title mb-0">Payment Information</h5>
                    </div>
                    <div class="card-body">
                        <form class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Card Number</label>
                                <input type="text" placeholder="1234 5678 9012 3456" class="form-control" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Expiry Date</label>
                                <input type="text" placeholder="MM/YY" class="form-control" />
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Cardholder Name</label>
                                <input type="text" placeholder="John Doe" class="form-control" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow sticky-top" style="top: 2rem;">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-4">Order Summary</h5>

                        @php
                        $items = \App\Models\CartItem::with('product')
                        ->where('user_id', auth()->id())
                        ->get();
                        $subtotal = $items->sum(fn($i) => $i->quantity * $i->product->price);
                        $tax = $subtotal * 0.1;
                        $total = $subtotal + $tax;
                        @endphp

                        <div class="space-y-3 mb-6">
                            <div class="d-flex justify-content-between mb-2 text-muted">
                                <span>Subtotal</span>
                                <span>${{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 text-muted">
                                <span>Shipping</span>
                                <span class="text-success">Free</span>
                            </div>
                            <div class="d-flex justify-content-between text-muted">
                                <span>Tax (10%)</span>
                                <span>${{ number_format($tax, 2) }}</span>
                            </div>
                            <div class="border-top pt-3 mt-3">
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>Total</span>
                                    <span class="text-success">${{ number_format($total, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-success d-flex gap-2 mb-4">
                            <svg class="icon flex-shrink-0" width="20" height="20" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <strong class="d-block">Secure Payment</strong>
                                <small>Your payment information is encrypted and secure.</small>
                            </div>
                        </div>

                        <button data-action="place-order"
                            class="btn btn-success btn-lg w-100 fw-bold d-flex align-items-center justify-content-center gap-2">
                            <svg class="icon" width="20" height="20" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                            Complete Purchase
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection