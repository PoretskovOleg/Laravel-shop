<?php

namespace Database\Seeders;

use Database\Factories\OptionFactory;
use Illuminate\Database\Seeder;

class OptionSeeder extends Seeder
{
    public function run(): void
    {
        OptionFactory::new()->count(2)->create();
    }
}
