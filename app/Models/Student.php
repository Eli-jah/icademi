<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Student extends Authenticatable
{
    use HasApiTokens, Notifiable;

    public $type = 'student';

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

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'type',
        'school_name',
    ];

    /* Accessors */
    public function getTypeAttribute()
    {
        return $this->type;
    }

    public function getSchoolNameAttribute()
    {
        return School::query()
            ->find($this->attributes['school_id'])
            ->name;
    }

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
