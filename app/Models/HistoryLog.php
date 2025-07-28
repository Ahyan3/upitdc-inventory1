<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryLog extends Model
{
    use HasFactory;

    protected $casts = [
        'action_date' => 'datetime',
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    protected $fillable = [
        'staff_id',
        'user_id',
        'action',
        'action_date',
        'model_brand',
        'model_id',
        'old_values',
        'new_values',
        'user_id',
        'staff_id',
        'ip_address',
        'user_agent',
        'description'
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where('description', 'like', "%{$search}%")
                ->orWhere('model_brand', 'like', "%{$search}%");
        }
        return $query;
    }

    public function scopeAction($query, $action)
    {
        return $action !== 'all' ? $query->where('action', $action) : $query;
    }
}
