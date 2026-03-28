customer = {};

customer.folder_id       = 0; 
customer.folder_id_exist = false; 
customer.subfolder_id    = 0; 
customer.upload_index  	 = 0;
customer.dropped_files 	 = {};
customer.search          = '';


customer.init_actions  	 = function () {
customer.subfolder_id  	 = 0;
customer.folder_id_exist = false; 

        $( '.encrypted-text' ).each( function() {
            let text = $( this ).data( 'text' );
            $( this ).html( atob( text ));
        });

// Click event for adding new customer
	$( 'input[data-function=add-customer]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			load_page( 'customer', {
				'task' : 'addedit'
			}, customer.form_actions );

			e.handled = true;
		}
	} );


// Click event for editing an existing customer
	$( 'tr' ).on( 'click', function( e ) {
		e.preventDefault();
	
		if ( e.handled !== true ) {
			load_page( 'customer', {
				'task'     : 'addedit',
				'customer_id' : $( this ).data( 'customer_id' ),
			}, customer.form_actions );

			e.handled = true;
		}
	} );


	if( typeof window.customer === 'undefined' ) {
		window.customer = {};
	}

	// Go to where we left off
	if( typeof window.customer.page !== 'undefined' ) {
		customer_search();
	}else{
		window.customer.page = 1;
	}

	if( typeof window.customer.search !== 'undefined' && window.customer.search != '' ) {
		$( '.customer-search' ).val( window.customer.search );
	}


	$( 'span[data-function=view-page]' ).on( 'click', function() {

		window.customer.page = $( this ).data( 'page-num' );

		customer_search();
		
		} );


	$('.customer-search').off().on( "keyup", function(e) {

		window.customer.search = $( this ).val();
		window.customer.page   = 1;

		customer_search();
	});
		
	function customer_search() {
		var values = {
			'task'   : 'index',
			'i'      : window.customer.page,
			'search' : window.customer.search
		};

		 $.post( '/handlers/customer.php', values, function ( result ) {
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

					load_page( 'customer', {
						'task'     : 'addedit',
						 'customer_id' : $( this ).data( 'customer_id' )
					}, customer.form_actions );

					e.handled = true;
				}
			} );

			$( 'span[data-function=view-page]' ).on( 'click', function() {
				// Save the page number we are going to
				window.customer.page = $( this ).data( 'page-num' );
				// Get the page
				customer_search();
				
				} );
			

			} else {
			     alert( result );
		

			}

		} );

	}

}

customer.form_actions = function () {	

	// Click event for Add File button
	$( 'input[value=Upload]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			
			if (customer.subfolder_id == 0) {
				customer.folder_id = $( '[name=folder_id]' ).val();
			    customer.file_upload();
				console.log(customer.folder_id);
				
			} else {
				customer.folder_id = customer.subfolder_id;
				customer.file_upload(); 
				console.log(customer.folder_id);
			}	
		
			e.handled = true;
		}
	} );

	// Click event for Add Folder button
	$( 'input[value=Create\\\ Folder]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {

			if (customer.subfolder_id == 0) {
				customer.folder_id = $( '[name=folder_id]' ).val();
			    customer.folder_create();
				console.log(customer.folder_id);
				
			} else {
				customer.folder_id = customer.subfolder_id;
				customer.folder_create(); 
				console.log(customer.folder_id);
			}	
			e.handled = true;
		}
	} );

	$( '#show-file' ).click( function(e) {
		e.stopPropagation();
		e.preventDefault();
		customer.showfile();

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

		customer.list_file( e.originalEvent.dataTransfer.files );

		$( this ).find( 'h1' ).html( '<h1>Drag and Drop file here<br/>Or<br/>Click to select file</h1>' );

	} );

	$( '.file-upload-area' ).click( function() {
		$( '#file-upload' ).click();
	} );

	$( '#file-upload' ).change( function( e ) {

		customer.list_file( e.target.files );
	
	} );

// Click event for back button
	$( 'input[data-function=back-customer]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			e.handled = true;

			load_page( "customer", {}, customer.init_actions );
		}
	} );

// Click event for remove & edit link property
$(".remove-customer-by-id").off().on( 'click', function( e ) {
	e.preventDefault();
	e.stopPropagation();

	var customer_id	  = $( '[name=customer_id]' ).val();
	var property_id   = $(this).data('id');
	var property_name = $(this).prop('name');	
	var remove 		  ="Remove " + property_name + " property?" ;

			if ( confirm( remove ) == true ) {

				load_page('customer', {
					'task' : 'addedit',
					'delete' 	  : true,
					'customer_id' : customer_id,
					'property_id' : property_id

				}, customer.form_actions );
			}

});


	$(".add-link-property").off().on( 'click', function( e ) {
		e.preventDefault();
		e.stopPropagation();

		var customer_id = $( '[name=customer_id]' ).val();
		
			load_page('customer', {
				'task' 			 : 'selectproperty',
				'customer_id' 	 : customer_id

			}, customer.form_actions );
	});


	$( 'input[data-function=save]' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {

			if ( typeof $( '[name=customer_id]' ).val() !== 'undefined' ) {
				
				customer.customer_save( $( '[name=customer_id]' ).val() );
				
			} else {
				customer.customer_save();
			}

			e.handled = true;
		}
	} );

	
	$(".add-note-customer").off().on( 'click', function( e ) {
		e.preventDefault();
		e.stopPropagation();

		var customer_id = $( '[name=customer_id]' ).val();
		
			load_page('customer', {
				'task' 			 : 'selectnote',
				'customer_id' 	 : customer_id

			}, customer.form_actions );
	});

	$(".edit-note-by-id").off().on( 'click', function( e ) {
		e.preventDefault();
		e.stopPropagation();

		var note_id 	= $(this).data('id');
		var customer_id = $( '[name=customer_id]' ).val();

		load_page('customer', {
			'task' 			 : 'selectnote',
			'note_id' 	 	 : note_id,
			'customer_id' 	 : customer_id

		}, customer.form_actions );

	} );


	$( 'input[data-function=customer-save-note]' ).on( 'click', function( e ) {
		e.preventDefault(); 
		if ( e.handled !== true ) {

			var note_id 	= $( '[name=note_id]' ).val();
			var customer_id = $( '[name=customer_id]' ).val();

			if ( note_id ) {
				customer.note_save( note_id, customer_id );
			} else {
				customer.note_save();
			}

			e.handled = true;
		}
	} );

	$( '.breadcrumbsclick' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			customer.showfile();
		}
	} );
	}

	customer.file_upload = function() {

		$( '.add-file input[name=folder_id]' ).val( customer.folder_id );
	
		var form_data = new FormData( document.getElementById( 'form-uploader' ));
		var customer_id = $( '[name=customer_id]' ).val();
		var customername = $('[name=name]' ).val();

		form_data.append( 'task',      'file_upload' );
		form_data.append( 'folder_id', customer.folder_id );
		form_data.append( 'customer_id', customer_id );
		form_data.append( 'customername', customername );
	
		$.each( customer.dropped_files, function ( i, v ) {
			form_data.append( 'file['+i+']', customer.dropped_files[i] );
		} );
	
		$.ajax( {
			url         : '/handlers/files.php',
			type        : 'post',
	
			xhr         : function() {
				my_xhr = $.ajaxSettings.xhr();
				if( my_xhr.upload ) {
					my_xhr.upload.addEventListener( 'progress', customer.handler_progress, false );
				}
	
				return my_xhr;
			},
	
			beforeSend  : customer.handler_before_send,
			success     : customer.handler_complete,
			error       : customer.handler_error,
	
			enctype     : 'multipart/form-data',
			data        : form_data,
			dataType    : 'json',
			cache       : false,
			contentType : false,
			processData : false
		} );
	
	}

	customer.list_file = function( files ) {

		if ( files.length > 0 ) {
			for( let i = 0; i < files.length; i++ ){
	
				let target_id = 'file_item_' + customer.upload_index;
				let image     = '';
	
				
					image = '<a href="' + URL.createObjectURL( files[ i ] ) + '" target="_blank" data-magnify="gallery" data-caption="' + files[ i ].name + '">' +
								'<img style="width: 100px;" data-file="' + customer.upload_index + '" src="' + URL.createObjectURL( files[ i ] ) + '" class="img-responsive">' +
							'</a>';
			
	
				let str_content = '<div class="each-item-picture" id="' + target_id + '">' +
						'           <div class="img">' + image + '</div>' +
						'           <div class="details">' +
						'               <p class="file-name">' + files[ i ].name + '</p>' +
						'               <p class="file-size">' + bytesToSize( files[ i ].size ) + '</p>' +
						'           </div>' +
						'    </div>';
	
				$( '#file-upload-list' ).append( str_content );
	
				customer.dropped_files[customer.upload_index] = files[ i ];
				customer.upload_index++;
			}
		}
	}

customer.customer_save = function ( customer_id ) {
/**
* customer Save
* Takes changes from the customer form and submits them to the server
* to be saved.
*/
	var values = {
		'task' : 'save'
	};

	$.each( $( 'form[name=customer_save]' ).serializeArray(), function ( i, field ) {
		values[ field.name ] = field.value;
	} );


	if ( typeof customer_id !== 'undefined' ) {
		values.customer_id = customer_id;
	}

	$.post( '/handlers/customer.php', values, function ( result ) {

		var result_json = $.parseJSON( result );

		if ( result_json.status == 'success' ) {
			load_page( 'customer', {}, customer.init_actions );
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
				'function' : 'customer_save',
				'error'    : result_json.errors,
				'data'     : result_json.data
			} );
		}

	} );
}


customer.note_save = function ( note_id, customer_id ) { 
	
	var values = {
		'task' : 'save_customer_note'
	};

	$.each( $( 'form[name=customer_note_save]' ).serializeArray(), function ( i, field ) {
		values[ field.name ] = field.value;
	} );

		values.note_id = 0;

	if ( typeof note_id !== 'undefined' ) {
		values.note_id = note_id;
	}

	if ( typeof customer_id !== 'undefined' ) {
		values.customer_id = customer_id;
	}


	$.post( '/handlers/customer.php', values, function ( result ) {

		var result_json = $.parseJSON( result );

		if ( result_json.status == 'success' ) {

			var customer_id = $( '[name=customer_id]' ).val();

			load_page('customer', {
				'task' : 'addedit',
				'customer_id' : customer_id

			}, customer.form_actions );


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


customer.handler_before_send = function ( e ) {
	var progress = $( '<progress></progress>' ).attr( 'id', 'progress-bar' );
	$( '#form-uploader' ).append( progress );
}

customer.handler_complete = function ( result ) {
	if( result['status'] == 'success' ) { 
	// Remove progress bar
		$( '#progress-bar' ).remove();
		customer.upload_index  = 0;
		customer.dropped_files = {};

	// Clear file input field
		$( '.add-file form' )[0].reset();
		var customer_id = $( '[name=customer_id]' ).val();
	// Refresh page 
		load_page('customer', { 'task': 'addedit', 'folder_id': customer.folder_id, 'customer_id': customer_id }, customer.form_actions);
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

customer.handler_error = function ( e ) {
	$( '#progress-bar' ).remove();
	alert( 'Missing file' );
}

customer.handler_progress = function ( e ) {
	if( e.lengthComputable ) {
		$( 'progress' ).attr( {value:e.loaded, max:e.total} );
	}
}


customer.showfile = function () {
	var folder_id = $('[name=folder_id]').val();
	var customer_id = $('[name=customer_id]').val();
	var customername = $('[name=name]').val();
	customer.subfolder_id = 0;

	if (folder_id) {
		console.log("Exist");
		customer.folder_id_exist = true; 

			load_page('customer', {
				'task': 'addedit',
				'customer_id': customer_id,
				'folder_id': folder_id,

			}, customer.form_actions);

		} else {
			console.log("Create folder for customer");

			load_page('customer', {
				'task': 'addedit',
				'customer_id': customer_id,
				'createfolder': true,
				'customername': customername,

			}, customer.form_actions);
		
			setTimeout(function(){
			console.log(folder_id);

				load_page('customer', {
					'task': 'addedit',
					'customer_id': customer_id,
					'folder_id': customer.folder_id,
				}, customer.form_actions);

				 setTimeout(function(){
				customer.showfile();
				 }, 700);
			}, 300);

		}
	
}

customer.file_get = function ( file_id ) {
	var values = {
		'task'    : 'file_get',
		'file_id' : file_id
	}

	window.open( '/includes/scripts/file.php?file_id=' + file_id );
}

customer.folder_list = function ( folder_id, search = '' ) { 
	showviewbutton.style.display = "none";

	var values = {
		'task'      : 'addedit',
		'folder_id' : folder_id
	}

	if ( search != '' ) {
		values['search'] = search;
	}

	load_page('customer', {
		'task': 'addedit',
		'customer_id': customer_id,
		'folder_id': folder_id,

	}, customer.form_actions);

	setTimeout(function(){
		showviewbutton.style.display = "none";
		showaddfile.style.display = "block";
   }, 500);
}

customer.folder_create = function () {

	var folder_name = $( '.add-folder input[name=folder_name]' ).val();
	
	var values = {
		'task'	     : 'folder_create',
		'parent_id'	 : customer.folder_id,
		'name'       : folder_name
	}

	$.post( '/handlers/files.php', values, function ( result ) {
 
		var result_json = $.parseJSON( result );
  
		if ( result_json.status == 'success' ) {
			var customer_id = $( '[name=customer_id]' ).val();
			load_page('customer', { 'task': 'addedit', 'folder_id': customer.folder_id, 'customer_id': customer_id }, customer.form_actions);
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










