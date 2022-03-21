<?php

namespace Database\Seeders;

use App\Models\System\Tenant;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (Tenant::where('id', '1')->count() === 0) {
            $tenant = new \App\Models\System\Tenant();
            $tenant->uuid = '00000000-0000-0000-0000-000000000000';
            $tenant->name = 'Default';
            $tenant->save();
        }
    }
}
