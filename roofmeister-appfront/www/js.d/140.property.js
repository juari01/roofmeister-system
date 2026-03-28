property = {};

property.folder_id    	 = 0;
property.folder_id_exist = false; 
property.subfolder_id 	 = 0;  
property.upload_index  	 = 0;
property.dropped_files 	 = {};
property.search        	 = '';

property.init_actions  	 = function () {
property.subfolder_id 	 = 0; 
property.folder_id_exist = false; 

// Click event for adding new property
	$( 'input[data-function=add-property]' ).on( 'click', function( e ) {
			
		e.preventDefault();
		if ( e.handled !== true ) {
			load_page( 'property', {
				'task' : 'addedit'
			}, property.form_actions );

			e.handled = true;
		}
	} );


	if( typeof window.property === 'undefined' ) {
		window.property = {};
	}

	// Go to where we left off
	if( typeof window.property.page !== 'undefined' ) {
		property_search();
	}else{
		window.property.page = 1;
	}

	if( typeof window.property.search !== 'undefined' && window.property.search != '' ) {
		$( '.search-property' ).val( window.property.search );
	}


	$( 'span[data-function=view-page]' ).on( 'click', function() {
		window.property.page = $( this ).data( 'page-num' );

		property_search();
		
	} );

	$('.search-property').off().on( "keyup", function(e) {
		window.property.search = $( this ).val();
		window.property.page   = 1;

		property_search();
	});

	function property_search() {
		var values = {
			'task'   : 'index',
			'i'      : window.property.page,
			'search' : window.property.search
		};

		$.post( '/handlers/property.php', values, function ( result ) {
			var result_json = $.parseJSON( result ); 
		
			if ( result_json.status == 'success' ) {

				$( 'div[data-name=table-container]' ).html( result_json['content']['table'] );
				$( 'span[data-name=page-container]' ).html( result_json['content']['pages'] );

			  $( '.encrypted-text' ).each( function() {
				  let text = $( this ).data( 'text' );
				  $( this ).html( atob( text ));
			  });


			  $( 'tr' ).on( 'click', function( e ) {
				e.preventDefault();
				if ( e.handled !== true ) {
					load_page( 'property', {
						'task'     : 'addedit',
						'property_id' : $( this ).data( 'property_id' )
					}, property.form_actions );
		
					e.handled = true;
				}
			} );

			$( 'span[data-function=view-page]' ).on( 'click', function() {
				window.property.page = $( this ).data( 'page-num' );
		
				property_search();
				
				} );


			}
			else 
			{
				alert( result );
			}
		} );
	}

// Click event for editing an existing group
	$( 'tr' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			load_page( 'property', {
				'task'     : 'addedit',
				'property_id' : $( this ).data( 'property_id' )
			}, property.form_actions );

			e.handled = true;
		}
	} );
}


property.form_actions = function () {

	property.folder_id = $( '[name=folder_id]' ).val();
	// Click event for Add File button
	$( 'input[value=Upload]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			
			if (property.subfolder_id == 0) {
				property.folder_id = $( '[name=folder_id]' ).val();
			    property.file_upload();
				console.log(property.folder_id);
				
			} else {
				property.folder_id = property.subfolder_id;
				property.file_upload(); 
				console.log(property.folder_id);
			}	
		
			e.handled = true;
		}
	} );

	$( 'input[value=Create\\\ Folder]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {

			if (property.subfolder_id == 0) {
				property.folder_id = $( '[name=folder_id]' ).val();
			    property.folder_create();
				console.log(property.folder_id);
				
			} else {
				property.folder_id = property.subfolder_id;
				property.folder_create(); 
				console.log(property.folder_id);
			}	
			e.handled = true;
		}
	} );

	$( '#show-file' ).click( function(e) {
		e.stopPropagation();
		e.preventDefault();
		property.showfile();

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
	

		property.list_file( e.originalEvent.dataTransfer.files );

		$( this ).find( 'h1' ).html( '<h1>Drag and Drop file here<br/>Or<br/>Click to select file</h1>' );

	} );

	$( '.file-upload-area' ).click( function() {
		$( '#file-upload' ).click();
	} );

	$( '#file-upload' ).change( function( e ) {

		property.list_file( e.target.files );
	
	} );


	$(".back-selectcustomer").off().on( 'click', function( e ) {
		e.preventDefault();
		e.stopPropagation();

		back_page();
	} );

// Click event for back button
	$( 'input[data-function=back-property]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			 e.handled = true;

			load_page( "property", {}, property.init_actions );
		}
	} );

	$( 'input[data-function=back_propcontact]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			e.handled = true;

			back_page();
		}
	} );

	$( ".back-selectcustomer" ).off().on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			 e.handled = true;

			 back_page();
		}
	} );

	$( ".remove-customer-by-id" ).off().on( 'click', function( e ) {
		e.preventDefault();
		e.stopPropagation();
		
		var property_id = $( '[name=property_id]' ).val();
		var customer_id = $(this).data('id');
		var customer_name = $(this).prop('name');	
		var remove ="Remove " + customer_name + " customer?" ;

	

				if (confirm(remove) == true) {

					load_page('property', {
						'task' : 'addedit',
						'customer_id' : customer_id,
						'delete' 	  : true,
						'property_id' : property_id
	
					}, property.form_actions );

				}
	
	});



	$(".add-propcontact").off().on( 'click', function( e ) {
		e.preventDefault();
		e.stopPropagation();


		var property_id = $( '[name=property_id]' ).val();
		load_page('property', {
			'task' 			 : 'addeditpropcontact',
			'property_id' 	 : property_id

		}, property.form_actions );
		
	});

	$(".editprop-contact-id").off().on( 'click', function( e ) {
		e.preventDefault();
		e.stopPropagation();
		var propcontactid = $(this).data('id');
		var property_id = $( '[name=property_id]' ).val();

		load_page('property', {
			'task' 			 : 'addeditpropcontact',
			'propcontact_id' : propcontactid,
			'property_id' 	 : property_id

		}, property.form_actions );

	} );


	$(".add-link-customer").off().on( 'click', function( e ) {
		e.preventDefault();
		e.stopPropagation();


		var property_id = $( '[name=property_id]' ).val();
		load_page('property', {
			'task' 			 : 'selectcustomer',
			'property_id' 	 : property_id

		}, property.form_actions );
		

		// select_link_property(property_id);
	});

	
	$( 'input[data-function=save]' ).on( 'click', function( e ) {
		e.preventDefault();

		if ( e.handled !== true ) {

			if ( typeof $( '[name=property_id]' ).val() !== 'undefined' ) {

				property.property_save( $( '[name=property_id]' ).val() );

			} else {

				property.property_save();
			}

			e.handled = true;
		}
	} );

	$( 'input[data-function=savepropcontact]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {

			if ( typeof $( '[name=contact_id]' ).val() !== 'undefined' ) {

				property.propcontact_save( $( '[name=contact_id]' ).val() );

			} else {

				property.propcontact_save();

			}

			e.handled = true;
		}
	} );

	$( '.breadcrumbsclick' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			property.showfile();
		}
	} );

	$(".add-note-property").off().on( 'click', function( e ) {
		e.preventDefault();
		e.stopPropagation();

		var property_id = $( '[name=property_id]' ).val();
		
			load_page('property', {
				'task' 			 : 'add_edit_note',
				'property_id' 	 : property_id

			}, property.form_actions );
	});

	$(".prop_edit-note-by-id").off().on( 'click', function( e ) {
		e.preventDefault();
		e.stopPropagation();

		var note_id 	= $(this).data('id');
		var property_id = $( '[name=property_id]' ).val();

		load_page('property', {
			'task' 			 : 'add_edit_note',
			'note_id' 	 	 : note_id,
			'property_id' 	 : property_id

		}, property.form_actions );

	} );

	$( 'input[data-function=property-save-note]' ).on( 'click', function( e ) {
		e.preventDefault(); 
		if ( e.handled !== true ) {

			var note_id 	= $( '[name=note_id]' ).val();
			var property_id = $( '[name=property_id]' ).val();

			if ( note_id ) {
				property.note_save( note_id, property_id );
			} else {
				property.note_save();
			}

			e.handled = true;
		}
	} );
}	

property.note_save = function ( note_id, property_id ) { 
	
	var values = {
		'task' : 'save_property_note'
	};

	$.each( $( 'form[name=property_note_save]' ).serializeArray(), function ( i, field ) {
		values[ field.name ] = field.value;
	} );

		values.note_id = 0;

	if ( typeof note_id !== 'undefined' ) {
		values.note_id = note_id;
	}

	if ( typeof property_id !== 'undefined' ) {
		values.property_id = property_id;
	}


	$.post( '/handlers/property.php', values, function ( result ) {

		var result_json = $.parseJSON( result );

		if ( result_json.status == 'success' ) {

			var property_id = $( '[name=property_id]' ).val();

			load_page('property', {
				'task' : 'addedit',
				'property_id' : property_id

			}, property.form_actions );


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

property.propcontact_save = function ( contact_id ) { 
	var values = {
		'task' : 'savepropcontact'
	};

	$.each( $( 'form[name=propcontact_save]' ).serializeArray(), function ( i, field ) {
		values[ field.name ] = field.value;
	} );

	if ( typeof contact_id !== 'undefined' ) {
		values.contact_id = contact_id;
	}

	
	$.post( '/handlers/property.php', values, function ( result ) {

		var result_json = $.parseJSON( result );

		if ( result_json.status == 'success' ) {

			var property_id = $( '[name=property_id]' ).val();

			load_page('property', {
				'task' : 'addedit',
				'property_id' : property_id

			}, property.form_actions );


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
				'function' : 'property_save',
				'error'    : result_json.errors,
				'data'     : result_json.data
			} );
		}

	} );

}

property.property_save = function ( property_id ) {
/**
* Property Save
* Takes changes from the property form and submits them to the server
* to be saved.
*/
	
	var values = {
		'task' : 'save'
	};

	$.each( $( 'form[name=property_save]' ).serializeArray(), function ( i, field ) {
		values[ field.name ] = field.value;
	} );

	if ( typeof property_id !== 'undefined' ) {
		values.property_id = property_id;
	}

	$.post( '/handlers/property.php', values, function ( result ) {

		var result_json = $.parseJSON( result );

		if ( result_json.status == 'success' ) {
			load_page( 'property', {}, property.init_actions );
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
				'function' : 'property_save',
				'error'    : result_json.errors,
				'data'     : result_json.data
			} );
		}

	} );
}

property.handler_before_send = function ( e ) {
	
	var progress = $( '<progress></progress>' ).attr( 'id', 'progress-bar' );
	$( '#form-uploader' ).append( progress );
}

property.file_get = function ( file_id ) {
	var values = {
		'task'    : 'file_get',
		'file_id' : file_id
	}

	window.open( '/includes/scripts/file.php?file_id=' + file_id );
}

property.handler_complete = function ( result ) {
	if( result['status'] == 'success' ) { 
	// Remove progress bar
		$( '#progress-bar' ).remove();
		property.upload_index  = 0;
		property.dropped_files = {};

	// Clear file input field
		$( '.add-file form' )[0].reset();
		var property_id = $( '[name=property_id]' ).val();
	// Refresh page 
		load_page('property', { 'task': 'addedit', 'folder_id': property.folder_id, 'property_id': property_id }, property.form_actions);
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

property.handler_error = function ( e ) {
	$( '#progress-bar' ).remove();
	alert( 'Missing file' );
}

property.handler_progress = function ( e ) {
	if( e.lengthComputable ) {
		$( 'progress' ).attr( {value:e.loaded, max:e.total} );
	}
}

property.file_upload = function() {

	$( '.add-file input[name=folder_id]' ).val( property.folder_id );

	var form_data = new FormData( document.getElementById( 'form-uploader' ));
	var property_id = $( '[name=property_id]' ).val();
	var propertyname = $('[name=name]' ).val();

	form_data.append( 'task',      'file_upload' );
	form_data.append( 'folder_id', property.folder_id );
	form_data.append( 'property_id', property_id );
	form_data.append( 'propertyname', propertyname );

	$.each( property.dropped_files, function ( i, v ) {
		form_data.append( 'file['+i+']', property.dropped_files[i] );
	} );

	$.ajax( {
		url         : '/handlers/files.php',
		type        : 'post',

		xhr         : function() {
			my_xhr = $.ajaxSettings.xhr();
			if( my_xhr.upload ) {
				my_xhr.upload.addEventListener( 'progress', property.handler_progress, false );
			}

			return my_xhr;
		},

		beforeSend  : property.handler_before_send,
		success     : property.handler_complete,
		error       : property.handler_error,

		enctype     : 'multipart/form-data',
		data        : form_data,
		dataType    : 'json',
		cache       : false,
		contentType : false,
		processData : false
	} );

}

property.list_file = function( files ) {

		if ( files.length > 0 ) {
			for( let i = 0; i < files.length; i++ ){
	
				let target_id = 'file_item_' + property.upload_index;
				let image     = '';
	
				
					image = '<a href="' + URL.createObjectURL( files[ i ] ) + '" target="_blank" data-magnify="gallery" data-caption="' + files[ i ].name + '">' +
								'<img style="width: 100px;" data-file="' + property.upload_index + '" src="' + URL.createObjectURL( files[ i ] ) + '" class="img-responsive">' +
							'</a>';
			
	
				let str_content = '<div class="each-item-picture" id="' + target_id + '">' +
						'           <div class="img">' + image + '</div>' +
						'           <div class="details">' +
						'               <p class="file-name">' + files[ i ].name + '</p>' +
						'               <p class="file-size">' + bytesToSize( files[ i ].size ) + '</p>' +
						'           </div>' +
						'    </div>';
	
				$( '#file-upload-list' ).append( str_content );
	
				property.dropped_files[property.upload_index] = files[ i ];
				property.upload_index++;
			}
		}
	}

	property.showfile = function () {

		var folder_id    = $( '[name=folder_id]' ).val();
		var property_id  = $( '[name=property_id]' ).val();
		var propertyname = $( '[name=name]' ).val();
		property.subfolder_id = 0;

		if (folder_id) {
			console.log( "Exist" );
			property.folder_id_exist = true; 
	
				load_page('property', {
					'task': 'addedit',
					'property_id': property_id,
					'folder_id': folder_id,
	
				}, property.form_actions);
			
			} else { 
				console.log("Create folder for property");
	
				load_page('property', {
					'task': 'addedit',
					'property_id': property_id,
					'createfolder': true,
					'propertyname': propertyname,
	
				}, property.form_actions);
			
				setTimeout(function(){
				console.log(folder_id);
	
					load_page('property', {
						'task': 'addedit',
						'property_id': property_id,
					}, property.form_actions);
	
					setTimeout(function(){
						property.showfile();
					}, 700);
				}, 300);
	
			}	
	}

	property.folder_create = function () {

		var folder_name = $( '.add-folder input[name=folder_name]' ).val();
		
		var values = {
			'task'	     : 'folder_create',
			'parent_id'	 : property.folder_id,
			'name'       : folder_name
		}
	
		$.post( '/handlers/files.php', values, function ( result ) {
	 
			var result_json = $.parseJSON( result );
	  
			if ( result_json.status == 'success' ) {
				var property_id = $( '[name=property_id]' ).val();
				load_page('property', { 'task': 'addedit', 'folder_id': property.folder_id, 'property_id': property_id }, property.form_actions);
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

	property.folder_list = function ( folder_id, search = '' ) { 
		showviewbutton.style.display = "none";
	
		var values = {
			'task'      : 'addedit',
			'folder_id' : folder_id
		}
	
		if ( search != '' ) {
			values['search'] = search;
		}
	
		load_page('property', {
			'task': 'addedit',
			'property_id': property_id,
			'folder_id': folder_id,
	
		}, property.form_actions);
	
		setTimeout(function(){
			showviewbutton.style.display = "none";
			showaddfile.style.display = "block";
	   }, 500);
	}