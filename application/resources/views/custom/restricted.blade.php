
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="title" content="Reserva completada" />
    <meta name="description" content="Sistema de Reservaciones" />
    <meta name="keywords" content="reservar, golf, partidas" />
    <meta name="author" content="Sevicon Soluciones" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="6YitsSAgtnORcx5j7UrqA4swuSH6a88Rr3x5C43J">

    <!-- INDEX URL -->
    <meta name="index" content="reservaciones">

    <!-- Title -->
    <title>Reservacion de Partidas</title>
    <link rel="stylesheet" href="css/app.css">
    <link rel="icon" href="favicon.png">
        <!-- GENERATED CUSTOM COLORS -->
<style>
    .top-nav {
        background-color: #007bff !important;
    }
    .bottom-nav {
        background-color: #4e5e6a !important;
    }
    .type_title.active {
        background-color: #4e5e6a !important;
    }
    .btn-outline-dark {
        border-color: #4e5e6a !important;
    }
    .btn-outline-dark:hover {
        background-color: #4e5e6a !important;
    }
    .btn-primary {
        color:#FFFFFF !important;
    }
    .btn-danger {
        color:#FFFFFF !important;
    }
    .btn-dark {
        color:#FFFFFF !important;
    }
    .fas {
        color:#007bff !important;
    }
    .text-primary {
        color:#007bff !important;
    }
    .footer {
        background-color: #4e5e6a !important;
    }
</style>    <style>
        .promo {
            background: linear-gradient(rgba(0,0,0,.5),rgba(0,0,0,.7)),rgba(0,0,0,.7) url('images/promoClosed.jpg') no-repeat;
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body style="background-color: #F2F2F2;">

<nav class="navbar navbar-light navbar-expand-lg bg-primary top-nav">
    <a class="navbar-brand"  style="color:#FFFFFF;"><img src="images/logo-light.png" height="40"></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

</nav>

<?php
	$error_message_view_type= Session::get('error_message_view_type');
	switch ($error_message_view_type) {
		case 1:
			$title="Horario Reservaciones Cerrado";
			$body = "Horario de Acceso <br> {{ config('settings.bookingUser_startTime') }} - {{ config('settings.bookingUser_endTime') }}";
			break;
		case 2:
			$title= "Horario no se encuentra disponible";
			$body = "Otro usuario ya reservo en el horario seleccionado, <br><a href='select-booking-time'>favor intente seleccionar otro horario</a>";
			//link
			break;
		case 3:
			$title= "Maxima Cantidad reservas por dia alcanzadas";
			$body = "No puede realizar mas reservaciones para este dia, <br><a href='select-booking-time'>favor intente seleccionar otra fecha diferente</a>";
			break;
}
?>



    <div class="jumbotron promo">
        <div class="container">
            <h1 class="text-center promo-heading"> <?php echo $title; ?></h1>
            <p class="promo-desc text-center"></p>
        </div>
    </div>

    <div class="container">
        <div class="content">
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8 text-center">
                    <br>
              

                    <p style="text-align: center;">
                        <img src="images/icon-booking-failed.png">
                    </p>
                    <br>
                    <h1 class="text-dark"><strong> <?php echo $body; ?>  </strong></h1>
                    
                    <p class="text-muted"></p></b>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                        <span class="text-copyrights">
                            Derechos de autor. &copy; 2020. Todos los derechos reservados a {{ config('settings.business_name') }}.
                        </span>
                </div>
            </div>
        </div>
    </footer>

<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/app.js"></script>
</body>
</html>
