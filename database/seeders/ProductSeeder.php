<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $ayam     = Category::where('slug', 'ayam')->first()->id;
        $paket    = Category::where('slug', 'paket')->first()->id;
        $tambahan = Category::where('slug', 'tambahan')->first()->id;

        $products = [
            // Menu Satuan - Ayam
            [
                'code'        => 'AYM-001',
                'name'        => 'Paha Atas',
                'category_id' => $ayam,
                'price'       => 12000,
                'sort_order'  => 1,
            ],
            [
                'code'        => 'AYM-002',
                'name'        => 'Dada',
                'category_id' => $ayam,
                'price'       => 12000,
                'sort_order'  => 2,
            ],
            [
                'code'        => 'AYM-003',
                'name'        => 'Paha Bawah',
                'category_id' => $ayam,
                'price'       => 10000,
                'sort_order'  => 3,
            ],
            [
                'code'        => 'AYM-004',
                'name'        => 'Sayap',
                'category_id' => $ayam,
                'price'       => 10000,
                'sort_order'  => 4,
            ],
            // Tambahan
            [
                'code'        => 'TMB-001',
                'name'        => 'Nasi',
                'category_id' => $tambahan,
                'price'       => 3000,
                'sort_order'  => 1,
            ],
            // Menu Paket
            [
                'code'        => 'PKT-001',
                'name'        => 'Paket Paha Atas + Nasi',
                'category_id' => $paket,
                'price'       => 15000,
                'sort_order'  => 1,
            ],
            [
                'code'        => 'PKT-002',
                'name'        => 'Paket Dada + Nasi',
                'category_id' => $paket,
                'price'       => 15000,
                'sort_order'  => 2,
            ],
            [
                'code'        => 'PKT-003',
                'name'        => 'Paket Paha Bawah + Nasi',
                'category_id' => $paket,
                'price'       => 13000,
                'sort_order'  => 3,
            ],
            [
                'code'        => 'PKT-004',
                'name'        => 'Paket Sayap + Nasi',
                'category_id' => $paket,
                'price'       => 13000,
                'sort_order'  => 4,
            ],
        ];

        foreach ($products as $product) {
            Product::create(array_merge($product, ['is_active' => true]));
        }
    }
}
