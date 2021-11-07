<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'school_id',
        'user_id',
        'email',
        'recipient_name',
        'random_code',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'school_name',
    ];

    /* Accessors */
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

    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
