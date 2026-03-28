<?php

	function validate_date( $date, $format ) {
	/**
	 * Validates whether a string date format is valid.
	 *
	 * @param string date   - The date to validate.
	 * @param string format - The format of the provided date.
	 *
	 * @return bool - TRUE If the date is valid, FALSE if it's not.
	 */

		$date = DateTime::createFromFormat( $format, $date );

		$date_errors = DateTime::getLastErrors();

		if ( $date_errors['warning_count'] + $date_errors['error_count'] > 0 ) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

?>
