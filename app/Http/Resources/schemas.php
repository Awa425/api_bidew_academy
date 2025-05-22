<?php

declare(strict_types=1);

/**
 * @OA\Schema(
 *     schema="User",
 *     required={"name", "email", "role"},
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *     @OA\Property(property="role", type="string", enum={"admin", "formateur", "apprenant"}, example="apprenant"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

/**
 * @OA\Schema(
 *     schema="Course",
 *     required={"title", "user_id"},
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="title", type="string", example="Introduction to Laravel"),
 *     @OA\Property(property="description", type="string", nullable=true, example="A comprehensive course about Laravel framework"),
 *     @OA\Property(property="category", type="string", nullable=true, example="Web Development"),
 *     @OA\Property(property="level", type="string", nullable=true, example="Beginner"),
 *     @OA\Property(property="image_path", type="string", format="uri", nullable=true, example="images/courses/laravel.jpg"),
 *     @OA\Property(property="duration_minutes", type="integer", nullable=true, example=120),
 *     @OA\Property(property="is_published", type="boolean", default=false),
 *     @OA\Property(property="user_id", type="integer", format="int64", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(
 *         property="lessons",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Lesson")
 *     ),
 *     @OA\Property(
 *         property="resources",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Resource")
 *     )
 * )
 */

/**
 * @OA\Schema(
 *     schema="Lesson",
 *     required={"title", "course_id"},
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="title", type="string", example="Introduction to Laravel"),
 *     @OA\Property(property="content", type="string", nullable=true, example="This is the first lesson content"),
 *     @OA\Property(property="duration_minutes", type="integer", nullable=true, example=30),
 *     @OA\Property(property="video_url", type="string", format="uri", nullable=true, example="https://example.com/video1"),
 *     @OA\Property(property="course_id", type="integer", format="int64", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

/**
 * @OA\Schema(
 *     schema="Resource",
 *     required={"title", "type", "url", "course_id"},
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="title", type="string", example="PDF Guide"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Detailed guide about the course"),
 *     @OA\Property(property="type", type="string", enum={"pdf", "video", "link", "document"}, example="pdf"),
 *     @OA\Property(property="url", type="string", format="uri", example="https://example.com/guide.pdf"),
 *     @OA\Property(property="course_id", type="integer", format="int64", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

/**
 * @OA\Schema(
 *     schema="Evaluation",
 *     required={"title", "description", "passing_score", "course_id"},
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="title", type="string", example="Final Exam"),
 *     @OA\Property(property="description", type="string", example="Final evaluation for the course"),
 *     @OA\Property(property="passing_score", type="integer", example=70),
 *     @OA\Property(property="course_id", type="integer", format="int64", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

/**
 * @OA\Schema(
 *     schema="LoginRequest",
 *     required={"email", "password"},
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *     @OA\Property(property="password", type="string", format="password", example="password")
 * )
 */

/**
 * @OA\Schema(
 *     schema="RegisterRequest",
 *     required={"name", "email", "password"},
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *     @OA\Property(property="password", type="string", format="password", example="password"),
 *     @OA\Property(property="role", type="string", enum={"admin", "formateur", "apprenant"}, example="apprenant")
 * )
 */

/**
 * @OA\Schema(
 *     schema="TokenResponse",
 *     @OA\Property(property="access_token", type="string", example="1|abcdef123456"),
 *     @OA\Property(property="token_type", type="string", example="Bearer"),
 *     @OA\Property(property="user", ref="#/components/schemas/User")
 * )
 */

/**
 * @OA\Schema(
 *     schema="Error",
 *     @OA\Property(property="message", type="string", example="Error message"),
 *     @OA\Property(property="errors", type="object", example={"field": ["Error message"]})
 * )
 */

/**
 * @OA\Schema(
 *     schema="ValidationError",
 *     @OA\Property(property="message", type="string", example="The given data was invalid."),
 *     @OA\Property(property="errors", type="object", example={"email": ["The email field is required."]})
 * )
 */

/**
 * @OA\Schema(
 *     schema="NotFoundError",
 *     @OA\Property(property="message", type="string", example="Resource not found")
 * )
 */

/**
 * @OA\Schema(
 *     schema="UnauthenticatedError",
 *     @OA\Property(property="message", type="string", example="Unauthenticated")
 * )
 */

/**
 * @OA\Schema(
 *     schema="ForbiddenError",
 *     @OA\Property(property="message", type="string", example="This action is unauthorized")
 * )
 */
