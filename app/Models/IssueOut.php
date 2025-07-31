<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IssueOut extends Model
{
    protected $fillable = [
        'user_id',
        'equipment_id',
        'issued_at',
        'expected_return_at',
        'returned_at',
        'status',
        'notes',
        'return_notes',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}