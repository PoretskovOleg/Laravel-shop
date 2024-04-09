<?php

declare(strict_types=1);

namespace App\Faker;

use Faker\Provider\Base;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class FakerImageProvider extends Base
{
    public function loremFlickrImage(string $dir = '', int $width = 500, int $height = 500): ?string
    {
        $name = $dir.DIRECTORY_SEPARATOR.Str::random(10).'.jpg';

        $result = Storage::put(
            $name,
            Http::get("https://loremflickr.com/$width/$height")->body()
        );

        if (! $result) {
            return null;
        }

        return '/storage/'.$name;
    }
}
