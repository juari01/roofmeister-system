<?php namespace customer;

	function add_linkproperty( $params ) {
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

				
	$check_query = <<<SQL
 SELECT *
   FROM xref_customer_property
  WHERE customer_id = :customer_id
	AND property_id = :property_id
SQL;

	$check_stmt = \DB::dbh()->prepare( $check_query );
	
	
	$add_link_customer_query = <<<SQL
  INSERT INTO xref_customer_property
	 SET customer_id   = :customer_id,
			property_id	  = :property_id
SQL;

	$add_link_customer_stmt = \DB::dbh()->prepare( $add_link_customer_query );
	
	$check_stmt->execute( array( 
	  ':customer_id' => $params['addxrefcustomer_id'],
	  ':property_id' => $params['addxrefproperty_id']
	) );

	if ( !$check_stmt->rowCount() ) {
		$add_link_customer_stmt ->execute([
		  ':customer_id' => $params['addxrefcustomer_id'],
		  ':property_id' => $params['addxrefproperty_id']
	]);
	}

	  return \JSONRPC::build_result( TRUE, 'addxref_customer_property');
	}
?>
