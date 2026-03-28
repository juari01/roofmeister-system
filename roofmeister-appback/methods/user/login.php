<?php namespace user;

	function login( $params ) {
	/**
	 * Login
	 * Searches database for a username and password match.
	 * 
	 * @param string  api_token - The token used to authenticate the JSON-RPC client.
	 * @param string  username  - User username
	 * @param string  password  - User password
	 * @param string  hash      - The hash for the user accessing this method.
	 *
	 * @return array build_result()
	 */

	// Atlas class autoloader
		require( \env::$paths['methods'] . '/../autoloader_atlas.php' );

	// Load application configuration
		$config = new \Atlas\Config( file_get_contents( \env::$paths['methods']. '/../config.ini' ));

	// Application class autoloader
		require( $config->get( 'paths\autoloader' ));

	// Verify authorized API Token
		if ( empty( $params['api_token'] )) {

			return \JSONRPC::build_result( FALSE, 'api_token_missing' );
		}

		if ( !\JSONRPC::check_api_token( $params['api_token'] )) {

			return \JSONRPC::build_result( FALSE, "api_token_failure: {$params['api_token']}" );
		}

		\JSONRPC::audit_log( 0, __NAMESPACE__ . '\\' . __FUNCTION__, json_encode( $params ) );

	// Get user account from database by name and password
		$user_query = <<<SQL
  SELECT *
    FROM user
   WHERE username  = :username
     AND password != ''
     AND active    = '1'
SQL;
		$user_result = \DB::dbh()->prepare( $user_query );
		$user_result->bindParam( ':username', $params['username'], \PDO::PARAM_STR );
		$user_result->execute();

		$user_row = $user_result->fetch( \PDO::FETCH_ASSOC );

		if ( password_verify( $params['password'], $user_row['password'] )) {
		// User account found
			$hash = md5( microtime() . $params['password'] . $user_row['user_id'] );

			$user_row['hash'] = $hash;

			$login_query = <<<SQL
  UPDATE user
     SET last_login = :last_login,
         hash       = :hash
   WHERE user_id    = :user_id
SQL;
			$login_result = \DB::dbh()->prepare( $login_query );
			$login_result->bindValue( ':last_login', date_format( date_create(), 'Y-m-d H:i:s' ), \PDO::PARAM_STR );
			$login_result->bindParam( ':hash',       $hash,                                       \PDO::PARAM_STR );
			$login_result->bindParam( ':user_id',    $user_row['user_id'],                        \PDO::PARAM_STR );
			$login_result->execute();

			return \JSONRPC::build_result( TRUE, 'login_successul', [ 'user' => $user_row ]);
		} else {
		// User account not found or bad authentication
			return \JSONRPC::build_result( FALSE, 'Invalid username and/or password.' );
		}
	}

?>
