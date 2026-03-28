<?php

// Common start
	include( "{$_SERVER['DOCUMENT_ROOT']}/../components/start.php" );

// Initialize framework
	$framework = new \Atlas\Framework();

// Generate CSS from LessCSS
	require( __DIR__ . '/../components/css.php' );

// Check for logged in
	if ( isset( $_SESSION['user']['user_id'] )) {
	// User is logged in, generate application DOM

	// Get the security codes associated with the user
		$jsonrpc_client = new jsonrpc\client();
		$jsonrpc_client->server( $config->get( 'jsonrpc\main\server' ));

		$security = new jsonrpc\method( 'user.security_get' );
		$security->param( 'api_token', $jsonrpc_api_token );
		$security->param( 'hash',      $_SESSION['user']['hash'] );
		$security->id = $jsonrpc_client->generate_unique_id();

		$jsonrpc_client->method( $security );
		$jsonrpc_client->send();

		$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

		if ( $result[ $security->id ]['status'] ) {
			$security_codes = $result[ $security->id ]['data'];
		} else {
			$security_codes = [];
		}

	// Import navigational structure: This is the associative array that defines the navigation elements and their related JS
	// events
		require( __DIR__ . '/../components/navigation.php' );

	// Compare security codes to navigation requirements and eliminate the ones the user isn't allowed to access
		for ( $i = 0; $i < count( $navigation ); ++$i ) {

			if ( !empty( $navigation[ $i ]['security'] )) {

				$nav_codes = explode( ',', $navigation[ $i ]['security'] );

				$allowed = FALSE;

				foreach ( $nav_codes as $nav_code ) {

					if ( in_array( $nav_code, $security_codes )) {

						$allowed = TRUE;
					}
				}

				if ( !$allowed ) {

					unset( $navigation[ $i ] );
				}
			}
		}

		$framework->set_navigation( $navigation );

	// Import the navigation HTML template: This is the HTML structure of the navigation
		require( __DIR__ . '/../templates/navigation.php' );

	// Set the DOM: This is the HTML document that functions as the base of the application
		$framework->set_dom( file_get_contents( __DIR__ . '/../templates/dom.php' ));

	// Set the navigation templates
		$framework->set_nav_templates( $templates );

	// Load JS files: These are the files symlinked in www/js.d
		$framework->js_loader( __DIR__ . '/js.d/' );

	// Attach the logout event to the Logout element
		$framework->attach_logout( '[data-function=logout]', 'logout()' );

	// Define the idle timeout in seconds, the script to call every 60 seconds to keep the server-side session alive, and the
	// popup to call when the timeout has expired
		$framework->set_idle_timeout( 3600, '/handlers/keepalive.php', '/handlers/idle_timeout.php' );

	// Check to see if a page was passed in the URL so we can load it
		if ( isset( $_GET['p'] )) {

			switch ( $_GET['p'] ) {

				case 'telemarketing'  : $load_page = 'load_page( "telemarketing" );';  break;
				case 'leads_websites' : $load_page = 'load_page( "leads_websites" );'; break;
				case 'reports'        : $load_page = 'load_page( "reports" );';        break;
				case 'administration' : $load_page = 'load_page( "administration" );'; break;

				default : $load_page = 'load_page( "dashboard" );'; break;
			}
		} else {

			$load_page = 'load_page( "dashboard" );';
		}

		echo str_replace( '%LOAD_PAGE%', $load_page, $framework->generate() );
	} else {
	// User is not logged in, display the login page

	// Set the DOM: This is the HTML document that functions as the base of the application
		$framework->set_dom( file_get_contents( __DIR__ . '/../templates/login.php' ));

	// Load JS files: These are the files symlinked in www/js.d
		$framework->js_loader( __DIR__ . '/js.d/' );

	// Generate login form
		require( __DIR__ . '/../templates/form.php' );

		$login_form = new Atlas\Framework\Form( $form_templates['main_form'] );

	// * Username field
		$login_form->add_element( new Atlas\Framework\Form\Element( 'text', [
			'label'       => '',
			'name'        => 'username',
			'value'       => '',
			'placeholder' => 'Username',
			'class'       => 'width-300px',
			'attr'        => [
				[
					'name'  => 'autocomplete',
					'value' => 'off'
				]
			]
		] ));

	// * Password field
		$login_form->add_element( new Atlas\Framework\Form\Element( 'password', [
			'label'       => '',
			'name'        => 'password',
			'value'       => '',
			'placeholder' => 'Password',
			'class'       => 'width-300px'
		] ));

	// * Login button
		$login_form->add_element( new Atlas\Framework\Form\Element( 'button', [
			'name'  => 'login',
			'value' => 'Login',
			'class' => 'go'
		] ));

	// Display login page
		echo str_replace( [
				'%LOGIN_FORM%'
			],
			[
				$login_form->render()
			],
			$framework->generate()
		);
	}

?>
