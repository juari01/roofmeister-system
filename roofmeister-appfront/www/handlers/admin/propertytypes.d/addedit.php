<?php

use Atlas\Framework\Form;
use Atlas\Framework\Form\Element;

// Close session writing
	session_write_close();

	if ( isset( $_POST['propertytype_id'] ) ) {
		$get_propertytypes = new jsonrpc\method( 'admin.propertytypes.get' );
		$get_propertytypes->param( 'api_token',    $jsonrpc_api_token );
		$get_propertytypes->param( 'hash',         $_SESSION['user']['hash'] );
		$get_propertytypes->param( 'propertytype_id', $_POST['propertytype_id'] );
		$get_propertytypes->id = $jsonrpc_client->generate_unique_id();

		$jsonrpc_client->method( $get_propertytypes );
	}

// Send request to JSON-RPC
	$jsonrpc_client->send();

	try {
		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

	// Get jsonrpc results
		if ( isset( $_POST['propertytype_id'] ) && $result[ $get_propertytypes->id ]['status'] ) {
			$propertytypes = $result[ $get_propertytypes->id ]['data'][0];
		}

		

		require( $_SERVER['DOCUMENT_ROOT'] . '/../templates/form.php' );

		$propertytypes_form = new Form( $form_templates['main_form'] );

		$propertytypes_form->add_element( new Element ( 'text', [
			'label' => 'Name',
			'name'  => 'name',
			'value' => ( isset( $propertytypes['name'] ) ? $propertytypes['name'] : '' ),
			'class' => 'width-300px label-required'
		] ) );

		$propertytypes_form->add_element( new Element( 'linebreak', [] ));

		$propertytypes_form->add_element( new Element( 'checkbox', [
			'label'   => 'Active',
			'options' => [
				[
					'display' => 'Active',
					'name'    => 'active',
					'value'   => 1,
					'checked' => ( isset( $propertytypes['active'] ) && $propertytypes['active'] == 1 ? TRUE : FALSE )
				]
			]
		] ) );

		if ( isset( $propertytypes['type_id'] ) ) {

			$propertytypes_form->add_element( new Element( 'hidden', [
				'name'  => 'type_id',
				'value' => $propertytypes['type_id']
			] ) );
		}

		include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/common.php" );

		$content_addedit = str_replace( '%FORM_CONTENT%',
			App::form_wrapper( 'propertytype_save', [
				'Property Type Form' => [
					'content' =>  [
						'Property Type Information' => $propertytypes_form->render()
					]
				],
			] ),
			$content_addedit
		);

		$content_addedit = str_replace( '%BACK_BUTTON_FUNCTION%', 'back-propertytypes', $content_addedit );

		echo json_encode( [
			'status'  => 'success',
			'content' => $content_addedit
		] );
	} catch ( Exception $e ) {
		error_log( 'DEBUG: ' . $jsonrpc_client->result_raw );

		echo json_encode( [
			'status'  => FALSE,
			'errors' => $jsonrpc_client->result_raw
		] );
	}

?>
