<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ServicesSeeder::class,
            PortfolioSeeder::class,
            SolutionsSeeder::class,
            SettingsSeeder::class,
            AdminUserSeeder::class,
            RedirectSeeder::class,
        ]);
    }
}
