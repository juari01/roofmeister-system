<?php



	function bind_params( &$statement, $params ) {
		
		// Atlas class autoloader
		require( \env::$paths['methods'] . '/../autoloader_atlas.php' );

	// Load application configuration
		$config = new \Atlas\Config( file_get_contents( \env::$paths['methods']. '/../config.ini' ));


	// Application class autoloader
		require( $config->get( 'paths\autoloader' ));
	/**
	 * Binds parameters to a prepared statement
	 *
	 * @param string statement - Prepared query statement
	 * @param array  params    - The parameters to be bind into the statement
	 *
	 * @return bool
	 */

		if( is_array( $params ) && count( $params ) > 0 ) {
			foreach( $params as $key => $param ) {
				$statement->bindParam( $key, $param['value'], $param['type'] );
			}
		}
	}
	
?>