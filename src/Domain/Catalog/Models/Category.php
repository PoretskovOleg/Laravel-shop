<?php

namespace Domain\Catalog\Models;

use App\Models\Product;
use Domain\Catalog\Collections\CategoryCollection;
use Domain\Catalog\QueryBuilders\CategoryQueryBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;
use Support\Traits\Models\HasSlug;

/**
 * @property string $slug
 * @property string $title
 * @property bool $on_home_page
 * @property int $sorting
 *
 * @method static CategoryQueryBuilder|Category query()
 */
class Category extends Model
{
    use HasFactory;
    use HasSlug;

    protected $fillable = [
        'slug',
        'title',
        'on_home_page',
        'sorting',
    ];

    protected static function booted(): void
    {
        static::created(function () {
            Cache::forget('category_home_page');
        });
        static::updated(function () {
            Cache::forget('category_home_page');
        });
        static::deleted(function () {
            Cache::forget('category_home_page');
        });
    }

    public function newEloquentBuilder($query): CategoryQueryBuilder
    {
        return new CategoryQueryBuilder($query);
    }

    public function newCollection(array $models = []): CategoryCollection
    {
        return new CategoryCollection($models);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
