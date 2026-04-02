<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ContactInfo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Default admin account (password: admin123)
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'email'       => 'admin@digimark.com',
                'password'    => Hash::make('admin123'),
                'full_name'   => 'Administrator',
                'role'        => 'admin',
                'badge'       => 'Profesional Portfolio',
                'headline'    => 'Wujudkan Ide & Proyek Terbaik Anda.',
                'description' => 'Selamat datang di portfolio saya. Temukan karya dan layanan terbaik yang saya tawarkan.',
            ]
        );

        // Default contact info for admin
        ContactInfo::firstOrCreate(
            ['user_id' => $admin->id],
            [
                'cta_title'       => 'Mari Berkolaborasi!',
                'cta_description' => 'Butuh bantuan mengelola media sosial atau merancang strategi digital?',
            ]
        );

        $this->command->info('✅ Admin seeded: admin@digimark.com / admin123');
    }
}
