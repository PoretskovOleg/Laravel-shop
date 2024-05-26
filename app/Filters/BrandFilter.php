<?php

declare(strict_types=1);

namespace App\Filters;

use Domain\Catalog\Filters\AbstractFilter;
use Domain\Catalog\Models\Brand;
use Illuminate\Contracts\Database\Eloquent\Builder;

final class BrandFilter extends AbstractFilter
{
    public function title(): string
    {
        return 'Бренд';
    }

    public function key(): string
    {
        return 'brands';
    }

    public function apply(Builder $builder): Builder
    {
        return $builder->when($this->requestValue(), function (Builder $query) {
            $query->whereIn('brand_id', $this->requestValue());
        });
    }

    public function values(): array
    {
        return Brand::query()
            ->select(['id', 'title'])
            ->has('products')
            ->pluck('title', 'id')
            ->toArray();
    }

    public function view(): string
    {
        return 'catalog.filters.brands';
    }
}
