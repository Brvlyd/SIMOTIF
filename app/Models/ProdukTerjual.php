<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukTerjual extends Model
{
    use HasFactory;

    protected $table = 'produk_terjual';

    protected $fillable = [
        'stock_id',
        'jumlah',
        'tanggal_jual',
        'user_id',
        'harga_jual'
    ];

    protected $dates = [
        'tanggal_jual'
    ];

    // Relasi ke Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}