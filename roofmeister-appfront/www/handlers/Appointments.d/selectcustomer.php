<?php

use Atlas\Framework\Form\Element;
use Atlas\Framework\Form;


// Close session writing
	session_write_close();
	include( "{$_SERVER['DOCUMENT_ROOT']}/includes/contents/projects/projects.php" );
	include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/appointments/comselectcustomer.php" );
	require( $_SERVER['DOCUMENT_ROOT'] . '/../templates/form.php' );

	$content = new Project();


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

	if ( isset( $_POST[ 'search' ] )) {
		$select_customer->param( 'search' , $_POST[ 'search' ] );
	}

	$jsonrpc_client->method( $select_customer );
	$jsonrpc_client->send();

	$result = jsonrpc\client::parse_result( $jsonrpc_client->result );


	if ( $result[ $select_customer->id ]['status'] ) {
		
		$selectcustomer_form = new Form( $form_templates['main_form'] );

		$selectcustomer_form->add_element( new Element( 'hidden', [
			'label' => '',
			'name'  => 'appointment_id',
			'value' => ( isset( $_POST['appointment_id'] ) ? $_POST['appointment_id'] : '' ),
			'class' => 'width-300px label-required'
		] ) );
		
		$selectcustomer_form->add_element( new Element( 'hidden', [
			'label' => 'apptype',
			'name'  => 'apptype',
			'value' => ( isset( $_POST['type_id'] ) ? $_POST['type_id'] : '' ),
			'class' => 'width-300px label-required'
		] ));

		$selectcustomer_form->add_element( new Element( 'hidden', [
			'label' => 'Start',
			'name'  => 'start',
		    'value' => ( isset( $_POST['start'] ) ? $_POST['start'] : '' ),
			'class' => 'width-300px label-required'
		] ));

		$selectcustomer_form->add_element( new Element( 'hidden', [
			'label' => 'End',
			'name'  => 'end',
		    'value' => ( isset( $_POST['end'] ) ? $_POST['end'] : '' ),
			'class' => 'width-300px label-required'
		] ));

		$selectcustomer_form->add_element( new Element( 'hidden', [
			'label' => 'Description',
			'name'  => 'description',
			'value' => ( isset( $_POST['description'] )  ? $_POST['description']: '' ),
			'class' => 'width-300px label-required'
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
		) );

	
	
	} else { 

	   $content_index  = str_replace( "%PAGES%", $page_nav, $content_index );
	   $content_index  = str_replace( '%TABLE_CONTENT%' , App::table_display( $table_customer ), $content_index );

	  
	   echo json_encode( [
		'status'  => 'success',
		 'content' => $content_index . $selectcustomer_form->render()

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
