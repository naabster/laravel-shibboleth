<?php

namespace StudentAffairsUwm\Shibboleth;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Route;

class ShibalikeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../resources/views/Shibalike/' => resource_path('views/vendor/shibalike'),
        ]);

        if (config('shibboleth.emulate_idp')) {
            $this->loadRoutesFrom(__DIR__ . '/../../routes/shibalike.php');
        }
    }
}
