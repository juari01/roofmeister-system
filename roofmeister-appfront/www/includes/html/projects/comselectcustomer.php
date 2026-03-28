<?php ob_start(); ?>
<div class="row">
      <div class="col-1">
	 	<input type="button" value="Back" class="back-projselectcustomer" data-function="back-projselectcustomer">
		<span class="page_nav" data-name="page-container">%PAGES%</span>
      </div>
      <div class="col-2">
	  <span class="search"><input type="text" id="search" name="search" class="search-property" placeholder=" search..."></span>
      </div>
</div>

<div class="table-container" data-name="table-container">
%TABLE_CONTENT%
</div>


<script type="text/javascript">

$( document ).ready( function() {

	if( typeof window.selectcustomer === 'undefined' ) {
			 window.selectcustomer = {};
			
	}

	if( typeof window.selectcustomer.page !== 'undefined' ) {
			 selectcustomer_search();
	}else{
		 window.selectcustomer.page = 1;
	}

	if( typeof window.selectcustomer.search !== 'undefined' && window.selectcustomer.search != '' ) {
		
		 window.selectcustomer.search = '';
		 window.selectcustomer.page   = 1;
		selectcustomer_search();

	
	} else {

		 window.selectcustomer.search = '';
		 window.selectcustomer.page   = 1;
		selectcustomer_search();
	}


	$( 'span[data-function=view-page]' ).on( 'click', function() {
		window.selectcustomer.page = $( this ).data( 'page-num' );
			selectcustomer_search();

	} );



	$('.search-selectcustomer').off().on( "keyup", function(e) {
		 window.selectcustomer.search = $( this ).val();
		 window.selectcustomer.page   = 1;

			selectcustomer_search();
	});



	function selectcustomer_search() { 
		var property_id = $( '[name=property_id]' ).val();
		var values = {
			'task'   	  : 'selectcustomer',
			'property_id' : property_id,
			'i'       	  : window.selectcustomer.page,
			'search' 	  : window.selectcustomer.search
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

				$(".select-customer-by-id").off().on( 'click', function( e ) {

				e.preventDefault();
				e.stopPropagation();
			
				var project_id 			 = $( '[name=project_id]' ).val();
				var projectname 		 = $( '[name=name]' ).val();
				var projdescription 	 = $( '[name=description]' ).val();
				var property_id 		 = $( '[name=property_id]' ).val();
				var select_customer_id   = $(this).data('id');
				var select_customer_name = $(this).prop('name');	
				var addlinkcus 			 ="Select " + select_customer_name + " to this project?" ;

				if (confirm(addlinkcus) == true) {

				load_page('projects', {
					'task' 			  : 'addedit',
					'addlinkcustomer' : true,
					'JSproject_id' 	  : project_id,
					'customer_id' 	  : select_customer_id,
					'customer_name'   : select_customer_name,
					'projectname' 	  : projectname,
					'projdescription' : projdescription
					

				}, project.form_actions);
				}


				} );


				$( 'span[data-function=view-page]' ).on( 'click', function() {
				 window.selectcustomer.page = $( this ).data( 'page-num' );
		
				selectcustomer_search();
				
				} );

			}
			else 
			{
				alert( result );
			}


		} );

	}




	$(".back-selectcustomer").off().on( 'click', function( e ) {
		e.preventDefault();
		e.stopPropagation();

		back_page();
	} );


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
