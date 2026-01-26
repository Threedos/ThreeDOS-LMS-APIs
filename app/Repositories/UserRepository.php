<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Models\Task;
use App\Http\Requests\PaginatedRequest;

class UserRepository implements UserRepositoryInterface
{
    public function getAllUsers(array $filters)
    {
        $query = User::query();

        if (isset($filters['council_id'])) {
            $query->where('council_id', $filters['council_id']);
        }

        if (!empty($filters['role'])) {
            return $query->whereHas('role', function ($query) use ($filters) {
                $query->where('name', $filters['role']);
            })->get();
        }

        return $query->get();
    }

    public function getAllUsersPaginated(array $filters)
    {
        $pageIndex = $filters['pageIndex'] ?? 1;
        $pageSize = $filters['pageSize'] ?? 10;
        $search = $filters['search'] ?? null;
        $sort = $filters['sort'] ?? null;
        $role_id = $filters['role_id'] ?? null;

        $query = User::query()->select('id', 'name', 'email', 'role_id', 'council_id');

        if (array_key_exists('council_id', $filters)) {
            $query->where('council_id', $filters['council_id']);
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }
        if ($sort) {
            $query->orderBy($sort);
        }
        if ($role_id) {
            $query->where('role_id', $role_id);
        }
        $query->orderBy('created_at', 'desc');
        $query
            ->join('roles', 'roles.id', '=', 'users.role_id')
            ->orderByRaw("
        CASE roles.name
            WHEN 'VicePresident' THEN 4
            WHEN 'Head' THEN 3
            WHEN 'Instructor' THEN 2
            ELSE 1
        END DESC
    ")
            ->select('users.*');

        // Laravel pagination (LengthAwarePaginator)
        return $query->paginate($pageSize, ['*'], 'page', $pageIndex);
    }

    public function getUserById($userId)
    {

        return User::select('id', 'name', 'email', 'role_id', 'council_id')
            ->findOrFail($userId);
    }

    public function createUser(array $userDetails)
    {
        return User::create($userDetails);
    }

    public function updateUser($userId, array $newDetails)
    {
        return User::whereId($userId)->update($newDetails);
    }

    public function deleteUser($userId)
    {
        User::destroy($userId);
    }

    public function bulkCreateUsers($file)
    {
        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\UsersImport, $file);
    }

    public function getDashboardData($userId)
    {
        $user = User::findOrFail($userId);

        // Eager load relationships for the current user
        $user->load(['submissions', 'attendances']);

        $submissions = $user->submissions;

        // 1. GPA Calculation (Average of graded submissions)
        $gradedSubmissions = $submissions->where('status', 'graded');
        $totalGrades = $gradedSubmissions->sum('grade');
        $gradedCountTotal = $gradedSubmissions->count();
        $gpa = $gradedCountTotal > 0 ? round($totalGrades / $gradedCountTotal, 2) : 0;

        // 2. Grade Distribution
        $gradeDistribution = [
            'A' => $submissions->where('grade', '>=', 8)->count(),
            'B' => $submissions->whereBetween('grade', [7, 7.99])->count(),
            'C' => $submissions->whereBetween('grade', [6, 6.99])->count(),
            'D' => $submissions->whereBetween('grade', [5, 5.99])->count(),
            'F' => $submissions->where('grade', '<', 5)->count(),
        ];

        // 3. Attendance Statistics
        $attendances = $user->attendances;
        $totalClasses = $attendances->count();
        $presentCount = $attendances->where('status', 'present')->count();
        $absentCount = $attendances->where('status', 'absent')->count();
        $lateCount = $attendances->where('status', 'late')->count();
        $attendanceRate = $totalClasses > 0 ? round(($presentCount / $totalClasses) * 100, 2) : 0;

        // 4. Task Statistics
        $councilId = $user->council_id;
        $totalCouncilTasksCount = Task::whereHas('councilSession', function ($q) use ($councilId) {
            $q->where('council_id', $councilId);
        })->count();

        // Get count of unique tasks the user has submitted
        $submittedTaskIds = $submissions->pluck('task_id')->unique();
        $submittedCount = $submittedTaskIds->count();

        // Logic: Tasks Not Submitted = Total Council Tasks - Unique Submitted Tasks
        $notSubmittedCount = max(0, $totalCouncilTasksCount - $submittedCount);

        // Detailed submission status
        $gradedCount = $submissions->where('status', 'graded')->count();
        $pendingGradingCount = $submittedCount - $gradedCount;

        $taskCompletionRate = $totalCouncilTasksCount > 0
            ? round(($submittedCount / $totalCouncilTasksCount) * 100, 2)
            : 0;

        // 5. Recent Submissions (Last 5)
        $recentSubmissions = $submissions->sortByDesc('created_at')->take(5)->values();

        return [
            'gpa' => $gpa,
            'total_submissions' => $submissions->count(),
            'grade_distribution' => $gradeDistribution,
            'attendance_rate' => $attendanceRate,
            'total_classes' => $totalClasses,
            'present_count' => $presentCount,
            'absent_count' => $absentCount,
            'late_count' => $lateCount,
            'task_stats' => [
                'total_tasks' => $totalCouncilTasksCount,
                'submitted_tasks' => $submittedCount,
                'not_submitted_tasks' => $notSubmittedCount,
                'submitted_but_pending' => $pendingGradingCount,
                'graded_tasks' => $gradedCount,
                'completion_rate' => $taskCompletionRate,
            ],
            'recent_submissions' => $recentSubmissions,
            'joined_at' => $user->created_at,
        ];
    }
}
