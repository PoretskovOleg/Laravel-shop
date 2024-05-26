<?php

namespace App\Models;

use Domain\Catalog\Facades\Sorter;
use Domain\Catalog\Models\Brand;
use Domain\Catalog\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Pipeline\Pipeline;
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
 * @property int $price
 * @property bool $on_home_page
 * @property int $sorting
 * @property string $text
 * @property Brand $brand
 * @property Collection<Category> $categories
 *
 * @method static Builder|Product query()
 * @method static Builder homePage()
 * @method static Builder|Product filtered()
 * @method static Builder|Product sorted()
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
    ];

    protected $casts = [
        'price' => PriceCast::class,
    ];

    protected function thumbnailDir(): string
    {
        return 'products';
    }

    public function scopeHomePage(Builder $query): Builder
    {
        return $query->where('on_home_page', true)
            ->orderBy('sorting')
            ->limit(6);
    }

    public function scopeFiltered(Builder $query)
    {
        return app(Pipeline::class)
            ->send($query)
            ->through(filters())
            ->thenReturn();
    }

    public function scopeSorted(Builder $query): Builder
    {
        return Sorter::run($query);
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

    #[SearchUsingFullText(['title', 'text'])]
    public function toSearchableArray(): array
    {
        return [
            'title' => $this->title,
            'text' => $this->text,
        ];
    }
}
