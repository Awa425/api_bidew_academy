<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAnswer extends Model
{
     protected $fillable = ['user_quiz_id', 'question_id', 'answer_id', 'text_response'];
    public function userQuiz() { return $this->belongsTo(UserQuiz::class); }
    public function question() { return $this->belongsTo(Question::class); }
    public function answer() { return $this->belongsTo(Answer::class); }
}
