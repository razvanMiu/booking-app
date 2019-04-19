<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Building extends Eloquent
{
    protected $fillable = [
    	'name', 'noOfFloors', 'coords', 'shape'
    ];

    public function floors()
    {
    	
    	return $this->hasMany(Floor::class);
    }
}
