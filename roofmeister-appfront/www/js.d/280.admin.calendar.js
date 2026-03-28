calendars = {};

calendars.init_actions = function () {

	$( 'input[data-function=add-calendars]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			load_page( 'admin/calendars', {
				'task' : 'addedit'
			}, calendars.form_actions );

			e.handled = true;
		}
	} );


	$( 'tr' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			load_page( 'admin/calendars', {
				'task'         : 'addedit',
				'calendar_id' : $( this ).data( 'calendar_id' )
			}, calendars.form_actions );

			e.handled = true;
		}
	} );
}

calendars.form_actions = function () {
// Click event for back button
	$( 'input[data-function=back-calendar]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			e.handled = true;

			back_page();
		}
	} );

	$( 'input[data-function=save]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {

			if ( typeof $( '[name=calendar_id]' ).val() !== 'undefined' ) {

				calendars.calendars_save( $( '[name=calendar_id]' ).val() );
			} else {

				calendars.calendars_save();
			}

			e.handled = true;
		}
	} );
}

calendars.calendars_save = function ( calendar_id ) {
/**
* calendars Save
* Takes changes from the calendars form and submits them to the server
* to be saved.
*/
	var values = {
		'task' : 'save'
	};


	$.each( $( 'form[name=calendar_save]' ).serializeArray(), function ( i, field ) {
		values[ field.name ] = field.value;
	} );

	if ( typeof calendar_id !== 'undefined' ) {
		values.calendar_id = calendar_id;
	}

	$.post( '/handlers/admin/calendars.php', values, function ( result ) {

		var result_json = $.parseJSON( result );

		if ( result_json.status == 'success' ) {
			load_page( 'admin/calendars', {}, calendars.init_actions );
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
				'function' : 'calendars_save',
				'error'    : result_json.errors,
				'data'     : result_json.data
			} );
		}

	} );
}
