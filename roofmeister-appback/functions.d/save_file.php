<?php

	function save_file ( $params ) {
	/**
	 * Given the params of the file, returns the file ID after saving it.
	 *
	 * @param array params - File params
	 *
	 * @return int - The file ID of the file.
	 */
		// Atlas class autoloader
		require( \env::$paths['methods'] . '/../autoloader_atlas.php' );

		// Load application configuration
		$config = new \Atlas\Config( file_get_contents( \env::$paths['methods']. '/../config.ini' ));

		// Application class autoloader
		require( $config->get( 'paths\autoloader' ));

		$folder_id = isset( $params['values']['folder_id'] ) ? $params['values']['folder_id'] : 0;
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
		
				if ( $file_result->rowCount() > 0 && $file_data != '' ) {
		
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
		
				} else {
					$params['values']['folder_id'] = $params['values']['folder_id'] == 0 ? null : $params['values']['folder_id'];
					unset( $params['values']['file_data'] );
					unset( $params['values']['name'] );
					unset( $params['values']['folder_id'] );
					$params['values']['created'] = date( 'Y-m-d H:i:s' );
		
					$file_id = \DB::save_to_db( 'file', $params );
				}

		return $file_id;
	}

?>
