<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
        protected $fillable = [
        'lesson_id',
        'title',
        'type',
        'path',
        'description',
        'is_downloadable',
    ];
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

 
}
