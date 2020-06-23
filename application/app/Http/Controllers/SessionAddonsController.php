<?php

namespace App\Http\Controllers;

use App\SessionAddon;
use App\SessionPlayer;
use App\AddonsParameter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SessionAddonsController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Session Addon Controller
    |--------------------------------------------------------------------------
    | This controller acts as an auxiliary controller to add or remove addons
    | during booking process.
    |
    */

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $input['package_id'] = Session::get('package_id');
        $addonParameters = AddonsParameter::where('addon_id', $input['addon_id'])->where('package_id', Session::get('package_id'))->first();
        $playerExist = SessionAddon::where('doc_id', $input['doc_id'])->where('addon_id',$input['addon_id'])->first();
        $sessionPlayer = SessionPlayer::where('doc_id',$input['doc_id'])->where('package_id', Session::get('package_id'))->first();
        $min = 0;
        $max = 0;
        $isUser = auth()->user()->doc_id == $input['doc_id'] ? true : false;
        if($isUser) {
            $min = $addonParameters->player_min;
            $max = $addonParameters->player_max;
        }
        if(!$isUser && $sessionPlayer && $sessionPlayer->player_type == 1) {
            $min = $addonParameters->guest_min;
            $max = $addonParameters->guest_max;
        }
        if($addonParameters && $input['cant'] >= $min && $input['cant'] <= $max) {
            if($playerExist) {
                SessionAddon::where('doc_id', $playerExist->doc_id)->where('addon_id', $playerExist->addon_id)->update(['cant' => $input['cant']]);
            } else {
                SessionAddon::create($input);
            }
            return response()->json([ 
                'success' => true,
                'message' => 'Addon Included',
            ]);
        } else {
            return response()->json([ 
                'success' => false,
                'message' => 'Para el Addon: '.$addonParameters->addon->title.', solo se permite seleccionar minimo '.$min.' y maximo '.$max,
            ]);
        }
        
       
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeAddonByParticipant(Request $request)
    {
        SessionAddon::destroy($request['id']);
        return response()->json([ 'success' => true ]);
    }

        /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getTotal()
    {
        $userEmail = auth()->user()->email;
        $package_id = Session::get('package_id');
        $addons = \DB::select("SELECT sum(se.cant) as total, a.title 
        from session_addons se, addons a
        WHERE a.id = se.addon_id
        AND se.session_email = '".$userEmail."'
        AND se.package_id = ".$package_id."
        group by se.addon_id , a.title ");;
        return response()->json([ 'success' => true, 'addons' => $addons ]);
    }


        /**
     * Remove the specified resource by participant from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        SessionAddon::destroy($id);
        return redirect()->route('loadFinalStep');
    }
}
