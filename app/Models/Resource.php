<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $fillable = ['type', 'title', 'url', 'module_id', 'course_id'];

    /**
     * Get the course that owns the resource.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the module that owns the resource.
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
