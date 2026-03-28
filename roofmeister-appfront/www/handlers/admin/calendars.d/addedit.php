<?php

use Atlas\Framework\Form;
use Atlas\Framework\Form\Element;

// Close session writing
	session_write_close();

	if ( isset( $_POST['calendar_id'] ) ) {
		$get_calendars = new jsonrpc\method( 'admin.calendar.get' );
		$get_calendars->param( 'api_token',    $jsonrpc_api_token );
		$get_calendars->param( 'hash',         $_SESSION['user']['hash'] );
		$get_calendars->param( 'calendar_id',  $_POST['calendar_id'] );
		$get_calendars->id = $jsonrpc_client->generate_unique_id();
		$jsonrpc_client->method( $get_calendars );

		$get_appointmenttypes = new jsonrpc\method( 'admin.calendar.getapptypetocalendar' );
		$get_appointmenttypes->param( 'api_token',   $jsonrpc_api_token );
		$get_appointmenttypes->param( 'hash',        $_SESSION['user']['hash'] );
		$get_appointmenttypes->param( 'calendar_id', $_POST['calendar_id'] );
		$get_appointmenttypes->id = $jsonrpc_client->generate_unique_id();
		$jsonrpc_client->method( $get_appointmenttypes );
	}


		$jsonrpc_client->send();


	try {
		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

	// Get jsonrpc results
		if ( isset( $_POST['calendar_id'] ) && $result[ $get_calendars->id ]['status'] ) {
			$calendars = $result[ $get_calendars->id ]['data'][0];
		}

		$calendars_form = new Form( $form_templates['main_form'] );
		include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/common.php" );
		
		$calendars_form->add_element( new Element ( 'text', [
			'label' => 'Name',
			'name'  => 'name',
			'value' => ( isset( $calendars['name'] ) ? $calendars['name'] : '' ),
			'class' => 'width-300px label-required'
		] ) );


		if ( isset( $calendars['calendar_id'] ) ) {

			if ( $result[ $get_appointmenttypes->id ]['status'] ) { 
				$appointmenttypes = $result[ $get_appointmenttypes->id ]['data'];
			}

			$calendars_form->add_element( new Element( 'hidden', [
				'name'  => 'calendar_id',
				'value' => $calendars['calendar_id']
			] ) );
		

		$table_appointmenttypes = [
			'header' => [
				[
					'value' => 'Status'
				],
				[
					'value' => 'Appointment Type Name'
				]
			]
		];

		foreach ( $result[ $get_appointmenttypes->id ]['data'] as $appointment ) {
			$table_appointmenttypes['body'][] = [
				'data'  => [
					[
						'attr'  => 'appointment_id',
						'value' => $appointment['type_id']
					]
				],
				'cells' => [
					[
						'value' => ( $appointment['active'] 
							? App::image_display( [ 'src' => '/images/active.png',   'alt' => 'Active'   ] ) 
							: App::image_display( [ 'src' => '/images/inactive.png', 'alt' => 'Inactive' ] )
						),
					],
					[
						'value' => $appointment['name'] 
					]
				]
			];
		}


		
		$tabsapptype = table_display($table_appointmenttypes);
	

		include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/common.php" );

		$content_addedit = str_replace( '%FORM_CONTENT%',
			App::form_wrapper( 'calendar_save', [
				'Calendar Form' => [
					'content' =>  [
						'Calendar Information' => $calendars_form->render(),
						'Appointment Type'	   => $tabsapptype
					]
				],
			] ),
			$content_addedit
		);

	} else {

	
		$content_addedit = str_replace( '%FORM_CONTENT%',
			App::form_wrapper( 'calendar_save', [
				'Calendar Form' => [
					'content' =>  [
						'Calendar Information' => $calendars_form->render()
					]
				],
			] ),
			$content_addedit
		);
	}

		$content_addedit = str_replace( '%BACK_BUTTON_FUNCTION%', 'back-calendar', $content_addedit );

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
