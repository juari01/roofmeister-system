<?php

use Atlas\Framework\Form\Element;
use Atlas\Framework\Form;

// Close session writing
	session_write_close();


	if ( isset( $_POST['appointment_idtr'] ) ) { 

		$get_appointment = new jsonrpc\method( 'appointments.get' );
		$get_appointment->param( 'api_token', $jsonrpc_api_token );
		$get_appointment->param( 'hash',      $_SESSION['user']['hash'] );
		$get_appointment->param( 'appointment_id',  $_POST['appointment_idtr'] );
		$get_appointment->id = $jsonrpc_client->generate_unique_id();

		$jsonrpc_client->method( $get_appointment );
	}

		$get_appointment_type = new jsonrpc\method( 'appointments.get_appointment_type' );
		$get_appointment_type->param( 'api_token', $jsonrpc_api_token );
		$get_appointment_type->param( 'hash',      $_SESSION['user']['hash'] );
		$get_appointment_type->param( 'active',    true );
		$get_appointment_type->param( 'type_id',  isset( $_POST['type_id'] ) ? $_POST['type_id'] : NULL );
		$get_appointment_type->id = $jsonrpc_client->generate_unique_id();

		$jsonrpc_client->method( $get_appointment_type );

		$jsonrpc_client->send();


	try {

		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );
		
		require( $_SERVER['DOCUMENT_ROOT'] . '/../templates/form.php' );
		$appointment_form = new Form( $form_templates['main_form'] );
		$appoption_form   = new Form( $form_templates['main_form'] );
		

		if ( isset( $_POST['appointment_idtr'] ) && $result[ $get_appointment->id ]['status'] ) {
			$appointmentvar = $result[ $get_appointment->id ]['data'][0];
		}

		if ( $result[ $get_appointment_type->id ]['status'] ) {
			$appointmenttype_rows = $result[ $get_appointment_type->id ]['data'];
		}


		$appointment_options = array( 
		array(
			'value'   => '0',
			'id'	  => 'compropcheck',
			'checked' => ( isset( $_POST['compropcheck'] ) ? $_POST['compropcheck'] : false),
			'name'    => 'appointmentradio',
            'display' => 'Customer/Property'
        ),
		array(
            'value'   => '1',
			'id'	  => 'projectcheck',
			'checked' => ( isset( $_POST['projectcheck'] ) ? $_POST['projectcheck'] : false),
			'name'    => 'appointmentradio',
            'display' => 'Project'
        )
		);	


		$appoption_form->add_element( new Element( 'radio', [

			'label'   => '',
			'options' => $appointment_options

		] ));

		$appoption_form->add_element( new Element( 'linebreak', [] ) );

		$appointment_form->add_element( new Element( 'hidden', [
			'label' => 'appointment_id',
			'name'  => 'appointment_id',
			'value' =>  ( isset( $appointmentvar['appointment_id'] ) ? $appointmentvar['appointment_id'] : '') .
			 			( isset( $_POST['JSappointment_id'] ) ? $_POST['JSappointment_id'] : '') 
		] ) );

		$appointment_form->add_element( new Element( 'hidden', [
			'label' => 'project_id',
			'name'  => 'project_id',
			'value' => ( isset( $_POST['selectproject_id'] ) 	? $_POST['selectproject_id'] 	: '') .
			 		   ( isset( $appointmentvar['project_id'] ) ? $appointmentvar['project_id'] : '')	   
		] ) );

		$appointment_form->add_element( new Element( 'hidden', [
			'label' => 'customer_id',
			'name'  => 'customer_id',
		    'value' => ( isset( $_POST['customer_id'] ) 	? $_POST['customer_id'] 	: '') .
			  		   ( isset( $appointmentvar['customer_id'] ) ? $appointmentvar['customer_id'] : '')	   
		] ) );

		$appointment_form->add_element( new Element( 'hidden', [
			'label' => 'property_id',
			'name'  => 'property_id',
			'value' => ( isset( $_POST['property_id'] ) 		? $_POST['property_id'] 		 : '') .
			 		   ( isset( $appointmentvar['property_id'] ) ? $appointmentvar['property_id']: '')	   
		] ) );

	if ( isset( $appointmentvar['customer_id'] ) or isset($_POST['compropcheck']) or isset($_POST['customer_id']) ) {

		$appointment_form->add_element( new Element( 'button', [
			'name'  => 'customer',
			'value' => 'Select customer',
			'class' => 'label-required select-Appointmentcustomer-by-id'
		] ) );

		$appointment_form->add_element( new Element( 'text', [
			'label' => '',
			'name'  => 'appcustomer_name',
			'disabled' => 'DISABLED',
			'value' => ( isset( $_POST['customer_name'] ) 	   ? $_POST['customer_name'] 	  : '') .
			  		   ( isset( $appointmentvar['customer_name'] )  ? $appointmentvar['customer_name']  : '') ,
			 'class' => 'width-300px label-required'
		] ) );

		if ( isset( $_POST['customer_id'] ) or isset( $appointmentvar['customer_id'] ) ) {

		$appointment_form->add_element( new Element( 'button', [
			'name'  => 'property',
			'value' => 'Select property',
			'class' => 'label-required select-Appointmentproperty-by-id'
		] ) );


		$appointment_form->add_element( new Element( 'text', [
			'label' => '',
			'name'  => 'appproperty_name',
			'disabled' => 'DISABLED',
			'value' => ( isset( $_POST['property_name'] ) 	       ? $_POST['property_name'] 	      : '') . 
			  		   ( isset( $appointmentvar['property_name'] ) ? $appointmentvar['property_name'] : ''),
			 'class' => 'width-300px label-required'
		] ) );
	}
	
}


	if ( isset( $appointmentvar['project_id'] ) or isset( $_POST['projectcheck'] ) or isset( $_POST['selectproject_id'] ) ){

		$appointment_form->add_element( new Element( 'button', [
			'name'  => 'project',
			'value' => 'Select project',
			'class' => 'label-required select-Appointmentproject-by-id'
		] ) );

		$appointment_form->add_element( new Element( 'text', [
			'label' => '',
			'name'  => 'projcustomer_name',
			'disabled' => 'DISABLED',
			'value' => ( isset( $_POST['selectproject_name'] ) 	   ? $_POST['selectproject_name'] 	  : '') . 
					   ( isset( $appointmentvar['project_name'] )  ? $appointmentvar['project_name']  : '') ,
			'class' => 'width-300px label-required'
		] ) );

	}


			$appointment_form->add_element( new Element( 'linebreak', [] ) );
			$appointment_form->add_element( new Element( 'linebreak', [] ) );
	

			$appointment_type = array( array(
				'value'   => '0',
				'display' => '[Select]'
			) );
			
			if ( !empty( $appointmenttype_rows ) ) {
				foreach ( $appointmenttype_rows as $appointmenttype ) {
					$appointment_type[] = [
						'display' => $appointmenttype['name'],
						'name'    => 'name',
						'value'   => $appointmenttype['type_id']
						];
					}
				}

			$appointment_form->add_element( new Element( 'select', [
				'label'    => 'Appointment type',
				'name' 	   => 'type_id',
				'selected'=> ( isset( $_POST['selectapptype'] ) 	  ? $_POST['selectapptype'] 	: '' ) . 
				 			 ( isset( $appointmentvar[ 'type_id' ] )  ? $appointmentvar[ 'type_id' ]: '' ),
				'options'  => $appointment_type,
				'class'    => 'width-300px label-required',
				'params'   => '{allow_single_deselect: true}'
			] ));

			$appointment_form->add_element( new Element( 'text', [
				'label' => 'Start',
				'name'  => 'start',
				'id'    => 'startappointment',
				'value' => ( isset( $_POST['select_start'] )   ? $_POST['select_start']   :'' ) . 
						   ( isset( $appointmentvar['start'] ) ? $appointmentvar['start'] :'' ),
				'class' => 'width-300px label-required'
			] ));

			$appointment_form->add_element( new Element( 'text', [
				'label' => 'End',
				'name'  => 'end',
				'id'    => 'endappointment',
			    'value' => ( isset( $_POST['select_end'] ) 	 ? $_POST['select_end'] 	  : '' ) .
						   ( isset( $appointmentvar['end'] ) ? $appointmentvar['end']	  : '' ),
				'class' => 'width-300px label-required'
			] ));
	
			$appointment_form->add_element( new Element( 'linebreak', [] ) );
	
			$appointment_form->add_element( new Element( 'textarea', [
				'label' => 'Description',
				'name'  => 'description',
			    'value' => ( isset( $_POST['selectdescription'] )  ? $_POST['selectdescription'] 	 : '' ) .
						   ( isset( $appointmentvar['description'] ) ? $appointmentvar['description']: '' ),
				'class' => 'width-300px label-required'
			] ) );

						
		include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/appointments/comappointments.php" );
		
		if ( isset( $_POST['projectcheck'] ) or isset( $_POST['compropcheck'] ) ) {

			$content_addedit = str_replace( '%FORM_CONTENT%',
			App::form_wrapper( 'appointment_save', [
				'Appointment Form' => [
					'content' =>  [ 
						'Appointment Information' => $appointment_form->render()
					]
				],
			] ),
			$content_addedit
		);
		}
		
		if( isset( $_POST['display_option'] ) ){

		$content_addedit = str_replace( '%FORM_CONTENT%',
			App::form_wrapper( 'appointment_save', [
				'Appointment Form' => [
					'content' =>  [ 
						'Appointment Option' => $appoption_form->render()
						
					]
				],
			] ),
			$content_addedit
		);
		}

		if( isset( $_POST['appointment_idtr'] ) or isset( $_POST['selectproject_id'] ) or isset( $_POST['customer_id'] ) ){

		$content_addedit = str_replace( '%FORM_CONTENT%',
			App::form_wrapper( 'appointment_save', [
				'Appointment Form' => [
					'content' =>  [ 
						'Appointment Information' => $appointment_form->render()
						
					]
				],
			] ),
			$content_addedit
		);
		}
		$content_addedit = str_replace( '%BACK_BUTTON_FUNCTION%', 'back-appointment', $content_addedit );

		if ( isset( $_POST['appointment_idtr'] ) ){
			$content_addedit = str_replace( '%DISABLED%', ( isset( $_POST['appointment_idtr'] ) ? 'ENABLED' : 'ENABLED'), $content_addedit );
		}

	   	$content_addedit = str_replace( '%DISABLED%', ( isset( $_POST['savedisable'] ) ? 'ENABLED' : 'DISABLED'), $content_addedit );
		
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
