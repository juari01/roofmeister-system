<?php

use Atlas\Framework\Form\Element;
use Atlas\Framework\Form;

	session_write_close();

	if ( isset( $_POST['property_id'] ) && isset( $_POST['propcontact_id'] ) ) {

		$get_prop_contact = new jsonrpc\method( 'property.get_propcontact' );
		$get_prop_contact->param( 'api_token', 	    $jsonrpc_api_token );
		$get_prop_contact->param( 'hash',      	    $_SESSION['user']['hash'] );
		$get_prop_contact->param( 'property_id',    $_POST['property_id'] );
		$get_prop_contact->param( 'propcontact_id', $_POST['propcontact_id'] );
		$get_prop_contact->id = $jsonrpc_client->generate_unique_id();

		$jsonrpc_client->method( $get_prop_contact );
	}


	$jsonrpc_client->send();


	try {

		require( $_SERVER['DOCUMENT_ROOT'] . '/../templates/form.php' );
		$contact_propform = new Form( $form_templates['main_form'] );


		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );
		

		if ( isset( $_POST['property_id'] ) && isset( $_POST['propcontact_id'] ) ) {
			$propcontact = $result[ $get_prop_contact->id ]['data'][0];

			$contact_propform->add_element( new Element( 'hidden', [
				'name'  => 'contact_id',
				'value' => $propcontact['contact_id']
			] ));

			$contact_propform->add_element( new Element( 'hidden', [
				'name'  => 'property_id',
				'value' => $propcontact['property_id'] 
			] ));
		}

		if ( isset( $_POST['property_id'] ) ) {

			$contact_propform->add_element( new Element( 'hidden', [
				'name'  => 'property_id',
				'value' => $_POST['property_id'] 
			] ));
			
		}


		$contact_propform->add_element( new Element( 'linebreak', [] ) );

		$contact_propform->add_element( new Element( 'text', [
			'label' => 'Company',
			'name'  => 'company',
		    'value' => ( isset( $propcontact['company'] ) ? $propcontact['company'] : '' ),
			'class' => 'width-300px label-required'
		] ));

		$contact_propform->add_element( new Element( 'text', [
			'label' => 'First name',
			'name'  => 'first_name',
			'value' => ( isset( $propcontact['first_name'] ) ? $propcontact['first_name'] : '' ),
			'class' => 'width-300px label-required'
		] ));

		$contact_propform->add_element( new Element( 'text', [
			'label' => 'Last name',
			'name'  => 'last_name',
			'value' => ( isset( $propcontact['last_name'] ) ? $propcontact['last_name'] : '' ),
			'class' => 'width-300px label-required'
		] ));


		$contact_propform->add_element( new Element( 'linebreak', [] ) );

		$contact_propform->add_element( new Element( 'text', [
			'label' => 'Phone work',
			'name'  => 'phone_work',
			'value' => ( isset( $propcontact['phone_work'] ) ? $propcontact['phone_work'] : '' ),
			'class' => 'width-300px label-required'
		] ));


		$contact_propform->add_element( new Element( 'text', [
			'label' => 'Phone mobile',
			'name'  => 'phone_mobile',
			'value' => ( isset( $propcontact['phone_mobile'] ) ? $propcontact['phone_mobile'] : '' ),
			'class' => 'width-300px label-required'
		] ));

		$contact_propform->add_element( new Element( 'linebreak', [] ) );

		$contact_propform->add_element( new Element( 'checkbox', [
			'label'   => 'Active',
			'options' => [
				[
					'display' => 'Active',
					'name'    => 'active',
					'value'   => 1,
				    'checked' => ( isset( $propcontact['active'] ) && $propcontact['active']  == 1 ? TRUE : FALSE )
				]
			]
		] ));


		$contact_propform->add_element( new Element( 'linebreak', [] ) );


		include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/property/comaddeditpropcontact.php" );

		$content_addedit = str_replace( '%FORM_CONTENT%',
			App::form_wrapper( 'propcontact_save', [
				'Contact Form' => [
					'content' =>  [
						'Contact Information' => $contact_propform->render()
					]
				],
			] ),
			$content_addedit
		);


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
