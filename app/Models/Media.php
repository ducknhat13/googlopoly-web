<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Album;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Media extends Model {
    use HasFactory;

    protected $table = 'media';
    protected $fillable = [
        'title',
        'description',
        'file_path',
        'type',
        'file_size',
        'mime_type',
        'user_id'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function albums() {
        return $this->belongsToMany(Album::class, 'album_media', 'media_id', 'album_id');
    }
}
