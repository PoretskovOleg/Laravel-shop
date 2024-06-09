<?php

declare(strict_types=1);

namespace App\View\ViewModels;

use App\View\ViewModels\Traits\Viewable;
use Domain\Catalog\Models\Category;
use Domain\Product\Models\Product;

final class CatalogViewModel
{
    use Viewable;

    public function __construct(
        public ?Category $category
    ) {
    }

    protected function viewData(): array
    {
        $categories = Category::query()
            ->select(['id', 'title', 'slug'])
            ->has('products')
            ->get();

        $products = Product::query()
            ->select(['id', 'title', 'slug', 'thumbnail', 'price', 'json_properties'])
            ->search()
            ->withCategory($this->category)
            ->filtered()
            ->sorted()
            ->paginate(6);

        return [
            'category' => $this->category,
            'categories' => $categories,
            'products' => $products,
        ];
    }
}
