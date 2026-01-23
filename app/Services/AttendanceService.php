<?php

namespace App\Services;

use App\Interfaces\AttendanceRepositoryInterface;

class AttendanceService
{
    protected $attendanceRepository;

    public function __construct(AttendanceRepositoryInterface $attendanceRepository)
    {
        $this->attendanceRepository = $attendanceRepository;
    }

    public function getAllAttendances($request)
    {
            return $this->attendanceRepository->getAllAttendances($request->all());
     
    }

    public function getAttendanceById($id)
    {
        return $this->attendanceRepository->getAttendanceById($id);
    }

    public function createAttendance(array $details)
    {
        return $this->attendanceRepository->createAttendance($details);
    }

    public function updateAttendance($id, array $details)
    {
        return $this->attendanceRepository->updateAttendance($id, $details);
    }

    public function deleteAttendance($id)
    {
        return $this->attendanceRepository->deleteAttendance($id);
    }

    public function bulkStoreAttendances($file)
    {
        return $this->attendanceRepository->bulkStoreAttendances($file);
    }
}
