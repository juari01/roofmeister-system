<?php namespace admin\category;

	function get( $params ) {
	/**
	 * Get
	 * Retrieve a list of categories.
	 * 
	 * @param string  api_token   - The token used to authenticate the JSON-RPC client.
	 * @param int     category_id - Selected Category ID
	 * @param int     active      - 1 - Active | 0 - Inactive
	 * @param string  order       - comma-separated list of columns with sorting order
	 * @param string  hash        - The hash for the user accessing this method.
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

		if ( !empty( $params['category_id'] )) {
			$wheres[]                    = 'category_id = :category_id';
			$bind_params[':category_id'] = [
				'value' => $params['category_id'],
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
		$query_order = !empty( $params['order'] ) ? ' ORDER BY ' . $params['order']         : 'ORDER BY active DESC, `category`.name';

		$category_query = <<<SQL
  SELECT *
    FROM `category`
         $query_where
         $query_order
SQL;
		$category_stmt = \DB::dbh()->prepare( $category_query );

		\DB::bind_params( $category_stmt, $bind_params );

		$category_stmt->execute();

		$category_row = $category_stmt->fetchAll( \PDO::FETCH_ASSOC );

		return \JSONRPC::build_result( TRUE, 'category', $category_row );
	}

?>
