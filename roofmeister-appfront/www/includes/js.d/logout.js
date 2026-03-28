
	function logout() {
	/**
	 * Logout
	 * Calls /handlers/logout.php, which destroys the session, then
	 * redirects to the login page.
	 */

		$.post( '/handlers/logout.php', function() {
			window.location.href = '/';
		} );
	}

