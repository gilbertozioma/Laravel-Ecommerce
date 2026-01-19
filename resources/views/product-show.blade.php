@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 900px;">
    <div class="py-4">
        <!-- Back Button -->
        <a href="{{ url('/shop') }}" class="btn btn-outline-secondary btn-sm mb-4">
            <svg class="icon me-2" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Back to Shop
        </a>

        <!-- Product Detail -->
        <div class="row">
            <!-- Product Image -->
            <div class="col-lg-5 mb-4">
                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 400px;">
                    <svg class="text-secondary" width="120" height="120" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l-1.586-1.586a2 2 0 00-2.828 0L6 14m6-6l.01.01">
                        </path>
                    </svg>
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-lg-7">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h1 class="display-6 fw-bold mb-2">{{ $product->name }}</h1>
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="d-flex align-items-center">
                                <svg width="16" height="16" class="text-warning me-1" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                    </path>
                                </svg>
                                <span class="ms-1 small text-muted">(0 reviews)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Price and Stock -->
                <div class="bg-light rounded p-4 mb-4">
                    <div class="row">
                        <div class="col-6">
                            <div class="fs-5 text-muted mb-1">Price</div>
                            <div class="fs-3 fw-bold text-warning">${{ number_format($product->price, 2) }}</div>
                        </div>
                        <div class="col-6">
                            <div class="fs-5 text-muted mb-1">Stock Available</div>
                            <div class="fs-3 fw-bold text-dark">
                                @if($product->stock_quantity == 0)
                                <span class="badge bg-danger">Out of Stock</span>
                                @else
                                {{ $product->stock_quantity }} units
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <h5 class="fw-bold mb-2">Description</h5>
                    <p class="text-muted lh-lg">{{ $product->description }}</p>
                </div>

                <!-- Additional Details -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-2">
                            <svg class="text-success me-2" width="20" height="20" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span>Premium Quality</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <svg class="text-success me-2" width="20" height="20" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span>Fast Shipping</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-2">
                            <svg class="text-success me-2" width="20" height="20" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span>Secure Checkout</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <svg class="text-success me-2" width="20" height="20" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span>30-Day Returns</span>
                        </div>
                    </div>
                </div>

                <!-- Add to Cart -->
                <div class="d-flex gap-2 mb-4">
                    @if($product->stock_quantity == 0)
                    <button class="btn btn-secondary flex-grow-1" disabled>
                        <svg class="icon me-2" width="20" height="20" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        Out of Stock
                    </button>
                    @else
                    <button data-action="add-to-cart" data-id="{{ $product->id }}"
                        class="btn btn-warning flex-grow-1 fw-semibold">
                        <svg class="icon me-2" width="20" height="20" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        Add to Cart
                    </button>
                    @endif
                    <button class="btn btn-outline-secondary">
                        <svg class="icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                            </path>
                        </svg>
                    </button>
                </div>

                <!-- Product Code -->
                <div class="border-top pt-3">
                    <small class="text-muted">Product Code: <strong>#{{ str_pad($product->id, 5, '0', STR_PAD_LEFT)
                            }}</strong></small>
                </div>
            </div>
        </div>

        <!-- Related Products Section (Optional) -->
        <div class="mt-5 pt-4 border-top">
            <h3 class="fw-bold mb-4">Related Products</h3>
            <div class="row g-3">
                @php
                $related = \App\Models\Product::where('id', '!=', $product->id)->limit(4)->get();
                @endphp
                @if($related->count() > 0)
                @foreach($related as $item)
                <div class="col-sm-6 col-lg-3">
                    <div class="card h-100 border-1 shadow-sm position-relative overflow-hidden"
                        style="transition: all 0.3s ease;">
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                            style="height: 160px;">
                            <a href="{{ route('shop.show', $item) }}" class="text-decoration-none">
                                <svg class="text-secondary" width="64" height="64" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l-1.586-1.586a2 2 0 00-2.828 0L6 14m6-6l.01.01">
                                    </path>
                                </svg>
                            </a>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold text-truncate">
                                <a href="{{ route('shop.show', $item) }}" class="text-decoration-none text-dark">{{
                                    $item->name }}</a>
                            </h5>
                            <p class="card-text text-muted small" style="height: 40px; overflow: hidden;">{{
                                $item->description }}</p>
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-end">
                                    <div>
                                        <p class="fs-5 fw-bold text-dark mb-1">${{ number_format($item->price, 2) }}</p>
                                        <p class="text-muted small mb-0">Stock: {{ $item->stock_quantity }}</p>
                                    </div>
                                    <div>
                                        @if($item->stock_quantity == 0)
                                        <button class="btn btn-secondary btn-sm" disabled>
                                            Add to cart
                                        </button>
                                        @else
                                        <button data-action="add-to-cart" data-id="{{ $item->id }}"
                                            class="btn btn-warning btn-sm fw-semibold">
                                            Add to cart
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                @else
                <div class="col-12">
                    <p class="text-muted text-center py-4">No related products available</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection