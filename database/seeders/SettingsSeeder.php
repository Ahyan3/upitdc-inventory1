<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Settings;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            [
                'key' => 'system_title',
                'value' => 'UPITDC - Inventory System',
                'type' => 'string',
                'description' => 'The title displayed in the application header.',
            ],
            [
                'key' => 'default_return_period',
                'value' => '30',
                'type' => 'integer',
                'description' => 'Default number of days for equipment return.',
            ],
            [
                'key' => 'allow_duplicate_pr',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Allow duplicate PR numbers in equipment records.',
            ],
        ];

        foreach ($settings as $setting) {
            Settings::firstOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'description' => $setting['description'],
                ]
            );
        }
    }
}