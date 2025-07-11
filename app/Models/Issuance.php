<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issuance extends Model
{
    use HasFactory;

    protected $fillable = ['staff_name', 'department', 'equipment_id', 'date_issued', 'date_returned', 'pr_number', 'remarks'];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}