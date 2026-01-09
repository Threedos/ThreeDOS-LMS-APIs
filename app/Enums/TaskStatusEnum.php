<?php

namespace App\Enums;


enum TaskStatusEnum: string
{
    case SUBMITTED = 'submitted';
    case GRADED    = 'graded';
}
