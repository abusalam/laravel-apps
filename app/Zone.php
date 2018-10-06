<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    public function circles()
    {
        return $this->hasMany('App\Circle');
    }

    public function schools()
    {
        return $this->hasManyThrough('App\School', 'App\Circle');
    }
}
