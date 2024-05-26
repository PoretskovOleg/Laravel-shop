<?php

declare(strict_types=1);

namespace Domain\Catalog\Sorter;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Stringable;

final class Sorter
{
    public const SORT_KEY = 'sort';

    public function __construct(
        protected array $columns = []
    ) {
    }

    public function run(Builder $builder): Builder
    {
        $sortData = $this->sortData();

        return $builder->when($sortData->contains($this->columns()), function (Builder $builder) use ($sortData) {
            $builder->orderBy(
                $sortData->remove('-')->value(),
                $sortData->contains('-') ? 'desc' : 'asc'
            );
        });
    }

    public function key(): string
    {
        return self::SORT_KEY;
    }

    public function columns(): array
    {
        return $this->columns;
    }

    private function sortData(): Stringable
    {
        return request()->str($this->key());
    }

    public function isActive(string $column, string $direction = 'asc'): bool
    {
        $column = trim($column, '-');

        if (strtolower($direction) === 'desc') {
            $column = '-'.$column;
        }

        return request($this->key()) === $column;
    }
}
