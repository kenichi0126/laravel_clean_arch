<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->environment() !== 'local') {
            throw new Exception('This command can be executed only in the local environment.');
        }
        throw new Exception("Don't use.");
        $this->call(SponsorsTableSeeder::class);
        $this->call(SponsorRolesTableSeeder::class);
        $this->call(SponsorTrialsTableSeeder::class);
        $this->call(MembersTableSeeder::class);
        $this->call(SystemInformationTableSeeder::class);
    }
}
