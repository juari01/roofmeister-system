<?php

	function security_check( $user_id, $security ) {
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
				$security_result = dbh()->prepare( $security_query );
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

?>
