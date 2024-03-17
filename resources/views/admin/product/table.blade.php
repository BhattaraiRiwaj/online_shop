<table class="table table-hover text-nowrap">
    <thead>
        <tr>
            <th width="60">ID</th>
            <th width="80">Product Image</th>
            <th>Product</th>
            <th>Price</th>
            <th>Qty</th>
            <th>SKU</th>
            <th width="100">Status</th>
            <th width="100">Action</th>
        </tr>
    </thead>
    <tbody>
        @if ($products->isNotEmpty())
            @foreach ($products as $product)
            @php
                $productImage = $product->product_images->first();
            @endphp
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>
                        @if (!empty($productImage->image))
                            <img src="{{ asset('uploads/products/smallImage/'.$productImage->image) }}" class="img-thumbnail" width="50">
                        @endif
                    </td>
                    <td><a href="#">{{ $product->title }}</a></td>
                    <td>{{ $product->price }}</td>
                    <td>{{ $product->qty }}</td>
                    <td>{{ $product->sku }}</td>
                    <td>
                        <a href="{{ route('products.status', $product->id) }}"
                            class="btn btn-sm btn-{{ $product->status ? 'success' : 'danger' }}">
                            {{ $product->status ? 'Active' : 'Block' }}
                        </a>
                    </td>
                    <td>
                        <a href="#">
                            <svg class="filament-link-icon w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                                </path>
                            </svg>
                        </a>
                        <a href="#" class="text-danger w-4 h-4 mr-1">
                            <svg wire:loading.remove.delay="" wire:target="" class="filament-link-icon w-4 h-4 mr-1"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                aria-hidden="true">
                                <path ath fill-rule="evenodd"
                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </a>
                    </td>
                </tr>
            @endforeach
        @else
        <tr>
            <td colspan="8">Product Not Found</td>
        </tr>

        @endif
    </tbody>
</table>
