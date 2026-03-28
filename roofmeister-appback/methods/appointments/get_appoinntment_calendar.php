<?php namespace appointments;

	function get_appoinntment_calendar( $params ) {
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

		\JSONRPC::audit_log( $user_id, __NAMESPACE__ . '\\' . __FUNCTION__, json_encode( $params ) );
		
		try {	
	// Assemble WHERE clause
		$bind_params = array();
		$wheres      = array();

		if ( empty( $params['date_start'] )) {
				return _build_response( ERROR, 'missing_date_start' );
			}

			if ( empty( $params['date_end'] )) {
				return _build_response( ERROR, 'missing_date_end' );
			}

			$bind_params[':date_start'] = array(
				"value" => $params['date_start'],
				"type"  => \PDO::PARAM_STR
			);

			$bind_params[':date_end'] = array(
				"value" => $params['date_end'],
				"type"  => \PDO::PARAM_STR
			);

			$bind_params[':user_id'] = array(
				"value" => $params['user_id'],
				"type"  => \PDO::PARAM_INT
			);

	
		// $query_where = !empty( $wheres )          ? ' WHERE ' . implode( ' AND ', $wheres ) : '';
		$query_order = !empty( $params['order'] ) ? ' ORDER BY ' . $params['order']         : 'ORDER BY `appointment`.start ASC';
	
	

		$appointment_query = <<<SQL
SELECT  `appointment`.appointment_id, `customer`.customer_id, `customer`.name AS customer_name, `property`.property_id, `property`.name AS property_name, 
		`project`.project_id, `project`.name AS project_name, `appointment_type`.type_id, `appointment_type`.name AS appointment_name, `appointment_type`.active,
		`appointment_type`.color,`appointment`.start, `appointment`.end, `appointment`.description,`calendar`.calendar_id,`xref_calendar_user`.access,
		DAY( appointment.start ) AS day, MONTH( appointment.start ) AS month, YEAR( appointment.start ) AS year
		FROM `appointment`
		LEFT JOIN `customer` 		 ON `customer`.customer_id 	   = `appointment`.customer_id
		LEFT JOIN `property` 		 ON `property`.property_id 	   = `appointment`.property_id
		LEFT JOIN `project`  		 ON `project`.project_id   	   = `appointment`.project_id
		LEFT JOIN `appointment_type` ON `appointment_type`.type_id = `appointment`.type_id
		LEFT JOIN `calendar` 		 ON `calendar`.`calendar_id`   = `appointment_type`.calendar_id
		LEFT JOIN `xref_calendar_user` ON `xref_calendar_user`.`calendar_id` = `calendar`.`calendar_id`
		WHERE DATE( start ) BETWEEN :date_start AND :date_end
		AND `xref_calendar_user`.access = 1 AND `xref_calendar_user`.user_id = :user_id
		GROUP BY `appointment`.appointment_id
		$query_order
	
SQL;


		$appointment_stmt = \DB::dbh()->prepare( $appointment_query );

		\DB::bind_params( $appointment_stmt, $bind_params );

		$appointment_stmt->execute();
		
		$appointments = $appointment_stmt->fetchAll( \PDO::FETCH_ASSOC );

		$appointments_array = array();

		foreach( $appointments as $appointment ) {
			$appointments_array[ $appointment['day'] ][] = $appointment;
		}
	
	// 	}

	// }

	} catch ( Exception $e ) {

		\DB::dbh()->rollback();

	return \JSONRPC::build_result( FALSE, 'check_update' );
	}
			

		return \JSONRPC::build_result( TRUE, 'appointments', $appointments_array );
			
		
	}

?>
