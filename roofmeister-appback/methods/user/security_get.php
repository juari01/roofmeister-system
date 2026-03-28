<?php namespace user;

	function security_get( $params ) {
	/**
	 * Get a list of security codes allowed to a user.
	 *
	 * @param string api_token - The token used to authenticate the JSON-RPC client.
	 * @param int    user_id   - User ID of user to be checked
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
		if ( empty( $params['api_token'] ) ) {
			return \JSONRPC::build_result( FALSE, 'api_token_missing' );
		}

		if ( !\JSONRPC::check_api_token( $params['api_token'] ) ) {
			return \JSONRPC::build_result( FALSE, "api_token_failure: {$params['api_token']}" );
		}

		$user_id = \User::verify_hash( $params['hash'] );
		if( empty( $user_id )) {
			return \JSONRPC::build_result( FALSE, 'invalid_hash' );
		}

		\JSONRPC::audit_log( $user_id, __NAMESPACE__ . '\\' . __FUNCTION__, json_encode( $params ) );

		if ( !empty( $params['user_id'] ) ) {
		// Check for non-empty user ID

			$user_id = $params['user_id'];
		}

	// Retrieve security codes
		$security_query = <<<SQL
  SELECT security.code
    FROM security
		   INNER JOIN xref_group_security 
				   ON security.security_id = xref_group_security.security_id
           INNER JOIN xref_user_group
                   ON xref_group_security.group_id = xref_user_group.group_id
   WHERE xref_user_group.user_id = :user_id
   GROUP BY security.security_id
   ORDER BY security.code
SQL;

		$security_stmt = \DB::dbh()->prepare( $security_query );

		$security_stmt->bindParam( ':user_id', $user_id, \PDO::PARAM_INT );

		$security_stmt->execute();

		$codes = $security_stmt->fetchAll( \PDO::FETCH_COLUMN );

		return \JSONRPC::build_result( TRUE, 'security', $codes );
	}
	
?>
