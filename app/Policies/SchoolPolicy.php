<?php

namespace App\Policies;

use App\Models\SchoolUser;
use App\Models\User;
use App\Models\School;
use App\Models\Student;
use Illuminate\Auth\Access\HandlesAuthorization;

class SchoolPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the school.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\School  $school
     * @return mixed
     */
    public function view(User $user, School $school)
    {
        return SchoolUser::query()
            ->where('school_id', $school->id)
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Determine whether the user can create schools.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the school.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\School  $school
     * @return mixed
     */
    public function update(User $user, School $school)
    {
        //
    }

    /**
     * Determine whether the user can delete the school.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\School  $school
     * @return mixed
     */
    public function delete(User $user, School $school)
    {
        //
    }

    /**
     * Determine whether the user can work in the school.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\School  $school
     * @return mixed
     */
    public function work(User $user, School $school)
    {
        return SchoolUser::query()
            ->where('school_id', $school->id)
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Determine whether the user can manage the school.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\School  $school
     * @return mixed
     */
    public function manage(User $user, School $school)
    {
        return SchoolUser::query()
            ->where('school_id', $school->id)
            ->where('user_id', $user->id)
            ->where('is_founder', true)
            ->exists();
    }

    /**
     * Determine whether the student can study in the school.
     *
     * @param  \App\Models\Student  $student
     * @param  \App\Models\School  $school
     * @return mixed
     */
    public function study(Student $student, School $school)
    {
        return $student->school_id == $school->id;
    }

}
