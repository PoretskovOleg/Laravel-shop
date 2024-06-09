<?php

namespace Database\Factories;

use Domain\Product\Models\Option;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Option>
 */
class OptionFactory extends Factory
{
    protected $model = Option::class;

    public function definition(): array
    {
        return [
            'title' => Str::ucfirst($this->faker->word()),
        ];
    }
}
