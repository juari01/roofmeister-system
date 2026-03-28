<?php

	session_start();

// Include Atlas autoloader
	require( __DIR__ . '/../autoloader_atlas.php' );

// Include App autoloader
	require( __DIR__ . '/../autoloader_app.php' );

	require( $_SERVER['DOCUMENT_ROOT'] . '/../templates/form.php' );

// Read application configuration
    $config = new \Atlas\Config( file_get_contents( __DIR__ . '/../config.ini' ));


// Calculate JSON-RPC client token
	$jsonrpc_api_token = $config->get( 'jsonrpc\main\key' ) . crypt( $config->get( 'jsonrpc\main\pass' ), '$6$' . substr( md5( uniqid( rand(), TRUE )), 0, 16 ));

// Load function files
	foreach ( glob( "{$_SERVER['DOCUMENT_ROOT']}/includes/functions.d/*.php" ) as $function ) {

		if ( substr( $function, 0, 1 ) != '.' ) {
			include( $function );
		}
	}

// JSON-RPC setup
	include( $config->get( 'jsonrpc\client' ));

// Messages
	$_messages = array(
		'no_direct_calls' => 'Do not call this script directly.'
	);

?>
