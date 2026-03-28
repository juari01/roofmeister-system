<?php

// Close session writing
	session_write_close();

// Start JSON-RPC Request


	$customer_note_save = new jsonrpc\method( 'customer.save_note' );
	$customer_note_save->param( 'api_token',  $jsonrpc_api_token );
	$customer_note_save->param( 'hash',       $_SESSION['user']['hash'] );
	$customer_note_save->param( 'user_id',	  $_SESSION['user']['user_id'] );
	$customer_note_save->param( 'note',		  $_POST['note'] );
	$customer_note_save->param( 'customer_id',$_POST['customer_id'] );
	$customer_note_save->param( 'note_id',	  $_POST['note_id'] );


	$customer_note_save->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $customer_note_save );
	$jsonrpc_client->send();

	try {
		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

		if ( $result[ $customer_note_save->id ]['status'] ) {

			echo json_encode( [
				'status'  => 'success',
				'content' => '',
				'data'    => $result[ $customer_note_save->id ]['data']
			] );
		} else {
			echo json_encode( [
				'status' => 'error',
				'errors' => $result[ $customer_note_save->id ]['message'],
				'data'   => $result[ $customer_note_save->id ]['data']
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
