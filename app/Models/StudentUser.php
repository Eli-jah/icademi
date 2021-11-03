<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class StudentUser extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'student_user';

    /**
     * The parent model of the relationship.
     *
     * @var Model
     */
    public $pivotParent = User::class;

    /**
     * The name of the foreign key column.
     *
     * @var string
     */
    protected $foreignKey = 'user_id';

    /**
     * The name of the "other key" column.
     *
     * @var string
     */
    protected $relatedKey = 'student_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'student_id',
    ];

    /* Eloquent Relationships */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }
}
