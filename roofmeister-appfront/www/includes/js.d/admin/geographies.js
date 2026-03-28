geographies = {};

geographies.init_actions = function () {
// Click event for adding new geographies
	$( 'input[data-function=add-geography]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			load_page( 'admin/geographies', {
				'task' : 'addedit'
			}, geographies.form_actions );

			e.handled = true;
		}
	} );

// Click event for editing an existing geography
	$( 'tr' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			load_page( 'admin/geographies', {
				'task'         : 'addedit',
				'geography_id' : $( this ).data( 'geography_id' )
			}, geographies.form_actions );

			e.handled = true;
		}
	} );
}

geographies.form_actions = function () {
// Click event for back button
	$( 'input[data-function=back-geography]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			e.handled = true;

			back_page();
		}
	} );

	$( 'input[data-function=save]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {

			if ( typeof $( '[name=geography_id]' ).val() !== 'undefined' ) {

				geographies.geography_save( $( '[name=geography_id]' ).val() );
			} else {

				geographies.geography_save();
			}

			e.handled = true;
		}
	} );
}

geographies.geography_save = function ( geography_id ) {
/**
* Geography Save
* Takes changes from the geographies form and submits them to the server
* to be saved.
*/
	var values = {
		'task' : 'save'
	};

	var security = [];

	$.each( $( 'form[name=geography_save]' ).serializeArray(), function ( i, field ) {
		values[ field.name ] = field.value;
	} );

	if ( typeof geography_id !== 'undefined' ) {
		values.geography_id = geography_id;
	}

	$.post( '/handlers/admin/geographies.php', values, function ( result ) {

		var result_json = $.parseJSON( result );

		if ( result_json.status == 'success' ) {
			load_page( 'admin/geographies', {}, geographies.init_actions );
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
				'function' : 'geography_save',
				'error'    : result_json.errors,
				'data'     : result_json.data
			} );
		}

	} );
}
