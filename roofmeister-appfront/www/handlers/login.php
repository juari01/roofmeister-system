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

// Submit the login attempt
	$login = new jsonrpc\method( 'user.login' );
	$login->param( 'api_token', $jsonrpc_api_token );
	$login->param( 'username',  $_POST['username'] );
	$login->param( 'password',  $_POST['password'] );
	$login->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $login );
	$jsonrpc_client->send();

	$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

	if ( $result[ $login->id ]['status'] ) {
	// Login successful, store session and redirect to main page

		$_SESSION['user'] = $result[ $login->id ]['data']['user'];

		echo 'success';
	} else {
	// Login unsuccessful, store email in session and redirect to login page

		$_SESSION['values']['username'] = $_POST['username'];

		echo 'Invalid username and/or password.';
	}

?>
