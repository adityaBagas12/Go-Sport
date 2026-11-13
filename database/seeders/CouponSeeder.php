<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::updateOrCreate(['code' => 'GOSPORT10'], [
            'type' => 'percent',
            'value' => 10,
            'min_order' => 100000,
            'is_active' => true,
        ]);

        Coupon::updateOrCreate(['code' => 'GOSPORT50'], [
            'type' => 'fixed',
            'value' => 50000,
            'min_order' => 300000,
            'is_active' => true,
        ]);
    }
}
