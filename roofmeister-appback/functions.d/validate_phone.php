<?php

	function validate_phone( $phone, $country = 'US' ) {
	/*
	 * Validate Phone
	 * This function validates a phone number.
	 * So far, it only validates numbers that are part of the NANP (numbers with
	 * the country code "1").
	 * Returns a result of TRUE or FALSE.
	 */

		if ( $country == 'US' ) {

		// Remove non-numbers
			$phone = preg_replace( '/\D/', '', $phone );

		// Remove leading "1" if it exists
			if ( substr( $phone, 0, 1 ) == '1') {
				$phone = substr( $phone, 1 );
			}

			if ( strlen( $phone ) != 10 ) {
			// The number is not 10 digits
				return FALSE;
			}

			if ( preg_match( '/^1/', $phone )) {
			// Area code cannot start with "1"
				return FALSE;
			}

			if ( preg_match( '/^.11/', $phone )) {
			// Area codes cannot be N11
				return FALSE;
			}

			if ( preg_match( '/^...1/', $phone )) {
			// Exchange cannot start with "1"
				return FALSE;
			}

			if ( preg_match( '/^....11/', $phone )) {
			// Exchange cannot be N11
				return FALSE;
			}

			if ( preg_match( '/^.9/', $phone )) {
			// Area code cannot be N9N
				return FALSE;
			}

			return TRUE;
		} else {
		// A country has been specified that we don't support
			return FALSE;
		}
	}

?>
