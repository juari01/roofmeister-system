<?php

	function validate_password( $password, $repassword = false) {
	/**
	 * Validate the password, repassword and format.
	 *
	 * @param string password   - The password of the user.
	 * @param string repassword - The repassword of the user.
	 *
	 * @return int/bool
	 */

	// Validate password
		$errors = [];

		if ( strlen( $password ) < 8 ) {
		// Check for minimum password length

			$errors[] = 'Password must be at least 8 characters.';
		}

		if ( $repassword != false ) {
			if ( $password != $repassword ) {
			// Check that both passwords match

				$errors[] = "Passwords don't match.";
			}
		}

		if ( preg_replace( '/[^0-9]/', '', $password ) == '' ) {
		// Check that number exists in password

			$errors[] = 'Password must contain at least one number.';
		}

		if ( preg_replace( '/[^A-Za-z]/', '', $password ) == '' ) {
		// Check that letter exists in password

			$errors[] = 'Password must contain at least one letter.';
		}

		return $errors;
	}

?>
