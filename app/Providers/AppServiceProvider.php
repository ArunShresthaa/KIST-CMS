<?php

namespace App\Providers;

use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
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
        FilamentAsset::register([
            // CSS
            Css::make(
                'nepali-datepicker-css',
                asset('nepali.datepicker.v4.0.8/nepali.datepicker.v4.0.8.min.css')
            ),

            Css::make(
                'nepali-datepicker-override',
                asset('css/nepali-datepicker-override.css')
            ),

            // JS
            Js::make(
                'nepali-datepicker-js',
                asset('nepali.datepicker.v4.0.8/nepali.datepicker.v4.0.8.min.js')
            ),

            Js::make(
                'nepali-datepicker-init',
                asset('js/nepali-datepicker-init.js')
            ),
        ]);
    }
}
