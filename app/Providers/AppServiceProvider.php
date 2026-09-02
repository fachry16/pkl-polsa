<?php

namespace App\Providers;

use App\Models\TahunAkademik;
use Illuminate\Support\Facades\View;
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
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $globalTahunAkademik = TahunAkademik::where('is_active', true)->first()
                    ?? TahunAkademik::orderByDesc('tahun')->first();
                $view->with('globalTahunAkademik', $globalTahunAkademik);
            }
        });
    }
}
