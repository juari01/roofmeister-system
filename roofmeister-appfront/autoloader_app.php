<?php

// Application class autoloader
	spl_autoload_register( function ( $name ) {

		$class_file = __DIR__ . '/classes/' . str_replace( '\\', '/', $name . '.php' );

		if ( file_exists( $class_file ) ) {
			require( $class_file );
		}
	} );

?>
