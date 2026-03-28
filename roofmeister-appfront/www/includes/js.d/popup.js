
	function create_popup( name, values ) {
	/**
	 * Create Popup
	 * Displays a popup box and populates it with the contents of a
	 * supplied URL.
	 */
		if ( typeof values === 'undefined' ) {
			$( '.popup[data-name=' + name + ']' ).remove();
			$( '.popup-grayout[data-name=' + name + ']' ).remove();
		} else {
			var popup = $( '<div>' )
				.addClass( 'popup ' + name )
				.attr( 'data-name', name );

			$( 'body' ).css( 'overflow', 'hidden' );

			if( typeof values.overflow !== 'undefined' ) {
				$( popup ).css( {
					display    : 'block',
					height     : values.height,
					left       : (( $( window ).width() - parseInt( values.width )) / 2 ) + 'px',
					position   : 'absolute',
					top        : (( $( window ).height() - parseInt( values.height )) / 2 + $( document ).scrollTop() ) + 'px',
					width      : values.width,
					zIndex     : 10 + $( '.popup' ).length,
					overflow   : values.overflow
				} );
			} else {
				$( popup ).css( {
					display    : 'block',
					height     : values.height,
					left       : (( $( window ).width() - parseInt( values.width )) / 2 ) + 'px',
					position   : 'absolute',
					top        : (( $( window ).height() - parseInt( values.height )) / 2 + $( document ).scrollTop() ) + 'px',
					width      : values.width,
					zIndex     : 10 + $( '.popup' ).length
				} );
			}

			$( 'body' ).append( $( '<div>' )
				.addClass( 'popup-grayout' )
				.attr( 'data-name', name )
				.css( {
					background : '#000',
					height     : '100%',
					left       : '0',
					opacity    : '0.5',
					position   : 'absolute',
					top        : '0',
					width      : '100%',
					zIndex     : 9 + $( '.popup' ).length
				} )
			).append( popup );

			if ( typeof values.url !== 'undefined' ) {
				$( '.popup[data-name=' + name + ']' ).append( $( '<div>' )
					.addClass( 'container' )
					.load( values.url )
				);
			} else if ( typeof values.content !== 'undefined' ) {
				$( '.popup[data-name=' + name + ']' ).append( $( '<div>' )
					.addClass( 'container' )
					.html( values.content )
				);
			}

			if ( values.close != false ) {
				$( '.popup[data-name=' + name + ']' ).append( $( '<div>' )
					.addClass( 'close' )
					.on( 'click', function() {
						create_popup( name );
					} )
				);
			}

			$( '.popup[data-name=' + name + ']' ).append( $( '<div>' )
				.addClass( 'title-bar' )
				.html( values.title )
			);
		}
	}

