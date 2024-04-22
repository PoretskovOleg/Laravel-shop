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
            $model->setSlug();
        });
    }

    private function setSlug(): void
    {
        $slug = $this->{$this->slugColumn()} ?? Str::slug($this->{$this->slugFrom()});

        $this->{$this->slugColumn()} = $this->uniqueSlug($slug);
    }

    private function slugFrom(): string
    {
        return 'title';
    }

    private function slugColumn(): string
    {
        return 'slug';
    }

    private function uniqueSlug(string $slug): string
    {
        $slugColumn = $this->slugColumn();
        $relatedSlugs = self::query()
            ->where($slugColumn, 'like', $slug.'%')
            ->orderBy($slugColumn)
            ->withoutGlobalScopes()
            ->pluck('id', $slugColumn)
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
