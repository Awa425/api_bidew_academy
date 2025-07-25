<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserQuiz extends Model
{
    protected $fillable = ['user_id', 'quiz_id', 'score', 'percentage','submitted_at'];
    public function user() { return $this->belongsTo(User::class); }
    public function quiz() { return $this->belongsTo(Quiz::class); }
    public function answers() { return $this->hasMany(UserAnswer::class); }
}
