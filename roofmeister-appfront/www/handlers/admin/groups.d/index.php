<?php

// Close session writing
	session_write_close();

	$get_groups = new jsonrpc\method( 'admin.group.get' );
	$get_groups->param( 'api_token', $jsonrpc_api_token );
	$get_groups->param( 'hash',      $_SESSION['user']['hash'] );
	$get_groups->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $get_groups );
	$jsonrpc_client->send(); 
		
	$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

	if ( $result[ $get_groups->id ]['status'] ) {
		$table_group = [
			'header' => [
				[
					'value' => 'Status'
				],
				[
					'value' => 'Group'
				],
			]
		];

		foreach ( $result[ $get_groups->id ]['data'] as $group ) {
			$table_group['body'][] = [
				'data'  => [
					[
						'attr'  => 'group_id',
						'value' => $group['group_id']
					]
				],
				'cells' => [
					[
						'value' => ( $group['active'] 
							? App::image_display( [ 'src' => '/images/active.png',   'alt' => 'Active'   ] ) 
							: App::image_display( [ 'src' => '/images/inactive.png', 'alt' => 'Inactive' ] )
						),
					],
					[
						'value' => $group['group'] 
					],
				]
			];
		}

		include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/common.php" );

		$content_index = str_replace( '%TABLE_CONTENT%', App::table_display( $table_group ), $content_index );

		$content_index = str_replace( '%ADD_BUTTON_VALUE%',    'Add Group', $content_index );
		$content_index = str_replace( '%ADD_BUTTON_FUNCTION%', 'add-group', $content_index );

		echo json_encode( [
			'status'  => 'success',
			'content' => $content_index
		] );
	} else {
		echo json_encode( [
			'status' => 'error',
			'errors' => $result[ $get_groups->id ]['message']
		] );
	}

?>
