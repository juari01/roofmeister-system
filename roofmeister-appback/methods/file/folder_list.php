<?php namespace file;

	function folder_list( $params ) {
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

		if( !isset( $params['folder_id'] )) {
			return build_result( FALSE, 'missing_folder_id' );
		}

	  $data['breadcrumbs'] = \folder::folder_breadcrumbs( $params['folder_id'] );

		$bind_params = [];
		$wheres      = [];
		

		if ( !empty( $params['folder_id'] ) ) {
			$wheres[]                  = 'file_folder.parent_id = :folder_id';
			$bind_params[':folder_id'] = [
				'value' => $params['folder_id'],
				'type'  => \PDO::PARAM_INT
			];
		} else {
			$wheres[] = '( file_folder.parent_id IS NULL OR file_folder.parent_id = 0 )';
		}


		$bind_params[':user_id'] = [
			'value' => $user_id,
			'type'  => \PDO::PARAM_INT
		];

		
		$query_where        = ( !empty( $wheres ) ? ' WHERE ' . implode( ' AND ', $wheres ) : ''  );
		$query_group        = ( !empty( $params['group'] ) ? " GROUP BY " . $params['group'] : ' GROUP BY file_folder.folder_id' );
		$query_order        = ( !empty( $params['order'] ) ? ' ORDER BY ' . $params['order'] : ' ORDER BY file_folder.name' );

		$folder_query = <<<SQL
  SELECT file_folder.*, 
         (
         	SELECT GROUP_CONCAT(xref_folder_group.access)
							    FROM xref_user_group
							           INNER JOIN xref_folder_group 
							                   ON xref_folder_group.group_id = xref_user_group.group_id
							   WHERE xref_user_group.user_id     = :user_id
         ) AS folder_access
    FROM file_folder
             LEFT JOIN file_folder AS sub_file_folder ON file_folder.folder_id = sub_file_folder.parent_id
             LEFT JOIN xref_folder_file ON file_folder.folder_id = xref_folder_file.folder_id
   $query_where
   $query_group
   $query_order
SQL;
	
	$folder_stmt = \DB::dbh()->prepare( $folder_query );

				   \DB::bind_params( $folder_stmt, $bind_params );

	$folder_stmt->execute();

	$data['folders'] = $folder_stmt->fetchAll( \PDO::FETCH_ASSOC );
	$data['files']   = [];




//Display file 

	$folder_idfile = !empty( $params['folder_id'] ) ? ' AND xref_folder_file.folder_id = :folder_id' : 'AND xref_folder_file.folder_id IS NULL';
	$folder_idtext = !empty( $params['folder_id'] ) ? $params['folder_id'] : '';
	$search_file = !empty( $params['search'] ) ? ' AND xref_folder_file.name LIKE :search' : '';
	$search_text = !empty( $params['search'] ) ? "%{$params['search']}%" : '';

	$file_query1 = <<<SQL
SELECT xref_folder_file.*,
 file.hash,
 file.type, 
 (
	 SELECT GROUP_CONCAT(xref_file_group.access)
						FROM xref_user_group
							   INNER JOIN xref_file_group 
									   ON xref_file_group.group_id = xref_user_group.group_id
					   WHERE xref_user_group.user_id     = :user_id
 ) AS file_access
FROM xref_folder_file
	INNER JOIN file ON xref_folder_file.file_id = file.file_id
WHERE xref_folder_file.deleted = 0
$folder_idfile
$search_file
ORDER BY xref_folder_file.name
SQL;


$file_stmt1 = \DB::dbh()->prepare( $file_query1 );
$file_stmt1 ->bindParam( ':user_id',$user_id,             \PDO::PARAM_INT );
if ( !empty( $params['search'] ) ) {
$file_stmt1->bindParam( ':search',$search_text,             \PDO::PARAM_STR );
}
if ( !empty( $params['folder_id'] ) ) {
	$file_stmt1->bindParam( ':folder_id',$folder_idtext,      \PDO::PARAM_INT );
}
$file_stmt1->execute();

// $data['files'] =  $file_stmt->fetch( \PDO::FETCH_ASSOC );
$i = 0;
while( $file_row = $file_stmt1->fetch( \PDO::FETCH_ASSOC )) {

$file_path = $config->get( 'paths\files' );


 if( file_exists( $file_path . '/' . substr( $file_row['hash'], 0, 2 ). '/' . substr( $file_row['hash'], 2, 2 ))) {
	$data['files'][ $i ]             = $file_row;
	  $data['files'][ $i ]['filesize'] = filesize( $file_path . '/' . substr( $file_row['hash'], 0, 2 ) . '/' . substr( $file_row['hash'], 2, 2 ). '/' . substr( $file_row['hash'], 4 ) . '-' . $file_row['file_id'] );

	if( substr( $data['files'][ $i ]['type'], 0, 5 ) == 'image' ) {
		$data['files'][ $i ]['image_details'] = getimagesize( $file_path . '/' . substr( $file_row['hash'], 0, 2 ) . '/' . substr( $file_row['hash'], 2, 2 ). '/' . substr( $file_row['hash'], 4 ) . '-' . $file_row['file_id'] );
	} 

	++$i;
}			
}

//END

	

 if ( !empty( $params['folder_id'] ) ) {


	$sub_folder_query = <<<SQL
    SELECT folder_id, name, parent_id 
      FROM ( SELECT * 
      	        FROM file_folder
           ORDER BY parent_id, folder_id) tmp,
           ( SELECT @pv := :parent_id ) initialisation
     WHERE FIND_IN_SET(parent_id, @pv) > 0
       AND @pv := CONCAT(@pv, ',', folder_id)
SQL;
			$sub_folder_stmt = \DB::dbh()->prepare( $sub_folder_query );
			$sub_folder_stmt->bindParam( ':parent_id', $params['folder_id'], \PDO::PARAM_INT );
			$sub_folder_stmt->execute();

	$subfolder_ids = [];

	if ( $sub_folder_stmt->rowCount() > 0 ) {
		while ( $sub_folder_row = $sub_folder_stmt->fetch( \PDO::FETCH_ASSOC ) ) {
			$subfolder_ids[] = $sub_folder_row['folder_id'];
		}
	}

	$search_file 	  = !empty( $params['search'] ) ? ' AND xref_folder_file.name LIKE :search' : '';
	$search_text      = !empty( $params['search'] ) ? "%{$params['search']}%" : '';
	$search_subfolder = !empty( $subfolder_ids ) ? ' OR xref_folder_file.folder_id IN ( ' . implode( ',', $subfolder_ids ) . ' )' : '';

		$file_query = <<<SQL
  SELECT xref_folder_file.*,
         file.hash,
         file.type, 
         (
         	SELECT GROUP_CONCAT(xref_file_group.access)
							    FROM xref_user_group
							           INNER JOIN xref_file_group 
							                   ON xref_file_group.group_id = xref_user_group.group_id
							   WHERE xref_user_group.user_id     = :user_id
         ) AS file_access
    FROM xref_folder_file
            INNER JOIN file ON xref_folder_file.file_id = file.file_id
   WHERE ( 
   	      xref_folder_file.folder_id = :folder_id
   	      $search_subfolder
   	     )
     AND xref_folder_file.deleted = 0
     $search_file
   ORDER BY xref_folder_file.name
SQL;



			$file_stmt= \DB::dbh()->prepare( $file_query );
			$file_stmt->bindParam( ':folder_id', $params['folder_id'], \PDO::PARAM_INT );
			$file_stmt->bindParam( ':user_id',   $user_id,      \PDO::PARAM_INT );
		
			if ( !empty( $params['search'] ) ) {
			$file_stmt->bindParam( ':search',   $search_text, \PDO::PARAM_STR );
			}
			$file_stmt->execute();

			$data['file_stmt'] = $file_stmt->fetchAll( \PDO::FETCH_ASSOC );

			$i = 0;
			while( $file_row = $file_stmt->fetch( \PDO::FETCH_ASSOC )) {
				$file_path = $config->get( 'paths\files' );

				if( file_exists( $file_path . '/' . substr( $file_row['hash'], 0, 2 ). '/' . substr( $file_row['hash'], 2, 2 ))) {
					$data['files'][ $i ]             = $file_row;
				    // $data['files'][ $i ]['filesize'] = filesize( $file_path . '/' . substr( $file_row['hash'], 0, 2 ) . '/' . substr( $file_row['hash'], 2, 2 ). '/' . substr( $file_row['hash'], 4 ) . '-' . $file_row['file_id'] );

					if( substr( $data['files'][ $i ]['type'], 0, 5 ) == 'image' ) {
						$data['files'][ $i ]['image_details'] = getimagesize( $file_path . '/' . substr( $file_row['hash'], 0, 2 ) . '/' . substr( $file_row['hash'], 2, 2 ). '/' . substr( $file_row['hash'], 4 ) . '-' . $file_row['file_id'] );
					} 

					++$i;
				}			
			}

			
		} elseif ( empty( $params['folder_id'] ) && !empty( $params['search'] ) ) {


			$search_file = !empty( $params['search'] ) ? ' AND xref_folder_file.name LIKE :search' : '';
			$search_text = !empty( $params['search'] ) ? "%{$params['search']}%" : '';

		$file_query = <<<SQL
SELECT xref_folder_file.*,
		file.hash,
		file.type, 
		(
	   SELECT GROUP_CONCAT(xref_file_group.access)
		 FROM xref_user_group
   INNER JOIN xref_file_group 
		ON xref_file_group.group_id = xref_user_group.group_id
		WHERE xref_user_group.user_id     = :user_id
		) AS file_access
	    FROM xref_folder_file
  INNER JOIN file ON xref_folder_file.file_id = file.file_id
       WHERE xref_folder_file.deleted = 0
		$search_file
	ORDER BY xref_folder_file.name
SQL;


$file_stmt = \DB::dbh()->prepare( $file_query );
$file_stmt ->bindParam( ':user_id',   $user_id,             \PDO::PARAM_INT );
if ( !empty( $params['search'] ) ) {
$file_stmt->bindParam( ':search',   $search_text,             \PDO::PARAM_STR );
}
$file_stmt->execute();

$i = 0;
	while( $file_row = $file_stmt->fetch( \PDO::FETCH_ASSOC )) {

		$file_path = $config->get( 'paths\files' );
	

		 if( file_exists( $file_path . '/' . substr( $file_row['hash'], 0, 2 ). '/' . substr( $file_row['hash'], 2, 2 ))) {
			$data['files'][ $i ]             = $file_row;
		    $data['files'][ $i ]['filesize'] = filesize( $file_path . '/' . substr( $file_row['hash'], 0, 2 ) . '/' . substr( $file_row['hash'], 2, 2 ). '/' . substr( $file_row['hash'], 4 ) . '-' . $file_row['file_id'] );

			if( substr( $data['files'][ $i ]['type'], 0, 5 ) == 'image' ) {
				$data['files'][ $i ]['image_details'] = getimagesize( $file_path . '/' . substr( $file_row['hash'], 0, 2 ) . '/' . substr( $file_row['hash'], 2, 2 ). '/' . substr( $file_row['hash'], 4 ) . '-' . $file_row['file_id'] );
			} 

			++$i;
		}			
	}

}


		// return \JSONRPC::build_result( TRUE, 'project',  array('folder_list' => $data, 'countresult' => $count ));
		 return \JSONRPC::build_result( TRUE, 'folder_list', $data );

		 
			
	}

	

?>
