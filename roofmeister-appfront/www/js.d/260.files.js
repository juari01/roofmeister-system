files = {};
 
files.folder_id    = 0; 
files.context_menu = false;
files.search       = '';

window.upload_index  = 0;
window.dropped_files = {};
 
files.init_actions = function () {

	$( 'input[name=search_text]' ).val( files.search );

// preventing page from redirecting
	$( 'html' ).on( 'dragover' , function( e ) {
		e.preventDefault();
		e.stopPropagation();
		$( 'h1' ).text( 'Drag here' );
	} );

	$( 'html' ).on( 'dragleave' , function( e ) {
		e.preventDefault();
		e.stopPropagation();
		$( 'h1' ).html( '<h1>Drag and Drop file here<br/>Or<br/>Click to select file</h1>' );
	} );

	$( 'html' ).on( 'drop', function( e ) {
		e.preventDefault();
		e.stopPropagation();
	} );

// Drag enter
	$( '.file-upload-area' ).on( 'dragenter', function ( e ) {
		e.stopPropagation();
		e.preventDefault();
		$( this ).find( 'h1' ).text( 'Drop' );
	} );

	$( '.file-upload-area' ).on( 'dragleave', function ( e ) {
		e.stopPropagation();
		e.preventDefault();
		$( this ).find( 'h1' ).html( '<h1>Drag and Drop file here<br/>Or<br/>Click to select file</h1>' );
	} );

// Drag over
	$( '.file-upload-area' ).on('dragover', function ( e ) {
		e.stopPropagation();
		e.preventDefault();
		$( this ).find( 'h1' ).text( 'Drop' );
	} );

// Drop
	$( '.file-upload-area' ).on( 'drop', function ( e ) {
		e.stopPropagation();
		e.preventDefault();

		files.list_file( e.originalEvent.dataTransfer.files );

		$( this ).find( 'h1' ).html( '<h1>Drag and Drop file here<br/>Or<br/>Click to select file</h1>' );

	} );

	$( '.file-upload-area' ).click( function() {
		$( '#file-upload' ).click();
	} );

	$( '#file-upload' ).change( function( e ) {

		files.list_file( e.target.files );
	
	} );

// Click event for adding new file
	$( 'input[data-function=add-file' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			$( 'div.add-file' ).slideToggle( 250 );

			e.handled = true;
		}
	} );

// Click event for adding new folder
	$( 'input[data-function=add-folder' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			$( 'div.add-folder' ).slideToggle( 250, function() {
				$( '.add-folder input[name=folder_name]' ).focus();
			} );

			e.handled = true;
		}
	} );

// Click event for Add File button
	$( 'input[value=Upload]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			if(files.folder_id < 1) {
				alert("File must be inside the folder!");
				} else {
				files.file_upload(); 
			}
			
			e.handled = true;
		}
	} );

// Click event for Add Folder button
	$( 'input[value=Create\\\ Folder]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			files.folder_create();

			e.handled = true;
		}
	} );

	$( document ).on( 'click', 'input[data-function=rename-folder-save]', function ( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {	
			
			files.save_folder_name();
			e.handled = true;
		}
	} );

	$( document ).on( 'click', 'input[data-function=rename-folder-cancel]', function ( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {	
			
			 create_popup( "folder_rename" );
			e.handled = true;
		}
	} );


// Add context menu
	$( 'tr:not(:first-child)' ).on( 'contextmenu', function( e ) {
		
	} );

	$( 'tr[draggable=true]' ).on( 'contextmenu', function( e ) {

		if( $( this ).hasClass( 'grayed' ) ) {
			alert( 'You do not have permission to access this resource.' );
		} else { 
		files.file_context_menu( 'show', e );
		}
		return false;
	} );


	files.drag_element = null;
	files.drag_items   = document.querySelectorAll( "[draggable=true]:not(.grayed)" );

	for( var i = 0; i < files.drag_items.length; i++ ) {
		
		files.drag_items[i].ondragstart = function ( e ) {
		
		if( this.id.substr( 0, 4 ) == "file" ) {
		var file_id = this.id.substr( 8 );
			e.dataTransfer.setDragImage( document.getElementById( "file_id_" + file_id ), 0, 0 );
		} else if( this.id.substr( 0, 6 ) == "folder" ) {
			var folder_id = this.id.substr( 10 );
			e.dataTransfer.setDragImage( document.getElementById( "folder_id_" + folder_id ), 0, 0 );
		}

			e.dataTransfer.setData( "Text", this.id );
			e.dataTransfer.effectAllowed = "move";
		}
	}

	$( 'tr.file' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			if( $( this ).hasClass( 'grayed' ) ) {
				alert( 'You do not have permission to access this resource.' );
			} else {
				files.file_get( $( this ).data( 'fileid' ));
			}

			e.handled = true;
		}
	} );
  
	$( 'tr.folder' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			if( $( this ).hasClass( 'grayed' ) ) {
				alert( 'You do not have permission to access this resource.' );
			} else {
				var dstfolder_id = $( this ).data( 'folderid' )

				files.folder_list( dstfolder_id );

				files.folder_id = dstfolder_id
			}

			e.handled = true;
		}
	} );

	$( "tr.folder" ).on( "dragenter", function( e ) {
		if ( $( this ).hasClass( 'grayed' ) ) {
			alert( 'You do not have permission to access this resource.' );
		} else {
			files.ignore_drag( e );

			var dstfolder_id = this.id.substr( 10 );
			var src_id       = e.originalEvent.dataTransfer.getData( "Text" );

			if( src_id.substr( 0, 6 ) == "folder" ) {
				var srcfolder_id = src_id.substr( 10 );

				if( dstfolder_id == srcfolder_id ) {
					return false;
				}
			}

			$( this ).addClass( "drag-active" );
		}
	} );

	$( "tr.folder" ).on( "dragover", function( ev ) {
		files.ignore_drag( ev );
		return false;
	} );

	$( "tr.folder" ).on( "drop", function( ev ) {
		files.ignore_drag( ev );
		$( this ).removeClass( "drag-active" );

		var dstfolder_id = this.id.substr( 10 );
		var src_id       = ev.originalEvent.dataTransfer.getData( "Text" );

		if( src_id.substr( 0, 4 ) == "file" ) {
			var srcfile_id = src_id.substr( 8 );

			values = {
				"task"      : "file_move",
				"file_id"   : srcfile_id,
				"folder_id" : dstfolder_id
			};

			// $.post( "files", values, function( result ) {
				$.post( '/handlers/files.php', values, function ( result ) {
 
					var result_json = $.parseJSON( result );
			  
					if ( result_json.status == 'success' ) {
						load_page( 'files', { 'folder_id' : dstfolder_id }, srcfile_id );
					} else if ( result_json.status == 'error' ) { 
						let error_msg = '';
			
						if ( result_json.errors instanceof Array ) {
							for ( let i = 0; i < result_json.errors.length; i++ ) {
								error_msg += result_json.errors[ i ] + "\n";
							}
						} else {
							error_msg = result_json.errors
						}
			
						alert( error_msg );			
					} else { 
						error_handler( { 
							'error'    : result_json.errors,
							'data'     : result_json.data
						} );
					}
			
				} );
		} else if( src_id.substr( 0, 6 ) == "folder" ) {
			var srcfolder_id = src_id.substr( 10 );

			if( dstfolder_id != srcfolder_id ) {
				values = {
					"task"      : "folder_move",
					"parent_id" : dstfolder_id,
					"folder_id" : srcfolder_id
				};

				$.post( '/handlers/files.php', values, function ( result ) {
 
					var result_json = $.parseJSON( result );
			  
					if ( result_json.status == 'success' ) {
						load_page( 'files', { 'parent_id' : dstfolder_id },files.init_actions);
					} else if ( result_json.status == 'error' ) { 
						let error_msg = '';
			
						if ( result_json.errors instanceof Array ) {
							for ( let i = 0; i < result_json.errors.length; i++ ) {
								error_msg += result_json.errors[ i ] + "\n";
							}
						} else {
							error_msg = result_json.errors
						}
			
						alert( error_msg );			
					} else { 
						error_handler( { 
							'error'    : result_json.errors,
							'data'     : result_json.data
						} );
					}
			
				} );
			} else {
				return false;
			}
		}
	} );

	$( "tr.folder" ).on( "dragleave", function( ev ) {
		files.ignore_drag( ev );

		var related = ev.relatedTarget;
		var inside  = false;

		if( related !== this ) {
			if( related ) {
				inside = $.contains( this, related );
			}

			if( !inside ) {
				$( this ).removeClass( "drag-active" );
			}
		}
	} );

	$( document ).on( 'click', 'input[data-function=search-folder]', function ( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {	
			var text = $.trim( $( 'div.search-folder input[name=search_text]' ).val() );

			files.folder_list( files.folder_id, text );

			e.handled = true;
		}
	} );
}

files.list_file = function( files ) {

	if ( files.length > 0 ) {
		for( let i = 0; i < files.length; i++ ){

			let target_id = 'file_item_' + window.upload_index;
			let image     = '';

			
				image = '<a href="' + URL.createObjectURL( files[ i ] ) + '" target="_blank" data-magnify="gallery" data-caption="' + files[ i ].name + '">' +
							'<img style="width: 100px;" data-file="' + window.upload_index + '" src="' + URL.createObjectURL( files[ i ] ) + '" class="img-responsive  ">' +
						'</a>';
		

			let str_content = '<div class="each-item-picture" id="' + target_id + '">' +
					'           <div class="img">' + image + '</div>' +
					'           <div class="details">' +
					'               <p class="file-name">' + files[ i ].name + '</p>' +
					'               <p class="file-size">' + bytesToSize( files[ i ].size ) + '</p>' +
					'           </div>' +
					'    </div>';

			$( '#file-upload-list' ).append( str_content );

			window.dropped_files[window.upload_index] = files[ i ];
			window.upload_index++;
		}
	}
}

files.file_get = function ( file_id ) {
	var values = {
		'task'    : 'file_get',
		'file_id' : file_id
	}

	window.open( '/includes/scripts/file.php?file_id=' + file_id );
}

 files.file_upload = function() {

	$( '.add-file input[name=folder_id]' ).val( files.folder_id );

	var form_data = new FormData( document.getElementById( 'form-uploader' ));

	form_data.append( 'task',      'file_upload' );
	form_data.append( 'folder_id', files.folder_id );

	$.each( window.dropped_files, function ( i, v ) {
		form_data.append( 'file['+i+']', window.dropped_files[i] );
	} );

	$.ajax( {
		url         : '/handlers/files.php',
		type        : 'post',

		xhr         : function() {
			my_xhr = $.ajaxSettings.xhr();
			if( my_xhr.upload ) {
				my_xhr.upload.addEventListener( 'progress', files.handler_progress, false );
			}

			return my_xhr;
		},

		beforeSend  : files.handler_before_send,
		success     : files.handler_complete,
		error       : files.handler_error,

		enctype     : 'multipart/form-data',
		data        : form_data,
		dataType    : 'json',
		cache       : false,
		contentType : false,
		processData : false
	} );

}

files.folder_create = function () {

	var folder_name = $( '.add-folder input[name=folder_name]' ).val();
	
	var values = {
		'task'	     : 'folder_create',
		'parent_id'	 : files.folder_id,
		'name'       : folder_name
	}

	$.post( '/handlers/files.php', values, function ( result ) {
 
		var result_json = $.parseJSON( result );
  
		if ( result_json.status == 'success' ) {
			load_page( 'files', { 'folder_id' : files.folder_id }, files.init_actions );
		} else if ( result_json.status == 'error' ) { 
			let error_msg = '';

			if ( result_json.errors instanceof Array ) {
				for ( let i = 0; i < result_json.errors.length; i++ ) {
					error_msg += result_json.errors[ i ] + "\n";
				}
			} else {
				error_msg = result_json.errors
			}

			alert( error_msg );			
		} else { 
			error_handler( { 
				'error'    : result_json.errors,
				'data'     : result_json.data
			} );
		}

	} );
 
}
 
files.folder_list = function ( folder_id, search = '' ) { 
	var values = {
		'task'      : 'index',
		'folder_id' : folder_id
	}

	if ( search != '' ) {
		values['search'] = search;
	}

	load_page( 'files', values, files.init_actions );
}

files.file_context_menu = function ( task, ev ) {

	if( task == 'show' && files.context_menu == false ) {
		files.context_menu = true;
		$( 'body' )
				.append( $( '<div>' )
				.css( {
					'left'     : ev.pageX + 'px',
					'position' : 'absolute',
					'top'      : ev.pageY + 'px',
					'z-index'  : 40
				} )
				.attr( 'id', 'file-context-menu' )
				.append( $( '<ul>' )
				.append( 
					( typeof $( ev.currentTarget ).data( 'folderid' ) !== 'undefined' && typeof $( ev.currentTarget ).data( 'fileid' ) === 'undefined' ) ? 					
					$( '<li>' )
						.html( 'Rename' )
						.on( 'click', function( e ) {
							e.preventDefault();
							if ( e.handled !== true ) {								
								files.folder_rename( '/ajax/files.php?task=folder_rename&folder_id=' + $( ev.currentTarget ).data( 'folderid' ) );							
								e.handled = true;
							}
						} )
					: ''
				)
				.append(
					// $( ev.currentTarget ).data( 'delete' ) ? 
					$( '<li>' )
						.html( 'Delete' )
						.on( 'click', function( e ) {
							e.preventDefault();
							if ( e.handled !== true ) {
								if( typeof $( ev.currentTarget ).data( 'folderid' ) !== 'undefined' && typeof $( ev.currentTarget ).data( 'fileid' ) === 'undefined' ) {
								// Deleting folder
									files.folder_delete( $( ev.currentTarget ).data( 'folderid' ));
								} else if( typeof $( ev.currentTarget ).data( 'fileid' ) !== 'undefined' ) {
								// Deleting file
									files.file_delete( $( ev.currentTarget ).data( 'fileid' ), $( ev.currentTarget ).data( 'folderid' ), $( ev.currentTarget ).data( 'filename' ) );
								}

								e.handled = true;
							}
						} )
					// : ''
					)
				) 
			)
			.on( 'click', function( e ) {
				e.preventDefault();
				if ( e.handled !== true ) {
					files.file_context_menu( 'hide' );

					e.handled = true;
				}
			} );
 
	} else if( task == 'hide' && files.context_menu == true ) {
		$( '#file-context-menu' ).remove();

		$( 'body' ).off( 'click', function( e ) {
			e.preventDefault();
			if ( e.handled !== true ) {
				files.file_context_menu( 'hide' );

				e.handled = true;
			}
		} );

		files.context_menu = false;
	}

}
 
files.handler_before_send = function ( e ) {
	var progress = $( '<progress></progress>' ).attr( 'id', 'progress-bar' );
	$( '#form-uploader' ).append( progress );
}

files.handler_complete = function ( result ) {
	if( result['status'] == 'success' ) { 
	// Remove progress bar and uploaded files
		window.upload_index  = 0;
		window.dropped_files = {};
		$( '#progress-bar' ).remove();

	// Clear file input field
		$( '.add-file form' )[0].reset();

	// Refresh page 
		load_page( 'files', { 'folder_id' : files.folder_id }, files.init_actions );
	} else if( result['status'] == 'error' ) {
		let error_msg = '';
		if ( result['errors'] instanceof Array ) {
			for ( let i = 0; i < result['errors'].length; i++ ) {
				error_msg += result['errors'][ i ] + "\n";
			}
		} else {
			error_msg = result['errors'];
		}

		alert( error_msg );
	} else {
		error_handler( {
			'function' : 'file_upload',
			'error'    : result['errors'],
			'data'     : result['data']
		} );
	}
	
}

files.handler_error = function ( e ) {
	$( '#progress-bar' ).remove();
	alert( 'No file selected or something error to upload' + e );
}

files.handler_progress = function ( e ) {
	if( e.lengthComputable ) {
		$( 'progress' ).attr( {value:e.loaded, max:e.total} );
	}
}

files.ignore_drag = function ( e ) {
	e.originalEvent.stopPropagation();
	e.originalEvent.preventDefault();
}

files.save_folder_permission = function ( folder_id ) {
/**
* Folder save permissions
*/

	var values = {
		'task' : 'save_folder_permissions'
	};

	var read_chk   = [];
	var upload_chk = [];
	var delete_chk = [];

	$( 'input:checkbox[name=read]:checked' ).each( function () {
		read_chk.push( $( this ).val( ) );
	} );

	$( 'input:checkbox[name=upload]:checked' ).each( function () {
		upload_chk.push( $( this ).val( ) );
	} );

	$( 'input:checkbox[name=delete]:checked' ).each( function () {
		delete_chk.push( $( this ).val( ) );
	} );


	values.read   = read_chk;
	values.upload = upload_chk;
	values.delete = delete_chk;

	if ( typeof folder_id !== 'undefined' ) {
		values.folder_id = folder_id;
	}

	$.post( '/handlers/files.php', values, function ( result ) {

		var result_json = $.parseJSON( result );

		if ( result_json.status == 'success' ) {
			create_popup( 'permissions' );
			load_page( 'files', { 'folder_id' : files.folder_id }, files.init_actions );
		} else if ( result_json.status == 'error' ) { 
			let error_msg = '';

			if ( result_json.errors instanceof Array ) {
				for ( let i = 0; i < result_json.errors.length; i++ ) {
					error_msg += result_json.errors[ i ] + "\n";
				}
			} else {
				error_msg = result_json.errors
			}

			alert( error_msg );
		} else {
			error_handler( {
				'function' : 'save_folder_permission',
				'error'    : result_json.errors,
				'data'     : result_json.data
			} );
		}

	} );

}

files.save_file_permission = function ( file_id ) {
/**
* File save permissions
*/

	var values = {
		'task' : 'save_file_permissions'
	};

	var read_chk   = [];
	var delete_chk = [];

	$( 'input:checkbox[name=read]:checked' ).each( function () {
		read_chk.push( $( this ).val( ) );
	} );

	$( 'input:checkbox[name=delete]:checked' ).each( function () {
		delete_chk.push( $( this ).val( ) );
	} );


	values.read   = read_chk;
	values.delete = delete_chk;

	if ( typeof file_id !== 'undefined' ) {
		values.file_id = file_id;
	}

	$.post( '/handlers/files.php', values, function ( result ) {

		var result_json = $.parseJSON( result );

		if ( result_json.status == 'success' ) {
			create_popup( 'permissions' );
			load_page( 'files', { 'folder_id' : files.folder_id }, files.init_actions );
		} else if ( result_json.status == 'error' ) { 
			let error_msg = '';

			if ( result_json.errors instanceof Array ) {
				for ( let i = 0; i < result_json.errors.length; i++ ) {
					error_msg += result_json.errors[ i ] + "\n";
				}
			} else {
				error_msg = result_json.errors
			}

			alert( error_msg );
		} else {
			error_handler( {
				'function' : 'save_file_permission',
				'error'    : result_json.errors,
				'data'     : result_json.data
			} );
		}

	} );

}

files.save_folder_name = function () {
/**
* File save permissions
*/

	var values = {
		'task' : 'save_folder'
	};

	$.each( $( 'div.folder_rename form' ).serializeArray(), function ( i, field ) {
		values[ field.name ] = field.value;
	} );

	$.post( '/handlers/files.php', values, function ( result ) {

		var result_json = $.parseJSON( result );

		if ( result_json.status == 'success' ) {
			create_popup( 'folder_rename' );
			$( '#folder_id_' + values['folder_id'] + ' td:eq(1)' ).text( values['name'] );
			//load_page( 'files', { 'folder_id' : files.folder_id }, files.init_actions );
		} else if ( result_json.status == 'error' ) { 
			let error_msg = '';

			if ( result_json.errors instanceof Array ) {
				for ( let i = 0; i < result_json.errors.length; i++ ) {
					error_msg += result_json.errors[ i ] + "\n";
				}
			} else {
				error_msg = result_json.errors
			}

			alert( error_msg );
		} else {
			error_handler( {
				'function' : 'save_folder_name',
				'error'    : result_json.errors,
				'data'     : result_json.data
			} );
		}

	} );

}

files.folder_rename = function ( url ) {
	create_popup( 'folder_rename', {
		'close'  : true,
		'height' : '200px',
		'title'  : 'Rename',
		'url'    : url,
		'width'  : '400px'
	}, 400 );
}

files.permission_actions = function ( url ) {
	create_popup( 'permissions', {
		'close'  : true,
		'height' : '300px',
		'title'  : 'Permissions',
		'url'    : url,
		'width'  : '550px'
	}, 400 );
}

files.checkbox_action = function( name, everyone = false ) {
	if( everyone ) {
		if( $( 'input[name=' + name + '_everyone]' ).is( ':checked' ) ) {
			$( 'input[name=' + name + ']' ).prop( 'checked', false );
		}
	} else {
		let all = true;
		$( 'input[name=' + name + ']' ).each( function() {
			if( $( this ).is( ':checked' ) ) {
				all = false;
			}
		});

		$( 'input[name=' + name + '_everyone]' ).prop( 'checked', all );
	}
}

files.file_delete = function ( file_id, folder_id, file_name ) {
	if( confirm( 'Are you sure you want to delete this file?' )) {
		var values = {
			'task'      : 'file_delete',
			'file_id'   : file_id,
			'folder_id' : folder_id,
			'file_name' : file_name
		}

		$.post( '/handlers/files.php', values, function( result ) {
			var result = $.parseJSON( result );

			if( result['status'] == 'success' ) {
				load_page( 'files', { 'folder_id' : files.folder_id }, files.init_actions );
			} else {
				alert( result['errors'] );
			}
		} );
	}
}

files.folder_delete = function ( folder_id ) {
	if( confirm( 'Deleting this folder will delete all of its contents as well.  Are you sure?' )) {
		var values = {
			'task'      : 'folder_delete',
			'folder_id' : folder_id
		}

		$.post( '/handlers/files.php', values, function( result ) {
			var result = $.parseJSON( result );

			if( result['status'] == 'success' ) {
				load_page( 'files', { 'folder_id' : files.folder_id }, files.init_actions );
			} else {
				alert( result['errors'] );
			}
		} );
	}
}