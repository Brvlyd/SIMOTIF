<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProdukTerjual;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')->paginate(10);
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
            'minimum_stock' => 'nullable|integer|min:0',
            'price' => 'required|numeric|min:0'
        ]);

        $data = $request->all();
        if (!isset($data['minimum_stock'])) {
            $data['minimum_stock'] = 10;
        }
        
        $data['status'] = 'available';
        $data['price'] = (int) str_replace(['.', ','], '', $request->price);

        Product::create($data);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan');
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
            'minimum_stock' => 'nullable|integer|min:0',
            'price' => 'required|numeric|min:0'
        ]);

        $oldStock = $product->stock;
        $newStock = $request->stock;

        $data = $request->all();
        $data['price'] = (int) str_replace(['.', ','], '', $request->price);
        
        $product->update($data);

        // Update status based on new stock
        $product->status = $product->stock == 0 ? 'sold' : 'available';
        $product->save();

        // Record stock change if different
        if ($oldStock != $newStock) {
            $product->inventoryTransactions()->create([
                'type' => $newStock > $oldStock ? 'in' : 'out',
                'quantity' => abs($newStock - $oldStock),
                'date' => now(),
                'user_id' => auth()->id()
            ]);
        }

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus');
    }

    public function search(Request $request)
    {
        $query = Product::query();

        // Search by name, brand, or vehicle type
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('brand', 'like', "%{$request->search}%")
                  ->orWhere('vehicle_type', 'like', "%{$request->search}%");
            });
        }

        // Filter by brand
        if ($request->filled('brand')) {
            $query->where('brand', 'like', "%{$request->brand}%");
        }

        // Filter by vehicle type
        if ($request->filled('vehicle_type')) {
            $query->where('vehicle_type', 'like', "%{$request->vehicle_type}%");
        }

        // Filter by stock status
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->whereRaw('stock <= minimum_stock');
            } elseif ($request->stock_status === 'available') {
                $query->whereRaw('stock > minimum_stock');
            }
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(10);
        $products->appends($request->all());

        if ($request->ajax()) {
            return response()->json([
                'table' => view('products.partials.product-table', compact('products'))->render(),
                'pagination' => $products->links()->toHtml()
            ]);
        }

        return view('products.index', compact('products'));
    }

    public function sold(Request $request)
    {
        $query = SaleDetail::with(['product', 'sale'])
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->select('sale_details.*')
            ->orderBy('sales.tanggal_jual', 'desc');

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('sales.tanggal_jual', $request->date);
        }

        $produkTerjual = $query->paginate(10);
        $totalPendapatan = $query->sum(DB::raw('sale_details.jumlah * sale_details.harga'));

        return view('products.sold', compact('produkTerjual', 'totalPendapatan'));
    }

    public function createSale()
    {
        $products = Product::where('stock', '>', 0)
            ->where('status', '!=', 'sold')
            ->orderBy('name')
            ->get();

        if ($products->isEmpty()) {
            return redirect()->route('products.sold')
                ->with('error', 'Tidak ada produk yang tersedia untuk dijual');
        }

        return view('products.create-sale', compact('products'));
    }

    public function storeSale(Request $request)
    {
        $request->validate([
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.jumlah' => 'required|integer|min:1',
            'products.*.harga' => 'required|numeric|min:0',
            'tanggal_jual' => 'required|date'
        ]);

        DB::beginTransaction();
        try {
            $sale = Sale::create([
                'tanggal_jual' => $request->tanggal_jual,
                'user_id' => auth()->id()
            ]);

            foreach ($request->products as $productData) {
                $product = Product::findOrFail($productData['product_id']);
                
                if ($product->stock < $productData['jumlah']) {
                    throw new \Exception("Stok tidak mencukupi untuk produk {$product->name}");
                }

                // Create sale detail
                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'jumlah' => $productData['jumlah'],
                    'harga' => $productData['harga']
                ]);

                // Update product stock
                $product->stock -= $productData['jumlah'];
                $product->status = $product->stock == 0 ? 'sold' : 'available';
                $product->save();

                // Record inventory transaction
                $product->inventoryTransactions()->create([
                    'type' => 'out',
                    'quantity' => $productData['jumlah'],
                    'date' => $request->tanggal_jual,
                    'user_id' => auth()->id()
                ]);
            }

            DB::commit();
            return redirect()->route('products.sold')
                ->with('success', 'Penjualan berhasil dicatat');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function exportPDF(Request $request)
    {
        $query = SaleDetail::with(['product', 'sale'])
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->select('sale_details.*')
            ->orderBy('sales.tanggal_jual', 'desc');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('sales.tanggal_jual', $request->date);
        }

        $produkTerjual = $query->get();
        
        // Calculate totals
        $totalPendapatan = $produkTerjual->sum(function($sale) {
            return $sale->jumlah * $sale->harga;
        });

        $pdf = PDF::loadView('products.sales_receipt', [
            'produkTerjual' => $produkTerjual,
            'totalPendapatan' => $totalPendapatan,
            'tanggal' => Carbon::now()->format('d/m/Y'),
            'waktu' => Carbon::now()->format('H:i:s')
        ]);

        return $pdf->download('resi-penjualan-' . Carbon::now()->format('d-m-Y') . '.pdf');
    }

    public function checkLowStock()
    {
        $lowStockCount = Product::whereRaw('stock <= minimum_stock')->count();
        return response()->json(['count' => $lowStockCount]);
    }
}