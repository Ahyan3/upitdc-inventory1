<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use SoftDeletes;
    use HasFactory;
    
    protected $fillable = ['name', 'department', 'email', 'status'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $dates = ['deleted_at'];

    public function issuances()
    {
        return $this->hasMany(Issuance::class); 
    }

    public function user()
    {
        return $this->hasOne(User::class, 'email', 'email');
    }

     public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

}
