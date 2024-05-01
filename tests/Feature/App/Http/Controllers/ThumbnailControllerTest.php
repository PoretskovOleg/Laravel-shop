<?php

declare(strict_types=1);

namespace Tests\Feature\App\Http\Controllers;

use Database\Factories\ProductFactory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ThumbnailControllerTest extends TestCase
{
    public function test_generated_success(): void
    {
        $size = '500x500';
        $method = 'resize';
        $storage = Storage::disk('images');

        config()->set('image', ['allow_sizes' => [$size]]);

        $product = ProductFactory::new()->create();

        $this->get($product->makeThumbnail($size, $method))
            ->assertOk();

        $storage->assertExists(
            "products/$method/$size/".File::basename($product->thumbnail)
        );
    }
}
