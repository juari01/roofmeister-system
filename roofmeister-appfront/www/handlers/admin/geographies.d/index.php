<?php

// Close session writing
	session_write_close();

	$get_geographies = new jsonrpc\method( 'admin.geography.get' );
	$get_geographies->param( 'api_token', $jsonrpc_api_token );
	$get_geographies->param( 'hash',      $_SESSION['user']['hash'] );
	$get_geographies->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $get_geographies );
	$jsonrpc_client->send(); 
		
	$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

	if ( $result[ $get_geographies->id ]['status'] ) {
		$table_geography = [
			'header' => [
				[
					'value' => 'Status'
				],
				[
					'value' => 'Geography Name'
				],
			]
		];

		foreach ( $result[ $get_geographies->id ]['data'] as $geography ) {
			$table_geography['body'][] = [
				'data'  => [
					[
						'attr'  => 'geography_id',
						'value' => $geography['geography_id']
					]
				],
				'cells' => [
					[
						'value' => ( $geography['active'] 
							? App::image_display( [ 'src' => '/images/active.png',   'alt' => 'Active'   ] ) 
							: App::image_display( [ 'src' => '/images/inactive.png', 'alt' => 'Inactive' ] )
						),
					],
					[
						'value' => $geography['name'] 
					],
				]
			];
		}

		include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/common.php" );

		$content_index = str_replace( '%TABLE_CONTENT%', App::table_display( $table_geography ), $content_index );

		$content_index = str_replace( '%ADD_BUTTON_VALUE%',    'Add Geography', $content_index );
		$content_index = str_replace( '%ADD_BUTTON_FUNCTION%', 'add-geography', $content_index );

		echo json_encode( [
			'status'  => 'success',
			'content' => $content_index
		] );
	} else {
		echo json_encode( [
			'status' => 'error',
			'errors' => $result[ $get_geographies->id ]['message']
		] );
	}

?>
