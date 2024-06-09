<?php

namespace Domain\Product\Models;

use App\Jobs\ProductJsonProperties;
use Domain\Catalog\Collections\CategoryCollection;
use Domain\Catalog\Models\Brand;
use Domain\Catalog\Models\Category;
use Domain\Product\Collections\OptionValueCollection;
use Domain\Product\Collections\ProductCollection;
use Domain\Product\Collections\PropertyCollection;
use Domain\Product\QueryBuilders\ProductQueryBuilder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;
use Laravel\Scout\Attributes\SearchUsingFullText;
use Laravel\Scout\Searchable;
use Support\Casts\PriceCast;
use Support\Traits\Models\HasSlug;
use Support\Traits\Models\HasThumbnail;

/**
 * @property int $id
 * @property string $slug
 * @property string $title
 * @property string $thumbnail
 * @property string $thumbnail_path
 * @property int $price
 * @property bool $on_home_page
 * @property int $sorting
 * @property string $text
 * @property Brand $brand
 * @property CategoryCollection<Category> $categories
 * @property PropertyCollection<Property> $properties
 * @property OptionValueCollection<OptionValue> $optionValues
 *
 * @method static ProductQueryBuilder|Product query()
 */
class Product extends Model
{
    use HasFactory;
    use HasSlug;
    use HasThumbnail;
    use Searchable;

    protected $fillable = [
        'slug',
        'title',
        'thumbnail',
        'price',
        'brand_id',
        'on_home_page',
        'sorting',
        'text',
        'json_properties',
    ];

    protected $casts = [
        'price' => PriceCast::class,
        'json_properties' => 'array',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::created(function (Product $product) {
            ProductJsonProperties::dispatch($product)->delay(now()->addSeconds(10));
        });
    }

    protected static function booted(): void
    {
        static::created(function () {
            Cache::forget('products_home_page');
        });
        static::updated(function () {
            Cache::forget('products_home_page');
        });
        static::deleted(function () {
            Cache::forget('products_home_page');
        });
    }

    public function newEloquentBuilder($query): ProductQueryBuilder
    {
        return new ProductQueryBuilder($query);
    }

    public function newCollection(array $models = []): ProductCollection
    {
        return new ProductCollection($models);
    }

    protected function thumbnailDir(): string
    {
        return 'products';
    }

    public function thumbnailPath(): Attribute
    {
        return Attribute::make(
            get: fn () => 'storage/'.$this->thumbnail
        );
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class)->withPivot('value');
    }

    public function optionValues(): BelongsToMany
    {
        return $this->belongsToMany(OptionValue::class);
    }

    #[SearchUsingFullText(['title', 'text'])]
    public function toSearchableArray(): array
    {
        return [
            'title' => $this->title,
            'text' => $this->text,
        ];
    }
}
