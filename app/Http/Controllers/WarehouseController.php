<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WarehouseController extends Controller
{
   public function inputBarang()
   {
       return view('warehouse.input-barang');
   }

   public function simpanBarang(Request $request)
   {
       $validated = $request->validate([
           'name' => 'required|string|max:255',
           'brand' => 'required|string|max:255',
           'vehicle_type' => 'required|string|max:255', 
           'stock' => 'required|integer|min:0',
           'minimum_stock' => 'nullable|integer|min:0'
       ], [
           'name.required' => 'Nama suku cadang wajib diisi',
           'brand.required' => 'Merek suku cadang wajib diisi',
           'vehicle_type.required' => 'Tipe kendaraan wajib diisi',
           'stock.required' => 'Jumlah stok wajib diisi',
           'stock.min' => 'Jumlah stok minimal 0',
           'minimum_stock.min' => 'Stok minimum tidak boleh kurang dari 0'
       ]);

       // Set default minimum stock jika tidak diisi
       if (!isset($validated['minimum_stock'])) {
           $validated['minimum_stock'] = 10;
       }

       // Set timestamps
       $validated['created_at'] = now();
       $validated['updated_at'] = now();

       // Simpan produk
       $product = Product::create($validated);

       // Catat transaksi inventory
       InventoryTransaction::create([
           'product_id' => $product->id,
           'type' => 'in',
           'quantity' => $validated['stock'],
           'date' => now(),
           'notes' => 'Stok awal produk',
           'user_id' => auth()->id()
       ]);

       return redirect()->route('stock')
           ->with('success', 'Barang berhasil ditambahkan ke gudang');
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
           'minimum_stock' => 'nullable|integer|min:0'
       ]);

       $oldStock = $product->stock;
       $newStock = $validated['stock'];
       $stockDiff = $newStock - $oldStock;

       $product->update($validated);

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

       return redirect()->route('stock')
           ->with('success', 'Data barang berhasil diperbarui');
   }

   public function hapusBarang($id)
   {
       $product = Product::findOrFail($id);
       $product->delete();

       return redirect()->route('stock')
           ->with('success', 'Barang berhasil dihapus');
   }

   public function riwayatBarang($id)
   {
       $product = Product::with('inventoryTransactions')->findOrFail($id);
       return view('warehouse.riwayat-barang', compact('product'));
   }

   public function adjustStock(Request $request, $id)
   {
       $request->validate([
           'adjustment' => 'required|integer',
           'notes' => 'required|string|max:255'
       ]);

       $product = Product::findOrFail($id);
       
       if ($request->adjustment > 0) {
           $product->incrementStock($request->adjustment);
           $type = 'in';
       } else {
           if (!$product->decrementStock(abs($request->adjustment))) {
               return redirect()->back()->with('error', 'Stok tidak mencukupi');
           }
           $type = 'out';
       }

       InventoryTransaction::create([
           'product_id' => $id,
           'type' => $type,
           'quantity' => abs($request->adjustment),
           'date' => now(),
           'notes' => $request->notes,
           'user_id' => auth()->id()
       ]);

       return redirect()->route('stock')
           ->with('success', 'Stok berhasil disesuaikan');
   }
}