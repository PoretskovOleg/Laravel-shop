<?php

declare(strict_types=1);

namespace Domain\Catalog\Facades;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Builder run(Builder $builder)
 *
 * @see \Domain\Catalog\Sorter\Sorter
 */
final class Sorter extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Domain\Catalog\Sorter\Sorter::class;
    }
}
