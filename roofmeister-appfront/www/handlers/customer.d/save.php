<?php

// Close session writing
	session_write_close();

// Start JSON-RPC Request
	$customer_save = new jsonrpc\method( 'customer.save' );
	$customer_save->param( 'api_token', $jsonrpc_api_token );
	$customer_save->param( 'hash',      $_SESSION['user']['hash'] );


	$customer_save->param( [
		'values' => $_POST
	] );


	if ( isset( $_POST['customer_id'] )) {
		$customer_save->param( [
			'where' => [
				'customer_id' => $_POST['customer_id']
			]
		] );
	}

	$customer_save->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $customer_save );
	$jsonrpc_client->send();

	try {
		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

		if ( $result[ $customer_save->id ]['status'] ) {

			echo json_encode( [
				'status'  => 'success',
				'content' => '',
				'data'    => $result[ $customer_save->id ]['data']
			] );
		} else {
			echo json_encode( [
				'status' => 'error',
				'errors' => $result[ $customer_save->id ]['message'],
				'data'   => $result[ $customer_save->id ]['data']
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
