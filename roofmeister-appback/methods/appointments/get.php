<?php namespace appointments;

	function get( $params ) {
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

	// Assemble WHERE clause
		$bind_params = array();
		$wheres      = array();

		
		if ( !empty( $params['appointment_id'] )) {
			$wheres[]                     = '`appointment`.appointment_id  = :appointment_id';
			$bind_params[':appointment_id'] = [
				'value' => $params['appointment_id'],
				'type'  => \PDO::PARAM_INT
			];
		}

		if( isset( $params['search'] )) {
			 $wheres[]               = '( `customer`.name LIKE :search OR `property`.name LIKE :search OR `project`.name LIKE :search OR `appointment_type`.name LIKE :search 
										 OR  `appointment`.start LIKE :search OR `appointment`.end LIKE :search OR `appointment`.description LIKE :search)';
			$bind_params[':search'] = array(
				'value' => "%{$params['search']}%",
				'type'  => \PDO::PARAM_STR
			);
		}
		
		if( !empty( $params['limit'] )) {
			$query_limit           = 'LIMIT :offset, :limit';
			$bind_params[':limit'] = array(
				'value' => (int)$params['limit'],
				'type'  => \PDO::PARAM_INT
			);
			if( !empty( $params['offset'] )) {
				$bind_params[':offset'] = array(
					'value' => (int)$params['offset'],
					'type'  => \PDO::PARAM_INT
				);
			} else {
				$bind_params[':offset'] = array(
					'value' => 0,
					'type'  => \PDO::PARAM_INT
				);
			}
		} else {
			$query_limit = "";
		}
		
		$query_where = !empty( $wheres )          ? ' WHERE ' . implode( ' AND ', $wheres ) : '';
		$query_order = !empty( $params['order'] ) ? ' ORDER BY ' . $params['order']         : 'ORDER BY `appointment`.start ASC';
		
		if( isset( $params['select'] )) {
			$select = $params['select'];
		} else {
			$select = '*';
		}
		
		$appointment_query = <<<SQL
  SELECT  `appointment`.appointment_id, `customer`.customer_id, `customer`.name AS customer_name, `property`.property_id, `property`.name AS property_name, 
  		  `project`.project_id, `project`.name AS project_name, `appointment_type`.type_id, `appointment_type`.name AS appointment_name, `appointment_type`.active, 
		  `appointment`.start, `appointment`.end, `appointment`.description
			FROM `appointment`
			LEFT JOIN `customer` 		 ON `customer`.customer_id 	   = `appointment`.customer_id
			LEFT JOIN `property` 		 ON `property`.property_id 	   = `appointment`.property_id
			LEFT JOIN `project`  		 ON `project`.project_id   	   = `appointment`.project_id
			LEFT JOIN `appointment_type` ON `appointment_type`.type_id = `appointment`.type_id
			$query_where
			$query_order
			$query_limit
SQL;

		$appointment_stmt = \DB::dbh()->prepare( $appointment_query );

		\DB::bind_params( $appointment_stmt, $bind_params );

		$appointment_stmt->execute();

		$data = array();
			foreach ( $appointment_row = $appointment_stmt->fetchAll( \PDO::FETCH_ASSOC ) as $i => $appointment_row ) {
				$data[ $i ] = $appointment_row;
			}
			
			if( !empty( $params['count'] )) {
		if( isset( $bind_params[':limit'] )) {
			unset( $bind_params[':limit'] );
		}
		if( isset( $bind_params[':offset'] )) {
			unset( $bind_params[':offset'] );
		} 
		
		$appointment_count = <<<SQL
 SELECT $select
   FROM `appointment` 
         $query_where
         $query_order
SQL;

		$result_count = \DB::dbh()->prepare( $appointment_count );
		\DB::bind_params( $result_count, $bind_params );
		$result_count->execute();
		
		$count = $result_count->rowCount();
		
		 return \JSONRPC::build_result( TRUE, 'appointment',  array('appointment' => $data, 'countresult' => $count ));
		
		} else {
			
			return \JSONRPC::build_result( TRUE, 'appointment', $data );
			
		}	

	}

?>
