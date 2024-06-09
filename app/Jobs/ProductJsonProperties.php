<?php

namespace App\Jobs;

use Domain\Product\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProductJsonProperties implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Product $product
    ) {
    }

    public function uniqueId(): int
    {
        return $this->product->getKey();
    }

    public function handle(): void
    {
        $this->product->updateQuietly([
            'json_properties' => $this->product->properties->keyValues(),
        ]);
    }
}
