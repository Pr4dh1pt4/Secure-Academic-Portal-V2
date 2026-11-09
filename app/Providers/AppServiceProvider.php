<?php

namespace App\Providers;

use App\Models\LogAnalyzer;
use App\Models\MalwareScanner;
use App\Models\SecurityScanner;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
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
        // Kolom loggable_type menyimpan alias pendek, bukan nama class lengkap,
        // sehingga data log tidak rusak bila namespace model berubah.
        // Alias 'user' juga dipakai kolom model_type di tabel model_has_roles milik Spatie.
        Relation::enforceMorphMap([
            'user' => User::class,
            'security_scanner' => SecurityScanner::class,
            'malware_scanner' => MalwareScanner::class,
            'log_analyzer' => LogAnalyzer::class,
        ]);

        // Lazy loading dilempar sebagai exception di luar production,
        // sehingga N+1 query langsung ketahuan saat development dan testing.
        Model::preventLazyLoading(! $this->app->isProduction());
    }
}
