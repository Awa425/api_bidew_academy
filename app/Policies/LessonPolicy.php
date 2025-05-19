<?php

namespace App\Policies;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LessonPolicy
{

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Lesson $lesson): bool
    {
        return false;
    }

    /**
     * Vérifie si l'utilisateur peut créer une leçon pour un cours.
     */
    public function create(User $user, Course $course): bool
    {
        return $user->id === $course->user_id;
    }

    /**
     * Vérifie si l'utilisateur peut mettre à jour la leçon.
     */
    public function update(User $user, Lesson $lesson): bool
    {
        return $user->id === $lesson->course->user_id;
    }
}
