<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = false;
        $type = false;
        if ($user = Auth::guard()->user()) {
            $type = 'teacher';
        } else if ($user = Auth::guard('student-web')->user()) {
            $type = 'student';
        }
        if (!$user) {
            return redirect()->route('login');
        }
        $user_id = $user->id;
        if ($type == 'teacher') {
            $user = User::query()
                ->with('founded_schools')
                ->with('joined_schools')
                ->with('fans_students')
                ->find($user_id);
            $token = $user->createToken('icademi-teacher')->accessToken;
            $schools = $user->joined_schools;
            $students = [];
            $student_ids = [];
            // $founded_school_ids = [];
            if ($user->founded_schools) {
                $founded_school_ids = $user->founded_schools
                    ->pluck('id')
                    ->toArray();
                $student_ids = Student::query()
                    ->whereIn('school_id', $founded_school_ids)
                    ->get()
                    ->pluck('id')
                    ->toArray();
            }
            $user->fans_students->each(function ($fans_student) use ($student_ids) {
                if (!in_array($fans_student->id, $student_ids)) {
                    $student_ids[] = $fans_student->id;
                }
            });
            if (count($student_ids) > 0) {
                $students = Student::query()
                    ->findMany($student_ids);
            }
            return view('home', [
                'user' => $user,
                'token' => $token,
                'schools' => $schools,
                'students' => $students,
            ]);
        } else if ($type == 'student') {
            $user = Student::query()
                ->with('school.teachers')
                // ->with('followed_teachers')
                ->find($user_id);
            $token = $user->createToken('icademi-student')->accessToken;
            $school = $user->school;
            $teachers = $school->teachers->map(function ($teacher) use ($user_id) {
                $teacher->is_followed = false;
                if (StudentUser::query()
                    ->where('user_id', $teacher->id)
                    ->where('student_id', $user_id)
                    ->exists()) {
                    $teacher->is_followed = true;
                }
                return $teacher;
            });
            // $followed_teachers = $user->followed_teachers;
            return view('home', [
                'user' => $user,
                'token' => $token,
                'school' => $school,
                'teachers' => $teachers,
                // 'followed_teachers' => $followed_teachers,
            ]);
        } else {
            return redirect()->route('login');
        }
    }
}
