<?php


include( "{$_SERVER['DOCUMENT_ROOT']}/../components/start.php" );

$jsonrpc_client = new jsonrpc\client();
$jsonrpc_client->server( $config->get( 'jsonrpc\main\server' ));

$params = [
    'hash'      => $_SESSION['user']['hash'],
    'file_id'   => $_GET['file_id'],
    'file_path' => true,
    'no_data'   => true,
    'api_token' =>  $jsonrpc_api_token 
];

$get_file = new jsonrpc\method( 'file.get_file' );
$get_file->id( $jsonrpc_client->generate_unique_id() );
$get_file->param( $params );

$jsonrpc_client->method( $get_file );
$jsonrpc_client->send();

$result = jsonrpc\client::parse_result( $jsonrpc_client->result ); 
	
if( $result[ $get_file->id ]['status'] ) {

    $file  = $result[ $get_file->id ]['data'];

header( 'Content-Description: File Transfer' ); 
header( 'Content-type: application/octet-stream' );
header( 'Cache-Control: public' );
header( 'Pragma: cache' );
header( 'Expires: ' . gmdate( 'D, d M Y H:i:s \G\M\T', time() + 86400 * 365 ));
header('Content-Disposition: attachment; filename="' . $file['name'] . '"');
readfile( $file['file_path'] );
exit();


} else {
	echo json_encode( [
		'status' => 'error',
		'errors' => $result[ $get_file->id ]['message']
	] );
}


?>