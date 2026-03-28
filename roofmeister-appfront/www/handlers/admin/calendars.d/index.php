<?php

// Close session writing
	session_write_close();

	$get_calendars = new jsonrpc\method( 'admin.calendar.get' );
	$get_calendars->param( 'api_token', $jsonrpc_api_token );
	$get_calendars->param( 'hash',      $_SESSION['user']['hash'] );
	$get_calendars->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $get_calendars );
	$jsonrpc_client->send(); 
		
	$result = jsonrpc\client::parse_result( $jsonrpc_client->result );


	if ( $result[ $get_calendars->id ]['status'] ) {
		$table_calendars = [
			'header' => [
				[
					'value' => 'Calendar Name'
				]
			]
		];

		foreach ( $result[ $get_calendars->id ]['data'] as $calendars ) {
			$table_calendars['body'][] = [
				'data'  => [
					[
						'attr'  => 'calendar_id',
						'value' => $calendars['calendar_id']
					]
				],
				'cells' => [
					[
						'value' => $calendars['name'] 
					]
				]
			];
		}

		include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/common.php" );

		$content_index = str_replace( '%TABLE_CONTENT%', table_display( $table_calendars ), $content_index );

		$content_index = str_replace( '%ADD_BUTTON_VALUE%',    'Add Calendar', $content_index );
		$content_index = str_replace( '%ADD_BUTTON_FUNCTION%', 'add-calendars', $content_index );

		echo json_encode( [
			'status'  => 'success',
			'content' => $content_index
		] );
	} else {
		echo json_encode( [
			'status' => 'error',
			'errors' => $result[ $get_calendars->id ]['message']
		] );
	}

?>
