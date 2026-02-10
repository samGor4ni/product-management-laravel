@extends('layouts.app')

@section('header')
Products
@endsection

@section('content')
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 text-gray-900 dark:text-gray-100">
        <div class="flex justify-between mb-4">
            <h3 class="text-lg font-semibold">Product List</h3>
            <div class="space-x-2">
                <a href="{{ route('products.create') }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Add Product</a>
                <a href="{{ route('products.export', request()->query()) }}"
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Export Excel</a>
            </div>
        </div>

        <form method="GET" action="{{ route('products.index') }}" class="mb-4 grid grid-cols-1 md:grid-cols-5 gap-4">
            <select name="category_id" class="rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id')==$category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
                @endforeach
            </select>
            <select name="enabled" class="rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                <option value="">All Status</option>
                <option value="1" {{ request('enabled')==='1' ? 'selected' : '' }}>Enabled</option>
                <option value="0" {{ request('enabled')==='0' ? 'selected' : '' }}>Disabled</option>
            </select>
            <select name="per_page" class="rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                <option value="10" {{ request('per_page')==10 ? 'selected' : '' }}>10 per page</option>
                <option value="25" {{ request('per_page')==25 ? 'selected' : '' }}>25 per page</option>
                <option value="50" {{ request('per_page')==50 ? 'selected' : '' }}>50 per page</option>
                <option value="100" {{ request('per_page')==100 ? 'selected' : '' }}>100 per page</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">Filter</button>
        </form>

        <form method="POST" action="{{ route('products.bulkDelete') }}" id="bulk-delete-form">
            @csrf
            <div class="mb-2">
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                    onclick="return confirm('Are you sure you want to delete selected products?')">Bulk Delete</button>
            </div>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="p-2 border-b"><input type="checkbox" id="select-all"></th>
                        <th class="p-2 border-b">Name</th>
                        <th class="p-2 border-b">Category</th>
                        <th class="p-2 border-b">Price</th>
                        <th class="p-2 border-b">Stock</th>
                        <th class="p-2 border-b">Status</th>
                        <th class="p-2 border-b">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="p-2 border-b"><input type="checkbox" name="ids[]" value="{{ $product->id }}"></td>
                        <td class="p-2 border-b">{{ $product->name }}</td>
                        <td class="p-2 border-b">{{ $product->category->name }}</td>
                        <td class="p-2 border-b">${{ number_format($product->price, 2) }}</td>
                        <td class="p-2 border-b">{{ $product->stock }}</td>
                        <td class="p-2 border-b">
                            <span
                                class="px-2 py-1 text-xs rounded {{ $product->enabled ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $product->enabled ? 'Enabled' : 'Disabled' }}
                            </span>
                        </td>
                        <td class="p-2 border-b space-x-2">
                            <a href="{{ route('products.edit', $product) }}"
                                class="text-blue-600 hover:underline">Edit</a>

                            <button type="button" class="text-red-600 hover:underline"
                                onclick="deleteProduct({{ $product->id }})">Delete</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </form>

        <form id="delete-single-form" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>

        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </div>
</div>

<script>
    document.getElementById('select-all').onclick = func tion          var checkboxes = document.querySelectorAll('input[name="ids[]"]');         (var checkbox of checkboxes) {
               x.checked = this.checked;
                      de {
        if (confirm('Delete this product?')) {
            const form = document.getElementById('delete-single-form');
            form.action = '/products/' + id;
            form.submit();
        }
    }
</script>
@endsection