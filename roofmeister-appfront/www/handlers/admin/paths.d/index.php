<?php

// Close session writing
	session_write_close();

	if ( isset($_POST['path_id'] ) ) {

	$save_paths = new jsonrpc\method( 'admin.path.linkfoldertopath' );
	$save_paths->param( 'api_token', $jsonrpc_api_token );
	$save_paths->param( 'hash',      $_SESSION['user']['hash'] );
	$save_paths->param( 'hash',      $_SESSION['user']['hash'] );
	$save_paths->param( 'path_id', $_POST['path_id'] );
	$save_paths->param( 'folder_id', $_POST['folder_id'] );
	$save_paths->id = $jsonrpc_client->generate_unique_id();
	
		$jsonrpc_client->method( $save_paths );
	}

	$get_paths = new jsonrpc\method( 'admin.path.get' );
	$get_paths->param( 'api_token', $jsonrpc_api_token );
	$get_paths->param( 'hash',      $_SESSION['user']['hash'] );
	$get_paths->param( 'hash',      $_SESSION['user']['hash'] );
	$get_paths->id = $jsonrpc_client->generate_unique_id();
	$jsonrpc_client->method( $get_paths );

	$jsonrpc_client->send(); 
		
	$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

	include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/paths/pathUI.php" );


	if ( $result[ $get_paths->id ]['status'] ) {
		$table_paths = [
			'header' => [
				[
					'value' => 'Path Name'
				],
				[
					'value' => 'Folder Name'
				]
			]
		];

		foreach ( $result[ $get_paths->id ]['data'] as $paths ) {
			$table_paths['body'][] = [
				'data'  => [
					[
						'attr'  => 'path_id',
						'value' => $paths['path_id']
					]
				],
				'cells' => [
					[
						'value' => $paths['name'] 
					],
					[
						'value' => $paths['foldername'] 
					]
				]
			];
		}

		$content_index = str_replace( '%TABLE_CONTENT%', table_display( $table_paths ), $content_index );

		echo json_encode( [
			'status'  => 'success',
			'content' => $content_index
		] );
	} else {
		echo json_encode( [
			'status' => 'error',
			'errors' => $result[ $get_paths->id ]['message']
		] );
	}

?>
