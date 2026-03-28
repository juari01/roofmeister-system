<?php namespace file;

	function folder_move( $params ) {
	/**
	 * Insert new customer.
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

	// Verify admin access
		if ( !\User::security_check( $user_id, 'admin' )) {
			return \JSONRPC::build_result( FALSE, 'not_authorized' );
		}

	// Specify the criteria
	$criteria = [
		'allowed_insert'  => [],
		'allowed_update'  => [
			'parent_id'
		],
		'required_insert' => [],
		'required_update' => []
	];

	// Validate submitted data
		$check_criteria = \Validate::verify_criteria( $criteria, $params, TRUE );

		if ( $check_criteria == 'verified' ) {
			
	
			try {

				\DB::dbh()->beginTransaction();

				$folder_id = \DB::save_to_db( 'file_folder', $params );

				\DB::dbh()->commit();

				return \JSONRPC::build_result( TRUE, 'save_success', [ 'folder_id' => $folder_id ] );

			} catch ( Exception $e ) {
			// Something went wrong while saving device, rollback and return

				\DB::dbh()->rollback();

				return \JSONRPC::build_result( FALSE, 'save_failed' );
			}
			
		}

		return \JSONRPC::build_result( FALSE, $check_criteria );

		

	}
	
?>
