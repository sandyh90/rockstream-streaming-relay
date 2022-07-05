<?php

namespace App\Component\Facades;

use App\Models\AppSettings;

use App\Component\Addons\CachedValuestore;

class AppInterfaces
{
    public function getsetting($key_name = NULL, $default = NULL)
    {
        if (is_null($key_name)) {
            return FALSE;
        } else {
            $value = CachedValuestore::make(storage_path('app/settings-app.json'));
            if ($value->has($key_name) && is_null($default)) {
                return $value->get($key_name);
            } else {
                return $value->get($key_name, $default);
            }
        }
    }
}
