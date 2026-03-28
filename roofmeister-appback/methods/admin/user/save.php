<?php namespace admin\user;

	function save( $params ) {
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

	// Verify admin access
		if ( !\User::security_check( $user_id, 'admin' )) {
			return \JSONRPC::build_result( FALSE, 'not_authorized' );
		}

	// Specify the criteria
		$criteria = [
			'allowed_insert'  => [
				'first_name',
				'last_name',
				'username',
				'password',
				'email',
				'active'
			],
			'allowed_update'  => [
				'first_name',
				'last_name',
				'username',
				'password',
				'email',
				'active'
			],
			'required_insert' => [
				'first_name' => 'Missing First Name',
				'last_name'  => 'Missing Last Name',
				'username'   => 'Missing Username',
				'password'   => 'Missing Password',
				'email'      => 'Missing Email Address',
			],
			'required_update' => [
				'first_name' => 'Missing First Name',
				'last_name'  => 'Missing Last Name',
				'username'   => 'Missing Username',
				'email'      => 'Missing Email Address',
			]
		];

	// Check if there are group options
		$group_values = [];
		if ( !empty( $params['values']['group'] ) ) {
			$group_values = $params['values']['group'];
		}

	// Check if there are calendar options
		$calendar_values = [];
		if ( !empty( $params['values']['calendar'] ) ) {
			$calendar_values = $params['values']['calendar'];
		}

	// Validate submitted data
		$check_criteria = \Validate::verify_criteria( $criteria, $params, TRUE );

		if ( $check_criteria == 'verified' ) {
		// Validate password
			$pass_error = [];
			if ( !empty( $params['values']['password'] )) {
				$pass_error = \Validate::validate_password( $params['values']['password'] );
			}

			if ( count( $pass_error ) ) {
			// Errors existed, send back
				return \JSONRPC::build_result( FALSE, $pass_error );
			}

		// Validate duplicate username
			$dupe_params = [
				'id'    => [ 'user_id'  => ( isset( $params['where']['user_id'] ) ? $params['where']['user_id'] : 0 ) ],
				'field' => [ 'username' => $params['values']['username'] ],
			];

			$dupe_count = \DB::check_duplicate( 'user', $dupe_params );

		// Return error and username if username has been used already
			if ( $dupe_count ) {
				return \JSONRPC::build_result( FALSE, 'Username has already been taken', $params['values']['username'] );
			}

		// Format names to upper case first character
			$params['values']['first_name'] = ucwords( strip_tags( trim( $params['values']['first_name'] ) ) );
			$params['values']['last_name']  = ucwords( strip_tags( trim( $params['values']['last_name'] ) ) );

			if ( !empty( $params['values']['password'] )) {
			// Create password hash
				$params['values']['password'] = password_hash( $params['values']['password'], PASSWORD_DEFAULT );
			} elseif ( isset( $params['values']['password'] )) {
			// Blank password sent, unset it so that the criteria verification fails
				unset( $params['values']['password'] );
			}

		// Begin transaction and commit only if all queries succeed
			$pdo = \DB::dbh();

			try {
				$pdo->beginTransaction();

			// Save info to database
				$user_id = \DB::save_to_db( 'user', $params );

			// Delete data from xref table
				$xref_params  = [ 'user_id' => $user_id ];
				$xref_ignored = [ 'column'  => 'group_id', 'values' => $group_values ];
				\DB::delete_xref_data( 'xref_user_group', $xref_params, $xref_ignored );

			// Delete data from xref table
				$xref_ignored_calendar = [ 'column'  => 'calendar_id', 'values' => $calendar_values ];
				\DB::delete_xref_data( 'xref_calendar_user', $xref_params, $xref_ignored_calendar );

				if ( !empty( $group_values ) ) {
					foreach ( $group_values as $group_id ) {

						$group_data = [
							'values' => [
								'group_id' => [
									'value' => $group_id,
									'type'  => \PDO::PARAM_INT
								],
								'user_id' => [
									'value' => $user_id,
									'type'  => \PDO::PARAM_INT
								]
							]
						];

						$dupe_params = [
							'field' => [ 'group_id' => $group_id, 'user_id' => $user_id ]
						];
			
						$dupe_count = \DB::check_duplicate( 'xref_user_group', $dupe_params );

						if ( !$dupe_count ) {
						// Save info to database
							\DB::save_to_db( 'xref_user_group', $group_data );
						}

					}
				}

				if ( !empty( $calendar_values ) ) {
					foreach ( $calendar_values as $calendar_id ) {

						$calendar_data = [
							'values' => [
								'calendar_id' => [
									'value' => $calendar_id,
									'type'  => \PDO::PARAM_INT
								],
								'user_id' => [
									'value' => $user_id,
									'type'  => \PDO::PARAM_INT
								],
								'access' => [
									'value' => 1,
									'type'  => \PDO::PARAM_INT
								]
							]
						];

						$dupe_params = [
							'field' => [ 'calendar_id' => $calendar_id, 'user_id' => $user_id ]
						];
			
						$dupe_count = \DB::check_duplicate( 'xref_calendar_user', $dupe_params );

						if ( !$dupe_count ) {
						// Save info to database
							\DB::save_to_db( 'xref_calendar_user', $calendar_data );
						}

					}
				}

				$pdo->commit();

				return \JSONRPC::build_result( TRUE, 'save_success', [ 'user_id' => $user_id ] );

			} catch ( Exception $e ) {
			// Something went wrong while saving device, rollback and return

				$pdo->rollback();

				return \JSONRPC::build_result( FALSE, 'save_failed' );
			}
		}

		return \JSONRPC::build_result( FALSE, $check_criteria );
	}
	
?>
