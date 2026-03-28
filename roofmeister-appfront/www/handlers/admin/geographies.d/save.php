<?php

// Close session writing
	session_write_close();

// Start JSON-RPC Request
	$geography_save = new jsonrpc\method( 'admin.geography.save' );
	$geography_save->param( 'api_token', $jsonrpc_api_token );
	$geography_save->param( 'hash',      $_SESSION['user']['hash'] );

	if ( !isset( $_POST['active'] )) {
		$_POST['active'] = 0;
	}

	$geography_save->param( [
		'values' => $_POST
	] );

	if ( isset( $_POST['geography_id'] )) {
		$geography_save->param( [
			'where' => [
				'geography_id' => $_POST['geography_id']
			]
		] );
	}

	$geography_save->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $geography_save );
	$jsonrpc_client->send();

	try {
		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

		if ( $result[ $geography_save->id ]['status'] ) {

			echo json_encode( [
				'status'  => 'success',
				'content' => '',
				'data'    => $result[ $geography_save->id ]['data']
			] );
		} else {
			echo json_encode( [
				'status' => 'error',
				'errors' => $result[ $geography_save->id ]['message'],
				'data'   => $result[ $geography_save->id ]['data']
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
