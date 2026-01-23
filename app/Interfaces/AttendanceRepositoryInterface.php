<?php

namespace App\Interfaces;

interface AttendanceRepositoryInterface
{
    public function getAllAttendances(array $filters);
    public function getAttendanceById($id);
    public function createAttendance(array $details);
    public function updateAttendance($id, array $details);
    public function deleteAttendance($id);
    public function bulkStoreAttendances($file);
}
