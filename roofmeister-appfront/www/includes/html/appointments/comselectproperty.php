<?php ob_start(); ?>
<div class="row">
      <div class="col-1">
	  	<input type="button" value="Back" class="back-appselectproperty">
		<span class="page_nav" data-name="page-container">%PAGES%</span>
      </div>
      <div class="col-2">
	  <span class="search"><input type="text" id="search" name="search" class="search-selectproperty" placeholder=" search..."></span>
      </div>
</div>

<div class="table-container" data-name="table-container">
%TABLE_CONTENT%
</div>

<script type="text/javascript">

$( document ).ready( function() {

	$(".back-appselectproperty").off().on( 'click', function( e ) {
		e.preventDefault();
		e.stopPropagation();

		back_page();
	} );


		if( typeof window.selectproperty === 'undefined' ) {
			window.selectproperty = {};		
		}

		if( typeof window.selectproperty.page !== 'undefined' ) {
			selectproperty_search();
		}else{
			window.selectproperty.page = 1;
		}

		if( typeof window.selectproperty.search !== 'undefined' && window.selectproperty.search != '' ) {
			
			window.selectproperty.search = '';
			window.selectproperty.page   = 1;
			selectproperty_search();

		} else 
		{

			window.selectproperty.search = '';
			window.selectproperty.page   = 1;
			selectproperty_search();

		}

		$( 'span[data-function=view-page]' ).on( 'click', function() {
			
			window.selectproperty.page = $( this ).data( 'page-num' );
			selectproperty_search();

		} );


		$('.search-selectproperty').off().on( "keyup", function(e) {
		 window.selectproperty.search = $( this ).val();
		 window.selectproperty.page   = 1;

		selectproperty_search();
		});


		function selectproperty_search() { 

		var projcustomer_id = $( '[name=appcustomer_id]' ).val();
	
		var values = {
			'task'   	  	  : 'selectproperty',
			'projcustomer_id' : projcustomer_id,
			'i'       	 	  : window.selectproperty.page,
			'search' 	  	  : window.selectproperty.search
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


				$(".select-projproperty-by-id").off().on( 'click', function( e ) {

					e.preventDefault();
					e.stopPropagation();
					
				var select_apptype		 = $( '[name=apptype]' ).val();
				var select_start		 = $( '[name=start]' ).val();
				var select_end		   	 = $( '[name=end]' ).val();
				var select_description	 = $( '[name=description]' ).val();
				var appointment_id 		 = $( '[name=appointment_id]' ).val();
				var appcustomer_id 		 = $( '[name=appcustomer_id]' ).val();
				var appcustomer_name 	 = $( '[name=appcustomer_name]' ).val();
				var select_property_id   = $(this).data('id');
				var select_property_name = $(this).prop('name');	
				var addlinkcus 			 ="Select " + select_property_name + " to this appointment?" ;

					if (confirm(addlinkcus) == true) {

					load_page('appointments', {
						'task' 			  : 'addedit',
						'savedisable'  	  : true,
						'JSappointment_id': appointment_id,
						'customer_id'	  : appcustomer_id,
						'property_id' 	  : select_property_id,
						'customer_name'   : appcustomer_name,
						'property_name'   : select_property_name,
						'selectapptype'	  : select_apptype,
						'select_start'	  : select_start,
						'select_end'	  : select_end,
						'selectdescription': select_description	

					}, appointment.form_actions );
					}

				} );

				$( 'span[data-function=view-page]' ).on( 'click', function() {
				 window.selectproperty.page = $( this ).data( 'page-num' );
		
				 selectproperty_search();
				
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
