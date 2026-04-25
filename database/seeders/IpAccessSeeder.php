<?php

namespace Database\Seeders;

use App\Models\IpAccessSetting;
use Illuminate\Database\Seeder;

class IpAccessSeeder extends Seeder
{
    public function run(): void
    {
        IpAccessSetting::updateOrCreate(
            ['id' => 1],
            [
                'mode' => IpAccessSetting::MODE_ALL,
                'is_active' => true,
            ]
        );
    }
}
