<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    // use Illuminate\Database\Eloquent\SoftDeletes; 
    use Illuminate\Database\Eloquent\Model;

    class HistoryLog extends Model
    {
        // use SoftDeletes;
        use HasFactory;
        
        protected $casts = [
            'action_date' => 'datetime',
            'old_values' => 'array',
            'new_values' => 'array',
        ];

        protected $fillable = [
            'staff_name',
            'user_id',
            'equipment_id',
            'equipment_name',
            'status',
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
            return $this->belongsTo(Staff::class, 'staff_id', 'id')->withTrashed();
        }

        public function user()
        {
            return $this->belongsTo(User::class, 'user_id', 'id');
        }

        public function equipment() 
        { 
            return $this->belongsTo(Equipment::class, 'model_id')->withTrashed();
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
