<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Issuance extends Model
{
    use SoftDeletes;
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
        'return_notes',
        'returned_condition'
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'expected_return_at' => 'datetime',
        'date_returned' => 'datetime',
        'returned_at' => 'datetime',
        'status' => 'string',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}