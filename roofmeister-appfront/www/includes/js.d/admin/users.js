users = {};

users.user_save = function ( user_id ) {
/**
* User Save
* Takes changes from the users form and submits them to the server
* to be saved.
*/
	var values = {
		'task' : 'save'
	};

	var group = [];

	$.each( $( 'form[name=user_save]' ).serializeArray(), function ( i, field ) {
		values[ field.name ] = field.value;
	} );

// Get the selected group of this user
	$( 'input:checkbox[name=group]:checked' ).each( function () {
		group.push( $( this ).val( ) );
	} );

	values.group = group;

	if ( typeof user_id !== 'undefined' ) {
		values.user_id = user_id;
	}

	$.post( '/handlers/admin/users.php', values, function ( result ) {

		var result_json = $.parseJSON( result );

		if ( result_json.status === true ) {
			load_page( 'admin/users', {}, users.init_actions );
		} else if ( result_json.status === false ) {
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
				'function' : 'user_save',
				'error'    : result_json.errors,
				'data'     : result_json.data
			} );
		}

	} );
}

users.form_actions = function () {
// Click event for back button
	$( 'input[data-function=back-user]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			back_page();

			e.handled = true;
		}
	} );

	$( 'input[data-function=save]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {

			if ( typeof $( '[name=user_id]' ).val() !== 'undefined' ) {

				users.user_save( $( '[name=user_id]' ).val() );
			} else {

				users.user_save();
			}

			e.handled = true;
		}
	} );
}

users.init_actions = function () {

// Click event for adding new users
	$( 'input[data-function=add-user]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {

			load_page( 'admin/users', {
				'task' : 'addedit'
			}, users.form_actions );

			e.handled = true;
		}
	} );

// Click event for editing an existing user
	$( 'tr' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {

			load_page( 'admin/users', {
				'task'    : 'addedit',
				'user_id' : $( this ).data( 'user_id' )
			}, users.form_actions );

			e.handled = true;
		}
	} );
}

