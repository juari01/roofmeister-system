<?php 

use Atlas\Framework\Form;
use Atlas\Framework\Form\Element;

// Close session writing
	session_write_close();

	include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/files/files.php" );

	$folder_id = ( isset( $_POST['folder_id'] ) ? $_POST['folder_id'] : 0 );
    
	$folder_list = new jsonrpc\method( 'file.folder_list' );
    $folder_list->param( 'api_token', $jsonrpc_api_token );
    $folder_list->param( 'hash',      $_SESSION['user']['hash'] );
    $folder_list->param( 'folder_id', $folder_id ); 
    if ( isset( $_POST['search'] ) ) {
        $folder_list->param( 'search', $_POST['search'] ); 
    }
    $folder_list->id = $jsonrpc_client->generate_unique_id();

    $jsonrpc_client->method( $folder_list );
    $jsonrpc_client->send();   

    $result = jsonrpc\client::parse_result( $jsonrpc_client->result ); 
	

	if ( $result[ $folder_list->id ]['status'] ) {

		$breadcrumbs = $result[ $folder_list->id ]['data']['breadcrumbs'];
		$folders     = $result[ $folder_list->id ]['data']['folders'];
		$files       = $result[ $folder_list->id ]['data']['files'];

		$table_file = [
			'header' => [
				[
					'value' => '',
					'attr'  => [
						'style' => 'width: 36px;',
					]
				],
				[
					'value' => 'Name'
				],
				[
					'value' => 'Updated',
					'attr'  => [
						'style' => 'width: 200px;',
					]
				],
				[
					'value' => 'Size',
					'attr'  => [
						'style' => 'width: 100px;',
					]
				]
			]
		];

	

	if ( $folder_id != 0 ) {

		$parent_id = $breadcrumbs[0]['parent_id'];

		$table_file['body'][] = [
			'attr'  => [
				 'id'        => 'folder_id_' . $parent_id,
				'class'     => 'folder',
				'draggable' => 'false'
			],
			'data'  => [
				[
					'attr'  => 'folderid',
				    'value' =>'',
				    'value' => empty( $_POST['search'] ) ? $parent_id : $folder_id,
					'value' => ''
				]
			],
			'cells' => [
				[
					'value' =>  App::image_display( [ 'src' => 'images/icon_folder-small.png', 'alt' => 'Back' ] ),
					'attr'  => [
						'style' => 'text-align: center;',
					]
				],
				[
					'value' => '(back)'
				],
				[
					'value' => ''
				],
				[
					'value' => ''
				],
			]
		];
	}

	if ( empty( $_POST['search'] ) ) {
		
		foreach( $folders as $folder ) {

			$folder_access = explode( ',', $folder['folder_access'] );

			$table_file['body'][] = [
					'attr'  => [
						'id'        => "folder_id_{$folder['folder_id']}",
						'class'     => 'folder ' . ( in_array( 'R', $folder_access ) ? '' : 'grayed' ),
						'draggable' => 'true'
					],
					'data'  => [
						[
							'attr'  => 'folderid',
							'value' => $folder['folder_id']
						],
						[
							'attr'  => 'delete',
							'value' => ( in_array( 'D', $folder_access ) ? true : false )
						],
					],
					'cells' => [
						[
							'value' => App::image_display( [ 
								'src' => in_array( 'R', $folder_access ) ? '/images/icon_folder-small.png' : '/images/icon_folder-small-gray.png', 
								'alt' => 'Folder', 
								'attr' => [
									'id' => "folder_image_{$folder['folder_id']}"
								] ] ),
							'attr'  => [
								'style' => 'text-align: center;',
							]
						],
						[
							'value' => $folder['name']
						],
						[
							'value' => ( $folder['updated'] )
						],
						[
							'value' => ''
						],
					]
				];
		}
	}

	if ( $folder_id || !empty( $_POST['search'] ) || empty( $folder_id ) ) {
	

		if ( !empty( $files ) ) {
			foreach( $files as $file ) {

				$file_access = explode( ',', $file['file_access'] );

				$table_file['body'][] = [
					'attr'  => [
						'id'        => "file_id_{$file['file_id']}",
						'class'     => 'file ' . ( in_array( 'R', $file_access ) ? '' : 'grayed' ),
						'draggable' => 'true'
					],
					'data'  => [
									[
							'attr'  => 'filename',
							'value' => trim( $file['name'] )
						],
									[
							'attr'  => 'folderid',
							'value' => $file['folder_id']
						],
						[
							'attr'  => 'fileid',
							'value' => $file['file_id']
						],
						[
							'attr'  => 'delete',
							'value' => in_array( 'D', $file_access ) ? true : false
						],
					],
					'cells' => [
						[
							'value' => App::image_display( [ 
								'src' => in_array( 'R', $file_access ) ? '/images/icon_file-.png' : '/images/icon_file-gray.png', 
								'alt' => 'File', 
								'attr' => [ 
									'id' => "file_image_{$file['file_id']}" 
								] 
							] ),
							'attr'  => [
								'style' => 'text-align: center;',
							]
						],
						[
							'value' => $file['name']
						],
						[
							'value' => ( $file['updated'] )
						],
						[
							  'value' => filesize_suffix( $file['filesize'] )
						],
					]
				];
			}
		}

	}
	


	krsort( $breadcrumbs );
    

	$content_index = str_replace( '%BREADCRUMBS_LIST%', breadcrumb_list( $breadcrumbs ), $content_index );
	

	$content_index = str_replace( 
		'%FILES_LIST%', 
		App::table_display( $table_file )
		.js_bind_variable( 'files.folder_id',    $folder_id )
		.js_bind_variable( 'files.search',       !empty( $_POST['search'] ) ? $_POST['search'] : '' ), 
	$content_index );
	
	$content_index = str_replace( '%ADD_FILE%', 'Add File', $content_index );
	$content_index = str_replace( '%ADD_FOLDER%', 'Add Folder', $content_index );

	
		echo json_encode( [
			'status'  => 'success',
			'content' => $content_index
		] );


} else {
	echo json_encode( [
		'status' => 'error',
		'errors' => $result[ $folder_list->id ]['message']
	] );
}

?>
