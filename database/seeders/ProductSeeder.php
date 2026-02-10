<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = \App\Models\Category::all();

        if ($categories->isEmpty()) {
            return;
        }

        for ($i = 0; $i < 50; $i++) {
            \App\Models\Product::factory()->create([
                'category_id' => $categories->random()->id,
            ]);
        }
    }
}