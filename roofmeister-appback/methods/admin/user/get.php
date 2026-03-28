<?php namespace admin\user;

	function get( $params ) {
	/**
	 * Retrieve a list of users.
	 * 
	 * @param string  api_token  - The token used to authenticate the JSON-RPC client.
	 * @param int     user_id    - User ID
	 * @param int     region_id  - Region ID
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

		\JSONRPC::audit_log( $user_id, __NAMESPACE__ . '\\' . __FUNCTION__, json_encode( $params ));

	// Assemble WHERE clause
		$bind_params = [];

		if ( !empty( $params['user_id'] )){
			$wheres[]                = 'user.user_id = :user_id';
			$bind_params[':user_id'] = [
				'value' => $params['user_id'],
				'type'  => \PDO::PARAM_INT
			];
		}

		if ( isset( $params['active'] )) {
			$wheres[]               = 'user.active = :active';
			$bind_params[':active'] = [
				'value' => !empty( $params['active'] ) ? '1' : '0',
				'type'  => \PDO::PARAM_INT
			];
		}

		$query_where = !empty( $wheres )          ? ' WHERE ' . implode( ' AND ', $wheres ) : '';
		$query_order = !empty( $params['order'] ) ? ' ORDER BY ' . $params['order']         : 'ORDER BY user.active DESC, user.first_name, user.last_name';

	// Assemble SELECT based on security
		if ( \User::security_check( $user_id, 'admin' )) {
			$select = 'user.*';
		} else {
			$select = 'user.user_id, user.active, user.username, user.first_name, user.last_name, user.email';
		}

		$user_query = <<<SQL
  SELECT $select
    FROM user
         $query_where
         $query_order
SQL;
		$user_result = \DB::dbh()->prepare( $user_query );

		\DB::bind_params( $user_result, $bind_params );

		$user_result->execute();

		$data = [];

		foreach ( $user_result->fetchAll( \PDO::FETCH_ASSOC ) as $i => $user_row ) {

			$data[ $i ] = $user_row;

			if ( \User::security_check( $user_id, 'admin' )) {
				$group_query = <<<SQL
  SELECT `group`.group_id, `group`.group
    FROM xref_user_group
           LEFT JOIN `group`
                  ON xref_user_group.group_id = `group`.group_id
   WHERE user_id = :user_id
SQL;
				$group_result = \DB::dbh()->prepare( $group_query );

				$group_result->bindParam( ':user_id', $user_row['user_id'], \PDO::PARAM_INT );

				$group_result->execute();

				$group = $group_result->fetchAll( \PDO::FETCH_ASSOC );

				$data[ $i ]['group'] = $group;
			}
		}

		return \JSONRPC::build_result( TRUE, 'user', $data );
	}

?>
