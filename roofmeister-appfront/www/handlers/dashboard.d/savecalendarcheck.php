<?php

// Close session writing
	session_write_close();

// Start JSON-RPC Request
	$customer_check = new jsonrpc\method( 'dashboard.savecalendarcheck' );
	$customer_check->param( 'api_token', $jsonrpc_api_token );
	$customer_check->param( 'hash',      $_SESSION['user']['hash'] );
	$customer_check->param( 'user_id',   $_SESSION['user']['user_id'] );
	$customer_check->param( [
		'values' => $_POST
	] );


	$customer_check->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $customer_check );
	$jsonrpc_client->send();

	try {
		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

		if ( $result[ $customer_check->id ]['status'] ) {

			echo json_encode( [
				'status'  => 'success',
				'content' => '',
				'data'    => $result[ $customer_check->id ]['data']
			] );
		} else {
			echo json_encode( [
				'status' => 'error',
				'errors' => $result[ $customer_check->id ]['message'],
				'data'   => $result[ $customer_check->id ]['data']
			] );
		}
	} catch ( Exception $e ) {
		error_log( 'DEBUG: ' . $jsonrpc_client->result_raw );

		echo json_encode( [
			'status'  => FALSE,
			'errors' => $jsonrpc_client->result_raw
		] );
	}

?>
