<?php

// Close session writing
	session_write_close();

// Start JSON-RPC Request

	if ( !empty( $_POST['jscustomer_id'] ) ) {
		$appointment_save = new jsonrpc\method( 'appointments.savecustomerproperty' );
	}
	if ( !empty( $_POST['jsproject_id'] ) ) {
		$appointment_save = new jsonrpc\method( 'appointments.saveproject' );
	}
		$appointment_save->param( 'api_token', $jsonrpc_api_token );
		$appointment_save->param( 'hash',      $_SESSION['user']['hash'] );
		$appointment_save->param( 'user_id',   $_SESSION['user']['user_id'] );
		$appointment_save->param( 'appointment_id', isset( $_POST['appointment_id'] ) ? $_POST['appointment_id'] : '' );

		
		$appointment_save->param( [
			'values' => $_POST
		] );
	
	if ( isset( $_POST['appointment_id'] ) && isset( $_POST['saveEditAppointment_id'] )  ) {
		$appointment_save->param( [
			'where' => [
				'appointment_id' => $_POST['appointment_id']
			]
		] );
	}

	$appointment_save->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $appointment_save );
	$jsonrpc_client->send();

	try {
		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

		if ( $result[ $appointment_save->id ]['status'] ) {

			echo json_encode( [
				'status'  => 'success',
				'content' => '',
				'data'    => $result[ $appointment_save->id ]['data']
			] );
		} else {
			echo json_encode( [
				'status' => 'error',
				'errors' => $result[ $appointment_save->id ]['message'],
				'data'   => $result[ $appointment_save->id ]['data']
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
