<?php 
	//echo $_SERVER['REQUEST_URI'];

	require 'wsLibrary.php';
	
	// require __DIR__ . '/application/vendor/autoload.php';

	// use App\Application;
	
	
	function str_Normalize($data)
	{
		$aux = $data;
		$aux = str_replace("'","",$aux);	
		$aux = str_replace("\"","",$aux);	
		return $aux;
	}

	{ // Initialization of variables 
	$token = "";
	$command = "";
	$field_separator = ";";
	$fieldgroup_separator = "#";
	$fieldinternal_separator = "*";
    $status = 0;
	$err_message = "";

	$first_name = "";
	$last_name = "";
	$playername= "N/A";
	$player_type=0;
	$is_user=0;	
	$email = "";
	$session_email = "";
	$phone_number = "";
	$has_errors=0;
	$has_warnings=0;
	$cant=0;
	$cant_Guests=0;
	$blacklist=0;
	$blacklistReason="";
	$group_id="-1";
	$groupActive = 0;
	$groupSuspended = 0;
	$balance = 0;
	$balanceDate = "";
		
	// $max_days = 2;
	// $max_guests = 2;
	// $max_players = 3;
	}
	
	{ // getting ajax invocation parameters 
	
		$session_email = $_GET['email'];
		$command = $_GET['command'];
		$package_id = $_GET['package_id'];
		$categoryType = $_GET['categoryType'];
		$categoryType = $_GET['categoryType'];
		
		//echo $command;
		//exit();
		
		if ($command == 'group')
		{
			$group_id =  $_GET['group_id'];	
		}
		else if ($command == 'include')
		{
			$doc_id = $_GET['doc_id'];	
		}
		else if ($command == 'delete')
		{
			$doc_id = $_GET['doc_id'];	
		}			
		else
		{
			//$doc_id = $_GET['doc_id'];	
		}			
	}

	
	function GetSaldo($connection, $group_id, &$balance, &$comments, &$has_errors, &$err_message)
	{

		//validate balance
		$status = wsConsultaSaldo($group_id, $balance, $comments);
		
		//-2 no existe, 1 saldo > 0, -1: err, 0: saldo <=0
		//echo $status . "<br>";
		
        // status:
		// -2: ok (no existe), 
        // -1: error (no hubo conexión a SQL)
        //  0: saldo<=0
        //  1: saldo>0 con deuda, 
		
		if ($status == -3 ) // -3: Cuando no se puedo recuperar la variable de ambiente de la URI del Webservice
		{
			$has_errors = 1;	
			$err_message = $err_message . "<br>Acción " . $group_id . " no pudo ser consultada porque  : " . $comments;
		}
		
		if ($status == -2 )  // -2: No existe el registro de saldo en Backoffice. Se borrará de BD Mysql local 'fronoffice'
		{
			//$balanceDate =  date('Y-m-d H:i:s');
			//$err_message = $err_message . "<br>OK" ;
			$has_errors = 0;
			
			//update local table
			$query = "DELETE FROM groups WHERE id='" . $group_id . "'";	
			$qry_result = sqlsrv_query($connection,$query ) or die( print_r( sqlsrv_errors(), true));
			
		}			
		elseif ($status == -1 )  //-1: error (no hubo conexión a SQL) , Se consultarán datos locales en MySQL
		{
			//$balanceDate =  date('Y-m-d H:i:s');
			//$has_errors = 1;			
			//$err_message = $err_message . "Error de conexión a base de datos" ;
		}
		elseif ($status == 0 ) // 0: se obtuvo registro de Backoffice con valor en el saldo menor o igual que cero
		{
			//$balanceDate =  date('Y-m-d H:i:s');
			//$err_message = $err_message . "<br>OK " ;
			$has_errors = 0;		

			//update local table
			$query = "DELETE FROM groups WHERE id='" . $group_id . "'";	
			$qry_result = sqlsrv_query($connection,$query ) or die( print_r( sqlsrv_errors(), true));

			$query = "INSERT INTO groups (id, balance,is_suspended, is_active, balance_date,created_at, updated_at) VALUES ('" . $group_id . "'," . $balance . ",0,1,GETDATE(),GETDATE(), GETDATE())";	
			// echo $query;
			$qry_result = sqlsrv_query($connection,$query ) or die( print_r( sqlsrv_errors(), true));
			
		}
		elseif ($status == 1 ) // 1: se obtuvo registro de Backoffice con valor en el saldo mayor que cero
		{
			$has_errors = 1;			
			
			$balanceDate =  date('Y-m-d H:i:s');
			//$err_message = $err_message . "<br>Acción " . $group_id . " presenta deuda  : " . $balance . " @ " . $balanceDate =  date('Y-m-d H:i:s');
			
			$err_message = $err_message . "<br>Acción " . $group_id . " presenta deuda  - Dirijase al Club para mayor informacion";			
			
			//update local table
			$query = "DELETE FROM groups WHERE id='" . $group_id . "'";	
			$qry_result = sqlsrv_query($connection,$query ) or die( print_r( sqlsrv_errors(), true));

			$query = "INSERT INTO groups (id, balance,is_suspended, is_active, balance_date,created_at, updated_at) VALUES ('" . $group_id . "'," . $balance . ",0,1,GETDATE(),GETDATE(), GETDATE())";
			// echo $query;
			$qry_result = sqlsrv_query($connection,$query ) or die( print_r( sqlsrv_errors(), true));
		}
		
		
		
		if ($status == -1 )  // -1: error Webservice o error deo conexión a SQL, Se consultarán datos locales en MySQL
		{
			//validate balance of group users
		   $queryBalanceCount = "select u.first_name, u.last_name, u.doc_id, g.balance, g.balance_date, g.is_active, g.is_suspended, u.group_id from users u, groups g where u.group_id=g.id and group_id='" . $group_id . "'";
		   
			$resultBalance = sqlsrv_query($connection, $queryBalanceCount); 
			  
			if ($resultBalance) 
			{ 
				$rowBalanceCount = sqlsrv_num_rows($resultBalance); 
			   // printf("Number of row in the table : " . $row); 
				if ($rowBalanceCount>0) 
				{
					while($row = sqlsrv_fetch_array($resultBalance)){
						$balance = $row['balance'];
						$balanceDate = $row['balance_date'];
						$groupActive = $row['is_active'];
						$groupSuspended = $row['is_suspended'];
						$group_id = $row['group_id'];
					}
				
						if ($balance>0)
						{
							//$err_message = $err_message . "<br>Acción " . $group_id . " presenta deuda  - " . $balance . "@" . $balanceDate;
							
							$err_message = $err_message . "<br>Acción " . $group_id . " presenta deuda  - Dirijase al Club para mayor informacion";
							
							$has_errors = 1;
						}
						if ($groupActive==0)
						{
							$err_message = $err_message . "<br>Accion " . $group_id . " Inactiva ";
							$has_errors = 1;
						}
						// if ($groupSuspended==1)
						// {
							// $err_message = $err_message . "<br>Accion " . $group_id . " Suspendida ";
							// $has_errors = 1;
						// }	

				}
				else
				{

				}
			}
			@sqlsrv_next_result($resultBalance); 
		}
		else
		{
			
		}
		
		return $status;
		
		
	}
	
	
	// $app = new Application();
	
	// echo '**' . getenv('URI_WEB_SERVICE') . '**';
	// exit();
	
    // Setting up connection with database 
	// require 'config.inc';
	
	{ // Database connection 
		if ($servername=='' || $username=='' || $password=='' || $database=='')
		{
			$has_errors = 1;	
			$err_message =  $err_message . "<br>Retrieval of environment variables failed."; 
			// $aux is used as output variable via post in outputHelper
			$aux = '' . $field_separator . '' . $field_separator . '' . $field_separator . $err_message . $field_separator; 
			//$aux = $err_message;
			//echo $aux;		
			exit(); //die();
		}
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
			// echo $err_message;
			// $aux is used as output variable via post in outputHelper
			//$aux = $err_message;
			$aux = '' . $field_separator . '' . $field_separator . '' . $field_separator . $err_message . $field_separator; 
			//echo $aux;		
			exit(); //die();
		}

		{ //get settings
			$querySettings = "select * from settings";
		   
			$resultSettings = sqlsrv_query($connection, $querySettings); 

			if( $resultSettings === false) {
				die( print_r( sqlsrv_errors(), true) );
			}
			
			while( $row = sqlsrv_fetch_array( $resultSettings, SQLSRV_FETCH_ASSOC) ) {
				$domain_id = $row['business_name'];
				$max_days = $row['bookingUser_maxDays'];
				// $min_players = $row['bookingUser_minPlayers'];
				$max_players = $row['bookingUser_maxPlayers'];
				$max_guests = $row['bookingUser_maxGuests'];
				$bookings_perday =  $row['bookingUserPerDay'];
				$bookings_playsperday =  $row['bookingUserPlayPerDay'];

				// Parametros para Tenis

				$bookingUser_maxTimePerDay =  $row['bookingUser_maxTimePerDay'];
				$bookingUser_maxTimePerWeek =  $row['bookingUser_maxTimePerWeek'];
				$bookingUser_maxTimePerMonth =  $row['bookingUser_maxTimePerMonth'];
				$bookingGuest_maxTimePerDay =  $row['bookingGuest_maxTimePerDay'];
				$bookingGuest_maxTimePerWeek =  $row['bookingGuest_maxTimePerWeek'];
				$bookingGuest_maxTimePerMonth =  $row['bookingGuest_maxTimePerMonth'];

			}

			sqlsrv_free_stmt( $resultSettings);			

		}
	}
	
	{ //token
		@$token = $_GET['token'];
		
		date_default_timezone_set('America/Caracas');

		//calculate token
		$date = date('Y-m-d');
		$calculated_token = md5($domain_id.$date);
		//$calculated_token = $domain_id.$date.date_default_timezone_get();		
		//$calculated_token = "123";. date_default_timezone_get()
		// if ($token !== $calculated_token)
		// {
		// 	$has_errors = 1;
		// 	$err_message =  $err_message . "<br>Invalid token " .  $token . " vs " . $calculated_token;	
		// 	$err_message =  $err_message . "<br>Invalid token ";	
			
		// 	//$aux = $err_message;
		// 	$aux = '' . $field_separator . '' . $field_separator . '' . $field_separator . $err_message . $field_separator; 
		// 	// echo $aux;		
		// 	exit();
		// }
	}

if ($command == "favorites") // query favorites
{
	$err_message = "";
	// $field_separator = ";";
	// $fieldgroup_separator = "#";
	// $fieldinternal_separator = "*";
	$members = "";
	$member = "";
	$comments = "";
	{

		$queryFavorites = "SELECT f.doc_id,   
					CONCAT(
					(SELECT CONCAT( p1.first_name,' ', p1.last_name) FROM users p1 WHERE p1.doc_id=f.doc_id  ), 
					(SELECT CONCAT( p2.first_name,' ', p2.last_name) FROM guests p2 WHERE p2.doc_id=f.doc_id  ))
					 AS PlayerName	
			FROM users_favorites f, users u 
			WHERE u.id=f.user_id
			AND u.email = '" . $session_email . "'
			ORDER BY f.created_at asc";
			
		$resultFavorites = sqlsrv_query($connection, $queryFavorites); 

		if( $resultFavorites === false) {
			die( print_r( sqlsrv_errors(), true) );
		}

		$playername = "";
		$playerid = "";
		
		while( $row = sqlsrv_fetch_array( $resultFavorites, SQLSRV_FETCH_ASSOC) ) {
			//$playername = $row['first_name'] . " " . $row['last_name'];
			$playername = $row['PlayerName'];
			$playerid = $row['doc_id'];
			$member =   $playerid . $fieldinternal_separator .  $playername ;

			$members = $members . $member . $fieldgroup_separator;	
		}
		sqlsrv_free_stmt( $resultFavorites);

		//$aux = $playername . $field_separator . $player_type . $field_separator . $status . $field_separator . $err_message . $field_separator; 
		$aux = $command  . $field_separator . $members. $field_separator . $err_message . $field_separator; 

		echo $aux;
	}

	//exit();	
}
else if ($command == "partners") // query frequent participants
{
	$err_message = "";
	// $field_separator = ";";
	// $fieldgroup_separator = "#";
	// $fieldinternal_separator = "*";
	$members = "";
	$member = "";
	$comments = "";
	
	{
		$queryPartners =  "SELECT p.doc_id, COUNT(p.doc_id), 
		CONCAT('Jugador',p.doc_id) AS PlayerName
		FROM bookings b, booking_players p, users u 
		WHERE u.id = b.user_id
		AND b.id = p.booking_id
		AND u.email = '" . $session_email . "'
		GROUP BY (p.doc_id)
		ORDER BY COUNT(p.doc_id) DESC";

		$queryPartners =  "SELECT p.doc_id, COUNT(p.doc_id) as cant,    p.player_type, 
					CASE 
						WHEN p.player_type=0 THEN (SELECT CONCAT( p1.first_name,' ', p1.last_name) FROM users p1 WHERE p1.doc_id=p.doc_id  )
						WHEN p.player_type=1 THEN (SELECT CONCAT( p2.first_name,' ', p2.last_name) FROM guests p2 WHERE p2.doc_id=p.doc_id  )
					END AS PlayerName	
					FROM bookings b, booking_players p, users u 
					WHERE u.id = b.user_id
					AND b.id = p.booking_id
					AND u.email = '" . $session_email . "' 
					GROUP BY (p.doc_id)
					ORDER BY COUNT(p.doc_id) DESC";
		
		
		$resultPartners = sqlsrv_query($connection, $queryPartners); 


		if( $resultPartners === false) {
			die( print_r( sqlsrv_errors(), true) );
		}

		$playername = "";
		$playerid = "";
		
		while( $row = sqlsrv_fetch_array( $resultPartners, SQLSRV_FETCH_ASSOC) ) {
			//$playername = $row['first_name'] . " " . $row['last_name'];
			$playername = $row['PlayerName'];
			$playerid = $row['doc_id'];
			$playerCount = $row['cant'];
			$member =   $playerid . $fieldinternal_separator .  $playername . $fieldinternal_separator . $playerCount  ;

			$members = $members . $member . $fieldgroup_separator;	
		}

		sqlsrv_free_stmt( $resultPartners);

		//$aux = $playername . $field_separator . $player_type . $field_separator . $status . $field_separator . $err_message . $field_separator; 
		$aux = $command  . $field_separator . $members. $field_separator . $err_message . $field_separator; 

		echo $aux;
	}

	//exit();
}		
else if ($command == "group") // query group
{
	$err_message = "";
	// $field_separator = ";";
	// $fieldgroup_separator = "#";
	// $fieldinternal_separator = "*";
	$members = "";
	$member = "";
	$comments = "";
	
	//check balance
	$status = GetSaldo($connection, $group_id, $balance, $comments, $has_errors, $err_message);
	
	if ($balance > 0)
	{
		$aux = $command  . $field_separator .  $members. $field_separator . $err_message . $field_separator; 
		echo $aux;
		
	}
	else if ($has_errors==0)
	{
		//query against Webservice otherwise query local
		
		$queryGroup = "SELECT * FROM users where group_id='" . $group_id . "'";

		$resultGroup = sqlsrv_query($connection, $queryGroup); 

		if( $resultGroup === false) {
			die( print_r( sqlsrv_errors(), true) );
		}

		$playername = "";
		$playerid = "";		
		
		while( $row = sqlsrv_fetch_array( $resultGroup, SQLSRV_FETCH_ASSOC) ) {
			$playername = $row['first_name'] . " " . $row['last_name'];
			$playerid = $row['doc_id'];
			$member =   $playerid . $fieldinternal_separator .  $playername ;

			$members = $members . $member . $fieldgroup_separator;	
		}
		sqlsrv_free_stmt( $resultGroup);

		//$aux = $playername . $field_separator . $player_type . $field_separator . $status . $field_separator . $err_message . $field_separator; 
		$aux = $command  . $field_separator . $members. $field_separator . $err_message . $field_separator; 

		echo $aux;
	}
	else
	{
		$aux = $command  . $field_separator . $members. $field_separator . $err_message . $field_separator; 

		echo $aux;
	}
	//exit();
}	
else if ($command == "include") // include player
{
	//get group_id
	$is_user= 1;
	$group_id = -1;
	$queryGroup = "select * from users where doc_id='" . $doc_id . "'";

	$resultGroup = sqlsrv_query($connection, $queryGroup); 

	if( $resultGroup === false) {
		die( print_r( sqlsrv_errors(), true) );
	}

	while( $row = sqlsrv_fetch_array( $resultGroup, SQLSRV_FETCH_ASSOC) ) {
		  $group_id = $row['group_id'];
	}

	sqlsrv_free_stmt( $resultGroup);	

	
	$comments = "";
	//doesnt have a group_id
	if ($group_id=="-1")
	{
		//its a guest not an user
		$is_user=0;
	}
	else
	{
		$is_user=1;
		//validate balance
		$status = wsConsultaSaldo($group_id, $balance, $comments);
		
		//-2 no existe, 1 saldo > 0, -1: err, 0: saldo <=0
		//echo $status . "<br>";
		
        // status:
		// -2: ok (no existe), 
        // -1: error (no hubo conexión a SQL)
        //  0: saldo<=0
        //  1: saldo>0 con deuda, 
		
		if ($status == -3 ) // -3: Cuando no se puedo recuperar la variable de ambiente de la URI del Webservice
		{
			$has_errors = 1;	
			$err_message = $err_message . "<br>Acción " . $group_id . " no pudo ser consultada porque  : " . $comments;
		}
		
		if ($status == -2 )  // -2: No existe el registro de saldo en Backoffice. Se borrará de BD Mysql local 'fronoffice'
		{
			//$balanceDate =  date('Y-m-d H:i:s');
			//$err_message = $err_message . "<br>OK" ;
			$has_errors = 0;
			
			//update local table
			$query = "DELETE FROM groups WHERE id='" . $group_id . "'";	
			$qry_result = sqlsrv_query($connection,$query ) or die( print_r( sqlsrv_errors(), true));
			
		}			
		elseif ($status == -1 )  //-1: error (no hubo conexión a SQL) , Se consultarán datos locales en MySQL
		{
			//$balanceDate =  date('Y-m-d H:i:s');
			//$has_errors = 1;			
			//$err_message = $err_message . "Error de conexión a base de datos" ;
		}
		elseif ($status == 0 ) // 0: se obtuvo registro de Backoffice con valor en el saldo menor o igual que cero
		{
			//$balanceDate =  date('Y-m-d H:i:s');
			//$err_message = $err_message . "<br>OK " ;
			$has_errors = 0;		

			//update local table
			$query = "DELETE FROM groups WHERE id='" . $group_id . "'";	
			$qry_result = sqlsrv_query($connection,$query ) or  die( print_r( sqlsrv_errors(), true));

			$query = "INSERT INTO groups (id, balance,is_suspended, is_active, balance_date,created_at, updated_at) VALUES ('" . $group_id . "'," . $balance . ",0,1,GETDATE(),GETDATE(), GETDATE())";	
			// echo $query;
			$qry_result = sqlsrv_query($connection,$query ) or die( print_r( sqlsrv_errors(), true));
			
		}
		elseif ($status == 1 ) // 1: se obtuvo registro de Backoffice con valor en el saldo mayor que cero
		{
			$has_errors = 1;			
			
			$balanceDate =  date('Y-m-d H:i:s');
			//$err_message = $err_message . "<br>Acción " . $group_id . " presenta deuda  : " . $balance . " @ " . $balanceDate =  date('Y-m-d H:i:s');
			
			$err_message = $err_message . "<br>Acción " . $group_id . " presenta deuda  - Dirijase al Club para mayor informacion";			
			
			//update local table
			$query = "DELETE FROM groups WHERE id='" . $group_id . "'";	
			$qry_result = sqlsrv_query($connection,$query ) or die( print_r( sqlsrv_errors(), true));

			$query = "INSERT INTO groups (id, balance,is_suspended, is_active, balance_date,created_at, updated_at) VALUES ('" . $group_id . "'," . $balance . ",0,1,GETDATE(),GETDATE(), GETDATE())";
			// echo $query;
			$qry_result = sqlsrv_query($connection,$query ) or die( print_r( sqlsrv_errors(), true));
		}
		
		
		if ($status == -1 )  // -1: error Webservice o error deo conexión a SQL, Se consultarán datos locales en MySQL
		{
			//validate balance of group users
			$queryBalanceCount = "select u.first_name, u.last_name, u.doc_id, g.balance, g.balance_date, g.is_active, g.is_suspended, u.group_id from users u, groups g where u.group_id=g.id and doc_id='" . $doc_id . "'";
		   
			$resultBalance = sqlsrv_query($connection, $queryBalanceCount); 
			  
			if( $resultBalance === false) {
				die( print_r( sqlsrv_errors(), true) );
			}

			while( $row = sqlsrv_fetch_array( $resultBalance, SQLSRV_FETCH_ASSOC) ) {
				$balance = $row['balance'];
				$balanceDate = $row['balance_date'];
				$groupActive = $row['is_active'];
				$groupSuspended = $row['is_suspended'];
				$group_id = $row['group_id'];
			}

			sqlsrv_free_stmt( $resultBalance);			 

			if ($balance>0)
			{
				//$err_message = $err_message . "<br>Acción " . $group_id . " presenta deuda  - " . $balance . "@" . $balanceDate;
				
				$err_message = $err_message . "<br>Acción " . $group_id . " presenta deuda  - Dirijase al Club para mayor informacion";
				
				$has_errors = 1;
			}
			if ($groupActive==0)
			{
				$err_message = $err_message . "<br>Accion " . $group_id . " Inactiva ";
				$has_errors = 1;
			}
			// if ($groupSuspended==1)
			// {
				// $err_message = $err_message . "<br>Accion " . $group_id . " Suspendida ";
				// $has_errors = 1;
			// }

		}
		else
		{
			
		}
		
	}
	
	//validate blacklist
	{ 
		$comments = "";
		$blacklist = 0;
		
		// 0: ok , 1: en lista negra, -1: error
		
		$retval = wsConsultarBlackList($doc_id, $comments, $first_name, $last_name);

		$first_name = str_Normalize($first_name);
		$last_name = str_Normalize($last_name);
		
			
		if ($retval == 1 )
		{
			$blacklist = 1;
			//$err_message = $err_message . "<br>En Lista Negra " . $first_name . " " .  $last_name . " - " . $comments ;
			
			$err_message = $err_message . "<br>Actualmente Sr/a " . $first_name . " " .  $last_name . " posee una condicion especial que le impide realizar reservas - Diríjase al Club para mayor información";
			
			$has_errors = 1;
			
			//update local table
			$query = "DELETE FROM blacklists WHERE doc_id='" . $doc_id . "'";	
			$qry_result = sqlsrv_query($connection,$query ) or die( print_r( sqlsrv_errors(), true));

			$query = "INSERT INTO blacklists (doc_id, comments, created_at, updated_at, first_name, last_name) VALUES ('" . $doc_id . "','" . $comments . "',GETDATE(), GETDATE(),'" . $first_name . "','" . $last_name . "')";	
			// echo $query;

			$qry_result = sqlsrv_query($connection,$query ) or die( print_r( sqlsrv_errors(), true));
			
		}
		else  if ($retval == 0 )
		{
			//update local table deleting records
			$query = "DELETE FROM blacklists WHERE doc_id='" . $doc_id . "'";	
			$qry_result = sqlsrv_query($connection,$query ) or die( print_r( sqlsrv_errors(), true));
			
		}
		elseif ($retval == -1 )  //error WS
		{
			$queryBlackList = "SELECT * from blacklists where doc_id='" . $doc_id . "'";
		   
			$resultBlackList = sqlsrv_query($connection, $queryBlackList); 

			if( $resultBlackList === false) {
				die( print_r( sqlsrv_errors(), true) );
			}

			while( $row = sqlsrv_fetch_array( $resultBlackList, SQLSRV_FETCH_ASSOC) ) {
				$blacklist=1;
				 $blacklistReason = $row['comments'];
			}

			sqlsrv_free_stmt( $resultBlackList);


			if ($blacklist==1)
			{
				//$err_message = $err_message . "<br>En Lista Negra - " . $blacklistReason;
				$err_message = $err_message . "<br>Actualmente presenta una condicion que le impide realizar reservas - Dirijase al Club para mayor informacion";
				
				$has_errors = 1;
			}
			else
			{
				//$err_message = $err_message . "<br>Participantes - " . $cant;
			}
			
		}
	}


	{ //validate number of players

		$cant = 0;
		$queryPlayerCount = "SELECT count(*) as cant from session_players where session_email='" . $session_email . "'";
   
		$resultPlayerCount = sqlsrv_query($connection, $queryPlayerCount); 

		if( $resultPlayerCount === false) {
			die( print_r( sqlsrv_errors(), true) );
		}

		while( $row = sqlsrv_fetch_array( $resultPlayerCount, SQLSRV_FETCH_ASSOC) ) {
			  $cant = $row['cant'];
		}

		sqlsrv_free_stmt( $resultPlayerCount);
		

		// Caso Estandar
		if ($categoryType == 0 && $cant>=$max_players-1)
		{
			$has_errors = 1;
			$err_message = $err_message . "<br>Máximo número de participantes permitidos: " . $max_players; // . " Maximo en settings: " . $max_players;
		}
		
		// if ($cant<$min_players-1)
		// {
			// $has_warnings = 1;
			// $err_message = $err_message . "<br>Recuerde el mínimo número de participantes permitidos: " . $min_players; // . " Mínimo en settings: " . $min_players;
		// }


	}

	//validate number of guests
	if ($is_user==0)  // It is assumed that the new player would be a guest
	{ 
		$cant_Guests = 0;
		$queryGuestCount = "SELECT  count(*) as cant from session_players where session_email='" . $session_email . "' and player_type=1";
   
		$resultGuestCount = sqlsrv_query($connection, $queryGuestCount); 

		if( $resultGuestCount === false) {
			die( print_r( sqlsrv_errors(), true) );
		}

		while( $row = sqlsrv_fetch_array( $resultGuestCount, SQLSRV_FETCH_ASSOC) ) {
			  $cant_Guests = $row['cant'];
		}

		sqlsrv_free_stmt( $resultGuestCount);


		if ($categoryType == 0 && $cant_Guests>$max_guests)
		{
			$has_errors = 1;
			$err_message = $err_message . "<br>Máximo número de Invitados permitidos: " . $max_guests;
		}
		else
		{
			//$err_message = $err_message . "<br>Participantes - " . $cant_Guests;
		}
		
    }


	
	{ // Chequea que no se exceda el máximo de partidas por día
		$bookingdate = $_GET['booking_date']; //date('d-m-Y'); //<-- ('Y-m-d'); ?
		$cancelled_status = "Cancelado";
		$intBookingPlayerCount = 0;
		$intBookingUserCount = 0;

		$queryBookingPlayerCount = "select count(*) as cant from booking_players p join bookings b on b.id = p.booking_id where p.doc_id = '" . $doc_id . "' and p.confirmed=1 and b.booking_date='" . $bookingdate . "' and b.status!='" . $cancelled_status . "'";
	   
		$resultBookingPlayerCount = sqlsrv_query($connection, $queryBookingPlayerCount); 


		if( $resultBookingPlayerCount === false) {
			die( print_r( sqlsrv_errors(), true) );
		}

		while( $row = sqlsrv_fetch_array( $resultBookingPlayerCount, SQLSRV_FETCH_ASSOC) ) {
			  $intBookingPlayerCount = $row['cant'];
		}

		sqlsrv_free_stmt( $resultBookingPlayerCount);



		$queryBookingUserCount = "select count(*) as cant from bookings b join users u on b.user_id = u.id where u.doc_id = '" . $doc_id . "' and b.booking_date='" . $bookingdate . "' and b.status!='" . $cancelled_status . "'";

		//echo $queryBookingUserCount;
		
		$resultBookingUserCount = sqlsrv_query($connection, $queryBookingUserCount); 


		if( $resultBookingUserCount === false) {
			die( print_r( sqlsrv_errors(), true) );
		}

		while( $row = sqlsrv_fetch_array( $resultBookingUserCount, SQLSRV_FETCH_ASSOC) ) {
			  $intBookingUserCount = $row['cant'];
		}
		sqlsrv_free_stmt( $resultBookingUserCount);		
		
		
		//if ($intBookingUserCount>0) $is_user=1;
		  
		if ($categoryType == 0 &&  $intBookingPlayerCount+$intBookingUserCount >= $bookings_playsperday) 
		{ 
			$has_errors = 1;
			$err_message = $err_message . "<br>Este participante no puede exceder el máximo de participaciones por día.";
			
		}
	}
	
	

	//checking user table	
	
	$playername="N/A";
    $query = "SELECT  * from users where doc_id = '" . $doc_id . "'"; 
    $result = sqlsrv_query($connection, $query); 


	if( $result === false) {
		die( print_r( sqlsrv_errors(), true) );
	}

	$is_user=0;
	$player_type=1;
	$is_active=0;
	
	while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC) ) {
		$player_type=0;
		$is_user=1;
		$playername = $row['first_name'] . " " . $row['last_name'];
		$first_name = $row['first_name'];
		$last_name = $row['last_name'];	
		$is_active = $row['is_active'];	
		$email = $row['email'];	
		$phone_number = $row['phone_number'];	
		
	//	. $field_separator . $player_type . $field_separator . $status . $field_separator . $err_message; 
	}

	sqlsrv_free_stmt( $result);
	
	//user (socio)
	if ($is_user==1)
	{
		if ($is_active==0)
		{
			$has_errors= 1;
			$err_message=  $err_message . "<br>Socio No activo";
		}

		
		//check not already registered			
		$queryRegistered = "SELECT  * from session_players where doc_id = '" . $doc_id . "' and session_email='" . $session_email . "'";
	   
		$resultRegistered = sqlsrv_query($connection, $queryRegistered); 
		
		if( $resultRegistered === false) {
			die( print_r( sqlsrv_errors(), true) );
		}

		$found=0;
		
		while( $row = sqlsrv_fetch_array( $resultRegistered, SQLSRV_FETCH_ASSOC) ) {
			$found=1;
		}
		
		if ($found == 1)
		{
			$has_errors= 1;
			$err_message = $err_message . "<br>Este participante ya está registrado";
		}

		if ($has_errors == 0)		
		{
			//insert row into session players
			$query = "INSERT INTO session_players (doc_id, player_type, session_email, created_at, updated_at, first_name, last_name, email, phone_number, package_id) VALUES ('" . $doc_id . "'," . $player_type . ",'" . $session_email . "',GETDATE(), GETDATE(),'" . $first_name . "','" . $last_name . "','" . $email . "','" . $phone_number . "','" . $package_id . "')"; 
			$qry_result = sqlsrv_query($connection,$query ) or die( print_r( sqlsrv_errors(), true));
		}
		
		sqlsrv_free_stmt( $resultRegistered);	
		
		//check status on groups
		$status=1;
		//check status on blacklist
	}	
  
	//if not user check as guest
	if ($is_user==0) 
	{
		//check guest table    
		$query = "SELECT  * from guests where doc_id = '" . $doc_id . "'"; 
		$result2 = sqlsrv_query($connection, $query); 

		if( $result2 === false) {
			die( print_r( sqlsrv_errors(), true) );
		}

		while( $row = sqlsrv_fetch_array( $result2, SQLSRV_FETCH_ASSOC) ) {
			$player_type=1;
			
			$playername = $row['first_name'] . " " . $row['last_name'];
			$first_name = $row['first_name'];
			$last_name = $row['last_name'];
			$is_active = $row['is_active'];
			$email = $row['email'];	
			$phone_number = $row['phone_number'];						
			
		//	. $field_separator . $player_type . $field_separator . $status . $field_separator . $err_message; 
		}

		sqlsrv_free_stmt( $result2);

		if ($is_active==0)
		{
			$has_errors= 1;
			$err_message=  $err_message . "<br>Invitado No activo";
			$status= 0;
		}
		else
		{
			//check not already registered			
			$queryRegistered = "SELECT  * from session_players where doc_id = '" . $doc_id . "' and session_email='" . $session_email . "'";
			$resultRegistered2 = sqlsrv_query($connection, $queryRegistered); 
			
			if( $resultRegistered2 === false) {
				die( print_r( sqlsrv_errors(), true) );
			}

			$found = 0;
			while( $row = sqlsrv_fetch_array( $resultRegistered2, SQLSRV_FETCH_ASSOC) ) {
				$found = 1;
			}
			
			sqlsrv_free_stmt( $resultRegistered2);			

			if ($found==1)
			{
					$has_errors= 1;
					$err_message=  $err_message . "<br>Ya es participante en la reservación";
			}


			if ($has_errors == 0)
			{
				//insert row into session players
				$query = "INSERT INTO session_players (doc_id, player_type, session_email, created_at, updated_at, first_name, last_name, email, phone_number, package_id) VALUES ('" . $doc_id . "'," . $player_type . ",'" . $session_email . "',GETDATE(), GETDATE(),'" . $first_name . "','" . $last_name . "','" . $email . "','" . $phone_number . "', '" . $package_id . "')";	
				$qry_result = sqlsrv_query($connection,$query ) or die( print_r( sqlsrv_errors(), true));
			}													
			
			$status=1;

			if ($playername=="N/A")
			{
				$has_errors = 1;
				$err_message =  $err_message . "<br>Participante No está registrado en el sistema";
				$player_type=-1;
				$status = 0;
			}

			//check status on blacklist
			//$status=0;
			
		}		


	}

	if ($err_message != "") $status=0;
	//$player_type = 1;
	
	$aux = $command  . $field_separator . $playername . $field_separator . $player_type . $field_separator . $status . $field_separator . $err_message . $field_separator; 
	
	echo $aux; 
	
}
else if ($command == "delete")  ///delete player
{
	
	//delete row into session players
	$query = "DELETE FROM session_players WHERE doc_id='" . $doc_id . "' AND session_email='"  . $session_email . "'"; 
	$qry_result = sqlsrv_query($connection,$query ) or die( print_r( sqlsrv_errors(), true));

	//$aux = $command  . $field_separator . $playername . $field_separator . $player_type . $field_separator . $status . $field_separator . $err_message . $field_separator; 
	$aux = "Deleted"; 
	echo $aux; 

} 


	
	date_default_timezone_set('America/Caracas');
	$date = date('d/m/Y h:i:s a', time());
	$myfile = fopen("logsdataHelper.txt", "a") or die("Unable to open file!");
	$txt = $date . " - " . $aux;
	fwrite($myfile, "\n". $txt);
	fclose($myfile);

	// if ($has_errors == 1) {
			// $err_message = '<font color="red">' . $err_message . '</font>';
	// }

	// if ($has_warnings == 1) {
			// $err_message = '<font color="yellow">' . $err_message . '</font>';
	// }

	// Connection close  
	sqlsrv_close($connection); 

?> 





