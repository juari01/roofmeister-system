<?php

	include( "{$_SERVER['DOCUMENT_ROOT']}/../components/lesscss.php" );
	// include( "{$_SERVER['DOCUMENT_ROOT']}/includes/css/glDatePicker.default.css" );
	// include( "{$_SERVER['DOCUMENT_ROOT']}/includes/css/jquery.datetimepicker.css" );

	if ( file_exists( "{$_SERVER['DOCUMENT_ROOT']}/includes/css/roofmeister.less" )) {
	    $less = new lessc( "{$_SERVER['DOCUMENT_ROOT']}/includes/css/roofmeister.less" );

		$output_css = str_replace( '//', '/', "{$_SERVER['DOCUMENT_ROOT']}/includes/css/roofmeister.css" );

		if ( is_writable( $output_css )) {
			file_put_contents( $output_css, $less->parse() );
		} else {
			die( "<span class=\"error\">$output_css is not writable.</span>\n" );
		}
	}

?>
