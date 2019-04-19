<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Booking extends Eloquent
{
    protected $fillable = [
    	'user_id', 'room_id', 'title', 'start', 'end', 'status', 'description', 'emails', 'participants', 'dow', 'ranges',
    ];
    protected $hidden   = ['user'];
    protected $with     = ['user'];
    protected $appends  = ['username'];

    public function user() 
    {
    	return $this->belongsTo(User::class);
    }

    public function room() 
    {
    	return $this->belongsTo(Room::class);
    }

    public function getUsernameAttribute()
    {
        return $this->user->name;
    }
}
