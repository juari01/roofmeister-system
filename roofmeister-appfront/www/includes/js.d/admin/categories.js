categories = {};

categories.init_actions = function () {
// Click event for adding new categories
	$( 'input[data-function=add-category]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			load_page( 'admin/categories', {
				'task' : 'addedit'
			}, categories.form_actions );

			e.handled = true;
		}
	} );

// Click event for editing an existing category
	$( 'tr' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			load_page( 'admin/categories', {
				'task'        : 'addedit',
				'category_id' : $( this ).data( 'category_id' )
			}, categories.form_actions );

			e.handled = true;
		}
	} );
}

categories.form_actions = function () {
// Click event for back button
	$( 'input[data-function=back-category]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			e.handled = true;

			back_page();
		}
	} );

	$( 'input[data-function=save]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {

			if ( typeof $( '[name=category_id]' ).val() !== 'undefined' ) {

				categories.category_save( $( '[name=category_id]' ).val() );
			} else {

				categories.category_save();
			}

			e.handled = true;
		}
	} );
}

categories.category_save = function ( category_id ) {
/**
* Category Save
* Takes changes from the categories form and submits them to the server
* to be saved.
*/
	var values = {
		'task' : 'save'
	};

	var geography = [];

	$.each( $( 'form[name=category_save]' ).serializeArray(), function ( i, field ) {
		values[ field.name ] = field.value;
	} );

// Get the selected geography of this category
	$( 'input:checkbox[name=geography]:checked' ).each( function () {
		geography.push( $( this ).val() );
	} );

	values.geography = geography;

	if ( typeof category_id !== 'undefined' ) {
		values.category_id = category_id;
	}

	$.post( '/handlers/admin/categories.php', values, function ( result ) {

		var result_json = $.parseJSON( result );

		if ( result_json.status == 'success' ) {
			load_page( 'admin/categories', {}, categories.init_actions );
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
				'function' : 'category_save',
				'error'    : result_json.errors,
				'data'     : result_json.data
			} );
		}

	} );
}
