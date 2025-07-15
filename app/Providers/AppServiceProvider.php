<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ✅ Auto-run migration on first request in production
        if (app()->environment('production')) {
            try {
                DB::connection()->getPdo();
                if (!Schema::hasTable('migrations')) {
                    Artisan::call('migrate', ['--force' => true]);
                    Log::info('Database migrated automatically.');
                }
            } catch (\Exception $e) {
                Log::error("Migration check failed: " . $e->getMessage());
            }
        }
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
