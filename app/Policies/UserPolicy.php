<?php

namespace App\Policies;

use App\Models\SchoolUser;
use App\Models\Student;
use App\Models\StudentUser;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function view(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function update(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function delete(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the student can follow the teacher.
     *
     * @param  \App\Models\Student  $student
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function follow(Student $student, User $user)
    {
        return SchoolUser::query()
            ->where('school_id', $student->school_id)
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Determine whether the student can unfollow the teacher.
     *
     * @param  \App\Models\Student  $student
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function unfollow(Student $student, User $user)
    {
        return StudentUser::query()
            ->where('student_id', $student->id)
            ->where('user_id', $user->id)
            ->exists();
    }
}
