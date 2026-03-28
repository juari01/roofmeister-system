<?php 
	// var_dump( $_POST['customername1'] ); die();
	if ( !empty( $_FILES['file'] ) ) {
		if ( is_array( $_FILES['file']['name'] ) ) {
			foreach ( $_FILES['file']['name'] as $key => $file ) {
				$save_file = new jsonrpc\method( 'file.save_file' );
				$save_file->param( 'api_token', $jsonrpc_api_token );
				$save_file->param( 'hash',      $_SESSION['user']['hash'] );
				$save_file->param( 'is_manual', true );

			if ( isset( $_POST['customer_id'] ) ) {
				$save_file->param( 'customername', $_POST['customername'] );
				$save_file->param( 'customer_id',  $_POST['customer_id'] );
			}
			if ( isset($_POST['property_id'] ) ) {
				$save_file->param( 'propertyname', $_POST['propertyname'] );
				$save_file->param( 'property_id',  $_POST['property_id'] );
			}
		
				$_POST['file_data'] = base64_encode( file_get_contents( $_FILES['file']['tmp_name'][$key] ) );
				$_POST['name']      = $_FILES['file']['name'][$key];

				$save_file->param( [
					'values' => $_POST
				] );

				$save_file->id = $jsonrpc_client->generate_unique_id();

				$jsonrpc_client->method( $save_file );
				$jsonrpc_client->send();
			}
		} else {
			$save_file = new jsonrpc\method( 'file.save_file' );
			$save_file->param( 'api_token', $jsonrpc_api_token );
			$save_file->param( 'hash',      $_SESSION['user']['hash'] );
			$save_file->param( 'customername', $_POST['customername'] );
			$save_file->param( 'customer_id',  $_POST['customer_id'] );

			if (isset( $_POST['customer_id'] ) ) {
				$save_file->param( 'customername', $_POST['customername'] );
				$save_file->param( 'customer_id',  $_POST['customer_id'] );
			}
			if (isset( $_POST['property_id'] ) ) {
				$save_file->param( 'propertyname', $_POST['propertyname'] );
				$save_file->param( 'property_id',  $_POST['property_id'] );
			}
			if ( isset( $_POST['project_id'] ) ) {
				$save_file->param( 'projectname', $_POST['projectname'] );
				$save_file->param( 'project_id',  $_POST['project_id'] );
			}


			$_POST['file_data'] = base64_encode( file_get_contents( $_FILES['file']['tmp_name'] ) );
			$_POST['name']      = $_FILES['file']['name'];
		

			$save_file->param( [
				'values' => $_POST
			] );

			$save_file->id = $jsonrpc_client->generate_unique_id();

			$jsonrpc_client->method( $save_file );
			$jsonrpc_client->send();
		}

		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

		if ( $result[ $save_file->id ]['status'] ) {

			echo json_encode( [
				'status'  => 'success',
				'content' => '',
				'data'    => $result[ $save_file->id ]['data']
			] );
		} else {
			echo json_encode( [
				'status' => 'error',
				'errors' => $result[ $save_file->id ]['message'],
				'data'   => $result[ $save_file->id ]['data']
			] );
		}
	} else {
		echo json_encode( [
			'status' => 'error',
			'errors' => 'Missing file to upload.'
		] );
	}
?>