<?php

	class JSONRPC {
	/**
	 * A class to hold static functions related to JSONRPC.
	 */

		public static function audit_log( $user_id, $methods, $params ) {
		/**
		 * Creates a log entry to log RPC method access.
		 *
		 * @param int    user_id - The ID of the user making the request.
		 * @param string methods - The method name being requested.
		 * @param string params  - A JSON-encoded string of the submitted parameters.
		 *
		 * @return void
		 */

		// Atlas class autoloader
			require( \env::$paths['methods'] . '/../autoloader_atlas.php' );

		// Load application configuration
			$config = new \Atlas\Config( file_get_contents( \env::$paths['methods']. '/../config.ini' ));

		// Application class autoloader
			require( $config->get( 'paths\autoloader' ));

			$file_path = $config->get( 'paths\audit_log' );

			$file_name = date('Y-m-d').'.log';

			$file_text = date( 'Y-m-d H:i:s' ) . ' - ' . $user_id . ' ' . implode( '\\', array_unique( explode( '\\', $methods )))  . ' ' . $params . "\n";

			$path = $file_path . '/' . $file_name;

			$content = file_put_contents( $path, $file_text . PHP_EOL, FILE_APPEND );
		}

		public static function build_result( $status, $message, $data = [] ) {
		/**
		 * Builds a result array with status, message, and data for the front-end to
		 * interpret.
		 *
		 * @param bool   status  - Either TRUE (indicating success) or FALSE (indicating failure).  
		 *        This refers to an application-level success or failure.
		 * @param string message - A short message to return to the front-end.
		 * @param array  data    - An option array of data to return to the front-end.
		 *
		 * @return bool
		 */

			return [
				'status'  => $status,
				'message' => $message,
				'data'    => $data
			];
		}

		public static function check_api_token( $api_token ) {
		/**
		 * Validate the API token.
		 *
		 * @param string api_token - The token used to authenticate the JSON-RPC client.
		 * @param string role      - An option role limiter.
		 *
		 * @return bool
		 */

		// Atlas class autoloader
			require( \env::$paths['methods'] . '/../autoloader_atlas.php' );

		// Load application configuration
			$config = new \Atlas\Config( file_get_contents( \env::$paths['methods']. '/../config.ini' ));

		// Application class autoloader
			require( $config->get( 'paths\autoloader' ));

		// Compare the values provided
			if ( preg_match( '/^([a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})(\$[156][a-z]{0,1}\$[a-zA-Z0-9.\/]+\$[a-zA-Z0-9.\/]+)$/', $api_token, $matches )) {

				$api_key = $matches[1];
				$hash    = $matches[2];

				if ( $api_key == $config->get( 'jsonrpc\key' ) && crypt( $config->get( 'jsonrpc\pass' ), $hash ) == $hash ) {

					return TRUE;
				}
			}

			return FALSE;
		}
		
	

	}

?>
