<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Ban Tubeless',
                'brand' => 'IRC',
                'vehicle_type' => 'Honda BeAT 2020',
                'stock' => 15,
                'minimum_stock' => 5
            ],
            [
                'name' => 'Aki Motor',
                'brand' => 'GS Astra',
                'vehicle_type' => 'Honda Vario 150',
                'stock' => 10,
                'minimum_stock' => 3
            ],
            [
                'name' => 'Kampas Rem Depan',
                'brand' => 'Honda Genuine Parts',
                'vehicle_type' => 'Honda Scoopy 2021',
                'stock' => 20,
                'minimum_stock' => 8
            ],
            [
                'name' => 'Rantai Motor',
                'brand' => 'SSS',
                'vehicle_type' => 'Yamaha NMAX 155',
                'stock' => 12,
                'minimum_stock' => 4
            ],
            [
                'name' => 'Filter Udara',
                'brand' => 'Yamaha Genuine Parts',
                'vehicle_type' => 'Yamaha Aerox 155',
                'stock' => 18,
                'minimum_stock' => 6
            ],
            [
                'name' => 'Oli Mesin',
                'brand' => 'Yamalube',
                'vehicle_type' => 'All Yamaha',
                'stock' => 25,
                'minimum_stock' => 10
            ],
            [
                'name' => 'Busi',
                'brand' => 'NGK Iridium',
                'vehicle_type' => 'Honda PCX 160',
                'stock' => 30,
                'minimum_stock' => 10
            ],
            [
                'name' => 'V-Belt',
                'brand' => 'Aspira',
                'vehicle_type' => 'Yamaha XMAX 250',
                'stock' => 8,
                'minimum_stock' => 3
            ],
            [
                'name' => 'Kabel Gas',
                'brand' => 'Kawahara',
                'vehicle_type' => 'Honda CBR 150R',
                'stock' => 15,
                'minimum_stock' => 5
            ],
            [
                'name' => 'Kampas Kopling',
                'brand' => 'Honda Genuine Parts',
                'vehicle_type' => 'Honda CB150R',
                'stock' => 10,
                'minimum_stock' => 4
            ],
            [
                'name' => 'Piston Kit',
                'brand' => 'Racing',
                'vehicle_type' => 'Yamaha R15',
                'stock' => 6,
                'minimum_stock' => 2
            ],
            [
                'name' => 'Roller CVT',
                'brand' => 'TDR',
                'vehicle_type' => 'Honda PCX 160',
                'stock' => 16,
                'minimum_stock' => 5
            ],
            [
                'name' => 'CDI Racing',
                'brand' => 'BRT',
                'vehicle_type' => 'Yamaha Vixion',
                'stock' => 8,
                'minimum_stock' => 3
            ],
            [
                'name' => 'Seal Shock Depan',
                'brand' => 'AHM',
                'vehicle_type' => 'Honda Revo',
                'stock' => 14,
                'minimum_stock' => 5
            ],
            [
                'name' => 'Bearing Roda',
                'brand' => 'SKF',
                'vehicle_type' => 'All Type',
                'stock' => 20,
                'minimum_stock' => 8
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}