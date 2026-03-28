<?php namespace user;

	function get_peer( $params ) {
	/**
	 * Get
	 * Retrieve a list of user peer.
	 * 
	 * @param string  api_token  - The token used to authenticate the JSON-RPC client.
	 * @param int     user_id    - Selected User ID
	 * @param string  order      - comma-separated list of columns with sorting order
	 * @param string  hash       - The hash for the user accessing this method.
	 *
	 * @return array build_result()
	 */

	require( \env::$paths['methods'] . '/../config.php' );

	\function_init( [ 'build_result', 'check_api_token', 'dbh', 'bind_params', 'verify_hash', 'security_check', 'audit_log' ] );

	// Verify authorized API Token
		if ( empty( $params['api_token'] )) {
			return build_result( FALSE, 'api_token_missing' );
		}

		if ( !\check_api_token( $params['api_token'] )) {
			return build_result( FALSE, "api_token_failure: {$params['api_token']}" );
		}

	// Verify hash
		$user_id = verify_hash( $params['hash'] );
		if ( empty( $user_id )) {
			return build_result( FALSE, 'invalid_hash' );
		}

		audit_log( $user_id, __NAMESPACE__ . '\\' . __FUNCTION__, json_encode( $params ) );

	// Assemble WHERE clause
		$bind_params = [];

	// Getting a single user by id
		if( !empty( $params['user_id'] )){
			$wheres[]                = "user_peer.user_id = :user_id";
			$bind_params[":user_id"] = [
				'value' => $params['user_id'],
				'type'  => \PDO::PARAM_INT
			];
		}

		$query_where = ( !empty( $wheres ) ? " WHERE " . implode( " AND ", $wheres ) : ""  );
		$query_order = ( !empty( $params['order'] ) ? " ORDER BY " . $params['order'] : "" );

		$peer_query = <<<SQL
  SELECT user_peer.*, user.first_name, user.last_name
    FROM user_peer
             INNER JOIN user ON user_peer.user_id = user.user_id
   $query_where
   $query_order
SQL;

		$peer_result = dbh()->prepare( $peer_query );
		\bind_params( $peer_result, $bind_params );
		$peer_result->execute( );

		$data = $peer_result->fetchAll( \PDO::FETCH_ASSOC );
 
		return build_result( TRUE, 'user_peer', $data );
	}

?>