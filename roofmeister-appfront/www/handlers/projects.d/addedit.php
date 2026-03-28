<?php

use Atlas\Framework\Form\Element;
use Atlas\Framework\Form;

// Close session writing
	session_write_close();


	if ( isset( $_POST['project_idtr'] ) ) {

		$get_project = new jsonrpc\method( 'project.get' );
		$get_project->param( 'api_token', $jsonrpc_api_token );
		$get_project->param( 'hash',      $_SESSION['user']['hash'] );
		$get_project->param( 'project_id',$_POST['project_idtr'] );
		$get_project->id = $jsonrpc_client->generate_unique_id();

		$jsonrpc_client->method( $get_project );

	}

		$get_note = new jsonrpc\method( 'project.get_notes' );
		$get_note->param( 'api_token',  $jsonrpc_api_token );
		$get_note->param( 'hash',       $_SESSION['user']['hash'] );
		$get_note->param( 'project_id', isset( $_POST['project_idtr'] ) ? $_POST['project_idtr'] : NULL );
		$get_note->id = $jsonrpc_client->generate_unique_id();
		$jsonrpc_client->method( $get_note );


	if ( isset( $_POST['folder_id'] ) ) {
	
		$folder_id = $_POST['folder_id'];
		
		$folder_list = new jsonrpc\method( 'file.folder_list' );
		$folder_list->param( 'api_token', $jsonrpc_api_token );
		$folder_list->param( 'hash',      $_SESSION['user']['hash'] );
		$folder_list->param( 'folder_id', $folder_id ); 
		$folder_list->id = $jsonrpc_client->generate_unique_id();
		$jsonrpc_client->method( $folder_list );
	}

	if ( isset( $_POST['createfolder'] ) && isset( $_POST['project_idtr'] ) ) {
		
		$project_id = $_POST['project_idtr'];
		$projectname = $_POST['projectname'];
			
		$create_folder = new jsonrpc\method( 'project.createfolder' );
		$create_folder->param( 'api_token',  $jsonrpc_api_token );
		$create_folder->param( 'hash',       $_SESSION['user']['hash'] );
		$create_folder->param( 'project_id', $project_id ); 
		$create_folder->param( 'projectname',$projectname ); 
		$create_folder->id = $jsonrpc_client->generate_unique_id();
		$jsonrpc_client->method( $create_folder );

		}

		$jsonrpc_client->send();


	try {
		$project_folder_id = 0;

		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );
		
		require( $_SERVER['DOCUMENT_ROOT'] . '/../templates/form.php' );
		$project_form = new Form( $form_templates['main_form'] );
		

		if ( isset( $_POST['project_idtr'] ) && $result[ $get_project->id ]['status'] ) {
			
			$project_var 	 = $result[ $get_project->id ]['data'][0];
			$project_folder_id = $project_var['folder_id'];
				
			$project_form->add_element( new Element( 'hidden', [
				'name'  => 'folder_id',
					'value' =>  $project_folder_id,
			] ));
		}
		

		$project_form->add_element( new Element( 'hidden', [
			'label' => 'project_id',
			'name'  => 'project_id',
			'value' =>  ( isset( $project_var['project_id'] ) ? $project_var['project_id'] : '' ) .
						( isset( $_POST['JSproject_id'] ) 	  ? $_POST['JSproject_id'] : '' ) 
		] ));

	
		$project_form->add_element( new Element( 'hidden', [
			'label' => 'customer_id',
			'name'  => 'customer_id',
			'value' => ( isset( $_POST['customer_id'] ) 	  ? $_POST['customer_id'] : '' ) . 
					   ( isset( $_POST['projcustomer_id'] )   ? $_POST['projcustomer_id'] : '' ) . 
					   ( isset( $project_var['customer_id'] ) ? $project_var['customer_id'] : '' )	   
		] ));


		$project_form->add_element( new Element( 'hidden', [
			'label' => 'property_id',
			'name'  => 'property_id',
			'value' => ( isset( $_POST['property_id'] ) 	  ? $_POST['property_id'] : '' ) . 
					   ( isset( $_POST['projproperty_id'] )   ? $_POST['projproperty_id'] : '' ) . 
					   ( isset( $project_var['property_id'] ) ? $project_var['property_id'] : '' )
		] ) );


		$project_form->add_element( new Element( 'button', [
			'name'  => 'customer',
			'value' => 'Select customer',
			'class' => 'label-required select-projcustomer-by-id'
		] ) );


		$project_form->add_element( new Element( 'text', [
			'label' => '',
			'name'  => 'projcustomer_name',
			'disabled' => 'DISABLED',
			'value' => ( isset( $_POST['customer_name'] ) 	  	? $_POST['customer_name'] : '') . 
					   ( isset( $_POST['projcustomer_name'] ) 	? $_POST['projcustomer_name'] : '') . 
					   ( isset( $project_var['customer_name'] ) ? $project_var['customer_name'] : '') ,
			'class' => 'width-300px label-required'
		] ) );

		$note_form = new Form( $form_templates['main_form'] );
		$note_form->add_element( new Element( 'button', [
			'name'  => 'Add Note',
			'value' => 'Add Note',
			'class' => 'label-required add-note-project'
		] ));

		
		if ( isset( $_POST['customer_id'] ) || isset( $_POST['projcustomer_id'] ) || isset( $_POST['project_idtr'] ) ) { 
			
			$project_form->add_element( new Element( 'button', [
				'name'  => 'property',
				'value' => 'Select property',
				'class' => 'label-required select-projproperty-by-id'
			] ));
	
			$project_form->add_element( new Element( 'text', [
				'label' => '',
				'name'  => 'projproperty_name',
				'disabled' => 'DISABLED',
				'value' => ( isset( $_POST['property_name'] ) ? $_POST['property_name'] : '' ) .
						   ( isset( $project_var['property_name'] ) ? $project_var['property_name'] : ''),
				'class' => 'width-300px label-required'
			] ));

			$project_form->add_element( new Element( 'linebreak', [] ) );
			$project_form->add_element( new Element( 'linebreak', [] ) );

			$project_form->add_element( new Element( 'text', [
				'label' => 'Project Name',
				'name'  => 'name',
				'value' => ( isset( $_POST['projectname'] ) ? $_POST['projectname'] : $project_var['project_name'] ),
				'class' => 'width-300px label-required'
			] ));
	
			$project_form->add_element( new Element( 'linebreak', [] ) );
	
			$project_form->add_element( new Element( 'textarea', [
				'label' => 'Description',
				'name'  => 'description',
				'value' => ( isset( $_POST['projdescription'] ) ? $_POST['projdescription'] : $project_var['description'] ),
				'class' => 'width-300px label-required'
			] ));

			
			
			
		}
						

		include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/projects/comprojects.php" );

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

		$table_notes = [
			'header' => [
				[
					'value' => 'Created'
				],
				[
					'value' => 'User'
				],
				[
					'value' => 'Note'
				],
				[
					'value' => 'Action'
				],
			]
		];

		if (  isset($result[ $get_note->id ]['status']) ) {

			foreach ( $result[ $get_note->id ]['data'] as $note ) {
				$note_id = $note['note_id'];

				$edit_note = new Form( $form_templates['main_form'] );
				$edit_note->add_element( new Element( 'button', [
					'name'  => $note_id,
					'value' => 'Edit',
					'data_id' => $note_id,
					'class' => 'label-required proj_edit-note-by-id'
				] ));

				$table_notes['body'][] = [
					'data'  => [
						[
							'attr'  => 'note_id',
							'value' => $note['note_id']
						]
					],
					'cells' => [
		
						[
							'value' => $note['created']
						],
						[
							'value' => $note['first_name']
						],
						[
							'value' => $note['note']
						],
						[				
							'value' => $edit_note->render()
						],
					]
				];


			}
		}
		
		
		 if ( isset( $_POST['folder_id']) ) {
			if ( $result[ $folder_list->id ]['status'] ) {

			$breadcrumbs = $result[ $folder_list->id ]['data']['breadcrumbs'];
			$folders 	 = $result[ $folder_list->id ]['data']['folders'];
			$files 	     = $result[ $folder_list->id ]['data']['files'];

//FOLDERS
foreach ( $folders as $folder ) {

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
//FILES
				if ( !empty( $files ) ) {
				   foreach ( $files as $file ) {
		
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

		$projects_addedit = str_replace( '%BREADCRUMBS_LIST%', breadcrumb_list( $breadcrumbs ), $projects_addedit );
		}

		$projects_addedit = str_replace(
			'%FILES_LIST%',
			App::table_display( $table_file)
			.js_bind_variable( 'project.folder_id',    $project_folder_id ),
			$projects_addedit
		);
		
		if ( isset( $_POST['customer_id'] ) || isset( $_POST['projcustomer_id'] ) || isset( $_POST['project_idtr'] ) ) { 

			$tab_note = App::table_display($table_notes);

			$projects_addedit = str_replace( '%FORM_CONTENT%',
				App::form_wrapper( 'project_save', [
					'Project Form' => [
						'content' =>  [
							'Project Information' => $project_form->render(),
							'Notes' => $note_form->render() . $tab_note					
						]
					],

				] ),
				$projects_addedit
			);

		} else {

			$projects_addedit = str_replace( '%FORM_CONTENT%',
				App::form_wrapper( 'project_save', [
					'Project Form' => [
						'content' =>  [
							'Project Information' => $project_form->render()	
						]
					],

				] ),
				$projects_addedit
			);
		}

		$projects_addedit = str_replace( '%ADD_FILE%', 'Add File', $projects_addedit );
		$projects_addedit = str_replace( '%ADD_FOLDER%', 'Add Folder', $projects_addedit );
		$projects_addedit = str_replace( '%BACK_BUTTON_FUNCTION%', 'back-project', $projects_addedit );

		echo json_encode( [
			'status'  => 'success',
			'content' => $projects_addedit
		] );
		
	} catch ( Exception $e ) {
		error_log( 'DEBUG: ' . $jsonrpc_client->result_raw );

		echo json_encode( [
			'status'  => FALSE,
			'errors' => $jsonrpc_client->result_raw
		] );
	}

?>
