<?php

// Close session writing
	session_write_close();

// Start JSON-RPC Request
	$category_save = new jsonrpc\method( 'admin.category.save' );
	$category_save->param( 'api_token', $jsonrpc_api_token );
	$category_save->param( 'hash',      $_SESSION['user']['hash'] );

	if ( !isset( $_POST['active'] ) ) {
		$_POST['active'] = 0;
	}

	$category_save->param( [
		'values' => $_POST
	] );

	if ( isset( $_POST['category_id'] ) ) {
		$category_save->param( [
			'where' => [
				'category_id' => $_POST['category_id']
			]
		] );
	}

	$category_save->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $category_save );
	$jsonrpc_client->send();

	try {
		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

		if ( $result[ $category_save->id ]['status'] ) {

			echo json_encode( [
				'status'  => 'success',
				'content' => '',
				'data'    => $result[ $category_save->id ]['data']
			] );
		} else {
			echo json_encode( [
				'status' => 'error',
				'errors' => $result[ $category_save->id ]['message'],
				'data'   => $result[ $category_save->id ]['data']
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
