<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PremiereVideo extends Model
{
    protected $table = 'premiere_video';

    protected $fillable = [
        'user_id',
        'title_video',
        'video_path',
        'video_thumbnail',
        'is_premiere',
        'active_premiere_video'
    ];

    public function user_data()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
