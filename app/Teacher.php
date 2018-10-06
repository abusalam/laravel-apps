<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use SoftDeletes;
    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['school'];

    public function school()
    {
        return $this->belongsTo('App\School');
    }
}
