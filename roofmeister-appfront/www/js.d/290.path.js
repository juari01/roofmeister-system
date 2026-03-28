paths = {};

paths.folder_id    = 0; 
paths.init_actions = function () { 
	$( 'tr' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			load_page( 'admin/paths', {
				'task'         : 'selectfolder',
				'path_id' : $( this ).data( 'path_id' )
			}, paths.form_actions );

			e.handled = true;

		
		}
	} );

}
