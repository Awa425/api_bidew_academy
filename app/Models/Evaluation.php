<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $fillable = ['user_id', 'activity_id', 'type', 'score', 'comment', 'course_id'];
    public function user()     { return $this->belongsTo(User::class); }
    public function activity() { return $this->belongsTo(Activity::class); }
    public function course()   { return $this->belongsTo(Course::class); }
}
