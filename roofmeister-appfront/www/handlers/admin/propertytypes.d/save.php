<?php

// Close session writing
	session_write_close();

// Start JSON-RPC Request
	$propertytypes_save = new jsonrpc\method( 'admin.propertytypes.save' );
	$propertytypes_save->param( 'api_token', $jsonrpc_api_token );
	$propertytypes_save->param( 'hash',      $_SESSION['user']['hash'] );

	if ( !isset( $_POST['active'] ) ) {
		$_POST['active'] = 0;
	}

	$propertytypes_save->param( [
		'values' => $_POST
	] );

	if ( isset( $_POST['type_id'] ) ) {
		$propertytypes_save->param( [
			'where' => [
				'type_id' => $_POST['type_id']
			]
		] );
	}

	$propertytypes_save->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $propertytypes_save );
	$jsonrpc_client->send();

	try {
		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

		if ( $result[ $propertytypes_save->id ]['status'] ) {

			echo json_encode( [
				'status'  => 'success',
				'content' => '',
				'data'    => $result[ $propertytypes_save->id ]['data']
			] );
		} else {
			echo json_encode( [
				'status' => 'error',
				'errors' => $result[ $propertytypes_save->id ]['message'],
				'data'   => $result[ $propertytypes_save->id ]['data']
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
