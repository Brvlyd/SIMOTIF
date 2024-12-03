<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InventoryController extends Controller
{
    public function productIn()
    {
        $products = Product::all();
        return view('inventory.product-in', compact('products'));
    }

    public function storeProductIn(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
            'date' => 'required|date'
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Create transaction
        InventoryTransaction::create([
            'product_id' => $validated['product_id'],
            'user_id' => auth()->id(),
            'type' => 'in',
            'quantity' => $validated['quantity'],
            'date' => $validated['date']
        ]);

        // Update stock
        $product->increment('stock', $validated['quantity']);

        return redirect()->route('inventory.product-in')
            ->with('success', 'Product stock added successfully');
    }

    public function productInHistory()
    {
        $transactions = InventoryTransaction::with(['product', 'user'])
            ->where('type', 'in')
            ->latest('date')
            ->paginate(10);

        return view('inventory.product-in-history', compact('transactions'));
    }

    public function productOut()
    {
        $products = Product::all();
        return view('inventory.product-out', compact('products'));
    }

    public function storeProductOut(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
            'date' => 'required|date'
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Check if enough stock
        if ($product->stock < $validated['quantity']) {
            return back()->with('error', 'Insufficient stock');
        }

        // Create transaction
        InventoryTransaction::create([
            'product_id' => $validated['product_id'],
            'user_id' => auth()->id(),
            'type' => 'out',
            'quantity' => $validated['quantity'],
            'date' => $validated['date']
        ]);

        // Update stock
        $product->decrement('stock', $validated['quantity']);

        return redirect()->route('inventory.product-out')
            ->with('success', 'Product stock reduced successfully');
    }

    public function productOutHistory()
    {
        $transactions = InventoryTransaction::with(['product', 'user'])
            ->where('type', 'out')
            ->latest('date')
            ->paginate(10);

        return view('inventory.product-out-history', compact('transactions'));
    }
}