<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SchoolUser extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'school_user';

    /**
     * The parent model of the relationship.
     *
     * @var Model
     */
    public $pivotParent = School::class;

    /**
     * The name of the foreign key column.
     *
     * @var string
     */
    protected $foreignKey = 'school_id';

    /**
     * The name of the "other key" column.
     *
     * @var string
     */
    protected $relatedKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'school_id',
        'user_id',
        'is_founder',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_founder' => 'boolean',
    ];

    /* Eloquent Relationships */
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
