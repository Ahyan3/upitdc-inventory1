<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where('name', 'like', "%{$search}%");
        }
        return $query;
    }
    
     /**
     * Get the equipment for the department.
     */
    public function equipment()
    {
        return $this->hasMany(Equipment::class, 'department_id');
    }

}

     $departments = Department::orderBy('name')->get();