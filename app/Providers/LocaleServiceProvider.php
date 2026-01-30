<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

class LocaleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Set locale from session on every request
        if (Session::has('locale')) {
            $locale = Session::get('locale');
            if (in_array($locale, ['en', 'sw'])) {
                App::setLocale($locale);
            }
        }
    }
}
