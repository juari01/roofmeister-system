<?php

// Close session writing
	session_write_close();

// Start JSON-RPC Request
	$file_move = new jsonrpc\method( 'file.file_move' );
	$file_move->param( 'api_token', $jsonrpc_api_token );
	$file_move->param( 'hash',      $_SESSION['user']['hash'] );


	$file_move->param( [
		'values' => $_POST
	] );


	if ( isset( $_POST['file_id'] ) ) {
		$file_move->param( [
			'where' => [
				'file_id' => $_POST['file_id']
			]
		] );
	}

	$file_move->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $file_move );
	$jsonrpc_client->send();

		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

		if ( $result[ $file_move->id ]['status'] ) {

			echo json_encode( [
				'status'  => 'success',
				'content' => '',
				'data'    => $result[ $file_move->id ]['data']
			] );
		} else {
			echo json_encode( [
				'status' => 'error',
				'errors' => $result[ $file_move->id ]['message'],
				'data'   => $result[ $file_move->id ]['data']
			] );
		}



?>
