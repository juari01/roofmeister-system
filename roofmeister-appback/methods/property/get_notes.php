<?php namespace property;

	function get_notes( $params ) {
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

		if ( !empty( $params['property_id'] )) {
			$wheres[]                    = ' `xref_note_property`.property_id = :property_id';
			$bind_params[':property_id'] = [
				'value' => $params['property_id'],
				'type'  => \PDO::PARAM_INT
			];
		}

		if ( !empty( $params['note_id'] )) {
			$wheres[]                    = ' `note`.note_id = :note_id';
			$bind_params[':note_id'] = [
				'value' => $params['note_id'],
				'type'  => \PDO::PARAM_INT
			];
		}
		
		
		$query_where = !empty( $wheres )          ? ' WHERE ' . implode( ' AND ', $wheres ) : '';
		$query_order = !empty( $params['order'] ) ? ' ORDER BY ' . $params['order']         : 'ORDER BY `note`.note_id ASC';
		

		$notes_query = <<<SQL
	select `note`.note_id,`note`.created,`note`.updated,`note`.note,`note`.is_system , `xref_note_property`.property_id, `user`.user_id , `user`.first_name
	  from `note`
INNER JOIN `xref_note_property`
    	ON `xref_note_property`.note_id = `note`.note_id
INNER JOIN `user`
    	ON `user`.user_id = `note`.user_id
		$query_where
		$query_order
	  			 
SQL;

		$note_stmt = \DB::dbh()->prepare( $notes_query );

		\DB::bind_params( $note_stmt, $bind_params );

		$note_stmt->execute();

		$note_rows = $note_stmt->fetchAll( \PDO::FETCH_ASSOC );

		return \JSONRPC::build_result( TRUE, 'List note', $note_rows );
		

	}

?>
