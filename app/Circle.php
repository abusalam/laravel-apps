<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Circle extends Model
{
    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['zone'];

    public function zone()
    {
        return $this->belongsTo('App\Zone');
    }

    public function schools()
    {
        return $this->hasMany('App\School');
    }

    public function teachers()
    {
        return $this->hasManyThrough('App\Teacher', 'App\School');
    }
}
