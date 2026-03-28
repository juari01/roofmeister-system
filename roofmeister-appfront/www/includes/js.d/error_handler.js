
	function error_handler( values ) {
	/**
	 * Error handler
	 */

		console.log( 'ERROR HANDLER' );

		console.log( values );

		alert( '"' + values.function + '": ' + values.error );

		if ( confirm( 'Logout?' )) {
			logout();
		}
	}

