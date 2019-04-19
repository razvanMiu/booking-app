<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Room extends Eloquent
{
	protected $fillable = [
    	'name', 'noOfSeats', 'status', 'floor_id', 'coords', 'shape'
    ];

    public function floor()
    {
    	return $this->belongsTo(Floor::class);
    }

    public function bookings()
    {
    	return $this->hasMany(Booking::class);
    }
}
