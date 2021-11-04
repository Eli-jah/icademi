<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    const PASSWORD = 'PASSWORD';
    public $type = 'teacher';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'type',
    ];

    /* Accessors */
    public function getTypeAttribute()
    {
        return $this->type;
    }

    /* Eloquent Relationships */
    public function founded_shools()
    {
        return $this->hasMany(School::class, 'user_id', 'id');
    }

    public function joined_schools()
    {
        return $this->belongsToMany(School::class);
        // return $this->belongsToMany(School::class, 'school_user');
        // return $this->belongsToMany(School::class, 'school_user', 'user_id', 'school_id', 'id', 'id');
    }

    public function fans_students()
    {
        // return $this->belongsToMany(Student::class);
        // return $this->belongsToMany(Student::class, 'student_user');
        // return $this->belongsToMany(Student::class, 'student_user', 'user_id', 'student_id', 'id', 'id');
        return $this->belongsToMany(Student::class)->using(StudentUser::class);
    }

    public function sent_invitations()
    {
        return $this->hasMany(Invitation::class, 'user_id', 'id');
    }

    public function received_invitations()
    {
        return $this->hasMany(Invitation::class, 'email', 'email');
    }
}
