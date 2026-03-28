<?php

use Atlas\Framework\Form\Element;
use Atlas\Framework\Form;

// Close session writing
	session_write_close();
	

	if ( isset( $_POST['addlinkproperty'] ) && isset( $_POST['property_id'] ) ) {

		$add_link_customer = new jsonrpc\method( 'customer.add_linkproperty' );
		$add_link_customer->param( 'api_token', $jsonrpc_api_token );
		$add_link_customer->param( 'hash',      $_SESSION['user']['hash'] );
		$add_link_customer->param( 'addxrefcustomer_id',  $_POST['customer_id'] ); 
		$add_link_customer->param( 'addxrefproperty_id',  $_POST['property_id'] );
		$add_link_customer->id = $jsonrpc_client->generate_unique_id();

		$jsonrpc_client->method( $add_link_customer );

	}

	if ( isset( $_POST['customer_id'] ) ) {

		$get_customer = new jsonrpc\method( 'customer.get' );
		$get_customer->param( 'api_token',  $jsonrpc_api_token );
		$get_customer->param( 'hash',       $_SESSION['user']['hash'] );
		$get_customer->param( 'customer_id',$_POST['customer_id'] );
		$get_customer->id = $jsonrpc_client->generate_unique_id();
		$jsonrpc_client->method( $get_customer );

	}

		$get_note = new jsonrpc\method( 'customer.get_notes' );
		$get_note->param( 'api_token',  $jsonrpc_api_token );
		$get_note->param( 'hash',       $_SESSION['user']['hash'] );
		$get_note->param( 'customer_id', isset( $_POST['customer_id'] ) ? $_POST['customer_id'] : NULL );
		$get_note->id = $jsonrpc_client->generate_unique_id();
		$jsonrpc_client->method( $get_note );

	if ( isset( $_POST['property_id'] ) && isset( $_POST['delete'] ) ) {
	
		$delete_link_property = new jsonrpc\method( 'customer.delete_linkproperty' );
		$delete_link_property->param( 'api_token', $jsonrpc_api_token );
		$delete_link_property->param( 'hash',      $_SESSION['user']['hash'] );
		$delete_link_property->param( 'xrefcustomer_id',  $_POST['customer_id'] );
		$delete_link_property->param( 'xrefproperty_id',  $_POST['property_id'] );
		$delete_link_property->id = $jsonrpc_client->generate_unique_id();
		$jsonrpc_client->method( $delete_link_property );
	}

		$get_property = new jsonrpc\method( 'customer.get_linkproperty' );
		$get_property->param( 'api_token', $jsonrpc_api_token );
		$get_property->param( 'hash',      $_SESSION['user']['hash'] );
		$get_property->param( 'linkcustomer_id', isset( $_POST['customer_id'] ) ? $_POST['customer_id'] : NULL );
		$get_property->id = $jsonrpc_client->generate_unique_id();
		$jsonrpc_client->method( $get_property );


		if (isset( $_POST['folder_id'] ) ) {
		$folder_id = $_POST['folder_id'];
		
		$folder_list = new jsonrpc\method( 'file.folder_list' );
		$folder_list->param( 'api_token', $jsonrpc_api_token );
		$folder_list->param( 'hash',      $_SESSION['user']['hash'] );
		$folder_list->param( 'folder_id', $folder_id );
		$folder_list->id = $jsonrpc_client->generate_unique_id();
		$jsonrpc_client->method( $folder_list );
		}

		if (isset( $_POST['createfolder'] ) && isset( $_POST['customer_id'] ) ) {
		
		$customer_id   = $_POST['customer_id'];
		$customer_name = $_POST['customername'];
			
		$create_folder = new jsonrpc\method( 'customer.createfolder' );
		$create_folder->param( 'api_token', $jsonrpc_api_token );
		$create_folder->param( 'hash',      $_SESSION['user']['hash'] );
		$create_folder->param( 'customer_id', $customer_id ); 
		$create_folder->param( 'customername', $customer_name ); 
		$create_folder->id = $jsonrpc_client->generate_unique_id();
		$jsonrpc_client->method( $create_folder );
		}

// Send request to JSON-RPC
	$jsonrpc_client->send();


	try {

		$customer_folder_id = 0;

		require( $_SERVER['DOCUMENT_ROOT'] . '/../templates/form.php' );
		$customer_form = new Form( $form_templates['main_form'] );
		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

	
		if ( isset( $_POST['customer_id'] ) && $result[ $get_customer->id ]['status'] ) {

			$customers = $result[ $get_customer->id ]['data'];

			foreach ( $customers as $customer ) { 

				$customer_name 	    = $customer['name'];
				$customerId   	    = $customer['customer_id'];
				$customer_folder_id = $customer['folder_id'];

			}	

			$customer_form->add_element( new Element( 'hidden', [
				'name'  => 'customer_id',
				'value' => $customerId
			] ));

			$customer_form->add_element( new Element( 'hidden', [
				'name'  => 'folder_id',
				'value' => $customer_folder_id
			] ));

		}

		$customer_form->add_element( new Element( 'text', [
			'label' => 'Name',
			'name'  => 'name',
			'value' => ( isset( $_POST['customer_id'] ) ? $customer_name : '' ),
			'class' => 'width-300px label-required'
		] ) );

		$customer_form->add_element( new Element( 'linebreak', [] ) );

		$note_form = new Form( $form_templates['main_form'] );
		$note_form->add_element( new Element( 'button', [
			'name'  => 'Add Note',
			'value' => 'Add Note',
			'class' => 'label-required add-note-customer'
		] ));


		$table_property = [
			'header' => [
				[
					'value' => 'Property Name'
				],
				[
					'value' => 'Address 1'
				],
				[
					'value' => 'Address 2'
				],
				[
					'value' => 'City'
				],
				[
					'value' => 'State'
				],
				[
					'value' => 'Zip'
				],
				[
					'value' => 'Action'
				],
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

		if (  $result[ $get_note->id ]['status'] ) {

			foreach ( $result[ $get_note->id ]['data'] as $note ) {
				$note_id = $note['note_id'];

				$edit_note = new Form( $form_templates['main_form'] );
				$edit_note->add_element( new Element( 'button', [
					'name'  => $note_id,
					'value' => 'Edit',
					'data_id' => $note_id,
					'class' => 'label-required edit-note-by-id'
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
	
		if ( $result[ $get_property->id ]['status'] ) {

	
			foreach ( $result[ $get_property->id ]['data'] as $property ) {
				$property_id = $property['property_id'];
				$property_name = $property['name'];


				$link_customer = new Form( $form_templates['main_form'] );
				$link_customer->add_element( new Element( 'button', [
					'name'  => $property_name,
					'value' => 'Remove',
					'data_id' => $property_id,
					'class' => 'label-required remove-customer-by-id'
				] ));
			
				$table_property['body'][] = [
					'data'  => [
						[
							'attr'  => 'property_id',
							'value' => $property['property_id']
						]
					],
					'cells' => [
		
						[
							'value' => $property['name']
						],
						[
							'value' => $property['address1']
						],
						[
							'value' => $property['address2']
						],
						[
							'value' => $property['city']
						],
						[
							'value' => $property['state']
						],
						[
							'value' => $property['zip']
						],
						[
							
							'value' => $link_customer->render()
						],
					]
				];


			}

			include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/customer/comaddeditlink.php" );
	
			$tabs = [
				'Link Property' => [
					'data'    => [
						'property' => 'property',
					],
					'content' => [
						'Property' => App::table_display( $table_property )
					]
				]
			];
	
			$content_index = str_replace(
				'%TABLE_CONTENT%',
				App::form_wrapper( 'property_tabs', $tabs, false, true ),
				$content_index
			);
			

		} 


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

		$content_index = str_replace( '%BREADCRUMBS_LIST%', breadcrumb_list( $breadcrumbs ), $content_index );
	}

		$content_index = str_replace(
			'%FILES_LIST%',
			App::table_display( $table_file)
			.js_bind_variable( 'customer.folder_id',    $customer_folder_id ),
			$content_index
		);

		

		if ( isset( $_POST['customer_id'] ) && $result[ $get_customer->id ]['status'] ) {
			
			$tab_note = App::table_display($table_notes);

			$content_addedit = str_replace( '%FORM_CONTENT%',
				App::form_wrapper( 'customer_save', [
					'Customer Form' => [
						'content' 	=> [
							'Customer Information' => $customer_form->render(),
							'Notes' => $note_form->render() . $tab_note, 
		
						]
					],

				] ),
				$content_addedit
			); 

		} else {

			$content_addedit = str_replace( '%FORM_CONTENT%',
				App::form_wrapper( 'customer_save', [
					'Customer Form' => [
						'content' =>  [
							'Customer Information' => $customer_form->render()
		
						]
					],

				] ),
				$content_addedit
			); 	
		}
		
		$content_index = str_replace( '%ADD_FILE%', 'Add File', $content_index );
		$content_index = str_replace( '%ADD_FOLDER%', 'Add Folder', $content_index );
		$content_addedit = str_replace( '%BACK_BUTTON_FUNCTION%', 'back-customer', $content_addedit );

		echo json_encode( [
			'status'  => 'success',
			'content' => $content_addedit . ( isset( $customer['customer_id'] ) ? $content_index : '')
		] );
		
	} catch ( Exception $e ) {
		error_log( 'DEBUG: ' . $jsonrpc_client->result_raw );

		echo json_encode( [
			'status'  => FALSE,
			'errors' => $jsonrpc_client->result_raw
		] );
	}

?>
