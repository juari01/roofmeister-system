<?php

use Atlas\Framework\Form\Element;
use Atlas\Framework\Form;

// Close session writing
	session_write_close();
	
	include( "{$_SERVER['DOCUMENT_ROOT']}/includes/contents/projects/projects.php" );
	include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/projects/comselectproperty.php" );
	require( $_SERVER['DOCUMENT_ROOT'] . '/../templates/form.php' );

	 $content = new Project();


	if ( !isset( $_POST['task'] ) || isset( $_POST['task'] ) == 'selectproperty' ) {
		if ( isset( $_POST['i'] ) ) {
			$index = $_POST['i'];
		} else {
			$index = 1;
		}

	

	$get_property = new jsonrpc\method( 'project.getlinkproperty' );
	$get_property->param( 'api_token',  	 $jsonrpc_api_token );
	$get_property->param( 'hash',       	 $_SESSION['user']['hash'] );
	$get_property->param( 'projcustomer_id', ( isset( $_POST['projcustomer_id'] ) ? $_POST['projcustomer_id'] : '' ) );
	$get_property->param( 'count',      	 true );
	$get_property->param( 'not_zero',    	 true );
	$get_property->param( 'limit',      	 20 );
	$get_property->param( 'offset',     	 ( $index - 1 ) * 20 );
	$get_property->id = $jsonrpc_client->generate_unique_id();


	if ( isset( $_POST['search'] ) ) {
	$get_property->param( 'search' , $_POST['search'] );
	}

	$jsonrpc_client->method( $get_property );
	$jsonrpc_client->send();

	$result = jsonrpc\client::parse_result( $jsonrpc_client->result );


	if ( $result[ $get_property->id ]['status'] ) {
		
		$selectproperty_form = new Form( $form_templates['main_form'] );

		
		$selectproperty_form->add_element( new Element( 'hidden', [
			'label' => '',
			'name'  => 'project_id',
			'value' => ( isset( $_POST['project_id'] ) ? $_POST['project_id'] : '' ),
			'class' => 'width-300px label-required'
		] ) );

		$selectproperty_form->add_element( new Element( 'hidden', [
			'label' => 'customer',
			'name'  => 'projcustomer_id',
			'value' => ( isset( $_POST['projcustomer_id'] ) ? $_POST['projcustomer_id'] : '' ),
			'class' => 'width-300px label-required'
		] ));

		$selectproperty_form->add_element( new Element( 'hidden', [
			'label' => '',
			'name'  => 'projcustomer_name',
			'value' => ( isset( $_POST['projcustomer_name'] ) ? $_POST['projcustomer_name'] : '' ),
			'class' => 'width-300px label-required'
		] ));

		$selectproperty_form->add_element( new Element( 'hidden', [
			'label' => '',
			'name'  => 'name',
			'value' => ( isset( $_POST['projectname'] ) ? $_POST['projectname'] : '' ),
			'class' => 'width-300px label-required'
		] ));

		$selectproperty_form->add_element( new Element( 'hidden', [
			'label' => '',
			'name'  => 'description',
			'value' => ( isset( $_POST['projdescription'] ) ? $_POST['projdescription'] : '' ),
			'class' => 'width-300px label-required'
		] ));


		
		$count 	        = $result[ $get_property->id ]['data']['countresult'];
		$properties     = $result[ $get_property->id ]['data']['property'];
		$page_nav       = $content->get_pagination( $count, $index );
		$table_property = $content->get_list_selectproperty( $properties );

	 
	   if ( isset( $_POST[ 'task' ] ) && isset( $_POST['search'] ) == 'search' ) {
	
		echo json_encode( array(
			'status'  => 'success',
			'content' => array(
			'table'   => App::table_display( $table_property ),
			'pages'   => $page_nav
			)
		) );

	} else { 

	$content_index = str_replace( "%PAGES%", $page_nav, $content_index );
	$content_index = str_replace( '%TABLE_CONTENT%' , App::table_display( $table_property ), $content_index );

	   echo json_encode( [
		'status'  => 'success',
		 'content' => $content_index . $selectproperty_form->render()

	] );
}

	 
	} else {

		echo json_encode( [
			'status' => FALSE,
			'errors' => $result[ $get_property->id ][ 'message' ]
		] );
	}

}

?>
