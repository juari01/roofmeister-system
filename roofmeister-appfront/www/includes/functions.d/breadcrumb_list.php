<?php

	function breadcrumb_list( $items ) {
	/**
	 * Breadcrumb list
	*/
	 $list = "<ul class=\"breadcrumb\">";

	 if ( $items ) {
	 	foreach ( $items as $item ) {
	 		$item_name = isset( $item['is_link'] ) &&  $item['is_link'] == true ? "<a class=\"item-link\" data-function=\"{$item['function']}\">{$item['name']}</a>" : $item['name'];
	 		$list .= "<li>{$item_name}</li>";
	 	}
	 }

		return $list;
	}

?>
