<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database. Roles must exist before the demo
     * data so the seeded users can be assigned their roles.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            DemoSeeder::class,
            RolesAndPermissionsSeeder::class,
        ]);
    }
}
