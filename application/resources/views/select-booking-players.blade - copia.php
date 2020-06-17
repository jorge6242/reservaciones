@extends('layouts.app', ['title' => __('app.step_players_page_title')])

 
@section('content')


<?php
	$domain_id = config('settings.business_name', 'Reservaciones');
	$date = date('Y-m-d');
	$calculated_token = md5($domain_id.$date);	
	//$calculated_token = $domain_id.$date.date_default_timezone_get();	
	//$calculated_token = "123";
?>

<script language="javascript" type="text/javascript"> 

function wait(ms){
   var start = new Date().getTime();
   var end = start;
   while(end < start + ms) {
     end = new Date().getTime();
  }
}

function Reload()
{
	document.location.reload(false);
	//window.location.reload(true); 
}

<!--  
//Browser Support Code 
function ajaxFunctionInclude(PlayerSlot){ 
	//var PlayerSlot;
    var ajaxRequest;  // The variable that makes Ajax possible! 
     
    try{ 
        // Opera 8.0+, Firefox, Safari 
        ajaxRequest = new XMLHttpRequest(); 
    } catch (e){ 
        // Internet Explorer Browsers 
        try{ 
            ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP"); 
        } catch (e) { 
            try{ 
                ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP"); 
            } catch (e){ 
                // Something went wrong 
                alert("Your browser broke!"); 
                return false; 
            } 
        } 
    } 

	//disable buttons meanwhile
	document.getElementById("btnSearch").disabled = true;
	document.getElementById("btnRefresh").disabled = true;

	var errorPlayer = 0;

    if (PlayerSlot==1)
	{
		var player1 = document.getElementById('player1').value; 
		
		//validate that user is not included as participant again
		if (player1=={{Auth::user()->doc_id}})
		{
			alert("Como solicitante ya estás registrado como participante");
			errorPlayer = 1;
		}
		else
		{

		}
		
		
//		if (player1)
		{
			var queryString = "?command=include&doc_id=" + player1+ "&email=" +  "{{Auth::user()->email}}" +  "&token=" + "<?php echo $calculated_token; ?>";  
			//PlayerSlot=1;
		}
	}

	if (errorPlayer==0)
	{
		// Create a function that will receive data sent from the server 
		ajaxRequest.onreadystatechange = function()
		{ 
			var ajaxDisplay;
			var ajaxDisplayType;
			var ajaxDisplayStatus;
			var ajaxDisplayError;
			var ajaxDisplayIcons;
			
			if(ajaxRequest.readyState == 4){ 
			
				if (PlayerSlot==1)
				{
					 ajaxDisplay = document.getElementById('player1Name'); 	
					 ajaxDisplayType = document.getElementById('player1Type');
					 ajaxDisplayStatus = document.getElementById('player1Status');
					 ajaxDisplayError = document.getElementById('player1ErrorMessage');
					 ajaxDisplayIcons = document.getElementById('player1Icons');
				}	
				
				//clean
				ajaxDisplay.value = '';
				ajaxDisplayType.value = '';
				ajaxDisplayStatus.value = '';
				ajaxDisplayIcons.innerHTML = '';
				ajaxDisplayError.innerHTML = '';		
				
				var responseData = ajaxRequest.responseText; 
				//split response
				var partsOfStr = responseData.split(';');

				for (var k = 0; k < partsOfStr.length; k++) {
					if (k==0)
					{  var PlayerName=partsOfStr[k]; }
					if (k==1)
					{  var PlayerType=partsOfStr[k]; }
					if (k==2)
					{  var PlayerStatus=partsOfStr[k]; }
					if (k==3)
					{  var PlayerErrorMessage=partsOfStr[k]; }		
				}
				
				PlayerIcons = '';
				// if (PlayerType==0)
				// {
					// //PlayerIcons = PlayerIcons + '<span class="glyphicon glyphicon-user"></span>';
					// PlayerIcons = PlayerIcons + 'Socio ';
				// }
				// if (PlayerType==1)
				// {
					// PlayerIcons = PlayerIcons + 'Invitado ';
				// }
				if (PlayerType==-1)
				{
					PlayerIcons = PlayerIcons + '<font color="red">Jugador No Registrado </font>';
				}
				
				if (PlayerStatus==0)
				{
					//PlayerIcons = PlayerIcons + '<span class="glyphicon glyphicon-ban-circle"></span>';
					// PlayerIcons = PlayerIcons + '<font color="red">NO PERMITIDO</font>';
				}	
				else
				{
					//PlayerIcons = PlayerIcons + '<span class="glyphicon glyphicon-ok-circle"></span>';
					//PlayerIcons = PlayerIcons + 'OK';
				}	//"?doc_id="

				ajaxDisplay.value = PlayerName;
				ajaxDisplayType.value = PlayerType;
				ajaxDisplayStatus.value = PlayerStatus;
				ajaxDisplayIcons.innerHTML = PlayerIcons;
				ajaxDisplayError.innerHTML = PlayerErrorMessage;
						

				//ajaxDisplay.value = ajaxRequest.responseText; 
				//ajaxDisplay.innerHTML = ajaxRequest.responseText; 

				if (PlayerErrorMessage=='')
				{
					//window.location.reload(false);
					Reload();
				}

				//enable buttons meanwhile
				//alert ("habilitando");
				document.getElementById("btnSearch").disabled = false;
				document.getElementById("btnRefresh").disabled = false;			

			} 
			
			//refresh page
			//wait(3000);
			//document.location.reload(true);
			//window.location.reload(true); 

		} 
	
		ajaxRequest.open("GET", "dataHelper.php" + queryString, true); 
		ajaxRequest.send(null);  
	}
	else
	{
		document.getElementById("btnSearch").disabled = false;
		document.getElementById("btnRefresh").disabled = false;	
	}
} 
//--> 

</script> 



<script language="javascript" type="text/javascript"> 

function ajaxFunctionDelete(doc_id){ 
	//var PlayerSlot;
    var ajaxRequest;  // The variable that makes Ajax possible! 
     
    try{ 
        // Opera 8.0+, Firefox, Safari 
        ajaxRequest = new XMLHttpRequest(); 
    } catch (e){ 
        // Internet Explorer Browsers 
        try{ 
            ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP"); 
        } catch (e) { 
            try{ 
                ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP"); 
            } catch (e){ 
                // Something went wrong 
                alert("Your browser broke!"); 
                return false; 
            } 
        } 
    } 
	
	var queryString = "?command=delete&doc_id=" + doc_id + "&email=" + "{{Auth::user()->email}}" +  
	"&token=" + "<?php echo $calculated_token; ?>";  
	

    // Create a function that will receive data sent from the server 
    ajaxRequest.onreadystatechange = function()
	{ 
		var ajaxDisplay;
		
        if(ajaxRequest.readyState == 4){ 
		
			 ajaxDisplay = document.getElementById('PlayerRow' + doc_id); 	
        //ajaxDisplay = document.getElementById('PlayerRow12422099'); 	
		
		
		var responseData = ajaxRequest.responseText; 
		//split response
		/*var partsOfStr = responseData.split(';');

		for (var k = 0; k < partsOfStr.length; k++) {
			if (k==0)
			{  var PlayerName=partsOfStr[k]; }
			if (k==1)
			{  var PlayerType=partsOfStr[k]; }
			if (k==2)
			{  var PlayerStatus=partsOfStr[k]; }
			if (k==3)
			{  var PlayerErrorMessage=partsOfStr[k]; }		
		}
		*/
		
		//hide row
		ajaxDisplay.style.display = "none";
		//ajaxDisplay.value = responseData;

		//ajaxDisplay.value = ajaxRequest.responseText; 
		//ajaxDisplay.innerHTML = ajaxRequest.responseText; 
        } 

    } 

	
    ajaxRequest.open("GET", "dataHelper.php" + queryString, true); 
    ajaxRequest.send(null);  

} 
</script>


    <div class="jumbotron promo">
        <div class="container">
            <h1 class="text-center promo-heading">{{ __('app.step_players_page_title') }}</h1>
            <p class="promo-desc text-center">{{ __('app.step_players_subtitle') }}</p>
        </div>
    </div>

    <form method="post" id="booking_step_player" action="{{ route('postStepPlayer') }}">
        <input type="hidden" name="session_email" value="{{ Auth::user()->email }}">
        {{ csrf_field() }}
        <div class="container">
            <div class="content">

			<div class="row begin-countdown">
			  <div class="col-md-12 text-center">
				<progress value="180" max="180" id="pageBeginCountdown"></progress>
				<p> Tiempo restante <span id="pageBeginCountdownText">180 </span> segundos</p>
			  </div>
			</div>
	
			<h6><i class="fas fa-calendar fa-lg text-primary"></i>&nbsp;&nbsp;{{ Session::get('event_date') }} {{ Session::get('booking_slot') }}</h6>

                <div class="row">
                    <div class="col-md-12">
                        <div class="progress mx-lg-5" style="height: 30px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 10%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100">50%</div>

                        </div>
                    </div>
                </div>

				<?php
				$i=0;
				?>
				
				<script>
				var ItemValue;
				var item;
				</script>
				<br><br>
				<div class="row">
						<div class="col-md-1">
							
						</div>
						<div class="col-md-10">
							<b>NOTAS:</b><br>
							
								<i class="fa fa-bullhorn"></i>  En caracter de invitado puede ingresar un maximo de dos (2) veces al mes al club.<br>
							<i class="fa fa-bullhorn"></i>  Como Socio, debe estar solvente con su cuota de mantenimiento previo a iniciar la partida.<br><br>
						</div>

						
				</div>
				
				<div class="row">
						<div class="col-md-1">
							<i class="fa fa-star"></i>
						</div>
						<div class="col-md-2">
							{{Auth::user()->doc_id}}
						</div>
						<div class="col-md-6">
							{{Auth::user()->first_name}} {{Auth::user()->last_name}}
						</div>
						<div class="col-md-2"></div>
				</div>
				@if(count($session_players))
					@foreach($session_players as $player)
					<div id="PlayerRow{{ $player->doc_id  }}" >
					<div class="row">
						<div class="col-md-1">

							<div class="text-left player_type">
							@if($player->player_type==1)
								 <i class="fa fa-user"></i>
							@else		
							      <i class="fa fa-user-plus"></i>
							@endif							
							</div>
									
								<script>
								/*
								ItemValue = '{{ $player->doc_id  }}';
								item = document.getElementById('player<?php	echo $i;?>');
								item.value = ItemValue;
								*/
								</script>
						</div>					
					
						<div class="col-md-2">

							<div class="text-left player_doc_id">{{ $player->doc_id  }} </div>
									
								<script>
								/*
								ItemValue = '{{ $player->doc_id  }}';
								item = document.getElementById('player<?php	echo $i;?>');
								item.value = ItemValue;
								*/
								</script>
						</div>
						
						<div class="col-md-6">
							<div class="text-left player_name">

							{{ $player->first_name  }}
							{{ $player->last_name  }}

							</div>
									
						</div>
						<form name='myForm{{$player->doc_id}}' method="post" action="outputHelper.php"> 
						<div class="col-md-2">
							<div class="text-left player_delete" >
							
								<input type='button' 
								onclick='ajaxFunctionDelete({{$player->doc_id}})' value='Eliminar' />
								
								<!--PlayerRow{{$player->doc_id}}.style.display="none"; 
								-->
							</div>
						</div>
						</form>
					</div>
					</div>
					@endforeach

				@else
				@endif

				<br><br>

				<form name='myForm' method="post" action="outputHelper.php"> 

				<div class="row">
						<div class="col-md-1">
                            <h5>{{ __('app.player') }}</h5>
						</div>	
                        <div class="col-md-3">    
							<input type="text"  rows="" class="form-control has-success has-feedback form-control-lg" name="player1"
                                   id="player1" placeholder="{{ __('app.player_placeholder') }}" autocomplete="off">
						</div>
						
						<input name="btnSearch" id="btnSearch" type='button' onclick='ajaxFunctionInclude(1)' value='Buscar' /> 
						<div id='ajaxDivPlayer1'> 
						</div>
						<div class="col-md-6">
							<input type="text" class="form-control form-control-lg" name="player1Name"
                                   id="player1Name"  autocomplete="off" disabled>
						</div>
						<div class="col-md-1">
							<input name="btnRefresh" id="btnRefresh"  type='button' onclick='Reload()' value='Refrescar' /> 
						</div>
						
						
				</div>	

				<div class="row">
						<div class="col-md-1">
						</div>
						<div class="col-md-9">
								   
							<input type="hidden" class="form-control form-control-lg" name="player1Type"
                                   id="player1Type"  autocomplete="off">
							<input type="hidden" class="form-control form-control-lg" name="player1Status"
                                   id="player1Status"  autocomplete="off">
							<div id="player1Icons" name="player1Icons"></div>
							<font color="red">
							<div id="player1ErrorMessage" name="player1ErrorMessage"></div></font>								  
                        </div>

				</div>	
				
				</form> 

            </div>
        </div>

        <br><br>
        <footer class="footer d-none d-sm-none d-md-block d-lg-block d-xl-block">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <span class="text-copyrights">
                            {{ __('auth.copyrights') }}. &copy; {{ date('Y') }}. {{ __('auth.rights_reserved') }} {{ config('settings.business_name', 'Bookify') }}.
                        </span>
                    </div>
					
                    <div class="col-md-6 text-right">
                        <a href="{{ route('loadStepExtraServices') }}" class="navbar-btn btn btn-primary btn-lg ml-auto">
                            {{ __('app.step_player_button') }}
                        </a>
                    </div>
					
	<!--				
					<div class="col-md-6 text-right">
                       <button type="submit" class="navbar-btn btn btn-primary btn-lg ml-auto">
                           {{ __('app.step_player_button') }}
                       </button>
                   </div>	
-->				   
					
                </div>
            </div>
        </footer>

        {{--FOOTER FOR PHONES--}}

        <footer class="footer d-block d-sm-block d-md-none d-lg-none d-xl-none">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <a href="{{ route('loadStepBookingTime') }}" class="navbar-btn btn btn-primary btn-lg ml-auto">
                            {{ __('app.step_player_button') }}
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </form>

@endsection

@section('scripts')
    <script>
        $('.addon_buttons').on('click', 'a.btn-addon', function() {

            var method = $(this).attr('data-method');

            if(method === "add") {
                $(this).removeClass('btn-primary').addClass('btn-danger').text('{{ __("app.remove_service_btn") }}');
            }

            else if(method === "remove") {
                $(this).removeClass('btn-danger').addClass('btn-primary').text('{{ __("app.add_service_btn") }}');
            }

        });
    </script>

	<script>
		ProgressCountdown(180, 'pageBeginCountdown', 'pageBeginCountdownText').then(value => window.location.href = `logoutBooking`);


		function ProgressCountdown(timeleft, bar, text) {
		  return new Promise((resolve, reject) => {
			var countdownTimer = setInterval(() => {
			  timeleft--;

			  document.getElementById(bar).value = timeleft;
			  document.getElementById(text).textContent = timeleft;

			  if (timeleft <= 0) {
				clearInterval(countdownTimer);
				resolve(true);
			  }
			}, 1000);
		  });
		}
	</script>
	
@endsection