<?php

	class User {
	/**
	 * A class to hold static functions related to users.
	 */

		public static function security_check( $user_id, $security ) {
		/**
		 * Security Check
		 * Checks to see if the specified user has the specified security
		 * level on the specified account.
		 * 
		 * @param int    user_id 	- Current User ID
		 * @param string security	- Security Code 
		 * 
		 * @return bool
		 */

			$check = FALSE;

			if ( !empty( $security )) {
				$security_array = explode( ',', $security );

				foreach( $security_array as $security ) {
					$security_query = <<<SQL
  SELECT xref_user_group.created
    FROM xref_user_group
		   INNER JOIN xref_group_security 
				   ON xref_group_security.group_id = xref_user_group.group_id
           INNER JOIN security
                   ON xref_group_security.security_id = security.security_id
   WHERE xref_user_group.user_id = :user_id
     AND security.code           = :security
SQL;
					$security_result = DB::dbh()->prepare( $security_query );
					$security_result->bindParam( ':user_id',  $user_id,  \PDO::PARAM_INT );
					$security_result->bindParam( ':security', $security, \PDO::PARAM_STR );
					$security_result->execute();

					if ( $security_result->rowCount() ) {
						$check = TRUE;
						break;
					}
				}
			}

			return $check;
		}

		public static function verify_hash( $hash ) {
		/**
		 * Returned a user_id of user with specified login hash, or FALSE if not
		 * found.
		 *
		 * @param string hash - The hash that matches the logged-in user.
		 *
		 * @return int/bool
		 */

		// Atlas class autoloader
			require( \env::$paths['methods'] . '/../autoloader_atlas.php' );

		// Load application configuration
			$config = new \Atlas\Config( file_get_contents( \env::$paths['methods']. '/../config.ini' ));

		// Application class autoloader
			require( $config->get( 'paths\autoloader' ));

			$user_query = <<<SQL
  SELECT user_id
    FROM user
   WHERE hash  = :hash
     AND hash != ''
SQL;

			$user_result = DB::dbh()->prepare( $user_query );
			$user_result->bindParam( ':hash', $hash, PDO::PARAM_STR );
			$user_result->execute();

			if ( $user_result->rowCount() ) {

				$user_row = $user_result->fetch( PDO::FETCH_ASSOC );

				return $user_row['user_id'];
			} else {

				return FALSE;
			}
		}

	}

?>
