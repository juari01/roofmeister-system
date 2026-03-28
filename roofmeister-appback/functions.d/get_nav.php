<?php

	function get_nav( $user_id, $parent_id = 0 ) {
	/**
	 * Get Nav
	 * A recurisve function to return the navigation allowable to the
	 * given user.
	 * 
	 * @param int user_id    - Current User ID
	 * @param int parent_id  - Nav Parent ID
	 * 
	 * @return array 
	 */

	// Initialize array
		$nav = [];

	// TO DO: User ID of user to get accessible menus

	// Select navigation for this user
		$nav_query = <<<SQL
  ( SELECT nav.nav_id, nav.name, nav.icon, nav.function, nav.disp_order, nav.parent_id
      FROM nav
             INNER JOIN xref_nav_security
                     ON nav.nav_id = xref_nav_security.nav_id
             INNER JOIN xref_group_security 
                     ON xref_nav_security.security_id = xref_group_security.security_id
             INNER JOIN xref_user_group  
                     ON xref_group_security.group_id = xref_user_group.group_id
     WHERE xref_user_group.user_id = :user_id
       AND (
                 nav.parent_id           = :parent_id
              OR (
                        nav.parent_id IS NULL
                    AND :parent_id = 0
                 )
           )
     GROUP BY nav.nav_id
  )
  UNION
  (
    SELECT nav.nav_id, nav.name, nav.icon, nav.function, nav.disp_order, nav.parent_id
      FROM nav
     WHERE (
                 nav.parent_id = :parent_id
              OR (
                        nav.parent_id IS NULL
                    AND :parent_id = 0
                 )
           )
       AND nav.nav_id NOT IN ( SELECT nav_id FROM xref_nav_security )
     GROUP BY nav.nav_id
   )
   ORDER BY disp_order
SQL;
		$nav_result = dbh()->prepare( $nav_query );
		$nav_result->bindParam( ':user_id',   $user_id,   PDO::PARAM_INT );
		$nav_result->bindParam( ':parent_id', $parent_id, PDO::PARAM_INT );
		$nav_result->execute();

		if ( $nav_result->rowCount() ) {
			while ( $nav_row = $nav_result->fetch( PDO::FETCH_ASSOC )) {
				$nav[ $nav_row['disp_order'] ] = [
					'name'      => $nav_row['name'],
					'icon'      => $nav_row['icon'],
					'function'  => $nav_row['function'],
					'parent_id' => $nav_row['parent_id']
				];

				$nav[ $nav_row['disp_order'] ]['subs'] = get_nav( $user_id, $nav_row['nav_id'] );
			}
		}

		return $nav;
	}

?>
