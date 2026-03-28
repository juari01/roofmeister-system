<?php
	class folder {
	public static function  folder_breadcrumbs( $folder_id ) {
		
		// Atlas class autoloader
			require( \env::$paths['methods'] . '/../autoloader_atlas.php' );

		// Load application configuration
			$config = new \Atlas\Config( file_get_contents( \env::$paths['methods']. '/../config.ini' ));

		// Application class autoloader
			require( $config->get( 'paths\autoloader' ));
	/**
	 * Recursive function to return an array of parents up to the root.
	 *
	 * @param int folder_id - The ID of the deepest folder in the chain to return.
	 *
	 * @return array - The array of folders.
 	*/

		 $breadcrumbs = [];

		

		if ( $folder_id != 0 ) {
$parent_query = <<<SQL
  SELECT *
  FROM file_folder
  WHERE folder_id = :folder_id
SQL;
		$parent_result = \DB::dbh()->prepare( $parent_query );
		

		$bind_params[':folder_id'] = [
			'value' => $folder_id,
			'type'  => \PDO::PARAM_INT
		];

		\DB::bind_params( $parent_result, $bind_params );

		$parent_result->execute();

		$parent_row    = $parent_result->fetchAll( \PDO::FETCH_ASSOC );
		
		foreach( $parent_row as $parent_row1 ) {
		 $breadcrumbs[] = $parent_row1;
		if ( $parent_row1['parent_id'] > 0 ) {
			 $breadcrumbs   = array_merge( $breadcrumbs, \folder::folder_breadcrumbs( $parent_row1['parent_id'] ));
		}
		}

	}
		

		return $breadcrumbs;



	}
	
	
	public static function save_file ( $params ) {
	

		// Atlas class autoloader
		require( \env::$paths['methods'] . '/../autoloader_atlas.php' );

		// Load application configuration
		$config = new \Atlas\Config( file_get_contents( \env::$paths['methods']. '/../config.ini' ));

		// Application class autoloader
		require( $config->get( 'paths\autoloader' ));

		$folder_id    = isset( $params['values']['folder_id'] ) ? $params['values']['folder_id'] : 0;
		$customer_id  = isset( $params['customer_id'] ) ? $params['customer_id'] : 0;
		$customername  = isset( $params['customername'] ) ? $params['customername'] : '';


		$file_data = base64_decode( $params['values']['file_data'] );
		$file_size = (int) ( strlen( rtrim( $params['values']['file_data'], '=' ) ) * 3 / 4 );
		$filename  = $params['values']['name'];
		$hash      = sha1( $file_data );
		
		
		$file_query = <<<SQL
	SELECT file.*
	FROM file
	WHERE hash = :hash
SQL;

		
		$file_result = \DB::dbh()->prepare( $file_query );
		$file_result->bindParam( ':hash',   $hash,  \PDO::PARAM_STR );
		$file_result->execute( );
		
				$file_id = 0;
		
				$file_result->rowCount();
				//EXISTING FILE
				if ( $file_result->rowCount() > 0 && $file_data != '' ) {

				$params['values']['folder_id'] = $params['values']['folder_id'] == 0 ? null : $params['values']['folder_id'];
		
					$file_row = $file_result->fetch( \PDO::FETCH_ASSOC );
					 $file_path = \folder::file_full_path( $file_row['file_id'] );
		
					if ( file_exists( $file_path ) && filesize( $file_path ) == $file_size ) {
						$file_id = $file_row['file_id'];
					} else {
						$params['values']['folder_id'] = $params['values']['folder_id'] == 0 ? null : $params['values']['folder_id'];
						unset( $params['values']['file_data'] );
						unset( $params['values']['name'] );
						unset( $params['values']['folder_id'] );
						$params['values']['created'] = date( 'Y-m-d H:i:s' );
		
						$file_id = \DB::save_to_db( 'file', $params );
					}
				//NEW FILE
				} else {
					$params['values']['folder_id'] = $params['values']['folder_id'] == 0 ? null : $params['values']['folder_id'];
					unset( $params['values']['file_data'] );
					unset( $params['values']['name'] );
					unset( $params['values']['folder_id'] );
					$params['values']['created'] = date( 'Y-m-d H:i:s' );
		
					$file_id = \DB::save_to_db( 'file', $params );
				}

				
// START
		$file_path = $config->get( 'paths\files' );


		$file_name = $file_path . "/" . substr( $hash, 0, 2 ) . "/" . substr( $hash, 2, 2 ). "/" . substr( $hash, 4 ) . '-' . $file_id;

		if ( !file_exists( $file_name ) ) {
			
			if( !file_exists( $file_path . "/" . substr( $hash, 0, 2 ))) {
				mkdir( $file_path . "/" . substr( $hash, 0, 2 ));
				chmod( $file_path . "/" . substr( $hash, 0, 2 ), 0777 );
			}

			if( !file_exists( $file_path . "/" . substr( $hash, 0, 2 ). "/" . substr( $hash, 2, 2 ) )) {
				mkdir( $file_path . "/" . substr( $hash, 0, 2 ). "/" . substr( $hash, 2, 2 ));
				chmod( $file_path . "/" . substr( $hash, 0, 2 ). "/" . substr( $hash, 2, 2 ), 0777 );
			}

			file_put_contents( $file_name , $file_data );

			chmod( $file_name, 0777 );

			$type =  \folder::mime_type( $file_name, $file_data );

			$file_query = <<<SQL
  UPDATE file
     SET hash = :hash,
         type = :type
   WHERE file_id = :file_id
SQL;

			$file_result = \DB::dbh()->prepare( $file_query );
			$file_result->bindParam( ':hash',    $hash,    \PDO::PARAM_STR );
			$file_result->bindParam( ':type',    $type,    \PDO::PARAM_STR );
			$file_result->bindParam( ':file_id', $file_id, \PDO::PARAM_INT );

			$file_result->execute();
		}

		$folder_id = $folder_id > 0 ? $folder_id : NULL;

		$xref_file_query = <<<SQL
  SELECT *
    FROM xref_folder_file
	WHERE folder_id = :folder_id
	  AND file_id   = :file_id
	  AND name      = :name
SQL;

		$xref_file_result = \DB::dbh()->prepare( $xref_file_query );
		$xref_file_result->bindParam( ':folder_id', $folder_id, \PDO::PARAM_INT );
		$xref_file_result->bindParam( ':file_id',   $file_id,   \PDO::PARAM_INT );
		$xref_file_result->bindParam( ':name',      $filename,  \PDO::PARAM_STR );
		$xref_file_result->execute();

		if ( $xref_file_result->rowCount() == 0 ) {
			$insert_file_query = <<<SQL
  INSERT INTO xref_folder_file
	SET folder_id = :folder_id,
			file_id   = :file_id,
			name      = :name
SQL;

			$insert_file_result = \DB::dbh()->prepare( $insert_file_query );
			$insert_file_result->bindParam( ':folder_id', $folder_id, \PDO::PARAM_INT );
			$insert_file_result->bindParam( ':file_id',   $file_id,   \PDO::PARAM_INT );
			$insert_file_result->bindParam( ':name',      $filename,  \PDO::PARAM_STR );
			$insert_file_result->execute();
		} else {
			$update_file_query = <<<SQL
  UPDATE xref_folder_file
     SET deleted = 0
   	 WHERE folder_id = :folder_id
	 AND file_id   = :file_id
	 AND name      = :name
SQL;

			$update_file_result = \DB::dbh()->prepare( $update_file_query );
			$update_file_result->bindParam( ':folder_id', $folder_id, \PDO::PARAM_INT );
			$update_file_result->bindParam( ':file_id',   $file_id,   \PDO::PARAM_INT );
			$update_file_result->bindParam( ':name',      $filename,  \PDO::PARAM_STR );
			$update_file_result->execute();
		}

		return $file_id;
	}

	
	public static function file_full_path ( $file_id ) {
	/**
	 * File full path
	 * Gets the full path of the given file id.
	 * 
	 * @param int file_id  
	 * 
	 * @return string
	 */

		// Atlas class autoloader
		require( \env::$paths['methods'] . '/../autoloader_atlas.php' );

		// Load application configuration
		$config = new \Atlas\Config( file_get_contents( \env::$paths['methods']. '/../config.ini' ));

		// Application class autoloader
		require( $config->get( 'paths\autoloader' ));


		$file_query = <<<SQL
  SELECT *
    FROM file 
   WHERE file_id = :file_id
SQL;
		$file_stmt = \DB::dbh()->prepare( $file_query );
		$file_stmt->bindValue( ':file_id', $file_id, \PDO::PARAM_STR );
		$file_stmt->execute();

		$file_row  = $file_stmt->fetch( \PDO::FETCH_ASSOC );

		$file_path = $config->get( 'paths\files' );

		$file_path = $file_path . '/' . substr( $file_row['hash'], 0, 2 ) . '/' . substr( $file_row['hash'], 2, 2 ). '/' . substr( $file_row['hash'], 4 ) . '-' . $file_row['file_id'];

		return $file_path;
	}

	public static function mime_type( $file ) {
		/**
		 * Mime Type
		 * Uses PHP's FileInfo Functions to determine the mime type of a file.
		 */
	
		// See if filename was provided
			if( !trim( $file )) {
				return FALSE;
			}
	
			$file_info = new finfo( FILEINFO_MIME );
	
			return $file_info->file( $file );
		}

		public static function get_folder_id_by_path( $path ) {
		/**
     * Given the name of the path, returns the folder ID.
     *
     * @param string path - The name of the path to return.
     *
     * @return int - The folder ID of the path.
     */

	 $wheres[] = 'name = :path_name';


	 $query_where = ( !empty( $wheres ) ? ' WHERE ' . implode( ' AND ', $wheres ) : '' );

	 $path_query = <<<SQL
SELECT folder_path.*
 FROM folder_path
	  $query_where
SQL;


	 $path_result = \DB::dbh()->prepare( $path_query );
	 $path_result->bindParam( ':path_name',   $path,  \PDO::PARAM_STR );
	 $path_result->execute( );

	 $path_row = $path_result->fetch( \PDO::FETCH_ASSOC );


	 return $path_row;

	}




	}

?>
