<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Order History') }}</h3>
                
                @if(auth()->user()->orders->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100 border-b">
                                <tr>
                                    <th class="px-4 py-2 text-left">Order ID</th>
                                    <th class="px-4 py-2 text-left">Date</th>
                                    <th class="px-4 py-2 text-left">Total</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                    <th class="px-4 py-2 text-left">Items</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(auth()->user()->orders->sortByDesc('created_at') as $order)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-2 font-medium">#{{ $order->id }}</td>
                                    <td class="px-4 py-2">{{ $order->created_at->format('M d, Y') }}</td>
                                    <td class="px-4 py-2 font-semibold">${{ number_format($order->total_amount, 2) }}</td>
                                    <td class="px-4 py-2">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium
                                            @if($order->status === 'completed')
                                                bg-green-100 text-green-800
                                            @elseif($order->status === 'processing')
                                                bg-blue-100 text-blue-800
                                            @elseif($order->status === 'cancelled')
                                                bg-red-100 text-red-800
                                            @else
                                                bg-yellow-100 text-yellow-800
                                            @endif
                                        ">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <button onclick="toggleOrderDetails({{ $order->id }})" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            View ({{ $order->items->count() }})
                                        </button>
                                    </td>
                                </tr>
                                <!-- Order Details Row -->
                                <tr id="order-{{ $order->id }}-details" class="hidden">
                                    <td colspan="5" class="px-4 py-4 bg-gray-50">
                                        <div class="space-y-2">
                                            <h4 class="font-medium text-gray-900">Items:</h4>
                                            @foreach($order->items as $item)
                                            <div class="flex justify-between text-sm text-gray-600 ml-4">
                                                <span>{{ $item->product->name }} x {{ $item->quantity }}</span>
                                                <span>${{ number_format($item->price * $item->quantity, 2) }}</span>
                                            </div>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-600">{{ __('No orders yet.') }}</p>
                    <a href="/shop" class="mt-4 inline-block text-blue-600 hover:text-blue-800 font-medium">
                        {{ __('Start Shopping') }}
                    </a>
                @endif

            </div>

        </div>
    </div>
</x-app-layout>

<script>
function toggleOrderDetails(orderId) {
    const detailsRow = document.getElementById(`order-${orderId}-details`);
    detailsRow.classList.toggle('hidden');
}
</script>