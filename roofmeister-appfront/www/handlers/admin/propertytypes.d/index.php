<?php

// Close session writing
	session_write_close();

	$get_propertytypes = new jsonrpc\method( 'admin.propertytypes.get' );
	$get_propertytypes->param( 'api_token', $jsonrpc_api_token );
	$get_propertytypes->param( 'hash',      $_SESSION['user']['hash'] );
	$get_propertytypes->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $get_propertytypes );
	$jsonrpc_client->send(); 
		
	$result = jsonrpc\client::parse_result( $jsonrpc_client->result );


	if ( $result[ $get_propertytypes->id ]['status'] ) {
		$table_propertytypes = [
			'header' => [
				[
					'value' => 'Status'
				],
				[
					'value' => 'Name'
				],
			]
		];

		foreach ( $result[ $get_propertytypes->id ]['data'] as $propertytype ) {
			$table_propertytypes['body'][] = [
				'data'  => [
					[
						'attr'  => 'propertytype_id',
						'value' => $propertytype['type_id']
					]
				],
				'cells' => [
					[
						'value' => ( $propertytype['active'] 
							? App::image_display( [ 'src' => '/images/active.png',   'alt' => 'Active'   ] ) 
							: App::image_display( [ 'src' => '/images/inactive.png', 'alt' => 'Inactive' ] )
						),
					],
					[
						'value' => $propertytype['name'] 
					],
				]
			];
		}

		include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/common.php" );

		$content_index = str_replace( '%TABLE_CONTENT%', App::table_display( $table_propertytypes ), $content_index );

		$content_index = str_replace( '%ADD_BUTTON_VALUE%',    'Add Property Type', $content_index );
		$content_index = str_replace( '%ADD_BUTTON_FUNCTION%', 'add-propertytype', $content_index );

		echo json_encode( [
			'status'  => 'success',
			'content' => $content_index
		] );
	} else {
		echo json_encode( [
			'status' => 'error',
			'errors' => $result[ $get_propertytypes->id ]['message']
		] );
	}

?>
