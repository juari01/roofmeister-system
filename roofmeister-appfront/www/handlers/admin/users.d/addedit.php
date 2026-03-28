<?php

use Atlas\Framework\Form\Element;
use Atlas\Framework\Form;

// Close session writing
	session_write_close();

	if ( isset( $_POST['user_id'] ) ) {
		$get_user = new jsonrpc\method( 'admin.user.get' );
		$get_user->param( 'api_token', $jsonrpc_api_token );
		$get_user->param( 'hash',      $_SESSION['user']['hash'] );
		$get_user->param( 'user_id',   $_POST['user_id'] );
		$get_user->id = $jsonrpc_client->generate_unique_id();

		$jsonrpc_client->method( $get_user );
	}

// Get group
	$get_group = new jsonrpc\method( 'admin.user.get_group' );
	$get_group->param( 'api_token', $jsonrpc_api_token );
	$get_group->param( 'hash',	  $_SESSION['user']['hash'] );
	$get_group->param( 'user_id', isset( $_POST['user_id'] ) ? $_POST['user_id'] : NULL );
	$get_group->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $get_group );

	// Get group
	$get_calendar = new jsonrpc\method( 'admin.user.get_calendar' );
	$get_calendar->param( 'api_token', $jsonrpc_api_token );
	$get_calendar->param( 'hash',	  $_SESSION['user']['hash'] );
	$get_calendar->param( 'user_id', isset( $_POST['user_id'] ) ? $_POST['user_id'] : NULL );
	$get_calendar->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $get_calendar );

// Send request to JSON-RPC
	$jsonrpc_client->send();

	try {
		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

	// Get jsonrpc results
		if ( isset( $_POST['user_id'] ) && $result[ $get_user->id ]['status'] ) {
			$user = $result[ $get_user->id ]['data'][0];
		}

		if ( $result[ $get_group->id ]['status'] ) {
			$group_rows = $result[ $get_group->id ]['data'];
		}
		if ( $result[ $get_calendar->id ]['status'] ) {
			$calendar_rows = $result[ $get_calendar->id ]['data'];
		}

	// Create the group options form

		$group_form    = new Form( $form_templates['main_form'] );
		$calendar_form = new Form( $form_templates['main_form'] );

		$group_options = [];

		foreach ( $group_rows as $group ) {
			$group_options[] = [
				'display' => $group['group'],
				'name'	=> 'group',
				'value'   => $group['group_id'],
				'checked' => ( !empty( $group['enabled'] ) ? TRUE : FALSE )
			];
		}

		$group_form->add_element( new Element( 'checkbox', [
			'label'   => 'Groups',
			'options' => $group_options
		] ));


		$calendar_options = [];

		foreach ( $calendar_rows as $calendar ) {
			$calendar_options[] = [
				'display' => $calendar['name'],
				'name'	=> 'name',
				'value'   => $calendar['calendar_id'],
			    'checked' => ( !empty( $calendar['enabled'] ) ? TRUE : FALSE )
			];
		}

		$calendar_form->add_element( new Element( 'checkbox', [
			'label'   => 'Calendars',
			'options' => $calendar_options
		] ));

	// Create user form
		$user_form = new Form( $form_templates['main_form'] );

		$user_form->add_element( new Element( 'text', [
			'label' => 'First Name',
			'name'  => 'first_name',
			'value' => ( isset( $user['first_name'] ) ? $user['first_name'] : '' ),
			'class' => 'width-300px label-required'
		] ));

		$user_form->add_element( new Element( 'text', [
			'label' => 'Last Name',
			'name'  => 'last_name',
			'value' => ( isset( $user['last_name'] ) ? $user['last_name'] : '' ),
			'class' => 'width-300px label-required'
		] ));

		$user_form->add_element( new Element( 'linebreak', [] ));

		$user_form->add_element( new Element( 'text', [
			'label' => 'Email',
			'name'  => 'email',
			'value' => ( isset( $user['email'] ) ? $user['email'] : '' ),
			'class' => 'width-300px label-required'
		] ));

		$user_form->add_element( new Element( 'linebreak', [] ));

		$user_form->add_element( new Element( 'checkbox', [
			'label'   => 'Active',
			'options' => [
				[
					'display' => 'Active',
					'name'	=> 'active',
					'value'   => 1,
					'checked' => ( isset( $user['active'] ) && $user['active'] == 1 || !isset( $_POST['user_id'] ) ? TRUE : FALSE )
				]
			]
		] ));

		$user_form->add_element( new Element( 'linebreak', [] ));

		$user_form->add_element( new Element( 'text', [
			'label' => 'Username',
			'name'  => 'username',
			'value' => ( isset( $user['username'] ) ? $user['username'] : '' ),
			'class' => 'width-300px label-required'
		] ));

		$user_form->add_element( new Element( 'password', [
			'label' => 'Password',
			'name'  => 'password',
			'value' => '',
			'class' => 'width-300px label-required'
		] ));

		if ( isset( $user['user_id'] )) {

			$user_form->add_element( new Element( 'hidden', [
				'name'  => 'user_id',
				'value' => $user['user_id']
			] ));
		}

		include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/common.php" );

		$content_addedit = str_replace( '%FORM_CONTENT%',
			App::form_wrapper( 'user_save', [
				'User Form' => [
					'content' =>  [
						'User Information' => $user_form->render(),
						'Groups'           => $group_form->render(),
						'Calendars'        => $calendar_form->render()
					]
				],
			] ),
			$content_addedit
		);

		$content_addedit = str_replace( '%BACK_BUTTON_FUNCTION%', 'back-user', $content_addedit );
		
		echo json_encode( [
			'status'  => TRUE,
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