<br><br>
<h5>{{ __('app.select_date_title') }}</h5>
<p class="text-info">{{ __('app.select_date_info') }}</p>
<br>
<div class="row custom-slot-list">
    @for($a=0; $a<$hours;$a++)
        @if($list_slot[$a]['is_available'])
            <div class="col-md-3" id="slot-container">
                <a class="btn btn-outline-{{ \Session::get('booking_type_id') == 2 ? 'yellow' : 'dark' }} btn-lg btn-block btn-slot available" data-slot-time="{{ $list_slot[$a]['slot'] }}">{{ $list_slot[$a]['slot'] }}</a>
            </div>
		@elseif($list_slot[$a]['description']=='PROGRESS')
            <div class="col-md-3" id="slot-container">
                <a class="btn   btn-lg btn-block  btn-slot btn-danger disabled"><font color="FFFFFF"> {{ $list_slot[$a]['slot'] }}</font></a>
            </div>	
		@elseif($list_slot[$a]['is_blocked'])
            <div class="col-md-3" id="slot-container">
                <a class="btn   btn-lg btn-block  btn-slot btn-warning disabled"  title="{{ $list_slot[$a]['description'] }}"  data-slot-time="{{ $list_slot[$a]['slot'] }}"  >{{ $list_slot[$a]['slot'] }}</a>
            </div>
		@elseif($list_slot[$a]['is_expired'])
            <div class="col-md-3" id="slot-container">
                <a class="btn   btn-lg btn-block  btn-slot btn-secondary disabled"><font color="FFFFFF"> {{ $list_slot[$a]['slot'] }}</font></a>
            </div>	
        @elseif($list_slot[$a]['is_event'])
            <div class="col-md-3" id="slot-container">
                <a class="btn   btn-lg btn-block  btn-slot btn-warning disabled"><font color="FFFFFF"> {{ $list_slot[$a]['slot'] }}</font></a>
            </div>	
        @else
            <div class="col-md-3" id="slot-container">  <!-- btn-outline-dark  LA -->
                <a class="btn   btn-lg btn-block  btn-slot btn-success disabled"><font color="FFFFFF"> {{ $list_slot[$a]['slot'] }}</font></a>
            </div>
        @endif
    @endfor
</div>
<br><br>