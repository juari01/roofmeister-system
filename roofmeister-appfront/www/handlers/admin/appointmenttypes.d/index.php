<?php

// Close session writing
	session_write_close();

	$get_appointmenttypes = new jsonrpc\method( 'admin.appointmenttypes.get' );
	$get_appointmenttypes->param( 'api_token', $jsonrpc_api_token );
	$get_appointmenttypes->param( 'hash',      $_SESSION['user']['hash'] );
	$get_appointmenttypes->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $get_appointmenttypes );
	$jsonrpc_client->send(); 
		
	$result = jsonrpc\client::parse_result( $jsonrpc_client->result );


	if ( $result[ $get_appointmenttypes->id ]['status'] ) {

		$html = '';
		$html .= "<table>\n";
		$html .= "<thead>\n";
		$html .= "<tr>\n";
		$html .= "<th >Status</th>\n";
		$html .= "<th >Appointment Type Name</th>\n";
		$html .= "<th >Appointment Type Color</th>\n";
		$html .= "</tr>\n";
		$html .= "</thead>";
		$html .= "<tbody>\n";

		foreach ( $result[ $get_appointmenttypes->id ]['data'] as $appointment ) {

			$active = ( $appointment['active'] 
			? App::image_display( [ 'src' => '/images/active.png',   'alt' => 'Active'   ] ) 
			: App::image_display( [ 'src' => '/images/inactive.png', 'alt' => 'Inactive' ] )
			);

			$typecolor = $appointment['color'];

			$html .= "<tr data-appointment_id=\"{$appointment['type_id']}\">\n";
			$html .= "<td>{$active}</td>\n";
			$html .= "<td>{$appointment['name']}</td>\n";
			$html .= "<td  style=\"background-color:{$typecolor};\"></td>\n";
			$html .= "</tr>\n";
	
		}

		$html .= "</tbody>\n";
		$html .= "</table>\n";

		
		include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/common.php" );

		$content_index = str_replace( '%TABLE_CONTENT%', '' ,$content_index );
		$content_index = str_replace( '%ADD_BUTTON_VALUE%',    'Add Appointment type', $content_index );
		$content_index = str_replace( '%ADD_BUTTON_FUNCTION%', 'add-appointment', $content_index );

		echo json_encode( [
			'status'  => 'success',
			'content' => $content_index . $html
		] );

	} else {

		echo json_encode( [
			'status' => 'error',
			'errors' => $result[ $get_appointmenttypes->id ]['message']
		] );

	}

?>