<?php namespace file;

	function file_delete( $params ) {
	/*
	 * Delete
	 * Deletes a file to the file manager.
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

		if ( !\JSONRPC::check_api_token( $params['api_token'] ) ) {
			return \JSONRPC::build_result( FALSE, "api_token_failure: {$params['api_token']}" );
		}

	// Verify user hash
		$user_id = \User::verify_hash( $params['hash'] );
		if ( empty( $user_id )) {
			return \JSONRPC::build_result( FALSE, 'invalid_hash' );
		}

		\JSONRPC::audit_log( $user_id, __NAMESPACE__ . '\\' . __FUNCTION__, json_encode( $params ) );

		if( empty( $params['folder_id'] )) {
			return \JSONRPC::build_result( FALSE, 'missing_folder_id' );
		}

		if( empty( $params['file_id'] )) {
			return \JSONRPC::build_result( FALSE, 'missing_file_id' );
		}

		if( empty( $params['file_name'] )) {
			return \JSONRPC::build_result( FALSE, 'missing_file_name' );
		}

		$bind_params[':folder_id'] = [
			'value' => $params['folder_id'],
			'type'  => \PDO::PARAM_INT
		];

		$bind_params[':file_id'] = [
			'value' => $params['file_id'],
			'type'  => \PDO::PARAM_INT
		];

		$bind_params[':file_name'] = [
			'value' => $params['file_name'],
			'type'  => \PDO::PARAM_STR
		];

		$file_query = <<<SQL
  UPDATE xref_folder_file
     SET deleted = 1
   WHERE folder_id = :folder_id
     AND file_id   = :file_id
     AND name      = :file_name
SQL;
		$file_result = \DB::dbh()->prepare( $file_query );

		\DB::bind_params( $file_result, $bind_params );

		$file_result->execute();

		return \JSONRPC::build_result( TRUE, 'file_deleted' );
	}
?>