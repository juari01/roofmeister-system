<?php namespace project;

	function getlinkproperty( $params ) {
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

		if ( !empty( $params['projcustomer_id'] )) {
			$wheres[]                    = '`xref_customer_property`.customer_id  = :projcustomer_id';
			$bind_params[':projcustomer_id'] = [
				'value' => $params['projcustomer_id'],
				'type'  => \PDO::PARAM_INT
			];
		}

		
		if ( !empty( $params['property_id'] )) {
			$wheres[]                     = '`property`.property_id  = :property_id';
			$bind_params[':property_id'] = [
				'value' => $params['property_id'],
				'type'  => \PDO::PARAM_INT
			];
		}
		
		if ( isset( $params['search'] )) {
			$wheres[]               = '( property.name LIKE :search OR property.address1 LIKE :search OR property.address2 LIKE :search )';
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
		$query_order = !empty( $params['order'] ) ? ' ORDER BY ' . $params['order']         : 'ORDER BY property.name ASC';
		
		if ( isset( $params['select'] )) {
			$select = $params['select'];
		} else {
			$select = '*';
		}
		
		 
		 $property_query = <<<SQL
  SELECT `xref_customer_property`.customer_id,`xref_customer_property`.`property_id`,`state`.state_id,`property`.name,`property`.address1,`property`.address2,`property`.city,`state`.state,`property`.zip, `property`.type_id
	from `xref_customer_property`
			INNER JOIN `property` 
					ON `property`.property_id =`xref_customer_property`.property_id
        	INNER JOIN `customer` 
					ON `customer`.customer_id =`xref_customer_property`.customer_id
        	INNER JOIN `state` 
					ON `property`.state_id =`state`.state_id
         $query_where
         $query_order
		 $query_limit
SQL;

		$property_stmt = \DB::dbh()->prepare( $property_query );

		\DB::bind_params( $property_stmt, $bind_params );

		$property_stmt->execute();

		$data = array();
			foreach ( $property_rows = $property_stmt->fetchAll( \PDO::FETCH_ASSOC ) as $i => $property_rows ) {
				$data[ $i ] = $property_rows;
			}
			
		if ( !empty( $params['count'] )) {

			if ( isset( $bind_params[':limit'] )) {
				unset( $bind_params[':limit'] );
			}

			if( isset( $bind_params[':offset'] )) {
				unset( $bind_params[':offset'] );
			} 
		
		$property_count_query = <<<SQL
 SELECT $select
   FROM `property` 
        $query_where
        $query_order
SQL;

		$property_count_stmt = \DB::dbh()->prepare( $property_count_query );
		\DB::bind_params( $property_count_stmt, $bind_params );
		$property_count_stmt->execute();
		
		$count = $property_count_stmt->rowCount();
		
		 return \JSONRPC::build_result( TRUE, 'property',  array('property' => $data, 'countresult' => $count ));
		
		} else {
			
		return \JSONRPC::build_result( TRUE, 'property', $data );
			
		}	

	}

?>
