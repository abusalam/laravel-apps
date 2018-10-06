<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['circle'];

    protected $targetRatio;

    public function getTargetRatio()
    {
        $this->targetRatio = ceil(School::all()->sum('students') / School::all()->sum('teachers'));
        return $this->targetRatio;
    }

    public function ratio()
    {
        return $this->final() > 1
            ? floor($this->students / $this->final())
            : $this->targetRatio;
    }

    public function final()
    {
        return ($this->teachers + $this->movement);
    }

    public function circle()
    {
        return $this->belongsTo('App\Circle');
    }

    public function teachers()
    {
        return $this->hasMany('App\Teacher');
    }

    public function hasMovement()
    {
        return (! $this->movement == 0);
    }
}
