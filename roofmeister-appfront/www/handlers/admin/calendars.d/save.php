<?php

// Close session writing
	session_write_close();

// Start JSON-RPC Request
	$calendar_save = new jsonrpc\method( 'admin.calendar.save' );
	$calendar_save->param( 'api_token', $jsonrpc_api_token );
	$calendar_save->param( 'hash',      $_SESSION['user']['hash'] );

	$calendar_save->param( [
		'values' => $_POST
	] );

	if ( isset( $_POST['calendar_id'] ) ) {
		$calendar_save->param( [
			'where' => [
				'calendar_id' => $_POST['calendar_id']
			]
		] );
	}

	$calendar_save->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $calendar_save );
	$jsonrpc_client->send();

	try {
		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

		if ( $result[ $calendar_save->id ]['status'] ) {

			echo json_encode( [
				'status'  => 'success',
				'content' => '',
				'data'    => $result[ $calendar_save->id ]['data']
			] );
		} else {
			echo json_encode( [
				'status' => 'error',
				'errors' => $result[ $calendar_save->id ]['message'],
				'data'   => $result[ $calendar_save->id ]['data']
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
