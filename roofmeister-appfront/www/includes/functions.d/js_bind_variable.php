<?php

	function js_bind_variable( $variable, $value, $string = TRUE ) {
	/**
	 * JS Bind Variable
	 * Generates the JS code to create js variables
	 */
		$js  = "<script type=\"type/javascript\">\n";
		
		if ( $string ) {
			$js .= "\t$variable='$value'\n";
		} else {
			$js .= "\t$variable=$value\n";
		}
		$js .= "</script>\n";

		return $js;
    }

?>
