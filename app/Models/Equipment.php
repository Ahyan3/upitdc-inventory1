<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;
    use SoftDeletes;

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
        'date_issued' => 'datetime',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function issuances()
    {
        return $this->hasMany(Issuance::class);
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('staff_name', 'like', "%{$search}%")
                    ->orWhere('equipment_name', 'like', "%{$search}%")
                    ->orWhere('model_brand', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhere('pr_number', 'like', "%{$search}%");
            });
            return $query;
        }
    }

    public function scopeStatus($query, $status)
    {
        return $status !== 'all' ? $query->where('status', $status) : $query;
    }
}
