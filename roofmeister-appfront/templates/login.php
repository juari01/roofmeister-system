<!DOCTYPE html>
<html>
	<head>
		<title>Roofmeister - by Hyperion Works</title>


		<link rel="stylesheet" type="text/css" media="print, screen" href="/includes/css/roofmeister.css">

		<!-- <meta http-equiv="Content-Type" content="text/html; charset=utf-8"> -->
		<meta charset="utf-8">
   		 <meta name="viewport" content="width=device-width, initial-scale=1.0">

// Insert auto-loaded JS files from js.d directory
%JS_AUTOLOAD%

		<script type="text/javascript">

	$( document ).ready( function() {
	// Put focus in username field
		$( function() {
			$( '[name=username]' ).focus();
		} );

	// Assign click event to login button
		$( 'input[name=login]' ).on( 'click', function() {
			$.post( '/handlers/login.php',
				{
					'username'    : $( 'input[name=username]' ).val(),
					'password'    : $( 'input[name=password]' ).val()
				},
				function( result ) {
					if ( result == 'success' ) {
						window.location.href = '/dashboard';
					} else {
						$( '.errors' ).html( result );

						$( 'input[name=password]' ).val( '' );
					}
				}
			);
		} );

		$( '#form-login' ).on( 'keypress', function( ev ) {
			if ( ev.which == 13 ) {
				$( 'input[name=login]' ).click();
			}
		} );

	// Send the user's timezone to the server
		var date_tz_offset = new Date();

		$.get( '/handler/set_tz_offset.php?offset=' + date_tz_offset.getTimezoneOffset() );
	} );

		</script>
	</head>

	<body>
		
		<div id="login">
			<div class="login-frame form">
				<div class="logo"><img src="/images/logo.png" alt="Roofmeister Logo"></div>
				<form id="form-login">
					<div class="errors"></div>
					%LOGIN_FORM%
				</form>
			</div><!-- /login-frame -->
		</div><!-- /login -->
	</body>
</html>
