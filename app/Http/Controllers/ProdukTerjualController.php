<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\ProdukTerjual;
use Illuminate\Http\Request;

class ProdukTerjualController extends Controller
{
    public function index()
    {
        $produkTerjual = ProdukTerjual::with('stock')->latest()->get();
        return view('produk-terjual.index', compact('produkTerjual'));
    }

    public function create()
    {
        $stocks = Stock::where('stock', '>', 0)->get();
        return view('produk-terjual.create', compact('stocks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'stock_id' => 'required|exists:stocks,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal_jual' => 'required|date',
        ], [
            'required' => 'Kolom :attribute harus diisi',
            'integer' => 'Kolom :attribute harus berupa angka',
            'min' => 'Kolom :attribute minimal 1',
            'date' => 'Kolom :attribute harus berupa tanggal'
        ]);

        $stock = Stock::findOrFail($request->stock_id);

        if ($stock->stock < $request->jumlah) {
            return back()->with('error', 'Stok tidak mencukupi!');
        }

        // Buat record produk terjual
        ProdukTerjual::create([
            'stock_id' => $request->stock_id,
            'jumlah' => $request->jumlah,
            'tanggal_jual' => $request->tanggal_jual,
            'user_id' => auth()->id(),
        ]);

        // Update jumlah stok
        $stock->decrement('stock', $request->jumlah);

        return redirect()->route('produk-terjual.index')->with('success', 'Data penjualan berhasil disimpan');
    }
}