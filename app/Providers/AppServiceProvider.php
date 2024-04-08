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

        if (app()->isProduction()) {
            DB::whenQueryingForLongerThan(
                CarbonInterval::seconds(3),
                function (Connection $connection, QueryExecuted $query) {
                    Log::channel('telegram')
                        ->debug(
                            'WhenQueryingForLongerThan: '.$connection->totalQueryDuration().PHP_EOL.'SQL: '.$query->sql,
                            $query->bindings
                        );
                }
            );

            DB::listen(function ($query) {
                if ($query->time > 500) {
                    Log::channel('telegram')
                        ->debug(
                            'SQL TIME: '.$query->time.PHP_EOL.'SQL: '.$query->sql,
                            $query->bindings
                        );
                }
            });

            app(Kernel::class)->whenRequestLifecycleIsLongerThan(
                CarbonInterval::seconds(4),
                function () {
                    Log::channel('telegram')
                        ->debug('WhenRequestLifecycleIsLongerThan: '.request()->url());
                }
            );
        }
    }
}
