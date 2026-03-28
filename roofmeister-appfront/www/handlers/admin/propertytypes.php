<?php

// Common start
	include( "{$_SERVER['DOCUMENT_ROOT']}/../components/start.php" );

// Don't allow calling this handler directly
	if ( $_SERVER['REQUEST_METHOD'] != 'POST' ) {
		die( $_messages['no_direct_calls'] );
	}

// Setup the JSON-RPC client
	$jsonrpc_client = new jsonrpc\client();
	$jsonrpc_client->server( $config->get( 'jsonrpc\main\server' ));

// Determine which task was called
	if ( !isset( $_POST['task'] ) ) {
	// No task specified, load the index

		require_once( "propertytypes.d/index.php" );
	} else if ( file_exists( "propertytypes.d/{$_POST['task']}.php" ) ) {
	// Task defined by name

		require_once( "propertytypes.d/{$_POST['task']}.php" );
	} else {
	// The requested task was not found

		echo json_encode( [
			'status' => 'error',
			'errors' => 'Invalid Task',
			'data'   => 'Invalid Task'
		] );
	}

?>
