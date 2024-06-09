<?php

declare(strict_types=1);

namespace Domain\Product\QueryBuilders;

use Domain\Catalog\Facades\Sorter;
use Domain\Catalog\Models\Category;
use Domain\Product\Collections\ProductCollection;
use Domain\Product\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pipeline\Pipeline;

/**
 * @method ProductCollection get($columns = ['*'])
 */
final class ProductQueryBuilder extends Builder
{
    public function homePage(): ProductQueryBuilder
    {
        return $this
            ->select(['id', 'title', 'slug', 'thumbnail', 'price'])
            ->where('on_home_page', true)
            ->orderBy('sorting')
            ->limit(6);
    }

    public function withCategory(?Category $category): ProductQueryBuilder
    {
        return $this->when($category->exists, function (Builder $query) use ($category) {
            $query->whereRelation(
                'categories',
                'categories.id',
                '=',
                $category->id
            );
        });
    }

    public function search(): ProductQueryBuilder
    {
        return $this->when(request('search'), function (Builder $query) {
            $query->whereFullText(['title', 'text'], request('search'));
        });
    }

    public function filtered(): ProductQueryBuilder
    {
        app(Pipeline::class)
            ->send($this)
            ->through(filters())
            ->thenReturn();

        return $this;
    }

    public function sorted(): ProductQueryBuilder
    {
        Sorter::run($this);

        return $this;
    }

    public function alsoProducts(Product $product): ProductQueryBuilder
    {
        return $this
            ->whereNot('id', $product->id)
            ->whereIn('id', session('also'))
            ->limit(6);
    }
}
