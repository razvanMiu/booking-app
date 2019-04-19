<?php

namespace App\Http\Controllers\AdminControllers;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Building;
use App\Floor;
use App\FloorArea;
use App\Room;

class FloorController extends Controller
{
    public function store()
    {
      $this->validate(request(), [
        'floorName' => 'required|min:2',
        'noOfRooms' => 'required|numeric'
      ]);
      $floor = Floor::create([
        'name' => request('floorName'),
        'noOfRooms' => request('noOfRooms'),
        'building_id' => request('currentBuilding')['_id'],
      ]);

      return response()->json([
        'floor' => $floor
      ]);

    }

    public function update()
    {
      $floor = Floor::where('_id', '=', request('currentFloor')['_id'])->first();
      $this->validate(request(), [
        'floorName' => 'required|min:2',
        'noOfRooms' => 'required|numeric',
      ]);
      $floor->name = request('floorName');
      $floor->noOfRooms = request('noOfRooms');

      $floor->save();

      return response()->json([
        'history' => $floor
      ]);
    }

    public function delete()
    {
      $floor = Floor::find(request('floor')['_id']);
      $floor->rooms()->delete();
      return $floor->delete() ? 'success' : 'fail';
    }
}
