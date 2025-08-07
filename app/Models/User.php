<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Automatically create staff record when user is created
    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            // Only create staff if one doesn't already exist
            Staff::firstOrCreate(
                ['email' => $user->email], // Check by email
                [
                    'name' => $user->name,
                    'department' => 'Admin', 
                    'status' => 'Active',
                ]
            );
        });

        // Optional: Update staff when user is updated
        static::updated(function ($user) {
            Staff::where('email', $user->email)->update([
                'name' => $user->name,
            ]);
        });

        // Optional: Delete staff when user is deleted
        static::deleted(function ($user) {
            Staff::where('email', $user->email)->delete();
        });
    }

    // Relationship to staff
    public function staff()
    {
        return $this->hasOne(Staff::class, 'email', 'email');
    }

    /**
     * Get the history logs for the user.
     */
    public function historyLogs()
    {
        return $this->hasMany(HistoryLog::class, 'user_id');
    }
}
