<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSettings extends Model
{
    protected $table = 'settings_app';

    protected $fillable = [
        'name',
        'value',
        'extras',
    ];
}
