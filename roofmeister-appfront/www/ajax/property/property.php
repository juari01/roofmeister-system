<?php 

// Common start
	include( "{$_SERVER['DOCUMENT_ROOT']}/../components/start.php" );

// Close session writing
	session_write_close();

// Don't allow calling this handler directly
	if ( $_SERVER['REQUEST_METHOD'] != 'GET' ) {
		die( $_messages['no_direct_calls'] );
	}

// Setup the JSON-RPC client
	$jsonrpc_client = new jsonrpc\client();
	$jsonrpc_client->server( $config->get( 'jsonrpc\main\server' ));


	if( $_GET['task'] == 'select-link-customer' ) { 

	include( "{$_SERVER['DOCUMENT_ROOT']}/includes/contents/customer/customer.php" );
	include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/customer/comcustomer.php" );
	$content = new Customer();

	if ( !isset( $_POST['task'] ) || $_POST['task'] == 'index' ) {
		if ( isset( $_POST['i'] )) {
			$index = $_POST['i'];
		} else {
			$index = 1;
		}
	

	$get_customer = new jsonrpc\method( 'customer.get' );
	$get_customer->param( 'api_token', $jsonrpc_api_token );
	$get_customer->param( 'hash',      $_SESSION['user']['hash'] );
	$get_customer->param( 'count',      true );
	$get_customer->param( 'not_zero',   true );
	$get_customer->param( 'limit',      20 );
	$get_customer->param( 'offset',     ( $index - 1 ) * 20 );
	$get_customer->id = $jsonrpc_client->generate_unique_id();

	if( isset( $_POST[ 'search' ] )) {
	$get_customer->param( 'search' , $_POST[ 'search' ] );
	}

	$jsonrpc_client->method( $get_customer );
	$jsonrpc_client->send();


	require( $_SERVER['DOCUMENT_ROOT'] . '/../templates/form.php' );
	$customer_form = new Form( $form_templates['main_form'] );

	echo '<form name="payment">';
	echo App::form_display( array(
		array(
			'type'  => 'hidden',
			'name'  => 'customer_id',
			'value' => $_GET['customer_id']
		),
		array(
			'type'  => 'text',
			'label' => 'Customer Name',
			'class' => 'width-200px',
			'name'  => 'customer_name',
			'value' => ''
		),
		array(
			'type' => 'linebreak'
		),
		array(
			'type'  => 'text',
			'label' => 'Credit Card Number',
			'class' => 'width-200px',
			'name'  => 'card_number',
			'value' => ''
		),
		array(
			'type' => 'linebreak'
		),
		array(
			'type'  => 'text',
			'label' => 'Exp Month',
			'class' => 'width-50px',
			'name'  => 'exp_month',
			'value' => ''
		),
		array(
			'type'  => 'text',
			'label' => 'Exp Year',
			'class' => 'width-50px',
			'name'  => 'exp_year',
			'value' => ''
		),
		array(
			'type' => 'linebreak'
		),
		array(
			'type'  => 'text',
			'label' => 'CVV Number',
			'class' => 'width-200px',
			'name'  => 'cvv_number',
			'value' => ''
		),
		array(
			'type' => 'linebreak'
		),
		array(
			'type'  => 'text',
			'label' => 'Sales Tax',
			'class' => 'width-200px',
			'name'  => 'sales_tax',
			'value' => '0.00',
			'attr'  => array( array(
				'name'  => 'readonly',
				'value' => ''
			)),
		),
		array(
			'type' => 'linebreak'
		),
		array(
			'type'  => 'text',
			'label' => 'Amount to Charge The Card',
			'class' => 'width-200px',
			'name'  => 'subtotal',
			'value' => '0.00',
			'attr'  => array( array(
				'name'  => 'readonly',
				'value' => ''
			)),
		),
		array(
			'type' => 'linebreak'
		),
		array(
			'type'  => 'button',
			'value' => 'Submit payment',
			'data'  => array(
				array(
					'name'  => 'function',
					'value' => 'process-cc-payment'
				)
			)
		)
	), $form_templates['popup_form'] );

	echo '</form>';

	$result = jsonrpc\client::parse_result( $jsonrpc_client->result );


	if ( $result[ $get_customer->id ][ 'status' ] ) {

   	   $count 	  	   = $result[ $get_customer->id ][ 'data' ][ 'countresult' ];
	   $customers  	   = $result[ $get_customer->id ][ 'data' ][ 'customers' ];
	   $page_nav 	   = $content->get_pagination( $count, $index );
	   $table_customer = $content->get_list( $customers );

		if( isset( $_POST[ 'task' ] ) && $_POST[ 'task' ] == 'index' ) {
			echo json_encode( array(
				'status'  => 'success',
				'content' => array(
				'table'   => App::table_display( $table_customer ),
				'pages'   => $page_nav
				)
			));
		} else {

	    $content_index = str_replace( "%PAGES%", $page_nav, $content_index );
		$content_index = str_replace( '%TABLE_CONTENT%' , App::table_display( $table_customer ), $content_index );
		
		echo json_encode( [
			'status'  => 'success',
			'content' => $content_index
		] );

	}

	} else {

		echo json_encode( [
			'status' => FALSE,
			'errors' => $result[ $get_customer->id ]['message']
		] );
	}

}

}



?>
