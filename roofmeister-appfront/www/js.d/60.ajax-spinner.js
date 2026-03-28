
	$( document ).ajaxStart( function() {
		window.spinner_timeout = setTimeout( function() {
			var height = 64;
			var width  = 128;

			$( 'body' ).append( $( '<div>' )
				.attr( 'id', 'ajax-spinner' )
				.css( {
					'height'        : height + 'px',
					'left'          : (( $( window ).width() - width ) / 2 ) + 'px',
					'position'      : 'absolute',
					'top'           : (( $( window ).height() - height ) / 2 + $( document ).scrollTop() ) + 'px',
					'width'         : width + 'px',
					'z-index'       : '1001'
				} )
				.append( $( '<img>' )
					.attr( 'src', '/images/ajax-spinner.gif' )
					.attr( 'alt', 'AJAX Spinner' )
				)
			)
			.append( $( '<div>' )
				.attr( 'id', 'ajax-spinner-background' )
				.css( {
					'bottom'   : '0',
					'left'     : '0',
					'position' : 'absolute',
					'right'    : '0',
					'top'      : '0',
					'z-index'  : '1000'
				} )
			);
		}, 200 );
	} )
	.ajaxStop( function() {
		clearTimeout( window.spinner_timeout );

		$( '#ajax-spinner' ).remove();
		$( '#ajax-spinner-background' ).remove();
	} );

