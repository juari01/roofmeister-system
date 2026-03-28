<?php ob_start(); ?>
<div class="row">
      <div class="col-1">
	 	<input type="button" value="Back" class="back-selectproperty" data-function="back-selectproperty">
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

	$(".back-selectproperty").off().on( 'click', function( e ) {
		e.preventDefault();
		e.stopPropagation();

		back_page();
	} );

	$(".select-property-by-id").off().on( 'click', function( e ) {

		e.preventDefault();
		e.stopPropagation();

		var customer_id 	     = $( '[name=customer_id]' ).val();
		var select_property_id   = $(this).data('id');
		var select_property_name = $(this).prop('name');	
		var addlinkprop 		 = "Link " + select_property_name + " to this customer?" ;

		if (confirm(addlinkprop) == true) {

			load_page('customer', {
						'task' : 'addedit',
						'addlinkproperty' : true,
						'customer_id' : customer_id,
						'property_id' : select_property_id

					}, customer.form_actions);
		}

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
		var customer_id = $( '[name=customer_id]' ).val();


		var values = {
			'task'   	  : 'selectproperty',
			'customer_id' : customer_id,
			'i'       	  : window.selectproperty.page,
			'search' 	  : window.selectproperty.search
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


				$(".select-property-by-id").off().on( 'click', function( e ) {

					e.preventDefault();
					e.stopPropagation();

					var customer_id 	     = $( '[name=customer_id]' ).val();
					var select_property_id   = $(this).data('id');
					var select_property_name = $(this).prop('name');	
					var addlinkprop 		 = "Link " + select_property_name + " to this customer?" ;

					if (confirm(addlinkprop) == true) {

						load_page('customer', {
									'task' : 'addedit',
									'addlinkproperty' : true,
									'customer_id' : customer_id,
									'property_id' : select_property_id

								}, customer.form_actions);
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
