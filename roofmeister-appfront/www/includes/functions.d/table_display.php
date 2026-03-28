<?php

	function table_display( $table, $class = null, $header = null ) {
	/**
	 * Table Display
	 * Converts the supplied array into an HTML-formatted table and returns
	 * the result.
	*/
		$html = '';

		if( isset( $header )) {
			$html .= "<h2 class=\"$class\">$header</h2>\n";
		}

		if( isset( $class )) {
			$html .= "<table class=\"$class\">\n";
		} else {
			$html .= "<table>\n";
		}

	// Always have a <thead> section
		$html .= "<thead>\n";
		if( isset( $table['header'] ) && is_array( $table['header'] )) {
			$html .= "<tr>\n";

			foreach( $table['header'] as $header ) {
				$attr = "";

				if( isset( $header['attr'] ) && is_array( $header['attr'] )) {
					foreach( $header['attr'] as $name => $value ) {
						$attr .= " $name=\"$value\"";
					}
				}

				$html .= "<th $attr>{$header['value']}</th>\n";
			}

			$html .= "</tr>\n";
		}
		$html .= "</thead>";

		if( isset( $table['footer'] ) && is_array( $table['footer'] )) {
			$html .= "<tfoot>\n<tr>\n";

			foreach( $table['footer'] as $footer ) {
				$html .= "<th>$footer</th>\n";
			}

			$html .= "</tr>\n</tfoot>\n";
		}

	// Always have a <tbody> section
		$html .= "<tbody>\n";
		if( isset( $table['body'] ) && is_array( $table['body'] )) {
			$html .= "<tr>\n";

			foreach( $table['body'] as $body ) {
				$attr = "";

				if( isset( $body['attr'] ) && is_array( $body['attr'] )) {
					foreach( $body['attr'] as $name => $value ) {
						$attr .= " $name=\"$value\"";
					}
				}

				if( isset( $body['data'] ) && is_array( $body['data'] )) {
					foreach( $body['data'] as $data ) {
						$attr .= " data-{$data['attr']}=\"{$data['value']}\"";
					}
				}

				$html .= "<tr" . $attr . ">\n";

				foreach( $body['cells'] as $row ) {
					$attr = "";

					if( isset( $row['attr'] ) && is_array( $row['attr'] )) {
						foreach( $row['attr'] as $name => $value ) {
							$attr .= " $name=\"$value\"";
						}
					}

					$html .= "<td $attr>{$row['value']}</td>\n";
				}

				$html .= "</tr>\n";
			}

			$html .= "</tr>\n";
		}
		$html .= "</tbody>\n";

		$html .= "</table>\n";

		return $html;
    }

?>
