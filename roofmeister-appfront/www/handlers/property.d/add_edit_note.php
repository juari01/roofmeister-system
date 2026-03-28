<?php

use Atlas\Framework\Form\Element;
use Atlas\Framework\Form;


// Close session writing
	session_write_close();
	include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/property/compropertynote.php" );
	require( $_SERVER['DOCUMENT_ROOT'] . '/../templates/form.php' );

	$get_note = new jsonrpc\method( 'property.get_notes' );
	$get_note->param( 'api_token',   $jsonrpc_api_token );
	$get_note->param( 'hash',        $_SESSION['user']['hash'] );
	$get_note->param( 'note_id', 	 isset( $_POST['note_id'] ) ? $_POST['note_id'] : NULL );
	$get_note->param( 'property_id', isset( $_POST['property_id'] ) ? $_POST['property_id'] : NULL );
	$get_note->id = $jsonrpc_client->generate_unique_id();
	$jsonrpc_client->method( $get_note );

	$jsonrpc_client->send();

	$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

	if (  $result[ $get_note->id ]['status'] ) {
		foreach ( $result[ $get_note->id ]['data'] as $note ) {
		$property_note =	$note['note'];
		}
	}
	
	$property_note_form = new Form( $form_templates['main_form'] );

	if ( isset( $_POST['property_id'] )) {

		$propertyId = $_POST['property_id'];
		$note_id 	=  isset( $_POST['note_id'] ) ? $_POST['note_id'] : '';
	
		$property_note_form->add_element( new Element( 'hidden', [
			'name'  => 'property_id',
			'value' => $propertyId
		] ));

		$property_note_form->add_element( new Element( 'hidden', [
			'name'  => 'note_id',
			'value' => $note_id
		] ));

	}

	$property_note_form->add_element( new Element( 'textarea', [
		'label' => 'Note',
		'name'  => 'note',
		'value' => ( isset( $_POST['note_id'] ) ? $property_note : '' ),
		'class' => 'width-500px label-required'
	] ) );


	$content_addedit = str_replace( '%FORM_CONTENT%',
	App::form_wrapper( 'property_note_save', [
		'Note Form' => [
			'content' =>  [
				'Add Note' => $property_note_form->render(),

			]
		],

	] ),
	$content_addedit
); 	

	   echo json_encode( [
		'status'  => 'success',
		 'content' => $content_addedit
	] );


?>
