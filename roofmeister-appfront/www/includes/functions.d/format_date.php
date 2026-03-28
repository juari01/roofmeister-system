<?php

	function format_date( $date, $format = NULL, $tz_offset = '' ) {
	/**
	 * Formats the specified date according to the configured format with either the specified timezone, or the timezone
	 * stored in the SESSION applied. If neither are specified, the returned date will have the server's timezone
	 * applied.
	 *
	 * @param string date      - The date to be formatted. Must be either "YYYY-MM-DD HH:ii:ss" or "YYYY-MM-DD". The
	 *        passed format will tell the function if it's just a date, or a date with a time.
	 * @param string format    - An optional format to override that set in the conifg. Note that this can either be a
	 *        date or datetime format. It's up to the caller of the function to know which is correct in context.
	 * @param string tz_offset - An optional timezone offset from GMT in hours.
	 *
	 * @return string - The formatted time.
	 */

	// Read in configuration
		// global $config;

	// Check to see if this is a date, datetime, or neither
		if ( $date == date( 'Y-m-d', strtotime( $date ))) {
			$type = 'date';
		} elseif ( $date = date( 'Y-m-d H:i:s', strtotime( $date ))) {
			$type = 'datetime';
		} else {
			throw new Exception( "Invalid date $date specified, must be 'YYYY-MM-DD HH:ii:ss' or 'YYYY-MM-DD'." );
		}

	// Calculate the server's offset
		$server_offset = date( 'P' );

		$sign = substr( $server_offset, 0, 1 );
		$hour = substr( $server_offset, 1, 2 );
		$min  = substr( $server_offset, 4, 2 );

		$server_offset = $sign . intval( $hour ) . '.' . round( $min / 60, 2 );

	// Determine if we're using a specified value, a session value, or the server's value
		if ( $tz_offset == 'LOCAL' ) {
		// Use the specified timezone value

			$offset = 0;
		} elseif ( $tz_offset != '' ) {
		// Explicitly use the server's local timezone

			$offset = $tz_offset - $server_offset;
		} elseif ( isset( $_SESSION['tz_offset'] )) {
		// Use the timezone stored in the session

			$offset = $_SESSION['tz_offset'] - $server_offset;
		} else {
		// Use the server's local timezone

			$offset = 0;
		}

	// Format the date and return
		$date_o = new DateTime( $date );

		$date_offset = strpos( $offset, '.' ) !== FALSE ? 'PT' . ( $offset * 60 ) . 'M' : 'PT' . abs( $offset ) . 'H';

		$interval = new DateInterval( $date_offset );
		if ( ( strpos( $offset, '.' ) === FALSE ) && $offset < 0 ) {
			$interval->invert = 1;
		}

		$date_o->add( $interval );

		if ( is_null( $format )) {
			if ( !isset( $config['formats'][ $type ] )) {
				throw new Exception( "Unable to format date, be sure to set $config->get['formats'][ $type ] in config.php." );
			} else {
				$this_format = $config['formats'][ $type ];
			}
		} else {
			$this_format = $format;
		}

		return $date_o->format( $this_format );
	}

?>
