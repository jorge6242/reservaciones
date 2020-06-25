<?php

namespace App\Http\Controllers;

use App\Addon;
use App\SessionAddon;
use App\SessionPlayer;
use App\Draw;
use App\BookingTime;
use App\Settings;
use App\DrawRequest;
use App\Booking;
use App\Event;
use App\Category;
use App\Package;
use Carbon\Carbon;
use App\SessionSlot;
use App\PackagesType;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
//use Spatie\GoogleCalendar\Event;

class UserBookingController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | User Booking Controller
    |--------------------------------------------------------------------------
    |
    | This controller loads all frontend booking views and process
    | all requests. Also loads specific user's bookings to view.
    |
    */


    /**
     * get user bookings and load user bookings view
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $bookings = Auth::user()->bookings()->orderBy('created_at', 'ASC')->get();
        return view('customer.bookings.index', compact('bookings'));
    }

    /**
     * Initialize a booking
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function loadBooking()
    {
        $random_pass_string = str_random(10);
        $categories = Category::all();
        return view('welcome', compact('random_pass_string', 'categories'));
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getPackages()
    {
        $category_id = \request('parent');
        $packages = Category::find($category_id)->packages()->get();
        return view('blocks.packages', compact('packages'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getPackagesByType(Request $request)
    {
        $category_id = \request('parent');
        $dates = [
            [ 'date' => Carbon::now() ],
            [ 'date' => Carbon::now()->addDay(1) ],
            [ 'date' => Carbon::now()->addDay(2) ],
        ];
        $category = Category::where('id', $request['id'])->with(['packages'])->get();
        return response()->json([ 'success' => true, 'data' => $category, 'dates' => $dates ]);
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getTimingSlots()
    {
			echo "Leyenda";
       
            echo '<div class="row">';
	   
            if(Session::get('booking_type_id') == 2) {
                echo '<div class="col-md-2">';
                echo '<a class="btn btn-outline-yellow btn-lg btn-block btn-slot disabled"> DISPONIBLE</a>';
                echo '</div>';
                
                echo '<div class="col-md-2">';
                echo '<a class="btn   btn-lg btn-block  btn-slot btn-warning disabled">SELECCION</a>';
                echo '</div>';
            } else {
                echo '<div class="col-md-2">';
                echo '<a class="btn btn-outline-dark btn-lg btn-block btn-slot disabled"> DISPONIBLE</a>';
                echo '</div>';
                
                echo '<div class="col-md-2">';
                echo '<a class="btn   btn-lg btn-block  btn-slot btn-warning disabled">EVENTO</a>';
                echo '</div>';
                
                echo '<div class="col-md-2">';
                echo '<a class="btn   btn-lg btn-block  btn-slot btn-secondary disabled"><font color="FFFFFF"> EXPIRADO</font></a>';
                echo '</div>';
                
                echo '<div class="col-md-2">';
                echo '<a class="btn   btn-lg btn-block  btn-slot btn-success disabled"><font color="FFFFFF"> RESERVADO</font></a>';
                echo '</div>';

                
                echo '<div class="col-md-2">';
                echo '<a class="btn   btn-lg btn-block  btn-slot btn-danger disabled"><font color="FFFFFF"> EN PROCESO </font></a>';
                echo '</div>';
            }

			echo '</div>';

        Session::put('selectedPackageType',null);
	
		date_default_timezone_set(env('LOCAL_TIMEZONE','America/Caracas'));
				
		//$now = date('m/d/Y h:i:s a', time());
		//DB::table('session_slots')->where('expiration_date','<', time())->delete();
		//DB::table('session_slots')->where('expiration_date','<', GETDATE())->delete();
       
		
	   
			//delete all session slots expired
            require 'config.inc';
       
            $connectionInfo = array( 
                "Database"=> $database,
                "UID"=> $username, 
                "PWD"=> $password
                );
            $connection = sqlsrv_connect($servername, $connectionInfo);
            
			$queryExpiredSlots = "DELETE FROM session_slots WHERE expiration_date<GETDATE()";

			// Check connection 
			if (!$connection) 
			{ 
				$has_errors = 1;	
				echo $err_message =  $err_message . "<br>Database connection failed"; 
				exit(); //die();
			}
			$resultqueryBookingPlayerCount = sqlsrv_query($connection, $queryExpiredSlots); 
		
        $bookingType =  Session::get('booking_type_id');
		
        //get selected event date
        $event_date = \request('event_date');

        //get selected package_id
        $selected_package_id = Session::get('package_id');

        $drawId = Session::get('draw_id');
		
        echo Package::find($selected_package_id)->title; 
        
        //get selected category_id
        $selected_category_id = Package::find($selected_package_id)->category->id;

        //get day name to select slot timings
        $timestamp_for_event = strtotime($event_date);
        $today_number = date('N', $timestamp_for_event);
        $settings = Settings::query()->first();	
               
		//use booking schedule per package or global
		if($settings->bookingTime_perpackage)
		//if (1==1)	
		{
			
			//require 'config.inc';
			
			 $servername = $_ENV['DB_HOST'];
			 $username=  $_ENV['DB_USERNAME'];
			 $password = $_ENV['DB_PASSWORD'];
             $database = $_ENV['DB_DATABASE'];
             
             $connectionInfo = array( 
                "Database"=> $database,
                "UID"=> $username, 
                "PWD"=> $password
                );
              
			$conn = sqlsrv_connect($servername, $connectionInfo);
			// Check connection
			if (!$conn) {
				die("Connection failed: " . sqlsrv_errors());
			}
			
			
            //validar horario para confirmacion con horario de uso de app
            $timePackage = $bookingType == 1 ? 'booking' : 'draw';

			$sql = "select opening_time, closing_time from ".$timePackage."_times_packages WHERE package_id=" . $selected_package_id . " AND  number=" . $today_number ;
            
            if($bookingType == 2) {
                $sql = "SELECT e.time1 as opening_time, e.time2 as closing_time  from events e , draws d where e.id=d.event_id and d.id = ".$drawId."";
            }

            //echo $sql;
			
			$result1 = sqlsrv_query($conn, $sql);
			if( $result1 === false) {
				die( print_r( sqlsrv_errors(), true) );
			}

			while( $row = sqlsrv_fetch_array( $result1, SQLSRV_FETCH_ASSOC) ) {

				if($bookingType == 2) {

					  $hour_start = date_format($row["opening_time"], 'h:i A');
					  $hour_end = date_format($row["closing_time"], 'h:i A');
				}
				else
				{
					  $hour_start = $row["opening_time"];					
					  $hour_end = $row["closing_time"];				
				}
			}

			sqlsrv_free_stmt( $result1);
			sqlsrv_close($conn);		
		}
		else
		{
			$booking_time = BookingTime::findOrFail($today_number);
			//decide starting and ending hours for selected date
			$hour_start = $booking_time->opening_time;
			$hour_end = $booking_time->closing_time;
			
		}
		/*
		$hour_start ='06:00 AM';
		$hour_end='08:00 PM';
		*/
		echo " @ " .  $hour_start . " - " . 	$hour_end . "<br>";
		
        //decide what will be the duration of each slot
        if($settings->slots_with_package_duration)
        {
            //use package duration as slot duration
            $package = Package::find($selected_package_id);
            $slot_duration = $package->duration * 60;
        }
        else
        {
            //use regular slot duration
            $slot_duration = $settings->slot_duration * 60;
        }

        //decide how many slots will be generated
        if(strtotime($hour_start)>strtotime($hour_end))
        {
            $hours = round((strtotime($hour_start) - strtotime($hour_end))/$slot_duration, 1);
        }
        else if(strtotime($hour_end)>strtotime($hour_start))
        {
            $hours = round((strtotime($hour_end) - strtotime($hour_start))/$slot_duration, 1);
        }
        else if(strtotime($hour_start)==strtotime($hour_end))
        {
            $hours = 24;
        }

        //get all bookings to block some already booked slots
        $bookings = Booking::all()->where('status', '!=',__('backend.cancelled'));

        //get all events to block some slots  - LA
		$events = Event::all()->where('is_active', '=',1);
		
		//get all session slots to block some slots  - LA
		$sessionSlots =  DB::table('session_slots')->where('booking_date','=',$event_date)->get();
		
		//$sessionSlots =  DB::table('session_slots')->where('booking_date','=',$event_date)->where('expiration_date','>=',time())->get();		
        $setting = Settings::query()->first();
		//reset the counter to disable slots
        $count_next_disabled = 0;
        //start loop for slot generation
        for($i = 0; $i < $hours; $i++)
        {
            // minutes to add in lap of each slot
            $minutes_to_add = $slot_duration * $i;

            // increment each slot by minutes_to_add
            $timeslot = date('H:i:s', strtotime($hour_start)+$minutes_to_add);
            
            //clock format choice
            if($settings->clock_format == 12)
            {
                $list_slot[$i]['slot'] = date('h:i A', strtotime($timeslot));
            }
            else
            {
                $list_slot[$i]['slot'] = date('H:i', strtotime($timeslot));
            }

            //if counter for disabling slots is not zero, block the slot as already booked
            if($count_next_disabled!=0)
            {
                $list_slot[$i]['is_available'] = false;
                $count_next_disabled--;
            }
            else
            {
                $list_slot[$i]['is_available'] = true;
            }


            //checking slot availability
            foreach ($bookings as $booking)
            {
                if(strtotime($booking->booking_date)==strtotime($event_date) && strtotime($booking->booking_time)==strtotime($timeslot))
                {
                    //put multiple booking logic

                    //one booking at one slot
                    
                    if($settings->slots_method == 1)
                    {
                        //prevent multiple bookings at same time
                        $list_slot[$i]['is_available'] = false;
                        $package_booking = Package::find($booking->package_id);
                        $package = Package::find($selected_package_id);
                        
                        if($setting->slots_with_package_duration)
                        {
                            $count_next_disabled = ($package_booking->duration / $package->duration) - 1;
                        }
                        else
                        {
                            $count_next_disabled = ($package_booking->duration / $setting->slot_duration) - 1;

                        }
                    }

                    //multiple with different package
                    
                    if($settings->slots_method == 3)
                    {
                        if($selected_package_id == $booking->package->id)
                        {
                            //prevent multiple bookings at same time
                            $list_slot[$i]['is_available'] = false;
                            $package_booking = Package::find($booking->package_id);
                            $package = Package::find($selected_package_id);
                            if($settings->slots_with_package_duration)
                            {
                                $count_next_disabled = ($package_booking->duration / $package->duration) - 1;
                            }
                            else
                            {
                                $count_next_disabled = ($package_booking->duration / $settings->slot_duration) - 1;

                            }
                        }
                    }

                    //multiple with different category

                    if($setting->slots_method == 4)
                    {
                        if($selected_category_id == $booking->package->category->id)
                        {
                            //prevent multiple bookings at same time
                            $list_slot[$i]['is_available'] = false;
                            $package_booking = Package::find($booking->package_id);
                            $package = Package::find($selected_package_id);
                            if($settings->slots_with_package_duration)
                            {
                                $count_next_disabled = ($package_booking->duration / $package->duration) - 1;
                            }
                            else
                            {
                                $count_next_disabled = ($package_booking->duration / $settings->slot_duration) - 1;

                            }
                            break;
                        }
                    }
                }
            }

			if ($bookingType!=2)
			{
				//$list_slot[$i]['is_blocked'] = false;

				//check slot availability against Events -- LA
				foreach ($events as $event)
				{
					if(strtotime($event->date)==strtotime($event_date) && strtotime($event->time1)<=strtotime($timeslot) && strtotime($event->time2)>=strtotime($timeslot)   )
					{
							$list_slot[$i]['is_available'] = false;
							$list_slot[$i]['is_blocked'] = true;
							$list_slot[$i]['description'] = $event->description;
					}
					else
					{
							$list_slot[$i]['is_blocked'] = false;
					}
				}


				//check slot availability against SessionSlots -- LA
				foreach ($sessionSlots as $sessionSlot)
				{
					if (strtotime($sessionSlot->booking_date)==strtotime($event_date) && strtotime($sessionSlot->booking_time)==strtotime($timeslot))
					{
							$list_slot[$i]['is_available'] = false;
							$list_slot[$i]['is_blocked'] = true;
							$list_slot[$i]['description'] = "PROGRESS";
					}
					else
					{
							//$list_slot[$i]['is_blocked'] = false;
					}
				}

				//check if slot is expired
                
				if($settings->clock_format == 12)
				{
					$current_time = date('h:i A');
				}
				else
				{
					$current_time = date('H:i');
				}

				$today_date = date('d-m-Y');
				//$today_date = date('Y-m-d');
				
				if ((strtotime( $list_slot[$i]['slot']) <= strtotime($current_time)) && ($event_date== $today_date ))
				{		
					$list_slot[$i]['is_available'] = false;
					$list_slot[$i]['is_blocked'] = false;
					$list_slot[$i]['is_expired'] = true;
				}		
			}
        }

        return view('blocks.slots', compact('list_slot', 'hours'));
    }


    public function getUpdateSlots()
    {
        $event_date = \request('event_date');
        $booking_id = \request('booking');
        $booking = Booking::find($booking_id);
        $selected_package_id = $booking->package_id;
        $selected_category_id = Package::find($selected_package_id)->category->id;

		
        $timestamp_for_event = strtotime($event_date);
        $today_number = date('N', $timestamp_for_event);

        //get related booking time for day number
        $booking_time = BookingTime::findOrFail($today_number);

        $hour_start = $booking_time->opening_time;
        $hour_end = $booking_time->closing_time;

        //decide what will be the duration of each slot
        if(config('settings.slots_with_package_duration'))
        {
            //use package duration as slot duration
            $package = Package::find($selected_package_id);
            $slot_duration = $package->duration * 60;
        }
        else
        {
            //use regular slot duration
            $slot_duration = config('settings.slot_duration') * 60;
        }

        if(strtotime($hour_start)>strtotime($hour_end))
        {
            $hours = round((strtotime($hour_start) - strtotime($hour_end))/$slot_duration, 1);
        }
        else if(strtotime($hour_end)>strtotime($hour_start))
        {
            $hours = round((strtotime($hour_end) - strtotime($hour_start))/$slot_duration, 1);
        }
        else if(strtotime($hour_start)==strtotime($hour_end))
        {
            $hours = 24;
        }

        $bookings = Booking::all()->where('status', '!=',__('backend.cancelled'));

        $count_next_disabled = 0;

        for($i = 0; $i < $hours; $i++)
        {
            // increment by 1 hour
            $minutes_to_add = $slot_duration * $i;

            // add 1 hour to each next slot
            $timeslot = date('H:i:s', strtotime($hour_start)+$minutes_to_add);

            //clock format choice
            if(config('settings.clock_format')==12)
            {
                $list_slot[$i]['slot'] = date('h:i A', strtotime($timeslot));
            }
            else
            {
                $list_slot[$i]['slot'] = date('H:i', strtotime($timeslot));
            }

            if($count_next_disabled!=0)
            {
                $list_slot[$i]['is_available'] = false;
                $count_next_disabled--;
            }
            else
            {
                $list_slot[$i]['is_available'] = true;
            }

            //checking slot availability
            //checking slot availability
            foreach ($bookings as $booking)
            {
                if(strtotime($booking->booking_date)==strtotime($event_date) && strtotime($booking->booking_time)==strtotime($timeslot))
                {
                    //put multiple booking logic

                    //one booking at one slot

                    if(config('settings.slots_method') == 1)
                    {
                        //prevent multiple bookings at same time
                        $list_slot[$i]['is_available'] = false;
                        $package_booking = Package::find($booking->package_id);
                        $package = Package::find($selected_package_id);
                        if(config('settings.slots_with_package_duration'))
                        {
                            $count_next_disabled = ($package_booking->duration / $package->duration) - 1;
                        }
                        else
                        {
                            $count_next_disabled = ($package_booking->duration / config('settings.slot_duration')) - 1;

                        }
                    }

                    //multiple with different package

                    if(config('settings.slots_method') == 3)
                    {
                        if($selected_package_id == $booking->package->id)
                        {
                            //prevent multiple bookings at same time
                            $list_slot[$i]['is_available'] = false;
                            $package_booking = Package::find($booking->package_id);
                            $package = Package::find($selected_package_id);
                            if(config('settings.slots_with_package_duration'))
                            {
                                $count_next_disabled = ($package_booking->duration / $package->duration) - 1;
                            }
                            else
                            {
                                $count_next_disabled = ($package_booking->duration / config('settings.slot_duration')) - 1;

                            }
                        }
                    }

                    //multiple with different category

                    if(config('settings.slots_method') == 4)
                    {
                        if($selected_category_id == $booking->package->category->id)
                        {
                            //prevent multiple bookings at same time
                            $list_slot[$i]['is_available'] = false;
                            $package_booking = Package::find($booking->package_id);
                            $package = Package::find($selected_package_id);
                            if(config('settings.slots_with_package_duration'))
                            {
                                $count_next_disabled = ($package_booking->duration / $package->duration) - 1;
                            }
                            else
                            {
                                $count_next_disabled = ($package_booking->duration / config('settings.slot_duration')) - 1;

                            }
                            break;
                        }
                    }
                }
            }

        }
        return view('blocks.backendSlots', compact('list_slot', 'hours'));

    }


    public function update_booking(Request $request, $id)
    {
        $booking = Booking::find($id);
        if($booking->user->id == Auth::user()->id)
        {
            $input = $request->all();

            //update booking

            $booking->update([
                'booking_date' => $input['event_date_bk'],
                'booking_time' => $input['booking_slot']
            ]);

            //if sync is enabled and booking have calender event_id

            if(config('settings.sync_events_to_calendar') && config('settings.google_calendar_id') && $booking->google_calendar_event_id != NULL) {

                //create new timestamp
                $time_string = $input['event_date_bk'] . " " . $input['booking_slot'];
                $start_instance = Carbon::createFromTimestamp(strtotime($time_string), env('LOCAL_TIMEZONE'));
                $end_instance = Carbon::createFromTimestamp(strtotime($time_string), env('LOCAL_TIMEZONE'))->addMinutes($booking->package->duration);

                try{
                    //update google calendar event
                    $event = Event::find($booking->google_calendar_event_id);
                    $event->startDateTime = $start_instance;
                    $event->endDateTime = $end_instance;
                    $event->save();
                } catch(\Exception $ex) {
                    //do nothing
                }

            }

        }

        return redirect()->route('customerBookings');

    }

    /**
     * @param BookingStep1 $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function postStep1(Request $request)
    {
        $input = $request->all();
        $selectedCategoryDraw = Package::find($input['package_id'])->category->draw;
        $categoryType = Package::find($input['package_id'])->category->category_type;
        $request->session()->put('package_id', $input['package_id']);
        $request->session()->put('selectedCategoryDraw', $selectedCategoryDraw);
        $request->session()->put('categoryType', $categoryType);
        
        $request->session()->put('booking_type_id', '');
        if($selectedCategoryDraw == 0) {
            $request->session()->put('booking_type_id', '1');
        }

		$request->session()->put('countdown', $input['countdown']);	
        
        //return redirect()->route('getBookingType');
         return redirect()->route('loadStep2');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getBookingType()
    {    
        return view('select-booking-type');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getDraws()
    {  
    //     $data = DB::select(" SELECT *
    //     FROM events e
    //     WHERE e.event_type = 2 AND e.drawtime1 <= ".$date." AND e.drawtime2 >= ".$date." ");
    // dd($data);
        //$date = Carbon::GETDATE()->format('Y-m-d H:i:s');
		//$date = Carbon::now()->format('Y-m-d H:i:s');
		$date = Carbon::now()->toDateTimeString();
        $package = Package::find(Session::get('package_id'));
        DB::table('session_slots')->where('session_email','=', Auth::user()->email)->where('booking_type', 2)->delete();

        $events = DB::select("SELECT * from events
            WHERE is_active  = 1
            AND event_type = 2
            AND category_id = ".$package->category_id."
            AND drawtime1 <= convert(varchar, getdate(), 120)
            AND drawtime2 >= convert(varchar, getdate(), 120)
        ");

        $draws = [];
        if(count($events)) {
            $draws = array();
            foreach ($events as $key => $value) {
                $newDraW = Draw::where('status',1)->where('event_id',$value->id)->first();
                if($newDraW) {
                    array_push($draws, $newDraW);
                }
            }
        }

        $data = [ 'draws' => $draws ];
        return view('blocks.draws', $data);
    }

    public function getDrawTimes() {
        return view('blocks.drawtimes');
    }

        /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getDateDraw(Request $request)
    {  
        DB::table('session_slots')->where('session_email','=', Auth::user()->email)->where('booking_type', 2)->delete();
        $data = Draw::where('id', $request['id'])->with(['event'])->first();
        $date = $data->event()->first()->date;
        $request->session()->put('draw_id', $request['id']);
        return response()->json([ 'date' => $date ]);
    }

    public function checkHour($hour){
        $list = Session::get('hours-by-date');
        if($list) {
            foreach ($list as $key => $value) {
             if($value === $hour) {
                 return true;
             } 
            }
        }
        return false;
    }

            /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getHoursByDate(Request $request)
    {  
        $array = array();
        $current = Session::get('hours-by-date');
        if($current) {
            if(count($current) < 3){
                $valid = $this->checkHour($request['hour']);
                if(!$valid) {
                    array_push($current, $request['hour'] );
                }
            }
            $array = $current;
        } else {
            if(count($array) < 3) {
                if(!$valid) {
                    array_push($array, $request['hour']);
                }      
            }
        }
        $request->session()->put('hours-by-date',  $array);
        return response()->json([ 'hours' => $array ]);
    }

    public function removeHoursByDate(Request $request) {
        $currentHours = Session::get('hours-by-date');
        $array = array();
        foreach ($currentHours as $key => $value) {
            if($value === $request['hour']) {
                unset($currentHours[$key]);
            } else {
                array_push($array, $value);
            }
        }
        $request->session()->put('hours-by-date',   $array);
        return response()->json([ 'hours' =>  $array ]);
    }

    public function checkUserDraw() {
        $drawUser = DrawRequest::where('user_id', Auth::user()->id)->where('draw_id', Session::get('draw_id'))->first();
        if($drawUser) {
        return response()->json([ 'check' =>  true ]);
        }
        return response()->json([ 'check' =>  false ]);
    }

            /**
     * @param BookingStep1 $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function setStep2(Request $request)
    {
        $input = $request->all();
		$request->session()->put('booking_type_id', $input['booking_type_id']);
        return redirect()->route('loadStep2');
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function loadStepPlayer()
    {
        //select all players of session
        $user = Auth::user();
        $exist = SessionPlayer::where('doc_id', $user->doc_id)->where('player_type', -1)->first();

        if(!$exist) {
            SessionPlayer::create([
                'doc_id' => $user->doc_id,
                'player_type' => -1,
                'session_email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'package_id' => Session::get('package_id'),
            ]);
        }
        $session_players = DB::table('session_players')->where('session_email','=',Auth::user()->email)->where('player_type','!=',-1)->get();
         //load step Player
		return view('select-booking-players', compact('session_players'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postStepPlayer(Request $request)
    {
        $input = $request->all();
		$request->session()->put('countdown', $input['countdown']);	
		
        //store form input into session and load next step
        // $request->session()->put('doc_id', $input['doc_id']);
		// $request->session()->put('player_type', $input['player_type']);
		// $request->session()->put('session_email', $input['session_email']);


		//validate min Players
		
		/*
		$minPlayers = config('settings.bookingUser_minPlayers');
		$maxPlayers = config('settings.bookingUser_maxPlayers');
		
		$session_players = count(DB::table('session_players')->where('session_email','=', Auth::user()->email)->get()) + 1;
	
		echo $session_players . " vs " . $minPlayers  . " hasta " . $maxPlayers   ;
		//return redirect('/select-booking-players');

		// $session_players = DB::table('session_players')->where('session_email','=',Auth::user()->email)->get();

		//return view('select-booking-players', compact('session_players'));
		
		// die();

		if (($session_players >= $minPlayers) && ($session_players <= $maxPlayers))
		{
			//OK
			//load step 2
			return redirect('/select-booking-time');
			//return view('select-booking-time', compact('disable_days_string'));
		}
		else
		{
			if (($session_players< $minPlayers))
				echo "<center>El mínimo de participantes debe ser " . $minPlayers . "</center><br>"; 
			
			if (($session_players > $maxPlayers))
				echo "<center>El máximo de participantes debe ser " . $maxPlayers . "</center>"; 
			
			//select all players of session
			//$session_players = DB::table('session_players')->where('session_email','=',Auth::user()->email)->get();

			 //load step Player
			//return view('select-booking-players', compact('session_players'));
			return redirect('/select-booking-players');
		}
*/
        return redirect('/select-booking-time');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function loadStep2()
    {
        //generating a string for off days

        $off_days = DB::table('booking_times')
            ->where('is_off_day', '=', '1')
            ->get();

        $daynum = array();

        foreach ($off_days as $off_day)
        {
            if($off_day->id != 7)
            {
                $daynum[] = $off_day->id;
            }
            else
            {
                $daynum[] = $off_day->id - 7;
            }
        }

        $disable_days_string = implode(",", $daynum);

		//return view('select-booking-time', compact('disable_days_string'));

		//validate min Players
		
		$minPlayers = config('settings.bookingUser_minPlayers');
		$maxPlayers = config('settings.bookingUser_maxPlayers');
		
		$session_players = count(DB::table('session_players')->where('session_email','=', Auth::user()->email)->get()) + 1;
		//echo $session_players . " vs " . $minPlayers;
		
		//if (($session_players >= $minPlayers) && ($session_players <= $maxPlayers))
		// if (($session_players < $minPlayers) || ($session_players > $maxPlayers))
		// {
			// if (($session_players< $minPlayers))
			// echo "<font color='#ff0000'><center>El mínimo de participantes debe ser " . $minPlayers . "</center></font><br>"; 
			
			// if (($session_players > $maxPlayers))
			
			// echo "<font color='#ff0000'><center>El máximo de participantes debe ser " . $maxPlayers . "</center></font>"; 
			
			// //select all players of session
			// $session_players = DB::table('session_players')->where('session_email','=',Auth::user()->email)->get();

			 // //load step Player
			// return view('select-booking-players', compact('session_players'));
		// }
		// else
		{
			//OK
			//load step 2
			return view('select-booking-time', compact('disable_days_string'));
		}

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postStep2(Request $request)
    {
        $input = $request->all();
        $setting = Settings::query()->first();
        //store form input into session and load next step
        $request->session()->put('address', $input['address']);
        $request->session()->put('event_date', $input['custom-event_date']);
        $request->session()->put('instructions', $input['instructions']);
        $request->session()->put('booking_slot', $input['booking_slot']);
		$request->session()->put('countdown', $input['countdown']);	

		//date_default_timezone_set(env('LOCAL_TIMEZONE','America/Caracas'));

		//check if logged
		//if($user = Auth::user())
		//if (!Auth::check())
			
		//se hacen las validaciones de reservas para el caso de reservas directas
		if(Session::get('booking_type_id') === "1") 
		{	
	
	
			//$todayBookings = date("d-m-Y");
			$dateBookings = $input['custom-event_date']; 

			//obviar las cancelaciones
			$bookings_count = count(DB::table('bookings')->where('booking_date','=', $dateBookings)->where('user_id','=', Auth::user()->id)->where('status','!=', __('backend.cancelled'))->get());
            //$bookings_today = Auth::user()->bookings()->where('booking_date','=',($todayBookings));
			$bookings_perday =  $setting->bookingUserPerDay;
			
			//echo $bookings_perday;
			//die();
			
			//echo Auth::user()->id . " : " . $bookings_count . " vs " . $bookings_perday . " @ " . $dateBookings;
			//exit();

			//validate as participant confirmed
			// $bookingsparticipant_count = count(DB::table('booking_players')->where('doc_id','=', Auth::user()->doc_id)->where('confirmed','=', 1)->booking->where('booking_date','=', $dateBookings)->get());
			
			$queryBookingPlayerCount = "select count(*) as cant from booking_players p join bookings b on b.id = p.booking_id where p.doc_id = '" . Auth::user()->doc_id . "' and p.confirmed=1 and b.booking_date='" . $dateBookings . "' and b.status!='" . __('backend.cancelled') . "'";	



			// $row = DB::table('booking_players')
                // ->select(DB::raw('booking_players.id as ID'))
                // ->join('bookings', 'bookings.id', '=', 'booking_players.booking_id')
                // ->where('booking_players.doc_id', '=', "'" . Auth::user()->doc_id . "'")
				// ->where('booking_players.confirmed', '=', 1)
				// ->where('bookings.booking_date', '=', "'" . $dateBookings . "'")
				// ->where('bookings.status', '!=', 'Cancelado')
                // ->get();
				
				//__('backend.cancelled')
				
			//$bookingsparticipant = count($row);
			// $row = DB::table('log_user_login')
                // ->select(DB::raw('log_user_login.Password as LogPassword'), 'user.*')
                // ->join('user', 'log_user_login.Username', '=', 'user.Username')
                // ->where('log_user_login.LoginSession', '!=', '')
                // ->groupBy('user.ID')
                // ->get();
				
			//echo $bookingsparticipant = count($row);

	
			//echo $queryBookingPlayerCount;
			require 'config.inc';
			
            $connectionInfo = array( 
                "Database"=> $database,
                "UID"=> $username, 
                "PWD"=> $password
                );
			
			$connection = sqlsrv_connect($servername, $connectionInfo); 

			// Check connection 
			if (!$connection) 
			{ 
				$has_errors = 1;	
				$err_message =  $err_message . "<br>Database connection failed."; 
				exit(); //die();
			}

			$resultqueryBookingPlayerCount = sqlsrv_query($connection, $queryBookingPlayerCount); 
			 

			if( $resultqueryBookingPlayerCount === false) {
			    die( print_r( sqlsrv_errors(), true) );
			}	

			$bookingsparticipant_count= 0;
			while( $row = sqlsrv_fetch_array( $resultqueryBookingPlayerCount, SQLSRV_FETCH_ASSOC) ) {
				 $bookingsparticipant_count = $row['cant'];
			}

			sqlsrv_free_stmt( $resultqueryBookingPlayerCount);			
			

/*
			if ($resultqueryBookingPlayerCount) 
			{ 
				$rowGroupCount = sqlsrv_num_rows($resultqueryBookingPlayerCount); 
			    //printf("Number of row in the table : " . $rowGroupCount); 
				if ($rowGroupCount>0) 
				{
					while($row = sqlsrv_fetch_array($resultqueryBookingPlayerCount)){
						$bookingsparticipant_count = $row['cant'];
					}
				}
				else
				{
					
				}
			}			
*/
			 //echo Auth::user()->id . " : $bookingsparticipant_count " . "+" . $bookings_count . " vs " . $bookings_perday . " @ " . $dateBookings;
			 //die();
			
			//check total bookings or participate per day
			if ($bookings_count + $bookingsparticipant_count < $bookings_perday)
			{
				//echo "OK";	
				//echo $todayBookings;
			}
			else
			{
				
				echo "<center>MAX RESERVACIONES POR DIA EXCEDIDO</center>";	
				//return view('custom.restricted');		
				
				echo '<script>';
				echo '			window.location.href = `custom/RestrictedUserBooking.php?type=custom&customText=MAX RESERVACIONES POR DIA EXCEDIDO`;';
			//	echo '			window.location.href = `{{ url('login') }}`;';		
				echo '			</script>';
				exit;	
			}
			//			@if(count($session_players))
			//				@foreach($session_players as $player)				
		}

		// date_default_timezone_set('America/Caracas');
		// $packageEndDate = date('Y-m-d H:i:s', strtotime('+' . config('settings.bookingTimeout') . ' minute'));

        //create session_slot
        $tennisSlot = json_decode($input['tennis_slot']);
        $package_id = Session::get('package_id');
        $packageType = $input['package-type'] ? $input['package-type'] : null; // Validar package_type 
        $bookingTime2 = null; 

        if($tennisSlot !== null) {
            $input['booking_slot'] = $tennisSlot[0];
            $bookingTime2 = end($tennisSlot);
            Session::put('tennisSlots',$tennisSlot);
            Session::put('booking_slot',$input['booking_slot']);
        }
        Session::put('packageType',$packageType);
        Session::put('booking_time2',$bookingTime2);
        
        
        if(Session::get('booking_type_id') === "1") {
            SessionSlot::create([
                'session_email' =>  Auth::user()->email ,
                'booking_date' =>  $input['custom-event_date'] ,
                'booking_time' =>  $input['booking_slot'] ,
                'booking_type' =>  Session::get('booking_type_id'),
                'package_id' =>  $package_id,
                'package_type_id' =>  $packageType,
                'booking_time2' =>  $bookingTime2,
            ]);
        }
        if(Session::get('booking_type_id') === "2") {
            $hourSlots = $input['draw_booking_slot'];
            $hourSlots = json_decode($hourSlots);
            foreach ($hourSlots as $key => $value) {
                SessionSlot::create([
                    'session_email' =>  Auth::user()->email ,
                    'booking_date' =>  $input['custom-event_date'] ,
                    'booking_time' =>  $value ,
                    'booking_type' =>  Session::get('booking_type_id'),
                    'package_type_id' =>  $packageType,
                    'booking_time2' =>  $bookingTime2,
                ]);
            }
        }

		//update expiration date
	
		$param =  $setting->bookingTimeout;

		// $sql2="UPDATE session_slots SET expiration_date=TIMESTAMPADD(MINUTE, " . $param  .  " , created_at) WHERE session_email='" .  Auth::user()->email  .  "' and booking_date='" .  Session::get('event_date')  . "' AND booking_time='" .  Session::get('booking_slot')  . "'"  ;
		
		$sql2="UPDATE session_slots SET created_at=GETDATE(), updated_at=GETDATE(),  expiration_date=    dateadd(MINUTE, " . $param  .  " , GETDATE()) WHERE session_email='" .  Auth::user()->email  .  "' and booking_date='" .  $input['custom-event_date']  . "' AND booking_time='" .  $input['booking_slot']  . "'"  ;
		
		
		
		$result = sqlsrv_query($connection, $sql2);


        return redirect('/select-booking-players');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postStep3(Request $request)
    {
		$request->session()->put('countdown', $input['countdown']);
        return redirect('/finalize-booking');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function loadStep3()
    {
        $settings = Settings::query()->first();
		$minPlayers = $settings->bookingUser_minPlayers;
        $maxPlayers = $settings->bookingUser_maxPlayers;
		
		$session_players = count(DB::table('session_players')->where('session_email','=', Auth::user()->email)->get()) + 1;
	
		
		//echo "<br>" . $minPlayers . " - " . $session_players . " - "   . $maxPlayers;
		
		//if (($session_players >= $minPlayers) && ($session_players <= $maxPlayers))
		if (($session_players < $minPlayers) || ($session_players > $maxPlayers))
		{
			if (($session_players< $minPlayers))
			echo "<font color='#ff0000'><center>El mínimo de participantes debe ser " . $minPlayers . "</center></font><br>"; 
			
			if (($session_players > $maxPlayers))
			
			echo "<font color='#ff0000'><center>El máximo de participantes debe ser " . $maxPlayers . "</center></font>"; 
			
			//select all players of session
			$session_players = DB::table('session_players')->where('session_email','=',Auth::user()->email)->get();
			 //load step Player
			return view('select-booking-players', compact('session_players'));
		}		
		
        $package_id = Session::get('package_id');
        $package = Package::find($package_id);
        $category_id = $package->category_id;

        //select all addons of category
        $addons = Category::find($category_id)->addons()->get();
        $session_addons = DB::table('session_addons')->where('session_email','=',Auth::user()->email)->get();
        $selectedPlayer = Session::get('selectedPlayer');
        foreach ($addons as $key => $value) {
            $sessionAddon = SessionAddon::where('addon_id', $value->id)->where('doc_id', $selectedPlayer)->where('package_id',$package_id)->where('session_email',Auth::user()->email)->first();
            if($sessionAddon){
                $addons[$key]->cant = $sessionAddon->cant;
                $addons[$key]->buttonText = __('app.update_service_btn');
                $addons[$key]->showDelete = 'show';
            } else {
                $addons[$key]->cant = 1;
                $addons[$key]->buttonText = __('app.add_service_btn');
                $addons[$key]->showDelete = 'hidde';
            }
        }
        return view('select-extra-services', compact('addons', 'session_addons','selectedPlayer'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function loadFinalStep()
    {
        $event_address = str_replace(' ', '+', Session::get('address'));
        $category = Package::find(Session::get('package_id'))->category->title;
        $package = Package::find(Session::get('package_id'));
        $session_addons = DB::table('session_addons')->where('session_email','=',Auth::user()->email)->get();

        //calculate total
        $total = $package->price;
        //add addons price if any
        foreach($session_addons as $session_addon)
        {
            $total = $total + Addon::find($session_addon->addon_id)->price;
        }

        //check if GST is enabled and add it to total invoice
        if(config('settings.enable_gst'))
        {
            $gst_amount = ( config('settings.gst_percentage') / 100 ) * $total;
            $gst_amount = round($gst_amount,2);
            $total_with_gst = $total + $gst_amount;
            $total_with_gst = round($total_with_gst,2);
        }


        return view('finalize-booking', compact('event_address', 'category',
            'package', 'session_addons', 'total', 'total_with_gst', 'gst_amount'));
    }

    /**
     *
     * Thank you - payment completed
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function thankYou()
    {
        return view('thank-you');
    }

    /**
     * Payment failed
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function paymentFailed()
    {
        return view('payment-failed');
    }

    /**
     *
     * Show booking to customer
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $booking = Booking::find($id);

        //checking booking date to allow update or cancel
        $days_limit_to_update = config('settings.days_limit_to_update') * 86400;
        $days_limit_to_cancel = config('settings.days_limit_to_cancel') * 86400;
        $today = date('Y-m-d');

        if(strtotime($booking->booking_date) - strtotime($today) >= $days_limit_to_update)
        {
            $allow_to_update = true;
        }
        else
        {
            $allow_to_update = false;
        }

        if(strtotime($booking->booking_date) - strtotime($today) >= $days_limit_to_cancel)
        {
            $allow_to_cancel = true;
        }
        else
        {
            $allow_to_cancel = false;
        }

        return view('customer.bookings.view' , compact('booking','allow_to_update', 'allow_to_cancel'));
    }

    /**
     *
     * Remove addon from list of booking services
     */
    public function removeFromList()
    {
        $addon_id = \request('addon_id');
        $session_email = \request('session_email');

        DB::table('session_addons')->where('addon_id', '=', $addon_id)->where('session_email','=',$session_email)->delete();

    }


    /**
     *
     * check if addon is added in list of booking services
     */
    public function checkIfAdded($addon_id,$session_email)
    {
        $row = DB::table('session_addons')->where('addon_id', '=', $addon_id)->where('session_email','=',$session_email)->get();
        if(count($row)==0)
        {
            return 0;
        }
        else
        {
            return 1;
        }
    }


    /**
     *
     * load booking update view for user
     */

    public function update($id)
    {
        $booking = Booking::find($id);

        $cancel_request = $booking->cancel_request()->first();

        //generating a string for off days

        $off_days = DB::table('booking_times')
            ->where('is_off_day', '=', '1')
            ->get();



        $daynum = array();

        foreach ($off_days as $off_day)
        {
            if($off_day->id != 7)
            {
                $daynum[] = $off_day->id;
            }
            else
            {
                $daynum[] = $off_day->id - 7;
            }
        }

        $disable_days_string = implode(",", $daynum);

        if($booking->user->id == Auth::user()->id
            && $booking->status != __('backend.cancelled')
            && count($cancel_request)==0)
        {
            return view('customer.bookings.update', compact('booking', 'disable_days_string'));
        }
        else
        {
            return view('errors.404');
        }
    }

    public function getExtraServiceParticipants() {
        $package_id = Session::get('package_id');
        $user = auth()->user();
        $arrayParticipants = array();
        $user->isUser = true;
        array_push($arrayParticipants, $user);
        $participants = SessionPlayer::where('session_email', $user->email)->where('player_type','!=', -1)->get();
        foreach ($participants as $key => $value) {
            $participants[$key]->isUser = false;
            array_push($arrayParticipants, $value);
        }
        foreach ($arrayParticipants as $key => $value) {
            $addons = SessionAddon::where('package_id', $package_id)->where('doc_id', $value->doc_id)->with(['addon'])->get();
            if($addons) {
                $arrayParticipants[$key]->addons = $addons;
            } else {
                $arrayParticipants[$key]->addons = [];
            }
        }
        return response()->json([ 'success' => true, 'data' => $arrayParticipants ]);
    }

    public function setParticipant(Request $request) {
        Session::put('selectedPlayer',$request['doc_id']);
        return response()->json([ 'success' => true ]);
    }

    public function getPackageType() {
        $packageId = Session::get('package_id');
        $packageTypes = [];
        $category = Package::where('id',$packageId)->with(['category'])->first();
        if($category && $category->category()->first()->category_type == 1) {
            $packageTypes = PackagesType::where('package_id', $packageId)->get();
        }
        return response()->json([ 'success' => true, 'data' => $packageTypes ]);
    }

    public function setPackageType(Request $request) {
        $packageType = PackagesType::find($request['id']);
        Session::put('selectedPackageType', $packageType);
        return response()->json([ 'success' => true, 'data' => $packageType ]);
    }

}