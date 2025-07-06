<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function showDetailDetails($id)
    {
        $user = User::with([
            'courseProgress.course',
            'lessonProgress.lesson' => function ($query) {
                $query->with('course');
            }
        ])->findOrFail($id);

        // Formatage des données
        $courses = $user->courseProgress->map(function ($progress) {
            return [
                'course_id' => $progress->course->id,
                'course_title' => $progress->course->title,
                'progress_percent' => $progress->progress_percent,
            ];
        });

        $lessonsCompleted = $user->lessonProgress->where('is_completed', true)->map(function ($progress) {
            return [
                'lesson_id' => $progress->lesson->id,
                'lesson_title' => $progress->lesson->title,
                'course_title' => $progress->lesson->course->title,
                'completed_at' => $progress->updated_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'courses_progress' => $courses,
            'lessons_completed' => $lessonsCompleted,
        ]);
    }
    public function index()
    {
        return response()->json(User::all());
    }

    public function show($id)
    {
        return response()->json(User::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,formateur,apprenant',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json($user, 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->only(['name', 'email', 'role']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(null, 204);
    }
}
