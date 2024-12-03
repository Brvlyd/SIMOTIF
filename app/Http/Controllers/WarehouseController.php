<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    public function inputBarang()
    {
        $transactions = InventoryTransaction::with(['product', 'user'])
            ->where('type', 'in')
            ->latest()
            ->paginate(10);
    
        $totalTransactions = InventoryTransaction::where('type', 'in')->count();
        $totalProducts = InventoryTransaction::where('type', 'in')->sum('quantity');
        $uniqueProducts = InventoryTransaction::where('type', 'in')
            ->distinct('product_id')
            ->count();
    
        return view('warehouse.input-barang', compact(
            'transactions',
            'totalTransactions',
            'totalProducts',
            'uniqueProducts'
        ));
    }

    public function simpanBarang(Request $request)
    {
        // Validate products array
        $request->validate([
            'products' => 'required|array',
            'products.*.name' => 'required|string|max:255',
            'products.*.brand' => 'required|string|max:255',
            'products.*.vehicle_type' => 'required|string|max:255',
            'products.*.stock' => 'required|integer|min:1',
            'products.*.price' => 'required|string'
        ], [
            'products.*.name.required' => 'Nama suku cadang wajib diisi',
            'products.*.brand.required' => 'Merek suku cadang wajib diisi',
            'products.*.vehicle_type.required' => 'Tipe kendaraan wajib diisi',
            'products.*.stock.required' => 'Jumlah stok wajib diisi',
            'products.*.stock.min' => 'Jumlah stok minimal 1',
            'products.*.price.required' => 'Harga wajib diisi'
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->products as $productData) {
                // Set default minimum stock dan status
                $productData['minimum_stock'] = 10;
                $productData['status'] = 'available';
                
                // Konversi harga: hilangkan titik/koma dan konversi ke integer
                $price = (int) str_replace(['.', ','], '', $productData['price']);
                $productData['price'] = $price;
            
                // Simpan produk
                $product = Product::create($productData);
            
                // Catat transaksi inventory
                InventoryTransaction::create([
                    'product_id' => $product->id,
                    'type' => 'in',
                    'quantity' => $productData['stock'],
                    'date' => now(),
                    'notes' => 'Stok awal produk - Harga: Rp ' . number_format($price, 0, ',', '.'),
                    'user_id' => auth()->id()
                ]);
            }

            DB::commit();
            return redirect()->route('warehouse.input-barang')
                ->with('success', 'Semua barang berhasil ditambahkan ke gudang');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function editBarang($id)
    {
        $product = Product::findOrFail($id);
        return view('warehouse.edit-barang', compact('product'));
    }

    public function updateBarang(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'vehicle_type' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'minimum_stock' => 'nullable|integer|min:0',
            'price' => 'required|numeric|min:0'
        ]);

        $oldStock = $product->stock;
        $newStock = $validated['stock'];
        $stockDiff = $newStock - $oldStock;

        DB::beginTransaction();
        try {
            // Update product
            $product->update($validated);

            // Record stock change if different
            if ($stockDiff != 0) {
                InventoryTransaction::create([
                    'product_id' => $product->id,
                    'type' => $stockDiff > 0 ? 'in' : 'out',
                    'quantity' => abs($stockDiff),
                    'date' => now(),
                    'notes' => 'Penyesuaian stok manual',
                    'user_id' => auth()->id()
                ]);
            }

            DB::commit();
            return redirect()->route('stock')
                ->with('success', 'Data barang berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui data')
                ->withInput();
        }
    }

    public function hapusBarang($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();

            return redirect()->route('stock')
                ->with('success', 'Barang berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus data');
        }
    }

    public function riwayatBarang($id)
    {
        $product = Product::with(['inventoryTransactions' => function($query) {
            $query->latest('date');
        }])->findOrFail($id);
        
        return view('warehouse.riwayat-barang', compact('product'));
    }

    public function adjustStock(Request $request, $id)
    {
        $request->validate([
            'adjustment' => 'required|integer',
            'notes' => 'required|string|max:255'
        ]);

        DB::beginTransaction();
        try {
            $product = Product::findOrFail($id);
            
            // Check if adjustment would make stock negative
            if ($product->stock + $request->adjustment < 0) {
                throw new \Exception('Penyesuaian akan membuat stok menjadi negatif');
            }
            
            // Update stock
            $product->stock += $request->adjustment;
            $product->save();

            // Create transaction record
            InventoryTransaction::create([
                'product_id' => $id,
                'type' => $request->adjustment > 0 ? 'in' : 'out',
                'quantity' => abs($request->adjustment),
                'date' => now(),
                'notes' => $request->notes,
                'user_id' => auth()->id()
            ]);

            DB::commit();
            return redirect()->route('stock')
                ->with('success', 'Stok berhasil disesuaikan');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}