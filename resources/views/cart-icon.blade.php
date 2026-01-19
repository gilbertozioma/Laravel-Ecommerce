<div class="position-relative cart-icon-root">
    <a href="/cart" class="d-flex align-items-center gap-2 text-decoration-none text-dark text-hover-dark">
        <svg width="30px" height="30px" viewBox="0 0 30 30" xmlns="http://www.w3.org/2000/svg">
            <path cx="16.5" cy="18.5" r="1.5"
                d="M22.5 23.125a1.875 1.875 0 0 1 -1.875 1.875 1.875 1.875 0 0 1 -1.875 -1.875 1.875 1.875 0 0 1 3.75 0" />
            <path cx="9.5" cy="18.5" r="1.5"
                d="M13.75 23.125a1.875 1.875 0 0 1 -1.875 1.875 1.875 1.875 0 0 1 -1.875 -1.875 1.875 1.875 0 0 1 3.75 0" />
            <path
                d="M22.5 20H10a1.248 1.248 0 0 1 -1.198 -0.891L5.32 7.5H3.75a1.25 1.25 0 0 1 0 -2.5h2.5a1.248 1.248 0 0 1 1.198 0.891l0.482 1.609H26.25a1.25 1.25 0 0 1 1.171 1.69l-3.75 10a1.248 1.248 0 0 1 -1.171 0.809m-11.57 -2.5h10.704l2.813 -7.5H8.68z" />
        </svg>
        <span class="visually-hidden">Cart</span>
        @php
            $count = 0;
            if (auth()->check()) {
                $count = \App\Models\CartItem::where('user_id', auth()->id())->sum('quantity');
            } else {
                // For guests, count items from session
                $sessionCart = session()->get('cart', []);
                foreach ($sessionCart as $item) {
                    if (isset($item['quantity'])) {
                        $count += $item['quantity'];
                    }
                }
            }
        @endphp
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-danger {{ $count > 0 ? 'show' : '' }}"
            style="width: 24px; height: 24px; align-items: center; justify-content: center; font-size: 0.75rem; display: {{ $count > 0 ? 'flex' : 'none' }};"
            data-cart-count>{{ $count > 0 ? $count : '' }}
        </span>
    </a>
</div>