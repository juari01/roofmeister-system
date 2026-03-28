<!DOCTYPE html>
<html>
	<head>
		<title>Roofmeister - by Hyperion Works</title>

		<link rel="stylesheet" type="text/css" media="print, screen" href="/includes/css/roofmeister.css">
		<link rel="stylesheet" type="text/css" media="print, screen" href="/includes/css/jquery.datetimepicker.css">
		<link rel="stylesheet" type="text/css" media="print, screen" href="/includes/css/glDatePicker.default.css">

		<!-- <meta http-equiv="Content-Type" content="text/html; charset=utf-8"> -->
		<meta charset="utf-8">
   		 <meta name="viewport" content="width=device-width, initial-scale=1.0">


// Insert auto-loaded JS files from js.d directory
%JS_AUTOLOAD%

		<script type="text/javascript">

// Navigation events
%NAV_JS%

// Attach event to logout
%LOGOUT_EVENT%
	
// Set idle timeout, and keepalive time to prevent back-end session from expiring
%IDLE_TIMEOUT%

// Load the startup page
%LOAD_PAGE%

	</script>
	</head>

	<body>
		<header>
			<div class="logo"><img src="/images/hw_logo-yellow.png" alt="Logo"></div>
			<div class="menu" id="shownav"><img src="/images/icons8-menu-94.png" alt="Menu" width="65px"></div>
			<div class="system-tray">
				<div data-function="notification"><img src="/images/header_icon_notification.png" alt="Notification"></div>
				<div data-function="profile" title="User Profile"><img src="/images/header_icon_profile.png" alt="User Profile"></div>
				<div data-function="logout" title="Logout"><img src="/images/header_icon_logout.png" alt="Logout"></div>
			</div><!-- /.system-tray -->
		</header>

		<nav id ="nav">
			<ul>
%NAVIGATION%
			</ul>
		</nav>

		<section id="content">

		</section><!-- /#content -->
	</body>

	<script>

	$( '#shownav' ).click( function(e) {

		e.stopPropagation();
		e.preventDefault();

		$('nav').slideToggle(250);

	} );

	navbar_display_none = function () { 
	document.getElementById("nav").style.display = "none";
	}


	</script>
</html>
