<?php

declare(strict_types=1);

namespace App\View\ViewModels;

use App\View\ViewModels\Traits\Viewable;
use Domain\Catalog\ViewModels\BrandViewModel;
use Domain\Catalog\ViewModels\CategoryViewModel;
use Domain\Catalog\ViewModels\ProductViewModel;

final class HomeViewModel
{
    use Viewable;

    private function viewData(): array
    {
        $brands = BrandViewModel::make()->homePage();
        $categories = CategoryViewModel::make()->homePage();
        $products = ProductViewModel::make()->homePage();

        return compact('brands', 'categories', 'products');
    }
}
