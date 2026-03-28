<?php

use Atlas\Framework\Form\Element;
use Atlas\Framework\Form;
// Close session writing
	session_write_close();

	$get_folders = new jsonrpc\method( 'admin.path.getfolder' );
	$get_folders->param( 'api_token', $jsonrpc_api_token );
	$get_folders->param( 'hash',      $_SESSION['user']['hash'] );
	$get_folders->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $get_folders );
	$jsonrpc_client->send(); 
		
	$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

	 include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/paths/pathfolder.php" );

	if ( $result[ $get_folders->id ]['status'] ) {
		
		$hiddenpathID_form = new Form( $form_templates['main_form'] );
		$hiddenpathID_form->add_element( new Element( 'hidden', [
			'name'  => 'path_id',
			'value' => $_POST['path_id']
		] ));



		$table_paths = [
			'header' => [
				[
					'value' => 'Folder Name'
				],
				[
					'value' => 'Action'
				]
			]
		];

		foreach ( $result[ $get_folders->id ]['data'] as $folders ) {
		$folder_selectform = new Form( $form_templates['main_form'] );

		$folder_selectform->add_element( new Element( 'button' , [
			'name'  => $folders['name'],
			'value' => 'Select',
			'data_id' => $folders['folder_id'],
			'class' => 'label-required select-folder-by-id'
		] ));
	
	
		
		$table_paths['body'][] = [
				'data'  => [
					[
						'attr'  => 'folder_id',
						'value' => $folders['folder_id']
					]
				],
				'cells' => [
					[
						'value' => $folders['name'] 
					],
					[
						'value' => $folder_selectform->render()
					]
				]
			];
		}

		$content_index = str_replace( '%TABLE_CONTENT%', table_display( $table_paths ), $content_index );

		$content_index = str_replace( '%ADD_BUTTON_VALUE%',    'Add Calendar', $content_index );
		$content_index = str_replace( '%ADD_BUTTON_FUNCTION%', 'add-paths', $content_index );

		echo json_encode( [
			'status'  => 'success',
			'content' => $content_index . $hiddenpathID_form->render()
		] );
	} else {
		echo json_encode( [
			'status' => 'error',
			'errors' => $result[ $get_folders->id ]['message']
		] );
	}

?>
