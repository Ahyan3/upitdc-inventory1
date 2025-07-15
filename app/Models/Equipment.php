<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_name',
        'equipment_name',
        'model_brand',
        'serial_number',
        'department',
        'date_issued',
        'pr_number',
        'remarks',
    ];
    public function issuances()
    {
        return $this->hasMany(Issuance::class);
    }
}   