<?php

// Close session writing
	session_write_close();

// Start JSON-RPC Request
	$property_save = new jsonrpc\method( 'property.save' );
	$property_save->param( 'api_token', $jsonrpc_api_token );
	$property_save->param( 'hash',      $_SESSION['user']['hash'] );

	$property_save->param( [
		'values' => $_POST
	] );

	if ( isset( $_POST['property_id'] )) {
		$property_save->param( [
			'where' => [
				'property_id' => $_POST['property_id']
			]
		] );
	}

	$property_save->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $property_save );
	$jsonrpc_client->send();

	try {
		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

		if ( $result[ $property_save->id ]['status'] ) {

			echo json_encode( [
				'status'  => 'success',
				'content' => '',
				'data'    => $result[ $property_save->id ]['data']
			] );
		} else {
			echo json_encode( [
				'status' => 'error',
				'errors' => $result[ $property_save->id ]['message'],
				'data'   => $result[ $property_save->id ]['data']
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
