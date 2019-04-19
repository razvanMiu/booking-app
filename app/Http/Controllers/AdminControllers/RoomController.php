<?php

namespace App\Http\Controllers\AdminControllers;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Building;
use App\Floor;
use App\FloorArea;
use App\Room;

class RoomController extends Controller
{
    public function store()
    {
      $this->validate(request(),[
        'RoomName' => 'required|min:3',
        'noOfSeats' => 'required|numeric',
        'coords' => 'required',
        'status' => 'required',
        'shape' => 'required'
      ]);

        $room = Room::create([
        'name' => request('RoomName'),
        'noOfSeats' => request('noOfSeats'),
        'status' => request('status'),
        'floor_id' => request('currentFloor')['_id'],
        'coords' => request('coords'),
        'shape' => request('shape')
      ]);

      return response([
        'room' => $room
      ]);

    }

    public function update()
    {

      $room = Room::where('_id', '=', request('currentRoom')['_id'])->first();
      $this->validate(request(), [
        'RoomName' => 'required|min:3',
        'noOfSeats' => 'required|numeric',
        'coords' => 'required',
        'status' => 'required',
        'shape' => 'required',
      ]);

      $room->name = request('RoomName');
      $room->noOfSeats = request('noOfSeats');
      $room->coords = request('coords');
      $room->status = request('status');
      $room->shape = request('shape');
      $room->save();

      return response()->json([
          'history' => $room
        ]);
    }

    public function delete()
    {
      $roomDeleteConfirmation = Room::where('_id', '=', request('room')['_id'])->delete();
      return ($roomDeleteConfirmation ? 'success' : 'fail' );
    }

}
