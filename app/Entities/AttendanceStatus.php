<?php

namespace App\Entities;

use App\Interfaces\Enum;

class AttendanceStatus extends Enum
{
    const present = 'present';
    const match = 'match';
    const lateness = 'lateness';
    const absence = 'absence';
    const comfort = 'comfort';
    const injury_treatment = 'injury_treatment';
    const external_mission = 'external_mission';
    const violation_instructions = 'violation_instructions';
    const no_fingerprint_registration = 'no_fingerprint_registration';
    const delete_player_from_list = 'delete_player_from_list';
    
}

?>
