
	var history_count = 0;

	var page_history_data = [];

	function load_page( page, values, post_load ) {
	/**
	 * Load Page
	 * Loads the specified page.
	 */

		let page_data = {};

		page_data.page      = page;
		page_data.values    = values;
		page_data.post_load = post_load;

		page_history_data.push( page_data );

		if ( typeof values === 'undefined' ) {
			var values = {};
		}

		if ( typeof ( history.pushState ) != 'undefined' && history_count != 0 ) {
			history.pushState( null, null, ( '/' + page ));
		}

		var page_link = page.split( '/' );

		if ( !isNaN( page_link[ page_link.length - 1 ] )) {
			page = page.replace( '/' + page_link[ page_link.length - 1 ], '' );
		}

		$.post( '/handlers/' + page + '.php', values, function( result ) {
			try {
				var result_json = $.parseJSON( result );

				if ( result_json.status ) {

					//load_notifications();
					//set_current_nav( page );

					$( '#content' ).html( result_json.content );

					if ( typeof post_load === 'function' ) {
						post_load();
					}
				} else if ( typeof result_json.alert !== 'undefined' ) {
					alert( result_json.alert );
				} else {
					error_handler( {
						'function' : 'load_page',
						'error'    : result_json.errors,
						'result'   : result
					} );

					console.log( page, values, post_load );
				}
			} catch ( e ) {
				error_handler( {
					'function' : 'load_page',
					'error'    : e.name + ": '" + e.message,
					'result'   : result
				} );

				console.log( page, values, post_load );
			}
		} );

		history_count++;
	}

	function back_page() {

		console.log( page_history_data );

		let page_data = page_history_data[ page_history_data.length - 2 ];

		load_page( page_data.page, page_data.values, page_data.post_load );
	}

