<?php

namespace App\Entities;

use App\Interfaces\Enum;

class UserRoles extends Enum
{
    const ADMIN = 'admin';
    const FAN = 'fan';
    const EMPLOYEE = 'employee';
}

?>
