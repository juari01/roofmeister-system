<?php

// Close session writing
	session_write_close();

// Start JSON-RPC Request
	$prop_contact_save = new jsonrpc\method( 'property.savepropcontact' );
	$prop_contact_save->param( 'api_token', $jsonrpc_api_token );
	$prop_contact_save->param( 'hash',      $_SESSION['user']['hash'] );
	
	if ( !isset( $_POST['active'] ) ) {
		$_POST['active'] = 0;
	}

	$prop_contact_save->param( [
		'values' => $_POST
	] );

	if ( isset( $_POST['contact_id'] ) ) {
		$prop_contact_save->param( [
			'where' => [
				'contact_id' => $_POST['contact_id']
			]
		] );
	}

	$prop_contact_save->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $prop_contact_save );
	$jsonrpc_client->send();

	try {
		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

		if ( $result[ $prop_contact_save->id ]['status'] ) {

			echo json_encode( [
				'status'  => 'success',
				'content' => '',
				'data'    => $result[ $prop_contact_save->id ]['data']
			] );
		} else {
			echo json_encode( [
				'status' => 'error',
				'errors' => $result[ $prop_contact_save->id ]['message'],
				'data'   => $result[ $prop_contact_save->id ]['data']
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
