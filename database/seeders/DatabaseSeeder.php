<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Equipment;
use App\Models\Issuance;
use App\Models\Request;
use App\Models\Staff;
use App\Models\Setting;
use App\Models\HistoryLog;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
      Setting::create([
            'key' => 'system_title',
            'value' => 'UPITDC - Inventory System',
        ]);
    }
}
