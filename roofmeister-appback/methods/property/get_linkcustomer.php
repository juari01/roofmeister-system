<?php namespace property;

	function get_linkcustomer( $params ) {
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

		if ( !empty( $params['linkcustomer_id'] )) {
			$wheres[]                     = '`xref_customer_property`.property_id  = :linkcustomer_id';
			$bind_params[':linkcustomer_id'] = [
				'value' => $params['linkcustomer_id'],
				'type'  => \PDO::PARAM_INT
			];
		}
		
		$query_where = !empty( $wheres )          ? ' WHERE ' . implode( ' AND ', $wheres ) : '';
		$query_order = !empty( $params['order'] ) ? ' ORDER BY ' . $params['order']         : 'ORDER BY `customer`.name ASC';
		

		$property_query = <<<SQL
SELECT `xref_customer_property`.customer_id,`xref_customer_property`.`property_id`,`customer`.name
  from `xref_customer_property`
		INNER JOIN `property` 
			    ON `property`.property_id =`xref_customer_property`.property_id
		INNER JOIN `customer` 
				ON `customer`.customer_id =`xref_customer_property`.customer_id
		$query_where
		$query_order
SQL;

		$property_stmt = \DB::dbh()->prepare( $property_query );

		\DB::bind_params( $property_stmt, $bind_params );

		$property_stmt->execute();

		$property_rows = $property_stmt->fetchAll( \PDO::FETCH_ASSOC );

		return \JSONRPC::build_result( TRUE, 'customer', $property_rows );
		

	}

?>
