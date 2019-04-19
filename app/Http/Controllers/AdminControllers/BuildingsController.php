<?php

namespace App\Http\Controllers\AdminControllers;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Building;
use App\Floor;
use App\Room;

class BuildingsController extends Controller
{

    public function fetch()
    {
      
      return response()->json([
        'buildings' => Building::with(['floors', 'floors.rooms'])->get()
      ]);
    }

    public function store()
    {
      $this->validate(Request(),[
        'noOfFloors' => 'required|numeric',
        'buildingName' => 'required|min:2'
      ]);


      $building = Building::create([
            'noOfFloors' => request('noOfFloors'),
            'name' => request('buildingName'),
            ]);
      return response()->json([
        'building' => $building
      ]);
    }

    public function update()
    {
        $building = Building::where('_id', '=', request('currentBuilding')['_id'])->first();
        $this->validate(request(),[
          'buildingName' => 'min:2',
           'noOfFloors' => 'numeric',
        ]);
        $building->name = request('buildingName');
        $building->noOfFloors = request('noOfFloors');  

          // $building->fill(request()->only(['buildingNameUpdate','noOfFloors']));
       
        $building->save();

        return response()->json([
          'history' => $building
        ]);
    }

    public function delete()
    {
      
      $floors = request('building')['floors'];
      foreach($floors as $floor)
      {
        Room::where('floor_id' , '=', $floor['_id'])->delete();
      }
      Floor::where('building_id', '=', request('building')['_id'])->delete();
      Building::find(request('building')['_id'])->delete();
      return 'success';
    }
}
