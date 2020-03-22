<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Tank;
use App\Record;
use App\Transfer;

class TransferController extends Controller
{
    public function __construct (Tank $tank, Record $record, Transfer $transfer)
    {
        $this->tank = $tank;
        $this->record = $record;
        $this->transfer = $transfer;
    }

    /**
     * Transfer content from one underground tank of a location 
     * to an underground tank of another location.
     * 
     */

     public function transfer(Request $request)
     {
        $this->validate($request, [
            'from_tank' => 'required',
            'to_tank' => 'required',
            'volume' => 'required',
        ]);
 
        $from_tank = $request->from_tank;
        $to_tank = $request->to_tank;
        $volume = $request->volume;

        //Get Previous Volume of From Tank
        $get_from_tank = $this->tank->find($from_tank);
        $prev_volume_from_tank = $get_from_tank->volume;
            //print $prev_volume_from_tank;

        //Get Previous Volume of To Tank
        $get_to_tank = $this->tank->find($to_tank);
        $prev_volume_to_tank = $get_to_tank->volume;
           // print $prev_volume_to_tank;
       
        //Make new transfer
        $this->transfer->from_tank = $from_tank;
        $this->transfer->to_tank = $to_tank;
        $this->transfer->volume = $volume;

        //Check if thier is an error in making this transfer
        if (!$this->transfer->save()) {
            return response()->json([
                'success' => false,
                'message' => 'Transfer fail'
            ], 400);
        
        } elseif ($this->transfer->save()){
        /**
        * Create new record for this transfer, to take record of previous volume of From tank,
        * and new volume of  From tank. i.e (Tank that you are making transfer from).
        */
            
            $myRecord = [
                [
                    'opening_volume' =>  $prev_volume_from_tank, 
                    'closing_volume' =>  $prev_volume_from_tank - $volume,
                    'tank_id' => $from_tank,
                    'created_at' => Carbon::now()->toDateString(),
                    'updated_at' =>  Carbon::now()->toDateString(),
                ],
        /**
        * Create new record for this transfer, to take record of previous volume of To tank,
        * and new volume of  To tank. i.e (Tank that you are making transfer to).
        */
                [
                    'opening_volume' => $prev_volume_to_tank,
                    'closing_volume' => $prev_volume_to_tank + $volume,
                    'tank_id' => $to_tank,
                    'created_at' => Carbon::now()->toDateString(),
                    'updated_at' =>  Carbon::now()->toDateString(),
                ]
                ];

                $takeRecord = DB::table("records")->insert($myRecord);

            if($takeRecord)
            {
        /**
         *  update tank volume, From Tank 
         *  i.e Volume remain in Tank you are making transfer from.
        */
            $get_from_tank->volume = $prev_volume_from_tank - $volume;
            $get_from_tank->save();
        /**
         *  update tank volume, To Tank 
         *  i.e Volume remain in Tank you are making transfer to.
        */
            $get_to_tank->volume = $prev_volume_to_tank + $volume;
            $get_to_tank->save();

            
            return response()->json([
                'success' => true,
                'message' => 'Transfer made succesful'
            ], 200);

            }else{

            /**
             * Delete transfer that just created Now, if record is not save
             * and tanks table is not updated.
            */
                $lastest_transfer = $this->transfer->latest()->first();
                $lastest_transfer->delete();
            /**
             * Return Message that the transfer is not succesful.
             */
                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong, Pleased try again'
                ], 400);
            }       

        }
    }
    
}    
