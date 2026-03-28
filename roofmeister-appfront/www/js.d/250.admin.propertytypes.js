propertytypes = {};

propertytypes.init_actions = function () {

	$( 'input[data-function=add-propertytype]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			load_page( 'admin/propertytypes', {
				'task' : 'addedit'
			}, propertytypes.form_actions );

			e.handled = true;
		}
	} );


	$( 'tr' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			load_page( 'admin/propertytypes', {
				'task'         : 'addedit',
				'propertytype_id' : $( this ).data( 'propertytype_id' )
			}, propertytypes.form_actions );

			e.handled = true;
		}
	} );
}

propertytypes.form_actions = function () {
// Click event for back button
	$( 'input[data-function=back-propertytypes]' ).on( 'click', function( e ) {
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

				propertytypes.propertytypes_save( $( '[name=type_id]' ).val() );
			} else {

				propertytypes.propertytypes_save();
			}

			e.handled = true;
		}
	} );
}

propertytypes.propertytypes_save = function ( type_id ) {
/**
* propertytypes Save
* Takes changes from the propertytypes form and submits them to the server
* to be saved.
*/
	var values = {
		'task' : 'save'
	};


	$.each( $( 'form[name=propertytype_save]' ).serializeArray(), function ( i, field ) {
		values[ field.name ] = field.value;
	} );

	if ( typeof type_id !== 'undefined' ) {
		values.type_id = type_id;
	}

	$.post( '/handlers/admin/propertytypes.php', values, function ( result ) {

		var result_json = $.parseJSON( result );

		if ( result_json.status == 'success' ) {
			load_page( 'admin/propertytypes', {}, propertytypes.init_actions );
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
				'function' : 'propertytype_save',
				'error'    : result_json.errors,
				'data'     : result_json.data
			} );
		}

	} );
}
