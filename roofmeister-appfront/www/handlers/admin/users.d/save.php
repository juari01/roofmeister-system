<?php 

// Close session writing
	session_write_close();

// Start JSON-RPC Request
	$user_save = new jsonrpc\method( 'admin.user.save' );
	$user_save->param( 'api_token',   $jsonrpc_api_token );
	$user_save->param( 'hash',        $_SESSION['user']['hash'] );

	if ( !isset( $_POST['active'] )) {
		$_POST['active'] = 0;
	}

	if ( !isset( $_POST['is_team_leader'] )) {
		$_POST['is_team_leader'] = 0;
	}

	$user_save->param( [
		'values' => $_POST
	] );

	if ( isset( $_POST['user_id'] )) {
		$user_save->param( [
			'where' => [
				 'user_id' => $_POST['user_id']
			]
		] );
	}

	$user_save->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $user_save );
	$jsonrpc_client->send();

	try {
		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

		if ( $result[ $user_save->id ]['status'] ) {

		// Assign clients to this user if above is success
			if ( isset( $_POST['user_id'] ) ) {
				$user_id = $_POST['user_id'];
			} else {
				$user_id = $result[ $user_save->id ]['data']['user_id'];
			}

			echo json_encode( [
				'status'  => TRUE,
				'content' => '',
				'data'    => $result[ $user_save->id ]['data']
			] );
		} else {
			echo json_encode( [
				'status' => FALSE,
				'errors' => $result[ $user_save->id ]['message'],
				'data'   => $result[ $user_save->id ]['data']
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
