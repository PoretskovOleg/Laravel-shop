<?php

declare(strict_types=1);

namespace App\View\ViewModels;

use App\View\ViewModels\Traits\Viewable;
use Domain\Product\Models\Product;

final class ProductViewModel
{
    use Viewable;

    public function __construct(
        public Product $product
    ) {
        session()->put('also.'.$product->id, $product->id);
    }

    private function viewData(): array
    {
        $this->product->load(['optionValues.option']);

        $alsoProducts = [];
        if (session('also')) {
            $alsoProducts = Product::query()
                ->select(['id', 'title', 'slug', 'thumbnail', 'price', 'json_properties'])
                ->alsoProducts($this->product)
                ->get();
        }

        return [
            'product' => $this->product,
            'options' => $this->product->optionValues->keyValues(),
            'alsoProducts' => $alsoProducts,
        ];
    }
}
