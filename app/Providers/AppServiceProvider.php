<?php

namespace App\Providers;

use App\Http\Kernel;
use Carbon\CarbonInterval;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict(! app()->isProduction());

        DB::whenQueryingForLongerThan(
            CarbonInterval::seconds(2),
            function (Connection $connection, QueryExecuted $event) {
                Log::channel('telegram')
                    ->debug('WhenQueryingForLongerThan: '.$connection->query()->toSql());
            }
        );

        $kernel = app(Kernel::class);
        $kernel->whenRequestLifecycleIsLongerThan(
            CarbonInterval::seconds(4),
            function () {
                Log::channel('telegram')
                    ->debug('WhenRequestLifecycleIsLongerThan: '.request()->url());
            }
        );
    }
}
