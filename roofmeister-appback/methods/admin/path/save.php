<?php namespace admin\path;

	function save( $params ) {
	/**
	 * Insert new paths.
	 *
	 * @param string  api_token - The token used to authenticate the JSON-RPC client.
	 * @param array   values    - Array of field values
	 * @param array   where     - Array of where values
	 * @param string  hash      - The hash for the user accessing this method.
	 *
	 * @return array build_result()
	 */

	// Read application config
		require( \env::$paths['methods'] . '/../config.php' );

		\function_init( [ 'build_result', 'check_api_token', 'dbh', 'save_to_db', 'verify_hash', 'verify_criteria', 'bind_params', 'delete_xref_data', 'check_duplicate', 'security_check', 'audit_log' ] );

	// Verify authorized API Token
		if ( empty( $params['api_token'] )) {
			return build_result( FALSE, 'api_token_missing' );
		}

		if ( !\check_api_token( $params['api_token'] ) ) {
			return build_result( FALSE, "api_token_failure: {$params['api_token']}" );
		}

	// Verify user hash
		$user_id = \verify_hash( $params['hash'] );
		if ( empty( $user_id )) {
			return build_result( FALSE, 'invalid_hash' );
		}

		audit_log( $user_id, __NAMESPACE__ . '\\' . __FUNCTION__, json_encode( $params ));

	// Verify admin access
		if ( !security_check( $user_id, 'admin' )) {
			return build_result( FALSE, 'not_authorized' );
		}

	// Specify the criteria
		$criteria = [
			'allowed_update'  => [
				'folder_id'
			],
			'required_update' => [
				'folder_id' => 'Missing Folder Id'
			]
		];
 
		$check_criteria = verify_criteria( $criteria, $params, TRUE );

		if ( $check_criteria == "verified" ) {
			// Begin transaction and commit only if all queries succeed
			try {
				dbh()->beginTransaction();

			// Save info to database
				$path_id = \save_to_db( 'file_path', $params );

				dbh()->commit();

				return build_result( TRUE, 'save_success', [ 'path_id' => $path_id ] );

			} catch ( Exception $e ) {
			// Something went wrong while saving device, rollback and return

				dbh()->rollback();

				return build_result( FALSE, 'save_failed' );
			}
 
		}

		return build_result( FALSE, $check_criteria );
	}

?>
