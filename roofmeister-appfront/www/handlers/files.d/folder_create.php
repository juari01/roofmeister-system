<?php

// Close session writing
	session_write_close();

// Start JSON-RPC Request
	$folder_save = new jsonrpc\method( 'file.folder_create' );
	$folder_save->param( 'api_token', $jsonrpc_api_token );
	$folder_save->param( 'hash',      $_SESSION['user']['hash'] );


	$folder_save->param( [
		'values' => $_POST
	] );


	 if ( isset( $_POST['folder_id'] ) ) {
            $folder_create->param( [
                'where' => [
                    'folder_id' => $_POST['folder_id']
                ]
            ] );
        }

	$folder_save->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $folder_save );
	$jsonrpc_client->send();

	try {
		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

		if ( $result[ $folder_save->id ]['status'] ) {

			echo json_encode( [
				'status'  => 'success',
				'content' => '',
				'data'    => $result[ $folder_save->id ]['data']
			] );
		} else {
			echo json_encode( [
				'status' => 'error',
				'errors' => $result[ $folder_save->id ]['message'],
				'data'   => $result[ $folder_save->id ]['data']
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
