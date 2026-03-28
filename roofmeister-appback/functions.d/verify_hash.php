<?php

	function verify_hash( $hash ) {
	/**
	 * Returned a user_id of user with specified login hash, or FALSE if not
	 * found.
	 *
	 * @param string hash - The hash that matches the logged-in user.
	 *
	 * @return int/bool
	 */

		$user_query = <<<SQL
  SELECT user_id
    FROM user
   WHERE hash  = :hash
     AND hash != ''
SQL;

		$user_result = dbh()->prepare( $user_query );
		$user_result->bindParam( ':hash', $hash, PDO::PARAM_STR );
		$user_result->execute();

		if ( $user_result->rowCount() ) {
			$user_row = $user_result->fetch( PDO::FETCH_ASSOC );

			return $user_row['user_id'];
		} else {
			return FALSE;
		}
	}

?>