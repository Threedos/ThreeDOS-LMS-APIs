<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Council;
use App\Models\CouncilSession;
use App\Models\Role;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Team;
use Faker\Factory;
use App\Models\TeamMember;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            /*
            |--------------------------------------------------------------------------
            | Roles
            |--------------------------------------------------------------------------
            */
            $roles = [
                'VicePresident',
                'Head',
                'Instructor',
                'Delegate',
            ];

            $roleModels = [];
            foreach ($roles as $role) {
                $roleModels[$role] = Role::firstOrCreate(['name' => $role]);
            }

            /*
            |--------------------------------------------------------------------------
            | Councils
            |--------------------------------------------------------------------------
            */
            $backendCouncil = Council::firstOrCreate(
                ['name' => 'Backend Development Council'],
                ['description' => 'Backend Development Council']
            );

            $frontendCouncil = Council::firstOrCreate(
                ['name' => 'Frontend Development Council'],
                ['description' => 'Frontend Development Council']
            );

            $marketingCouncil = Council::firstOrCreate(
                ['name' => 'Marketing Council'],
                ['description' => 'Marketing Council']
            );

            /*
            |--------------------------------------------------------------------------
            | Users
            |--------------------------------------------------------------------------
            */

            // // Vice President (NO council)
            // $vicePresident = User::firstOrCreate(
            //     ['email' => 'vp@threedos.local'],
            //     [
            //         'name'       => 'Vice President',
            //         'password'   => Hash::make('password'),
            //         'role_id'    => $roleModels['VicePresident']->id,
            //         'council_id' => null,
            //     ]
            // );

            // Head (Backend)
            $head = User::firstOrCreate(
                ['email' => 'head.backend@threedos.local'],
                [
                    'name'       => 'Mohamed Tarek Badr',
                    'password'   => Hash::make('password'),
                    'role_id'    => $roleModels['Head']->id,
                    'council_id' => $backendCouncil->id,
                ]
            );

            // Instructor (Frontend)
            $instructor = User::firstOrCreate(
                ['email' => 'instructor.frontend@threedos.local'],
                [
                    'name'       => 'John Doe',
                    'password'   => Hash::make('password'),
                    'role_id'    => $roleModels['Instructor']->id,
                    'council_id' => $frontendCouncil->id,
                ]
            );

            // Delegate (Frontend)
            $frontendDelegate = User::firstOrCreate(
                ['email' => 'delegate.frontend@threedos.local'],
                [
                    'name'       => 'Frontend Delegate',
                    'password'   => Hash::make('password'),
                    'role_id'    => $roleModels['Delegate']->id,
                    'council_id' => $frontendCouncil->id,
                ]
            );
            //Delegate (backend)
            $backendDelegate = User::firstOrCreate(
                
                [
                    'email' => 'delegate.backend@threedos.local',
                    'name'       => 'Backend Delegate',
                    'password'   => Hash::make('password'),
                    'role_id'    => $roleModels['Delegate']->id,
                    'council_id' => $backendCouncil->id,
                ]
            );
            /*
            |--------------------------------------------------------------------------
            | Council Sessions
            |--------------------------------------------------------------------------
            */
            $backendSession = CouncilSession::firstOrCreate(
                [
                    'title'      => 'Backend Session 1',
                    'council_id' => $backendCouncil->id,
                ],
                [
                    'date'        => now(),
                    'description' => 'Backend Council Session',
                    'material'    => 'Backend Material',
                ]
            );

            $frontendSession = CouncilSession::firstOrCreate(
                [
                    'title'      => 'Frontend Session 1',
                    'council_id' => $frontendCouncil->id,
                ],
                [
                    'date'        => now(),
                    'description' => 'Frontend Council Session',
                    'material'    => 'Frontend Material',
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Tasks
            |--------------------------------------------------------------------------
            */
            $backendTask = Task::firstOrCreate(
                [
                    'title' => 'Backend Task 1',
                    'council_session_id' => $backendSession->id,
                ],
                [
                    'description' => 'Backend Task Description',
                    'due_date'    => now()->addDays(7),
                    'status'      => 'Pending',
                ]
            );

            $frontendTask = Task::firstOrCreate(
                [
                    'title' => 'Frontend Task 1',
                    'council_session_id' => $frontendSession->id,
                ],
                [
                    'description' => 'Frontend Task Description',
                    'due_date'    => now()->addDays(7),
                    'status'      => 'Pending',
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Task Submissions
            |--------------------------------------------------------------------------
            */
            TaskSubmission::firstOrCreate(
                [
                    'task_id' => $frontendTask->id,
                    'user_id' => $frontendDelegate->id,
                ],
                [
                    'file'    => 'frontend_task.zip',
                    'status'  => 'Pending',
                    'grade'   => 'A',
                    'comment' => 'Good work',
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Attendance
            |--------------------------------------------------------------------------
            */
            Attendance::firstOrCreate(
                [
                    'user_id'            => $frontendDelegate->id,
                    'council_session_id' => $frontendSession->id,
                ],
                [
                    'status' => 'present',
                ]
            );

            
            /**
             * 
             * Teams
             */
          $backendTeam1 = Team::firstOrCreate([
                'team_number' => 1,
                'council_id' => $backendCouncil->id,
                'task_link' => null,
            ]);
            $backendTeam2 = Team::firstOrCreate([
                'team_number' => 2,
                'council_id' => $backendCouncil->id,
                'task_link' => null,
            ]);
            $frontendTeam1 = Team::firstOrCreate([
                'team_number' => 1,
                'council_id' => $frontendCouncil->id,
                'task_link' => null,
            ]);
            $frontendTeam2 = Team::firstOrCreate([
                'team_number' => 2,
                'council_id' => $frontendCouncil->id,
                'task_link' => null,
            ]);
            $marketingTeam1 = Team::firstOrCreate([
                'team_number' => 1,
                'council_id' => $marketingCouncil->id,
                'task_link' => null,
            ]);
            $marketingTeam2 = Team::firstOrCreate([
                'team_number' => 2,
                'council_id' => $marketingCouncil->id,
                'task_link' => null,
            ]);

            /**
             * 
             * Team Members
             */
            TeamMember::firstOrCreate([
                'team_id' => $backendTeam1->id,
                'user_id' => $backendDelegate->id,
            ]);
            TeamMember::firstOrCreate([
                'team_id' => $frontendTeam1->id,
                'user_id' => $frontendDelegate->id,
            ]);
        });
    }
}
