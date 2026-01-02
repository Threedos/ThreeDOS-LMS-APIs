<?php

namespace Database\Seeders;

use App\Models\Council;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();


        Role::create(['name' => 'Head']);
        Role::create(['name' => 'Instructor']);
        Role::create(['name' => 'Delegate']);

        Council::create([
            'name' => 'Backend Development Council',
            'description' => 'Backend Development Council',
        ]);





        User::create([
            'name' => 'Mohamed Tarek',
            'email' => 'mohamedtarek@example.com',
            'password' => 'password',
            'role_id' => Role::where('name', 'Head')->first()->id,
            'council_id' => Council::where('name', 'Backend Development Council')->first()->id,
        ]);




    }
}
