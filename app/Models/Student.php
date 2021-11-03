<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Student extends Model
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'school_id',
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /* Eloquent Relationships */
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }

    public function followed_teachers()
    {
        return $this->belongsToMany(User::class);
        // return $this->belongsToMany(User::class, 'student_user');
        // return $this->belongsToMany(User::class, 'student_user', 'student_id', 'user_id', 'id', 'id');
    }
}
