@extends('layouts.app')

@section('content')
<div class="container-fluid py-5">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="display-6 fw-bold mb-4">Products</h1>
            <div class="row g-3">
                @foreach($products as $product)
                <div class="col-sm-6 col-lg-3">
                    <div class="card h-100 border-1 shadow-sm position-relative overflow-hidden"
                        style="transition: all 0.3s ease;">
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                            style="height: 160px;">
                            <a href="{{ route('shop.show', $product) }}" class="text-decoration-none">
                                <svg class="text-warning" width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </a>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold text-truncate">
                                <a href="{{ route('shop.show', $product) }}" class="text-decoration-none text-dark">{{
                                    $product->name }}</a>
                            </h5>
                            <p class="card-text text-muted small" style="height: 40px; overflow: hidden;">{{
                                $product->description }}</p>
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-end">
                                    <div>
                                        <p class="fs-5 fw-bold text-dark mb-1">${{ number_format($product->price, 2)
                                            }}</p>
                                        <p class="text-muted small mb-0">Stock: {{ $product->stock_quantity }}</p>
                                    </div>
                                    <div>
                                        @if($product->stock_quantity == 0)
                                        <button class="btn btn-secondary btn-sm" disabled>
                                            Add to cart
                                        </button>
                                        @else
                                        <button data-action="add-to-cart" data-id="{{ $product->id }}"
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
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
</div>
@endsection