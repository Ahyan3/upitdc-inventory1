<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'action', 
        'staff_name', 
        'department', 
        'model', 
        'model_id',
        'old_values',
        'new_values',
        'user_id',
        'staff_id',
        'ip_address',
        'user_agent',
        'description', 
        'action_date'
    ];
}