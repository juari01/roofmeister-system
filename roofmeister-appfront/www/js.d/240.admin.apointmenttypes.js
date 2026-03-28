appointmenttypes = {};

appointmenttypes.init_actions = function () {

	$( 'input[data-function=add-appointment]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			load_page( 'admin/appointmenttypes', {
				'task' : 'addedit'
			}, appointmenttypes.form_actions );

			e.handled = true;
		}
	} );


	$( 'tr' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			load_page( 'admin/appointmenttypes', {
				'task'         : 'addedit',
				'appointment_id' : $( this ).data( 'appointment_id' )
			}, appointmenttypes.form_actions );

			e.handled = true;
		}
	} );
}

appointmenttypes.form_actions = function () {
// Click event for back button
	$( 'input[data-function=back-appointment]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			e.handled = true;

			back_page();
		}
	} );

	$( 'input[data-function=save]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {

			if ( typeof $( '[name=type_id]' ).val() !== 'undefined' ) {

				appointmenttypes.appointmenttypes_save( $( '[name=type_id]' ).val() );
			} else {

				appointmenttypes.appointmenttypes_save();
			}

			e.handled = true;
		}
	} );
}

appointmenttypes.appointmenttypes_save = function ( type_id ) {
/**
* appointmenttypes Save
* Takes changes from the appointmenttypes form and submits them to the server
* to be saved.
*/
	var values = {
		'task' : 'save'
	};


	$.each( $( 'form[name=appointment_save]' ).serializeArray(), function ( i, field ) {
		values[ field.name ] = field.value;
	} );

	if ( typeof type_id !== 'undefined' ) {
		values.type_id = type_id;
	}

	$.post( '/handlers/admin/appointmenttypes.php', values, function ( result ) {

		var result_json = $.parseJSON( result );

		if ( result_json.status == 'success' ) {
			load_page( 'admin/appointmenttypes', {}, appointmenttypes.init_actions );
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
