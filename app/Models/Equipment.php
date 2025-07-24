<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'staff_name',
        'department_id',
        'equipment_name',
        'model_brand',
        'serial_number',
        'pr_number',
        'date_issued',
        'status',
        'remarks',
        'location',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'status' => 'string',
        'date_issued' => 'date',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function issuances()
    {
        return $this->hasMany(Issuance::class);
    }
}