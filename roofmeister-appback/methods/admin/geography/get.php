<?php namespace admin\geography;

	function get( $params ) {
	/**
	 * Get
	 * Retrieve a list of categories.
	 * 
	 * @param string  api_token    - The token used to authenticate the JSON-RPC client.
	 * @param int     geography_id - Selected geography ID
	 * @param int     active       - 1 - Active | 0 - Inactive
	 * @param string  order        - comma-separated list of columns with sorting order
	 * @param string  hash         - The hash for the user accessing this method.
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

		if ( !empty( $params['geography_id'] )) {
			$wheres[]                     = 'geography_id = :geography_id';
			$bind_params[':geography_id'] = [
				'value' => $params['geography_id'],
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
		$query_order = !empty( $params['order'] ) ? ' ORDER BY ' . $params['order']         : 'ORDER BY active DESC, `geography`.name';

		$geography_query = <<<SQL
  SELECT *
    FROM `geography`
         $query_where
         $query_order
SQL;
		$geography_stmt = \DB::dbh()->prepare( $geography_query );

		\DB::bind_params( $geography_stmt, $bind_params );

		$geography_stmt->execute();

		$geography_row = $geography_stmt->fetchAll( \PDO::FETCH_ASSOC );

		return \JSONRPC::build_result( TRUE, 'geography', $geography_row );
	}

?>
