<?php

	require( "{$_SERVER['DOCUMENT_ROOT']}/../components/lesscss.php" );

	$less_file = "{$_SERVER['DOCUMENT_ROOT']}/includes/css/{$_GET['file']}.css.less";

	$less = new lessc;

	$output = $less->compile( file_get_contents( $less_file ));

	header( 'Content-type: text/css' );
	header( 'Content-length: ' . strlen( $output ));
	header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', filemtime( $less_file )));

	echo $output;

?>
