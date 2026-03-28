<?php 

// Close session writing
	session_write_close();

	include( "{$_SERVER['DOCUMENT_ROOT']}/includes/contents/appointments/appointments.php" );
	include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/appointments/comappointments.php" );
	$content = new Appointments();

	if ( !isset( $_POST['task'] ) || $_POST['task'] == 'index' ) {
		if ( isset( $_POST['i'] ) ) {
			$index = $_POST['i'];
		} else {
			$index = 1;
		}

	$get_appointments = new jsonrpc\method( 'appointments.get' );
	$get_appointments->param( 'api_token', $jsonrpc_api_token );
	$get_appointments->param( 'hash',      $_SESSION['user']['hash'] );
	$get_appointments->param( 'count',     true );
	$get_appointments->param( 'not_zero',  true );
	$get_appointments->param( 'limit',     20 );
	$get_appointments->param( 'offset',    ( $index - 1 ) * 20 );
	$get_appointments->id = $jsonrpc_client->generate_unique_id();

	
	if ( isset( $_POST['search'] ) ) {
		$get_appointments->param( 'search', $_POST[ 'search' ] );
	}

	$jsonrpc_client->method( $get_appointments );
	$jsonrpc_client->send();

	$result = jsonrpc\client::parse_result( $jsonrpc_client->result );


	if ( $result[ $get_appointments->id ]['status'] ) {

		$count 	        	= $result[ $get_appointments->id ]['data']['countresult'];
		$appointments  	    = $result[ $get_appointments->id ]['data']['appointment'];
		$page_nav       	= $content->get_pagination( $count, $index );
		$table_appointments = $content->get_listappointments( $appointments );

			if ( isset( $_POST[ 'task' ] ) && $_POST['task'] == 'index' ) {
				echo json_encode( array(
					'status'  => 'success',
					'content' => array(
					'table'   => App::table_display( $table_appointments ),
					'pages'   => $page_nav
					)
				) );
			} else {

		$content_index = str_replace( "%PAGES%", $page_nav, $content_index );
		$content_index = str_replace( '%TABLE_CONTENT%' , App::table_display( $table_appointments ), $content_index );

		echo json_encode( [
			'status'  => TRUE,
			'content' => $content_index
		] );
		
	}

	} else {
		echo json_encode( [
			'status' => FALSE,
			'errors' => $result[ $get_appointments->id ]['message']
		] );
	}

	}
?>
