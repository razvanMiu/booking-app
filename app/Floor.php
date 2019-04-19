<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Floor extends Eloquent
{
    protected $fillable = [
    	'name', 'noOfRooms', 'building_id'
    ];

    public function building()
    {
    	return $this->belongsTo(Building::class);
    }

    public function rooms()
    {
    	return $this->hasMany(Room::class);
    }   
}
