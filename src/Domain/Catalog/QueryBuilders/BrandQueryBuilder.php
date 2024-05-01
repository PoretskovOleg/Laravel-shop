<?php

declare(strict_types=1);

namespace Domain\Catalog\QueryBuilders;

use Domain\Catalog\Collections\BrandCollection;
use Illuminate\Database\Eloquent\Builder;

/**
 * @method BrandCollection get($columns = ['*'])
 */
final class BrandQueryBuilder extends Builder
{
    public function homePage(): BrandQueryBuilder
    {
        return $this->select(['id', 'title', 'thumbnail', 'slug'])
            ->where('on_home_page', true)
            ->orderBy('sorting')
            ->limit(6);
    }
}
