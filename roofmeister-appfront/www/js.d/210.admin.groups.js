groups = {};

groups.init_actions = function () {
// Click event for adding new groups
	$( 'input[data-function=add-group]' ).on( 'click', function( e ) {

		e.preventDefault();
		if ( e.handled !== true ) {
			load_page( 'admin/groups', {
				'task' : 'addedit'
			}, groups.form_actions );

			e.handled = true;
		}
	} );

// Click event for editing an existing group
	$( 'tr' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			load_page( 'admin/groups', {
				'task'     : 'addedit',
				'group_id' : $( this ).data( 'group_id' )
			}, groups.form_actions );

			e.handled = true;
		}
	} );
}

groups.form_actions = function () {
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

			if ( typeof $( '[name=group_id]' ).val() !== 'undefined' ) {

				groups.group_save( $( '[name=group_id]' ).val() );
			} else {

				groups.group_save();
			}

			e.handled = true;
		}
	} );
}

groups.group_save = function ( group_id ) {
/**
* Group Save
* Takes changes from the groups form and submits them to the server
* to be saved.
*/
	var values = {
		'task' : 'save'
	};

	var security = [];

	$.each( $( 'form[name=group_save]' ).serializeArray(), function ( i, field ) {
		values[ field.name ] = field.value;
	} );

// Get the selected security of this group
	$( 'input:checkbox[name=security]:checked' ).each( function () {
		security.push( $( this ).val() );
	} );

	values.security = security;

	if ( typeof group_id !== 'undefined' ) {
		values.group_id = group_id;
	}

	$.post( '/handlers/admin/groups.php', values, function ( result ) {

		var result_json = $.parseJSON( result );

		if ( result_json.status == 'success' ) {
			load_page( 'admin/groups', {}, groups.init_actions );
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
				'function' : 'group_save',
				'error'    : result_json.errors,
				'data'     : result_json.data
			} );
		}

	} );
}
