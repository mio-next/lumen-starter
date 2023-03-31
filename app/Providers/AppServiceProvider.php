<?php

namespace App\Providers;

use App\Extend\Paginate;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        /*
        |--------------------------------------------------------------------------
        | Register Config Files
        |--------------------------------------------------------------------------
        |
        | Now we will register the "app" configuration file. If the file exists in
        | your configuration directory it will be loaded; otherwise, we'll load
        | the default version. You may register other files below as needed.
        |
        */

        foreach (File::files(base_path('config')) as $file) {
            if ($file->getExtension() === 'php') {
                $this->app->configure($file->getBasename('.php'));
            }
        }

        $this->app->bind(LengthAwarePaginator::class, Paginate::class);

        $sqlDebug = env('SQL_DEBUG');
        $sqlDebug ? DB::enableQueryLog() : DB::disableQueryLog();
        if ($sqlDebug) {
            $no = 1;
            DB::listen(function ($query) use (&$no) {
                Log::withContext([
                    "SQL{$no}" => $query->sql, "SQL{$no}-Bindings" => $query->bindings, "SQL{$no}-Time" => $query->time
                ]);

                $no++;
            });
        }
    }
}
