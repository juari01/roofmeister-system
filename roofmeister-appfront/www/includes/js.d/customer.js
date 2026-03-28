customer = {};

customer.init_actions = function () {
// Click event for adding new customer
	$( 'input[data-function=add-customer]' ).on( 'click', function( e ) {
			
		e.preventDefault();
		if ( e.handled !== true ) {
			load_page( 'customer', {
				'task' : 'addedit'
			}, customer.form_actions );

			e.handled = true;
		}
	} );

// Click event for editing an existing group
	$( 'tr' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			load_page( 'customer', {
				'task'     : 'addedit',
				'customer_id' : $( this ).data( 'customer_id' )
			}, customer.form_actions );

			e.handled = true;
		}
	} );
}

customer.form_actions = function () {
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

			if ( typeof $( '[name=customer_id]' ).val() !== 'undefined' ) {

				customer.customer_save( $( '[name=customer_id]' ).val() );
			} else {

				customer.customer_save();
			}

			e.handled = true;
		}
	} );
}

customer.customer_save = function ( customer_id ) {
/**
* Group Save
* Takes changes from the customer form and submits them to the server
* to be saved.
*/
	var values = {
		'task' : 'save'
	};

	var security = [];

	$.each( $( 'form[name=customer_save]' ).serializeArray(), function ( i, field ) {
		values[ field.name ] = field.value;
	} );

// Get the selected security of this group
	$( 'input:checkbox[name=security]:checked' ).each( function () {
		security.push( $( this ).val() );
	} );

	values.security = security;

	if ( typeof customer_id !== 'undefined' ) {
		values.customer_id = customer_id;
	}

	$.post( '/handlers/customer.php', values, function ( result ) {

		var result_json = $.parseJSON( result );

		if ( result_json.status == 'success' ) {
			load_page( 'customer', {}, customer.init_actions );
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
				'function' : 'customer_save',
				'error'    : result_json.errors,
				'data'     : result_json.data
			} );
		}

	} );
}
