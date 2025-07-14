<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category', 'model_brand', 'serial_number', 'remarks'];

    public function issuances()
    {
        return $this->hasMany(Issuance::class);
    }
}   