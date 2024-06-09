<?php

namespace Database\Factories;

use Domain\Product\Models\Option;
use Domain\Product\Models\OptionValue;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<OptionValue>
 */
class OptionValueFactory extends Factory
{
    protected $model = OptionValue::class;

    public function definition(): array
    {
        return [
            'title' => Str::ucfirst($this->faker->word()),
            'option_id' => Option::query()->inRandomOrder()->value('id'),
        ];
    }
}
