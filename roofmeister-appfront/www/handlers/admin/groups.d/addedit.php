<?php

use Atlas\Framework\Form\Element;
use Atlas\Framework\Form;

// Close session writing
	session_write_close();

	if ( isset( $_POST['group_id'] )) {
		$get_group = new jsonrpc\method( 'admin.group.get' );
		$get_group->param( 'api_token', $jsonrpc_api_token );
		$get_group->param( 'hash',      $_SESSION['user']['hash'] );
		$get_group->param( 'group_id',  $_POST['group_id'] );
		$get_group->id = $jsonrpc_client->generate_unique_id();

		$jsonrpc_client->method( $get_group );
	}

// Get security
	$get_security = new jsonrpc\method( 'admin.group.get_security' );
	$get_security->param( 'api_token', $jsonrpc_api_token );
	$get_security->param( 'hash',      $_SESSION['user']['hash'] );
	$get_security->param( 'group_id',  isset( $_POST['group_id'] ) ? $_POST['group_id'] : NULL );
	$get_security->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $get_security );

// Send request to JSON-RPC
	$jsonrpc_client->send();

	try {
		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );
	// Get jsonrpc results
		if ( isset( $_POST['group_id'] ) && $result[ $get_group->id ]['status'] ) {
			$group = $result[ $get_group->id ]['data'][0];
		}

		if ( $result[ $get_security->id ]['status'] ) {
			$security_rows = $result[ $get_security->id ]['data'];
		}

	// Create the group options form
		require( $_SERVER['DOCUMENT_ROOT'] . '/../templates/form.php' );


		$group_form = new Form( $form_templates['main_form'] );
		
		$group_form->add_element( new Element( 'text', [
			'label' => 'Group',
			'name'  => 'group',
			'value' => ( isset( $group['group'] ) ? $group['group'] : '' ),
			'class' => 'width-300px label-required'
		] ));

		$group_form->add_element( new Element( 'linebreak', [] ));

		$group_form->add_element( new Element( 'checkbox', [
			'label'   => 'Active',
			'options' => [
				[
					'display' => 'Active',
					'name'    => 'active',
					'value'   => 1,
					'checked' => ( isset( $group['active'] ) && $group['active'] == 1 || !isset( $_POST['group_id'] ) ? TRUE : FALSE )
				]
			]
		] ));

	// Create the Security Options form
		$security_options = [];

		foreach ( $security_rows as $security ) {
			$security_options[] = [
				'display' => $security['name'],
				'name'    => 'security',
				'value'   => $security['security_id'],
				'checked' => ( !empty( $security['enabled'] ) ? TRUE : FALSE ),
				'attr'    => [
					[
						'name'  => 'title',
						'value' => $security['description'],
					]
				]
			];
		}

		$security_form = new Form( $form_templates['main_form'] );

		$security_form->add_element( new Element( 'checkbox', [
			'type'    => 'checkbox',
			'label'   => 'Security Options',
			'options' => $security_options
		] ));

		$security_form->add_element( new Element( 'linebreak', [] ));

		if ( isset( $group['group_id'] )) {

			$group_form->add_element( new Element( 'hidden', [
				'name'  => 'group_id',
				'value' => $group['group_id']
			] ));
		}

		include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/common.php" );

		$content_addedit = str_replace( '%FORM_CONTENT%',
			App::form_wrapper( 'group_save', [
				'Group Form' => [
					'content' =>  [
						'Group Information' => $group_form->render(),
						'Security'          => $security_form->render()
					]
				],
			] ),
			$content_addedit
		);

		$content_addedit = str_replace( '%BACK_BUTTON_FUNCTION%', 'back-group', $content_addedit );

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
