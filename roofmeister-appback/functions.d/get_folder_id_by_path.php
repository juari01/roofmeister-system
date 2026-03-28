<?php

	function get_folder_id_by_path( $path ) {
	/**
	 * Given the name of the path, returns the folder ID.
	 *
	 * @param string path - The name of the path to return.
	 *
	 * @return int - The folder ID of the path.
	 */

		$wheres[]                  = 'name = :path_name';
		$bind_params[':path_name'] = [
			'value' => $path,
			'type'  => \PDO::PARAM_STR
		];;

		$query_where = ( !empty( $wheres ) ? ' WHERE ' . implode( ' AND ', $wheres ) : '' );

		$path_query = <<<SQL
  SELECT file_path.*
    FROM file_path
         $query_where
SQL;

		$path_result = dbh()->prepare( $path_query );

		\bind_params( $path_result, $bind_params );

		$path_result->execute( );

		$path_row = $path_result->fetch( \PDO::FETCH_ASSOC );

		return $path_row;
	}

?>
