<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Album extends Model {
    protected $table = 'albums';
    protected $fillable = ['title', 'description'];

    public function media() {
        return $this->belongsToMany(Media::class, 'album_media', 'album_id', 'media_id');
    }
}
