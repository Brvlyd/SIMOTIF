<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand',
        'vehicle_type',
        'stock',
        'status'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $attributes = [
        'minimum_stock' => 10,
        'stock' => 0
    ];

    protected $casts = [
        'stock' => 'integer',
        'minimum_stock' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function isLowStock()
    {
        return $this->stock <= $this->minimum_stock;
    }

    public function updateStock($quantity)
    {
        $this->stock = $quantity;
        $this->updated_at = now();
        $this->save();
    }

    public function incrementStock($amount = 1)
    {
        $this->stock += $amount;
        $this->updated_at = now();
        $this->save();
    }

    public function decrementStock($amount = 1)
    {
        if ($this->stock >= $amount) {
            $this->stock -= $amount;
            $this->updated_at = now();
            $this->save();
            return true;
        }
        return false;
    }

    public function getStockStatusAttribute()
    {
        return $this->isLowStock() ? 'Stok Menipis' : 'Tersedia';
    }

    public function getLastUpdateAttribute()
    {
        return $this->updated_at ? $this->updated_at->format('d/m/Y H:i:s') : '-';
    }

     // Relasi ke ProdukTerjual
     public function penjualan()
     {
         return $this->hasMany(ProdukTerjual::class);
     }
    
}