<?php 

// Close session writing
	session_write_close();

	include( "{$_SERVER['DOCUMENT_ROOT']}/includes/contents/projects/projects.php" );
	include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/projects/comprojects.php" );
	$content = new Project();

	if ( !isset( $_POST['task'] ) || $_POST['task'] == 'index' ) {
		if ( isset( $_POST['i'] ) ) {
			$index = $_POST['i'];
		} else {
			$index = 1;
		}

	$get_projects = new jsonrpc\method( 'project.get' );
	$get_projects->param( 'api_token', $jsonrpc_api_token );
	$get_projects->param( 'hash',      $_SESSION['user']['hash'] );
	$get_projects->param( 'count',     true );
	$get_projects->param( 'not_zero',  true );
	$get_projects->param( 'limit',     20 );
	$get_projects->param( 'offset',    ( $index - 1 ) * 20 );
	$get_projects->id = $jsonrpc_client->generate_unique_id();
	
	if ( isset( $_POST[ 'search' ] ) ) {
	$get_projects->param( 'search', $_POST[ 'search' ] );
	}

	$jsonrpc_client->method( $get_projects );
	$jsonrpc_client->send();

	$result = jsonrpc\client::parse_result( $jsonrpc_client->result );


	if ( $result[ $get_projects->id ][ 'status' ] ) {

		$count 	        = $result[ $get_projects->id ]['data']['countresult'];
		$projects       = $result[ $get_projects->id ]['data']['project'];
		$page_nav       = $content->get_pagination( $count, $index );
		$table_projects = $content->get_list( $projects );

			if ( isset( $_POST[ 'task' ] ) && $_POST['task'] == 'index' ) {
				echo json_encode( array(
					'status'  => 'success',
					'content' => array(
					'table'   => App::table_display( $table_projects ),
					'pages'   => $page_nav
					)
				) );
			} else {

		$projects_index = str_replace( "%PAGES%", $page_nav, $projects_index );
		$projects_index = str_replace( '%TABLE_CONTENT%' , App::table_display( $table_projects ), $projects_index );

		echo json_encode( [
			'status'  => TRUE,
			'content' => $projects_index
		] );
		
	}

	} else {
		echo json_encode( [
			'status' => FALSE,
			'errors' => $result[ $get_projects->id ]['message']
		] );
	}

	}
?>
