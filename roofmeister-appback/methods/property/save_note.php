<?php namespace property;

	function save_note( $params ) {
	/**
	 * Insert new property.
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

		\JSONRPC::audit_log( $user_id, __NAMESPACE__ . '\\' . __FUNCTION__, json_encode( $params ));

	// Verify admin access
		if ( !\User::security_check( $user_id, 'admin' )) {
			return \JSONRPC::build_result( FALSE, 'not_authorized' );
		}

		
	$user_id 	 = $params['user_id'];
	$note	 	 = $params['note'];
	$property_id = $params['property_id'];
	$cust_note_id     = $params['note_id'];

	if ( $cust_note_id !== "0" ) {

		$update_note_query = <<<SQL
  UPDATE note
	 SET note    = :note
     WHERE note_id = :note_id
SQL;
		$update_stmt = \DB::dbh()->prepare( $update_note_query );
		$update_stmt ->execute( array(
			':note_id' => $cust_note_id,
			':note'    => $note	
			));
 	} 

	else  {

		$insert_note_query = <<<SQL
  INSERT INTO note
	 SET user_id = :user_id,
		 note    = :note
SQL;
	$insert_stmt = \DB::dbh()->prepare( $insert_note_query );
	$insert_stmt ->execute( array(
			':user_id' => $user_id,
			':note'    => $note
		));

	$get_last_id_query = <<<SQL
	SELECT LAST_INSERT_ID() as last_note_id
SQL;

	$last_insert_id_stmt = \DB::dbh()->prepare($get_last_id_query);
	$last_insert_id_stmt->execute();
	$last_insert_id_row = $last_insert_id_stmt->fetch(\PDO::FETCH_ASSOC);
	$last_id_row = $last_insert_id_row['last_note_id'];


	$insert_note_xref = <<<SQL
  INSERT INTO xref_note_property
	 SET note_id 	 = :note_id,
		 property_id = :property_id
SQL;

	$last_insert_note_stmt = \DB::dbh()->prepare($insert_note_xref);

	$last_insert_note_stmt ->execute( array(
		':note_id' 		=> $last_id_row,
		':property_id'  => $property_id
	));
		
	}

	return \JSONRPC::build_result( TRUE, 'save note');
	}
		
?>