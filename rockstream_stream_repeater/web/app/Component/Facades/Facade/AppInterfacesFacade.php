<?php

namespace App\Component\Facades\Facade;

use Illuminate\Support\Facades\Facade;

class AppInterfacesFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'appinterfaces';
    }
}
