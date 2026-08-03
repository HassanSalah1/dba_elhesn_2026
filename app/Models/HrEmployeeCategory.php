<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrEmployeeCategory extends Model
{
    protected $table = 'hr_employee_categories';

    protected $fillable = [
        'row_id',
        'name_ar',
        'name_en',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function employees()
    {
        return $this->hasMany(HrEmployee::class, 'category_id');
    }
}
