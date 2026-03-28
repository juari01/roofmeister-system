<?php 

	use Atlas\Framework\Form\Element;
	use Atlas\Framework\Form;

// Close session writing
	session_write_close();

	include( "{$_SERVER['DOCUMENT_ROOT']}/includes/contents/appointments/appointments.php" );
	include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/appointments/comselectproject.php" );
	require( $_SERVER['DOCUMENT_ROOT'] . '/../templates/form.php' );

	$content = new Appointments();

	if ( !isset( $_POST['task'] ) || $_POST['task'] == 'selectproject' ) {

		if ( isset( $_POST['i'] ) ) {
			$index = $_POST['i'];
		} else {
			$index = 1;
		}

		$get_projects = new jsonrpc\method( 'appointments.getprojects' );
		$get_projects->param( 'api_token', $jsonrpc_api_token );
		$get_projects->param( 'hash',      $_SESSION['user']['hash'] );
		$get_projects->param( 'count',     true );
		$get_projects->param( 'not_zero',  true );
		$get_projects->param( 'limit',     20 );
		$get_projects->param( 'offset',    ( $index - 1 ) * 20 );
		$get_projects->id = $jsonrpc_client->generate_unique_id();
	
	if ( isset( $_POST[ 'search' ] ) ) {
		$get_projects->param( 'search', $_POST[ 'search' ] );
	}

		$jsonrpc_client->method( $get_projects );
		$jsonrpc_client->send();

		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );


	if ( $result[ $get_projects->id ]['status'] ) {

		$selectappointment_form = new Form( $form_templates['main_form'] );

		$selectappointment_form->add_element( new Element( 'hidden', [
			'label' => 'appointment_id',
			'name'  => 'appointment_id',
			'value' => ( isset( $_POST['appointment_id'] ) ? $_POST['appointment_id'] : '' ),
			'class' => 'width-300px label-required'
		] ));

		$selectappointment_form->add_element( new Element( 'hidden', [
			'label' => 'apptype',
			'name'  => 'apptype',
			'value' => ( isset( $_POST['type_id'] ) ? $_POST['type_id'] : '' ),
			'class' => 'width-300px label-required'
		] ));

		$selectappointment_form->add_element( new Element( 'hidden', [
			'label' => 'Start',
			'name'  => 'start',
		    'value' => ( isset( $_POST['start'] ) ? $_POST['start'] : '' ),
			'class' => 'width-300px label-required'
		] ));

		$selectappointment_form->add_element( new Element( 'hidden', [
			'label' => 'End',
			'name'  => 'end',
		    'value' => ( isset( $_POST['end'] ) ? $_POST['end'] : '' ),
			'class' => 'width-300px label-required'
		] ));

		$selectappointment_form->add_element( new Element( 'hidden', [
			'label' => 'Description',
			'name'  => 'description',
			'value' => ( isset( $_POST['description'] )  ? $_POST['description']: '' ),
			'class' => 'width-300px label-required'
		] ));

		$count 	        = $result[ $get_projects->id ]['data']['countresult'];
		$projects       = $result[ $get_projects->id ]['data']['project'];
		$page_nav       = $content->get_pagination( $count, $index );
		$table_projects = $content->get_listproject( $projects );

		if ( isset( $_POST[ 'task' ] ) && isset( $_POST[ 'search' ] ) == 'search' ) {
				echo json_encode( array(
					'status'  => 'success',
					'content' => array(
					'table'   => App::table_display( $table_projects ),
					'pages'   => $page_nav
					)
				) );

			} else {

		$content_index = str_replace( "%PAGES%", $page_nav, $content_index );
		$content_index = str_replace( '%TABLE_CONTENT%' , App::table_display( $table_projects ), $content_index );

		echo json_encode( [
			'status'  => TRUE,
			'content' => $content_index . $selectappointment_form->render()
		] );
		
	}

	} else {

		echo json_encode( [
			'status' => FALSE,
			'errors' => $result[ $get_projects->id ]['message']
		] );
	}

	}
?>
