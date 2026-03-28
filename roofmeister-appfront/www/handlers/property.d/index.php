<?php 

// Close session writing
	session_write_close();

	include( "{$_SERVER['DOCUMENT_ROOT']}/includes/contents/property/property.php" );
	include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/property/comproperty.php" );
	$content = new Propety();

	if ( !isset( $_POST['task'] ) || $_POST['task'] == 'index' ) {
		if ( isset( $_POST['i'] ) ) {
			$index = $_POST['i'];
		} else {
			$index = 1;
		}

	$get_property = new jsonrpc\method( 'property.get' );
	$get_property->param( 'api_token',  $jsonrpc_api_token );
	$get_property->param( 'hash',       $_SESSION['user']['hash'] );
	$get_property->param( 'count',      true );
	$get_property->param( 'not_zero',   true );
	$get_property->param( 'limit',      20 );
	$get_property->param( 'offset',     ( $index - 1 ) * 20 );
	$get_property->id = $jsonrpc_client->generate_unique_id();
	
	if ( isset( $_POST['search'] ) ) {
	$get_property->param( 'search', $_POST['search'] );
	}

	$jsonrpc_client->method( $get_property );
	$jsonrpc_client->send();

	$result = jsonrpc\client::parse_result( $jsonrpc_client->result );


	if ( $result[ $get_property->id ]['status'] ) {

		$count 	        = $result[ $get_property->id ]['data']['countresult'];
		$properties     = $result[ $get_property->id ]['data']['property'];
		$page_nav       = $content->get_pagination( $count, $index );
		$table_property = $content->get_list( $properties );

			if ( isset( $_POST[ 'task' ] ) && $_POST['task'] == 'index' ) {
				echo json_encode( array(
					'status'  => 'success',
					'content' => array(
					'table'   => App::table_display( $table_property ),
					'pages'   => $page_nav
					)
				));
			} else {

		$content_index = str_replace( "%PAGES%", $page_nav, $content_index );
		$content_index = str_replace( '%TABLE_CONTENT%' , App::table_display( $table_property ), $content_index );

		echo json_encode( [
			'status'  => TRUE,
			'content' => $content_index
		] );
		
	}

	} else {
		echo json_encode( [
			'status' => FALSE,
			'errors' => $result[ $get_property->id ]['message']
		] );
	}

	}
?>
