<?php

namespace App\Enums;

enum RolesEnum: string
{
    case VicePresident = 'VicePresident';
    case President = 'President';
    case Head = 'Head';
    case Instructor = 'Instructor';
    case HR = 'HR';
    case Delegate = 'Delegate';
    case Leader = 'Leader';
    case CoLeader = 'CoLeader';
}
