@extends('layouts.app', ['title' => __('app.welcome_page_title')])

@section('content')

<style>

.package_box {
  margin-bottom: 0px !important;
  cursor: pointer;
  box-shadow: 0 0.3em 0.88em rgba(0, 0, 0, 0.3);
  border-radius: 5px;
}

.booking-collapse-button {
    border: 3px solid #007bff;
    padding: .375rem .75rem;
    font-size: .9rem;
    line-height: 1.6;
    border-radius: .25rem;
    color: #007bff;
}

.booking-collapse-button:hover {
    text-decoration: none;
}

.collapse-href a {
	font-size: 20px;
	color: white;
}


.owl-theme .owl-nav .owl-next {
    margin-top: -45px !important;
    border-radius: 56px !important;
    background: #007bff !important;
}

.owl-theme .owl-nav .owl-prev {
    margin-top: -45px !important;
    border-radius: 56px !important;
    background: #007bff !important;
}

#tennis-calendar {
	margin: 20px 0px 20px 0px;
}

#tennis-calendar .cell {
	border: 1px solid #2c3e50;
}
#tennis-calendar .cell.active {
	background-color: #f1c40f;
}
#tennis-calendar .header, .time {
	font-weight: bold;
}

@media only screen and (max-width: 600px) {
	#tennis-calendar .cell {
		font-size: 9px;
		flex: 0 0 16.66666667%;
		max-width: 16.66666667%;
		padding-left: 5px;
		padding-right: 0px;
	}
}

</style>

{{-- aqui va el codigo --}}

    <div class="jumbotron promo">
        <div class="container">
            <h1 class="text-center promo-heading">{{ __('app.welcome_title') }}</h1>
            <p class="promo-desc text-center">
                {{ __('app.welcome_subtitle') }}
            </p>
        </div>
    </div>

    <form method="post" id="custom_booking_step_1" action="{{ Auth::user() ? route('postStep1') : route('register') }}">

        {{csrf_field()}}

        @if(!Auth::user())
            <input type="hidden" name="password" value="{{ $random_pass_string }}">
            <input type="hidden" name="password_confirmation" value="{{ $random_pass_string }}">
			
			

			<!-- Force to be logged to book  LA 			-->
			<script>
			window.location.href = `{{ url('login') }}`;
			</script>

        @else
		@endif

		<input type="hidden"  id="countdown" name="countdown" >

        <div class="container">
            <div class="content">
			<div class="col-md-12 text-center">
				<h6><a href="http://www.clublagunita.com/" target="_blank"> <i class="fas fa-briefcase fa-lg text-primary"></i>&nbsp;Ver Reglamento del Golf<br><br></a> </h6>
			</div>
			
			<?php
			/*
			<div class="row begin-countdown">
			  <div class="col-md-12 text-center">
				<progress value="180" max="180" id="pageBeginCountdown"></progress>
				<p> Tiempo restante <span id="pageBeginCountdownText">180 </span> segundos</p>
			  </div>
			</div>*/
			?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="progress mx-lg-5">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
                        </div>
                        
                        <br>
                        @if($errors->has('email'))
                            <div class="alert alert-danger">
                                {{ __('app.existing_email_error') }}
                            </div>
                        @endif
						
						
                        @if($errors->has('phone_number'))
                            <div class="alert alert-danger">
                                {{ __('app.existing_phone_error') }}
                            </div>
                        @endif
						
                    </div>
                </div>

                @if(!Auth::user())

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" autocomplete="off" class="form-control form-control-lg"
                                       name="first_name" id="first_name" placeholder="{{ __('app.first_name') }}">
                                <p id="first_name_error_holder" class="form-text text-danger d-none">
                                    {{ __('app.first_name_error') }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" autocomplete="off" class="form-control form-control-lg"
                                       name="last_name" id="last_name" placeholder="{{ __('app.last_name') }}">
                                <p id="last_name_error_holder" class="form-text text-danger d-none">
                                    {{ __('app.last_name_error') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="number" autocomplete="off" class="form-control form-control-lg"
                                       name="phone_number" id="phone_number" placeholder="{{ __('app.phone_number') }}">
                                <p id="phone_number_error_holder" class="form-text text-danger d-none">
                                    {{ __('app.phone_error') }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="email" autocomplete="off" class="form-control form-control-lg"
                                       name="email" id="email" placeholder="{{ __('app.email') }}">
                                <p id="email_error_holder" class="text-danger d-none">
                                    {{ __('app.email_error') }}
                                </p>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" autocomplete="off" class="form-control form-control-lg"
                                       name="doc_id" id="doc_id" placeholder="{{ __('app.doc_id') }}">
                                <p id="doc_id_error_holder" class="text-danger d-none">
                                    {{ __('app.doc_id_error') }}
                                </p>
                            </div>
                        </div>					
					
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" autocomplete="off" class="form-control form-control-lg"
                                       name="group_id" id="group_id" placeholder="{{ __('app.group_id') }}">
                                <p id="group_id_error_holder" class="form-text text-danger d-none">
                                    {{ __('app.group_id_error') }}
                                </p>
                            </div>
                        </div>

                    </div>

                @else
						<div class="col-md-12 form-group"> 
                                <div class="row">
                                    <div class="col-md-12 form-group btn-primary"> 
                                        <div class="row" style="padding: 10px;">
										<div class="col-md-6 " style="text-align: left; font-weigth: bold; line-height: 2; font-size: 1.125rem;"> {{ __('app.personal_details') }} </div>
										<div class="col-md-6 collapse-href" style="text-align: right;"> 
											<a class="collapsed" data-toggle="collapse" href="#participantsCollapse" role="button" aria-expanded="false" aria-controls="participantsCollapse">
                                       			 <i class="fa fa-angle-down" style="margin-left: 5px"></i>
                                        	</a>
										 </div>
										</div>
										
                                    </div>
                                    <div class="col-md-12 collapse" id="participantsCollapse"> 
                                        <div class="row" id="extra-service-participants">

										<div class="col-md-6">
											<div class="form-group">
												<input type="text" value="{{ Auth::user()->first_name }}" readonly disabled=""
													autocomplete="off" class="form-control form-control-lg"
													name="first_name" id="first_name" placeholder="{{ __('app.first_name') }}">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<input type="text" autocomplete="off" value="{{ Auth::user()->last_name }}"
														readonly disabled="" class="form-control form-control-lg"
														name="last_name" id="last_name" placeholder="{{ __('app.last_name') }}">
											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group">
												<input type="text" autocomplete="off" value="{{ Auth::user()->phone_number }}"
													readonly disabled="" class="form-control form-control-lg"
													name="phone_numberNOTVALID" id="phone_numberNOTVALID" placeholder="{{ __('app.phone_number') }}">
											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group">
												<input type="email" autocomplete="off" value="{{ Auth::user()->email }}"
													readonly disabled="" class="form-control form-control-lg"
													name="email" id="email" placeholder="{{ __('app.email') }}">
											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group">
												<input type="text" autocomplete="off" value="{{ Auth::user()->doc_id }}"
														readonly disabled="" class="form-control form-control-lg"
														name="doc_id" id="doc_id" placeholder="{{ __('app.doc_id') }}">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<input type="text" autocomplete="off" value="{{ Auth::user()->group_id }}"
														readonly disabled="" class="form-control form-control-lg"
														name="group_id" id="group_id" placeholder="{{ __('app.group_id') }}">
											</div>
										</div>

										</div>
                                    </div>
                                </div>
                            </div>			

                @endif

                <div id="categories_holder">
                    <br>
                    <div class="row"><div class="col-md-12"><h5>{{ __('app.booking_category') }}</h5></div></div>
                    <br>
                    <div class="row">
                        @if(count($categories))
                            @foreach($categories as $category)
                                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-3">
                                    <div class="type_box custom_category_box" data-category-type="{{ $category->category_type }}" data-category-id="{{ $category->id }}">
                                        <div class="responsive-image"><img class="responsive-image" alt="{{ $category->title }}" src="{{ asset($category->photo->file) }}"></div>
                                        <div class="type_title">
                                            <div class="text-container">
                                                <p class="text_type">{{ $category->title }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-md-12">
                                <div class="alert alert-danger">{{ __('app.no_category_error') }}</div>
                            </div>
                        @endif
                    </div>
                    <br>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div id="packages_loader" class="d-none"><p style="text-align: center;"><img src="{{ asset('images/loader.gif') }}" width="52" height="52"></p></div>
                    </div>
                </div>

                <div id="packages-by-type"></div>
                <div id="tennis-calendar"></div>
                <div id="packages_holder"></div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-danger d-none" id="package_error">{{ __('app.no_package_selected_error') }}</div>
                        <div class="alert alert-danger d-none" id="welcome-message-error"></div>
                        <br>
                    </div>
                </div>

            </div>
        </div>

        <footer class="footer d-none d-sm-none d-md-block d-lg-block d-xl-block">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <span class="text-copyrights">
                            {{ __('auth.copyrights') }}. &copy; {{ date('Y') }}. {{ __('auth.rights_reserved') }} {{ config('settings.business_name', 'Bookify') }}.
                        </span>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="submit" class="navbar-btn btn btn-primary btn-lg ml-auto">
                       <i class="far fa-clock"></i> &nbsp; {{ __('app.welcome_post_btn') }}
                        </button>
                    </div>
                </div>
            </div>
        </footer>

        {{--FOOTER FOR PHONES--}}

        <footer class="footer d-block d-sm-block d-md-none d-lg-none d-xl-none">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <button type="submit" class="navbar-btn btn btn-primary btn-lg ml-auto">
                            <i class="far fa-clock"></i> &nbsp; {{ __('app.welcome_post_btn') }}

                        </button>
                    </div>
                </div>
            </div>
        </footer>

    </form>

@endsection

@section('scripts')

    <script>
        $('body').on('click', 'a.btn_package_select', function() {
			$('.package_title.container').removeClass('active');
			$('.type_title.pack').removeClass('active');
        	$(this).find('.type_title.pack').addClass('active');
            $('.btn_package_select').text('{{ __("app.booking_package_btn_select") }}').removeClass('btn-danger').addClass('btn-primary');
            $(this).text('{{ __("app.booking_package_btn_selected") }}').removeClass('btn-primary').addClass('btn-danger');
        });
    </script>

{{ Session::get('timeout') }}


	<script>
		// ProgressCountdown(180, 'pageBeginCountdown', 'pageBeginCountdownText').then(value => window.location.href = `logoutBooking`);


		// function ProgressCountdown(timeleft, bar, text) {
		  // return new Promise((resolve, reject) => {
			// var countdownTimer = setInterval(() => {
			  // timeleft--;

			  // document.getElementById(bar).value = timeleft;
			  // document.getElementById(text).textContent = timeleft;
			  // document.getElementById('countdown').value = timeleft;
 

			  // if (timeleft <= 0) {
				// clearInterval(countdownTimer);
				// resolve(true);
			  // }
			// }, 1000);
		  // });
		// }

		function handleSelectReport() {
			$('#tennis-calendar').empty();
			let html = `
				<div class="row header">
					<div class="col-sm-2 col-xs-2 col-md-2 cell"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell">Cancha 1</div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell">Cancha 2</div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell">Cancha 3</div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell">Cancha 4</div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell">Cancha 5</div>
                </div>
                <div class="row">
					<div class="col-sm-2 col-xs-2 col-md-2 cell time">6:00</div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell active"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell active"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell"></div>
                </div>
                <div class="row">
					<div class="col-sm-2 col-xs-2 col-md-2 cell time">6:30</div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell active"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell active"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell active"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell active"></div>
                </div>
                <div class="row">
					<div class="col-sm-2 col-xs-2 col-md-2 cell time">7:00</div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell active"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell active"></div>
                </div>
                <div class="row">
					<div class="col-sm-2 col-xs-2 col-md-2 cell time">7:30</div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell active"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell active"></div>
                </div>
                <div class="row">
					<div class="col-sm-2 col-xs-2 col-md-2 cell time">8:00</div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell active"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell"></div>
                </div>
                <div class="row">
					<div class="col-sm-2 col-xs-2 col-md-2 cell time">8:30</div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell active"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell active"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell active"></div>
                </div>
                <div class="row">
					<div class="col-sm-2 col-xs-2 col-md-2 cell time">9:00</div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell active"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell active"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell active"></div>
                </div>
                <div class="row">
					<div class="col-sm-2 col-xs-2 col-md-2 cell time">9:30</div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell active"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell active"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell active"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell"></div>
                </div>
                <div class="row">
					<div class="col-sm-2 col-xs-2 col-md-2 cell time">10:00</div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell active"></div>
					<div class="col-sm-2 col-xs-2 col-md-2 cell"></div>
				</div>
			`;
			$('#tennis-calendar').html(html);
			
		}

		function renderDates(dates) {
			let html = '';
			dates.forEach(element => {
				html +=`<option value="${element.date}">${moment(element.date.date).format('MMMM Do YYYY')}</option>`;
			})
			return html;
		}

	$("div").on("click", "div.custom_category_box", function(){
        var category_id = $(this).attr('data-category-id');
        var category_type = $(this).attr('data-category-type');
        $('.type_title').removeClass('active');
        $(this).find('.type_title').addClass('active');
        var URL_CONCAT = $('meta[name="index"]').attr('content');
		if(category_type == 0) {
			$.ajax({
            type: 'POST',
            url: URL_CONCAT + '/get_packages',
            data: {parent:category_id},
            beforeSend: function() {
                $('#packages_loader').removeClass('d-none');
				$('#packages-by-type').empty();
				$('#tennis-calendar').empty();
                $('#packages_holder').html('&nbsp;');
            },
            success: function(response) {
                $('#packages_holder').fadeIn().html(response);
				$(".owl-carousel").owlCarousel({
                    margin:20,
                    dots:false,
                    nav:true,
					items: 1,
                    navText: [
                        '<img src="'+ URL_CONCAT + '/images/left.png">',
                        '<img src="'+ URL_CONCAT + '/images/right.png">'
                    ],
                    responsiveClass: true,
                    responsive: {
                        0: {
                            items: 1,
							loop:true,
                        },
                        480: {
                            items: 1,
							loop:true,
                        },
                        769: {
                            items: 3,
                        }
                    }
                });
                
            },
            complete: function () {
                $('#packages_loader').addClass('d-none');
            }
        });
		}

		if(category_type == 1) {
			$.ajax({
            type: 'GET',
            url: URL_CONCAT + '/get-packages-by-type',
            data: {id:category_id},
            beforeSend: function() {
                $('#packages_loader').removeClass('d-none');
				$('#packages_holder').empty();
                $('#packages-by-type').empty();
                $('#tennis-calendar').empty();
            },
            success: function(response) {
				let html = '';
				html +=` <select class="form-control" name="tennis-time" onchange="handleSelectReport()">
							${renderDates(response.dates)}	
						</select> `;
				console.log('response ', response);
                
				$('#packages-by-type').fadeIn().html(html);
				$.ajax({
				type: 'POST',
				url: URL_CONCAT + '/get_packages',
				data: {parent:category_id},
				beforeSend: function() {
					$('#packages_loader').removeClass('d-none');
					$('#packages_holder').html('&nbsp;');
				},
				success: function(response) {
					$('#packages_holder').fadeIn().html(response);
					$(".owl-carousel").owlCarousel({
						margin:20,
						dots:false,
						nav:true,
						items: 1,
						navText: [
							'<img src="'+ URL_CONCAT + '/images/left.png">',
							'<img src="'+ URL_CONCAT + '/images/right.png">'
						],
						responsiveClass: true,
						responsive: {
							0: {
								items: 1,
								loop:true,
							},
							480: {
								items: 1,
								loop:true,
							},
							769: {
								items: 3
							}
						}
				});
				},
				complete: function () {
					$('#packages_loader').addClass('d-none');
				}
				});

            	},
				complete: function () {
					$('#packages_loader').addClass('d-none');
				}
				});
		}
    });

    $('body').on('click', 'div.package_box', function() {
        var package_id = $(this).attr('custom-data-package-id');
		$('.package_title.container').removeClass('active');
		$('.type_title.pack').removeClass('active');
		$(this).find('.type_title.pack').addClass('active');
		$(this).find('.package_title.container').addClass('active');
        $('#package_error').addClass('d-none');


        $('#package_id').remove();
        $('#custom_booking_step_1').append('<input type="hidden" name="package_id" id="package_id" value="'+package_id+'">');
    });

	function checkPackageParameters() {
		const URL_CONCAT = $('meta[name="index"]').attr('content');
		const package_id  = $('input[name=package_id]').val();
		return new Promise(function(resolve, reject) {
			$.ajax({
			type: 'GET',
			url: `${URL_CONCAT}/check-user-package-parameters`,
			data: { package_id: package_id },
				success: function(response) {
					resolve(response);
					return false;
				},
			})
		});
	}

    $('#custom_booking_step_1').submit(async function(e){
		e.preventDefault();
        var check;
        check = true;
        var first_name;
        first_name = $('input[name=first_name]').val();
        var last_name;
        last_name = $('input[name=last_name]').val();
        var phone_number;
        phone_number = $('input[name=phone_number]').val();
        var email;
        email = $('input[name=email]').val();
		$('#welcome-message-error').addClass('d-none').empty();

        if(first_name === "") {
            $('#first_name').addClass('is-invalid');
            $('#first_name_error_holder').removeClass('d-none');
            check = false;
        }

        if(last_name === "") {
            $('#last_name').addClass('is-invalid');
            $('#last_name_error_holder').removeClass('d-none');
            check = false;
        }

        if(phone_number === "") {
            $('#phone_number').addClass('is-invalid');
            $('#phone_number_error_holder').removeClass('d-none');
            check = false;
        }

        if(email === "") {
            $('#email').addClass('is-invalid');
            $('#email_error_holder').removeClass('d-none');
            check = false;
        }

        var emailReg = /^([\w-.]+@([\w-]+\.)+[\w-]{2,4})?$/;
        if(!emailReg.test(email)) {
            $('#email').addClass('is-invalid');
            $('#email_error_holder').removeClass('d-none');
            check = false;
        }

        if(check === false) {
            $("html, body").animate({ scrollTop: 0 }, "slow");
            return false;
        }

        var package_id  = $('input[name=package_id]').val();
		
        if(package_id === undefined) {
            $('#package_error').removeClass('d-none');
            $("html, body").animate({ scrollTop: $(document).height()-$(window).height() });
            check = false;
        }

		const res = await checkPackageParameters();
        if(res.success) {
            $('#welcome-message-error').removeClass('d-none').html(res.message);           
            check = false;
        }
        if(check === false) {
            return false;
        }
		var enviar = document.getElementById("custom_booking_step_1");
		enviar.submit();
    });
 


	</script>


@endsection