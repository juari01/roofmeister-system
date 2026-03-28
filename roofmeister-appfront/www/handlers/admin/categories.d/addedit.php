<?php

use Atlas\Framework\Form;
use Atlas\Framework\Form\Element;

// Close session writing
	session_write_close();

	if ( isset( $_POST['category_id'] ) ) {
		$get_category = new jsonrpc\method( 'admin.category.get' );
		$get_category->param( 'api_token',   $jsonrpc_api_token );
		$get_category->param( 'hash',        $_SESSION['user']['hash'] );
		$get_category->param( 'category_id', $_POST['category_id'] );
		$get_category->id = $jsonrpc_client->generate_unique_id();

		$jsonrpc_client->method( $get_category );
	}


// Get geography
	$get_geography = new jsonrpc\method( 'admin.category.get_geography' );
	$get_geography->param( 'api_token',    $jsonrpc_api_token );
	$get_geography->param( 'hash',         $_SESSION['user']['hash'] );
	$get_geography->param( 'category_id',  isset( $_POST['category_id'] ) ? $_POST['category_id'] : NULL );
	$get_geography->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $get_geography );

// Send request to JSON-RPC
	$jsonrpc_client->send();

	try {
		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

	// Get jsonrpc results
		if ( isset( $_POST['category_id'] ) && $result[ $get_category->id ]['status'] ) {
			$category = $result[ $get_category->id ]['data'][0];
		}

		if ( $result[ $get_geography->id ]['status'] ) {
			$geography_rows = $result[ $get_geography->id ]['data'];
		}

	// Create the category options form
		require( $_SERVER['DOCUMENT_ROOT'] . '/../templates/form.php' );

		$category_form = new Form( $form_templates['main_form'] );

		$category_form->add_element( new Element ( 'text', [
			'label' => 'Name',
			'name'  => 'name',
			'value' => ( isset( $category['name'] ) ? $category['name'] : '' ),
			'class' => 'width-300px label-required'
		] ) );

		$category_form->add_element( new Element( 'linebreak', [] ) );

		$category_form->add_element( new Element( 'checkbox', [
			'label'   => 'Active',
			'options' => [
				[
					'display' => 'Active',
					'name'    => 'active',
					'value'   => 1,
					'checked' => ( isset( $category['active'] ) && $category['active'] == 1 || !isset( $_POST['category_id'] ) ? TRUE : FALSE )
				]
			]
		] ) );
		// Create the Security Options form
		$geography_options = [];

		foreach ( $geography_rows as $geography ) {
			$geography_options[] = [
				'display' => $geography['name'],
				'name'    => 'geography',
				'value'   => $geography['geography_id'],
				'checked' => ( !empty( $geography['enabled'] ) ? TRUE : FALSE ),
			];
		}

		$geography_form = new Form( $form_templates['main_form'] );

		$geography_form->add_element( new Element( 'checkbox', [
			'type'    => 'checkbox',
			'label'   => 'Geography Options',
			'options' => $geography_options
		] ) );

		$geography_form->add_element( new Element( 'linebreak', [] ) );

		if ( isset( $category['category_id'] ) ) {

			$category_form->add_element( new Element( 'hidden', [
				'name'  => 'category_id',
				'value' => $category['category_id']
			] ) );
		}

		include( "{$_SERVER['DOCUMENT_ROOT']}/includes/html/common.php" );

		$content_addedit = str_replace( '%FORM_CONTENT%',
			App::form_wrapper( 'category_save', [
				'Category Form' => [
					'content' =>  [
						'Category Information' => $category_form->render(),
						'Geography'            => $geography_form->render()
					]
				],
			] ),
			$content_addedit
		);

		$content_addedit = str_replace( '%BACK_BUTTON_FUNCTION%', 'back-category', $content_addedit );

		echo json_encode( [
			'status'  => 'success',
			'content' => $content_addedit
		] );
	} catch ( Exception $e ) {
		error_log( 'DEBUG: ' . $jsonrpc_client->result_raw );

		echo json_encode( [
			'status'  => FALSE,
			'errors' => $jsonrpc_client->result_raw
		] );
	}

?>
