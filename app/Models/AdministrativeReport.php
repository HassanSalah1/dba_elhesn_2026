<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdministrativeReport extends Model
{
    use HasFactory;

    protected $table = 'administrative_reports';
    protected $fillable = [
        'row_id',
        'user_team_id',
        'user_id',
        'official_id',
        'date',
        'subject',
        'events',
        'pros',
        'cons',
        'recommendations',
        'location',
        'synced_to_sqlserver',
    ];

    protected $casts = [
        'synced_to_sqlserver' => 'boolean',
        'date' => 'date',
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
