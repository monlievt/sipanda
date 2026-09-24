<?php

namespace App\Providers;

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
        // Pastikan direktori storage framework selalu tersedia dengan permission yang sesuai (0777)
        $storagePaths = [
            storage_path('framework/views'),
            storage_path('framework/cache'),
            storage_path('framework/cache/data'),
            storage_path('framework/sessions'),
            storage_path('framework/testing'),
            storage_path('app/public'),
            storage_path('app/temp'),
            storage_path('logs'),
            base_path('bootstrap/cache'),
        ];

        foreach ($storagePaths as $dir) {
            if (!is_dir($dir)) {
                @mkdir($dir, 0777, true);
            }
            @chmod($dir, 0777);
        }
    }
}
