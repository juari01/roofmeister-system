<?php

/**
 This script doesn't actually really do anything. We just need to call
 it periodically so that we can keep the PHP session alive and let idle
 timeouts be handled on the front-end.
*/

	include( "{$_SERVER['DOCUMENT_ROOT']}/../components/start.php" );

// Close session writing
	session_write_close();

?>
