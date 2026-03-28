<?php


    // Common start
	include( "{$_SERVER['DOCUMENT_ROOT']}/../components/start.php" );


    // Don't allow calling this handler directly
        if ( $_SERVER['REQUEST_METHOD'] != 'GET' ) {
            die( $_messages['no_direct_calls'] );
        }
    
    // Setup the JSON-RPC client
        $jsonrpc_client = new jsonrpc\client();
        $jsonrpc_client->server( $config->get( 'jsonrpc\main\server' ));

    if ( isset( $_GET['task'] ) && $_GET['task'] == 'folder_permissions') {
    // Get group
        $get_group = new jsonrpc\method( 'file.get_folder_permissions' );
        $get_group->param( 'api_token', $jsonrpc_api_token );
        $get_group->param( 'hash',      $_SESSION['user']['hash'] ); 
        $get_group->param( 'folder_id', $_GET['folder_id'] ); 
        $get_group->id = $jsonrpc_client->generate_unique_id();
  
        $jsonrpc_client->method( $get_group );
        $jsonrpc_client->send();

        $result = jsonrpc\client::parse_result( $jsonrpc_client->result );

        if( $result[ $get_group->id ]['status'] ) {
            $group_rows  = $result[ $get_group->id ]['data']['permissions'];
            $read_rows   = $result[ $get_group->id ]['data']['read'];
            $upload_rows = $result[ $get_group->id ]['data']['upload'];
            $delete_rows = $result[ $get_group->id ]['data']['delete'];
        }

        $table_permissions = [
            'header' => [
                [
                    'value' => 'Groups'
                ],
                [
                    'value' => 'Read'
                ],
                [
                    'value' => 'Upload'
                ],
                [
                    'value' => 'Delete'
                ],
            ]
        ];
 
        $table_permissions['body'][] = [
            'data'  => [
                [
                    'attr'  => 'group_id',
                    'value' => 0
                ]
            ],
            'cells' => [
                [
                    'value' => 'Everyone'
                ],
                [
                    'value' => App::form_display( [
                        [
                            'type' => 'checkbox',
                            'options' => [
                                [
                                    'name'    => 'read_everyone',
                                    'value'   => '0',
                                    'checked' => !$read_rows ? TRUE : FALSE
                                ]
                            ]
                        ]
                    ], $form_templates['popup_form'] )
                ],
                [
                    'value' => App::form_display( [
                        [
                            'type' => 'checkbox',
                            'options' => [
                                [
                                    'name'    => 'upload_everyone',
                                    'value'   => '0',
                                    'checked' => !$upload_rows ? TRUE : FALSE
                                ]
                            ]
                        ]
                    ], $form_templates['popup_form'] )
                ],
                [
                    'value' => App::form_display( [
                        [
                            'type' => 'checkbox',
                            'options' => [
                                [
                                    'name'    => 'delete_everyone',
                                    'value'   => '0',
                                    'checked' => !$delete_rows ? TRUE : FALSE
                                ]
                            ]
                        ]
                    ], $form_templates['popup_form'] )
                ],
            ]
        ];

        foreach ( $group_rows as $group ) {

            $table_permissions['body'][] = [
                'data'  => [
                    [
                        'attr'  => 'group_id',
                        'value' => $group['group_id']
                    ]
                ],
                'cells' => [
                    [
                        'value' => $group['group']
                    ],
                    [
                        'value' => App::form_display( [
                            [
                                'type' => 'checkbox',
                                'options' => [
                                    [
                                        'name'    => 'read',
                                        'value'   => $group['group_id'],
                                        'checked' => $group['read'] == 1 ? TRUE : FALSE
                                    ]
                                ]
                            ]
                        ], $form_templates['popup_form'] )
                    ],
                    [
                        'value' => App::form_display( [
                            [
                                'type' => 'checkbox',
                                'options' => [
                                    [
                                        'name'    => 'upload',
                                        'value'   => $group['group_id'],
                                        'checked' => $group['upload'] == 1 ? TRUE : FALSE
                                    ]
                                ]
                            ]
                        ], $form_templates['popup_form'] )
                    ],
                    [
                        'value' => App::form_display( [
                            [
                                'type' => 'checkbox',
                                'options' => [
                                    [
                                        'name'    => 'delete',
                                        'value'   => $group['group_id'],
                                        'checked' => $group['delete'] == 1 ? TRUE : FALSE
                                    ]
                                ]
                            ]
                        ], $form_templates['popup_form'] )
                    ],
                ]
            ];
        }

        include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/files/files.php" );

        $permissions_popup_view = str_replace( 
                [
                    '%%PERMISSIONS%%'
                ],
                [
                    table_display( $table_permissions, 'table_permissions' )
                ],
                  
                $permissions_popup_view )
            . App::form_display( [
                [
                    'type'  => 'button',
                    'class' => 'save-permission',
                    'value' => 'Save',
                    'data'  => [
                        [
                            'name'  => 'function',
                            'value' => 'save-folder-permission'
                        ]
                    ]
                ]
            ],$form_templates['popup_form'] )
            . js_bind_element(
                'input[data-function=save-folder-permission',
                "files.save_folder_permission( '{$_GET['folder_id']}' )",
                'click'
            )
            . js_bind_element(
                'input[name=read_everyone]',
                "files.checkbox_action( 'read', true )",
                'click'
            )
            . js_bind_element(
                'input[name=read]',
                "files.checkbox_action( 'read' )",
                'click'
            )
            . js_bind_element(
                'input[name=upload_everyone]',
                "files.checkbox_action( 'upload', true )",
                'click'
            )
            . js_bind_element(
                'input[name=upload]',
                "files.checkbox_action( 'upload' )",
                'click'
            )
            . js_bind_element(
                'input[name=delete_everyone]',
                "files.checkbox_action( 'delete', true )",
                'click'
            )
            . js_bind_element(
                'input[name=delete]',
                "files.checkbox_action( 'delete' )",
                'click'
            );
            echo $permissions_popup_view;
    } else if ( isset( $_GET['task'] ) && $_GET['task'] == 'file_permissions') {
        // Get group
            $get_group = new jsonrpc\method( 'file.get_file_permissions' );
            $get_group->param( 'api_token', $config_client['jsonrpc']['api_token'] );
            $get_group->param( 'hash',      $_SESSION['user']['hash'] ); 
            $get_group->param( 'file_id',   $_GET['file_id'] ); 
            $get_group->id = $jsonrpc_client->generate_unique_id();
      
            $jsonrpc_client->method( $get_group );
            $jsonrpc_client->send();
    
            $result = jsonrpc\client::parse_result( $jsonrpc_client->result );
    
            if( $result[ $get_group->id ]['status'] ) {
                $group_rows  = $result[ $get_group->id ]['data']['permissions'];
                $read_rows   = $result[ $get_group->id ]['data']['read'];
                $delete_rows = $result[ $get_group->id ]['data']['delete'];
            }
    
            $table_permissions = [
                'header' => [
                    [
                        'value' => 'Groups'
                    ],
                    [
                        'value' => 'Read'
                    ],
                    [
                        'value' => 'Delete'
                    ],
                ]
            ];
     
            $table_permissions['body'][] = [
                'data'  => [
                    [
                        'attr'  => 'group_id',
                        'value' => 0
                    ]
                ],
                'cells' => [
                    [
                        'value' => 'Everyone'
                    ],
                    [
                        'value' => App::form_display( [
                            [
                                'type' => 'checkbox',
                                'options' => [
                                    [
                                        'name'    => 'read_everyone',
                                        'value'   => '0',
                                        'checked' => !$read_rows ? TRUE : FALSE
                                    ]
                                ]
                            ]
                        ], $form_templates['popup_form'] )
                    ],
                    [
                        'value' => App::form_display( [
                            [
                                'type' => 'checkbox',
                                'options' => [
                                    [
                                        'name'    => 'delete_everyone',
                                        'value'   => '0',
                                        'checked' => !$delete_rows ? TRUE : FALSE
                                    ]
                                ]
                            ]
                        ], $form_templates['popup_form'] )
                    ],
                ]
            ];
    
            foreach ( $group_rows as $group ) {
    
                $table_permissions['body'][] = [
                    'data'  => [
                        [
                            'attr'  => 'group_id',
                            'value' => $group['group_id']
                        ]
                    ],
                    'cells' => [
                        [
                            'value' => $group['group']
                        ],
                        [
                            'value' => App::form_display( [
                                [
                                    'type' => 'checkbox',
                                    'options' => [
                                        [
                                            'name'    => 'read',
                                            'value'   => $group['group_id'],
                                            'checked' => $group['read'] == 1 ? TRUE : FALSE
                                        ]
                                    ]
                                ]
                            ], $form_templates['popup_form'] )
                        ],
                        [
                            'value' => App::form_display( [
                                [
                                    'type' => 'checkbox',
                                    'options' => [
                                        [
                                            'name'    => 'delete',
                                            'value'   => $group['group_id'],
                                            'checked' => $group['delete'] == 1 ? TRUE : FALSE
                                        ]
                                    ]
                                ]
                            ], $form_templates['popup_form'] )
                        ],
                    ]
                ];
            }
    
            include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/files/files.php" );
    
            $permissions_popup_view = str_replace( 
                    [
                        '%%PERMISSIONS%%'
                    ],
                    [
                        table_display( $table_permissions, 'table_permissions' )
                    ],
                      
                    $permissions_popup_view )
                . App::form_display( [
                    [
                        'type'  => 'button',
                        'class' => 'save-permission',
                        'value' => 'Save',
                        'data'  => [
                            [
                                'name'  => 'function',
                                'value' => 'save-file-permission'
                            ]
                        ]
                    ]
                ],$form_templates['popup_form'] )
                . js_bind_element(
                    'input[data-function=save-file-permission]',
                    "files.save_file_permission( '{$_GET['file_id']}' )",
                    'click'
                )
                . js_bind_element(
                    'input[name=read_everyone]',
                    "files.checkbox_action( 'read', true )",
                    'click'
                )
                . js_bind_element(
                    'input[name=read]',
                    "files.checkbox_action( 'read' )",
                    'click'
                )
                . js_bind_element(
                    'input[name=delete_everyone]',
                    "files.checkbox_action( 'delete', true )",
                    'click'
                )
                . js_bind_element(
                    'input[name=delete]',
                    "files.checkbox_action( 'delete' )",
                    'click'
                );
                echo $permissions_popup_view;
        }
    if ( isset( $_GET['task'] ) && $_GET['task'] == 'folder_rename') {  
    // Get folder
        $get_group = new jsonrpc\method( 'file.get_folder' );
        $get_group->param( 'api_token', $jsonrpc_api_token );
        $get_group->param( 'hash',      $_SESSION['user']['hash'] ); 
        $get_group->param( 'folder_id', $_GET['folder_id'] ); 
        $get_group->id = $jsonrpc_client->generate_unique_id();
  
        $jsonrpc_client->method( $get_group );
        $jsonrpc_client->send();

        $result = jsonrpc\client::parse_result( $jsonrpc_client->result );

        $folder_info = [];

        if( $result[ $get_group->id ]['status'] ) {
            $folder_info  = $result[ $get_group->id ]['data'];
        }

        // Build the email form
            $folder_form = [
                [
                    'type'  => 'hidden',
                    'name'  => 'parent_id',
                    'value' => ( isset( $folder_info['parent_id'] ) ? $folder_info['parent_id'] : 0 )
                ],
                [
                    'type'  => 'hidden',
                    'name'  => 'folder_id',
                    'value' => ( isset( $_GET['folder_id'] ) ? $_GET['folder_id'] : 0 )
                ],
                [
                    'type'     => 'parent-container',
                    'class'    => 'full-box',
                    'children' => [
                        [
                            'type'  => 'text',
                            'label' => 'Folder Name',
                            'class' => 'width-200px',
                            'name'  => 'name',
                            'value' => ( isset( $folder_info['name'] ) ? $folder_info['name'] : '' )
                        ]                        
                    ]
                ],
                [
                    'type'     => 'parent-container',
                    'class'    => 'full-box',
                    'children' => [
                        [
                            'type'  => 'button',
                            'value' => 'Save',
                            'class' => 'two-button-middle',
                            'data'  => [
                                [
                                    'name'  => 'function',
                                    'value' => 'rename-folder-save'
                                ]
                            ]
                        ],
                        [
                            'type'  => 'button',
                            'value' => 'Cancel',
                            'data'  => [
                                [
                                    'name'  => 'function',
                                    'value' => 'rename-folder-cancel'
                                ]
                            ]
                        ]
                    ]
                ]
            ];
             $rename_folder = '<form>' . form_display( $folder_form, $form_templates['popup_form'] ) . '</form>';
            echo $rename_folder;
    }

?>
 