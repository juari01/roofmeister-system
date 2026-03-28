<?php namespace admin\group;

	function save( $params ) {
	/**
	 * Insert new group.
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
			'allowed_insert'  => [
				'group',
				'active'
			],
			'allowed_update'  => [
				'group',
				'active'
			],
			'required_insert' => [
				'group' => 'Missing Group'
			],
			'required_update' => [
				'group' => 'Missing Group'
			]
		];


	// Check if there are security options
		$security_values = [];
		if ( !empty( $params['values']['security'] ) ) {
			$security_values = $params['values']['security'];
		}

	// Validate submitted data
		$check_criteria = \Validate::verify_criteria( $criteria, $params, TRUE );

		if ( $check_criteria == 'verified' ) {

		// Validate duplicate group
			$dupe_params = [
				'id'    => [ 'group_id' => ( isset( $params['where']['group_id'] ) ? $params['where']['group_id'] : 0 ) ],
				'field' => [ 'group'    => $params['values']['group'] ],
			];

			$dupe_count = \DB::check_duplicate( 'group', $dupe_params );

		// Return error and group if group has been used already
			if ( $dupe_count ) {
				return \JSONRPC::build_result( FALSE, 'Group has already been taken', $params['values']['group'] );
			}

		// Begin transaction and commit only if all queries succeed
			try {
				\DB::dbh()->beginTransaction();

			// Save info to database
				$group_id = \DB::save_to_db( 'group', $params );

			//Delete data from xref table
				$xref_params  = [ 'group_id' => $group_id ];
				$xref_ignored = [ 'column'  => 'security_id', 'values' => $security_values ];
				\DB::delete_xref_data( 'xref_group_security', $xref_params, $xref_ignored );

				if ( !empty( $security_values ) ) {
					foreach ( $security_values as $security_id ) {

						$security_data = [
							'values' => [
								'group_id' => [
									'value' => $group_id,
									'type'  => \PDO::PARAM_INT
								],
								'security_id' => [
									'value' => $security_id,
									'type'  => \PDO::PARAM_INT
								]
							]
						];

						$dupe_params = [
							'field' => [ 'group_id' => $group_id, 'security_id' => $security_id ]
						];
			
						$dupe_count = \DB::check_duplicate( 'xref_group_security', $dupe_params );

						if ( !$dupe_count ) {
						// Save info to database
							\DB::save_to_db( 'xref_group_security', $security_data );
						}
					}
				}
				
				\DB::dbh()->commit();

				return \JSONRPC::build_result( TRUE, 'save_success', [ 'group_id' => $group_id ] );

			} catch ( Exception $e ) {
			// Something went wrong while saving device, rollback and return

				\DB::dbh()->rollback();

				return \JSONRPC::build_result( FALSE, 'save_failed' );
			}
		}

		return \JSONRPC::build_result( FALSE, $check_criteria );

	}
	
?>
