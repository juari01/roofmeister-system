<?php 
    $file_delete = new jsonrpc\method( 'file.file_delete' );
    $file_delete->param( 'api_token', $jsonrpc_api_token );
    $file_delete->param( 'hash',      $_SESSION['user']['hash'] );
    $file_delete->param( 'file_id',   $_POST['file_id'] );
    $file_delete->param( 'folder_id', $_POST['folder_id'] );
    $file_delete->param( 'file_name', $_POST['file_name'] );
     
    $file_delete->id = $jsonrpc_client->generate_unique_id();

    $jsonrpc_client->method( $file_delete );
    $jsonrpc_client->send();  
    
    $result = jsonrpc\client::parse_result( $jsonrpc_client->result );
    
        if ( $result[ $file_delete->id ]['status'] ) { 
            echo json_encode( [
                'status'  => 'success',
                'content' => ''
            ] );
        } else {
            echo json_encode( [
                'status' => 'error',
                'errors' => $result[ $file_delete->id ]['message']
            ] );
        }
?>