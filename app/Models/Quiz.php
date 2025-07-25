<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
     protected $fillable = ['title', 'description', 'course_id'];
    public function course() { return $this->belongsTo(Course::class); }
    public function questions() { return $this->hasMany(Question::class); }
    public function userQuizzes() { return $this->hasMany(UserQuiz::class); }
    public function userAttempts(){ return $this->hasMany(UserQuiz::class);}
}
