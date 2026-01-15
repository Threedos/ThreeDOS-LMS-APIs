<?php

namespace App\Enums;

enum RolesEnum: string
{
    case Head = 'Head';
    case Instructor = 'Instructor';
    case Delegate = 'Delegate';
    case Leader = 'Leader';
    case CoLeader = 'CoLeader';
}
