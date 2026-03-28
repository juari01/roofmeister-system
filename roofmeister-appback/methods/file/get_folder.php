<?php namespace file;

	function get_folder( $params ) {

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

		if( empty( $params['folder_id'] )) {
			return build_result( FALSE, 'missing_folder_id' );
		}

	// Assemble WHERE clause
		$bind_params[':folder_id'] = [
			'value' => $params['folder_id'],
			'type'  => \PDO::PARAM_INT
		];

		$folder_query = <<<SQL
SELECT *
  FROM file_folder
 WHERE folder_id = :folder_id
SQL;
 
		$folder_stmt = \DB::dbh()->prepare( $folder_query );
		\DB::bind_params( $folder_stmt, $bind_params );
		$folder_stmt->execute();

		$folder_row = $folder_stmt->fetch( \PDO::FETCH_ASSOC );
		
		return \JSONRPC::build_result( TRUE, 'folder', $folder_row );

	}

?>
