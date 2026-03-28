<?php

	function file_full_path ( $file_id ) {
	/**
	 * File full path
	 * Gets the full path of the given file id.
	 * 
	 * @param int file_id  
	 * 
	 * @return string
	 */

		require( \env::$paths['methods'] . '/../config.php' );

		$file_query = <<<SQL
  SELECT *
    FROM file 
   WHERE file_id = :file_id
SQL;
		$file_stmt = dbh()->prepare( $file_query );
		$file_stmt->bindValue( ':file_id', $file_id, \PDO::PARAM_STR );
		$file_stmt->execute();

		$file_row  = $file_stmt->fetch( \PDO::FETCH_ASSOC );

		$file_path = $config_server['paths']['files'];

		$file_path = $file_path . '/' . substr( $file_row['hash'], 0, 2 ) . '/' . substr( $file_row['hash'], 2, 2 ). '/' . substr( $file_row['hash'], 4 ) . '-' . $file_row['file_id'];

		return $file_path;
	}

?>
