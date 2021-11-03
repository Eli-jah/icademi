<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $teachers = [
            [
                'name' => 'teacher-no-1',
                'email' => 'teacher-email-1@test.com',
                'password' => bcrypt('Qwerty123456'),
            ],
            [
                'name' => 'teacher-no-2',
                'email' => 'teacher-email-2@test.com',
                'password' => bcrypt('Qwerty123456'),
            ],
            [
                'name' => 'teacher-no-3',
                'email' => 'teacher-email-3@test.com',
                'password' => bcrypt('Qwerty123456'),
            ],
            [
                'name' => 'teacher-no-4',
                'email' => 'teacher-email-4@test.com',
                'password' => bcrypt('Qwerty123456'),
            ],
            [
                'name' => 'teacher-no-5',
                'email' => 'teacher-email-5@test.com',
                'password' => bcrypt('Qwerty123456'),
            ],
            [
                'name' => 'teacher-no-6',
                'email' => 'teacher-email-6@test.com',
                'password' => bcrypt('Qwerty123456'),
            ],
            [
                'name' => 'elijah-wang',
                'email' => 'elijah-wang@outlook.com',
                'password' => bcrypt('Qwerty123456'),
            ],
        ];
        foreach ($teachers as $teacher) {
            User::query()
                ->create($teacher);
        }
    }
}
