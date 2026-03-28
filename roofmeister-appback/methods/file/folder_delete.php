<?php namespace file;

	function folder_delete( $params ) {
	/*
	 * Folder Delete
	 * Deletes a folder in the file manager.
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

		try {
			//Check subfolders
				// \folder::subfolder_delete( $params['folder_id'] );


				$folder_group_query = <<<SQL
  DELETE
    FROM xref_folder_group
   WHERE folder_id = :folder_id
SQL;
				$folder_group_result = \DB::dbh()->prepare( $folder_group_query );
				$folder_group_result->bindValue( ':folder_id', $params['folder_id'], \PDO::PARAM_INT );
				$folder_group_result->execute();

				$folder_file_query = <<<SQL
  DELETE
    FROM xref_folder_file
   WHERE folder_id = :folder_id
SQL;
				$folder_file_result = \DB::dbh()->prepare( $folder_file_query );
				$folder_file_result->bindValue( ':folder_id', $params['folder_id'], \PDO::PARAM_INT );
				$folder_file_result->execute();

				$folder_query = <<<SQL
  DELETE
    FROM file_folder
   WHERE folder_id = :folder_id
SQL;
				$folder_result = \DB::dbh()->prepare( $folder_query );
				$folder_result->bindValue( ':folder_id', $params['folder_id'], \PDO::PARAM_INT );
				$folder_result->execute();

				return \JSONRPC::build_result( TRUE, 'folder_deleted' );
			} catch ( Exception $e ) {
				return \JSONRPC::build_result( FALSE, 'folder_deleted', $e->getMessage() );
			}
	}

?>
