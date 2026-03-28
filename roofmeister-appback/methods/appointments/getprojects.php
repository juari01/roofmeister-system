<?php namespace appointments;

	function getprojects( $params ) {
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

		if ( !empty( $params['project_id'] )) {
			$wheres[]                     = '`project`.project_id  = :project_id';
			$bind_params[':project_id'] = [
				'value' => $params['project_id'],
				'type'  => \PDO::PARAM_INT
			];
		}
		
		if( isset( $params['search'] )) {
			$wheres[]               = '( project.name LIKE :search OR `customer`.name LIKE :search OR `property`.name LIKE :search OR `project`.description LIKE :search )';
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
		$query_order = !empty( $params['order'] ) ? ' ORDER BY ' . $params['order']         : 'ORDER BY project.name ASC';
		
		if( isset( $params['select'] )) {
			$select = $params['select'];
		} else {
			$select = '*';
		}
		
		$project_query = <<<SQL
  SELECT `project`.project_id, `customer`.customer_id,`property`.property_id, `customer`.name AS customer_name, `property`.name AS property_name, `project`.name AS project_name, `project`.description
FROM `PROJECT`
		INNER JOIN `customer` ON `customer`.customer_id=`project`.customer_id
        INNER JOIN `property` ON `property`.property_id=`project`.property_id
         $query_where
         $query_order
		 $query_limit
SQL;

		$project_stmt = \DB::dbh()->prepare( $project_query );

		\DB::bind_params( $project_stmt, $bind_params );

		$project_stmt->execute();

		$data = array();
			foreach ( $project_row = $project_stmt->fetchAll( \PDO::FETCH_ASSOC ) as $i => $project_row ) {
				$data[ $i ] = $project_row;
			}
			
			if( !empty( $params['count'] )) {
		if( isset( $bind_params[':limit'] )) {
			unset( $bind_params[':limit'] );
		}
		if( isset( $bind_params[':offset'] )) {
			unset( $bind_params[':offset'] );
		} 
		
		$project_count = <<<SQL
 SELECT $select
   FROM `project` 
         $query_where
         $query_order
SQL;

		$result_count = \DB::dbh()->prepare( $project_count );
		\DB::bind_params( $result_count, $bind_params );
		$result_count->execute();
		
		$count = $result_count->rowCount();
		
		 return \JSONRPC::build_result( TRUE, 'project',  array('project' => $data, 'countresult' => $count ));
		
		} else {
			
			return \JSONRPC::build_result( TRUE, 'project', $data );
			
		}	

	}

?>
