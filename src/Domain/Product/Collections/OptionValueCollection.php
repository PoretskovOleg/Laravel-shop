<?php

declare(strict_types=1);

namespace Domain\Product\Collections;

use Domain\Product\Models\OptionValue;
use Illuminate\Database\Eloquent\Collection;

final class OptionValueCollection extends Collection
{
    public function keyValues(): \Illuminate\Support\Collection
    {
        return $this->mapToGroups(function (OptionValue $item) {
            return [$item->option->title => $item];
        });
    }
}
