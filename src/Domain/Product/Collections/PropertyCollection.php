<?php

declare(strict_types=1);

namespace Domain\Product\Collections;

use Domain\Product\Models\Property;
use Illuminate\Database\Eloquent\Collection;

final class PropertyCollection extends Collection
{
    public function keyValues(): PropertyCollection|\Illuminate\Support\Collection
    {
        return $this->mapWithKeys(
            fn (Property $property) => [$property->title => $property->pivot->value]
        );
    }
}
