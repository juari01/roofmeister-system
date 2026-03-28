<?php

use Atlas\Framework\Form;
use Atlas\Framework\Form\Element;

// Close session writing
	session_write_close();

	if ( isset( $_POST['geography_id'] )) {
		$get_geography = new jsonrpc\method( 'admin.geography.get' );
		$get_geography->param( 'api_token',    $jsonrpc_api_token );
		$get_geography->param( 'hash',         $_SESSION['user']['hash'] );
		$get_geography->param( 'geography_id', $_POST['geography_id'] );
		$get_geography->id = $jsonrpc_client->generate_unique_id();

		$jsonrpc_client->method( $get_geography );
	}

// Send request to JSON-RPC
	$jsonrpc_client->send();

	try {
		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

	// Get jsonrpc results
		if ( isset( $_POST['geography_id'] ) && $result[ $get_geography->id ]['status'] ) {
			$geography = $result[ $get_geography->id ]['data'][0];
		}

	// Create the geography options form
		require( $_SERVER['DOCUMENT_ROOT'] . '/../templates/form.php' );

		$geography_form = new Form( $form_templates['main_form'] );

		$geography_form->add_element( new Element ( 'text', [
			'label' => 'Name',
			'name'  => 'name',
			'value' => ( isset( $geography['name'] ) ? $geography['name'] : '' ),
			'class' => 'width-300px label-required'
		] ));

		$geography_form->add_element( new Element( 'linebreak', [] ));

		$geography_form->add_element( new Element( 'checkbox', [
			'label'   => 'Active',
			'options' => [
				[
					'display' => 'Active',
					'name'    => 'active',
					'value'   => 1,
					'checked' => ( isset( $geography['active'] ) && $geography['active'] == 1 || !isset( $_POST['geography_id'] ) ? TRUE : FALSE )
				]
			]
		] ));

		if ( isset( $geography['geography_id'] )) {

			$geography_form->add_element( new Element( 'hidden', [
				'name'  => 'geography_id',
				'value' => $geography['geography_id']
			] ));
		}

		include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/common.php" );

		$content_addedit = str_replace( '%FORM_CONTENT%',
			App::form_wrapper( 'geography_save', [
				'Geograhy Form' => [
					'content' =>  [
						'Geograhy Information' => $geography_form->render()
					]
				],
			] ),
			$content_addedit
		);

		$content_addedit = str_replace( '%BACK_BUTTON_FUNCTION%', 'back-geography', $content_addedit );

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
