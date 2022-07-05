<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StreamInput extends Model
{
    protected $table = 'input_stream';

    protected $fillable = [
        'user_id',
        'name_input',
        'key_input_stream',
        'name_input_stream',
        'is_live',
        'identifier_stream',
        'active_input_stream'
    ];

    public function user_data()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ingest_destination_data()
    {
        return $this->hasMany(IngestStreamDestination::class, 'input_stream_id');
    }
}
