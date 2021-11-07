<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'is_approved',
        'is_rejected',
        'founder_name',
        'student_count',
    ];

    /* Accessors */
    public function getIsApprovedAttribute()
    {
        return !empty($this->attributes['approved_at']);
    }

    public function getIsRejectedAttribute()
    {
        return !empty($this->attributes['rejected_at']);
    }

    public function getFounderNameAttribute()
    {
        return User::query()->find($this->attributes['user_id'])->attributes['name'];
    }

    public function getStudentCountAttribute()
    {
        return Student::query()->where('school_id', $this->attributes['id'])->count();
    }

    /* Mutators */
    public function setIsApprovedAttribute($value)
    {
        if ($value) {
            $this->attributes['approved_at'] = Carbon::now()->toDateTimeString();
            $this->attributes['rejected_at'] = null;
        } else {
            $this->attributes['approved_at'] = null;
            $this->attributes['rejected_at'] = Carbon::now()->toDateTimeString();
        }
    }

    public function setIsRejectedAttribute($value)
    {
        if ($value) {
            $this->attributes['approved_at'] = null;
            $this->attributes['rejected_at'] = Carbon::now()->toDateTimeString();
        } else {
            $this->attributes['approved_at'] = Carbon::now()->toDateTimeString();
            $this->attributes['rejected_at'] = null;
        }
    }

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
