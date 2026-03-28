<?php namespace customer;

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

		\JSONRPC::audit_log( $user_id, __NAMESPACE__ . '\\' . __FUNCTION__, json_encode( $params ));

	// Assemble WHERE clause
		$bind_params = array();
		$wheres      = array();
		

		if ( !empty( $params['customer_id'] )) {
			$wheres[]                    = 'customer_id  = :customer_id';
			$bind_params[':customer_id'] = [
				'value' => $params['customer_id'],
				'type'  => \PDO::PARAM_INT
			];
		}
		
		if ( isset( $params['search'] )) {
			$wheres[]               = '( customer.name LIKE :search )';
			$bind_params[':search'] = array(
				'value' => "%{$params['search']}%",
				'type'  => \PDO::PARAM_STR
			);
		}
		
		if ( !empty( $params['limit'] )) {
			$query_limit           = 'LIMIT :offset, :limit';
			$bind_params[':limit'] = array(
				'value' => (int)$params['limit'],
				'type'  => \PDO::PARAM_INT
			);
			if ( !empty( $params['offset'] )) {
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
		$query_order = !empty( $params['order'] ) ? ' ORDER BY ' . $params['order']         : 'ORDER BY customer.name ASC';
		
		if ( isset( $params['select'] )) {
			$select = $params['select'];
		} else {
			$select = '*';
		}

		$customer_query = <<<SQL
  SELECT $select
    FROM `customer`
         $query_where
		 $query_order
		 $query_limit
SQL;

	
		$customer_stmt = \DB::dbh()->prepare( $customer_query );

		\DB::bind_params( $customer_stmt, $bind_params );

		$customer_stmt->execute();
	
		$data = array();
			foreach ( $customer_stmt->fetchAll( \PDO::FETCH_ASSOC ) as $i => $customer_row ) {
				$data[ $i ] = $customer_row;
			}
			
	if ( !empty( $params['count'] )) {
		if ( isset( $bind_params[':limit'] )) {
			unset( $bind_params[':limit'] );
		}
		if ( isset( $bind_params[':offset'] )) {
			unset( $bind_params[':offset'] );
		} 
					
	$customer_count_query = <<<SQL
 SELECT $select
   FROM `customer` 
         $query_where
         $query_order
SQL;
		$customer_count_stmt = \DB::dbh()->prepare( $customer_count_query );
		\DB::bind_params( $customer_count_stmt, $bind_params );
		$customer_count_stmt->execute();
		
		$count = $customer_count_stmt->rowCount();
	
		 return \JSONRPC::build_result( TRUE, 'customers',  array('customers' => $data, 'countresult' => $count ));
	}	
	else {
		 return \JSONRPC::build_result( TRUE, 'customers', $data );
	}

				
	}

?>
