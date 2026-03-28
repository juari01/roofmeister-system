<?php

	class DB {
	/**
	 * A class to hold static functions related to the database.
	 */

		public static function bind_params( &$statement, $params ) {
		/**
		 * Binds parameters to a prepared statement
		 *
		 * @param string statement - Prepared query statement
		 * @param array  params    - The parameters to be bind into the statement
		 *
		 * @return bool
		 */

			if ( is_array( $params ) && count( $params ) > 0 ) {

				foreach ( $params as $key => $param ) {

					$statement->bindParam( $key, $param['value'], $param['type'] );
				}
			}
		}

		public static function check_duplicate( $table, $params ) {
		/**
		 * Check duplicate data from table
		 * 
		 * @param string table  - Name of table
		 * @param array  params - Array of where values | Array ( id => [ name => value ], field => [ name => value ] )
		 *
		 * @return int 
		 */

			$bind_params = [];
			$wheres      = [];

			if ( isset( $params['id'] ) && is_array( $params['id'] )) {

				$key = key( $params['id'] );
				$id  = current( $params['id'] );

				$bind_params[ ":$key" ] = [
					'value' => $id,
					'type'  => PDO::PARAM_INT
				];

				$wheres[] = "`$key` != :$key ";
			}

			if ( isset( $params['field'] ) && is_array( $params['field'] )) {

				foreach ( $params['field'] as $key => $value ) {
					$bind_params[ ":$key" ] = [
						'value' => $value,
						'type'  => PDO::PARAM_STR
					];
	
					$wheres[] = "`$key` = :$key ";
				}
			}

			$query_where = !empty( $wheres ) ? ' WHERE ' . implode( ' AND ', $wheres )  : '';

			$dupe_query = <<<SQL
  SELECT *
    FROM `$table`
  $query_where
SQL;

			$dupe_stmt = \DB::dbh()->prepare( $dupe_query );
			\DB::bind_params( $dupe_stmt, $bind_params );

			$dupe_stmt->execute();

			return $dupe_stmt->fetch( \PDO::FETCH_ASSOC );
		}

		public static function db_connect() {
		/**
		 * Makes a PDO connection to the given database and returns the PDO object.
		 *
		 * @return PDO
		 */

		// Atlas class autoloader
			require( \env::$paths['methods'] . '/../autoloader_atlas.php' );

		// Load application configuration
			$config = new \Atlas\Config( file_get_contents( \env::$paths['methods']. '/../config.ini' ));

		// Application class autoloader
			require( $config->get( 'paths\autoloader' ));

		// Set default port if none specified
			if ( empty( $config->get( 'db\port' ))) {
			// No port is set in config_server, set a default value

				$port = 3306;
			} else {

				$port = $config->get( 'db\port' );
			}

			$pdo = new PDO(
				$config->get( 'db\type' ) . ':host=' . $config->get( 'db\host' ) . ';port=' . $port . ';dbname=' . $config->get( 'db\name' ) . ';charset=UTF8',
				$config->get( 'db\user' ),
				$config->get( 'db\pass' )
			);

			$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION ); 

			return $pdo;
		}


		public static function db_date( $date, $time = FALSE ) {
		/**
		 * DB Date
		 * Returns a date formatted appropriate for inserting into a SQL DB.
		 */

			if ( $date == '0000-00-00' || $date == '0000-00-00 00:00:00' || $date == '' || $date == '11/30/-0001' ) {

				if ( $time ) {

					return '0000-00-00 00:00:00';
				} else {

					return '0000-00-00';
				}
			}

			$date = new DateTime( $date );

			if ( $time === TRUE ) {

				return $date->format( 'Y-m-d H:i:s' );
			} elseif( $time == 'timeonly' ) {

				return $date->format( 'H:i:s' );
			} else {

				return $date->format( 'Y-m-d' );
			}
		}

		public static function dbh() {
		/**
		 * Establishes a connection to the database and returns the PDO object, or
		 * returns the already opened object if it exists, or returns FALSE if
		 ( unable to establish a connection.
		 *
		 * @return PDO - The open database connection.
		 */

		// Initialize $pdo to null the first time this function is being called
			static $pdo = null;
			static $err = 0;

			if ( $pdo === null ) {
			// This is the first call to this function, establish a new connection

			// Atlas class autoloader
				require( \env::$paths['methods'] . '/../autoloader_atlas.php' );

			// Load application configuration
				$config = new \Atlas\Config( file_get_contents( \env::$paths['methods']. '/../config.ini' ));

			// Application class autoloader
				require( $config->get( 'paths\autoloader' ));

				if ( empty( $config->get( 'db\port' ))) {
				// No port is set in config_server, set a default value

					$port = 3306;
				} else {

					$port = $config->get( 'db\port' );
				}

				try {
				// Establish a new connection

					$pdo = new PDO(
						$config->get( 'db\type' ) . ':host=' . $config->get( 'db\host' ) . ';port=' . $port . ';dbname=' . $config->get( 'db\name' ) . ';charset=UTF8',
						$config->get( 'db\user' ),
						$config->get( 'db\pass' )
					);
				} catch( PDOException $e ) {
				// Couldn't establish a connection

					error_log( "DBH: Can't establish connection: " . print_r( $e, TRUE ));

					return FALSE;
				}
			} elseif ( $pdo instanceof PDO ) {
			// Check if the connection is still valid

				try {
					@$pdo->query( 'SELECT 1' );
				} catch ( PDOException $e ) {
					if ( $e->errorInfo[2] == 'MySQL server has gone away' && $err < 2 ) {
					// Increment error counter to preventive infinite recursion
						++$err;

					// Sleep for half a second to see if the MySQL server comes back
						usleep( 500000 );

					// Re-establish the broken connection
						$pdo = \DB::dbh();
					} else {
					// Couldn't re-establish connection

						error_log( "DBH: Can't re-establish connection: " . print_r( $e, TRUE ));

						return FALSE;
					}
				}
			}

			return $pdo;
		}

		public static function delete_xref_data( $table, $params, $ignored_values = [] ) {
		/**
		 * Delete data from xref table
		 * 
		 * @param string table          - Name of xref_table
		 * @param array  params         - Array of where values | Array ( column_name => column_value )
		 * @param array  ignored_values - Array of where values that shoud be ignored in deleting | Array ( column_name, column_values ( array ) )
		 * 
		 * @return void
		 */
	
			$key = key( $params );
			$id  = current( $params );

			$bind_params[ ":$key" ] = [
				'value' => $id,
				'type'  => PDO::PARAM_INT
			];

			$ignored_query = '';

			if ( !empty( $ignored_values ) ) {
				$ignored_query  = ' AND ' . $ignored_values['column'] . ' NOT IN ( ' . implode( ',', $ignored_values['values'] ) . ' )';
			}

			$type_query = <<<SQL
  DELETE FROM `$table`
   WHERE $key = :$key
        $ignored_query
SQL;
			$type_result = \DB::dbh()->prepare( $type_query );
			\DB::bind_params( $type_result, $bind_params );

			$type_result->execute();
		}

		public static function save_to_db( $table, $params ) {
		/**
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
				$type_result = \DB::dbh()->prepare( $type_query );
				\DB::bind_params( $type_result, $bind_params );

				$type_result->execute();

				return $id;

			} else {
			// Adding a new type
				$type_query = <<<SQL
  INSERT INTO `$table`
     SET $sets
SQL;
				$type_result = \DB::dbh()->prepare( $type_query );
				\DB::bind_params( $type_result, $bind_params );

				$type_result->execute();

				$get_last_id_query = <<<SQL
  SELECT LAST_INSERT_ID() as last_id
SQL;
				$get_last_id_result = \DB::dbh()->prepare( $get_last_id_query );

				$get_last_id_result->execute();

				$get_last_id_row = $get_last_id_result->fetch( \PDO::FETCH_ASSOC );

				return $get_last_id_row['last_id'];
			}
		}
	}

?>
