<?php

	function save_to_db( $table, $params ) {
	/**
	 * Save to DB
	 * Given the table name a values from the JSON-RPC client, saves the
	 * entry to the database.
	 *
	 * @param string table  - Name of table
	 * @param array  params - Array of values | Array ( values => [ name => value ], where => [ name => value ] )
	 *
	 * @return int
	 */

	// Bind params based on submitted values
		if ( isset( $params['values'] ) && is_array( $params['values'] )) {
			$set_array = [];

			foreach ( $params['values'] as $name => $value ) {
				$set_array[] = "`$name` = :$name";

				if ( is_array( $value )) {
					$bind_params[ ":$name" ] = [
						'value' => $value['value'],
						'type'  => $value['type']
					];
				} else {
					$bind_params[ ":$name" ] = [
						'value' => $value,
						'type'  => PDO::PARAM_STR
					];
				}
			}

			$sets = implode( ",\n", $set_array );
		}

	// Apply values to database
		if ( isset( $params['where'] ) && is_array( $params['where'] )) {
		// Updating an existing entry
			$key = key( $params['where'] );
			$id  = current( $params['where'] );

			$bind_params[ ":$key" ] = [
				'value' => $id,
				'type'  => PDO::PARAM_INT
			];

			$type_query = <<<SQL
  UPDATE `$table`
     SET $sets
   WHERE $key = :$key
SQL;
			$type_result = dbh()->prepare( $type_query );
			bind_params( $type_result, $bind_params );

			$type_result->execute();

			return $id;

		} else {
		// Adding a new type
			$type_query = <<<SQL
  INSERT INTO `$table`
     SET $sets
SQL;
			$type_result = dbh()->prepare( $type_query );
			bind_params( $type_result, $bind_params );

			$type_result->execute();

			$get_last_id_query = <<<SQL
  SELECT LAST_INSERT_ID() as last_id
SQL;
			$get_last_id_result = dbh()->prepare( $get_last_id_query );

			$get_last_id_result->execute();

			$get_last_id_row = $get_last_id_result->fetch( \PDO::FETCH_ASSOC );

			return $get_last_id_row['last_id'];
		}
	}

?>
