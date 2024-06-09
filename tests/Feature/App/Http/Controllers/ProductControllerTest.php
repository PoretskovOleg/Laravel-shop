<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\Catalog\ProductController;
use Database\Factories\ProductFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_success_response(): void
    {
        $product = ProductFactory::new()->createOne();

        $this->get(action(ProductController::class, $product))
            ->assertOk();
    }
}
