<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $fillable = ['type', 'title', 'url', 'course_id'];

    /**
     * Get the course that owns the resource.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

 
}
