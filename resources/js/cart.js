import { Notyf } from 'notyf';
import 'notyf/notyf.min.css';

const notyf = typeof window !== 'undefined' ? new Notyf({ duration: 2600, position: { x: 'right', y: 'top' } }) : null;

function csrfToken() {
    const el = document.querySelector('meta[name="csrf-token"]');
    return el ? el.getAttribute('content') : '';
}

async function jsonFetch(url, opts = {}) {
    const headers = Object.assign({
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken(),
    }, opts.headers || {});

    const res = await fetch(url, Object.assign({ credentials: 'same-origin', headers }, opts));
    const contentType = res.headers.get('content-type') || '';
    const isJson = contentType.includes('application/json');
    const body = isJson ? await res.json() : await res.text();
    if (!res.ok) throw { status: res.status, body };
    return body;
}

async function refreshCartCount(count) {
    try {
        const data = await jsonFetch('/cart/count');
        console.log('Cart count API response:', data);

        const badge = document.querySelector('[data-cart-count]');
        if (!badge) {
            console.warn('Cart badge element not found');
            return;
        }

        const numCount = parseInt(count, 10) || 0;
        console.log('Updating badge to:', numCount);

    } catch (e) {
        console.error('Error refreshing cart count:', e);
    }
}

async function refreshCartItems() {
    console.log('refreshCartItems called');
    try {
        console.log('Fetching /cart/items...');
        const data = await jsonFetch('/cart/items');
        console.log('Cart items data received:', data);

        const cartContainer = document.querySelector('[data-cart-items]');
        console.log('Cart container element:', cartContainer);

        if (!cartContainer) {
            console.log('No cart container found - not on cart page');
            return;
        }

        if (!data.items || data.items.length === 0) {
            console.log('No items in cart');
            // Show empty cart message
            cartContainer.innerHTML = `
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
                    <h3 class="fs-5 fw-bold text-dark mb-2">Your cart is empty</h3>
                    <p class="text-muted mb-4">Add some items to get started</p>
                    <a href="/shop" class="btn btn-warning">
                        <svg class="icon me-2" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Continue Shopping
                    </a>
                </div>
            `;
            const summaryEl = document.querySelector('[data-cart-summary]');
            if (summaryEl) summaryEl.style.display = 'none';
            return;
        }

        console.log(`Rendering ${data.items.length} cart items`);
        // Show cart items
        const summaryEl = document.querySelector('[data-cart-summary]');
        if (summaryEl) summaryEl.style.display = 'block';

        const itemsHTML = data.items.map(item => {
            const price = parseFloat(item.product_price);
            const total = parseFloat(item.total);
            const stock = parseInt(item.product_stock);
            const qty = parseInt(item.quantity);
            // Use product_id for guests (id is null), use id for authenticated users
            const itemId = item.id !== null ? item.id : item.product_id;

            return `
            <div class="card border-1 shadow-sm mb-3" style="transition: all 0.2s ease;">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                style="width: 64px; height: 64px;">
                                <svg class="text-warning" width="32" height="32" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="col">
                            <h5 class="card-title fw-bold mb-2">${item.product_name}</h5>
                            <div class="d-flex gap-3">
                                <span class="fw-semibold text-warning">$${price.toFixed(2)}</span>
                                <span class="text-muted">•</span>
                                <small class="text-muted">Stock: ${stock}</small>
                            </div>
                        </div>
                        <div class="col-auto d-flex align-items-center gap-3">
                            <div>
                                <input type="number" min="1" max="${qty + stock}" value="${qty}"
                                    data-action="update-quantity" data-id="${itemId}"
                                    class="form-control form-control-sm text-center" style="width: 80px;" />
                            </div>
                            <div style="min-width: 80px;">
                                <div class="fw-bold">$${total.toFixed(2)}</div>
                            </div>
                            <button data-action="remove-item" data-id="${itemId}"
                                class="btn btn-link text-danger p-2" title="Remove item">
                                <svg class="icon" width="20" height="20" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `}).join('');

        cartContainer.innerHTML = itemsHTML;
        console.log('Cart items rendered');

        // Update total
        const totalEl = document.querySelector('[data-cart-total]');
        if (totalEl) {
            const subtotal = parseFloat(data.subtotal);
            totalEl.textContent = `$${subtotal.toFixed(2)}`;
            console.log('Cart total updated to:', subtotal);
        }
    } catch (e) {
        console.error('Error refreshing cart items:', e);
        const cartContainer = document.querySelector('[data-cart-items]');
        if (cartContainer) {
            cartContainer.innerHTML = `<div class="alert alert-danger">Error loading cart: ${e.body?.message || e.message || 'Unknown error'}</div>`;
        }
    }
}

async function handleAddToCart(button) {
    const id = button.dataset.id;
    try {
        const res = await jsonFetch('/cart/add', { method: 'POST', body: JSON.stringify({ product_id: id }) });

        // Immediately refresh the cart count
        await refreshCartCount();

        // Dispatch events
        window.dispatchEvent(new CustomEvent('cartUpdated'));
        window.dispatchEvent(new CustomEvent('addedToCart', { detail: res.product }));

        if (notyf) notyf.success(`${res.product} added to cart!`);
    } catch (err) {
        if (err.status === 401) {
            window.dispatchEvent(new CustomEvent('authRequired'));
            return;
        }
        if (err.status === 409 && err.body && err.body.name) {
            if (notyf) notyf.error(`${err.body.name} is out of stock`);
            window.dispatchEvent(new CustomEvent('outOfStock', { detail: err.body.name }));
            return;
        }
        if (notyf) notyf.error('Unable to add item');
    }
}

async function handleUpdateQuantity(input) {
    const id = input.dataset.id;
    const quantity = input.value;
    try {
        await jsonFetch(`/cart/${id}/quantity`, { method: 'POST', body: JSON.stringify({ quantity }) });

        // Immediately refresh the cart count
        await refreshCartCount();

        window.dispatchEvent(new CustomEvent('cartUpdated'));
        if (notyf) notyf.success('Quantity updated!');
    } catch (err) {
        if (err.status === 401) {
            window.dispatchEvent(new CustomEvent('authRequired'));
            return;
        }
        if (notyf) notyf.error('Unable to update quantity');
    }
}

async function handleRemoveItem(button) {
    const id = button.dataset.id;
    try {
        await jsonFetch(`/cart/${id}`, { method: 'DELETE' });

        // Immediately refresh the cart count
        await refreshCartCount();

        window.dispatchEvent(new CustomEvent('cartUpdated'));
        if (notyf) notyf.success('Item removed from cart!');
    } catch (err) {
        if (err.status === 401) {
            window.dispatchEvent(new CustomEvent('authRequired'));
            return;
        }
        if (notyf) notyf.error('Unable to remove item');
    }
}

async function handleClearCart() {
    try {
        await jsonFetch('/cart/clear', { method: 'POST' });

        // Immediately refresh the cart count
        await refreshCartCount();

        window.dispatchEvent(new CustomEvent('cartUpdated'));
        if (notyf) notyf.success('Cart cleared!');
    } catch (err) {
        if (err.status === 401) {
            window.dispatchEvent(new CustomEvent('authRequired'));
            return;
        }
        if (notyf) notyf.error('Unable to clear cart');
    }
}

async function handlePlaceOrder(button) {
    try {
        await jsonFetch('/order/place', { method: 'POST' });

        // Immediately refresh the cart count
        await refreshCartCount();

        window.dispatchEvent(new CustomEvent('cartUpdated'));
        window.dispatchEvent(new CustomEvent('orderPlaced'));
        if (notyf) notyf.success('Order placed successfully!');
    } catch (err) {
        if (err.status === 401) {
            // Redirect to login page for guests
            window.location.href = '/login';
            return;
        }
        if (notyf) notyf.error('Unable to place order');
    }
}

export function attachCartBindings() {
    console.log('Attaching cart bindings...');

    // delegation for clicks
    document.addEventListener('click', (e) => {
        const add = e.target.closest('[data-action="add-to-cart"]');
        if (add) {
            e.preventDefault();
            handleAddToCart(add);
            return;
        }

        const remove = e.target.closest('[data-action="remove-item"]');
        if (remove) {
            e.preventDefault();
            handleRemoveItem(remove);
            return;
        }

        const clear = e.target.closest('[data-action="clear-cart"]');
        if (clear) {
            e.preventDefault();
            handleClearCart();
            return;
        }

        const place = e.target.closest('[data-action="place-order"]');
        if (place) {
            e.preventDefault();
            handlePlaceOrder(place);
            return;
        }

    });

    // delegation for change events on inputs
    document.addEventListener('change', (e) => {
        const input = e.target.closest('[data-action="update-quantity"]');
        if (input) {
            handleUpdateQuantity(input);
        }
    });

    // Initial load
    console.log('Initial cart count refresh...');
    refreshCartCount();
    refreshCartItems();

    // allow external triggers to refresh
    window.addEventListener('cartUpdated', () => {
        console.log('cartUpdated event received');
        refreshCartCount();
        refreshCartItems();
    });
}

// initialize on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        console.log('Cart.js: Initializing on DOMContentLoaded');
        attachCartBindings();
    });
} else {
    console.log('Cart.js: Initializing immediately (DOM already loaded)');
    attachCartBindings();
}

// expose helpers for tests
export default { refreshCartCount, refreshCartItems, attachCartBindings };