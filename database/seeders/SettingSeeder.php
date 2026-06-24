<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Informasi Toko
            ['key' => 'store_name',    'value' => 'FRIED CHICKEN',           'group' => 'store'],
            ['key' => 'store_address', 'value' => 'Jl. Contoh No. 1',        'group' => 'store'],
            ['key' => 'store_phone',   'value' => '08123456789',             'group' => 'store'],
            ['key' => 'store_tagline', 'value' => 'Ayam Goreng Crispy Enak', 'group' => 'store'],

            // Struk
            ['key' => 'receipt_footer',       'value' => "Terima Kasih\nSelamat Menikmati",         'group' => 'receipt'],
            ['key' => 'receipt_paper_size',   'value' => '80',                                       'group' => 'receipt'],
            ['key' => 'receipt_show_tax',     'value' => '0',                                        'group' => 'receipt'],

            // Pajak & Diskon
            ['key' => 'tax_rate',     'value' => '0',  'group' => 'finance'],
            ['key' => 'currency',     'value' => 'IDR', 'group' => 'finance'],

            // QRIS
            ['key' => 'qris_image',   'value' => '',    'group' => 'payment'],
            ['key' => 'qris_enabled', 'value' => '1',   'group' => 'payment'],
            ['key' => 'cash_enabled', 'value' => '1',   'group' => 'payment'],

            // Aplikasi
            ['key' => 'app_version',  'value' => '1.0.0', 'group' => 'app'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
