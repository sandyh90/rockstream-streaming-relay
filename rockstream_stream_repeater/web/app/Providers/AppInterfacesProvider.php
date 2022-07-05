<?php

namespace App\Providers;

use App\Component\Facades\AppInterfaces;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class AppInterfacesProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind('appinterfaces', function () {

            return new AppInterfaces();
        });
    }
}
