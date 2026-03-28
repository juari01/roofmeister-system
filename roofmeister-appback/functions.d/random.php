<?php

	function random( $type, $length ) {
	/**
	 * Generates a random string of a particular type and length.
	 *
	 * @param type string   - One of the following values:
	 *        alpha         - Upper and lower case alpha.
	 *        alphal        - Lower case alpha.
	 *        alphau        - Upper case alpha.
	 *        alphanumeric  - Upper and lowe case alpha with numeric.
	 *        alphalnumeric - Lower case alpha with numeric.
	 *        alphaunumeric - Upper case alpha with numeric.
	 *        numeric       - Numeric.
	 *        ascii         - Printable ASCII characters
	 * @param int  length   - The length of the string to generate.
	 *
	 * @return string
	 */

		switch ( $type ) {
			case 'alpha'         : $chars  = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';                                         break;
			case 'alphal'        : $chars  = 'abcdefghijklmnopqrstuvwxyz';                                                                   break;
			case 'alphau'        : $chars  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';                                                                   break;
			case 'alphanumeric'  : $chars  = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';                               break;
			case 'alphalnumeric' : $chars  = 'abcdefghijklmnopqrstuvwxyz0123456789';                                                         break;
			case 'alphaunumeric' : $chars  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';                                                         break;
			case 'numeric'       : $chars  = '0123456789';                                                                                   break;
			case 'ascii'		 : $chars  = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789~`!@#$%^&*()_+-={}[]\/:;"<>,.?'; break;
			default              : return FALSE;
		}

		$value = '';

		for ( $i = 0; $i < $length; ++$i ) {
			$value .= substr( $chars, rand( 0, strlen( $chars ) - 1 ), 1 );
		}

		return $value;
	}

?>
