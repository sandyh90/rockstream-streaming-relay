<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IngestStreamDestination extends Model
{
    protected $table = 'stream_ingest_dest';

    protected $fillable = [
        'input_stream_id',
        'user_id',
        'name_stream_dest',
        'platform_dest',
        'url_stream_dest',
        'key_stream_dest',
        'active_stream_dest',
    ];

    public function user_data()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function input_stream_data()
    {
        return $this->belongsTo(StreamInput::class, 'input_stream_id');
    }
}
