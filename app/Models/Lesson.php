<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'title',
        'content',
        'order',
        'duration_minutes',
        'is_published',
        'course_id'
    ];

    /**
     * Get the course that owns the lesson.
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
