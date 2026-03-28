<?php namespace property;

	function get_propcontact( $params ) {
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

		if ( !empty( $params['propcontact_id'] )) {
			$wheres[]                     = '`property_contact`.contact_id  = :propcontact_id';
			$bind_params[':propcontact_id'] = [
				'value' => $params['propcontact_id'],
				'type'  => \PDO::PARAM_INT
			];
		}
		
		if ( !empty( $params['property_id'] )) {
			$wheres[]                     = '`property_contact`.property_id  = :property_id';
			$bind_params[':property_id'] = [
				'value' => $params['property_id'],
				'type'  => \PDO::PARAM_INT
			];
		}
		
		$query_where = !empty( $wheres )          ? ' WHERE ' . implode( ' AND ', $wheres ) : '';
		$query_order = !empty( $params['order'] ) ? ' ORDER BY ' . $params['order']         : 'ORDER BY `property_contact`.company ASC';
		

		$propcontact_query = <<<SQL
		SELECT * FROM `property_contact`
		$query_where
		$query_order
	  
			 
SQL;

		$propcontact_stmt = \DB::dbh()->prepare( $propcontact_query );

		\DB::bind_params( $propcontact_stmt, $bind_params );

		$propcontact_stmt->execute();

		$propcontact_rows = $propcontact_stmt->fetchAll( \PDO::FETCH_ASSOC );

		return \JSONRPC::build_result( TRUE, 'property_contact', $propcontact_rows );
		

	}

?>
