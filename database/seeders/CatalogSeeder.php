<?php

namespace Database\Seeders;

use Database\Factories\CategoryFactory;
use Database\Factories\OptionValueFactory;
use Database\Factories\ProductFactory;
use Database\Factories\PropertyFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $properties = PropertyFactory::new()->count(10)->create();
        $optionValues = OptionValueFactory::new()->count(10)->create();

        CategoryFactory::new()
            ->count(10)
            ->has(
                ProductFactory::new()->count(rand(10, 20))
                    ->hasAttached($properties, function () {
                        return ['value' => Str::ucfirst(fake()->word())];
                    })
                    ->hasAttached($optionValues)
            )
            ->create();
    }
}
