<?php namespace admin\group;

	function get_security( $params ) {
	/**
	 * Get Security
	 * Retrieve a list of security options.
	 * 
	 * @param string  api_token - The token used to authenticate the JSON-RPC client.
	 * @param string  hash      - The hash for the user accessing this method.
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

	// Verify admin access
		if ( !\User::security_check( $user_id, 'admin' )) {
			return \JSONRPC::build_result( FALSE, 'not_authorized' );
		}

		if ( $params['group_id'] ) {
			$security_query = <<<SQL
  SELECT *,
         (
           SELECT COUNT( xref_group_security.created )
             FROM xref_group_security
            WHERE xref_group_security.group_id    = :group_id
              AND xref_group_security.security_id = security.security_id
         ) AS enabled
    FROM security
   WHERE active = 1
   ORDER BY name
SQL;
		} else {
			$security_query = <<<SQL
  SELECT *
    FROM security
   WHERE active = 1
   ORDER BY name
SQL;
		}

		$security_stmt = \DB::dbh()->prepare( $security_query );

		if ( $params['group_id'] ) {
			$security_stmt->bindParam( ':group_id', $params['group_id'], \PDO::PARAM_INT );
		}

		$security_stmt->execute();

		$security = $security_stmt->fetchAll( \PDO::FETCH_ASSOC );

		return \JSONRPC::build_result( TRUE, 'security', $security );
	}

?>
