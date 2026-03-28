<?php

// Close session writing
	session_write_close();

// Start JSON-RPC Request
	$project_save = new jsonrpc\method( 'project.save' );
	$project_save->param( 'api_token', $jsonrpc_api_token );
	$project_save->param( 'hash',      $_SESSION['user']['hash'] );


	$project_save->param( [
		'values' => $_POST
	] );
	
	if ( isset( $_POST['project_id'] ) && isset( $_POST['addproject_id'] ) ) {
		$project_save->param( [
			'where' => [
				'project_id' => $_POST['project_id']
			]
		] );
	}


	$project_save->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $project_save );
	$jsonrpc_client->send();

	try {
		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

		if ( $result[ $project_save->id ]['status'] ) {

			echo json_encode( [
				'status'  => 'success',
				'content' => '',
				'data'    => $result[ $project_save->id ]['data']
			] );
		} else {
			echo json_encode( [
				'status' => 'error',
				'errors' => $result[ $project_save->id ]['message'],
				'data'   => $result[ $project_save->id ]['data']
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
