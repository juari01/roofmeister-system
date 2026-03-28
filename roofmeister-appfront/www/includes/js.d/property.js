property = {};

property.init_actions = function () {
// Click event for adding new property
	$( 'input[data-function=add-property]' ).on( 'click', function( e ) {
			
		e.preventDefault();
		if ( e.handled !== true ) {
			load_page( 'property', {
				'task' : 'addedit'
			}, property.form_actions );

			e.handled = true;
		}
	} );

// Click event for editing an existing group
	$( 'tr' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			load_page( 'property', {
				'task'     : 'addedit',
				'property_id' : $( this ).data( 'property_id' )
			}, property.form_actions );

			e.handled = true;
		}
	} );
}

property.form_actions = function () {
// Click event for back button
	$( 'input[data-function=back-group]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			e.handled = true;

			back_page();
		}
	} );

	$( 'input[data-function=save]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {

			if ( typeof $( '[name=property_id]' ).val() !== 'undefined' ) {

				property.property_save( $( '[name=property_id]' ).val() );
			} else {

				property.property_save();
			}

			e.handled = true;
		}
	} );
}

property.property_save = function ( property_id ) {
/**
* Group Save
* Takes changes from the property form and submits them to the server
* to be saved.
*/
	var values = {
		'task' : 'save'
	};

	var security = [];

	$.each( $( 'form[name=property_save]' ).serializeArray(), function ( i, field ) {
		values[ field.name ] = field.value;
	} );

// Get the selected security of this group
	$( 'input:checkbox[name=security]:checked' ).each( function () {
		security.push( $( this ).val() );
	} );

	values.security = security;

	if ( typeof property_id !== 'undefined' ) {
		values.property_id = property_id;
	}

	$.post( '/handlers/property.php', values, function ( result ) {

		var result_json = $.parseJSON( result );

		if ( result_json.status == 'success' ) {
			load_page( 'property', {}, property.init_actions );
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
				'function' : 'property_save',
				'error'    : result_json.errors,
				'data'     : result_json.data
			} );
		}

	} );
}
