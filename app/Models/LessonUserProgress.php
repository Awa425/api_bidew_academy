<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonUserProgress extends Model
{
     protected $table = 'lesson_user_progress';
         protected $fillable = [
        'lesson_id',
        'user_id',
        'is_locked',
        'is_completed',
        'started_at',
        'completed_at',
    ];

        public function user()
        {
            return $this->belongsTo(User::class);
        }

        public function lesson()
        {
            return $this->belongsTo(Lesson::class);
        }

        public function course()
        {
            return $this->belongsTo(Course::class);
        }
}
