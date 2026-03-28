<?php

// Close session writing
	session_write_close();

// Start JSON-RPC Request


	$property_note_save = new jsonrpc\method( 'property.save_note' );
	$property_note_save->param( 'api_token',  $jsonrpc_api_token );
	$property_note_save->param( 'hash',       $_SESSION['user']['hash'] );
	$property_note_save->param( 'user_id',	  $_SESSION['user']['user_id'] );
	$property_note_save->param( 'note',		  $_POST['note'] );
	$property_note_save->param( 'property_id',$_POST['property_id'] );
	$property_note_save->param( 'note_id',	  $_POST['note_id'] );


	$property_note_save->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $property_note_save );
	$jsonrpc_client->send();

	try {
		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

		if ( $result[ $property_note_save->id ]['status'] ) {

			echo json_encode( [
				'status'  => 'success',
				'content' => '',
				'data'    => $result[ $property_note_save->id ]['data']
			] );
		} else {
			echo json_encode( [
				'status' => 'error',
				'errors' => $result[ $property_note_save->id ]['message'],
				'data'   => $result[ $property_note_save->id ]['data']
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
