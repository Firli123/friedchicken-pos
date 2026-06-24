<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->orderBy('category_id')->orderBy('sort_order');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }

        $products   = $query->paginate(20)->withQueryString();
        $categories = Category::orderBy('sort_order')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('sort_order')->get();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'category_id' => ['required', 'exists:categories,id'],
            'price'       => ['required', 'integer', 'min:0'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active'   => ['boolean'],
            'sort_order'  => ['integer', 'min:0'],
        ]);

        // Auto-generate kode produk
        $category = Category::findOrFail($data['category_id']);
        $data['code'] = Product::generateCode($category->slug);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', true);

        $product = Product::create($data);

        ActivityLog::log('create', "Produk '{$product->name}' ditambahkan", 'Product', $product->id, null, $product->toArray());

        return redirect()->route('products.index')
            ->with('success', "Produk '{$product->name}' berhasil ditambahkan.");
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('sort_order')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'category_id' => ['required', 'exists:categories,id'],
            'price'       => ['required', 'integer', 'min:0'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active'   => ['boolean'],
            'sort_order'  => ['integer', 'min:0'],
        ]);

        $oldData = $product->toArray();

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', true);

        $product->update($data);

        ActivityLog::log('update', "Produk '{$product->name}' diperbarui", 'Product', $product->id, $oldData, $product->fresh()->toArray());

        return redirect()->route('products.index')
            ->with('success', "Produk '{$product->name}' berhasil diperbarui.");
    }

    public function destroy(Product $product)
    {
        $name = $product->name;

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        ActivityLog::log('delete', "Produk '{$name}' dihapus", 'Product', $product->id);

        return redirect()->route('products.index')
            ->with('success', "Produk '{$name}' berhasil dihapus.");
    }

    /**
     * Toggle active status via AJAX.
     */
    public function toggleActive(Product $product)
    {
        $product->update(['is_active' => ! $product->is_active]);
        return response()->json(['is_active' => $product->is_active]);
    }
}
