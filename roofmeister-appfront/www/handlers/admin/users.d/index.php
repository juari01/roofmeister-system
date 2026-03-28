<?php 

// Close session writing
	session_write_close();

	$get_users = new jsonrpc\method( 'admin.user.get' );
	$get_users->param( 'api_token', $jsonrpc_api_token );
	$get_users->param( 'hash',      $_SESSION['user']['hash'] );
	$get_users->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $get_users );
	$jsonrpc_client->send();

	$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

	$table_user = [
		'header' => [
			[
				'value' => 'Status'
			],
			[
				'value' => 'User'
			],
		]
	];

	if ( $result[ $get_users->id ]['status'] ) {

		foreach ( $result[ $get_users->id ]['data'] as $user ) {

			$table_user['body'][] = [
				'data'  => [
					[
						'attr'  => 'user_id',
						'value' => $user['user_id']
					]
				],
				'cells' => [
					[
						'value' => ( $user['active'] 
						    ? App::image_display( [ 'src' => '/images/active.png',   'alt' => 'Active'   ] ) 
						    : App::image_display( [ 'src' => '/images/inactive.png', 'alt' => 'Inactive' ] )
						),
					],
					[
						'value' => $user['first_name'] . ' ' . $user['last_name']
					],
				]
			];
		}

		include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/common.php" );

		$tabs = [
			'User List' => [
				'data'    => [
					'users' => 'users',
				],
				'content' => [
					'Users' => App::table_display( $table_user )
				]
			]
		];

		$content_index = str_replace(
			'%TABLE_CONTENT%',
			App::form_wrapper( 'user_tabs', $tabs, false, true ),
			$content_index
		);

		$content_index = str_replace( '%ADD_BUTTON_VALUE%',    'Add User', $content_index );
		$content_index = str_replace( '%ADD_BUTTON_FUNCTION%', 'add-user', $content_index );

		echo json_encode( [
			'status'  => TRUE,
			'content' => $content_index
		] );
	} else {
		echo json_encode( [
			'status' => FALSE,
			'errors' => $result[ $get_users->id ]['message']
		] );
	}

?>
