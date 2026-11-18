<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin GoSport',
            'email' => 'admin@gosport.test',
            'phone' => '081234567890',
            'role' => 'admin',
            'password' => 'password',
        ]);

        User::factory()->create([
            'name' => 'Customer GoSport',
            'email' => 'customer@gosport.test',
            'phone' => '081298765432',
            'role' => 'customer',
            'password' => 'password',
        ]);

        User::factory(8)->create();

        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            ProductVariantSeeder::class,
            CouponSeeder::class,
        ]);
    }
}
