<?php 
    $folder_delete = new jsonrpc\method( 'file.folder_delete' );
    $folder_delete->param( 'api_token', $jsonrpc_api_token );
    $folder_delete->param( 'hash',      $_SESSION['user']['hash'] );
    $folder_delete->param( 'folder_id', $_POST['folder_id'] );
     
    $folder_delete->id = $jsonrpc_client->generate_unique_id();

    $jsonrpc_client->method( $folder_delete );
    $jsonrpc_client->send();  
    
    $result = jsonrpc\client::parse_result( $jsonrpc_client->result );
    
    if ( $result[ $folder_delete->id ]['status'] ) { 
        echo json_encode( [
            'status'  => 'success',
            'content' => '',
            'data'    => $result[ $folder_delete->id ]['data']
        ] );
    } else {
        echo json_encode( [
            'status' => 'error',
            'errors' => $result[ $folder_delete->id ]['message'],
            'data'   => $result[ $folder_delete->id ]['data']
        ] );
    }
?>