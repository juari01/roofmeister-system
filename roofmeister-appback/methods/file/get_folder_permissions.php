<?php namespace file;

	function get_folder_permissions( $params ) {

	/**
	 * Get Folder Permissions
	 * Retrieve list of group permissions of the folder.
	 * 
	 * @param string  api_token  - The token used to authenticate the JSON-RPC client.
	 * @param int     folder_id  - Selected Folder ID
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

		if( empty( $params['folder_id'] )) {
			return build_result( FALSE, 'missing_folder_id' );
		}

	// Assemble WHERE clause
		$bind_params[':folder_id'] = [
			'value' => $params['folder_id'],
			'type'  => \PDO::PARAM_INT
		];

		$permissions_query = <<<SQL
SELECT `group`.*, 
  IFNULL((
      SELECT count(*) 
        FROM xref_folder_group xref 
       WHERE xref.folder_id = :folder_id
         AND xref.group_id  = `group`.group_id
         AND xref.access    = 'R'
  ), 0 ) as `read` , 
  IFNULL((
      SELECT count(*) 
        FROM xref_folder_group xref 
       WHERE xref.folder_id = :folder_id
         AND xref.group_id  = `group`.group_id
         AND xref.access    = 'U'
  ), 0 ) as `upload`, 
  IFNULL((
      SELECT count(*) 
        FROM xref_folder_group xref 
       WHERE xref.folder_id = :folder_id
         AND xref.group_id  = `group`.group_id
         AND xref.access    = 'D'
  ), 0 ) as `delete`
FROM `group`
SQL;
 
		$permissions_stmt = \DB::dbh()->prepare( $permissions_query );
		\DB::bind_params( $permissions_stmt, $bind_params );
		$permissions_stmt->execute();

		$permissions_row = $permissions_stmt->fetchAll( \PDO::FETCH_ASSOC );

		$permissions_count_query = <<<SQL
SELECT count(*) as count
  FROM xref_folder_group xref 
 WHERE xref.folder_id = :folder_id
   AND xref.access    = :folder_access
SQL;

		$bind_params[':folder_access'] = [
			'value' => 'R',
			'type'  => \PDO::PARAM_STR
		];

		$permissions_count_stmt = \DB::dbh()->prepare( $permissions_count_query );
		\DB::bind_params( $permissions_count_stmt, $bind_params );
		$permissions_count_stmt->execute();

		$read_row = $permissions_count_stmt->fetch( \PDO::FETCH_ASSOC );


		$bind_params[':folder_access'] = [
			'value' => 'U',
			'type'  => \PDO::PARAM_STR
		];

		$permissions_count_stmt = \DB::dbh()->prepare( $permissions_count_query );
		\DB::bind_params( $permissions_count_stmt, $bind_params );
		$permissions_count_stmt->execute();

		$upload_row = $permissions_count_stmt->fetch( \PDO::FETCH_ASSOC );

		$bind_params[':folder_access'] = [
			'value' => 'D',
			'type'  => \PDO::PARAM_STR
		];

		$permissions_count_stmt = \DB::dbh()->prepare( $permissions_count_query );
		\DB::bind_params( $permissions_count_stmt, $bind_params );
		$permissions_count_stmt->execute();

		$delete_row = $permissions_count_stmt->fetch( \PDO::FETCH_ASSOC );
		
		return \JSONRPC::build_result( TRUE, 'folder_permissions', [ 
			'permissions' => $permissions_row, 
			'read'        => $read_row['count'],
			'upload'      => $upload_row['count'],
			'delete'      => $delete_row['count'] 
		] );

	}

?>
