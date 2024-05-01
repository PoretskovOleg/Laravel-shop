<?php

declare(strict_types=1);

namespace Domain\Catalog\QueryBuilders;

use Domain\Catalog\Collections\CategoryCollection;
use Illuminate\Database\Eloquent\Builder;

/**
 * @method CategoryCollection get($columns = ['*'])
 */
final class CategoryQueryBuilder extends Builder
{
    public function homePage(): CategoryQueryBuilder
    {
        return $this->select(['id', 'title', 'slug'])
            ->where('on_home_page', true)
            ->orderBy('sorting')
            ->limit(6);
    }
}
