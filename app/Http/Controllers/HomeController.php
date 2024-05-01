<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Domain\Catalog\ViewModels\BrandViewModel;
use Domain\Catalog\ViewModels\CategoryViewModel;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $brands = BrandViewModel::make()->homePage();
        $categories = CategoryViewModel::make()->homePage();
        $products = Product::query()->homePage()->get(['id', 'title', 'thumbnail', 'price']);

        return view(
            'index',
            compact('brands', 'categories', 'products')
        );
    }
}
