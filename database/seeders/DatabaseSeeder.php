<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Equipment;
use App\Models\Issuance;
use App\Models\Request;
use App\Models\Staff;
use App\Models\Settings;
use App\Models\HistoryLog;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
      Settings::create([
            'key' => 'system_title',
            'value' => 'UPITDC - Inventory System',
        ]);
    }
}
