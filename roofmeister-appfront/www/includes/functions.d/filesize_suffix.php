<?php

	function filesize_suffix( $bytes ) {
	/**
	 * Filesize Suffix
	 * Calculates the appropriate filesize suffix for the given number
	 * of bytes.
	 */
		if( $bytes > 1024 * 1024 * 1024 * 1024 ) {
			$bytes = ( floor( $bytes / ( 1024 * 1024 * 1024 * 1024 ) * 100 ) / 100 ) . 'TB';
		} elseif( $bytes > 1024 * 1024 * 1024 ) {
			$bytes = ( floor( $bytes / ( 1024 * 1024 * 1024 ) * 100 ) / 100 ) . 'GB';
		} elseif( $bytes > 1024 * 1024 ) {
			$bytes = ( floor( $bytes / ( 1024 * 1024 ) * 100 ) / 100 ) . 'MB';
		} elseif( $bytes > 1024 ) {
			$bytes = ( floor( $bytes / ( 1024 ) * 100 ) / 100 ) . 'KB';
		} else {
			$bytes = $bytes . 'B';
		}

		return $bytes;
	}

?>
