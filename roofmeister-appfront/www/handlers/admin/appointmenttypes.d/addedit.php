<?php

use Atlas\Framework\Form;
use Atlas\Framework\Form\Element;

// Close session writing
	session_write_close();

	if ( isset( $_POST['appointment_id'] ) ) {
		$get_appointment = new jsonrpc\method( 'admin.appointmenttypes.get' );
		$get_appointment->param( 'api_token',      $jsonrpc_api_token );
		$get_appointment->param( 'hash',           $_SESSION['user']['hash'] );
		$get_appointment->param( 'appointment_id', $_POST['appointment_id'] );
		$get_appointment->id = $jsonrpc_client->generate_unique_id();
		$jsonrpc_client->method( $get_appointment );
	}

		$get_calendars = new jsonrpc\method( 'admin.calendar.get' );
		$get_calendars->param( 'api_token', $jsonrpc_api_token );
		$get_calendars->param( 'hash',      $_SESSION['user']['hash'] );
		$get_calendars->id = $jsonrpc_client->generate_unique_id();
		$jsonrpc_client->method( $get_calendars );

		$jsonrpc_client->send();

	try {
		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

		if ( isset( $_POST['appointment_id'] ) && $result[ $get_appointment->id ]['status'] ) {
			$appointment = $result[ $get_appointment->id ]['data'][0];
		}

		if ( $result[ $get_calendars->id ]['status'] ) {
			$calendar_rows = $result[ $get_calendars->id ]['data'];
		}

		$appointment_form = new Form( $form_templates['main_form'] );
		
		
		$appointment_form->add_element( new Element ( 'text', [
			'label' => 'Name',
			'name'  => 'name',
			'value' => ( isset( $appointment['name'] ) ? $appointment['name'] : '' ),
			'class' => 'width-300px label-required'
		] ) );

		$calendar_options = array( array(
            'value'   => '0',
            'display' => '[Select]'
        ) );

		if ( !empty( $calendar_rows ) ) {
		foreach ( $calendar_rows as $calendar ) {
			$calendar_options[] = [
				'display' => $calendar['name'],
				'name'    => 'state',
				'value'   => $calendar['calendar_id']
			];
		}
		}
	
		$appointment_form->add_element( new Element( 'select', [
			'label'    => 'Select calendar',
			'name' 	   => 'calendar_id',
			'selected' => ( isset( $appointment['calendar_id'] ) ? $appointment['calendar_id']: '' ),
			'options'  =>$calendar_options,
			'class'    => 'width-300px label-required',
			'params'   => '{allow_single_deselect: true}'
		] ) );

		$appointment_form->add_element( new Element( 'linebreak', [] ) );

		$appointment_form->add_element( new Element ( 'color', [
			'label' => 'Select color:',
			'name'  => 'color',
		    'value' => ( isset( $appointment['color'] ) ? $appointment['color'] : '' ),
			'class' => 'width-300px label-required'
		] ) );


		$appointment_form->add_element( new Element( 'linebreak', [] ));

		$appointment_form->add_element( new Element( 'checkbox', [
			'label'   => 'Active',
			'options' => [
				[
					'display' => 'Active',
					'name'    => 'active',
					'value'   => 1,
					'checked' => ( isset( $appointment['active'] ) && $appointment['active'] == 1 || !isset( $_POST['appointment_id'] ) ? TRUE : FALSE )
				]
			]
		] ) );

		if ( isset( $appointment['type_id'] ) ) {

			$appointment_form->add_element( new Element( 'hidden', [
				'name'  => 'type_id',
				'value' => $appointment['type_id']
			] ) );
		}

		include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/common.php" );

		$content_addedit = str_replace( '%FORM_CONTENT%',
			App::form_wrapper( 'appointment_save', [
				'Appointment Type Form' => [
					'content' =>  [
						'Appointment Type Information' => $appointment_form->render()
					]
				],
			] ),
			$content_addedit
		);

		$content_addedit = str_replace( '%BACK_BUTTON_FUNCTION%', 'back-appointment', $content_addedit );

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
