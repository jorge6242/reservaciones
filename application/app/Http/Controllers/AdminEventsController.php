<?php

namespace App\Http\Controllers;

use App\Event;
use App\Draw;
use App\Category;
use App\Http\Requests\EventsRequest;
use App\Http\Requests\EventsUpdateRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

use Carbon\Carbon;

class AdminEventsController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Admin Events Controller
    |--------------------------------------------------------------------------
    | This controller is responsible for providing events views
    | to admin, to show all views, provide ability to edit and delete
    | specific view.
    |
    */

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $events = Event::query()->with(['category'])->get();
        return view('events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        return view('events.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EventsRequest $request)
    {
        $input = $request->all();
        if($input['event_type'] === "2") {
            $input['drawtime1'] = str_replace("T"," ",$input['drawtime1']);
            $input['drawtime2'] = str_replace("T"," ",$input['drawtime2']);
        }
        $event = Event::create($input);

        if($input['event_type'] === "2") {
            Draw::create([
                'description' => $input['description'],
                'status' => 1,
                'event_id' => $event->id,
            ]);
        }

        //set session message
        Session::flash('event_created', __('backend.event_created'));

        //redirect back to events.index
        return redirect('/events');
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
        $events = Event::find($id);  //findOrFail($id);
		return view('events.view', compact('events'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $event = Event::findOrFail($id);
        $categories = Category::all();
        $event->time1 = Carbon::parse($event->time1)->format('H:i:s');
        $event->time2 = Carbon::parse($event->time2)->format('H:i:s');
        return view('events.edit', compact('event','categories'));
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
        $input = $request->all();
        $request->validate([
			'date' => 'required|date',
			 'time1' => 'required',
			 'time2' => 'required',
			'description' => 'required|string|max:191',
            'is_active' => 'required',
        ]);
        //update data into events table
        if($input['event_type'] === "2") {
            $input['drawtime1'] = str_replace("T"," ",$input['drawtime1']);
            $input['drawtime2'] = str_replace("T"," ",$input['drawtime2']);
        }

        Event::findOrFail($id)->update($input);

        if($input['event_type'] === "2") {
            Draw::where('event_id', $id)->update(['status' => $input['is_active']]);
        }

        //set session message and redirect back events.index
        Session::flash('event_updated', __('backend.event_updated'));
        return redirect('/events');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //find specific event
        $event = Event::findOrFail($id);


        //delete event
        Event::destroy($event->id);

        //set session message and redirect back to events.index
        Session::flash('event_deleted', __('backend.event_deleted'));
        return redirect('/events');
    }
}
