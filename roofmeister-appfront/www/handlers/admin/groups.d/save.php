<?php

// Close session writing
	session_write_close();

// Start JSON-RPC Request
	$group_save = new jsonrpc\method( 'admin.group.save' );
	$group_save->param( 'api_token', $jsonrpc_api_token );
	$group_save->param( 'hash',      $_SESSION['user']['hash'] );

	if ( !isset( $_POST['active'] )) {
		$_POST['active'] = 0;
	}

	$group_save->param( [
		'values' => $_POST
	] );

	if ( isset( $_POST['group_id'] )) {
		$group_save->param( [
			'where' => [
				'group_id' => $_POST['group_id']
			]
		] );
	}

	$group_save->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $group_save );
	$jsonrpc_client->send();

	try {
		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

		if ( $result[ $group_save->id ]['status'] ) {

			echo json_encode( [
				'status'  => 'success',
				'content' => '',
				'data'    => $result[ $group_save->id ]['data']
			] );
		} else {
			echo json_encode( [
				'status' => 'error',
				'errors' => $result[ $group_save->id ]['message'],
				'data'   => $result[ $group_save->id ]['data']
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
