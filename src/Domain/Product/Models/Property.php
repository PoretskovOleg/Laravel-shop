<?php

namespace Domain\Product\Models;

use Domain\Product\Collections\PropertyCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 */
class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
    ];

    public function newCollection(array $models = []): PropertyCollection
    {
        return new PropertyCollection($models);
    }
}
