<?php

use Illuminate\Database\Seeder;

class StudentUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $relationships = [
            [
                'user_id' => 7,
                'student_id' => 1,
            ],
            [
                'user_id' => 7,
                'student_id' => 2,
            ],
            [
                'user_id' => 7,
                'student_id' => 3,
            ],
        ];
    }
}
