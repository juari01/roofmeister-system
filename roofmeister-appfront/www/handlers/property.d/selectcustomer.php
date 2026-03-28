<?php

use Atlas\Framework\Form\Element;
use Atlas\Framework\Form;


// Close session writing
	session_write_close();
	include( "{$_SERVER['DOCUMENT_ROOT']}/includes/contents/property/property.php" );
	include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/property/comselectcustomer.php" );
	require( $_SERVER['DOCUMENT_ROOT'] . '/../templates/form.php' );

	$content = new Propety();


	if ( !isset( $_POST['task'] ) || isset( $_POST['task'] ) == 'selectcustomer' ) {

		if ( isset( $_POST['i'] ) ) {
			$index = $_POST['i'];
		} else {
			$index = 1;
		}

	$select_customer = new jsonrpc\method( 'property.selectcustomer' );
	$select_customer->param( 'api_token', $jsonrpc_api_token );
	$select_customer->param( 'hash',      $_SESSION['user']['hash'] );
	$select_customer->param( 'count',     true );
	$select_customer->param( 'not_zero',  true );
	$select_customer->param( 'limit',     20 );
	$select_customer->param( 'offset',    ( $index - 1 ) * 20 );
	$select_customer->id = $jsonrpc_client->generate_unique_id();

	if( isset( $_POST[ 'search' ] )) {
	$select_customer->param( 'search' , $_POST[ 'search' ] );
	}

	$jsonrpc_client->method( $select_customer );
	$jsonrpc_client->send();

	$result = jsonrpc\client::parse_result( $jsonrpc_client->result );


	if ( $result[ $select_customer->id ]['status'] ) {
		
		$hidden_propid_form = new Form( $form_templates['main_form'] );
		$hidden_propid_form->add_element( new Element( 'hidden', [
			'name'  => 'property_id',
			'value' => $_POST['property_id']
		] ));

		
	   $count 	  	   = $result[ $select_customer->id ]['data']['countresult'];
	   $customers  	   = $result[ $select_customer->id ]['data']['customers'];
	   $page_nav 	   = $content->get_pagination( $count, $index );
	   $table_customer = $content->get_list_selectcustomer( $customers );

	   
	   if ( isset( $_POST['task'] ) && isset( $_POST['search'] ) == 'search' ) {
	
		echo json_encode( array(
			'status'  => 'success',
			'content' => array(
			'table'   => App::table_display( $table_customer ),
			'pages'   => $page_nav
			)
		));
	
		} else { 

	   $content_index  = str_replace( "%PAGES%", $page_nav, $content_index );
	   $content_index  = str_replace( '%TABLE_CONTENT%' , App::table_display( $table_customer ), $content_index );

	   echo json_encode( [
		'status'  => 'success',
		'content' => $content_index . $hidden_propid_form->render()

		] );
		}

	 
	} else {

		echo json_encode( [
			'status' => FALSE,
			'errors' => $result[ $select_customer->id ][ 'message' ]
		] );
	}

}

?>
