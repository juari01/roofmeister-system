<?php namespace user;

	function security_check( $params ) {
	/**
	 * Check security if user has access to it
	 *
	 * @param string  api_token      - The token used to authenticate the JSON-RPC client.
	 * @param int     user_id        - User ID of user to be checked
	 * @param string  security       - The name of security action
	 * @param int     security_id    - The ID of security action
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

		if ( empty( $params['security']  ) && empty( $params['security_id'] ) ) {
		// Check for non-empty security ID or name
			return \JSONRPC::build_result( FALSE, 'errors', 'Missing Security Identifier' );
		}

		if ( !empty( $params['user_id'] ) ) {
		// Check for non-empty user ID

			$user_id = $params['user_id'];
		}


	// Assemble WHERE clause
		$bind_params = [];
		$wheres      = [];

	// Check user security for this User ID
		if ( isset( $user_id ) ) {
			$wheres[]                = 'xref_user_group.user_id = :user_id';
			$bind_params[':user_id'] = [
				'value' => $user_id,
				'type'  => \PDO::PARAM_INT
			];
		}

	// Check user security access via security name
		if ( isset( $params['security'] ) ) {
			$wheres[]                 = 'security.code = :security';
			$bind_params[':security'] = [
				'value' => $params['security'],
				'type'  => \PDO::PARAM_STR
			];
		}

	// Check user security access via security ID
		if ( isset( $params['security_id'] ) ) {
			$wheres[]                    = 'security.security_id = :security_id';
			$bind_params[':security_id'] = [
				'value' => $params['security_id'],
				'type'  => \PDO::PARAM_INT
			];
		}

		$query_where = !empty( $wheres ) ? ' WHERE ' . implode( ' AND ', $wheres ) : '';

	// Retrieve the type data
		$security_query = <<<SQL
  SELECT xref_user_group.created
    FROM xref_user_group
		   INNER JOIN xref_group_security 
				   ON xref_group_security.group_id = xref_user_group.group_id
           INNER JOIN security
                   ON xref_group_security.security_id = security.security_id
         $query_where
SQL;

		$security_result = \DB::dbh()->prepare( $security_query );
		\DB::bind_params( $security_result, $bind_params );
		$security_result->execute();

		if ( $security_result->rowCount() > 0 ) {
		// Return result that user has access to it

			return \JSONRPC::build_result( TRUE, 'has_access', TRUE );
		} else {
			// Return result that user has not access to it

			return \JSONRPC::build_result( FALSE, 'no_access', FALSE );
		}
	}
	
?>
