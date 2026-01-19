<x-mail::message>
    # Low Stock Alert

    The product **{{ $product->name }}** (ID: {{ $product->id }}) is low on stock.

    - Current stock: **{{ $product->stock_quantity }}**
    - Low threshold: **{{ $product->low_stock_threshold }}**

    Please review inventory.

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>