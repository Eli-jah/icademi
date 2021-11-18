<?php

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $students = [
            [
                'school_id' => 1,
                'name' => 'student-no-1',
                'email' => 'student-email-1@test.com',
                'password' => bcrypt('Qwerty123456'),
            ],
            [
                'school_id' => 3,
                'name' => 'student-no-2',
                'email' => 'student-email-2@test.com',
                'password' => bcrypt('Qwerty123456'),
            ],
            [
                'school_id' => 5,
                'name' => 'student-no-3',
                'email' => 'student-email-3@test.com',
                'password' => bcrypt('Qwerty123456'),
            ],
            [
                'school_id' => 1,
                'name' => 'student-no-4',
                'email' => 'student-email-4@test.com',
                'password' => bcrypt('Qwerty123456'),
            ],
            [
                'school_id' => 3,
                'name' => 'student-no-5',
                'email' => 'student-email-5@test.com',
                'password' => bcrypt('Qwerty123456'),
            ],
            [
                'school_id' => 5,
                'name' => 'student-no-6',
                'email' => 'student-email-6@test.com',
                'password' => bcrypt('Qwerty123456'),
            ],
        ];
        foreach ($students as $student) {
            Student::query()
                ->create($student);
        }
    }
}
