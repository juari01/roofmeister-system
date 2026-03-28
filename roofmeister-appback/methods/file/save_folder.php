<?php namespace file;

	function save_folder ( $params ) {
	/**
	 * Insert new file.
	 *
	 * @param string  api_token - The token used to authenticate the JSON-RPC client.
	 * @param array   values    - Array of field values
	 * @param array   where     - Array of where values
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

	// Verify hash
		$user_id = \User::verify_hash( $params['hash'] );
		if ( empty( $user_id )) {
			return \JSONRPC::build_result( FALSE, 'invalid_hash' );
		}

		\JSONRPC::audit_log( $user_id, __NAMESPACE__ . '\\' . __FUNCTION__, json_encode( $params ) );

	// Specify the criteria
		$criteria = [
			'allowed_insert'  => [],
			'allowed_update'  => [
				'parent_id',
				'name'
			],
			'required_insert' => [],
			'required_update' => [
				'name'	=> 'Missing Folder Name'
			]
		];

	// Validate submitted data
		$check_criteria =  \Validate::verify_criteria( $criteria, $params, TRUE );

		if( $check_criteria == 'verified' ) {

			if ( isset( $params['values']['parent_id'] ) && $params['values']['parent_id'] == 0 ) {
				$params['values']['parent_id'] = NULL;
			}

			$check_parent = $params['values']['parent_id'] > 0 ? ' parent_id = ' . $params['values']['parent_id'] : ' ( parent_id = 0 OR parent_id IS NULL )';

			$folder_query = <<<SQL
  SELECT *
    FROM file_folder
   WHERE name = :name
     AND $check_parent
SQL;
			$folder_stmt = \DB::dbh()->prepare( $folder_query );
			$folder_stmt->bindValue( ':name', $params['values']['name'], \PDO::PARAM_STR );
			$folder_stmt->execute();

			if( $folder_stmt->rowCount() > 0 ) {
				return \JSONRPC::build_result( FALSE, 'Folder already exists!', $params['values']['name'] );
			}

			// Begin transaction and commit only if all queries succeed
			try {

				\DB::dbh()->beginTransaction();

				$folder_id = \DB::save_to_db( 'file_folder', $params );

				\DB::dbh()->commit();

				return \JSONRPC::build_result( TRUE, 'save_success', [ 'folder_id' => $folder_id ] );


			} catch ( Exception $e ) {
			// Something went wrong while saving device, rollback and return

				\DB::dbh()->rollback();

				return \JSONRPC:: build_result( FALSE, 'save_failed' );
			}		
		}

	return \JSONRPC::build_result( FALSE, $check_criteria );


}
?>