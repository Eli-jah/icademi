<?php

use App\Models\School;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SchoolsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $schools = [
            [
                'user_id' => 1,
                'name' => 'school-no-1',
                'approved_at' => Carbon::now()->addDay(1)->toDateTimeString(),
                'rejected_at' => null,
            ],
            [
                'user_id' => 2,
                'name' => 'school-no-2',
                'approved_at' => null,
                'rejected_at' => Carbon::now()->addDay(1)->toDateTimeString(),
            ],
            [
                'user_id' => 3,
                'name' => 'school-no-3',
                'approved_at' => Carbon::now()->addDay(1)->toDateTimeString(),
                'rejected_at' => null,
            ],
            [
                'user_id' => 4,
                'name' => 'school-no-4',
                'approved_at' => null,
                'rejected_at' => Carbon::now()->addDay(1)->toDateTimeString(),
            ],
            [
                'user_id' => 5,
                'name' => 'school-no-5',
                'approved_at' => Carbon::now()->addDay(1)->toDateTimeString(),
                'rejected_at' => null,
            ],
            [
                'user_id' => 6,
                'name' => 'school-no-6',
                'approved_at' => null,
                'rejected_at' => Carbon::now()->addDay(1)->toDateTimeString(),
            ],
        ];
        foreach ($schools as $school) {
            School::query()
                ->create($school);
        }
    }
}
