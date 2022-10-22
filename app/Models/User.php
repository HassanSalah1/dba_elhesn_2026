<?php

namespace App\Models;

use App\Entities\Status;
use App\Entities\UserRoles;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'phone', 'email', 'password', 'role', 'status', 'lang',
        'image', 'edited_email'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'integer',
    ];


    public function getImageUrlAttribute()
    {
        return $this->image ? url($this->image, [], env('APP_ENV') === 'local' ? false : true) : null;
    }

    public function scopeEnabled($query)
    {
        return $query->where('status', Status::ACTIVE);
    }

    function isDashboardAuth()
    {
        if ($this->role === UserRoles::ADMIN) {
            return true;
        }
        return false;
    }

    function isCustomerAuth()
    {
        if ($this->role === UserRoles::FAN) {
            return true;
        }
        return false;
    }


    function isActiveCustomerAuth()
    {
        if ($this->role === UserRoles::FAN && $this->status === Status::ACTIVE) {
            return true;
        }
        return false;
    }

    function isActiveUser()
    {
        if ($this->status === Status::ACTIVE) {
            return true;
        }
        return false;
    }

    function isBlocked()
    {
        if ($this->status === Status::INACTIVE) {
            return true;
        }
        return false;
    }

    function isNotPhoneVerified()
    {
        if ($this->status === Status::UNVERIFIED) {
            return true;
        }
        return false;
    }

    function devices()
    {
        return $this->hasMany(Devices::class);
    }

    function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}
