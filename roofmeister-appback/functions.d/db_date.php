<?php

	function _db_date( $date, $time = FALSE ) {
	/**
	 * DB Date
	 * Returns a date formatted appropriate for inserting into a SQL DB.
	 */
		if( $date == "0000-00-00" || $date == "0000-00-00 00:00:00" || $date == "" || $date == "11/30/-0001" ) {
			if( $time ) {
				return "0000-00-00 00:00:00";
			} else {
				return "0000-00-00";
			}
		}

		$date = new DateTime( $date );

		if( $time === TRUE ) {
			return $date->format( "Y-m-d H:i:s" );
		} elseif( $time == "timeonly" ) {
			return $date->format( "H:i:s" );
		} else {
			return $date->format( "Y-m-d" );
		}
	}

?>
