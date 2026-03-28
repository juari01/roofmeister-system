<?php namespace user;

	function group_check( $params ) {
	/**
	 * Check group if user has access to it
	 *
	 * @param string  api_token   - The token used to authenticate the JSON-RPC client.
	 * @param int     user_id     - User ID of user to be checked
	 * @param string  group       - The name of group action
	 * @param int     group_id    - The ID of group action
	 *
	 * @return array build_result()
	 */

	// Read application config

		require( \env::$paths['methods'] . '/../config.php' );

		\function_init( [ 'dbh', 'build_result', 'check_api_token', 'db_connect', 'bind_params', 'verify_hash', 'audit_log' ] );

	// Verify authorized API Token
		if ( empty( $params['api_token'] ) ) {
			return build_result( FALSE, 'api_token_missing' );
		}

		if ( !\check_api_token( $params['api_token'] ) ) {
			return build_result( FALSE, "api_token_failure: {$params['api_token']}" );
		}

		$user_id = verify_hash( $params['hash'] );
		if( empty( $user_id )) {
			return build_result( FALSE, 'invalid_hash' );
		}

		audit_log( $user_id, __NAMESPACE__ . '\\' . __FUNCTION__, json_encode( $params ) );

		if ( empty( $params['group']  ) && empty( $params['group_id'] ) ) {
		// Check for non-empty group ID or name
			return build_result( FALSE, 'errors', 'Missing Group Identifier' );
		}

		if ( !empty( $params['user_id'] ) ) {
		// Check for non-empty user ID
			$user_id = $params['user_id'];
		}


	// Assemble WHERE clause
		$bind_params = [];
		$wheres      = [];

	// Check user group for this User ID
		if ( isset( $user_id ) ) {
			$wheres[]                = 'xref_user_group.user_id = :user_id';
			$bind_params[':user_id'] = [
				'value' => $user_id,
				'type'  => \PDO::PARAM_INT
			];
		}

	// Check user group access via group name
		if ( isset( $params['group'] ) ) {
			$wheres[]                 = 'group.group = :group';
			$bind_params[':group'] = [
				'value' => $params['group'],
				'type'  => \PDO::PARAM_STR
			];
		}

	// Check user group access via group ID
		if ( isset( $params['group_id'] ) ) {
			$wheres[]                 = 'group.group_id = :group_id';
			$bind_params[':group_id'] = [
				'value' => $params['group_id'],
				'type'  => \PDO::PARAM_INT
			];
		}

		$query_where = !empty( $wheres ) ? ' WHERE ' . implode( ' AND ', $wheres ) : '';

	// Retrieve the type data
		$group_query = <<<SQL
  SELECT xref_user_group.created
    FROM xref_user_group
		   INNER JOIN `group` 
				   ON xref_user_group.group_id = group.group_id
         $query_where
SQL;

		$group_result = dbh()->prepare( $group_query );
		bind_params( $group_result, $bind_params );
		$group_result->execute();

		if ( $group_result->rowCount() > 0 ) {
			// Return result that user has access to it

				return build_result( TRUE, 'has_access', TRUE );
		} else {
			// Return result that user has not access to it

			return build_result( FALSE, 'no_access', FALSE );
		}
	}
	
?>