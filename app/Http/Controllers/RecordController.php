<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tank;
use App\Record;

class RecordController extends Controller
{

    public function __construct(Tank $tank, Record $record)
    {
        $this->tank = $tank;
        $this->record = $record;
    }

    /**
     *  Get daily volume in reserved tanks at all location
     * 
     * @reserved tank i.e underground tank
     * 
     * @underground tank_type_id is 1
     */
    public function alldailyVolume()
    {
        if($alldailyvolume = $this->tank->where('tank_type_id', '=', 1)->get())
        {

            return response()->json([
            'success' => true,
            'data' => $alldailyvolume->toArray()
        ], 200);
    

    }else{

        return response()->json([
            'success' => false,
            'message' => 'Can not get daily volume of reselved tank at this moment'
        ], 501);
    }

    }

    /**
     * Get daily record of tank volume before the addition or substraction
     * and after the addition or subtraction.
     * 
     * @Addition means tranfering to the underground tank
     * @Substraction means transfering from the ungerground tank.
     * 
     * @opening_volume means Volume record before addition or substraction
     * @closing_volume means volume record after  addition or substraction
     * 
     */

    public function volumedaily()
    {
       if($volumedaily = $this->record->all()) 
       {
           
        return response()->json([
            'success' => true,
            'data' => $volumedaily->toArray()
        ], 200);
       }else{
           return response()->json([
               'success' => false,
               'message' => 'Something went wrong!'
           ]);
       }
        
    }
 
}
