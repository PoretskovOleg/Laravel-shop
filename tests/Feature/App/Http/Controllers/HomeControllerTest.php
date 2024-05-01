<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\HomeController;
use Database\Factories\BrandFactory;
use Database\Factories\CategoryFactory;
use Database\Factories\ProductFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        BrandFactory::new()->count(5)->create([
            'on_home_page' => true,
            'sorting' => 999,
        ]);

        $this->brand = BrandFactory::new()->createOne([
            'on_home_page' => true,
            'sorting' => 1,
        ]);

        CategoryFactory::new()->count(5)->create([
            'on_home_page' => true,
            'sorting' => 999,
        ]);

        $this->category = CategoryFactory::new()->createOne([
            'on_home_page' => true,
            'sorting' => 1,
        ]);

        ProductFactory::new()->count(5)->create([
            'on_home_page' => true,
            'sorting' => 999,
        ]);

        $this->product = ProductFactory::new()->createOne([
            'on_home_page' => true,
            'sorting' => 1,
        ]);
    }

    public function test_success_response(): void
    {
        $this->get(action(HomeController::class))
            ->assertViewIs('index')
            ->assertViewHas('brands.0', $this->brand)
            ->assertViewHas('categories.0', $this->category)
            ->assertViewHas('products.0', $this->product)
            ->assertOk();
    }
}
