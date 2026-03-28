<?php

// Close session writing
	session_write_close();

	$get_categories = new jsonrpc\method( 'admin.category.get' );
	$get_categories->param( 'api_token', $jsonrpc_api_token );
	$get_categories->param( 'hash',      $_SESSION['user']['hash'] );
	$get_categories->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $get_categories );
	$jsonrpc_client->send(); 
		
	$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

	if ( $result[ $get_categories->id ]['status'] ) {
		$table_category = [
			'header' => [
				[
					'value' => 'Status'
				],
				[
					'value' => 'Category Name'
				],
			]
		];

		foreach ( $result[ $get_categories->id ]['data'] as $category ) {
			$table_category['body'][] = [
				'data'  => [
					[
						'attr'  => 'category_id',
						'value' => $category['category_id']
					]
				],
				'cells' => [
					[
						'value' => ( $category['active'] 
							? App::image_display( [ 'src' => '/images/active.png',   'alt' => 'Active'   ] ) 
							: App::image_display( [ 'src' => '/images/inactive.png', 'alt' => 'Inactive' ] )
						),
					],
					[
						'value' => $category['name'] 
					],
				]
			];
		}

		include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/common.php" );

		$content_index = str_replace( '%TABLE_CONTENT%', App::table_display( $table_category ), $content_index );

		$content_index = str_replace( '%ADD_BUTTON_VALUE%',    'Add Category', $content_index );
		$content_index = str_replace( '%ADD_BUTTON_FUNCTION%', 'add-category', $content_index );

		echo json_encode( [
			'status'  => 'success',
			'content' => $content_index
		] );
	} else {
		echo json_encode( [
			'status' => 'error',
			'errors' => $result[ $get_categories->id ]['message']
		] );
	}

?>
