<x-mail::message>
    # Daily Sales Report — {{ $date }}

    @if(empty($report))
    No sales for today.
    @else
    | Product | Quantity | Revenue |
    |---|---:|---:|
    @foreach($report as $row)
    | {{ $row['product']->name }} | {{ $row['quantity'] }} | ${{ number_format($row['revenue'],2) }} |
    @endforeach

    @endif

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>