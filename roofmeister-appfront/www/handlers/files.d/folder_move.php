<?php

// Close session writing
	session_write_close();

// Start JSON-RPC Request
	$folder_move = new jsonrpc\method( 'file.folder_move' );
	$folder_move->param( 'api_token', $jsonrpc_api_token );
	$folder_move->param( 'hash',      $_SESSION['user']['hash'] );


	$folder_move->param( [
		'values' => $_POST
	] );


	if ( isset( $_POST['folder_id'] ) ) {
		$folder_move->param( [
			'where' => [
				'folder_id' => $_POST['folder_id']
			]
		] );
	}

	$folder_move->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $folder_move );
	$jsonrpc_client->send();

		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

		if ( $result[ $folder_move->id ]['status'] ) {

			echo json_encode( [
				'status'  => 'success',
				'content' => '',
				'data'    => $result[ $folder_move->id ]['data']
			] );
		} else {
			echo json_encode( [
				'status' => 'error',
				'errors' => $result[ $folder_move->id ]['message'],
				'data'   => $result[ $folder_move->id ]['data']
			] );
		}

?>
