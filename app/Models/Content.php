<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $fillable = ['lesson_id','type','data','file_path','external_url'];

    public function lesson()
{
    return $this->belongsTo(Lesson::class);
}

}
