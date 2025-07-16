<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $fillable = [
        'staff_name',
        'department_id',
        'equipment_name',
        'category',
        'description',
        'serial_number',
        'model_brand',
        'status',
        'date_issued',
        'pr_number',
        'remarks',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function issuances()
    {
        return $this->hasMany(Issuance::class);
    }
}