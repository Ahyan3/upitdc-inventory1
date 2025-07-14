<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issuance extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'user_id', 
        'staff_id',
        'issued_at',           
        'expected_return_at',
        'returned_at',
        'date_returned',
        'status',
        'notes',               
        'return_notes'
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
    
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}