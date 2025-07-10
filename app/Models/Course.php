<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'title',
        'description',
        'prerequis',
        'objectif',
        'progression',
        'category',
        'level',
        'image_path',
        'duration_minutes',
        'is_published',
        'user_id',
    ];

    protected $casts = [
        'is_published' => 'boolean'
    ];

    /**
     * Get the user who created the course.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Get the lessons for the course.
     */
    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    /**
     * Get the resources for the course.
     */
    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

        public function quizzes()
    {
        return $this->hasOne(Quiz::class);
    }

    /**
     * Get the evaluations for the course.
     */
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }




}
