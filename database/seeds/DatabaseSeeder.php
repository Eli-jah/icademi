<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AdminTablesSeeder::class);
        // $this->call(OldAdminTablesSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(SchoolsTableSeeder::class);
        $this->call(SchoolUserTableSeeder::class);
        $this->call(StudentsTableSeeder::class);
        $this->call(StudentUserTableSeeder::class);
    }
}
