<?php 

// Close session writing
	session_write_close();

	include( "{$_SERVER['DOCUMENT_ROOT']}/includes/contents/customer/customer.php" );
	include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/customer/comcustomer.php" );
	$content = new Customer();

	if ( !isset( $_POST['task'] ) || $_POST['task'] == 'index' ) {
		if ( isset( $_POST['i'] ) ) {
			$index = $_POST['i'];
		} else {
			$index = 1;
	}
	
	$get_customer = new jsonrpc\method( 'customer.get' );
	$get_customer->param( 'api_token', $jsonrpc_api_token );
	$get_customer->param( 'hash',      $_SESSION['user']['hash'] );
	$get_customer->param( 'count',     true );
	$get_customer->param( 'not_zero',  true );
	$get_customer->param( 'limit',     20 );
	$get_customer->param( 'offset',    ( $index - 1 ) * 20 );
	$get_customer->id = $jsonrpc_client->generate_unique_id();
	
	if( isset( $_POST['search'] ) ) {
		$get_customer->param('search', $_POST['search'] );
	}

	$jsonrpc_client->method( $get_customer );
	$jsonrpc_client->send();

	$result = jsonrpc\client::parse_result( $jsonrpc_client->result );


	if ( $result[ $get_customer->id ]['status'] ) {

   	   $count 	  	   = $result[ $get_customer->id ]['data']['countresult'];
	   $customers  	   = $result[ $get_customer->id ]['data']['customers'];
	   $page_nav 	   = $content->get_pagination( $count, $index );
	   $table_customer = $content->get_list( $customers );

		if ( isset( $_POST['task'] ) && $_POST['task'] == 'index' ) {
			echo json_encode( array(
				'status'  => 'success',
				'content' => array(
				'table'   => App::table_display( $table_customer ),
				'pages'   => $page_nav
				)
			));
		} else {

	    $content_index = str_replace( "%PAGES%", $page_nav, $content_index );
		$content_index = str_replace( '%TABLE_CONTENT%' , App::table_display( $table_customer ), $content_index );
		
		echo json_encode( [
			'status'  => 'success',
			'content' => $content_index
		] );

	}

	} else {

		echo json_encode( [
			'status' => FALSE,
			'errors' => $result[ $get_customer->id ]['message']
		] );
	}

}

?>
