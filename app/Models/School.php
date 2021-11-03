<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'name',
        'approved_at',
        'rejected_at',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'approved_at',
        'rejected_at',
    ];

    /* Eloquent Relationships */
    public function founder()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function teachers()
    {
        // return $this->belongsToMany(User::class);
        // return $this->belongsToMany(User::class, 'school_user');
        // return $this->belongsToMany(User::class, 'school_user', 'school_id', 'user_id', 'id', 'id');
        return $this->belongsToMany(User::class)->using(SchoolUser::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'school_id', 'id');
    }
}
