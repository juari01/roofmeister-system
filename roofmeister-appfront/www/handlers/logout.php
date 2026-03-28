<?php

	include( "{$_SERVER['DOCUMENT_ROOT']}/../components/start.php" );

	if ( $_SERVER['REQUEST_METHOD'] != 'POST' ) {
		die( $_messages['no_direct_calls'] );
	}

	unset( $_SESSION );

	session_destroy();

?>
