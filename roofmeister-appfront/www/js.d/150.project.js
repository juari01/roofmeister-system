project = {};

project.folder_id     	 = 0;
project.folder_id_exist = false;  
project.subfolder_id 	 = 0; 
project.upload_index  	 = 0;
project.dropped_files	 = {};
project.search        	 = '';


project.init_actions 	 = function () {
project.subfolder_id 	 = 0;
project.folder_id_exist  = false;
// Click event for adding new project
	$( 'input[data-function=add-project]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			load_page( 'projects', {
				'task' : 'addedit'
			}, project.form_actions );

			e.handled = true;
		}
	} );


	$( 'tr' ).on( 'click', function( e ) {
		e.preventDefault();

		if ( e.handled !== true ) {

			load_page( 'projects', {

				'task'       : 'addedit',
				'project_idtr' : $( this ).data( 'project_id' )
			
			}, project.form_actions );

			e.handled = true;
		}
	} );


	if( typeof window.project === 'undefined' ) {
		window.project = {};
	}

	// Go to where we left off
	if( typeof window.project.page !== 'undefined' ) {
		project_search();
	}else{
		window.project.page = 1;
	}

	if( typeof window.project.search !== 'undefined' && window.project.search != '' ) {
		$( '.search-project' ).val( window.project.search );
	}

	$( 'span[data-function=view-page]' ).on( 'click', function() {

		window.project.page = $( this ).data( 'page-num' );

		project_search();
		
		} );



	$('.search-project').off().on( "keyup", function(e) {

		window.project.search = $( this ).val();
		window.project.page   = 1;

		project_search();
	});

	function project_search() {
		var values = {
			'task'   : 'index',
			'i'      : window.project.page,
			'search' : window.project.search
		};

		 $.post( '/handlers/projects.php', values, function ( result ) {
			var result_json = $.parseJSON( result );

	
				if ( result_json.status == 'success' ) {

				  $( 'div[data-name=table-container]' ).html( result_json['content']['table'] );
				  $( 'span[data-name=page-container]' ).html( result_json['content']['pages'] );

				$( '.encrypted-text' ).each( function() {
                    let text = $( this ).data( 'text' );
                    $( this ).html( atob( text ));
                });

				// Click event for editing an existing customer
				$( 'tr' ).on( 'click', function( e ) {
					e.preventDefault();
			
					if ( e.handled !== true ) {
			
						load_page( 'projects', {
							'task'       : 'addedit',
							'project_idtr' : $( this ).data( 'project_id' )
						}, project.form_actions );
			
						e.handled = true;
					}
				} );

			$( 'span[data-function=view-page]' ).on( 'click', function() {
				// Save the page number we are going to
				window.project.page = $( this ).data( 'page-num' );
				// Get the page
				project_search();
				
				} );
			

			} else {
			     alert( result );
		

			}

		} );

	}

}


project.form_actions = function () {

	project.folder_id = $( '[name=folder_id]' ).val();
	// Click event for Add File button
	$( 'input[value=Upload]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
	
			if (project.subfolder_id == 0) {
				project.folder_id = $( '[name=folder_id]' ).val();
			    project.file_upload();
				console.log(project.folder_id);
				
			} else {
				project.folder_id = project.subfolder_id;
				project.file_upload();
				console.log(project.subfolder_id);
			}	
		
			e.handled = true;
		}
	} );

	// Click event for Add Folder button
	$( 'input[value=Create\\\ Folder]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {

			if (project.subfolder_id == 0) {
				project.folder_id = $( '[name=folder_id]' ).val();
			    project.folder_create();
				console.log(project.folder_id);
				
			} else {
				project.folder_id = project.subfolder_id;
				project.folder_create(); 
				console.log(project.folder_id);
			}	
			e.handled = true;
		}
	} );

	$( '#show-file' ).click( function(e) {
		e.stopPropagation();
		e.preventDefault();
		project.showfile();

	} );

	$( 'input[data-function=add-file' ).on( 'click', function( e ) {
		$('div.add-file').slideToggle(250);
	} );

	$( 'input[data-function=add-folder' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			$( 'div.add-folder' ).slideToggle( 250, function() {
				$( '.add-folder input[name=folder_name]' ).focus();
			} );

			e.handled = true;
		}
	} );

	// Drag enter
	$( '.file-upload-area' ).on( 'dragenter', function ( e ) {
		$( this ).find( 'h1' ).text( 'Drop' );
	} );

	$( '.file-upload-area' ).on( 'dragleave', function ( e ) {
		$( this ).find( 'h1' ).html( '<h1>Drag and Drop file here<br/>Or<br/>Click to select file</h1>' );
	} );

// Drag over
	$( '.file-upload-area' ).on('dragover', function ( e ) {
		$( this ).find( 'h1' ).text( 'Drop' );
	} );

// Drop
	$( '.file-upload-area' ).on( 'drop', function ( e ) {

		project.list_file( e.originalEvent.dataTransfer.files );

		$( this ).find( 'h1' ).html( '<h1>Drag and Drop file here<br/>Or<br/>Click to select file</h1>' );

	} );

	$( '.file-upload-area' ).click( function() {
		$( '#file-upload' ).click();
	} );

	$( '#file-upload' ).change( function( e ) {

		project.list_file( e.target.files );
	
	} );

	
	$(".select-projcustomer-by-id").off().on( 'click', function( e ) {
		e.preventDefault();
		e.stopPropagation();

		var project_id 	= $( '[name=project_id]' ).val();
		var projectname 	= $( '[name=name]' ).val();
		var projdescription = $( '[name=description]' ).val();
		
		load_page('projects', {

			'task' 			 	: 'selectcustomer',
			'project_id'		: project_id,
			'projectname'		: projectname,
			'projdescription'	: projdescription

		}, project.form_actions );

	});


	$(".select-projproperty-by-id").off().on( 'click', function( e ) {

		e.preventDefault();
		e.stopPropagation();

		var project_id 		  = $( '[name=project_id]' ).val();
		var projcustomer_id   = $( '[name=customer_id]' ).val();
		var projcustomer_name = $( '[name=projcustomer_name]' ).val();
		var projectname 	  = $( '[name=name]' ).val();
		var projdescription   = $( '[name=description]' ).val();

		load_page('projects', {
			
			'task' 				: 'selectproperty',
			'project_id'		: project_id,
			'projcustomer_id'   : projcustomer_id,
			'projcustomer_name'	: projcustomer_name,
			'projectname'		: projectname,
			'projdescription'	: projdescription,

		}, project.form_actions );

	});



// Click event for back button
	$( 'input[data-function=back-project]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			e.handled = true;

			load_page( "projects",  {}, project.init_actions );
		}
	} );

	$( ".back-projselectcustomer" ).off().on( 'click', function( e ) {

		e.preventDefault();
		if ( e.handled !== true ) {
			e.handled = true;

			 back_page();
		}
	} );


	// $( 'input[data-function=back-selectcustomer]' ).on( 'click', function( e ) {
		$(".back-projselectproperty").off().on( 'click', function( e ) {
		e.preventDefault();

		if ( e.handled !== true ) {
			e.handled = true;

			back_page();
		}
	} );


	$( 'input[data-function=save]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {

			var varproject_id = $( '[name=project_id]' ).val();

			// if ( typeof $( '[name=project_id]' ).val() !== 'undefined' ) {
			if  ( varproject_id ) {

				project.project_save( varproject_id);

			} else {
				project.project_save();
			}

			e.handled = true;
		}
	} );

	$( '.breadcrumbsclick' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			project.showfile();
		}
	} );

	$(".add-note-project").off().on( 'click', function( e ) {
		e.preventDefault();
		e.stopPropagation();

		var project_id 		  = $( '[name=project_id]' ).val();

		
			load_page('projects', {
				'task' 			 : 'add_edit_note',
				'project_id' 	 : project_id,

			}, project.form_actions );

	});

	$(".proj_edit-note-by-id").off().on( 'click', function( e ) {
		e.preventDefault();
		e.stopPropagation();

		var note_id 	= $(this).data('id');
		var project_id = $( '[name=project_id]' ).val();

		load_page('projects', {
			'task' 			 : 'add_edit_note',
			'note_id' 	 	 : note_id,
			'project_id' 	 : project_id

		}, project.form_actions );

	} );

	$( 'input[data-function=project-save-note]' ).on( 'click', function( e ) {
		e.preventDefault(); 
		if ( e.handled !== true ) {

			var note_id 	= $( '[name=note_id]' ).val();
			var project_id  = $( '[name=project_id]' ).val();

			if ( note_id ) {
				project.note_save( note_id, project_id );
			} else {
				project.note_save();
			}

			e.handled = true;
		}
	} );

	project.note_save = function ( note_id, project_id ) { 
	
		var values = {
			'task' : 'save_project_note'
		};
	
		$.each( $( 'form[name=project_note_save]' ).serializeArray(), function ( i, field ) {
			values[ field.name ] = field.value;
		} );
	
			values.note_id = 0;
	
		if ( typeof note_id !== 'undefined' ) {
			values.note_id = note_id;
		}
	
		if ( typeof project_id !== 'undefined' ) {
			values.project_id = project_id;
		}
	
	
		$.post( '/handlers/projects.php', values, function ( result ) {
	
			var result_json = $.parseJSON( result );
	
			if ( result_json.status == 'success' ) {
	
				var project_id = $( '[name=project_id]' ).val();
			
				load_page('projects', {
					'task' : 'addedit',
					'project_id' : project_id,
					'project_idtr' : project_id
	
				}, project.form_actions );
	
	
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
					'function' : 'note_save',
					'error'    : result_json.errors,
					'data'     : result_json.data
				} );
			}
	
		} );
	
	}
	

}

project.project_save = function (project_id) {

	var values = {
		'task' : 'save'
	};

	$.each( $( 'form[name=project_save]' ).serializeArray(), function ( i, field ) {
		values[ field.name ] = field.value;
	} );

	if ( project_id ) {

		values.project_id = project_id;
		values.addproject_id = true;
	} 

	$.post( '/handlers/projects.php', values, function ( result ) {

		var result_json = $.parseJSON( result );

		if ( result_json.status == 'success' ) {
			load_page( 'projects', {}, project.init_actions );
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
				'function' : 'project_save',
				'error'    : result_json.errors,
				'data'     : result_json.data
			} );
		}

	} );
}

project.file_upload = function() {

	$( '.add-file input[name=folder_id]' ).val( project.folder_id );

	var form_data = new FormData( document.getElementById( 'form-uploader' ));
	var project_id = $( '[name=project_id]' ).val();
	var projectname = $('[name=name]' ).val();

	form_data.append( 'task',      'file_upload' );
	form_data.append( 'folder_id', project.folder_id );
	form_data.append( 'project_id', project_id );
	form_data.append( 'projectname', projectname );

	$.each( project.dropped_files, function ( i, v ) {
		form_data.append( 'file['+i+']', project.dropped_files[i] );
	} );

	$.ajax( {
		url         : '/handlers/files.php',
		type        : 'post',

		xhr         : function() {
			my_xhr = $.ajaxSettings.xhr();
			if( my_xhr.upload ) {
				my_xhr.upload.addEventListener( 'progress', project.handler_progress, false );
			}

			return my_xhr;
		},

		beforeSend  : project.handler_before_send,
		success     : project.handler_complete,
		error       : project.handler_error,

		enctype     : 'multipart/form-data',
		data        : form_data,
		dataType    : 'json',
		cache       : false,
		contentType : false,
		processData : false
	} );

}

project.list_file = function( files ) {

	if ( files.length > 0 ) {
		for( let i = 0; i < files.length; i++ ){

			let target_id = 'file_item_' + project.upload_index;
			let image     = '';

			
				image = '<a href="' + URL.createObjectURL( files[ i ] ) + '" target="_blank" data-magnify="gallery" data-caption="' + files[ i ].name + '">' +
							'<img style="width: 100px;" data-file="' + project.upload_index + '" src="' + URL.createObjectURL( files[ i ] ) + '" class="img-responsive">' +
						'</a>';
		

			let str_content = '<div class="each-item-picture" id="' + target_id + '">' +
					'           <div class="img">' + image + '</div>' +
					'           <div class="details">' +
					'               <p class="file-name">' + files[ i ].name + '</p>' +
					'               <p class="file-size">' + bytesToSize( files[ i ].size ) + '</p>' +
					'           </div>' +
					'    </div>';

			$( '#file-upload-list' ).append( str_content );

			project.dropped_files[project.upload_index] = files[ i ];
			project.upload_index++;
		}
	}
}


project.handler_before_send = function ( e ) {
	var progress = $( '<progress></progress>' ).attr( 'id', 'progress-bar' );
	$( '#form-uploader' ).append( progress );
}

project.handler_complete = function ( result ) {
	if( result['status'] == 'success' ) { 
	// Remove progress bar
		$( '#progress-bar' ).remove();
		project.upload_index  = 0;
		project.dropped_files = {};

	// Clear file input field
		$( '.add-file form' )[0].reset();
		var project_id = $( '[name=project_id]' ).val();
	// Refresh page 
		load_page('projects', { 'task': 'addedit', 'folder_id': project.folder_id, 'project_idtr': project_id }, project.form_actions);
		setTimeout(function(){
			showviewbutton.style.display = "none";
			showaddfile.style.display = "block";
	   }, 500);
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

project.handler_error = function ( e ) {
	$( '#progress-bar' ).remove();
	alert( 'Missing file' );
}

project.handler_progress = function ( e ) {
	if( e.lengthComputable ) {
		$( 'progress' ).attr( {value:e.loaded, max:e.total} );
	}
}


project.showfile = function () {
	var folder_id   = $( '[name=folder_id]' ).val();
	var project_id  = $( '[name=project_id]' ).val();
	var projectname = $( '[name=name]' ).val();
	project.subfolder_id = 0;
	
	if (folder_id) {
		console.log("Exist");
		project.folder_id_exist = true; 

			load_page('projects', {
				'task': 'addedit',
				'project_idtr': project_id,
				'folder_id': folder_id,

			}, project.form_actions);
			
		} else {
			console.log("Create folder for project");

			load_page('projects', {
				'task': 'addedit',
				'project_idtr': project_id,
				'createfolder': true,
				'projectname': projectname,

			}, project.form_actions);
		
			setTimeout(function(){
			console.log(folder_id);

				load_page('projects', {
					'task': 'addedit',
					'project_idtr': project_id,
					'folder_id': project.folder_id,
				}, project.form_actions);

				setTimeout(function(){
					project.showfile();
					 }, 700);
			}, 300);

		}
	
}

project.file_get = function ( file_id ) {
	var values = {
		'task'    : 'file_get',
		'file_id' : file_id
	}

	window.open( '/includes/scripts/file.php?file_id=' + file_id );
}

project.folder_list = function ( folder_id, search = '' ) { 
	showviewbutton.style.display = "none";

	var values = {
		'task'      : 'addedit',
		'folder_id' : folder_id
	}

	if ( search != '' ) {
		values['search'] = search;
	}

	load_page('projects', {
		'task': 'addedit',
		'project_idtr': project_id,
		'folder_id': folder_id,

	}, project.form_actions);

	setTimeout(function(){
		showviewbutton.style.display = "none";
		showaddfile.style.display = "block";
   }, 500);
}

project.folder_create = function () {

	var folder_name = $( '.add-folder input[name=folder_name]' ).val();
	
	var values = {
		'task'	     : 'folder_create',
		'parent_id'	 : project.folder_id,
		'name'       : folder_name
	}

	$.post( '/handlers/files.php', values, function ( result ) {
 
		var result_json = $.parseJSON( result );
  
		if ( result_json.status == 'success' ) {
			var project_id = $( '[name=project_id]' ).val();
			load_page('projects', { 'task': 'addedit', 'folder_id': project.folder_id, 'project_idtr': project_id }, project.form_actions);
		setTimeout(function(){
			showviewbutton.style.display = "none";
			showaddfile.style.display = "block";
	   }, 500);
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