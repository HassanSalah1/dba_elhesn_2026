<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdministrativeReport extends Model
{
    use HasFactory;

    protected $table = 'administrative_reports';
    protected $fillable = [
        'user_team_id',
        'user_id',
        'date',
        'subject',
        'events',
        'pros',
        'cons',
        'recommendations',
        'location'
    ];

    public function user_team()
    {
        return $this->belongsTo(UserTeam::class, 'user_team_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
