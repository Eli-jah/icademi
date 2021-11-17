<?php

use App\Models\SchoolUser;
use Illuminate\Database\Seeder;

class SchoolUserTableSeeder extends Seeder
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
                'school_id' => 1,
                'user_id' => 7,
                'is_founder' => false,
            ],
            [
                'school_id' => 3,
                'user_id' => 7,
                'is_founder' => false,
            ],
            [
                'school_id' => 5,
                'user_id' => 7,
                'is_founder' => false,
            ],
        ];
        foreach ($relationships as $relationship) {
            SchoolUser::query()
                ->create($relationship);
        }
    }
}
