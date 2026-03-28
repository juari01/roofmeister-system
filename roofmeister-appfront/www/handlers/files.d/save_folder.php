<?php 

    // Close session writing
    session_write_close();

    $folder_save = new jsonrpc\method( 'file.save_folder' );
    $folder_save->param( 'api_token', $jsonrpc_api_token );
	$folder_save->param( 'hash',      $_SESSION['user']['hash'] );
     
    $folder_save->param( [
            'values' => $_POST
        ] );

        if ( isset( $_POST['folder_id'] )) {
            $folder_save->param( [
                'where' => [
                    'folder_id' => $_POST['folder_id']
                ]
            ] );
        }
 
    $folder_save->id = $jsonrpc_client->generate_unique_id();

    $jsonrpc_client->method( $folder_save );
    $jsonrpc_client->send();  
    
    $result = jsonrpc\client::parse_result( $jsonrpc_client->result );
    
        if( $result[ $folder_save->id ]['status'] ) { 
            echo json_encode( [
                'status'  => 'success',
                'content' => '',
                'data'    => $result[ $folder_save->id ]['data']
            ] );
        } else {
            echo json_encode( [
                'status' => 'error',
                'errors' => $result[ $folder_save->id ]['message'],
                'data'   => $result[ $folder_save->id ]['data']
            ] );
        }
 

?>