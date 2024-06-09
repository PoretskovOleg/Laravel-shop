<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\View\ViewModels\ProductViewModel;
use Domain\Product\Models\Product;
use Illuminate\Contracts\View\View;

class ProductController extends Controller
{
    public function __invoke(Product $product): View
    {
        return (new ProductViewModel($product))->view('product.show');
    }
}
