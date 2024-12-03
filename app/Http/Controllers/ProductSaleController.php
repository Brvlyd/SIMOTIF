<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductSaleController extends Controller
{
    public function create()
    {
        $products = Product::where('stock', '>', 0)->get(); // Hanya ambil produk yang masih ada stok
        return view('products.create-sale', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.jumlah' => 'required|integer|min:1',
            'products.*.harga' => 'required|numeric|min:0',
            'tanggal_jual' => 'required|date',
        ]);
    
        try {
            DB::beginTransaction();
    
            $sale = Sale::create([
                'tanggal_jual' => $request->tanggal_jual,
                'user_id' => auth()->id(),
            ]);
    
            foreach ($request->products as $productData) {
                $product = Product::find($productData['product_id']);
                
                if ($product->stock < $productData['jumlah']) {
                    throw new \Exception("Stok tidak mencukupi untuk produk {$product->name}");
                }
    
                // Simpan total sesuai dengan harga yang diinput
                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $productData['product_id'],
                    'jumlah' => $productData['jumlah'],
                    'harga' => $productData['harga'],
                    'total' => $productData['harga'] // Total sama dengan harga yang diinput
                ]);
    
                $product->update([
                    'stock' => $product->stock - $productData['jumlah']
                ]);
            }
    
            DB::commit();
            return redirect()->route('products.sold')->with('success', 'Penjualan berhasil disimpan');
    
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    public function index()
    {
        $produkTerjual = SaleDetail::with(['product', 'sale'])
            ->when(request('search'), function($query) {
                $query->whereHas('product', function($q) {
                    $q->where('name', 'like', '%'.request('search').'%')
                    ->orWhere('brand', 'like', '%'.request('search').'%');
                });
            })
            ->when(request('date'), function($query) {
                $query->whereHas('sale', function($q) {
                    $q->whereDate('tanggal_jual', request('date'));
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Hitung total pendapatan dari kolom total
        $totalPendapatan = $produkTerjual->sum('total');

        return view('products.sold', compact('produkTerjual', 'totalPendapatan'));
    }
}