<?php namespace dashboard;

	function savecalendarcheck( $params ) {
	/**
	 * Register new user.
	 *
	 * @param string  api_token - The token used to authenticate the JSON-RPC client.
	 * @param array   values    - Array of field values
	 * @param array   where     - Array of where values
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
			return \JSONRPC::build_result( FALSE, 'api_token_missing' );
		}

		if ( !\JSONRPC::check_api_token( $params['api_token'] ) ) {
			return \JSONRPC::build_result( FALSE, "api_token_failure: {$params['api_token']}" );
		}

	// Verify user hash
		$user_id = \User::verify_hash( $params['hash'] );
		if ( empty( $user_id )) {
			return \JSONRPC::build_result( FALSE, 'invalid_hash' );
		}

		\JSONRPC::audit_log( $user_id, __NAMESPACE__ . '\\' . __FUNCTION__, json_encode( $params ) );

		

	try {	

		$calendar_values = [];

	if ( !empty( $params['values']['calendarlist'] )) {
			
		$calendar_values = $params['values']['calendarlist'];

		if ( !empty( $calendar_values )) {
			foreach ( $calendar_values as $calendar_id ) { 
	
		$xref_query = <<<SQL
update xref_calendar_user
   SET access = 1
 WHERE calendar_id = :calendar_id 
   and user_id  = :user_id
SQL;
		$xref_stmt = \DB::dbh()->prepare( $xref_query );
		$xref_stmt->bindParam( ':calendar_id', $calendar_id, \PDO::PARAM_INT );
		$xref_stmt->bindParam( ':user_id', $params['user_id'], \PDO::PARAM_INT );
		$xref_stmt->execute();
		
			}

		}
	

	$implode_calendar_values = implode(",", $calendar_values); 
	$xref_access_query = <<<SQL
SELECT calendar_id FROM `xref_calendar_user`
 WHERE calendar_id NOT IN ($implode_calendar_values)
   and user_id = :user_id
SQL;

	$xref_access_query_stmt = \DB::dbh()->prepare( $xref_access_query );
	$xref_access_query_stmt->bindParam( ':user_id', $params['user_id'], \PDO::PARAM_INT );
	$xref_access_query_stmt->execute();
	
	$xref_access_query_rows = $xref_access_query_stmt->fetchall( \PDO::FETCH_ASSOC );

	if ( !empty( $xref_access_query_rows )) {

		foreach ( $xref_access_query_rows as $upd_calendar_id ) { 
	
		$xref_update_query = <<<SQL
update xref_calendar_user
   SET access = 0
 WHERE calendar_id = :calendar_id 
   and user_id  = :user_id
SQL;
		$xref_update_stmt = \DB::dbh()->prepare( $xref_update_query );
		$xref_update_stmt->bindParam( ':calendar_id', $upd_calendar_id['calendar_id'], \PDO::PARAM_INT );
		$xref_update_stmt->bindParam( ':user_id', $params['user_id'], \PDO::PARAM_INT );
		$xref_update_stmt->execute();

		}
	}
	
	} else {

	$xref_query = <<<SQL
update xref_calendar_user
   SET access = 0
 WHERE user_id  = :user_id
SQL;
	$xref_stmt = \DB::dbh()->prepare( $xref_query );
	$xref_stmt->bindParam( ':user_id', $params['user_id'], \PDO::PARAM_INT );
	$xref_stmt->execute();
		
	}

		return \JSONRPC::build_result( TRUE, 'check_update' );

	} catch ( Exception $e ) {

			\DB::dbh()->rollback();

		return \JSONRPC::build_result( FALSE, 'check_update' );
	}


	}
	
?>