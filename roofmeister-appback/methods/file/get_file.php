<?php namespace file;

	function get_file( $params ) {

	/**
	 * Get Folder
	 * 
	 * @param string  api_token  - The token used to authenticate the JSON-RPC client.
	 * @param int     folder_id  - Selected Folder ID
	 * @param string  hash       - The hash for the user accessing this method.
	 *
	 * @return array build_result()
	 */

		// Atlas class autoloader
		require( \env::$paths['methods'] . '/../autoloader_atlas.php' );

	// Load application configuration
		$config = new \Atlas\Config( file_get_contents( \env::$paths['methods']. '/../config.ini' ));


	// Application class autoloader
		require( $config->get( 'paths\autoloader' ));

	// Verify authorized API Token
		if ( empty( $params['api_token'] )) {
			return \JSONRPC::build_result( FALSE, 'api_token_missing' );
		}

		if ( !\JSONRPC::check_api_token( $params['api_token'] )) {
			return \JSONRPC::build_result( FALSE, "api_token_failure: {$params['api_token']}" );
		}

	// Verify hash
		$user_id = \User::verify_hash( $params['hash'] );
		if ( empty( $user_id )) {
			return \JSONRPC::build_result( FALSE, 'invalid_hash' );
		}

		\JSONRPC::audit_log( $user_id, __NAMESPACE__ . '\\' . __FUNCTION__, json_encode( $params ) );

		if( empty( $params['file_id'] )) {
			return \JSONRPC::build_result( FALSE, 'missing_file_id' );
		}


	$file_query = <<<SQL
	SELECT file.*, xref_folder_file.name
		FROM file 
				LEFT JOIN xref_folder_file ON file.file_id = xref_folder_file.file_id
		WHERE file.file_id = :file_id
SQL;

	$file_stmt = \DB::dbh()->prepare( $file_query );
	$file_stmt->bindValue( ':file_id', $params['file_id'], \PDO::PARAM_STR );
	$file_stmt->execute();

	if( !$file_stmt->rowCount() ) {
		return \JSONRPC::build_result( FALSE, 'file_not_found' );
	}


	if( !empty( $params['file_id'] ) ) {

		$file_row  = $file_stmt->fetch( \PDO::FETCH_ASSOC );
		$file_path = $config->get( 'paths\files' );

		if( empty( $params['no_data'] )) {
			$file_name = $file_path . '/' . substr( $file_row['hash'], 0, 2 ) . '/' . substr( $file_row['hash'], 2, 2 ). '/' . substr( $file_row['hash'], 4 ) . '-' . $file_row['file_id'];
			$file_data = base64_encode( file_get_contents( $file_name ));

			$file_row['file_data'] = $file_data;
		}

		if( !empty( $params['file_path'] )) {
			$file_name = $file_path . '/' . substr( $file_row['hash'], 0, 2 ) . '/' . substr( $file_row['hash'], 2, 2 ). '/' . substr( $file_row['hash'], 4 ) . '-' . $file_row['file_id'];
			$file_row['file_path'] = $file_name;
		}

	}
		
		return \JSONRPC::build_result( TRUE, 'file_data', $file_row );

	}

?>
