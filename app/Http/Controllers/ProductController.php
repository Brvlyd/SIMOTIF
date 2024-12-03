<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProdukTerjual; 
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')->get();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255', 
            'vehicle_type' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
        ]);

        Product::create([
            'name' => $request->name,
            'brand' => $request->brand,
            'vehicle_type' => $request->vehicle_type,
            'stock' => $request->stock,
            'status' => 'available',
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'vehicle_type' => 'required|string|max:255', 
            'stock' => 'required|integer|min:0',
        ]);

        $product->update([
            'name' => $request->name,
            'brand' => $request->brand,
            'vehicle_type' => $request->vehicle_type,
            'stock' => $request->stock,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus');
    }

    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $product->update([
            'stock' => $request->stock,
            'status' => $request->stock == 0 ? 'sold' : 'available',
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Stok berhasil diperbarui');
    }

    public function lowStock()
    {
        $products = Product::where('stock', '<=', 10)->get();
        return view('products.low_stock', compact('products'));
    }

    public function sold()
    {
        $produkTerjual = ProdukTerjual::with(['product', 'user'])
            ->orderBy('tanggal_jual', 'desc')
            ->get();
        return view('products.sold', compact('produkTerjual'));
    }

    public function createSale()
    {
    $products = Product::where('stock', '>', 0)
                    ->whereNull('deleted_at')  // Jika menggunakan soft deletes
                    ->where(function($query) {
                        $query->where('status', '!=', 'sold')
                              ->orWhereNull('status');
                    })
                    ->get();
                    
    if($products->isEmpty()) {
        return redirect()->route('products.sold')
            ->with('error', 'Tidak ada produk yang tersedia untuk dijual');
    }
    
    return view('products.create_sale', compact('products'));
}

    public function storeSale(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal_jual' => 'required|date'
        ]);

        try {
            DB::beginTransaction();

            $product = Product::findOrFail($request->product_id);

            if ($product->stock < $request->jumlah) {
                return back()->with('error', 'Stok tidak mencukupi!');
            }

            // Kurangi stok
            $product->stock -= $request->jumlah;
            $product->status = $product->stock == 0 ? 'sold' : 'available';
            $product->save();

            // Catat penjualan
            ProdukTerjual::create([
                'product_id' => $request->product_id,
                'user_id' => auth()->id(),
                'jumlah' => $request->jumlah,
                'tanggal_jual' => Carbon::parse($request->tanggal_jual)
            ]);

            DB::commit();
            return redirect()->route('products.sold')
                ->with('success', 'Produk berhasil dijual!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan penjualan: ' . $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        $search = $request->get('search');
        
        $products = Product::where('name', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%")
                        ->orWhere('vehicle_type', 'like', "%{$search}%")
                        ->get();

        return view('products.index', compact('products'));
    }

    public function searchSoldProducts(Request $request) 
    {
        $search = $request->get('search');
        
        $produkTerjual = ProdukTerjual::with(['product', 'user'])
            ->whereHas('product', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('brand', 'like', "%{$search}%")
                      ->orWhere('vehicle_type', 'like', "%{$search}%");
            })
            ->get();

        return view('products.sold', compact('produkTerjual'));
    }
}