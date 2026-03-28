<?php ob_start(); ?>
<div class="row">
      <div class="col-1">
	  	<input type="button" value="Back" class="back-appselectproj" data-function="back-appselectproj">
		<span class="page_nav" data-name="page-container">%PAGES%</span>
      </div>
      <div class="col-2">
	  <span class="search"><input type="text" id="search" name="search" class="search-appselectproject" placeholder=" search..."></span>
      </div>
</div>

<div class="table-container" data-name="table-container">
%TABLE_CONTENT%
</div>


<script type="text/javascript">

$( document ).ready( function() {

	$(".back-appselectproj").off().on( 'click', function( e ) {
		e.preventDefault();
		e.stopPropagation();

		back_page();

	} );



	if( typeof  window.selectproject === 'undefined' ) {
			 window.selectproject = {};
				
		}

		if( typeof  window.selectproject.page !== 'undefined' ) {
			selectproject_search();
		}else{
			 window.selectproject.page = 1;
		}

		if( typeof  window.selectproject.search !== 'undefined' &&  window.selectproject.search != '' ) {
			
			 window.selectproject.search = '';
			 window.selectproject.page   = 1;
			selectproject_search();

		} else 
		{

			 window.selectproject.search = '';
			 window.selectproject.page   = 1;
			selectproject_search();

		}

		$( 'span[data-function=view-page]' ).on( 'click', function() {
			
			 window.selectproject.page = $( this ).data( 'page-num' );
			selectproject_search();

		} );


	$('.search-appselectproject').off().on( "keyup", function(e) {
		 window.selectproject.search = $( this ).val();
		 window.selectproject.page   = 1;

		selectproject_search();
		});

		function selectproject_search() { 

		var values = {
			'task'   	  : 'selectproject',
			'i'       	  : window.selectproject.page,
			'search' 	  : window.selectproject.search
		};

		$.post( '/handlers/appointments.php', values, function ( result ) { 
			var result_json = $.parseJSON( result ); 

			if ( result_json.status == 'success' ) {

				$( 'div[data-name=table-container]' ).html( result_json['content']['table'] );
				$( 'span[data-name=page-container]' ).html( result_json['content']['pages'] );

				$( '.encrypted-text' ).each( function() {
				  let text = $( this ).data( 'text' );
				  $( this ).html( atob( text ));
				});


				$( 'span[data-function=view-page]' ).on( 'click', function() {
				 window.selectproject.page = $( this ).data( 'page-num' );
		
				 selectproject_search();
				
				} );


				$(".select-appproject-by-id").off().on( 'click', function( e ) {

				e.preventDefault();
				e.stopPropagation();

				var appointment_id 		   = $( '[name=appointment_id]' ).val();
				var select_appproject_id   = $(this).data('id');
				var select_appproject_name = $(this).prop('name');
				var select_apptype		   = $( '[name=apptype]' ).val();
				var select_start		   = $( '[name=start]' ).val();
				var select_end		   	   = $( '[name=end]' ).val();
				var select_description	   = $( '[name=description]' ).val();
				var addprojtoapp 		   = "Select " + select_appproject_name + " to this appointment?" ;
					
				if (confirm(addprojtoapp) == true) {

					load_page('appointments', {
						
								'task' 				: 'addedit',
								'addlinkproject' 	: true,
								'savedisable'  		: true,
								'selectproject_id' 	: select_appproject_id,
								'JSappointment_id' 	: appointment_id,
								'selectproject_name': select_appproject_name,
								'selectapptype'	 	: select_apptype,
								'select_start'	 	: select_start,
								'select_end'	 	: select_end,
								'selectdescription': select_description

					}, appointment.form_actions);
					}

				} );


			}
			else 
			{
				alert( result );
			}

		} );

	}




} );
</script>


<?php $content_index = ob_get_clean(); ?>

<?php ob_start(); ?>
<div class="button-header">
	<input type="button" value="Back" class="back" data-function="%BACK_BUTTON_FUNCTION%">
	<input type="button" value="Save" class="save" data-function="save">
</div><!-- /.button-header -->
<div class="errors"></div>
%FORM_CONTENT%
<?php $content_addedit = ob_get_clean(); ?>
