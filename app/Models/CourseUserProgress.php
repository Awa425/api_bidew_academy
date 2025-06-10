<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseUserProgress extends Model
{
     protected $fillable = [
        'user_id',
        'course_id',
        'current_lesson_id',
        'completed_lessons',
        'progress_percent',
        'started_at',
        'completed_at'
    ];

        protected $casts = [
        'completed_lessons' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function currentLesson()
    {
        return $this->belongsTo(Lesson::class, 'current_lesson_id');
    }
}
