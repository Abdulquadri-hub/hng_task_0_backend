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
        // $bindings = config('bindings');

        // foreach($bindings as $category => $interfaces) {
        //     foreach($interfaces as $interface => $implementation) {
        //         $this->app->bind($interface, $implementation);
        //     }
        // }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
