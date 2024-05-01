<?php

declare(strict_types=1);

namespace Support\Testing;

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

        return $name;
    }

    public function fixturesImage(string $fixturesDir, string $storageDir): string
    {
        $storage = Storage::disk('images');

        if (! $storage->exists($storageDir)) {
            $storage->makeDirectory($storageDir);
        }

        $file = $this->generator->file(
            base_path("tests/Fixtures/images/$fixturesDir"),
            $storage->path($storageDir),
            false
        );

        return 'images/'.trim($storageDir, '/').'/'.$file;
    }
}
