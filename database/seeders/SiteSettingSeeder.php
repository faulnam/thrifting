<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'store_name', 'value' => 'fifa', 'group' => 'general'],
            ['key' => 'store_email', 'value' => 'help@fifa.test', 'group' => 'general'],
            ['key' => 'store_phone', 'value' => '0812-3456-7890', 'group' => 'general'],
            ['key' => 'announcement_text', 'value' => '🔥 FRESH VINTAGE DROPS SETIAP MINGGU | 100% Autentik 1-of-1 & Sudah Dicuci Higienis | Gratis Ongkir Min. Belanja Rp 500.000 | Pengiriman Cepat ke Seluruh Indonesia', 'group' => 'general'],
            ['key' => 'free_shipping_threshold', 'value' => '500000', 'group' => 'shipping'],
            ['key' => 'origin_address', 'value' => 'Jl. Jenderal Sudirman Kav. 52-53, SCBD', 'group' => 'shipping'],
            ['key' => 'origin_postal_code', 'value' => '12190', 'group' => 'shipping'],
            ['key' => 'origin_biteship_area_id', 'value' => 'IDNP6IDNC148IDND859', 'group' => 'shipping'],
            ['key' => 'payment_gateway_active', 'value' => 'midtrans', 'group' => 'payment'],
            ['key' => 'social_instagram', 'value' => 'https://instagram.com/fifa.thrift', 'group' => 'social'],
            ['key' => 'social_facebook', 'value' => 'https://facebook.com/fifa.thrift', 'group' => 'social'],
            ['key' => 'social_tiktok', 'value' => 'https://tiktok.com/@fifa.thrift', 'group' => 'social'],
            ['key' => 'newsletter_popup_enabled', 'value' => '0', 'group' => 'marketing'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'group' => $setting['group']]
            );
        }
    }
}
