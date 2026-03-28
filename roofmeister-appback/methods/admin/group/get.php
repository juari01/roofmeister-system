<?php namespace admin\group;

	function get( $params ) {
	/**
	 * Get
	 * Retrieve a list of groups.
	 * 
	 * @param string  api_token  - The token used to authenticate the JSON-RPC client.
	 * @param int     group_id   - Selected Group ID
	 * @param int     active     - 1 - Active | 0 - Inactive
	 * @param string  order      - comma-separated list of columns with sorting order
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

	// Verify admin access
		if ( !\User::security_check( $user_id, 'admin' )) {
			return \JSONRPC::build_result( FALSE, 'not_authorized' );
		}

	// Assemble WHERE clause
		$bind_params = [];

		if ( !empty( $params['group_id'] )) {
			$wheres[]                 = 'group_id = :group_id';
			$bind_params[':group_id'] = [
				'value' => $params['group_id'],
				'type'  => \PDO::PARAM_INT
			];
		}

		if ( isset( $params['active'] )) {
			$wheres[]               = 'active = :active';
			$bind_params[':active'] = [
				'value' => !empty( $params['active'] ) ? '1' : '0',
				'type'  => \PDO::PARAM_INT
			];
		}

		$query_where = !empty( $wheres )          ? ' WHERE ' . implode( ' AND ', $wheres ) : '';
		$query_order = !empty( $params['order'] ) ? ' ORDER BY ' . $params['order']         : 'ORDER BY active DESC, `group`.group';

		$group_query = <<<SQL
  SELECT *
    FROM `group`
         $query_where
         $query_order
SQL;
		$group_stmt = \DB::dbh()->prepare( $group_query );

		\DB::bind_params( $group_stmt, $bind_params );

		$group_stmt->execute();

		$data = [];
		$i    = 0;
		foreach ( $group_stmt->fetchAll( \PDO::FETCH_ASSOC ) as $i => $group_row ) {
			$data[ $i ] = $group_row;

			if ( \User::security_check( $user_id, 'admin' )) {
				$security_query = <<<SQL
  SELECT security.security_id, security.name, security.description
    FROM xref_group_security
            LEFT JOIN security
                   ON xref_group_security.security_id = security.security_id
   WHERE group_id = :group_id
SQL;
				$security_result = \DB::dbh()->prepare( $security_query );

				$security_result->bindParam( ':group_id', $group_row['group_id'], \PDO::PARAM_INT );

				$security_result->execute();

				$security = $security_result->fetchAll( \PDO::FETCH_ASSOC );

				$data[ $i ]['security'] = $security;
			}
		}

		return \JSONRPC::build_result( TRUE, 'group', $data );
	}

?>
