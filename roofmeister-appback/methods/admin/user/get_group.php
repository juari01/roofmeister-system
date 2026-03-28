<?php namespace admin\user;

	function get_group( $params ) {
	/**
	 * Retrieve a list of group options.
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
			return \JSORPC::build_result( FALSE, 'api_token_missing' );
		}

		if ( !\JSONRPC::check_api_token( $params['api_token'] )) {
			return \JSONRPC::build_result( FALSE, "api_token_failure: {$params['api_token']}" );
		}

	// Verify hash
		$user_id = \User::verify_hash( $params['hash'] );
		if ( empty( $user_id )) {
			return \JSONRPC::build_result( FALSE, 'invalid_hash' );
		}

		\JSONRPC::audit_log( $user_id, __NAMESPACE__ . '\\' . __FUNCTION__, json_encode( $params ));

	// Verify admin access
		if ( !\User::security_check( $user_id, 'admin' )) {
			return \JSONRPC::build_result( FALSE, 'not_authorized' );
		}

		if ( $params['user_id'] ) {
			$group_query = <<<SQL
  SELECT *,
         (
           SELECT COUNT( xref_user_group.created )
             FROM xref_user_group
            WHERE xref_user_group.user_id  = :user_id
              AND xref_user_group.group_id = group.group_id
         ) AS enabled
    FROM `group`
   WHERE active = 1
   ORDER BY `group`.group
SQL;
		} else {
			$group_query = <<<SQL
  SELECT *
    FROM `group`
   WHERE active = 1
   ORDER BY `group`.group
SQL;
		}

		$group_stmt = \DB::dbh()->prepare( $group_query );

		if ( $params['user_id'] ) {
			$group_stmt->bindParam( ':user_id', $params['user_id'], \PDO::PARAM_INT );
		}

		$group_stmt->execute();

		$group = $group_stmt->fetchAll( \PDO::FETCH_ASSOC );

		return \JSONRPC::build_result( TRUE, 'group', $group );
	}

?>
