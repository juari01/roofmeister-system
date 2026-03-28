<?php namespace admin\category;

	function get_geography( $params ) {
	/**
	 * Retrieve a list of geography options.
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

		\JSONRPC::audit_log( $user_id, __NAMESPACE__ . '\\' . __FUNCTION__, json_encode( $params ) );

	// Verify admin access
		if ( !\User::security_check( $user_id, 'admin' )) {
			return \JSONRPC::build_result( FALSE, 'not_authorized' );
		}

		if ( $params['category_id'] ) {
			$geography_query = <<<SQL
  SELECT *,
         (
           SELECT COUNT( xref_category_geography.created )
             FROM xref_category_geography
            WHERE xref_category_geography.category_id  = :category_id
              AND xref_category_geography.geography_id = geography.geography_id
         ) AS enabled
    FROM `geography`
   WHERE active = 1
   ORDER BY `geography`.name
SQL;
		} else {
			$geography_query = <<<SQL
  SELECT *
    FROM `geography`
   WHERE active = 1
   ORDER BY `geography`.name
SQL;
		}

		$geography_result = \DB::dbh()->prepare( $geography_query );

		if ( $params['category_id'] ) {
			$geography_result->bindParam( ':category_id', $params['category_id'], \PDO::PARAM_INT );
		}

		$geography_result->execute();

		$geography = $geography_result->fetchAll( \PDO::FETCH_ASSOC );

		return \JSONRPC::build_result( TRUE, 'geography', $geography );
	}

?>
