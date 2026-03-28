<?php namespace admin\category;

	function save( $params ) {
	/**
	 * Insert new category.
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
				'name',
				'active'
			],
			'allowed_update'  => [
				'name',
				'active'
			],
			'required_insert' => [
				'name' => 'Missing Name'
			],
			'required_update' => [
				'name' => 'Missing Name'
			]
		];


	// Check if there are geography options
		$geography_values = [];
		if ( !empty( $params['values']['geography'] ) ) {
			$geography_values = $params['values']['geography'];
		}

		error_log ( print_r( $params['values'] , TRUE  ));

	// Validate submitted data
		$check_criteria = \Validate::verify_criteria( $criteria, $params, TRUE );

		if ( $check_criteria == 'verified' ) {

		// Validate duplicate category
			$dupe_params = [
				'id'    => [ 'category_id' => ( isset( $params['where']['category_id'] ) ? $params['where']['category_id'] : 0 ) ],
				'field' => [ 'name'        => $params['values']['name'] ],
			];

			$dupe_count = \DB::check_duplicate( 'category', $dupe_params );

		// Return error and category if category has been used already
			if ( $dupe_count ) {
				return \JSONRPC::build_result( FALSE, 'category has already been taken', $params['values']['category'] );
			}

		// Begin transaction and commit only if all queries succeed
			try {
				\DB::dbh()->beginTransaction();

			// Save info to database
				$category_id = \DB::save_to_db( 'category', $params );

			//Delete data from xref table
				$xref_params  = [ 'category_id' => $category_id ];
				$xref_ignored = [ 'column'      => 'geography_id', 'values' => $geography_values ];
				\DB::delete_xref_data( 'xref_category_geography', $xref_params, $xref_ignored );

				if ( !empty( $geography_values ) ) {
					foreach ( $geography_values as $geography_id ) {

						$geography_data = [
							'values' => [
								'category_id' => [
									'value' => $category_id,
									'type'  => \PDO::PARAM_INT
								],
								'geography_id' => [
									'value' => $geography_id,
									'type'  => \PDO::PARAM_INT
								]
							]
						];

						$dupe_params = [
							'field' => [ 'category_id' => $category_id, 'geography_id' => $geography_id ]
						];
			
						$dupe_count = \DB::check_duplicate( 'xref_category_geography', $dupe_params );

						if ( !$dupe_count ) {
						// Save info to database
							\DB::save_to_db( 'xref_category_geography', $geography_data );
						}
					}
				}

				\DB::dbh()->commit();

				return \JSONRPC::build_result( TRUE, 'save_success', [ 'category_id' => $category_id ] );

			} catch ( Exception $e ) {
			// Something went wrong while saving device, rollback and return

				\DB::dbh()->rollback();

				return \JSONRPC::build_result( FALSE, 'save_failed' );
			}
		}

		return \JSONRPC::build_result( FALSE, $check_criteria );

	}
	
?>
