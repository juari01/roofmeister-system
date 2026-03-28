<?php

	function verify_criteria( $criteria, &$params, $fix = FALSE ) {
	/**
	 * Verify Criteria
	 * Verifies the submitted criteria to make sure everything is
	 * submitted as required and allowed.
	 * 
	 * @param array criteria	- Array of required and allowed criterias | 
	 * 							  Array ( allowed_update, required_update, allowed_insert, required_insert )
	 * @param array params 		- Array of values | Array ( values => [ name => value ], where => [ name => value ] )
	 * 
	 * @return string|array
	 */

		if ( isset( $params['where'] ) && is_array( $params['where'] )) {
		// Check for values allowed on an update
			if ( isset( $params['values'] ) && is_array( $params['values'] )) {
			// Check that specified values are allowed
				foreach ( $params['values'] as $name => $value ) {
					if ( !in_array( $name, $criteria['allowed_update'] )) {
						if ( $fix ) {
							unset( $params['values'][ $name ] );
						} else {
							return [ 'Disallowed value specified - ' . $name ];
						}
					}
				}
			} else {
				return [ 'Missing values' ];
			}

			$errors = [];

		// Check for values required on an update
			if ( isset( $criteria['required_update'] ) && is_array( $criteria['required_update'] )) {
				foreach ( $criteria['required_update'] as $required => $error ) {
					if ( !array_key_exists( $required, $params['values'] ) || empty( $params['values'][ $required ] ) ) {
						$errors[] = $error;
					}
				}
			}

			if( !empty( $errors )) {
				return $errors;
			}

		} else {
		// Check for values allowed on an insert
			if ( isset( $params['values'] ) && is_array( $params['values'] )) {
			// Check that specified values are allowed
				foreach ( $params['values'] as $name => $value ) {
					if ( !in_array( $name, $criteria['allowed_insert'] )) {
						if ( $fix ) {
							unset( $params['values'][ $name ] );
						} else {
							return [ 'Disallowed value specified - ' . $name ];
						}
					}
				}
			} else {
				return [ 'Missing values' ];
			}

			$errors = [];

		// Check for values required on an insert
			if ( isset( $criteria['required_insert'] ) && is_array( $criteria['required_insert'] )) {
				foreach ( $criteria['required_insert'] as $required => $error ) {
					if ( !array_key_exists( $required, $params['values'] ) || empty( $params['values'][ $required ] ) ) {
						$errors[] = $error;
					}
				}
			}
			
			if( !empty( $errors )) {
				return $errors;
			}
		}

		return 'verified';
	}

?>
