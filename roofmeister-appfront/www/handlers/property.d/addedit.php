<?php

use Atlas\Framework\Form\Element;
use Atlas\Framework\Form;

	session_write_close();

		
	if ( isset( $_POST['addlinkcustomer'] ) && isset( $_POST['customer_id'] ) ) {

		$add_link_property = new jsonrpc\method( 'property.add_linkcustomer' );
		$add_link_property->param( 'api_token', 		  $jsonrpc_api_token );
		$add_link_property->param( 'hash',      		  $_SESSION['user']['hash'] );
		$add_link_property->param( 'addxrefcustomer_id',  $_POST['customer_id'] ); 
		$add_link_property->param( 'addxrefproperty_id',  $_POST['property_id'] );
		$add_link_property->id = $jsonrpc_client->generate_unique_id();

		$jsonrpc_client->method( $add_link_property );

	}


	if ( isset( $_POST['property_id'] ) ) {

		$get_property = new jsonrpc\method( 'property.get' );
		$get_property->param( 'api_token', 	  $jsonrpc_api_token );
		$get_property->param( 'hash',      	  $_SESSION['user']['hash'] );
		$get_property->param( 'property_id',  $_POST['property_id'] );
		$get_property->id = $jsonrpc_client->generate_unique_id();

		$jsonrpc_client->method( $get_property );


		$get_propcontact = new jsonrpc\method( 'property.get_propcontact' );
		$get_propcontact->param( 'api_token', 	$jsonrpc_api_token );
		$get_propcontact->param( 'hash',      	$_SESSION['user']['hash'] );
		$get_propcontact->param( 'property_id', $_POST['property_id'] );
		$get_propcontact->param( 'order',     	true);
		$get_propcontact->id = $jsonrpc_client->generate_unique_id();

		$jsonrpc_client->method( $get_propcontact );
	}

		$get_note = new jsonrpc\method( 'property.get_notes' );
		$get_note->param( 'api_token',  $jsonrpc_api_token );
		$get_note->param( 'hash',       $_SESSION['user']['hash'] );
		$get_note->param( 'property_id', isset( $_POST['property_id'] ) ? $_POST['property_id'] : NULL );
		$get_note->id = $jsonrpc_client->generate_unique_id();
		$jsonrpc_client->method( $get_note );

	if ( isset( $_POST['customer_id'] ) && isset( $_POST['delete'] ) ) {
	
		$delete_link_property = new jsonrpc\method( 'property.delete_linkcustomer' );
		$delete_link_property->param( 'api_token', 		$jsonrpc_api_token );
		$delete_link_property->param( 'hash',      		$_SESSION['user']['hash'] );
		$delete_link_property->param( 'xrefcustomer_id',$_POST['customer_id'] );
		$delete_link_property->param( 'xrefproperty_id',$_POST['property_id'] );
		$delete_link_property->id = $jsonrpc_client->generate_unique_id();
	
		$jsonrpc_client->method( $delete_link_property );
	}

	if ( isset( $_POST['createfolder'] ) && isset( $_POST['property_id'] ) ) {
		
		$property_id  = $_POST['property_id'];
		$propertyname = $_POST['propertyname'];
			
		$create_folder = new jsonrpc\method( 'property.createfolder' );
		$create_folder->param( 'api_token',   $jsonrpc_api_token );
		$create_folder->param( 'hash',        $_SESSION['user']['hash'] );
		$create_folder->param( 'property_id', $property_id ); 
		$create_folder->param( 'propertyname',$propertyname ); 
		$create_folder->id = $jsonrpc_client->generate_unique_id();
		$jsonrpc_client->method( $create_folder );
		}


	$get_state = new jsonrpc\method( 'property.get_state' );
	$get_state->param( 'api_token', $jsonrpc_api_token );
	$get_state->param( 'hash',      $_SESSION['user']['hash'] );
	$get_state->param( 'state_id',  isset( $_POST['state_id'] ) ? $_POST['state_id'] : NULL );
	$get_state->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $get_state );


	$get_property_type = new jsonrpc\method( 'admin.propertytypes.get' );
	$get_property_type->param( 'api_token', $jsonrpc_api_token );
	$get_property_type->param( 'hash',      $_SESSION['user']['hash'] );
	$get_property_type->param( 'active',     true );
	$get_property_type->param( 'propertytype_id',  isset( $_POST['propertytype_id'] ) ? $_POST['propertytype_id'] : NULL );
	$get_property_type->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $get_property_type );


	$get_customer = new jsonrpc\method( 'property.get_linkcustomer' );
	$get_customer->param( 'api_token', $jsonrpc_api_token );
	$get_customer->param( 'hash',      $_SESSION['user']['hash'] );
	$get_customer->param( 'linkcustomer_id', isset( $_POST['property_id'] ) ? $_POST['property_id'] : NULL );
	$get_customer->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $get_customer );

	if ( isset( $_POST['folder_id'] ) ) {
	$folder_id = $_POST['folder_id'];
	
	$folder_list = new jsonrpc\method( 'file.folder_list' );
	$folder_list->param( 'api_token', $jsonrpc_api_token );
	$folder_list->param( 'hash',      $_SESSION['user']['hash'] );
	$folder_list->param( 'folder_id', $folder_id ); 
	$folder_list->id = $jsonrpc_client->generate_unique_id();
	$jsonrpc_client->method( $folder_list );
	}


	$jsonrpc_client->send();


	try {
		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

		if ( isset( $_POST['property_id'] ) && $result[ $get_property->id ]['status'] ) {
			$property = $result[ $get_property->id ]['data'][0];
		}

		if ( $result[ $get_state->id ]['status'] ) {
			$state_rows = $result[ $get_state->id ]['data'];
		}

		if ( $result[ $get_property_type->id ]['status'] ) {
			$property_type_row = $result[ $get_property_type->id ]['data'];
		}


		require( $_SERVER['DOCUMENT_ROOT'] . '/../templates/form.php' );
		$property_form 	  = new Form( $form_templates['main_form'] );
		$propcontact_form = new Form( $form_templates['main_form'] );

		$property_form->add_element( new Element( 'linebreak', [] ) );

		$property_form->add_element( new Element( 'text', [
			'label' => 'Property Name',
			'name'  => 'name',
		    'value' => ( isset( $property['name'] ) ? $property['name'] : '' ),
			'class' => 'width-300px label-required'
		] ) );

		$property_form->add_element( new Element( 'text', [
			'label' => 'Address 1',
			'name'  => 'address1',
			'value' => ( isset( $property['address1'] ) ? $property['address1'] : '' ),
			'class' => 'width-300px label-required'
		] ) );

		$property_form->add_element( new Element( 'text', [
			'label' => 'Address 2',
			'name'  => 'address2',
			'value' => ( isset( $property['address2'] ) ? $property['address2'] : '' ),
			'class' => 'width-300px label-required'
		] ) );


		$property_form->add_element( new Element( 'text', [
			'label' => 'City',
			'name'  => 'city',
			'value' => ( isset( $property['city'] ) ? $property['city'] : '' ),
			'class' => 'width-300px label-required'
		] ) );

		$property_form->add_element( new Element( 'linebreak', [] ) );

		// Create the State Options form
		$state_options = array( array(
            'value'   => '0',
            'display' => '[Select]'
        ) );

		if ( !empty( $state_rows ) ) {
		foreach ( $state_rows as $state ) {
			$state_options[] = [
				'display' => $state['state'],
				'name'    => 'state',
				'value'   => $state['state_id']
			];
		}
		}
	
		$property_form->add_element( new Element( 'select', [
			'label'    => 'State',
			'name' 	   => 'state_id',
			'selected' => ( isset( $property['state_id'] ) ? $property['state_id']: '' ),
			'options'  =>$state_options,
			'class'    => 'width-300px label-required',
			'params'   => '{allow_single_deselect: true}'
		] ) );

		$property_form->add_element( new Element( 'text', [
			'label' => 'Zip',
			'name'  => 'zip',
			'value' => ( isset( $property['zip'] ) ? $property['zip'] : '' ),
			'class' => 'width-300px label-required'
		] ) );

		$proptype_options = array( array(
            'value'   => '0',
            'display' => '[Select]'
        ) );

		if ( !empty( $property_type_row ) ) {
		foreach ( $property_type_row as $propertytype ) {
			$proptype_options[] = [
				'display' => $propertytype['name'],
				'name'    => 'type_id',
				'value'   => $propertytype['type_id']
				];
			}
		}
		
		$property_form->add_element( new Element( 'select', [
			'label'    => 'Property Type',
			'name' 	   => 'type_id',
			'selected' => ( isset( $property['type_id'] ) ? $property['type_id']: '' ),
			'options'  => $proptype_options,
			'class'    => 'width-300px label-required',
			'params'   => '{allow_single_deselect: true}'
		] ));

		$property_form->add_element( new Element( 'linebreak', [] ) );

		$table_propcontact = [
			'header' => [
				[
					'value' => 'Active'
				],
				[
					'value' => 'Company'
				],
				[
					'value' => 'First name'
				],
				[
					'value' => 'Last name'
				],
				[
					'value' => 'Phone work'
				],
				[
					'value' => 'Phone mobile'
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


		if ( $result[ $get_customer->id ]['status'] ) {

			$table_customer = [
				'header' => [
					[
						'value' => 'Customer Name'
					],
					[
						'value' => 'Action'
					],
				]
			];
	
			foreach ( $result[ $get_customer->id ]['data'] as $customer ) {

				$customer_id   = $customer['customer_id'];
				$customer_name = $customer['name'];

				
				$link_customer = new Form( $form_templates['main_form'] );
				$link_customer->add_element( new Element( 'button', [
					'name'    => $customer_name,
					'value'   => 'Remove',
					'data_id' => $customer_id,
					'class'   => 'label-required remove-customer-by-id'
				] ) );
			
				$table_customer['body'][] = [
					'data'  => [
						[
							'attr'  => 'customer_id',
							'value' => $customer['customer_id']
						]
					],
					'cells' => [
		
						[
							'value' => $customer['name']
						],
			
						[
							'value' => $link_customer->render()
						],
					]
				];


			}


			include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/property/comaddeditlink.php" );
	
			$tabs = [
				'Link Customer' => [
					'data'    => [
						'customer' => 'customer',
					],
					'content' => [
						'customer' => App::table_display( $table_customer )
					]
				]
			];
	
			$content_index = str_replace(
				'%TABLE_CONTENT%',
				App::form_wrapper( 'property_tabs', $tabs, false, true ),
				$content_index
			);
			

		} 


		
		$propcontact_form = new Form( $form_templates['main_form'] );
		$propcontact_form->add_element( new Element( 'button', [
			'name'  => 'Add Contact',
			'value' => 'Add Contact',
			'class' => 'label-required add-propcontact'
		] ));

		$note_form = new Form( $form_templates['main_form'] );
		$note_form->add_element( new Element( 'button', [
			'name'  => 'Add Note',
			'value' => 'Add Note',
			'class' => 'label-required add-note-property'
		] ));





		if ( isset( $property['property_id'] ) ) {


			$property_form->add_element( new Element( 'hidden', [
				'name'  => 'property_id',
				'value' => $property['property_id']
			] ) );

			$property_form->add_element( new Element( 'hidden', [
				'name'  => 'folder_id',
				'value' => $property['folder_id']
			] ) );

			
		if ( $result[ $get_propcontact->id ]['status'] ) {

		foreach ( $result[ $get_propcontact->id ]['data'] as $propcontact ) { 

			$prop_contact_form = new Form( $form_templates['main_form'] );
			$prop_contact_form->add_element( new Element( 'button', [
					'value' => 'Edit',
					'data_id' => $propcontact['contact_id'],
					'class' => 'label-required editprop-contact-id'
				] ));

			$table_propcontact['body'][] = [
				'data'  => [
					[
						'attr'  => 'propcontact_id',
						'value' => $propcontact['contact_id']
						
					]
				],
				'cells' => [
	
					[
						'value' => ( $propcontact['active'] 
							? App::image_display( [ 'src' => '/images/active.png',   'alt' => 'Active'   ] ) 
							: App::image_display( [ 'src' => '/images/inactive.png', 'alt' => 'Inactive' ] )
						),
					],
					[
						'value' => $propcontact['company']
					],
					[
						'value' => $propcontact['first_name']
					],
					[
						'value' => $propcontact['last_name']
					],
					[
						'value' => $propcontact['phone_work']
					],
					[
						'value' => $propcontact['phone_mobile']
					],
					[
						'value' => $prop_contact_form->render()
					],
		
				]
			];


		}

	}

	if (  $result[ $get_note->id ]['status'] ) {

		foreach ( $result[ $get_note->id ]['data'] as $note ) {
			$note_id = $note['note_id'];

			$edit_note = new Form( $form_templates['main_form'] );
			$edit_note->add_element( new Element( 'button', [
				'name'  => $note_id,
				'value' => 'Edit',
				'data_id' => $note_id,
				'class' => 'label-required prop_edit-note-by-id'
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

	if ( isset( $_POST['folder_id'] ) ) {
		if ( $result[ $folder_list->id ]['status'] ) {

		$breadcrumbs = $result[ $folder_list->id ]['data']['breadcrumbs'];
		$folders     = $result[ $folder_list->id ]['data']['folders'];
		$files       = $result[ $folder_list->id ]['data']['files'];
		
		//FOLDERS
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

			if ( !empty ( $files ) ) {
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
		App::table_display( $table_file),
		$content_index
	);

	$content_index = str_replace( '%ADD_FILE%', 'Add File', $content_index );
	$content_index = str_replace( '%ADD_FOLDER%', 'Add Folder', $content_index );

		$tabscontact = App::table_display($table_propcontact);
		$tab_note 	 = App::table_display($table_notes);

		$content_addedit = str_replace( '%FORM_CONTENT%',
			App::form_wrapper( 'property_save', [
				'Property Form'	=> [
					'content'	=> [
						'Property Information' => $property_form->render(),
						'Contacts'			   => $propcontact_form->render() . $tabscontact,
						// 'Notes' => $note_form->render() . $tab_note
						'Notes' 			   => $note_form->render() . $tab_note,
					]
				],
			] ),
			$content_addedit
		);

	} else {

		$content_addedit = str_replace( '%FORM_CONTENT%',
			App::form_wrapper( 'property_save', [
				'Property Form'		   => [
				'content'			   => [
				'Property Information' => $property_form->render()
					]
				],
			] ),
			$content_addedit
		);

	}


		echo json_encode( [
			'status'  => 'success',
			'content' => $content_addedit . ( isset( $property['property_id'] ) ? $content_index : '')
		] );
	} catch ( Exception $e ) {
		error_log( 'DEBUG: ' . $jsonrpc_client->result_raw );

		echo json_encode( [
			'status'  => FALSE,
			'errors' => $jsonrpc_client->result_raw
		] );
	}

?>
