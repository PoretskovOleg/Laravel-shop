<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\View\ViewModels\CatalogViewModel;
use Domain\Catalog\Models\Category;
use Illuminate\Contracts\View\View;

class CatalogController extends Controller
{
    public function __invoke(?Category $category): View
    {
        return (new CatalogViewModel($category))->view('catalog.index');
    }
}
