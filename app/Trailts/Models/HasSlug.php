<?php

declare(strict_types=1);

namespace App\Trailts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasSlug
{
    protected static function bootHasSlug(): void
    {
        static::creating(function (Model $model) {
            $slug = $model->slug ?? Str::slug($model->{self::slugFrom()});
            $model->{self::slugColumn()} = self::uniqueSlug($slug);
        });
    }

    public static function slugFrom(): string
    {
        return 'title';
    }

    public static function slugColumn(): string
    {
        return 'slug';
    }

    private static function uniqueSlug(string $slug): string
    {
        $columnSlug = self::slugColumn();
        $relatedSlugs = self::query()
            ->where($columnSlug, 'like', $slug.'%')
            ->orderBy($columnSlug)
            ->pluck('id', $columnSlug)
            ->toArray();

        if (empty($relatedSlugs)) {
            return $slug;
        }

        $cnt = 0;
        while (true) {
            $relatedSlug = $cnt == 0 ? $slug : $slug.'-'.$cnt;
            if (! isset($relatedSlugs[$relatedSlug])) {
                break;
            }
            $cnt++;
        }

        return $relatedSlug;
    }
}
