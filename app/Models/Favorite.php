<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model {
    protected $table = 'favorites';
    protected $fillable = ['media_id'];

    public function media() {
        return $this->belongsTo(Media::class, 'media_id');
    }
}
