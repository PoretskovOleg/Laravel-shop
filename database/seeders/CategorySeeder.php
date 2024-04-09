<?php

namespace Database\Seeders;

use Database\Factories\CategoryFactory;
use Database\Factories\ProductFactory;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        CategoryFactory::new()
            ->count(10)
            ->has(
                ProductFactory::new()->count(rand(10, 20))
            )
            ->create();
    }
}
