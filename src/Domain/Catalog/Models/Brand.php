<?php

namespace Domain\Catalog\Models;

use Domain\Catalog\QueryBuilders\BrandQueryBuilder;
use Domain\Product\Models\Product;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Support\Traits\Models\HasSlug;
use Support\Traits\Models\HasThumbnail;

/**
 * @property string $slug
 * @property string $title
 * @property string $thumbnail
 * @property bool $on_home_page
 * @property int $sorting
 *
 * @method static BrandQueryBuilder|Brand query()
 */
class Brand extends Model
{
    use HasFactory;
    use HasSlug;
    use HasThumbnail;

    protected $fillable = [
        'slug',
        'title',
        'thumbnail',
        'on_home_page',
        'sorting',
    ];

    protected static function booted(): void
    {
        static::created(function () {
            Cache::forget('brand_home_page');
        });
        static::updated(function () {
            Cache::forget('brand_home_page');
        });
        static::deleted(function () {
            Cache::forget('brand_home_page');
        });
    }

    public function newEloquentBuilder($query): BrandQueryBuilder
    {
        return new BrandQueryBuilder($query);
    }

    protected function thumbnailDir(): string
    {
        return 'brands';
    }

    public function thumbnailPath(): Attribute
    {
        return Attribute::make(
            get: fn () => 'storage/'.$this->thumbnail
        );
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
