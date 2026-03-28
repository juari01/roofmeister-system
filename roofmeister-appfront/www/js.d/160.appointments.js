appointment = {};

appointment.init_actions = function () {
// Click event for adding new property
	$( 'input[data-function=add-appointment]' ).on( 'click', function( e ) {
		sessionStorage.removeItem("SessionAddAppntmntDshbrd");	
		e.preventDefault();

		if ( e.handled !== true ) {

			load_page( 'appointments', {
				'task' 			 : 'addedit',
				'addAppsavedisable' : 1,
				'display_option' : true
			}, appointment.form_actions );

			e.handled = true;

		}
	} );


	$( 'tr' ).on( 'click', function( e ) {

			e.preventDefault();

			if ( e.handled !== true ) {

				load_page( 'appointments', {
					'task'     		   : 'addedit',
					'appointment_idtr' : $( this ).data( 'appointment_id' )
				}, appointment.form_actions );
	
				e.handled = true;

			}
	} );


	if( typeof window.appointment === 'undefined' ) {
		window.appointment = {};
	}

	// Go to where we left off
	if( typeof window.appointment.page !== 'undefined' ) {
		appointment_search();
	}else{
		window.appointment.page = 1;
	}

	if( typeof window.appointment.search !== 'undefined' && window.appointment.search != '' ) {
		$( '.search-appointment' ).val( window.appointment.search );
	}


	$( 'span[data-function=view-page]' ).on( 'click', function() {
		window.appointment.page = $( this ).data( 'page-num' );
		appointment_search();
		
	} );

	$('.search-appointment').off().on( "keyup", function(e) {
		window.appointment.search = $( this ).val();
		window.appointment.page   = 1;

		appointment_search();
	});

	function appointment_search() {
		var values = {
			'task'   : 'index',
			'i'      : window.appointment.page,
			'search' : window.appointment.search
		};

		$.post( '/handlers/appointments.php', values, function ( result ) {
			var result_json = $.parseJSON( result ); 
		
			if ( result_json.status == 'success' ) {

				$( 'div[data-name=table-container]' ).html( result_json['content']['table'] );
				$( 'span[data-name=page-container]' ).html( result_json['content']['pages'] );

			  $( '.encrypted-text' ).each( function() {
				  let text = $( this ).data( 'text' );
				  $( this ).html( atob( text ));
			  });


			  $( 'tr' ).on( 'click', function( e ) {

				e.preventDefault();
	
				if ( e.handled !== true ) {
	
					load_page( 'appointments', {
						'task'     		   : 'addedit',
						'appointment_idtr' : $( this ).data( 'appointment_id' )
					}, appointment.form_actions );
		
					e.handled = true;
	
				}
			} );
	

			$( 'span[data-function=view-page]' ).on( 'click', function() {
				window.appointment.page = $( this ).data( 'page-num' );
		
				appointment_search();
				
				} );


			}
			else 
			{
				alert( result );
			}
		} );
	}

}


appointment.form_actions = function () {

	jQuery('#startappointment').datetimepicker({
	onShow:function( ct ){
	this.setOptions({
		maxDate:jQuery('#endappointment').val()?jQuery('#endappointment').val():false
	})
	},
	});

	jQuery('#endappointment').datetimepicker({
	onShow:function( ct ){
	this.setOptions({
		minDate:jQuery('#startappointment').val()?jQuery('#startappointment').val():false
	})
	},
	});


	$("#projectcheck").off().on( 'click', function( e ) {
		e.preventDefault();
		e.stopPropagation();

			load_page( 'appointments', {
				'task' 		   : 'addedit',
				'projectcheck' : true
			}, appointment.form_actions );

	});

	$("#compropcheck").off().on( 'click', function( e ) {
		
		e.preventDefault();
		e.stopPropagation();

			load_page( 'appointments', {
				'task' 		   : 'addedit',
				'compropcheck' : true
			}, appointment.form_actions );

		
	});

	$(".select-Appointmentproject-by-id").off().on( 'click', function( e ) {

		e.preventDefault();
		e.stopPropagation();

		var appointment_id 		= $( '[name=appointment_id]' ).val();
		var type_id 			= $( '[name=type_id]' ).val();
		var start 				= $( '[name=start]' ).val();
		var end 				= $( '[name=end]' ).val();
		var description 		= $( '[name=description]' ).val();
		
		load_page('appointments', {

			'task' 			 	 : 'selectproject',
		    'appointment_id'	 : appointment_id,
			'type_id'	 		 : type_id,
			'start' 	 		 : start,
			'end' 	 		 	 : end,
			'description' 	 	 : description

		}, appointment.form_actions );

	});

	$(".select-Appointmentcustomer-by-id").off().on( 'click', function( e ) {

		e.preventDefault();
		e.stopPropagation();

		var appointment_id 		= $( '[name=appointment_id]' ).val();
		var type_id 			= $( '[name=type_id]' ).val();
		var start 				= $( '[name=start]' ).val();
		var end 				= $( '[name=end]' ).val();
		var description 		= $( '[name=description]' ).val();
		
		load_page('appointments', {

			'task' 			 	 : 'selectcustomer',
		    'appointment_id'	 : appointment_id,
			'type_id'	 		 : type_id,
			'start' 	 		 : start,
			'end' 	 		 	 : end,
			'description' 	 	 : description

		}, appointment.form_actions );

	});

	$(".select-Appointmentproperty-by-id").off().on( 'click', function( e ) {

		e.preventDefault();
		e.stopPropagation();

		var appointment_id 		= $( '[name=appointment_id]' ).val();
		var customer_id 		= $( '[name=customer_id]' ).val();
		var customer_name 		= $( '[name=appcustomer_name]' ).val();
		var type_id 			= $( '[name=type_id]' ).val();
		var start 				= $( '[name=start]' ).val();
		var end 				= $( '[name=end]' ).val();
		var description 		= $( '[name=description]' ).val();
		
		load_page('appointments', {

			'task' 			 	 : 'selectproperty',
		    'appointment_id'	 : appointment_id,
		    'appcustomer_id'	 : customer_id,
		    'appcustomer_name'	 : customer_name,
			'type_id'	 		 : type_id,
			'start' 	 		 : start,
			'end' 	 		 	 : end,
			'description' 	 	 : description

		}, appointment.form_actions );

	});


// Click event for back button
	$( 'input[data-function=back-appointment]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			e.handled = true;

			load_page( "appointments",  {}, appointment.init_actions );
		}
	} );

		$(".back-selectcustomer").off().on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			e.handled = true;

			back_page();
		}
	} );


	$( 'input[data-function=save]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {

			var varappointment_id = $( '[name=appointment_id]' ).val();

			if  ( varappointment_id ) {

				appointment.appointment_save(varappointment_id);

			} else {

				appointment.appointment_save();
				
			}



			e.handled = true;
		}
	} );

}
	appointment.appointment_save = function (appointment_id) {

		var values = {
			'task' : 'save'
		};
	
		$.each( $( 'form[name=appointment_save]' ).serializeArray(), function ( i, field ) {
			values[ field.name ] = field.value;
		} );

		var varcheckcustomerselected = $( '[name=customer_id]' ).val();
		var varcheckprojectselected  = $( '[name=project_id]' ).val();

			values.jscustomer_id = varcheckcustomerselected;
			values.jsproject_id  = varcheckprojectselected;
	
		if ( appointment_id ) {
			values.appointment_id 	 = appointment_id;
			values.saveEditAppointment_id = true;
		} 
	
		$.post( '/handlers/appointments.php', values, function ( result ) {
	
			var result_json = $.parseJSON( result );
	
			if ( result_json.status == 'success' ) {

				let DshbrdAddAppntmnt = sessionStorage.getItem("SessionAddAppntmntDshbrd");
				
				if (DshbrdAddAppntmnt) {
					sessionStorage.removeItem("SessionAddAppntmntDshbrd");
					load_page( 'dashboard', {}, appointment.init_actions );
				} else {
					sessionStorage.removeItem("SessionAddAppntmntDshbrd");
					load_page( 'appointments', {}, appointment.init_actions );
				}

			} else if ( result_json.status == 'error' ) {
				let error_msg = '';
	
				if ( result_json.errors instanceof Array ) {
					for ( let i = 0; i < result_json.errors.length; i++ ) {
						error_msg += result_json.errors[ i ] + "\n";
					}
				} else {
					error_msg = result_json.errors
				}
	
				alert( error_msg );
			} else {
				error_handler( {
					'function' : 'appointment_save',
					'error'    : result_json.errors,
					'data'     : result_json.data
				} );
			}
	
		} );
	}

	
