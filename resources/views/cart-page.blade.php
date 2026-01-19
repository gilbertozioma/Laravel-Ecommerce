@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 56rem;">
    <div class="py-4">
        <h1 class="display-6 fw-bold mb-4">Your Cart</h1>

        <div data-cart-items></div>

        <div data-cart-summary style="display: none;">
            <div class="mt-5 pt-4 border-top">
                <div class="row">
                    <div class="col-6">
                        <button data-action="clear-cart" class="btn btn-outline-danger">
                            <svg class="icon me-2" width="16" height="16" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                            Clear Cart
                        </button>
                    </div>
                    <div class="col-6 text-end">
                        <div class="fs-5 fw-bold text-dark mb-2">
                            Total: <span data-cart-total>$0.00</span>
                        </div>
                        <p class="text-muted small mb-3">Including all applicable taxes</p>
                        <button data-action="place-order" class="btn btn-success fw-semibold">
                            <svg class="icon me-2" width="20" height="20" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            Place Order
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="/checkout" class="btn btn-warning">Proceed to Checkout</a>
        </div>
    </div>
</div>

<script>
    // Initialize cart display on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            window.dispatchEvent(new CustomEvent('cartUpdated'));
        });
    } else {
        window.dispatchEvent(new CustomEvent('cartUpdated'));
    }
</script>
@endsection