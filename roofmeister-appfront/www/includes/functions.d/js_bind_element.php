<?php

	function js_bind_element( $element, $function, $event ) {
	/**
	 * JS Bind Element
	 * Generates the JS code to bind the specified function and event to
	 * the specified element.
	 */
		$js  = "<script type=\"type/javascript\">\n";
		$js .= "\t$( '$element' ).on( '$event', function( e ) {\n";
		$js .= "\t\t$function;\n";
		$js .= "\t} );\n";
		$js .= "</script>\n";

		return $js;
    }

?>
