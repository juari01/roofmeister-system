<?php

	class form_validation {
	/**
	 * Form Validation
	 */

		var $input = [];

		function __construct() {

		}

		function add_input( $input ) {
			if ( !is_array( $input )) {
				throw new Exception( 'Not an array' );
			}

			if ( isset( $input[0] ) && is_array( $input[0] )) {
				$inputs    = $input;
			} else {
				$inputs[0] = $input;
			}

			foreach ( $inputs as $i => $input ) {
				if ( !isset( $input['name'] )) {
					throw new Exception( "Missing name in index $i" );
				}

				if ( !isset( $input['type'] )) {
					throw new Exception( "Missing type in index $i" );
				}

				$types = [ 'binary', 'currency', 'float', 'integer', 'text' ];

				if ( !in_array( $input['type'], $types )) {
					throw new Exception( "Invalid type specified in index $i" );
				}

				$this->input[] = $input;
			}

			return TRUE;
		}

		function validate() {
			$validate = [];

			foreach ( $this->input as $input ) {
				$validate[ $input['name'] ] = [
					'validates' => TRUE
				];

				if ( $input['type'] == 'binary' ) {

				} elseif ( $input['type'] == 'currency' ) {
					if ( !preg_match( '/^((?:\d{1,3}[,\.]?)+\d*)$/', $input['value'] )) {
						$validate[ $input['name'] ] = [
							'validates' => FALSE
						];
					}
				} elseif ( $input['type'] == 'float' ) {
					if ( !is_numeric( $input['value'] )) {
						$validate[ $input['name'] ] = [
							'validates' => FALSE
						];
					}
				} elseif ( $input['type'] == 'integer' ) {
					if ( (int)$input['value'] != $input['value'] ) {
						$validate[ $input['name'] ] = [
							'validates' => FALSE
						];
					}
				} elseif ( $input['type'] == 'text' ) {
					if ( preg_match( '/[^\x00-\x7f]/', $input['value'] )) {
						$validate[ $input['name'] ] = [
							'validates' => FALSE
						];
					}
				}

				if ( isset( $input['callback']['function'] )) {
					if ( isset( $input['callback']['params'] ) && is_array( $input['callback']['params'] )) {
						if ( !call_user_func_array( $input['callback']['function'], $input['callback']['params'] )) {
							$validate[ $input['name'] ] = [
								'validates' => FALSE
							];
						}
					} else {
						if ( !call_user_func( $input['callback']['function'] )) {
							$validate[ $input['name'] ] = [
								'validates' => FALSE
							];
						}
					}
				}
			}

			$validates = TRUE;

			foreach ( $validate as $valid ) {
				if ( $valid['validates'] === FALSE ) {
					$validates = FALSE;
				}
			}

			$invalid = 'Invalid fields: ';

			if ( $validates ) {
				$invalid .= '(none)';
			} else {
				foreach ( $validate as $field => $value ) {
					if ( !$value['validates'] ) {
						$invalid .= $field . ' ';
					}
				}

				$invalid = trim( $invalid );
			}

			return [
				'validates' => $validates,
				'invalid'   => $invalid,
				'fields'    => $validate
			];
		}
	}

?>
