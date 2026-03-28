<?php namespace property;

	function createfolder( $params ) {
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

		$property_id  = isset( $params['property_id'] ) ? $params['property_id'] : 0;
		$propertyname = isset( $params['propertyname'] ) ? $params['propertyname'] : '';

		$get_path_folder_id = \folder::get_folder_id_by_path('Property');
		$pathfolderid     = $get_path_folder_id['folder_id'];
		$propertynameid   ="$propertyname ($property_id)";

//CHECK PROPERTY FOLDER_ID IF NOT EMPTY 
	$check_folder_id_query = <<<SQL
SELECT *  
  FROM `property`
 WHERE `property_id` = $property_id
SQL;
	
		$check_folder_id_stmt = \DB::dbh()->prepare( $check_folder_id_query );
		$check_folder_id_stmt->execute();
		$check_folder_id_rows = $check_folder_id_stmt->fetchAll( \PDO::FETCH_ASSOC );


	if (empty($check_folder_id_rows['folder_id']) && ($pathfolderid)) {
		
//IF FOLDER_ID IS EMPTY THEN INSERT	
	$pathfolderid_query = <<<SQL
INSERT INTO `file_folder`
   SET parent_id = :parent_id,
name  = :propertynameid
SQL;

		$pathfolderid_stmt = \DB::dbh()->prepare($pathfolderid_query);
		$pathfolderid_stmt->bindParam( ':propertynameid',$propertynameid,\PDO::PARAM_STR );
		$pathfolderid_stmt->bindParam( ':parent_id',$pathfolderid,\PDO::PARAM_INT );
		$pathfolderid_stmt->execute();
	
//GET LAST INSERTED ID
	$get_last_id_query = <<<SQL
SELECT LAST_INSERT_ID() as last_id
SQL;
		$get_last_id_stmt = \DB::dbh()->prepare($get_last_id_query);
		$get_last_id_stmt->execute();
		$get_last_id_row = $get_last_id_stmt->fetch(\PDO::FETCH_ASSOC);
		
		 $last_id_row = $get_last_id_row['last_id'];

//UPDATE PROPERTY SET `FOLDER_ID` TO LAST INSERT ID
	$property_query = <<<SQL
UPDATE `property`
	SET folder_id =  $last_id_row
	WHERE property_id  = $property_id
SQL;
	
		$property_stmt = \DB::dbh()->prepare($property_query);
		$property_stmt->execute();

	}

	  return \JSONRPC::build_result( TRUE, 'Create folder');
	}
?>
