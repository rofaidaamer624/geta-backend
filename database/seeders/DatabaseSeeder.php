<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdminUser;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // إنشاء أدمن افتراضي
        AdminUser::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Main Admin',
                'password' => Hash::make('password123'),
            ]
        );

        // لو حبيتي تضيفي بيانات تانية (partners تجريبية مثلاً) تقدري تضيفيها هنا
        // مثال بسيط (اختياري):

        // \App\Models\Partner::create([
        //     'name'        => 'Oman Arab Bank',
        //     'website_url' => 'https://www.oab.om',
        //     'sort_order'  => 1,
        // ]);

        // \App\Models\Partner::create([
        //     'name'        => 'ICC',
        //     'website_url' => 'https://www.icc.com',
        //     'sort_order'  => 2,
        // ]);
    }
}
