<?php

namespace Database\Seeders;

use App\Models\Council;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();


               // Roles (run once)
        $headRole = Role::firstOrCreate(['name' => 'Head']);
        $instructorRole = Role::firstOrCreate(['name' => 'Instructor']);
        $delegateRole = Role::firstOrCreate(['name' => 'Delegate']);

        // Council (run once)
        $backendCouncil = Council::firstOrCreate(
            ['name' => 'Backend Development Council'],
            ['description' => 'Backend Development Council']
        );
        $frontendCouncil = Council::firstOrCreate(
            ['name' => 'Frontend Development Council'],
            ['description' => 'Frontend Development Council']
        );



       
        User::firstOrCreate([
            'name' => 'Mohamed Tarek',
            'email' => fake()->email(),
            'password' => Hash::make('password'),
            'role_id' => $headRole->id,
            'council_id' => $backendCouncil->id,
        ]);

        User::firstOrCreate([
            'name' => 'John Doe',
            'email' => fake()->email(),
            'password' => Hash::make('password'),
            'role_id' => $instructorRole->id,
            'council_id' => $frontendCouncil->id,
        ]);

            


    }
}
