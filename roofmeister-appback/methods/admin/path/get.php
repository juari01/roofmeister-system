<?php
namespace admin\path;

function get($params)
{
	/**
	 * Get
	 * Retrieve a list of categories.
	 * 
	 * @param string  api_token    - The token used to authenticate the JSON-RPC client.
	 * @param int     path_id - Selected calendar ID
	 * @param int     active       - 1 - Active | 0 - Inactive
	 * @param string  order        - comma-separated list of columns with sorting order
	 * @param string  hash         - The hash for the user accessing this method.
	 *
	 * @return array build_result()
	 */

	// Atlas class autoloader
	require( \env::$paths['methods'] . '/../autoloader_atlas.php' );

	// Load application configuration
	$config = new \Atlas\Config( file_get_contents( \env::$paths['methods'] . '/../config.ini' ));

	// Application class autoloader
	require( $config->get( 'paths\autoloader' ));

	// Verify authorized API Token
	if ( empty($params['api_token'] )) {
		return \JSONRPC::build_result( FALSE, 'api_token_missing' );
	}

	if ( !\JSONRPC::check_api_token($params['api_token'] )) {
		return \JSONRPC::build_result( FALSE, "api_token_failure: {$params['api_token']}" );
	}

	// Verify hash
	$user_id = \User::verify_hash( $params['hash'] );
	if ( empty( $user_id )) {
		return \JSONRPC::build_result( FALSE, 'invalid_hash' );
	}

	\JSONRPC::audit_log( $user_id, __NAMESPACE__ . '\\' . __FUNCTION__, json_encode( $params ));

	// Verify admin access
	if ( !\User::security_check($user_id, 'admin' )) {
		return \JSONRPC::build_result( FALSE, 'not_authorized' );
	}

	$bind_params = [];

	$query_where = !empty( $wheres ) ? ' WHERE ' . implode( ' AND ', $wheres ) : '';
	$query_order = !empty( $params['order'] ) ? ' ORDER BY ' . $params['order'] : 'ORDER BY name ASC';

	$path_query = <<<SQL
   SELECT `folder_path`.path_id , `folder_path`.name, `file_folder`.folder_id, `file_folder`.name as foldername
     FROM `folder_path`
			LEFT JOIN `file_folder`
 				   ON `folder_path`.folder_id = `file_folder`.folder_id
	ORDER by `folder_path`.name
SQL;
	$path_stmt = \DB::dbh()->prepare( $path_query );

	\DB::bind_params( $path_stmt, $bind_params );

	$path_stmt->execute();

	$calendar_rows = $path_stmt->fetchAll( \PDO::FETCH_ASSOC );

	return \JSONRPC::build_result(TRUE, 'paths', $calendar_rows );

}

?>